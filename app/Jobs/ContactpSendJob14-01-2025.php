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
use Str;

class ContactpSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $ontactp,$getAPI,$post;

    public function __construct($ontactp,$getAPI,$post)
    {
        $this->ontactp = $ontactp;
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

        $tempfileurl = $this->post['temp_file_url'];

        // Get Country
        $getCountry = Country::find($this->ontactp['country_id']);
        if ($getCountry) {
            $countryName = $getCountry->name;
        } else {
            $countryName = '';
        }
        // Get City
        $getCity = City::find($this->ontactp['city_id']);
        if ($getCity) {
            $cityName = $getCity->name;
        } else {
            $cityName = '';
        }

        $random_string = Str::random(32);

        $unsubscribe_url = url('/whatsapp/unsubscribe/request/'.$random_string.'/'.$this->ontactp['id']);

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
            $this->ontactp['office_eng_name'],
            $this->ontactp['office_ar_name'],
            $countryName,
            $cityName,
            $this->ontactp['prim_concern_name'],
            $this->ontactp['prim_contact'],
            $this->ontactp['prim_email'],
            $this->ontactp['sec_concern_name'],
            $this->ontactp['sec_contact'],
            $this->ontactp['sec_email'],
            $this->ontactp['concern_name3'],
            $this->ontactp['contact3'],
            $this->ontactp['concern_name4'],
            $this->ontactp['contact4'],
            $this->ontactp['concern_name5'],
            $this->ontactp['contact5'],
            $this->ontactp['concern_name6'],
            $this->ontactp['contact6'],
            $this->ontactp['status'],
            $unsubscribe_url,
            $this->ontactp['office_eng_name'],

        ];

        $finalMsgBody = str_replace($remStr,$repStr,$this->post['whs_msg']);
        $finalMsgBodyAr = str_replace($remStr,$repStr,$this->post['whs_msg_ar']);

        $contacttypes = explode(",",$this->post['contactp_contact_type']);

        if ($this->post['temp_file'] != '') {

            foreach ($contacttypes as $contacttype) {
                // Send Message as pe contact type
                if (($contacttype == '1' || $contacttype == '2') && $this->ontactp['owner_contact'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['owner_contact']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['owner_contact']."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }
                }

                if (($contacttype == '1' || $contacttype == '3') && $this->ontactp['prim_contact'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['prim_contact']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['prim_contact']."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }
                }

                if (($contacttype == '1' || $contacttype == '4') && $this->ontactp['sec_contact'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['sec_contact']."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['sec_contact']."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

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

            foreach ($contacttypes as $contacttype) {
                // Send Message as pe contact type
                if (($contacttype == '1' || $contacttype == '2') && $this->ontactp['owner_contact'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['owner_contact']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['owner_contact']."&type=text&message=".urlencode($finalMsgBodyAr)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }
                }

                if (($contacttype == '1' || $contacttype == '3') && $this->ontactp['prim_contact'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['prim_contact']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['prim_contact']."&type=text&message=".urlencode($finalMsgBodyAr)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }
                }

                if (($contacttype == '1' || $contacttype == '4') && $this->ontactp['sec_contact'] != '') {
                    if ($this->post['whs_msg'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['sec_contact']."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);

                        echo $result;
                        curl_close($ch);
                    }

                    if ($this->post['whs_msg_ar'] != '') {
                        $url = $txt_url."?number=".$this->ontactp['sec_contact']."&type=text&message=".urlencode($finalMsgBodyAr)."&instance_id=".$ins."&access_token=".$api;

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
