<?php

namespace App\Jobs;

use App\Mail\MofaNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class MofaPaidJob implements ShouldQueue
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
    protected $pass_data;
    protected $getAPI;

    public function __construct($post,$cand_d,$party,$user,$pass_data,$getAPI)
    {
        $this->post = $post;
        $this->party = $party;
        $this->user = $user;
        $this->cand_d = $cand_d;
        $this->pass_data = $pass_data;
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
        $ins_id = $this->getAPI['instance_key'];
        $access_token = $this->getAPI['api_key'];
        $txt_url = $this->getAPI['text_message_url']; // https://qamr.in/api/send.php?
        
        // Store API Details
        // $ins = '05fc4dc2935608fb72db8dacba987eada2c6b94bf667fe77f7bbad994de02f03';
        // $api = "fe3b062f9606a7e66202d776b1e471f10bd16ae0becc1622a01370bfdbd439e1";

		// $url5 = "http://whatsapi.smsinsta.com/api/send-text";

        if($this->cand_d['cand_mname'] != ''){
            $cand_name = $this->cand_d['cand_fname'].' '.$this->cand_d['cand_mname'].' '.$this->cand_d['cand_lname'];
        }else{
            $cand_name = $this->cand_d['cand_fname'].' '.$this->cand_d['cand_lname'];
        }


        

        $whsbody = "Dear ".$this->user['name']."\n".$this->party['pty_full_name']."\nThe MOFA No. ".$this->pass_data['mofa_no']." has been successfully generated of the Candidate. ".$cand_name." bearing Passport No. ".$this->cand_d['cand_passport_no'];

        $whsbody2 = "Dear ".$this->party['pty_full_name']."\n".ucfirst($this->party['pty_ag_name'])."\nThe MOFA No. ".$this->pass_data['mofa_no']." has been successfully generated of the Candidate. ".$cand_name." bearing Passport No. ".$this->cand_d['cand_passport_no'];

        $phone1 = $this->party['pty_comp_contact'];
        $phone2 = $this->party['pty_contact_no'];
        $phone3 = $this->user['mobile'];


        if($this->party['pty_comp_contact'] != '' || $this->party['pty_comp_contact'] != '--None--'){
            

            $url = $txt_url."?number=".$phone1."&type=text&message=".urlencode($whsbody2)."&instance_id=".$ins_id."&access_token=".$access_token;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$url);
            $result =  curl_exec($ch);
            echo $result;
            curl_close($ch);
        }

        if($this->party['pty_contact_no'] != '' || $this->party['pty_contact_no'] != '--None--'){
            
            

            $url = $txt_url."?number=".$phone2."&type=text&message=".urlencode($whsbody2)."&instance_id=".$ins_id."&access_token=".$access_token;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$url);
            $result = curl_exec($ch);
            echo $result;
            curl_close($ch);
        }


        if($this->user['mobile'] != '' || $this->user['mobile'] != '--None--'){
            

            $url = $txt_url."?number=".$phone3."&type=text&message=".urlencode($whsbody2)."&instance_id=".$ins_id."&access_token=".$access_token;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$url);
            $result = curl_exec($ch);
            echo $result;
            curl_close($ch);
        }


    }
}
