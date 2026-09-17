<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Leadnote;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;

class ReassignUnfollowedLeads extends Command
{
    protected $signature = 'leads:reassign-unfollowed';

    protected $description = 'Reassign today leads having no followup till 2:30 PM';

    public function handle()
    {
        Log::channel('lead_followup_reassign')->info('Lead Reassignment Cron Started');

        $today = Carbon::today();

        /**
         * Admins who have taken at least one followup today
         */
        $activeAdminIds = Leadnote::whereDate('created_at', $today)
            ->whereHas('admin', function ($q) {
                $q->whereJsonContains('role', 'Candidate Source');
            })
            ->distinct()
            ->pluck('admin_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
            
        if (empty($activeAdminIds)) {

            Log::channel('lead_followup_reassign')->warning(
                'No active followup users found today.'
            );

            $this->info('No active followup users found.');

            return Command::SUCCESS;
        }

        /**
         * Leads having followup today
         */
        $leadIdsWithFollowup = Leadnote::whereDate('created_at', $today)
            ->distinct()
            ->pluck('lead_id')
            ->toArray();

        /**
         * Today's leads with no followup
         */
        $leads = Lead::whereDate('lead_date', $today)
            ->whereNotIn('id', $leadIdsWithFollowup)
            ->get();

        if ($leads->isEmpty()) {

            Log::channel('lead_followup_reassign')->info(
                'No unfollowed leads found.'
            );

            $this->info('No unfollowed leads found.');

            return Command::SUCCESS;
        }

        /**
         * Current lead counts for active users
         */
        $leadCounts = Lead::whereDate('lead_date', $today)
            ->whereIn('leadassign_id', $activeAdminIds)
            ->selectRaw('leadassign_id, COUNT(*) as total')
            ->groupBy('leadassign_id')
            ->pluck('total', 'leadassign_id')
            ->toArray();

        foreach ($activeAdminIds as $adminId) {
            $leadCounts[$adminId] = $leadCounts[$adminId] ?? 0;
        }

        $reassignedCount = 0;

        $autometanotifications = DB::table('autometanotifications')
            ->where('trigger_template_type', 'lead_assign')
            ->where('status', 1)
            ->get();

        foreach ($leads as $lead) {

            $availableAdmins = collect($leadCounts)
                ->except($lead->leadassign_id)
                ->toArray();

            if (empty($availableAdmins)) {
                continue;
            }

            asort($availableAdmins);

            $assignAdminId = array_key_first($availableAdmins);

            $oldAdmin = $lead->leadassign_id;

            $lead->update([
                'leadassign_id' => $assignAdminId
            ]);

            $leadCounts[$assignAdminId]++;

            $reassignedCount++;

            Log::channel('lead_followup_reassign')->info(
                'Lead Reassigned',
                [
                    'lead_id'     => $lead->id,
                    'old_admin'   => $oldAdmin,
                    'new_admin'   => $assignAdminId,
                ]
            );

            // Send WhatsApp template(s)
            foreach ($autometanotifications as $notification) {
                Helper::sendWhatsappAssignTemplate($lead, $notification);
            }

            $sendmail = Helper::sendLeadAssignMail($lead);

        }

        Log::channel('lead_followup_reassign')->info(
            'Lead Reassignment Cron Completed',
            [
                'total_reassigned' => $reassignedCount
            ]
        );

        $this->info(
            "Lead Reassignment Completed. Total Reassigned: {$reassignedCount}"
        );

        return Command::SUCCESS;
    }
}