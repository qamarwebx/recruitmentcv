<?php

namespace Tests\Feature\Backup;

use App\Models\Admin;
use App\Models\BackupHistory;
use App\Models\BackupSetting;
use App\Services\Backup\Contracts\DumpRunner;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\Support\FakeDumpRunner;
use Tests\TestCase;

/**
 * Exercises App\Console\Commands\RunBackupScheduler - the single artisan
 * command the scheduler ticks every minute (see App\Console\Kernel) to
 * drive both the Daily and Interval backups independently.
 */
class BackupSchedulerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('backup.disk'));
        Cache::flush();
        $this->app->bind(DumpRunner::class, FakeDumpRunner::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** 2 & 8. Daily backup runs only at the configured time. */
    public function test_daily_backup_dispatches_only_at_the_configured_time()
    {
        Admin::factory()->superAdmin()->create();
        BackupSetting::current()->update(['daily_enabled' => true, 'daily_time' => '02:00', 'daily_retention' => 7]);

        Carbon::setTestNow(Carbon::parse('today 01:59'));
        $this->artisan('backup:run-scheduler');
        $this->assertSame(0, BackupHistory::type('daily')->count());

        Carbon::setTestNow(Carbon::parse('today 02:00'));
        $this->artisan('backup:run-scheduler');
        $this->assertSame(1, BackupHistory::type('daily')->successful()->count());
    }

    /** 9. Duplicate scheduler execution must not create a second daily backup the same day. */
    public function test_daily_backup_does_not_dispatch_twice_in_the_same_day()
    {
        BackupSetting::current()->update(['daily_enabled' => true, 'daily_time' => '02:00', 'daily_retention' => 7]);
        Carbon::setTestNow(Carbon::parse('today 02:00'));

        $this->artisan('backup:run-scheduler');
        $this->artisan('backup:run-scheduler'); // accidental double-trigger

        $this->assertSame(1, BackupHistory::type('daily')->count());
    }

    public function test_daily_backup_does_not_dispatch_when_disabled()
    {
        BackupSetting::current()->update(['daily_enabled' => false, 'daily_time' => '02:00', 'daily_retention' => 7]);
        Carbon::setTestNow(Carbon::parse('today 02:00'));

        $this->artisan('backup:run-scheduler');

        $this->assertSame(0, BackupHistory::type('daily')->count());
    }

    /** 5. Interval backup fires as soon as it's first due (no history yet). */
    public function test_interval_backup_dispatches_when_first_due()
    {
        BackupSetting::current()->update(['interval_enabled' => true, 'interval_value' => 30, 'interval_retention' => 5]);

        $this->artisan('backup:run-scheduler');

        $this->assertSame(1, BackupHistory::type('interval')->successful()->count());
    }

    /** Interval backup does not fire again before the configured interval elapses. */
    public function test_interval_backup_does_not_dispatch_before_due()
    {
        BackupSetting::current()->update(['interval_enabled' => true, 'interval_value' => 30, 'interval_retention' => 5]);
        Carbon::setTestNow(Carbon::parse('2026-01-01 10:00:00'));

        $this->artisan('backup:run-scheduler');
        $this->assertSame(1, BackupHistory::type('interval')->count());

        Carbon::setTestNow(Carbon::parse('2026-01-01 10:15:00')); // only 15 of 30 minutes elapsed
        $this->artisan('backup:run-scheduler');
        $this->assertSame(1, BackupHistory::type('interval')->count());

        Carbon::setTestNow(Carbon::parse('2026-01-01 10:30:00')); // now due
        $this->artisan('backup:run-scheduler');
        $this->assertSame(2, BackupHistory::type('interval')->count());
    }

    /** Daily and Interval schedules are fully independent - enabling one never triggers the other. */
    public function test_daily_and_interval_schedules_are_independent()
    {
        BackupSetting::current()->update([
            'daily_enabled' => false, 'daily_time' => '02:00', 'daily_retention' => 7,
            'interval_enabled' => true, 'interval_value' => 10, 'interval_retention' => 5,
        ]);
        Carbon::setTestNow(Carbon::parse('today 02:00'));

        $this->artisan('backup:run-scheduler');

        $this->assertSame(0, BackupHistory::type('daily')->count());
        $this->assertSame(1, BackupHistory::type('interval')->count());
    }
}
