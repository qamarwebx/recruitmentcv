<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class Autoupdateleadassignstatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:autoupdateleadassignstatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Deactive Lead Assign Status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = Admin::where('lead_assign_status','=',true)->get();

        foreach ($users as $user) {
            $user->lead_assign_status = false;
            $user->save();
        }

        return Command::SUCCESS;
    }
}
