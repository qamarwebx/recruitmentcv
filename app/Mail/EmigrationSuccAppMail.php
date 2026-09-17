<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmigrationSuccAppMail extends Mailable
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
        if ($this->emig_camp['file'] != '') {
            $filename = public_path('image/emigration/employer').'/'.$this->emig_camp['file'];


            return $this->view('mail.emig_succ_msg')->subject('emigration successful apply of the '.$this->emig_camp['cand_name'].' / '.$this->emig_camp['pass_np'])->attach($filename);
        } else {
            return $this->view('mail.emig_succ_msg')->subject('emigration successful apply of the '.$this->emig_camp['cand_name'].' / '.$this->emig_camp['pass_np']);
        }
        
    }
}
