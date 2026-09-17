@extends('layout.admin.admin_layout')

@section('title','Payroll')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />

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

        .payroll-slip-preview-trigger {
            cursor: pointer;
            border: none;
            transition: filter 0.15s ease;
        }

        .payroll-slip-preview-trigger:hover,
        .payroll-slip-preview-trigger:focus {
            filter: brightness(0.92);
            text-decoration: none;
        }

        .payroll-total-paid-trigger {
            cursor: pointer;
        }

        .payroll-total-paid-trigger:hover,
        .payroll-total-paid-trigger:focus {
            text-decoration: underline;
        }
    </style>
@endsection

@php
    $currentYear = now()->year;

    $savedFilterData = optional($savedFilter)->filter_data ?? [];
    $savedStaffIds = (array) ($savedFilterData['staff_id'] ?? []);
    $savedStatuses = (array) ($savedFilterData['status'] ?? []);
    $savedPaymentStatuses = (array) ($savedFilterData['payment_status'] ?? []);
    $savedMonth = $savedFilterData['month'] ?? '';
    $savedYear = $savedFilterData['year'] ?? '';
    $savedGeneratedFrom = $savedFilterData['generated_from'] ?? '';
    $savedGeneratedTo = $savedFilterData['generated_to'] ?? '';
    $savedGeneratedRangeText = ($savedGeneratedFrom && $savedGeneratedTo)
        ? \Carbon\Carbon::parse($savedGeneratedFrom)->format('m/d/Y') . ' - ' . \Carbon\Carbon::parse($savedGeneratedTo)->format('m/d/Y')
        : '';
