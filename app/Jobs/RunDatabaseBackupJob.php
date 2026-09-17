<?php

namespace App\Jobs;

use App\Services\Backup\DatabaseBackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Runs one backup of $type ("daily" or "interval"). Duplicate/concurrent
 * runs of the same type are prevented by a single cache "busy" flag that
 * spans the whole lifecycle (queued -> running -> finished): the dispatcher
 * (scheduler command or manual "Generate Now") sets it atomically before
 * dispatch via dispatchIfFree(), and this job clears it when it's done.
 * An additional Cache::lock is the actual mutex around the dump itself, so
 * a stale busy flag (e.g. a killed worker) can never cause two dumps of the
 * same type to run at once.
 */
class RunDatabaseBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 1900;

    public function __construct(public string $type)
    {
    }

    public function backoff(): array
    {
        return [30, 90];
    }

    /**
     * Attempt to dispatch a backup for $type, but only if one isn't already
     * queued or running. Returns whether it was dispatched.
     */
    public static function dispatchIfFree(string $type): bool
    {
        $busyKey = static::busyKey($type);
        $seconds = (int) config('backup.queued_marker_seconds', 600);

        if (!Cache::add($busyKey, true, now()->addSeconds($seconds))) {
            return false;
        }

        static::dispatch($type);

        return true;
    }

    public static function isBusy(string $type): bool
    {
        return (bool) Cache::get(static::busyKey($type), false);
    }

    protected static function busyKey(string $type): string
    {
        return config('backup.lock_prefix').":{$type}:busy";
    }

    public function handle(DatabaseBackupService $service)
    {
        $lock = Cache::lock(
            config('backup.lock_prefix').":{$this->type}:running",
            (int) config('backup.running_lock_seconds', 1800)
        );

        if (!$lock->get()) {
            Log::channel('db_backup')->info('Backup skipped - another run for this type is already in progress.', [
                'type' => $this->type,
            ]);

            return;
        }

        try {
            $service->run($this->type);
        } finally {
            $lock->release();

            if ($this->attempts() >= $this->tries) {
                Cache::forget(static::busyKey($this->type));
            }
        }

        Cache::forget(static::busyKey($this->type));
    }

    public function failed(\Throwable $e)
    {
        Cache::forget(static::busyKey($this->type));

        Log::channel('db_backup')->error('Backup job permanently failed after all retries.', [
            'type' => $this->type,
            'error' => $e->getMessage(),
        ]);
    }
}
