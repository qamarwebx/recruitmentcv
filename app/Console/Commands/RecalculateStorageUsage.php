<?php

namespace App\Console\Commands;

use App\Jobs\RecalculateStorageUsageJob;
use Illuminate\Console\Command;

class RecalculateStorageUsage extends Command
{
    protected $signature = 'storage-usage:recalculate';

    protected $description = 'Recompute module-wise storage usage stats used by Settings > Storage Usage';

    public function handle()
    {
        RecalculateStorageUsageJob::dispatch();

        $this->info('Storage usage recalculation dispatched.');

        return Command::SUCCESS;
    }
}
