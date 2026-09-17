@extends('layout.admin.admin_layout')

@section('title','Attendance')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />

    <style>
        .filter-indicator {
            position: absolute;
            top: 1px;
            right: 2px;
            width: 8px;
            height: 8px;
            background: #ffc107;
            border-radius: 50%;
        }
        .salary-tile {
            border: 1px solid var(--bs-border-color, #e7e7e7);
            border-radius: .5rem;
            padding: .9rem 1rem;
            height: 100%;
        }
        .salary-tile .salary-tile-label {
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .02em;
            color: #8592a3;
            margin-bottom: .25rem;
        }
        .salary-tile .salary-tile-value {
            font-size: 1.15rem;
            font-weight: 600;
        }
        .salary-tile.is-deduction .salary-tile-value { color: #d9534f; }
        .salary-tile.is-net .salary-tile-value { color: #28a745; font-size: 1.35rem; }
        .salary-tile.is-net { background: rgba(40,167,69,.06); border-color: rgba(40,167,69,.3); }
    </style>
@endsection

@php
    $savedFilterData = optional($savedFilter)->filter_data ?? [];
    $savedStaffIds = (array) ($savedFilterData['staff_id'] ?? []);
    $savedTypes = (array) ($savedFilterData['attendance_type'] ?? []);
    $savedStatuses = (array) ($savedFilterData['status'] ?? []);
    $savedApprovedBy = (array) ($savedFilterData['approved_by'] ?? []);
    $savedDateFrom = $savedFilterData['date_from'] ?? '';
    $savedDateTo = $savedFilterData['date_to'] ?? '';
    $savedDateRangeText = ($savedDateFrom && $savedDateTo)
        ? \Carbon\Carbon::parse($savedDateFrom)->format('m/d/Y') . ' - ' . \Carbon\Carbon::parse($savedDateTo)->format('m/d/Y')
        : '';
@endphp

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

  {{-- Live Salary Dashboard - reuses SalaryController::live() (the exact
       same endpoint /admin/salary/dashboard calls), so the figures here
       always agree with the Salary Dashboard. Staff-selection is only
       offered to whoever the Salary module itself lets view another
       staff member's salary (Super Admin / Team Head); everyone else is
       locked to their own record, both here (dropdown hidden) and on the
       server (SalaryController::live()'s own canView() check).

       Super Admin never sees this widget at all (hidden server-side, not
       just via CSS) - they operate at the Salary Dashboard/Payroll level,
       not a per-staff "my salary" view, and Attendance List already
       has its own admin-facing staff/HR tooling. Everyone else (normal
       staff, and HR/Team Head users with View All) is unaffected. --}}
  @if(!$isSuperAdmin)
  <div class="card mb-4">
    <div class="card-header border-bottom d-flex align-items-center">
      <h5 class="mb-0"><i class="ti ti-report-money me-1"></i> Live Salary Dashboard</h5>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-3 col-6 mb-2">
          <label class="form-label">Month</label>
          <select id="atd-live-month" class="form-select">
            @foreach(range(1,12) as $m)
              <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 col-6 mb-2">
          <label class="form-label">Year</label>
          <select id="atd-live-year" class="form-select">
            @foreach(range(now()->year - 2, now()->year + 1) as $y)
              <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
          </select>
        </div>
        @if(!$canApprove && !$canViewAll)
        {{-- Plain-staff-only: both buttons download the logged-in admin's
             own attendance slip / salary slip for whatever Month/Year is
             selected above (the same selector the dashboard cards already
             use - no second month picker). Excludes $canApprove/$canViewAll
             so Managers, Super Admin and HR "View All" keep their existing
             staff-picker + arbitrary-month modals further down the page,
             unchanged.

             Attendance Slip: the click handler below (JS) blocks anything
             but the real current/previous calendar month client-side;
             downloadSlip() (admin.attendance.slip.download) enforces the
             same rule server-side.

             Salary Slip: any Month/Year in the selector is allowed (matches
             the Salary Dashboard's own "Download Salary Slip" modal, which
             never restricted staff to current/previous either) - it hits
             admin.salary.slip.download (SalaryController::downloadSlip()),
             the exact same route/PDF the Salary module's own View/Download
             Slip actions already use, no new generation logic added.

             Both routes' canView() checks already reject any adminId other
             than the logged-in staff member's own, so URL/request
             tampering can't reach another staff's slip. --}}
        <div class="col-md-3 col-6 mb-2 d-flex align-items-end">
          <button type="button" class="btn btn-outline-primary w-100 atd-my-slip-download-trigger">
            <i class="ti ti-download me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Download Attendance Slip</span>
          </button>
        </div>
        <div class="col-md-3 col-6 mb-2 d-flex align-items-end">
          <button type="button" class="btn btn-outline-primary w-100 atd-my-salary-slip-download-trigger">
            <i class="ti ti-download me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Download Salary Slip</span>
          </button>
        </div>
        @endif
        @if($canApprove)
        <div class="col-md-6 mb-2">
          <label class="form-label">Viewing Staff</label>
          <select id="atd-live-staff" class="form-select select2-init" data-placeholder="Select Staff">
            <option value="{{ Auth::guard('admin')->id() }}" selected>{{ Auth::guard('admin')->user()->name }} (Me)</option>
            @foreach($staffList as $staffMember)
              @if($staffMember->id != Auth::guard('admin')->id())
              <option value="{{ $staffMember->id }}">{{ $staffMember->name }}</option>
              @endif
            @endforeach
          </select>
        </div>
        @endif
      </div>

      <div id="atd-live-locked-banner" class="alert alert-success d-none">
        <i class="ti ti-lock me-1"></i> This is the <strong>Final Salary</strong> for this period<span id="atd-live-locked-at"></span> - figures below are the locked payroll snapshot, not a live recalculation.
      </div>

      <div class="row g-3" id="atd-live-dashboard-cards"></div>
    </div>
  </div>
  @endif

  <div class="card">
    <div class="card-header border-bottom">
      <div class="px-3 float-start">
        <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel">
          <i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter
          <span class="filter-indicator d-none"></span>
        </button>
        @if($canApprove)
        <button type="button" class="btn btn-xs btn-outline-primary mx-1" data-bs-toggle="modal" data-bs-target="#attendanceSlipModal">
          <i class="ti ti-download me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Download Attendance Slip</span>
        </button>
        @endif
      
      </div>

      <div class="mb-1 float-end">
        
        <button type="button" class="btn btn-sm btn-primary mx-1 attendance-punch-btn" data-bs-toggle="modal" data-bs-target="#timeInOutModal" data-type="in">
          <i class="ti ti-login me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Time In</span>
        </button>
        <button type="button" class="btn btn-sm btn-outline-primary mx-1 attendance-punch-btn" data-bs-toggle="modal" data-bs-target="#timeInOutModal" data-type="out">
          <i class="ti ti-logout me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Time Out</span>
        </button>
      </div>
    </div>

    <div class="card-datatable table-responsive">
      <table class="datatables-users table border-top">
        <thead>
          <tr>
            <th>Staff ID</th>
            <th>Staff Name</th>
            <th>Date</th>
            <th>In Time</th>
            <th>Out Time</th>
            <th>Normal Approval</th>
            <th>Final Approval</th>
            <th>Payable</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  {{-- Time In / Out Modal --}}
  <div class="modal fade" id="timeInOutModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title" id="timeInOutModalLabel">Time In</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="timeInOutForm">
          @csrf
          <input type="hidden" name="attendance_type" id="attendance_type" value="in">
          <div class="modal-body">
            @if($canApprove)
            <div class="mb-3">
              <select name="admin_id" id="attendance_admin_id" class="form-select select2-init" data-placeholder="Select Staff">
                <option value=""></option>
                @foreach($allActiveStaff as $staffMember)
                  <option value="{{ $staffMember->id }}">{{ $staffMember->name }}</option>
                @endforeach
              </select>
            </div>
            @else
            <input type="hidden" name="admin_id" id="attendance_admin_id" value="{{ Auth::guard('admin')->id() }}">
            @endif

            @if($canApprove)
            <div class="mb-3">
              <label class="form-label" for="attendance_date">Attendance Date <span class="text-danger">*</span></label>
              <input type="date" name="attendance_date" id="attendance_date" class="form-control" required>
            </div>
            @else
            <input type="hidden" name="attendance_date" id="attendance_date">
            @endif

            <div class="mb-3">
              <label class="form-label" for="attendance_time">Attendance Time <span class="text-danger">*</span></label>
              <input type="time" name="attendance_time" id="attendance_time" class="form-control" required>
            </div>

            <div class="mb-3" id="reason_wrapper">
              <label class="form-label" for="reason">
                Reason <span class="text-danger reason-required d-none">*</span>
                <small class="text-muted">(mandatory for Time In after 10:00 AM)</small>
              </label>
              <textarea name="reason" id="reason" rows="3" class="form-control"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @if($canApprove || $canFinalApprove)
  {{-- Attendance Status Modal (Normal or Final Approve / Reject) --}}
  <div class="modal fade" id="attendanceStatusModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title" id="attendanceStatusModalLabel">Update Normal Approval</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="status_attendance_id">
          <input type="hidden" id="status_approval_type" value="normal">

          <div class="mb-3">
            <label class="form-label d-block">Action</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status_action" id="status_action_approve" value="approve" checked>
              <label class="form-check-label" for="status_action_approve">Approve</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="status_action" id="status_action_reject" value="reject">
              <label class="form-check-label" for="status_action_reject">Reject</label>
            </div>
          </div>

          <div class="mb-3 d-none" id="status_rejection_reason_wrapper">
            <label class="form-label" for="status_rejection_reason">Rejection Reason <span class="text-danger">*</span></label>
            <textarea class="form-control" id="status_rejection_reason" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary btn-sm" id="statusActionSubmit">Submit</button>
        </div>
      </div>
    </div>
  </div>

  @if($canApprove)
  {{-- Edit Attendance Modal (Super Admin / Manager only) --}}
  <div class="modal fade" id="editAttendanceModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title">Edit Attendance <small class="text-muted" id="edit_staff_name"></small></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editAttendanceForm">
          @csrf
          <input type="hidden" name="id" id="edit_attendance_id">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label" for="edit_attendance_type">Attendance Type <span class="text-danger">*</span></label>
              <select name="attendance_type" id="edit_attendance_type" class="form-select" required>
                <option value="in">Time In</option>
                <option value="out">Time Out</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label" for="edit_attendance_date">Attendance Date <span class="text-danger">*</span></label>
              <input type="date" name="attendance_date" id="edit_attendance_date" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label" for="edit_attendance_time">Attendance Time <span class="text-danger">*</span></label>
              <input type="time" name="attendance_time" id="edit_attendance_time" class="form-control" required>
            </div>

            <div class="mb-3" id="edit_reason_wrapper">
              <label class="form-label" for="edit_reason">
                Reason <span class="text-danger edit-reason-required d-none">*</span>
                <small class="text-muted">(mandatory for Time In after 10:00 AM)</small>
              </label>
              <textarea name="reason" id="edit_reason" rows="3" class="form-control"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-sm">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif
  @endif

  {{-- Approval History Modal - available to anyone who can view the record --}}
  <div class="modal fade" id="attendanceHistoryModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title">Approval History</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="attendance_history_empty" class="text-muted d-none">No approval activity recorded yet.</div>
          <ul id="attendance_history_list" class="list-unstyled mb-0"></ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Filter Modal --}}
  <div class="modal fade" id="filterpanel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title" id="filterpanelLabel">Attendance Filter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div class="row">
            @if($canApprove || $canViewAll)
            <div class="col-md-6 mb-3">
              <select id="by-staff" class="form-select select2-init select2-filter" multiple data-placeholder="Staff">
                @foreach($staffList as $staffMember)
                  <option value="{{ $staffMember->id }}" @if(in_array($staffMember->id, $savedStaffIds)) selected @endif>{{ $staffMember->name }}</option>
                @endforeach
              </select>
            </div>
            @endif

            <div class="col-md-6 mb-3">
              <select id="by-attendance-type" class="form-select select2-init select2-filter" multiple data-placeholder="Attendance Type">
                <option value="in" @if(in_array('in', $savedTypes)) selected @endif>Time In</option>
                <option value="out" @if(in_array('out', $savedTypes)) selected @endif>Time Out</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <select id="by-status" class="form-select select2-init select2-filter" multiple data-placeholder="Status">
                <option value="waiting" @if(in_array('waiting', $savedStatuses)) selected @endif>Waiting for Approval</option>
                <option value="approved" @if(in_array('approved', $savedStatuses)) selected @endif>Approved</option>
                <option value="rejected" @if(in_array('rejected', $savedStatuses)) selected @endif>Rejected</option>
              </select>
            </div>

            @if($canApprove)
            <div class="col-md-6 mb-3">
              <select id="by-approved-by" class="form-select select2-init select2-filter" multiple data-placeholder="Approved By">
                @foreach($staffList as $staffMember)
                  <option value="{{ $staffMember->id }}" @if(in_array($staffMember->id, $savedApprovedBy)) selected @endif>{{ $staffMember->name }}</option>
                @endforeach
              </select>
            </div>
            @endif

            <div class="col-md-6 mb-3">
              <input type="text" id="by-date-range" class="form-control bsdatpicket" placeholder="Attendance Date Range..." value="{{ $savedDateRangeText }}">
              <input type="hidden" id="by-date-from" value="{{ $savedDateFrom }}">
              <input type="hidden" id="by-date-to" value="{{ $savedDateTo }}">
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-warning btn-sm resetfilter">Reset Filter</button>
          <button type="button" class="btn btn-success btn-sm savetodoFilter">Save Filter</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Attendance Slip Modal --}}
  <div class="modal fade" id="attendanceSlipModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title">Download Attendance Slip</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @if($canApprove || $canViewAll)
          <div class="mb-3">
            <label class="form-label">Staff</label>
            <select id="slip_admin_id" class="form-select select2-init" data-placeholder="Select Staff">
              <option value="{{ Auth::guard('admin')->id() }}" selected>{{ Auth::guard('admin')->user()->name }} (Me)</option>
              @foreach($staffList as $staffMember)
                @if($staffMember->id != Auth::guard('admin')->id())
                <option value="{{ $staffMember->id }}">{{ $staffMember->name }}</option>
                @endif
              @endforeach
            </select>
          </div>
          @endif
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Month</label>
              <select id="slip_month" class="form-select">
                @foreach(range(1,12) as $m)
                  <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Year</label>
              <select id="slip_year" class="form-select">
                @foreach(range(now()->year - 2, now()->year + 1) as $y)
                  <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <a href="javascript:void(0);" id="slip_download_btn" class="btn btn-primary btn-sm" target="_blank">Download</a>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
