<?php

namespace Tests\Feature\Backup;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\BackupSetting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Uses DatabaseTransactions against the real configured DB (see
 * tests/Feature/FileManager/FileManagerTest.php for why) - every test rolls
 * back cleanly, and BackupSetting::current() creates the singleton row
 * inside that same transaction.
 */
class BackupSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
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

    protected function staffWithPermissions(array $permissions = []): Admin
    {
        $admin = Admin::factory()->create();
        Adminpermission::factory()->create(array_merge(['staff_id' => $admin->id], $permissions));

        return $admin;
    }

    /** 17. Permission restrictions - no permission row at all. */
    public function test_staff_without_permission_cannot_access_index()
    {
        $staff = Admin::factory()->create();
        Adminpermission::factory()->create(['staff_id' => $staff->id]);

        $response = $this->actingAs($staff, 'admin')->get($this->adminUrl('admin.settings.db_backup.index'));

        $response->assertStatus(403);
    }

    public function test_staff_with_db_backup_setting_can_access_index()
    {
        $staff = $this->staffWithPermissions(['db_backup_setting' => true]);

        $response = $this->actingAs($staff, 'admin')->get($this->adminUrl('admin.settings.db_backup.index'));

        $response->assertOk();
    }

    public function test_full_access_staff_can_access_index_without_explicit_flag()
    {
        $staff = $this->staffWithPermissions(['full_access' => true]);

        $response = $this->actingAs($staff, 'admin')->get($this->adminUrl('admin.settings.db_backup.index'));

        $response->assertOk();
    }

    public function test_superadmin_can_access_index()
    {
        $response = $this->actingAs($this->superAdmin(), 'admin')->get($this->adminUrl('admin.settings.db_backup.index'));

        $response->assertOk();
    }

    /** 1. Daily ON/OFF + different times + retention. */
    public function test_daily_settings_can_be_saved()
    {
        $admin = $this->superAdmin();

        $response = $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.daily.save'), [
            'daily_enabled' => 1,
            'daily_time' => '02:30',
            'daily_retention' => 4,
        ]);

        $response->assertOk();
        $settings = BackupSetting::current();
        $this->assertTrue((bool) $settings->daily_enabled);
        $this->assertSame('02:30', $settings->daily_time);
        $this->assertSame(4, $settings->daily_retention);
    }

    public function test_daily_time_must_be_a_valid_time_format()
    {
        $admin = $this->superAdmin();

        $response = $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.daily.save'), [
            'daily_enabled' => 1,
            'daily_time' => '27:99',
            'daily_retention' => 4,
        ]);

        $response->assertStatus(422);
    }

    /** Retention 1 is valid; 0 and negative are not. */
    public function test_daily_retention_must_be_a_positive_integer()
    {
        $admin = $this->superAdmin();

        $ok = $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.daily.save'), [
            'daily_enabled' => 0, 'daily_time' => '02:00', 'daily_retention' => 1,
        ]);
        $ok->assertOk();

        $bad = $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.daily.save'), [
            'daily_enabled' => 0, 'daily_time' => '02:00', 'daily_retention' => 0,
        ]);
        $bad->assertStatus(422);
    }

    /** 5. Every interval option is accepted; anything else is rejected. */
    public function test_interval_value_only_accepts_defined_options()
    {
        $admin = $this->superAdmin();

        foreach (array_keys(config('backup.interval_options')) as $minutes) {
            $response = $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.interval.save'), [
                'interval_enabled' => 1,
                'interval_value' => $minutes,
                'interval_retention' => 3,
            ]);
            $response->assertOk();
            $this->assertSame($minutes, BackupSetting::current()->interval_value);
        }

        $rejected = $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.interval.save'), [
            'interval_enabled' => 1,
            'interval_value' => 15, // not one of the allowed options
            'interval_retention' => 3,
        ]);
        $rejected->assertStatus(422);
    }

    /** Daily and Interval schedules must work completely independently. */
    public function test_daily_and_interval_settings_are_independent()
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.daily.save'), [
            'daily_enabled' => 1, 'daily_time' => '03:15', 'daily_retention' => 2,
        ])->assertOk();

        $this->actingAs($admin, 'admin')->postJson($this->adminUrl('admin.settings.db_backup.interval.save'), [
            'interval_enabled' => 0, 'interval_value' => 60, 'interval_retention' => 6,
        ])->assertOk();

        $settings = BackupSetting::current();
        $this->assertTrue((bool) $settings->daily_enabled);
        $this->assertSame('03:15', $settings->daily_time);
        $this->assertFalse((bool) $settings->interval_enabled);
        $this->assertSame(60, $settings->interval_value);
    }
}
