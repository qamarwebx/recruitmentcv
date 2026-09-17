<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileManager\UpdateQuotaRequest;
use App\Models\Admin;
use App\Models\FileManagerItem;
use App\Models\FileManagerQuota;
use App\Services\FileManagerQuotaService;
use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FileManagerQuotaController extends Controller
{
    public function __construct(
        protected FileManagerQuotaService $quota,
        protected FileManagerService $service
    ) {
    }

    protected function actor(): Admin
    {
        return Auth::guard('admin')->user();
    }

    /**
     * Settings > File Manager (storage/quota administration) is restricted
     * to full admins only - not delegated to staff via a permission toggle.
     */
    protected function authorizeSettings(): void
    {
        if ((int) $this->actor()->user_type !== 1) {
            abort(403, 'Only administrators can manage File Manager storage quotas.');
        }
    }

    public function index()
    {
        $this->authorizeSettings();

        $summary = $this->quota->storageSummary();
        $summaryHuman = array_merge($summary, [
            'physical_total_human' => FileManagerQuotaService::humanReadable($summary['physical_total']),
            'physical_used_human' => FileManagerQuotaService::humanReadable($summary['physical_used']),
            'physical_free_human' => FileManagerQuotaService::humanReadable($summary['physical_free']),
            'total_allocated_human' => FileManagerQuotaService::humanReadable($summary['total_allocated']),
            'unallocated_human' => FileManagerQuotaService::humanReadable($summary['unallocated']),
            'file_manager_usage_human' => FileManagerQuotaService::humanReadable($summary['file_manager_usage']),
        ]);

        $usersCount = Admin::where('status', 1)->count();
        $allowOverallocation = (bool) config('file-manager.allow_overallocation');

        return view('admin.settings.file_manager.index', compact('summaryHuman', 'usersCount', 'allowOverallocation'));
    }

    /**
     * Every active admin/staff joined with their derived usage figures.
     * Kept in-memory (not a DB-paginated query) because the join is across
     * two different aggregations (per-owner file sums/counts) that are
     * cheap in bulk but awkward to paginate at the SQL level - user counts
     * in this CRM are small enough (tens to low hundreds) for this to stay
     * fast.
     */
    protected function usersWithUsage(): \Illuminate\Support\Collection
    {
        $usedByOwner = FileManagerItem::withTrashed()->where('type', FileManagerItem::TYPE_FILE)
            ->groupBy('owner_id')->selectRaw('owner_id, SUM(size) as used')->pluck('used', 'owner_id');
        $fileCounts = FileManagerItem::where('type', FileManagerItem::TYPE_FILE)
            ->groupBy('owner_id')->selectRaw('owner_id, COUNT(*) as c')->pluck('c', 'owner_id');
        $folderCounts = FileManagerItem::where('type', FileManagerItem::TYPE_FOLDER)
            ->groupBy('owner_id')->selectRaw('owner_id, COUNT(*) as c')->pluck('c', 'owner_id');
        $quotas = FileManagerQuota::get()->keyBy('admin_id');

        return Admin::where('status', 1)->orderBy('name')->get()->map(function ($admin) use ($usedByOwner, $fileCounts, $folderCounts, $quotas) {
            $quota = $quotas->get($admin->id);
            $allocated = (int) ($quota->allocated_bytes ?? 0);
            $used = (int) ($usedByOwner[$admin->id] ?? 0);
            $available = max(0, $allocated - $used);
            $percent = $allocated > 0 ? round(($used / $allocated) * 100, 2) : 0.0;

            if ($allocated === 0) {
                $status = 'no_allocation';
            } elseif ($used > $allocated) {
                $status = 'over_limit';
            } elseif ($percent >= 100) {
                $status = 'full';
            } elseif ($percent >= 80) {
                $status = 'near_limit';
            } else {
                $status = 'available';
            }

            return (object) [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'user_type' => (int) $admin->user_type === 1 ? 'Admin' : 'Staff',
                'allocated_bytes' => $allocated,
                'used_bytes' => $used,
                'available_bytes' => $available,
                'usage_percent' => $percent,
                'file_count' => (int) ($fileCounts[$admin->id] ?? 0),
                'folder_count' => (int) ($folderCounts[$admin->id] ?? 0),
                'quota_status' => $status,
                'updated_at' => optional($quota)->updated_at,
            ];
        });
    }

    /** Card-grid data feed for the Storage Management Dashboard. */
    public function browseUsers(Request $request)
    {
        $this->authorizeSettings();

        $rows = $this->usersWithUsage();

        if ($request->filled('search_user')) {
            $search = mb_strtolower($request->search_user);
            $rows = $rows->filter(fn ($r) => str_contains(mb_strtolower($r->name), $search) || str_contains(mb_strtolower($r->email), $search));
        }
        if ($request->filled('user_type')) {
            $type = $request->user_type == 1 ? 'Admin' : 'Staff';
            $rows = $rows->where('user_type', $type);
        }
        if ($request->filled('quota_status')) {
            $status = $request->quota_status;
            $rows = $status === 'highest_usage'
                ? $rows->sortByDesc('usage_percent')
                : ($status === 'recently_updated'
                    ? $rows->filter(fn ($r) => $r->updated_at && $r->updated_at->gte(now()->subDays(7)))
                    : $rows->where('quota_status', $status));
        }
        if ($request->filled('alloc_from')) {
            $rows = $rows->filter(fn ($r) => $r->allocated_bytes >= (int) $request->alloc_from);
        }
        if ($request->filled('alloc_to')) {
            $rows = $rows->filter(fn ($r) => $r->allocated_bytes <= (int) $request->alloc_to);
        }
        if ($request->filled('used_from')) {
            $rows = $rows->filter(fn ($r) => $r->used_bytes >= (int) $request->used_from);
        }
        if ($request->filled('used_to')) {
            $rows = $rows->filter(fn ($r) => $r->used_bytes <= (int) $request->used_to);
        }

        $sort = $request->input('sort', 'name');
        $rows = match ($sort) {
            'allocated' => $rows->sortByDesc('allocated_bytes'),
            'used' => $rows->sortByDesc('used_bytes'),
            'available' => $rows->sortByDesc('available_bytes'),
            'usage_percent' => $rows->sortByDesc('usage_percent'),
            'file_count' => $rows->sortByDesc('file_count'),
            'updated_at' => $rows->sortByDesc('updated_at'),
            default => $rows->sortBy('name'),
        };
        $rows = $rows->values();

        $perPage = min(60, (int) $request->input('per_page', 24));
        $page = max(1, (int) $request->input('page', 1));
        $slice = $rows->slice(($page - 1) * $perPage, $perPage)->values();

        $data = $slice->map(function ($r) {
            return [
                'id' => $r->id,
                'name' => $r->name,
                'email' => $r->email,
                'user_type' => $r->user_type,
                'allocated_bytes' => $r->allocated_bytes,
                'allocated_human' => FileManagerQuotaService::humanReadable($r->allocated_bytes),
                'used_bytes' => $r->used_bytes,
                'used_human' => FileManagerQuotaService::humanReadable($r->used_bytes),
                'available_bytes' => $r->available_bytes,
                'available_human' => FileManagerQuotaService::humanReadable($r->available_bytes),
                'usage_percent' => $r->usage_percent,
                'file_count' => $r->file_count,
                'folder_count' => $r->folder_count,
                'quota_status' => $r->quota_status,
                'updated_at' => optional($r->updated_at)->format('d M Y, h:i A'),
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $page,
                'last_page' => max(1, (int) ceil($rows->count() / $perPage)),
                'total' => $rows->count(),
                'per_page' => $perPage,
            ],
            'quick_counts' => [
                'near_limit' => $rows->where('quota_status', 'near_limit')->count(),
                'full' => $rows->whereIn('quota_status', ['full', 'over_limit'])->count(),
                'no_allocation' => $rows->where('quota_status', 'no_allocation')->count(),
                'recently_updated' => $rows->filter(fn ($r) => $r->updated_at && $r->updated_at->gte(now()->subDays(7)))->count(),
            ],
        ]);
    }

    public function updateQuota(UpdateQuotaRequest $request)
    {
        $admin = Admin::findOrFail($request->input('admin_id'));

        $result = DB::transaction(function () use ($admin, $request) {
            $check = $this->quota->validateAllocation($admin, (int) $request->input('allocated_bytes'));

            if (!$check['ok']) {
                return $check;
            }

            $quota = $this->quota->lockQuota($admin);
            $quota->update([
                'allocated_bytes' => (int) $request->input('allocated_bytes'),
                'status' => $request->input('status', $quota->status),
                'updated_by' => $this->actor()->id,
            ]);

            return ['ok' => true, 'message' => null, 'quota' => $quota];
        });

        if (!$result['ok']) {
            return response()->json(['status' => 'error', 'message' => $result['message']], 422);
        }

        return response()->json(['status' => 'success', 'message' => 'Quota updated.']);
    }

    public function userFiles(Request $request, Admin $admin)
    {
        $this->authorizeSettings();

        $items = FileManagerItem::where('owner_id', $admin->id)
            ->orderByRaw("CASE WHEN type = 'folder' THEN 0 ELSE 1 END")
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        $items->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'type' => $item->type,
                'size_human' => $item->isFolder() ? '-' : FileManagerQuotaService::humanReadable((int) $item->size),
                'updated_at' => optional($item->updated_at)->format('d M Y H:i'),
            ];
        });

        return response()->json($items);
    }
}
