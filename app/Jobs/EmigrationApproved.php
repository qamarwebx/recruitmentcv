<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmigrationApprovedMail;
use App\User;

class EmigrationApproved implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $campList,$party,$getAPI,$emig_post;

    public function __construct($campList,$party,$getAPI,$emig_post)
    {
        $this->campList = $campList;
        $this->party = $party;
        $this->getAPI = $getAPI;
        $this->emig_post = $emig_post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // API details
        $ins = $this->getAPI['instance_key'];
        $acc_token = $this->getAPI['api_key'];
        $api_url = $this->getAPI['text_message_url'];

        // Media Details
        // $url_path = 'http://crm.qamrintl.com';
        // $url_path =  "http://crm.qamr.in";
        $url_path = url('/');
        $file_path2 = 'image/emigration/employer';
        // $media = $url_path.'/'.$file_path2.'/'.$this->campList['file'];

        $media = url('/image/emigration/employer/'.$this->campList['file']);

        if($this->party['pty_id'] != '200'){
            // Send Whatsapp

            $whmsg = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n\nCongratulation!\n\nEmigration approved of ".$this->campList['cand_name']." having passport no. ".$this->campList['pass_no']."\nKindly find the bolow attachment of emigration sticker";


            if($this->party['pty_comp_contact'] != ''){
                $sendURL = $api_url."?number=".$this->party['pty_comp_contact']."&type=media&message=".urlencode($whmsg)."&media_url=".$media."&filename=".$this->campList['file']."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            if($this->party['pty_contact_no'] != ''){
                $sendURL2 = $api_url."?number=".$this->party['pty_contact_no']."&type=media&message=".urlencode($whmsg)."&media_url=".$media."&filename=".$this->campList['file']."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL2);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            // Send Mail

            if($this->party['pty_email'] != ''){
                Mail::to($this->party['pty_email'])->send(new EmigrationApprovedMail($this->campList,$this->party));
            }

            if($this->party['sec_email'] != ''){
                Mail::to($this->party['sec_email'])->send(new EmigrationApprovedMail($this->campList,$this->party));
            }
        }

        // If care of is not null then send message or whatsapp
        $user = User::where('user_id','=',$this->emig_post['careoff_id'])->where('user_id','!=','')->where('user_id','!=',200)->first();
        if(isset($user)){
            $whmsg = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n\nCongratulation!\n\nEmigration approved of ".$this->campList['cand_name']." having passport no. ".$this->campList['pass_no']."\nKindly find the bolow attachment of emigration sticker";            

            if ($user->mobile != '') {
                $sendURL = $api_url."?number=".$user->mobile."&type=media&message=".urlencode($whmsg)."&media_url=".$media."&filename=".$this->campList['file']."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);
            }

            

            if ($user->email != '') {
                Mail::to($user->email)->send(new EmigrationApprovedMail($this->campList,$this->party));
            }
        }

        // if ($this->emig_post['careoff_id'] != '' || $this->emig_post['careoff_id'] != 200) {
        //     $user = User::where('user_id','=',$this->emig_post['careoff_id'])->first();

        //     $whmsg = "Dear ".$this->party['pty_full_name']."\n".$this->party['pty_ag_name']."\n\nCongratulation!\n\nEmigration approved of ".$this->campList['cand_name']." having passport no. ".$this->campList['pass_no']."\nKindly find the bolow attachment of emigration sticker";

        //     if ($user->mobile != '') {
        //         $sendURL = $api_url."?number=".$user->mobile."&type=media&message=".urlencode($whmsg)."&media_url=".$media."&filename=".$this->campList['file']."&instance_id=".$ins."&access_token=".$acc_token;
        //         $ch = curl_init();
        //         curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //         curl_setopt($ch,CURLOPT_URL,$sendURL);
        //         $result =  curl_exec($ch);
        //         echo $result;
        //         curl_close($ch);
        //     }

        //     if ($user->email != '') {
        //         Mail::to($user->email)->send(new EmigrationApprovedMail($this->campList,$this->party));
        //     }

        // }

    }
}
