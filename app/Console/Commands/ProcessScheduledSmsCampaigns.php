<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\SmsCampaign;
use App\Models\SmsApi;
use App\Models\SmsTemplate;
use App\Models\SmsCampaignResponse;
use App\Models\Allcontact;
use App\Models\Associates;
use App\Models\Contactplus;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProcessScheduledSmsCampaigns extends Command
{
    protected $signature = 'sms:process-scheduled';
    protected $description = 'Process all SMS campaigns scheduled for the current minute.';

    public function handle()
    {
        ini_set('memory_limit', '1G');
        gc_enable();

        $now = Carbon::now();
        $this->info("⏱ [START] handle() at {$now->format('H:i:s')}");

        SmsCampaign::where('sch_type', 'Scheduled')
            ->whereBetween('date_and_time', [
                $now->copy()->startOfMinute(),
                $now->copy()->endOfMinute(),
            ])
            ->chunk(10, function ($campaigns) {
                foreach ($campaigns as $campaign) {
                    try {
                        $this->processCampaign($campaign);
                    } catch (\Throwable $e) {
                        Log::error("❌ Campaign {$campaign->id} failed: " . $e->getMessage(), [
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                    gc_collect_cycles();
                }
            });

        $this->info('✅ [END] handle() - All scheduled campaigns processed.');
    }

    /**
     * Process a single scheduled campaign
     */
    protected function processCampaign(SmsCampaign $campaign)
    {
        $this->info("📤 Processing campaign ID: {$campaign->id}");

        $smsAPI = SmsApi::find($campaign->sms_api_id);
        $smsTemplate = SmsTemplate::find($campaign->sms_temp_id);

        if (!$smsAPI || !$smsTemplate) {
            $campaign->update([
                'message_status' => 'Failed',
                'message_text'   => 'Missing SMS API or Template',
            ]);
            Log::warning("⚠️ Missing SMS API or Template for campaign {$campaign->id}");
            return;
        }

        $message  = trim($campaign->msg_body ?? $smsTemplate->message ?? '');
        $audience = $campaign->audience;
        $totalSent = 0;

        /**
         * ✅ 1. Rebuild original Request from stored JSON
         */
        $rawData = json_decode($campaign->raw_request_data ?? '[]', true);
        $request = new Request($rawData);

        if (!$rawData || empty($rawData)) {
            Log::warning("⚠️ No raw_request_data for campaign {$campaign->id}");
            $campaign->update([
                'message_status' => 'Failed',
                'message_text'   => 'Missing stored request data.',
            ]);
            return;
        }

        /**
         * ✅ 2. Collect contacts freshly using stored filters
         */
        $this->info("📞 Collecting contacts for audience: {$audience}");
        $contacts = $this->collectCampaignContacts($request, $audience);

        if (empty($contacts)) {
            $campaign->update([
                'message_status' => 'Failed',
                'message_text'   => 'No valid contacts found.',
            ]);
            SmsCampaignResponse::create([
                'sms_campaign_id' => $campaign->id,
                'message_status'  => 'Failed',
                'message_text'    => 'No valid contacts found.',
            ]);
            Log::warning("⚠️ No valid contacts found for campaign {$campaign->id}");
            return;
        }

        /**
         * ✅ 3. Extract clean numbers
         */
        $mobileNumbers = [];
        foreach ($contacts as $c) {
            $num = $this->cleanNumber($c['mobile'] ?? '');
            if ($num) $mobileNumbers[] = $num;
        }

        $mobileNumbers = array_unique($mobileNumbers);

        if (empty($mobileNumbers)) {
            $campaign->update([
                'message_status' => 'Failed',
                'message_text'   => 'No valid mobile numbers after cleaning.',
            ]);
            Log::warning("⚠️ No valid numbers after cleaning for campaign {$campaign->id}");
            return;
        }

        /**
         * ✅ 4. Send SMS via ZapIM API
         */
        $response = $this->sendSmsViaApi($smsAPI, $message, $mobileNumbers, $campaign->id);
        $totalSent = $response['totalSent'] ?? 0;

        $status = $totalSent > 0 ? 'Success' : 'Failed';
        $msg = "Scheduled SMS sent to {$totalSent} of " . count($mobileNumbers) . " contacts";

        $campaign->update([
            'message_status' => $status,
            'message_text'   => $msg,
        ]);

        Log::info("✅ Campaign {$campaign->id} completed — {$msg}");
        $this->info("✅ Campaign {$campaign->id} completed — {$msg}");
    }

    /**
     * 🔹 Include your collectCampaignContacts() function exactly as is
     * (copy your full version here)
     */
    private function collectCampaignContacts(Request $request, $audience)
    {
        $contacts = [];
    
        /*
        |--------------------------------------------------------------------------
        | LEADS
        |--------------------------------------------------------------------------
        */
        if ($audience == 'Leads') {
            $lead_is_qualified = $request->lead_is_qualified;
            $leadassign_id     = $request->leadassign_id;
            $lead_contact_type = $request->lead_contact_type;
    
            $query = DB::table('leads')
                ->when($lead_is_qualified, fn($q) => $q->whereIn('is_qualified', (array) $lead_is_qualified))
                ->when($leadassign_id, fn($q) => $q->whereIn('leadassign_id', (array) $leadassign_id));
    
            if (empty($lead_contact_type)) {
                $query->where(function ($sub) {
                    $sub->whereNotNull('mob_no')->where('mob_no', '!=', '')
                        ->orWhereNotNull('whatsapp_no')->where('whatsapp_no', '!=', '');
                });
            } elseif ($lead_contact_type == 'mob_no') {
                $query->whereNotNull('mob_no')->where('mob_no', '!=', '');
            } elseif ($lead_contact_type == 'whatsapp_no') {
                $query->whereNotNull('whatsapp_no')->where('whatsapp_no', '!=', '');
            }
    
            $leads = $query->get(['id', 'cand_name', 'mob_no', 'whatsapp_no']);
    
            foreach ($leads as $lead) {
                $name = $lead->cand_name ?? null;
                if ($lead_contact_type == 'mob_no' && $lead->mob_no) {
                    $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->mob_no];
                } elseif ($lead_contact_type == 'whatsapp_no' && $lead->whatsapp_no) {
                    $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->whatsapp_no];
                } else {
                    if ($lead->mob_no) $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->mob_no];
                    if ($lead->whatsapp_no) $contacts[] = ['id' => $lead->id, 'name' => $name, 'mobile' => $lead->whatsapp_no];
                }
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | ALLCONTACT
        |--------------------------------------------------------------------------
        */
        elseif ($audience == 'allcontact') {
            $group        = $request->groupmallc;
            $careoff      = $request->careoff_id2;
            $country      = $request->country_id;
            $leadtype     = $request->lead_type;
            $optinout     = $request->allcontact_subscribe;
            $country_code = $request->country_code;
            $contact_type = $request->allcontact_contact_type;
    
            $query = Allcontact::query();
    
            if ($group) $query->whereIn('group_id', $group);
            if ($careoff) $query->whereIn('careoff_id', $careoff);
            if ($country) $query->whereIn('country_id', $country);
            if ($leadtype) $query->whereIn('lead_type', $leadtype);
            if ($optinout) $query->whereIn('optinout', $optinout);
    
            if ($country_code) {
                $query->where(function ($q) use ($country_code) {
                    foreach ($country_code as $code) {
                        $q->orWhere('primary_no_wsp', 'like', $code.'%')
                        ->orWhere('secondary_no_wsp', 'like', $code.'%')
                        ->orWhere('mobile_no1_wsp', 'like', $code.'%');
                    }
                });
            }
    
            $allcontacts = $query->get(['id', 'full_name', 'mobile_no1_wsp', 'primary_no_wsp', 'secondary_no_wsp']);
    
            foreach ($allcontacts as $c) {
                $name = $c->full_name ?? null;
                if ($contact_type == '4' && $c->mobile_no1_wsp)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->mobile_no1_wsp];
                elseif ($contact_type == '3' && $c->primary_no_wsp)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->primary_no_wsp];
                elseif ($contact_type == '2' && $c->secondary_no_wsp)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->secondary_no_wsp];
                else {
                    if ($c->mobile_no1_wsp) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->mobile_no1_wsp];
                    if ($c->primary_no_wsp) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->primary_no_wsp];
                    if ($c->secondary_no_wsp) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->secondary_no_wsp];
                }
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | CONTACT+
        |--------------------------------------------------------------------------
        */
        elseif ($audience == 'contactp') {
            $group     = $request->contact_group;
            $business  = $request->business_type_contact;
            $careoff   = $request->careoff_id;
            $subscribe = $request->contactp_subscribe;
            $type      = $request->contactp_contact_type;
    
            $query = Contactplus::query();
            if ($group) $query->whereIn('group_id', $group);
            if ($business) $query->whereIn('businesstype_id', $business);
            if ($careoff) $query->whereIn('careoff_id', $careoff);
            if ($subscribe) $query->whereIn('subscribe', $subscribe);
    
            $contactsPlus = $query->get(['id', 'office_eng_name', 'owner_contact', 'prim_contact', 'sec_contact']);
    
            foreach ($contactsPlus as $c) {
                $name = $c->office_eng_name ?? null;
                if ($type == '4' && $c->sec_contact)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->sec_contact];
                elseif ($type == '3' && $c->prim_contact)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->prim_contact];
                elseif ($type == '2' && $c->owner_contact)
                    $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->owner_contact];
                else {
                    if ($c->owner_contact) $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->owner_contact];
                    if ($c->prim_contact)  $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->prim_contact];
                    if ($c->sec_contact)   $contacts[] = ['id' => $c->id, 'name' => $name, 'mobile' => $c->sec_contact];
                }
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | ASSOCIATES, CLIENTS, PARTNERS
        |--------------------------------------------------------------------------
        */
        elseif ($audience == 'associate') {
            $list = Associates::get(['id', 'pty_full_name', 'mobile_no']);
            foreach ($list as $c) {
                if ($c->mobile_no)
                    $contacts[] = ['id' => $c->id, 'name' => $c->pty_full_name, 'mobile' => $c->mobile_no];
            }
        } elseif ($audience == 'client') {
            $list = User::get(['id', 'name', 'mobile_no']);
            foreach ($list as $c) {
                if ($c->mobile_no)
                    $contacts[] = ['id' => $c->id, 'name' => $c->name, 'mobile' => $c->mobile_no];
            }
        } elseif ($audience == 'partner') {
            $list = Partner::get(['id', 'owner_name', 'mobile_no']);
            foreach ($list as $c) {
                if ($c->mobile_no)
                    $contacts[] = ['id' => $c->id, 'name' => $c->owner_name, 'mobile' => $c->mobile_no];
            }
        }
    
        /*
        |--------------------------------------------------------------------------
        | CLEAN + UNIQUE
        |--------------------------------------------------------------------------
        */
        $contacts = collect($contacts)
            ->filter(fn($c) => !empty($c['mobile']))
            ->unique('mobile')
            ->values()
            ->toArray();
    
        return $contacts;
    }

    /**
     * ✅ Clean number
     */
    protected function cleanNumber($num)
    {
        if (!is_scalar($num) || empty($num)) return null;

        $num = trim((string)$num);
        $num = preg_replace(
            ['/^\+?91|^\+?971|^\+?966|^\+?1|^\+?44|^\+?880|^\+?94|^\+?92|^\+?60|^\+?65|^\+?81|^\+?20/', '/\D/'],
            '',
            $num
        );
        return strlen($num) >= 8 ? $num : null;
    }

    /**
     * ✅ Send SMS via ZapIM (your existing working version)
     */
    protected function sendSmsViaApi($smsAPI, $message, array $mobileNumbers, $campaignId){

        $totalSent = 0;
    
        try {
            $base_url = $smsAPI->api_base_url ?? "https://qlogin.zapim.com";
            $send_url = rtrim($base_url, '/') . '/api/v2/SendSMS';
    
            // ✅ Clean & validate numbers
            $cleanNumbers = [];
            foreach ($mobileNumbers as $num) {
                if (is_array($num)) $num = $num['mobile'] ?? '';
    
                if (!is_scalar($num) || empty($num)) continue;
    
                $num = trim((string) $num);
                $num = preg_replace(
                    '/^\+?91|^\+?971|^\+?966|^\+?1|^\+?44|^\+?880|^\+?94|^\+?92|^\+?60|^\+?65|^\+?81|^\+?20/',
                    '',
                    $num
                );
                $num = preg_replace('/\D/', '', $num);
    
                if (strlen($num) >= 8) $cleanNumbers[] = $num;
            }
    
            $cleanNumbers = array_unique($cleanNumbers);
            if (empty($cleanNumbers)) {
                \Log::warning('No valid mobile numbers found for SMS sending.');
                return ['totalSent' => 0];
            }
    
            $chunks = array_chunk($cleanNumbers, 100);


            foreach ($chunks as $key => $batch) {

                $payload = [
                    "senderId" => $smsAPI->sender_id ?? "QAMR",
                    "is_Unicode" => false,
                    "is_Flash" => false,
                    "isRegisteredForDelivery" => true,
                    "validityPeriod" => 1440,
                    "dataCoding" => 0,
                    "schedTime" => "",
                    "groupId" => "7",
                    "message" => $message,
                    "mobileNumbers" => implode(',', $batch),
                    "principleEntityId" => $smsAPI->entity_id ?? "1101647460000048498",
                    "templateId" => $smsAPI->template_id ?? "1107176190670884363",
                    "apiKey" => $smsAPI->api_key ?? "+5vVPV8PYZ4UEU/4MbBDUl3ci/wal1pRnsg2/cBixfk=",
                    "clientId" => $smsAPI->client_id ?? "e4b5a2f4-08cd-4159-8807-cb917e74666f"
                ];
    
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ])->timeout(25)->post($send_url, $payload);
    
                    $data = $response->json();
    
                    if ($response->successful() && isset($data['ErrorCode'])) {
                        $errorCode = $data['ErrorCode'];
                        $errorDesc = $data['ErrorDescription'];
    
                        // ✅ Success case: ErrorCode 0
                        if ($errorCode == 0 && !empty($data['Data'])) {
                            foreach ($data['Data'] as $item) {
                                $msgStatus = ($item['MessageErrorCode'] == 0) ? 'success' : 'failed';
                                $msgText = $item['MessageErrorDescription'] ?? 'Unknown';
                                $mobile = $item['MobileNumber'] ?? null;
    
                                SmsCampaignResponse::create([
                                    'sms_campaign_id' => $campaignId,
                                    'message_status'  => $msgStatus,
                                    'message_text'    => $msgText,
                                    'mobile_no'       => $mobile,
                                    'main_response'   => json_encode($item),
                                    'error_data_field'=> ($item['MessageErrorCode'] == 0) ? null : $item['MessageErrorDescription']
                                ]);
    
                                if ($msgStatus === 'success') $totalSent++;
                            }
                        }
    
                        // ❌ Invalid numbers or API-level error
                        elseif ($errorCode != 0) {
                            foreach ($batch as $num) {
                                SmsCampaignResponse::create([
                                    'sms_campaign_id' => $campaignId,
                                    'message_status'  => 'failed',
                                    'message_text'    => $errorDesc ?? 'Zapim API Error',
                                    'mobile_no'       => $num,
                                    'main_response'   => json_encode($data),
                                    'error_data_field'=> "ErrorCode: {$errorCode}"
                                ]);
                            }
                        }
    
                        // ❌ Empty Data array
                        else {
                            foreach ($batch as $num) {
                                SmsCampaignResponse::create([
                                    'sms_campaign_id' => $campaignId,
                                    'message_status'  => 'failed',
                                    'message_text'    => 'Empty response Data from Zapim',
                                    'mobile_no'       => $num,
                                    'main_response'   => json_encode($data),
                                    'error_data_field'=> 'Empty Data'
                                ]);
                            }
                        }
    
                    } else {
                        // ❌ HTTP Error
                        foreach ($batch as $num) {
                            SmsCampaignResponse::create([
                                'sms_campaign_id' => $campaignId,
                                'message_status'  => 'failed',
                                'message_text'    => 'HTTP Error from Zapim',
                                'mobile_no'       => $num,
                                'main_response'   => $response->body(),
                                'error_data_field'=> 'HTTP failure'
                            ]);
                        }
                    }
    
                } catch (\Throwable $e) {
                    foreach ($batch as $num) {
                        SmsCampaignResponse::create([
                            'sms_campaign_id' => $campaignId,
                            'message_status'  => 'failed',
                            'message_text'    => $e->getMessage(),
                            'mobile_no'       => $num,
                            'error_data_field'=> 'Exception during batch sending'
                        ]);
                    }
    
                    \Log::error('Zapim Batch Exception', [
                        'error' => $e->getMessage(),
                        'payload' => $payload
                    ]);
                }
            }
        } catch (\Throwable $ex) {
            \Log::error('sendSmsViaApi() Exception', [
                'error' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString()
            ]);
    
            SmsCampaignResponse::create([
                'sms_campaign_id' => $campaignId,
                'message_status'  => 'failed',
                'message_text'    => 'Exception during API sending: ' . $ex->getMessage(),
                'mobile_no'       => null,
                'main_response'   => null,
                'error_data_field'=> 'Exception'
            ]);
        }
    
        return ['totalSent' => $totalSent];
    }
}
