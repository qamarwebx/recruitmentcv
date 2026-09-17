@extends('layout.admin.admin_layout')

@section('title','Meta Whatsapp API List')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($perm) && $perm->add_sms_api == 0)
        <style>
            .addsmsapi{
                display: none !important;
            }
        </style>
        @endif
        @if (isset($perm) && $perm->edit_sms_api == 0)
        <style>
        .edtemplate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->delete_sms_api == 0)
        <style>
        .deltemplate{
            display: none !important;
        }
        </style>
        @endif

        @if (isset($perm) && $perm->assign_sms_api == 0)
        <style>
        .assigntoapi{
            display: none !important;
        }
        </style>
        @endif
    
        @if (isset($perm) && $perm->change_status_sms_api == 0)
        <style>
        .change_status_sms_api{
            display: none !important;
        }
        </style>
        @endif

    @endif

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
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
                            <th>Mobile No</th>
                            <th>API Name</th>
                            <th>API Assign To</th>
                            <th>API Base URL</th>
                            <th>Status</th>
                            <th>Assign To</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!--Start Offcanvas to add new user -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add SMS API</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.sms.api.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="add-api-name" class="form-label">API Name <span class="text-danger">*</span></label>
                        <input type="text" name="api_name" id="add-api-name" class="form-control" placeholder="Enter API Name">
                    </div>
                    <div class="mb-3">
                        <label for="add-mobile-no" class="form-label">Mobile No <span class="text-danger">*</span></label>
                        <input type="text" name="mobile_no" id="add-mobile-no" class="form-control" placeholder="Enter Mobile No....">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-base-url-api">Base URL <span class="text-danger">*</span></label>
                        <input type="text" id="add-base-url-api" class="form-control" placeholder="Enter API Base URL..." name="api_base_url"/>
                    </div>
                    <div class="mb-3">
                        <label for="add-notes" class="form-label">Notes</label>
                        <textarea name="notes" id="add-notes" class="form-control" placeholder="Enter notes..." cols="30" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--End Offcanvas to add new user -->

        <!-- API Assignto Start -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="apiassignto" aria-labelledby="apiassigntoLabel">
            <div class="offcanvas-header">
              <h5 id="apiassigntoLabel" class="offcanvas-title">API Assign to</h5>
              <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
              <form class="add-new-user pt-0" id="apiassigntoFormValidation" action="{{ route('admin.sms.api.getassigntoStore') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="editid2">

                <div class="mb-3">
                    <label for="edit-api-assign-to" class="form-label">API For <span class="text-danger">*</span></label>
                    <select name="api_assign_to[]" id="edit-api-assign-to" class="form-control select2" data-placeholder="Select Api Assign To" multiple>
                        <option value="otp">OTP</option>
                        <option value="notification">Notification</option> 
                        <option value="employer_notification">Employer Notification</option>
                        <option value="Allcontact">Allcontact</option>
                        <option value="todo_notification">Todo Notification</option>
                        <option value="leads_employer">Leads Employer</option>
                        <option value="leads_candidate">Leads Candidate</option>
                    </select>
                </div>


                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
              </form>
            </div>
        </div>
        <!-- API Assignto End -->

        <!-- Offcanvas to edit user Start-->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel">
            <div class="offcanvas-header">
              <h5 id="edituserLabel" class="offcanvas-title">Edit Whatsapp API</h5>
              <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
              <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.sms.api.update') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="editid">

                <div class="mb-3">
                    <label for="edit-api-name" class="form-label">API Name <span class="text-danger">*</span></label>
                    <input type="text" name="api_name" id="edit-api-name" class="form-control" placeholder="Enter API Name">
                </div>

                <div class="mb-3">
                    <label for="edit-mobile-no" class="form-label">Mbile No <span class="text-danger">*</span></label>
                    <input type="text" name="mobile_no" id="edit-mobile-no" class="form-control" placeholder="Enter Mobile No....">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="edit-base-url-api">Base URL <span class="text-danger">*</span></label>
                    <input type="text" id="edit-base-url-api" class="form-control" placeholder="Enter API Base URL..." name="api_base_url"/>
                </div>
                <div class="mb-3">
                    <label for="edit-notes" class="form-label">Notes</label>
                    <textarea name="notes" id="edit-notes" class="form-control" placeholder="Enter notes..." cols="30" rows="5"></textarea>
                </div>


                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
              </form>
            </div>
        </div>
        <!-- Offcanvas to edit user End-->
        <!-- Change Status start -->
        <div class="modal fade" id="changeStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Change Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="{{ route('admin.sms.api.statusUptd') }}" method="POST">
                    @csrf
                    <input type="hidden" name="statusID" id="statusID">
                    <div class="modal-body">
                      <div class="row">
                        <div class="col mb-12">
                            <label for="">Change Status</label>
                            <div class="mb-3">
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="status" id="activestatus" value="1"/>
                                    <label class="form-check-label" for="activestatus">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="inactivestatus" value="0"/>
                                    <label class="form-check-label" for="inactivestatus">Inactive</label>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                      <button type="submit" class="btn btn-danger btn-sm ">Save</button>
                    </div>
                  </form>
                </div>
              </div>
        </div>
        <!-- Change Status End -->
        <!-- Delete API Start -->
        <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel1">Delete Whatsapp API</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.sms.api.delete') }}" method="POST">
                  @csrf
                  <input type="hidden" name="delID" id="delID">
                  <div class="modal-body">
                    <div class="row">
                      <div class="col mb-12">
                        <p>Are you sure!, to delete Staff?</p>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                  </div>
                </form>
              </div>
            </div>
        </div>
        <!-- Delete API End -->

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
    <script src="{{ asset('admin/assets/pages/app-sms-api-list.js') }}"></script>
    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/sms-api-validation.js') }}"></script>

    <script>

        $(document).ready(function(){

            var select2 = $('.select2');
            if (select2.length) {
                select2.each(function(){
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        dropdownParent: $this.parent()
                    });
                });
            }

            $('#apiassignto').on('show.bs.offcanvas',function(e){
                var apiID = $(e.relatedTarget).data('id');
                $('#editid2').val(apiID);

                jQuery.ajax({
                    url: "{{ route('admin.sms.api.getassignto') }}",
                    method: "GET",
                    type: "html",
                    data:{
                        "id": apiID
                    },
                    success: function(data){
                        var apiassig = data.api_assign_to;
                        var newassignto = apiassig.split(",");
                        $('#edit-api-assign-to').val(newassignto).change();
                    }
                });
            });

            $('#edituser').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');
                // console.log(editID);
                $('#editid').val(editID);
                $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
                });

                jQuery.ajax({
                    url : '{{ url("admin/sms-api-list/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#edit-api-name').val(data.api_name);
                        $('#edit-mobile-no').val(data.mobile_no);
                        $('#edit-base-url-api').val(data.api_base_url);
                        $('#edit-notes').val(data.notes).change();
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#changeStatus').on('show.bs.modal',function(e){
                var status_id =  $(e.relatedTarget).data('id');
                // alert(status_id);
                $('#statusID').val(status_id);
                // check already exist in any table or nor
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url("admin/sms-api-list/get/data") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": status_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $("input[type='radio'][name='status'][value='"+data.status+"']").prop('checked',true);
                    }
                });
            });
        });
    </script>


    <script>
        $(document).ready(function(){
        $('#deleteStaff').on('show.bs.modal',function(e){
            var delID =  $(e.relatedTarget).data('id');
            // alert(delID);
            $('#delID').val(delID);
            // check already exist in any table or nor

        });
        });
    </script>



@endsection
