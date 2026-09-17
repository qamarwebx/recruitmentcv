<?php

namespace App\Console\Commands;

use App\AdminModel\Ipaddress;
use Illuminate\Console\Command;

class AutoupdateIPAdress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:ipaddressupdate';

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

        // Get IP address which is expired
        $posts = Ipaddress::where('expired_at','<',date('Y-m-d H:i:s'))->get();

        if ($posts->count() > 0) {
            foreach ($posts as $post) {
                $updIP = Ipaddress::find($post->id);
                $updIP->status = false;
                $updIP->save();
            }
        }

        return 0;
    }
}
