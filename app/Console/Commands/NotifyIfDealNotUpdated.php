<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DealPipeline;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyIfDealNotUpdated extends Command
{
    protected $signature = 'deals:notify-not-updated';
    protected $description = 'Notify if any deal is not updated for 6 days';

    public function handle()
    {
        try {

            $sixDaysAgo = Carbon::now()->subDays(6);

            // 👉 get stale deals
            $deals = DealPipeline::with('careOf')
                ->where('updated_at', '<=', $sixDaysAgo)
                ->get();

            if ($deals->isEmpty()) {
                $this->info('No inactive deals found.');
                return;
            }

            Log::info("Deal #{$deals->count()} deals not updated for 6 days. Processing notifications...");


            foreach ($deals as $deal) {

                try {

                    // skip if no assigned admin
                    if (!$deal->care_of) continue;

                    $admin = Admin::find($deal->care_of);

                    if (!$admin || !$admin->working_email) continue;

                    /* --------------------------
                       👉 LOG
                    -------------------------- */
                    Log::info("Deal #{$deal->id} not updated for 6 days. Notified Admin #{$admin->id}");

                    /* --------------------------
                       👉 EMAIL SEND
                    -------------------------- */
                    Mail::to($admin->working_email)
                        ->send(new \App\Mail\DealNotUpdatedMail($deal));

                } catch (\Exception $e) {

                    // per deal error log
                    Log::error("Deal Notify Error (Deal ID: {$deal->id}) - " . $e->getMessage());

                }
            }

            $this->info('Inactive deal notifications processed.');

        } catch (\Exception $e) {

            // global error log
            Log::error("NotifyIfDealNotUpdated Cron Failed - " . $e->getMessage());

            $this->error('Something went wrong while processing deals.');

        }
    }
}