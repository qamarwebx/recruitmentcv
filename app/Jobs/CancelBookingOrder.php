<?php

namespace App\Jobs;

use App\Models\Country;
use App\Models\Templatecampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CancelBookingOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $userphone;
    protected $whatsappAPI;
    protected $candDet;

    public function __construct($userphone,$whatsappAPI,$candDet)
    {
        $this->userphone = $userphone;
        $this->whatsappAPI = $whatsappAPI;
        $this->candDet = $candDet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get API List Details and Send Message
        $ins_id = $this->whatsappAPI['instance_id'];
        $acc_token = $this->whatsappAPI['access_token'];
        $api_url = $this->whatsappAPI['api_url'];

        // Get Order Confirm Table
        $template = Templatecampaign::where('template_name','=','Order Confirmation')->first();
        
        // Get phone code from country ID
        $getCont = Country::where('id','=',$this->userphone['country_id'])->first();
        $phone = $getCont->country_code.''.$this->userphone['mobile_no'];
        
        $sendURL = $api_url."?number=".$phone."&type=text&message=".urlencode($template->msg_whatsapp)."&instance_id=".$ins_id."&access_token=".$acc_token;

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_URL,$sendURL);
        $result = curl_exec($ch);
        echo $result;
        curl_close($ch);


    }
}
