<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmigrationDocsReq extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $emig_camp,$party;

    public function __construct($emig_camp,$party)
    {
        $this->emig_camp = $emig_camp;
        $this->party = $party;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.emig_docs_req')->subject('Documents required of the '.$this->emig_camp['cand_name'].' / '.$this->emig_camp['pass_no']);
    }
}
