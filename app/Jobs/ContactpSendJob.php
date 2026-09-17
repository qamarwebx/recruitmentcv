<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\Country;
use App\Models\Normalwhatsappcampaignresponse;
use App\Models\Sendwhatsappresponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Str;

class ContactpSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $contactp,$getAPI,$post;

    public function __construct($contactp,$getAPI,$post)
    {
        $this->contactp = $contactp;
        $this->getAPI = $getAPI;
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get API Data
        $api_endpoint = $this->getAPI['api_url'];
        $instance_id = $this->getAPI['instance_id'];
        $access_token = $this->getAPI['access_token'];

        // Campaign List Data For Send Message
        $filename = $this->post['temp_file'];
        $fullpath_url = $this->post['temp_file_url'];

        $country = Country::find($this->contactp['country_id']);
        $city = City::find($this->contactp['city_id']);

        if (isset($country)) {
            $country_name = $country->name;
        } else {
            $country_name = "";
        }

        if (isset($city)) {
            $city_name = $city->name;
        } else {
            $city_name = "";
        }

        // Unsubscribe Link for User
        $random_str = Str::random(32);
        $unsubscribe_url = 'whatsapp/unsubscribe/request/'.$random_str.'/'.$this->contactp['id'];


        $remStr = [
            "[Office Name (English)]",
            "[Office Name (Arabic)]",
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
            "[Unsubscribe]",
            "[Company]"
        ];
        $repStr = [
            $this->contactp['office_eng_name'],
            $this->contactp['office_ar_name'],
            $country_name,
            $city_name,
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
            $this->contactp['status'],
            $unsubscribe_url,
            $this->contactp['office_eng_name'],

        ];

        $finalMsgBody = str_replace($remStr,$repStr,$this->post['whs_msg']);
        $finalMsgBodyAr = str_replace($remStr,$repStr,$this->post['whs_msg_ar']);

        $contacttypes = explode(",",$this->post['contactp_contact_type']);

        foreach ($contacttypes as $contacttype) {
            if ($filename != '') {
                if (($contacttype == '1' || $contacttype == '2') && $this->contactp['owner_contact'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $this->contactp['owner_contact'],
                            'type' => 'media',
                            'message' => $finalMsgBody,
                            'media_url' => $fullpath_url,
                            'filename' => $filename,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['owner_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $data_send = [
                            'number' => $this->contactp['owner_contact'],
                            'type' => 'media',
                            'message' => $finalMsgBodyAr,
                            'media_url' => $fullpath_url,
                            'filename' => $filename,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['owner_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }
                }

                if (($contacttype == '1' || $contacttype == '3') && $this->contactp['prim_contact'] != '') {


                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $this->contactp['prim_contact'],
                            'type' => 'media',
                            'message' => $finalMsgBody,
                            'media_url' => $fullpath_url,
                            'filename' => $filename,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['prim_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $data_send = [
                            'number' => $this->contactp['prim_contact'],
                            'type' => 'media',
                            'message' => $finalMsgBodyAr,
                            'media_url' => $fullpath_url,
                            'filename' => $filename,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['prim_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }
                }

                if (($contacttype == '1' || $contacttype == '4') && $this->contactp['sec_contact'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $this->contactp['sec_contact'],
                            'type' => 'media',
                            'message' => $finalMsgBody,
                            'media_url' => $fullpath_url,
                            'filename' => $filename,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['sec_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $data_send = [
                            'number' => $this->contactp['sec_contact'],
                            'type' => 'media',
                            'message' => $finalMsgBodyAr,
                            'media_url' => $fullpath_url,
                            'filename' => $filename,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['sec_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }
                }
            } else {
                if (($contacttype == '1' || $contacttype == '2') && $this->contactp['owner_contact'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $this->contactp['owner_contact'],
                            'type' => 'text',
                            'message' => $finalMsgBody,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['owner_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $data_send = [
                            'number' => $this->contactp['owner_contact'],
                            'type' => 'text',
                            'message' => $finalMsgBodyAr,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['owner_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }
                }

                if (($contacttype == '1' || $contacttype == '3') && $this->contactp['prim_contact'] != '') {


                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $this->contactp['prim_contact'],
                            'type' => 'text',
                            'message' => $finalMsgBody,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['prim_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $data_send = [
                            'number' => $this->contactp['prim_contact'],
                            'type' => 'text',
                            'message' => $finalMsgBodyAr,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['prim_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }
                }

                if (($contacttype == '1' || $contacttype == '4') && $this->contactp['sec_contact'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $this->contactp['sec_contact'],
                            'type' => 'text',
                            'message' => $finalMsgBody,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['sec_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $data_send = [
                            'number' => $this->contactp['sec_contact'],
                            'type' => 'text',
                            'message' => $finalMsgBodyAr,
                            'instance_id' => $instance_id,
                            'access_token' => $access_token
                        ];

                        $ch = curl_init();
                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $api_endpoint,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => json_encode($data_send),
                        ]);

                        $result = curl_exec($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);

                        $respost = new Normalwhatsappcampaignresponse();
                        $respost->campaignlist_id = $this->post['id'];
                        $respost->name = $this->contactp['prim_concern_name'];
                        $respost->mobile_no = $this->contactp['sec_contact'];
                        $respost->contactp_id = $this->contactp['id'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                            }else{
                                $respost->message_status = "error";
                                $respost->message_text = "Message Not Send";
                            }
                        }else{
                            $respost->message_status = "error";
                            $respost->message_text = "Message Not Send";
                        }
                        $respost->save();
                    }
                }
            }
        }




    }
}
