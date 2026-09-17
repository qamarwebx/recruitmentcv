<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\AdminModel\Partywakalacard;

class PtyMulSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $tempDet,$party,$getAPI;
    public function __construct($tempDet,$party,$getAPI)
    {
        $this->tempDet = $tempDet;
        $this->party = $party;
        $this->getAPI = $getAPI;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // API Details
        $ins = $this->getAPI['instance_key'];
        $api = $this->getAPI['api_key'];

        $txt_url = $this->getAPI['text_message_url']; // https://qamr.in/api/send.php?

        // $urlPath = 'http://crm.qamrintl.com';
        // $urlPath =  "http://crm.qamr.in";
        $urlPath = url('/');
        $filePath = 'image/whatsapp';
        $fileName = $this->tempDet['file'];
        // $fileName = '5.jpg';
        $type = "image";

        $repstr = ["[Party Name]","[Agency Name]","[City]","[State]","[ID]","[Email]","[Primary Contact]","[Secondary Contact]","[DOB]","[Wakala Card]","[Membership]"];
        $repstr = [$this->party['pty_full_name'],$this->party['pty_ag_name'],$this->party['city'],$this->party['state'],$this->party['pty_id'],$this->party['pty_email'],$this->party['pty_comp_contact'],$this->party['pty_contact_no'],$this->party['pty_dob'],"",$this->party['member_id']];

        $finalmsgbody = str_replace($repstr,$repstr,$this->tempDet['msgBody']);

        $phone1 = $this->party['pty_comp_contact'];
        $phone2 = $this->party['pty_contact_no'];
        $phone3 = $this->party['p_mobile'];


        if($this->tempDet['file'] != ''){
            // $media = $urlPath.'/'.$filePath.'/'.$fileName;
            $media = url('/image/whatsapp/'.$fileName);

            // Send Message on First Number
            if($this->party['pty_comp_contact'] != '' || $this->party['pty_comp_contact'] != '--None--'){
                
                // $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($whsMesg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($finalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
                
            }
            // Send Message on Second Number
            if($this->party['pty_contact_no'] != '' || $this->party['pty_contact_no'] != '--None--'){
                // $url = $txt_url."?number=".$phone2."&type=media&message=".urlencode($whsMesg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                
                $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($finalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }
            // Send Message on Third Number
            if($this->party['p_mobile'] !='' || $this->party['p_mobile'] != '--None--'){
                
                // $url = $txt_url."?number=".$phone3."&type=media&message=".urlencode($whsMesg)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
                
                $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($finalmsgbody)."&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;

                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }
        }else{
            // Send Message on First Number
            if($this->party['pty_comp_contact'] != '' || $this->party['pty_comp_contact'] != '--None--'){
                
                // $url = $txt_url."?number=".$phone1."&type=text&message=".urlencode($whsMesg)."&instance_id=".$ins."&access_token=".$api;

                $url = $txt_url."?number=".$phone1."&type=text&message=".urlencode($finalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }
            // Send Message on Second Number
            if($this->party['pty_contact_no'] != '' || $this->party['pty_contact_no'] != '--None--'){
                
                $url = $txt_url."?number=".$phone2."&type=text&message=".urlencode($finalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result = curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }
            // Send Message on Third Number
            if($this->party['p_mobile'] != '' || $this->party['p_mobile'] != '--None--'){
                
                $url = $txt_url."?number=".$phone3."&type=text&message=".urlencode($finalmsgbody)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
                
            }
        }


        // Check wakala card status
        if ($this->tempDet['wakala_st'] == 1) {
            $getwak = Partywakalacard::where('pty_id','=',$this->party['pty_id'])->first();
            $filepathw = "image/service-master";
            // $media = $urlPath.'/'.$filepathw.'/'.$getwak->file;
            $media = url('/image/service-master/'.$getwak->file);

            // Send Message on First Number
            if($this->party['pty_comp_contact'] != '' || $this->party['pty_comp_contact'] != '--None--'){
                    
                $url = $txt_url."?number=".$phone1."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }
            // Send Message on Second Number
            if($this->party['pty_contact_no'] != '' || $this->party['pty_contact_no'] != '--None--'){
                $url = $txt_url."?number=".$phone2."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }
            // Send Message on Third Number
            if($this->party['p_mobile'] !='' || $this->party['p_mobile'] != '--None--'){
                
                $url = $txt_url."?number=".$phone3."&type=media&message=&media_url=".$media."&filename=".$fileName."&instance_id=".$ins."&access_token=".$api;
            
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
