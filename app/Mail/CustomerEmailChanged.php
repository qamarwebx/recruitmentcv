<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to a customer's NEW email address once the existing email-change
 * OTP (DashboardController::getEmailOTPandUpdateValidate/Ar) has been
 * verified and saved - never before. Through App\Support\CustomerMail
 * with the current site partner's SMTP.
 * $data: brand (CustomerMail::brand()), customer_name, new_email,
 * changed_at, account_url.
 */
class CustomerEmailChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function envelope()
    {
        return new Envelope(
            subject: __('locale.Your email address has been changed') . ' | ' . $this->data['brand']['name'],
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.customer.email-changed',
        );
    }

    public function attachments()
    {
        return [];
    }
}
