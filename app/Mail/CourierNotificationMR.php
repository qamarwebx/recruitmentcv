<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourierNotificationMR extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $uploadCN,$party,$user,$branch25;

    public function __construct($uploadCN,$party,$user,$branch25)
    {
        $this->uploadCN = $uploadCN;
        $this->party = $party;
        $this->user = $user; 
        $this->branch25 = $branch25;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.courierrn')->subject('Receipt of acknowledgement for received documents');

    }
}
