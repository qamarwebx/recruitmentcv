<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The email of a RecruitmentCV notification event (App\Support\
 * NotificationCenter). $data: recipient_name, brand (name, logo, site_url),
 * title, message, lines ([[label, value], ...]), action ([label, url]),
 * note, occurred_at. Twin file in both apps.
 */
class RecruitmentNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $mailSubject, public array $data)
    {
    }

    public function envelope()
    {
        return new Envelope(subject: $this->mailSubject);
    }

    public function content()
    {
        return new Content(view: 'emails.recruitment-notification', with: ['n' => $this->data, 'subjectLine' => $this->mailSubject]);
    }

    public function attachments()
    {
        return [];
    }
}
