<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdvancePayment;
use App\Models\AttendanceLog;
use App\Models\Payroll;
use App\Models\PayrollSaveFilter;
use App\Models\SalarySetting;
use App\Services\AdvancePaymentService;
use App\Services\PayrollService;
use App\Models\Adminpermission;
use App\Services\SalaryCalculationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class SalaryController extends Controller
{
    private function loggedAdmin()
    {
        return Auth::guard('admin')->user();
    }

    private function isSuperAdmin($admin)
    {
        return $admin->user_type == 1;
    }

    private function isManager($admin)
    {
        $roles = json_decode((string) $admin->role, true) ?: [];
        return in_array('Team Head', $roles);
    }

    private function canApprove($admin)
    {
        return $this->isSuperAdmin($admin) || $this->isManager($admin);
    }

    private function teamAdminIds($admin)
    {
        return Admin::where('id', $admin->id)
            ->orWhere('createby_id', $admin->id)
            ->pluck('id');
    }

    /**
     * Can $admin view the salary of $targetAdminId? Everyone can view their
     * own; Manager only within their team; Super Admin can view anyone.
     */
    private function canView($admin, $targetAdminId)
    {
        if ((int) $targetAdminId === (int) $admin->id) {
            return true;
        }

        if ($this->isSuperAdmin($admin) || $this->isManager($admin)) {
            return true;
        }

        return $this->isManager($admin) && $this->teamAdminIds($admin)->contains((int) $targetAdminId);
    }

    /**
     * Same access tier the Payroll page itself requires (index()): Super
     * Admin, or a permission row with full_access or hr_payroll enabled.
     * Reused by payroll actions (e.g. Mark as Paid) that aren't gated by
     * route middleware, so they can't be hit by a staff member who merely
     * guesses the endpoint.
     */
    private function authorizePayrollAccess($admin)
    {
        $permission = Adminpermission::where('staff_id', $admin->id)->first();

        if ($this->isSuperAdmin($admin) || optional($permission)->full_access == 1) {
            return $permission;
        }

        if (optional($permission)->hr_payroll != 1) {
            abort(403, 'You do not have permission to access Payroll.');
        }

        return $permission;
    }

    /**
     * Payroll (Final Salary) page - Super Admin only, same tier that's
     * always been required to generate/lock/edit payroll records.
     */
    public function index(Request $request)
    {
        $admin = $this->loggedAdmin();

        $permission = Adminpermission::where('staff_id', $admin->id)->first();

        if (optional($permission)->full_access == 1) {
            return;
        }

        if (optional($permission)->hr_payroll != 1 && !$this->isSuperAdmin($admin)) {
            abort(403, 'You do not have permission to access Payroll.');
        }


        $staffList = Admin::isActiveAdmin()->orderBy('name')->get(['id', 'name', 'monthly_salary']);

        $savedFilter = PayrollSaveFilter::where('admin_id', $admin->id)->first();

        return view('admin.salary.payroll', compact('admin', 'staffList', 'permission', 'savedFilter'));
    }

    /**
     * Salary Settings page - Holidays are manageable by Super Admin and
     * anyone with the hr_attendance_view_all permission ("HR Management ->
     * View All"), same tier as AttendanceController::canViewAll(); the
     * Configuration + Staff Monthly Salary panels stay Super Admin only.
     */
    public function settingsPage(Request $request)
    {
        $admin = $this->loggedAdmin();
        $isSuperAdmin = $this->isSuperAdmin($admin);
        $isManager = $this->isManager($admin);
        $canManageRoles = $isSuperAdmin || $isManager;

        $permission = Adminpermission::where('staff_id', $admin->id)->first();
        $canManageHolidays = $isSuperAdmin
            || optional($permission)->full_access == 1
            || optional($permission)->hr_attendance_view_all == 1;

        // Role Configuration needs the staff list for Super Admin AND Team
        // Head; Staff Monthly Salary (further down the page) stays Super
        // Admin only even though both sections share this same list.
        $staffList = $canManageRoles
            ? Admin::isActiveAdmin()->orderBy('name')->get(['id', 'name', 'monthly_salary', 'role'])
            : collect();

        $settings = SalarySetting::current();

        // Final Approval Access (2-phase attendance approval) is configured
        // the same way as Role Configuration, but stays Super Admin only -
        // it's the higher-trust gate that decides what actually gets paid.
        $canManageFinalApproval = $isSuperAdmin;

        // Whoever currently holds each exclusive role, derived from the
        // staff list already fetched above - drives the default-selected
        // staff + toggle state on page load, no extra query needed.
        $currentTeamHead = $staffList->first(fn ($s) => in_array('Team Head', $this->decodeRoles($s->role)));
        $currentFinalApprover = $staffList->first(fn ($s) => in_array('Final Approver', $this->decodeRoles($s->role)));

        return view('admin.salary.settings', compact('admin', 'isSuperAdmin', 'isManager', 'canManageRoles', 'canManageHolidays', 'canManageFinalApproval', 'staffList', 'settings', 'currentTeamHead', 'currentFinalApprover'));
    }

    /**
     * Read-only reference page documenting every salary/attendance
     * calculation rule in plain language, with the current live Salary
     * Settings values substituted in - so "what does Late Deduction % of 50
     * actually mean" is answered on the page itself, not just in code
     * comments. Same visibility tier as the Settings page (Super Admin or
     * Team Head); no write action happens here.
     */
    public function rulesPage(Request $request)
    {
        $admin = $this->loggedAdmin();
        $isSuperAdmin = $this->isSuperAdmin($admin);
        $isManager = $this->isManager($admin);

        if (!$isSuperAdmin && !$isManager) {
            abort(403, 'You do not have permission to view Salary Calculation Rules.');
        }

        $settings = SalarySetting::current();

        return view('admin.salary.rules', compact('admin', 'settings'));
    }

    /**
     * admins.role is a mixed bag historically: some rows are a proper JSON
     * array (e.g. ["Candidate Source"]), others are a bare legacy string
     * (e.g. "Candidate Source") that isn't valid JSON at all. A plain
     * json_decode() ?: [] would silently drop that legacy string and any
     * other tags the moment this saves back as JSON.
     *
     * $exclusive roles (Team Head, Final Approver) may only be held by one
     * admin at a time: enabling it for $staff strips it from whoever else
     * currently holds it first, inside the same locked transaction, so two
     * concurrent requests can never leave two admins holding the tag.
     */
    private function toggleAdminRole(Admin $staff, string $roleName, bool $enabled, bool $exclusive = false): void
    {
        DB::transaction(function () use ($staff, $roleName, $enabled, $exclusive) {
            if ($enabled && $exclusive) {
                // Lock every admin row up front so a concurrent toggle for a
                // different staff member serializes behind this transaction
                // instead of racing it.
                Admin::lockForUpdate()->get(['id', 'role'])->each(function (Admin $other) use ($staff, $roleName) {
                    if ((int) $other->id === (int) $staff->id) {
                        return;
                    }

                    $roles = $this->decodeRoles($other->role);

                    if (in_array($roleName, $roles)) {
                        $other->role = json_encode(array_values(array_diff($roles, [$roleName])));
                        $other->save();
                    }
                });
            }

            $staff = Admin::whereKey($staff->id)->lockForUpdate()->firstOrFail();
            $roles = $this->decodeRoles($staff->role);

            if ($enabled) {
                if (!in_array($roleName, $roles)) {
                    $roles[] = $roleName;
                }
            } else {
                $roles = array_values(array_diff($roles, [$roleName]));
            }

            $staff->role = json_encode($roles);
            $staff->save();
        });
    }

    private function decodeRoles($rawRole): array
    {
        $decoded = json_decode((string) $rawRole, true);

        return is_array($decoded) ? $decoded : (filled($rawRole) ? [$rawRole] : []);
    }

    /**
     * Add or remove the "Team Head" role for a staff member - Super Admin
     * and Team Head only, matching Role Configuration's visibility.
     */
    public function updateStaffRole(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->isSuperAdmin($admin) && !$this->isManager($admin)) {
            return response()->json(['res' => 'You are not authorized to manage roles.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|integer|exists:admins,id',
            'is_team_head' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $staff = Admin::findOrFail($request->admin_id);
        $this->toggleAdminRole($staff, 'Team Head', $request->boolean('is_team_head'), exclusive: true);

        return response()->json(['res' => 'Role updated successfully!']);
    }

    /**
     * Add or remove Final Approval Access (2-phase attendance approval,
     * Phase 2) for a staff member - Super Admin only. Anyone holding this
     * access can give/reject Final Approval on any attendance record that
     * has already received Normal Approval.
     */
    public function updateStaffFinalApprovalAccess(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->isSuperAdmin($admin)) {
            return response()->json(['res' => 'You are not authorized to manage Final Approval Access.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|integer|exists:admins,id',
            'is_final_approver' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $staff = Admin::findOrFail($request->admin_id);
        $this->toggleAdminRole($staff, 'Final Approver', $request->boolean('is_final_approver'), exclusive: true);

        return response()->json(['res' => 'Final Approval Access updated successfully!']);
    }

    public function dashboard(Request $request)
    {
        $admin = $this->loggedAdmin();
        $canApprove = $this->canApprove($admin);
        $isSuperAdmin = $this->isSuperAdmin($admin);

        $staffList = $isSuperAdmin
            ? Admin::isActiveAdmin()->orderBy('name')->get(['id', 'name', 'monthly_salary'])
            : ($this->isManager($admin)
                ? Admin::isActiveAdmin()->whereIn('id', $this->teamAdminIds($admin))->orderBy('name')->get(['id', 'name', 'monthly_salary'])
                : collect());

        return view('admin.salary.dashboard', compact('admin', 'canApprove', 'isSuperAdmin', 'staffList'));
    }

    public function live(Request $request, SalaryCalculationService $calculator, AdvancePaymentService $advancePaymentService)
    {
        $admin = $this->loggedAdmin();
        $targetAdminId = $request->filled('staff_id') ? (int) $request->staff_id : $admin->id;

        if (!$this->canView($admin, $targetAdminId)) {
            return response()->json(['res' => 'You are not authorized to view this salary.'], 403);
        }

        $target = Admin::find($targetAdminId);

        if (!$target) {
            return response()->json(['res' => 'Staff member not found.'], 404);
        }

        $month = (int) ($request->month ?: now()->month);
        $year = (int) ($request->year ?: now()->year);

        return response()->json($this->resolveSalaryResult($target, $month, $year, $calculator, $advancePaymentService));
    }

    /**
     * Locked Payroll is authoritative once it exists for the period;
     * otherwise fall back to a fresh live calculation. Shared by the live
     * dashboard endpoint and the salary slip PDF so both always agree.
     *
     * Also folds in the applicable Advance Payment total (locked payroll's
     * frozen figure if one exists, otherwise a live sum - see
     * AdvancePaymentService::resolveApplicableAdvance()) as
     * 'advance_deduction' / 'final_payable_after_advance', so every caller
     * of this method automatically stays in sync without recomputing it
     * separately.
     */
    private function resolveSalaryResult(Admin $target, int $month, int $year, SalaryCalculationService $calculator, AdvancePaymentService $advancePaymentService): array
    {
        $locked = Payroll::where('admin_id', $target->id)
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'locked')
            ->first();

        $result = $locked
            ? $this->payrollToLiveShape($locked, $target, $calculator)
            : $calculator->calculate($target, $month, $year);

        $result['advance_deduction'] = $advancePaymentService->resolveApplicableAdvance($target->id, $month, $year);
        $result['final_payable_after_advance'] = round($result['net_payable'] - $result['advance_deduction'], 2);

        return $result;
    }

    /**
     * Download a Salary Slip PDF for one staff member / one month. Uses the
     * same locked-vs-live resolution as the dashboard, so the slip always
     * matches what the staff member sees on screen.
     */
    public function downloadSlip(Request $request, int $adminId, int $month, int $year, SalaryCalculationService $calculator, AdvancePaymentService $advancePaymentService)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canView($admin, $adminId)) {
            abort(403, 'You are not authorized to download this salary slip.');
        }

        // Current/previous-month restriction is scoped to the Live Salary
        // Dashboard's own "Download Salary Slip" shortcut on the Attendance
        // page only (that button appends ?source=attendance_live_dashboard
        // below) - the Salary Dashboard page's own Download Salary Slip
        // modal and the Payroll table's slip actions intentionally keep
        // unrestricted month access for every tier, unchanged. Enforced
        // here (not just in that button's own JS) so it holds even if the
        // request URL is edited directly, as long as the source flag is
        // still present; stripping the flag reaches the same unrestricted
        // capability the Salary Dashboard page already exposes for a
        // staff member's own record, so it isn't a new access path.
        $isOwnSlip = (int) $adminId === (int) $admin->id;
        $isFromAttendanceLiveDashboard = $request->query('source') === 'attendance_live_dashboard';
        $isRestrictedToRecentMonths = $isOwnSlip && $isFromAttendanceLiveDashboard && !$this->canApprove($admin);

        if ($isRestrictedToRecentMonths) {
            $current = \Carbon\Carbon::now();
            $previous = $current->copy()->subMonthNoOverflow();
            $isCurrentPeriod = (int) $month === $current->month && (int) $year === $current->year;
            $isPreviousPeriod = (int) $month === $previous->month && (int) $year === $previous->year;

            if (!$isCurrentPeriod && !$isPreviousPeriod) {
                abort(403, 'You can only download the salary slip for the current or previous month.');
            }
        }

        $target = Admin::find($adminId);

        if (!$target) {
            abort(404, 'Staff member not found.');
        }

        $result = $this->resolveSalaryResult($target, $month, $year, $calculator, $advancePaymentService);
        $periodLabel = \Carbon\Carbon::create($year, $month, 1)->format('F Y');
        // Cutoff-aware: the current month in progress only counts weekly
        // offs/working days up to today, same period the rest of $result
        // (present/absent/deductions/net payable) was evaluated over. A
        // completed previous month (or a locked snapshot) naturally covers
        // the full month here since elapsed_days == days_in_month for those.
        $weeklyOffDays = $result['weekly_off_days'];

        $pdf = Pdf::loadView('admin.salary.slip_pdf', [
            'target' => $target,
            'result' => $result,
            'periodLabel' => $periodLabel,
            'companyName' => 'QAMR INTERNATIONAL',
            'companySubtitle' => 'HR CONSULTANCY',
            'logoPath' => public_path('admin/assets/images/pdf/qamr-logo.png'),
            'dateOfJoining' => $target->created_at?->format('d M Y') ?? '-',
            'weeklyOffDays' => $weeklyOffDays,
            'totalWorkingDays' => max($result['elapsed_days'] - $weeklyOffDays, 0),
            'amountInWords' => $this->amountInWords((float) ($result['advance_deduction'] > 0 ? $result['final_payable_after_advance'] : $result['net_payable'])),
        ]);

        $fileName = 'Salary-Slip-' . str_replace(' ', '-', $target->name) . '-' . $periodLabel . '.pdf';

        // ?view=1 opens the PDF inline in the browser tab (e.g. the Payroll
        // table's "View Slip" action); otherwise it force-downloads as before.
        return $request->boolean('view')
            ? $pdf->stream($fileName)
            : $pdf->download($fileName);
    }

    /**
     * How many Sundays (Weekly Off) fall in this month - a static calendar
     * fact independent of attendance data, used for the "Total Working
     * Days" / "Weekly Off" figures on the slip.
     */
    /**
     * "Rupees Twenty Two Thousand Four Hundred Fifty and Fifty Paise Only"
     * style amount-in-words for the slip footer.
     */
    private function amountInWords(float $amount): string
    {
        $rupees = (int) floor($amount);
        $paise = (int) round(($amount - $rupees) * 100);

        $transformer = (new \NumberToWords\NumberToWords())->getNumberTransformer('en');
        $toTitleWords = fn (int $n) => ucwords(str_replace('-', ' ', $transformer->toWords($n)));

        $words = 'Rupees ' . $toTitleWords($rupees);

        if ($paise > 0) {
            $words .= ' and ' . $toTitleWords($paise) . ' Paise';
        }

        return $words . ' Only';
    }

    public function updateStaffSalary(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->isSuperAdmin($admin)) {
            return response()->json(['res' => 'You are not authorized to update staff salary.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|integer|exists:admins,id',
            'monthly_salary' => 'required|numeric|min:0',
        ], [
            'admin_id.exists' => 'Selected staff member was not found.',
            'monthly_salary.required' => 'Monthly salary is required.',
            'monthly_salary.numeric' => 'Monthly salary must be a number.',
            'monthly_salary.min' => 'Monthly salary cannot be negative.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $staff = Admin::findOrFail($request->admin_id);
        $staff->monthly_salary = $request->monthly_salary;
        $staff->save();

        return response()->json(['res' => 'Monthly salary updated successfully!']);
    }

    public function settingsUpdate(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->isSuperAdmin($admin)) {
            return response()->json(['res' => 'You are not authorized to update salary settings.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'office_start_time' => 'required|date_format:H:i',
            'qualifying_late_end_time' => 'required|date_format:H:i|after:office_start_time',
            'late_window_end_time' => 'required|date_format:H:i|after:qualifying_late_end_time',
            'half_day_after_time' => 'required|date_format:H:i',
            'free_late_count' => 'required|integer|min:0',
            'free_late_max_minutes' => 'required|integer|min:0',
            'deduction_calculation_type' => 'required|in:' . implode(',', SalarySetting::DEDUCTION_TYPES),
            // Each of these is only required for the calculation type it
            // belongs to - Percentage Based never needs Slab Amount, Fixed
            // Slab/Fixed Amount never need Percentage, etc. Whichever one(s)
            // aren't required are still validated as numeric when present,
            // so a stray value can't corrupt the stored setting even though
            // it won't be read for calculation.
            'late_deduction_percent' => 'required_if:deduction_calculation_type,percentage|nullable|numeric|min:0|max:100',
            'deduction_slab_amount' => 'required_if:deduction_calculation_type,fixed_slab|nullable|numeric|min:0.01',
            'deduction_per_slab' => 'required_if:deduction_calculation_type,fixed_slab,fixed_amount|nullable|numeric|min:0',
            'retroactive_late_deduction' => 'nullable|boolean',
            'sat_absent_sunday_deduction' => 'nullable|boolean',
            'mon_absent_prev_sunday_deduction' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $settings = SalarySetting::current();
        $settings->office_start_time = $request->office_start_time;
        $settings->qualifying_late_end_time = $request->qualifying_late_end_time;
        $settings->late_window_end_time = $request->late_window_end_time;
        $settings->half_day_after_time = $request->half_day_after_time;
        $settings->free_late_count = $request->free_late_count;
        $settings->free_late_max_minutes = $request->free_late_max_minutes;
        $settings->deduction_calculation_type = $request->deduction_calculation_type;
        // Only the field(s) relevant to the type actually being submitted
        // this request are guaranteed present (required_if above) - for the
        // others, keep whatever was already configured rather than
        // overwriting a real value with null.
        $settings->late_deduction_percent = $request->late_deduction_percent ?? $settings->late_deduction_percent;
        $settings->deduction_slab_amount = $request->deduction_slab_amount ?? $settings->deduction_slab_amount;
        $settings->deduction_per_slab = $request->deduction_per_slab ?? $settings->deduction_per_slab;
        $settings->retroactive_late_deduction = $request->boolean('retroactive_late_deduction');
        $settings->sat_absent_sunday_deduction = $request->boolean('sat_absent_sunday_deduction');
        $settings->mon_absent_prev_sunday_deduction = $request->boolean('mon_absent_prev_sunday_deduction');
        $settings->updated_by = $admin->id;
        $settings->save();

        return response()->json(['res' => 'Salary settings updated successfully!']);
    }

    public function payrollDatatable(Request $request)
    {
        $admin = $this->loggedAdmin();

        $this->authorizePayrollAccess($admin);

        $filterFields = ['staff_id', 'status', 'payment_status', 'month', 'year', 'generated_from', 'generated_to'];
        $hasRequestFilter = collect($filterFields)->contains(fn ($field) => filled($request->$field));

        // select('payrolls.*') is load-bearing: Yajra's server-side sorting
        // on the admin.name column (the default initial sort) LEFT JOINs
        // admins in behind the scenes with no explicit select, so a bare
        // SELECT * would let admins.id silently overwrite payrolls.id on
        // every row - which is exactly what made Mark as Paid receive the
        // staff member's admin_id instead of the payroll row's own id.
        $posts = Payroll::select('payrolls.*')
            ->with(['admin:id,name', 'lockedBy:id,name'])
            ->latest('year')
            ->latest('month');

        if ($hasRequestFilter) {
            $posts->FilterStaff($request->staff_id)
                ->FilterStatus($request->status)
                ->FilterPaymentStatus($request->payment_status)
                ->FilterMonth($request->month)
                ->FilterYear($request->year)
                ->FilterGeneratedFrom($request->generated_from)
                ->FilterGeneratedTo($request->generated_to);
        } else {
            // No filter params on this request at all (e.g. a direct hit on this
            // endpoint) - fall back to the admin's persisted filter, same as
            // Attendance/Leads.
            $saved = PayrollSaveFilter::where('admin_id', $admin->id)->first();

            if ($saved) {
                $data = $saved->filter_data ?? [];
                $posts->FilterStaff($data['staff_id'] ?? null)
                    ->FilterStatus($data['status'] ?? null)
                    ->FilterPaymentStatus($data['payment_status'] ?? null)
                    ->FilterMonth($data['month'] ?? null)
                    ->FilterYear($data['year'] ?? null)
                    ->FilterGeneratedFrom($data['generated_from'] ?? null)
                    ->FilterGeneratedTo($data['generated_to'] ?? null);
            }
        }

        return DataTables::of($posts)
            ->filter(function ($query) use ($request) {
                $searchText = data_get($request->all(), 'search.value');
                if (filled($searchText)) {
                    $query->FilterSearchText($searchText);
                }
            })
            ->addColumn('staff_id', fn ($row) => optional($row->admin)->id ?? '-')
            ->addColumn('staff_name', fn ($row) => optional($row->admin)->name ?? '-')
            ->addColumn('period', fn ($row) => \Carbon\Carbon::create($row->year, $row->month, 1)->format('F Y'))
            ->addColumn('total_paid', fn ($row) => $row->total_paid)
            ->addColumn('status_badge', function ($row) {
                return $row->status === 'locked'
                    ? '<span class="badge bg-label-success">Locked (Final)</span>'
                    : '<span class="badge bg-label-warning">Draft</span>';
            })
            ->addColumn('payment_badge', function ($row) {
                if ($row->payment_status === Payroll::PAYMENT_PAID) {
                    $viewUrl = route('admin.salary.payroll.slip.view', $row->id);
                    $downloadUrl = route('admin.salary.payroll.slip.download', $row->id);
                    $ext = $row->payment_slip ? strtolower(pathinfo($row->payment_slip, PATHINFO_EXTENSION)) : '';

                    return '
                        <a href="javascript:void(0);" class="payroll-slip-preview-trigger badge bg-label-success" data-id="' . $row->id . '" data-ext="' . $ext . '" data-url="' . $viewUrl . '" data-download-url="' . $downloadUrl . '" title="Click to view salary slip">
                            Paid <i class="ti ti-eye ti-xs align-middle"></i>
                        </a>
                    ';
                }

                return '
                    <a href="javascript:void(0);" class="payroll-mark-paid-trigger" data-id="' . $row->id . '" title="Mark as Paid">
                        <span class="badge bg-label-warning">Pending</span>
                    </a>
                ';
            })
            ->addColumn('actions', function ($row) {
                // Same adminId/month/year pair the Salary Dashboard's own
                // "Download Salary Slip" / "Download Attendance Slip" modals
                // use (SalaryController::downloadSlip / AttendanceController::
                // downloadSlip) - reused as-is so a row's slip is always the
                // slip for that row's own staff member + period, never the
                // page's current date.
                $slipParams = ['adminId' => $row->admin_id, 'month' => $row->month, 'year' => $row->year];

                $viewSlipUrl = route('admin.salary.slip.download', $slipParams + ['view' => 1]);
                $downloadSalarySlipUrl = route('admin.salary.slip.download', $slipParams);
                $downloadAttendanceSlipUrl = route('admin.attendance.slip.download', $slipParams);

                $items = '<a class="dropdown-item" href="' . $viewSlipUrl . '" target="_blank"><i class="ti ti-eye me-2"></i> View Slip</a>';
                $items .= '<a class="dropdown-item" href="' . $downloadSalarySlipUrl . '"><i class="ti ti-download me-2"></i> Download Salary Slip</a>';
                $items .= '<a class="dropdown-item" href="' . $downloadAttendanceSlipUrl . '"><i class="ti ti-calendar-stats me-2"></i> Download Attendance Slip</a>';

                if ($row->status === 'draft') {
                    $items .= '<a class="dropdown-item payroll-lock-trigger" href="javascript:void(0);" data-id="' . $row->id . '"><i class="ti ti-lock me-2"></i> Lock (Finalize)</a>';
                }

                if ($row->payment_status === Payroll::PAYMENT_PAID) {
                    // The uploaded proof-of-payment file (distinct from the
                    // generated Salary Slip PDF above), same file the Payment
                    // column's "Paid" badge already links to.
                    $receiptUrl = route('admin.salary.payroll.slip.download', $row->id);
                    $items .= '<a class="dropdown-item" href="' . $receiptUrl . '"><i class="ti ti-receipt me-2"></i> Download Payment Receipt</a>';
                } else {
                    $items .= '<a class="dropdown-item payroll-mark-paid-trigger" href="javascript:void(0);" data-id="' . $row->id . '"><i class="ti ti-cash me-2"></i> Mark as Paid</a>';
                }

                $items .= '<a class="dropdown-item payroll-delete-trigger text-danger" href="javascript:void(0);" data-id="' . $row->id . '" data-status="' . $row->status . '"><i class="ti ti-trash me-2"></i> Delete</a>';

                return '
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="payrollActions' . $row->id . '" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="payrollActions' . $row->id . '">
                            ' . $items . '
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['status_badge', 'payment_badge', 'actions'])
            ->make(true)
            // This listing is the only source the Mark as Paid modal has for a
            // row's id. A GET response with no explicit cache directive can be
            // served from the browser's disk/back-forward cache on the next
            // "fresh" page load - silently showing ids from a previous
            // database state (e.g. before a reseed) instead of the current
            // one, which is exactly what makes a stale-id 404 look
            // unreproducible server-side yet happen "every time" client-side.
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
    }

    public function payrollFilterSave(Request $request)
    {
        $admin = $this->loggedAdmin();

        $filterData = [
            'staff_id' => $request->staff_id,
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'month' => $request->month,
            'year' => $request->year,
            'generated_from' => $request->generated_from,
            'generated_to' => $request->generated_to,
        ];

        $filter = PayrollSaveFilter::updateOrCreate(
            ['admin_id' => $admin->id],
            ['filter_data' => $filterData]
        );

        return response()->json([
            'res' => $filter->wasRecentlyCreated ? 'Filter saved successfully!' : 'Filter updated successfully!',
        ]);
    }

    public function payrollFilterReset(Request $request)
    {
        $admin = $this->loggedAdmin();

        PayrollSaveFilter::where('admin_id', $admin->id)->delete();

        return response()->json(['res' => 'Filter reset successfully!']);
    }

    public function payrollGenerate(Request $request, PayrollService $payrollService)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canApprove($admin)) {
            return response()->json(['res' => 'You are not authorized to generate payroll.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|integer|exists:admins,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2000,2100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $staff = Admin::findOrFail($request->admin_id);

        try {
            $payrollService->generate($staff, (int) $request->month, (int) $request->year, $admin);
        } catch (\RuntimeException $e) {
            return response()->json(['res' => $e->getMessage()], 422);
        }

        return response()->json(['res' => 'Payroll generated successfully!']);
    }

    /**
     * Generate payroll for every staff member who has at least one
     * attendance record in the given month/year - "attendance wise" bulk
     * generation, so nobody with zero attendance activity gets a payroll.
     * Staff whose payroll for that period is already locked are skipped,
     * not failed, since generate() never touches a locked row.
     */
    public function payrollGenerateAll(Request $request, PayrollService $payrollService)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canApprove($admin)) {
            return response()->json(['res' => 'You are not authorized to generate payroll.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|between:2000,2100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $month = (int) $request->month;
        $year = (int) $request->year;

        $adminIds = AttendanceLog::whereYear('attendance_time', $year)
            ->whereMonth('attendance_time', $month)
            ->distinct()
            ->pluck('admin_id');

        if ($adminIds->isEmpty()) {
            return response()->json(['res' => 'No staff have attendance records for that month.'], 422);
        }

        $staffMembers = Admin::whereIn('id', $adminIds)->get();

        $generated = 0;
        $skipped = 0;

        foreach ($staffMembers as $staff) {
            try {
                $payrollService->generate($staff, $month, $year, $admin);
                $generated++;
            } catch (\RuntimeException $e) {
                $skipped++;
            }
        }

        $message = "Generated payroll for {$generated} staff member(s) with attendance this period.";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} already locked.";
        }

        return response()->json(['res' => $message]);
    }

    public function payrollShow(Request $request, $id)
    {
        $admin = $this->loggedAdmin();

        if (!$this->isSuperAdmin($admin)) {
            return response()->json(['res' => 'You are not authorized to view payroll records.'], 403);
        }

        $payroll = Payroll::with('admin:id,name')->find($id);

        if (!$payroll) {
            return response()->json(['res' => 'Payroll record not found.'], 404);
        }

        return response()->json($payroll);
    }

    public function payrollLock(Request $request, PayrollService $payrollService)
    {
        $admin = $this->loggedAdmin();

        if (!$this->isSuperAdmin($admin)) {
            return response()->json(['res' => 'You are not authorized to lock payroll.'], 403);
        }

        $payroll = Payroll::find($request->id);

        if (!$payroll) {
            return response()->json(['res' => 'Payroll record not found.'], 404);
        }

        $payrollService->lock($payroll, $admin);

        return response()->json(['res' => 'Payroll locked as Final Salary.']);
    }

    public function payrollUpdate(Request $request, PayrollService $payrollService)
    {
        $admin = $this->loggedAdmin();

        if (!$this->isSuperAdmin($admin)) {
            return response()->json(['res' => 'You are not authorized to edit payroll.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:payrolls,id',
            'monthly_salary' => 'required|numeric|min:0',
            'present_days' => 'required|integer|min:0',
            'absent_days' => 'required|integer|min:0',
            'half_days' => 'required|integer|min:0',
            'qualifying_late_count' => 'required|integer|min:0',
            'late_count' => 'required|integer|min:0',
            'late_deduction' => 'required|numeric|min:0',
            'absent_deduction' => 'required|numeric|min:0',
            'half_day_deduction' => 'required|numeric|min:0',
            'sunday_deduction' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payroll = Payroll::findOrFail($request->id);
        $payrollService->applyManualUpdate($payroll, $request->all());

        return response()->json(['res' => 'Payroll updated successfully!']);
    }

    public function payrollDelete(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canApprove($admin)) {
            return response()->json(['res' => 'You are not authorized to delete payroll.'], 403);
        }

        $payroll = Payroll::find($request->id);

        if (!$payroll) {
            return response()->json(['res' => 'Payroll record not found.'], 404);
        }

        // Super Admin can delete a payroll regardless of lock status - with
        // Unlock removed, this is now the only way to undo a locked record.
        $payroll->delete();

        return response()->json(['res' => 'Payroll deleted successfully!']);
    }

    /**
     * Mark a payroll's Payment Status as Paid - a Salary Slip upload is
     * mandatory (same pattern as Testimonial::markPaid() /
     * GoogleReview::markPaid()): the file is stored first, and the payment
     * fields are only written if that upload succeeds, so a failed upload
     * can never leave a record marked Paid without a slip on file.
     *
     * Marking Paid also locks the payroll (if it wasn't already) - once
     * money has actually been disbursed the underlying calculation must
     * stop being editable/regenerable, otherwise a later "Generate Payroll"
     * run could silently overwrite an already-paid record (generate() only
     * ever refuses to touch a *locked* row).
     */
    public function payrollMarkPaid(Request $request, PayrollService $payrollService)
    {
        $admin = $this->loggedAdmin();

        $this->authorizePayrollAccess($admin);

        Log::info('Payroll markPaid: request received', [
            'admin_id' => $admin->id,
            'payroll_id_raw' => $request->input('payroll_id'),
            'has_file' => $request->hasFile('payment_slip'),
        ]);

        // payroll_id is only checked for *shape* here (present, integer) -
        // whether it still refers to a real row is checked separately below
        // via Payroll::find(), the same way payrollLock()/payrollDelete()
        // already do it in this controller. Folding that into an
        // exists:payrolls,id rule would report both cases as one generic
        // "invalid" 422 - indistinguishable from a malformed request - when
        // they need different handling: a genuinely bad id is a validation
        // error, but an id that simply no longer exists (the row was
        // deleted, or "Generate Payroll" replaced it with a fresh id since
        // the table was last loaded - a real gap, since serverSide
        // DataTables never re-fetches a row the user hasn't paged to) is a
        // stale-table condition the client can recover from by refreshing.
        $validator = Validator::make($request->all(), [
            'payroll_id' => 'required|integer',
            'payment_slip' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5 MB
            'extra_paid' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            Log::warning('Payroll markPaid: validation failed', [
                'admin_id' => $admin->id,
                'payroll_id_raw' => $request->input('payroll_id'),
                'errors' => $validator->errors()->toArray(),
            ]);

            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payroll = Payroll::find($request->payroll_id);

        if (!$payroll) {
            Log::warning('Payroll markPaid: payroll id not found (stale row)', [
                'admin_id' => $admin->id,
                'payroll_id' => $request->payroll_id,
                'existing_ids' => Payroll::pluck('id'),
                // The row's own id and its owning staff member's id are two
                // different numbers - if this ever matches an admins.id, the
                // client sent the staff id instead of the payroll row id,
                // which points at a stale/cached page rather than a genuinely
                // deleted row.
                'looks_like_admin_id' => Admin::whereKey($request->payroll_id)->exists(),
            ]);

            return response()->json([
                'res' => 'This payroll record is no longer available (it may have been deleted or regenerated). The list has been refreshed - please try again.',
            ], 404);
        }

        Log::info('Payroll markPaid: resolved payroll', [
            'payroll_id' => $payroll->id,
            'payroll_admin_id' => $payroll->admin_id,
            'status' => $payroll->status,
            'payment_status' => $payroll->payment_status,
        ]);

        if ($payroll->payment_status === Payroll::PAYMENT_PAID) {
            Log::info('Payroll markPaid: already paid, rejecting', ['payroll_id' => $payroll->id]);

            return response()->json([
                'res' => 'This payroll has already been marked as paid.',
            ], 422);
        }

        try {
            $file = $request->file('payment_slip');
            $fileName = time() . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
            $stored = $file->storeAs('payroll/payment_slips', $fileName, 'public');

            if (!$stored) {
                throw new \RuntimeException('Salary slip could not be stored.');
            }
        } catch (\Throwable $e) {
            // Do not mark the payroll as Paid if the slip upload fails.
            Log::error('Payroll markPaid: slip upload failed', [
                'payroll_id' => $payroll->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'res' => 'Salary slip upload failed. Payroll was not marked as paid. Please try again.',
            ], 500);
        }

        Log::info('Payroll markPaid: slip uploaded', ['payroll_id' => $payroll->id, 'file' => $fileName]);

        if ($payroll->status !== 'locked') {
            $payrollService->lock($payroll, $admin);
        }

        $payroll->payment_status = Payroll::PAYMENT_PAID;
        $payroll->payment_slip = $fileName;
        $payroll->paid_by = $admin->id;
        $payroll->paid_at = now();
        $payroll->extra_paid = $request->filled('extra_paid') ? $request->extra_paid : 0;
        $payroll->save();

        Log::info('Payroll markPaid: marked as paid', [
            'payroll_id' => $payroll->id,
            'paid_by' => $admin->id,
            'paid_at' => (string) $payroll->paid_at,
            'extra_paid' => $payroll->extra_paid,
            'total_paid' => $payroll->total_paid,
        ]);

        return response()->json([
            'res' => 'Payroll marked as paid successfully!',
            'payment_status' => $payroll->payment_status,
            'paid_by_name' => $admin->name,
            'paid_at' => $payroll->paid_at->format('d M Y, h:i A'),
        ]);
    }

    /**
     * Resolve an uploaded salary slip to an on-disk path for one specific
     * payroll, so a slip can't be fetched via another payroll's id.
     */
    private function resolvePayrollSlipPath(Payroll $payroll): string
    {
        if (!$payroll->payment_slip) {
            abort(404, 'No salary slip found for this payroll.');
        }

        $path = storage_path('app/public/payroll/payment_slips/' . basename($payroll->payment_slip));

        if (!file_exists($path)) {
            abort(404, 'Salary slip file not found.');
        }

        return $path;
    }

    /**
     * Stream an uploaded salary slip inline (for browser preview).
     */
    public function payrollSlipView(Request $request, $id)
    {
        $admin = $this->loggedAdmin();
        $this->authorizePayrollAccess($admin);

        $payroll = Payroll::findOrFail($id);
        $path = $this->resolvePayrollSlipPath($payroll);

        return response()->file($path);
    }

    /**
     * Force-download an uploaded salary slip.
     */
    public function payrollSlipDownload(Request $request, $id)
    {
        $admin = $this->loggedAdmin();
        $this->authorizePayrollAccess($admin);

        $payroll = Payroll::findOrFail($id);
        $path = $this->resolvePayrollSlipPath($payroll);

        return response()->download($path, basename($payroll->payment_slip));
    }

    /**
     * Advance Payment list (Advance Payment management modal) - same access
     * tier as the Payroll page/table itself (authorizePayrollAccess), since
     * it lives inside the same screen.
     */
    public function advancePaymentDatatable(Request $request)
    {
        $admin = $this->loggedAdmin();
        $this->authorizePayrollAccess($admin);

        $posts = AdvancePayment::select('advance_payments.*')
            ->with(['admin:id,name', 'payroll:id,month,year,status,payment_status'])
            ->FilterStaff($request->staff_id)
            ->latest('advance_date');

        return DataTables::of($posts)
            ->addColumn('staff_id', fn ($row) => optional($row->admin)->id ?? '-')
            ->addColumn('staff_name', fn ($row) => optional($row->admin)->name ?? '-')
            ->addColumn('date_time', fn ($row) => optional($row->advance_date)->format('d M Y, h:i A'))
            ->addColumn('amount_fmt', fn ($row) => number_format((float) $row->amount, 2))
            ->addColumn('payroll_month', fn ($row) => \Carbon\Carbon::create($row->year, $row->month, 1)->format('F Y'))
            ->addColumn('status_badge', function ($row) {
                $status = $row->status;
                $classes = [
                    'Pending' => 'bg-label-warning',
                    'Applied' => 'bg-label-info',
                    'Locked' => 'bg-label-secondary',
                    'Paid' => 'bg-label-success',
                ];

                return '<span class="badge ' . ($classes[$status] ?? 'bg-label-secondary') . '">' . $status . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $isLocked = $row->status === 'Locked' || $row->status === 'Paid';

                if ($isLocked) {
                    return '<span class="text-muted small"><i class="ti ti-lock ti-xs me-1"></i>Locked</span>';
                }

                return '
                    <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-text-primary advance-edit-trigger" data-id="' . $row->id . '" title="Edit">
                        <i class="ti ti-edit"></i>
                    </a>
                    <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-text-danger advance-delete-trigger" data-id="' . $row->id . '" title="Delete">
                        <i class="ti ti-trash"></i>
                    </a>
                ';
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    private function advancePaymentValidationRules(): array
    {
        return [
            'admin_id' => [
                'required',
                'integer',
                Rule::exists('admins', 'id')->where(fn ($q) => $q->where('status', 1)),
            ],
            'advance_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:2000',
        ];
    }

    private function advancePaymentValidationMessages(): array
    {
        return [
            'admin_id.required' => 'Staff is required.',
            'admin_id.exists' => 'Selected staff member is not an active staff/associate.',
            'advance_date.required' => 'Advance Payment Date & Time is required.',
            'advance_date.date' => 'Advance Payment Date & Time is invalid.',
            'amount.required' => 'Advance Amount is required.',
            'amount.numeric' => 'Advance Amount must be a number.',
            'amount.min' => 'Advance Amount must be greater than 0.',
        ];
    }

    public function advancePaymentStore(Request $request, AdvancePaymentService $advancePaymentService)
    {
        $admin = $this->loggedAdmin();
        $this->authorizePayrollAccess($admin);

        $validator = Validator::make(
            $request->all(),
            $this->advancePaymentValidationRules(),
            $this->advancePaymentValidationMessages()
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $staff = Admin::findOrFail($request->admin_id);

        try {
            $advance = $advancePaymentService->create(
                $staff,
                \Carbon\Carbon::parse($request->advance_date),
                (float) $request->amount,
                $request->remarks,
                $admin
            );
        } catch (\RuntimeException $e) {
            return response()->json(['res' => $e->getMessage()], 422);
        }

        return response()->json(['res' => 'Advance payment added successfully!', 'id' => $advance->id]);
    }

    public function advancePaymentShow(Request $request, $id)
    {
        $admin = $this->loggedAdmin();
        $this->authorizePayrollAccess($admin);

        $advance = AdvancePayment::find($id);

        if (!$advance) {
            return response()->json(['res' => 'Advance payment record not found.'], 404);
        }

        return response()->json([
            'id' => $advance->id,
            'admin_id' => $advance->admin_id,
            'advance_date' => optional($advance->advance_date)->format('Y-m-d\TH:i'),
            'amount' => (float) $advance->amount,
            'remarks' => $advance->remarks,
            'status' => $advance->status,
        ]);
    }

    public function advancePaymentUpdate(Request $request, AdvancePaymentService $advancePaymentService)
    {
        $admin = $this->loggedAdmin();
        $this->authorizePayrollAccess($admin);

        $rules = ['id' => 'required|integer'] + $this->advancePaymentValidationRules();
        $validator = Validator::make($request->all(), $rules, $this->advancePaymentValidationMessages());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $advance = AdvancePayment::find($request->id);

        if (!$advance) {
            return response()->json(['res' => 'This advance payment record is no longer available. The list has been refreshed - please try again.'], 404);
        }

        $staff = Admin::findOrFail($request->admin_id);

        try {
            $advancePaymentService->update(
                $advance,
                $staff,
                \Carbon\Carbon::parse($request->advance_date),
                (float) $request->amount,
                $request->remarks,
                $admin
            );
        } catch (\RuntimeException $e) {
            return response()->json(['res' => $e->getMessage()], 422);
        }

        return response()->json(['res' => 'Advance payment updated successfully!']);
    }

    public function advancePaymentDelete(Request $request, AdvancePaymentService $advancePaymentService)
    {
        $admin = $this->loggedAdmin();
        $this->authorizePayrollAccess($admin);

        $validator = Validator::make($request->all(), ['id' => 'required|integer']);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $advance = AdvancePayment::find($request->id);

        if (!$advance) {
            return response()->json(['res' => 'This advance payment record is no longer available. The list has been refreshed - please try again.'], 404);
        }

        try {
            $advancePaymentService->delete($advance);
        } catch (\RuntimeException $e) {
            return response()->json(['res' => $e->getMessage()], 422);
        }

        return response()->json(['res' => 'Advance payment deleted successfully!']);
    }

    /**
     * Advance Payment breakdown for one payroll - feeds the Payment Details
     * modal's "Advance Payment" section. Only ever returns advances actually
     * linked to this payroll (payroll_id = $id), which for a Locked/Paid
     * payroll is a frozen list matching its advance_deduction figure.
     */
    public function payrollAdvances(Request $request, $id)
    {
        $admin = $this->loggedAdmin();
        $this->authorizePayrollAccess($admin);

        $payroll = Payroll::find($id);

        if (!$payroll) {
            return response()->json(['res' => 'Payroll record not found.'], 404);
        }

        $advances = AdvancePayment::where('payroll_id', $payroll->id)
            ->orderBy('advance_date')
            ->get(['id', 'advance_date', 'amount', 'remarks'])
            ->map(fn ($row) => [
                'date_time' => optional($row->advance_date)->format('d M Y, h:i A'),
                'amount' => (float) $row->amount,
                'remarks' => $row->remarks,
            ]);

        return response()->json(['advances' => $advances, 'total' => (float) $payroll->advance_deduction]);
    }

    private function payrollToLiveShape(Payroll $payroll, Admin $admin, SalaryCalculationService $calculator): array
    {
        // A locked payroll is a finalized snapshot of a (by definition)
        // complete period, so it always covers the full month here - unlike
        // the live calculation, it never gets cut off at "today".
        $monthStart = \Carbon\Carbon::create($payroll->year, $payroll->month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->startOfDay();

        return [
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'month' => $payroll->month,
            'year' => $payroll->year,
            'monthly_salary' => (float) $payroll->monthly_salary,
            'daily_salary' => (float) $payroll->daily_salary,
            'days_in_month' => $payroll->days_in_month,
            'elapsed_days' => $payroll->days_in_month,
            'payable_gross_salary' => (float) $payroll->monthly_salary,
            'current_date' => now()->toDateString(),
            'present' => $payroll->present_days,
            'absent' => $payroll->absent_days,
            'half_days' => $payroll->half_days,
            'late_marks' => $payroll->qualifying_late_count,
            'late_marks_non_qualifying' => $payroll->late_count,
            'total_late_count' => $payroll->qualifying_late_count + $payroll->late_count,
            'holiday_days' => $payroll->holiday_days,
            'weekly_off_days' => $calculator->countSundays($monthStart, $monthEnd),
            'sunday_off_count' => null,
            'late_deduction' => (float) $payroll->late_deduction,
            'absent_deduction' => (float) $payroll->absent_deduction,
            'half_day_deduction' => (float) $payroll->half_day_deduction,
            'sunday_deduction' => (float) $payroll->sunday_deduction,
            'total_deduction' => (float) $payroll->total_deduction,
            'net_payable' => (float) $payroll->net_payable,
            'is_locked' => true,
            'locked_at' => optional($payroll->locked_at)->format('d M Y h:i A'),
        ];
    }
}
