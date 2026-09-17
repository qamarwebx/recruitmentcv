<?php

namespace App\Services\Backup\Contracts;

interface DumpRunner
{
    /**
     * Dump the configured database to $destinationAbsolutePath (a gzip file),
     * writing directly to disk - never buffering the dump content in PHP
     * memory. Must throw \App\Services\Backup\Exceptions\BackupDumpException
     * (with a message that never contains credentials) on any failure.
     */
    public function dump(string $destinationAbsolutePath): void;
}
