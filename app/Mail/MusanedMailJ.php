<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MusanedMailJ extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $file_name,$mesSub,$data;

    public function __construct($file_name,$mesSub,$data)
    {
        $this->file_name = $file_name;
        $this->mesSub = $mesSub;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.musanedRegM')->with('data',$this->data)->subject($this->mesSub)->attach($this->file_name);
    }
}
