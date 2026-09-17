<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\Country;
use App\Models\Metawhatsappcampaign;
use App\Models\Metawhatsappcampaignresponse;
use App\Models\Admin;
use App\Models\Whatsappchaturl;
use App\Models\TeamMemberWebPage;
use App\Models\ImageUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class ContactplusSendMetaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $contactp, $phone, $api_data, $metatemplatedata, $requestData, $metaTemplate, $campaign_store;

    public function __construct($contactp, $phone, $api_data, $metatemplatedata, $requestData, $metaTemplate, $campaign_store)
    {
        $this->contactp         = $contactp;
        $this->phone            = $phone; // ✅ NEW
        $this->api_data         = $api_data;
        $this->metatemplatedata = $metatemplatedata;
        $this->requestData      = $requestData;
        $this->metaTemplate     = $metaTemplate;
        $this->campaign_store   = $campaign_store;
    }
    
    public function handle()
    {
        try {

            \Log::channel('contactplus')->info("===== ContactplusSendMetaJob Job Started =====", [
                'template'    => $this->metaTemplate['template_name']
            ]);
    
            $response_ids = [];
    
            /* =====================================================
            | SINGLE PHONE (NEW - DO NOT REMOVE)
            =====================================================*/
            $phone = $this->phone ?? ($this->requestData['phone'] ?? null);
    
            if (!$phone) {
                Log::channel('contactplus')->warning('No phone found', [
                    'contact_id' => $this->contactp['id']
                ]);
                return;
            }
    
            /* =====================================================
            | TEMPLATE VARIABLES
            =====================================================*/
            $fieldVars  = array_map('trim', explode(',', (string) $this->metaTemplate['field_variable']));
            $assignVars = array_map('trim', explode(',', (string) $this->metaTemplate['assign_variable']));
    
            $staticCareoffIds = array_map(
                'trim',
                explode(',', (string) ($this->metaTemplate['careoff_id_static'] ?? ''))
            );
    
            $staticCareoffFields = array_map(
                'trim',
                explode(',', (string) ($this->metaTemplate['careoff_field_static'] ?? ''))
            );
    
            $staticWhatsappCareoffIds = array_map(
                'trim',
                explode(',', (string) ($this->metaTemplate['whatsup_chat_careoff_id_static'] ?? ''))
            );
    
            /* =====================================================
            | LOCATION
            =====================================================*/
            $countryName = optional(Country::find($this->contactp['country_id'] ?? null))->name ?? '';
            $cityName    = optional(City::find($this->contactp['city_id'] ?? null))->name ?? '';
    
            /* =====================================================
            | CAREOFF
            =====================================================*/
            $dynamicAdmin = Admin::where('id', $this->contactp['careoff_id'] ?? null)
                ->where('status', true)
                ->first();
    
            $careoffname = $dynamicAdmin->name ?? '';
            $careoff_no1 = $dynamicAdmin->care_no_1 ?? '';
            $careoff_no2 = $dynamicAdmin->care_no_2 ?? '';
            $careoff_calling_number = "call/".$dynamicAdmin->calling_number ?? '';
    
            $defaultChat = Whatsappchaturl::where('staff_id', $this->contactp['careoff_id'] ?? null)
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
            | STATIC MAP
            =====================================================*/
            $staticMap = [
                "[Owner Name]" => $this->contactp['owner_name'] ?? '',
                "[Owner Email]" => $this->contactp['owenr_email'] ?? $this->contactp['owner_email'] ?? '',
                "[Owner Contact]" => $this->contactp['owner_contact'] ?? '',
                "[Country]" => $countryName,
                "[City]" => $cityName,
                "[Primary Concern Person]" => $this->contactp['prim_concern_name'] ?? '',
                "[Primary Contact No]" => $this->contactp['prim_contact'] ?? '',
                "[Primary Email]" => $this->contactp['prim_email'] ?? '',
                "[Status]" => $this->contactp['status'] ?? '',
                "[Calling Number]" => $careoff_calling_number,
                "[Dynamic Webpage Team]" => $dynamicWebpageTeam,
                "[Image URL]" => $dynamic_image_url
            ];
    
            $resolveAssign = function ($assign) use ($staticMap) {
                if (isset($staticMap[$assign])) return $staticMap[$assign];
    
                if (preg_match('/^\[(.+)\]$/', $assign, $m)) {
                    return $this->contactp[$m[1]] ?? '';
                }
    
                return '';
            };
    
            $resolveStaticCareoff = function ($index) use ($staticCareoffIds, $staticCareoffFields) {
                $careoffId = $staticCareoffIds[$index] ?? null;
                $careoffField = $staticCareoffFields[$index] ?? null;
    
                if (!$careoffId || !$careoffField) return '';
    
                $admin = Admin::where('id', $careoffId)->where('status', true)->first();
                if (!$admin) return '';
    
                return match ($careoffField) {
                    '[Static Careoff Name]' => $admin->name ?? '',
                    '[Static Careoff Contact 1]' => $admin->care_no_1 ?? '',
                    '[Static Careoff Contact 2]' => $admin->care_no_2 ?? '',
                    default => ''
                };
            };
    
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
            | BUILD PAYLOAD
            =====================================================*/
            $payload = [];
    
            foreach ($fieldVars as $i => $field) {
    
                $assign = $assignVars[$i] ?? null;
                if (!$field || !$assign) continue;
    
                if ($assign === '[Enter Static Careoff]') {
                    $payload[$field] = $resolveStaticCareoff($i);
                    continue;
                }
                
                if ($assign === '[Whatsapp Chat Dynamic URL]') {
                    if (str_starts_with($field, 'field')) {
                        $payload[$field] = str_replace('https://crm.qamarhire.com/', 'https://qamarhire.com/', $resolveDynamicWhatsupChatUrl);
                    } else {
                        $payload[$field] = str_replace('https://crm.qamarhire.com/','',$resolveDynamicWhatsupChatUrl);
                    }
                    continue;
                }
    
                if ($assign === '[Whatsapp Chat Static URL]') {
                    $payload[$field] = $resolveDynamicWhatsupChatUrl($i);
                    continue;
                }
    
                switch ($assign) {
                    case '[Careoff Name]':
                        $payload[$field] = $careoffname;
                        break;
                    case '[Careoff Contact 1]':
                        $payload[$field] = $careoff_no1;
                        break;
                    case '[Careoff Contact 2]':
                        $payload[$field] = $careoff_no2;
                        break;
                    default:
                        $payload[$field] = $resolveAssign($assign);
                }
            }
    
            /* =====================================================
            | FINAL PAYLOAD
            =====================================================*/
            $payload['phone_number'] = $phone;
            $payload['template_name'] = $this->metaTemplate['template_name'];
            $payload['template_language'] = $this->metaTemplate['language_code'] ?? 'en_US';
    
            $email = $this->contactp['owner_email'] ?? null;
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = 'no-reply@yourdomain.com';
            }
    
            $payload['contact'] = [
                'first_name' => $this->contactp['owner_name'] ?? 'Customer',
                'last_name' => ' ',
                'email' => $email,
                'country' => $countryName,
                'language_code' => $this->metaTemplate['language_code'] ?? 'en_US',
            ];
    
            /* =====================================================
            | API CALL
            =====================================================*/
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_HTTPHEADER => ['Content-Type: application/json', $this->api_data['token']],
                CURLOPT_URL => $this->api_data['endpoint_api'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
            ]);
    
            $res = curl_exec($curl);
            curl_close($curl);
    
            $r = json_decode($res);

            \Log::channel('contactplus')->info('responseJson', [
                'responseJson' => $r
            ]);
    
            /* =====================================================
            | SAVE RESPONSE
            =====================================================*/
            $log = new Metawhatsappcampaignresponse();
            $log->name = $this->contactp['owner_name'];
            $log->mobile_no = $phone;
            $log->message_status = $r->result ?? 'failed';
            $log->message_text = $r->message ?? 'Unknown Error';
            $log->metawhatsappcampaign_id = $this->campaign_store['id'];
            $log->contactp_id = $this->contactp['id'];
            $log->save();
    
            $response_ids[] = $log->id;
    
            /* =====================================================
            | UPDATE CAMPAIGN
            =====================================================*/
            $camp = Metawhatsappcampaign::find($this->campaign_store['id']);
            if ($camp) {
                $camp->message_status = 'success';
                $camp->message_text = 'Message processed for WhatsApp contact';
                $camp->metaresponse_id = implode(',', $response_ids);
                $camp->save();
            }
    
        } catch (\Throwable $e) {
    
            Log::channel('contactplus')->critical("🔥 Contactplus Job Failed", [
                'error' => $e->getMessage(),
            ]);
    
            throw $e;
        }
    }
}
