<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailCampaign;
use App\Models\EmailSmtp;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProcessScheduledEmailCampaigns extends Command
{
    protected $signature = 'email:process-scheduled';
    protected $description = 'Process scheduled email campaigns at the exact minute';

    protected int $chunkSize = 100;

    public function handle()
    {
        $now = Carbon::now();

        // Fetch scheduled campaigns that match this minute
        $campaigns = EmailCampaign::where('schedule_type', 'Scheduled')   // UPDATED
            ->where('email_status', 'Scheduled')
            ->whereBetween('schedule_datetime', [
                $now->copy()->startOfMinute(),
                $now->copy()->endOfMinute()
            ])
            ->get();

        if ($campaigns->isEmpty()) {
            $this->info("No scheduled email campaigns to process this minute.");
            return;
        }

        foreach ($campaigns as $campaign) {

            try {
                $this->info("Processingn Email Campaign ID: {$campaign->id}");
                Log::info("Processing Email Campaign ID: {$campaign->id}");

                // Atomically claim campaign
                $claimed = EmailCampaign::where('id', $campaign->id)
                    ->where('email_status', 'Scheduled')
                    ->update([
                        'email_status'   => 'Processing',
                        'email_response' => 'Scheduler started at ' . Carbon::now(),
                    ]);

                if (!$claimed) {
                    $this->info("Campaign {$campaign->id} already claimed. Skipping.");
                    continue;
                }

                // Reload after claiming
                $campaign = EmailCampaign::find($campaign->id);

                // Load controller (correct namespace)
                $controller = app(\App\Http\Controllers\EmailCampaignController::class);

                // Get SMTP
                $smtp = EmailSmtp::find($campaign->smtp_id);

                if (!$smtp) {
                    $campaign->update([
                        'email_status'   => 'Failed',
                        'email_response' => 'Invalid SMTP configuration'
                    ]);
                    continue;
                }

                // Convert JSON → Fake Request
                $requestData = json_decode($campaign->raw_request_data ?? '{}', true) ?: [];
                $fakeRequest = new Request($requestData);

                // Collect emails
                $emails = $controller->collectCampaignEmails($fakeRequest, $campaign->audience);

                if (empty($emails)) {
                    $campaign->update([
                        'email_status'   => 'Failed',
                        'email_response' => 'No valid recipients found'
                    ]);
                    continue;
                }

                $this->info("Recipients found: " . count($emails));

                // Chunk recipients
                $chunks = array_chunk($emails, $this->chunkSize);

                $totalSuccess = 0;
                $totalFailed  = 0;

                foreach ($chunks as $i => $chunk) {

                    $this->info("Sending chunk " . ($i + 1) . " of " . count($chunks));

                    $result = $controller->sendBulkEmails(
                        $smtp,
                        $chunk,
                        $campaign->email_subject,
                        $campaign->email_body,
                        $campaign->id,
                        $campaign->attachment
                    );

                    $totalSuccess += $result['success'] ?? 0;
                    $totalFailed  += $result['failed'] ?? 0;
                }

                // Update final campaign status
                $campaign->update([
                    'email_status'   => $totalSuccess > 0 ? 'Success' : 'Failed',
                    'email_response' => "{$totalSuccess} sent, {$totalFailed} failed."
                ]);

                $this->info("EmailCampaign {$campaign->id} completed.");
                Log::info("EmailCampaign {$campaign->id} completed.");


            } catch (\Throwable $e) {

                Log::error("Scheduled Email Error: Campaign #{$campaign->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                $campaign->update([
                    'email_status'   => 'Failed',
                    'email_response' => 'Exception: ' . $e->getMessage()
                ]);

                $this->error("Campaign {$campaign->id} failed: " . $e->getMessage());
            }
        }

        $this->info("All campaigns for this minute processed.");
    }
}
