<?php

namespace App\Jobs;

use App\Models\Automessageresponse;
use App\Models\Autometanotification;
use App\Models\Country;
use App\Models\Metawhatsappapi;
use App\Models\Metawhatsapptemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoSendMessageForLeadJobs implements ShouldQueue
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
        // Lead Source From www.qamrjob.com means Leads From Candidate
        if ($this->post['lead_source'] == "www.qamrjob.com") {
            $metanotification = Autometanotification::where('template_for','=','leads_candidate')->where('status',1)->first();

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
                    $otherMetApi = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['leads_candidate'])->first();

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

                if ($base_url != "" && $vendor_id != "" && $access_token != "") {
                    $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                    $token = "Authorization: Bearer ".$access_token;

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


                    $remStr = [
                        "[Company]",
                        "[Full Name]",
                        "[Email]",
                        "[Country]",
                        "[Mobile No]",
                        "[Whatsapp No]"
                    ];

                    $repStr = [
                        $this->post['company_name'],
                        $this->post['cand_name'],
                        $this->post['email'],
                        $this->post['country'],
                        $this->post['mob_no'],
                        $this->post['whatsapp_no']
                    ];


                    $finalAssignVar = str_replace($remStr,$repStr,$assgn_variable);
                    $rem2 = ["[","]"];
                    $rep2 = ["",""];

                    if ($this->post['whatsapp_no'] != '') {

                        $whatsapp_no = $this->post['whatsapp_no'];

                        $data = [];
                        $data['phone_number'] = $whatsapp_no;
                        $data['template_name'] = $template->template_name;
                        $data['template_language'] = $template->template_language ?? "en_US";

                        for ($i=0; $i < count($field_variable); $i++) {
                            if ($assgn_variable[$i] == '[Others]') {
                                $data[$field_variable[$i]] = $metatemplatedata['header_file_path'];
                            }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                                // $data[$field_variable[$i]] = $unsubscribe_url;
                                $data[$field_variable[$i]] = "";
                            }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                                // $data[$field_variable[$i]] = $dynamichaturl;
                                $data[$field_variable[$i]] = "";
                            }elseif ($assgn_variable[$i] == '[Document Name]') {
                                $data[$field_variable[$i]] = $template->document_name;
                            }else{
                                $data[$field_variable[$i]] = $finalAssignVar[$i];
                                // $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                            }
                        }



                        $data['contact'] =  [
                            'first_name' => $this->post['cand_name'],
                            'last_name' => " ",
                            "email" => $this->post['email'],
                            "country" => $this->post['country'],
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
                        $msg_response->template_for = "leads_candidate";
                        $msg_response->autometanotification_id = $metanotification->id;
                        $msg_response->metatemplate_id = $metanotification->metatemp_id;
                        $msg_response->metaapi_id = $metaapi_id;
                        $msg_response->allcontact_id = $this->post['id'];
                        $msg_response->mobile_no = $whatsapp_no;
                        $msg_response->main_response = json_encode($responseGet);
                        if (isset($responseGet->result)){
                            $msg_response->message_status = $responseGet->result;
                            $msg_response->message_text = $responseGet->message;
                        }else{
                            $msg_response->message_status = "Failed";
                            if (isset($responseGet->message)) {
                                $msg_response->message_text = $responseGet->message;
                                $msg_response->error_message_text = json_encode($responseGet->errors);
                            }else{
                                $msg_response->message_text = "Unknown Error";
                                $msg_response->error_message_text = "Unknown Error";
                            }
                        }

                        $msg_response->save();

                    }

                    if ($this->post['mob_no'] != '') {

                        $mob_no = $this->post['mob_no'];

                        $data = [];
                        $data['phone_number'] = $mob_no;
                        $data['template_name'] = $template->template_name;
                        $data['template_language'] = $template->template_language ?? "en_US";

                        for ($i=0; $i < count($field_variable); $i++) {
                            if ($assgn_variable[$i] == '[Others]') {
                                $data[$field_variable[$i]] = $metatemplatedata['header_file_path'];
                            }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                                // $data[$field_variable[$i]] = $unsubscribe_url;
                                $data[$field_variable[$i]] = "";
                            }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                                // $data[$field_variable[$i]] = $dynamichaturl;
                                $data[$field_variable[$i]] = "";
                            }elseif ($assgn_variable[$i] == '[Document Name]') {
                                $data[$field_variable[$i]] = $template->document_name;
                            }else{
                                $data[$field_variable[$i]] = $finalAssignVar[$i];
                                // $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                            }
                        }



                        $data['contact'] =  [
                            'first_name' => $this->post['cand_name'],
                            'last_name' => " ",
                            "email" => $this->post['email'],
                            "country" => $this->post['country'],
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
                        $msg_response->template_for = "leads_candidate";
                        $msg_response->autometanotification_id = $metanotification->id;
                        $msg_response->metatemplate_id = $metanotification->metatemp_id;
                        $msg_response->metaapi_id = $metaapi_id;
                        $msg_response->allcontact_id = $this->post['id'];
                        $msg_response->mobile_no = $mob_no;
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

                    }

                }

            }

        }
        // Lead Source From Qamr HR means Leads From Employer
        if ($this->post['lead_source'] == "Qamr HR") {
            $metanotification = Autometanotification::where('template_for','=','leads_employer')->where('status',1)->first();

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
                    $otherMetApi = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['leads_employer'])->first();

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

                if ($base_url != "" && $vendor_id != "" && $access_token != "") {
                    $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';
                    $token = "Authorization: Bearer ".$access_token;

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


                    $remStr = [
                        "[Company]",
                        "[Full Name]",
                        "[Email]",
                        "[Country]",
                        "[Mobile No]",
                        "[Whatsapp No]"
                    ];

                    $repStr = [
                        $this->post['company_name'],
                        $this->post['cand_name'],
                        $this->post['email'],
                        $this->post['country'],
                        $this->post['mob_no'],
                        $this->post['whatsapp_no']
                    ];


                    $finalAssignVar = str_replace($remStr,$repStr,$assgn_variable);
                    $rem2 = ["[","]"];
                    $rep2 = ["",""];

                    if ($this->post['whatsapp_no'] != '') {

                        $whatsapp_no = $this->post['whatsapp_no'];

                        $data = [];
                        $data['phone_number'] = $whatsapp_no;
                        $data['template_name'] = $template->template_name;
                        $data['template_language'] = $template->template_language ?? "en_US";

                        for ($i=0; $i < count($field_variable); $i++) {
                            if ($assgn_variable[$i] == '[Others]') {
                                $data[$field_variable[$i]] = $metatemplatedata['header_file_path'];
                            }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                                // $data[$field_variable[$i]] = $unsubscribe_url;
                                $data[$field_variable[$i]] = "";
                            }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                                // $data[$field_variable[$i]] = $dynamichaturl;
                                $data[$field_variable[$i]] = "";
                            }elseif ($assgn_variable[$i] == '[Document Name]') {
                                $data[$field_variable[$i]] = $template->document_name;
                            }else{
                                $data[$field_variable[$i]] = $finalAssignVar[$i];
                                // $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                            }
                        }



                        $data['contact'] =  [
                            'first_name' => $this->post['cand_name'],
                            'last_name' => " ",
                            "email" => $this->post['email'],
                            "country" => $this->post['country'],
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
                        $msg_response->template_for = "leads_employer";
                        $msg_response->autometanotification_id = $metanotification->id;
                        $msg_response->metatemplate_id = $metanotification->metatemp_id;
                        $msg_response->metaapi_id = $metaapi_id;
                        $msg_response->allcontact_id = $this->post['id'];
                        $msg_response->mobile_no = $whatsapp_no;
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

                    }

                    if ($this->post['mob_no'] != '') {

                        $mob_no = $this->post['mob_no'];

                        $data = [];
                        $data['phone_number'] = $mob_no;
                        $data['template_name'] = $template->template_name;
                        $data['template_language'] = $template->template_language ?? "en_US";

                        for ($i=0; $i < count($field_variable); $i++) {
                            if ($assgn_variable[$i] == '[Others]') {
                                $data[$field_variable[$i]] = $metatemplatedata['header_file_path'];
                            }elseif ($assgn_variable[$i] == '[Dynamic Unsubscribe URL]') {
                                // $data[$field_variable[$i]] = $unsubscribe_url;
                                $data[$field_variable[$i]] = "";
                            }elseif ($assgn_variable[$i] == '[Whatsapp Chat Dynamic URL]') {
                                // $data[$field_variable[$i]] = $dynamichaturl;
                                $data[$field_variable[$i]] = "";
                            }elseif ($assgn_variable[$i] == '[Document Name]') {
                                $data[$field_variable[$i]] = $template->document_name;
                            }else{
                                $data[$field_variable[$i]] = $finalAssignVar[$i];
                                // $data[$field_variable[$i]] = str_replace($rem2,$rep2,$finalAssignVar[$i]);
                            }
                        }



                        $data['contact'] =  [
                            'first_name' => $this->post['cand_name'],
                            'last_name' => " ",
                            "email" => $this->post['email'],
                            "country" => $this->post['country'],
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
                        $msg_response->template_for = "leads_employer";
                        $msg_response->autometanotification_id = $metanotification->id;
                        $msg_response->metatemplate_id = $metanotification->metatemp_id;
                        $msg_response->metaapi_id = $metaapi_id;
                        $msg_response->allcontact_id = $this->post['id'];
                        $msg_response->mobile_no = $mob_no;
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

                    }

                }

            }
        }
    }
}

