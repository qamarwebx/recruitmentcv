<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contactsendwhatsapp;
use App\Models\Whatsappapi;
use App\Models\Contactp;
use App\Models\Contactplus;
use App\Jobs\BulkContactSendJob;
use App\Models\Admin;
use App\Models\Metawhatsapptemplate;
use App\Models\Metawhatsappapi;
use App\Models\Country;
use App\Models\City;
use App\Models\Sendwhatsappresponse;
use App\Models\Contactsendtag;
use App\Models\Whatsappchaturl;
use Str;

class BulkContactWhatsappScheduled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'auto:bulkcontactsend';

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
        $getSchList = Contactsendwhatsapp::where('campaign_type','=',2)->where('date_time','=',$datetime)->get();
        if (isset($getSchList)) {

            foreach ($getSchList as $post) {
                if ($post->for_whatsapp == 'normal_whatsapp') {
                    // Get Whatsapp API
                    $getAPIs = Whatsappapi::wherein('id',explode(",",$post->whatsapp_api))->get();
                    // Contactplus Data
                    $contactdet = Contactplus::find($post->contactp_id);
                    foreach ($getAPIs as $getAPI) {
                        dispatch(new BulkContactSendJob($contactdet,$post,$getAPI))->onConnection('database')->onQueue('default');
                    }
                }

                if ($post->for_whatsapp == 'meta_whatsapp') {
                    $templateDet = Metawhatsapptemplate::find($post->metatemplate_id);
                    // Meta Whatsapp API
                    // $metaAPI = Metawhatsappapi::where('status','=',1)->first();

                    $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['campaign'])->first();

                    // Contactplus Data
                    $contactdet = Contactplus::find($post->contactp_id);


                    if (isset($metaAPI)) {
                        // API Detai
                        $base_url = $metaAPI->api_base_url;
                        $vendor_id = $metaAPI->vendor_uid;
                        $access_token = $metaAPI->api_access_token;
                        $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                        $token = "Authorization: Bearer ".$access_token;

                        $contact_types = $post->contact_type;

                        // Get Header Data if file not blank
                        if ($templateDet->meta_url_type == 0) {
                            if($templateDet->whatsapp_file != ''){
                                $headerfilepath = url('admin/assets/images/template/'.$templateDet->whatsapp_file);
                            }else{
                                $headerfilepath = "";
                            }
                        } elseif ($templateDet->meta_url_type == 1) {
                            if($templateDet->static_url != ''){
                                $headerfilepath = $templateDet->static_url;
                            }else{
                                $headerfilepath = "";
                            }
                        } else {
                            if($templateDet->whatsapp_file != ''){
                                $headerfilepath = url('admin/assets/images/template/'.$templateDet->whatsapp_file);
                            }else{
                                $headerfilepath = "";
                            }
                        }

                        // Get Unsubscribe URL
                        $random_string = Str::random(32);
                        $unsubscribe_url = 'whatsapp/unsubscribe/request/'.$random_string.'/'.$contactdet->id;

                        // ChatURL Link
                        // $staffDet = Admin::find($contactdet->careoff_id);
                        // if (isset($staffDet)) {
                        //     // $dynamichaturl = $staffDet->whatsaapp_chat_url.'/'.$staffDet->work_number;
                        //     $dynamichaturl = $staffDet->whatsaapp_chat_url;
                        // }else{
                        //     $dynamichaturl = "https://wa.me";
                        // }

                        $staffDet = Whatsappchaturl::where('staff_id','=',$contactdet->careoff_id)->where('status',true)->first();
                        if ($staffDet) {
                            $dynamichaturl = $staffDet->chat_url;
                        } else {
                            $dynamichaturl = "https://wa.me";
                        }

                        // Get Country
                        $getCountry = Country::find($contactdet->country_id);

                        if ($getCountry) {
                            $countryName = $getCountry->name;
                        } else {
                            $countryName = '';
                        }

                        // Get City
                        $getCity = City::find($contactdet->city_id);
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
                            "[$contactdet->office_eng_name]",
                            "[$contactdet->office_ar_name]",
                            "[$contactdet->office_no]",
                            "[$contactdet->office_email]",
                            "[$contactdet->owner_name]",
                            "[$contactdet->owner_contact]",
                            "[$contactdet->owenr_email]",
                            "[$countryName]",
                            "[$cityName]",
                            "[$contactdet->prim_concern_name]",
                            "[$contactdet->prim_contact]",
                            "[$contactdet->prim_email]",
                            "[$contactdet->sec_concern_name]",
                            "[$contactdet->sec_contact]",
                            "[$contactdet->sec_email]",
                            "[$contactdet->concern_name3]",
                            "[$contactdet->contact3]",
                            "[$contactdet->concern_name4]",
                            "[$contactdet->contact4]",
                            "[$contactdet->concern_name5]",
                            "[$contactdet->contact5]",
                            "[$contactdet->concern_name6]",
                            "[$contactdet->contact6]",
                            "[$contactdet->status]",
                            "[$contactdet->office_eng_name]",
                        ];

                        $final_array = str_replace($remStr,$repStr,$assign_var);

                        $rem2 = ["[","]"];
                        $rep2 = ["",""];

                        // Send Whatsapp Start
                        $data = [];
                        $data['template_name'] = $templateDet->template_name;
                        $data['template_language'] = $templateDet->template_language ?? "en_US";

                        foreach ($contact_types as $contact_type) {
                            if (($contact_type == 'all' || $contact_type == 'owner') && $contactdet->owner_contact != '') {
                                $data['phone_number'] = $contactdet->owner_contact;



                                for ($i=0; $i < count($field_var); $i++) {
                                    if ($final_array[$i] == '[Others]') {
                                        $data[$field_var[$i]] = $headerfilepath;
                                    }elseif ($final_array[$i] == '[Dynamic Unsubscribe URL]') {
                                        $data[$field_var[$i]] = $unsubscribe_url;
                                    }elseif ($final_array[$i] == '[Whatsapp Chat Dynamic URL]') {
                                        $data[$field_var[$i]] = $dynamichaturl;
                                    }elseif ($final_array[$i] == '[Document Name]') {
                                        $data[$field_var[$i]] = $templateDet->document_name;
                                    }else{
                                        $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                                    }
                                }


                                $data['contact'] =  [
                                    'first_name' => $contactdet->owner_name,
                                    'last_name' => "--",
                                    "email" => $contactdet->owenr_email,
                                    "country" => $countryName,
                                    "language_code" => "en"
                                ];

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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactdet->owner_name;
                                $respost->mobile_no = $contactdet->owner_contact;
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


                            if (($contact_type == 'all' || $contact_type == 'Primary') && $contactdet->prim_contact != '') {
                                $data['phone_number'] = $contactdet->prim_contact;



                                for ($i=0; $i < count($field_var); $i++) {
                                    if ($final_array[$i] == '[Others]') {
                                        $data[$field_var[$i]] = $headerfilepath;
                                    }elseif ($final_array[$i] == '[Dynamic Unsubscribe URL]') {
                                        $data[$field_var[$i]] = $unsubscribe_url;
                                    }elseif ($final_array[$i] == '[Whatsapp Chat Dynamic URL]') {
                                        $data[$field_var[$i]] = $dynamichaturl;
                                    }elseif ($final_array[$i] == '[Document Name]') {
                                        $data[$field_var[$i]] = $templateDet->document_name;
                                    }else{
                                        $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                                    }
                                }

                                $data['contact'] =  [
                                    'first_name' => $contactdet->prim_concern_name,
                                    'last_name' => "--",
                                    "email" => $contactdet->prim_email,
                                    "country" => $countryName,
                                    "language_code" => "en"
                                ];

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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactdet->prim_concern_name;
                                $respost->mobile_no = $contactdet->prim_contact;
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

                            if (($contact_type == 'all' || $contact_type == 'Secondary') && $contactdet->sec_contact != '') {
                                $data['phone_number'] = $contactdet->sec_contact;


                                for ($i=0; $i < count($field_var); $i++) {
                                    if ($final_array[$i] == '[Others]') {
                                        $data[$field_var[$i]] = $headerfilepath;
                                    }elseif ($final_array[$i] == '[Dynamic Unsubscribe URL]') {
                                        $data[$field_var[$i]] = $unsubscribe_url;
                                    }elseif ($final_array[$i] == '[Whatsapp Chat Dynamic URL]') {
                                        $data[$field_var[$i]] = $dynamichaturl;
                                    }elseif ($final_array[$i] == '[Document Name]') {
                                        $data[$field_var[$i]] = $templateDet->document_name;
                                    }else{
                                        $data[$field_var[$i]] = str_replace($rem2,$rep2,$final_array[$i]);
                                    }
                                }

                                $data['contact'] =  [
                                    'first_name' => $contactdet->sec_concern_name,
                                    'last_name' => "--",
                                    "email" => $contactdet->sec_email,
                                    "country" => $countryName,
                                    "language_code" => "en"
                                ];

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
                                $respost->contactsend_id = $post->id;
                                $respost->name = $contactdet->sec_concern_name;
                                $respost->mobile_no = $contactdet->sec_contact;
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

                        // Update Contact Send Tag and Store Send Tag
                        $countcontactTag = Contactsendtag::where('contact_id','=',$contactdet->id)->count();
                        $contactTag = new Contactsendtag();
                        $contactTag->contact_id = $contactdet->id;
                        $contactTag->send_tag = $countcontactTag + 1;
                        $contactTag->send_date = date('Y-m-d');
                        $contactTag->save();

                        // Update Contact Send Tag and Send Date
                        $updContactSendTag = Contactplus::find($contactdet->id);
                        $updContactSendTag->send_tag = "Send ".$contactTag->send_tag;
                        $updContactSendTag->send_date = $contactTag->send_date.",".$contactdet->send_date;
                        $updContactSendTag->save();

                    }else{
                        $respost = new Sendwhatsappresponse();
                        $respost->contactsend_id = $post->id;
                        $respost->message_status = "Failed";
                        $respost->message_text = "Meta Whatsapp API Not Connect";
                        $respost->save();
                    }

                }



            }
        }


        return Command::SUCCESS;
    }
}
