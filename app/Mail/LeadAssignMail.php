<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadAssignMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lead;
    public $admin;

    public function __construct($lead, $admin)
    {
        $this->lead = $lead;
        $this->admin = $admin;
    }

    public function build()
    {
        return $this->subject('New Lead Assigned')
            ->view('emails.lead_assign');
    }
}
