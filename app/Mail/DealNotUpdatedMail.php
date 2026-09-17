<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DealNotUpdatedMail extends Mailable
{
    use SerializesModels;

    public $deal;

    public function __construct($deal)
    {
        $this->deal = $deal;
    }

    public function build()
    {
        return $this->subject('Deal Not Updated Alert')
                    ->view('emails.deal_not_updated');
    }
}