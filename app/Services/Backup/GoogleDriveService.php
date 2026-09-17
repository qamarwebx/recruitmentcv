<?php

namespace App\Services\Backup;

use App\Models\BackupDriveHistory;
use App\Models\BackupGoogleAccount;
use App\Models\BackupHistory;
use App\Models\BackupSetting;
use App\Services\Backup\Contracts\DriveTokenRefresher;
use App\Services\Backup\Contracts\DriveUploader;
use App\Services\Backup\Exceptions\GoogleDriveException;
use Google\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Two\User as SocialiteUser;

/**
 * Orchestrates the Google Drive backup feature: connection lifecycle
 * (connect/disconnect/test), access-token refresh, and the
 * upload+retention flow triggered after each successful local backup.
 * Mirrors DatabaseBackupService's split from its DumpRunner - the actual
 * Drive API calls live behind DriveUploader/DriveTokenRefresher so this
 * class stays testable without hitting Google.
 */
class GoogleDriveService
{
    public function __construct(
        protected DriveUploader $uploader,
        protected DriveTokenRefresher $tokenRefresher,
    ) {
    }

    /**
     * Persist the tokens returned by a completed OAuth exchange onto the
     * singleton account row. Google only returns a refresh_token on the
     * authorization that actually prompts for consent (see the
     * prompt=consent + access_type=offline params used when redirecting),
     * so an existing refresh_token is kept when a later exchange omits one.
     */
    public function connect(SocialiteUser $googleUser): BackupGoogleAccount
    {
        $account = BackupGoogleAccount::current();

        $account->fill([
            'google_id' => $googleUser->getId(),
            'google_email' => $googleUser->getEmail(),
            'google_name' => $googleUser->getName(),
            'access_token' => $googleUser->token,
            'token_expires_at' => now()->addSeconds((int) ($googleUser->expiresIn ?? 3600)),
            'scope' => implode(' ', (array) config('backup.google_drive.scopes')),
            'status' => BackupGoogleAccount::STATUS_CONNECTED,
            'last_error' => null,
            'connected_at' => now(),
        ]);

        if (!empty($googleUser->refreshToken)) {
            $account->refresh_token = $googleUser->refreshToken;
        }

        $account->save();

        if (!$account->refresh_token) {
            Log::channel('drive_backup')->warning('Google Drive connected without a refresh token; scheduled uploads will fail once the access token expires.', [
                'google_email' => $account->google_email,
            ]);
        }

        // Eagerly verify the token works and create the dedicated folder,
        // so a broken connection surfaces immediately instead of at the
        // next scheduled backup.
        try {
            $this->testConnection($account);
        } catch (GoogleDriveException $e) {
            // testConnection() already recorded the error on the account.
        }

        return $account->fresh();
    }

