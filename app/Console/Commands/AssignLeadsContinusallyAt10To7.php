<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lead;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helper;

class AssignLeadsContinusallyAt10To7 extends Command
{
    protected $signature = 'leads:assign-continuously';

    protected $description = 'Assign unassigned leads continuously between 10:30 AM and 7:00 PM';

    /**
     * Max leads one admin can receive in a single run. Caps the "catch-up
     * flood" a newly-activated admin would otherwise absorb in one tick when
     * their today-count starts at 0 while others already have leads; the
     * remainder simply waits for the next minute's tick.
     */
    protected int $maxLeadsPerAdminPerRun = 5;

    public function handle()
    {
        Log::channel('lead_assign_10_to_7')->info('Lead assignment cron started');

        // Get active admins
        $admins = Admin::where('status', 1)
            ->where('login_status', 1)
            ->where('lead_assign_status', 1)
            ->whereJsonContains('role', 'Candidate Source')
            ->get();


        if ($admins->isEmpty()) {

            Log::channel('lead_assign_10_to_7')->warning('No active admins found');

            $this->info('No active admins found.');

            return Command::SUCCESS;
        }

        /**
         * Get admin IDs
         */
        $adminIds = $admins->pluck('id')->toArray();

        /**
         * Current lead count of each active admin
         */
        $today = now()->toDateString();
        
        $leadCounts = Lead::whereDate('lead_date', $today)
            ->whereIn('leadassign_id', $adminIds)
            ->selectRaw('leadassign_id, COUNT(*) as total')
            ->groupBy('leadassign_id')
            ->pluck('total', 'leadassign_id')
            ->toArray();


        // Set zero count for admins having no leads
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

        /**
         * Assign all unassigned leads to least loaded admin
         */
       Lead::whereNull('leadassign_id')
            // ->whereDate('lead_date', $today)
            ->orderBy('id')
            ->get()
            ->each(function ($lead) use ($admins, &$leadCounts, &$assignedThisRun, &$assignedCount, $autometanotifications) {

                // Only consider admins who haven't hit this run's cap yet
                $available = array_filter(
                    $leadCounts,
                    fn ($count, $adminId) => $assignedThisRun[$adminId] < $this->maxLeadsPerAdminPerRun,
                    ARRAY_FILTER_USE_BOTH
                );

                if (empty($available)) {
                    // Everyone hit the per-run cap — leave the rest for the next tick
                    return false;
                }

                // Sort by least leads
                asort($available);

                // Get admin with minimum leads
                $assignAdminId = array_key_first($available);

                $assignStaff = $admins->firstWhere('id', $assignAdminId);

                if (!$assignStaff) {
                    return;
                }

                // Atomically claim the lead inside a row lock so a concurrent
                // run (e.g. an overlapping manual invocation) can't also grab
                // it; the actual write still goes through Eloquent's update()
                // so the leadassign_id change fires Lead::booted()'s
                // `updated` event and lands in the assignment activity log.
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
                    return;
                }

                // Update local counts so the next lead in this run stays balanced
                $leadCounts[$assignAdminId]++;
                $assignedThisRun[$assignAdminId]++;

                $assignedCount++;

                Log::channel('lead_assign_10_to_7')->info('Lead Assigned', [
                    'lead_id'     => $lead->id,
                    'assigned_to' => $assignAdminId,
                ]);

                // Send WhatsApp templates
                foreach ($autometanotifications as $notification) {
                    Helper::sendWhatsappAssignTemplate($lead, $notification);
                }

                // Send email
                Helper::sendLeadAssignMail($lead);
            });

        Log::channel('lead_assign_10_to_7')->info('Lead assignment cron completed', [
            'total_assigned' => $assignedCount,
        ]);

        $this->info("Lead assignment completed. Assigned: {$assignedCount}");

        return Command::SUCCESS;
    }

}