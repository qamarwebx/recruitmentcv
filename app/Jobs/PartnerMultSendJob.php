<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\Country;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use function PHPUnit\Framework\fileExists;

class PartnerMultSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $partner,$getAPI,$post,$template_list;

    public function __construct($partner,$getAPI,$post,$template_list)
    {
        $this->partner = $partner;
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

        $country = Country::find($this->partner['country_id']);
        $city = City::find($this->partner['city_id']);

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

        $remStr = [
            "[Recruitement Office Name (English)]",
            "[Recruitment Office Name (Arabic)]",
            "[Owner Name ]",
            "[Username]",
            "[Owner Contact Number]",
            "[City]",
            "[Country]",
            "[Primary Email]",
            "[Secondary Email]",
            "[Office Number]",
            "[Primary Mobile]",
            "[Secondary Mobile]"
        ];

        $repStr = [
            $this->partner['rec_off_name'],
            $this->partner['rec_office_arname'],
            $this->partner['owner_name'],
            $this->partner['username'],
            $this->partner['owner_mobile_no'],
            $city_name,
            $country_name,
            $this->partner['primary_email'],
            $this->partner['secondary_email'],
            $this->partner['office_no'],
            $this->partner['primary_mob'],
            $this->partner['secondary_mob']
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
            if ($this->post['partner_contact_type'] == '1' || $this->post['partner_contact_type'] == '2') {
                if ($this->partner['owner_mobile_no'] != '') {
                    if ($this->template_list['msg_whatsapp'] != '') {
                        $url = $txt_url."?number=".$this->partner['owner_mobile_no']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->template_list['msg_whatsapp_ar'] != '') {
                        $url = $txt_url."?number=".$this->partner['owner_mobile_no']."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }
                }
            }

            if ($this->post['partner_contact_type'] == '1' || $this->post['partner_contact_type'] == '3') {
                if ($this->partner['primary_mob'] != '') {
                    if ($this->template_list['msg_whatsapp'] != '') {
                        $url = $txt_url."?number=".$this->partner['primary_mob']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->template_list['msg_whatsapp_ar'] != '') {
                        $url = $txt_url."?number=".$this->partner['primary_mob']."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }
                }
            }

            if ($this->post['partner_contact_type'] == '1' || $this->post['partner_contact_type'] == '4') {
                if ($this->partner['secondary_mob'] != '') {
                    if ($this->template_list['msg_whatsapp'] != '') {
                        $url = $txt_url."?number=".$this->partner['secondary_mob']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->template_list['msg_whatsapp_ar'] != '') {
                        $url = $txt_url."?number=".$this->partner['secondary_mob']."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }
                }
            }
        }else{
            if ($this->post['partner_contact_type'] == '1' || $this->post['partner_contact_type'] == '2') {
                if ($this->partner['owner_mobile_no'] != '') {
                    if ($this->template_list['msg_whatsapp'] != '') {
                        $url = $txt_url."?number=".$this->partner['owner_mobile_no']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->template_list['msg_whatsapp_ar'] != '') {
                        $url = $txt_url."?number=".$this->partner['owner_mobile_no']."&type=text&message=".urlencode($finalMsgBodyAr)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }
                }
            }

            if ($this->post['partner_contact_type'] == '1' || $this->post['partner_contact_type'] == '3') {
                if ($this->partner['primary_mob'] != '') {

                    if ($this->template_list['msg_whatsapp'] != '') {
                        $url = $txt_url."?number=".$this->partner['primary_mob']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->template_list['msg_whatsapp_ar'] != '') {
                        $url = $txt_url."?number=".$this->partner['primary_mob']."&type=text&message=".urlencode($finalMsgBodyAr)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }
                }
            }

            if ($this->post['partner_contact_type'] == '1' || $this->post['partner_contact_type'] == '4') {
                if ($this->partner['secondary_mob'] != '') {

                    if ($this->template_list['msg_whatsapp'] != '') {
                        $url = $txt_url."?number=".$this->partner['secondary_mob']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->template_list['msg_whatsapp_ar'] != '') {
                        $url = $txt_url."?number=".$this->partner['secondary_mob']."&type=text&message=".urlencode($finalMsgBodyAr)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }
                }
            }
        }


    }
}
