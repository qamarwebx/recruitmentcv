<?php

namespace App\Http\Controllers;

use App\Jobs\RecalculateStorageUsageJob;
use App\Models\Adminpermission;
use App\Services\FileManagerQuotaService;
use App\Services\StorageUsage\StorageUsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StorageUsageController extends Controller
{
    public function __construct(protected StorageUsageService $service)
    {
    }

    /**
     * Returns the acting staff member's permission row (or null for a
     * user_type=1 superadmin, who bypasses per-field checks entirely).
     * The sidebar partial pulled in by the layout reads $permission itself,
     * so every view-rendering action must pass this along - otherwise the
     * whole staff menu silently renders empty instead of just missing this
     * one item.
     */
    protected function authorizeAccess(): ?Adminpermission
    {
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        $allowed = $user->user_type == 1
            || (isset($permission) && $permission->full_access == 1)
            || (isset($permission) && $permission->storage_usage_setting == 1);

        if (!$allowed) {
            abort(403, 'You do not have permission to access Storage Usage.');
        }

        return $permission;
    }

    protected function statusRunning(): bool
    {
        return (bool) Cache::get(config('storage_usage.lock_key') . ':running', false);
    }

    public function index()
    {
        $permission = $this->authorizeAccess();

        $summary = $this->service->summary();

        $summaryHuman = [
            'total_human' => FileManagerQuotaService::humanReadable($summary['total_bytes']),
            'used_human' => FileManagerQuotaService::humanReadable($summary['used_bytes']),
            'free_human' => FileManagerQuotaService::humanReadable($summary['free_bytes']),
            'usage_percent' => $summary['usage_percent'],
            'last_calculated_at' => optional($summary['last_calculated_at'])->format('d M Y, h:i A'),
        ];

        $modules = $summary['modules']->map(function ($row) {
            return [
                'module_key' => $row->module_key,
                'label' => $row->label,
                'file_count' => $row->file_count,
                'size_bytes' => $row->size_bytes,
                'size_human' => FileManagerQuotaService::humanReadable($row->size_bytes),
                'updated_at' => optional($row->last_calculated_at)->format('d M Y, h:i A'),
            ];
        })->values();

        return view('admin.settings.storage_usage.index', [
            'summaryHuman' => $summaryHuman,
            'modules' => $modules,
            'isRunning' => $this->statusRunning(),
            'permission' => $permission,
        ]);
    }

    public function json(Request $request)
    {
        $this->authorizeAccess();

        $summary = $this->service->summary();
        $rows = $summary['modules'];

        if ($request->filled('search')) {
            $search = mb_strtolower($request->input('search'));
            $rows = $rows->filter(fn ($r) => str_contains(mb_strtolower($r->label), $search));
        }

        $sort = $request->input('sort', 'size_desc');
        $rows = match ($sort) {
            'size_asc' => $rows->sortBy('size_bytes'),
            'name' => $rows->sortBy('label'),
            'files' => $rows->sortByDesc('file_count'),
            default => $rows->sortByDesc('size_bytes'),
        };

        $data = $rows->values()->map(function ($row) {
            return [
                'module_key' => $row->module_key,
                'label' => $row->label,
                'file_count' => $row->file_count,
                'size_bytes' => $row->size_bytes,
                'size_human' => FileManagerQuotaService::humanReadable($row->size_bytes),
                'updated_at' => optional($row->last_calculated_at)->format('d M Y, h:i A'),
            ];
        });

        return response()->json([
            'data' => $data,
            'summary' => [
                'total_bytes' => $summary['total_bytes'],
                'used_bytes' => $summary['used_bytes'],
                'free_bytes' => $summary['free_bytes'],
                'usage_percent' => $summary['usage_percent'],
                'total_human' => FileManagerQuotaService::humanReadable($summary['total_bytes']),
                'used_human' => FileManagerQuotaService::humanReadable($summary['used_bytes']),
                'free_human' => FileManagerQuotaService::humanReadable($summary['free_bytes']),
                'last_calculated_at' => optional($summary['last_calculated_at'])->format('d M Y, h:i A'),
            ],
        ]);
    }

    public function recalculate()
    {
        $this->authorizeAccess();

        if ($this->statusRunning()) {
            return response()->json(['status' => 'already_running', 'message' => 'A recalculation is already in progress.']);
        }

        RecalculateStorageUsageJob::dispatch();

        return response()->json(['status' => 'queued', 'message' => 'Recalculation started.']);
    }

    public function status()
    {
        $this->authorizeAccess();

        $summary = $this->service->summary();

        return response()->json([
            'running' => $this->statusRunning(),
            'last_calculated_at' => optional($summary['last_calculated_at'])->format('d M Y, h:i A'),
        ]);
    }
}
