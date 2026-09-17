<?php

namespace Tests\Feature\Backup;

use App\Models\BackupHistory;
use App\Models\BackupSetting;
use App\Services\Backup\DatabaseBackupService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupRetentionTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake(config('backup.disk'));
    }

    /**
     * Creates a fake-but-real successful history row with a matching file
     * on the faked disk, so retention's exists()/delete() calls are real.
     */
    protected function makeSuccess(string $type, \DateTimeInterface $generatedAt): BackupHistory
    {
        $dir = config("backup.directories.{$type}");
        $filename = 'db_backup_'.$type.'_'.$generatedAt->format('YmdHis').'_'.uniqid().'.sql.gz';
        $path = $dir.'/'.$filename;

        Storage::disk(config('backup.disk'))->put($path, 'dummy');

        return BackupHistory::create([
            'type' => $type,
            'disk' => config('backup.disk'),
            'path' => $path,
            'filename' => $filename,
            'file_size' => 5,
            'status' => BackupHistory::STATUS_SUCCESS,
            'generated_at' => $generatedAt,
        ]);
    }

    /** 3 & 4. Retention copies 1/4/etc keep only the newest N and prune the rest. */
    public function test_retention_keeps_only_the_configured_number_of_daily_backups()
    {
        BackupSetting::current()->update(['daily_retention' => 2]);

        $old = $this->makeSuccess('daily', now()->subDays(3));
        $mid = $this->makeSuccess('daily', now()->subDays(2));
        $new = $this->makeSuccess('daily', now()->subDay());

        app(DatabaseBackupService::class)->applyRetention('daily');

        $this->assertDatabaseMissing('backup_histories', ['id' => $old->id]);
        $this->assertDatabaseHas('backup_histories', ['id' => $mid->id]);
        $this->assertDatabaseHas('backup_histories', ['id' => $new->id]);
        Storage::disk(config('backup.disk'))->assertMissing($old->path);
        Storage::disk(config('backup.disk'))->assertExists($new->path);
    }

    public function test_retention_of_one_keeps_only_the_single_newest_backup()
    {
        BackupSetting::current()->update(['interval_retention' => 1]);

        $this->makeSuccess('interval', now()->subMinutes(60));
        $this->makeSuccess('interval', now()->subMinutes(30));
        $newest = $this->makeSuccess('interval', now());

        app(DatabaseBackupService::class)->applyRetention('interval');

        $this->assertSame(1, BackupHistory::type('interval')->count());
        $this->assertDatabaseHas('backup_histories', ['id' => $newest->id]);
    }

    /** 6. Retention must never mix Daily and Interval backups. */
    public function test_retention_never_mixes_daily_and_interval_backups()
    {
        BackupSetting::current()->update(['daily_retention' => 1, 'interval_retention' => 1]);

        $dailyOld = $this->makeSuccess('daily', now()->subDays(2));
        $dailyNew = $this->makeSuccess('daily', now()->subDay());
        $intervalOld = $this->makeSuccess('interval', now()->subHour());
        $intervalNew = $this->makeSuccess('interval', now());

        app(DatabaseBackupService::class)->applyRetention('daily');

        // Interval backups are completely untouched by a daily retention run.
        $this->assertDatabaseHas('backup_histories', ['id' => $intervalOld->id]);
        $this->assertDatabaseHas('backup_histories', ['id' => $intervalNew->id]);
        $this->assertDatabaseMissing('backup_histories', ['id' => $dailyOld->id]);
        $this->assertDatabaseHas('backup_histories', ['id' => $dailyNew->id]);
    }

    /** Failed attempts carry no file and are left alone by retention (audit trail). */
    public function test_retention_does_not_touch_failed_backups()
    {
        BackupSetting::current()->update(['daily_retention' => 1]);

        $failed = BackupHistory::create([
            'type' => 'daily',
            'disk' => config('backup.disk'),
            'status' => BackupHistory::STATUS_FAILED,
            'error_message' => 'boom',
            'generated_at' => now()->subDays(5),
        ]);
        $this->makeSuccess('daily', now());

        app(DatabaseBackupService::class)->applyRetention('daily');

        $this->assertDatabaseHas('backup_histories', ['id' => $failed->id]);
    }
}
