<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Services\Dashboard\ActivitySummaryService;
use App\Services\Dashboard\ContactDashboardService;
use App\Services\Dashboard\DashboardDateRange;
use App\Services\Dashboard\DealDashboardService;
use App\Services\Dashboard\LeadDashboardService;
use App\Services\Dashboard\StaffPerformanceService;
use App\Services\Dashboard\TodoDashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Role-based CRM dashboard (/admin/dashboard). Replaces the previous
 * decorative BackEndController@index dashboard (kept intact, unrouted —
 * see resources/views/admin/dashboard_backup_pre_redesign.blade.php for the
 * original view). Staff see an action-oriented "what do I work on now"
 * view; admins (user_type == 1) see an org-wide performance view.
 *
 * All widgets are built from real Lead/Todo/DealPipeline data via the
 * app/Services/Dashboard/* services, respecting each module's own
 * assignment/permission columns rather than inventing new access rules.
 */
class DashboardController extends Controller
{
    public function __construct(
        protected LeadDashboardService $leadService,
        protected TodoDashboardService $todoService,
        protected DealDashboardService $dealService,
        protected StaffPerformanceService $staffPerformance,
        protected ContactDashboardService $contactService,
        protected ActivitySummaryService $activityService,
    ) {
    }

    protected function isAdmin(): bool
    {
        return Auth::guard('admin')->user()->user_type == 1;
    }

    /**
     * Common "Staff" filter scope for the admin dashboard — every AJAX
     * widget endpoint resolves it the same way instead of each inventing
     * its own filtering, and an unrecognized/inactive id quietly falls
     * back to "All Staff" rather than erroring.
     */
    protected function resolveStaffId(Request $request): ?int
    {
        $id = $request->input('staff_id');
        if (!$id) {
            return null;
        }

        $valid = Admin::where('status', 1)->where('user_type', '!=', 1)->where('id', $id)->exists();
        return $valid ? (int) $id : null;
    }

    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();

        if ($this->isAdmin()) {
            return $this->adminIndex($user);
        }

