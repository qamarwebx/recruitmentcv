<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourierReminderMail2525 extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $post,$courier,$user;

    public function __construct($post,$courier,$user)
    {
        $this->post = $post;
        $this->courier = $courier;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.courierreminderdis2525')->subject($this->post['stage'].' update reminder for '.$this->courier['cand_fullname']);
    }
}
