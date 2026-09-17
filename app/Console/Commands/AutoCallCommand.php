<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\AllContact;
use App\Jobs\AiCallJob;

class AutoCallCommand extends Command
{
    /**
     * Command Signature
     */
    protected $signature = 'auto:call';

    /**
     * Command Description
     */
    protected $description = 'Automatically dispatch pending contacts for AI outbound calling';

    public function handle()
    {
        Log::channel('ai_call_log')->info('=========================================================');
        Log::channel('ai_call_log')->info('AUTO CALL COMMAND STARTED');
        Log::channel('ai_call_log')->info('Started At : '.now());

        $this->info('Auto Call Started...');

        try {

            $limit = env('AUTO_CALL_LIMIT', 20);

            Log::channel('ai_call_log')->info('Fetching Pending Contacts', [
                'limit' => $limit
            ]);

            $contacts = AllContact::where('ai_call_status', 'pending')
                ->whereNotNull('primary_no_wsp')
                ->where('primary_no_wsp', '!=', '')
                ->orderBy('id')
                ->limit($limit)
                ->get();

            Log::channel('ai_call_log')->info('Pending Contacts Found', [
                'count' => $contacts->count()
            ]);

            if ($contacts->isEmpty()) {

                Log::channel('ai_call_log')->info('No Pending Contacts Found.');

                $this->info('No Pending Contacts.');

                return Command::SUCCESS;
            }

            foreach ($contacts as $contact) {

                try {

                    Log::channel('ai_call_log')->info('------------------------------------------');

                    Log::channel('ai_call_log')->info('Processing Contact', [
                        'contact_id' => $contact->id,
                        'name'       => $contact->name ?? '',
                        'mobile'     => $contact->primary_no_wsp,
                    ]);

                    if (empty($contact->primary_no_wsp)) {

                        Log::channel('ai_call_log')->warning('Skipped (Empty Number)', [
                            'contact_id' => $contact->id
                        ]);

                        continue;
                    }

                    // Mark queued
                    $contact->update([
                        'ai_call_status' => 'queued'
                    ]);

                    Log::channel('ai_call_log')->info('Status Updated', [
                        'contact_id' => $contact->id,
                        'status'     => 'queued'
                    ]);

                    AiCallJob::dispatch($contact->id);

                    Log::channel('ai_call_log')->info('Job Dispatched', [
                        'contact_id' => $contact->id
                    ]);

                    $this->line("Queued Contact #{$contact->id}");

                } catch (\Exception $e) {

                    Log::channel('ai_call_log')->error('Queue Failed', [
                        'contact_id' => $contact->id,
                        'message'    => $e->getMessage(),
                        'file'       => $e->getFile(),
                        'line'       => $e->getLine(),
                    ]);

                    $contact->update([
                        'ai_call_status'   => 'failed',
                        'ai_call_response' => json_encode([
                            'error' => $e->getMessage()
                        ])
                    ]);
                }

            }

            Log::channel('ai_call_log')->info('AUTO CALL COMMAND COMPLETED', [
                'total_dispatched' => $contacts->count()
            ]);

            Log::channel('ai_call_log')->info('Finished At : '.now());

            $this->info("Completed. {$contacts->count()} contacts queued.");

            return Command::SUCCESS;

        } catch (\Exception $e) {

            Log::channel('ai_call_log')->critical('AUTO CALL COMMAND FAILED', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}