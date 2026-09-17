<?php

namespace App\Services\StorageUsage\Adapters;

interface StorageAdapterInterface
{
    /**
     * Compute usage for a single module.
     *
     * Returns ['file_count' => int, 'size_bytes' => int, 'claimed_paths' => string[]].
     * "claimed_paths" is the set of absolute file paths this module's DB rows
     * account for, so the caller can diff a directory listing against it to
     * find files nothing references (reported as Uncategorized). Adapters
     * that own a directory/prefix outright (no DB ambiguity) return [].
     *
     * @param array $options the module's "options" array from config/storage_usage.php
     */
    public function compute(array $options): array;
}