<script>
$(function () {

    const canApprove = @json($canApprove);
    const canViewAll = @json($canViewAll);
    let latestServerDateTime = '';

    /* -----------------------------------------------------------------
     | Live Salary Dashboard - same data/endpoint as /admin/salary/dashboard
     | (SalaryController::live(), route admin.salary.live). Staff-selection
     | mirrors that page's own canApprove gate: everyone else is hard-locked
     | to their own admin id, both here and by canView() on the server.
     | Widget markup doesn't exist in the DOM for Super Admin (see the
     | isSuperAdmin check above), so this whole block is skipped for them
     | too - no dead AJAX call.
     |------------------------------------------------------------------ */
    @if(!$isSuperAdmin)
    const atdLiveLoggedAdminId = {{ Auth::guard('admin')->id() }};
    const atdLiveFieldMeta = [
        { key: 'current_date', label: 'Current Date' },
        { key: 'present', label: 'Present' },
        { key: 'absent', label: 'Absent' },
        { key: 'half_days', label: 'Half Days' },
        { key: 'total_late_count', label: 'Total Late Marks' },
        { key: 'late_deduction', label: 'Late Deduction', money: true, deduction: true },
        { key: 'absent_deduction', label: 'Absent Deduction', money: true, deduction: true },
        { key: 'half_day_deduction', label: 'Half-Day Deduction', money: true, deduction: true },
        { key: 'sunday_deduction', label: 'Sunday Deduction', money: true, deduction: true },
        { key: 'payable_gross_salary', label: 'Payable Gross Salary', money: true },
        { key: 'total_deduction', label: 'Total Deductions', money: true, deduction: true },
        { key: 'net_payable', label: 'Net Payable', money: true, net: true },
    ];

    function atdLiveMoney(v) {
        v = parseFloat(v || 0);
        return '₹' + v.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    $('#atd-live-staff').select2({ placeholder: function () { return $(this).data('placeholder') || ''; }, allowClear: true });

    function renderAtdLiveDashboard(data) {
        let html = '';
        atdLiveFieldMeta.forEach(function (tile) {
            let value = data[tile.key];
            let displayValue = tile.money ? atdLiveMoney(value) : (value === null || value === undefined ? '-' : value);
            let cls = 'salary-tile' + (tile.deduction ? ' is-deduction' : '') + (tile.net ? ' is-net' : '');
            html += '<div class="col-xl-3 col-md-4 col-6">' +
                '<div class="' + cls + '">' +
                    '<div class="salary-tile-label">' + tile.label + '</div>' +
                    '<div class="salary-tile-value">' + displayValue + '</div>' +
                '</div>' +
            '</div>';
        });
        $('#atd-live-dashboard-cards').html(html);

        if (data.is_locked) {
            $('#atd-live-locked-banner').removeClass('d-none');
            $('#atd-live-locked-at').text(data.locked_at ? (' on ' + data.locked_at) : '');
        } else {
            $('#atd-live-locked-banner').addClass('d-none');
        }
    }

    function loadAtdLiveDashboard() {
        const staffId = canApprove ? ($('#atd-live-staff').val() || atdLiveLoggedAdminId) : atdLiveLoggedAdminId;

        $.get("{{ route('admin.salary.live') }}", {
            staff_id: staffId,
            month: $('#atd-live-month').val(),
            year: $('#atd-live-year').val()
        }, function (data) {
            renderAtdLiveDashboard(data);
        }).fail(function (xhr) {
            toastr.error(xhr.responseJSON?.res || 'Unable to load salary data.', 'Error');
        });
    }

    $('#atd-live-month, #atd-live-year, #atd-live-staff').on('change', loadAtdLiveDashboard);

    loadAtdLiveDashboard();

    /* -----------------------------------------------------------------
     | Shared current/previous-month check for the two plain-staff download
     | buttons below - both read the same #atd-live-month/#atd-live-year
     | selector as the dashboard cards, so "the selected period" always
     | means whatever is chosen there, never today's date directly.
     |------------------------------------------------------------------ */
    function atdIsCurrentOrPreviousPeriod(month, year) {
        const today = new Date();
        const curMonth = today.getMonth() + 1;
        const curYear = today.getFullYear();
        let prevMonth = curMonth - 1;
        let prevYear = curYear;
        if (prevMonth === 0) {
            prevMonth = 12;
            prevYear -= 1;
        }

        return (month === curMonth && year === curYear) || (month === prevMonth && year === prevYear);
    }

    /* -----------------------------------------------------------------
     | Plain-staff "Download Attendance Slip" button - restricted
     | client-side to the real current/previous calendar month. This is a
     | UX shortcut only: AttendanceController::downloadSlip() enforces the
     | identical current-or-previous rule server-side for this same tier of
     | staff, so a blocked month can't be reached by skipping this check
     | (e.g. editing the request URL directly).
     |------------------------------------------------------------------ */
    if (!canApprove && !canViewAll) {
        $('.atd-my-slip-download-trigger').on('click', function () {
            const month = parseInt($('#atd-live-month').val(), 10);
            const year = parseInt($('#atd-live-year').val(), 10);

            if (!atdIsCurrentOrPreviousPeriod(month, year)) {
                toastr.error('You can only download the attendance slip for the current or previous month.', 'Error');
                return;
            }

            window.location.href = "{{ url('admin/attendance/slip') }}/" + atdLiveLoggedAdminId + '/' + month + '/' + year;
        });

        /* -----------------------------------------------------------------
         | Plain-staff "Download Salary Slip" button - same Month/Year
         | selector as above, same current/previous-month restriction.
         | Reuses admin.salary.slip.download (SalaryController::
         | downloadSlip()) - its own canView() check enforces staff can
         | only ever reach their own record, and it already handles a
         | missing/empty period gracefully (renders the slip with zero
         | figures rather than erroring), same as everywhere else that
         | route is used. ?source=attendance_live_dashboard tells the
         | server this request came from this specific button, so the
         | current/previous restriction below (also enforced server-side)
         | applies only here - the Salary Dashboard page's own Download
         | Salary Slip modal deliberately keeps unrestricted month access.
         |------------------------------------------------------------------ */
        $('.atd-my-salary-slip-download-trigger').on('click', function () {
            const month = parseInt($('#atd-live-month').val(), 10);
            const year = parseInt($('#atd-live-year').val(), 10);

            if (!atdIsCurrentOrPreviousPeriod(month, year)) {
                toastr.error('You can only download the salary slip for the current or previous month.', 'Error');
                return;
            }

            window.location.href = "{{ url('admin/salary/slip') }}/" + atdLiveLoggedAdminId + '/' + month + '/' + year + '?source=attendance_live_dashboard';
        });
    }
    @endif

    $('body').on('shown.bs.modal', '.modal', function () {
        $(this).find('.select2-init').each(function () {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    dropdownParent: $(this).closest('.modal'),
                    placeholder: $(this).data('placeholder') || '',
                    allowClear: true
                });
            }
        });
    });

    /* -----------------------------------------------------------------
     | Date Range Picker (same pattern as Lead Created Date filter) -
     | applies/cancels reload the DataTable immediately, no Apply button.
     |------------------------------------------------------------------ */
    $('.bsdatpicket').daterangepicker({
        autoUpdateInput: false,
        opens: 'right',
        locale: { cancelLabel: 'Clear' }
    }).on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        $('#by-date-from').val(picker.startDate.format('YYYY-MM-DD'));
        $('#by-date-to').val(picker.endDate.format('YYYY-MM-DD'));
        reloadAttendanceList();
    }).on('cancel.daterangepicker', function () {
        $(this).val('');
        $('#by-date-from').val('');
        $('#by-date-to').val('');
        reloadAttendanceList();
    });

    /* -----------------------------------------------------------------
     | Auto Apply - every filter select reloads the DataTable the moment
     | it changes, exactly like the Leads module (no Apply Filter button).
     |------------------------------------------------------------------ */
    $('.select2-filter').on('change', function () {
        reloadAttendanceList();
    });

    /* -----------------------------------------------------------------
     | DataTable
     |------------------------------------------------------------------ */
    let table = $('.datatables-users').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        ordering: false,
        destroy: true,
        pageLength: 10,
        ajax: {
            url: "{{ route('admin.attendance.json') }}",
            type: "GET",
            data: function (d) {
                d.staff_id = $('#by-staff').val();
                d.attendance_type = $('#by-attendance-type').val();
                d.status = $('#by-status').val();
                d.approved_by = $('#by-approved-by').val();
                d.date_from = $('#by-date-from').val();
                d.date_to = $('#by-date-to').val();
            }
        },
        columns: [
            { data: 'staff_id', name: 'admin_id' },
            { data: 'staff_name', name: 'admin.name' },
            { data: 'date', name: 'attendance_time' },
            { data: 'in_time', name: 'attendance_time', orderable: false },
            { data: 'out_time', name: 'attendance_time', orderable: false },
            { data: 'normal_approval', name: 'status' },
            { data: 'final_approval', name: 'final_status', orderable: false },
            { data: 'payable_badge', name: 'payable', searchable: false, orderable: false },
            { data: 'actions', name: 'actions', searchable: false, orderable: false },
        ],
        language: {
            processing: '<i class="fa fa-spinner fa-spin"></i> Loading...'
        }
    });

    function reloadAttendanceList() {
        table.ajax.reload(null, false);
        updateFilterIndicator();
    }

    /* -----------------------------------------------------------------
     | Filter Indicator
     |------------------------------------------------------------------ */
    function updateFilterIndicator() {
        let isFiltered =
            ($('#by-staff').length && $('#by-staff').val() && $('#by-staff').val().length > 0) ||
            ($('#by-attendance-type').val() && $('#by-attendance-type').val().length > 0) ||
            ($('#by-status').val() && $('#by-status').val().length > 0) ||
            ($('#by-approved-by').length && $('#by-approved-by').val() && $('#by-approved-by').val().length > 0) ||
            $('#by-date-from').val() ||
            $('#by-date-to').val();

        if (isFiltered) {
            $('.filterpanel .filter-indicator').removeClass('d-none').addClass('d-block');
        } else {
            $('.filterpanel .filter-indicator').removeClass('d-block').addClass('d-none');
        }
    }

    /* -----------------------------------------------------------------
     | Reason: only shown for Time In, and mandatory when Time In is
     | after 10:00 AM. Completely hidden (and cleared) for Time Out.
     |------------------------------------------------------------------ */
    function toggleReasonVisibility(typeSel, wrapperSel, reasonSel) {
        const isOut = $(typeSel).val() === 'out';
        $(wrapperSel).toggleClass('d-none', isOut);

        if (isOut) {
            $(reasonSel).val('').prop('required', false);
        }
    }

    function toggleReasonRequirementFor(typeSel, timeSel, reasonSel, indicatorSel) {
        const type = $(typeSel).val();
        const time = $(timeSel).val();
        const isLate = type === 'in' && time && time > '10:00';

        $(reasonSel).prop('required', isLate);
        $(indicatorSel).toggleClass('d-none', !isLate);
    }

    function toggleReasonRequirement() {
        toggleReasonVisibility('#attendance_type', '#reason_wrapper', '#reason');
        toggleReasonRequirementFor('#attendance_type', '#attendance_time', '#reason', '.reason-required');
    }

    function toggleEditReasonRequirement() {
        toggleReasonVisibility('#edit_attendance_type', '#edit_reason_wrapper', '#edit_reason');
        toggleReasonRequirementFor('#edit_attendance_type', '#edit_attendance_time', '#edit_reason', '.edit-reason-required');
    }

    $('#attendance_time').on('change input', toggleReasonRequirement);
    $('#edit_attendance_type, #edit_attendance_time').on('change input', toggleEditReasonRequirement);

    /* -----------------------------------------------------------------
     | Time In / Time Out buttons
     |------------------------------------------------------------------ */
    $('#timeInOutModal').on('show.bs.modal', function (e) {
        const button = $(e.relatedTarget);
        const type = button.data('type') || 'in';

        $('#attendance_type').val(type);
        $('#timeInOutModalLabel').text(type === 'in' ? 'Time In' : 'Time Out');

        $.get("{{ route('admin.attendance.create') }}", function (data) {
            latestServerDateTime = data.attendance_date + ' ' + data.attendance_time;
            $('#attendance_date').val(data.attendance_date);
            $('#attendance_time').val(data.attendance_time);
            toggleReasonRequirement();
        });
    });

    /* -----------------------------------------------------------------
     | Store Time In / Out
     |------------------------------------------------------------------ */
    $('#timeInOutForm').on('submit', function (e) {
        e.preventDefault();

        if (!canApprove) {
            const submittedAt = $('#attendance_date').val() + ' ' + $('#attendance_time').val();
            if (latestServerDateTime && submittedAt > latestServerDateTime) {
                toastr.error('Attendance date/time cannot be in the future.', 'Validation Error');
                return;
            }
        }

        $.ajax({
            url: "{{ route('admin.attendance.store') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function (data) {
                $('#timeInOutModal').modal('hide');
                $('#timeInOutForm')[0].reset();
                if ($('#attendance_admin_id').is('select')) {
                    $('#attendance_admin_id').val(null).trigger('change');
                }
                toastr.success(data.res, 'Success', { timeOut: 3000 });
                reloadAttendanceList();
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                    toastr.error(firstError, 'Validation Error');
                } else if (xhr.status === 403) {
                    toastr.error(xhr.responseJSON?.res || 'You are not authorized to perform this action.', 'Not Authorized');
                } else {
                    toastr.error('Something went wrong. Please try again.', 'Error');
                }
            }
        });
    });

    /* -----------------------------------------------------------------
     | Edit Attendance (Super Admin / Manager only) - triggered from the
     | three-dot action dropdown, available regardless of record status.
     |------------------------------------------------------------------ */
    $('body').on('click', '.attendance-edit-trigger', function () {
        const id = $(this).data('id');

        $.get("{{ url('admin/attendance/edit') }}/" + id, function (data) {
            $('#edit_attendance_id').val(data.id);
            $('#edit_staff_name').text(data.staff_name ? '- ' + data.staff_name : '');
            $('#edit_attendance_type').val(data.attendance_type);
            $('#edit_attendance_date').val(data.attendance_date);
            $('#edit_attendance_time').val(data.attendance_time);
            $('#edit_reason').val(data.reason);
            toggleEditReasonRequirement();
            $('#editAttendanceModal').modal('show');
        }).fail(function (xhr) {
            toastr.error(xhr.responseJSON?.res || 'Unable to load attendance record.', 'Error');
        });
    });

    $('#editAttendanceForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('admin.attendance.update') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function (data) {
                $('#editAttendanceModal').modal('hide');
                toastr.success(data.res, 'Success', { timeOut: 3000 });
                reloadAttendanceList();
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                    toastr.error(firstError, 'Validation Error');
                } else if (xhr.status === 403) {
                    toastr.error(xhr.responseJSON?.res || 'You are not authorized to perform this action.', 'Not Authorized');
                } else {
                    toastr.error('Something went wrong. Please try again.', 'Error');
                }
            }
        });
    });

    /* -----------------------------------------------------------------
     | Delete Attendance (Super Admin / Manager only)
     |------------------------------------------------------------------ */
    $('body').on('click', '.attendance-delete-trigger', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This attendance record will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: { confirmButton: 'btn btn-danger me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.attendance.delete') }}",
                    method: 'POST',
                    data: { _token: "{{ csrf_token() }}", id: id },
                    success: function (data) {
                        toastr.success(data.res, 'Success', { timeOut: 3000 });
                        reloadAttendanceList();
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON?.res || 'Something went wrong.', 'Error');
                    }
                });
            }
        });
    });

    /* -----------------------------------------------------------------
     | Attendance Status (Normal or Final, Approve / Reject) - triggered
     | from a status badge click or the three-dot action dropdown. The
     | approval type (normal/final) travels on the trigger's data-type and
     | decides both the modal title and which pair of routes gets called.
     |------------------------------------------------------------------ */
    const statusRoutes = {
        normal: {
            approve: "{{ route('admin.attendance.approve') }}",
            reject: "{{ route('admin.attendance.reject') }}"
        },
        final: {
            approve: "{{ route('admin.attendance.finalapprove') }}",
            reject: "{{ route('admin.attendance.finalreject') }}"
        }
    };

    $('body').on('click', '.attendance-status-trigger', function () {
        const id = $(this).data('id');
        const type = $(this).data('type') || 'normal';
        const preset = $(this).data('preset') || 'approve';

        $('#status_attendance_id').val(id);
        $('#status_approval_type').val(type);
        $('#attendanceStatusModalLabel').text(type === 'final' ? 'Update Final Approval' : 'Update Normal Approval');
        $('#status_action_' + preset).prop('checked', true).trigger('change');
    });

    $('body').on('change', 'input[name="status_action"]', function () {
        $('#status_rejection_reason_wrapper').toggleClass('d-none', $(this).val() !== 'reject');
    });

    $('#attendanceStatusModal').on('hidden.bs.modal', function () {
        $('#status_action_approve').prop('checked', true).trigger('change');
        $('#status_rejection_reason').val('');
    });

    $('#statusActionSubmit').on('click', function () {
        const id = $('#status_attendance_id').val();
        const type = $('#status_approval_type').val() === 'final' ? 'final' : 'normal';
        const action = $('input[name="status_action"]:checked').val();

        if (action === 'reject') {
            const reason = $('#status_rejection_reason').val();

            if (!reason) {
                toastr.error('Rejection reason is required.', 'Validation Error');
                return;
            }

            $.ajax({
                url: statusRoutes[type].reject,
                method: 'POST',
                data: { _token: "{{ csrf_token() }}", id: id, rejection_reason: reason },
                success: function (data) {
                    $('#attendanceStatusModal').modal('hide');
                    toastr.success(data.res, 'Success', { timeOut: 3000 });
                    reloadAttendanceList();
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.res || (xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors)[0][0] : 'Something went wrong.'), 'Error');
                }
            });

            return;
        }

        $.ajax({
            url: statusRoutes[type].approve,
            method: 'POST',
            data: { _token: "{{ csrf_token() }}", id: id },
            success: function (data) {
                $('#attendanceStatusModal').modal('hide');
                toastr.success(data.res, 'Success', { timeOut: 3000 });
                reloadAttendanceList();
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON?.res || 'Something went wrong.', 'Error');
            }
        });
    });

    /* -----------------------------------------------------------------
     | Approval History - full Normal + Final audit trail for one record.
     |------------------------------------------------------------------ */
    const approvalTypeLabels = { normal: 'Normal Approval', final: 'Final Approval' };
    const approvalActionLabels = { approved: 'Approved', rejected: 'Rejected' };

    $('body').on('click', '.attendance-history-trigger', function () {
        const id = $(this).data('id');

        $('#attendance_history_list').empty();
        $('#attendance_history_empty').addClass('d-none');
        $('#attendanceHistoryModal').modal('show');

        $.get("{{ url('admin/attendance/approval-history') }}/" + id, function (data) {
            const history = data.history || [];

            if (history.length === 0) {
                $('#attendance_history_empty').removeClass('d-none');
                return;
            }

            history.forEach(function (entry) {
                const typeLabel = approvalTypeLabels[entry.approval_type] || entry.approval_type;
                const actionLabel = approvalActionLabels[entry.action] || entry.action;
                const badgeClass = entry.action === 'approved' ? 'bg-label-success' : 'bg-label-danger';
                const remarks = entry.remarks ? '<div class="small text-muted mt-1">' + $('<div>').text(entry.remarks).html() + '</div>' : '';

                $('#attendance_history_list').append(
                    '<li class="border rounded p-2 mb-2">' +
                        '<div class="d-flex justify-content-between align-items-center">' +
                            '<span><span class="badge ' + badgeClass + ' me-1">' + actionLabel + '</span>' + typeLabel + '</span>' +
                            '<span class="small text-muted">' + entry.created_at + '</span>' +
                        '</div>' +
                        '<div class="small text-muted mt-1">By ' + $('<div>').text(entry.admin_name).html() + '</div>' +
                        remarks +
                    '</li>'
                );
            });
        }).fail(function (xhr) {
            $('#attendanceHistoryModal').modal('hide');
            toastr.error(xhr.responseJSON?.res || 'Unable to load approval history.', 'Error');
        });
    });

    /* -----------------------------------------------------------------
     | Save Filter - persists the current filter for this admin. It will
     | automatically reload and re-apply on every future page load / login
     | (see index()/datatable() - same pattern as the Leads module).
     |------------------------------------------------------------------ */
    $('.savetodoFilter').on('click', function () {
        $.ajax({
            url: "{{ route('admin.attendance.filter.save') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                staff_id: $('#by-staff').val(),
                attendance_type: $('#by-attendance-type').val(),
                status: $('#by-status').val(),
                approved_by: $('#by-approved-by').val(),
                date_from: $('#by-date-from').val(),
                date_to: $('#by-date-to').val(),
            },
            success: function (data) {
                toastr.success(data.res, 'Success', { timeOut: 3000 });
                reloadAttendanceList();
            },
            error: function () {
                toastr.error('Something went wrong.', 'Error');
            }
        });
    });

    /* -----------------------------------------------------------------
     | Reset Filter - clears the fields AND deletes the persisted filter
     | row, so nothing auto-applies on the next page load / login.
     |------------------------------------------------------------------ */
    $('.resetfilter').on('click', function () {
        $.ajax({
            url: "{{ route('admin.attendance.filter.reset') }}",
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function (data) {
                $('#by-staff, #by-attendance-type, #by-status, #by-approved-by').val(null).trigger('change');
                $('#by-date-range').val('');
                $('#by-date-from, #by-date-to').val('');
                toastr.success(data.res, 'Success', { timeOut: 3000 });
                reloadAttendanceList();
            },
            error: function () {
                toastr.error('Something went wrong.', 'Error');
            }
        });
    });

    updateFilterIndicator();

    /* -----------------------------------------------------------------
     | Attendance Slip download
     |------------------------------------------------------------------ */
    function updateSlipDownloadLink() {
        const staffId = (canApprove || canViewAll) ? ($('#slip_admin_id').val() || {{ Auth::guard('admin')->id() }}) : {{ Auth::guard('admin')->id() }};
        const month = $('#slip_month').val();
        const year = $('#slip_year').val();
        $('#slip_download_btn').attr('href', "{{ url('admin/attendance/slip') }}/" + staffId + '/' + month + '/' + year);
    }

    $('#attendanceSlipModal').on('show.bs.modal', updateSlipDownloadLink);
    $('#slip_admin_id, #slip_month, #slip_year').on('change', updateSlipDownloadLink);

});
</script>
@endsection
