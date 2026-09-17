<?php

namespace App\Jobs;

use App\AdminModel\Userwhatsappapi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CourierNotificationW implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $uploadCN;
     protected $party;
     protected $user;
    protected $branch25;

    public function __construct($uploadCN,$party,$user,$branch25)
    {
        $this->uploadCN = $uploadCN;
        $this->party = $party;
        $this->user = $user;
        $this->branch25 = $branch25;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $getAPI = Userwhatsappapi::where('api_for','Visa Service')->where('status','=',1)->first();

        $ins = $getAPI->instance_key;
        $acc_token = $getAPI->api_key;
        $api_url = $getAPI->text_message_url;

        // Document List Start
        $iw = 1;
        if ($this->uploadCN['valid_pass'] == 1) {
            $list_doc = $iw.") Valid Passport\n";
            $iw++;
        }

        if ($this->uploadCN['old_pass'] == 1) {
            $list_doc .= $iw.") Old Passport\n";
            $iw++;
        }

        if ($this->uploadCN['driving_lic'] == 1) {
            $list_doc .= $iw.") Driving License\n";
            $iw++;
        }
        if ($this->uploadCN['exit_paper'] == 1) {
            $list_doc .= $iw.") Exit Paper\n";
            $iw++;
        }
        if ($this->uploadCN['photo_r'] == 1) {
            $list_doc .= $iw.") Photo\n";
            $iw++;
        }
        if ($this->uploadCN['med_rep'] == 1) {
            $list_doc .= $iw.") Medical Report\n";
            $iw++;
        }
        if ($this->uploadCN['other_doc'] == 1) {
            $list_doc .= $iw.") ".$this->uploadCN['doc_name']."\n";
            $iw++;
        }
        if ($this->uploadCN['medical_token'] == 1) {
            $list_doc .= $iw.") Medical Token\n";
            $iw++;
        }

        // Document List End

        // Message for Party
        // $msgWh1 = "Dear ".$this->party['pty_ag_name']."\nThis is to acknowledge that we have received the documents of ".$this->uploadCN['candidate_name']." and ".$this->uploadCN['pass_no']." on ".date("d-m-Y",strtotime($this->uploadCN['dor']))." through ".$this->csn['name'].". We are glad to receive the documents at the right time. Here are the details of the received documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for receiving your documents.\n हमें ".$this->uploadCN['candidate_name']." और ".$this->uploadCN['pass_no']." के दस्तावेज ".date("d-m-Y",strtotime($this->uploadCN['dor']))." हालांकि ".$this->csn['name']."प्राप्त हुए हैं। हम सही समय पर दस्तावेज़ प्राप्त करके खुश हैं। यहां प्राप्त दस्तावेजों का विवरण दिया गया है।\n".$list_doc."\nRegards,\nQamr International\nGulf Jobs Consultancy";
        $msgWh1 = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\nWe have received the documents of ".$this->uploadCN['candidate_name']." having Passport No. ".$this->uploadCN['pass_no']." on ".date("d-m-Y",strtotime($this->uploadCN['dor']))." at ".$this->branch25['br_name']." We are glad to receive the documents at the right time. Here are the details of the received documents.\n\n".$list_doc."\n\nहमें ".$this->uploadCN['candidate_name']." पासपोर्ट नंबर ".$this->uploadCN['pass_no']." का दस्तावेज ".date('d-m-Y',strtotime($this->uploadCN['dor']))." को ".$this->branch25['br_name']." में प्राप्त हुए हैं। हमें सही समय पर दस्तावेज़ प्राप्त करके ख़ुशी हैं। यहां प्राप्त दस्तावेजों का विवरण दिया गया है।\n\n".$list_doc."\n\nRegards,\nQamr International\nGulf Jobs Consultancy";

        // $msgWh1 = "Dear ".$this->party['pty_ag_name']."\nThis is to acknowledge that we have received the documents of ".$this->uploadCN['candidate_name']." and ".$this->uploadCN['pass_no']." on ".date("d-m-Y",strtotime($this->uploadCN['dor'])).". We are glad to receive the documents at the right time. Here are the details of the received documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for receiving your documents.\n हमें ".$this->uploadCN['candidate_name']." और ".$this->uploadCN['pass_no']." के दस्तावेज ".date("d-m-Y",strtotime($this->uploadCN['dor']))."प्राप्त हुए हैं। हम सही समय पर दस्तावेज़ प्राप्त करके खुश हैं। यहां प्राप्त दस्तावेजों का विवरण दिया गया है।\n".$list_doc."\nRegards,\nQamr International\nGulf Jobs Consultancy";
        if ($this->party['pty_id'] != 200) {
            // $msgWh2 = "Dear ".$this->party['pty_ag_name']."\nThis is to acknowledge that we have received the documents of ".$this->uploadCN['candidate_name']." and ".$this->uploadCN['pass_no']." on ".date("d-m-Y",strtotime($this->uploadCN['dor']))." through ".$this->csn['name'].". We are glad to receive the documents at the right time. Here are the details of the received documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for receiving your documents.\n हमें ".$this->uploadCN['candidate_name']." और ".$this->uploadCN['pass_no']." के दस्तावेज ".date("d-m-Y",strtotime($this->uploadCN['dor']))." हालांकि ".$this->csn['name']."प्राप्त हुए हैं। हम सही समय पर दस्तावेज़ प्राप्त करके खुश हैं। यहां प्राप्त दस्तावेजों का विवरण दिया गया है।\n".$list_doc."\nRegards,\nQamr International\nGulf Jobs Consultancy";
            // $msgWh2 = "Dear ".$this->party['pty_ag_name']."\nThis is to acknowledge that we have received the documents of ".$this->uploadCN['candidate_name']." and ".$this->uploadCN['pass_no']." on ".date("d-m-Y",strtotime($this->uploadCN['dor'])).". We are glad to receive the documents at the right time. Here are the details of the received documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for receiving your documents.\n हमें ".$this->uploadCN['candidate_name']." और ".$this->uploadCN['pass_no']." के दस्तावेज ".date("d-m-Y",strtotime($this->uploadCN['dor']))."प्राप्त हुए हैं। हम सही समय पर दस्तावेज़ प्राप्त करके खुश हैं। यहां प्राप्त दस्तावेजों का विवरण दिया गया है।\n".$list_doc."\nRegards,\nQamr International\nGulf Jobs Consultancy";
            $msgWh2 = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\nWe have received the documents of ".$this->uploadCN['candidate_name']." having Passport No. ".$this->uploadCN['pass_no']." on ".date("d-m-Y",strtotime($this->uploadCN['dor']))." at ".$this->branch25['br_name']." We are glad to receive the documents at the right time. Here are the details of the received documents.\n\n".$list_doc."\n\nहमें ".$this->uploadCN['candidate_name']." पासपोर्ट नंबर ".$this->uploadCN['pass_no']." का दस्तावेज ".date('d-m-Y',strtotime($this->uploadCN['dor']))." को ".$this->branch25['br_name']." में प्राप्त हुए हैं। हमें सही समय पर दस्तावेज़ प्राप्त करके ख़ुशी हैं। यहां प्राप्त दस्तावेजों का विवरण दिया गया है।\n\n".$list_doc."\n\nRegards,\nQamr International\nGulf Jobs Consultancy";

        } else {

            // $msgWh2 = "Dear ".$this->user['name']."\nThis is to acknowledge that we have received the documents of ".$this->uploadCN['candidate_name']." and ".$this->uploadCN['pass_no']." on ".date("d-m-Y",strtotime($this->uploadCN['dor']))." through ".$this->csn['name'].". We are glad to receive the documents at the right time. Here are the details of the received documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for receiving your documents.\n हमें ".$this->uploadCN['candidate_name']." और ".$this->uploadCN['pass_no']." के दस्तावेज ".date("d-m-Y",strtotime($this->uploadCN['dor']))." हालांकि ".$this->csn['name']."प्राप्त हुए हैं। हम सही समय पर दस्तावेज़ प्राप्त करके खुश हैं। यहां प्राप्त दस्तावेजों का विवरण दिया गया है।\n".$list_doc."\nRegards,\nQamr International\nGulf Jobs Consultancy";
            // $msgWh2 = "Dear ".$this->user['name']."\nThis is to acknowledge that we have received the documents of ".$this->uploadCN['candidate_name']." and ".$this->uploadCN['pass_no']." on ".date("d-m-Y",strtotime($this->uploadCN['dor'])).". We are glad to receive the documents at the right time. Here are the details of the received documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for receiving your documents.\n हमें ".$this->uploadCN['candidate_name']." और ".$this->uploadCN['pass_no']." के दस्तावेज ".date("d-m-Y",strtotime($this->uploadCN['dor']))."प्राप्त हुए हैं। हम सही समय पर दस्तावेज़ प्राप्त करके खुश हैं। यहां प्राप्त दस्तावेजों का विवरण दिया गया है।\n".$list_doc."\nRegards,\nQamr International\nGulf Jobs Consultancy";
            $msgWh2 = "Dear ".$this->user['name']."\nWe have received the documents of ".$this->uploadCN['candidate_name']." having Passport No. ".$this->uploadCN['pass_no']." on ".date("d-m-Y",strtotime($this->uploadCN['dor']))." at ".$this->branch25['br_name']." We are glad to receive the documents at the right time. Here are the details of the received documents.\n\n".$list_doc."\n\nहमें ".$this->uploadCN['candidate_name']." पासपोर्ट नंबर ".$this->uploadCN['pass_no']." का दस्तावेज ".date('d-m-Y',strtotime($this->uploadCN['dor']))." को ".$this->branch25['br_name']." में प्राप्त हुए हैं। हमें सही समय पर दस्तावेज़ प्राप्त करके ख़ुशी हैं। यहां प्राप्त दस्तावेजों का विवरण दिया गया है।\n\n".$list_doc."\n\nRegards,\nQamr International\nGulf Jobs Consultancy";
        }

        if($this->party['pty_id'] != 200){
                    
            if ($this->party['pty_comp_contact'] != '') {
                $sendURL = $api_url."?number=".$this->party['pty_comp_contact']."&type=text&message=".urlencode($msgWh1)."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            if ($this->party['pty_contact_no'] != '') {
                $sendURL2 = $api_url."?number=".$this->party['pty_contact_no']."&type=text&message=".urlencode($msgWh1)."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL2);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }
        }
        // Send whatsapp for careoff
        if($this->user['user_id'] != 200){
            if($this->user['mobile'] != ''){
                $sendURL3 = $api_url."?number=".$this->user['mobile']."&type=text&message=".urlencode($msgWh2)."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL3);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }
        }
    }
}
