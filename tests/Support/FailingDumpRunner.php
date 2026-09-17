<?php

namespace Tests\Support;

use App\Services\Backup\Contracts\DumpRunner;
use App\Services\Backup\Exceptions\BackupDumpException;

/**
 * Test double that always fails, for exercising the Backup module's
 * failure path (failed history row recorded, previous backup preserved).
 */
class FailingDumpRunner implements DumpRunner
{
    public function dump(string $destinationAbsolutePath): void
    {
        throw new BackupDumpException('Simulated dump failure for testing.');
    }
}
