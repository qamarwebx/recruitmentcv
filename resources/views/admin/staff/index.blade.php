@extends('layout.admin.admin_layout')

@section('title','Staff List')

@section('page-style')
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

  @if (Auth::guard('admin')->user()->user_type == 2)
    @if (isset($perm) && $perm->add_staff == 0)
      <style>
        .addcand{
          display: none !important;

        }

      </style>
    @endif
    @if (isset($perm) && $perm->edit_staff == 0)
      <style>
        .edcand{
          display: none !important;
        }
      </style>
    @endif
    @if (isset($perm) && $perm->delete_staff == 0)
      <style>
        .delcand{
          display: none !important;
        }
      </style>
    @endif
  @endif

@endsection

@section('content')

    <div class="container-fluid flex-grow-1 container-p-y">
      <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                  <span>Session</span>
                  <div class="d-flex align-items-center my-1">
                    <h4 class="mb-0 me-2">21,459</h4>
                    <span class="text-success">(+29%)</span>
                  </div>
                  <span>Total Users</span>
                </div>
                <span class="badge bg-label-primary rounded p-2">
                  <i class="ti ti-user ti-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                  <span>Paid Users</span>
                  <div class="d-flex align-items-center my-1">
                    <h4 class="mb-0 me-2">4,567</h4>
                    <span class="text-success">(+18%)</span>
                  </div>
                  <span>Last week analytics </span>
                </div>
                <span class="badge bg-label-danger rounded p-2">
                  <i class="ti ti-user-plus ti-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                  <span>Active Users</span>
                  <div class="d-flex align-items-center my-1">
                    <h4 class="mb-0 me-2">19,860</h4>
                    <span class="text-danger">(-14%)</span>
                  </div>
                  <span>Last week analytics</span>
                </div>
                <span class="badge bg-label-success rounded p-2">
                  <i class="ti ti-user-check ti-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                  <span>Pending Users</span>
                  <div class="d-flex align-items-center my-1">
                    <h4 class="mb-0 me-2">237</h4>
                    <span class="text-success">(+42%)</span>
                  </div>
                  <span>Last week analytics</span>
                </div>
                <span class="badge bg-label-warning rounded p-2">
                  <i class="ti ti-user-exclamation ti-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Users List Table -->
      <div class="card">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-3">Search Filter</h5>
          <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
            <div class="col-md-4 user_role"></div>
            <div class="col-md-4 user_plan"></div>
            <div class="col-md-4 user_status"></div>
          </div>
        </div>
        <div class="card-datatable table-responsive">
          <table class="datatables-users table border-top">
            <thead>
              <tr>
                <th></th>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Username</th>
                <th>Careoff</th>
                <th>Login</th>
                <th>Actions</th>
              </tr>
            </thead>
          </table>
        </div>
        <!-- Offcanvas to add new user -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
          <div class="offcanvas-header">
            <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Team Member</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.staff.store') }}" method="POST">
              @csrf
              <div class="mb-3">
                <label class="form-label" for="add-user-fullname">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="add-user-fullname" placeholder="Enter name..." name="name"/>
              </div>
              <div class="mb-3">
                <label class="form-label" for="add-user-email">Email <span class="text-danger">*</span></label>
                <input type="text" id="add-user-email" class="form-control" placeholder="Enter email..." name="email"/>
              </div>
              <div class="mb-3">
                <label class="form-label" for="add-user-contact">Contact</label>
                <input type="text" id="add-user-contact" class="form-control" placeholder="Enter phone number..." name="phone"/>
              </div>
              <div class="mb-3">
                <label class="form-label" for="add-user-password">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" id="add-user-password" class="form-control" placeholder="Enter password...">
              </div>
              <div class="mb-3">
                <label class="form-label" for="add-username">Username</label>
                <input type="text" name="username" id="add-username" placeholder="Enter username..." class="form-control">
              </div>
              <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
              <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
          </div>
        </div>
        <!-- Offcanvas to add new user -->
        <!-- Offcanvas to edit user -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel">
          <div class="offcanvas-header">
            <h5 id="edituserLabel" class="offcanvas-title">Edit Team Member</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="edit-new-user pt-0" id="editUserForm" action="{{ route('admin.staff.update') }}" method="POST">
              @csrf
              <div class="mb-3">
                <input type="hidden" name="edit_id" id="editid">
                <label class="form-label" for="edit-user-fullname">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit-user-fullname" placeholder="Enter name..." name="name"/>
              </div>
              <div class="mb-3">
                <label class="form-label" for="add-user-email">Email <span class="text-danger">*</span></label>
                <input type="text" id="edit-user-email" class="form-control" placeholder="Enter email..." name="email"/>
              </div>
              <div class="mb-3">
                <label class="form-label" for="add-user-contact">Contact</label>
                <input type="text" id="edit-user-contact" class="form-control" placeholder="Enter phone number..." name="phone"/>
              </div>
              {{-- <div class="mb-3">
                <label class="form-label" for="add-user-password">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" id="edit-user-password" class="form-control" placeholder="Enter password...">
              </div> --}}
              <div class="mb-3">
                <label class="form-label" for="add-username">Username</label>
                <input type="text" name="username" id="edit-username" placeholder="Enter username..." class="form-control">
              </div>
              <div class="mb-3">
                <label for="edit-work-number" class="form-label">Work Number</label>
                <input type="text" name="work_number" id="edit-work-number" class="form-control" placeholder="Enter Work Number...">
              </div>

              <div class="mb-3">
                <label class="form-label" for="edit-care-no-1">Careoff No.01 (Marketing Call)</label>
                <input type="text" name="care_no_1" id="edit-care-no-1" class="form-control" placeholder="Enter Care No.1">
              </div>

                <div class="mb-3">
                    <label class="form-label" for="edit-care-no-2">Careoff No.02</label>
                    <input type="text" name="care_no_2" id="edit-care-no-2" class="form-control" placeholder="Enter Care No.2">
                </div>
              <!-- 
              <div class="mb-3">
                <label for="edit-chat-url" class="form-label">Whatsapp Chat URL</label>
                <input type="text" name="whatsaapp_chat_url" id="edit-chat-url" class="form-control" placeholder="Enter Whatsapp Chat URL...">
              </div> -->
              <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
              <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
          </div>
        </div>
        <!-- Offcanvas to edit user -->

        <!-- Active Staff Modal -->
        <div class="modal fade" id="updateStatusStaffAct" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Active Careoff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="actStaffID">

                        <p>Are you sure you want to activate this staff?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">
                            Close
                        </button>

                        <button type="button" class="btn btn-success btn-sm" id="btnActiveStaff">
                            Active Careoff
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Deactive Staff Modal -->
        <div class="modal fade" id="updateStatusStaffDeact" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Deactive Careoff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="deactStaffID">

                        <p>Are you sure you want to deactivate this staff?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">
                            Close
                        </button>

                        <button type="button" class="btn btn-danger btn-sm" id="btnDeactiveStaff">
                            Deactive Careoff
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Active Careoff Modal -->
        <div class="modal fade" id="updateCareoffAct" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Active Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="actCareOfID">
                        <p>Are you sure you want to activate?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="btnActiveCareoff">
                            Active Login
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deactive Careoff Modal -->
        <div class="modal fade" id="updateCareoffDeact" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Deactive Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="deactCareOfID">
                        <p>Are you sure you want to deactivate?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="btnDeactiveCareoff">
                            Deactive Login
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Staff to active start -->
        {{-- <div class="modal fade" id="updateStatusStaffAct" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel1">Active Careoff</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.staff.active') }}" method="POST">
                  @csrf
                  <input type="hidden" name="staff_id" id="actStaffID">
                  <div class="modal-body">
                    <div class="row">
                      <div class="col mb-12">
                        <p>Are you sure!, to activate?</p>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-danger btn-sm " id="disbtn">Active Careoff</button>
                  </div>
                </form>
              </div>
            </div>
        </div> --}}
        <!-- Update staff to active end -->

        <!-- Update Staff to active start -->
        {{-- <div class="modal fade" id="updateStatusStaffDeact" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Deactive Careoff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.staff.deactive') }}" method="POST">
                        @csrf
                        <input type="hidden" name="staff_id" id="deactStaffID">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-12">
                                    <p>Are you sure!, to deactivate?</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                            <button type="submit" class="btn btn-danger btn-sm " id="disbtn">Deactive Careoff</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> --}}
        <!-- Update staff to active end -->

        <!-- Update Staff to active start -->
        {{-- <div class="modal fade" id="updateCareoffAct" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel1">Active Login</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.staff.careoff.active') }}" method="POST">
                  @csrf
                  <input type="hidden" name="careoff_id" id="actCareOfID">
                  <div class="modal-body">
                    <div class="row">
                      <div class="col mb-12">
                        <p>Are you sure!, to activate?</p>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-danger btn-sm " id="disbtn">Active Login</button>
                  </div>
                </form>
              </div>
            </div>
        </div> --}}
        <!-- Update staff to active end -->

        <!-- Update Careoff to deactive start -->
        {{-- <div class="modal fade" id="updateCareoffDeact" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Deactive Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.staff.careoff.deactive') }}" method="POST">
                        @csrf
                        <input type="hidden" name="careoff_id" id="deactCareOfID">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-12">
                                    <p>Are you sure!, to deactivate?</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                            <button type="submit" class="btn btn-danger btn-sm " id="disbtn">Deactive Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> --}}
        <!-- Update Careoff to deactive end -->

        <!-- Delete Careoff start -->
        <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Delete Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="{{ route('admin.staff.delete') }}" method="POST" id="deleteStaffForm">
                @csrf
                <input type="hidden" name="staff_id" id="delStaffID">
                <div class="modal-body">
                  <div class="row">
                    <div class="col mb-12">
                      <p>Are you sure!, to delete Staff?</p>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                  <button type="button" class="btn btn-danger btn-sm " id="disbtn">Delete</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- Delete Careoff end -->
      </div>
    </div>

