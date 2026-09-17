<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Autoleadassignstaff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:autoleadassignstaff';

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
        // $checkLeads = Lead::whereNull('leadassign_id')->get();
        // $users = Admin::where('user_type','!=',1)->where('lead_assign_status',true)->get();

        // if (count($users) > 0 && count($checkLeads) > 0) {
        //     foreach ($checkLeads as $indx => $checkLead) {
        //         $user = $users[$indx % count($users)];

        //         $checkLead->leadassign_id = $user->id;
        //         $checkLead->save();
        //     }
        // }

        $checkLeads = Lead::whereNull('leadassign_id')->get();
        if (count($checkLeads) > 0) {

            foreach ($checkLeads as $checkLead) {
                $admin = DB::table('admins as admin')
                ->leftJoin('leads as lead','lead.leadassign_id','=','admin.id')
                ->select('admin.id',DB::raw('COUNT(lead.id) as lead_count'))
                ->groupBy('admin.id')
                ->orderBy('lead_count','asc')
                ->where('admin.lead_assign_status',true)
                ->get();

                if (count($admin) > 0) {
                    $assignStaff = $admin->shuffle()->first();

                    $checkLead->leadassign_id = $assignStaff->id;
                    $checkLead->save();
                }
            }


        }

        return Command::SUCCESS;
    }
}
