<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendCandRecordMail;

class SendCandidateRecord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $candidate,$getAPI,$party;

    public function __construct($candidate,$getAPI,$party)
    {
        $this->candidate = $candidate;
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
        if($this->party['pty_id'] != 200){
            // Send Mail
            if($this->party['pty_email'] != ''){
                Mail::to($this->party['pty_email'])->send(new SendCandRecordMail($this->candidate,$this->party));
            }

            if($this->party['sec_email'] != ''){
                Mail::to($this->party['sec_email'])->send(new SendCandRecordMail($this->candidate,$this->party));
            }

            // Send Whatsapp
            // API Details
            $ins = $this->getAPI['instance_key'];
            $api = $this->getAPI['api_key'];
            $txt_url = $this->getAPI['text_message_url'];

            $msgbody = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n\n".$this->candidate['cand_fname']." ".$this->candidate['cand_lname']." and ".$this->candidate['cand_passport_no']." passport information has been recently updated within our system for MOFA and visa endorsement, also immigration process if required. Rest assured, we will keep you informed as soon as the process progresses further.\n\nThank you for choosing Qamar International. We look forward to serving you with excellence and dedication.\n\nWarm regards,\nKhursheed Khan / Juned Khan\n*Qamr International*\nVisa and Immigration services";

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
