@extends('layout.admin.admin_layout')

@section('title','Settings')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <style>
       #salary-holidays-tab,#salary-settings-tab .row {
          margin-top: 50px;
        }
    </style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header border-bottom p-0">
      <ul class="nav nav-tabs card-header-tabs mx-0" role="tablist">
        <li class="nav-item">
          <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#salary-holidays-tab">
            <i class="ti ti-sun me-1"></i> Holidays
          </button>
        </li>
        @if($canManageRoles)
        <li class="nav-item">
          <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#salary-settings-tab">
            <i class="ti ti-settings me-1"></i> Settings
          </button>
        </li>
        @endif
      </ul>
    </div>

    <div class="card-body">
      <div class="tab-content p-0">

        {{-- ================= HOLIDAYS ================= --}}
        <div class="tab-pane fade show active" id="salary-holidays-tab" role="tabpanel">
              @if($canManageHolidays)
              <div class="mb-2 text-end">
                <button type="button" class="btn btn-sm btn-primary" id="addHolidayBtn" data-bs-toggle="modal" data-bs-target="#holidayModal">
                  <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Holiday</span>
                </button>
              </div>
              @endif

            <div class="card-datatable table-responsive">
              <table class="table border-top" id="holidays-table">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
        </div>

        @if($canManageRoles)
        {{-- ================= SETTINGS ================= --}}
        <div class="tab-pane fade" id="salary-settings-tab" role="tabpanel">
          <div class="row">
            <!-- Left Side -->
            @if($isSuperAdmin)
            <div class="col-lg-6 mb-4">
              <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Salary Configuration</h6></div>
                <div class="card-body">
                  <form id="salarySettingsForm">
                    @csrf
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Office Start Time</label>
                        <input type="time" name="office_start_time" id="set_office_start_time" class="form-control" value="{{ substr($settings->office_start_time,0,5) }}" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Qualifying Late Ends</label>
                        <input type="time" name="qualifying_late_end_time" id="set_qualifying_late_end_time" class="form-control" value="{{ substr($settings->qualifying_late_end_time,0,5) }}" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Late Window Ends</label>
                        <input type="time" name="late_window_end_time" id="set_late_window_end_time" class="form-control" value="{{ substr($settings->late_window_end_time,0,5) }}" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Half Day After</label>
                        <input type="time" name="half_day_after_time" id="set_half_day_after_time" class="form-control" value="{{ substr($settings->half_day_after_time,0,5) }}" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Free Qualifying Lates / Month</label>
                        <input type="number" min="0" name="free_late_count" class="form-control" value="{{ $settings->free_late_count }}" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Qualifying Late Max Minutes</label>
                        <input type="number" min="0" name="free_late_max_minutes" class="form-control" value="{{ $settings->free_late_max_minutes }}" required>
                      </div>
                    </div>

                    <hr class="my-3">
                    <h6 class="mb-2">Payroll Deduction Calculation</h6>
                    <small class="text-muted d-block mb-3">Decides the rupee amount charged for each Late Mark (once the Free Qualifying Lates allowance above is exceeded).</small>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Deduction Calculation Type</label>
                        <select name="deduction_calculation_type" id="set_deduction_calculation_type" class="form-select" required>
                          <option value="fixed_slab" {{ $settings->deduction_calculation_type === 'fixed_slab' ? 'selected' : '' }}>Fixed Slab Based</option>
                          <option value="percentage" {{ $settings->deduction_calculation_type === 'percentage' ? 'selected' : '' }}>Percentage Based</option>
                          <option value="fixed_amount" {{ $settings->deduction_calculation_type === 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                      </div>
                      <div class="w-100 d-none d-md-block"></div>
                      <div class="col-md-6 mb-3" id="deduction_percentage_wrapper">
                        <label class="form-label">Percentage</label>
                        <input type="number" min="0" max="100" step="0.01" name="late_deduction_percent" id="input_late_deduction_percent" class="form-control" value="{{ $settings->late_deduction_percent }}">
                      </div>
                      <div class="col-md-6 mb-3" id="deduction_slab_amount_wrapper">
                        <label class="form-label">Slab Amount</label>
                        <input type="number" min="0.01" step="0.01" name="deduction_slab_amount" id="input_deduction_slab_amount" class="form-control" value="{{ $settings->deduction_slab_amount }}">
                      </div>
                      <div class="col-md-6 mb-3" id="deduction_per_slab_wrapper">
                        <label class="form-label" id="deduction_per_slab_label">Deduction Per Slab</label>
                        <input type="number" min="0" step="0.01" name="deduction_per_slab" id="input_deduction_per_slab" class="form-control" value="{{ $settings->deduction_per_slab }}">
                      </div>
                    </div>

                    <div class="form-check form-switch mb-2">
                      <input class="form-check-input" type="checkbox" name="retroactive_late_deduction" id="set_retroactive" {{ $settings->retroactive_late_deduction ? 'checked' : '' }}>
                      <label class="form-check-label" for="set_retroactive">Retroactive late deduction (applies to all qualifying lates once the free allowance is exceeded)</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                      <input class="form-check-input" type="checkbox" name="sat_absent_sunday_deduction" id="set_sat_rule" {{ $settings->sat_absent_sunday_deduction ? 'checked' : '' }}>
                      <label class="form-check-label" for="set_sat_rule">Saturday Absent deducts the following Sunday (Weekly Off)</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                      <input class="form-check-input" type="checkbox" name="mon_absent_prev_sunday_deduction" id="set_mon_rule" {{ $settings->mon_absent_prev_sunday_deduction ? 'checked' : '' }}>
                      <label class="form-check-label" for="set_mon_rule">Monday Absent deducts the previous Sunday (Weekly Off)</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">Save Configuration</button>
                  </form>

                  <hr class="my-3">
                  <a href="{{ route('admin.salary.rules') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-book me-1"></i> View All Salary Calculation Rules
                  </a>
                </div>
              </div>
            </div>
            @endif
            <!-- Right Side -->
            <div class="col-lg-6 mb-4">
              <div class="card">
                <div class="card-header">
                  <h6 class="mb-0">Role Configuration</h6>
                  <small class="text-muted">Assign a staff member as the Team Member responsible for handling and managing assigned CRM activities.</small>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label">Select Staff</label>
                    <select id="role_config_staff" class="form-select select2-init" data-placeholder="Select Staff">
                      <option value=""></option>
                      @foreach($staffList as $s)
                        <option value="{{ $s->id }}" data-is-team-head="{{ $currentTeamHead && $s->id == $currentTeamHead->id ? '1' : '0' }}" {{ $currentTeamHead && $s->id == $currentTeamHead->id ? 'selected' : '' }}>{{ $s->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div id="role_config_result" class="d-none d-flex align-items-center border rounded p-2">
                    <div class="form-check form-switch mb-0">
                      <input class="form-check-input" type="checkbox" id="role_config_team_head_toggle">
                      <label class="form-check-label" for="role_config_team_head_toggle">Team Head</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="role_config_save">Save</button>
                  </div>
                </div>

                @if($canManageFinalApproval)
                <div class="card-header">
                  <h6 class="mb-0">Final Approval Access</h6>
                  <small class="text-muted">Assign a staff member who is authorized to provide final approval for the relevant CRM process.</small>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label">Select Staff</label>
                    <select id="final_approval_config_staff" class="form-select select2-init" data-placeholder="Select Staff">
                      <option value=""></option>
                      @foreach($staffList as $s)
                        <option value="{{ $s->id }}" data-is-final-approver="{{ $currentFinalApprover && $s->id == $currentFinalApprover->id ? '1' : '0' }}" {{ $currentFinalApprover && $s->id == $currentFinalApprover->id ? 'selected' : '' }}>{{ $s->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div id="final_approval_config_result" class="d-none d-flex align-items-center border rounded p-2">
                    <div class="form-check form-switch mb-0">
                      <input class="form-check-input" type="checkbox" id="final_approval_config_toggle">
                      <label class="form-check-label" for="final_approval_config_toggle">Final Approval Access</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="final_approval_config_save">Save</button>
                  </div>
                </div>
                @endif

               @if($isSuperAdmin)
                  <div class="card-header">
                    <h6 class="mb-0">Staff Monthly Salary</h6>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label">Select Staff</label>
                      <select id="salary_config_staff" class="form-select select2-init" data-placeholder="Select Staff">
                        <option value=""></option>
                        @foreach($staffList as $s)
                          <option value="{{ $s->id }}" data-monthly-salary="{{ $s->monthly_salary }}">{{ $s->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div id="salary_config_result" class="d-none">
                      <div class="d-flex justify-content-between align-items-center border rounded p-2">
                        <span id="salary_config_staff_name" class="fw-medium"></span>
                        <div class="d-flex align-items-center gap-2">
                          <input type="number" min="0" step="0.01" class="form-control form-control-sm" id="salary_config_input" style="width: 130px;">
                          <button type="button" class="btn btn-sm btn-outline-primary" id="salary_config_save">Save</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endif
            </div>
          </div>


        </div>
        @endif

      </div>
    </div>
  </div>

  {{-- ============== Holiday Modal ============== --}}
  @if($canManageHolidays)
  <div class="modal fade" id="holidayModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="offcanvas-title" id="holidayModalLabel">Add Holiday</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="holidayForm">
          @csrf
          <input type="hidden" name="id" id="holiday_id">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Date <span class="text-danger">*</span></label>
              <input type="date" name="holiday_date" id="holiday_date" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="holiday_name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" id="holiday_description" rows="2" class="form-control"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-sm">Save</button>
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
    const isSuperAdmin = @json($isSuperAdmin);

    @if($canManageRoles)
    $('#role_config_staff').select2({ placeholder: function () { return $(this).data('placeholder') || ''; }, allowClear: true });

    $('#role_config_staff').on('change', function () {
        const adminId = $(this).val();

        if (!adminId) {
            $('#role_config_result').addClass('d-none');
            return;
        }

        const selected = $(this).find('option:selected');
        const isTeamHead = selected.data('is-team-head') == 1;

        $('#role_config_team_head_toggle').data('admin-id', adminId).prop('checked', isTeamHead);
        $('#role_config_result').removeClass('d-none');
    });

    // Reflect the actual DB state on page load: preselected option (if any)
    // shows its toggle already ON, nothing selected keeps the panel hidden.
    $('#role_config_staff').trigger('change');

    $('#role_config_save').on('click', function () {
        const checkbox = $('#role_config_team_head_toggle');
        const adminId = checkbox.data('admin-id');
        const isTeamHead = checkbox.is(':checked');
        const selectedName = $('#role_config_staff option:selected').text();

        function saveRoleConfig() {
            $.ajax({
                url: "{{ route('admin.salary.staff.role.update') }}",
                method: 'POST',
                data: { _token: "{{ csrf_token() }}", admin_id: adminId, is_team_head: isTeamHead ? 1 : 0 },
                success: function (data) {
                    toastr.success(data.res, 'Success', { timeOut: 3000 });
                    if (isTeamHead) {
                        $('#role_config_staff option').not(':selected').each(function () {
                            $(this).data('is-team-head', 0);
                        });
                    }
                    $('#role_config_staff option:selected').data('is-team-head', isTeamHead ? 1 : 0);
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        toastr.error(Object.values(xhr.responseJSON.errors)[0][0], 'Validation Error');
                    } else {
                        toastr.error(xhr.responseJSON?.res || 'Something went wrong.', 'Error');
                    }
                }
            });
        }

        if (isTeamHead) {
            const currentHolder = $('#role_config_staff option').filter(function () {
                return $(this).val() && $(this).val() != adminId && $(this).data('is-team-head') == 1;
            }).first();

            if (currentHolder.length) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: currentHolder.text() + ' is already team member. Do you want to replace ' + currentHolder.text() + ' with ' + selectedName + ' as team member?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, replace',
                    customClass: { confirmButton: 'btn btn-primary me-3', cancelButton: 'btn btn-label-secondary' },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.isConfirmed) {
                        saveRoleConfig();
                    }
                });
                return;
            }
        }

        saveRoleConfig();
    });
    @endif

    @if($canManageFinalApproval)
    $('#final_approval_config_staff').select2({ placeholder: function () { return $(this).data('placeholder') || ''; }, allowClear: true });

    $('#final_approval_config_staff').on('change', function () {
        const adminId = $(this).val();

        if (!adminId) {
            $('#final_approval_config_result').addClass('d-none');
            return;
        }

        const selected = $(this).find('option:selected');
        const isFinalApprover = selected.data('is-final-approver') == 1;

        $('#final_approval_config_toggle').data('admin-id', adminId).prop('checked', isFinalApprover);
        $('#final_approval_config_result').removeClass('d-none');
    });

    // Reflect the actual DB state on page load: preselected option (if any)
    // shows its toggle already ON, nothing selected keeps the panel hidden.
    $('#final_approval_config_staff').trigger('change');

    $('#final_approval_config_save').on('click', function () {
        const checkbox = $('#final_approval_config_toggle');
        const adminId = checkbox.data('admin-id');
        const isFinalApprover = checkbox.is(':checked');
        const selectedName = $('#final_approval_config_staff option:selected').text();

        function saveFinalApprovalConfig() {
            $.ajax({
                url: "{{ route('admin.salary.staff.finalapproval.update') }}",
                method: 'POST',
                data: { _token: "{{ csrf_token() }}", admin_id: adminId, is_final_approver: isFinalApprover ? 1 : 0 },
                success: function (data) {
                    toastr.success(data.res, 'Success', { timeOut: 3000 });
                    if (isFinalApprover) {
                        $('#final_approval_config_staff option').not(':selected').each(function () {
                            $(this).data('is-final-approver', 0);
                        });
                    }
                    $('#final_approval_config_staff option:selected').data('is-final-approver', isFinalApprover ? 1 : 0);
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        toastr.error(Object.values(xhr.responseJSON.errors)[0][0], 'Validation Error');
                    } else {
                        toastr.error(xhr.responseJSON?.res || 'Something went wrong.', 'Error');
                    }
                }
            });
        }

        if (isFinalApprover) {
            const currentHolder = $('#final_approval_config_staff option').filter(function () {
                return $(this).val() && $(this).val() != adminId && $(this).data('is-final-approver') == 1;
            }).first();

            if (currentHolder.length) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: currentHolder.text() + ' already has Final Approval Access. Do you want to replace ' + currentHolder.text() + ' with ' + selectedName + '?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, replace',
                    customClass: { confirmButton: 'btn btn-primary me-3', cancelButton: 'btn btn-label-secondary' },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.isConfirmed) {
                        saveFinalApprovalConfig();
                    }
                });
                return;
            }
        }

        saveFinalApprovalConfig();
    });
    @endif

    let holidaysTable = $('#holidays-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        destroy: true,
        pageLength: 10,
        ajax: { url: "{{ route('admin.holidays.json') }}", type: 'GET' },
        columns: [
            { data: 'date', name: 'holiday_date' },
            { data: 'day', name: 'holiday_date', orderable: false },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'actions', name: 'actions', searchable: false, orderable: false },
        ],
        language: { processing: '<i class="fa fa-spinner fa-spin"></i> Loading...' }
    });

    @if($canManageHolidays)
    $('#addHolidayBtn').on('click', function () {
        $('#holidayForm')[0].reset();
        $('#holiday_id').val('');
        $('#holidayModalLabel').text('Add Holiday');
    });

    $('body').on('click', '.holiday-edit-trigger', function () {
        const id = $(this).data('id');
        $.get("{{ url('admin/holidays/edit') }}/" + id, function (data) {
            $('#holiday_id').val(data.id);
            $('#holiday_date').val(data.holiday_date);
            $('#holiday_name').val(data.name);
            $('#holiday_description').val(data.description);
            $('#holidayModalLabel').text('Edit Holiday');
            $('#holidayModal').modal('show');
        }).fail(function (xhr) { toastr.error(xhr.responseJSON?.res || 'Unable to load holiday.', 'Error'); });
    });

    $('#holidayForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#holiday_id').val();
        const url = id ? "{{ route('admin.holidays.update') }}" : "{{ route('admin.holidays.store') }}";

        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            success: function (data) {
                $('#holidayModal').modal('hide');
                toastr.success(data.res, 'Success', { timeOut: 3000 });
                holidaysTable.ajax.reload(null, false);
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

    $('body').on('click', '.holiday-delete-trigger', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This holiday will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: { confirmButton: 'btn btn-danger me-3', cancelButton: 'btn btn-label-secondary' },
            buttonsStyling: false
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.holidays.delete') }}",
                    method: 'POST',
                    data: { _token: "{{ csrf_token() }}", id: id },
                    success: function (data) {
                        toastr.success(data.res, 'Success', { timeOut: 3000 });
                        holidaysTable.ajax.reload(null, false);
                    },
                    error: function (xhr) { toastr.error(xhr.responseJSON?.res || 'Something went wrong.', 'Error'); }
                });
            }
        });
    });
    @endif

    @if($isSuperAdmin)
    /* -----------------------------------------------------------------
     | Payroll Deduction Calculation - only the field(s) relevant to the
     | selected Deduction Calculation Type are shown AND required; every
     | other field is hidden and its `required` dropped so a value left
     | over from a different type can never block saving. "Deduction Per
     | Slab" relabels to "Fixed Amount" for that type (same underlying
     | field, since Fixed Amount has no slab concept of its own).
     |------------------------------------------------------------------ */
    function toggleDeductionTypeFields() {
        const type = $('#set_deduction_calculation_type').val();

        const showPercentage = type === 'percentage';
        const showSlabAmount = type === 'fixed_slab';
        const showPerSlab = type === 'fixed_slab' || type === 'fixed_amount';

        $('#deduction_percentage_wrapper').toggleClass('d-none', !showPercentage);
        $('#input_late_deduction_percent').prop('required', showPercentage);

        $('#deduction_slab_amount_wrapper').toggleClass('d-none', !showSlabAmount);
        $('#input_deduction_slab_amount').prop('required', showSlabAmount);

        $('#deduction_per_slab_wrapper').toggleClass('d-none', !showPerSlab);
        $('#input_deduction_per_slab').prop('required', showPerSlab);

        $('#deduction_per_slab_label').text(type === 'fixed_amount' ? 'Fixed Amount' : 'Deduction Per Slab');
    }

    toggleDeductionTypeFields();
    $('#set_deduction_calculation_type').on('change', toggleDeductionTypeFields);

    $('#salarySettingsForm').on('submit', function (e) {
        e.preventDefault();
        let data = $(this).serialize();
        data += '&retroactive_late_deduction=' + ($('#set_retroactive').is(':checked') ? 1 : 0);
        data += '&sat_absent_sunday_deduction=' + ($('#set_sat_rule').is(':checked') ? 1 : 0);
        data += '&mon_absent_prev_sunday_deduction=' + ($('#set_mon_rule').is(':checked') ? 1 : 0);

        $.ajax({
            url: "{{ route('admin.salary.settings.update') }}",
            method: 'POST',
            data: data,
            success: function (data) {
                toastr.success(data.res, 'Success', { timeOut: 3000 });
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

    $('#salary_config_staff').select2({ placeholder: function () { return $(this).data('placeholder') || ''; }, allowClear: true });

    $('#salary_config_staff').on('change', function () {
        const adminId = $(this).val();

        if (!adminId) {
            $('#salary_config_result').addClass('d-none');
            return;
        }

        const selected = $(this).find('option:selected');

        $('#salary_config_staff_name').text(selected.text());
        $('#salary_config_input').val(selected.data('monthly-salary'));
        $('#salary_config_save').data('admin-id', adminId);
        $('#salary_config_result').removeClass('d-none');
    });

    $('#salary_config_save').on('click', function () {
        const adminId = $(this).data('admin-id');
        const salary = $('#salary_config_input').val();

        $.ajax({
            url: "{{ route('admin.salary.staff.update') }}",
            method: 'POST',
            data: { _token: "{{ csrf_token() }}", admin_id: adminId, monthly_salary: salary },
            success: function (data) {
                toastr.success(data.res, 'Success', { timeOut: 3000 });
                $('#salary_config_staff option:selected').data('monthly-salary', salary);
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
    @endif
});
</script>
@endsection
