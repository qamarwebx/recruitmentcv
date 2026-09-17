<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmpFileNumberGenerateMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $emp,$party,$dataNew;

    public function __construct($emp,$party,$dataNew)
    {
        $this->emp = $emp;
        $this->party = $party;
        $this->dataNew = $dataNew;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.filegeneratemail')->subject('Your applicant visa Details file number '.$this->emp['emp_file_no'].' has been generated for your reference.');
    }
}
