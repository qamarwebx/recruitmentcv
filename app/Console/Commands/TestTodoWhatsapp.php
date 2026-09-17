<?php

namespace App\Console\Commands;

use App\AdminModel\Todo;
use App\AdminModel\TodoAssignee;
use App\AdminModel\Userwhatsappapi;
use App\User;
use Illuminate\Console\Command;

class TestTodoWhatsapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:testtodotask';

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
        

        $posts = TodoAssignee::all();
        foreach ($posts as $post) {
            $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=','1')->first();
            $todo = Todo::where('id','=',$post->todo_id)->where('process_status','=',1)->where('status','!=',3)->whereRaw('find_in_set("Low",tags)')->first();
            $userD = User::where('user_id','=',$post->user_id)->first();

            $whmsg = "Dear ".$userD->name."\nYour ".$todo->title." task is pending.Pleae complete this task before ".date('d-m-Y',strtotime($todo->due_date))."\n".strip_tags($todo->description);
            $url = $getAPI->text_message_url."?number=91".$userD->mobile."&type=text&message=".urlencode($whmsg)."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;
            
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch,CURLOPT_URL,$url);
            $result =  curl_exec($ch);
            echo $result;
            curl_close($ch);
        }

        return 0;
    }
}
