@extends('layout.admin.admin_layout')

@section('title','Dashboard')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <style>
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

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom d-flex align-items-center">
      <h5 class="mb-0 me-2"><i class="ti ti-report-money me-1"></i> Live Salary Dashboard</h5>

      @if($canApprove)
      <div class="btn-group">
        <button class="btn btn-xs btn-primary dropdown-toggle optionBtn"
                type="button"
                id="dashboardOptionButton"
                data-bs-toggle="dropdown"
                aria-expanded="false">
          Option
        </button>

        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dashboardOptionButton">
          <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#downloadSalarySlipModal">
            <i class="ti ti-file-invoice me-2"></i> Download Salary Slip
          </button>
          <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#downloadAttendanceSlipModal">
            <i class="ti ti-calendar-stats me-2"></i> Download Attendance Slip
          </button>
          @if($isSuperAdmin)
          <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#dashboardGeneratePayrollModal">
            <i class="ti ti-calculator me-2"></i> Generate Payroll
          </button>
          @endif
        </div>
      </div>
      @endif
    </div>

    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-3 col-6 mb-2">
          <label class="form-label">Month</label>
          <select id="live-month" class="form-select">
            @foreach(range(1,12) as $m)
              <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 col-6 mb-2">
          <label class="form-label">Year</label>
          <select id="live-year" class="form-select">
            @foreach(range(now()->year - 2, now()->year + 1) as $y)
              <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
          </select>
        </div>
        @if($canApprove)
        <div class="col-md-6 mb-2">
          <label class="form-label">Viewing Staff</label>
          <select id="live-staff" class="form-select select2-init" data-placeholder="Select Staff">
            <option value="{{ $admin->id }}" selected>{{ $admin->name }} (Me)</option>
            @foreach($staffList as $s)
              @if($s->id != $admin->id)
              <option value="{{ $s->id }}">{{ $s->name }}</option>
              @endif
            @endforeach
          </select>
        </div>
        @endif
      </div>

      <div id="live-locked-banner" class="alert alert-success d-none">
        <i class="ti ti-lock me-1"></i> This is the <strong>Final Salary</strong> for this period<span id="live-locked-at"></span> - figures below are the locked payroll snapshot, not a live recalculation.
      </div>

      <div class="row g-3" id="live-dashboard-cards"></div>
    </div>
  </div>

  {{-- ============== Download Salary Slip Modal ============== --}}
  <div class="modal fade" id="downloadSalarySlipModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title">Download Salary Slip</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @if($canApprove)
          <div class="mb-3">
            <label class="form-label">Staff</label>
            <select id="salary_slip_admin_id" class="form-select select2-init" data-placeholder="Select Staff">
              <option value="{{ $admin->id }}">{{ $admin->name }} (Me)</option>
              @foreach($staffList as $s)
                @if($s->id != $admin->id)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endif
              @endforeach
            </select>
          </div>
          @endif
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Month</label>
              <select id="salary_slip_month" class="form-select">
                @foreach(range(1,12) as $m)
                  <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Year</label>
              <select id="salary_slip_year" class="form-select">
                @foreach(range(now()->year - 2, now()->year + 1) as $y)
                  <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <a href="javascript:void(0);" id="salary_slip_download_btn" class="btn btn-primary btn-sm" target="_blank">Download</a>
        </div>
      </div>
    </div>
  </div>

  {{-- ============== Download Attendance Slip Modal ============== --}}
  <div class="modal fade" id="downloadAttendanceSlipModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title">Download Attendance Slip</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @if($canApprove)
          <div class="mb-3">
            <label class="form-label">Staff</label>
            <select id="att_slip_admin_id" class="form-select select2-init" data-placeholder="Select Staff">
              <option value="{{ $admin->id }}">{{ $admin->name }} (Me)</option>
              @foreach($staffList as $s)
                @if($s->id != $admin->id)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endif
              @endforeach
            </select>
          </div>
          @endif
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Month</label>
              <select id="att_slip_month" class="form-select">
                @foreach(range(1,12) as $m)
                  <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Year</label>
              <select id="att_slip_year" class="form-select">
                @foreach(range(now()->year - 2, now()->year + 1) as $y)
                  <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <a href="javascript:void(0);" id="att_slip_download_btn" class="btn btn-primary btn-sm" target="_blank">Download</a>
        </div>
      </div>
    </div>
  </div>

  @if($isSuperAdmin)
  {{-- ============== Generate Payroll Modal ============== --}}
  <div class="modal fade" id="dashboardGeneratePayrollModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title">Generate Payroll</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="dashboardGeneratePayrollForm">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Staff <span class="text-danger">*</span></label>
              <select name="admin_id" id="gen_payroll_admin_id" class="form-select select2-init" data-placeholder="Select Staff" required>
                <option value=""></option>
                @foreach($staffList as $s)
                  <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Month <span class="text-danger">*</span></label>
                <select name="month" id="gen_payroll_month" class="form-select" required>
                  @foreach(range(1,12) as $m)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Year <span class="text-danger">*</span></label>
                <select name="year" id="gen_payroll_year" class="form-select" required>
                  @foreach(range(now()->year - 2, now()->year + 1) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <small class="text-muted">This snapshots the current live calculation as a Draft. Lock it from the Payroll page afterwards to make it the Final Salary.</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-sm">Generate</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

</div>
@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
<script>
$(function () {
    const canApprove = @json($canApprove);
    const loggedAdminId = @json($admin->id);
    const salaryFieldMeta = [
        { group: 'Overview', tiles: [
            { key: 'monthly_salary', label: 'Monthly Salary', money: true },
            { key: 'daily_salary', label: 'Daily Salary', money: true },
            { key: 'days_in_month', label: 'Days in Month' },
            { key: 'elapsed_days', label: 'Days Considered' },
            { key: 'current_date', label: 'Current Date' },
        ]},
        { group: 'Attendance', tiles: [
            { key: 'present', label: 'Present' },
            { key: 'absent', label: 'Absent' },
            { key: 'half_days', label: 'Half Days' },
            { key: 'total_late_count', label: 'Total Late Marks' },
        ]},
        { group: 'Deductions', tiles: [
            { key: 'late_deduction', label: 'Late Deduction', money: true, deduction: true },
            { key: 'absent_deduction', label: 'Absent Deduction', money: true, deduction: true },
            { key: 'half_day_deduction', label: 'Half-Day Deduction', money: true, deduction: true },
            { key: 'sunday_deduction', label: 'Sunday Deduction', money: true, deduction: true },
        ]},
        { group: 'Payable', tiles: [
            { key: 'payable_gross_salary', label: 'Payable Gross Salary', money: true },
            { key: 'total_deduction', label: 'Total Deductions', money: true, deduction: true },
            { key: 'net_payable', label: 'Net Payable', money: true, net: true },
        ]},
    ];

    function money(v) {
        v = parseFloat(v || 0);
        return '₹' + v.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // #live-staff sits directly on the page, so it can init immediately.
    $('#live-staff').select2({ placeholder: function(){ return $(this).data('placeholder') || ''; }, allowClear: true });

    // The modal selects must wait until each modal is actually shown -
    // select2 miscalculates its width if initialized while still hidden.
    $('body').on('shown.bs.modal', '.modal', function () {
        $(this).find('.select2-init').each(function () {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({ dropdownParent: $(this).closest('.modal'), placeholder: $(this).data('placeholder') || '', allowClear: true });
            }
        });
    });

    function renderLiveDashboard(data) {
        let html = '';
        salaryFieldMeta.forEach(function (group) {
            group.tiles.forEach(function (tile) {
                let value = data[tile.key];
                let displayValue = tile.money ? money(value) : (value === null || value === undefined ? '-' : value);
                let cls = 'salary-tile' + (tile.deduction ? ' is-deduction' : '') + (tile.net ? ' is-net' : '');
                html += '<div class="col-xl-3 col-md-4 col-6">' +
                    '<div class="' + cls + '">' +
                        '<div class="salary-tile-label">' + tile.label + '</div>' +
                        '<div class="salary-tile-value">' + displayValue + '</div>' +
                    '</div>' +
                '</div>';
            });
        });
        $('#live-dashboard-cards').html(html);

        if (data.is_locked) {
            $('#live-locked-banner').removeClass('d-none');
            $('#live-locked-at').text(data.locked_at ? (' on ' + data.locked_at) : '');
        } else {
            $('#live-locked-banner').addClass('d-none');
        }
    }

    function loadLiveDashboard() {
        const staffId = canApprove ? ($('#live-staff').val() || loggedAdminId) : loggedAdminId;

        $.get("{{ route('admin.salary.live') }}", {
            staff_id: staffId,
            month: $('#live-month').val(),
            year: $('#live-year').val()
        }, function (data) {
            renderLiveDashboard(data);
        }).fail(function (xhr) {
            toastr.error(xhr.responseJSON?.res || 'Unable to load salary data.', 'Error');
        });
    }

    function currentSelection() {
        return {
            staffId: canApprove ? ($('#live-staff').val() || loggedAdminId) : loggedAdminId,
            month: $('#live-month').val(),
            year: $('#live-year').val()
        };
    }

    $('#live-month, #live-year, #live-staff').on('change', loadLiveDashboard);

    loadLiveDashboard();

    /* -----------------------------------------------------------------
     | Download Salary Slip modal - prefilled from the dashboard's
     | current selection on open, independently adjustable before download.
     |------------------------------------------------------------------ */
    function updateSlipDownloadLink(prefix, urlBase) {
        const staffId = canApprove ? ($('#' + prefix + '_admin_id').val() || loggedAdminId) : loggedAdminId;
        const month = $('#' + prefix + '_month').val();
        const year = $('#' + prefix + '_year').val();
        $('#' + prefix + '_download_btn').attr('href', urlBase + '/' + staffId + '/' + month + '/' + year);
    }

    $('#downloadSalarySlipModal').on('show.bs.modal', function () {
        const sel = currentSelection();
        if ($('#salary_slip_admin_id').length) $('#salary_slip_admin_id').val(sel.staffId).trigger('change');
        $('#salary_slip_month').val(sel.month);
        $('#salary_slip_year').val(sel.year);
        updateSlipDownloadLink('salary_slip', "{{ url('admin/salary/slip') }}");
    });
    $('#salary_slip_admin_id, #salary_slip_month, #salary_slip_year').on('change', function () {
        updateSlipDownloadLink('salary_slip', "{{ url('admin/salary/slip') }}");
    });

    $('#downloadAttendanceSlipModal').on('show.bs.modal', function () {
        const sel = currentSelection();
        if ($('#att_slip_admin_id').length) $('#att_slip_admin_id').val(sel.staffId).trigger('change');
        $('#att_slip_month').val(sel.month);
        $('#att_slip_year').val(sel.year);
        updateSlipDownloadLink('att_slip', "{{ url('admin/attendance/slip') }}");
    });
    $('#att_slip_admin_id, #att_slip_month, #att_slip_year').on('change', function () {
        updateSlipDownloadLink('att_slip', "{{ url('admin/attendance/slip') }}");
    });

    @if($isSuperAdmin)
    /* -----------------------------------------------------------------
     | Generate Payroll modal - same fields/behavior as the Payroll page's
     | Generate Payroll modal, prefilled with the dashboard's current
     | month/year on open.
     |------------------------------------------------------------------ */
    $('#dashboardGeneratePayrollModal').on('show.bs.modal', function () {
        const sel = currentSelection();
        $('#gen_payroll_admin_id').val(canApprove ? sel.staffId : null).trigger('change');
        $('#gen_payroll_month').val(sel.month);
        $('#gen_payroll_year').val(sel.year);
    });

    $('#dashboardGeneratePayrollForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.salary.payroll.generate') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function (data) {
                $('#dashboardGeneratePayrollModal').modal('hide');
                toastr.success(data.res, 'Success', { timeOut: 3000 });
                loadLiveDashboard();
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    toastr.error(Object.values(xhr.responseJSON.errors)[0][0], 'Validation Error');
                } else {
                    toastr.error(xhr.responseJSON?.res || 'Something went wrong.', 'Error');
                }
            }
        });
    });
    @endif
});
</script>
@endsection
