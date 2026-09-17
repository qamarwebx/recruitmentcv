<?php

namespace App\Services\StorageUsage\Adapters;

use App\Models\FileManagerItem;
use App\Services\FileManagerQuotaService;

/**
 * File Manager already tracks a per-file "size" column, so usage is a plain
 * SUM() query - no filesystem scan needed here at all.
 */
class FileManagerUsageAdapter implements StorageAdapterInterface
{
    public function __construct(protected FileManagerQuotaService $quota)
    {
    }

    public function compute(array $options): array
    {
        return [
            // withTrashed() to match totalFileManagerUsageBytes() below - soft-deleted
            // files still consume quota until purged, so both figures must agree.
            'file_count' => FileManagerItem::withTrashed()->where('type', FileManagerItem::TYPE_FILE)->count(),
            'size_bytes' => $this->quota->totalFileManagerUsageBytes(),
            'claimed_paths' => [],
        ];
    }
}
