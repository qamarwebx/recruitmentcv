<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\Country;
use App\Models\Sendwhatsappresponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Str;

class BulkAllContactSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $contactdet,$post,$getAPI;

    public function __construct($contactdet,$post,$getAPI)
    {
        $this->contactdet = $contactdet;
        $this->post = $post;
        $this->getAPI = $getAPI;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->post['for_whatsapp'] == 'normal_whatsapp') {
            // API Details
            $desiredConnection = 'database';
            $ins = $this->getAPI['instance_id'];
            $api = $this->getAPI['access_token'];
            $txt_url = $this->getAPI['api_url'];

            // $urlPath = url('/');

            // $filePath = 'admin/assets/images/template';
            $fileName = $this->post['template_file'];
            // $mediaFullpath = $urlPath.'/'.$filePath.'/'.$fileName;
            $mediaFullpath = url('/admin/assets/images/template/'.$fileName);
            // $mediaFullpath = url('/admin/assets/images/template/download.jpg');

            $media_url = $this->post['file_path_url'];

            // Get Country
            $getCountry = Country::find($this->contactdet['country_id']);
            if ($getCountry) {
                $countryName = $getCountry->name;
            } else {
                $countryName = '';
            }
            // Get City
            $getCity = City::find($this->contactdet['city_id']);
            if ($getCity) {
                $cityName = $getCity->name;
            } else {
                $cityName = '';
            }

            $random_string = Str::random(32);

            $unsubscribe_url = 'whatsapp/unsubscribe/request/'.$random_string.'/'.$this->contactdet['id'];

            $remStr = [
                "[Business Type]",
                "[Full Name]",
                "[Mobile No]",
                "[Email]",
                "[Country]",
                "[City]",
                "[Phone0]",
                "[Email0]",
                "[Phone1]",
                "[Email1]",
                "[Phone2]",
                "[Email2]",
                "[Unsubscribe]",
                "[Company]"
            ];



            $repStr = [
                $this->contactdet['lead_type'],
                $this->contactdet['full_name'],
                $this->contactdet['primary_no_wsp'],
                $this->contactdet['email'],
                $countryName,
                $cityName,
                $this->contactdet['mobile_no1_wsp'],
                $this->contactdet['email0'],
                $this->contactdet['mobile_no2_wsp'],
                $this->contactdet['email1'],
                $this->contactdet['mobile_no3_wsp'],
                $this->contactdet['email2'],
                $unsubscribe_url,
                $this->contactdet['company_name']

            ];

            $contacttypes = explode(",",$this->post['contact_type']);

            $finalMsgBody = str_replace($remStr,$repStr,$this->post['msg_body_temp']);
            // $finalMsgBodyAr = str_replace($remStr,$repStr,$this->post['whs_msg_ar']);



            if ($fileName != '') {
                foreach ($contacttypes as $contacttype) {
                    if (($contacttype == 'all' || $contacttype == 'owner') && $this->contactdet['secondary_no_wsp'] != '') {

                        $primary_contact_no = $this->contactdet['secondary_no_wsp_dial_code'] != '' ? $this->contactdet['secondary_no_wsp_dial_code'].''.$this->contactdet['secondary_no_wsp']  : $this->contactdet['secondary_no_wsp'];

                        $data_send = [
                            'number' => $primary_contact_no,
                            'type' => 'media',
                            'message' => $finalMsgBody,
                            'media_url' => $media_url,
                            'filename' => $fileName,
                            'instance_id' => $ins,
                            'access_token' => $api
                        ];



                        // $url = $txt_url."?number=".$this->contactdet['primary_contact_no']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        // $ch = curl_init();
                        // curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        // curl_setopt($ch,CURLOPT_URL,$url);
                        // $result =  curl_exec($ch);
                        // curl_close($ch);

                        $ch = curl_init();

                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                            CURLOPT_URL => $txt_url,
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
                        $respost = new Sendwhatsappresponse();
                        $respost->allcontactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['full_name'];
                        $respost->mobile_no = $this->contactdet['secondary_no_wsp'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                // $respost->message_text = json_encode($normalResult->message);
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

                    if (($contacttype == 'all' || $contacttype == 'Primary') && $this->contactdet['primary_no_wsp'] != '') {
                        // $url = $txt_url."?number=".$this->contactdet['mobile_no']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".urlencode($mediaFullpath)."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        // $url = $txt_url."?number=".$this->contactdet['mobile_no']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        // $ch = curl_init();
                        // curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        // curl_setopt($ch,CURLOPT_URL,$url);
                        // $result =  curl_exec($ch);
                        // curl_close($ch);

                        $mobile_no = $this->contactdet['primary_no_wsp_dial_code'] != '' ? $this->contactdet['primary_no_wsp_dial_code'].''.$this->contactdet['primary_no_wsp']  : $this->contactdet['primary_no_wsp'];


                        $data_send = [
                            'number' => $mobile_no,
                            'type' => 'media',
                            'message' => $finalMsgBody,
                            'media_url' => $media_url,
                            'filename' => $fileName,
                            'instance_id' => $ins,
                            'access_token' => $api
                        ];

                        $ch = curl_init();

                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json','Accept: application/json'],
                            CURLOPT_URL => $txt_url,
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
                        $respost = new Sendwhatsappresponse();
                        $respost->allcontactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['prim_concern_name'];
                        $respost->mobile_no = $this->contactdet['primary_no_wsp'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                                // $respost->message_text = json_encode($normalResult->message);
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


                    if (($contacttype == 'all' || $contacttype == 'Secondary') && $this->contactdet['mobile_no1_wsp'] != '') {
                        // $url = $txt_url."?number=".$this->contactdet['phone0']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".urlencode($mediaFullpath)."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        // $ch = curl_init();
                        // curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        // curl_setopt($ch,CURLOPT_URL,$url);
                        // $result =  curl_exec($ch);
                        // curl_close($ch);

                        $phone0 = $this->contactdet['mobile_no1_wsp_dial_code'] != '' ? $this->contactdet['mobile_no1_wsp_dial_code'].''.$this->contactdet['mobile_no1_wsp']  : $this->contactdet['mobile_no1_wsp'];


                        $data_send = [
                            'number' => $phone0,
                            'type' => 'media',
                            'message' => $finalMsgBody,
                            'media_url' => $media_url,
                            'filename' => $fileName,
                            'instance_id' => $ins,
                            'access_token' => $api
                        ];

                        $ch = curl_init();

                        curl_setopt_array($ch,[
                            CURLOPT_HTTPHEADER => ['Content-Type: application/json','Accept: application/json'],
                            CURLOPT_URL => $txt_url,
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
                        $respost = new Sendwhatsappresponse();
                        $respost->allcontactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['sec_concern_name'];
                        $respost->mobile_no = $this->contactdet['mobile_no1_wsp'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                                // $respost->message_text = json_encode($normalResult->message);
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
                foreach ($contacttypes as $contacttype) {
                    if (($contacttype == 'all' || $contacttype == 'owner') && $this->contactdet['secondary_no_wsp'] != '') {

                        $primary_contact_no = $this->contactdet['secondary_no_wsp_dial_code'] != '' ? $this->contactdet['secondary_no_wsp_dial_code'].''.$this->contactdet['secondary_no_wsp']  : $this->contactdet['secondary_no_wsp'];

                        $url = $txt_url."?number=".$primary_contact_no."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        curl_close($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);
                        $respost = new Sendwhatsappresponse();
                        $respost->allcontactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['full_name'];
                        $respost->mobile_no = $this->contactdet['secondary_no_wsp'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = "Message has been send!";
                                // $respost->message_text = json_encode($normalResult->message);
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

                    if (($contacttype == 'all' || $contacttype == 'Primary') && $this->contactdet['primary_no_wsp'] != '') {

                        $mobile_no = $this->contactdet['primary_no_wsp_dial_code'] != '' ? $this->contactdet['primary_no_wsp_dial_code'].''.$this->contactdet['primary_no_wsp']  : $this->contactdet['primary_no_wsp'];

                        $url = $txt_url."?number=".$mobile_no."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        curl_close($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);
                        $respost = new Sendwhatsappresponse();
                        $respost->allcontactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['prim_concern_name'];
                        $respost->mobile_no = $this->contactdet['primary_no_wsp'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                // $respost->message_text = json_encode($normalResult->message);
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

                    if (($contacttype == 'all' || $contacttype == 'Secondary') && $this->contactdet['mobile_no1_wsp'] != '') {

                        $phone0 = $this->contactdet['mobile_no1_wsp_dial_code'] != '' ? $this->contactdet['mobile_no1_wsp_dial_code'].''.$this->contactdet['mobile_no1_wsp']  : $this->contactdet['mobile_no1_wsp'];


                        $url = $txt_url."?number=".$phone0."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        curl_close($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);
                        $respost = new Sendwhatsappresponse();
                        $respost->allcontactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['sec_concern_name'];
                        $respost->mobile_no = $this->contactdet['mobile_no1_wsp'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                // $respost->message_text = json_encode($normalResult->message);
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



        } else {

        }
    }
}
