<?php

namespace App\Console\Commands;

use App\Models\Contactsendwhatsapp;
use Illuminate\Console\Command;

class ContactWhatsappScheduled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:contactsend';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $posts = Contactsendwhatsapp::where('send_type','1')->where('campaign_type','=',2)->get();

        return Command::SUCCESS;
    }
}
