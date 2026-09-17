<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\City;
use App\Models\Country;
use App\Models\Whatslinemessagestatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssocSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $assoc,$getAPI,$post;

    public function __construct($assoc,$getAPI,$post)
    {
        $this->assoc = $assoc;
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
        // API Details

        $ins = $this->getAPI['instance_id'];
        $api = $this->getAPI['access_token'];
        $txt_url = $this->getAPI['api_url'];

        $urlPath = url('/');
        $filePath = 'admin/assets/images/template';
        $fileName = $this->post['temp_file'];
        $mediaFullpath = $urlPath.'/'.$filePath.'/'.$fileName;

        // Get Careoff
        $getCaroff = Admin::find($this->assoc['admin_id']);
        if ($getCaroff) {
            $careoffName = $getCaroff->name;
        } else {
            $careoffName = '';
        }
        // Get Country
        $getCountry = Country::find($this->assoc['country_id']);
        if ($getCountry) {
            $countryName = $getCountry->name;
        } else {
            $countryName = '';
        }
        // Get City
        $getCity = City::find($this->assoc['city_id']);
        if ($getCity) {
            $cityName = $getCity->name;
        } else {
            $cityName = '';
        }

        $remStr = ["[Name]","[Agency Name]","[Email]","[Primary Mobile]","[Secondary Mobile]","[Careoff]","[Address]","[City]","[Country]"];
        $repStr = [$this->assoc['pty_full_name'],$this->assoc['pty_ag_name'],$this->assoc['pty_email'],$this->assoc['pty_mobile'],$this->assoc['sec_mob_no'],$careoffName,$this->assoc['address'],$cityName,$countryName];

        $finalMsgBody = str_replace($remStr,$repStr,$this->post['whs_msg']);
        $finalMsgBodyAr = str_replace($remStr,$repStr,$this->post['whs_msg_ar']);

        if ($this->post['temp_file'] != '') {
            // Send Message as pe contact type
            if ($this->post['assoc_contact_type'] == '1' || $this->post['assoc_contact_type'] == '2') {
                if ($this->assoc['pty_mobile'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->assoc['pty_mobile']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        // echo $result;

                        // $result_data = json_decode($result);

                        // if ($result_data->status == 'success') {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->mobile_no = $this->assoc['pty_mobile'];
                        //     $new_msg_status->message_text = $finalMsgBody;
                        //     $new_msg_status->message_time = $result_data->message->messageTimestamp;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // } else {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->error_msg = $result_data->message;
                        //     $new_msg_status->mobile_no = $this->assoc['pty_mobile'];
                        //     $new_msg_status->message_text = $finalMsgBody;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // }
                        
                        curl_close($ch);
                    }
                    
                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->assoc['pty_mobile']."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        // $result_data = json_decode($result);

                        // if ($result_data->status == 'success') {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->mobile_no = $this->assoc['pty_mobile'];
                        //     $new_msg_status->message_text = $finalMsgBodyAr;
                        //     $new_msg_status->message_time = $result_data->message->messageTimestamp;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // } else {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->error_msg = $result_data->message;
                        //     $new_msg_status->mobile_no = $this->assoc['pty_mobile'];
                        //     $new_msg_status->message_text = $finalMsgBodyAr;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // }

                        // echo $result;
                        curl_close($ch);
                    }
                }
            }

            if ($this->post['assoc_contact_type'] == '1' || $this->post['assoc_contact_type'] == '3') {
                if ($this->assoc['sec_mob_no'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->assoc['sec_mob_no']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        // $result_data = json_decode($result);

                        // if ($result_data->status == 'success') {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->mobile_no = $this->assoc['sec_mob_no'];
                        //     $new_msg_status->message_text = $finalMsgBody;
                        //     $new_msg_status->message_time = $result_data->message->messageTimestamp;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // } else {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->error_msg = $result_data->message;
                        //     $new_msg_status->mobile_no = $this->assoc['sec_mob_no'];
                        //     $new_msg_status->message_text = $finalMsgBody;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // }

                        // echo $result;
                        curl_close($ch);
                    }
                    
                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->assoc['sec_mob_no']."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        // echo $result;

                        // $result_data = json_decode($result);

                        // if ($result_data->status == 'success') {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->mobile_no = $this->assoc['sec_mob_no'];
                        //     $new_msg_status->message_text = $finalMsgBodyAr;
                        //     $new_msg_status->message_time = $result_data->message->messageTimestamp;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // } else {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->error_msg = $result_data->message;
                        //     $new_msg_status->mobile_no = $this->assoc['sec_mob_no'];
                        //     $new_msg_status->message_text = $finalMsgBodyAr;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // }

                        curl_close($ch);
                    }
                }
            }
        } else {
            // Send Message as pe contact type
            if ($this->post['assoc_contact_type'] == '1' || $this->post['assoc_contact_type'] == '2') {
                if ($this->assoc['pty_mobile'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->assoc['pty_mobile']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        // $result_data = json_decode($result);

                        // if ($result_data->status == 'success') {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->mobile_no = $this->assoc['pty_mobile'];
                        //     $new_msg_status->message_text = $finalMsgBody;
                        //     $new_msg_status->message_time = $result_data->message->messageTimestamp;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // } else {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->error_msg = $result_data->message;
                        //     $new_msg_status->mobile_no = $this->assoc['pty_mobile'];
                        //     $new_msg_status->message_text = $finalMsgBody;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // }
                        

                        // echo $result;
                        curl_close($ch);
                    }
                    
                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->assoc['pty_mobile']."&type=text&message=".urlencode($finalMsgBodyAr)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        // $result_data = json_decode($result);

                        // if ($result_data->status == 'success') {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->mobile_no = $this->assoc['pty_mobile'];
                        //     $new_msg_status->message_text = $finalMsgBodyAr;
                        //     $new_msg_status->message_time = $result_data->message->messageTimestamp;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // } else {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->error_msg = $result_data->message;
                        //     $new_msg_status->mobile_no = $this->assoc['pty_mobile'];
                        //     $new_msg_status->message_text = $finalMsgBodyAr;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // }

                        // echo $result;
                        curl_close($ch);
                    }
                }
            }

            if ($this->post['assoc_contact_type'] == '1' || $this->post['assoc_contact_type'] == '3') {
                if ($this->assoc['sec_mob_no'] != '') {

                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->assoc['sec_mob_no']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        // echo $result;
                        // $result_data = json_decode($result);

                        // if ($result_data->status == 'success') {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->mobile_no = $this->assoc['sec_mob_no'];
                        //     $new_msg_status->message_text = $finalMsgBody;
                        //     $new_msg_status->message_time = $result_data->message->messageTimestamp;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // } else {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->error_msg = $result_data->message;
                        //     $new_msg_status->mobile_no = $this->assoc['sec_mob_no'];
                        //     $new_msg_status->message_text = $finalMsgBody;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // }

                        curl_close($ch);
                    }
                    
                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->assoc['sec_mob_no']."&type=text&message=".urlencode($finalMsgBodyAr)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        // $result_data = json_decode($result);

                        // if ($result_data->status == 'success') {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->mobile_no = $this->assoc['sec_mob_no'];
                        //     $new_msg_status->message_text = $finalMsgBodyAr;
                        //     $new_msg_status->message_time = $result_data->message->messageTimestamp;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // } else {
                        //     $new_msg_status = new Whatslinemessagestatus();
                        //     $new_msg_status->message_status = $result_data->status;
                        //     $new_msg_status->error_msg = $result_data->message;
                        //     $new_msg_status->mobile_no = $this->assoc['sec_mob_no'];
                        //     $new_msg_status->message_text = $finalMsgBodyAr;
                        //     $new_msg_status->message_type = "Campaign Message";
                        //     $new_msg_status->save();
                        // }

                        // echo $result;
                        curl_close($ch);
                    }
                }
            }
        }
        
    }
}
