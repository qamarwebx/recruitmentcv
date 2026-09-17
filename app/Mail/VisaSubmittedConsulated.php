<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VisaSubmittedConsulated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $post,$party,$cand_det,$visa_d;
    public function __construct($post,$party,$cand_det,$visa_d)
    {
        $this->post = $post;
        $this->party = $party;
        $this->cand_det = $cand_det;
        $this->visa_d = $visa_d;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->cand_det['cand_mname'] != ''){
            $cand_name = $this->cand_det['cand_fname'].' '.$this->cand_det['cand_mname'].' '.$this->cand_det['cand_lname'];
        }else{
            $cand_name = $this->cand_det['cand_fname'].' '.$this->cand_det['cand_lname'];
        } 
        $cand_pass = $this->cand_det['cand_passport_no'];
        $visa_no = $this->visa_d['emp_visa_no'];
       
        return $this->view('mail.submittedinconsulate')->subject($cand_name.' '.$cand_pass.' This passport will be submitted to Saudi consulate on '.date('d-m-Y',strtotime($this->post['musaned_date'])));
    }
}
