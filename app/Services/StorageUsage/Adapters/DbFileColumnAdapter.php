<?php

namespace App\Services\StorageUsage\Adapters;

use App\Services\StorageUsage\LegacyRootResolver;
use Illuminate\Support\Facades\DB;

/**
 * Derives usage from filename/path columns on one or more legacy tables.
 * None of these tables store a file size, so each referenced file is
 * stat()'d on disk. Runs only from the background recalculation job/command,
 * never on a dashboard page load.
 */
class DbFileColumnAdapter implements StorageAdapterInterface
{
    public function __construct(protected LegacyRootResolver $roots)
    {
    }

    public function compute(array $options): array
    {
        // path => size, keyed to avoid double-counting a physical file that
        // more than one DB row happens to reference.
        $claimed = [];

        foreach ($options['sources'] ?? [] as $source) {
            $root = $this->roots->resolve($source['root']);
            $dir = $root . '/' . trim($source['scan_dir'], '/');

            $values = DB::table($source['table'])
                ->whereNotNull($source['column'])
                ->where($source['column'], '!=', '')
                ->pluck($source['column']);

            foreach ($values as $value) {
                // Multi-file columns (e.g. Expense receipt_file) store a JSON
                // array of filenames instead of one bare filename/path - decode
                // and treat each entry like its own value. Anything that isn't
                // a JSON array (the common case) falls through unchanged.
                $decoded = json_decode((string) $value, true);
                $entries = is_array($decoded) ? $decoded : [$value];

                foreach ($entries as $entry) {
                    $entry = trim((string) $entry);
                    if ($entry === '') {
                        continue;
                    }

                    // Some columns (e.g. Contacts) already store a path relative
                    // to the root ("uploads/contacts/x.pdf"); others store just
                    // a filename that lives directly under the module's dir.
                    $path = str_contains($entry, '/')
                        ? $root . '/' . ltrim($entry, '/')
                        : $dir . '/' . basename($entry);

                    if (!is_file($path)) {
                        continue;
                    }

                    $size = @filesize($path);
                    if ($size === false) {
                        continue;
                    }

                    $claimed[realpath($path) ?: $path] = $size;
                }
            }
        }

        return [
            'file_count' => count($claimed),
            'size_bytes' => array_sum($claimed),
            'claimed_paths' => array_keys($claimed),
        ];
    }

}
