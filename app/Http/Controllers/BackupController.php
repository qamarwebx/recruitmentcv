<?php

namespace App\Http\Controllers;

use App\Jobs\RunDatabaseBackupJob;
use App\Models\Adminpermission;
use App\Models\BackupDriveHistory;
use App\Models\BackupGoogleAccount;
use App\Models\BackupHistory;
use App\Models\BackupSetting;
use App\Services\Backup\DatabaseBackupService;
use App\Services\Backup\Exceptions\GoogleDriveException;
use App\Services\Backup\GoogleDriveService;
use App\Services\FileManagerQuotaService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\InvalidStateException;

class BackupController extends Controller
{
    public function __construct(
        protected DatabaseBackupService $service,
        protected GoogleDriveService $driveService,
    ) {
    }

    /**
     * Mirrors StorageUsageController::authorizeAccess() - returns the
     * acting staff member's permission row (null for a superadmin, who
     * bypasses per-field checks), aborting 403 if neither applies.
     */
    protected function authorizeAccess(): ?Adminpermission
    {
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        $allowed = $user->user_type == 1
            || (isset($permission) && $permission->full_access == 1)
            || (isset($permission) && $permission->db_backup_setting == 1);

        if (!$allowed) {
            abort(403, 'You do not have permission to access DB Backup.');
        }

        return $permission;
    }

    public function index()
    {
        $permission = $this->authorizeAccess();

        $settings = BackupSetting::current();

        return view('admin.settings.backup.index', [
            'permission' => $permission,
            'settings' => $settings,
            'intervalOptions' => config('backup.interval_options'),
            'retentionMin' => config('backup.retention.min'),
            'retentionMax' => config('backup.retention.max'),
            'summary' => $this->typeSummary($settings),
            'driveAccount' => $this->presentDriveAccount(BackupGoogleAccount::current()),
        ]);
    }

    public function json(Request $request)
    {
        $this->authorizeAccess();

        $query = BackupHistory::query()->orderByDesc('generated_at');

        if ($request->filled('type') && in_array($request->input('type'), config('backup.types'), true)) {
            $query->type($request->input('type'));
        }

        $backups = $query->paginate(min((int) $request->input('per_page', 15), 100));

        $backups->getCollection()->transform(function (BackupHistory $backup) {
            return $this->presentBackup($backup);
        });

        return response()->json($backups);
    }

    public function status()
    {
        $this->authorizeAccess();

        $settings = BackupSetting::current();

        return response()->json([
            'summary' => $this->typeSummary($settings),
        ]);
    }

