<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SinglePartyCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $post,$partyD;
    public function __construct($post,$partyD)
    {
        $this->post = $post;
        $this->partyD = $partyD;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->post['file'] != ''){
            $filename = public_path('image/whatsapp').'/'.$this->post['file'];
            return $this->view('mail.singlepartymailc')->subject($this->post['mailsub'])->attach($filename);
        }else{
            return $this->view('mail.singlepartymailc')->subject($this->post['mailsub']);
        }
    }
}
