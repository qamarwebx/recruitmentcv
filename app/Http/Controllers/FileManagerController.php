<?php

namespace App\Http\Controllers;

use App\Exceptions\FileManagerException;
use App\Http\Requests\FileManager\MoveItemRequest;
use App\Http\Requests\FileManager\RenameItemRequest;
use App\Http\Requests\FileManager\StoreFolderRequest;
use App\Http\Requests\FileManager\UploadFileRequest;
use App\Models\Admin;
use App\Models\FileManagerAdminSaveFilter;
use App\Models\FileManagerItem;
use App\Services\FileManagerQuotaService;
use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileManagerController extends Controller
{
    public function __construct(
        protected FileManagerService $service,
        protected FileManagerQuotaService $quota
    ) {
    }

    protected function actor(): Admin
    {
        return Auth::guard('admin')->user();
    }

    protected function permissionFlags(Admin $actor): array
    {
        return [
            'canView' => $this->service->can($actor, 'view'),
            'canCreateFolder' => $this->service->can($actor, 'create_folder'),
            'canUpload' => $this->service->can($actor, 'upload'),
            'canDownload' => $this->service->can($actor, 'download'),
            'canPreview' => $this->service->can($actor, 'preview'),
            'canRename' => $this->service->can($actor, 'rename'),
            'canMove' => $this->service->can($actor, 'move'),
            'canDelete' => $this->service->can($actor, 'delete'),
            'canManageAll' => $this->service->canManageAll($actor),
        ];
    }

    public function index(Request $request)
    {
        $actor = $this->actor();

        if (!$this->service->can($actor, 'view')) {
            abort(403, 'You do not have permission to access the File Manager.');
        }

        $flags = $this->permissionFlags($actor);
        $stats = $this->statsFor($actor);

        $owners = $flags['canManageAll']
            ? Admin::where('status', 1)->orderBy('name')->get(['id', 'name', 'user_type'])
            : collect([$actor]);

        $maxUploadSize = (int) config('file-manager.max_upload_size');
        $allowedExtensions = config('file-manager.allowed_extensions');
        $savedFilter = FileManagerAdminSaveFilter::where('admin_id', $actor->id)->first();

        return view('admin.file_manager.index', compact(
            'flags', 'stats', 'owners', 'maxUploadSize', 'allowedExtensions', 'savedFilter'
        ));
    }

    protected function statsFor(Admin $actor): array
    {
        $quota = $this->quota->getOrCreateQuota($actor);
        $used = $this->quota->usedBytes($actor);
        $available = $this->quota->availableBytes($actor);

        return [
            'total_files' => $this->quota->fileCount($actor),
            'total_folders' => $this->quota->folderCount($actor),
            'used_bytes' => $used,
            'used_human' => FileManagerQuotaService::humanReadable($used),
            'available_bytes' => $available,
            'available_human' => FileManagerQuotaService::humanReadable($available),
            'allocated_bytes' => $quota->allocated_bytes,
            'allocated_human' => FileManagerQuotaService::humanReadable($quota->allocated_bytes),
            'usage_percent' => $quota->allocated_bytes > 0 ? round(($used / $quota->allocated_bytes) * 100, 1) : 0,
        ];
    }

    public function stats(Request $request)
    {
        $actor = $this->actor();

        if (!$this->service->can($actor, 'view')) {
            abort(403);
        }

        return response()->json($this->statsFor($actor));
    }

    /**
     * Card-grid data feed for the Drive-style UI. Replaces a DataTable: one
     * JSON page of items for the current context (folder / recent /
     * favorites / trash / search), never the whole tree.
     */
    public function browse(Request $request)
    {
        $actor = $this->actor();

        if (!$this->service->can($actor, 'view')) {
            abort(403);
        }

        $context = $request->input('context', 'folder');

        $query = match ($context) {
            'recent' => $this->service->recentQuery($actor),
            'favorites' => $this->service->favoritesQuery($actor),
            'trash' => $this->service->trashQuery($actor),
            'search' => $this->service->searchQuery($actor, (string) $request->input('q', '')),
            default => $this->service->visibleQuery($actor)->with(['owner:id,name,user_type']),
        };

        if ($context === 'folder') {
            $filters = $request->only([
                'name', 'type', 'extension', 'mime_type', 'owner_id',
                'date_from', 'date_to', 'size_from', 'size_to', 'quick',
            ]);
            $filters['actor_id'] = $actor->id;

            if ($request->input('location', 'current') === 'all') {
                // Search-everywhere mode inside "My Files": drop the folder scope.
            } else {
                $filters['parent_id'] = $request->filled('parent_id') ? (int) $request->parent_id : null;
            }

            $this->service->applyFilters($query, $filters);
        }

        if ($context !== 'trash') {
            $query->with('owner:id,name,user_type');
        }

        $sort = $request->input('sort', 'name_asc');
        $foldersFirst = in_array($context, ['folder'], true);

        if ($foldersFirst) {
            $query->orderByRaw("CASE WHEN type = 'folder' THEN 0 ELSE 1 END");
        }

        match ($sort) {
            'name_desc' => $query->orderBy('name', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'modified' => $query->orderBy('updated_at', 'desc'),
            'largest' => $query->orderBy('size', 'desc'),
            'smallest' => $query->orderBy('size', 'asc'),
            'type' => $query->orderBy('extension', 'asc'),
            default => $query->orderBy('name', 'asc'),
        };

        $perPage = min(60, (int) $request->input('per_page', 40));
        $paginator = $query->paginate($perPage)->withQueryString();

        $items = collect($paginator->items())->map(function (FileManagerItem $item) use ($actor, $context) {
            $canManage = (int) $item->owner_id === (int) $actor->id || $this->service->canManageAll($actor);

            return [
                'id' => $item->id,
                'type' => $item->type,
                'name' => $item->name,
                'extension' => $item->extension,
                'mime_type' => $item->mime_type,
                'category' => $item->category(),
                'color' => $item->color,
                'size' => $item->size,
                'size_human' => $item->isFolder() ? null : FileManagerQuotaService::humanReadable((int) $item->size),
                'owner' => optional($item->owner)->only(['id', 'name']),
                'owner_type' => optional($item->owner) && (int) optional($item->owner)->user_type === 1 ? 'Admin' : 'Staff',
                'favorite' => (bool) $item->favorite,
                'modified' => optional($item->updated_at)->format('d M Y, h:i A'),
                'modified_diff' => optional($item->updated_at)->diffForHumans(),
                'deleted_at' => optional($item->deleted_at)->format('d M Y, h:i A'),
                'child_count' => $item->isFolder() ? FileManagerItem::where('parent_id', $item->id)->count() : null,
                'has_thumbnail' => $this->service->hasThumbnailSupport($item),
                'thumbnail_url' => $item->isFile() ? route('admin.file_manager.thumbnail', $item->id) : null,
                'can_manage' => $canManage,
                'can_force_delete' => $context === 'trash' && $this->service->canForceDelete($actor, $item),
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    /** Lazy folder-tree children for the left navigation panel. */
    public function folderTree(Request $request, $parentId = null)
    {
        $actor = $this->actor();

        if (!$this->service->can($actor, 'view')) {
            abort(403);
        }

        try {
            $children = $this->service->folderChildren($actor, $parentId ? (int) $parentId : null);
        } catch (FileManagerException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $e->statusCode());
        }

        return response()->json($children->map(fn ($f) => [
            'id' => $f->id,
            'name' => $f->name,
            'color' => $f->color,
            'has_children' => $f->children_count > 0,
        ]));
    }

    public function breadcrumb(Request $request, $itemId = null)
    {
        $actor = $this->actor();
        $trail = [];

        $current = $itemId ? FileManagerItem::folders()->find($itemId) : null;

        while ($current) {
            $this->service->authorizeItem($actor, $current, 'view');
            array_unshift($trail, ['id' => $current->id, 'name' => $current->name]);
            $current = $current->parent;
        }

        return response()->json(['trail' => $trail]);
    }

    public function createFolder(StoreFolderRequest $request)
    {
        $this->authorize('createFolder', FileManagerItem::class);

        try {
            $folder = $this->service->createFolder(
                $this->actor(),
                $request->integer('parent_id') ?: null,
                $request->input('name'),
                $request->integer('owner_id') ?: null
            );

            if ($request->filled('color')) {
                $folder->update(['color' => $request->input('color')]);
            }

            return response()->json(['status' => 'success', 'message' => 'Folder created.', 'data' => $folder]);
        } catch (FileManagerException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $e->statusCode());
        }
    }

    public function upload(UploadFileRequest $request)
    {
        $this->authorize('upload', FileManagerItem::class);

        try {
            $items = $this->service->upload(
                $this->actor(),
                $request->integer('parent_id') ?: null,
                $request->file('files', []),
                $request->integer('owner_id') ?: null
            );

            return response()->json(['status' => 'success', 'message' => count($items) . ' file(s) uploaded.', 'data' => $items]);
        } catch (FileManagerException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $e->statusCode());
        }
    }

    public function rename(RenameItemRequest $request)
    {
        try {
            $item = FileManagerItem::findOrFail($request->input('id'));
            $this->authorize('rename', $item);
            $item = $this->service->rename($this->actor(), $item, $request->input('name'));

            return response()->json(['status' => 'success', 'message' => 'Renamed.', 'data' => $item]);
        } catch (FileManagerException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $e->statusCode());
        }
    }

    public function move(MoveItemRequest $request)
    {
        try {
            $item = FileManagerItem::findOrFail($request->input('id'));
            $this->authorize('move', $item);
            $item = $this->service->move($this->actor(), $item, $request->integer('parent_id') ?: null);

            return response()->json(['status' => 'success', 'message' => 'Moved.', 'data' => $item]);
        } catch (FileManagerException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $e->statusCode());
        }
    }

    /** Bulk drag-and-drop / multi-select move: same guarantees as move(), per item. */
    public function bulkMove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:file_manager_items,id',
            'parent_id' => 'nullable|integer|exists:file_manager_items,id',
        ]);

        $moved = [];
        $errors = [];

        foreach ($request->input('ids') as $id) {
            try {
                $item = FileManagerItem::findOrFail($id);
                $this->authorize('move', $item);
                $this->service->move($this->actor(), $item, $request->integer('parent_id') ?: null);
                $moved[] = $id;
            } catch (\Throwable $e) {
                $errors[] = ['id' => $id, 'message' => $e instanceof FileManagerException ? $e->getMessage() : 'Move failed.'];
            }
        }

        return response()->json(['status' => 'success', 'moved' => $moved, 'errors' => $errors]);
    }

    public function favorite(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:file_manager_items,id']);

        try {
            $item = FileManagerItem::findOrFail($request->input('id'));
            $this->authorize('favorite', $item);
            $item = $this->service->toggleFavorite($this->actor(), $item);

            return response()->json(['status' => 'success', 'favorite' => $item->favorite]);
        } catch (FileManagerException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $e->statusCode());
        }
    }

    /**
     * Bulk multi-select favorite: marks every selected item as a favorite
     * (a set, not a toggle - a mixed selection of already-favorited and
     * not-yet-favorited items should end up uniformly favorited).
     */
    public function bulkFavorite(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $updated = [];
        $errors = [];

        foreach ($request->input('ids') as $id) {
            try {
                $item = FileManagerItem::findOrFail($id);
                $this->authorize('favorite', $item);
                $this->service->setFavorite($this->actor(), $item, true);
                $updated[] = $id;
            } catch (\Throwable $e) {
                $errors[] = ['id' => $id, 'message' => $e instanceof FileManagerException ? $e->getMessage() : 'Favorite failed.'];
            }
        }

        return response()->json(['status' => 'success', 'updated' => $updated, 'errors' => $errors]);
    }

    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:file_manager_items,id']);

        try {
            $item = FileManagerItem::findOrFail($request->input('id'));
            $this->authorize('delete', $item);

            if ($item->isFolder() && !$request->boolean('force')) {
                $count = $this->service->countDescendants($item);
                if ($count > 0) {
                    return response()->json([
                        'status' => 'confirm',
                        'message' => "This folder contains {$count} item(s). Delete everything inside?",
                        'count' => $count,
                    ]);
                }
            }

            $this->service->delete($this->actor(), $item);

            return response()->json(['status' => 'success', 'message' => 'Moved to Trash.']);
        } catch (FileManagerException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $e->statusCode());
        }
    }

    /** Bulk multi-select soft delete (send to Trash): same guarantees as delete(), per item. */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $deleted = [];
        $errors = [];

        foreach ($request->input('ids') as $id) {
            try {
                $item = FileManagerItem::find($id);

                if (!$item) {
                    // Already trashed via an ancestor folder's cascade earlier in this same batch.
                    if ($this->alreadyHandled($id, 'trashed')) {
                        $deleted[] = $id;
                        continue;
                    }
                    throw new FileManagerException('Item not found.', 404);
                }

                $this->authorize('delete', $item);
                $this->service->delete($this->actor(), $item);
                $deleted[] = $id;
            } catch (\Throwable $e) {
                $errors[] = ['id' => $id, 'message' => $e instanceof FileManagerException ? $e->getMessage() : 'Delete failed.'];
            }
        }

        return response()->json(['status' => 'success', 'deleted' => $deleted, 'errors' => $errors]);
    }

    public function restore(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        try {
            $item = FileManagerItem::onlyTrashed()->findOrFail($request->input('id'));
            $this->authorize('restore', $item);
            $this->service->restore($this->actor(), $item);

            return response()->json(['status' => 'success', 'message' => 'Restored.']);
        } catch (FileManagerException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $e->statusCode());
        }
    }

    /** Bulk multi-select restore from Trash: same guarantees as restore(), per item. */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $restored = [];
        $errors = [];

        foreach ($request->input('ids') as $id) {
            try {
                $item = FileManagerItem::onlyTrashed()->find($id);

                if (!$item) {
                    // Already restored via an ancestor folder's cascade earlier in this same batch.
                    if ($this->alreadyHandled($id, 'restored')) {
                        $restored[] = $id;
                        continue;
                    }
                    throw new FileManagerException('Item not found in Trash.', 404);
                }

                $this->authorize('restore', $item);
                $this->service->restore($this->actor(), $item);
                $restored[] = $id;
            } catch (\Throwable $e) {
                $errors[] = ['id' => $id, 'message' => $e instanceof FileManagerException ? $e->getMessage() : 'Restore failed.'];
            }
        }

        return response()->json(['status' => 'success', 'restored' => $restored, 'errors' => $errors]);
    }

    public function forceDelete(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        try {
            $item = FileManagerItem::onlyTrashed()->findOrFail($request->input('id'));
            $this->authorize('forceDelete', $item);
            $this->service->forceDelete($item);

            return response()->json(['status' => 'success', 'message' => 'Permanently deleted.']);
        } catch (FileManagerException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $e->statusCode());
        }
    }

    /** Bulk multi-select permanent delete from Trash: same guarantees as forceDelete(), per item. */
    public function bulkForceDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $deleted = [];
        $errors = [];

        foreach ($request->input('ids') as $id) {
            try {
                $item = FileManagerItem::onlyTrashed()->find($id);

                if (!$item) {
                    // Already purged via an ancestor folder's cascade earlier in this same batch.
                    if ($this->alreadyHandled($id, 'purged')) {
                        $deleted[] = $id;
                        continue;
                    }
                    throw new FileManagerException('Item not found in Trash.', 404);
                }

                $this->authorize('forceDelete', $item);
                $this->service->forceDelete($item);
                $deleted[] = $id;
            } catch (\Throwable $e) {
                $errors[] = ['id' => $id, 'message' => $e instanceof FileManagerException ? $e->getMessage() : 'Delete failed.'];
            }
        }

        return response()->json(['status' => 'success', 'deleted' => $deleted, 'errors' => $errors]);
    }

    /**
     * True if $id no longer needs processing in a bulk loop because an
     * ancestor folder's recursive delete/restore/purge already applied to
     * it earlier in this same request (a mixed selection can include both
     * a folder and one of its own descendants).
     */
    protected function alreadyHandled(int $id, string $expectedState): bool
    {
        return match ($expectedState) {
            'trashed' => FileManagerItem::withTrashed()->whereKey($id)->whereNotNull('deleted_at')->exists(),
            'restored' => FileManagerItem::whereKey($id)->exists(),
            'purged' => !FileManagerItem::withTrashed()->whereKey($id)->exists(),
        };
    }

    public function download(FileManagerItem $item)
    {
        $this->authorize('download', $item);

        if (!$item->isFile() || !$item->storage_path || !Storage::disk($item->disk)->exists($item->storage_path)) {
            abort(404, 'File not found.');
        }

        $this->service->touchLastOpened($item);

        return Storage::disk($item->disk)->download($item->storage_path, $item->name);
    }

    public function preview(FileManagerItem $item)
    {
        $this->authorize('preview', $item);

        $disk = Storage::disk($item->disk);

        if (!$item->isFile() || !$item->storage_path || !$disk->exists($item->storage_path)) {
            abort(404, 'File not found.');
        }

        $this->service->touchLastOpened($item);

        $previewable = config('file-manager.previewable_extensions');
        if (!in_array(strtolower((string) $item->extension), $previewable, true)) {
            return $disk->download($item->storage_path, $item->name);
        }

        $headers = [
            'Content-Type' => $item->mime_type ?: $disk->mimeType($item->storage_path),
            'Content-Disposition' => 'inline; filename="' . addslashes($item->name) . '"',
        ];

        // Video/audio scrubbing (and efficient loading of any large previewable
        // file) needs the server to honor Range requests. Storage::response()
        // always returns a StreamedResponse, which never does - a real
        // filesystem path lets us return a BinaryFileResponse instead, which
        // handles Range/206 automatically. Falls back to the streamed response
        // for any disk driver that can't expose a local path.
        try {
            return response()->file($disk->path($item->storage_path), $headers);
        } catch (\Throwable $e) {
            return $disk->response($item->storage_path, $item->name, $headers);
        }
    }

    public function thumbnail(FileManagerItem $item)
    {
        $this->authorize('preview', $item);

        $path = $this->service->thumbnailPath($item);

        if (!$path) {
            abort(404);
        }

        return Storage::disk($item->disk)->response($path, null, ['Content-Type' => 'image/png']);
    }

    public function saveFilter(Request $request)
    {
        $actor = $this->actor();

        $filter = FileManagerAdminSaveFilter::firstOrNew(['admin_id' => $actor->id]);
        $filter->by_type = $request->input('by_type');
        $filter->by_category = is_array($request->input('by_category')) ? implode(',', $request->input('by_category')) : $request->input('by_category');
        $filter->by_owner = is_array($request->input('by_owner')) ? implode(',', $request->input('by_owner')) : $request->input('by_owner');
        $filter->by_size_min = $request->input('by_size_min') ?: null;
        $filter->by_size_max = $request->input('by_size_max') ?: null;
        $filter->by_date_from = $request->input('by_date_from');
        $filter->by_date_to = $request->input('by_date_to');
        $filter->by_location = $request->input('by_location', 'current');
        $filter->save();

        return response()->json(['message' => $filter->wasRecentlyCreated ? 'Filter saved successfully!' : 'Filter updated successfully!']);
    }

    public function resetFilter(Request $request)
    {
        $actor = $this->actor();

        $filter = FileManagerAdminSaveFilter::where('admin_id', $actor->id)->first();
        if ($filter) {
            $filter->fill([
                'by_type' => null, 'by_category' => null, 'by_owner' => null,
                'by_size_min' => null, 'by_size_max' => null,
                'by_date_from' => null, 'by_date_to' => null, 'by_location' => 'current',
            ])->save();
        }

        return response()->json(['message' => 'Filter reset successfully!']);
    }
}
