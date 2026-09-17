<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\AttendanceApprovalLog;
use App\Models\AttendanceLog;
use App\Models\AttendanceSaveFilter;
use App\Models\Holiday;
use App\Models\SalarySetting;
use App\Services\AdvancePaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    /**
     * Per-request cache so a single request never queries Adminpermission
     * more than once for the same admin (authorizeAttendance, canViewAll and
     * index() all need it).
     */
    private $permissionCache = [];

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

    private function adminPermission($admin)
    {
        if (!array_key_exists($admin->id, $this->permissionCache)) {
            $this->permissionCache[$admin->id] = Adminpermission::where('staff_id', $admin->id)->first();
        }

        return $this->permissionCache[$admin->id];
    }

    /**
     * Gate the Attendance module itself so a direct hit on
     * /admin/attendance/list still respects the dedicated Attendance
     * permission, same as the sidebar visibility rules. Super Admin and
     * staff with full_access always pass.
     */
    private function authorizeAttendance($admin)
    {
        if ($this->isSuperAdmin($admin)) {
            return;
        }

        $permission = $this->adminPermission($admin);

        if (optional($permission)->full_access == 1) {
            return;
        }

        if (optional($permission)->hr_attendance != 1) {
            abort(403, 'You do not have permission to access Attendance.');
        }
    }

    /**
     * Attendance "View All" - Super Admin, full_access, or the dedicated
     * hr_attendance_view_all permission. Grants visibility across every
     * staff member's attendance, same scope as a Manager's team but without
     * being tied to the "Team Head" role tag.
     */
    private function canViewAll($admin)
    {
        if ($this->isSuperAdmin($admin)) {
            return true;
        }

        $permission = $this->adminPermission($admin);

        return optional($permission)->full_access == 1 || optional($permission)->hr_attendance_view_all == 1;
    }

    /**
     * Final Approval Access - configured per-admin from Salary Settings
     * (Settings tab -> Final Approval Access), same "Final Approver" role
     * tag mechanism as Team Head. Super Admin always has it.
     */
    private function isFinalApprover($admin)
    {
        $roles = json_decode((string) $admin->role, true) ?: [];
        return $this->isSuperAdmin($admin) || in_array('Final Approver', $roles);
    }

    /**
     * Records a row in the approval audit trail. Called from every
     * approve/reject action (both phases) so there's always a durable
     * history of who did what and when, independent of the current state
     * of the attendance_logs row itself.
     */
    private function logApproval(AttendanceLog $log, string $type, string $action, ?string $previousStatus, string $newStatus, $admin, ?string $remarks = null)
    {
        AttendanceApprovalLog::create([
            'attendance_log_id' => $log->id,
            'approval_type' => $type,
            'action' => $action,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'admin_id' => $admin->id,
            'remarks' => $remarks,
        ]);
    }

    /**
     * A Manager's team = themselves + staff they created (admins.createby_id).
     * There is no dedicated team/department table, so this reuses the existing
     * createby_id link that StaffController already relies on.
     */
    private function teamAdminIds($admin)
    {
        return Admin::where('id', $admin->id)
            ->orWhere('createby_id', $admin->id)
            ->pluck('id');
    }

    /**
     * Can $admin view the attendance of $targetAdminId? Everyone can view
     * their own; Manager only within their team; Super Admin / View All can
     * view anyone.
     */
    private function canView($admin, $targetAdminId)
    {
        if ((int) $targetAdminId === (int) $admin->id) {
            return true;
        }

        if ($this->canViewAll($admin)) {
            return true;
        }

        return $this->isManager($admin) && $this->teamAdminIds($admin)->contains((int) $targetAdminId);
    }

    /**
     * One Time In and one Time Out per staff member per day, and Time Out
     * can't be recorded before a Time In exists for that same day.
     * $excludeId lets update() re-check without conflicting with itself.
     */
    private function duplicateOrSequenceError($adminId, $type, $date, $excludeId = null)
    {
        $existing = fn ($t) => AttendanceLog::where('admin_id', $adminId)
            ->whereDate('attendance_time', $date)
            ->where('attendance_type', $t)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        if ($type === 'in' && $existing('in')) {
            return 'Time In has already been recorded for this staff member on the selected date.';
        }

        if ($type === 'out') {
            if ($existing('out')) {
                return 'Time Out has already been recorded for this staff member on the selected date.';
            }

            if (!$existing('in')) {
                return 'Please record Time In before adding Time Out.';
            }
        }

        return null;
    }

    public function index(Request $request)
    {
        $admin = $this->loggedAdmin();

        $this->authorizeAttendance($admin);

        $canViewAll = $this->canViewAll($admin);

        // Staff filter list: everyone with View All sees every active staff
        // member; everyone else (including a Manager without View All) is
        // scoped to their own team (teamAdminIds already narrows to just
        // themselves for a non-manager).
        $staffList = $canViewAll
            ? Admin::isActiveAdmin()->orderBy('name')->get(['id', 'name'])
            : Admin::isActiveAdmin()->whereIn('id', $this->teamAdminIds($admin))->orderBy('name')->get(['id', 'name']);

        $savedFilter = AttendanceSaveFilter::where('admin_id', $admin->id)->first();

        $isSuperAdmin = $this->isSuperAdmin($admin);
        $isManager = $this->isManager($admin);
        $canApprove = $this->canApprove($admin);
        $canFinalApprove = $this->isFinalApprover($admin);

        // Time In / Out "on behalf of" picker for Manager & Super Admin - any
        // active staff member, independent of the Manager's view/approve team scope.
        $allActiveStaff = $canApprove
            ? Admin::isActiveAdmin()->where('status', 1)->orderBy('name')->get(['id', 'name'])
            : collect();

        $permission = $this->adminPermission($admin);

        return view('admin.attendance.index', compact(
            'staffList',
            'allActiveStaff',
            'savedFilter',
            'isSuperAdmin',
            'isManager',
            'canApprove',
            'canViewAll',
            'canFinalApprove',
            'permission'
        ));
    }

    public function datatable(Request $request)
    {
        $admin = $this->loggedAdmin();

        $this->authorizeAttendance($admin);

        $filterFields = ['staff_id', 'attendance_type', 'status', 'approved_by', 'date_from', 'date_to'];
        $hasRequestFilter = collect($filterFields)->contains(fn ($field) => filled($request->$field));

        $posts = AttendanceLog::with(['admin:id,name', 'approver:id,name', 'finalApprover:id,name'])
            ->when(!$this->canViewAll($admin), function ($query) use ($admin) {
                if ($this->isManager($admin)) {
                    $query->whereIn('admin_id', $this->teamAdminIds($admin));
                } else {
                    $query->where('admin_id', $admin->id);
                }
            })
            ->latest('attendance_time');

        if ($hasRequestFilter) {
            $posts->FilterStaff($request->staff_id)
                ->FilterType($request->attendance_type)
                ->FilterStatus($request->status)
                ->FilterApprovedBy($request->approved_by)
                ->FilterDateFrom($request->date_from)
                ->FilterDateTo($request->date_to);
        } else {
            // No filter params on this request at all (e.g. a direct hit on this
            // endpoint) - fall back to the admin's persisted filter, same as Leads.
            $saved = AttendanceSaveFilter::where('admin_id', $admin->id)->first();

            if ($saved) {
                $data = $saved->filter_data ?? [];
                $posts->FilterStaff($data['staff_id'] ?? null)
                    ->FilterType($data['attendance_type'] ?? null)
                    ->FilterStatus($data['status'] ?? null)
                    ->FilterApprovedBy($data['approved_by'] ?? null)
                    ->FilterDateFrom($data['date_from'] ?? null)
                    ->FilterDateTo($data['date_to'] ?? null);
            }
        }

        return DataTables::of($posts)
            ->filter(function ($query) use ($request) {
                $searchText = data_get($request->all(), 'search.value');
                if (filled($searchText)) {
                    $query->FilterSearchText($searchText);
                }
            })
            ->addColumn('staff_id', fn ($row) => $row->admin_id)
            ->addColumn('staff_name', fn ($row) => optional($row->admin)->name ?? '-')
            ->addColumn('date', fn ($row) => optional($row->attendance_time)->format('d M Y'))
            ->addColumn('in_time', fn ($row) => $row->attendance_type == 'in' ? optional($row->attendance_time)->format('h:i A') : '-')
            ->addColumn('out_time', fn ($row) => $row->attendance_type == 'out' ? optional($row->attendance_time)->format('h:i A') : '-')
            ->addColumn('normal_approval', function ($row) use ($admin) {
                $badgeClasses = [
                    'waiting' => 'bg-label-warning',
                    'approved' => 'bg-label-success',
                    'rejected' => 'bg-label-danger',
                ];
                $labels = [
                    'waiting' => 'Waiting for Approval',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ];
                $badgeClass = $badgeClasses[$row->status] ?? 'bg-label-dark';
                $label = $labels[$row->status] ?? ucfirst($row->status);
                $badge = '<span class="badge ' . $badgeClass . '">' . $label . '</span>';

                $meta = '';
                // if ($row->approved_by) {
                //     $meta = '<div class="small text-muted mt-1">' . e(optional($row->approver)->name ?? '-')
                //         . '<br>' . ($row->approved_at ? $row->approved_at->format('d M Y h:i A') : '') . '</div>';
                // }

                // Super Admin has no status restriction and can re-open any
                // record; Manager still only acts on records that are waiting.
                $canChangeStatus = $this->isSuperAdmin($admin) || ($this->canApprove($admin) && $row->status == 'waiting');

                if ($canChangeStatus) {
                    return '
                        <a href="javascript:void(0);" class="attendance-status-trigger" data-bs-toggle="modal" data-bs-target="#attendanceStatusModal" data-type="normal" data-id="' . $row->id . '">
                            ' . $badge . '
                        </a>
                    ' . $meta;
                }

                return $badge . $meta;
            })
            ->addColumn('final_approval', function ($row) use ($admin) {
                if ($row->attendance_type !== 'in') {
                    return '<span class="text-muted">-</span>';
                }

                if ($row->status !== 'approved') {
                    return '<span class="badge bg-label-secondary">Awaiting Normal Approval</span>';
                }

                $badgeClasses = [
                    'pending' => 'bg-label-warning',
                    'approved' => 'bg-label-success',
                    'rejected' => 'bg-label-danger',
                ];
                $labels = [
                    'pending' => 'Pending Final Approval',
                    'approved' => 'Final Approved',
                    'rejected' => 'Final Rejected',
                ];
                $badgeClass = $badgeClasses[$row->final_status] ?? 'bg-label-dark';
                $label = $labels[$row->final_status] ?? ucfirst($row->final_status);
                $badge = '<span class="badge ' . $badgeClass . '">' . $label . '</span>';

                $meta = '';
                // if ($row->final_approved_by) {
                //     $meta = '<div class="small text-muted mt-1">' . e(optional($row->finalApprover)->name ?? '-')
                //         . '<br>' . ($row->final_approved_at ? $row->final_approved_at->format('d M Y h:i A') : '') . '</div>';
                // }

                // Super Admin has no status restriction; other Final Approvers
                // only act while final approval is still pending.
                $canChangeFinal = $this->isFinalApprover($admin) && ($this->isSuperAdmin($admin) || $row->final_status == 'pending');

                if ($canChangeFinal) {
                    return '
                        <a href="javascript:void(0);" class="attendance-status-trigger" data-bs-toggle="modal" data-bs-target="#attendanceStatusModal" data-type="final" data-id="' . $row->id . '">
                            ' . $badge . '
                        </a>
                    ' . $meta;
                }

                return $badge . $meta;
            })
            ->addColumn('payable_badge', function ($row) {
                if ($row->attendance_type !== 'in') {
                    return '<span class="text-muted">-</span>';
                }

                return $row->is_fully_approved
                    ? '<span class="badge bg-label-success">Payable</span>'
                    : '<span class="badge bg-label-secondary">Not Payable</span>';
            })
            ->addColumn('actions', function ($row) use ($admin) {
                $canApprove = $this->canApprove($admin);
                $canFinalApprove = $this->isFinalApprover($admin);
                $isWaiting = $row->status == 'waiting';
                $canChangeStatus = $this->isSuperAdmin($admin) || ($canApprove && $isWaiting);
                $canChangeFinal = $row->attendance_type === 'in'
                    && $row->status === 'approved'
                    && $canFinalApprove
                    && ($this->isSuperAdmin($admin) || $row->final_status == 'pending');
                $items = '';

                if ($canChangeStatus) {
                    $items .= '
                        <a class="dropdown-item attendance-status-trigger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendanceStatusModal" data-id="' . $row->id . '" data-type="normal" data-preset="approve">
                            <i class="ti ti-check me-2"></i> Approve
                        </a>
                        <a class="dropdown-item attendance-status-trigger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendanceStatusModal" data-id="' . $row->id . '" data-type="normal" data-preset="reject">
                            <i class="ti ti-x me-2"></i> Reject
                        </a>
                    ';
                }

                if ($canChangeFinal) {
                    $items .= '
                        <a class="dropdown-item attendance-status-trigger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendanceStatusModal" data-id="' . $row->id . '" data-type="final" data-preset="approve">
                            <i class="ti ti-shield-check me-2"></i> Final Approve
                        </a>
                        <a class="dropdown-item attendance-status-trigger" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#attendanceStatusModal" data-id="' . $row->id . '" data-type="final" data-preset="reject">
                            <i class="ti ti-shield-x me-2"></i> Final Reject
                        </a>
                    ';
                }

                if ($canApprove) {
                    $items .= '
                        <a class="dropdown-item attendance-edit-trigger" href="javascript:void(0);" data-id="' . $row->id . '">
                            <i class="ti ti-edit me-2"></i> Edit
                        </a>
                        <a class="dropdown-item attendance-delete-trigger text-danger" href="javascript:void(0);" data-id="' . $row->id . '">
                            <i class="ti ti-trash me-2"></i> Delete
                        </a>
                    ';
                }

                $items .= '
                    <a class="dropdown-item attendance-history-trigger" href="javascript:void(0);" data-id="' . $row->id . '">
                        <i class="ti ti-history me-2"></i> Approval History
                    </a>
                ';

                return '
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="attendanceActions' . $row->id . '" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="attendanceActions' . $row->id . '">
                            ' . $items . '
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['normal_approval', 'final_approval', 'payable_badge', 'actions'])
            ->make(true);
    }

    public function create()
    {
        return response()->json([
            'attendance_date' => now()->format('Y-m-d'),
            'attendance_time' => now()->format('H:i'),
        ]);
    }

    public function store(Request $request)
    {
        $admin = $this->loggedAdmin();
        $canManageOthers = $this->canApprove($admin);

        // Only Manager/Super Admin may submit attendance on behalf of someone else.
        if (!$canManageOthers && $request->filled('admin_id') && (int) $request->admin_id !== $admin->id) {
            return response()->json(['res' => 'You are not authorized to create attendance for another staff member.'], 403);
        }

        $targetAdminId = ($canManageOthers && $request->filled('admin_id'))
            ? (int) $request->admin_id
            : $admin->id;

        // Staff can only log attendance for today; Manager & Super Admin may
        // backdate/forward-date it (both get the visible date picker in the UI).
        $attendanceDate = ($canManageOthers && $request->attendance_date)
            ? $request->attendance_date
            : now()->format('Y-m-d');

        $isTimeOut = $request->attendance_type == 'out';

        $lateTimeIn = $request->attendance_type == 'in'
            && $request->attendance_time
            && $request->attendance_time > '10:00';

        $validator = Validator::make($request->all(), [
            'admin_id' => 'nullable|integer|exists:admins,id',
            'attendance_type' => 'required|in:in,out',
            'attendance_time' => 'required|date_format:H:i',
            'attendance_date' => 'nullable|date',
            'reason' => $lateTimeIn ? 'required|string|max:1000' : 'nullable|string|max:1000',
        ], [
            'admin_id.integer' => 'Staff selection is invalid.',
            'admin_id.exists' => 'Selected staff member was not found.',
            'attendance_type.required' => 'Attendance type is required.',
            'attendance_type.in' => 'Attendance type must be Time In or Time Out.',
            'attendance_time.required' => 'Attendance time is required.',
            'attendance_time.date_format' => 'Attendance time is invalid.',
            'attendance_date.date' => 'Attendance date is invalid.',
            'reason.required' => 'Reason is mandatory for a Time In after 10:00 AM.',
            'reason.max' => 'Reason may not be longer than 1000 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Staff cannot back/future-date beyond "now"; Manager & Super Admin are exempt.
        if (!$canManageOthers) {
            $submittedAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $attendanceDate . ' ' . $request->attendance_time);

            if ($submittedAt->gt(now())) {
                return response()->json(['errors' => ['attendance_time' => ['Attendance date/time cannot be in the future.']]], 422);
            }
        }

        $conflict = $this->duplicateOrSequenceError($targetAdminId, $request->attendance_type, $attendanceDate);

        if ($conflict) {
            return response()->json(['errors' => ['attendance_type' => [$conflict]]], 422);
        }

        // Time Out does not require approval - it's auto-approved by whoever
        // submitted it, on both phases (there's nothing for a Final Approver
        // to review on a closing punch).
        $log = AttendanceLog::create([
            'admin_id' => $targetAdminId,
            'attendance_type' => $request->attendance_type,
            'attendance_time' => $attendanceDate . ' ' . $request->attendance_time,
            'reason' => $isTimeOut ? null : $request->reason,
            'status' => $isTimeOut ? 'approved' : 'waiting',
            'approved_by' => $isTimeOut ? $admin->id : null,
            'approved_at' => $isTimeOut ? now() : null,
            'final_status' => $isTimeOut ? 'approved' : 'pending',
            'final_approved_by' => $isTimeOut ? $admin->id : null,
            'final_approved_at' => $isTimeOut ? now() : null,
        ]);

        return response()->json([
            'res' => $isTimeOut
                ? 'Time Out recorded successfully!'
                : 'Attendance recorded successfully! Waiting for approval.',
            'id' => $log->id,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canApprove($admin)) {
            return response()->json(['res' => 'You are not authorized to edit attendance records.'], 403);
        }

        $log = AttendanceLog::with('admin:id,name')->find($id);

        if (!$log) {
            return response()->json(['res' => 'Attendance record not found.'], 404);
        }

        if (!$this->canView($admin, $log->admin_id)) {
            return response()->json(['res' => 'You are not authorized to edit this attendance record.'], 403);
        }

        return response()->json([
            'id' => $log->id,
            'staff_name' => optional($log->admin)->name,
            'attendance_type' => $log->attendance_type,
            'attendance_date' => $log->attendance_time->format('Y-m-d'),
            'attendance_time' => $log->attendance_time->format('H:i'),
            'reason' => $log->reason,
        ]);
    }

    public function update(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canApprove($admin)) {
            return response()->json(['res' => 'You are not authorized to edit attendance records.'], 403);
        }

        $log = AttendanceLog::find($request->id);

        if (!$log) {
            return response()->json(['res' => 'Attendance record not found.'], 404);
        }

        if (!$this->canView($admin, $log->admin_id)) {
            return response()->json(['res' => 'You are not authorized to edit this attendance record.'], 403);
        }

        $lateTimeIn = $request->attendance_type == 'in'
            && $request->attendance_time
            && $request->attendance_time > '10:00';

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:attendance_logs,id',
            'attendance_type' => 'required|in:in,out',
            'attendance_time' => 'required|date_format:H:i',
            'attendance_date' => 'required|date',
            'reason' => $lateTimeIn ? 'required|string|max:1000' : 'nullable|string|max:1000',
        ], [
            'id.required' => 'Attendance record is required.',
            'id.exists' => 'Attendance record not found.',
            'attendance_type.required' => 'Attendance type is required.',
            'attendance_type.in' => 'Attendance type must be Time In or Time Out.',
            'attendance_time.required' => 'Attendance time is required.',
            'attendance_time.date_format' => 'Attendance time is invalid.',
            'attendance_date.required' => 'Attendance date is required.',
            'attendance_date.date' => 'Attendance date is invalid.',
            'reason.required' => 'Reason is mandatory for a Time In after 10:00 AM.',
            'reason.max' => 'Reason may not be longer than 1000 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conflict = $this->duplicateOrSequenceError($log->admin_id, $request->attendance_type, $request->attendance_date, $log->id);

        if ($conflict) {
            return response()->json(['errors' => ['attendance_type' => [$conflict]]], 422);
        }

        $log->attendance_type = $request->attendance_type;
        $log->attendance_time = $request->attendance_date . ' ' . $request->attendance_time;
        $log->reason = $request->attendance_type === 'out' ? null : $request->reason;
        $log->save();

        return response()->json(['res' => 'Attendance updated successfully!']);
    }

    public function delete(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canApprove($admin)) {
            return response()->json(['res' => 'You are not authorized to delete attendance records.'], 403);
        }

        $log = AttendanceLog::find($request->id);

        if (!$log) {
            return response()->json(['res' => 'Attendance record not found.'], 404);
        }

        if (!$this->canView($admin, $log->admin_id)) {
            return response()->json(['res' => 'You are not authorized to delete this attendance record.'], 403);
        }

        $log->delete();

        return response()->json(['res' => 'Attendance deleted successfully!']);
    }

    public function approve(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canApprove($admin)) {
            return response()->json(['res' => 'You are not authorized to approve attendance.'], 403);
        }

        $log = AttendanceLog::find($request->id);

        if (!$log) {
            return response()->json(['res' => 'Attendance record not found.'], 404);
        }

        if (!$this->canView($admin, $log->admin_id)) {
            return response()->json(['res' => 'You are not authorized to approve this attendance record.'], 403);
        }

        // Super Admin has no status restriction; Manager only acts while waiting.
        if (!$this->isSuperAdmin($admin) && $log->status != 'waiting') {
            return response()->json(['res' => 'Only records waiting for approval can be approved.'], 422);
        }

        $previousStatus = $log->status;

        $log->status = 'approved';
        $log->approved_by = $admin->id;
        $log->approved_at = now();
        $log->rejection_reason = null;
        $log->save();

        $this->logApproval($log, 'normal', 'approved', $previousStatus, 'approved', $admin);

        return response()->json(['res' => 'Attendance approved successfully!']);
    }

    public function reject(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canApprove($admin)) {
            return response()->json(['res' => 'You are not authorized to reject attendance.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:attendance_logs,id',
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'id.required' => 'Attendance record is required.',
            'id.integer' => 'Attendance record is invalid.',
            'id.exists' => 'Attendance record not found.',
            'rejection_reason.required' => 'Rejection reason is required.',
            'rejection_reason.max' => 'Rejection reason may not be longer than 1000 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $log = AttendanceLog::find($request->id);

        if (!$log) {
            return response()->json(['res' => 'Attendance record not found.'], 404);
        }

        if (!$this->canView($admin, $log->admin_id)) {
            return response()->json(['res' => 'You are not authorized to reject this attendance record.'], 403);
        }

        // Super Admin has no status restriction; Manager only acts while waiting.
        if (!$this->isSuperAdmin($admin) && $log->status != 'waiting') {
            return response()->json(['res' => 'Only records waiting for approval can be rejected.'], 422);
        }

        $previousStatus = $log->status;

        $log->status = 'rejected';
        $log->approved_by = $admin->id;
        $log->approved_at = now();
        $log->rejection_reason = $request->rejection_reason;

        // Normal Approval was just revoked (e.g. Super Admin re-opening a
        // previously approved record) - Final Approval can no longer stand
        // on its own, so it goes back to pending and must be re-granted.
        if ($previousStatus === 'approved' && $log->final_status !== 'pending') {
            $log->final_status = 'pending';
            $log->final_approved_by = null;
            $log->final_approved_at = null;
            $log->final_rejection_reason = null;
        }

        $log->save();

        $this->logApproval($log, 'normal', 'rejected', $previousStatus, 'rejected', $admin, $request->rejection_reason);

        return response()->json(['res' => 'Attendance rejected successfully!']);
    }

    /**
     * Phase 2 of attendance approval. Only Final Approval Access holders
     * (Salary Settings -> Settings -> Final Approval Access) or Super Admin
     * may act, and only once Normal Approval has already been granted -
     * Final Approval is a confirmation layer on top of Normal Approval, not
     * a replacement for it.
     */
    public function finalApprove(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->isFinalApprover($admin)) {
            return response()->json(['res' => 'You are not authorized to give Final Approval.'], 403);
        }

        $log = AttendanceLog::find($request->id);

        if (!$log) {
            return response()->json(['res' => 'Attendance record not found.'], 404);
        }

        if ($log->status !== 'approved') {
            return response()->json(['res' => 'This record must be Normally Approved before it can receive Final Approval.'], 422);
        }

        // Super Admin has no status restriction; other Final Approvers only
        // act while final approval is still pending.
        if (!$this->isSuperAdmin($admin) && $log->final_status != 'pending') {
            return response()->json(['res' => 'Only records pending Final Approval can be approved.'], 422);
        }

        $previousStatus = $log->final_status;

        $log->final_status = 'approved';
        $log->final_approved_by = $admin->id;
        $log->final_approved_at = now();
        $log->final_rejection_reason = null;
        $log->save();

        $this->logApproval($log, 'final', 'approved', $previousStatus, 'approved', $admin);

        return response()->json(['res' => 'Attendance given Final Approval successfully!']);
    }

    public function finalReject(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->isFinalApprover($admin)) {
            return response()->json(['res' => 'You are not authorized to reject Final Approval.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:attendance_logs,id',
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'id.required' => 'Attendance record is required.',
            'id.integer' => 'Attendance record is invalid.',
            'id.exists' => 'Attendance record not found.',
            'rejection_reason.required' => 'Rejection reason is required.',
            'rejection_reason.max' => 'Rejection reason may not be longer than 1000 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $log = AttendanceLog::find($request->id);

        if (!$log) {
            return response()->json(['res' => 'Attendance record not found.'], 404);
        }

        if ($log->status !== 'approved') {
            return response()->json(['res' => 'This record must be Normally Approved before Final Approval can be rejected.'], 422);
        }

        if (!$this->isSuperAdmin($admin) && $log->final_status != 'pending') {
            return response()->json(['res' => 'Only records pending Final Approval can be rejected.'], 422);
        }

        $previousStatus = $log->final_status;

        $log->final_status = 'rejected';
        $log->final_approved_by = $admin->id;
        $log->final_approved_at = now();
        $log->final_rejection_reason = $request->rejection_reason;
        $log->save();

        $this->logApproval($log, 'final', 'rejected', $previousStatus, 'rejected', $admin, $request->rejection_reason);

        return response()->json(['res' => 'Final Approval rejected successfully!']);
    }

    /**
     * Full audit trail for one attendance record - both approval phases,
     * newest first.
     */
    public function approvalHistory(Request $request, $id)
    {
        $admin = $this->loggedAdmin();
        $log = AttendanceLog::find($id);

        if (!$log) {
            return response()->json(['res' => 'Attendance record not found.'], 404);
        }

        if (!$this->canView($admin, $log->admin_id)) {
            return response()->json(['res' => 'You are not authorized to view this attendance history.'], 403);
        }

        $history = AttendanceApprovalLog::with('admin:id,name')
            ->where('attendance_log_id', $log->id)
            ->latest()
            ->get()
            ->map(fn ($entry) => [
                'approval_type' => $entry->approval_type,
                'action' => $entry->action,
                'previous_status' => $entry->previous_status,
                'new_status' => $entry->new_status,
                'admin_name' => optional($entry->admin)->name ?? '-',
                'remarks' => $entry->remarks,
                'created_at' => $entry->created_at->format('d M Y h:i A'),
            ]);

        return response()->json(['history' => $history]);
    }

    public function saveFilter(Request $request)
    {
        $admin = $this->loggedAdmin();

        $filterData = [
            'staff_id' => $request->staff_id,
            'attendance_type' => $request->attendance_type,
            'status' => $request->status,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'approved_by' => $request->approved_by,
        ];

        $filter = AttendanceSaveFilter::updateOrCreate(
            ['admin_id' => $admin->id],
            ['filter_data' => $filterData]
        );

        return response()->json([
            'res' => $filter->wasRecentlyCreated ? 'Filter saved successfully!' : 'Filter updated successfully!',
        ]);
    }

    public function resetFilter(Request $request)
    {
        $admin = $this->loggedAdmin();

        AttendanceSaveFilter::where('admin_id', $admin->id)->delete();

        return response()->json(['res' => 'Filter reset successfully!']);
    }

    /**
     * Download an Attendance Slip PDF for one staff member / one month - a
     * day-by-day Date/In/Out/Status report. Status boundaries mirror the
     * Salary module's rules (same office_start_time etc.) so the two never
     * disagree about what counts as Late vs Half Day vs Absent.
     */
    public function downloadSlip(Request $request, int $adminId, int $month, int $year, \App\Services\SalaryCalculationService $calculator, AdvancePaymentService $advancePaymentService)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canView($admin, $adminId)) {
            abort(403, 'You are not authorized to download this attendance slip.');
        }

        // Plain staff (no approve rights, no HR "View All") downloading
        // their own slip are locked to the current or previous calendar
        // month, same as the Live Salary Dashboard's own shortcut buttons -
        // enforced here too so the restriction holds even if that check is
        // bypassed (e.g. the request URL edited directly). Managers, Super
        // Admin and View-All staff keep unrestricted month access, same as
        // their existing arbitrary-month Download Attendance Slip modal.
        $isOwnSlip = (int) $adminId === (int) $admin->id;
        $isRestrictedToRecentMonths = $isOwnSlip && !$this->canApprove($admin) && !$this->canViewAll($admin);

        if ($isRestrictedToRecentMonths) {
            $current = Carbon::now();
            $previous = $current->copy()->subMonthNoOverflow();
            $isCurrentPeriod = (int) $month === $current->month && (int) $year === $current->year;
            $isPreviousPeriod = (int) $month === $previous->month && (int) $year === $previous->year;

            if (!$isCurrentPeriod && !$isPreviousPeriod) {
                abort(403, 'You can only download the attendance slip for the current or previous month.');
            }
        }

        $target = Admin::find($adminId);

        if (!$target) {
            abort(404, 'Staff member not found.');
        }

        $settings = SalarySetting::current();
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->startOfDay();
        $today = Carbon::today();
        $evalEnd = $today->lt($monthEnd) ? $today->copy() : $monthEnd->copy();

        // Same two-phase requirement as SalaryCalculationService, so the slip
        // and the live salary dashboard never disagree about a given day.
        $logsByDate = AttendanceLog::where('admin_id', $target->id)
            ->where('status', 'approved')
            ->where('final_status', 'approved')
            ->whereBetween('attendance_time', [$monthStart, $evalEnd->copy()->endOfDay()])
            ->orderBy('attendance_time')
            ->get()
            ->groupBy(fn ($log) => $log->attendance_time->format('Y-m-d'));

        $holidayDates = Holiday::whereBetween('holiday_date', [$monthStart->toDateString(), $evalEnd->toDateString()])
            ->pluck('holiday_date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->flip();

        $officeStartTime = (string) $settings->office_start_time;
        $qualifyingLateEndTime = (string) $settings->qualifying_late_end_time;
        $lateWindowEndTime = (string) $settings->late_window_end_time;
        $halfDayAfterTime = (string) $settings->half_day_after_time;

        $statusLabels = [
            'present' => 'Present',
            'qualifying_late' => 'Qualifying Late',
            'late' => 'Late',
            'half_day' => 'Half Day',
            'absent' => 'Absent',
            'holiday' => 'Holiday',
            'weekly_off' => 'Weekly Off',
        ];

        $counts = array_fill_keys(array_keys($statusLabels), 0);
        $rows = [];

        for ($date = $monthStart->copy(); $date->lte($evalEnd); $date->addDay()) {
            $dateKey = $date->toDateString();
            $dayLogs = $logsByDate->get($dateKey);
            $inLog = $dayLogs ? $dayLogs->firstWhere('attendance_type', 'in') : null;
            $outLog = $dayLogs ? $dayLogs->firstWhere('attendance_type', 'out') : null;

            if ($date->isSunday()) {
                $status = 'weekly_off';
            } elseif ($holidayDates->has($dateKey)) {
                $status = 'holiday';
            } elseif (!$inLog) {
                $status = 'absent';
            } else {
                $inTime = $inLog->attendance_time;
                $officeStart = $date->copy()->setTimeFromTimeString($officeStartTime);
                $qualifyingLateEnd = $date->copy()->setTimeFromTimeString($qualifyingLateEndTime);
                $lateWindowEnd = $date->copy()->setTimeFromTimeString($lateWindowEndTime);
                $halfDayAfter = $date->copy()->setTimeFromTimeString($halfDayAfterTime);

                if ($inTime->lte($officeStart)) {
                    $status = 'present';
                } elseif ($inTime->lte($qualifyingLateEnd)) {
                    $status = 'qualifying_late';
                } elseif ($inTime->lte($lateWindowEnd) && $inTime->lte($halfDayAfter)) {
                    $status = 'late';
                } else {
                    $status = 'half_day';
                }
            }

            $counts[$status]++;

            $workHrs = '-';
            if ($inLog && $outLog && $outLog->attendance_time->gt($inLog->attendance_time)) {
                $minutes = $inLog->attendance_time->diffInMinutes($outLog->attendance_time);
                $workHrs = sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
            }

            $remarks = match ($status) {
                'weekly_off' => 'WEEKLY OFF',
                'holiday' => 'HOLIDAY',
                'absent' => 'ABSENT',
                'qualifying_late', 'late' => 'LATE MARK',
                'half_day' => 'HALF DAY',
                default => '-',
            };

            $rows[] = [
                'date' => $date->format('d M Y'),
                'date_num' => $date->format('d'),
                'day' => $date->format('l'),
                'day_short' => strtoupper($date->format('D')),
                'in_time' => $inLog ? $inLog->attendance_time->format('h:i A') : '-',
                'out_time' => $outLog ? $outLog->attendance_time->format('h:i A') : '-',
                'work_hrs' => $workHrs,
                'status' => $statusLabels[$status],
                'status_key' => $status,
                'remarks' => $remarks,
            ];
        }

        $periodLabel = $monthStart->format('F Y');
        $totalLateMarks = $counts['qualifying_late'] + $counts['late'];
        $absentRows = array_values(array_filter($rows, fn ($row) => $row['status_key'] === 'absent'));

        $result = $calculator->calculate($target, $month, $year);
        // Cutoff-aware, same as the day-by-day $rows above: the month
        // currently in progress only counts weekly offs/working days up to
        // today, not the full calendar month.
        $weeklyOffDays = $result['weekly_off_days'];
        $totalWorkingDays = max($result['elapsed_days'] - $weeklyOffDays, 0);

        // Informational only (not used in the actual deduction math): a
        // standard 9-hour shift (10:00 AM - 7:00 PM) backs the "per hour"
        // figure shown on the slip.
        $standardShiftHours = 9;
        $perHourSalary = $standardShiftHours > 0 ? $result['daily_salary'] / $standardShiftHours : 0;
        $halfDayAmount = $result['daily_salary'] * 0.5;

        // Same resolution SalaryController's Salary Slip uses (locked
        // payroll's frozen figure if one exists, otherwise a live sum) - so
        // the two slips never disagree about the advance/final payable.
        $advanceDeduction = $advancePaymentService->resolveApplicableAdvance($target->id, $month, $year);
        $finalPayableAfterAdvance = round($result['net_payable'] - $advanceDeduction, 2);

        $pdf = Pdf::loadView('admin.attendance.slip_pdf', [
            'target' => $target,
            'periodLabel' => $periodLabel,
            'rows' => $rows,
            'absentRows' => $absentRows,
            'counts' => $counts,
            'statusLabels' => $statusLabels,
            'totalLateMarks' => $totalLateMarks,
            'result' => $result,
            'weeklyOffDays' => $weeklyOffDays,
            'totalWorkingDays' => $totalWorkingDays,
            'perHourSalary' => $perHourSalary,
            'halfDayAmount' => $halfDayAmount,
            'advanceDeduction' => $advanceDeduction,
            'finalPayableAfterAdvance' => $finalPayableAfterAdvance,
            'companyName' => 'QAMR INTERNATIONAL',
            'companySubtitle' => 'HR CONSULTANCY',
            'logoPath' => public_path('admin/assets/images/pdf/qamr-logo.png'),
        ]);

        $fileName = 'Attendance-Slip-' . str_replace(' ', '-', $target->name) . '-' . $periodLabel . '.pdf';

        return $pdf->download($fileName);
    }
}
