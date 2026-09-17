<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCandPassStatusP extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $candidate;
    public $getAPI;
    public $party;
    public function __construct($candidate,$getAPI,$party)
    {
        $this->candidate = $candidate;
        $this->getAPI = $getAPI;
        $this->party = $party;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.sendcandpassstatusp')->with('data',$this->candidate)->subject('Candidate Store at CRM');
    }
}
