<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\AdminModel\Userwhatsappapi;
use App\Mail\SinglePartyCompaignListM;
use Illuminate\Support\Facades\Mail;

class SinglePartyCampaignList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $post,$partyD,$getAPI;

    public function __construct($post,$partyD,$getAPI)
    {
        $this->post = $post;
        $this->partyD = $partyD;
        $this->getAPI = $getAPI;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->post['msg_type'] == 'Whatsapp'){
            // Get API as per user
            // $getAPI = Userwhatsappapi::where('user_id','=',$this->post['user_id'])->where('status','=',1)->first();
            $ins = $this->getAPI['instance_key'];
            $api = $this->getAPI['api_key'];
            $txt_url = $this->getAPI['text_message_url'];

            $whmsg = "Dear ".$this->partyD['pty_full_name']."\n".$this->partyD['pty_ag_name']."\n\n".$this->post['msgText'];

            if($this->post['file'] != ''){
                // $url_path = 'http://crm.qamrintl.com';
                // $url_path =  "http://crm.qamr.in";
                $url_path = url('/');
                $file_path2 = 'image/whatsapp';
                // $media = $url_path.'/'.$file_path2.'/'.$this->post['file'];
                $media = url('/image/whatsapp/'.$this->post['file']);

                if($this->partyD['pty_comp_contact'] != ''){
                    $url = $txt_url."?number=".$this->partyD['pty_comp_contact']."&type=media&message=".urlencode($whmsg)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins."&access_token=".$api;
                
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);
                    
                    echo $result;
                    curl_close($ch);
                }

                if($this->partyD['pty_contact_no'] != ''){
                    $url2 = $txt_url."?number=".$this->partyD['pty_contact_no']."&type=media&message=".urlencode($whmsg)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins."&access_token=".$api;
                
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url2);
                    $result =  curl_exec($ch);
                    
                    echo $result;
                    curl_close($ch);
                }

            }else{

                if($this->partyD['pty_comp_contact'] != ''){
                    $url = $txt_url."?number=".$this->partyD['pty_comp_contact']."&type=text&message=".urlencode($whmsg)."&instance_id=".$ins."&access_token=".$api;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result = curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

                if($this->partyD['pty_contact_no'] != ''){
                    $url = $txt_url."?number=".$this->partyD['pty_contact_no']."&type=text&message=".urlencode($whmsg)."&instance_id=".$ins."&access_token=".$api;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result = curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

            }
        }elseif($this->post['msg_type'] == 'Mail'){

            if ($this->partyD['pty_email'] != '') {
                Mail::to($this->partyD['pty_email'])->send(new SinglePartyCompaignListM($this->post,$this->partyD,$this->getAPI));
            }

            if ($this->partyD['sec_email'] != '') {
                Mail::to($this->partyD['sec_email'])->send(new SinglePartyCompaignListM($this->post,$this->partyD,$this->getAPI));
            }

        }elseif($this->post['msg_type'] == 'Message'){

        }
    }
}
