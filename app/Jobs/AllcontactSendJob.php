<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\Country;
use App\Models\Normalwhatsappcampaignresponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Str;

class AllcontactSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $allcontact,$getAPI,$post;

    public function __construct($allcontact,$getAPI,$post)
    {
        $this->allcontact = $allcontact;
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

        $country = Country::find($this->allcontact['country_id']);
        $city = City::find($this->allcontact['city_id']);

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
        $unsubscribe_url = 'whatsapp/unsubscribe/request/allcontact/'.$random_str.'/'.$this->allcontact['id'];

        $remStr = [
            "[Business Type]",
            "[Full Name]",
            "[Country]",
            "[City]",
            "[Email]",
            "[Phone0]",
            "[Email0]",
            "[Phone1]",
            "[Email1]",
            "[Phone2]",
            "[Email2]",
            "[Unsubscribe]",
            "[Company]",
            "[Membership]"
        ];

        $repStr = [
            $this->allcontact['lead_type'],
            $this->allcontact['full_name'],
            $country_name,
            $city_name,
            $this->allcontact['email'],
            $this->allcontact['mobile_no1_wsp'],
            $this->allcontact['email0'],
            $this->allcontact['mobile_no2_wsp'],
            $this->allcontact['email1'],
            $this->allcontact['mobile_no3_wsp'],
            $this->allcontact['email2'],
            $unsubscribe_url,
            $this->allcontact['company_name'],
            $this->allcontact['id']

        ];

        $finalMsgBody = str_replace($remStr,$repStr,$this->post['whs_msg']);
        $finalMsgBodyAr = str_replace($remStr,$repStr,$this->post['whs_msg_ar']);

        $contacttypes = explode(",",$this->post['allcontact_contact_type']);

        foreach ($contacttypes as $contacttype) {
            if ($filename != '') {
                if (($contacttype == '1' || $contacttype == '2') && $this->allcontact['secondary_no_wsp'] != '') {
                    $secondary_no_wsp = $this->allcontact['secondary_no_wsp_dial_code'] != '' ? $this->allcontact['secondary_no_wsp_dial_code'].''.$this->allcontact['secondary_no_wsp'] : $this->allcontact['secondary_no_wsp'];

                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $secondary_no_wsp,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $secondary_no_wsp;
                        $respost->allcontact_id = $this->allcontact['id'];
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
                            'number' => $secondary_no_wsp,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $secondary_no_wsp;
                        $respost->allcontact_id = $this->allcontact['id'];
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


                if (($contacttype == '1' || $contacttype == '3') && $this->allcontact['primary_no_wsp'] != '') {
                    $secondary_contact_no = $this->allcontact['primary_no_wsp_dial_code'] != '' ? $this->allcontact['primary_no_wsp_dial_code'].''.$this->allcontact['primary_no_wsp'] : $this->allcontact['primary_no_wsp'];

                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $secondary_contact_no,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $secondary_contact_no;
                        $respost->allcontact_id = $this->allcontact['id'];
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
                            'number' => $secondary_contact_no,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $secondary_contact_no;
                        $respost->allcontact_id = $this->allcontact['id'];
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


                if (($contacttype == '1' || $contacttype == '4') && $this->allcontact['mobile_no1_wsp'] != '') {
                    $mobile_no1_wsp_no = $this->allcontact['mobile_no1_wsp_dial_code'] != '' ? $this->allcontact['mobile_no1_wsp_dial_code'].''.$this->allcontact['mobile_no1_wsp'] : $this->allcontact['mobile_no1_wsp'];


                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $mobile_no1_wsp_no,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $mobile_no1_wsp_no;
                        $respost->allcontact_id = $this->allcontact['id'];
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
                            'number' => $mobile_no1_wsp_no,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $mobile_no1_wsp_no;
                        $respost->allcontact_id = $this->allcontact['id'];
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

            }else{

                if (($contacttype == '1' || $contacttype == '2') && $this->allcontact['secondary_no_wsp'] != '') {
                    $secondary_no_wsp = $this->allcontact['secondary_no_wsp_dial_code'] != '' ? $this->allcontact['secondary_no_wsp_dial_code'].''.$this->allcontact['secondary_no_wsp'] : $this->allcontact['secondary_no_wsp'];

                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $secondary_no_wsp,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $secondary_no_wsp;
                        $respost->allcontact_id = $this->allcontact['id'];
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
                            'number' => $secondary_no_wsp,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $secondary_no_wsp;
                        $respost->allcontact_id = $this->allcontact['id'];
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


                if (($contacttype == '1' || $contacttype == '3') && $this->allcontact['primary_no_wsp'] != '') {
                    $secondary_contact_no = $this->allcontact['primary_no_wsp_dial_code'] != '' ? $this->allcontact['primary_no_wsp_dial_code'].''.$this->allcontact['primary_no_wsp'] : $this->allcontact['primary_no_wsp'];

                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $secondary_contact_no,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $secondary_contact_no;
                        $respost->allcontact_id = $this->allcontact['id'];
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
                            'number' => $secondary_contact_no,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $secondary_contact_no;
                        $respost->allcontact_id = $this->allcontact['id'];
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


                if (($contacttype == '1' || $contacttype == '4') && $this->allcontact['mobile_no1_wsp'] != '') {
                    $mobile_no1_wsp_no = $this->allcontact['mobile_no1_wsp_dial_code'] != '' ? $this->allcontact['mobile_no1_wsp_dial_code'].''.$this->allcontact['mobile_no1_wsp'] : $this->allcontact['mobile_no1_wsp'];


                    if ($this->post['whs_msg'] != '') {
                        $data_send = [
                            'number' => $mobile_no1_wsp_no,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $mobile_no1_wsp_no;
                        $respost->allcontact_id = $this->allcontact['id'];
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
                            'number' => $mobile_no1_wsp_no,
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
                        $respost->name = $this->allcontact['full_name'];
                        $respost->mobile_no = $mobile_no1_wsp_no;
                        $respost->allcontact_id = $this->allcontact['id'];
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
