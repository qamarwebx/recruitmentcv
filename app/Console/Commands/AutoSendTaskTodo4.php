<?php

namespace App\Console\Commands;

use App\AdminModel\Party;
use App\AdminModel\Todo;
use App\AdminModel\Userwhatsappapi;
use App\User;
use Illuminate\Console\Command;

class AutoSendTaskTodo4 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:sendTodoTask4';

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
        $posts = Todo::where('process_status','=',1)->where('status','!=',3)->whereRaw('not find_in_set("Low",tags)')->whereRaw('not find_in_set("Medium",tags)')->whereRaw('not find_in_set("High",tags)')->get();
        
        if($posts->count() > 0){
            // Whatsapp API Details
            $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=','1')->first();
            // $ins = "feef179a6fa64b03e4db8dc9331d18e17ca2a5c5cf9493db55f6d66528b69c60";
            // $api = "1b561d13ce41aed6dcfcc5df3406cb72c7b9d02370954ea5e79825998ea694f8";
            $urlm = "http://whatsapi.smsinsta.com/api/send-text";

            foreach ($posts as $post) {
                if ($post->assignee != '') {
                    foreach (explode(",",$post->assignee) as $owner_id) {
                        $userD = User::where('user_id','=',$owner_id)->first();
                        $whmsg = "Dear ".$userD->name."\nYour ".$post->title." task is pending.Pleae complete this task before ".date('d-m-Y',strtotime($post->due_date))."\n".strip_tags($post->description);
                    

                        $url = $getAPI->text_message_url."?number=91".$userD->mobile."&type=text&message=".urlencode($whmsg)."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result =  curl_exec($ch);
                        echo $result;
                        curl_close($ch);
                        // Mail::to($userD->email)->send(new TodoSendMail($userD,$post));
                    }    
                }
                
                
                // Party Details
                if ($post->pty_id != '') {
                    
                    
                    $party = Party::where('pty_id','=',$post->pty_id)->where('pty_id',$post->pty_id)->first();

                    if(isset($party)){
                        $whmsg = "Dear ".$party->pty_ag_name."\nYour ".$post->title." task is pending.Pleae complete this task before ".date('d-m-Y',strtotime($post->due_date))."\n".strip_tags($post->description);
                    
                        $url = $getAPI->text_message_url."?number=".$party->pty_comp_contact."&type=text&message=".urlencode($whmsg)."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                        curl_setopt($ch,CURLOPT_URL,$url);
                        $result = curl_exec($ch);
                        echo $result;
                        curl_close($ch);
                    }


                }
            }
        }

        return 0;
    }
}
