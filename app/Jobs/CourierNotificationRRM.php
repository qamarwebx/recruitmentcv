<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\CourierNRRM;

class CourierNotificationRRM implements ShouldQueue
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

    public function __construct($uploadCN,$party,$user)
    {
        $this->uploadCN = $uploadCN;
        $this->party = $party;
        $this->user = $user;


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->party['pty_id'] != 200){
            if($this->party['pty_email'] != ''){
                Mail::to($this->party['pty_email'])->send(new CourierNRRM($this->uploadCN,$this->party,$this->user));
            }

            if($this->party['sec_email'] != ''){
                Mail::to($this->party['sec_email'])->send(new CourierNRRM($this->uploadCN,$this->party,$this->user));
            }
        }

        if($this->user['user_id'] != 200){
            if($this->user['email'] != ''){
                Mail::to($this->party['email'])->send(new CourierNRRM($this->uploadCN,$this->party,$this->user));
            }
        }
    }
}
