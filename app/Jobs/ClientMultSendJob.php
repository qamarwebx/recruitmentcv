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

use function PHPUnit\Framework\fileExists;

class ClientMultSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $client,$getAPI,$post,$template_list;

    public function __construct($client,$getAPI,$post,$template_list)
    {
        $this->client = $client;
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
        // Get API Data
        $api_endpoint = $this->getAPI['api_url'];
        $instance_id = $this->getAPI['instance_id'];
        $access_token = $this->getAPI['access_token'];

        $country = Country::find($this->client['country_id']);
        $city = City::find($this->client['city_id']);

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

        if ($this->client['status'] == '1') {
            $clientStatus = 'Verified';
        } else {
            $clientStatus = 'Not Verified';
        }

        $remStr = ["[Name]","[Email]","[Mobile]","[Country]","[City]","[Status]"];
        $repStr = [$this->client['name'],$this->client['email'],$this->client['mobile_no'],$country_name,$city_name,$clientStatus];

        $finalMsgBody = str_replace($remStr,$repStr,$this->template_list['msg_whatsapp']);
        $finalMsgBodyAr = str_replace($remStr,$repStr,$this->template_list['msg_whatsapp_ar']);

        // Get Template Data
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
                $filename = '';
                $fullpath_url = "";
            }

        } else {
            $filename = "";
            $fullpath_url = "";
        }

        if ($filename != '') {
            if ($this->client['mobile_no'] != '') {
                if ($this->template_list['msg_whatsapp'] != '') {
                    $data_send = [
                        'number' => $this->client['mobile_no'],
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
                    $respost->name = $this->client['name'];
                    $respost->mobile_no = $this->client['mobile_no'];
                    $respost->contactp_id = $this->client['id'];
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

                if ($this->template_list['msg_whatsapp_ar'] != '') {
                    $data_send = [
                        'number' => $this->client['mobile_no'],
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
                    $respost->name = $this->client['name'];
                    $respost->mobile_no = $this->client['mobile_no'];
                    $respost->contactp_id = $this->client['id'];
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
            if ($this->client['mobile_no'] != '') {
                if ($this->template_list['msg_whatsapp'] != '') {
                    $data_send = [
                        'number' => $this->client['mobile_no'],
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
                    $respost->name = $this->client['name'];
                    $respost->mobile_no = $this->client['mobile_no'];
                    $respost->contactp_id = $this->client['id'];
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

                if ($this->template_list['msg_whatsapp_ar'] != '') {
                    $data_send = [
                        'number' => $this->client['mobile_no'],
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
                    $respost->name = $this->client['name'];
                    $respost->mobile_no = $this->client['mobile_no'];
                    $respost->contactp_id = $this->client['id'];
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
