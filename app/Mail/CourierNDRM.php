<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourierNDRM extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $courier_not,$getParty,$getUser,$branch;

    public function __construct($courier_not,$getParty,$getUser,$branch)
    {
        $this->courier_not = $courier_not;
        $this->getParty = $getParty;
        $this->getUser = $getUser;
        $this->branch = $branch;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.courierdrm')->subject('Dispatched Receipt for Courier');
    }
}
