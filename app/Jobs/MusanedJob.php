<?php

namespace App\Jobs;

use App\Mail\MusanedMailJ;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class MusanedJob implements ShouldQueue
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
    protected $cand_sd;
    protected $getAPI;

    public function __construct($post,$party,$user,$cand_sd,$getAPI)
    {
        $this->post = $post;
        $this->party = $party;
        $this->user = $user;
        $this->cand_sd = $cand_sd;
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
        // $url4 = $this->getAPI['media_message_url'];
		// $url5 = $this->getAPI['text_message_url'];

        // Store API Details
        // $ins = '05fc4dc2935608fb72db8dacba987eada2c6b94bf667fe77f7bbad994de02f03';
        // $api = "fe3b062f9606a7e66202d776b1e471f10bd16ae0becc1622a01370bfdbd439e1";
        // $url_path = 'http://crm.qamrintl.com';
        // $url_path =  "http://crm.qamr.in";
        $url_path = url('/');
        $file_path2 = 'image/service-candidate';
        // $url4 = "http://whatsapi.smsinsta.com/api/send-media";
		// $url5 = "http://whatsapi.smsinsta.com/api/send-text";

        $cand_name = $this->cand_sd['cand_fname'].' '.$this->cand_sd['cand_mname'].' '.$this->cand_sd['cand_lname'];
        
        $whsbody = "Dear ".$this->party['pty_full_name']."\n".ucfirst($this->party['pty_ag_name'])."\nThe Candidate ".$cand_name." bearing passport No.".$this->cand_sd['cand_passport_no']." successfully registered at Musaned on ".date("d-m-Y",strtotime($this->post['musaned_date'])).".\nPlease check the below attachment of Musaned CV for your references.";
        $whsbody11 = "Dear ".$this->user['name']."\n".ucfirst($this->party['pty_ag_name'])."\nThe Candidate ".$cand_name." bearing passport No.".$this->cand_sd['cand_passport_no']." successfully registered at Musaned on ".date("d-m-Y",strtotime($this->post['musaned_date'])).".\nPlease check the below attachment of Musaned CV for your references.";        
        
        $whsbody2 = "Dear ".$this->party['pty_full_name']."\n".ucfirst($this->party['pty_ag_name'])."\nThe Candidate ".$cand_name." bearing passport No. ".$this->cand_sd['cand_passport_no']." of Musaned has been Registered at another office.";
        $whsbody22 = "Dear ".$this->user['name']."\n".ucfirst($this->party['pty_ag_name'])."\nThe Candidate ".$cand_name." bearing passport No. ".$this->cand_sd['cand_passport_no']." of Musaned has been Registered at another office.";

        // $media = $url_path.'/'.$file_path2.'/'.$this->post['file'];
        $media = url('/image/service-candidate/'.$this->post['file']);
        $type = $this->post['file_ext'];

        $phone1 = $this->party['pty_comp_contact'];
        $phone2 = $this->party['pty_contact_no'];
        $phone3 = $this->user['mobile'];


        if($this->post['send_type'] == 'registered'){
            $mesSub1 = 'Musaned registered Successfully for '.$cand_name.' with '.$this->cand_sd['cand_passport_no'];

            if($this->party['pty_comp_contact'] != '' || $this->party['pty_comp_contact'] != '--None--'){
                // Send Media
                // $data4 = [
                //     "number" => '91'.$this->party['pty_comp_contact'],
                //     "msg" => '',
                //     "media" => $media,
                //     "type" => $type,
                //     "instance" => $ins,
                //     "apikey" => $api
                // ];

                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
                // curl_setopt($ch, CURLOPT_URL, $url4);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // $result = curl_exec($ch);
                // curl_close($ch);

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

                $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($whsbody)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins_id."&access_token=".$access_token;
            
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }

            if($this->party['pty_contact_no'] != '' || $this->party['pty_contact_no'] != '--None--'){
                // $data5 = [
                //     "number" => '91'.$this->party['pty_contact_no'],
                //     "msg" => '',
                //     "media" => $media,
                //     "type" => $type,
                //     "instance" => $ins,
                //     "apikey" => $api
                // ];

                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
                // curl_setopt($ch, CURLOPT_URL, $url4);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // $result = curl_exec($ch);
                // curl_close($ch);

                // // Send Text
                // $data51 = [
                //     "number" => '91'.$this->party['pty_contact_no'],
                //     "msg" => $whsbody,
                //     "instance" => $ins,
                //     "apikey" => $api
                // ];

                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data51));
                // curl_setopt($ch, CURLOPT_URL, $url5);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // $result = curl_exec($ch);
                // curl_close($ch);

                $url = $txt_url."?number=".$phone2."&type=media&message=".urlencode($whsbody)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins_id."&access_token=".$access_token;
            
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }

            if($this->user['mobile'] != '' || $this->user['mobile'] != '--None--'){
                // Media Send
                // $data6 = [
                //     "number" => '91'.$this->user['mobile'],
                //     "msg" => '',
                //     "media" => $media,
                //     "type" => $type,
                //     "instance" => $ins,
                //     "apikey" => $api
                // ];

                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data6));
                // curl_setopt($ch, CURLOPT_URL, $url4);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // $result = curl_exec($ch);
                // curl_close($ch);


                // Text Send
                // $data7 = [
                //     "number" => '91'.$this->user['mobile'],
                //     "msg" => $whsbody11,
                //     "instance" => $ins,
                //     "apikey" => $api
                // ];

                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data7));
                // curl_setopt($ch, CURLOPT_URL, $url5);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // $result = curl_exec($ch);
                // curl_close($ch);


                $url = $txt_url."?number=".$phone3."&type=media&message=".urlencode($whsbody)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins_id."&access_token=".$access_token;
            
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }
            
        }else{
            $mesSub1 = 'Musaned regitered at another office of '.$cand_name.' with '.$this->cand_sd['cand_passport_no'];

            if ($this->party['pty_comp_contact'] != '' || $this->party['pty_comp_contact'] != '--None--') {
                // Send Media
                // $data4 = [
                //     "number" => '91'.$this->party['pty_comp_contact'],
                //     "msg" => '',
                //     "media" => $media,
                //     "type" => $type,
                //     "instance" => $ins,
                //     "apikey" => $api
                // ];

                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
                // curl_setopt($ch, CURLOPT_URL, $url4);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // $result = curl_exec($ch);
                // curl_close($ch);
                // Send Text
                // $data41 = [
                //     "number" => '91'.$this->party['pty_comp_contact'],
                //     "msg" => $whsbody2,
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

                $url = $txt_url."?number=".$phone1."&type=media&message=".urlencode($whsbody2)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins_id."&access_token=".$access_token;
            
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }

            if ($this->party['pty_contact_no'] != '' || $this->party['pty_contact_no'] != '--None--') {
                // $data5 = [
                //     "number" => '91'.$this->party['pty_contact_no'],
                //     "msg" => $whsbody2,
                //     "media" => $media,
                //     "type" => $type,
                //     "instance" => $ins,
                //     "apikey" => $api
                // ];

                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data5));
                // curl_setopt($ch, CURLOPT_URL, $url4);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // $result = curl_exec($ch);
                // curl_close($ch);

                $url = $txt_url."?number=".$phone2."&type=media&message=".urlencode($whsbody2)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins_id."&access_token=".$access_token;
            
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }

            if ($this->user['mobile'] != '' || $this->user['mobile'] != '--None--') {
                // Send Media
                // $data6 = [
                //     "number" => '91'.$this->user->mobile,
                //     "msg" => $whsbody22,
                //     "media" => $media,
                //     "type" => $type,
                //     "instance" => $ins,
                //     "apikey" => $api
                // ];

                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data6));
                // curl_setopt($ch, CURLOPT_URL, $url4);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                // $result = curl_exec($ch);
                // curl_close($ch);

                $url = $txt_url."?number=".$phone3."&type=media&message=".urlencode($whsbody2)."&media_url=".$media."&filename=".$this->post['file']."&instance_id=".$ins_id."&access_token=".$access_token;
            
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                
                echo $result;
                curl_close($ch);
            }

        }
    }
}
