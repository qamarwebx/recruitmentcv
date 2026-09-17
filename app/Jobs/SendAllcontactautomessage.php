<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\Automessageresponse;
use App\Models\Autometanotification;
use App\Models\City;
use App\Models\Country;
use App\Models\Metanotification;
use App\Models\Metawhatsappapi;
use App\Models\Metawhatsapptemplate;
use App\Models\TeamMemberWebPage;
use App\Models\ImageUrl;
use App\Models\Whatsappchaturl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Str;
use Illuminate\Support\Facades\Log;

class SendAllcontactautomessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        // Get Template and API base meta notification availability
        Log::channel('SendAllcontactautomessage')->info('⏳ check started from job');

        $metanotification = Autometanotification::where('template_for','=','allcontact')->where('status',1)->first();

        Log::channel('SendAllcontactautomessage')->info('⏳ check started from job',[$metanotification]);

        if ($metanotification) {
            // Get Template Based on ID
            $template = Metawhatsapptemplate::find($metanotification->metatemp_id);
            // Get API Based On Template Whatsapp API ID
            $metaApi = Metawhatsappapi::where('id','=',$template->metaapi_id)->where('status',1)->first();

            if ($metaApi) {
                $metaapi_id = $metaApi->id;
                $base_url = $metaApi->api_base_url;
                $vendor_id = $metaApi->vendor_uid;
                $access_token = $metaApi->api_access_token;
            } else {
                $otherMetApi = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['Allcontact'])->first();

                if ($otherMetApi) {
                    $metaapi_id = $otherMetApi->id;
                    $base_url = $otherMetApi->api_base_url;
                    $vendor_id = $otherMetApi->vendor_uid;
                    $access_token = $otherMetApi->api_access_token;
                } else {
                    $metaapi_id = "";
                    $base_url = "";
                    $vendor_id = "";
                    $access_token = "";
                }
            }

            Log::channel('SendAllcontactautomessage')->info('⏳ check started', ['base_url' => $base_url,'vendor_id' => $vendor_id,'access_token' => $access_token]);


            if ($base_url != "" && $vendor_id != "" && $access_token != "") {
                $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                $token = "Authorization: Bearer ".$access_token;

                // Generate 32 Random String
                $random_string = Str::random(32);
                $unsubscribe_url = 'whatsapp/unsubscribe/request/allcontact/'.$random_string.'/'.$this->post['id'];

                $staffDet = Whatsappchaturl::where('staff_id','=',$this->post['careoff_id'])->where('status',true)->first();
                if ($staffDet) {
                    $dynamichaturl = $staffDet->chat_url;
                } else {
                    $dynamichaturl = "https://wa.me";
                }


                $teamMemberPage = TeamMemberWebPage::where('staff_id', $this->post['careoff_id'])->latest('id')->first();
                if ($teamMemberPage) {
                    $dynamicWebpageTeam = "https://qamrjob.com/housedriver/" . intval($teamMemberPage->staff_id);
                } else {
                    $dynamicWebpageTeam = '';
                }

                // Dynamic Image URL
                $image_url = ImageUrl::where('staff_id', $this->post['careoff_id'])->latest('id')->first();
                if ($image_url) {
                    $dynamic_image_url = $image_url->url;
                } else {
                    $dynamic_image_url = '';
                }

                if ($template->meta_url_type == 0) {
                    if($template->whatsapp_file != ''){
                        // $header_file_path = url('admin/assets/images/template/'.$template->whatsapp_file);
                        $header_file_path = config('app.url').'/admin/assets/images/template/'.$template->whatsapp_file;
                    }else{
                        $header_file_path = "";
                    }
                } elseif ($template->meta_url_type == 1) {
                    if ($template->static_url != '') {
                        $header_file_path = $template->static_url;
                    } else {
                        $header_file_path = "";
                    }

                } else {
                    if($template->whatsapp_file != ''){
                        // $header_file_path = url('admin/assets/images/template/'.$template->whatsapp_file);
                        $header_file_path = config('app.url').'/admin/assets/images/template/'.$template->whatsapp_file;
                    }else{
                        $header_file_path = "";
                    }
                }

                $field_variable = explode(",",$template->meta_field_var);
                $assgn_variable = explode(",",$template->meta_assign_ar);

                $metatemplatedata = [
                    'header_file_path' => $header_file_path,
                    'field_variable' => $field_variable,
                    'assgn_variable' => $assgn_variable,
                    'template_name' => $template->template_name
                ];


                // Get All Details From Staff
                $getAdminStaff = Admin::where('id','=',$this->post['careoff_id'])->where('status',true)->first();

                if ($getAdminStaff) {
                    $careoffname = $getAdminStaff->name;
                    $careoff_no1 = $getAdminStaff->care_no_1;
                    $careoff_no2 = $getAdminStaff->care_no_2;
                    $call_link = "tel:+".$getAdminStaff->care_no_1;
                } else {
                    $careoffname = "";
                    $careoff_no1 = "";
                    $careoff_no2 = "";
                    $call_link = "";
                }

                // Get Country
                $getCountry = Country::find($this->post['country_id']);

                // Get City
                $getCity = City::find($this->post['city_id']);

                if ($getCity) {
                    $cityName = $getCity->name;
                } else {
                    $cityName = '';
                }

                if ($getCountry) {
                    $countryName = $getCountry->name;
                } else {
                    $countryName = '';
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
                    "[Membership]",
                    "[Careoff]",
                    "[Careoff Contact No.1]",
                    "[Careoff Contact No.2]"
                ];

                $repStr = [
                    $this->post['office_name'],
                    $this->post['lead_type'],
                    $this->post['full_name'],
                    $this->post['email'],
                    $countryName,
                    $cityName,
                    $this->post['mobile_no1_wsp'],
                    $this->post['email0'],
                    $this->post['mobile_no2_wsp'],
                    $this->post['email1'],
                    $this->post['mobile_no3_wsp'],
                    $this->post['email2'],
                    $this->post['id'],
                    $careoffname,
                    $careoff_no1,
                    $careoff_no2
                ];

                $finalAssignVar = str_replace($remStr,$repStr,$assgn_variable);
                $rem2 = ["[","]"];
                $rep2 = ["",""];

                Log::channel('SendAllcontactautomessage')
                ->info('⏳ check started', [
                    'secondary_no_wsp' => $this->post['secondary_no_wsp'],
                    'primary_no_wsp' => $this->post['primary_no_wsp'],
                    'access_token' => $this->post['access_token']
                ]);


                if ($this->post['secondary_no_wsp'] != '') {

                    $primary_mob = $this->post['secondary_no_wsp'];


                    $data = [];
                    $data['phone_number'] = $primary_mob;
                    $data['template_name'] = $template->template_name;
                    $data['template_language'] = $template->template_language ?? "en_US";

                    for ($i=0; $i < count($field_variable); $i++) {
                        if ($assgn_variable[$i] == '[Others]') {
                            $data[$field_variable[$i]] = $metatemplatedata['header_file_path'];
                        }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                            $data[$field_variable[$i]] = $unsubscribe_url;
                        }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                            $data[$field_variable[$i]] = $dynamichaturl;
                        }elseif ($assgn_variable[$i] == '[Dynamic Webpage Team]') {
                            $data[$field_variable[$i]] = $dynamicWebpageTeam;
                        }elseif ($assgn_variable[$i] == '[Image URL]') {
                            $data[$field_variable[$i]] = $dynamic_image_url;
                        }elseif ($assgn_variable[$i] == '[Document Name]') {
                             $data[$field_variable[$i]] = $template->document_name;
                        }elseif ($assgn_variable[$i] == '[Careoff Call Marketing]') {
                            $data[$field_variable[$i]] = $call_link;
                        }else{
                            $data[$field_variable[$i]] = $finalAssignVar[$i];
                            // $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                        }

                    }



                    $data['contact'] =  [
                        'first_name' => $this->post['full_name'],
                        'last_name' => " ",
                        "email" => $this->post['email'],
                        "country" => $countryName,
                        "language_code" => "en"
                    ];

                    // Send Campaign Message
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

                    // Upload Campaign Response
                    $msg_response = new Automessageresponse();
                    $msg_response->template_for = "allcontact";
                    $msg_response->autometanotification_id = $metanotification->id;
                    $msg_response->metatemplate_id = $metanotification->metatemp_id;
                    $msg_response->metaapi_id = $metaapi_id;
                    $msg_response->allcontact_id = $this->post['id'];
                    $msg_response->mobile_no = $primary_mob;
                    $msg_response->main_response = json_encode($responseGet);
                    if (isset($responseGet->result)){
                        $msg_response->message_status = $responseGet->result;
                        $msg_response->message_text = $responseGet->message;
                    }else{
                        $msg_response->message_status = "Failed";
                        if (isset($responseGet->message)) {
                            $msg_response->message_text = $responseGet->message;
                            $msg_response->error_data_field = json_encode($responseGet->errors);
                        }else{
                            $msg_response->message_text = "Unknown Error";
                            $msg_response->error_data_field = "Unknown Error";
                        }
                    }

                    $msg_response->save();

                    Log::channel('SendAllcontactautomessage')
                    ->info('⏳ check started in secondary_no_wsp', [
                        'msg_response' => json_decode($msg_response),
                        'responseGet' => $responseGet,
                    ]);

                }

                if ($this->post['primary_no_wsp'] != '') {

                    $secondary_mob = $this->post['primary_no_wsp'];


                    $data = [];
                    $data['phone_number'] = $secondary_mob;
                    $data['template_name'] = $template->template_name;
                    $data['template_language'] = $template->template_language ?? "en_US";

                    for ($i=0; $i < count($field_variable); $i++) {
                        if ($assgn_variable[$i] == '[Others]') {
                            $data[$field_variable[$i]] = $metatemplatedata['header_file_path'];
                        }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                            $data[$field_variable[$i]] = $unsubscribe_url;
                        }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                            $data[$field_variable[$i]] = $dynamichaturl;
                        }elseif ($assgn_variable[$i] == '[Dynamic Webpage Team]') {
                            $data[$field_variable[$i]] = $dynamicWebpageTeam;
                        }elseif ($assgn_variable[$i] == '[Image URL]') {
                            $data[$field_variable[$i]] = $dynamic_image_url;
                        }elseif ($assgn_variable[$i] == '[Document Name]') {
                             $data[$field_variable[$i]] = $template->document_name;
                        }elseif ($assgn_variable[$i] == '[Careoff Call Marketing]') {
                            $data[$field_variable[$i]] = $call_link;
                        }else{
                            $data[$field_variable[$i]] = $finalAssignVar[$i];
                            // $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                        }
                    }



                    $data['contact'] =  [
                        'first_name' => $this->post['full_name'],
                        'last_name' => " ",
                        "email" => $this->post['email'],
                        "country" => $countryName,
                        "language_code" => "en"
                    ];

                    // Send Campaign Message
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

                    // Upload Campaign Response
                    $msg_response = new Automessageresponse();
                    $msg_response->template_for = "allcontact";
                    $msg_response->autometanotification_id = $metanotification->id;
                    $msg_response->metatemplate_id = $metanotification->metatemp_id;
                    $msg_response->metaapi_id = $metaapi_id;
                    $msg_response->allcontact_id = $this->post['id'];
                    $msg_response->mobile_no = $secondary_mob;
                    $msg_response->main_response = json_encode($responseGet);
                    if (isset($responseGet->result)){
                        $msg_response->message_status = $responseGet->result;
                        $msg_response->message_text = $responseGet->message;
                    }else{
                        $msg_response->message_status = "Failed";
                        if (isset($responseGet->message)) {
                            $msg_response->message_text = $responseGet->message;
                            $msg_response->error_data_field = json_encode($responseGet->errors);
                        }else{
                            $msg_response->message_text = "Unknown Error";
                            $msg_response->error_data_field = "Unknown Error";
                        }
                    }

                    $msg_response->save();

                    Log::channel('SendAllcontactautomessage')
                    ->info('⏳ check started in primary_no_wsp', [
                        'msg_response' => json_decode($msg_response),
                        'responseGet' => $responseGet,
                    ]);

                }

                if ($this->post['mobile_no1_wsp'] != '') {

                    $mobile_no1 = $this->post['mobile_no1_wsp'];


                    $data = [];
                    $data['phone_number'] = $mobile_no1;
                    $data['template_name'] = $template->template_name;
                    $data['template_language'] = $template->template_language ?? "en_US";

                    for ($i=0; $i < count($field_variable); $i++) {
                        if ($assgn_variable[$i] == '[Others]') {
                            $data[$field_variable[$i]] = $metatemplatedata['header_file_path'];
                        }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                            $data[$field_variable[$i]] = $unsubscribe_url;
                        }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                            $data[$field_variable[$i]] = $dynamichaturl;
                        }elseif ($assgn_variable[$i] == '[Dynamic Webpage Team]') {
                            $data[$field_variable[$i]] = $dynamicWebpageTeam;
                        }elseif ($assgn_variable[$i] == '[Image URL]') {
                            $data[$field_variable[$i]] = $dynamic_image_url;
                        }elseif ($assgn_variable[$i] == '[Document Name]') {
                             $data[$field_variable[$i]] = $template->document_name;
                        }elseif ($assgn_variable[$i] == '[Careoff Call Marketing]') {
                            $data[$field_variable[$i]] = $call_link;
                        }else{
                            $data[$field_variable[$i]] = $finalAssignVar[$i];
                            // $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                        }
                    }



                    $data['contact'] =  [
                        'first_name' => $this->post['full_name'],
                        'last_name' => " ",
                        "email" => $this->post['email'],
                        "country" => $countryName,
                        "language_code" => "en"
                    ];

                    // Send Campaign Message
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

                    // Upload Campaign Response
                    $msg_response = new Automessageresponse();
                    $msg_response->template_for = "allcontact";
                    $msg_response->autometanotification_id = $metanotification->id;
                    $msg_response->metatemplate_id = $metanotification->metatemp_id;
                    $msg_response->metaapi_id = $metaapi_id;
                    $msg_response->allcontact_id = $this->post['id'];
                    $msg_response->mobile_no = $mobile_no1;
                    $msg_response->main_response = json_encode($responseGet);
                    if (isset($responseGet->result)){
                        $msg_response->message_status = $responseGet->result;
                        $msg_response->message_text = $responseGet->message;
                    }else{
                        $msg_response->message_status = "Failed";
                        if (isset($responseGet->message)) {
                            $msg_response->message_text = $responseGet->message;
                            $msg_response->error_data_field = json_encode($responseGet->errors);
                        }else{
                            $msg_response->message_text = "Unknown Error";
                            $msg_response->error_data_field = "Unknown Error";
                        }
                    }

                    $msg_response->save();

                    Log::channel('SendAllcontactautomessage')
                    ->info('⏳ check started in mobile_no1_wsp', [
                        'msg_response' => json_decode($msg_response),
                        'responseGet' => $responseGet,
                    ]);

                }

            }


        }

    }
}
