<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VisaCopyNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     public $post,$party,$user,$cand_d,$visa_d;

    public function __construct($post,$party,$user,$cand_d,$visa_d)
    {
        $this->post = $post;
        $this->party = $party;
        $this->user = $user;
        $this->cand_d = $cand_d;
        $this->visa_d = $visa_d;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Congratulations! Visa has been stamped Successfully for ".$this->cand_d['cand_fname']." ".$this->cand_d['cand_lname']." ".$this->cand_d['cand_passport_no']; 
        $data = [
            'party_name' => $this->party['pty_full_name'],
            'agency' => $this->party['pty_ag_name'],
            'cand_name' => $this->cand_d['cand_fname']." ".$this->cand_d['cand_lname'],
            'pass_no' => $this->cand_d['cand_passport_no'],
            'stamped_date' => date('d-m-Y',strtotime($this->post['musaned_date'])),
            'visa_no' => $this->visa_d['emp_visa_no'],
            'file' => $this->post['file'],
        ];

        if($this->post['file'] != ''){
            $filename = public_path('status-images/visa_copy').'/'.$this->post['file'];
            return $this->view('mail.visaNotification')->with('data',$data)->subject($subject)->attach($filename);
        }else{
            return $this->view('mail.visaNotification')->with('data',$data)->subject($subject);
        }
    }
}
