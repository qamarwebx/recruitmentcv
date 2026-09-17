<?php

namespace App\Jobs;

use App\Models\Allcontact;
use App\Models\City;
use App\Models\Contactplus;
use App\Models\Country;
use App\Models\Lead;
use App\Models\Metawhatsappcampaign;
use App\Models\Metawhatsappcampaignresponse;
use App\Models\Unsubscribedata;
use App\Models\Whatsappchaturl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Str;

class ResendMetawhastapppcampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $metaresponse,$metatemplatedata,$api_data,$metaTemplate,$metaAPI,$metacampaign;

    public function __construct($metaresponse,$metatemplatedata,$api_data,$metaTemplate,$metaAPI,$metacampaign)
    {
        $this->metaresponse = $metaresponse;
        $this->metatemplatedata = $metatemplatedata;
        $this->api_data = $api_data;
        $this->metaTemplate = $metaTemplate;
        $this->metaAPI = $metaAPI;
        $this->metacampaign = $metacampaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        if ($this->metacampaign['audience'] == 'allcontact') {
            $contactp = Allcontact::find($this->metaresponse['allcontact_id'])->first();

            $random_string = Str::random(32);
            $unsubscribe_url = 'whatsapp/unsubscribe/request/allcontact/'.$random_string.'/'.$this->metaresponse['allcontact_id'];

            $staffDet = Whatsappchaturl::where('staff_id','=',$contactp->careoff_id)->where('status',true)->first();
            if ($staffDet) {
                $dynamichaturl = $staffDet->chat_url;
            } else {
                $dynamichaturl = "https://wa.me";
            }

            // Get Country
            $getCountry = Country::find($contactp->country_id);
            if ($getCountry) {
                $countryName = $getCountry->name;
            } else {
                $countryName = '';
            }
            // Get City
            $getCity = City::find($contactp->city_id);
            if ($getCity) {
                $cityName = $getCity->name;
            } else {
                $cityName = '';
            }

            $remStr = [
                "[Company]",
                "[Business Type]",
                "[Full Name]",
                "[Email]",
                "[Country]",
                "[City]",
                "[Phone0]",
                "[Email0]",
                "[Phone1]",
                "[Email1]",
                "[Phone2]",
                "[Email2]",
                "[Membership]"
            ];

            $repStr = [
                $contactp->office_name,
                $contactp->lead_type,
                $contactp->full_name,
                $contactp->email,
                $countryName,
                $cityName,
                $contactp->phone0,
                $contactp->email0,
                $contactp->phone1,
                $contactp->email1,
                $contactp->phone2,
                $contactp->email2,
                $contactp->id
            ];

            $field_variable = explode(",",$this->metaTemplate['meta_field_var']);
            $assgn_variable = explode(",",$this->metaTemplate['meta_assign_ar']);

            $finalAssignVar = str_replace($remStr,$repStr,$assgn_variable);
            $rem2 = ["[","]"];
            $rep2 = ["",""];

            $subscribe_post = new Unsubscribedata();
            $subscribe_post->allcontact_id = $contactp->id;
            $subscribe_post->random_string = $random_string;
            $subscribe_post->save();

            $data = [];
            $data['phone_number'] = $this->metaresponse['mobile_no'];
            $data['template_name'] = $this->metaTemplate['template_name'];
            $data['template_language'] = $this->metaTemplate['language_code'] ?? 'en';


            for ($i=0; $i < count($field_variable); $i++) {
                if ($assgn_variable[$i] == '[Others]') {
                    $data[$field_variable[$i]] = $this->metatemplatedata['header_file_path'];
                }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                    $data[$field_variable[$i]] = $unsubscribe_url;
                }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                    $data[$field_variable[$i]] = $dynamichaturl;
                }elseif ($assgn_variable[$i] == '[Document Name]') {
                    $data[$field_variable[$i]] = $this->metaTemplate['document_name'];
                }else{
                    $data[$field_variable[$i]] = $finalAssignVar[$i];
                    // $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                }
            }

            $data['contact'] =  [
                'first_name' => $contactp->full_name,
                'last_name' => " ",
                "email" => $contactp->email,
                "country" => $countryName,
                "language_code" => "en"
            ];

            // Send Campaign Message
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$this->api_data['token']),
                CURLOPT_URL => $this->api_data['endpoint_api'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data)
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $responseGet = json_decode($response);

            // Upload Campaign Data
            $post = Metawhatsappcampaignresponse::find($this->metaresponse['id']);
            $post->main_response = json_encode($responseGet);
            if (isset($responseGet->result)){
                $post->message_status = $responseGet->result;
                $post->message_text = $responseGet->message;
            }else{
                $post->message_status = "Failed";
                if (isset($responseGet->message)) {
                    $post->message_text = $responseGet->message;
                    $post->error_data_field = json_encode($responseGet->errors);
                } else {
                    $post->message_text = "Unknown Error";
                    $post->error_data_field = "Unknown Error";
                }

                // $post->message_text = $responseGet->message;
                // $post->error_data_field = json_encode($responseGet->errors);
            }
            $post->save();

            // Get All Response
            $getMessages = Metawhatsappcampaignresponse::wherein('id',$this->metaresponse['id'])->where('message_status','!=','failed')->count();
            $getErrorMsg = Metawhatsappcampaignresponse::wherein('id',$this->metaresponse['id'])->where('message_status','=','failed')->get();

            // Update Meta Whatsapp Campaign
            $updateCampaign = Metawhatsappcampaign::find($this->metacampaign['id']);


            if ($getMessages > 0) {
                $updateCampaign->message_status = "success";
                $updateCampaign->message_text = "Message processed for WhatsApp contact";
            } else {
                $updateCampaign->message_status = "Failed";
                if(count($getErrorMsg) > 0){
                    $updateCampaign->message_text = $getErrorMsg[0]->message_text;
                }else{
                    $updateCampaign->message_text = "Unknown Error";
                }
            }

            $updateCampaign->save();

        }

        if ($this->metacampaign['audience'] == 'contactp') {
            $contactp = Contactplus::find($this->metaresponse['contactp_id'])->first();

            $random_string = Str::random(32);
            $unsubscribe_url = 'whatsapp/unsubscribe/request/'.$random_string.'/'.$this->metaresponse['allcontact_id'];

            $staffDet = Whatsappchaturl::where('staff_id','=',$contactp->careoff_id)->where('status',true)->first();
            if ($staffDet) {
                $dynamichaturl = $staffDet->chat_url;
            } else {
                $dynamichaturl = "https://wa.me";
            }


            // Get Country
            $getCountry = Country::find($contactp->country_id);
            if ($getCountry) {
                $countryName = $getCountry->name;
            } else {
                $countryName = '';
            }
            // Get City
            $getCity = City::find($contactp->city_id);
            if ($getCity) {
                $cityName = $getCity->name;
            } else {
                $cityName = '';
            }

            $remStr = [
                "[Office Name (English)]",
                "[Office Name (Arabic)]",
                "[Office Number]",
                "[Office Email]",
                "[Owner Name]",
                "[Owner Contact]",
                "[Owner Email]",
                "[Country]",
                "[City]",
                "[Primary Concern Person]",
                "[Primary Contact No]",
                "[Primary Email]",
                "[Secondary Concern Person]",
                "[Secondary Contact No]",
                "[Secondary Email]",
                "[Concern Person 3]",
                "[Contact No 3]",
                "[Concern Person 4]",
                "[Contact No 4]",
                "[Concern Person 5]",
                "[Contact No 5]",
                "[Concer Person 6]",
                "[Contact No 6]",
                "[Status]"
            ];

            $repStr = [
                $contactp->office_eng_name,
                $contactp->office_ar_name,
                $contactp->office_no,
                $contactp->office_email,
                $contactp->owner_name,
                $contactp->owner_contact,
                $contactp->owenr_email,
                $countryName,
                $cityName,
                $contactp->prim_concern_name,
                $contactp->prim_contact,
                $contactp->prim_email,
                $contactp->sec_concern_name,
                $contactp->sec_contact,
                $contactp->sec_email,
                $contactp->concern_name3,
                $contactp->contact3,
                $contactp->concern_name4,
                $contactp->contact4,
                $contactp->concern_name5,
                $contactp->contact5,
                $contactp->concern_name6,
                $contactp->contact6,
                $contactp->status
            ];

            $field_variable = explode(",",$this->metaTemplate['meta_field_var']);
            $assgn_variable = explode(",",$this->metaTemplate['meta_assign_ar']);

            $finalAssignVar = str_replace($remStr,$repStr,$assgn_variable);
            $rem2 = ["[","]"];
            $rep2 = ["",""];

            $subscribe_post = new Unsubscribedata();
            $subscribe_post->allcontact_id = $contactp->id;
            $subscribe_post->random_string = $random_string;
            $subscribe_post->save();

            $data = [];
            $data['phone_number'] = $this->metaresponse['mobile_no'];
            $data['template_name'] = $this->metaTemplate['template_name'];
            $data['template_language'] = "en_US";

            for ($i=0; $i < count($field_variable); $i++) {
                if ($assgn_variable[$i] == '[Others]') {
                    $data[$field_variable[$i]] = $this->metatemplatedata['header_file_path'];
                }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                    $data[$field_variable[$i]] = $unsubscribe_url;
                }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                    $data[$field_variable[$i]] = $dynamichaturl;
                }elseif ($assgn_variable[$i] == '[Document Name]') {
                    $data[$field_variable[$i]] = $this->metaTemplate['document_name'];
                }else{
                    $data[$field_variable[$i]] = $finalAssignVar[$i];
                    // $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                }
            }

            $data['contact'] =  [
                'first_name' => $this->metaresponse['name'],
                'last_name' => " ",
                "email" => $contactp->owenr_email,
                "country" => $countryName,
                "language_code" => "en"
            ];

            // Send Campaign Message
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$this->api_data['token']),
                CURLOPT_URL => $this->api_data['endpoint_api'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data)
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $responseGet = json_decode($response);

            // Upload Campaign Data
            $post = Metawhatsappcampaignresponse::find($this->metaresponse['id']);
            $post->main_response = json_encode($responseGet);
            if (isset($responseGet->result)){
                $post->message_status = $responseGet->result;
                $post->message_text = $responseGet->message;
            }else{
                $post->message_status = "Failed";
                if (isset($responseGet->message)) {
                    $post->message_text = $responseGet->message;
                    $post->error_data_field = json_encode($responseGet->errors);
                } else {
                    $post->message_text = "Unknown Error";
                    $post->error_data_field = "Unknown Error";
                }

                // $post->message_text = $responseGet->message;
                // $post->error_data_field = json_encode($responseGet->errors);
            }
            $post->save();

            // Get All Response
            $getMessages = Metawhatsappcampaignresponse::wherein('id',$this->metaresponse['id'])->where('message_status','!=','failed')->count();
            $getErrorMsg = Metawhatsappcampaignresponse::wherein('id',$this->metaresponse['id'])->where('message_status','=','failed')->get();

            // Update Meta Whatsapp Campaign
            $updateCampaign = Metawhatsappcampaign::find($this->metacampaign['id']);


            if ($getMessages > 0) {
                $updateCampaign->message_status = "success";
                $updateCampaign->message_text = "Message processed for WhatsApp contact";
            } else {
                $updateCampaign->message_status = "Failed";
                if(count($getErrorMsg) > 0){
                    $updateCampaign->message_text = $getErrorMsg[0]->message_text;
                }else{
                    $updateCampaign->message_text = "Unknown Error";
                }
            }

            $updateCampaign->save();

        }
    
        if ($this->metacampaign['audience'] == 'leads'){

            try {

                \Log::channel('SendMetaLeadJob')->info("===== Resend Job Started =====", [
                    'response_id' => $this->metaresponse['id']
                ]);

                /* =====================================================
                | GET LEAD + PHONE
                =====================================================*/
                if ($this->metacampaign['audience'] == 'leads') {
                    $lead = Lead::find($this->metaresponse['lead_id']);
                } else {
                    return;
                }

                if (!$lead) {
                    \Log::channel('SendMetaLeadJob')->warning('Lead not found', ['lead_id' => $this->metaresponse['lead_id']]);
                    return;
                }

                $phoneNumber = $this->metaresponse['mobile_no'];

                if (empty($phoneNumber)) {
                    \Log::channel('SendMetaLeadJob')->warning('Phone empty', ['response_id' => $this->metaresponse['id']]);
                    return;
                }

                /* =====================================================
                | BASIC DATA (same as SendMetaLeadJob)
                =====================================================*/
                $random = Str::random(32);
                $unsubscribeUrl = "whatsapp/unsubscribe/request/leads/{$random}/{$lead->id}";

                $leadChat = Whatsappchaturl::where('staff_id', $lead->leadassign_id)
                    ->where('status', true)
                    ->latest('id')
                    ->first();

                $leadChatUrl = isset($leadChat->chat_url)
                    ? str_replace('https://crm.qamarhire.com/', '', $leadChat->chat_url)
                    : "https://wa.me";

                $countryName = optional(Country::find($lead->country_id))->name ?? '';
                $cityName    = optional(City::find($lead->city_id))->name ?? '';

                /* =====================================================
                | CAREOFF
                =====================================================*/
                $careoffName = '';
                $careoffContact1 = '';
                $careoffContact2 = '';

                if ($lead->leadassign) {
                    $careoffName     = $lead->leadassign->name ?? '';
                    $careoffContact1 = $lead->leadassign->care_no_1 ?? '';
                    $careoffContact2 = $lead->leadassign->care_no_2 ?? '';
                }

                /* =====================================================
                | TEMPLATE VARIABLES
                =====================================================*/
                $fieldVars  = array_map('trim', explode(',', (string) $this->metaTemplate['meta_field_var']));
                $assignVars = array_map('trim', explode(',', (string) $this->metaTemplate['meta_assign_ar']));

                /* =====================================================
                | STATIC REPLACEMENTS
                =====================================================*/
                $staticReplace = [
                    '[Dynamic Unsubscribe URL]' => $unsubscribeUrl,
                    '[Whatsapp Chat Dynamic URL]' => $leadChatUrl,
                    '[Document Name]' => $this->metaTemplate['document_name'],
                    '[Careoff Name]' => $careoffName,
                    '[Careoff Contact 1]' => $careoffContact1,
                    '[Careoff Contact 2]' => $careoffContact2,
                ];

                /* =====================================================
                | BUILD DATA
                =====================================================*/
                $data = [
                    'phone_number'      => $phoneNumber,
                    'template_name'     => $this->metaTemplate['template_name'],
                    'template_language' => $this->metaTemplate['language_code'] ?? 'en'
                ];

                foreach ($fieldVars as $i => $field) {

                    $assign = $assignVars[$i] ?? '';

                    if (isset($staticReplace[$assign])) {
                        $data[$field] = $staticReplace[$assign];
                        continue;
                    }

                    $clean = str_replace(['[', ']'], '', $assign);
                    $data[$field] = $lead->$clean ?? '';
                }

                /* =====================================================
                | HEADER FILE
                =====================================================*/
                if (!empty($this->metatemplatedata['header_file_path'])) {
                    $data['header_document'] = $this->metatemplatedata['header_file_path'];
                    $data['header_image'] = $this->metatemplatedata['header_file_path'];
                    $data['header_document_name'] = basename($this->metatemplatedata['header_file_path']);
                }

                /* =====================================================
                | CONTACT
                =====================================================*/
                $data['contact'] = [
                    'first_name' => $lead->full_name ?? '',
                    'last_name'  => ' ',
                    'country'    => $countryName,
                    'language_code' => $this->metaTemplate['language_code'] ?? 'en_US',
                    'email' => filter_var($lead->email, FILTER_VALIDATE_EMAIL)
                        ? $lead->email
                        : 'no-reply@yourdomain.com'
                ];

                /* =====================================================
                | API CALL
                =====================================================*/
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_HTTPHEADER     => ['Content-Type: application/json', $this->api_data['token']],
                    CURLOPT_URL            => $this->api_data['endpoint_api'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST           => true,
                    CURLOPT_POSTFIELDS     => json_encode($data)
                ]);

                $response = curl_exec($curl);

                if (curl_errno($curl)) {
                    \Log::channel('SendMetaLeadJob')->error('Curl Error: ' . curl_error($curl));
                }

                curl_close($curl);

                $responseGet = json_decode($response);

                // Upload Campaign Data
                $post = Metawhatsappcampaignresponse::find($this->metaresponse['id']);
                $post->main_response = json_encode($responseGet);
                if (isset($responseGet->result)){
                    $post->message_status = $responseGet->result;
                    $post->message_text = $responseGet->message;
                }else{
                    $post->message_status = "Failed";
                    if (isset($responseGet->message)) {
                        $post->message_text = $responseGet->message;
                        $post->error_data_field = json_encode($responseGet->errors);
                    } else {
                        $post->message_text = "Unknown Error";
                        $post->error_data_field = "Unknown Error";
                    }
                }
                $post->save();

                // Get All Response
                $getMessages = Metawhatsappcampaignresponse::wherein('id',$this->metaresponse['id'])->where('message_status','!=','failed')->count();
                $getErrorMsg = Metawhatsappcampaignresponse::wherein('id',$this->metaresponse['id'])->where('message_status','=','failed')->get();

                // Update Meta Whatsapp Campaign
                $updateCampaign = Metawhatsappcampaign::find($this->metacampaign['id']);


                if ($getMessages > 0) {
                    $updateCampaign->message_status = "success";
                    $updateCampaign->message_text = "Message processed for WhatsApp contact";
                } else {
                    $updateCampaign->message_status = "Failed";
                    if(count($getErrorMsg) > 0){
                        $updateCampaign->message_text = $getErrorMsg[0]->message_text;
                    }else{
                        $updateCampaign->message_text = "Unknown Error";
                    }
                }

                $updateCampaign->save();

                \Log::channel('SendMetaLeadJob')->info("===== Resend Job Completed =====", [
                    'lead_id' => $lead->id,
                    'response' => $response,
                    'lead_leadassign_id' => $lead->leadassign->id
                ]);

            } catch (\Exception $e) {

                \Log::channel('SendMetaLeadJob')->error("===== Resend Job Failed =====", [
                    'error_message' => $e->getMessage(),
                    'line'          => $e->getLine(),
                    'file'          => $e->getFile(),
                    'response_id'   => $this->metaresponse['id'] ?? null
                ]);
        
            }
        }

    }
}
