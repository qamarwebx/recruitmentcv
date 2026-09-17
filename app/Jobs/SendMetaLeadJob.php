<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\Admin;
use App\Models\Country;
use App\Models\City;
use App\Models\Whatsappchaturl;
use App\Models\Metawhatsappcampaign;
use App\Models\Metawhatsappcampaignresponse;
use App\Models\TeamMemberWebPage;
use App\Models\ImageUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SendMetaLeadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lead, $phone, $apiData, $templateData, $metaTemplate, $campaign;

    public function __construct($lead, $phone, $apiData, $templateData, $metaTemplate, $campaign)
    {
        $this->lead         = $lead;
        $this->phone        = $phone; // ✅ REQUIRED
        $this->apiData      = $apiData;
        $this->templateData = $templateData;
        $this->metaTemplate = $metaTemplate;
        $this->campaign     = $campaign;
    }

    public function handle()
    {
        /* =====================================================
        | START LOG
        =====================================================*/
        \Log::channel('SendMetaLeadJob')->info("===== Lead Job Started =====", [
            'lead_id'     => $this->lead->id,
            'campaign_id' => $this->campaign->id,
            'template'    => $this->metaTemplate->template_name,
        ]);

        /* =====================================================
        | BASIC VALUES
        =====================================================*/
        $random = Str::random(32);
        $unsubscribeUrl = "whatsapp/unsubscribe/request/leads/{$random}/{$this->lead->id}";

        // Default dynamic chat URL (lead assigned careoff)
        $leadChat = Whatsappchaturl::where('staff_id', $this->lead->leadassign_id)
            ->where('status', true)
            ->latest('id')
            ->first();

        $leadChatUrl =  '';
        
        if(isset($leadChat->chat_url)){
            $leadChatUrl = str_replace('https://crm.qamarhire.com/', '', $leadChat->chat_url);
        }else{
            $leadChatUrl = "https://wa.me";
        }
        
        $defaultDynamicChatUrl = $leadChatUrl ?? 'https://wa.me';

        // Dynamic Webpage Team URL
        $teamMemberPage = TeamMemberWebPage::where('staff_id', $this->lead->leadassign_id)->latest('id')->first();
        if ($teamMemberPage) {
            $dynamicWebpageTeam = "https://qamrjob.com/housedriver/" . intval($teamMemberPage->staff_id);
        } else {
            $dynamicWebpageTeam = '';
        }

         // Dynamic Image URL
         $image_url = ImageUrl::where('staff_id', $this->lead->leadassign_id)->latest('id')->first();
         if ($image_url) {
             $dynamic_image_url = $image_url->url;
         } else {
             $dynamic_image_url = '';
         }


        $countryName = optional(Country::find($this->lead->country_id))->name ?? '';
        $cityName    = optional(City::find($this->lead->city_id))->name ?? '';

        /* =====================================================
        | LEAD ASSIGNED CAREOFF
        =====================================================*/
        $careoffName = '';
        $careoffContact1 = '';
        $careoffContact2 = '';
        $country_as_per_location = '---';
        $state_as_per_location   = '---';
        $city_as_per_location    = '---';
        $licenses = [];

        if (!empty($this->lead->saudi_license) && $this->lead->saudi_license === 'yes') {
            $licenses[] = 'Saudi License';
        }

        if (!empty($this->lead->india_license) && $this->lead->india_license === 'yes') {
            $licenses[] = 'Indian License';
        }

        $country_license = !empty($licenses) ? implode(', ', $licenses) : '---';


        if (!empty($this->lead->submit_lead_from)) {

            $location = json_decode($this->lead->submit_lead_from, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($location)) {
                $country_as_per_location = $location['country'] ?? '';
                $state_as_per_location   = $location['region'] ?? '';
                $city_as_per_location    = $location['city'] ?? '';
            }
        }

        if ($this->lead->leadassign) {
            $careoffName     = $this->lead->leadassign->name ?? '';
            $careoffContact1 = $this->lead->leadassign->care_no_1 ?? '';
            $careoffContact2 = $this->lead->leadassign->care_no_2 ?? '';
        }

        /* =====================================================
        | STATIC WHATSAPP CAREOFF IDS (UNCHANGED)
        =====================================================*/
        $staticWhatsappCareoffIds = array_map(
            'trim',
            explode(',', (string) ($this->metaTemplate->whatsup_chat_careoff_id_static ?? ''))
        );

        /* =====================================================
        | TEMPLATE VARIABLES
        =====================================================*/
        $fieldVars  = array_map('trim', explode(',', (string) $this->metaTemplate->meta_field_var));
        $assignVars = array_map('trim', explode(',', (string) $this->metaTemplate->meta_assign_ar));

        $staticCareoffIds = array_map(
            'trim',
            explode(',', (string) ($this->metaTemplate->careoff_id_static ?? ''))
        );

        $staticCareoffFields = array_map(
            'trim',
            explode(',', (string) ($this->metaTemplate->careoff_field_static ?? ''))
        );

        /* =====================================================
        | RESOLVE STATIC CAREOFF
        =====================================================*/
        $resolveStaticCareoff = function ($index) use ($staticCareoffIds, $staticCareoffFields) {

            $careoffId    = $staticCareoffIds[$index] ?? null;
            $careoffField = $staticCareoffFields[$index] ?? null;

            if (!$careoffId || !$careoffField) return '';

            $admin = Admin::where('id', $careoffId)
                ->where('status', true)
                ->first();

            if (!$admin) return '';

            $careoff_calling_number = "call/".$admin->calling_number ?? '';

            return match ($careoffField) {
                '[Static Careoff Name]'      => $admin->name ?? '',
                '[Static Careoff Contact 1]' => $admin->care_no_1 ?? '',
                '[Static Careoff Contact 2]' => $admin->care_no_2 ?? '',
                '[Calling Number]' => $careoff_calling_number ?? '',
                default => ''
            };
        };

        /* =====================================================
        | RESOLVE DYNAMIC WHATSAPP CHAT URL
        =====================================================*/
        $resolveDynamicWhatsupChatUrl = function ($index) use (
            $staticWhatsappCareoffIds,
            $defaultDynamicChatUrl
        ) {

            $careoffId = $staticWhatsappCareoffIds[$index] ?? null;

            if (!$careoffId) {
                return $defaultDynamicChatUrl;
            }

            $chat = Whatsappchaturl::where('staff_id', $careoffId)
                ->where('status', true)
                ->latest('id')
                ->first();

            return $chat->chat_url ?? $defaultDynamicChatUrl;
        };

        /* =====================================================
        | STATIC REPLACEMENTS
        =====================================================*/
        $staticReplace = [
            '[Dynamic Unsubscribe URL]' => $unsubscribeUrl,
            '[Dynamic Webpage Team]' => $dynamicWebpageTeam,
            '[Image URL]' => $dynamic_image_url,
            '[Whatsapp Chat Dynamic URL]' => $defaultDynamicChatUrl,
            '[Document Name]' => $this->metaTemplate->document_name,
            '[Careoff Name]' => $careoffName,
            '[Careoff Contact 1]' => $careoffContact1,
            '[Careoff Contact 2]' => $careoffContact2,
            '[country_as_per_location]' => $country_as_per_location,
            '[state_as_per_location]' => $state_as_per_location,
            '[city_as_per_location]' => $city_as_per_location,
            '[license]' => $country_license
        ];

      /* =====================================================
        | 🔥 CONTACT TYPE LOGIC (UPDATED → SINGLE PHONE)
        =====================================================*/

        $phoneNumber = $this->phone;

        if (empty($phoneNumber)) {
            \Log::channel('SendMetaLeadJob')->warning('No valid contact number', [
                'lead_id' => $this->lead->id
            ]);
            return;
        }

        \Log::channel('SendMetaLeadJob')->info('Processing Single Number', [
            'lead_id' => $this->lead->id,
            'phone'   => $phoneNumber
        ]);


        /* =====================================================
        | SEND MESSAGE FOR SINGLE NUMBER
        =====================================================*/

        $data = [
            'phone_number'      => $phoneNumber,
            'template_name'     => $this->metaTemplate->template_name,
            'template_language' => $this->metaTemplate->language_code ?? 'en'
        ];

        foreach ($fieldVars as $i => $field) {

            $assign = $assignVars[$i] ?? '';

            if ($assign === '[Enter Static Careoff]') {
                $data[$field] = $resolveStaticCareoff($i);
                continue;
            }

            if ($assign === '[Whatsapp Chat Static URL]') {
                $data[$field] = $resolveDynamicWhatsupChatUrl($i);
                continue;
            }

            if ($assign === '[Others]') {
                $data[$field] = $this->templateData['header_file_path'];
                continue;
            }

            if ($assign === '[Whatsapp Chat Dynamic URL]') {
                if (str_starts_with($field, 'field')) {
                    $data[$field] = str_replace('https://crm.qamarhire.com/', 'https://qamarhire.com/', $defaultDynamicChatUrl);
                } else {
                    $data[$field] = str_replace('https://crm.qamarhire.com/','',$defaultDynamicChatUrl);
                }
                continue;
            }

            if (isset($staticReplace[$assign])) {
                $data[$field] = $staticReplace[$assign];
                continue;
            }

            $clean = str_replace(['[', ']'], '', $assign);
            $data[$field] = $this->lead->$clean ?? '';
        }

        // ================= HEADER DOCUMENT FIX =================
        if (!empty($this->templateData['header_file_path'])) {

            $data['header_document'] = $this->templateData['header_file_path'];

            $data['header_document_name'] =
                $this->templateData['header_file_name']
                ?? basename($this->templateData['header_file_path']);
        }

        $data['contact'] = [
            'first_name'    => $this->lead->full_name ?? '',
            'last_name'     => ' ',
            'country'       => $countryName,
            'language_code' => $this->metaTemplate->language_code ?? 'en_US'
        ];

        $email = $this->lead->email ?? null;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = 'no-reply@yourdomain.com';
        }

        $data['contact']['email'] = $email;

        /* =====================================================
        | SEND API
        =====================================================*/
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', $this->apiData['token']],
            CURLOPT_URL            => $this->apiData['endpoint_api'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data)
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $responseJson = json_decode($response);

        \Log::channel('SendMetaLeadJob')->warning('responseJson', [
            'responseJson' => $responseJson
        ]);

        /* =====================================================
        | SAVE RESPONSE
        =====================================================*/
        $res = new Metawhatsappcampaignresponse();
        $res->metawhatsappcampaign_id = $this->campaign->id;
        $res->name = $this->lead->full_name;
        $res->mobile_no = $phoneNumber;
        $res->main_response = $response;
        $res->lead_id = $this->lead->id;

        if (isset($responseJson->result)) {
            $res->message_status = $responseJson->result;
            $res->message_text   = $responseJson->message;
        } else {
            $res->message_status = 'Failed';
            $res->message_text   = $responseJson->message ?? 'Unknown Error';
            $res->error_data_field = json_encode($responseJson->errors ?? []);
        }

        $res->save();

        \Log::channel('SendMetaLeadJob')->info('===== Lead Job Completed =====', [
            'lead_id' => $this->lead->id,
           'numbers_used' => [$phoneNumber]
        ]);
    }
}
