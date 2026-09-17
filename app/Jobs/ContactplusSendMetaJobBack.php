<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\Country;
use App\Models\Metawhatsappcampaign;
use App\Models\Metawhatsappcampaignresponse;
use App\Models\Unsubscribedata;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ContactplusSendMetaJobBack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $contactp,$api_data,$metatemplatedata,$requestData,$metaTemplate,$campaign_store;

    public function __construct($contactp,$api_data,$metatemplatedata,$requestData,$metaTemplate,$campaign_store)
    {
        $this->contactp = $contactp;
        $this->api_data = $api_data;
        $this->metatemplatedata = $metatemplatedata;
        $this->requestData = $requestData;
        $this->metaTemplate = $metaTemplate;
        $this->campaign_store = $campaign_store;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message_response = [];

        $field_variable = $this->metatemplatedata['field_variable'];
        $assgn_variable = $this->metatemplatedata['assgn_variable'];

        // Get Country
        $getCountry = Country::find($this->contactp['country_id']);
        if ($getCountry) {
            $countryName = $getCountry->name;
        } else {
            $countryName = '';
        }
        // Get City
        $getCity = City::find($this->contactp['city_id']);
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
            $this->contactp['office_eng_name'],
            $this->contactp['office_ar_name'],
            $this->contactp['office_no'],
            $this->contactp['office_email'],
            $this->contactp['owner_name'],
            $this->contactp['owner_contact'],
            $this->contactp['owenr_email'],
            $countryName,
            $cityName,
            $this->contactp['prim_concern_name'],
            $this->contactp['prim_contact'],
            $this->contactp['prim_email'],
            $this->contactp['sec_concern_name'],
            $this->contactp['sec_contact'],
            $this->contactp['sec_email'],
            $this->contactp['concern_name3'],
            $this->contactp['contact3'],
            $this->contactp['concern_name4'],
            $this->contactp['contact4'],
            $this->contactp['concern_name5'],
            $this->contactp['contact5'],
            $this->contactp['concern_name6'],
            $this->contactp['contact6'],
            $this->contactp['status']
        ];



        $finalAssignVar = str_replace($remStr,$repStr,$assgn_variable);
        $rem2 = ["[","]"];
        $rep2 = ["",""];

        if (($this->requestData['contactp_contact_type'] == '1' || $this->requestData['contactp_contact_type'] == '2') && $this->contactp['owner_contact'] != '') {
            $subscribe_post = new Unsubscribedata();
            $subscribe_post->contactp_id = $this->contactp['id'];
            $subscribe_post->random_string = $this->requestData['random_string'];
            $subscribe_post->save();

            $data = [];
            $data['phone_number'] = $this->contactp['owner_contact'];
            $data['template_name'] = $this->metaTemplate['template_name'];
            $data['template_language'] = "en_US";

            for ($i=0; $i < count($field_variable); $i++) {
                if ($assgn_variable[$i] == '[Others]') {
                    $data[$field_variable[$i]] = $this->requestData['header_file_path'];
                }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                    $data[$field_variable[$i]] = $this->requestData['unsubscribe_url'];
                }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                    $data[$field_variable[$i]] = $this->requestData['dynamichaturl'];
                }elseif ($assgn_variable[$i] == '[Document Name]') {
                    $data[$field_variable[$i]] = $this->metaTemplate['document_name'];
                }else{
                    // $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                    $data[$field_variable[$i]] = $finalAssignVar[$i];
                }
            }

            $data['contact'] =  [
                'first_name' => $this->contactp['owner_name'],
                'last_name' => " ",
                "email" => $this->contactp['owenr_email'],
                "country" => $this->requestData['countryName'],
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
            $post = new Metawhatsappcampaignresponse();
            $post->name = $this->contactp['owner_name'];
            $post->mobile_no = $this->contactp['owner_contact'];

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
                    $post->error_data_field = "Not Find";
                }

            }

            $post->contactp_id = $this->contactp['id'];
            $post->save();

            $message_response[] = $post->id;
        }


        if (($this->requestData['contactp_contact_type'] == '1' || $this->requestData['contactp_contact_type'] == '3') && $this->contactp['prim_contact'] != '') {
            $subscribe_post = new Unsubscribedata();
            $subscribe_post->contactp_id = $this->contactp['id'];
            $subscribe_post->random_string = $this->requestData['random_string'];
            $subscribe_post->save();

            $data = [];
            $data['phone_number'] = $this->contactp['prim_contact'];
            $data['template_name'] = $this->metaTemplate['template_name'];
            $data['template_language'] = "en_US";

            for ($i=0; $i < count($field_variable); $i++) {
                if ($assgn_variable[$i] == '[Others]') {
                    $data[$field_variable[$i]] = $this->requestData['header_file_path'];
                }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                    $data[$field_variable[$i]] = $this->requestData['unsubscribe_url'];
                }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                    $data[$field_variable[$i]] = $this->requestData['dynamichaturl'];
                }elseif ($assgn_variable[$i] == '[Document Name]') {
                    $data[$field_variable[$i]] = $this->metaTemplate['document_name'];
                }else{
                    $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                }
            }

            $data['contact'] =  [
                'first_name' => $this->contactp['prim_concern_name'],
                'last_name' => " ",
                "email" => $this->contactp['prim_email'],
                "country" => $this->requestData['countryName'],
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
            $post = new Metawhatsappcampaignresponse();
            $post->name = $this->contactp['prim_concern_name'];
            $post->mobile_no = $this->contactp['prim_contact'];

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
                    $post->error_data_field = "Not Find";
                }

            }

            $post->contactp_id = $this->contactp['id'];
            $post->save();

            $message_response[] = $post->id;
        }


        if (($this->requestData['contactp_contact_type'] == '1' || $this->requestData['contactp_contact_type'] == '4') && $this->contactp['sec_contact'] != '') {
            $subscribe_post = new Unsubscribedata();
            $subscribe_post->contactp_id = $this->contactp['id'];
            $subscribe_post->random_string = $this->requestData['random_string'];
            $subscribe_post->save();

            $data = [];
            $data['phone_number'] = $this->contactp['sec_contact'];
            $data['template_name'] = $this->metaTemplate['template_name'];
            $data['template_language'] = "en_US";

            for ($i=0; $i < count($field_variable); $i++) {
                if ($assgn_variable[$i] == '[Others]') {
                    $data[$field_variable[$i]] = $this->requestData['header_file_path'];
                }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                    $data[$field_variable[$i]] = $this->requestData['unsubscribe_url'];
                }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                    $data[$field_variable[$i]] = $this->requestData['dynamichaturl'];
                }elseif ($assgn_variable[$i] == '[Document Name]') {
                    $data[$field_variable[$i]] = $this->metaTemplate['document_name'];
                }else{
                    $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                }
            }

            $data['contact'] =  [
                'first_name' => $this->contactp['sec_concern_name'],
                'last_name' => " ",
                "email" => $this->contactp['sec_email'],
                "country" => $this->requestData['countryName'],
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
            $post = new Metawhatsappcampaignresponse();
            $post->name = $this->contactp['sec_concern_name'];
            $post->mobile_no = $this->contactp['sec_contact'];

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
                    $post->error_data_field = "Not Find";
                }

            }

            $post->contactp_id = $this->contactp['id'];
            $post->save();

            $message_response[] = $post->id;
        }


        // Get All Response
        $getMessages = Metawhatsappcampaignresponse::wherein('id',$message_response)->where('message_status','!=','failed')->count();
        $getErrorMsg = Metawhatsappcampaignresponse::wherein('id',$message_response)->where('message_status','=','failed')->get();

        // Update Meta Whatsapp Campaign
        $updateCampaign = Metawhatsappcampaign::find($this->campaign_store['id']);


        if (count($message_response) > 0) {
            if ($updateCampaign->metaresponse_id != '') {
                $response_id = explode(",",$updateCampaign->metaresponse_id);
                $updatereponse_id = array_unique(array_merge($response_id,$message_response));
            } else {
                $updatereponse_id = $message_response;
            }

            $updateCampaign->metaresponse_id = implode(",",$updatereponse_id);
        }


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
}
