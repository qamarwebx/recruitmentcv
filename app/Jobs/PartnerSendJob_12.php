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

class PartnerSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $partner,$getAPI,$post;

    public function __construct($partner,$getAPI,$post)
    {
        $this->partner = $partner;
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
        $desiredConnection = 'database';
        $ins = $this->getAPI['instance_id'];
        $api = $this->getAPI['access_token'];
        $txt_url = $this->getAPI['api_url'];

        $urlPath = url('/');
        $filePath = 'admin/assets/images/template';
        $fileName = $this->post['temp_file'];
        $mediaFullpath = $urlPath.'/'.$filePath.'/'.$fileName;

    
        // Get Country
        $getCountry = Country::find($this->partner['country_id']);
        if ($getCountry) {
            $countryName = $getCountry->name;
        } else {
            $countryName = '';
        }
        // Get City
        $getCity = City::find($this->partner['city_id']);
        if ($getCity) {
            $cityName = $getCity->name;
        } else {
            $cityName = '';
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
            $this->partner['mobile_no'],
            $cityName,
            $countryName,
            $this->partner['primary_email'],
            $this->partner['secondary_email'],
            $this->partner['office_no'],
            $this->partner['primary_mob'],
            $this->partner['secondary_mob']
        ];

        $finalMsgBody = str_replace($remStr,$repStr,$this->post['whs_msg']);
        $finalMsgBodyAr = str_replace($remStr,$repStr,$this->post['whs_msg_ar']);
        if ($this->post['temp_file'] != '') {
            // Send Message as pe contact type
            if ($this->post['partner_contact_type'] == '1' || $this->post['partner_contact_type'] == '2') {
                if ($this->partner['mobile_no'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->partner['mobile_no']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }
                    
                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->partner['mobile_no']."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

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
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->partner['primary_mob']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }
                    
                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->partner['primary_mob']."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

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
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->partner['secondary_mob']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }
                    
                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->partner['secondary_mob']."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$mediaFullpath."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }
                }
            }

        } else {
            // Send Message as pe contact type
            if ($this->post['partner_contact_type'] == '1' || $this->post['partner_contact_type'] == '2') {
                if ($this->partner['mobile_no'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->partner['mobile_no']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }
                    
                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->partner['mobile_no']."&type=text&message=".urlencode($finalMsgBodyAr)."&instance_id=".$ins."&access_token=".$api;

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
                    
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->partner['primary_mob']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }
                    
                    if ($this->post['whs_msg_ar'] != '') {
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
                    
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->partner['secondary_mob']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }
                    
                    if ($this->post['whs_msg_ar'] != '') {
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
