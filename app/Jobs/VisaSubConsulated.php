<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\AdminModel\Userwhatsappapi;
use Illuminate\Support\Facades\Mail;
use App\Mail\VisaSubmittedConsulated;

class VisaSubConsulated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $post,$party,$cand_det,$visa_d;

    public function __construct($post,$party,$cand_det,$visa_d)
    {
        $this->post = $post;
        $this->party = $party;
        $this->cand_det = $cand_det;
        $this->visa_d = $visa_d;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get Whatsapp API
        $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=','1')->first();

        $ins = $getAPI->instance_key;
        $api = $getAPI->api_key;
        $txt_url = $getAPI->text_message_url; // https://qamr.in/api/send.php?

        if($this->cand_det['cand_mname'] != ''){
            $cand_name = $this->cand_det['cand_fname'].' '.$this->cand_det['cand_mname'].' '.$this->cand_det['cand_lname'];
        }else{
            $cand_name = $this->cand_det['cand_fname'].' '.$this->cand_det['cand_lname'];
        }

        $whsbody = "Dear ".$this->party['pty_full_name']."\n".ucfirst($this->party['pty_ag_name'])."\n\n".$cand_name." passport No. is. ".$this->cand_det['cand_passport_no']." This passport will be submitted to Saudi consulate on ".date("d-m-Y",strtotime($this->post['musaned_date']))." for visa stamping having visa number ".$this->visa_d['emp_visa_no']." Please review the details of the visa If there is any change in the above please contact us immediately.\n\n".$cand_name." पासपोर्ट नंबर ".$this->cand_det['cand_passport_no']." है। यह पासपोर्ट सऊदी कांसुलेट में ".date("d-m-Y",strtotime($this->post['musaned_date']))." को जमा किया जाएगा वीजा स्टम्पिंग के लिए जिस का वीजा नंबर है ".$this->visa_d['emp_visa_no']." कृपया वीजा के विवरण की समीक्षा करें यदि उपर्युक्त में कोई परिवर्तन होता है तो कृपया हमें तुरंत संपर्क करें।\n\nThank you";

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

            if($this->party['pty_email'] != ''){
                Mail::to($this->party['pty_email'])->send(new VisaSubmittedConsulated($this->post,$this->party,$this->cand_det,$this->visa_d));
            }

            if($this->party['sec_email'] != ''){
                Mail::to($this->party['sec_email'])->send(new VisaSubmittedConsulated($this->post,$this->party,$this->cand_det,$this->visa_d));
            }



        }
    }
}
