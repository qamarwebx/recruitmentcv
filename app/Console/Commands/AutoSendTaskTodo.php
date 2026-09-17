<?php

namespace App\Console\Commands;

use App\AdminModel\Party;
use App\AdminModel\Todo;
use App\Mail\TodoSendMail;
use App\Mail\TodoSendMailP;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AutoSendTaskTodo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:sendTodoTask';

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

        $posts = Todo::where('process_status','=',3)->where('status','!=',3)->get();
        
        if($posts->count() > 0){
            // foreach ($posts as $post) {

            //     if ($post->assignee != '') {
            //         foreach (explode(",",$post->assignee) as $owner_id) {
            //             $userD = User::where('user_id','=',$owner_id)->first();
            //             Mail::to($userD->email)->send(new TodoSendMail($userD,$post));
            //         }   
            //     }
            // }

            // // Party Details
            // if ($post->pty_id != '') {
                    
            //     $party = Party::where('pty_id','=',$post->pty_id)->where('pty_id','!=',200)->first();
            //     if(isset($party)){
            //         Mail::to($party->pty_email)->send(new TodoSendMailP($party,$post));
            //     }
            // }


        }

        return 0;
    }
}
