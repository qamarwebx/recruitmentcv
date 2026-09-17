<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourierDeliveratOffceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $courierC,$getPrty,$branch;

    public function __construct($courierC,$getPrty,$branch)
    {
        $this->courierC = $courierC;
        $this->getPrty = $getPrty;
        $this->branch = $branch;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.courierdao')->subject('Documents of '.$this->courierC['cand_fname'].' '.$this->courierC['cand_lname'].' and '.$this->courierC['pass_no'].' were successfully delivered');
    }
}
