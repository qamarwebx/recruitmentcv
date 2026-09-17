<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\FileManagerItem;
use App\Services\FileManagerService;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Resource-level authorization for File Manager items. The underlying
 * permission matrix (adminpermissions columns) is this app's single
 * existing permission system - this policy is a thin Gate-facing wrapper
 * around FileManagerService::can()/canManageAll() so controllers can use
 * $this->authorize() while the permission source of truth stays in one
 * place (also used internally by the service and the artisan command).
 */
class FileManagerItemPolicy
{
    use HandlesAuthorization;

    public function __construct(protected FileManagerService $service)
    {
    }

    protected function check(Admin $actor, FileManagerItem $item, string $ability): bool
    {
        if (!$this->service->can($actor, $ability)) {
            return false;
        }

        return (int) $item->owner_id === (int) $actor->id || $this->service->canManageAll($actor);
    }

    public function view(Admin $actor, FileManagerItem $item)
    {
        return $this->check($actor, $item, 'view');
    }

    public function download(Admin $actor, FileManagerItem $item)
    {
        return $this->check($actor, $item, 'download');
    }

    public function preview(Admin $actor, FileManagerItem $item)
    {
        return $this->check($actor, $item, 'preview');
    }

    public function rename(Admin $actor, FileManagerItem $item)
    {
        return $this->check($actor, $item, 'rename');
    }

    public function move(Admin $actor, FileManagerItem $item)
    {
        return $this->check($actor, $item, 'move');
    }

    public function delete(Admin $actor, FileManagerItem $item)
    {
        return $this->check($actor, $item, 'delete');
    }

    public function restore(Admin $actor, FileManagerItem $item)
    {
        return $this->check($actor, $item, 'delete');
    }

    /**
     * Permanent deletion: admins/managers can purge anyone's trash; a
     * staff member with delete_file_manager can also purge their own.
     */
    public function forceDelete(Admin $actor, FileManagerItem $item)
    {
        return $this->service->canForceDelete($actor, $item);
    }

    public function favorite(Admin $actor, FileManagerItem $item)
    {
        return $this->check($actor, $item, 'view');
    }

    public function createFolder(Admin $actor)
    {
        return $this->service->can($actor, 'create_folder');
    }

    public function upload(Admin $actor)
    {
        return $this->service->can($actor, 'upload');
    }

    /**
     * Settings > File Manager (storage/quota administration) is admin-only.
     */
    public function manageQuota(Admin $actor)
    {
        return (int) $actor->user_type === 1;
    }
}