@endsection

@section('page-script')
  <!-- Not delete Self-->

    <script>
      var isCheckID = {{ Auth::guard('admin')->user()->id }};
    </script>

  <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
  <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
  <!-- Page JS -->
  <script src="{{ asset('admin/assets/pages/app-staff-list.js') }}?v={{ file_exists(public_path('admin/assets/pages/app-staff-list.js')) ? filemtime(public_path('admin/assets/pages/app-staff-list.js')) : time() }}"></script>

  <!-- Page Validate Page -->
  <script src="{{ asset('admin/assets/pages/validation/staff-validation-2.js') }}?v={{ file_exists(public_path('admin/assets/pages/validation/staff-validation-2.js')) ? filemtime(public_path('admin/assets/pages/validation/staff-validation-2.js')) : time() }}"></script>

  <script>

    // Activate Staff
    $(document).on('click', '#btnActiveStaff', function () {

        let btn = $(this);
        let id = $('#actStaffID').val();

        $.ajax({
            url: "{{ route('admin.staff.active') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                staff_id: id
            },

            beforeSend: function () {
                btn.prop('disabled', true)
                  .html('<span class="spinner-border spinner-border-sm"></span> Please wait...');
            },

            success: function (response) {

                $('#updateStatusStaffAct').modal('hide');

                // Update badge without reloading DataTable
                $('#staff-status-' + id).html(
                    '<span class="badge bg-label-success text-capitalized" ' +
                    'data-bs-toggle="modal" ' +
                    'data-bs-target="#updateStatusStaffDeact" ' +
                    'data-id="' + id + '">' +
                    'Active' +
                    '</span>'
                );

                toastr.success(response.message);
            },

            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
            },

            complete: function () {
                btn.prop('disabled', false).text('Active Careoff');
            }
        });

    });

    // Deactivate Staff
    $(document).on('click', '#btnDeactiveStaff', function () {

        let btn = $(this);
        let id = $('#deactStaffID').val();

        $.ajax({
            url: "{{ route('admin.staff.deactive') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                staff_id: id
            },

            beforeSend: function () {
                btn.prop('disabled', true)
                  .html('<span class="spinner-border spinner-border-sm"></span> Please wait...');
            },

            success: function (response) {

                $('#updateStatusStaffDeact').modal('hide');

                // Update badge without reloading DataTable
                $('#staff-status-' + id).html(
                    '<span class="badge bg-label-warning text-capitalized" ' +
                    'data-bs-toggle="modal" ' +
                    'data-bs-target="#updateStatusStaffAct" ' +
                    'data-id="' + id + '">' +
                    'Inactive' +
                    '</span>'
                );

                toastr.success(response.message);
            },

            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Something went wrong.');
            },

            complete: function () {
                btn.prop('disabled', false).text('Deactive Careoff');
            }
        });

    });

    // Activate careoff
    $(document).on('click', '#btnActiveCareoff', function () {

      let btn = $(this);
      let careoff_id = $('#actCareOfID').val();

      $.ajax({
          url: "{{ route('admin.staff.careoff.active') }}",
          type: "POST",
          data: {
              _token: "{{ csrf_token() }}",
              careoff_id: careoff_id
          },

          beforeSend: function () {
              btn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm"></span> Please wait...');
          },

          success: function (response) {

              $('#updateCareoffAct').modal('hide');

              // Update login status badge
              $('#login-status-' + careoff_id).html(
                  '<span class="badge bg-label-success text-capitalized" ' +
                  'data-bs-toggle="modal" ' +
                  'data-bs-target="#updateCareoffDeact" ' +
                  'data-id="' + careoff_id + '">' +
                  'Active' +
                  '</span>'
              );

              toastr.success(response.message || 'Login activated successfully.');
          },

          error: function (xhr) {
              toastr.error(xhr.responseJSON?.message ?? 'Something went wrong.');
          },

          complete: function () {
              btn.prop('disabled', false).text('Active Login');
          }
      });

    });

    // Deactivate careoff
    $(document).on('click', '#btnDeactiveCareoff', function () {

      let btn = $(this);
      let careoff_id = $('#deactCareOfID').val();

      $.ajax({
          url: "{{ route('admin.staff.careoff.deactive') }}",
          type: "POST",
          data: {
              _token: "{{ csrf_token() }}",
              careoff_id: careoff_id
          },

          beforeSend: function () {
              btn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm"></span> Please wait...');
          },

          success: function (response) {

              $('#updateCareoffDeact').modal('hide');

              // Update login status badge
              $('#login-status-' + careoff_id).html(
                  '<span class="badge bg-label-warning text-capitalized" ' +
                  'data-bs-toggle="modal" ' +
                  'data-bs-target="#updateCareoffAct" ' +
                  'data-id="' + careoff_id + '">' +
                  'Inactive' +
                  '</span>'
              );

              toastr.success(response.message || 'Login deactivated successfully.');
          },

          error: function (xhr) {
              toastr.error(xhr.responseJSON?.message ?? 'Something went wrong.');
          },

          complete: function () {
              btn.prop('disabled', false).text('Deactive Login');
          }
      });

    });

    $(document).ready(function(){
      $('#edituser').on('show.bs.offcanvas',function(e){
        var editID = $(e.relatedTarget).data('id');
        // console.log(editID);
        $.ajaxSetup({
          headers:{
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
          }
        });

        jQuery.ajax({
          url : '{{ url('admin/staff/edit') }}',
          method: "POST",
          type: "html",
          data: {
            "id": editID,
            "_token": "{{ csrf_token() }}",
          },
          success: function(data){
            // console.log(data);
            $('#editid').val(data.id);
            $('#edit-user-fullname').val(data.name);
            $('#edit-user-email').val(data.email);
            $('#edit-user-contact').val(data.phone);
            $('#edit-username').val(data.username);
            $('#edit-work-number').val(data.work_number);
            $('#edit-care-no-1').val(data.care_no_1);
            $('#edit-care-no-2').val(data.care_no_2);

          }
        });

      });
    });
  </script>

  <script>
    $(document).ready(function(){

        $('#updateStatusStaffDeact').on('show.bs.modal',function(e){
            var staff_id = $(e.relatedTarget).data('id');

            $('#deactStaffID').val(staff_id);
        });

        $('#updateStatusStaffAct').on('show.bs.modal',function(e){
            var staff_id = $(e.relatedTarget).data('id');

            $('#actStaffID').val(staff_id);
        });

        
        $('#updateCareoffAct').on('show.bs.modal',function(e){
            var staff_id = $(e.relatedTarget).data('id');

            $('#actCareOfID').val(staff_id);
        });

        $('#updateCareoffDeact').on('show.bs.modal',function(e){
            var staff_id = $(e.relatedTarget).data('id');

            $('#deactCareOfID').val(staff_id);
        });

      $('#deleteStaff').on('show.bs.modal',function(e){
        var staff_id =  $(e.relatedTarget).data('id');
        // alert(staff_id);
        $('#delStaffID').val(staff_id);
        // check already exist in any table or nor
        $.ajaxSetup({
          headers:{
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
          }
        });

        jQuery.ajax({
          url : '{{ url('admin/staff/check/exist') }}',
          method: "POST",
          type: "html",
          data: {
            "id": staff_id,
            "_token": "{{ csrf_token() }}",
          },
          success: function(data){
            if (data) {
              $('#disbtn').prop('disabled',true);
            } else {
              $('#disbtn').prop('disabled',false);
            }
          }
        });
      });

      var isDeleteStaffSubmitting = false;

      $(document).on('click', '#deleteStaff #disbtn', function (e) {
        e.preventDefault();

        if (isDeleteStaffSubmitting) {
          return;
        }
        isDeleteStaffSubmitting = true;

        var btn = $(this);
        var originalHtml = btn.html();
        var staffId = $('#delStaffID').val();

        $.ajax({
          url: "{{ route('admin.staff.delete') }}",
          method: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            staff_id: staffId
          },
          dataType: 'json',
          timeout: 20000,

          beforeSend: function () {
            btn.prop('disabled', true)
              .html('<span class="spinner-border spinner-border-sm"></span> Deleting...');
          },

          success: function (res) {
            if (res.success) {
              toastr.success(res.message || 'Staff deleted!');

              if (window.dt_user) {
                window.dt_user.ajax.reload(null, false);
              }

              try {
                $('#deleteStaff').modal('hide');
              } catch (e) {}
            } else {
              toastr.error(res.message || 'Unable to delete staff.');
            }
          },

          error: function (xhr, statusText) {
            if (statusText === 'timeout') {
              toastr.error('The request timed out. Please try again.');
            } else {
              toastr.error(xhr.responseJSON?.message || 'Unable to delete staff.');
            }
          },

          complete: function () {
            isDeleteStaffSubmitting = false;
            btn.prop('disabled', false).html(originalHtml);
          }
        });
      });
    });
  </script>



@endsection
