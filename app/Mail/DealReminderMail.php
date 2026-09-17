<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class DealReminderMail extends Mailable
{
    public $deal;
    public $user;
    public $description;

    public function __construct($deal, $user, $description)
    {
        $this->deal = $deal;
        $this->user = $user;
        $this->description = $description;
    }

    public function build()
    {
        return $this->subject('Deal Reminder Notification')
                ->view('emails.deal_reminder')
                ->with([
                    'deal' => $this->deal,
                    'user' => $this->user,
                    'description' => $this->description,
                ]);
    }
}