<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\CourierNotificationMR;

class CourierNotificationM implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $uploadCN;
    protected $party;
    protected $user;
    protected $branch25;

    public function __construct($uploadCN,$party,$user,$branch25)
    {
        $this->uploadCN = $uploadCN;
        $this->party = $party;
        $this->user = $user; 
        $this->branch25 = $branch25;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        if($this->party['pty_id'] != 200){
            if($this->party['pty_email'] != '' || $this->party['pty_email'] != ''){
                Mail::to($this->party['pty_email'])->send(new CourierNotificationMR($this->uploadCN,$this->party,$this->user,$this->branch25));
            }

            if($this->party['sec_email'] != ''){
                Mail::to($this->party['sec_email'])->send(new CourierNotificationMR($this->uploadCN,$this->party,$this->user,$this->branch25));
            }
        }

        // send mail to care off

        if($this->user['user_id'] != 200){
            if($this->user['email'] != ''){
                Mail::to($this->user['email'])->send(new CourierNotificationMR($this->uploadCN,$this->party,$this->user,$this->branch25));
            }
        }
    }
}

