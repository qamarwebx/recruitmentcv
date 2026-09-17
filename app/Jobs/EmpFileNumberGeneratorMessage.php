<?php

namespace App\Jobs;

use App\AdminModel\SubmissionPlace;
use App\Mail\EmpFileNumberGenerateMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EmpFileNumberGeneratorMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $emp,$getAPI,$party;

    public function __construct($emp,$getAPI,$party)
    {
        $this->emp = $emp;
        $this->getAPI = $getAPI;
        $this->party = $party;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->emp['pty_id'] != 200) {

            $visa_qty = explode(",",$this->emp['openings']);
            $final_qty = array_sum($visa_qty);
            
            // Submission Place
            $sub_place = SubmissionPlace::where('sub_pl_id','=',$this->emp['sub_place'])->first();

            $dataNew = [
                'submisPlace' => $sub_place->sub_pl_name,
                'visQty' => $final_qty
            ];

            // Send Mail
            if ($this->party['pty_email'] != '') {
                Mail::to($this->party['pty_email'])->send(new EmpFileNumberGenerateMail($this->emp,$this->party,$dataNew));
            }
            
            if ($this->party['sec_email'] != '') {
                Mail::to($this->party['sec_email'])->send(new EmpFileNumberGenerateMail($this->emp,$this->party,$dataNew));
            }
            // Send Whatsapp   
            $ins = $this->getAPI['instance_key'];
            $api = $this->getAPI['api_key'];
            $txt_url = $this->getAPI['text_message_url'];

            $msgbody = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n\nWe hope this message finds you well.\n\nWe're delighted to inform you that your applicant visa details have been recorded in our records, and a unique file number ".$this->emp['emp_file_no']." has been generated for your reference.\n\n1.File Number: ".$this->emp['emp_file_no']."\n2.Sponsor's Name: ".$this->emp['spon_nm_arab']."\n3.Visa Number: ".$this->emp['emp_visa_no']."\n4.Visa quantity: ".$final_qty."\n5.Visa Submission Location: ".$sub_place->sub_pl_name."\n\nThank you for choosing us for visa processing needs. We will keep you updated on the progress of your applicant.\n\nWarm regards,\nKhursheed Khan / Juned Khan\n*Qamr International*\nVisa and Immigration services";

            if($this->party['pty_comp_contact'] != ''){
                $send_url = $txt_url."?number=".$this->party['pty_comp_contact']."&type=text&message=".urlencode($msgbody)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$send_url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            if($this->party['pty_contact_no'] != ''){
                $send_url = $txt_url."?number=".$this->party['pty_contact_no']."&type=text&message=".urlencode($msgbody)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$send_url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            if($this->party['p_mobile'] != ''){
                $send_url = $txt_url."?number=".$this->party['p_mobile']."&type=text&message=".urlencode($msgbody)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$send_url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }
        }
    }
}