        return $this->staffIndex($user);
    }

    /**
     * Staff only ever see their own records (leadassign_id / assignto_id /
     * care_of = their own admin id), which every module already permits
     * regardless of the *_view "see everyone else's" flag. What we do need
     * to respect is each module's own top-level toggle (leads/todo/
     * deal_pipeline on `adminpermissions`) — a staff member without access
     * to a module at all should not see even their own data for it here.
     * Shared by staffIndex() and staffSummary() so the rule lives in one
     * place.
     */
    protected function resolveModuleAccess(Admin $user): array
    {
        $permission = Adminpermission::where('staff_id', $user->id)->first();
        $fullAccess = (bool) optional($permission)->full_access;

        return [
            $fullAccess || !$permission || $permission->leads == 1,
            $fullAccess || !$permission || $permission->todo == 1,
            $fullAccess || !$permission || $permission->deal_pipeline == 1,
        ];
    }

    protected function staffIndex(Admin $user)
    {
        [$showLeads, $showTodo, $showDeals] = $this->resolveModuleAccess($user);

        $leadKpis = $showLeads ? $this->leadService->kpis($user->id) : null;
        $todoKpis = $showTodo ? $this->todoService->kpis($user->id) : null;
        $dealKpis = $showDeals ? $this->dealService->kpis($user->id) : null;

        $actionCenter = [
            'leads' => $showLeads ? $this->leadService->actionCenterItems($user->id, 8) : collect(),
            'todos' => $showTodo ? $this->todoService->actionCenterItems($user->id, 8) : collect(),
            'deals' => $showDeals ? $this->dealService->actionCenterItems($user->id, 8) : collect(),
        ];

        return view('admin.dashboard.staff', compact(
            'user',
            'showLeads',
            'showTodo',
            'showDeals',
            'leadKpis',
            'todoKpis',
            'dealKpis',
            'actionCenter'
        ));
    }

    protected function adminIndex(Admin $user)
    {
        $leadKpis = $this->leadService->kpis();
        $todoKpis = $this->todoService->kpis();
        $dealKpis = $this->dealService->kpis();
        $contactKpis = $this->contactService->kpis();
        $staffList = $this->staffPerformance->activeStaff();
        $staffCount = $staffList->count();

        return view('admin.dashboard.admin', compact(
            'user',
            'leadKpis',
            'todoKpis',
            'dealKpis',
            'contactKpis',
            'staffList',
            'staffCount'
        ));
    }

    /**
     * GET admin/dashboard/staff-summary?range=&from=&to= — the Staff
     * dashboard's "My Activity" period row. Always scoped to the logged-in
     * user's own id; unlike the admin endpoints there is no staff_id input
     * to resolve, so a staff member can never pull another staff's numbers
     * through this endpoint. Reuses the exact same service methods (and
     * their $scopeAdminId parameter) as the admin dashboard's period
     * charts — no separate query logic.
     */
    public function staffSummary(Request $request)
    {
        $user = Auth::guard('admin')->user();
        [$from, $to] = DashboardDateRange::resolve($request->range, $request->from, $request->to);
        [$showLeads, $showTodo, $showDeals] = $this->resolveModuleAccess($user);

        $leadStatus = $showLeads ? $this->leadService->statusBreakdown($user->id, $from, $to) : [];
        $converted = collect($leadStatus)->firstWhere('id', 3)['total'] ?? 0;
        $todoTrend = $showTodo ? $this->todoService->completedVsPendingTrend($from, $to, $user->id) : ['completed' => []];
        $dealTrend = $showDeals ? $this->dealService->wonLostTrend($from, $to, $user->id) : ['won' => []];

        return response()->json([
            'range' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'new_leads' => $showLeads ? array_sum($this->leadService->trend($from, $to, $user->id)) : null,
            'leads_worked' => $showLeads ? ($this->leadService->staffActivity([$user->id], $from, $to)[$user->id]['leads_worked'] ?? 0) : null,
            'leads_converted' => $showLeads ? $converted : null,
            'tasks_completed' => $showTodo ? array_sum($todoTrend['completed'] ?? []) : null,
            'deals_won' => $showDeals ? array_sum($dealTrend['won'] ?? []) : null,
        ]);
    }

    /**
     * GET admin/dashboard/admin-summary?range=&from=&to=&staff_id= — admin
     * only. Bundles both the "Live Snapshot" KPIs and the period charts so
     * a Staff/Date filter change only costs one round trip.
     */
    public function adminSummary(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

        [$from, $to] = DashboardDateRange::resolve($request->range, $request->from, $request->to);
        $staffId = $this->resolveStaffId($request);

        return response()->json([
            'range' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'staff_id' => $staffId,
            'lead_kpis' => $this->leadService->kpis($staffId),
            'todo_kpis' => $this->todoService->kpis($staffId),
            'deal_kpis' => $this->dealService->kpis($staffId),
            'contact_kpis' => $this->contactService->kpis($staffId),
            'lead_trend' => $this->leadService->trend($from, $to, $staffId),
            'lead_status' => $this->leadService->statusBreakdown($staffId, $from, $to),
            'lead_qualified_trend' => $this->leadService->qualifiedMovementTrend($from, $to, $staffId),
            'todo_trend' => $this->todoService->completedVsPendingTrend($from, $to, $staffId),
            'todo_status' => $this->todoService->statusBreakdown($staffId),
            'deal_stages' => $this->dealService->stageBreakdown($staffId),
            'deal_trend' => $this->dealService->createdTrend($from, $to, $staffId),
            'deal_won_lost_trend' => $this->dealService->wonLostTrend($from, $to, $staffId),
            'contact_trend' => $this->contactService->createdTrend($from, $to, $staffId),
            'calls_made_trend' => $this->contactService->callsMadeTrend($from, $to, $staffId),
        ]);
    }

    /** GET admin/dashboard/staff-performance?range=&from=&to=&staff_id= — admin only. */
    public function staffPerformance(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

        [$from, $to] = DashboardDateRange::resolve($request->range, $request->from, $request->to);
        $staffId = $this->resolveStaffId($request);

        return response()->json([
            'range' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'staff_id' => $staffId,
            'ranking' => $this->staffPerformance->ranking($from, $to, $staffId),
        ]);
    }

    /** GET admin/dashboard/work-report?range=&from=&to=&staff_id= — admin only. */
    public function workReport(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

        [$from, $to] = DashboardDateRange::resolve($request->range, $request->from, $request->to);
        $staffId = $this->resolveStaffId($request);

        return response()->json([
            'range' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'staff_id' => $staffId,
            'report' => $this->staffPerformance->report($from, $to, $staffId),
        ]);
    }

    /**
     * GET admin/dashboard/pipeline-movement?range=&from=&to=&staff_id= —
     * admin only. Split out from adminSummary() (unlike its other fields,
     * this hits the new stage_histories table with several extra grouped
     * queries per call) — same "dedicated endpoint for heavier queries"
     * precedent as workReport()/staffSummary().
     */
    public function pipelineMovement(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

        [$from, $to] = DashboardDateRange::resolve($request->range, $request->from, $request->to);
        $staffId = $this->resolveStaffId($request);

        return response()->json([
            'range' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'staff_id' => $staffId,
            'stages' => $this->dealService->stageMovement($from, $to, $staffId),
        ]);
    }

    /**
     * GET admin/dashboard/activity-summary?range=&from=&to=&staff_id= —
     * admin only. Touches 5 different tables plus TodoDashboardService —
     * same weight class as workReport(), split out for the same reason.
     */
    public function activitySummary(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

        [$from, $to] = DashboardDateRange::resolve($request->range, $request->from, $request->to);
        $staffId = $this->resolveStaffId($request);

        return response()->json([
            'range' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'staff_id' => $staffId,
            'summary' => $this->activityService->summary($from, $to, $staffId),
        ]);
    }
}
