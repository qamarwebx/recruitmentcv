<?php

namespace App\Jobs;

use App\Services\StorageUsage\StorageUsageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecalculateStorageUsageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 600;

    public function handle(StorageUsageService $service)
    {
        $lockKey = config('storage_usage.lock_key');
        $lock = Cache::lock($lockKey, config('storage_usage.lock_seconds', 300));

        if (!$lock->get()) {
            Log::info('Storage usage recalculation already running, skipping.');

            return;
        }

        Cache::put($lockKey . ':running', true, config('storage_usage.lock_seconds', 300));

        try {
            $service->recalculate();
        } catch (\Throwable $e) {
            Log::error('Storage usage recalculation failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        } finally {
            Cache::forget($lockKey . ':running');
            $lock->release();
        }
    }
}
