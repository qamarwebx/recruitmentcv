<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmigrationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
     public $campList,$party;

    public function __construct($campList,$party)
    {
        $this->campList = $campList;
        $this->party = $party;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $filename = public_path('image/emigration/employer').'/'.$this->campList['file'];
        return $this->view('mail.emig_appr_message')->subject($this->campList['subject'])->attach($filename);
    }
}
