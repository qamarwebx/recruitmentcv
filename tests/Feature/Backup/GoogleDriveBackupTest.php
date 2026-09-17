<?php

namespace Tests\Feature\Backup;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\BackupDriveHistory;
use App\Models\BackupGoogleAccount;
use App\Models\BackupHistory;
use App\Models\BackupSetting;
use App\Services\Backup\Contracts\DriveTokenRefresher;
use App\Services\Backup\Contracts\DriveUploader;
use App\Services\Backup\Contracts\DumpRunner;
use App\Services\Backup\GoogleDriveService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\Support\FailingDriveUploader;
use Tests\Support\FakeDriveTokenRefresher;
use Tests\Support\FakeDriveUploader;
use Tests\Support\FakeDumpRunner;
use Tests\TestCase;

/**
 * QUEUE_CONNECTION=sync in phpunit.xml, so UploadBackupToGoogleDriveJob
 * (dispatched by DatabaseBackupService::run() right after a local backup
 * succeeds) runs inline within the same request - no queue worker needed.
 * The real Google Drive API/OAuth calls are swapped for Tests\Support
 * fakes via the container so these tests never hit Google.
 */
class GoogleDriveBackupTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        Storage::fake(config('backup.disk'));
        $this->app->bind(DumpRunner::class, FakeDumpRunner::class);
        $this->app->bind(DriveUploader::class, FakeDriveUploader::class);
        $this->app->bind(DriveTokenRefresher::class, FakeDriveTokenRefresher::class);
        FakeDriveUploader::$deleted = [];
    }

    private function adminUrl(string $routeName, ...$params): string
    {
        return 'https://crm.qamarhire.com'.parse_url(route($routeName, ...$params), PHP_URL_PATH);
    }

    protected function superAdmin(): Admin
    {
        return Admin::factory()->superAdmin()->create();
    }

    protected function connectedAccount(array $overrides = []): BackupGoogleAccount
    {
        $account = BackupGoogleAccount::current();
        $account->update(array_merge([
            'enabled' => true,
            'google_email' => 'backup@example.com',
            'google_name' => 'Backup Bot',
            'access_token' => 'fake-access-token',
            'refresh_token' => 'fake-refresh-token',
            'token_expires_at' => now()->addHour(),
            'status' => BackupGoogleAccount::STATUS_CONNECTED,
        ], $overrides));

        return $account->fresh();
    }

    protected function makeLocalSuccess(string $type): BackupHistory
    {
        $dir = config("backup.directories.{$type}");
        $filename = 'db_backup_'.$type.'_'.now()->format('YmdHis').'_'.uniqid().'.sql.gz';
        $path = $dir.'/'.$filename;

        Storage::disk(config('backup.disk'))->put($path, 'dummy');

        return BackupHistory::create([
            'type' => $type,
            'disk' => config('backup.disk'),
            'path' => $path,
            'filename' => $filename,
            'file_size' => 5,
            'status' => BackupHistory::STATUS_SUCCESS,
            'generated_at' => now(),
        ]);
    }

    /** 1 & 2. Manual DB + Drive backup: local backup triggers a Drive upload when enabled+connected. */
    public function test_manual_backup_uploads_to_drive_when_enabled_and_connected()
    {
        $admin = $this->superAdmin();
        $this->connectedAccount();

        $this->actingAs($admin, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.generate', 'daily')
        )->assertOk();

        $backup = BackupHistory::type('daily')->successful()->first();
        $this->assertNotNull($backup);

        $drive = BackupDriveHistory::where('backup_history_id', $backup->id)->first();
        $this->assertNotNull($drive);
        $this->assertSame(BackupDriveHistory::STATUS_UPLOADED, $drive->status);
        $this->assertNotEmpty($drive->drive_file_id);
        $this->assertNotEmpty($drive->drive_view_link);
        $this->assertSame('backup@example.com', $drive->google_account_email);
    }

    public function test_backup_is_not_uploaded_when_drive_backup_is_disabled()
    {
        $admin = $this->superAdmin();
        $this->connectedAccount(['enabled' => false]);

        $this->actingAs($admin, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.generate', 'daily')
        )->assertOk();

        $this->assertSame(0, BackupDriveHistory::count());
    }

    public function test_backup_is_not_uploaded_when_drive_is_not_connected()
    {
        $admin = $this->superAdmin();
        BackupGoogleAccount::current()->update(['enabled' => true]); // no refresh token -> not connected

        $this->actingAs($admin, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.generate', 'daily')
        )->assertOk();

        $this->assertSame(0, BackupDriveHistory::count());
    }

    /** 8 & 9. A failed Drive upload never affects the already-successful local backup. */
    public function test_failed_drive_upload_is_recorded_but_local_backup_stays_successful()
    {
        $admin = $this->superAdmin();
        $this->connectedAccount();
        $this->app->bind(DriveUploader::class, FailingDriveUploader::class);

        $this->actingAs($admin, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.generate', 'daily')
        )->assertOk();

        $backup = BackupHistory::type('daily')->successful()->first();
        $this->assertNotNull($backup);
        Storage::disk(config('backup.disk'))->assertExists($backup->path);

        $drive = BackupDriveHistory::where('backup_history_id', $backup->id)->first();
        $this->assertNotNull($drive);
        $this->assertSame(BackupDriveHistory::STATUS_FAILED, $drive->status);
        $this->assertStringContainsString('Simulated Google Drive upload failure', $drive->error_message);
    }

    /** 16. Retry protection: uploading the same backup twice never creates a duplicate row or re-uploads. */
    public function test_uploading_the_same_backup_twice_is_idempotent()
    {
        $this->connectedAccount();
        $backup = $this->makeLocalSuccess('daily');

        $service = app(GoogleDriveService::class);
        $first = $service->upload($backup);
        $second = $service->upload($backup);

        $this->assertSame($first->id, $second->id);
        $this->assertSame($first->drive_file_id, $second->drive_file_id);
        $this->assertSame(1, BackupDriveHistory::count());
    }

    /** Drive retention reuses the same per-type retention count as local backups. */
    public function test_drive_retention_keeps_only_the_configured_number_of_uploads()
    {
        BackupSetting::current()->update(['daily_retention' => 2]);
        $this->connectedAccount();

        $old = BackupDriveHistory::create([
            'backup_history_id' => $this->makeLocalSuccess('daily')->id,
            'backup_reference' => 'old.sql.gz',
            'type' => 'daily',
            'file_size' => 5,
            'google_account_email' => 'backup@example.com',
            'drive_file_id' => 'drive-old',
            'status' => BackupDriveHistory::STATUS_UPLOADED,
            'uploaded_at' => now()->subDays(3),
        ]);
        $mid = BackupDriveHistory::create([
            'backup_history_id' => $this->makeLocalSuccess('daily')->id,
            'backup_reference' => 'mid.sql.gz',
            'type' => 'daily',
            'file_size' => 5,
            'google_account_email' => 'backup@example.com',
            'drive_file_id' => 'drive-mid',
            'status' => BackupDriveHistory::STATUS_UPLOADED,
            'uploaded_at' => now()->subDays(2),
        ]);
        $new = BackupDriveHistory::create([
            'backup_history_id' => $this->makeLocalSuccess('daily')->id,
            'backup_reference' => 'new.sql.gz',
            'type' => 'daily',
            'file_size' => 5,
            'google_account_email' => 'backup@example.com',
            'drive_file_id' => 'drive-new',
            'status' => BackupDriveHistory::STATUS_UPLOADED,
            'uploaded_at' => now()->subDay(),
        ]);

        app(GoogleDriveService::class)->applyRetention('daily');

        $this->assertDatabaseMissing('backup_drive_histories', ['id' => $old->id]);
        $this->assertDatabaseHas('backup_drive_histories', ['id' => $mid->id]);
        $this->assertDatabaseHas('backup_drive_histories', ['id' => $new->id]);
        $this->assertContains('drive-old', FakeDriveUploader::$deleted);
    }

    /** 14. Delete a Drive backup via the management table. */
    public function test_can_delete_a_drive_backup()
    {
        $admin = $this->superAdmin();
        $this->connectedAccount();

        $history = BackupDriveHistory::create([
            'backup_reference' => 'file.sql.gz',
            'type' => 'daily',
            'file_size' => 5,
            'google_account_email' => 'backup@example.com',
            'drive_file_id' => 'drive-to-delete',
            'status' => BackupDriveHistory::STATUS_UPLOADED,
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.drive.delete', $history->id)
        );

        $response->assertOk();
        $this->assertDatabaseMissing('backup_drive_histories', ['id' => $history->id]);
        $this->assertContains('drive-to-delete', FakeDriveUploader::$deleted);
    }

    public function test_drive_delete_requires_permission()
    {
        $history = BackupDriveHistory::create([
            'backup_reference' => 'file.sql.gz',
            'type' => 'daily',
            'status' => BackupDriveHistory::STATUS_UPLOADED,
        ]);

        $staff = Admin::factory()->create();
        Adminpermission::factory()->create(['staff_id' => $staff->id]);

        $response = $this->actingAs($staff, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.drive.delete', $history->id)
        );

        $response->assertStatus(403);
        $this->assertDatabaseHas('backup_drive_histories', ['id' => $history->id]);
    }

    /** Enable/disable toggle is permission-gated and persists. */
    public function test_save_drive_enabled_toggle()
    {
        $admin = $this->superAdmin();
        $this->connectedAccount(['enabled' => false]);

        $response = $this->actingAs($admin, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.drive.enabled'),
            ['enabled' => 1]
        );

        $response->assertOk();
        $this->assertTrue(BackupGoogleAccount::current()->enabled);
    }

    public function test_save_drive_enabled_requires_permission()
    {
        $staff = Admin::factory()->create();
        Adminpermission::factory()->create(['staff_id' => $staff->id]);

        $response = $this->actingAs($staff, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.drive.enabled'),
            ['enabled' => 1]
        );

        $response->assertStatus(403);
    }

    /** Test Connection surfaces success/failure without ever exposing tokens. */
    public function test_test_connection_endpoint_reports_success()
    {
        $admin = $this->superAdmin();
        $this->connectedAccount();

        $response = $this->actingAs($admin, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.drive.test')
        );

        $response->assertOk()->assertJsonPath('status', 'ok');
        $this->assertArrayNotHasKey('access_token', $response->json('account'));
        $this->assertSame(BackupGoogleAccount::STATUS_CONNECTED, BackupGoogleAccount::current()->status);
    }

    public function test_test_connection_endpoint_reports_failure_and_marks_account_error()
    {
        $admin = $this->superAdmin();
        $this->connectedAccount();
        $this->app->bind(DriveUploader::class, FailingDriveUploader::class);

        $response = $this->actingAs($admin, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.drive.test')
        );

        $response->assertStatus(422);
        $this->assertSame(BackupGoogleAccount::STATUS_ERROR, BackupGoogleAccount::current()->status);
    }

    /** Disconnect clears the stored credentials. */
    public function test_disconnect_clears_tokens_and_disables_the_feature()
    {
        $admin = $this->superAdmin();
        $this->connectedAccount();

        $response = $this->actingAs($admin, 'admin')->postJson(
            $this->adminUrl('admin.settings.db_backup.drive.disconnect')
        );

        $response->assertOk();
        $account = BackupGoogleAccount::current();
        $this->assertSame(BackupGoogleAccount::STATUS_DISCONNECTED, $account->status);
        $this->assertFalse($account->enabled);
        $this->assertNull($account->access_token);
        $this->assertNull($account->refresh_token);
    }

    /** An expired access token is refreshed transparently before use. */
    public function test_expired_access_token_is_refreshed_before_upload()
    {
        $this->connectedAccount(['token_expires_at' => now()->subMinute()]);
        $backup = $this->makeLocalSuccess('daily');

        app(GoogleDriveService::class)->upload($backup);

        $this->assertSame('fake-refreshed-access-token', BackupGoogleAccount::current()->access_token);
    }

    public function test_drive_json_lists_uploaded_backups()
    {
        $admin = $this->superAdmin();
        $this->connectedAccount();

        BackupDriveHistory::create([
            'backup_reference' => 'file.sql.gz',
            'type' => 'daily',
            'file_size' => 5,
            'google_account_email' => 'backup@example.com',
            'drive_file_id' => 'drive-1',
            'drive_view_link' => 'https://drive.google.com/file/d/drive-1/view',
            'status' => BackupDriveHistory::STATUS_UPLOADED,
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')->getJson(
            $this->adminUrl('admin.settings.db_backup.drive.json')
        );

        $response->assertOk();
        $this->assertSame(1, $response->json('total'));
        $this->assertSame('https://drive.google.com/file/d/drive-1/view', $response->json('data.0.drive_view_link'));
    }
}
