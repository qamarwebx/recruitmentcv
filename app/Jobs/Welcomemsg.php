<?php

namespace App\Jobs;

use App\AdminModel\WhatsappTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Welcomemsg implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $conts,$getWelAPI;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($conts,$getWelAPI)
    {
        $this->conts = $conts;
        $this->getWelAPI = $conts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ins = $this->getWelAPI['instance_key'];
        $api = $this->getWelAPI['api_key'];
        $txt_url = $this->getWelAPI['text_message_url'];

        $phone1 = $this->conts['mobile_no'];
        $phone2 = $this->conts['phone0'];
        $phone3 = $this->conts['phone1'];
        $phone4 = $this->conts['phone2'];

        $posts = WhatsappTemplate::where('campaign_for','=','25AC')->where('status','=',1)->get();
        if ($posts->count() > 0) {
            foreach ($posts as $post) {
                $remStr = ["[Business Type]","[Full Name]","[Mobile No]","[Email]","[ID]","[Country]","[City]","[Source]","[Job Title]","[Agency Name]","[Company Name]","[Industry]","[Created Date]"];
                $repStr = [$post->lead_type,$post->full_name,$post->mobile_no,$post->email,$post->id,$post->country_id,$post->city,$post->source,$post->job_title,$post->office_name,$post->company_name,$post->indust_id,$post->created_at];
                $finalmsgbody = str_replace($remStr,$repStr,$post->msgBody);

                if ($post->file != '') {
                    $fileName = $post->file;
                    $media = url('/image/whatsapp/'.$fileName);

                    if ($phone1 != '') {
                        $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($finalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }

                    if ($phone2 != '') {
                        $url = $txt_url."?number=".$phone2."&type=media&message=".urlencode($finalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }

                    if ($phone3 != '') {
                        $url = $txt_url."?number=".$phone3."&type=media&message=".urlencode($finalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }

                    if ($phone4 != '') {
                        $url = $txt_url."?number=".$phone4."&type=media&message=".urlencode($finalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }

                } else {
                    if ($phone1 != '') {
                        $url = $txt_url."?number=".$phone1."&type=text&message=".urlencode($finalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }
                    if ($phone2 != '') {
                        $url = $txt_url."?number=".$phone2."&type=text&message=".urlencode($finalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }
                    if ($phone3 != '') {
                        $url = $txt_url."?number=".$phone3."&type=text&message=".urlencode($finalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        
                        echo $result;
                        curl_close($ch);
                    }
                    if ($phone4 != '') {
                        $url = $txt_url."?number=".$phone4."&type=text&message=".urlencode($finalmsgbody)."&instance_id=".$ins."&access_token=".$api;
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
