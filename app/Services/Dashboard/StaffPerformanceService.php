<?php

namespace App\Services\Dashboard;

use App\Models\Admin;
use Carbon\Carbon;

/**
 * Combines Lead + Todo + Deal Pipeline activity into one staff-wise work
 * report / performance ranking, per CRM dashboard spec section 8 ("do NOT
 * make this report lead-only"). Staff list mirrors the existing pattern
 * used elsewhere in the app (BackEndController::index): active, non-admin
 * Admins.
 */
class StaffPerformanceService
{
    public function __construct(
        protected LeadDashboardService $leads,
        protected TodoDashboardService $todos,
        protected DealDashboardService $deals,
    ) {
    }

    public function activeStaff(?int $staffId = null)
    {
        return Admin::where('status', 1)
            ->where('user_type', '!=', 1)
            ->when($staffId, fn ($q) => $q->where('id', $staffId))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * One row per active staff member with combined Lead/Todo/Deal activity
     * for the given period, plus a total_activity score used both for the
     * "Staff Performance Ranking" (sorted desc) and the unified work report
     * table (sortable by the UI, unsorted here). Pass $staffId to scope the
     * whole report down to a single staff member (the dashboard's Staff
     * filter) — same shape either way, just fewer rows.
     */
    public function report(Carbon $from, Carbon $to, ?int $staffId = null): array
    {
        $staff = $this->activeStaff($staffId);
        $staffIds = $staff->pluck('id')->all();

        $leadActivity = $this->leads->staffActivity($staffIds, $from, $to);
        $todoActivity = $this->todos->staffActivity($staffIds, $from, $to);
        $dealActivity = $this->deals->staffActivity($staffIds, $from, $to);

        $rows = [];
        foreach ($staff as $s) {
            $l = $leadActivity[$s->id] ?? ['leads_assigned' => 0, 'leads_worked' => 0, 'leads_converted' => 0, 'followups_logged' => 0];
            $t = $todoActivity[$s->id] ?? ['tasks_assigned' => 0, 'tasks_completed' => 0];
            $d = $dealActivity[$s->id] ?? ['deals_handled' => 0, 'deals_won' => 0];

            $totalActivity = $l['leads_worked'] + $l['followups_logged'] + $t['tasks_completed'] + $d['deals_handled'];

            $rows[] = [
                'admin_id' => $s->id,
                'name' => $s->name,
                'leads_assigned' => $l['leads_assigned'],
                'leads_worked' => $l['leads_worked'],
                'leads_converted' => $l['leads_converted'],
                'followups_logged' => $l['followups_logged'],
                'tasks_assigned' => $t['tasks_assigned'],
                'tasks_completed' => $t['tasks_completed'],
                'deals_handled' => $d['deals_handled'],
                'deals_won' => $d['deals_won'],
                'total_activity' => $totalActivity,
            ];
        }

        return $rows;
    }

    public function ranking(Carbon $from, Carbon $to, ?int $staffId = null): array
    {
        $rows = $this->report($from, $to, $staffId);
        usort($rows, fn ($a, $b) => $b['total_activity'] <=> $a['total_activity']);
        return $rows;
    }
}
