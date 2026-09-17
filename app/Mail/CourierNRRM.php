<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourierNRRM extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $uploadCN,$party,$user;

    public function __construct($uploadCN,$party,$user)
    {
        $this->uploadCN = $uploadCN;
        $this->party = $party;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.courierdrrn')->subject('Request Received for Courier');
    }
}
