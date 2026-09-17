<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\AdminModel\BranchDetails;
use Illuminate\Support\Facades\Mail;
use App\Mail\CourierDeliveratOffceMail;

class CourierDeliveratOffce implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $courierC,$getAPI,$getPrty;

    public function __construct($courierC,$getAPI,$getPrty)
    {
        $this->courierC = $courierC;
        $this->getAPI = $getAPI;
        $this->getPrty = $getPrty;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->getPrty['pty_id'] != 200){
            // Get Branch Name
            $branch = BranchDetails::where('br_id','=',$this->courierC['courier_from'])->first();

            // Document List Start
            $iw = 1;
            if ($this->courierC['valid_pass'] == 1) {
                $list_doc = "- ".$iw." Valid Passport\n";
                $iw++;
            }

            if ($this->courierC['old_pass'] == 1) {
                $list_doc .= "- ".$iw." Old Passport\n";
                $iw++;
            }

            if ($this->courierC['driving_lic'] == 1) {
                $list_doc .= "- ".$iw." Driving License\n";
                $iw++;
            }
            if ($this->courierC['exit_paper'] == 1) {
                $list_doc .= "- ".$iw." Exit Paper\n";
                $iw++;
            }
            if ($this->courierC['photo_r'] == 1) {
                $list_doc .= "- ".$iw." Photo\n";
                $iw++;
            }
            if ($this->courierC['med_rep'] == 1) {
                $list_doc .= "- ".$iw." Medical Report\n";
                $iw++;
            }
            if ($this->courierC['medical_token'] == 1) {
                $list_doc .= "- ".$iw." Medical Token\n";
                $iw++;
            }

            // Document List End


            // Send Mail
            if($this->getPrty['pty_email'] != ''){
                Mail::to($this->getPrty['pty_email'])->send(new CourierDeliveratOffceMail($this->courierC,$this->getPrty,$branch));
            }

            if($this->getPrty['sec_email'] != ''){
                Mail::to($this->getPrty['sec_email'])->send(new CourierDeliveratOffceMail($this->courierC,$this->getPrty,$branch));
            }

            // Send Whatsapp
            $ins = $this->getAPI['instance_key'];
            $api = $this->getAPI['api_key'];
            $txt_url = $this->getAPI['text_message_url'];

            $msgbody = "Dear ".$this->getPrty['pty_full_name']."\n".$this->getPrty['pty_ag_name']." We are pleased to inform you that the documents of ".$this->courierC['cand_fname']." ".$this->courierC['cand_lname']." and ".$this->courierC['pass_no']." were successfully delivered to ".$this->courierC['rec_name_dao']." at ".$branch->br_name." on ".date("d-m-Y h:i",strtotime($this->courierC['updated_at'])).", The contents of the delivered documents include the following:\n\n".$list_doc."We kindly request that you take a moment to ensure the accuracy of the documents and regard this reminder as a formal confirmation of the successful delivery.\n\nBest regards,\n";

            if($this->getPrty['pty_comp_contact'] != ''){
                $send_url = $txt_url."?number=".$this->getPrty['pty_comp_contact']."&type=text&message=".urlencode($msgbody)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$send_url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            if($this->getPrty['pty_contact_no'] != ''){
                $send_url = $txt_url."?number=".$this->getPrty['pty_contact_no']."&type=text&message=".urlencode($msgbody)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$send_url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            if($this->getPrty['p_mobile'] != ''){
                $send_url = $txt_url."?number=".$this->getPrty['p_mobile']."&type=text&message=".urlencode($msgbody)."&instance_id=".$ins."&access_token=".$api;
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
