<?php

namespace App\Services;

use App\Exceptions\FileManagerException;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\FileManagerItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManagerService
{
    protected const ABILITY_COLUMNS = [
        'view' => 'file_manager',
        'create_folder' => 'add_file_manager',
        'upload' => 'upload_file_manager',
        'download' => 'download_file_manager',
        'preview' => 'preview_file_manager',
        'rename' => 'rename_file_manager',
        'move' => 'move_file_manager',
        'delete' => 'delete_file_manager',
        'manage_all' => 'manage_all_file_manager',
    ];

    public function __construct(protected FileManagerQuotaService $quota)
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | The app has no Gate/Policy layer anywhere else - every module checks
    | permissions inline via the flat `adminpermissions` table. These
    | methods centralize that same check so it isn't re-implemented in
    | every controller action, without introducing a new auth architecture.
    |
    */

    public function can(Admin $actor, string $ability): bool
    {
        if ((int) $actor->user_type === 1) {
            return true;
        }

        $permission = Adminpermission::where('staff_id', $actor->id)->first();

        if (!$permission) {
            return false;
        }

        if ((bool) $permission->full_access) {
            return true;
        }

        $column = self::ABILITY_COLUMNS[$ability] ?? null;

        return $column && (bool) ($permission->{$column} ?? false);
    }

    public function canManageAll(Admin $actor): bool
    {
        return $this->can($actor, 'manage_all');
    }

    /**
     * Permanent deletion from Trash: always allowed for a super admin or
     * manage_all_file_manager (any item), and additionally allowed for the
     * item's own owner as long as they hold delete_file_manager - the same
     * permission that let them send it to Trash in the first place. A staff
     * member without delete_file_manager could never trash the item, so
     * they can't purge it either.
     */
    public function canForceDelete(Admin $actor, FileManagerItem $item): bool
    {
        if ((int) $actor->user_type === 1 || $this->canManageAll($actor)) {
            return true;
        }

        return (int) $item->owner_id === (int) $actor->id && $this->can($actor, 'delete');
    }

    /**
     * Throws unless the actor both has the ability and either owns the item
     * or has manage_all_file_manager. Never trust an item id from the
     * frontend without this check.
     */
    public function authorizeItem(Admin $actor, FileManagerItem $item, string $ability): void
    {
        if (!$this->can($actor, $ability)) {
            throw new FileManagerException('Permission denied.', 403);
        }

        if ((int) $item->owner_id !== (int) $actor->id && !$this->canManageAll($actor)) {
            throw new FileManagerException('You do not have access to this item.', 403);
        }
    }

    /**
     * Resolves & authorizes the owner a new item should be created for.
     * Defaults to the actor; only an actor with manage_all may target
     * another owner (e.g. an admin creating inside a staff member's space).
     */
    public function resolveOwner(Admin $actor, ?int $requestedOwnerId): Admin
    {
        if (!$requestedOwnerId || (int) $requestedOwnerId === (int) $actor->id) {
            return $actor;
        }

        if (!$this->canManageAll($actor)) {
            throw new FileManagerException('You do not have access to manage this user\'s files.', 403);
        }

        $owner = Admin::find($requestedOwnerId);
        if (!$owner) {
            throw new FileManagerException('Owner not found.', 404);
        }

        return $owner;
    }

    /*
    |--------------------------------------------------------------------------
    | Listing
    |--------------------------------------------------------------------------
    */

    /**
     * Query scoped to what the actor is allowed to see: their own items,
     * or everyone's if they have manage_all_file_manager.
     */
    public function visibleQuery(Admin $actor): Builder
    {
        $query = FileManagerItem::query();

        if (!$this->canManageAll($actor)) {
            $query->where('owner_id', $actor->id);
        }

        return $query;
    }

    public function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['parent_id']) || array_key_exists('parent_id', $filters)) {
            $query->inFolder($filters['parent_id'] ?? null);
        }

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['type']) && in_array($filters['type'], [FileManagerItem::TYPE_FILE, FileManagerItem::TYPE_FOLDER], true)) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['extension'])) {
            $query->where('extension', strtolower($filters['extension']));
        }

        if (!empty($filters['mime_type'])) {
            $query->where('mime_type', $filters['mime_type']);
        }

        if (!empty($filters['owner_id'])) {
            $query->where('owner_id', $filters['owner_id']);
        }

        if (!empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['updated_from'])) {
            $query->whereDate('updated_at', '>=', $filters['updated_from']);
        }

        if (!empty($filters['updated_to'])) {
            $query->whereDate('updated_at', '<=', $filters['updated_to']);
        }

        if (isset($filters['size_from']) && $filters['size_from'] !== '') {
            $query->where('size', '>=', (int) $filters['size_from']);
        }

        if (isset($filters['size_to']) && $filters['size_to'] !== '') {
            $query->where('size', '<=', (int) $filters['size_to']);
        }

        switch ($filters['quick'] ?? null) {
            case 'images':
                $query->where('mime_type', 'like', 'image/%');
                break;
            case 'documents':
                $query->whereIn('extension', ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf', 'odt', 'ods']);
                break;
            case 'pdfs':
                $query->where('extension', 'pdf');
                break;
            case 'videos':
                $query->where('mime_type', 'like', 'video/%');
                break;
            case 'audio':
                $query->where('mime_type', 'like', 'audio/%');
                break;
            case 'archives':
                $query->whereIn('extension', ['zip', 'rar', '7z']);
                break;
            case 'my_files':
                $query->where('owner_id', $filters['actor_id'] ?? 0);
                break;
            case 'recently_updated':
                $query->where('updated_at', '>=', now()->subDays(7));
                break;
            case 'large_files':
                $query->where('size', '>=', 25 * 1024 * 1024);
                break;
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate names
    |--------------------------------------------------------------------------
    */

    protected function nameExists(int $ownerId, ?int $parentId, string $name, ?int $exceptId = null): bool
    {
        $query = FileManagerItem::inFolder($parentId)
            ->where('owner_id', $ownerId)
            ->whereRaw('LOWER(name) = ?', [Str::lower($name)]);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    /**
     * Appends " (n)" until the display name is unique in scope. Used for
     * uploads so one colliding filename in a batch doesn't fail the whole
     * batch.
     */
    protected function uniqueFileName(int $ownerId, ?int $parentId, string $name): string
    {
        if (!$this->nameExists($ownerId, $parentId, $name)) {
            return $name;
        }

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $base = $extension ? substr($name, 0, -(strlen($extension) + 1)) : $name;

        $i = 1;
        do {
            $candidate = $extension ? "{$base} ({$i}).{$extension}" : "{$base} ({$i})";
            $i++;
        } while ($this->nameExists($ownerId, $parentId, $candidate));

        return $candidate;
    }

    /*
    |--------------------------------------------------------------------------
    | Folders
    |--------------------------------------------------------------------------
    */

    public function findFolder(?int $parentId, Admin $actor): ?FileManagerItem
    {
        if (!$parentId) {
            return null;
        }

        $folder = FileManagerItem::folders()->find($parentId);

        if (!$folder) {
            throw new FileManagerException('Folder not found.', 404);
        }

        $this->authorizeItem($actor, $folder, 'view');

        return $folder;
    }

    public function createFolder(Admin $actor, ?int $parentId, string $name, ?int $requestedOwnerId = null): FileManagerItem
    {
        if (!$this->can($actor, 'create_folder')) {
            throw new FileManagerException('Permission denied.', 403);
        }

        $parent = $this->findFolder($parentId, $actor);
        $owner = $this->resolveOwner($actor, $requestedOwnerId ?? optional($parent)->owner_id);

        $name = trim($name);

        if ($this->nameExists($owner->id, $parentId, $name)) {
            throw new FileManagerException('A folder or file with this name already exists here.');
        }

        return FileManagerItem::create([
            'owner_id' => $owner->id,
            'owner_user_type' => 'admin',
            'parent_id' => $parentId,
            'type' => FileManagerItem::TYPE_FOLDER,
            'name' => $name,
            'size' => 0,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    /**
     * @param UploadedFile[] $files
     * @return FileManagerItem[]
     */
    public function upload(Admin $actor, ?int $parentId, array $files, ?int $requestedOwnerId = null): array
    {
        if (!$this->can($actor, 'upload')) {
            throw new FileManagerException('Permission denied.', 403);
        }

        if (empty($files)) {
            throw new FileManagerException('No files were provided.');
        }

        $maxBatch = (int) config('file-manager.max_batch_files');
        if (count($files) > $maxBatch) {
            throw new FileManagerException("A maximum of {$maxBatch} files may be uploaded at once.");
        }

        $parent = $this->findFolder($parentId, $actor);
        $owner = $this->resolveOwner($actor, $requestedOwnerId ?? optional($parent)->owner_id);

        $allowedExtensions = array_map('strtolower', config('file-manager.allowed_extensions'));
        $allowedMimes = config('file-manager.allowed_mimes');
        $maxSize = (int) config('file-manager.max_upload_size');

        $totalIncoming = 0;
        foreach ($files as $file) {
            if (!$file->isValid()) {
                throw new FileManagerException('One or more uploaded files failed to transfer.');
            }

            $extension = strtolower($file->getClientOriginalExtension());
            // Never trust the client-supplied extension or mime type alone;
            // getMimeType() inspects the file's actual content (fileinfo).
            $detectedMime = $file->getMimeType();

            if (!in_array($extension, $allowedExtensions, true) || !in_array($detectedMime, $allowedMimes, true)) {
                throw new FileManagerException("File type not allowed: {$file->getClientOriginalName()}");
            }

            if ($file->getSize() > $maxSize) {
                throw new FileManagerException("File too large: {$file->getClientOriginalName()} (max " . FileManagerQuotaService::humanReadable($maxSize) . ').');
            }

            $totalIncoming += $file->getSize();
        }

        return DB::transaction(function () use ($actor, $owner, $parentId, $files, $totalIncoming) {
            $lockedQuota = $this->quota->lockQuota($owner);

            $check = $this->quota->validateUploadFits($owner, $lockedQuota, $totalIncoming);
            if (!$check['ok']) {
                throw new FileManagerException($check['message']);
            }

            $created = [];

            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());
                $mimeType = $file->getMimeType();
                $size = $file->getSize();

                $displayName = $this->uniqueFileName($owner->id, $parentId, $originalName);

                $storageName = Str::uuid()->toString() . ($extension ? ".{$extension}" : '');
                $storagePath = $owner->id . '/' . $storageName;

                $stream = fopen($file->getRealPath(), 'r');
                Storage::disk(config('file-manager.disk'))->put($storagePath, $stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }

                $created[] = FileManagerItem::create([
                    'owner_id' => $owner->id,
                    'owner_user_type' => 'admin',
                    'parent_id' => $parentId,
                    'type' => FileManagerItem::TYPE_FILE,
                    'name' => $displayName,
                    'storage_path' => $storagePath,
                    'original_name' => $originalName,
                    'extension' => $extension,
                    'mime_type' => $mimeType,
                    'size' => $size,
                    'disk' => config('file-manager.disk'),
                    'created_by' => $actor->id,
                    'updated_by' => $actor->id,
                ]);
            }

            return $created;
        });
    }

    /**
     * Handles a same-slot replace/overwrite: quota should only move by the
     * delta between old and new size, not the full new size.
     */
    public function replace(Admin $actor, FileManagerItem $item, UploadedFile $file): FileManagerItem
    {
        $this->authorizeItem($actor, $item, 'upload');

        if (!$item->isFile()) {
            throw new FileManagerException('Only files can be replaced.');
        }

        $allowedExtensions = array_map('strtolower', config('file-manager.allowed_extensions'));
        $allowedMimes = config('file-manager.allowed_mimes');
        $maxSize = (int) config('file-manager.max_upload_size');

        $extension = strtolower($file->getClientOriginalExtension());
        $detectedMime = $file->getMimeType();

        if (!in_array($extension, $allowedExtensions, true) || !in_array($detectedMime, $allowedMimes, true)) {
            throw new FileManagerException('File type not allowed.');
        }

        if ($file->getSize() > $maxSize) {
            throw new FileManagerException('File too large (max ' . FileManagerQuotaService::humanReadable($maxSize) . ').');
        }

        $owner = $item->owner;
        $delta = $file->getSize() - $item->size;

        return DB::transaction(function () use ($actor, $item, $owner, $file, $extension, $detectedMime, $delta) {
            $lockedQuota = $this->quota->lockQuota($owner);

            if ($delta > 0) {
                $check = $this->quota->validateUploadFits($owner, $lockedQuota, $delta);
                if (!$check['ok']) {
                    throw new FileManagerException($check['message']);
                }
            }

            $oldPath = $item->storage_path;
            $storageName = Str::uuid()->toString() . ($extension ? ".{$extension}" : '');
            $storagePath = $owner->id . '/' . $storageName;

            $stream = fopen($file->getRealPath(), 'r');
            Storage::disk($item->disk)->put($storagePath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }

            $item->update([
                'storage_path' => $storagePath,
                'original_name' => $file->getClientOriginalName(),
                'extension' => $extension,
                'mime_type' => $detectedMime,
                'size' => $file->getSize(),
                'updated_by' => $actor->id,
            ]);

            if ($oldPath) {
                Storage::disk($item->disk)->delete($oldPath);
            }

            return $item->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Rename
    |--------------------------------------------------------------------------
    */

    public function rename(Admin $actor, FileManagerItem $item, string $newName): FileManagerItem
    {
        $this->authorizeItem($actor, $item, 'rename');

        $newName = trim($newName);
        if ($newName === '') {
            throw new FileManagerException('Name cannot be empty.');
        }

        if ($this->nameExists($item->owner_id, $item->parent_id, $newName, $item->id)) {
            throw new FileManagerException('A folder or file with this name already exists here.');
        }

        $item->update(['name' => $newName, 'updated_by' => $actor->id]);

        return $item->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | Move
    |--------------------------------------------------------------------------
    */

    public function move(Admin $actor, FileManagerItem $item, ?int $newParentId): FileManagerItem
    {
        $this->authorizeItem($actor, $item, 'move');

        if ($newParentId === $item->id) {
            throw new FileManagerException('Cannot move a folder into itself.');
        }

        $destination = $this->findFolder($newParentId, $actor);

        if ($item->isFolder() && $newParentId) {
            $this->assertNotDescendant($item, $newParentId);
        }

        if ($destination && (int) $destination->owner_id !== (int) $item->owner_id) {
            throw new FileManagerException('Cannot move an item to a different owner\'s folder.');
        }

        if ($this->nameExists($item->owner_id, $newParentId, $item->name, $item->id)) {
            throw new FileManagerException('A folder or file with this name already exists in the destination.');
        }

        $item->update(['parent_id' => $newParentId, 'updated_by' => $actor->id]);

        return $item->fresh();
    }

    /**
     * Walks up from $candidateParentId toward the root; throws if it ever
     * reaches $item, which would create a circular hierarchy.
     */
    protected function assertNotDescendant(FileManagerItem $item, int $candidateParentId): void
    {
        $currentId = $candidateParentId;
        $depth = 0;

        while ($currentId !== null && $depth < 1000) {
            if ((int) $currentId === (int) $item->id) {
                throw new FileManagerException('Cannot move a folder into one of its own subfolders.');
            }

            $currentId = FileManagerItem::where('id', $currentId)->value('parent_id');
            $depth++;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete / Restore / Purge
    |--------------------------------------------------------------------------
    */

    public function countDescendants(FileManagerItem $item): int
    {
        if (!$item->isFolder()) {
            return 0;
        }

        $count = 0;
        $queue = [$item->id];

        while ($queue) {
            $childIds = FileManagerItem::whereIn('parent_id', $queue)->pluck('id')->all();
            $count += count($childIds);
            $queue = $childIds;
        }

        return $count;
    }

    public function delete(Admin $actor, FileManagerItem $item): void
    {
        $this->authorizeItem($actor, $item, 'delete');

        DB::transaction(function () use ($item, $actor) {
            $this->softDeleteRecursive($item, $actor);
        });
    }

    protected function softDeleteRecursive(FileManagerItem $item, Admin $actor): void
    {
        if ($item->isFolder()) {
            $children = FileManagerItem::where('parent_id', $item->id)->get();
            foreach ($children as $child) {
                $this->softDeleteRecursive($child, $actor);
            }
        }

        $item->updated_by = $actor->id;
        $item->save();
        $item->delete();
    }

    public function purge(FileManagerItem $item): void
    {
        DB::transaction(function () use ($item) {
            $this->purgeRecursive($item);
        });
    }

    protected function purgeRecursive(FileManagerItem $item): void
    {
        $children = FileManagerItem::withTrashed()->where('parent_id', $item->id)->get();
        foreach ($children as $child) {
            $this->purgeRecursive($child);
        }

        if ($item->isFile() && $item->storage_path) {
            Storage::disk($item->disk)->delete($item->storage_path);
        }
        if ($item->isFile() && $thumb = data_get($item->metadata, 'thumbnail_path')) {
            Storage::disk($item->disk)->delete($thumb);
        }

        $item->forceDelete();
    }

    /**
     * Restores a trashed item and, for a folder, every descendant that was
     * trashed along with it - restoring only the item itself would leave
     * its contents orphaned in the trash with no way to reach them.
     */
    public function restore(Admin $actor, FileManagerItem $item): FileManagerItem
    {
        if (!$this->can($actor, 'delete')) {
            throw new FileManagerException('Permission denied.', 403);
        }
        if ((int) $item->owner_id !== (int) $actor->id && !$this->canManageAll($actor)) {
            throw new FileManagerException('You do not have access to this item.', 403);
        }

        DB::transaction(function () use ($item, $actor) {
            $this->restoreRecursive($item, $actor);
        });

        return $item->fresh();
    }

    protected function restoreRecursive(FileManagerItem $item, Admin $actor): void
    {
        $item->restore();
        $item->updated_by = $actor->id;
        $item->save();

        if ($item->isFolder()) {
            $children = FileManagerItem::onlyTrashed()->where('parent_id', $item->id)->get();
            foreach ($children as $child) {
                $this->restoreRecursive($child, $actor);
            }
        }
    }

    /**
     * Permanently deletes a trashed item (and descendants). Reserved for
     * super admins / manage_all_file_manager - enforced by the caller via
     * the FileManagerItemPolicy::forceDelete gate.
     */
    public function forceDelete(FileManagerItem $item): void
    {
        $this->purge($item);
    }

    public function toggleFavorite(Admin $actor, FileManagerItem $item): FileManagerItem
    {
        $this->authorizeItem($actor, $item, 'view');

        $item->update(['favorite' => !$item->favorite, 'updated_by' => $actor->id]);

        return $item->fresh();
    }

    public function setFavorite(Admin $actor, FileManagerItem $item, bool $value): FileManagerItem
    {
        $this->authorizeItem($actor, $item, 'view');

        $item->update(['favorite' => $value, 'updated_by' => $actor->id]);

        return $item->fresh();
    }

    public function touchLastOpened(FileManagerItem $item): void
    {
        $item->forceFill(['last_opened_at' => now()])->saveQuietly();
    }

    /*
    |--------------------------------------------------------------------------
    | Recent / Favorites / Trash / Search
    |--------------------------------------------------------------------------
    */

    public function recentQuery(Admin $actor)
    {
        return $this->visibleQuery($actor)
            ->whereNotNull('last_opened_at')
            ->orderBy('last_opened_at', 'desc');
    }

    public function favoritesQuery(Admin $actor)
    {
        return $this->visibleQuery($actor)->favorited()->orderBy('updated_at', 'desc');
    }

    public function trashQuery(Admin $actor)
    {
        $query = FileManagerItem::onlyTrashed();

        if (!$this->canManageAll($actor)) {
            $query->where('owner_id', $actor->id);
        }

        return $query->orderBy('deleted_at', 'desc');
    }

    public function searchQuery(Admin $actor, string $term)
    {
        return $this->visibleQuery($actor)
            ->where('name', 'like', '%' . $term . '%')
            ->orderBy('updated_at', 'desc');
    }

    /**
     * Folders directly inside $parentId, for lazy tree expansion - never
     * loads the whole hierarchy at once.
     */
    public function folderChildren(Admin $actor, ?int $parentId)
    {
        $this->findFolder($parentId, $actor);

        return $this->visibleQuery($actor)
            ->folders()
            ->inFolder($parentId)
            ->withCount(['children' => fn ($q) => $q->folders()])
            ->orderBy('name')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Thumbnails
    |--------------------------------------------------------------------------
    */

    protected const THUMBNAILABLE_IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
    protected const THUMBNAILABLE_PDF_MIMES = ['application/pdf'];
    protected const THUMBNAILABLE_SPREADSHEET_MIMES = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    protected const THUMBNAIL_MAX_DIM = 320;

    /**
     * Source files larger than this are never rendered into a thumbnail -
     * the listing falls back to a file-type icon instead, so a huge PDF or
     * spreadsheet can never be read in full just to produce a preview.
     */
    protected const THUMBNAIL_MAX_SOURCE_BYTES = 20 * 1024 * 1024;

    /** True if $item is a type thumbnailPath() can render a real preview for. */
    public function hasThumbnailSupport(FileManagerItem $item): bool
    {
        if (!$item->isFile()) {
            return false;
        }

        return in_array($item->mime_type, self::THUMBNAILABLE_IMAGE_MIMES, true)
            || (in_array($item->mime_type, self::THUMBNAILABLE_PDF_MIMES, true) && class_exists(\Imagick::class))
            || in_array($item->mime_type, self::THUMBNAILABLE_SPREADSHEET_MIMES, true);
    }

    /**
     * Generates (once) and returns the storage path of a small preview
     * thumbnail: images via GD, PDFs (first page only) via Imagick +
     * Ghostscript, and Excel workbooks (first sheet, top-left range only)
     * via PhpSpreadsheet + GD. Cached in metadata so it's only ever
     * generated once per file. Word/PowerPoint and anything else fall back
     * to a plain file-type icon in the UI - there's no reliable way to
     * render their real layout without an office suite installed.
     */
    public function thumbnailPath(FileManagerItem $item): ?string
    {
        if (!$this->hasThumbnailSupport($item)) {
            return null;
        }

        $cached = data_get($item->metadata, 'thumbnail_path');
        $disk = Storage::disk($item->disk);

        if ($cached && $disk->exists($cached)) {
            return $cached;
        }

        if (!$item->storage_path || !$disk->exists($item->storage_path)) {
            return null;
        }

        if ($disk->size($item->storage_path) > self::THUMBNAIL_MAX_SOURCE_BYTES) {
            return null;
        }

        try {
            $sourcePath = $disk->path($item->storage_path);

            $binary = match (true) {
                in_array($item->mime_type, self::THUMBNAILABLE_IMAGE_MIMES, true) => $this->renderImageThumbnail($sourcePath, $item->mime_type),
                in_array($item->mime_type, self::THUMBNAILABLE_PDF_MIMES, true) => $this->renderPdfThumbnail($sourcePath),
                in_array($item->mime_type, self::THUMBNAILABLE_SPREADSHEET_MIMES, true) => $this->renderSpreadsheetThumbnail($sourcePath),
                default => null,
            };

            if (!$binary) {
                return null;
            }

            $thumbPath = $item->owner_id . '/thumbnails/' . $item->id . '.png';
            $disk->put($thumbPath, $binary);

            $item->forceFill(['metadata' => array_merge($item->metadata ?? [], ['thumbnail_path' => $thumbPath])])->saveQuietly();

            return $thumbPath;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function renderImageThumbnail(string $sourcePath, string $mimeType): ?string
    {
        [$width, $height] = getimagesize($sourcePath);

        $image = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/gif' => imagecreatefromgif($sourcePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($sourcePath) : null,
            'image/bmp' => function_exists('imagecreatefrombmp') ? imagecreatefrombmp($sourcePath) : null,
            default => null,
        };

        if (!$image) {
            return null;
        }

        $ratio = min(self::THUMBNAIL_MAX_DIM / $width, self::THUMBNAIL_MAX_DIM / $height, 1);
        $thumbWidth = max(1, (int) ($width * $ratio));
        $thumbHeight = max(1, (int) ($height * $ratio));

        $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);

        ob_start();
        imagepng($thumb, null, 6);
        $binary = ob_get_clean();

        imagedestroy($image);
        imagedestroy($thumb);

        return $binary ?: null;
    }

    /**
     * Rasterizes just the first page of the PDF via Imagick/Ghostscript -
     * readImage() with a "[0]" page selector never touches the rest of the
     * document. Flattened onto an opaque white background since PDF pages
     * commonly rasterize with a transparent backdrop.
     */
    protected function renderPdfThumbnail(string $sourcePath): ?string
    {
        if (!class_exists(\Imagick::class)) {
            return null;
        }

        $imagick = new \Imagick();

        try {
            $imagick->setResolution(110, 110);
            $imagick->setBackgroundColor(new \ImagickPixel('white'));
            $imagick->readImage($sourcePath . '[0]');
            $imagick->setImageFormat('png');
            $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
            $imagick->setImageBackgroundColor(new \ImagickPixel('white'));

            $width = $imagick->getImageWidth();
            $height = $imagick->getImageHeight();
            $ratio = min(self::THUMBNAIL_MAX_DIM / $width, self::THUMBNAIL_MAX_DIM / $height, 1);
            $thumbWidth = max(1, (int) ($width * $ratio));
            $thumbHeight = max(1, (int) ($height * $ratio));

            $imagick->thumbnailImage($thumbWidth, $thumbHeight);

            return $imagick->getImageBlob() ?: null;
        } finally {
            $imagick->clear();
        }
    }

    /**
     * Renders the first sheet's top-left cell range as a lightweight grid
     * image via GD - not a pixel-accurate render of the workbook, but
     * enough to visually identify it at a glance. The read filter keeps
     * PhpSpreadsheet from ever materializing cells outside that range, so
     * a workbook with many rows/columns doesn't inflate memory use just to
     * produce a small preview.
     */
    protected function renderSpreadsheetThumbnail(string $sourcePath): ?string
    {
        $maxRows = 10;
        $maxCols = 6;

        $filter = new class($maxRows, $maxCols) implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
            private $maxRow;
            private $maxCol;

            public function __construct($maxRow, $maxCol)
            {
                $this->maxRow = $maxRow;
                $this->maxCol = $maxCol;
            }

            public function readCell($columnAddress, $row, $worksheetName = '')
            {
                return $row <= $this->maxRow
                    && \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnAddress) <= $this->maxCol;
            }
        };

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($sourcePath);
        $reader->setReadDataOnly(true);

        $sheetNames = $reader->listWorksheetNames($sourcePath);
        if (empty($sheetNames)) {
            return null;
        }

        $reader->setLoadSheetsOnly($sheetNames[0]);
        $reader->setReadFilter($filter);
        $sheet = $reader->load($sourcePath)->getActiveSheet();

        $rows = min($maxRows, max(1, $sheet->getHighestDataRow()));
        $cols = min($maxCols, max(1, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestDataColumn())));

        $cellWidth = 52;
        $cellHeight = 22;
        $width = $cols * $cellWidth;
        $height = $rows * $cellHeight;

        $canvas = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        $gridColor = imagecolorallocate($canvas, 226, 232, 240);
        $headerBg = imagecolorallocate($canvas, 236, 253, 245);
        $textColor = imagecolorallocate($canvas, 51, 65, 85);

        for ($row = 1; $row <= $rows; $row++) {
            for ($col = 1; $col <= $cols; $col++) {
                $x1 = ($col - 1) * $cellWidth;
                $y1 = ($row - 1) * $cellHeight;
                $x2 = $x1 + $cellWidth;
                $y2 = $y1 + $cellHeight;

                if ($row === 1) {
                    imagefilledrectangle($canvas, $x1, $y1, $x2, $y2, $headerBg);
                }
                imagerectangle($canvas, $x1, $y1, $x2, $y2, $gridColor);

                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $value = trim((string) $sheet->getCell($colLetter . $row)->getFormattedValue());

                if ($value !== '') {
                    // GD's built-in bitmap fonts only handle Latin-1; transliterate so
                    // accented text degrades gracefully instead of rendering as boxes.
                    $text = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $value) ?: $value;
                    imagesetclip($canvas, $x1 + 1, $y1 + 1, $x2 - 2, $y2 - 2);
                    imagestring($canvas, 3, $x1 + 4, $y1 + 5, $text, $textColor);
                    imagesetclip($canvas, 0, 0, $width - 1, $height - 1);
                }
            }
        }

        ob_start();
        imagepng($canvas, null, 6);
        $binary = ob_get_clean();
        imagedestroy($canvas);

        return $binary ?: null;
    }
}
