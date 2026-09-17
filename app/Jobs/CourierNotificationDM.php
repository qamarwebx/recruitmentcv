<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\CourierNotificationDisM;

class CourierNotificationDM implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    
     protected $courier_not;
     protected $getAPI;
     protected $getarty;
     protected $getUser;
     protected $getCsn;

    public function __construct($courier_not,$getAPI,$getarty,$getUser,$getCsn)
    {
        $this->courier_not = $courier_not;
        $this->getAPI = $getAPI;
        $this->getarty = $getarty;
        $this->getUser = $getUser;
        $this->getCsn = $getCsn;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        if($this->getarty['pty_id'] != 200){
            if($this->getarty['pty_email'] != ''){
                Mail::to($this->getarty['pty_email'])->send(new CourierNotificationDisM($this->courier_not,$this->getarty,$this->getUser,$this->getCsn));
            }

            if($this->getarty['sec_email'] != ''){
                Mail::to($this->getarty['sec_email'])->send(new CourierNotificationDisM($this->courier_not,$this->getarty,$this->getUser,$this->getCsn));
            }
        }

        if($this->getUser['user_id'] != 200){
            if($this->getUser['email'] != ''){
                Mail::to($this->getarty['email'])->send(new CourierNotificationDisM($this->courier_not,$this->getarty,$this->getUser,$this->getCsn));
            }
        }
    }
}
