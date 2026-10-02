<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Customer "Hire Now" order confirmation (CustomerHireController::store),
 * sent through App\Support\CustomerMail with the booking partner's SMTP.
 * $data: brand (CustomerMail::brand()), customer_name, candidate_name,
 * candidate_profession, candidate_age, candidate_url, reference_no,
 * booking_date, work_city, embassy, orders_url.
 */
class CustomerBookingConfirmation extends Mailable
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
            subject: __('locale.Order Confirmed') . ' - ' . $this->data['reference_no'] . ' | ' . $this->data['brand']['name'],
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.customer.booking-confirmation',
        );
    }

    public function attachments()
    {
        return [];
    }
}
