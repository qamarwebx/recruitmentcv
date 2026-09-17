<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\PartyWelcomeMail;

class PartyWelcomeMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $post,$getAPI;

    public function __construct($post,$getAPI)
    {
        $this->post = $post;
        $this->getAPI = $getAPI;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Send mail
        if($this->post['pty_id'] != 200){
            if($this->post['pty_email'] != ''){
                Mail::to($this->post['pty_email'])->send(new PartyWelcomeMail($this->post));
            }

            if($this->post['sec_email'] != ''){
                Mail::to($this->post['sec_email'])->send(new PartyWelcomeMail($this->post));
            }
        }

        // Send Whatsapp
        // API Details
        $ins = $this->getAPI['instance_key'];
        $api = $this->getAPI['api_key'];

        $txt_url = $this->getAPI['text_message_url'];

        $msgbody = "Dear ".$this->post['pty_full_name']."\n".$this->post['pty_ag_name']."\n\nWelcome to Qamar International, your trusted partner for Visa Endorsement and immigration services. We're delighted to have you on board!\n\nOur commitment is to provide you with a seamless experience throughout your Visa Endorsement journey. We understand the importance of this process for you, and we're here to ensure it's smooth, efficient, and hassle-free.\n\nIf you have any questions or need assistance at any stage, please don't hesitate to reach out to us. Your satisfaction is our top priority.\n\nThank you for choosing Qamar International. We look forward to serving you with excellence and dedication.\n\nHere are our updated contact details for your convenience:\n\n- Email: visa@qamrintl.com\n- WhatsApp: Musaned and Mofa +918828339090\n- Mobile:+919004441088 /+919004200560\n\nWarm regards,\nKhursheed Khan / Juned Khan\n*Qamr International*\nVisa and Immigration services";
    
        if($this->post['pty_id'] != 200){

            if($this->post['pty_comp_contact'] != ''){
                $send_url = $txt_url."?number=".$this->post['pty_comp_contact']."&type=text&message=".urlencode($msgbody)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$send_url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            if($this->post['pty_contact_no'] != ''){
                $send_url = $txt_url."?number=".$this->post['pty_contact_no']."&type=text&message=".urlencode($msgbody)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$send_url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            if($this->post['p_mobile'] != ''){
                $send_url = $txt_url."?number=".$this->post['p_mobile']."&type=text&message=".urlencode($msgbody)."&instance_id=".$ins."&access_token=".$api;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$send_url);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

        }
    }
}
