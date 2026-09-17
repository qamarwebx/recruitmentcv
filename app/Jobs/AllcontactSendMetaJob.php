<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\City;
use App\Models\Country;
use App\Models\Metawhatsappcampaign;
use App\Models\Metawhatsappcampaignresponse;
use App\Models\Unsubscribedata;
use App\Models\Whatsappchaturl;
use App\Models\TeamMemberWebPage;
use App\Models\ImageUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Log;


class AllcontactSendMetaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $contactp, $phone, $api_data, $metatemplatedata, $metaTemplate, $campaign_store;

    public $timeout = 1800; // 30 minutes
    public $tries = 1;

    public function __construct($contactp, $phone, $api_data, $metatemplatedata, $metaTemplate, $campaign_store)
    {
        $this->contactp         = $contactp;
        $this->phone            = $phone; // ✅ NEW
        $this->api_data         = $api_data;
        $this->metatemplatedata = $metatemplatedata;
        $this->metaTemplate     = $metaTemplate;
        $this->campaign_store   = $campaign_store;
    }

    public function handle()
    {
        Log::channel('AllcontactSendMetaJob')->info("===== Allcontact Job Started =====", [
            'contact_id'  => $this->contactp['id'],
            'campaign_id' => $this->campaign_store['id'],
            'template'    => $this->metaTemplate['template_name'],
            'phone'       => $this->phone
        ]);
    
        $message_response = [];
    
        /* =====================================================
        | BASIC VALUES
        =====================================================*/
        $random_string   = Str::random(32);
        $unsubscribe_url = 'whatsapp/unsubscribe/request/allcontact/' . $random_string . '/' . $this->contactp['id'];
    
        $defaultChat = Whatsappchaturl::where('staff_id', $this->contactp['careoff_id'])
            ->where('status', true)
            ->latest('id')
            ->first();
    
        $defaultDynamicChatUrl = $defaultChat->chat_url ?? 'https://wa.me';

        // Dynamic Webpage Team URL

        $teamMemberPage = TeamMemberWebPage::where('staff_id', $this->contactp['careoff_id'])->latest('id')->first();
        if ($teamMemberPage) {
            $dynamicWebpageTeam = "https://qamrjob.com/housedriver/" . intval($teamMemberPage->staff_id);
        } else {
            $dynamicWebpageTeam = '';
        }

        // Dynamic Image URL
        $image_url = ImageUrl::where('staff_id', $this->contactp['careoff_id'])->latest('id')->first();
        if ($image_url) {
            $dynamic_image_url = $image_url->url;
        } else {
            $dynamic_image_url = '';
        }
  
    
        /* =====================================================
        | CONTACT CAREOFF
        =====================================================*/
        $dynamicAdmin = Admin::where('id', $this->contactp['careoff_id'])
            ->where('status', true)
            ->first();
    
        $careoffname = $dynamicAdmin->name ?? '';
        $careoff_no1 = $dynamicAdmin->care_no_1 ?? '';
        $careoff_no2 = $dynamicAdmin->care_no_2 ?? '';
        $call_link   = $careoff_no1 ? "tel:+{$careoff_no1}" : '';
        $careoff_calling_number = "call/".$dynamicAdmin->calling_number ?? '';
    
        /* =====================================================
        | LOCATION
        =====================================================*/
        $countryName = optional(Country::find($this->contactp['country_id']))->name ?? '';
        $cityName    = optional(City::find($this->contactp['city_id']))->name ?? '';
    
        /* =====================================================
        | STATIC MAP
        =====================================================*/
        $staticMap = [
            '[Company]'            => $this->contactp['office_name'],
            '[Business Type]'      => $this->contactp['lead_type'],
            '[Full Name]'          => $this->contactp['full_name'],
            '[Email]'              => $this->contactp['email'],
            '[Country]'            => $countryName,
            '[City]'               => $cityName,
            '[Phone0]'             => $this->contactp['mobile_no1_wsp'],
            '[Email0]'             => $this->contactp['email0'],
            '[Phone1]'             => $this->contactp['mobile_no2_wsp'],
            '[Email1]'             => $this->contactp['email1'],
            '[Phone2]'             => $this->contactp['mobile_no3_wsp'],
            '[Email2]'             => $this->contactp['email2'],
            '[Membership]'         => $this->contactp['id'],
            '[Careoff Name]'       => $careoffname,
            '[Careoff Contact 1]'  => $careoff_no1,
            '[Careoff Contact 2]'  => $careoff_no2,
            '[Calling Number]'     => $careoff_calling_number,
            '[Dynamic Webpage Team]' => $dynamicWebpageTeam,
            '[Image URL]' => $dynamic_image_url,

        ];
    
        /* =====================================================
        | TEMPLATE VARIABLES
        =====================================================*/
        $fieldVars  = array_map('trim', explode(',', (string) $this->metaTemplate['meta_field_var']));
        $assignVars = array_map('trim', explode(',', (string) $this->metaTemplate['meta_assign_ar']));
    
        $staticCareoffId     = trim($this->metaTemplate['careoff_id_static'] ?? '');
        $staticCareoffField  = trim($this->metaTemplate['careoff_field_static'] ?? '');
    
        $staticWhatsappCareoffIds = array_map(
            'trim',
            explode(',', (string) ($this->metaTemplate['whatsup_chat_careoff_id_static'] ?? ''))
        );
    
        /* =====================================================
        | RESOLVE STATIC CAREOFF
        =====================================================*/
        $resolveStaticCareoff = function () use ($staticCareoffId, $staticCareoffField) {
    
            if (!$staticCareoffId || !$staticCareoffField) return '';
    
            $admin = Admin::where('id', $staticCareoffId)
                ->where('status', true)
                ->first();
    
            if (!$admin) return '';
    
            return match ($staticCareoffField) {
                '[Static Careoff Name]'      => $admin->name ?? '',
                '[Static Careoff Contact 1]' => $admin->care_no_1 ?? '',
                '[Static Careoff Contact 2]' => $admin->care_no_2 ?? '',
                default => ''
            };
        };
    
        /* =====================================================
        | RESOLVE DYNAMIC WHATSAPP CHAT URL
        =====================================================*/
        $resolveDynamicWhatsupChatUrl = function ($index) use ($staticWhatsappCareoffIds, $defaultDynamicChatUrl) {
    
            $careoffId = $staticWhatsappCareoffIds[$index] ?? null;
    
            if (!$careoffId) return $defaultDynamicChatUrl;
    
            $chat = Whatsappchaturl::where('staff_id', $careoffId)
                ->where('status', true)
                ->latest('id')
                ->first();
    
            return $chat->chat_url ?? $defaultDynamicChatUrl;
        };
    
        /* =====================================================
        | ASSIGN RESOLVER
        =====================================================*/
        $resolveAssign = function ($assign, $index, $field) use (
            $staticMap,
            $unsubscribe_url,
            $call_link,
            $resolveStaticCareoff,
            $resolveDynamicWhatsupChatUrl,
            $defaultDynamicChatUrl,
            $careoff_calling_number
        ) {
    
            if ($assign === '[Enter Static Careoff]') {
                return $resolveStaticCareoff();
            }
    
            if ($assign === '[Whatsapp Chat Static URL]') {
                return $resolveDynamicWhatsupChatUrl($index);
            }
    
            if ($assign === '[Calling Number]') {
                return $careoff_calling_number;
            }
    
            if ($assign === '[Whatsapp Chat Dynamic URL]') {
                if (str_starts_with($field, 'field')) {
                    return str_replace('https://crm.qamarhire.com/', 'https://qamarhire.com/', $defaultDynamicChatUrl);
                }
                return str_replace('https://crm.qamarhire.com/', '', $defaultDynamicChatUrl);

            }
    
            if ($assign === '[Dynamic Unsubscribe URL]') return $unsubscribe_url;
            if ($assign === '[Careoff Call Marketing]') return $call_link;
            if ($assign === '[Others]') return $this->metatemplatedata['header_file_path'];
    
            if (isset($staticMap[$assign])) return $staticMap[$assign];
    
            if (preg_match('/^\[(.*?)\]$/', $assign, $m)) {
                return $this->contactp[$m[1]] ?? '';
            }
    
            return '';
        };
    
        /* =====================================================
        | SINGLE PHONE SEND
        =====================================================*/
        $phoneNumber = $this->phone;
    
        if (!$phoneNumber) {
            Log::channel('AllcontactSendMetaJob')->warning('No phone found', [
                'contact_id' => $this->contactp['id']
            ]);
            return;
        }
    
        $sub = new Unsubscribedata();
        $sub->allcontact_id = $this->contactp['id'];
        $sub->random_string = $random_string;
        $sub->save();
    
        $data = [
            'phone_number'      => $phoneNumber,
            'template_name'     => $this->metaTemplate['template_name'],
            'template_language' => $this->metaTemplate['language_code'] ?? 'en_US',
        ];
    
        foreach ($fieldVars as $i => $field) {
            $data[$field] = $resolveAssign($assignVars[$i] ?? '', $i, $field);
        }
    
        $email = $this->contactp['email'] ?? null;
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = 'no-reply@yourdomain.com';
        }
    
        $data['contact'] = [
            'first_name'    => $this->contactp['full_name'] ?? '---',
            'last_name'     => ' ',
            'email'         => $email,
            'country'       => $countryName ?? '---',
            'language_code' => $this->metaTemplate['language_code'] ?? 'en_US',
        ];
    
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', $this->api_data['token']],
            CURLOPT_URL => $this->api_data['endpoint_api'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);
    
        $response = curl_exec($curl);
        curl_close($curl);
    
        $responseGet = json_decode($response);
    
        Log::channel('AllcontactSendMetaJob')->info("Allcontact API Response", [
            'phone'    => $phoneNumber,
            'response' => $responseGet,
            'data' => $data
        ]);
    
        $post = new Metawhatsappcampaignresponse();
        $post->name = $this->contactp['full_name'];
        $post->mobile_no = $phoneNumber;
        $post->main_response = json_encode($responseGet);
        $post->metawhatsappcampaign_id = $this->campaign_store['id'];
        $post->allcontact_id = $this->contactp['id'];
        $post->message_status = $responseGet->result ?? 'Failed';
        $post->message_text   = $responseGet->message ?? 'Unknown Error';
        $post->save();
    
        $message_response[] = $post->id;
    
        $camp = Metawhatsappcampaign::find($this->campaign_store['id']);
        if ($camp) {
            $camp->metaresponse_id = implode(',', $message_response);
            $camp->message_status = count($message_response) ? 'success' : 'Failed';
            $camp->save();
        }
    
        Log::channel('AllcontactSendMetaJob')->info("===== Allcontact Job Completed =====", [
            'contact_id' => $this->contactp['id'],
            'phone'      => $phoneNumber
        ]);
    }
}
