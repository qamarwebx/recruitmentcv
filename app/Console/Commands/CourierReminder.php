<?php

namespace App\Console\Commands;

use App\AdminModel\Courierclone;
use App\AdminModel\CourierReminder as AdminModelCourierReminder;
use App\AdminModel\CourierReminderList;
use App\AdminModel\Userwhatsappapi;
use App\Mail\CourierReminderMail2525;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CourierReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:courierreminder';

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

        $posts = CourierReminderList::where('status','=',0)->get();

        if (isset($posts)) {
            foreach ($posts as $post) {
                // Get Courier details
                $courier = Courierclone::where('courier_id','=',$post->courier_id)->first();
                // Get User details   
                $user = User::where('user_id','=',$post->user_id)->first();

                // Send Whatsapp
                $msg = "Dear ".$user->name."\nPlease complete the courier ".$post->stage_name." status of ".$courier->cand_fullname." having passport no ".$courier->pass_no;

                // Get API Details
                $getAPI = Userwhatsappapi::where('api_for','Visa Service')->where('status','=',1)->first();

                $ins = $getAPI->instance_key;
                $acc_token = $getAPI->api_key;
                $api_url = $getAPI->text_message_url;

                $sendURL = $api_url."?number=".$user->mobile."&type=text&message=".urlencode($msg)."&instance_id=".$ins."&access_token=".$acc_token;
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch,CURLOPT_URL,$sendURL);
                $result =  curl_exec($ch);
                echo $result;
                curl_close($ch);

                // Send Mail
                Mail::to($user->email)->send(new CourierReminderMail2525($post,$courier,$user));
            }
        }

        return 0;
    }
}
