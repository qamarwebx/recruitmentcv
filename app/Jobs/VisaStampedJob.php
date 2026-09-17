<?php

namespace App\Jobs;

use App\Mail\VisaCopyNotification;
use App\Mail\VisaCopyNotification2;
use App\Mail\VisaCopyNotification3;
use App\Mail\VisaNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class VisaStampedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $post;
    protected $party;
    protected $user;
    protected $cand_d;
    protected $visa_d;
    protected $getAPI;
    public function __construct($post,$party,$user,$cand_d,$visa_d,$getAPI)
    {
        $this->post = $post;
        $this->party = $party;
        $this->user = $user;
        $this->cand_d = $cand_d;
        $this->visa_d = $visa_d;
        $this->getAPI = $getAPI;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get API List Details
        $ins = $this->getAPI['instance_key'];
        $api = $this->getAPI['api_key'];

        $txt_url = $this->getAPI['text_message_url']; // https://qamr.in/api/send.php?

        // Store API Details
        // $ins = '05fc4dc2935608fb72db8dacba987eada2c6b94bf667fe77f7bbad994de02f03';
        // $api = "fe3b062f9606a7e66202d776b1e471f10bd16ae0becc1622a01370bfdbd439e1";
        // $url_path = 'http://crm.qamrintl.com';
        // $url_path =  "http://crm.qamr.in";
        $url_path = url('/');
        $file_path2 = 'status-images/visa_copy';
        $url4 = "http://whatsapi.smsinsta.com/api/send-media";
		$url5 = "http://whatsapi.smsinsta.com/api/send-text";

        $cand_name = $this->cand_d['cand_fname'].' '.$this->cand_d['cand_mname'].' '.$this->cand_d['cand_lname'];
        
        
        if($this->post['file'] != ''){
            $whsbody = "Dear ".$this->party['pty_full_name']."\n".ucfirst($this->party['pty_ag_name'])."\n\nCongratulations!\n\n".$cand_name." bearing ".$this->cand_d['cand_passport_no']." visa has been successfully stamped from Saudi Consulate on ".date('d-m-Y',strtotime($this->post['musaned_date']))."whose visa number is ".$this->visa_d['emp_visa_no'].".\n\nबधाई हो!\n\n".$cand_name." जिसका पासपोर्ट नंबर ".$this->cand_d['cand_passport_no']." सऊदी कंसुलेट से ".$this->post['musaned_date']." को सफलतापूर्वक वीजा स्टाम्प होगया है जिसका वीजा नंबर ".$this->visa_d['emp_visa_no']." है\n\n";
        }else{
            $whsbody = "Dear ".$this->party['pty_full_name']."\n".ucfirst($this->party['pty_ag_name'])."\n\nCongratulations!\n\n".$cand_name." bearing ".$this->cand_d['cand_passport_no']." visa has been successfully stamped from Saudi Consulate on ".date('d-m-Y',strtotime($this->post['musaned_date']))."whose visa number is ".$this->visa_d['emp_visa_no']."\n\nPlease wait for visa copy it will be sent to you by tomorrow as soon as passport is received from Saudi consulate\n\nबधाई हो!\n\n".$cand_name." जिसका पासपोर्ट नंबर ".$this->cand_d['cand_passport_no']." सऊदी कंसुलेट से ".$this->post['musaned_date']." को सफलतापूर्वक वीजा स्टाम्प होगया है जिसका वीजा नंबर ".$this->visa_d['emp_visa_no']." है\n\nकृपया वीज़ा कॉपी के लिए प्रतीक्षा करें, जैसे ही सऊदी कंसुलेट से पासपोर्ट प्राप्त होगा, आपको वीजा कॉपी भेज दी जाएगी";
        }
        // $whsbody2 = "Dear ".$this->user['name']."\n".$this->party['pty_ag_name']."\nThe Candidate ".$cand_name." bearing passport No. ".$this->cand_d['cand_passport_no']." with Visa number ".$this->visa_d['emp_visa_no']." has been stamped successfully from Saudi Consulate on ".date("d-m-Y",strtotime($this->post['musaned_date'])).".";


        // $media = $url_path.'/'.$file_path2.'/'.$this->post['file'];
        $media = url('/status-images/visa_copy/'.$this->post['file']);
        $type = $this->post['file_ext'];

        $phone1 = $this->party['pty_comp_contact'];
        $phone2 = $this->party['pty_contact_no'];
        $phone3 = $this->user['mobile'];


        if($this->post['file'] != ''){
            // Send message and whatsapp on party
            if($this->party['pty_id'] != '200'){
                if($this->party['pty_comp_contact'] != ''){
                    $url = $txt_url."?number=".$this->party['pty_comp_contact']."&type=media&message=".urlencode($whsbody)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins."&access_token=".$api;
            
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);
                    
                    echo $result;
                    curl_close($ch);
                }

                if($this->party['pty_contact_no'] != ''){
                    $url = $txt_url."?number=".$this->party['pty_contact_no']."&type=media&message=".urlencode($whsbody)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins."&access_token=".$api;
            
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);
            
                    echo $result;
                    curl_close($ch);
                }

                if($this->party['pty_email'] != ''){
                    Mail::to($this->party['pty_email'])->send(new VisaCopyNotification($this->post,$this->party,$this->user,$this->cand_d,$this->visa_d));
                }

                if($this->party['sec_email'] != ''){
                    Mail::to($this->party['sec_email'])->send(new VisaCopyNotification2($this->post,$this->party,$this->user,$this->cand_d,$this->visa_d));
                }



            }

            if($this->user['user_id'] != '200 '){
                if($this->user['mobile'] != ''){
                    $url = $txt_url."?number=".$this->user['mobile']."&type=media&message=".urlencode($whsbody)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins."&access_token=".$api;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);
            
                    echo $result;
                    curl_close($ch);
                }

                if($this->user['email'] != ''){
                    Mail::to($this->user['email'])->send(new VisaCopyNotification3($this->post,$this->party,$this->user,$this->cand_d,$this->visa_d));
                }
            }
        }else{
            if($this->party['pty_id'] != '200'){

                if($this->party['pty_comp_contact'] != ''){
                    $url = $txt_url."?number=".$this->party['pty_comp_contact']."&type=text&message=".urlencode($whsbody)."&instance_id=".$ins."&access_token=".$api;
            
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);
                    
                    echo $result;
                    curl_close($ch);
                }

                if($this->party['pty_contact_no'] != ''){
                    $url = $txt_url."?number=".$this->party['pty_contact_no']."&type=text&message=".urlencode($whsbody)."&instance_id=".$ins."&access_token=".$api;
            
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);
            
                    echo $result;
                    curl_close($ch);
                }

                if($this->party['pty_email'] != '' && $this->party['sec_email'] != ''){
                    Mail::to($this->party['pty_email'])->cc($this->party['sec_email'])->send(new VisaCopyNotification($this->post,$this->party,$this->user,$this->cand_d,$this->visa_d));
                }

                if($this->party['pty_email'] != '' && $this->party['sec_email'] != ''){
                    Mail::to($this->party['pty_email'])->send(new VisaCopyNotification2($this->post,$this->party,$this->user,$this->cand_d,$this->visa_d));
                }

            }

            if($this->user['user_id'] != '200 '){

                if($this->user['mobile'] != ''){
                    $url = $txt_url."?number=".$this->user['mobile']."&type=text&message=".urlencode($whsbody)."&instance_id=".$ins."&access_token=".$api;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);
            
                    echo $result;
                    curl_close($ch);
                }

                if($this->user['email'] != ''){
                    Mail::to($this->user['email'])->send(new VisaCopyNotification3($this->post,$this->party,$this->user,$this->cand_d,$this->visa_d));
                }
            }
        }


    }
}
