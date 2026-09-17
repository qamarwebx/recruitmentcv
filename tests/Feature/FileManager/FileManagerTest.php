<?php

namespace Tests\Feature\FileManager;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\FileManagerItem;
use App\Models\FileManagerQuota;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Uses DatabaseTransactions (not RefreshDatabase) - this app's production
 * database is also its only configured connection, so these tests assume
 * the file_manager_* migrations have already been applied and simply wrap
 * each test in a transaction that is rolled back afterwards.
 */
class FileManagerTest extends TestCase
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

    protected function giveQuota(Admin $admin, int $bytes): FileManagerQuota
    {
        return FileManagerQuota::create([
            'admin_id' => $admin->id,
            'allocated_bytes' => $bytes,
            'status' => 'active',
        ]);
    }

    /** 1. User can create folder with permission. */
    public function test_user_can_create_folder_with_permission()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'add_file_manager' => true]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.file_manager.folder.create'), ['name' => 'My Folder']);

        $response->assertOk()->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('file_manager_items', [
            'owner_id' => $admin->id,
            'name' => 'My Folder',
            'type' => 'folder',
        ]);
    }

    /** 2. User cannot create folder without permission. */
    public function test_user_cannot_create_folder_without_permission()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'add_file_manager' => false]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.file_manager.folder.create'), ['name' => 'Nope']);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('file_manager_items', ['name' => 'Nope']);
    }

    /** 3. User can upload within quota. */
    public function test_user_can_upload_within_quota()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'upload_file_manager' => true]);
        $this->giveQuota($admin, 10 * 1024 * 1024); // 10 MB

        $file = UploadedFile::fake()->create('doc.pdf', 1024, 'application/pdf'); // 1 MB

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.file_manager.upload'), ['files' => [$file]]);

        $response->assertOk()->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('file_manager_items', [
            'owner_id' => $admin->id,
            'type' => 'file',
            'original_name' => 'doc.pdf',
        ]);
    }

    /** 4. Upload fails when quota exceeded. */
    public function test_upload_fails_when_quota_exceeded()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'upload_file_manager' => true]);
        $this->giveQuota($admin, 5 * 1024 * 1024); // 5 MB total
        // Simulate 4.8 MB already used.
        FileManagerItem::create([
            'owner_id' => $admin->id, 'owner_user_type' => 'admin', 'type' => 'file',
            'name' => 'existing.pdf', 'storage_path' => 'x/existing.pdf', 'size' => (int) (4.8 * 1024 * 1024),
            'disk' => config('file-manager.disk'), 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        $file = UploadedFile::fake()->create('new.pdf', 300, 'application/pdf'); // 300 KB pushes total over 5MB

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.file_manager.upload'), ['files' => [$file]]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('file_manager_items', ['original_name' => 'new.pdf']);
    }

    /** 5. Multiple upload quota is enforced (whole batch validated up front). */
    public function test_batch_upload_quota_is_enforced_for_total_size()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'upload_file_manager' => true]);
        $this->giveQuota($admin, 2 * 1024 * 1024); // 2 MB

        $files = [
            UploadedFile::fake()->create('a.txt', 800, 'text/plain'),
            UploadedFile::fake()->create('b.txt', 800, 'text/plain'),
            UploadedFile::fake()->create('c.txt', 800, 'text/plain'), // 3 files * 800KB = 2.4MB > 2MB
        ];

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.file_manager.upload'), ['files' => $files]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('file_manager_items', ['original_name' => 'a.txt']);
        $this->assertDatabaseMissing('file_manager_items', ['original_name' => 'b.txt']);
        $this->assertDatabaseMissing('file_manager_items', ['original_name' => 'c.txt']);
    }

    /** 6. User cannot download another user's private file. */
    public function test_user_cannot_download_another_users_file()
    {
        $owner = $this->staffWithPermissions(['file_manager' => true]);
        $stranger = $this->staffWithPermissions(['file_manager' => true, 'download_file_manager' => true]);

        $item = FileManagerItem::create([
            'owner_id' => $owner->id, 'owner_user_type' => 'admin', 'type' => 'file',
            'name' => 'secret.pdf', 'storage_path' => 'x/secret.pdf', 'size' => 100,
            'disk' => config('file-manager.disk'), 'created_by' => $owner->id, 'updated_by' => $owner->id,
        ]);
        Storage::disk(config('file-manager.disk'))->put('x/secret.pdf', 'content');

        $response = $this->actingAs($stranger, 'admin')
            ->get(route('admin.file_manager.download', $item->id));

        $response->assertStatus(403);
    }

    /** 7. Admin with proper permission (manage_all) can access other users' files. */
    public function test_admin_with_manage_all_can_download_other_users_file()
    {
        $owner = $this->staffWithPermissions(['file_manager' => true]);
        $manager = $this->staffWithPermissions([
            'file_manager' => true, 'download_file_manager' => true, 'manage_all_file_manager' => true,
        ]);

        $item = FileManagerItem::create([
            'owner_id' => $owner->id, 'owner_user_type' => 'admin', 'type' => 'file',
            'name' => 'report.pdf', 'storage_path' => 'x/report.pdf', 'size' => 100,
            'disk' => config('file-manager.disk'), 'created_by' => $owner->id, 'updated_by' => $owner->id,
        ]);
        Storage::disk(config('file-manager.disk'))->put('x/report.pdf', 'content');

        $response = $this->actingAs($manager, 'admin')
            ->get(route('admin.file_manager.download', $item->id));

        $response->assertOk();
    }

    /** 8. Rename works. */
    public function test_rename_works()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'rename_file_manager' => true]);
        $folder = FileManagerItem::create([
            'owner_id' => $admin->id, 'owner_user_type' => 'admin', 'type' => 'folder',
            'name' => 'Old Name', 'size' => 0, 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.file_manager.rename'), ['id' => $folder->id, 'name' => 'New Name']);

        $response->assertOk();
        $this->assertDatabaseHas('file_manager_items', ['id' => $folder->id, 'name' => 'New Name']);
    }

    /** 9. Move works. */
    public function test_move_works()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'move_file_manager' => true]);
        $destination = FileManagerItem::create([
            'owner_id' => $admin->id, 'owner_user_type' => 'admin', 'type' => 'folder',
            'name' => 'Destination', 'size' => 0, 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $item = FileManagerItem::create([
            'owner_id' => $admin->id, 'owner_user_type' => 'admin', 'type' => 'file',
            'name' => 'file.txt', 'storage_path' => 'x/file.txt', 'size' => 10,
            'disk' => config('file-manager.disk'), 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.file_manager.move'), ['id' => $item->id, 'parent_id' => $destination->id]);

        $response->assertOk();
        $this->assertDatabaseHas('file_manager_items', ['id' => $item->id, 'parent_id' => $destination->id]);
    }

    /** 10. Circular folder move is prevented. */
    public function test_circular_folder_move_is_prevented()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'move_file_manager' => true]);
        $parent = FileManagerItem::create([
            'owner_id' => $admin->id, 'owner_user_type' => 'admin', 'type' => 'folder',
            'name' => 'Parent', 'size' => 0, 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $child = FileManagerItem::create([
            'owner_id' => $admin->id, 'owner_user_type' => 'admin', 'type' => 'folder', 'parent_id' => $parent->id,
            'name' => 'Child', 'size' => 0, 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.file_manager.move'), ['id' => $parent->id, 'parent_id' => $child->id]);

        $response->assertStatus(422);
        $this->assertDatabaseHas('file_manager_items', ['id' => $parent->id, 'parent_id' => null]);
    }

    /** 11. Delete works. */
    public function test_delete_works()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'delete_file_manager' => true]);
        $item = FileManagerItem::create([
            'owner_id' => $admin->id, 'owner_user_type' => 'admin', 'type' => 'file',
            'name' => 'file.txt', 'storage_path' => 'x/file.txt', 'size' => 10,
            'disk' => config('file-manager.disk'), 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.file_manager.delete'), ['id' => $item->id]);

        $response->assertOk()->assertJson(['status' => 'success']);
        $this->assertSoftDeleted('file_manager_items', ['id' => $item->id]);
    }

    /** 13. Invalid MIME/file type rejected. */
    public function test_invalid_file_type_is_rejected()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'upload_file_manager' => true]);
        $this->giveQuota($admin, 10 * 1024 * 1024);

        $file = UploadedFile::fake()->create('malware.exe', 10, 'application/x-msdownload');

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.file_manager.upload'), ['files' => [$file]]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('file_manager_items', ['original_name' => 'malware.exe']);
    }

    /** 14. Duplicate folder name prevented. */
    public function test_duplicate_folder_name_is_prevented()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'add_file_manager' => true]);
        FileManagerItem::create([
            'owner_id' => $admin->id, 'owner_user_type' => 'admin', 'type' => 'folder',
            'name' => 'Docs', 'size' => 0, 'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.file_manager.folder.create'), ['name' => 'Docs']);

        $response->assertStatus(422);
        $this->assertEquals(1, FileManagerItem::where('owner_id', $admin->id)->where('name', 'Docs')->count());
    }

    /**
     * 15. Concurrent quota uploads cannot exceed quota.
     *
     * True parallel requests cannot be exercised inside a single PHPUnit
     * process; what's tested here is that the quota check is re-evaluated
     * (against a locked quota row - see FileManagerQuotaService::lockQuota())
     * on every individual upload, so a second upload that would push the
     * user over quota is rejected even though it is validated independently
     * from the first. This is the same guard that serializes true concurrent
     * requests in production via SELECT ... FOR UPDATE.
     */
    public function test_sequential_uploads_cannot_exceed_quota()
    {
        $admin = $this->staffWithPermissions(['file_manager' => true, 'upload_file_manager' => true]);
        $this->giveQuota($admin, 500 * 1024); // 500 KB

        $first = UploadedFile::fake()->create('first.txt', 400, 'text/plain');
        $second = UploadedFile::fake()->create('second.txt', 400, 'text/plain');

        $responseOne = $this->actingAs($admin, 'admin')
            ->post(route('admin.file_manager.upload'), ['files' => [$first]]);
        $responseTwo = $this->actingAs($admin, 'admin')
            ->post(route('admin.file_manager.upload'), ['files' => [$second]]);

        $responseOne->assertOk();
        $responseTwo->assertStatus(422);
        $this->assertEquals(1, FileManagerItem::where('owner_id', $admin->id)->where('type', 'file')->count());
    }
}
