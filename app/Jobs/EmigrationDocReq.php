<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\EmigrationDocsReq;
use App\User;
use Illuminate\Support\Facades\Mail;

class EmigrationDocReq implements ShouldQueue
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
            $msgWH = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." holding  passport No. ".$this->emig_camp['pass_no']." and ".$this->emig_camp['sponsor_name']." and his ID NO ".$this->emig_camp['sponsor_id']." for applying emigration clearance The documents required is  Sponsor nation id, and Address proof ,also CR copy.it is Our humble request please submit all the mentioned documents So We can do the further process\n\nडिअर ".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." उनका पासपोर्ट नंबर ".$this->emig_camp['pass_no']." और स्पोंसर का नाम ".$this->emig_camp['sponsor_name']." और उनकी आईडी नंबर ".$this->emig_camp['sponsor_id']." एमिग्रेशन क्लेअरन्स करने के लिएआवश्यक स्पोंसर नेशन आईडी एड्रेस प्रूफ और सी आर कॉपी। यह हमारा विनम्र अनुरोध है कि कृपया सभी उल्लेखित दस्तावेज जमा करें, उसके बाद हम आगे की प्रक्रिया कर सकते हैं";
            
            if($this->party['pty_comp_contact'] != ''){
                $url = $api_url."?number=".$this->party['pty_comp_contact']."&type=text&message=".urlencode($msgWH)."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            if($this->party['pty_contact_no'] != ''){
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
                Mail::to($this->party['pty_email'])->send(new EmigrationDocsReq($this->emig_camp,$this->party));
            }

            if($this->party['sec_email'] != ''){
                Mail::to($this->party['sec_email'])->send(new EmigrationDocsReq($this->emig_camp,$this->party));
            }
        }

        

        // If care of is not null then send message or whatsapp
        $user = User::where('user_id','=',$this->emig_post['careoff_id'])->where('user_id','!=','')->where('user_id','!=',200)->first();
        if (isset($user)) {
            // Send Whatsapp
            $msgWH = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." holding  passport No. ".$this->emig_camp['pass_no']." and ".$this->emig_camp['sponsor_name']." and his ID NO ".$this->emig_camp['sponsor_id']." for applying emigration clearance The documents required is  Sponsor nation id, and Address proof ,also CR copy.it is Our humble request please submit all the mentioned documents So We can do the further process\n\nडिअर ".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." उनका पासपोर्ट नंबर ".$this->emig_camp['pass_no']." और स्पोंसर का नाम ".$this->emig_camp['sponsor_name']." और उनकी आईडी नंबर ".$this->emig_camp['sponsor_id']." एमिग्रेशन क्लेअरन्स करने के लिएआवश्यक स्पोंसर नेशन आईडी एड्रेस प्रूफ और सी आर कॉपी। यह हमारा विनम्र अनुरोध है कि कृपया सभी उल्लेखित दस्तावेज जमा करें, उसके बाद हम आगे की प्रक्रिया कर सकते हैं";
            
            if($user->mobile != ''){
                $url = $api_url."?number=".$user->mobile."&type=text&message=".urlencode($msgWH)."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            if($user->email != ''){
                Mail::to($user->email)->send(new EmigrationDocsReq($this->emig_camp,$this->party));
            }
        }

        // if ($this->emig_post['careoff_id'] != '' || $this->emig_post['careoff_id'] != 200) {
        //     $user = User::where('user_id','=',$this->emig_post['careoff_id'])->first();

        //     // Send Whatsapp
        //     $msgWH = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." holding  passport No. ".$this->emig_camp['pass_no']." and ".$this->emig_camp['sponsor_name']." and his ID NO ".$this->emig_camp['sponsor_id']." for applying emigration clearance The documents required is  Sponsor nation id, and Address proof ,also CR copy.it is Our humble request please submit all the mentioned documents So We can do the further process\n\nडिअर ".$this->party['pty_ag_name']."\n".$this->emig_camp['cand_name']." उनका पासपोर्ट नंबर ".$this->emig_camp['pass_no']." और स्पोंसर का नाम ".$this->emig_camp['sponsor_name']." और उनकी आईडी नंबर ".$this->emig_camp['sponsor_id']." एमिग्रेशन क्लेअरन्स करने के लिएआवश्यक स्पोंसर नेशन आईडी एड्रेस प्रूफ और सी आर कॉपी। यह हमारा विनम्र अनुरोध है कि कृपया सभी उल्लेखित दस्तावेज जमा करें, उसके बाद हम आगे की प्रक्रिया कर सकते हैं";
            
        //     if($user->mobile != ''){
        //         $url = $api_url."?number=".$user->mobile."&type=text&message=".urlencode($msgWH)."&instance_id=".$ins."&access_token=".$acc_token;
        //         $ch = curl_init();
        //         curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //         curl_setopt($ch,CURLOPT_URL,$url);
        //         $result =  curl_exec($ch);
        //         echo $result;
        //         curl_close($ch);
        //     }

        //     if($user->email != ''){
        //         Mail::to($user->email)->send(new EmigrationDocsReq($this->emig_camp,$this->party));
        //     }

        // }

    }
}
