<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\FileManagerItem;
use App\Models\FileManagerQuota;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * All storage-number math for the File Manager module lives here so the
 * controller/views never compute usage/quota figures themselves. Usage is
 * always derived from file_manager_items.size (never a cached counter) to
 * avoid drift between the ledger and reality.
 */
class FileManagerQuotaService
{
    public function disk()
    {
        return Storage::disk(config('file-manager.disk'));
    }

    protected function diskRoot(): string
    {
        return config('filesystems.disks.' . config('file-manager.disk') . '.root');
    }

    /**
     * Locks (or creates) the quota row for update. Must be called inside a
     * DB::transaction() by the caller so concurrent uploads can't both read
     * a stale "used" figure before either one commits its new file record.
     */
    public function lockQuota(Admin $admin): FileManagerQuota
    {
        $quota = FileManagerQuota::where('admin_id', $admin->id)->lockForUpdate()->first();

        if (!$quota) {
            $quota = FileManagerQuota::create([
                'admin_id' => $admin->id,
                'allocated_bytes' => (int) config('file-manager.default_quota_bytes'),
                'status' => FileManagerQuota::STATUS_ACTIVE,
            ]);
            $quota = FileManagerQuota::where('admin_id', $admin->id)->lockForUpdate()->first();
        }

        return $quota;
    }

    public function getOrCreateQuota(Admin $admin): FileManagerQuota
    {
        return FileManagerQuota::firstOrCreate(
            ['admin_id' => $admin->id],
            [
                'allocated_bytes' => (int) config('file-manager.default_quota_bytes'),
                'status' => FileManagerQuota::STATUS_ACTIVE,
            ]
        );
    }

    /**
     * Soft-deleted files still consume quota until purged, so this
     * intentionally includes trashed rows.
     */
    public function usedBytes(Admin $admin): int
    {
        return (int) FileManagerItem::withTrashed()
            ->where('owner_id', $admin->id)
            ->where('type', FileManagerItem::TYPE_FILE)
            ->sum('size');
    }

    public function fileCount(Admin $admin): int
    {
        return FileManagerItem::where('owner_id', $admin->id)->where('type', FileManagerItem::TYPE_FILE)->count();
    }

    public function folderCount(Admin $admin): int
    {
        return FileManagerItem::where('owner_id', $admin->id)->where('type', FileManagerItem::TYPE_FOLDER)->count();
    }

    public function availableBytes(Admin $admin): int
    {
        $quota = $this->getOrCreateQuota($admin);
        $used = $this->usedBytes($admin);

        return max(0, $quota->allocated_bytes - $used);
    }

    public function usagePercent(Admin $admin): float
    {
        $quota = $this->getOrCreateQuota($admin);

        if ($quota->allocated_bytes <= 0) {
            return 0.0;
        }

        return round(($this->usedBytes($admin) / $quota->allocated_bytes) * 100, 2);
    }

    public function totalAllocatedBytes(): int
    {
        return (int) FileManagerQuota::sum('allocated_bytes');
    }

    public function totalFileManagerUsageBytes(): int
    {
        return (int) FileManagerItem::withTrashed()->where('type', FileManagerItem::TYPE_FILE)->sum('size');
    }

    public function physicalDiskTotalBytes(): int
    {
        $root = $this->diskRoot();
        if (!is_dir($root)) {
            @mkdir($root, 0755, true);
        }

        $bytes = @disk_total_space($root);

        return $bytes !== false ? (int) $bytes : 0;
    }

    public function physicalDiskFreeBytes(): int
    {
        $root = $this->diskRoot();
        if (!is_dir($root)) {
            @mkdir($root, 0755, true);
        }

        $bytes = @disk_free_space($root);

        return $bytes !== false ? (int) $bytes : 0;
    }

    public function physicalDiskUsedBytes(): int
    {
        return max(0, $this->physicalDiskTotalBytes() - $this->physicalDiskFreeBytes());
    }

    /**
     * physical File Manager capacity - total allocated quota.
     */
    public function availableAllocatableBytes(): int
    {
        return max(0, $this->physicalDiskTotalBytes() - $this->totalAllocatedBytes());
    }

    public function allocationPercent(): float
    {
        $capacity = $this->physicalDiskTotalBytes();
        if ($capacity <= 0) {
            return 0.0;
        }

        return round(($this->totalAllocatedBytes() / $capacity) * 100, 2);
    }

    public function storageSummary(): array
    {
        return [
            'physical_total' => $this->physicalDiskTotalBytes(),
            'physical_used' => $this->physicalDiskUsedBytes(),
            'physical_free' => $this->physicalDiskFreeBytes(),
            'total_allocated' => $this->totalAllocatedBytes(),
            'unallocated' => $this->availableAllocatableBytes(),
            'allocation_percent' => $this->allocationPercent(),
            'file_manager_usage' => $this->totalFileManagerUsageBytes(),
        ];
    }

    /**
     * Never allow allocation to exceed available allocatable storage
     * (unless overallocation is explicitly enabled), and never allow it to
     * drop below the user's current usage.
     */
    public function validateAllocation(Admin $admin, int $newAllocatedBytes): array
    {
        if ($newAllocatedBytes < 0) {
            return ['ok' => false, 'message' => 'Allocation cannot be negative.'];
        }

        $used = $this->usedBytes($admin);
        if ($newAllocatedBytes < $used) {
            return [
                'ok' => false,
                'message' => 'Allocation cannot be reduced below the user\'s current usage (' . $this->humanReadable($used) . ').',
            ];
        }

        if (!config('file-manager.allow_overallocation')) {
            $quota = $this->getOrCreateQuota($admin);
            $availableExcludingThisUser = $this->availableAllocatableBytes() + $quota->allocated_bytes;

            if ($newAllocatedBytes > $availableExcludingThisUser) {
                return [
                    'ok' => false,
                    'message' => 'Allocation exceeds available allocatable storage (' . $this->humanReadable($availableExcludingThisUser) . ' available).',
                ];
            }
        }

        return ['ok' => true, 'message' => null];
    }

    /**
     * Validates a single or batch upload fits within the user's remaining
     * quota. Caller is expected to already hold the locked quota row
     * (see lockQuota()) inside a transaction.
     */
    public function validateUploadFits(Admin $admin, FileManagerQuota $lockedQuota, int $incomingBytes): array
    {
        $used = $this->usedBytes($admin);

        if ($used + $incomingBytes > $lockedQuota->allocated_bytes) {
            return [
                'ok' => false,
                'message' => 'Upload exceeds available quota. Available: ' . $this->humanReadable(max(0, $lockedQuota->allocated_bytes - $used)) . '.',
            ];
        }

        return ['ok' => true, 'message' => null];
    }

    public static function humanReadable(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        $value = $bytes / (1024 ** $power);

        return round($value, $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }
}
