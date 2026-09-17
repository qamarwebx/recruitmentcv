<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\AdminModel\Userwhatsappapi;

class CourierNotificationRRW implements ShouldQueue
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

    public function __construct($uploadCN,$party,$user)
    {
        $this->uploadCN = $uploadCN;
        $this->party = $party;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        // API Details
        $getAPI2 = Userwhatsappapi::where('api_for','Visa Service')->where('status','=',1)->first();

        $ins = $getAPI2->instance_key;
        $acc_token = $getAPI2->api_key;
        $api_url = $getAPI2->text_message_url;

        // Message for Party
        $msgWh1 = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\nWe have received your request for courier of ".$this->uploadCN['candidate_name']." and ".$this->uploadCN['pass_no']." once we will check the payment status for your passport. And then the passport shall be delivered accordingly.\n हमें ".$this->uploadCN['candidate_name']." और ".$this->uploadCN['pass_no']." के कूरियर के लिए आपका अनुरोध प्राप्त हुआ है और पासपोर्ट तदनुसार वितरित किया जाएगा\n\n\nRegards,\nQamr International\nGulf Jobs Consultancy";

        if ($this->party['pty_id'] != 200) {
            $msgWh2 = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\nWe have received your request for courier of ".$this->uploadCN['candidate_name']." and ".$this->uploadCN['pass_no']." once we will check the payment status for your passport. And then the passport shall be delivered accordingly.\n हमें ".$this->uploadCN['candidate_name']." और ".$this->uploadCN['pass_no']." के कूरियर के लिए आपका अनुरोध प्राप्त हुआ है और पासपोर्ट तदनुसार वितरित किया जाएगा\n\n\nRegards,\nQamr International\nGulf Jobs Consultancy";
            // $msgWh2 = "Dear ".$this->party['pty_ag_name']."\nThis is to acknowledge that we have received the documents of ".$this->uploadCN['candidate_name']." and ".$this->uploadCN['pass_no']." on ".date("d-m-Y",strtotime($this->uploadCN['dor']))." through ".$this->csn['name'].". We are glad to receive the documents at the right time.  Here are the details of the received documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for receiving your documents.\nRegards,\nQamr International\nGulf Jobs Consultancy";
        } else {
            $msgWh2 = "Dear ".$this->user['name']."\nWe have received your request for courier of ".$this->uploadCN['candidate_name']." and ".$this->uploadCN['pass_no']." once we will check the payment status for your passport. And then the passport shall be delivered accordingly.\n हमें ".$this->uploadCN['candidate_name']." और ".$this->uploadCN['pass_no']." के कूरियर के लिए आपका अनुरोध प्राप्त हुआ है और पासपोर्ट तदनुसार वितरित किया जाएगा\n\n\nRegards,\nQamr International\nGulf Jobs Consultancy";
        //    $msgWh2 = "Dear ".$this->user['name']."\nThis is to acknowledge that we have received the documents of ".$this->uploadCN['candidate_name']." and ".$this->uploadCN['pass_no']." on ".date("d-m-Y",strtotime($this->uploadCN['dor']))." through ".$this->csn['name'].". We are glad to receive the documents at the right time.  Here are the details of the received documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for receiving your documents.\nRegards,\nQamr International\nGulf Jobs Consultancy";
        }

        // Send whatsapp for party
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
