<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmigrationSuccAppMail;
use App\User;

class EmigrationSuccApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $emig_camp,$party,$getApi,$emig_post;

    public function __construct($emig_camp,$party,$getApi,$emig_post)
    {
        $this->emig_camp = $emig_camp;
        $this->party = $party;
        $this->getApi = $getApi;
        $this->emig_post = $emig_post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ins = $this->getApi['instance_key'];
        $acc_token = $this->getApi['api_key'];
        $api_url = $this->getApi['text_message_url'];

        if($this->party['pty_id'] != '200'){

            // Send Whatsapp
            $msgWH = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." Emigration applied Successfully passport no".$this->emig_camp['pass_no']." on the date of ".date('d-m-Y',strtotime($this->emig_camp['emig_app_date']))." This is the ".$this->emig_camp['en_no']." waiting for the approval if you want more detail Visit this website www.emigrate.gov.in\n\nडिअर ".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." का ".$this->emig_camp['pass_no']." ये है इन का एमिग्रेशन अप्पीलिएड सफलता पूर्वक कर दिया गया है इस ".date('d-m-Y',strtotime($this->emig_camp['emig_app_date']))." मे ".$this->emig_camp['en_no']." अप्रूवल का इंतज़ार किया जा रहा है प्रोसेस स्टेटस जानने के लिए इस वेबसाइट पर विजिट करे www.emigrate.gov.in";

            if ($this->emig_camp['file'] != '') {

                // $url_path = 'http://crm.qamrintl.com';
                // $url_path =  "http://crm.qamr.in";
                $url_path = url('/');
                $file_path2 = 'image/emigration/employer';
                // $media = $url_path.'/'.$file_path2.'/'.$this->emig_camp['file'];
                $media = url('/image/emigration/employer/'.$this->emig_camp['file']);

                if($this->party['pty_comp_contact'] != '' || $this->party['pty_comp_contact'] != '--None--'){
                    $url = $api_url."?number=".$this->party['pty_comp_contact']."&type=media&message=".urlencode($msgWH)."&media_url=".$media."&filename=".$this->emig_camp['file']."&instance_id=".$ins."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }
    
                if($this->party['pty_contact_no'] != '' || $this->party['pty_contact_no'] != '--None--'){
                    $url2 = $api_url."?number=".$this->party['pty_contact_no']."&type=media&message=".urlencode($msgWH)."&media_url=".$media."&filename=".$this->emig_camp['file']."&instance_id=".$ins."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url2);
                    $result =  curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

            }else{
                if($this->party['pty_comp_contact'] != '' || $this->party['pty_comp_contact'] != '--None--'){
                    $url = $api_url."?number=".$this->party['pty_comp_contact']."&type=text&message=".urlencode($msgWH)."&instance_id=".$ins."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }
    
                if($this->party['pty_contact_no'] != '' || $this->party['pty_contact_no'] != '--None--'){
                    $url2 = $api_url."?number=".$this->party['pty_contact_no']."&type=text&message=".urlencode($msgWH)."&instance_id=".$ins."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url2);
                    $result =  curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }
            }


            // Send Mail
            if($this->party['pty_email'] != ''){
                Mail::to($this->party['pty_email'])->send(new EmigrationSuccAppMail($this->emig_camp,$this->party));
            }

            if($this->party['sec_email'] != ''){
                Mail::to($this->party['sec_email'])->send(new EmigrationSuccAppMail($this->emig_camp,$this->party));
            }
        }

        // If care of is not null then send message or whatsapp
        $user = User::where('user_id','=',$this->emig_post['careoff_id'])->where('user_id','!=','')->where('user_id','!=',200)->first();
        if(isset($user)){
            // Send Whatsapp
            $msgWH = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." Emigration applied Successfully passport no".$this->emig_camp['pass_no']." on the date of ".date('d-m-Y',strtotime($this->emig_camp['emig_app_date']))." This is the ".$this->emig_camp['en_no']." waiting for the approval if you want more detail Visit this website www.emigrate.gov.in\n\nडिअर ".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." का ".$this->emig_camp['pass_no']." ये है इन का एमिग्रेशन अप्पीलिएड सफलता पूर्वक कर दिया गया है इस ".date('d-m-Y',strtotime($this->emig_camp['emig_app_date']))." मे ".$this->emig_camp['en_no']." अप्रूवल का इंतज़ार किया जा रहा है प्रोसेस स्टेटस जानने के लिए इस वेबसाइट पर विजिट करे www.emigrate.gov.in";
        
            if ($this->emig_camp['file'] != '') {
                $media = url('/image/emigration/employer/'.$this->emig_camp['file']);
                if($user->mobile != ''){
                    $url = $api_url."?number=".$user->mobile."&type=media&message=".urlencode($msgWH)."&media_url=".$media."&filename=".$this->emig_camp['file']."&instance_id=".$ins."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }
            }else{
                if($user->mobile != ''){
                    $url = $api_url."?number=".$user->mobile."&type=text&message=".urlencode($msgWH)."&instance_id=".$ins."&access_token=".$acc_token;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result =  curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }
            }

            if($user->email != ''){
                Mail::to($user->email)->send(new EmigrationSuccAppMail($this->emig_camp,$this->party));
            }
        }

        // if ($this->emig_post['careoff_id'] != '' || $this->emig_post['careoff_id'] != 200) {
        //     $user = User::where('user_id','=',$this->emig_post['careoff_id'])->first();

        //     // Send Whatsapp
        //     $msgWH = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." Emigration applied Successfully passport no".$this->emig_camp['pass_no']." on the date of ".date('d-m-Y',strtotime($this->emig_camp['emig_app_date']))." This is the ".$this->emig_camp['en_no']." waiting for the approval if you want more detail Visit this website www.emigrate.gov.in\n\nडिअर ".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." का ".$this->emig_camp['pass_no']." ये है इन का एमिग्रेशन अप्पीलिएड सफलता पूर्वक कर दिया गया है इस ".date('d-m-Y',strtotime($this->emig_camp['emig_app_date']))." मे ".$this->emig_camp['en_no']." अप्रूवल का इंतज़ार किया जा रहा है प्रोसेस स्टेटस जानने के लिए इस वेबसाइट पर विजिट करे www.emigrate.gov.in";
        
        //     if ($this->emig_camp['file'] != '') {
        //         $media = url('/image/emigration/employer/'.$this->emig_camp['file']);
        //         if($user->mobile != ''){
        //             $url = $api_url."?number=".$user->mobile."&type=media&message=".urlencode($msgWH)."&media_url=".$media."&filename=".$this->emig_camp['file']."&instance_id=".$ins."&access_token=".$acc_token;
        //             $ch = curl_init();
        //             curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //             curl_setopt($ch,CURLOPT_URL,$url);
        //             $result =  curl_exec($ch);
        //             echo $result;
        //             curl_close($ch);
        //         }
        //     }else{
        //         if($user->mobile != ''){
        //             $url = $api_url."?number=".$user->mobile."&type=text&message=".urlencode($msgWH)."&instance_id=".$ins."&access_token=".$acc_token;
        //             $ch = curl_init();
        //             curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //             curl_setopt($ch,CURLOPT_URL,$url);
        //             $result =  curl_exec($ch);
        //             echo $result;
        //             curl_close($ch);
        //         }
        //     }

        //     if($user->email != ''){
        //         Mail::to($user->email)->send(new EmigrationSuccAppMail($this->emig_camp,$this->party));
        //     }
            
        // }
    }
}

