<?php

namespace App\Jobs;

use App\Models\Country;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ClientSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $client2,$getAPI,$post;

    public function __construct($client2,$getAPI,$post)
    {
        $this->client2 = $client2;
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
        $tempfileurl = $this->post['temp_file_url'];

        // Get Country
        $getCountry = DB::table('users as user')
            // ->leftJoin('userprofiles as userpr','userpr.user_id','=','user.id')
            ->leftJoin('countries as country','country.id','=','user.country_id')
            ->leftJoin('cities as city','city.id','=','user.city_id')
            ->where('user.id','=',$this->client2['id'])
            ->select('user.*','country.name as contname','city.name as cityname')
            ->first();
        if ($getCountry) {
            $countryName = $getCountry->contname;
            $cityName = $getCountry->cityname;
            $mobno = $getCountry->mobile_no;
        } else {
            $countryName = '';
            $cityName = '';
            $mobno = '';
        }

        if ($this->client2['status'] == '1') {
            $clientStatus = 'Verified';
        } else {
            $clientStatus = 'Not Verified';
        }


        $remStr = ["[Name]","[Email]","[Mobile]","[Country]","[City]","[Status]"];
        $repStr = [$this->client2['name'],$this->client2['email'],$mobno,$countryName,$cityName,$clientStatus];

        $finalMsgBody = str_replace($remStr,$repStr,$this->post['whs_msg']);
        $finalMsgBodyAr = str_replace($remStr,$repStr,$this->post['whs_msg_ar']);

        if ($this->post['temp_file'] != '') {
            // Send Message as pe contact type

            if ($mobno != '') {
                if ($this->post['whs_msg'] != '') {
                    $url = $txt_url."?number=".$mobno."&type=media&message=".urlencode($finalMsgBody)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);

                    echo $result;
                    curl_close($ch);
                }

                if ($this->post['whs_msg_ar'] != '') {
                    $url = $txt_url."?number=".$mobno."&type=media&message=".urlencode($finalMsgBodyAr)."&media_url=".$tempfileurl."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);

                    echo $result;
                    curl_close($ch);
                }
            }


        } else {
            // Send Message as pe contact type

            if ($mobno != '') {
                if ($this->post['whs_msg'] != '') {
                    $url = $txt_url."?number=".$mobno."&type=text&message=".urlencode($finalMsgBody)."&instance_id=".$ins."&access_token=".$api;

                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);

                    echo $result;
                    curl_close($ch);
                }

                if ($this->post['whs_msg_ar'] != '') {
                    $url = $txt_url."?number=".$mobno."&type=text&message=".urlencode($finalMsgBodyAr)."&instance_id=".$ins."&access_token=".$api;

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
