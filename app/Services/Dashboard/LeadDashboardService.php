<?php

namespace App\Services\Dashboard;

use App\Models\Lead;
use App\Models\Admin;
use App\Models\StageHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Read-only aggregate queries over the Leads module (app/Models/Lead.php,
 * table `leads`) for the CRM dashboard. Mirrors the column/status semantics
 * used by LeadController (is_qualified enum, leadassign_id, staff_updated_at)
 * without touching that controller's own logic.
 *
 * "Needs follow-up" definitions are an operational SLA (not a real column):
 *   - unworked        = is_qualified IS NULL (never actioned)
 *   - no_followup     = lead has zero Leadnote rows (never noted)
 *   - needs_followup  = is_qualified IN (1,2) i.e. Followed Up / Call Not
 *                       Connected — open, non-terminal
 *   - overdue         = needs_followup AND staff_updated_at older than
 *                       FOLLOWUP_SLA_DAYS
 *   - due_today       = needs_followup AND staff_updated_at aged exactly to
 *                       the SLA boundary today
 * Terminal states (3 Qualified, 4 Not Qualified, 5 Not Relevant) never count
 * as needing follow-up.
 */
class LeadDashboardService
{
    public const FOLLOWUP_SLA_DAYS = 2;

    // Single source of truth lives on the model — see Lead::QUALIFIED_STATUS_LABELS.
    public const STATUS_LABELS = \App\Models\Lead::QUALIFIED_STATUS_LABELS;

    protected function baseQuery(?int $scopeAdminId)
    {
        $q = Lead::query();
        if ($scopeAdminId) {
            $q->where('leadassign_id', $scopeAdminId);
        }
        return $q;
    }

    public function kpis(?int $scopeAdminId = null): array
    {
        $threshold = Carbon::now()->subDays(self::FOLLOWUP_SLA_DAYS);
        $dueTodayStart = $threshold->copy()->startOfDay();
        $dueTodayEnd = $threshold->copy()->endOfDay();

        $base = $this->baseQuery($scopeAdminId);

        return [
            'total_assigned' => (clone $base)->count(),
            'unworked' => (clone $base)->whereNull('is_qualified')->count(),
            'without_followup' => (clone $base)->whereDoesntHave('notes')->count(),
            'needs_followup' => (clone $base)->whereIn('is_qualified', [1, 2])->count(),
            'overdue_followup' => (clone $base)->whereIn('is_qualified', [1, 2])
                ->where('staff_updated_at', '<', $threshold)->count(),
            'due_today_followup' => (clone $base)->whereIn('is_qualified', [1, 2])
                ->whereBetween('staff_updated_at', [$dueTodayStart, $dueTodayEnd])->count(),
            'reassigned' => (clone $base)->where('is_reassigned', 1)->count(),
            'previous_lead' => (clone $base)->where('is_repeted', 1)->count(),
            'qualified' => (clone $base)->where('is_qualified', 3)->count(),
            'recently_assigned' => (clone $base)->where('lead_date', '>=', Carbon::now()->subDays(2))->count(),
            'avg_unworked_age_days' => (int) round(
                (clone $base)->whereNull('is_qualified')->selectRaw('AVG(DATEDIFF(NOW(), lead_date)) as a')->value('a') ?? 0
            ),
        ];
    }

    public function statusBreakdown(?int $scopeAdminId = null, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $q = $this->baseQuery($scopeAdminId);
        if ($from && $to) {
            $q->whereBetween('lead_date', [$from, $to]);
        }

        $rows = $q->select(DB::raw('COALESCE(is_qualified,0) as status_id'), DB::raw('COUNT(*) as total'))
            ->groupBy('status_id')
            ->pluck('total', 'status_id');

        $out = [];
        foreach (self::STATUS_LABELS as $id => $label) {
            $out[] = ['id' => $id, 'label' => $label, 'total' => (int) ($rows[$id] ?? 0)];
        }
        return $out;
    }

