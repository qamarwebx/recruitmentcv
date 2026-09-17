<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MofaNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $mesSubject,$data;

    public function __construct($data,$mesSubject)
    {
        // $this->file_name = $file_name;
        $this->mesSubject = $mesSubject;
        $this->data = $data;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.mofaNotication')->with('data',$this->data)->subject($this->mesSubject);
        // return $this->view('mail.mofaNotication')->with('data',$this->data)->subject($this->mesSubject)->attach($this->file_name);
    }
}
