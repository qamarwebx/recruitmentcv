<?php

namespace App\Services\StorageUsage\Adapters;

use Illuminate\Support\Facades\Storage;

/**
 * For modules that already save through Laravel's Storage facade under a
 * known prefix (currently Testimonial). The directory listing itself is the
 * source of truth, so there's no DB join and no "uncategorized" ambiguity -
 * every file under the prefix belongs to this module.
 */
class DiskPrefixAdapter implements StorageAdapterInterface
{
    public function compute(array $options): array
    {
        $disk = Storage::disk($options['disk']);
        $prefix = trim($options['prefix'], '/');

        $fileCount = 0;
        $sizeBytes = 0;

        foreach ($disk->allFiles($prefix) as $file) {
            $fileCount++;
            $sizeBytes += (int) $disk->size($file);
        }

        return ['file_count' => $fileCount, 'size_bytes' => $sizeBytes, 'claimed_paths' => []];
    }
}
