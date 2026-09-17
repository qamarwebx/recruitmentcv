<?php

namespace App\Services\Dashboard;

use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Read-only aggregate queries over the Todo module (app/Models/Todo.php,
 * table `todos`) for the CRM dashboard. `assignto_id` is a comma-separated
 * list of admin ids (no proper FK/pivot), so staff scoping uses the same
 * FIND_IN_SET pattern TodoController itself uses. There is no due-date
 * scope in the codebase today, so overdue/due-today/upcoming are derived
 * here from `finish_on` + `is_completed` (there is no linkage from Todo to
 * Leads or Deals — none exists on the `todos` table).
 */
class TodoDashboardService
{
    protected function scopeToStaff($q, ?int $adminId)
    {
        if ($adminId) {
            $q->whereRaw('FIND_IN_SET(?, assignto_id)', [$adminId]);
        }
        return $q;
    }

    public function kpis(?int $scopeAdminId = null): array
    {
        $today = Carbon::today();

        $pending = $this->scopeToStaff(Todo::query(), $scopeAdminId)->where('is_completed', 0);
        $pendingCount = (clone $pending)->count();
        $completedCount = $this->scopeToStaff(Todo::query(), $scopeAdminId)->where('is_completed', 1)->count();

        return [
            'total' => $pendingCount + $completedCount,
            'pending' => $pendingCount,
            'due_today' => (clone $pending)->whereDate('finish_on', $today)->count(),
            'overdue' => (clone $pending)->whereDate('finish_on', '<', $today)->count(),
            'upcoming' => (clone $pending)->whereDate('finish_on', '>', $today)->count(),
            'high_priority' => (clone $pending)->whereIn('reminder_cycle', ['High', 'Urgent'])->count(),
            'completed_total' => $completedCount,
        ];
    }

    public function statusBreakdown(?int $scopeAdminId = null): array
    {
        return $this->scopeToStaff(Todo::query(), $scopeAdminId)
            ->select('task_status', DB::raw('COUNT(*) as total'))
            ->groupBy('task_status')
            ->pluck('total', 'task_status')
            ->toArray();
    }

    public function completedVsPendingTrend(Carbon $from, Carbon $to, ?int $scopeAdminId = null): array
    {
        $completed = $this->scopeToStaff(Todo::query(), $scopeAdminId)
            ->where('is_completed', 1)
            ->whereBetween('complete_date', [$from, $to])
            ->select(DB::raw('DATE(complete_date) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')->pluck('total', 'd');

        $created = $this->scopeToStaff(Todo::query(), $scopeAdminId)
            ->whereBetween('created_at', [$from, $to])
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as total'))
            ->groupBy('d')->pluck('total', 'd');

        return ['created' => $created->toArray(), 'completed' => $completed->toArray()];
    }

    /**
     * Ranked "needs action now" list for the staff Action Center: overdue
     * first (oldest due date = most urgent), then due today, then upcoming.
     */
    public function actionCenterItems(int $adminId, int $limit = 8)
    {
        return Todo::whereRaw('FIND_IN_SET(?, assignto_id)', [$adminId])
            ->where('is_completed', 0)
            ->orderByRaw('CASE WHEN finish_on < CURDATE() THEN 0 WHEN finish_on = CURDATE() THEN 1 ELSE 2 END ASC')
            ->orderBy('finish_on', 'ASC')
            ->limit($limit)
            ->get(['id', 'task_title', 'task_status', 'reminder_cycle', 'finish_on', 'is_completed']);
    }

    public function staffActivity(array $staffIds, Carbon $from, Carbon $to): array
    {
        $out = [];
        foreach ($staffIds as $id) {
            $completed = Todo::whereRaw('FIND_IN_SET(?, assignto_id)', [$id])
                ->where('is_completed', 1)
                ->where(function ($w) use ($from, $to) {
                    $w->whereBetween('complete_date', [$from, $to])
                        ->orWhereBetween('achieved_date', [$from, $to]);
                })->count();

            $assigned = Todo::whereRaw('FIND_IN_SET(?, assignto_id)', [$id])
                ->whereBetween('created_at', [$from, $to])->count();

            $out[$id] = [
                'tasks_assigned' => $assigned,
                'tasks_completed' => $completed,
            ];
        }
        return $out;
    }
}
