<?php

namespace App\Jobs;

use App\AdminModel\Party;
use App\AdminModel\PaymentForAcc;
use App\AdminModel\PaymentMethod;
use App\AdminModel\Userwhatsappapi;
use App\Mail\DtxnMail;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class DailyTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $dtxnlist,$post,$staff;


    public function __construct($dtxnlist,$post,$staff)
    {
        $this->dtxnlist = $dtxnlist; 
        $this->post = $post;
        $this->staff = $staff;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Whatsapp API
        $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();

        // Primary USER
        $user = User::where('user_id','=',1)->first();

        // Party Details

        if ($this->dtxnlist['pty_ag_name'] != '') {
            $party_name = $this->dtxnlist['pty_ag_name'];
        } else {
            $party_name = '---';
        }

        // Payment Method
        if ($this->dtxnlist['paymethod'] != '') {
            $payment_name = $this->dtxnlist['paymethod'];
        } else {
            $payment_name = '---';
        }
        
        // Payment For
        if ($this->dtxnlist['payfor'] != '') {
            $payment_for_acc = $this->dtxnlist['payfor'];
        } else {
            $payment_for_acc = '---';
        }

        // Candidate Name
        if ($this->dtxnlist['cand_name'] != '') {
            $cand_name = $this->dtxnlist['cand_name'];
        }else{
            $cand_name = '---';
        }

        // Passport No
        if ($this->dtxnlist['pass_no'] != '') {
            $pass_no = $this->dtxnlist['pass_no'];
        }else{
            $pass_no = '---';
        }

        // $message details
        $msg = "Payment Confirmation Request\n\nDear ".$user->name."\nDetails are below:\nParty Name: ".$party_name."\nCandidate Name: ".$cand_name."\nPassport No: ".$pass_no."\nPayment For: ".$payment_for_acc."\nPayment Method: ".$payment_name."\nAmount: ".$this->post['amount']."\nTransacation ID: ".$this->post['txn_utr_no']."\nTransaction Date: ".date('d-m-Y',strtotime($this->post['txn_date']))."\nCreated By: ".$this->staff['name'];

        // $msg = "Payment Confirmation Request\n\nDear ".$user->name."\nDetails are below:\nParty Name: ".$party_name."\nCandidate Name: ".$cand_name."\nPassport No: ".$pass_no."\nAmount: ".$this->post['amount']."\nTransacation ID: ".$this->post['txn_utr_no']."\nTransaction Date: ".date('d-m-Y',strtotime($this->post['txn_date']));

        // $url_path = 'http://crm.qamrintl.com';
        // $url_path =  "http://crm.qamr.in";
        $url_path = url('/');
        $file_path2 = 'image/accounts';
        // $media = $url_path.'/'.$file_path2.'/'.$this->dtxnlist['file'];

        $media = url('/image/accounts/'.$this->dtxnlist['file']);

        $url = $getAPI->text_message_url."?number=".$user->mobile."&type=media&message=".urlencode($msg)."&media_url=".$media."&filename=".$this->dtxnlist['file']."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;
        // $url = $getAPI->text_message_url."?number=919594785319&type=media&message=".urlencode($msg)."&media_url=".$media."&filename=".$this->dtxnlist['file']."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_URL,$url);
        $result = curl_exec($ch);
        echo $result;
        curl_close($ch);

        // Mail Send
        Mail::to($user->email)->send(new DtxnMail($this->dtxnlist,$this->post,$user,$this->staff));
    }
}
