<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $cont;
    protected $post;
     
    public function __construct($cont,$post)
    {
        $this->cont = $cont;
        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $url2 = 'http://whatsapi.smsinsta.com/api/send-text';
        $ins = '05fc4dc2935608fb72db8dacba987eada2c6b94bf667fe77f7bbad994de02f03';
        $api = 'fe3b062f9606a7e66202d776b1e471f10bd16ae0becc1622a01370bfdbd439e1';

        $data4 = [
            "number" => $this->cont['mobile_no'],
            "msg" => $this->post['desc'],
            "instance" => $ins,
            "apikey" => $api
        ];

        

        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data4));
        curl_setopt($ch, CURLOPT_URL, $url2);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
