<?php

namespace App\Services\Dashboard;

use App\Models\Allcontactnote;
use App\Models\DealNote;
use App\Models\DealPipeline;
use App\Models\Lead;
use App\Models\Leadnote;
use App\Models\StageHistory;
use Carbon\Carbon;

/**
 * Cross-module "what did this person do" report — no unified activity/
 * timeline table exists anywhere in this app, so this is a genuinely new
 * aggregation over the existing per-module note/log tables (Leadnote,
 * DealNote, Allcontactnote, StageHistory) plus TodoDashboardService/
 * DealDashboardService for the pieces they already compute.
 *
 * Unlike every other Deal/Lead widget on this dashboard (which scopes by
 * current ownership: leadassign_id / care_of), this service scopes by WHO
 * PERFORMED the action (admin_id / created_by / changed_by) — it's an
 * action-attribution report, not an ownership report. Matches the same
 * rule already applied to Contacts "calls made by whoever logged it."
 */
class ActivitySummaryService
{
    public function __construct(
        protected TodoDashboardService $todos,
        protected DealDashboardService $deals,
    ) {
    }

    public function summary(Carbon $from, Carbon $to, ?int $scopeAdminId = null): array
    {
        $todoTrend = $this->todos->completedVsPendingTrend($from, $to, $scopeAdminId);
        $closedStageIds = array_map('strval', $this->deals->closedStageIds());

        return [
            'leads_created' => Lead::whereBetween('lead_date', [$from, $to])
                ->when($scopeAdminId, fn ($q) => $q->where('leadassign_id', $scopeAdminId))
                ->count(),
            'leads_updated' => Lead::whereBetween('staff_updated_at', [$from, $to])
                ->when($scopeAdminId, fn ($q) => $q->where('leadassign_id', $scopeAdminId))
                ->count(),
            'calls_made' => $this->callsMade($from, $to, $scopeAdminId),
            'notes_added' => $this->notesAdded($from, $to, $scopeAdminId),
            'deals_created' => DealPipeline::whereBetween('created_at', [$from, $to])
                ->when($scopeAdminId, fn ($q) => $q->where('created_by', $scopeAdminId))
                ->count(),
            // whereNotNull('from_value') excludes the "entered pipeline"
            // StageHistory row every deal gets on creation — that event is
            // already counted in deals_created above; a "move" here means a
            // real stage-to-stage transition on an existing deal.
            'deals_moved' => StageHistory::where('trackable_type', 'deal')
                ->whereNotNull('from_value')
                ->whereBetween('created_at', [$from, $to])
                ->when($scopeAdminId, fn ($q) => $q->where('changed_by', $scopeAdminId))
                ->count(),
            'deals_closed' => StageHistory::where('trackable_type', 'deal')
                ->whereIn('to_value', $closedStageIds)
                ->whereBetween('created_at', [$from, $to])
                ->when($scopeAdminId, fn ($q) => $q->where('changed_by', $scopeAdminId))
                ->count(),
            'tasks_completed' => array_sum($todoTrend['completed'] ?? []),
        ];
    }

    protected function callsMade(Carbon $from, Carbon $to, ?int $scopeAdminId): int
    {
        $leadCalls = Leadnote::where('conversation_type', 'Call')
            ->whereBetween('created_at', [$from, $to])
            ->when($scopeAdminId, fn ($q) => $q->where('admin_id', $scopeAdminId))
            ->count();

        $dealCalls = DealNote::where('conversation_type', 'Call')
            ->whereBetween('created_at', [$from, $to])
            ->when($scopeAdminId, fn ($q) => $q->where('created_by', $scopeAdminId))
            ->count();

        $contactCalls = Allcontactnote::where('conversation_type', 'Call')
            ->whereBetween('created_at', [$from, $to])
            ->when($scopeAdminId, fn ($q) => $q->where('admin_id', $scopeAdminId))
            ->count();

        return $leadCalls + $dealCalls + $contactCalls;
    }

    protected function notesAdded(Carbon $from, Carbon $to, ?int $scopeAdminId): int
    {
        $leadNotes = Leadnote::whereBetween('created_at', [$from, $to])
            ->when($scopeAdminId, fn ($q) => $q->where('admin_id', $scopeAdminId))
            ->count();

        $dealNotes = DealNote::whereBetween('created_at', [$from, $to])
            ->when($scopeAdminId, fn ($q) => $q->where('created_by', $scopeAdminId))
            ->count();

        $contactNotes = Allcontactnote::whereBetween('created_at', [$from, $to])
            ->when($scopeAdminId, fn ($q) => $q->where('admin_id', $scopeAdminId))
            ->count();

        return $leadNotes + $dealNotes + $contactNotes;
    }
}
