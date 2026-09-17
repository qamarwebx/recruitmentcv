<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\AdminModel\BranchDetails;
use App\AdminModel\Userwhatsappapi;

class CourierNotificationDRW implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $courier_not;
     protected $getParty;
     protected $getUser;


    public function __construct($courier_not,$getParty,$getUser)
    {
        $this->courier_not = $courier_not;
        $this->getParty = $getParty;
        $this->getUser = $getUser;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Branch Details
        $branch = BranchDetails::where('br_id','=',$this->courier_not['courier_from'])->first();

        // API Details
        $getAPI2 = Userwhatsappapi::where('api_for','Visa Service')->where('status','=',1)->first();
        $ins = $getAPI2->instance_key;
        $acc_token = $getAPI2->api_key;
        $api_url = $getAPI2->text_message_url;

        
        // Message for Party
        $msgWh1 = "Dear ".$this->getParty['pty_full_name']."\n".$this->getParty['pty_ag_name']."\nWe have dispatched the documents of ".$this->courier_not['candidate_name']." and ".$this->courier_not['pass_no']." on ".date('d-m-Y',strtotime($this->courier_not['dod']))." from ".$branch->br_name.", ".$branch->city." the details of tracking number shall be shared to you shortly.\n\nहमने ".$branch->br_name.", ".$branch->city." से ".$this->courier_not['candidate_name']." और ".$this->courier_not['pass_no']." के दस्तावेज ".date('d-m-Y',strtotime($this->courier_not['dod']))." को भेज दिए हैं, ट्रैकिंग नंबर का विवरण आपको शीघ्र ही साझा किया जाएगा।\n\nRegards,\nQamr International\nGulf Jobs Consultancy";
        if ($this->getParty['pty_id'] != 200) {
            $msgWh2 = "Dear ".$this->getParty['pty_full_name']."\n".$this->getParty['pty_ag_name']."\nWe have dispatched the documents of ".$this->courier_not['candidate_name']." and ".$this->courier_not['pass_no']." on ".date('d-m-Y',strtotime($this->courier_not['dod']))." from ".$branch->br_name.", ".$branch->city." the details of tracking number shall be shared to you shortly.\n\nहमने ".$branch->br_name.", ".$branch->city." से ".$this->courier_not['candidate_name']." और ".$this->courier_not['pass_no']." के दस्तावेज ".date('d-m-Y',strtotime($this->courier_not['dod']))." को भेज दिए हैं, ट्रैकिंग नंबर का विवरण आपको शीघ्र ही साझा किया जाएगा।\n\nRegards,\nQamr International\nGulf Jobs Consultancy";
        } else {
            $msgWh2 = "Dear ".$this->getUser['name']."\nWe have dispatched the documents of ".$this->courier_not['candidate_name']." and ".$this->courier_not['pass_no']." on ".date('d-m-Y',strtotime($this->courier_not['dod']))." from ".$branch->br_name.", ".$branch->city." the details of tracking number shall be shared to you shortly.\n\nहमने ".$branch->br_name.", ".$branch->city." से ".$this->courier_not['candidate_name']." और ".$this->courier_not['pass_no']." के दस्तावेज ".date('d-m-Y',strtotime($this->courier_not['dod']))." को भेज दिए हैं, ट्रैकिंग नंबर का विवरण आपको शीघ्र ही साझा किया जाएगा।\n\nRegards,\nQamr International\nGulf Jobs Consultancy";
        }
        
        // Send whatsapp for party
        if ($this->getParty['pty_id'] != 200) {
            if ($this->getParty['pty_comp_contact'] != '') {

                $sendURL = $api_url."?number=".$this->getParty['pty_comp_contact']."&type=text&message=".urlencode($msgWh1)."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            if ($this->getParty['pty_contact_no'] != '') {
                $sendURL2 = $api_url."?number=".$this->getParty['pty_contact_no']."&type=text&message=".urlencode($msgWh1)."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL2);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }
        }

        if($this->getUser['user_id'] != 200){
            if ($this->getUser['mobile'] != '') {
                $sendURL3 = $api_url."?number=".$this->getUser['mobile']."&type=text&message=".urlencode($msgWh2)."&instance_id=".$ins."&access_token=".$acc_token;
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
