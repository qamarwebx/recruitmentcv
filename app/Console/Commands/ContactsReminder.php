<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AdminModel\ContactReminder;
use App\AdminModel\AllContact;
use App\User;
use App\AdminModel\Userwhatsappapi;

class ContactsReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:contactreminder';

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
        
        // $remparconts = ContactReminder::where('due_date','!=',$cdate)->where('status','=',0)->get();
        $remparconts = ContactReminder::where('due_date','=',$cdate)->where('time','=',$ctime)->where('status','=',0)->get();
        if(isset($remparconts)){
            foreach ($remparconts as $remparcont) {
                // Get contact details
                $contact = AllContact::where('id','=',$remparcont->cont_id)->first();

                // Get user details
                $user = User::where('user_id','=',$remparcont->user_id)->where('user_id','!=',200)->first();

                $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=',1)->first();

                if (isset($user)) {
                    // Message
                    $msg = "All Contact Reminder!\n\nDear ".$user->name."\nTitle: ".$remparcont->title."\n".$remparcont->desc."\n".$remparcont->reminder_type." to ".$contact->full_name." before ".date('d-m-Y',strtotime($remparcont->due_date))." in ".date('h:i A',strtotime($remparcont->time))."\n\n Thank You";
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
