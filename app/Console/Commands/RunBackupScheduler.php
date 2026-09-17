<?php

namespace App\Console\Commands;

use App\Jobs\RunDatabaseBackupJob;
use App\Models\BackupHistory;
use App\Models\BackupSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Single lightweight entry point for both the Daily and Interval backup
 * schedules (Settings > Backup > DB Backup). Registered once in Kernel.php
 * to run every minute - fine-grained enough for the shortest interval
 * option (1 minute) - and decides independently whether each schedule is
 * due, so the two never interfere with each other.
 */
class RunBackupScheduler extends Command
{
    protected $signature = 'backup:run-scheduler';

    protected $description = 'Dispatch Daily/Interval database backup jobs when they are due';

    public function handle(): int
    {
        $settings = BackupSetting::current();
        $now = Carbon::now();

        if ($settings->daily_enabled && $this->isDailyDue($settings, $now)) {
            if (RunDatabaseBackupJob::dispatchIfFree('daily')) {
                $this->info('Daily backup dispatched.');
            }
        }

        if ($settings->interval_enabled && $this->isIntervalDue($settings, $now)) {
            if (RunDatabaseBackupJob::dispatchIfFree('interval')) {
                $this->info('Interval backup dispatched.');
            }
        }

        return self::SUCCESS;
    }

    protected function isDailyDue(BackupSetting $settings, Carbon $now): bool
    {
        if ($now->format('H:i') !== $settings->daily_time) {
            return false;
        }

        // Already ran (success or failed) today - don't fire again even if
        // the scheduler happens to tick twice for the same minute.
        return !BackupHistory::query()
            ->type('daily')
            ->whereDate('generated_at', $now->toDateString())
            ->exists();
    }

    protected function isIntervalDue(BackupSetting $settings, Carbon $now): bool
    {
        $last = BackupHistory::query()
            ->type('interval')
            ->orderByDesc('generated_at')
            ->first();

        if (!$last) {
            return true;
        }

        $minutes = max(1, (int) $settings->interval_value);

        return $last->generated_at->copy()->addMinutes($minutes)->lte($now);
    }
}
