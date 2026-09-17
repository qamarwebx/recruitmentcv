<?php

namespace App\Services\StorageUsage;

use App\Models\StorageUsageSnapshot;
use App\Services\StorageUsage\Adapters\DbFileColumnAdapter;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Orchestrates the per-module adapters declared in config/storage_usage.php
 * and persists their results into storage_usage_snapshots. Only ever called
 * from the scheduled command / "Recalculate" job - the dashboard itself
 * only ever reads the persisted snapshot rows (see summary()).
 */
class StorageUsageService
{
    public function __construct(protected LegacyRootResolver $roots)
    {
    }

    public function recalculate(): void
    {
        $modules = config('storage_usage.modules', []);
        $now = now();

        $allClaimed = [];
        $watchedDirs = [];

        foreach ($modules as $key => $definition) {
            $adapter = app($definition['adapter']);
            $result = $adapter->compute($definition['options'] ?? []);

            StorageUsageSnapshot::updateOrCreate(
                ['module_key' => $key],
                [
                    'label' => $definition['label'],
                    'file_count' => $result['file_count'],
                    'size_bytes' => $result['size_bytes'],
                    'last_calculated_at' => $now,
                ]
            );

            foreach ($result['claimed_paths'] ?? [] as $path) {
                $allClaimed[$path] = true;
            }

            if ($adapter instanceof DbFileColumnAdapter) {
                foreach ($definition['options']['sources'] ?? [] as $source) {
                    $root = $this->roots->resolve($source['root']);
                    $dir = $root . '/' . trim($source['scan_dir'], '/');
                    $watchedDirs[$dir] = true;
                }
            }
        }

        [$uncategorizedCount, $uncategorizedSize] = $this->computeUncategorized(array_keys($watchedDirs), $allClaimed);

        StorageUsageSnapshot::updateOrCreate(
            ['module_key' => StorageUsageSnapshot::UNCATEGORIZED_KEY],
            [
                'label' => config('storage_usage.uncategorized_label', 'Uncategorized'),
                'file_count' => $uncategorizedCount,
                'size_bytes' => $uncategorizedSize,
                'last_calculated_at' => $now,
            ]
        );
    }

    /**
     * Any file physically present in a module's known upload directory that
     * no DB row (from any module) claims is reported as Uncategorized,
     * per the module's own "if it can't be confidently mapped" requirement.
     */
    protected function computeUncategorized(array $dirs, array $claimed): array
    {
        $count = 0;
        $size = 0;

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $fileInfo) {
                if (!$fileInfo->isFile()) {
                    continue;
                }

                $path = $fileInfo->getRealPath() ?: $fileInfo->getPathname();
                if (isset($claimed[$path])) {
                    continue;
                }

                $count++;
                $size += $fileInfo->getSize();
            }
        }

        return [$count, $size];
    }

    /**
     * Dashboard-facing read: purely reads persisted snapshot rows plus a
     * cheap disk_total_space()/disk_free_space() call - no scanning.
     */
    public function summary(): array
    {
        $rows = StorageUsageSnapshot::orderByDesc('size_bytes')->get();

        $used = (int) $rows->sum('size_bytes');
        $total = $this->physicalDiskTotalBytes();
        $free = $this->physicalDiskFreeBytes();

        return [
            'modules' => $rows,
            'total_bytes' => $total,
            'used_bytes' => $used,
            'free_bytes' => $free,
            'usage_percent' => $total > 0 ? round(($used / $total) * 100, 2) : 0.0,
            'last_calculated_at' => $rows->max('last_calculated_at'),
        ];
    }

    public function physicalDiskTotalBytes(): int
    {
        $bytes = @disk_total_space(base_path());

        return $bytes !== false ? (int) $bytes : 0;
    }

    public function physicalDiskFreeBytes(): int
    {
        $bytes = @disk_free_space(base_path());

        return $bytes !== false ? (int) $bytes : 0;
    }
}
