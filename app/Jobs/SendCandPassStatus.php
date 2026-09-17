<?php

namespace App\Jobs;

use App\Mail\SendCandPassStatusP;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCandPassStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $candidate;
    protected $getAPI;
    protected $party;

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
        // Get API List Details and Send Mails
        $ins = $this->getAPI['instance_key'];
        $api = $this->getAPI['api_key'];

        $txt_url = $this->getAPI['text_message_url']; // https://qamr.in/api/send.php?

        // $url5 = "http://whatsapi.smsinsta.com/api/send-text";

        if($this->candidate['cand_pass_status'] == 1){
            $statusp = "Passport Copy";
        }else{
            $statusp = "Original Passport";
        }

        $phone1 = '91'.$this->party['pty_comp_contact'];
        $phone2 = '91'.$this->party['pty_contact_no'];
        $phone3 = '91'.$this->user['mobile'];

        $cand_name = $this->candidate['cand_fname'].' '.$this->candidate['cand_mname'].' '.$this->candidate['cand_lname'];
        $whsbody = "Dear ".$this->party['pty_full_name']."\n".ucfirst($this->party['pty_ag_name'])."\nThe Candidate ".$cand_name." bearing passport No.".$this->cand_sd['cand_passport_no']." successfully registered at our CRM and we received ".$statusp;
        if($this->party['pty_comp_contact'] != '' || $this->party['pty_comp_contact'] != '--None--'){
            // Send Text
            // $data41 = [
            //     "number" => '91'.$this->party['pty_comp_contact'],
            //     "msg" => $whsbody,
            //     // "media" => $media,
            //     // "type" => $type,
            //     "instance" => $ins,
            //     "apikey" => $api
            // ];
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data41));
            // curl_setopt($ch, CURLOPT_URL, $url5);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            // $result = curl_exec($ch);
            // curl_close($ch);

            $url = $txt_url."?number=".$phone1."&type=text&message=".urlencode($whsbody)."&instance_id=".$ins."&access_token=".$api;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$url);
            $result =  curl_exec($ch);
            
            echo $result;
            curl_close($ch);
        }

        if($this->party['pty_contact_no'] != '' || $this->party['pty_contact_no'] != '--None--'){
            // Send Text
            // $data41 = [
            //     "number" => '91'.$this->party['pty_contact_no'],
            //     "msg" => $whsbody,
            //     // "media" => $media,
            //     // "type" => $type,
            //     "instance" => $ins,
            //     "apikey" => $api
            // ];
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data41));
            // curl_setopt($ch, CURLOPT_URL, $url5);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            // $result = curl_exec($ch);
            // curl_close($ch);

            $url = $txt_url."?number=".$phone2."&type=text&message=".urlencode($whsbody)."&instance_id=".$ins."&access_token=".$api;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$url);
            $result =  curl_exec($ch);
            
            echo $result;
            curl_close($ch);
        }

        if($this->party['mobile'] != '' || $this->party['mobile'] != '--None--'){
            // Send Text
            // $data41 = [
            //     "number" => '91'.$this->party['mobile'],
            //     "msg" => $whsbody,
            //     // "media" => $media,
            //     // "type" => $type,
            //     "instance" => $ins,
            //     "apikey" => $api
            // ];
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data41));
            // curl_setopt($ch, CURLOPT_URL, $url5);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            // $result = curl_exec($ch);
            // curl_close($ch);

            $url = $txt_url."?number=".$phone3."&type=text&message=".urlencode($whsbody)."&instance_id=".$ins."&access_token=".$api;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_URL,$url);
            $result =  curl_exec($ch);
            
            echo $result;
            curl_close($ch);
        }


        // Send Mail to all Party
        $email = new  SendCandPassStatusP($this->candidate,$this->getAPI,$this->party);
        Mail::to($this->party['email'])->send($email);

    }
}
