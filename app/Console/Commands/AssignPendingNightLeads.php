<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Lead;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;

class AssignPendingNightLeads extends Command
{
    protected $signature = 'leads:assign-pending-night';

    protected $description = 'Assign unassigned leads received between yesterday 7 PM and today 10:30 AM';

    /**
     * Max leads one admin can receive in this single run. This cron runs
     * once a day, so without a cap 1-2 early-active admins could absorb the
     * entire overnight backlog in one shot; anything left uncapped simply
     * waits and gets picked up by the next leads:assign-continuously tick
     * during the day (it re-scans all unassigned leads with no date filter).
     */
    protected int $maxLeadsPerAdminPerRun = 5;

    public function handle()
    {
        Log::channel('lead_pending_night_assign')->info(
            'Pending Night Leads Assignment Cron Started'
        );

        $admins = Admin::where('status', 1)
            ->where('login_status', 1)
            ->where('lead_assign_status', 1)
            ->whereJsonContains('role', 'Candidate Source')
            ->get();

        if ($admins->isEmpty()) {
            Log::channel('lead_pending_night_assign')->warning('No active admins found');
            return Command::SUCCESS;
        }

        $startDateTime = Carbon::yesterday()->setTime(19, 0, 0);
        $endDateTime   = Carbon::today()->setTime(10, 30, 0);

        $today = Carbon::today()->toDateString();

        /**
         * Current lead count of each active admin
         */
        $adminIds = $admins->pluck('id')->toArray();

        $leadCounts = Lead::whereDate('lead_date', $today)
            ->whereIn('leadassign_id', $adminIds)
            ->selectRaw('leadassign_id, COUNT(*) as total')
            ->groupBy('leadassign_id')
            ->pluck('total', 'leadassign_id')
            ->toArray();

        foreach ($adminIds as $adminId) {
            $leadCounts[$adminId] = $leadCounts[$adminId] ?? 0;
        }

        $assignedCount = 0;

        // Per-admin count of leads handed out in THIS run only, capped by
        // $maxLeadsPerAdminPerRun — separate from $leadCounts (today's
        // cumulative total), which keeps driving the least-loaded pick.
        $assignedThisRun = array_fill_keys($adminIds, 0);

        $autometanotifications = DB::table('autometanotifications')
            ->where('trigger_template_type', 'lead_assign')
            ->where('status', 1)
            ->get();

        Lead::whereNull('leadassign_id')
            ->whereBetween('lead_date', [$startDateTime, $endDateTime])
            ->orderBy('id')
            ->chunkById(100, function ($leads) use (
                $admins,
                &$leadCounts,
                &$assignedThisRun,
                &$assignedCount,
                $autometanotifications
            ) {

                $capReached = false;

                foreach ($leads as $lead) {

                    // Only consider admins who haven't hit this run's cap yet
                    $available = array_filter(
                        $leadCounts,
                        fn ($count, $adminId) => $assignedThisRun[$adminId] < $this->maxLeadsPerAdminPerRun,
                        ARRAY_FILTER_USE_BOTH
                    );

                    if (empty($available)) {
                        // Everyone hit the per-run cap — leave the rest for
                        // the daytime continuous cron to pick up later
                        $capReached = true;
                        break;
                    }

                    // Always assign to least loaded admin
                    asort($available);

                    $assignAdminId = array_key_first($available);

                    $assignStaff = $admins->firstWhere('id', $assignAdminId);

                    if (!$assignStaff) {
                        continue;
                    }

                    // Atomically claim the lead inside a row lock so a
                    // concurrent run can't also grab it; the actual write
                    // still goes through Eloquent's update() so the
                    // leadassign_id change fires Lead::booted()'s `updated`
                    // event and lands in the assignment activity log (a raw
                    // query-builder update, used here previously, bypasses
                    // that event entirely).
                    $wasAssigned = false;

                    DB::transaction(function () use ($lead, $assignAdminId, &$wasAssigned) {

                        $stillUnassigned = Lead::whereKey($lead->id)
                            ->whereNull('leadassign_id')
                            ->lockForUpdate()
                            ->exists();

                        if (!$stillUnassigned) {
                            return;
                        }

                        $lead->update([
                            'leadassign_id' => $assignAdminId
                        ]);

                        $wasAssigned = true;
                    });

                    if (!$wasAssigned) {
                        continue;
                    }

                    $leadCounts[$assignAdminId]++;
                    $assignedThisRun[$assignAdminId]++;

                    $assignedCount++;

                    Log::channel('lead_pending_night_assign')->info(
                        'Night Lead Assigned',
                        [
                            'lead_id'     => $lead->id,
                            'assigned_to' => $assignAdminId,
                        ]
                    );

                    foreach ($autometanotifications as $notification) {
                        Helper::sendWhatsappAssignTemplate($lead, $notification);
                    }

                    Helper::sendLeadAssignMail($lead);
                }

                if ($capReached) {
                    return false;
                }
            });

        Log::channel('lead_pending_night_assign')->info(
            'Pending Night Leads Assignment Completed',
            [
                'from' => $startDateTime->toDateTimeString(),
                'to' => $endDateTime->toDateTimeString(),
                'total_assigned' => $assignedCount,
            ]
        );

        $this->info("Night leads assignment completed. Assigned: {$assignedCount}");

        return Command::SUCCESS;
    }

