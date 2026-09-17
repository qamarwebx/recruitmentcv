<?php

namespace App\Jobs;

use App\Models\BackupDriveHistory;
use App\Models\BackupHistory;
use App\Services\Backup\GoogleDriveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Uploads one already-successful local backup to Google Drive. Dispatched
 * by DatabaseBackupService::run() right after a local backup succeeds -
 * see that class for why this never affects the local backup's own
 * success/failure. WithoutOverlapping + GoogleDriveService::upload()'s
 * own idempotency (unique backup_history_id) together prevent a retried
 * or duplicated dispatch from uploading the same backup twice.
 */
class UploadBackupToGoogleDriveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 1900;

    public function __construct(public int $backupHistoryId)
    {
    }

    public function backoff(): array
    {
        return [30, 90];
    }

    public function middleware(): array
    {
        return [new WithoutOverlapping('drive-upload:'.$this->backupHistoryId)];
    }

    public function handle(GoogleDriveService $service): void
    {
        $backup = BackupHistory::find($this->backupHistoryId);

        if (!$backup || !$backup->isSuccessful()) {
            return;
        }

        $service->upload($backup);
    }

    public function failed(\Throwable $e)
    {
        Log::channel('drive_backup')->error('Google Drive upload job permanently failed after all retries.', [
            'backup_history_id' => $this->backupHistoryId,
            'error' => $e->getMessage(),
        ]);

        BackupDriveHistory::where('backup_history_id', $this->backupHistoryId)
            ->where('status', '!=', BackupDriveHistory::STATUS_UPLOADED)
            ->update([
                'status' => BackupDriveHistory::STATUS_FAILED,
                'error_message' => Str::limit($e->getMessage(), 1000, ''),
            ]);
    }
}
