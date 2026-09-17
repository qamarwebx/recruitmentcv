<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\City;
use App\Models\Country;
use Str;
use App\Models\Sendwhatsappresponse;

class BulkContactSendJob implements ShouldQueue
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
            // $mediaFullpath = url('/admin/assets/images/template/'.$fileName);
            $mediaFullpath = $this->post['file_path_url'];
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
                $this->contactdet['office_eng_name'],
                $this->contactdet['office_ar_name'],
                $countryName,
                $cityName,
                $this->contactdet['prim_concern_name'],
                $this->contactdet['prim_contact'],
                $this->contactdet['prim_email'],
                $this->contactdet['sec_concern_name'],
                $this->contactdet['sec_contact'],
                $this->contactdet['sec_email'],
                $this->contactdet['concern_name3'],
                $this->contactdet['contact3'],
                $this->contactdet['concern_name4'],
                $this->contactdet['contact4'],
                $this->contactdet['concern_name5'],
                $this->contactdet['contact5'],
                $this->contactdet['concern_name6'],
                $this->contactdet['contact6'],
                $this->contactdet['status'],
                $unsubscribe_url,
                $this->contactdet['office_eng_name'],

            ];

            $contacttypes = explode(",",$this->post['contact_type']);

            $finalMsgBody = str_replace($remStr,$repStr,$this->post['msg_body_temp']);
            // $finalMsgBodyAr = str_replace($remStr,$repStr,$this->post['whs_msg_ar']);



            if ($fileName != '') {
                foreach ($contacttypes as $contacttype) {
                    if (($contacttype == 'all' || $contacttype == 'owner') && $this->contactdet['owner_contact'] != '') {
                        $url = $txt_url."?number=".$this->contactdet['owner_contact']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        curl_close($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);
                        $respost = new Sendwhatsappresponse();
                        $respost->contactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['owner_name'];
                        $respost->mobile_no = $this->contactdet['owner_contact'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = json_encode($normalResult->message);
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

                    if (($contacttype == 'all' || $contacttype == 'Primary') && $this->contactdet['prim_contact'] != '') {
                        $url = $txt_url."?number=".$this->contactdet['prim_contact']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        $url = $txt_url."?number=".$this->contactdet['prim_contact']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        curl_close($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);
                        $respost = new Sendwhatsappresponse();
                        $respost->contactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['prim_concern_name'];
                        $respost->mobile_no = $this->contactdet['prim_contact'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = json_encode($normalResult->message);
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

                    if (($contacttype == 'all' || $contacttype == 'Secondary') && $this->contactdet['sec_contact'] != '') {
                        $url = $txt_url."?number=".$this->contactdet['sec_contact']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        curl_close($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);
                        $respost = new Sendwhatsappresponse();
                        $respost->contactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['sec_concern_name'];
                        $respost->mobile_no = $this->contactdet['sec_contact'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = json_encode($normalResult->message);
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
                    if (($contacttype == 'all' || $contacttype == 'owner') && $this->contactdet['owner_contact'] != '') {
                        $url = $txt_url."?number=".$this->contactdet['owner_contact']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        curl_close($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);
                        $respost = new Sendwhatsappresponse();
                        $respost->contactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['owner_name'];
                        $respost->mobile_no = $this->contactdet['owner_contact'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = json_encode($normalResult->message);
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

                    if (($contacttype == 'all' || $contacttype == 'Primary') && $this->contactdet['prim_contact'] != '') {
                        $url = $txt_url."?number=".$this->contactdet['prim_contact']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        curl_close($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);
                        $respost = new Sendwhatsappresponse();
                        $respost->contactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['prim_concern_name'];
                        $respost->mobile_no = $this->contactdet['prim_contact'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = json_encode($normalResult->message);
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

                    if (($contacttype == 'all' || $contacttype == 'Secondary') && $this->contactdet['sec_contact'] != '') {
                        $url = $txt_url."?number=".$this->contactdet['sec_contact']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        curl_close($ch);
                        // Store Whatsapp Response
                        $normalResult = json_decode($result);
                        $respost = new Sendwhatsappresponse();
                        $respost->contactsend_id = $this->post['id'];
                        $respost->name = $this->contactdet['sec_concern_name'];
                        $respost->mobile_no = $this->contactdet['sec_contact'];
                        if(isset($normalResult)){
                            if ($normalResult->status == "error") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = $normalResult->message;
                            }elseif ($normalResult->status == "success") {
                                $respost->message_status = $normalResult->status;
                                $respost->message_text = json_encode($normalResult->message);
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
