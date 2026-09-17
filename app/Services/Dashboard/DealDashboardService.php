<?php

namespace App\Services\Dashboard;

use App\Models\DealPipeline;
use App\Models\DealStage;
use App\Models\StageHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Read-only aggregate queries over the Deal Pipeline module
 * (app/Models/DealPipeline.php, table `deal_pipeline`) for the CRM
 * dashboard. Stage names/ids are read live from `deal_stages` (never
 * hardcoded — the id set has already shifted once in this system).
 * Won/Lost is resolved by stage *name* ("Closed Won"/"Closed Lost"), not id.
 * "Stale" reuses the exact 6-day updated_at rule already enforced by the
 * app/Console/Commands/NotifyIfDealNotUpdated cron job.
 *
 * Staff scoping uses `care_of` (assignee) to match the full Deal Pipeline
 * list page's initial-load scoping — DealPipelineController is internally
 * inconsistent (its AJAX/load-more branches use `created_by` instead), a
 * pre-existing quirk this dashboard does not attempt to fix.
 */
class DealDashboardService
{
    public const STALE_DAYS = 6;

    protected ?array $stageIdByName = null;

    protected function stageIdByName(): array
    {
        if ($this->stageIdByName === null) {
            $this->stageIdByName = DealStage::pluck('id', 'name')->toArray();
        }
        return $this->stageIdByName;
    }

    public function closedStageIds(): array
    {
        $map = $this->stageIdByName();
        return array_filter([$map['Closed Won'] ?? null, $map['Closed Lost'] ?? null]);
    }

    protected function baseQuery(?int $scopeAdminId)
    {
        $q = DealPipeline::query();
        if ($scopeAdminId) {
            $q->where('care_of', $scopeAdminId);
        }
        return $q;
    }

    /**
     * `amount` is a free-text varchar with no validation — production data
     * contains at least one row with a garbage value (a 9-digit+ number,
     * clearly mis-entered) that would otherwise blow up SUM() into the
     * billions. Excluded from sums (not from counts) above this ceiling;
     * generous for this business's typical deal sizes (tens of thousands).
     */
    protected const MAX_PLAUSIBLE_AMOUNT = 5000000;

    protected function amountSumExpr(): string
    {
        return "SUM(CASE WHEN CAST(NULLIF(TRIM(amount), '') AS DECIMAL(20,2)) <= " . self::MAX_PLAUSIBLE_AMOUNT .
            " THEN CAST(NULLIF(TRIM(amount), '') AS DECIMAL(20,2)) ELSE 0 END)";
    }

    public function kpis(?int $scopeAdminId = null): array
    {
        $map = $this->stageIdByName();
        $wonId = $map['Closed Won'] ?? null;
        $lostId = $map['Closed Lost'] ?? null;
        $closed = $this->closedStageIds();
        $staleBefore = Carbon::now()->subDays(self::STALE_DAYS);

        $active = (clone $this->baseQuery($scopeAdminId))->whereNotIn('deal_stage_id', $closed ?: [0]);

        return [
            'active_deals' => (clone $active)->count(),
            'pipeline_value' => (float) ((clone $active)->selectRaw($this->amountSumExpr() . ' as s')->value('s') ?? 0),
            'won_deals' => $wonId ? (clone $this->baseQuery($scopeAdminId))->where('deal_stage_id', $wonId)->count() : 0,
            'lost_deals' => $lostId ? (clone $this->baseQuery($scopeAdminId))->where('deal_stage_id', $lostId)->count() : 0,
            'won_value' => $wonId ? (float) ((clone $this->baseQuery($scopeAdminId))->where('deal_stage_id', $wonId)
                ->selectRaw($this->amountSumExpr() . ' as s')->value('s') ?? 0) : 0,
            'stale_deals' => (clone $active)->where('updated_at', '<', $staleBefore)->count(),
            'recently_updated' => (clone $active)->where('updated_at', '>=', Carbon::now()->subDays(2))->count(),
        ];
    }

    public function stageBreakdown(?int $scopeAdminId = null): array
    {
        $counts = $this->baseQuery($scopeAdminId)
            ->select('deal_stage_id', DB::raw('COUNT(*) as total'), DB::raw($this->amountSumExpr() . ' as value'))
            ->groupBy('deal_stage_id')
            ->get()
            ->keyBy('deal_stage_id');

        $out = [];
        foreach (DealStage::orderBy('id')->get() as $stage) {
            $row = $counts->get($stage->id);
            $out[] = [
                'id' => $stage->id,
                'name' => $stage->name,
                'total' => $row ? (int) $row->total : 0,
                'value' => $row ? (float) $row->value : 0.0,
            ];
        }
        return $out;
    }