    public function disconnect(): BackupGoogleAccount
    {
        $account = BackupGoogleAccount::current();

        if ($account->access_token) {
            try {
                $client = new Client();
                $client->revokeToken($account->access_token);
            } catch (\Throwable $e) {
                Log::channel('drive_backup')->warning('Failed to revoke Google Drive token on disconnect (continuing anyway).', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $account->update([
            'enabled' => false,
            'access_token' => null,
            'refresh_token' => null,
            'token_expires_at' => null,
            'status' => BackupGoogleAccount::STATUS_DISCONNECTED,
            'last_error' => null,
        ]);

        return $account;
    }

    public function setEnabled(bool $enabled): BackupGoogleAccount
    {
        $account = BackupGoogleAccount::current();
        $account->update(['enabled' => $enabled]);

        return $account;
    }

    /**
     * Verify the stored credentials still work, refreshing the access
     * token first if needed. Updates status/last_error/last_tested_at on
     * the account either way.
     */
    public function testConnection(?BackupGoogleAccount $account = null): array
    {
        $account = $account ?: BackupGoogleAccount::current();

        try {
            $token = $this->validAccessToken($account);
            $result = $this->uploader->testConnection($account, $token);

            $account->update([
                'drive_folder_id' => $result['drive_folder_id'] ?? $account->drive_folder_id,
                'drive_folder_name' => $result['drive_folder_name'] ?? $account->drive_folder_name,
                'status' => BackupGoogleAccount::STATUS_CONNECTED,
                'last_error' => null,
                'last_tested_at' => now(),
            ]);

            return $result;
        } catch (GoogleDriveException $e) {
            $account->update([
                'status' => BackupGoogleAccount::STATUS_ERROR,
                'last_error' => Str::limit($e->getMessage(), 1000, ''),
                'last_tested_at' => now(),
            ]);

            Log::channel('drive_backup')->error('Google Drive test connection failed.', [
                'google_email' => $account->google_email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Return a currently-valid access token for $account, refreshing it
     * via the stored refresh token first if it is missing/expired.
     */
    public function validAccessToken(BackupGoogleAccount $account): string
    {
        $buffer = (int) config('backup.google_drive.token_refresh_buffer_seconds', 60);
        $expired = !$account->token_expires_at || now()->addSeconds($buffer)->gte($account->token_expires_at);

        if ($account->access_token && !$expired) {
            return $account->access_token;
        }

        if (!$account->refresh_token) {
            throw new GoogleDriveException('Google Drive is not connected (no refresh token available).');
        }

        $refreshed = $this->tokenRefresher->refresh($account->refresh_token);

        $account->update([
            'access_token' => $refreshed['access_token'],
            'token_expires_at' => now()->addSeconds($refreshed['expires_in']),
        ]);

        return $refreshed['access_token'];
    }

    /**
     * Upload $backup to the connected Drive account, recording the result
     * in backup_drive_histories. Idempotent: if this backup was already
     * uploaded, the existing row is returned untouched instead of
     * uploading again - this is what makes a scheduler/queue retry of the
     * same backup safe.
     */
    public function upload(BackupHistory $backup): ?BackupDriveHistory
    {
        $account = BackupGoogleAccount::current();

        if (!$account->isReadyToUpload()) {
            return null;
        }

        $history = BackupDriveHistory::firstOrNew(['backup_history_id' => $backup->id]);

        if ($history->exists && $history->isUploaded()) {
            return $history;
        }

        $history->fill([
            'backup_reference' => $backup->filename ?: $backup->path,
            'type' => $backup->type,
            'file_size' => $backup->file_size,
            'google_account_email' => $account->google_email,
            'status' => BackupDriveHistory::STATUS_PENDING,
        ])->save();

        try {
            $token = $this->validAccessToken($account);
            $result = $this->uploader->upload($backup, $account, $token);

            if (!empty($result['drive_folder_id']) && $result['drive_folder_id'] !== $account->drive_folder_id) {
                $account->update(['drive_folder_id' => $result['drive_folder_id']]);
            }

            $history->update([
                'drive_file_id' => $result['drive_file_id'],
                'drive_folder_id' => $result['drive_folder_id'],
                'drive_view_link' => $result['drive_view_link'],
                'file_size' => $result['file_size'] ?? $history->file_size,
                'status' => BackupDriveHistory::STATUS_UPLOADED,
                'error_message' => null,
                'uploaded_at' => now(),
            ]);

            Log::channel('drive_backup')->info('Backup uploaded to Google Drive.', [
                'backup_history_id' => $backup->id,
                'type' => $backup->type,
                'drive_file_id' => $result['drive_file_id'],
            ]);
        } catch (\Throwable $e) {
            $history->update([
                'status' => BackupDriveHistory::STATUS_FAILED,
                'error_message' => Str::limit($e->getMessage(), 1000, ''),
            ]);

            if ($e instanceof GoogleDriveException && str_contains($e->getMessage(), 'not connected')) {
                $account->update([
                    'status' => BackupGoogleAccount::STATUS_ERROR,
                    'last_error' => Str::limit($e->getMessage(), 1000, ''),
                ]);
            }

            Log::channel('drive_backup')->error('Google Drive backup upload failed.', [
                'backup_history_id' => $backup->id,
                'type' => $backup->type,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        // Same principle as DatabaseBackupService::applyRetention() - a
        // retention failure is logged, never turns a successful upload
        // into a failed one.
        try {
            $this->applyRetention($backup->type);
        } catch (\Throwable $e) {
            Log::channel('drive_backup')->error('Google Drive retention cleanup failed.', [
                'type' => $backup->type,
                'error' => $e->getMessage(),
            ]);
        }

        return $history;
    }

    /**
     * Keep exactly the configured number of uploaded Drive backups for
     * $type, deleting the Drive file + history row for anything older -
     * reuses the same daily_retention/interval_retention counts as the
     * local backups (App\Models\BackupSetting) so there is one retention
     * policy, not two.
     */
    public function applyRetention(string $type): int
    {
        $settings = BackupSetting::current();
        $retention = $type === 'daily' ? $settings->daily_retention : $settings->interval_retention;
        $retention = max(1, (int) $retention);

        $keepIds = BackupDriveHistory::query()
            ->type($type)
            ->uploaded()
            ->orderByDesc('uploaded_at')
            ->limit($retention)
            ->pluck('id');

        $stale = BackupDriveHistory::query()
            ->type($type)
            ->uploaded()
            ->whereNotIn('id', $keepIds)
            ->get();

        foreach ($stale as $old) {
            $this->deleteHistory($old);
        }

        return $stale->count();
    }

    /**
     * Delete one Drive backup: best-effort remove the remote file (a
     * failure here is logged, not fatal - the file may already be gone),
     * then always remove the tracking row, same shape as
     * DatabaseBackupService::deleteBackup().
     */
    public function deleteHistory(BackupDriveHistory $history): void
    {
        if ($history->drive_file_id) {
            $account = BackupGoogleAccount::current();

            try {
                $token = $this->validAccessToken($account);
                $this->uploader->delete($account, $token, $history->drive_file_id);
            } catch (\Throwable $e) {
                Log::channel('drive_backup')->error('Failed to delete file from Google Drive (removing local history row anyway).', [
                    'backup_drive_history_id' => $history->id,
                    'drive_file_id' => $history->drive_file_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $history->delete();
    }
}
