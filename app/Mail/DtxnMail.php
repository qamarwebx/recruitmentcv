<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DtxnMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $dtxnlist,$post,$user,$staff;
    public function __construct($dtxnlist,$post,$user,$staff)
    {
        $this->dtxnlist = $dtxnlist;
        $this->post = $post;
        $this->user = $user;
        $this->staff = $staff;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $filename = public_path('image/accounts').'/'.$this->dtxnlist['file'];
        return $this->view('mail.dailytransaction')->subject('Payment Confirmation Request of UTR no '.$this->post['txn_utr_no'])->attach($filename);
    }
}
