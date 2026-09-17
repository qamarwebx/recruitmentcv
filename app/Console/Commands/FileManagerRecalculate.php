<?php

namespace App\Console\Commands;

use App\Models\FileManagerItem;
use App\Services\FileManagerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FileManagerRecalculate extends Command
{
    protected $signature = 'file-manager:recalculate {--purge : Permanently purge trashed items past retention}';

    protected $description = 'Verify File Manager records against physical storage and purge expired trash';

    public function handle(FileManagerService $service)
    {
        $disk = config('file-manager.disk');
        $missing = 0;

        FileManagerItem::withTrashed()
            ->where('type', FileManagerItem::TYPE_FILE)
            ->whereNotNull('storage_path')
            ->chunkById(200, function ($items) use (&$missing, $disk) {
                foreach ($items as $item) {
                    if (!Storage::disk($item->disk ?: $disk)->exists($item->storage_path)) {
                        $missing++;
                        $this->warn("Missing physical file for item #{$item->id} ({$item->name}): {$item->storage_path}");
                    }
                }
            });

        $this->info("Checked records against physical storage. Missing files: {$missing}.");

        if ($this->option('purge')) {
            $retentionDays = (int) config('file-manager.trash_retention_days');

            if ($retentionDays <= 0) {
                $this->info('Trash retention is disabled (0 days); nothing purged.');
                return Command::SUCCESS;
            }

            $expired = FileManagerItem::onlyTrashed()
                ->where('deleted_at', '<=', now()->subDays($retentionDays))
                ->get();

            $purged = 0;
            foreach ($expired as $item) {
                // Skip children here - purge() walks descendants itself, and a
                // child whose ancestor was already purged in this same loop
                // will simply no longer exist by the time we reach it.
                if (!FileManagerItem::withTrashed()->find($item->id)) {
                    continue;
                }

                $service->purge($item);
                $purged++;
            }

            $this->info("Permanently purged {$purged} trashed item(s) older than {$retentionDays} day(s).");
        }

        return Command::SUCCESS;
    }
}
