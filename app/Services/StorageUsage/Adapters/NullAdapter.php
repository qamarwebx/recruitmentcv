<?php

namespace App\Services\StorageUsage\Adapters;

/**
 * For modules that don't store any files today (e.g. Leads, Orders,
 * Employer). Keeps them visible in the dashboard at zero usage so the
 * module list stays complete, and they pick up real numbers automatically
 * the moment config/storage_usage.php points them at a real adapter.
 */
class NullAdapter implements StorageAdapterInterface
{
    public function compute(array $options): array
    {
        return ['file_count' => 0, 'size_bytes' => 0, 'claimed_paths' => []];
    }
}
