<?php

namespace App\Services\StorageUsage;

use App\Models\Basepathstatus;

/**
 * Several legacy controllers (Deal, Todo, Candidate uploads) write under
 * either /public or /public_html depending on this single-row admin toggle.
 * Centralized here so every Storage Usage adapter resolves it the same way.
 */
class LegacyRootResolver
{
    public function resolve(string $type): string
    {
        if ($type === 'public') {
            return rtrim(public_path(), '/');
        }

        $status = Basepathstatus::first();
        $usePrimary = !$status || (int) $status->base_path_status === 1;

        return $usePrimary ? base_path('public') : base_path('public_html');
    }
}
