<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\EmigrationAlxMail;
use App\User;
use Illuminate\Support\Facades\Mail;

class EmigrationSpnAlx implements ShouldQueue
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
            $msgWH = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." ".$this->emig_camp['pass_no']." and ".$this->emig_camp['sponsor_name']." ".$this->emig_camp['sponsor_id']." is already exit on emigration Site that's the reason we can't do normal emigration process that's why please contact To sponsor and request him to provide USER ID and PASSWORD Of the emigration side\n\nडिअर ".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." जिसका ".$this->emig_camp['pass_no']." ये और ".$this->emig_camp['sponsor_name']." ".$this->emig_camp['sponsor_id']." पहले से ही एमिग्रेशन वेब साइड पे रेजिस्टरड है इसलिए हम सामान्य एमिग्रेशन नहीं करसकते है कृपया स्पोंसर से संपर्क करें और निवेदन करे की एमिग्रेटे वेब साइट का यूजर आईडी और पासवर्ड प्रदान करे जिससे आगे एमिग्रेशन का प्रक्रिया आसानी से किया जा सके";
            
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
                $url = $api_url."?number=".$this->party['pty_contact_no']."&type=text&message=".urlencode($msgWH)."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            // Send Mail
            if($this->party['pty_email'] != ''){
                Mail::to($this->party['pty_email'])->send(new EmigrationAlxMail($this->emig_camp,$this->party));
            }

            if($this->party['sec_email'] != ''){
                Mail::to($this->party['sec_email'])->send(new EmigrationAlxMail($this->emig_camp,$this->party));
            }
        }

        // If care of is not null then send message or whatsapp
        $user = User::where('user_id','=',$this->emig_post['careoff_id'])->where('user_id','!=','')->where('user_id','!=',200)->first();
        if (isset($user)) {
            $msgWH = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." ".$this->emig_camp['pass_no']." and ".$this->emig_camp['sponsor_name']." ".$this->emig_camp['sponsor_id']." is already exit on emigration Site that's the reason we can't do normal emigration process that's why please contact To sponsor and request him to provide USER ID and PASSWORD Of the emigration side\n\nडिअर ".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." जिसका ".$this->emig_camp['pass_no']." ये और ".$this->emig_camp['sponsor_name']." ".$this->emig_camp['sponsor_id']." पहले से ही एमिग्रेशन वेब साइड पे रेजिस्टरड है इसलिए हम सामान्य एमिग्रेशन नहीं करसकते है कृपया स्पोंसर से संपर्क करें और निवेदन करे की एमिग्रेटे वेब साइट का यूजर आईडी और पासवर्ड प्रदान करे जिससे आगे एमिग्रेशन का प्रक्रिया आसानी से किया जा सके";

            if($user->mobile != ''){
                $url = $api_url."?number=".$user->mobile."&type=text&message=".urlencode($msgWH)."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            // Send Mail
            if($user->email != ''){
                Mail::to($user->email)->send(new EmigrationAlxMail($this->emig_camp,$this->party));
            }
        }
        

        // if ($this->emig_post['careoff_id'] != '' || $this->emig_post['careoff_id'] != 200) {
        //     $user = User::where('user_id','=',$this->emig_post['careoff_id'])->first();

        //     $msgWH = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." ".$this->emig_camp['pass_no']." and ".$this->emig_camp['sponsor_name']." ".$this->emig_camp['sponsor_id']." is already exit on emigration Site that's the reason we can't do normal emigration process that's why please contact To sponsor and request him to provide USER ID and PASSWORD Of the emigration side\n\nडिअर ".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." जिसका ".$this->emig_camp['pass_no']." ये और ".$this->emig_camp['sponsor_name']." ".$this->emig_camp['sponsor_id']." पहले से ही एमिग्रेशन वेब साइड पे रेजिस्टरड है इसलिए हम सामान्य एमिग्रेशन नहीं करसकते है कृपया स्पोंसर से संपर्क करें और निवेदन करे की एमिग्रेटे वेब साइट का यूजर आईडी और पासवर्ड प्रदान करे जिससे आगे एमिग्रेशन का प्रक्रिया आसानी से किया जा सके";

        //     if($user->mobile != ''){
        //         $url = $api_url."?number=".$user->mobile."&type=text&message=".urlencode($msgWH)."&instance_id=".$ins."&access_token=".$acc_token;
        //         $ch = curl_init();
        //         curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //         curl_setopt($ch,CURLOPT_URL,$url);
        //         $result =  curl_exec($ch);
        //         echo $result;
        //         curl_close($ch);
        //     }

        //     // Send Mail
        //     if($user->email != ''){
        //         Mail::to($user->email)->send(new EmigrationAlxMail($this->emig_camp,$this->party));
        //     }
        // }
    }
}
