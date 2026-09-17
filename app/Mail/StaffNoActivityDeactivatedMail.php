<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffNoActivityDeactivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $todayDate;
    public $todoIds;

    public function __construct($user, $todayDate, $todoIds)
    {
        $this->user = $user;
        $this->todayDate = $todayDate;
        $this->todoIds = $todoIds;
    }

    public function envelope()
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Account Deactivated - No Activity Recorded Today'
        );
    }

    public function content()
    {
        return new \Illuminate\Mail\Mailables\Content(
            html: 'emails.staff_no_activity_deactivated',
        );
    }

    public function attachments()
    {
        return [];
    }
}