    public function trend(Carbon $from, Carbon $to, ?int $scopeAdminId = null): array
    {
        $q = $this->baseQuery($scopeAdminId)
            ->whereBetween('lead_date', [$from, $to])
            ->select(DB::raw('DATE(lead_date) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')
            ->orderBy('d');

        return $q->pluck('total', 'd')->toArray();
    }

    /**
     * "Qualified Leads" sourced from stage_histories — leads that actually
     * BECAME Qualified during this period, not (per statusBreakdown()) leads
     * that are currently Qualified and happened to be created in this
     * period, a different and less accurate question. Scoped by the lead's
     * current leadassign_id, matching every other method on this service.
     */
    public function qualifiedMovementTrend(Carbon $from, Carbon $to, ?int $scopeAdminId = null): array
    {
        $leadIds = $scopeAdminId
            ? Lead::where('leadassign_id', $scopeAdminId)->pluck('id')
            : null;

        return StageHistory::where('trackable_type', 'lead')
            ->where('to_value', '3')
            ->whereBetween('created_at', [$from, $to])
            ->when($leadIds, fn ($q) => $q->whereIn('trackable_id', $leadIds))
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')->pluck('total', 'd')->toArray();
    }

    /**
     * Ranked "needs action now" list for the staff Action Center. Unworked
     * leads first (oldest = most urgent), then reassigned, then overdue
     * follow-ups, capped at $limit.
     */
    public function actionCenterItems(int $adminId, int $limit = 8)
    {
        $threshold = Carbon::now()->subDays(self::FOLLOWUP_SLA_DAYS);

        return Lead::where('leadassign_id', $adminId)
            ->where(function ($w) use ($threshold) {
                $w->whereNull('is_qualified')
                    ->orWhere('is_reassigned', 1)
                    ->orWhere(function ($w2) use ($threshold) {
                        $w2->whereIn('is_qualified', [1, 2])->where('staff_updated_at', '<', $threshold);
                    });
            })
            ->orderByRaw('
                CASE
                    WHEN is_qualified IS NULL THEN 0
                    WHEN is_reassigned = 1 THEN 1
                    ELSE 2
                END ASC
            ')
            ->orderBy('lead_date', 'ASC')
            ->limit($limit)
            ->get(['id', 'cand_name', 'mob_no', 'whatsapp_no', 'required_service', 'is_qualified', 'is_reassigned', 'lead_date', 'staff_updated_at']);
    }

    /**
     * Per-staff activity for the Staff Performance ranking / unified work
     * report. $staffIds should be active non-admin Admin ids.
     */
    public function staffActivity(array $staffIds, Carbon $from, Carbon $to): array
    {
        if (empty($staffIds)) {
            return [];
        }

        $assigned = Lead::whereIn('leadassign_id', $staffIds)
            ->whereBetween('lead_date', [$from, $to])
            ->select('leadassign_id', DB::raw('COUNT(*) as total'))
            ->groupBy('leadassign_id')->pluck('total', 'leadassign_id');

        $worked = Lead::whereIn('leadassign_id', $staffIds)
            ->whereBetween('staff_updated_at', [$from, $to])
            ->select('leadassign_id', DB::raw('COUNT(*) as total'))
            ->groupBy('leadassign_id')->pluck('total', 'leadassign_id');

        $converted = Lead::whereIn('leadassign_id', $staffIds)
            ->where('is_qualified', 3)
            ->whereBetween('staff_updated_at', [$from, $to])
            ->select('leadassign_id', DB::raw('COUNT(*) as total'))
            ->groupBy('leadassign_id')->pluck('total', 'leadassign_id');

        $notes = DB::table('leadnotes')
            ->whereIn('admin_id', $staffIds)
            ->whereBetween('created_at', [$from, $to])
            ->select('admin_id', DB::raw('COUNT(*) as total'))
            ->groupBy('admin_id')->pluck('total', 'admin_id');

        $out = [];
        foreach ($staffIds as $id) {
            $out[$id] = [
                'leads_assigned' => (int) ($assigned[$id] ?? 0),
                'leads_worked' => (int) ($worked[$id] ?? 0),
                'leads_converted' => (int) ($converted[$id] ?? 0),
                'followups_logged' => (int) ($notes[$id] ?? 0),
            ];
        }
        return $out;
    }
}
