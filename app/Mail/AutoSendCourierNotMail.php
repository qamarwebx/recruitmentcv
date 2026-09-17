<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AutoSendCourierNotMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $party,$courier_det,$csn;

    public function __construct($party,$courier_det,$csn)
    {
        $this->party = $party;
        $this->courier_det = $courier_det; 
        $this->csn = $csn;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.couriernotification')->subject('Courier received notification');
    }
}
