<?php

namespace Tests\Feature\FileManager;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\FileManagerItem;
use App\Models\FileManagerQuota;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileManagerTrashFavoritesTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        Storage::fake(config('file-manager.disk'));
    }

    protected function staffWithPermissions(array $permissions = []): Admin
    {
        $admin = Admin::factory()->create();

        Adminpermission::factory()->create(array_merge([
            'staff_id' => $admin->id,
        ], $permissions));

        return $admin;
    }

    protected function superAdmin(): Admin
    {
        return Admin::factory()->superAdmin()->create();
    }

    protected function makeFile(Admin $owner, array $overrides = []): FileManagerItem
    {
        return FileManagerItem::create(array_merge([
            'owner_id' => $owner->id, 'owner_user_type' => 'admin', 'type' => 'file',
            'name' => 'file.txt', 'storage_path' => 'x/file.txt', 'size' => 10,
            'disk' => config('file-manager.disk'), 'created_by' => $owner->id, 'updated_by' => $owner->id,
        ], $overrides));
    }

    /** Restore: a trashed item owned by the actor comes back out of Trash. */
    public function test_restore_works()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'delete_file_manager' => true]);
        $item = $this->makeFile($admin);
        $item->delete();

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.file_manager.restore'), ['id' => $item->id]);

        $response->assertOk();
        $this->assertDatabaseHas('file_manager_items', ['id' => $item->id, 'deleted_at' => null]);
    }

    /** Restoring a folder also restores the descendants trashed with it. */
    public function test_restore_folder_restores_descendants()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'delete_file_manager' => true]);
        $folder = FileManagerItem::create([
            'owner_id' => $admin->id, 'owner_user_type' => 'admin', 'type' => 'folder',
            'name' => 'Docs', 'size' => 0, 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $child = $this->makeFile($admin, ['parent_id' => $folder->id, 'name' => 'inside.txt']);
        $folder->delete();
        $child->delete();

        $this->actingAs($admin, 'admin')
            ->postJson(route('admin.file_manager.restore'), ['id' => $folder->id])
            ->assertOk();

        $this->assertDatabaseHas('file_manager_items', ['id' => $child->id, 'deleted_at' => null]);
    }

    /** A staff member without delete_file_manager cannot restore their own trashed file. */
    public function test_staff_without_delete_permission_cannot_restore()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'delete_file_manager' => false]);
        $item = $this->makeFile($admin);
        $item->delete();

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.file_manager.restore'), ['id' => $item->id]);

        $response->assertStatus(403);
        $this->assertSoftDeleted('file_manager_items', ['id' => $item->id]);
    }

    /** Permanent delete: a super admin can purge anyone's trash. */
    public function test_super_admin_can_permanently_delete_trashed_item()
    {
        $owner = $this->staffWithPermissions(['file_manager' => true, 'delete_file_manager' => true]);
        $superAdmin = $this->superAdmin();
        $item = $this->makeFile($owner);
        Storage::disk(config('file-manager.disk'))->put('x/file.txt', 'data');
        $item->delete();

        $response = $this->actingAs($superAdmin, 'admin')
            ->postJson(route('admin.file_manager.force_delete'), ['id' => $item->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('file_manager_items', ['id' => $item->id]);
    }

    /** An owner with delete_file_manager can permanently delete their own trashed item, without needing manage_all. */
    public function test_owner_with_delete_permission_can_permanently_delete_own_item()
    {
        $owner = $this->staffWithPermissions(['file_manager' => true, 'delete_file_manager' => true]);
        $item = $this->makeFile($owner);
        Storage::disk(config('file-manager.disk'))->put('x/file.txt', 'data');
        $item->delete();

        $response = $this->actingAs($owner, 'admin')
            ->postJson(route('admin.file_manager.force_delete'), ['id' => $item->id]);

        $response->assertOk();
        $this->assertDatabaseMissing('file_manager_items', ['id' => $item->id]);
    }

    /** An owner without delete_file_manager cannot permanently delete their own trashed item. */
    public function test_owner_without_delete_permission_cannot_permanently_delete_own_item()
    {
        $owner = $this->staffWithPermissions(['file_manager' => true, 'delete_file_manager' => false]);
        $item = $this->makeFile($owner);
        // Trashed by a manager on the owner's behalf, since the owner themselves
        // lacks delete_file_manager and couldn't have trashed it directly.
        $item->delete();

        $response = $this->actingAs($owner, 'admin')
            ->postJson(route('admin.file_manager.force_delete'), ['id' => $item->id]);

        $response->assertStatus(403);
        $this->assertSoftDeleted('file_manager_items', ['id' => $item->id]);
    }

    /** A different staff member (not the owner, no manage_all) cannot permanently delete someone else's item. */
    public function test_non_owner_without_manage_all_cannot_permanently_delete()
    {
        $owner = $this->staffWithPermissions(['file_manager' => true, 'delete_file_manager' => true]);
        $stranger = $this->staffWithPermissions(['file_manager' => true, 'delete_file_manager' => true]);
        $item = $this->makeFile($owner);
        $item->delete();

        $response = $this->actingAs($stranger, 'admin')
            ->postJson(route('admin.file_manager.force_delete'), ['id' => $item->id]);

        $response->assertStatus(403);
        $this->assertSoftDeleted('file_manager_items', ['id' => $item->id]);
    }

    /** Favorite toggle works and is reflected back in the response. */
    public function test_favorite_toggle_works()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true]);
        $item = $this->makeFile($admin);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.file_manager.favorite'), ['id' => $item->id]);

        $response->assertOk()->assertJson(['status' => 'success', 'favorite' => true]);
        $this->assertDatabaseHas('file_manager_items', ['id' => $item->id, 'favorite' => 1]);
    }

    /** Bulk / drag-and-drop move relocates every authorized item in one call. */
    public function test_bulk_move_relocates_multiple_items()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'move_file_manager' => true]);
        $destination = FileManagerItem::create([
            'owner_id' => $admin->id, 'owner_user_type' => 'admin', 'type' => 'folder',
            'name' => 'Destination', 'size' => 0, 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $a = $this->makeFile($admin, ['name' => 'a.txt', 'storage_path' => 'x/a.txt']);
        $b = $this->makeFile($admin, ['name' => 'b.txt', 'storage_path' => 'x/b.txt']);

        $response = $this->actingAs($admin, 'admin')->postJson(route('admin.file_manager.bulk_move'), [
            'ids' => [$a->id, $b->id],
            'parent_id' => $destination->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('file_manager_items', ['id' => $a->id, 'parent_id' => $destination->id]);
        $this->assertDatabaseHas('file_manager_items', ['id' => $b->id, 'parent_id' => $destination->id]);
    }

    /** The FileManagerItemPolicy is registered and reachable through Gate directly. */
    public function test_policy_is_registered_and_denies_cross_owner_download()
    {
        $owner = $this->staffWithPermissions(['file_manager' => true]);
        $stranger = $this->staffWithPermissions(['file_manager' => true, 'download_file_manager' => true]);
        $item = $this->makeFile($owner);

        $this->assertTrue(Gate::forUser($owner)->allows('view', $item));
        $this->assertFalse(Gate::forUser($stranger)->allows('download', $item));
    }
}
