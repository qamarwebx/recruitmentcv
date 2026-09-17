<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SinglePartyCompaignListM extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $post,$partyD,$getAPI;

    public function __construct($post,$partyD,$getAPI)
    {
        $this->post = $post;
        $this->partyD = $partyD; 
        $this->getAPI = $getAPI;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        if ($this->post['file'] != '') {

            $filename = public_path('image/whatsapp/'.$this->post['file']);

            return $this->view('mail.singlepartymailsms')->subject($this->post['mailsub'])->attach($filename);
        } else {
            return $this->view('mail.singlepartymailsms')->subject($this->post['mailsub']);
        }
        

    }
}
