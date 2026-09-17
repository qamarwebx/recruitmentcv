<?php

namespace App\Console\Commands;

use App\AdminModel\CourierDetails;
use App\AdminModel\Couriernotification;
use App\AdminModel\CourierServiceName;
use App\AdminModel\Party;
use App\AdminModel\Userwhatsappapi;
use App\Mail\AutoSendCourierNot as MailAutoSendCourierNot;
use App\Mail\AutoSendCourierNotMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AutoSendCourierNot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:couriernotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $yesterday = now()->subDay()->toDateString();
        $posts = Couriernotification::where('status','=',0)->where('upload_date','<=',$yesterday)->get();
        foreach ($posts as $post) {
            $party = Party::find($post->pty_id);
            $courier_det = CourierDetails::where('courier_id','=',$post->id)->first();
            $csn = CourierServiceName::find($post->csn_id);
            $link_url = "{{ url('courier/update/recieved/'.$courier_det->tracking_id.'/'.$courier_det->courier_id) }}";
            $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=','1')->first();
            $whmsg = "Dear ".$party->pty_full_name."\n".$party->pty_ag_name." courier has been dispatch from Qamr International detail are in below:\nCourier Name:".$csn->name."\nPlease click the below link if you have recieved the courier,\n".$link_url ;
            if ($party->pty_id != 200) {
                if ($party->pty_comp_contact != '') {
                    $url = $getAPI->text_message_url."?number=".$party->pty_comp_contact."&type=text&message=".urlencode($whmsg)."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;    
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result = curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

                if ($party->pty_contact_no != '') {
                    $url = $getAPI->text_message_url."?number=".$party->pty_contact_no."&type=text&message=".urlencode($whmsg)."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;    
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result = curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

                if ($party->p_mobile != '') {
                    $url = $getAPI->text_message_url."?number=".$party->p_mobile."&type=text&message=".urlencode($whmsg)."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;    
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result = curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

                if ($party->pty_email != '') {
                    Mail::to($party->pty_email)->send(new AutoSendCourierNotMail($party,$courier_det,$csn));
                }

            }
        }

        return 0;
    }
}
