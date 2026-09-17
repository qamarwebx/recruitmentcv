<?php

namespace App\Console\Commands;

use App\AdminModel\Party;
use App\AdminModel\Userwhatsappapi;
use App\User;
use Illuminate\Console\Command;

class PartyReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:partyreminder';

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

        // list of party
        $cdate = date('Y-m-d');
        $ctime = date('h:i');
        // $remparties = PartyReminder::where('due_date','!=',$cdate)->where('status','=',0)->get();
        $remparties = PartyReminder::where('due_date','=',$cdate)->where('time','=',$ctime)->where('status','=',0)->get();
        if (isset($remparties)) {
            foreach ($remparties as $remparty) {
                // Get Party details
                $party = Party::where('pty_id','=',$remparty->pty_id)->first();

                // Get user details
                $user = User::where('user_id','=',$remparty->user_id)->where('user_id','!=',200)->first();

                $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();


                if (isset($user)) {
                    // Message
                    $msg = "Party Reminder!\n\nDear ".$user->name."\nTitle: ".$remparty->title."\n".$remparty->desc."\n".$remparty->reminder_type." to ".$party->pty_ag_name." before ".date('d-m-Y',strtotime($remparty->due_date))." in ".date('h:i A',strtotime($remparty->time))."\n\n Thank You";
                    $url = $getAPI->text_message_url."?number=".$user->mobile."&type=text&message=".urlencode($msg)."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;
                    $ch = curl_init();
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                    curl_setopt($ch,CURLOPT_URL,$url);
                    $result = curl_exec($ch);
                    echo $result;
                    curl_close($ch);
                }

            }
        }

        return 0;
    }
}
