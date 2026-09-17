<?php

namespace App\Console\Commands;

use App\AdminModel\Userwhatsappapi;
use Illuminate\Console\Command;

class AutoGreeting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:greeting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;

        $getAPI = Userwhatsappapi::where('api_for','=','Visa Service')->where('status','=','1')->first();

        $whmsg = "Dear Kalam Shaikh welcom to our hotel.";
        $phone = "919324838205";
        $url = $getAPI->text_message_url."?number=".$phone."&type=text&message=".urlencode($whmsg)."&instance_id=".$getAPI->instance_key."&access_token=".$getAPI->api_key;

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_URL,$url);
        $result = curl_exec($ch);
        echo $result;
        curl_close($ch);
    }
}
