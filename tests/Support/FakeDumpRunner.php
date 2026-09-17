<?php

namespace Tests\Support;

use App\Services\Backup\Contracts\DumpRunner;

/**
 * Test double for App\Services\Backup\Contracts\DumpRunner - writes a small
 * placeholder file instead of shelling out to a real mysqldump, so the
 * Backup Feature tests exercise the full dump -> move -> record -> retain
 * pipeline without depending on the mysqldump binary or the real database.
 */
class FakeDumpRunner implements DumpRunner
{
    public function dump(string $destinationAbsolutePath): void
    {
        file_put_contents($destinationAbsolutePath, 'fake-dump-content-'.uniqid());
    }
}