@endphp

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <h5 class="mb-0"><i class="ti ti-lock-dollar me-1"></i> Payroll (Final Salary)</h5>
        <button class="btn btn-xs btn-primary filterpanel position-relative" data-bs-toggle="modal" data-bs-target="#filterpanel">
          <i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter
          <span class="filter-indicator d-none"></span>
        </button>
      </div>
      <div>
         <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#advancePaymentModal">
          <i class="ti ti-cash-banknote me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Advance Payment</span>
        </button>
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#generatePayrollAllModal">
          <i class="ti ti-calendar-stats me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Generate All Payroll</span>
        </button>
       
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#generatePayrollModal">
          <i class="ti ti-calculator me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Generate Payroll</span>
        </button>
      </div>
    </div>

    <div class="card-datatable table-responsive">
      <table class="table border-top" id="payroll-table">
        <thead>
          <tr>
            <th>Staff ID</th>
            <th>Staff</th>
            <th>Period</th>
            <th>Monthly Salary</th>
            <th>Total Deduction</th>
            <th>Net Payable</th>
            <th>Total Paid</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  {{-- ============== Generate Payroll Modal ============== --}}
  <div class="modal fade" id="generatePayrollModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title">Generate Payroll</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="generatePayrollForm">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Staff <span class="text-danger">*</span></label>
              <select name="admin_id" id="payroll_admin_id" class="form-select select2-init" data-placeholder="Select Staff" required>
                <option value=""></option>
                @foreach($staffList as $s)
                  <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Month <span class="text-danger">*</span></label>
                <select name="month" id="payroll_month" class="form-select" required>
                  @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Year <span class="text-danger">*</span></label>
                <select name="year" id="payroll_year" class="form-select" required>
                  @foreach(range($currentYear - 2, $currentYear + 1) as $y)
                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <small class="text-muted">This snapshots the current live calculation as a Draft. Lock it afterwards to make it the Final Salary.</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-sm">Generate</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- ============== Generate Payroll for All (Attendance-based) Modal ============== --}}
  <div class="modal fade" id="generatePayrollAllModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title">Generate Payroll for All</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="generatePayrollAllForm">
          @csrf
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Month <span class="text-danger">*</span></label>
                <select name="month" id="payroll_all_month" class="form-select" required>
                  @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Year <span class="text-danger">*</span></label>
                <select name="year" id="payroll_all_year" class="form-select" required>
                  @foreach(range($currentYear - 2, $currentYear + 1) as $y)
                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <small class="text-muted">Generates a Draft payroll for every staff member who has at least one attendance record in this month - no staff picker needed. Already-locked payrolls for this period are skipped.</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-sm">Generate for All</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- ============== Advance Payment Modal (Add/Edit + List) ============== --}}
  <div class="modal fade" id="advancePaymentModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title">Advance Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="advanceForm" class="border rounded p-3 mb-4">
            @csrf
            <input type="hidden" name="id" id="advance_id">
            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Staff <span class="text-danger">*</span></label>
                <select name="admin_id" id="advance_admin_id" class="form-select select2-init" data-placeholder="Select Staff" required>
                  <option value=""></option>
                  @foreach($staffList as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Advance Payment Date &amp; Time <span class="text-danger">*</span></label>
                <input type="text" name="advance_date" id="advance_date" class="form-control flatpickr-datetime" placeholder="Select date & time..." autocomplete="off" required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Advance Amount <span class="text-danger">*</span></label>
                <input type="number" name="amount" id="advance_amount" class="form-control" min="0.01" step="0.01" placeholder="0.00" required>
              </div>
              <div class="col-md-12 mb-3">
                <label class="form-label">Remarks / Note</label>
                <textarea name="remarks" id="advance_remarks" class="form-control" rows="2" maxlength="2000" placeholder="Optional remarks..."></textarea>
              </div>
            </div>
            <div id="advanceFormMessage"></div>
            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-label-secondary btn-sm d-none" id="advanceCancelEditBtn">Cancel Edit</button>
              <button type="submit" class="btn btn-primary btn-sm" id="advanceSubmitBtn">
                <span id="advanceSubmitLabel">Add Advance Payment</span>
                <span class="spinner-border spinner-border-sm d-none" id="advanceSpinner"></span>
              </button>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table border-top" id="advance-table">
              <thead>
                <tr>
                  <th>Staff</th>
                  <th>Date &amp; Time</th>
                  <th>Amount</th>
                  <th>Payroll Month</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  {{-- ============== Filter Modal ============== --}}
  <div class="modal fade" id="filterpanel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title" id="filterpanelLabel">Payroll Filter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <select id="by-staff" class="form-select select2-init select2-filter" multiple data-placeholder="Staff / Associate">
                @foreach($staffList as $staffMember)
                  <option value="{{ $staffMember->id }}" @if(in_array($staffMember->id, $savedStaffIds)) selected @endif>{{ $staffMember->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <select id="by-status" class="form-select select2-init select2-filter" multiple data-placeholder="Status">
                <option value="draft" @if(in_array('draft', $savedStatuses)) selected @endif>Draft</option>
                <option value="locked" @if(in_array('locked', $savedStatuses)) selected @endif>Locked (Final)</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <select id="by-payment-status" class="form-select select2-init select2-filter" multiple data-placeholder="Payment Status">
                <option value="{{ \App\Models\Payroll::PAYMENT_PENDING }}" @if(in_array(\App\Models\Payroll::PAYMENT_PENDING, $savedPaymentStatuses)) selected @endif>Pending</option>
                <option value="{{ \App\Models\Payroll::PAYMENT_PAID }}" @if(in_array(\App\Models\Payroll::PAYMENT_PAID, $savedPaymentStatuses)) selected @endif>Paid</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <select id="by-month" class="form-select select2-filter">
                <option value="">All Months</option>
                @foreach(range(1,12) as $m)
                  <option value="{{ $m }}" @if((string) $savedMonth === (string) $m) selected @endif>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <select id="by-year" class="form-select select2-filter">
                <option value="">All Years</option>
                @foreach(range($currentYear + 1, $currentYear - 5) as $y)
                  <option value="{{ $y }}" @if((string) $savedYear === (string) $y) selected @endif>{{ $y }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <input type="text" id="by-generated-range" class="form-control bsdatpicket" placeholder="Generated Date Range..." value="{{ $savedGeneratedRangeText }}">
              <input type="hidden" id="by-generated-from" value="{{ $savedGeneratedFrom }}">
              <input type="hidden" id="by-generated-to" value="{{ $savedGeneratedTo }}">
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

  {{-- ============== Mark as Paid Modal ============== --}}
  <div class="modal fade" id="markPaidModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="markPaidForm" enctype="multipart/form-data">
          @csrf
          <div class="modal-header">
            <h5 class="offcanvas-title">Mark as Paid</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="markPaidPayrollId" name="payroll_id">

            <p class="mb-3">
              Upload the salary slip to confirm this payroll as paid. The status will only change to
              <strong>Paid</strong> once the slip is uploaded successfully.
            </p>

            <div class="mb-3">
              <label class="form-label" for="markPaidSlip">Salary Slip <span class="text-danger">*</span></label>
              <input type="file" class="form-control" id="markPaidSlip" name="payment_slip" accept=".jpg,.jpeg,.png,.pdf" required>
              <div class="form-text">Accepted: JPG, PNG, PDF. Max size 5 MB.</div>
              <div class="invalid-feedback" id="markPaidSlipError"></div>
            </div>

            <div class="mb-3">
              <label class="form-label" for="markPaidExtraPaid">Any Extra Paid</label>
              <input type="number" class="form-control" id="markPaidExtraPaid" name="extra_paid" min="0" step="0.01" value="0">
              <div class="form-text">Optional - any extra amount paid on top of Net Payable.</div>
              <div class="invalid-feedback" id="markPaidExtraPaidError"></div>
            </div>

            <div id="markPaidMessage"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm" id="markPaidSubmitBtn">
              <span class="mark-paid-label">Confirm Mark as Paid</span>
              <span class="spinner-border spinner-border-sm d-none" id="markPaidSpinner"></span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- ============== Salary Slip Preview Modal (images only - PDF opens in a new tab) ============== --}}
  <div class="modal fade" id="payrollSlipPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title">Salary Slip</h5>
          <a id="payrollSlipDownloadBtn" href="" class="btn btn-icon btn-label-primary btn-sm ms-auto me-2" title="Download salary slip" download>
            <i class="ti ti-download"></i>
          </a>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <img id="payrollSlipPreviewImage" src="" alt="Salary Slip" class="img-fluid rounded border" style="max-height: 70vh;">
          <div id="payrollSlipPreviewError" class="alert alert-warning mt-2 mb-0 d-none"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  {{-- ============== Payment Details Modal ============== --}}
  <div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title">Payment Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Net Payable</span>
            <span id="paymentDetailsNetPayable" class="fw-medium"></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Any Extra Paid</span>
            <span id="paymentDetailsExtraPaid" class="fw-medium"></span>
          </div>
          <div class="d-flex justify-content-between mb-2 d-none" id="paymentDetailsAdvanceRow">
            <span class="text-muted">Advance Payment</span>
            <span id="paymentDetailsAdvanceAmount" class="fw-medium text-danger"></span>
          </div>
          <div id="paymentDetailsAdvanceList" class="mb-2 small text-muted d-none"></div>
          <hr class="my-2">
          <div class="d-flex justify-content-between">
            <span class="fw-semibold">Total Paid</span>
            <span id="paymentDetailsTotalPaid" class="fw-semibold text-success"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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
<script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script>
$(function () {
    function money(v) {
        v = parseFloat(v || 0);
        return '₹' + v.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    $('body').on('shown.bs.modal', '.modal', function () {
        $(this).find('.select2-init').each(function () {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({ dropdownParent: $(this).closest('.modal'), placeholder: $(this).data('placeholder') || '', allowClear: true });
            }
        });
    });

    /* -----------------------------------------------------------------
     | Another admin (or another tab) can generate/lock/delete a payroll
     | row while it's still showing on this screen. Rather than just
     | erroring on a stale click, treat a 404 as "the list is out of
     | date" and refresh it - everything else still shows as a real error.
     |------------------------------------------------------------------ */
    function handlePayrollActionError(xhr) {
        if (xhr.status === 404) {
            toastr.info(xhr.responseJSON?.res || 'That payroll record no longer exists - the list has been refreshed.', 'Out of date');
            payrollTable.ajax.reload(null, false);
            return;
        }

        if (xhr.status === 422 && xhr.responseJSON?.errors) {
            toastr.error(Object.values(xhr.responseJSON.errors)[0][0], 'Validation Error');
            return;
        }

        toastr.error(xhr.responseJSON?.res || 'Something went wrong.', 'Error');
    }

    let payrollTable = $('#payroll-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        destroy: true,
        pageLength: 10,
        ajax: {
            url: "{{ route('admin.salary.payroll.json') }}",
            type: 'GET',
            data: function (d) {
                d.staff_id = $('#by-staff').val();
                d.status = $('#by-status').val();
                d.payment_status = $('#by-payment-status').val();
                d.month = $('#by-month').val();
                d.year = $('#by-year').val();
                d.generated_from = $('#by-generated-from').val();
                d.generated_to = $('#by-generated-to').val();
            }
        },
        columns: [
            { data: 'staff_id', name: 'admin.id' },
            { data: 'staff_name', name: 'admin.name' },
            { data: 'period', name: 'year' },
            { data: 'monthly_salary', name: 'monthly_salary', render: (d) => money(d) },
            { data: 'total_deduction', name: 'total_deduction', render: (d) => money(d) },
            { data: 'net_payable', name: 'net_payable', render: (d) => money(d) },
            {
                data: 'total_paid', name: 'total_paid', orderable: false, searchable: false,
                render: (d, type, row) => {
                    if (type !== 'display') {
                        return d;
                    }

                    return '<a href="javascript:void(0);" style="text-decoration:none;" class="payroll-total-paid-trigger text-primary fw-medium"'
                        + ' data-id="' + row.id + '"'
                        + ' data-net-payable="' + row.net_payable + '"'
                        + ' data-extra-paid="' + (row.extra_paid ?? 0) + '"'
                        + ' data-advance-deduction="' + (row.advance_deduction ?? 0) + '"'
                        + ' data-total-paid="' + row.total_paid + '"'
                        + ' title="Click to view payment details">'
                        + money(d) + ' <i class="ti ti-info-circle ti-xs"></i>'
                        + '</a>';
                }
            },
            { data: 'status_badge', name: 'status' },
            { data: 'payment_badge', name: 'payment_status' },
            { data: 'actions', name: 'actions', searchable: false, orderable: false },
        ],
        language: { processing: '<i class="fa fa-spinner fa-spin"></i> Loading...' }
    });

    /* -----------------------------------------------------------------
     | Filter reload helper - pagination/sorting/pageLength are preserved
     | automatically since ajax.reload(null, false) keeps DataTables'
     | current paging state and only re-reads the filter fields above.
     |------------------------------------------------------------------ */
    function reloadPayrollList() {
        payrollTable.ajax.reload(null, false);
        updateFilterIndicator();
    }

    /* -----------------------------------------------------------------
     | Generated Date Range Picker (same pattern as Attendance/Leads) -
     | applies/cancels reload the DataTable immediately, no Apply button.
     |------------------------------------------------------------------ */
    $('#by-generated-range').daterangepicker({
        autoUpdateInput: false,
        opens: 'right',
        locale: { cancelLabel: 'Clear' }
    }).on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        $('#by-generated-from').val(picker.startDate.format('YYYY-MM-DD'));
        $('#by-generated-to').val(picker.endDate.format('YYYY-MM-DD'));
        reloadPayrollList();
    }).on('cancel.daterangepicker', function () {
        $(this).val('');
        $('#by-generated-from').val('');
        $('#by-generated-to').val('');
        reloadPayrollList();
    });

    /* -----------------------------------------------------------------
     | Auto Apply - every filter field reloads the DataTable the moment
     | it changes, exactly like the Attendance/Leads modules (no Apply
     | Filter button - Save Filter only persists the current selection).
     |------------------------------------------------------------------ */
    $('.select2-filter').on('change', function () {
        reloadPayrollList();
    });

    /* -----------------------------------------------------------------
     | Filter Indicator
     |------------------------------------------------------------------ */
    function updateFilterIndicator() {
        let isFiltered =
            ($('#by-staff').val() && $('#by-staff').val().length > 0) ||
            ($('#by-status').val() && $('#by-status').val().length > 0) ||
            ($('#by-payment-status').val() && $('#by-payment-status').val().length > 0) ||
            $('#by-month').val() ||
            $('#by-year').val() ||
            $('#by-generated-from').val() ||
            $('#by-generated-to').val();

        if (isFiltered) {
            $('.filterpanel .filter-indicator').removeClass('d-none').addClass('d-block');
        } else {
            $('.filterpanel .filter-indicator').removeClass('d-block').addClass('d-none');
        }
    }

    /* -----------------------------------------------------------------
     | Save Filter - persists the current filter for this admin. It will
     | automatically reload and re-apply on every future page load / login
     | (see index()/payrollDatatable() - same pattern as Attendance/Leads).
     |------------------------------------------------------------------ */
    $('.savetodoFilter').on('click', function () {
        $.ajax({
            url: "{{ route('admin.salary.payroll.filter.save') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                staff_id: $('#by-staff').val(),
                status: $('#by-status').val(),
                payment_status: $('#by-payment-status').val(),
                month: $('#by-month').val(),
                year: $('#by-year').val(),
                generated_from: $('#by-generated-from').val(),
                generated_to: $('#by-generated-to').val(),
            },
            success: function (data) {
                toastr.success(data.res, 'Success', { timeOut: 3000 });
                reloadPayrollList();
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
            url: "{{ route('admin.salary.payroll.filter.reset') }}",
            method: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            success: function (data) {
                $('#by-staff, #by-status, #by-payment-status').val(null).trigger('change');
                $('#by-month, #by-year').val('');
                $('#by-generated-range').val('');
                $('#by-generated-from, #by-generated-to').val('');
                toastr.success(data.res, 'Success', { timeOut: 3000 });
                reloadPayrollList();
            },
            error: function () {
                toastr.error('Something went wrong.', 'Error');
            }
        });
    });

    updateFilterIndicator();

    /* -----------------------------------------------------------------
     | Mark as Paid - Salary Slip upload is mandatory; the modal never
     | submits without a file (native "required" + the check below), and
     | the server independently refuses to flip the status without one.
     |------------------------------------------------------------------ */
    $('#markPaidModal').on('hidden.bs.modal', function () {
        $('#markPaidForm')[0].reset();
        $('#markPaidPayrollId').val('');
        $('#markPaidSlip').removeClass('is-invalid');
        $('#markPaidSlipError').text('');
        $('#markPaidExtraPaid').removeClass('is-invalid');
        $('#markPaidExtraPaidError').text('');
        $('#markPaidMessage').html('');
    });

    $('#payrollSlipPreviewModal').on('hidden.bs.modal', function () {
        $('#payrollSlipPreviewImage').attr('src', '').removeClass('d-none');
        $('#payrollSlipPreviewError').addClass('d-none').text('');
        $('#payrollSlipDownloadBtn').attr('href', '');
    });

    $('body').on('click', '.payroll-mark-paid-trigger', function () {
        $('#markPaidPayrollId').val($(this).data('id'));
        new bootstrap.Modal(document.getElementById('markPaidModal')).show();
    });

    $('#markPaidForm').on('submit', function (e) {
        e.preventDefault();

        $('#markPaidSlip').removeClass('is-invalid');
        $('#markPaidSlipError').text('');
        $('#markPaidExtraPaid').removeClass('is-invalid');
        $('#markPaidExtraPaidError').text('');
        $('#markPaidMessage').html('');

        if (!$('#markPaidSlip')[0].files.length) {
            $('#markPaidSlip').addClass('is-invalid');
            $('#markPaidSlipError').text('Please select a salary slip to upload.');
            return;
        }

        const $btn = $('#markPaidSubmitBtn');
        $btn.prop('disabled', true);
        $('.mark-paid-label', $btn).addClass('d-none');
        $('#markPaidSpinner').removeClass('d-none');

        const formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.salary.payroll.mark_paid') }}",
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                bootstrap.Modal.getInstance(document.getElementById('markPaidModal')).hide();
                toastr.success(data.res || 'Payroll marked as paid successfully!', 'Success', { timeOut: 3000 });
                reloadPayrollList();
            },
            error: function (xhr) {
                // The row's id was resolved from whatever the table last
                // rendered - if it's since been deleted or regenerated with
                // a fresh id (see payrollMarkPaid()'s 404), the fix is to
                // show the current list, not a raw validation string, so
                // this mirrors handlePayrollActionError()'s same recovery.
                if (xhr.status === 404) {
                    bootstrap.Modal.getInstance(document.getElementById('markPaidModal'))?.hide();
                    toastr.info(xhr.responseJSON?.res || 'That payroll record no longer exists - the list has been refreshed.', 'Out of date');
                    reloadPayrollList();
                    return;
                }

                let message = 'Unable to mark payroll as paid.';

                if (xhr.status === 422 && xhr.responseJSON) {
                    if (xhr.responseJSON.errors && xhr.responseJSON.errors.payment_slip) {
                        $('#markPaidSlip').addClass('is-invalid');
                        $('#markPaidSlipError').text(xhr.responseJSON.errors.payment_slip[0]);
                    }
                    if (xhr.responseJSON.errors && xhr.responseJSON.errors.extra_paid) {
                        $('#markPaidExtraPaid').addClass('is-invalid');
                        $('#markPaidExtraPaidError').text(xhr.responseJSON.errors.extra_paid[0]);
                    }
                    message = xhr.responseJSON.res || message;
                } else if (xhr.status === 403) {
                    message = 'You do not have permission to perform this action.';
                } else if (xhr.responseJSON?.res) {
                    message = xhr.responseJSON.res;
                }

                $('#markPaidMessage').html('<div class="alert alert-danger mt-2 mb-0">' + message + '</div>');
            },
            complete: function () {
                $btn.prop('disabled', false);
                $('.mark-paid-label', $btn).removeClass('d-none');
                $('#markPaidSpinner').addClass('d-none');
            }
        });
    });

    $('#generatePayrollForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.salary.payroll.generate') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function (data) {
                $('#generatePayrollModal').modal('hide');
                $('#generatePayrollForm')[0].reset();
                $('#payroll_admin_id').val(null).trigger('change');
                toastr.success(data.res, 'Success', { timeOut: 3000 });
                payrollTable.ajax.reload(null, false);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    toastr.error(Object.values(xhr.responseJSON.errors)[0][0], 'Validation Error');
                } else {
                    toastr.error(xhr.responseJSON?.res || 'Something went wrong.', 'Error');
                }
            }
        });
    });

    $('#generatePayrollAllForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('admin.salary.payroll.generate.all') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function (data) {
                $('#generatePayrollAllModal').modal('hide');
                toastr.success(data.res, 'Success', { timeOut: 5000 });
                payrollTable.ajax.reload(null, false);
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    toastr.error(Object.values(xhr.responseJSON.errors)[0][0], 'Validation Error');
                } else {
                    toastr.error(xhr.responseJSON?.res || 'Something went wrong.', 'Error');
                }
            }
        });
    });

    $('body').on('click', '.payroll-lock-trigger, .payroll-delete-trigger', function () {
        const id = $(this).data('id');
        const isLock = $(this).hasClass('payroll-lock-trigger');
        const isLockedRecord = $(this).data('status') === 'locked';
        const url = isLock
            ? "{{ route('admin.salary.payroll.lock') }}"
            : "{{ route('admin.salary.payroll.delete') }}";
        const text = isLock
            ? 'Lock this payroll as the Final Salary? This cannot be undone.'
            : (isLockedRecord
                ? 'This is a LOCKED Final Salary record. Deleting it is permanent and cannot be undone.'
                : 'This payroll will be permanently deleted.');

        Swal.fire({
            title: 'Are you sure?',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed!',
            customClass: { confirmButton: 'btn btn-primary me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _token: "{{ csrf_token() }}", id: id },
                    success: function (data) {
                        toastr.success(data.res, 'Success', { timeOut: 3000 });
                        payrollTable.ajax.reload(null, false);
                    },
                    error: handlePayrollActionError
                });
            }
        });
    });

    /* -----------------------------------------------------------------
     | Salary Slip Preview - clicking the "Paid" badge opens the slip that
     | was uploaded via Mark as Paid. PDFs use the browser's own PDF viewer
     | (new tab); images open in the in-page preview modal. The badge only
     | renders (server-side) when payment_status is Paid, but the slip file
     | itself could still be missing on disk (data_id kept for a deleted
     | file, etc.) - both the empty-extension case and a failed image load
     | are handled with a message instead of a broken/blank view.
     |------------------------------------------------------------------ */
    $('body').on('click', '.payroll-slip-preview-trigger', function () {
        const url = $(this).data('url');
        const downloadUrl = $(this).data('download-url');
        const ext = ($(this).data('ext') || '').toString().toLowerCase();

        if (!ext) {
            toastr.info('No salary slip is available for this payroll record.', 'Not found');
            return;
        }

        if (ext === 'pdf') {
            window.open(url, '_blank');
            return;
        }

        $('#payrollSlipPreviewError').addClass('d-none').text('');
        $('#payrollSlipPreviewImage').removeClass('d-none').attr('src', url);
        $('#payrollSlipDownloadBtn').attr('href', downloadUrl);
        new bootstrap.Modal(document.getElementById('payrollSlipPreviewModal')).show();
    });

    $('#payrollSlipPreviewImage').on('error', function () {
        $(this).addClass('d-none');
        $('#payrollSlipPreviewError').removeClass('d-none').text('This salary slip could not be loaded. It may have been moved or deleted.');
    });

    /* -----------------------------------------------------------------
     | Payment Details - the Total Paid amount opens a compact breakdown.
     | All three figures come straight off the clicked row's own data
     | attributes (set from the same payroll record the table already
     | rendered) - money() only formats them for display, it never
     | recalculates the sum, so this can never drift from what the server
     | computed.
     |------------------------------------------------------------------ */
    $('body').on('click', '.payroll-total-paid-trigger', function () {
        const payrollId = $(this).data('id');
        const advanceDeduction = parseFloat($(this).data('advance-deduction') || 0);

        $('#paymentDetailsNetPayable').text(money($(this).data('net-payable')));
        $('#paymentDetailsExtraPaid').text(money($(this).data('extra-paid')));
        $('#paymentDetailsTotalPaid').text(money($(this).data('total-paid')));

        $('#paymentDetailsAdvanceList').addClass('d-none').html('');

        if (advanceDeduction > 0) {
            $('#paymentDetailsAdvanceAmount').text('- ' + money(advanceDeduction));
            $('#paymentDetailsAdvanceRow').removeClass('d-none');

            /* Breakdown (one row per Advance Payment linked to this payroll)
               is fetched lazily, only when this modal is actually opened -
               never on every row of the payroll table. */
            $.get("{{ url('admin/salary/payroll') }}/" + payrollId + "/advances", function (data) {
                if (!data.advances || !data.advances.length) {
                    return;
                }

                let html = '<div class="border rounded p-2 mt-1">';
                data.advances.forEach(function (adv) {
                    html += '<div class="d-flex justify-content-between">'
                        + '<span>' + adv.date_time + (adv.remarks ? ' <em>(' + $('<div>').text(adv.remarks).html() + ')</em>' : '') + '</span>'
                        + '<span>' + money(adv.amount) + '</span>'
                        + '</div>';
                });
                html += '</div>';

                $('#paymentDetailsAdvanceList').html(html).removeClass('d-none');
            });
        } else {
            $('#paymentDetailsAdvanceRow').addClass('d-none');
        }

        new bootstrap.Modal(document.getElementById('paymentDetailsModal')).show();
    });

    /* -----------------------------------------------------------------
     | Advance Payment - management modal (Add/Edit form + list table).
     |------------------------------------------------------------------ */
    let advanceTable = $('#advance-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        destroy: true,
        pageLength: 10,
        ajax: {
            url: "{{ route('admin.salary.advance.json') }}",
            type: 'GET'
        },
        columns: [
            { data: 'staff_name', name: 'admin.name' },
            { data: 'date_time', name: 'advance_date' },
            { data: 'amount', name: 'amount', render: (d) => money(d) },
            { data: 'payroll_month', name: 'year', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        language: { processing: '<i class="fa fa-spinner fa-spin"></i> Loading...' }
    });

    $('.flatpickr-datetime').flatpickr({
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        altInput: true,
        altFormat: 'd M Y, h:i K',
        maxDate: 'today',
        time_24hr: false
    });

    function resetAdvanceForm() {
        $('#advanceForm')[0].reset();
        $('#advance_id').val('');
        $('#advance_admin_id').val(null).trigger('change');
        $('#advanceSubmitLabel').text('Add Advance Payment');
        $('#advanceCancelEditBtn').addClass('d-none');
        $('#advanceFormMessage').html('');
        $('#advance_date, #advance_amount').removeClass('is-invalid');
    }

    $('#advancePaymentModal').on('hidden.bs.modal', function () {
        resetAdvanceForm();
    });

    $('#advanceCancelEditBtn').on('click', function () {
        resetAdvanceForm();
    });

    $('#advanceForm').on('submit', function (e) {
        e.preventDefault();

        $('#advanceFormMessage').html('');
        $('#advance_date, #advance_amount, #advance_admin_id').removeClass('is-invalid');

        const id = $('#advance_id').val();
        const url = id ? "{{ route('admin.salary.advance.update') }}" : "{{ route('admin.salary.advance.store') }}";

        const $btn = $('#advanceSubmitBtn');
        $btn.prop('disabled', true);
        $('#advanceSpinner').removeClass('d-none');

        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            success: function (data) {
                toastr.success(data.res || 'Saved successfully!', 'Success', { timeOut: 3000 });
                resetAdvanceForm();
                advanceTable.ajax.reload(null, false);
                payrollTable.ajax.reload(null, false);
            },
            error: function (xhr) {
                if (xhr.status === 404) {
                    toastr.info(xhr.responseJSON?.res || 'That advance payment no longer exists - the list has been refreshed.', 'Out of date');
                    resetAdvanceForm();
                    advanceTable.ajax.reload(null, false);
                    return;
                }

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    toastr.error(Object.values(xhr.responseJSON.errors)[0][0], 'Validation Error');
                } else {
                    $('#advanceFormMessage').html('<div class="alert alert-danger mt-2 mb-0">' + (xhr.responseJSON?.res || 'Something went wrong.') + '</div>');
                }
            },
            complete: function () {
                $btn.prop('disabled', false);
                $('#advanceSpinner').addClass('d-none');
            }
        });
    });

    $('body').on('click', '.advance-edit-trigger', function () {
        const id = $(this).data('id');

        $.get("{{ url('admin/salary/advance/show') }}/" + id, function (data) {
            $('#advance_id').val(data.id);
            $('#advance_admin_id').val(data.admin_id).trigger('change');
            $('#advance_amount').val(data.amount);
            $('#advance_remarks').val(data.remarks);

            const fp = document.querySelector('#advance_date')._flatpickr;
            if (fp) {
                fp.setDate(data.advance_date.replace('T', ' '), true);
            }

            $('#advanceSubmitLabel').text('Update Advance Payment');
            $('#advanceCancelEditBtn').removeClass('d-none');
            $('html, body, .modal-body').animate({ scrollTop: 0 }, 200);
        }).fail(function (xhr) {
            toastr.error(xhr.responseJSON?.res || 'Unable to load this advance payment.', 'Error');
        });
    });

    $('body').on('click', '.advance-delete-trigger', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This advance payment will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete!',
            customClass: { confirmButton: 'btn btn-primary me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.salary.advance.delete') }}",
                    method: 'POST',
                    data: { _token: "{{ csrf_token() }}", id: id },
                    success: function (data) {
                        toastr.success(data.res, 'Success', { timeOut: 3000 });
                        advanceTable.ajax.reload(null, false);
                        payrollTable.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        if (xhr.status === 404) {
                            toastr.info(xhr.responseJSON?.res || 'That advance payment no longer exists - the list has been refreshed.', 'Out of date');
                            advanceTable.ajax.reload(null, false);
                            return;
                        }
                        toastr.error(xhr.responseJSON?.res || 'Something went wrong.', 'Error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
