<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MusanedRegister3 extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $filename,$mesSub,$data;

    public function __construct($filename,$mesSub,$data)
    {
        $this->filename = $filename;
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
        return $this->view('mail.musanedReg3')->with('data',$this->data)->subject($this->mesSub)->attach($this->filename);
    }
}
