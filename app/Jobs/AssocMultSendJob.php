<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\City;
use App\Models\Country;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use function PHPUnit\Framework\fileExists;

class AssocMultSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $assoc,$getAPI,$post,$template_list;

    public function __construct($assoc,$getAPI,$post,$template_list)
    {
        $this->assoc = $assoc;
        $this->getAPI = $getAPI;
        $this->post = $post;
        $this->template_list = $template_list;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ins = $this->getAPI['instance_id'];
        $api = $this->getAPI['access_token'];
        $txt_url = $this->getAPI['api_url'];

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

        $remStr = [
            "[Name]",
            "[Agency Name]",
            "[Email]",
            "[Primary Mobile]",
            "[Secondary Mobile]",
            "[Careoff]",
            "[Address]",
            "[City]",
            "[Country]"
        ];
        $repStr = [
            $this->assoc['pty_full_name'],
            $this->assoc['pty_ag_name'],
            $this->assoc['pty_email'],
            $this->assoc['pty_mobile'],
            $this->assoc['sec_mob_no'],
            $careoffName,
            $this->assoc['address'],
            $cityName,
            $countryName
        ];

        $finalMsgBody = str_replace($remStr,$repStr,$this->template_list['msg_whatsapp']);
        $finalMsgBodyAr = str_replace($remStr,$repStr,$this->template_list['msg_whatsapp_ar']);

        if ($this->template_list['file'] != '') {
            // if (isset($basepathstatus) && $basepathstatus->base_path_status == 1) {
            //     $filepath = base_path('public/admin/assets/images/template/'.$this->template_list['file']);
            // } else {
            //     $filepath = base_path('public_html/admin/assets/images/template/'.$this->template_list['file']);
            // }

            // $filepath = url('admin/assets/images/template/'.$this->template_list['file']);

            $filepath = config('app.url').'/admin/assets/images/template/'.$this->template_list['file'];

            if (fileExists($filepath)) {
                $filename = $this->template_list['file'];
                $fullpath_url = $filepath;
            } else {
                $filename = "";
                $fullpath_url = "";
            }

        } else {
            $filename = "";
            $fullpath_url = "";
        }

        if ($filename != '') {
            if ($this->post['assoc_contact_type'] == '1' || $this->post['assoc_contact_type'] == '2') {
                if ($this->assoc['pty_mobile'] != '') {
                    if ($this->template_list['msg_whatsapp'] != '') {
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

                    if ($this->template_list['msg_whatsapp_ar'] != '') {
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
                    if ($this->template_list['msg_whatsapp'] != '') {
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

                    if ($this->template_list['msg_whatsapp_ar'] != '') {
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
        }else{
            if ($this->post['assoc_contact_type'] == '1' || $this->post['assoc_contact_type'] == '2') {
                if ($this->assoc['pty_mobile'] != '') {
                    if ($this->template_list['msg_whatsapp'] != '') {
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

                    if ($this->template_list['msg_whatsapp_ar'] != '') {
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

                    if ($this->template_list['msg_whatsapp'] != '') {
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

                    if ($this->template_list['msg_whatsapp_ar'] != '') {
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