    public function saveDaily(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'daily_enabled' => ['nullable', 'boolean'],
            'daily_time' => ['required', 'date_format:H:i'],
            'daily_retention' => ['required', 'integer', 'min:'.config('backup.retention.min'), 'max:'.config('backup.retention.max')],
        ]);

        $settings = BackupSetting::current();
        $settings->update([
            'daily_enabled' => (bool) $request->boolean('daily_enabled'),
            'daily_time' => $validated['daily_time'],
            'daily_retention' => $validated['daily_retention'],
        ]);

        return response()->json(['status' => 'ok', 'message' => 'Daily backup settings saved.']);
    }

    public function saveInterval(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'interval_enabled' => ['nullable', 'boolean'],
            'interval_value' => ['required', 'integer', Rule::in(array_keys(config('backup.interval_options')))],
            'interval_retention' => ['required', 'integer', 'min:'.config('backup.retention.min'), 'max:'.config('backup.retention.max')],
        ]);

        $settings = BackupSetting::current();
        $settings->update([
            'interval_enabled' => (bool) $request->boolean('interval_enabled'),
            'interval_value' => $validated['interval_value'],
            'interval_retention' => $validated['interval_retention'],
        ]);

        return response()->json(['status' => 'ok', 'message' => 'Interval backup settings saved.']);
    }

    public function generate(Request $request, string $type)
    {
        $this->authorizeAccess();

        if (!in_array($type, config('backup.types'), true)) {
            abort(404);
        }

        if (RunDatabaseBackupJob::isBusy($type)) {
            return response()->json([
                'status' => 'already_running',
                'message' => ucfirst($type).' backup is already in progress.',
            ]);
        }

        RunDatabaseBackupJob::dispatchIfFree($type);

        return response()->json([
            'status' => 'queued',
            'message' => ucfirst($type).' backup started.',
        ]);
    }

    public function download(BackupHistory $backup)
    {
        $this->authorizeAccess();

        if (!$backup->isSuccessful() || !$backup->path || !$this->service->disk()->exists($backup->path)) {
            abort(404, 'Backup file not found.');
        }

        return $this->service->disk()->download($backup->path, $backup->filename);
    }

    public function delete(BackupHistory $backup)
    {
        $this->authorizeAccess();

        $this->service->deleteBackup($backup);

        return response()->json(['status' => 'ok', 'message' => 'Backup deleted.']);
    }

    // ---------------------------------------------------------------
    // Google Drive backup (same permission gate as the rest of this
    // controller - Settings > Backup > DB Backup > Google Drive).
    // ---------------------------------------------------------------

    public function driveJson(Request $request)
    {
        $this->authorizeAccess();

        $query = BackupDriveHistory::query()->orderByDesc('created_at');

        if ($request->filled('type') && in_array($request->input('type'), config('backup.types'), true)) {
            $query->type($request->input('type'));
        }

        $histories = $query->paginate(min((int) $request->input('per_page', 15), 100));

        $histories->getCollection()->transform(function (BackupDriveHistory $history) {
            return $this->presentDriveHistory($history);
        });

        return response()->json($histories);
    }

    public function driveStatus()
    {
        $this->authorizeAccess();

        return response()->json([
            'account' => $this->presentDriveAccount(BackupGoogleAccount::current()),
        ]);
    }

    /**
     * Toggle the "Enable Google Drive Backup" switch, independent from
     * whether an account is currently connected.
     */
    public function saveDriveEnabled(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
        ]);

        $account = $this->driveService->setEnabled((bool) ($validated['enabled'] ?? false));

        return response()->json([
            'status' => 'ok',
            'message' => 'Google Drive backup setting saved.',
            'account' => $this->presentDriveAccount($account),
        ]);
    }

    /**
     * Redirect the admin to Google's OAuth consent screen. prompt=consent
     * (not just select_account) guarantees a refresh_token is returned
     * even if this account previously authorized the app, which is what
     * lets scheduled backups keep uploading without anyone logging in
     * again.
     */
    public function driveConnect()
    {
        $this->authorizeAccess();

        return Socialite::buildProvider(GoogleProvider::class, config('services.google_drive'))
            ->scopes(config('backup.google_drive.scopes'))
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent',
            ])
            ->redirect();
    }

    /**
     * OAuth callback. Socialite validates the "state" round-tripped
     * through the session itself (buildProvider() is never called with
     * ->stateless()), throwing InvalidStateException on a mismatch/replay -
     * caught below so a forged/expired callback shows a clean error
     * instead of a 500.
     */
    public function driveCallback(Request $request)
    {
        $this->authorizeAccess();

        try {
            $googleUser = Socialite::buildProvider(GoogleProvider::class, config('services.google_drive'))->user();
        } catch (InvalidStateException $e) {
            return redirect()
                ->route('admin.settings.db_backup.index')
                ->with('error', 'Google Drive connection failed: the authorization request could not be verified. Please try again.');
        } catch (\Throwable $e) {
            Log::channel('drive_backup')->error('Google Drive OAuth callback failed.', ['error' => $e->getMessage()]);

            return redirect()
                ->route('admin.settings.db_backup.index')
                ->with('error', 'Google Drive connection failed. Please try again.');
        }

        $this->driveService->connect($googleUser);

        return redirect()
            ->route('admin.settings.db_backup.index')
            ->with('success', 'Google Drive account connected successfully.');
    }

    public function driveDisconnect()
    {
        $this->authorizeAccess();

        $account = $this->driveService->disconnect();

        return response()->json([
            'status' => 'ok',
            'message' => 'Google Drive account disconnected.',
            'account' => $this->presentDriveAccount($account),
        ]);
    }

    public function driveTestConnection()
    {
        $this->authorizeAccess();

        try {
            $this->driveService->testConnection();

            return response()->json([
                'status' => 'ok',
                'message' => 'Google Drive connection is working.',
                'account' => $this->presentDriveAccount(BackupGoogleAccount::current()),
            ]);
        } catch (GoogleDriveException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'account' => $this->presentDriveAccount(BackupGoogleAccount::current()),
            ], 422);
        }
    }

    /**
     * Delete one uploaded Drive backup. $driveBackup is resolved by
     * Laravel's route-model binding from a numeric id only (see the
     * ->whereNumber() route constraint) - the browser can never point
     * this at an arbitrary Drive file id, only at one of our own history
     * rows, which is what authorizeAccess() then gates.
     */
    public function driveDelete(BackupDriveHistory $driveBackup)
    {
        $this->authorizeAccess();

        $this->driveService->deleteHistory($driveBackup);

        return response()->json(['status' => 'ok', 'message' => 'Google Drive backup deleted.']);
    }

    protected function presentDriveAccount(BackupGoogleAccount $account): array
    {
        return [
            'enabled' => (bool) $account->enabled,
            'status' => $account->status,
            'google_email' => $account->google_email,
            'google_name' => $account->google_name,
            'is_connected' => $account->isConnected(),
            'drive_folder_name' => $account->drive_folder_name ?: config('backup.google_drive.folder_name'),
            'connected_at' => optional($account->connected_at)->format('d M Y, h:i A'),
            'last_tested_at' => optional($account->last_tested_at)->format('d M Y, h:i A'),
            'last_error' => $account->status === BackupGoogleAccount::STATUS_ERROR ? $account->last_error : null,
        ];
    }

    protected function presentDriveHistory(BackupDriveHistory $history): array
    {
        return [
            'id' => $history->id,
            'type' => $history->type,
            'backup_reference' => $history->backup_reference,
            'created_at' => optional($history->created_at)->format('d M Y, h:i A'),
            'file_size' => $history->file_size,
            'file_size_human' => $history->file_size ? FileManagerQuotaService::humanReadable($history->file_size) : null,
            'status' => $history->status,
            'google_account_email' => $history->google_account_email,
            'error_message' => $history->status === BackupDriveHistory::STATUS_FAILED ? $history->error_message : null,
            'drive_view_link' => $history->isUploaded() ? $history->drive_view_link : null,
        ];
    }

    protected function presentBackup(BackupHistory $backup): array
    {
        return [
            'id' => $backup->id,
            'type' => $backup->type,
            'filename' => $backup->filename,
            'generated_at' => optional($backup->generated_at)->format('d M Y, h:i A'),
            'file_size' => $backup->file_size,
            'file_size_human' => $backup->file_size ? FileManagerQuotaService::humanReadable($backup->file_size) : null,
            'status' => $backup->status,
            'error_message' => $backup->status === BackupHistory::STATUS_FAILED ? $backup->error_message : null,
            'can_download' => $backup->isSuccessful() && $backup->path && $this->service->disk()->exists($backup->path),
        ];
    }

    protected function typeSummary(BackupSetting $settings): array
    {
        $summary = [];

        foreach (config('backup.types') as $type) {
            $lastSuccess = BackupHistory::query()->type($type)->successful()->orderByDesc('generated_at')->first();
            $lastAttempt = BackupHistory::query()->type($type)->orderByDesc('generated_at')->first();

            $summary[$type] = [
                'enabled' => $type === 'daily' ? (bool) $settings->daily_enabled : (bool) $settings->interval_enabled,
                'is_busy' => RunDatabaseBackupJob::isBusy($type),
                'last_success_at' => optional($lastSuccess)->generated_at?->format('d M Y, h:i A'),
                'last_attempt_status' => optional($lastAttempt)->status,
                'last_attempt_at' => optional($lastAttempt)->generated_at?->format('d M Y, h:i A'),
                'next_scheduled_at' => $this->nextScheduledAt($type, $settings),
            ];
        }

        return $summary;
    }

    protected function nextScheduledAt(string $type, BackupSetting $settings): ?string
    {
        if ($type === 'daily') {
            if (!$settings->daily_enabled) {
                return null;
            }

            $next = Carbon::createFromFormat('H:i', $settings->daily_time);
            $next->setDate(now()->year, now()->month, now()->day);
            if ($next->lte(now())) {
                $next->addDay();
            }

            return $next->format('d M Y, h:i A');
        }

        if (!$settings->interval_enabled) {
            return null;
        }

        $last = BackupHistory::query()->type('interval')->orderByDesc('generated_at')->first();
        $minutes = max(1, (int) $settings->interval_value);

        $next = $last ? $last->generated_at->copy()->addMinutes($minutes) : now();
        if ($next->lt(now())) {
            $next = now();
        }

        return $next->format('d M Y, h:i A');
    }
}
