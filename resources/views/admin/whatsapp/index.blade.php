@extends('layout.admin.admin_layout')

@section('title','Whatsapp API List')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1))
        <style>
            .testapibuttonwhatsapp{
                display: block !important;
            }
            .addbuttonwhatsapp{
                display: block !important;
            }
            .editbuttonwhatsapp{
                display: block !important;
            }
            .deletebuttonwhatsapp{
                display: block !important;
            }
            .staff-select-div{
                display: block !important;
            }
        </style>
    @elseif (Auth::user()->user_type == 2 && (isset($permission) && $permission->full_access == 0))
        @if ($permission->add_whatsapp_api == 1)
            <style>
                .addbuttonwhatsapp{
                    display: block !important;
                }
            </style>
        @else
            <style>
                .addbuttonwhatsapp{
                    display: none !important;
                }
            </style>
        @endif



        @if ($permission->edit_whatsapp_api == 1)
            <style>
                .editbuttonwhatsapp{
                    display: block !important;
                }
            </style>
        @else
            <style>
                .editbuttonwhatsapp{
                    display: none !important;
                }
            </style>
        @endif

        @if ($permission->delete_whatsapp_api == 1)
            <style>
                .deletebuttonwhatsapp{
                    display: block !important;
                }
            </style>
        @else
            <style>
                .deletebuttonwhatsapp{
                    display: none !important;
                }
            </style>
        @endif

        @if ($permission->whatsapp_api_test == 1)
            <style>
                .testapibuttonwhatsapp{
                    display: block !important;
                }
            </style>
        @else
            <style>
                .testapibuttonwhatsapp{
                    display: none !important;
                }
            </style>
        @endif

        @if ($permission->add_whatsapp_api_for_user == 1)
            <style>
                .staff-select-div{
                    display: block !important;
                }
            </style>
        @else
            <style>
                .staff-select-div{
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
                            <th>API Key</th>
                            <th>Access Token</th>
                            <th>URL</th>
                            <th>User Name</th>
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
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.whatsapp.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="add-mobile-number">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add-mobile-number" placeholder="Enter Mobile number..." name="mobile_no"/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-instance-id">Instance ID <span class="text-danger">*</span></label>
                        <input type="text" id="add-instance-id" class="form-control" placeholder="Enter Instance ID..." name="instance_id"/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-access-token">Access Token</label>
                        <input type="text" id="add-access-token" class="form-control" placeholder="Enter Access Token..." name="access_token"/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-api-for">API For <span class="text-danger">*</span></label>
                       <select name="api_for" id="add-api-for" class="form-select select2">
                            <option value="">Select</option>
                            <option value="booking_not">Booking Notification</option>
                            <option value="campaign_not">Campaign Notification</option>
                        </select>
                    </div>
                    <div class="mb-3 staff-select-div" id="staff-select-div">
                        <label class="form-label" for="add-staff-username">Staff <span class="text-danger">*</span></label>
                        <select name="staff_id" id="add-staff-username" class="form-select select2">
                            <option value="">Select</option>
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-api-url">Text, Media Message URL</label>
                        <input type="text" name="api_url" id="add-api-url" placeholder="Enter username..." class="form-control">
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
        <!-- Offcanvas to edit user Start-->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel">
            <div class="offcanvas-header">
              <h5 id="edituserLabel" class="offcanvas-title">Edit Whatsapp API</h5>
              <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
              <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.whatsapp.update') }}" method="POST">
                @csrf
                <input type="hidden" name="edit_id" id="editid">
                <div class="mb-3">
                    <label class="form-label" for="edit-mobile-number">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="edit-mobile-number" placeholder="Enter Mobile number..." name="mobile_no"/>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="edit-instance-id">Instance ID <span class="text-danger">*</span></label>
                    <input type="text" id="edit-instance-id" class="form-control" placeholder="Enter Instance ID..." name="instance_id"/>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="edit-access-token">Access Token</label>
                    <input type="text" id="edit-access-token" class="form-control" placeholder="Enter Access Token..." name="access_token"/>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="edit-api-for">API For <span class="text-danger">*</span></label>
                    <select name="api_for" id="edit-api-for" class="form-select select2">
                        <option value="">Select</option>
                        <option value="booking_not">Booking Notification</option>
                        <option value="campaign_not">Campaign Notification</option>
                    </select>
                </div>
                <div class="mb-3 staff-select-div" id="staff-select-div">
                    <label class="form-label" for="edit-staff-username">Staff <span class="text-danger">*</span></label>
                    <select name="staff_id" id="edit-staff-username" class="form-select select2">
                        <option value="">Select</option>
                        @foreach ($staffs as $staff2)
                            <option value="{{ $staff2->id }}">{{ $staff2->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="edit-api-url">Text, Media Message URL</label>
                    <input type="text" name="api_url" id="edit-api-url" placeholder="Enter username..." class="form-control">
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
                  <form action="{{ route('admin.whatsapp.statusUptd') }}" method="POST">
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
                <form action="{{ route('admin.whatsapp.delete') }}" method="POST">
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
        <!-- Check Whatsapp API Start-->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="whatsapptestApi" aria-labelledby="whatsapptestApiLabel">
            <div class="offcanvas-header">
                <h5 id="whatsapptestApiLabel" class="offcanvas-title">Send Test Message</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="whatsappValidation" action="{{ route('admin.whatsapp.testsend') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-mobile-no">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" name="mobile_no" id="add-mobile-no" class="form-control" placeholder="Enter mobile number...">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-api-id" class="form-label">API <span class="text-danger">*</span></label>
                                <select name="api_id" id="add-api-id" class="form-select select2">
                                    <option value="">Select API</option>
                                    @foreach ($apilists as $apilist)
                                        <option value="{{ $apilist->id }}">{{ $apilist->mobile_no }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-message-text" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea name="message_text" id="add-message-text" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-file-text" class="form-label">File</label>
                                <input type="file" name="photo" id="add-file-text" class="form-control addfiletext">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="useravatar" class="d-block w-px-100 h-px-100 rounded uploadAvatars" id="uploadAvatars">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Check Whatsapp API End-->
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
    <script src="{{ asset('admin/assets/pages/app-whatsapp-api-list.js') }}"></script>
    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/whatsapp-api-validation.js') }}"></script>

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
                url : '{{ url("admin/whatsapp-api/edit") }}',
                method: "POST",
                type: "html",
                data: {
                    "id": editID,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data){
                    // console.log(data);
                    $('#editid').val(data.id);
                    $('#edit-mobile-number').val(data.mobile_no);
                    $('#edit-instance-id').val(data.instance_id);
                    $('#edit-access-token').val(data.access_token);
                    $('#edit-api-for').val(data.api_for).change();
                    $('#edit-staff-username').val(data.staff_id).change();
                    $('#edit-api-url').val(data.api_url);
                    $('#edit-notes').val(data.notes);
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
                    url : '{{ url("admin/whatsapp-api-list/get/data") }}',
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
