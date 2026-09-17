<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCandPassStatusC extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $candidate;
    public $getAPI;
    public $careof;

    public function __construct($candidate,$getAPI,$careof)
    {
        $this->candidate = $candidate;
        $this->getAPI = $getAPI;
        $this->careof = $careof;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.sendcandpassstatusc')->with('data',$this->candidate)->subject('Candidate Store at CRM');
    }
}
