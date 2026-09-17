<?php

namespace Tests\Feature\Backup;

use App\Http\Middleware\VerifyCsrfToken;
use App\Jobs\RunDatabaseBackupJob;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\BackupHistory;
use App\Services\Backup\Contracts\DumpRunner;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\Support\FailingDumpRunner;
use Tests\Support\FakeDumpRunner;
use Tests\TestCase;

/**
 * QUEUE_CONNECTION=sync in phpunit.xml, so RunDatabaseBackupJob::dispatch()
 * (triggered by the "generate" endpoint) runs inline within the test
 * request - no queue worker needed. The real mysqldump/gzip pipeline is
 * swapped for Tests\Support\FakeDumpRunner via the container so these tests
 * never shell out or touch the real database dump.
 */
class BackupGenerationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        Storage::fake(config('backup.disk'));
        Cache::flush();
        $this->app->bind(DumpRunner::class, FakeDumpRunner::class);
    }

    /**
     * route() resolves against APP_URL (qamarhire.com), but
     * RedirectIfNotAdminDomain only lets /admin/* through on the
     * crm.qamarhire.com host - same fix as PayrollMarkPaidTest.
     */
    private function adminUrl(string $routeName, ...$params): string
    {
        return 'https://crm.qamarhire.com'.parse_url(route($routeName, ...$params), PHP_URL_PATH);
    }

    protected function superAdmin(): Admin
    {
        return Admin::factory()->superAdmin()->create();
    }

    /** 6. Manual Daily backup. */
    public function test_manual_daily_generate_creates_success_history_and_file()
    {
        $admin = $this->superAdmin();

        $response = $this->actingAs($admin, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.generate', 'daily')
        );

        $response->assertOk()->assertJson(['status' => 'queued']);

        $history = BackupHistory::type('daily')->successful()->first();
        $this->assertNotNull($history);
        $this->assertNotNull($history->path);
        Storage::disk(config('backup.disk'))->assertExists($history->path);
    }

    /** 7. Manual Interval backup - independent of Daily. */
    public function test_manual_interval_generate_creates_success_history_and_file()
    {
        $admin = $this->superAdmin();

        $response = $this->actingAs($admin, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.generate', 'interval')
        );

        $response->assertOk()->assertJson(['status' => 'queued']);

        $history = BackupHistory::type('interval')->successful()->first();
        $this->assertNotNull($history);
        Storage::disk(config('backup.disk'))->assertExists($history->path);
    }

    /** 10 & 11. Failed backup is recorded, and the previous successful backup survives. */
    public function test_failed_backup_records_failure_and_preserves_previous_backup()
    {
        $admin = $this->superAdmin();

        // First: a real success.
        $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.generate', 'daily'))->assertOk();
        $goodBackup = BackupHistory::type('daily')->successful()->first();
        $this->assertNotNull($goodBackup);

        // Second: force a failure. Run the job directly rather than through
        // the HTTP endpoint - in production (QUEUE_CONNECTION=database) a
        // job failure is isolated in the queue worker and never touches the
        // dispatching request; only phpunit.xml's QUEUE_CONNECTION=sync
        // (needed so the "happy path" assertions above can run inline)
        // would let it bubble into this request instead, which is a test
        // environment artifact, not real behavior worth asserting on.
        $this->app->bind(DumpRunner::class, FailingDumpRunner::class);
        try {
            (new RunDatabaseBackupJob('daily'))->handle(app(\App\Services\Backup\DatabaseBackupService::class));
        } catch (\Throwable $e) {
            // expected - the job rethrows so a real queue worker can retry it.
        }

        $failed = BackupHistory::type('daily')->where('status', 'failed')->first();
        $this->assertNotNull($failed);
        $this->assertStringContainsString('Simulated dump failure', $failed->error_message);
        $this->assertStringNotContainsString('password', strtolower($failed->error_message));

        // The original successful backup + its file must still be intact.
        $this->assertDatabaseHas('backup_histories', ['id' => $goodBackup->id, 'status' => 'success']);
        Storage::disk(config('backup.disk'))->assertExists($goodBackup->path);
    }

    /** 12 & 13. Duplicate scheduler trigger / concurrent backup of the same type is prevented. */
    public function test_generate_is_blocked_while_a_backup_of_the_same_type_is_already_busy()
    {
        $admin = $this->superAdmin();
        Cache::put('db-backup:daily:busy', true, now()->addMinutes(5));

        $response = $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.generate', 'daily'));

        $response->assertOk()->assertJson(['status' => 'already_running']);
        $this->assertSame(0, BackupHistory::type('daily')->count());
    }

    /** Daily and Interval must not block each other. */
    public function test_daily_busy_does_not_block_interval_generation()
    {
        $admin = $this->superAdmin();
        Cache::put('db-backup:daily:busy', true, now()->addMinutes(5));

        $response = $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.generate', 'interval'));

        $response->assertOk()->assertJson(['status' => 'queued']);
        $this->assertSame(1, BackupHistory::type('interval')->successful()->count());
    }

    /** A stale running lock (simulating another in-flight process) is respected by the job itself too. */
    public function test_job_skips_when_running_lock_is_already_held()
    {
        $lock = Cache::lock('db-backup:daily:running', 60);
        $lock->get();

        $job = new RunDatabaseBackupJob('daily');
        $job->handle(app(\App\Services\Backup\DatabaseBackupService::class));

        $this->assertSame(0, BackupHistory::type('daily')->count());
        $lock->release();
    }

    /** 15. Download. */
    public function test_can_download_a_successful_backup()
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.generate', 'daily'))->assertOk();
        $history = BackupHistory::type('daily')->successful()->first();

        $response = $this->actingAs($admin, 'admin')->get($this->adminUrl('admin.settings.db_backup.download', $history->id));

        $response->assertOk();
    }

    /** Manipulated/nonexistent IDs must not leak a 200 or any file. */
    public function test_download_404s_for_nonexistent_backup_id()
    {
        $admin = $this->superAdmin();

        $response = $this->actingAs($admin, 'admin')->get($this->adminUrl('admin.settings.db_backup.download', 999999));

        $response->assertStatus(404);
    }

    public function test_download_requires_permission()
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.generate', 'daily'))->assertOk();
        $history = BackupHistory::type('daily')->successful()->first();

        $staff = Admin::factory()->create();
        Adminpermission::factory()->create(['staff_id' => $staff->id]);

        $response = $this->actingAs($staff, 'admin')->get($this->adminUrl('admin.settings.db_backup.download', $history->id));

        $response->assertStatus(403);
    }

    /** 16. Delete. */
    public function test_can_delete_a_backup_and_its_file()
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.generate', 'daily'))->assertOk();
        $history = BackupHistory::type('daily')->successful()->first();
        $path = $history->path;

        $response = $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.delete', $history->id));

        $response->assertOk();
        $this->assertDatabaseMissing('backup_histories', ['id' => $history->id]);
        Storage::disk(config('backup.disk'))->assertMissing($path);
    }

    public function test_delete_requires_permission()
    {
        $admin = $this->superAdmin();
        $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.generate', 'daily'))->assertOk();
        $history = BackupHistory::type('daily')->successful()->first();

        $staff = Admin::factory()->create();
        Adminpermission::factory()->create(['staff_id' => $staff->id]);

        $response = $this->actingAs($staff, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.delete', $history->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('backup_histories', ['id' => $history->id]);
    }
}
