<?php

namespace App\Jobs;

use App\AdminModel\BranchDetails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\CourierNDRM;

class CourierNotificationDRM implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    
    protected $courier_not;
    protected $getParty;
    protected $getUser;

    public function __construct($courier_not,$getParty,$getUser)
    {
        $this->courier_not = $courier_not;
        $this->getParty = $getParty;
        $this->getUser = $getUser;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        // Branch Details
        $branch = BranchDetails::where('br_id','=',$this->courier_not['courier_from'])->first();
        
        // Party is not none
        if($this->getParty['pty_id'] != 200){
            if($this->getParty['pty_email'] != ''){
                Mail::to($this->getParty['pty_email'])->send(new CourierNDRM($this->courier_not,$this->getParty,$this->getUser,$branch));
            }

            if($this->getParty['sec_email'] != ''){
                Mail::to($this->getParty['sec_email'])->send(new CourierNDRM($this->courier_not,$this->getParty,$this->getUser,$branch));
            }
        }
        // Care off is not none
        if($this->getUser['user_id'] != 200){
            if($this->getUser['email'] != ''){
                Mail::to($this->getUser['email'])->send(new CourierNDRM($this->courier_not,$this->getParty,$this->getUser,$this->branch));
            }
        }
    }
}
