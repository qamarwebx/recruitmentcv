<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\Contactsendwhatsapp;
use App\Models\Country;
use App\Models\Metawhatsappapi;
use App\Models\Metawhatsappcampaignresponse;
use App\Models\Metawhatsapptemplate;
use App\Models\Sendwhatsappresponse;
use Illuminate\Console\Command;

class MetaWhatsappSceduledCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:metawhatsappcampaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $datetime = date('Y-m-d h:i');
        $getSchLists = Contactsendwhatsapp::where('campaign_type','=',2)->where('date_time','=',$datetime)->where('for_whatsapp','=','meta_whatsapp')->where('status','=',1)->get();

        if($getSchLists->count() > 0){
            // Get Meta Whatsapp API
            // $metaAPI = Metawhatsappapi::where('status','=',1)->first();
            $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['campaign'])->first();
            foreach($getSchLists as $getSchList){
                $templateDet = Metawhatsapptemplate::find($getSchList->metatemplate_id);

                // Get Country
                $getCountry = Country::find($getSchList->country_id);

                if ($getCountry) {
                    $countryName = $getCountry->name;
                } else {
                    $countryName = '';
                }

                // Get City
                $getCity = City::find($getSchList->city_id);
                if ($getCity) {
                    $cityName = $getCity->name;
                } else {
                    $cityName = '';
                }

                $field_var = explode(",",$templateDet->meta_field_var);
                $assign_var = explode(",",$templateDet->meta_assign_ar);

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
                    "[Status]",
                    "[Company]"
                ];

                $repStr = [
                    "[$getSchList->office_eng_name]",
                    "[$getSchList->office_ar_name]",
                    "[$getSchList->office_no]",
                    "[$getSchList->office_email]",
                    "[$getSchList->owner_name]",
                    "[$getSchList->owner_contact]",
                    "[$getSchList->owenr_email]",
                    "[$countryName]",
                    "[$cityName]",
                    "[$getSchList->prim_concern_name]",
                    "[$getSchList->prim_contact]",
                    "[$getSchList->prim_email]",
                    "[$getSchList->sec_concern_name]",
                    "[$getSchList->sec_contact]",
                    "[$getSchList->sec_email]",
                    "[$getSchList->concern_name3]",
                    "[$getSchList->contact3]",
                    "[$getSchList->concern_name4]",
                    "[$getSchList->contact4]",
                    "[$getSchList->concern_name5]",
                    "[$getSchList->contact5]",
                    "[$getSchList->concern_name6]",
                    "[$getSchList->contact6]",
                    "[$getSchList->status]",
                    "[$getSchList->office_eng_name]",
                ];

                $final_array = str_replace($remStr,$repStr,$assign_var);

                // Get Header Data if file not blank
                if($templateDet->whatsapp_file != ''){
                    $headerfilepath = url('admin/assets/images/template/'.$templateDet->whatsapp_file);
                }else{
                    $headerfilepath = "";
                }

                $data = [];
                $data['template_name'] = $templateDet->template_name;
                $data['template_language'] = $templateDet->template_language ?? "en_US";                

                $rem2 = ["[","]"];
                $rep2 = ["",""];



                if($getSchList->contact_type == 'all'){


                    if($getSchList->owner_contact != ''){
                        $data['phone_number'] = $getSchList->owner_contact;

                        for($i=0;count($field_var) > $i;$i++){
                           if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                $data[$field_var[$i]] = $headerfilepath;
                           }else{
                                $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                           }
                        }

                        $data['contact'] =  [
                            'first_name' => $getSchList->owner_name,
                            'last_name' => "--",
                            "email" => $getSchList->owenr_email,
                            "country" => $countryName,
                            "language_code" => "en"
                        ];

                        // Send Campaign Message
                        if(isset($metaAPI)){
                            $base_url = $metaAPI->api_base_url;
                            $vendor_id = $metaAPI->vendor_uid;
                            $access_token = $metaAPI->api_access_token;
                            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                            $token = "Authorization: Bearer ".$access_token;

                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                CURLOPT_URL => $endpoint_api,
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
                            $respost = new Sendwhatsappresponse();
                            $respost->contactsend_id = $getSchList->id;
                            $respost->name = $getSchList->owner_name;
                            $respost->mobile_no = $getSchList->owner_contact;
                            if (isset($responseGet->result)){
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = $responseGet->message;
                            }else{
                                $respost->message_status = "Failed";
                                $respost->message_text = $responseGet->message;
                                $respost->error_data_field = json_encode($responseGet->errors);
                            }

                            $respost->save();
                        }
                    }

                    if($getSchList->prim_contact != ''){
                        $data['phone_number'] = $getSchList->prim_contact;

                        for($i=0;count($field_var) > $i;$i++){
                           if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                $data[$field_var[$i]] = $headerfilepath;
                           }else{
                                $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                           }
                        }

                        $data['contact'] =  [
                            'first_name' => $getSchList->prim_concern_name,
                            'last_name' => "--",
                            "email" => $getSchList->prim_email,
                            "country" => $countryName,
                            "language_code" => "en"
                        ];

                        // Send Campaign Message
                        if(isset($metaAPI)){
                            $base_url = $metaAPI->api_base_url;
                            $vendor_id = $metaAPI->vendor_uid;
                            $access_token = $metaAPI->api_access_token;
                            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                            $token = "Authorization: Bearer ".$access_token;

                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                CURLOPT_URL => $endpoint_api,
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
                            $respost = new Sendwhatsappresponse();
                            $respost->contactsend_id = $getSchList->id;
                            $respost->name = $getSchList->prim_concern_name;
                            $respost->mobile_no = $getSchList->prim_contact;
                            if (isset($responseGet->result)){
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = $responseGet->message;
                            }else{
                                $respost->message_status = "Failed";
                                $respost->message_text = $responseGet->message;
                                $respost->error_data_field = json_encode($responseGet->errors);
                            }

                            $respost->save();
                        }
                    }

                    if($getSchList->sec_contact != ''){
                        $data['phone_number'] = $getSchList->sec_contact;

                        for($i=0;count($field_var) > $i;$i++){
                           if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                $data[$field_var[$i]] = $headerfilepath;
                           }else{
                                $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                           }
                        }

                        $data['contact'] =  [
                            'first_name' => $getSchList->sec_concern_name,
                            'last_name' => "--",
                            "email" => $getSchList->sec_email,
                            "country" => $countryName,
                            "language_code" => "en"
                        ];

                        // Send Campaign Message
                        if(isset($metaAPI)){
                            $base_url = $metaAPI->api_base_url;
                            $vendor_id = $metaAPI->vendor_uid;
                            $access_token = $metaAPI->api_access_token;
                            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                            $token = "Authorization: Bearer ".$access_token;

                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                CURLOPT_URL => $endpoint_api,
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
                            $respost = new Sendwhatsappresponse();
                            $respost->contactsend_id = $getSchList->id;
                            $respost->name = $getSchList->sec_concern_name;
                            $respost->mobile_no = $getSchList->sec_contact;
                            if (isset($responseGet->result)){
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = $responseGet->message;
                            }else{
                                $respost->message_status = "Failed";
                                $respost->message_text = $responseGet->message;
                                $respost->error_data_field = json_encode($responseGet->errors);
                            }

                            $respost->save();
                        }
                    }

                    if($getSchList->contact3 != ''){
                        $data['phone_number'] = $getSchList->contact3;

                        for($i=0;count($field_var) > $i;$i++){
                           if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                $data[$field_var[$i]] = $headerfilepath;
                           }else{
                                $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                           }
                        }

                        $data['contact'] =  [
                            'first_name' => $getSchList->concern_name3,
                            'last_name' => "--",
                            "email" => "---",
                            "country" => $countryName,
                            "language_code" => "en"
                        ];

                        // Send Campaign Message
                        if(isset($metaAPI)){
                            $base_url = $metaAPI->api_base_url;
                            $vendor_id = $metaAPI->vendor_uid;
                            $access_token = $metaAPI->api_access_token;
                            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                            $token = "Authorization: Bearer ".$access_token;

                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                CURLOPT_URL => $endpoint_api,
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
                            $respost = new Sendwhatsappresponse();
                            $respost->contactsend_id = $getSchList->id;
                            $respost->name = $getSchList->concern_name3;
                            $respost->mobile_no = $getSchList->contact3;
                            if (isset($responseGet->result)){
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = $responseGet->message;
                            }else{
                                $respost->message_status = "Failed";
                                $respost->message_text = $responseGet->message;
                                $respost->error_data_field = json_encode($responseGet->errors);
                            }

                            $respost->save();
                        }
                    }

                    if($getSchList->contact4 != ''){
                        $data['phone_number'] = $getSchList->contact4;

                        for($i=0;count($field_var) > $i;$i++){
                           if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                $data[$field_var[$i]] = $headerfilepath;
                           }else{
                                $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                           }
                        }

                        $data['contact'] =  [
                            'first_name' => $getSchList->concern_name4,
                            'last_name' => "--",
                            "email" => "---",
                            "country" => $countryName,
                            "language_code" => "en"
                        ];

                        // Send Campaign Message
                        if(isset($metaAPI)){
                            $base_url = $metaAPI->api_base_url;
                            $vendor_id = $metaAPI->vendor_uid;
                            $access_token = $metaAPI->api_access_token;
                            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                            $token = "Authorization: Bearer ".$access_token;

                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                CURLOPT_URL => $endpoint_api,
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
                            $respost = new Sendwhatsappresponse();
                            $respost->contactsend_id = $getSchList->id;
                            $respost->name = $getSchList->concern_name4;
                            $respost->mobile_no = $getSchList->contact4;
                            if (isset($responseGet->result)){
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = $responseGet->message;
                            }else{
                                $respost->message_status = "Failed";
                                $respost->message_text = $responseGet->message;
                                $respost->error_data_field = json_encode($responseGet->errors);
                            }

                            $respost->save();
                        }
                    }

                    if($getSchList->contact5 != ''){
                        $data['phone_number'] = $getSchList->contact5;

                        for($i=0;count($field_var) > $i;$i++){
                           if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                $data[$field_var[$i]] = $headerfilepath;
                           }else{
                                $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                           }
                        }

                        $data['contact'] =  [
                            'first_name' => $getSchList->concern_name5,
                            'last_name' => "--",
                            "email" => "---",
                            "country" => $countryName,
                            "language_code" => "en"
                        ];

                        // Send Campaign Message
                        if(isset($metaAPI)){
                            $base_url = $metaAPI->api_base_url;
                            $vendor_id = $metaAPI->vendor_uid;
                            $access_token = $metaAPI->api_access_token;
                            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                            $token = "Authorization: Bearer ".$access_token;

                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                CURLOPT_URL => $endpoint_api,
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
                            $respost = new Sendwhatsappresponse();
                            $respost->contactsend_id = $getSchList->id;
                            $respost->name = $getSchList->concern_name5;
                            $respost->mobile_no = $getSchList->contact5;
                            if (isset($responseGet->result)){
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = $responseGet->message;
                            }else{
                                $respost->message_status = "Failed";
                                $respost->message_text = $responseGet->message;
                                $respost->error_data_field = json_encode($responseGet->errors);
                            }
                            $respost->save();
                        }
                    }

                    if($getSchList->contact6 != ''){
                        $data['phone_number'] = $getSchList->contact6;

                        for($i=0;count($field_var) > $i;$i++){
                           if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                $data[$field_var[$i]] = $headerfilepath;
                           }else{
                                $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                           }
                        }

                        $data['contact'] =  [
                            'first_name' => $getSchList->concern_name6,
                            'last_name' => "--",
                            "email" => "---",
                            "country" => $countryName,
                            "language_code" => "en"
                        ];

                        // Send Campaign Message
                        if(isset($metaAPI)){
                            $base_url = $metaAPI->api_base_url;
                            $vendor_id = $metaAPI->vendor_uid;
                            $access_token = $metaAPI->api_access_token;
                            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                            $token = "Authorization: Bearer ".$access_token;

                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                CURLOPT_URL => $endpoint_api,
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
                            $respost = new Sendwhatsappresponse();
                            $respost->contactsend_id = $getSchList->id;
                            $respost->name = $getSchList->concern_name6;
                            $respost->mobile_no = $getSchList->contact6;
                            if (isset($responseGet->result)){
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = $responseGet->message;
                            }else{
                                $respost->message_status = "Failed";
                                $respost->message_text = $responseGet->message;
                                $respost->error_data_field = json_encode($responseGet->errors);
                            }

                            $respost->save();
                        }
                    }

                }

                if($getSchList->contact_type == 'owner'){
                    if($getSchList->owner_contact != ''){
                        $data['phone_number'] = $getSchList->owner_contact;

                        for($i=0;count($field_var) > $i;$i++){
                           if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                $data[$field_var[$i]] = $headerfilepath;
                           }else{
                                $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                           }
                        }

                        $data['contact'] =  [
                            'first_name' => $getSchList->owner_name,
                            'last_name' => "--",
                            "email" => $getSchList->owenr_email,
                            "country" => $countryName,
                            "language_code" => "en"
                        ];

                        // Send Campaign Message
                        if(isset($metaAPI)){
                            $base_url = $metaAPI->api_base_url;
                            $vendor_id = $metaAPI->vendor_uid;
                            $access_token = $metaAPI->api_access_token;
                            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                            $token = "Authorization: Bearer ".$access_token;

                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                CURLOPT_URL => $endpoint_api,
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
                            $respost = new Sendwhatsappresponse();
                            $respost->contactsend_id = $getSchList->id;
                            $respost->name = $getSchList->owner_name;
                            $respost->mobile_no = $getSchList->owner_contact;
                            if (isset($responseGet->result)){
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = $responseGet->message;
                            }else{
                                $respost->message_status = "Failed";
                                $respost->message_text = $responseGet->message;
                                $respost->error_data_field = json_encode($responseGet->errors);
                            }

                            $respost->save();
                        }
                    }
                }

                if($getSchList->contact_type == 'Primary'){
                    if($getSchList->prim_contact != ''){
                        $data['phone_number'] = $getSchList->prim_contact;

                        for($i=0;count($field_var) > $i;$i++){
                           if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                $data[$field_var[$i]] = $headerfilepath;
                           }else{
                                $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                           }
                        }

                        $data['contact'] =  [
                            'first_name' => $getSchList->prim_concern_name,
                            'last_name' => "--",
                            "email" => $getSchList->prim_email,
                            "country" => $countryName,
                            "language_code" => "en"
                        ];

                        // Send Campaign Message
                        if(isset($metaAPI)){
                            $base_url = $metaAPI->api_base_url;
                            $vendor_id = $metaAPI->vendor_uid;
                            $access_token = $metaAPI->api_access_token;
                            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                            $token = "Authorization: Bearer ".$access_token;

                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                CURLOPT_URL => $endpoint_api,
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
                            $respost = new Sendwhatsappresponse();
                            $respost->contactsend_id = $getSchList->id;
                            $respost->name = $getSchList->prim_concern_name;
                            $respost->mobile_no = $getSchList->prim_contact;
                            if (isset($responseGet->result)){
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = $responseGet->message;
                            }else{
                                $respost->message_status = "Failed";
                                $respost->message_text = $responseGet->message;
                                $respost->error_data_field = json_encode($responseGet->errors);
                            }

                            $respost->save();
                        }
                    }
                }

                if($getSchList->contact_type == 'Secondary'){
                    if($getSchList->sec_contact != ''){
                        $data['phone_number'] = $getSchList->sec_contact;

                        for($i=0;count($field_var) > $i;$i++){
                           if($field_var[$i] == 'header_image' || $field_var[$i] == 'header_video' || $field_var[$i] == 'header_document' || $field_var[$i] == 'header_document_name'){
                                $data[$field_var[$i]] = $headerfilepath;
                           }else{
                                $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                           }
                        }

                        $data['contact'] =  [
                            'first_name' => $getSchList->sec_concern_name,
                            'last_name' => "--",
                            "email" => $getSchList->sec_email,
                            "country" => $countryName,
                            "language_code" => "en"
                        ];

                        // Send Campaign Message
                        if(isset($metaAPI)){
                            $base_url = $metaAPI->api_base_url;
                            $vendor_id = $metaAPI->vendor_uid;
                            $access_token = $metaAPI->api_access_token;
                            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                            $token = "Authorization: Bearer ".$access_token;

                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                                CURLOPT_URL => $endpoint_api,
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
                            $respost = new Sendwhatsappresponse();
                            $respost->contactsend_id = $getSchList->id;
                            $respost->name = $getSchList->sec_concern_name;
                            $respost->mobile_no = $getSchList->sec_contact;
                            if (isset($responseGet->result)){
                                $respost->message_status = $responseGet->result;
                                $respost->message_text = $responseGet->message;
                            }else{
                                $respost->message_status = "Failed";
                                $respost->message_text = $responseGet->message;
                                $respost->error_data_field = json_encode($responseGet->errors);
                            }

                            $respost->save();
                        }
                    }
                }

            }
        }

        return Command::SUCCESS;
    }
}
