<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNoTodoLast24HoursDeactivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $date;
    public $todoIds;

    public function __construct($admin, $date, $todoIds = [])
    {
        $this->admin   = $admin;
        $this->date    = $date;
        $this->todoIds = $todoIds;
    }

    public function envelope()
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Account Deactivated – No Todo Created in Last 24 Hours'
        );
    }

    public function content()
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.admin_no_todo_last_24h_deactivated'
        );
    }

    public function attachments()
    {
        return [];
    }
}
