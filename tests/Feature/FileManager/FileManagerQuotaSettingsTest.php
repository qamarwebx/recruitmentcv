<?php

namespace Tests\Feature\FileManager;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\FileManagerItem;
use App\Models\FileManagerQuota;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FileManagerQuotaSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    protected function superAdmin(): Admin
    {
        return Admin::factory()->superAdmin()->create();
    }

    /** 12. Quota cannot be reduced below usage. */
    public function test_quota_cannot_be_reduced_below_current_usage()
    {
        $superAdmin = $this->superAdmin();
        $staff = Admin::factory()->create();

        $quota = FileManagerQuota::create([
            'admin_id' => $staff->id,
            'allocated_bytes' => 10 * 1024 * 1024 * 1024, // 10 GB
            'status' => 'active',
        ]);

        FileManagerItem::create([
            'owner_id' => $staff->id, 'owner_user_type' => 'admin', 'type' => 'file',
            'name' => 'big.zip', 'storage_path' => 'x/big.zip', 'size' => 7 * 1024 * 1024 * 1024, // 7 GB used
            'disk' => config('file-manager.disk'), 'created_by' => $staff->id, 'updated_by' => $staff->id,
        ]);

        $response = $this->actingAs($superAdmin, 'admin')->postJson(route('admin.settings.file_manager.quota.update'), [
            'admin_id' => $staff->id,
            'allocated_bytes' => 5 * 1024 * 1024 * 1024, // attempt 5 GB, below the 7 GB used
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseHas('file_manager_quotas', [
            'id' => $quota->id,
            'allocated_bytes' => 10 * 1024 * 1024 * 1024,
        ]);
    }

    public function test_quota_can_be_increased_above_usage()
    {
        $superAdmin = $this->superAdmin();
        $staff = Admin::factory()->create();

        FileManagerQuota::create([
            'admin_id' => $staff->id,
            'allocated_bytes' => 5 * 1024 * 1024 * 1024,
            'status' => 'active',
        ]);

        $response = $this->actingAs($superAdmin, 'admin')->postJson(route('admin.settings.file_manager.quota.update'), [
            'admin_id' => $staff->id,
            'allocated_bytes' => 8 * 1024 * 1024 * 1024,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('file_manager_quotas', [
            'admin_id' => $staff->id,
            'allocated_bytes' => 8 * 1024 * 1024 * 1024,
        ]);
    }

    /** Settings > File Manager is admin-only - no staff permission can unlock it. */
    public function test_staff_cannot_access_quota_settings()
    {
        $staff = Admin::factory()->create();
        Adminpermission::factory()->create(['staff_id' => $staff->id]);

        $response = $this->actingAs($staff, 'admin')->get(route('admin.settings.file_manager.index'));

        $response->assertStatus(403);
    }

    /** Not even a full-access or manage_all_file_manager staff member gets in - admin-only, not permission-gated. */
    public function test_full_access_staff_still_cannot_access_quota_settings()
    {
        $staff = Admin::factory()->create();
        Adminpermission::factory()->create([
            'staff_id' => $staff->id,
            'full_access' => true,
            'manage_all_file_manager' => true,
        ]);

        $response = $this->actingAs($staff, 'admin')->get(route('admin.settings.file_manager.index'));

        $response->assertStatus(403);
    }
}
