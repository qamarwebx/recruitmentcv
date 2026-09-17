@extends('layout.admin.admin_layout')

@section('title','Meta Whatsapp API List')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
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
                            <th>Access Token</th>
                            <th>Vendor UID</th>
                            <th>API Base URL</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!--Start Offcanvas to add new user -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Whatsapp API</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.metawhatsapp.api.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="add-api-name" class="form-label">API Name <span class="text-danger">*</span></label>
                        <input type="text" name="api_name" id="add-api-name" class="form-control" placeholder="Enter API Name">
                    </div>
                    <div class="mb-3">
                        <label for="add-mobile-no" class="form-label">Mobile No <span class="text-danger">*</span></label>
                        <input type="text" name="mobile_no" id="add-mobile-no" class="form-control mobile-check"  data-mode="add" placeholder="Enter Mobile No....">
                        <small class="mobile-feedback"></small>
                    </div>
                    <!-- <div class="mb-3">
                        <label for="add-mobile-no" class="form-label">Mobile No <span class="text-danger">*</span></label>
                        <input type="text" name="mobile_no" id="add-mobile-no" class="form-control" placeholder="Enter Mobile No....">
                    </div> -->
                    <div class="mb-3">
                        <label class="form-label" for="add-api-access-token">Access Token <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add-api-access-token" placeholder="Enter API Access Token..." name="api_access_token"/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-vendor-uid">Vendor UID <span class="text-danger">*</span></label>
                        <input type="text" id="add-vendor-uid" class="form-control" placeholder="Enter Vendor UID..." name="vendor_uid"/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-base-url-api">Base URL <span class="text-danger">*</span></label>
                        <input type="text" id="add-base-url-api" class="form-control" placeholder="Enter API Base URL..." name="api_base_url"/>
                    </div>
                    <div class="mb-3">
                        <label for="add-notes" class="form-label">Notes</label>
                        <textarea name="notes" id="add-notes" class="form-control" placeholder="Enter notes..." cols="30" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm me-sm-3 me-1 data-submit" disabled>Submit</button>
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
              <form class="add-new-user pt-0" id="apiassigntoFormValidation" action="{{ route('admin.metawhatsapp.api.getassigntoStore') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="editid2">

                <div class="mb-3">
                    <label for="edit-api-assign-to" class="form-label">API For <span class="text-danger">*</span></label>
                    <select name="api_assign_to[]" id="edit-api-assign-to" class="form-control select2" data-placeholder="Select Api Assign To" multiple>

                        <option value="otp">OTP</option>
                        {{-- <option value="campaign">Campaign</option> --}}
                        <option value="notification">Notification</option> 
                        {{-- <option value="house_driver_lead">House Driver Lead</option> --}}
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
              <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.metawhatsapp.api.update') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="editid">

                <div class="mb-3">
                    <label for="edit-api-name" class="form-label">API Name <span class="text-danger">*</span></label>
                    <input type="text" name="api_name" id="edit-api-name" class="form-control" placeholder="Enter API Name">
                </div>

                <!-- <div class="mb-3">
                    <label for="edit-mobile-no" class="form-label">Mbile No <span class="text-danger">*</span></label>
                    <input type="text" name="mobile_no" id="edit-mobile-no" class="form-control" placeholder="Enter Mobile No....">
                </div> -->
                <div class="mb-3">
                    <label for="edit-mobile-no" class="form-label"> Mobile No <span class="text-danger">*</span></label>
                    <input type="text" name="mobile_no" id="edit-mobile-no" class="form-control mobile-check" data-mode="edit" data-id="" placeholder="Enter Mobile No....">
                    <small class="mobile-feedback"></small>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="edit-api-access-token">Access Token <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit-api-access-token" placeholder="Enter API Access Token..." name="api_access_token"/>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="edit-vendor-uid">Vendor UID <span class="text-danger">*</span></label>
                    <input type="text" id="edit-vendor-uid" class="form-control" placeholder="Enter Vendor UID..." name="vendor_uid"/>
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
                  <form action="{{ route('admin.metawhatsapp.api.statusUptd') }}" method="POST">
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
                <form action="{{ route('admin.metawhatsapp.api.delete') }}" method="POST">
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
    <script src="{{ asset('admin/assets/pages/app-meta-whatsapp-api-list.js') }}"></script>
    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/meta-whatsapp-api-validation.js') }}"></script>

    <script>
        let mobileTimer;

        $(document).on('keyup', '.mobile-check', function () {

            clearTimeout(mobileTimer);

            let $input   = $(this);
            let mobileNo = $input.val().trim();
            let mode     = $input.data('mode');   // add | edit
            let recordId = $input.data('id') ?? '';
            let $form    = $input.closest('form');
            let $btn     = $form.find('.data-submit');
            let $feedback= $input.closest('.mb-3').find('.mobile-feedback');

            $feedback.text('');
            $btn.prop('disabled', true);

            // basic validation
            if (!/^[0-9]{8,15}$/.test(mobileNo)) {
                $feedback.text('Enter valid mobile number').css('color','red');
                return;
            }

            mobileTimer = setTimeout(function () {

                $.ajax({
                    url: '{{ route("admin.metawhatsapp.api.check-mobile") }}',
                    type: 'POST',
                    data: {
                        mobile_no: mobileNo,
                        id: recordId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        if (res.exists) {
                            $feedback
                                .text('Mobile number already exists')
                                .css('color', 'red');

                            $btn.prop('disabled', true);
                        } else {
                            $feedback
                                .text('Mobile number is available')
                                .css('color', 'green');

                            $btn.prop('disabled', false);
                        }
                    }
                });

            }, 500);
        });
    </script>

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
                    url: "{{ route('admin.metawhatsapp.api.getassignto') }}",
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
                    url : '{{ url("admin/whatsapp-api-plus-list/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        // console.log(data);
                        $('#edit-api-name').val(data.api_name);
                        $('#edit-mobile-no').val(data.mobile_no).attr('data-id', data.id);
                        // $('#edit-mobile-no').val(data.mobile_no);
                        $('#edit-api-access-token').val(data.api_access_token);
                        $('#edit-vendor-uid').val(data.vendor_uid);
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
                    url : '{{ url("admin/whatsapp-api-plus-list/get/data") }}',
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
