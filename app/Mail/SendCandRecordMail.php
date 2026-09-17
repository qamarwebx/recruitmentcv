<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCandRecordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $candidate,$party;

    public function __construct($candidate,$party)
    {
        $this->candidate = $candidate;
        $this->party = $party;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    
    public function build()
    {
        return $this->view('mail.sendrecordmail')->subject($this->candidate['cand_fname']." ".$this->candidate['cand_lname']." and ".$this->candidate['cand_passport_no']." passport information has been recently updated");
    }
}
