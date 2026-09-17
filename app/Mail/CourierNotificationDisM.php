<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CourierNotificationDisM extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $courier_not,$getarty,$getUser,$getCsn; 

    public function __construct($courier_not,$getarty,$getUser,$getCsn)
    {
        $this->courier_not = $courier_not;
        $this->getarty = $getarty;
        $this->getUser = $getUser;
        $this->getCsn = $getCsn;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        

        $filename = public_path('image/service-candidate').'/'.$this->courier_not['file'];

        if ($this->courier_not['file'] != '') {
            return $this->view('mail.courierdn')->subject('Acknowledgement Receipt for Courier')->attach($filename);
        } else {
            return $this->view('mail.courierdn')->subject('Acknowledgement Receipt for Courier');
        }
        

    }
}
