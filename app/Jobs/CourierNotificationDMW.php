<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CourierNotificationDMW implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $courier_not;
     protected $getAPI;
     protected $getarty;
     protected $getUser;
     protected $getCsn;

    public function __construct($courier_not,$getAPI,$getarty,$getUser,$getCsn)
    {
        $this->courier_not = $courier_not;
        $this->getAPI = $getAPI;
        $this->getarty = $getarty;
        $this->getUser = $getUser;
        $this->getCsn = $getCsn;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ins = $this->getAPI['instance_key'];
        $acc_token = $this->getAPI['api_key'];
        $api_url = $this->getAPI['text_message_url'];

        // Document List Start
        $iw = 1;
        if ($this->courier_not['valid_pass'] == 1) {
            $list_doc = $iw.") Valid Passport\n";
            $iw++;
        }

        if ($this->courier_not['old_pass'] == 1) {
            $list_doc .= $iw.") Old Passport\n";
            $iw++;
        }

        if ($this->courier_not['driving_lic'] == 1) {
            $list_doc .= $iw.") Driving License\n";
            $iw++;
        }
        if ($this->courier_not['exit_paper'] == 1) {
            $list_doc .= $iw.") Exit Paper\n";
            $iw++;
        }
        if ($this->courier_not['photo_r'] == 1) {
            $list_doc .= $iw.") Photo\n";
            $iw++;
        }
        if ($this->courier_not['med_rep'] == 1) {
            $list_doc .= $iw.") Medical Report\n";
            $iw++;
        }
        if ($this->courier_not['medical_token'] == 1) {
            $list_doc .= $iw.") Medical Token\n";
            $iw++;
        }

        // Document List End



        if($this->getarty['pty_id'] != 200){
            // Send Whatsapp

            // Message for Party
            $msgWh1 = "Dear ".$this->getarty['pty_ag_name']."\nWe have delivered  the documents of ".$this->courier_not['candidate_name']." and ".$this->courier_not['pass_no']." on ".date("d-m-Y",strtotime($this->courier_not['dod']))." through ".$this->getCsn['name']." ".$this->courier_not['tracking_id']." ".$this->courier_not['address'].". Here are the details of the delivered documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for delivering your documents.\nRegards,\nQamr International\nGulf Jobs Consultancy";
        
            
        }

        if($this->getUser['user_id'] != 200){
            // Send Whatsapp
        }
    }
}