    /**
     * Sourced from stage_histories (when a deal actually transitioned into
     * Won/Lost), not deal_pipeline.updated_at — the old proxy over-counted
     * any deal touched again after closing and mis-attributed a Won->Lost->Won
     * flip. Scoped by the deal's *current* care_of, matching every other
     * method on this service (see class docblock on the care_of vs
     * created_by convention).
     */
    public function wonLostTrend(Carbon $from, Carbon $to, ?int $scopeAdminId = null): array
    {
        $map = $this->stageIdByName();
        $wonId = $map['Closed Won'] ?? 0;
        $lostId = $map['Closed Lost'] ?? 0;

        $dealIds = $scopeAdminId
            ? DealPipeline::where('care_of', $scopeAdminId)->pluck('id')
            : null;

        $base = StageHistory::where('trackable_type', 'deal')
            ->whereBetween('created_at', [$from, $to])
            ->when($dealIds, fn ($q) => $q->whereIn('trackable_id', $dealIds));

        $won = (clone $base)->where('to_value', (string) $wonId)
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')->pluck('total', 'd');

        $lost = (clone $base)->where('to_value', (string) $lostId)
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')->pluck('total', 'd');

        return ['won' => $won->toArray(), 'lost' => $lost->toArray()];
    }

    /** "New Deals" — created in the period, scoped by current care_of. */
    public function createdTrend(Carbon $from, Carbon $to, ?int $scopeAdminId = null): array
    {
        return $this->baseQuery($scopeAdminId)
            ->whereBetween('created_at', [$from, $to])
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')->pluck('total', 'd')->toArray();
    }

    /**
     * Per-stage current count/value (reuses stageBreakdown() so the two
     * widgets can never numerically disagree) plus real historical movement
     * from stage_histories — never inferred from the current deal_stage_id
     * column. Movement is scoped to deals currently owned by $scopeAdminId
     * (an "in-flight funnel" view), not by who performed the move — that's
     * a different question, answered by ActivitySummaryService instead.
     */
    public function stageMovement(Carbon $from, Carbon $to, ?int $scopeAdminId = null): array
    {
        $current = collect($this->stageBreakdown($scopeAdminId))->keyBy('id');
        $closed = $this->closedStageIds();

        $dealIds = $scopeAdminId
            ? DealPipeline::where('care_of', $scopeAdminId)->pluck('id')
            : null;

        $historyBase = StageHistory::where('trackable_type', 'deal')
            ->whereBetween('created_at', [$from, $to])
            ->when($dealIds, fn ($q) => $q->whereIn('trackable_id', $dealIds));

        $movedIn = (clone $historyBase)->select('to_value', DB::raw('COUNT(*) as total'))
            ->groupBy('to_value')->pluck('total', 'to_value');

        $movedOut = (clone $historyBase)->select('from_value', DB::raw('COUNT(*) as total'))
            ->groupBy('from_value')->pluck('total', 'from_value');

        $out = [];
        foreach (DealStage::orderBy('id')->get() as $stage) {
            $row = $current->get($stage->id);
            $out[] = [
                'id' => $stage->id,
                'name' => $stage->name,
                'is_closed' => in_array($stage->id, $closed, true),
                'current_count' => $row ? $row['total'] : 0,
                'current_value' => $row ? $row['value'] : 0.0,
                'movement_in' => (int) ($movedIn[(string) $stage->id] ?? 0),
                'movement_out' => (int) ($movedOut[(string) $stage->id] ?? 0),
            ];
        }
        return $out;
    }

    /**
     * Top N open deals assigned to this staff for the Action Center — most
     * recently active first. Closed (Won/Lost) deals are excluded since
     * they don't need further action; no staleness filtering.
     */
    public function actionCenterItems(int $adminId, int $limit = 8)
    {
        $closed = $this->closedStageIds();

        return DealPipeline::with('stage')
            ->where('care_of', $adminId)
            ->whereNotIn('deal_stage_id', $closed ?: [0])
            ->orderBy('updated_at', 'DESC')
            ->limit($limit)
            ->get(['id', 'deal_name', 'name', 'company', 'candidate', 'amount', 'deal_stage_id', 'updated_at']);
    }

    public function staffActivity(array $staffIds, Carbon $from, Carbon $to): array
    {
        if (empty($staffIds)) {
            return [];
        }
        $map = $this->stageIdByName();
        $wonId = $map['Closed Won'] ?? 0;

        $handled = DealPipeline::whereIn('care_of', $staffIds)
            ->whereBetween('updated_at', [$from, $to])
            ->select('care_of', DB::raw('COUNT(*) as total'))
            ->groupBy('care_of')->pluck('total', 'care_of');

        $won = DealPipeline::whereIn('care_of', $staffIds)
            ->where('deal_stage_id', $wonId)
            ->whereBetween('updated_at', [$from, $to])
            ->select('care_of', DB::raw('COUNT(*) as total'))
            ->groupBy('care_of')->pluck('total', 'care_of');

        $out = [];
        foreach ($staffIds as $id) {
            $out[$id] = [
                'deals_handled' => (int) ($handled[$id] ?? 0),
                'deals_won' => (int) ($won[$id] ?? 0),
            ];
        }
        return $out;
    }
}
