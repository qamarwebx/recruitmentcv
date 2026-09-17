@extends('layout.admin.admin_layout')

@section('title','Mail Setup')

@section('page-style')
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

  @if (Auth::guard('admin')->user()->user_type == 2)
    @if (isset($perm) && $perm->add_mailsetup == 0)
    <style>
      .addmailsetup{
        display: none !important;
      }
    </style>
    @endif
    @if (isset($perm) && $perm->edit_mailsetup == 0)
    <style>
      .edmailsetup{
        display: none !important;
      }
    </style>
    @endif
    @if (isset($perm) && $perm->delete_mailsetup == 0)
    <style>
      .delmailsetup{
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
                <th>Email</th>
                <th>Mail Host</th>
                <th>Mail Port</th>
                <th>Mail Mailer</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
          </table>
        </div>
        <!-- Offcanvas to add new user -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="offcanvas-header">
            <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Mail Setup</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.mail.setup.store') }}" method="POST">
              @csrf
              <div class="row">
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="add-mail-mailer">Mail Mailer <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="add-mail-mailer" placeholder="Enter Mail mailer..." name="mail_mailer"/>
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="add-mail-host">Mail Host <span class="text-danger">*</span></label>
                  <input type="text" id="add-mail-host" class="form-control" placeholder="Enter Mail host..." name="mail_host"/>
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="add-mail-port">Mail Port <span class="text-danger">*</span></label>
                  <input type="text" id="add-mail-port" class="form-control" placeholder="Enter Mail port..." name="mail_port"/>
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="add-mail-username">Mail Username <span class="text-danger">*</span></label>
                  <input type="text" id="add-mail-username" class="form-control" placeholder="Enter Mail username..." name="mail_username"/>
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="add-mail-password">Mail Password <span class="text-danger">*</span></label>
                  <input type="text" name="mail_password" id="add-mail-password" class="form-control" placeholder="Enter Mail password...">
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="add-mail-encryption">Mail Encryption <span class="text-danger">*</span></label>
                  <input type="text" name="mail_encryption" id="add-mail-encryption" placeholder="Enter Mail encryption..." class="form-control">
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="add-mail-from-address">Mail From Address <span class="text-danger">*</span></label>
                  <input type="text" name="mail_from_address" id="add-mail-from-address" placeholder="Enter Mail from address..." class="form-control">
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="add-mail-from-name">Mail From Name <span class="text-danger">*</span></label>
                  <input type="text" name="mail_from_name" id="add-mail-from-name" placeholder="Enter Mail From name..." class="form-control">
                </div>
              </div>
              <button type="submit" class="btn btn-primary btn-sm me-sm-3 me-1 data-submit">Submit</button>
              <button type="reset" class="btn btn-label-secondary btn-sm" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
          </div>
        </div>
        <!-- Offcanvas to add new user -->
        <!-- Offcanvas to edit user -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="offcanvas-header">
            <h5 id="edituserLabel" class="offcanvas-title">Edit Mail Setup</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.mail.setup.update') }}" method="POST">
              @csrf
              <input type="hidden" name="edit_id" id="editid">
              <div class="row">
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="edit-mail-mailer">Mail Mailer <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="edit-mail-mailer" placeholder="Enter Mail mailer..." name="mail_mailer"/>
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="edit-mail-host">Mail Host <span class="text-danger">*</span></label>
                  <input type="text" id="edit-mail-host" class="form-control" placeholder="Enter Mail host..." name="mail_host"/>
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="edit-mail-port">Mail Port <span class="text-danger">*</span></label>
                  <input type="text" id="edit-mail-port" class="form-control" placeholder="Enter Mail port..." name="mail_port"/>
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="edit-mail-username">Mail Username <span class="text-danger">*</span></label>
                  <input type="text" id="edit-mail-username" class="form-control" placeholder="Enter Mail username..." name="mail_username"/>
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="edit-mail-password">Mail Password <span class="text-danger">*</span></label>
                  <input type="text" name="mail_password" id="edit-mail-password" class="form-control" placeholder="Enter Mail password...">
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="edit-mail-encryption">Mail Encryption <span class="text-danger">*</span></label>
                  <input type="text" name="mail_encryption" id="edit-mail-encryption" placeholder="Enter Mail encryption..." class="form-control">
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="edit-mail-from-address">Mail From Address <span class="text-danger">*</span></label>
                  <input type="text" name="mail_from_address" id="edit-mail-from-address" placeholder="Enter Mail from address..." class="form-control">
                </div>
                <div class="mb-3 col-md-6">
                  <label class="form-label" for="edit-mail-from-name">Mail From Name <span class="text-danger">*</span></label>
                  <input type="text" name="mail_from_name" id="edit-mail-from-name" placeholder="Enter Mail From name..." class="form-control">
                </div>
              </div>


              <button type="submit" class="btn btn-primary btn-sm me-sm-3 me-1 data-submit">Submit</button>
              <button type="reset" class="btn btn-label-secondary btn-sm" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
          </div>
        </div>
        <!-- Offcanvas to edit user -->
        <!-- Offcanvas to edit user -->
        <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
          <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel1">Delete Mail Setup</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="{{ route('admin.mail.setup.delete') }}" method="POST">
                  @csrf
                  <input type="hidden" name="proff_ids" id="delStaffID">
                  <div class="modal-body">
                      <div class="row">
                          <div class="col mb-12">
                              <p>Are you sure!, to delete mail setup?</p>
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                      <button type="submit" class="btn btn-danger btn-sm " id="disbtn">Delete</button>
                  </div>
              </form>
          </div>
          </div>
      </div>
      <!-- Delete Staff end -->
      </div>
    </div>

@endsection

@section('page-script')
  <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
  <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
  <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
  <!-- Page JS -->
  <script src="{{ asset('admin/assets/pages/app-mail-setup-list.js') }}"></script>

  <!-- Page Validate Page -->
  <script src="{{ asset('admin/assets/pages/validation/mail-setup-validation.js') }}"></script>

  <script>
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
          url : '{{ url('admin/mail/setup/edit') }}',
          method: "POST",
          type: "html",
          data: {
            "id": editID,
            "_token": "{{ csrf_token() }}",
          },
          success: function(data){
            // console.log(data);
            $('#editid').val(data.id);
            $('#edit-mail-mailer').val(data.mail_mailer);
            $('#edit-mail-host').val(data.mail_host);
            $('#edit-mail-port').val(data.mail_port);
            $('#edit-mail-username').val(data.mail_username);
            $('#edit-mail-password').val(data.mail_password);
            $('#edit-mail-encryption').val(data.mail_encryption);
            $('#edit-mail-from-address').val(data.mail_from_address);
            $('#edit-mail-from-name').val(data.mail_from_name);
          }
        });

      });
    });
  </script>

<script>
  $(document).ready(function(){
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
          url : '{{ url('admin/mail/setup/check/status/del') }}',
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
  });
</script>


@endsection