<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Partner Activity alert email to CRM staff (App\Support\PartnerActivity).
 * $context: event, activity, subject, partner_*, team_member, candidate_*,
 * occurred_at. Twin file in both apps.
 */
class PartnerActivityAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $context)
    {
    }

    public function envelope()
    {
        return new Envelope(
            subject: $this->context['subject'] . ' - ' . $this->context['partner_name'] . ' / ' . $this->context['candidate_ref'],
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.partner-activity-alert',
            with: ['alert' => $this->context],
        );
    }

    public function attachments()
    {
        return [];
    }
}
