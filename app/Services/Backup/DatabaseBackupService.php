<?php

namespace App\Services\Backup;

use App\Jobs\UploadBackupToGoogleDriveJob;
use App\Models\BackupGoogleAccount;
use App\Models\BackupHistory;
use App\Models\BackupSetting;
use App\Services\Backup\Contracts\DumpRunner;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseBackupService
{
    public function __construct(protected DumpRunner $dumpRunner)
    {
    }

    public function disk(): Filesystem
    {
        return Storage::disk(config('backup.disk'));
    }

    /**
     * Generate one backup of $type: dump to a temp file, atomically move it
     * into place only once the dump is verified good, record the result,
     * then prune old backups of the same type. A failure never touches
     * (let alone deletes) the previous successful backup, since nothing is
     * written under the real daily/interval directory until the move.
     */
    public function run(string $type): BackupHistory
    {
        $disk = $this->disk();
        $tmpDir = (string) config('backup.directories.tmp');
        $targetDir = (string) config("backup.directories.{$type}");

        $disk->makeDirectory($tmpDir);
        $disk->makeDirectory($targetDir);

        $timestamp = now();
        $filename = sprintf(
            'db_backup_%s_%s_%s.sql.gz',
            $type,
            $timestamp->format('Y-m-d_His'),
            Str::lower(Str::random(6))
        );

        $tmpRelative = $tmpDir.'/'.$filename.'.part';
        $finalRelative = $targetDir.'/'.$filename;
        $tmpAbsolute = $disk->path($tmpRelative);
        $history = null;

        try {
            $this->dumpRunner->dump($tmpAbsolute);

            $finalAbsolute = $disk->path($finalRelative);
            if (!@rename($tmpAbsolute, $finalAbsolute)) {
                throw new \RuntimeException('Dump completed but could not be moved into place.');
            }

            clearstatcache(true, $finalAbsolute);
            $size = is_file($finalAbsolute) ? (filesize($finalAbsolute) ?: 0) : 0;

            $history = BackupHistory::create([
                'type' => $type,
                'disk' => config('backup.disk'),
                'path' => $finalRelative,
                'filename' => $filename,
                'file_size' => $size,
                'status' => BackupHistory::STATUS_SUCCESS,
                'generated_at' => $timestamp,
            ]);

            Log::channel('db_backup')->info('Database backup succeeded', [
                'type' => $type,
                'timestamp' => $timestamp->toDateTimeString(),
                'filename' => $filename,
                'file_size' => $size,
            ]);
        } catch (\Throwable $e) {
            if (is_file($tmpAbsolute)) {
                @unlink($tmpAbsolute);
            }

            BackupHistory::create([
                'type' => $type,
                'disk' => config('backup.disk'),
                'path' => null,
                'filename' => null,
                'file_size' => null,
                'status' => BackupHistory::STATUS_FAILED,
                'error_message' => Str::limit($e->getMessage(), 1000, ''),
                'generated_at' => $timestamp,
            ]);

            Log::channel('db_backup')->error('Database backup failed', [
                'type' => $type,
                'timestamp' => $timestamp->toDateTimeString(),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        // Retention runs after the success/failure record is settled, and
        // never on its own turns a completed backup into a "failed" one -
        // a pruning error is logged, not conflated with the dump itself.
        try {
            $this->applyRetention($type);
        } catch (\Throwable $e) {
            Log::channel('db_backup')->error('Backup retention cleanup failed', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }

        // Google Drive upload is entirely best-effort from the local
        // backup's point of view: dispatching (or the upload itself,
        // which runs later in its own job) can never turn this already-
        // successful local backup into a failure - see
        // App\Jobs\UploadBackupToGoogleDriveJob.
        try {
            $this->maybeDispatchDriveUpload($history);
        } catch (\Throwable $e) {
            Log::channel('drive_backup')->error('Failed to dispatch Google Drive upload job', [
                'backup_history_id' => $history->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $history;
    }

    protected function maybeDispatchDriveUpload(BackupHistory $history): void
    {
        if (!$history->isSuccessful()) {
            return;
        }

        if (!BackupGoogleAccount::current()->isReadyToUpload()) {
            return;
        }

        UploadBackupToGoogleDriveJob::dispatch($history->id);
    }

    /**
     * Keep exactly the configured number of successful backups for $type,
     * newest first, deleting the file + history row for anything older.
     * Failed attempts carry no file and are left alone (audit trail).
     */
    public function applyRetention(string $type): int
    {
        $settings = BackupSetting::current();
        $retention = $type === 'daily' ? $settings->daily_retention : $settings->interval_retention;
        $retention = max(1, (int) $retention);

        // MariaDB rejects OFFSET without a LIMIT, so "skip the newest N" is
        // expressed as "keep these N ids, delete everything else" instead.
        $keepIds = BackupHistory::query()
            ->type($type)
            ->successful()
            ->orderByDesc('generated_at')
            ->limit($retention)
            ->pluck('id');

        $stale = BackupHistory::query()
            ->type($type)
            ->successful()
            ->whereNotIn('id', $keepIds)
            ->get();

        foreach ($stale as $old) {
            $this->deleteBackup($old);
        }

        return $stale->count();
    }

    public function deleteBackup(BackupHistory $backup): void
    {
        if ($backup->path && $this->disk()->exists($backup->path)) {
            $this->disk()->delete($backup->path);
        }

        $backup->delete();
    }
}
