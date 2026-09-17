<?php

namespace App\Jobs;

use App\AdminModel\Userwhatsappapi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CourierNotificationDW implements ShouldQueue
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
        // API Details
        $getAPI2 = Userwhatsappapi::where('api_for','Visa Service')->where('status','=',1)->first();

        // $ins = $getAPI2->instance_key;
        // $acc_token = $getAPI2->api_key;
        // $api_url = $getAPI2->text_message_url;

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

        // Message for Party
        $msgWh1 = "Dear ".$this->getarty['pty_full_name']."\n".$this->getarty['pty_ag_name']."\nWe have delivered  the documents of ".$this->courier_not['candidate_name']." and ".$this->courier_not['pass_no']." on ".date("d-m-Y",strtotime($this->courier_not['dod']))." through ".$this->getCsn['name']." ".$this->courier_not['tracking_id']." ".$this->courier_not['address'].". Here are the details of the delivered documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for delivering your documents.\nRegards,\nQamr International\nGulf Jobs Consultancy";

        if($this->getarty['pty_id'] != 200){
            $msgWh2 = "Dear ".$this->getarty['pty_full_name']."\n".$this->getarty['pty_ag_name']."\nWe have delivered  the documents of ".$this->courier_not['candidate_name']." and ".$this->courier_not['pass_no']." on ".date("d-m-Y",strtotime($this->courier_not['dod']))." through ".$this->getCsn['name']." ".$this->courier_not['tracking_id']." ".$this->courier_not['address'].". Here are the details of the delivered documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for delivering your documents.\nRegards,\nQamr International\nGulf Jobs Consultancy";
        }else{
            $msgWh2 = "Dear ".$this->getUser['name']."\nWe have delivered  the documents of ".$this->courier_not['candidate_name']." and ".$this->courier_not['pass_no']." on ".date("d-m-Y",strtotime($this->courier_not['dod']))." through ".$this->getCsn['name']." ".$this->courier_not['tracking_id']." ".$this->courier_not['address'].". Here are the details of the delivered documents.\n".$list_doc."\nKindly respond to this notification if all the documents are correct and treat this notification as a formal acknowledgment copy from us for delivering your documents.\nRegards,\nQamr International\nGulf Jobs Consultancy";
        }

        // Contact Number
        // $phone1 = '91'.$this->getarty['pty_comp_contact'];
        // $phone2 = '91'.$this->getarty['pty_contact_no'];
        // $phone3 = '91'.$this->getUser['mobile'];

        // file details with path

        // $media = url('/').'/image/service-candidate/'.$this->courier_not['file'];
        // $media = 'http://crm.qamrintl.com/image/service-candidate/'.$this->courier_not['file'];

        // $url_path = 'http://crm.qamrintl.com';
        // $url_path =  "http://crm.qamr.in";
        $url_path = url('/');
        $file_path2 = 'image/service-candidate';
        // $media = $url_path.'/'.$file_path2.'/'.$this->courier_not['file'];

        $media = url('/image/service-candidate/'.$this->courier_not['file']);

        // $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($whsbody2)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins_id."&access_token=".$access_token;

        // media URL
        // $sendURL = $api_url."?number=".$this->getarty['pty_comp_contact']."&type=media&message=".urlencode($msgWh1)."&media_url=".$media."&filename=".$this->courier_not['file']."&instance_id=".$ins."&access_token=".$acc_token;

        // file is not blank
        if($this->courier_not['file'] != ''){
            // Send whatsapp for party
            if($this->getarty['pty_id'] != 200){
                if ($this->getarty['pty_comp_contact'] != '') {
                    $sendURL = $api_url."?number=".$this->getarty['pty_comp_contact']."&type=media&message=".urlencode($msgWh1)."&media_url=".$media."&filename=".$this->courier_not['file']."&instance_id=".$ins."&access_token=".$acc_token;
                    // $sendURL = $api_url."?number=".$this->getarty['pty_comp_contact']."&type=media&message=".urlencode($msgWh1)."&media_url=".$media."&filename=".$this->courier_not['file']."&instance_id=".$ins."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$sendURL);
                    $result =  curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

                if ($this->getarty['pty_contact_no'] != '') {
                    $sendURL2 = $api_url."?number=".$this->getarty['pty_contact_no']."&type=media&message=".urlencode($msgWh1)."&media_url=".$media."&filename=".$this->courier_not['file']."&instance_id=".$ins."&access_token=".$acc_token;
                    // $sendURL2 = $api_url."?number=".$this->getarty['pty_contact_no']."&type=media&message=".urlencode($msgWh1)."&media_url=".$media."&filename=".$this->courier_not['file']."&instance_id=".$ins."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$sendURL2);
                    $result =  curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

            }

            // Send whatsapp for careoff
            if($this->getUser['user_id'] != 200){
                if($this->getUser['mobile'] != ''){
                    $sendURL3 = $api_url."?number=".$this->getUser['mobile']."&type=media&message=".urlencode($msgWh2)."&media_url=".$media."&filename=".$this->courier_not['file']."&instance_id=".$ins."&access_token=".$acc_token;
                    // $sendURL3 = $api_url."?number=".$this->getUser['mobile']."&type=media&message=".urlencode($msgWh2)."&media_url=".$media."&filename=".$this->courier_not['file']."&instance_id=".$ins."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$sendURL3);
                    $result =  curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }
            }

        }else{
            // Send whatsapp for party
            if($this->getarty['pty_id'] != 200){
                    

                if ($this->getarty['pty_comp_contact'] != '') {
                    $sendURL = $api_url."?number=".$this->getarty['pty_comp_contact']."&type=text&message=".urlencode($msgWh1)."&instance_id=".$ins."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$sendURL);
                    $result =  curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

                if ($this->getarty['pty_contact_no'] != '') {
                    $sendURL2 = $api_url."?number=".$this->getarty['pty_contact_no']."&type=text&message=".urlencode($msgWh1)."&instance_id=".$ins."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$sendURL2);
                    $result =  curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }
            }
            // Send whatsapp for careoff
            if($this->getUser['user_id'] != 200){
                if($this->getUser['mobile'] != ''){
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


        // Send whatsapp for party
        // if($this->getarty['pty_id'] != 200){
                    

        //     if ($this->getarty['pty_comp_contact'] != '') {
        //         $sendURL = $api_url."?number=91".$this->getarty['pty_comp_contact']."&type=text&message=".urlencode($msgWh1)."&instance_id=".$ins."&access_token=".$acc_token;
        //         $ch = curl_init();
        //         curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //         curl_setopt($ch,CURLOPT_URL,$sendURL);
        //         $result =  curl_exec($ch);
        //         echo $result;
        //         curl_close($ch);
        //     }

        //     if ($this->getarty['pty_contact_no'] != '') {
        //         $sendURL2 = $api_url."?number=91".$this->getarty['pty_contact_no']."&type=text&message=".urlencode($msgWh1)."&instance_id=".$ins."&access_token=".$acc_token;
        //         $ch = curl_init();
        //         curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //         curl_setopt($ch,CURLOPT_URL,$sendURL2);
        //         $result =  curl_exec($ch);
        //         echo $result;
        //         curl_close($ch);
        //     }
        // }
        // Send whatsapp for careoff
        // if($this->getUser['user_id'] != 200){
        //     if($this->getUser['mobile'] != ''){
        //         $sendURL3 = $api_url."?number=91".$this->getUser['mobile']."&type=text&message=".urlencode($msgWh2)."&instance_id=".$ins."&access_token=".$acc_token;
        //         $ch = curl_init();
        //         curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //         curl_setopt($ch,CURLOPT_URL,$sendURL3);
        //         $result =  curl_exec($ch);
        //         echo $result;
        //         curl_close($ch);
        //     }
        // }
        
    }
}