    // public function handle()
    // {
    //     Log::channel('lead_pending_night_assign')->info(
    //         'Pending Night Leads Assignment Cron Started'
    //     );

    //     $admins = Admin::where('status', 1)->where('login_status', 1)->where('lead_assign_status', 1)->whereJsonContains('role', 'Candidate Source')->get();

    //     if ($admins->isEmpty()) {

    //         Log::channel('lead_pending_night_assign')->warning(
    //             'No active admins found'
    //         );

    //         return Command::SUCCESS;
    //     }

    //     /**
    //      * Yesterday 7:00 PM
    //      */
    //     $startDateTime = Carbon::yesterday()->setTime(19, 0, 0);

    //     /**
    //      * Today 10:30 AM
    //      */
    //     $endDateTime = Carbon::today()->setTime(10, 30, 0);

    //     $assignedCount = 0;

    //    $autometanotifications = DB::table('autometanotifications')
    //         ->where('trigger_template_type', 'lead_assign')
    //         ->where('status', 1)
    //         ->get();

    //     Lead::whereNull('leadassign_id')
    //         ->whereBetween('lead_date', [
    //             $startDateTime,
    //             $endDateTime
    //         ])
    //         ->chunkById(100, function ($leads) use ($admins, &$assignedCount, $autometanotifications) {

    //             foreach ($leads as $lead) {

    //                 $assignStaff = $admins->random();

    //                 $lead->update([
    //                     'leadassign_id' => $assignStaff->id
    //                 ]);

    //                 $assignedCount++;

    //                 Log::channel('lead_pending_night_assign')->info(
    //                     'Night Lead Assigned',
    //                     [
    //                         'lead_id'     => $lead->id,
    //                         'assigned_to' => $assignStaff->id,
    //                     ]
    //                 );

    //                 // Send WhatsApp template(s)
    //                 foreach ($autometanotifications as $notification) {
    //                     Helper::sendWhatsappAssignTemplate($lead, $notification);
    //                 }

    //                 $sendmail = Helper::sendLeadAssignMail($lead);

    //             }
    //         });

    //     Log::channel('lead_pending_night_assign')->info(
    //         'Pending Night Leads Assignment Completed',
    //         [
    //             'from' => $startDateTime->toDateTimeString(),
    //             'to' => $endDateTime->toDateTimeString(),
    //             'total_assigned' => $assignedCount,
    //         ]
    //     );

    //     $this->info(
    //         "Night leads assignment completed. Assigned: {$assignedCount}"
    //     );

    //     return Command::SUCCESS;
    // }
}