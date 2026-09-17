<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Metawhatsappcampaign;
use App\Models\Metawhatsappapi;
use App\Models\Metawhatsapptemplate;
use App\Models\Lead;
use Carbon\Carbon;
use Log;
use DB;
use App\Jobs\SendMetaLeadJob;

class RunScheduledLeadsCampaignsBack extends Command
{
    protected $signature = 'meta:run-scheduled-leads';
    protected $description = 'Run scheduled WhatsApp campaigns for LEADS only';

    public function handle()
    {
        Log::channel('RunScheduledLeadsCampaigns')
            ->info("⏳ Cron Started: Checking scheduled LEADS campaigns");

        $now = Carbon::now();

        // ---------------------------------------------------
        // Fetch scheduled LEADS campaigns
        // ---------------------------------------------------
        $campaigns = Metawhatsappcampaign::where('audience', 'leads')
            ->where('sch_type', 'Scheduled')
            ->whereBetween('date_and_time', [
                $now->copy()->startOfMinute(),
                $now->copy()->endOfMinute()
            ])
            ->get();

        if ($campaigns->isEmpty()) {
            Log::channel('RunScheduledLeadsCampaigns')
                ->info("⚠ No scheduled campaigns found.");
            return Command::SUCCESS;
        }

        foreach ($campaigns as $campaign) {

            Log::channel('RunScheduledLeadsCampaigns')->info(
                "🚀 Running Scheduled Campaign",
                ['campaign_id' => $campaign->id]
            );

            // ---------------------------------------------------
            // 1. META API
            // ---------------------------------------------------
            $metaAPI = Metawhatsappapi::find($campaign->metaapi_id);

            if (!$metaAPI) {
                Log::channel('RunScheduledLeadsCampaigns')->error(
                    "❌ API not found",
                    ['campaign_id' => $campaign->id]
                );
                continue;
            }

            $endpoint_api = $metaAPI->api_base_url.'/'.$metaAPI->vendor_uid.'/contact/send-template-message';
            $apiData = [
                'base_url'     => $metaAPI->api_base_url,
                'vendor_id'    => $metaAPI->vendor_uid,
                'access_token' => $metaAPI->api_access_token,
                'endpoint_api' => $endpoint_api,
                'token'        => "Authorization: Bearer ".$metaAPI->api_access_token
            ];

            // ---------------------------------------------------
            // 2. META TEMPLATE
            // ---------------------------------------------------
            $metaTemplate = Metawhatsapptemplate::find($campaign->metatemp_id);

            if (!$metaTemplate) {
                Log::channel('RunScheduledLeadsCampaigns')->error(
                    "❌ Template not found",
                    ['campaign_id' => $campaign->id]
                );
                continue;
            }

            // Header file path (same behavior as controller)
            if ($metaTemplate->meta_url_type == 0) {
                $header_file_path = $metaTemplate->whatsapp_file
                    ? url('admin/assets/images/template/'.$metaTemplate->whatsapp_file)
                    : "";
            } elseif ($metaTemplate->meta_url_type == 1) {
                $header_file_path = $metaTemplate->static_url ?? "";
            } else {
                $header_file_path = "";
            }

            $templateData = [
                'header_file_path' => $header_file_path,
                'template_name'    => $metaTemplate->template_name
            ];

            // ---------------------------------------------------
            // 3. FILTER DATA (SAME AS FIRST CODE)
            // ---------------------------------------------------
            $jobTitles        = array_filter(explode(",", $campaign->leads_job_title ?? ""));
            $jobCountries     = array_filter(explode(",", $campaign->leads_country ?? ""));
            $dateRange        = $campaign->leads_date;
            $drivinglicense   = $campaign->lead_driving_license ?? null;
            $contacttypeleads = $campaign->leads_contact_type ?? 1;
            $careoffleads     = array_filter(explode(",", $campaign->lead_careoff_id ?? ""));

            // BASE QUERY
            $leads = Lead::query();

            // Job titles
            if (!empty($jobTitles)) {
                $leads->whereIn('required_service', $jobTitles);
            }

            // Careoff
            if (!empty($careoffleads)) {
                $leads->whereIn('leadassign_id', $careoffleads);
            }

            // Countries (JSON)
            if (!empty($jobCountries)) {
                $leads->whereIn(
                    DB::raw("JSON_UNQUOTE(JSON_EXTRACT(submit_lead_from, '$.country'))"),
                    $jobCountries
                );
            }

            // Date range
            if (!empty($dateRange)) {
                $dates = explode(" to ", $dateRange);
                $start = $dates[0];
                $end   = $dates[1] ?? $dates[0];

                $leads->whereBetween('lead_date', [
                    $start." 00:00:00",
                    $end." 23:59:59"
                ]);
            }

            // Driving license
            if (!empty($drivinglicense)) {
                $leads->filterDrivingLicense($drivinglicense);
            }

            // ---------------------------------------------------
            // 4. CONTACT TYPE LOGIC (🔥 EXACT MATCH)
            // ---------------------------------------------------
            if ($contacttypeleads == 1) {

                $mobileQuery = (clone $leads)
                    ->select('mob_no as phone')
                    ->whereNotNull('mob_no')
                    ->where('mob_no', '!=', '');

                $whatsappQuery = (clone $leads)
                    ->select('whatsapp_no as phone')
                    ->whereNotNull('whatsapp_no')
                    ->where('whatsapp_no', '!=', '');

                $phones = $mobileQuery
                    ->union($whatsappQuery)
                    ->pluck('phone')
                    ->unique();

            } elseif ($contacttypeleads == 2) {

                $phones = (clone $leads)
                    ->whereNotNull('mob_no')
                    ->where('mob_no', '!=', '')
                    ->distinct()
                    ->pluck('mob_no');

            } elseif ($contacttypeleads == 3) {

                $phones = (clone $leads)
                    ->whereNotNull('whatsapp_no')
                    ->where('whatsapp_no', '!=', '')
                    ->distinct()
                    ->pluck('whatsapp_no');

            } else {
                $phones = collect();
            }

            Log::channel('RunScheduledLeadsCampaigns')->info(
                "📌 Unique phones selected",
                ['campaign_id' => $campaign->id, 'count' => $phones->count()]
            );

            if ($phones->isEmpty()) {
                $campaign->update([
                    'message_status' => 'Failed',
                    'message_text'   => 'No Leads Found'
                ]);
                continue;
            }

            // ---------------------------------------------------
            // 5. DISPATCH JOBS (ONE PER UNIQUE PHONE) - UPDATED
            // ---------------------------------------------------

            $delaySeconds = 0;

            foreach ($phones as $phone) {

                $lead = Lead::where('mob_no', $phone)
                            ->orWhere('whatsapp_no', $phone)
                            ->first();

                if (!$lead) {
                    continue;
                }

                // 🔥 Delay logic (correct)
                if ($campaign->delay_frequency === 'Delay 10 to 60 Seconds') {
                    $rand = rand(10, 60);
                } elseif ($campaign->delay_frequency === 'Delay 30 sec to 2 min') {
                    $rand = rand(30, 120);
                } else {
                    $rand = 0;
                }

                $delaySeconds += ($rand + 5); // ✅ only once

                \Log::channel('SendMetaLeadJob')->info("===== Lead Job Dispatch from command =====", [
                    'lead_id'     => $lead->id,
                    'phone'       => $phone,
                    'added_delay' => $rand,
                    'total_delay' => $delaySeconds,
                    'run_at'      => now()->addSeconds($delaySeconds)->toDateTimeString()
                ]);

                SendMetaLeadJob::dispatch(
                    $lead,
                    $phone, // ✅ IMPORTANT (single phone)
                    $apiData,
                    $templateData,
                    $metaTemplate,
                    $campaign
                )
                ->delay(now()->addSeconds($delaySeconds))
                ->onQueue('default');
            }

            // ---------------------------------------------------
            // 6. UPDATE CAMPAIGN STATUS
            // ---------------------------------------------------
            $campaign->update([
                'message_status' => 'success',
                'message_text'   => 'Lead campaign queued successfully'
            ]);

            Log::channel('RunScheduledLeadsCampaigns')->info(
                "✅ Campaign queued",
                ['campaign_id' => $campaign->id]
            );
        }

        Log::channel('RunScheduledLeadsCampaigns')->info("🏁 Cron Completed");
        return Command::SUCCESS;
    }
}
