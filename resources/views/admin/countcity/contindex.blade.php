@extends('layout.admin.admin_layout')

@section('title','Country')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($perm) && $perm->add_country == 0)
        <style>
            .addcont{
                display: none !important;
            }
        </style>
        @endif
        @if (isset($perm) && $perm->edit_country == 0)
        <style>
            .edcont{
                display: none !important;
            }
        </style>
        @endif
        @if (isset($perm) && $perm->delete_country == 0)
        <style>
            .delcont{
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
                        <th>Name (Arabic)</th>
                        <th>Country Code</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                </table>
            </div>
            <!-- Offcanvas to add new user -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
                <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Country</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.country.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="add-name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add-name" placeholder="Enter name..." name="name"/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-arname">Arabic Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add-arname" placeholder="Enter name..." name="arname"/>
                    </div>
                    <div class="mb-3">
                        <label for="add-cont-code" class="form-label">Country Code <span class="text-danger">*</span></label>
                        <input type="text" name="country_code" id="add-cont-code" class="form-control" placeholder="Enter country code...">
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
                <h5 id="edituserLabel" class="offcanvas-title">Edit Profession</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.country.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="hidden" name="edit_id" id="editid">
                        <label class="form-label" for="edit-name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-name" placeholder="Enter name..." name="name"/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="edit-arname">Arabic Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-arname" placeholder="Enter name..." name="arname"/>
                    </div>
                    <div class="mb-3">
                        <label for="edit-cont-code" class="form-label">Country Code <span class="text-danger">*</span></label>
                        <input type="text" name="country_code" id="edit-cont-code" class="form-control" placeholder="Enter country code...">
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
                </div>
            </div>
            <!-- Offcanvas to edit user -->
            <!-- Delete Staff start -->
            <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete Country</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.country.delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="proff_ids" id="delStaffID">
                    <div class="modal-body">
                        <div class="row">
                        <div class="col mb-12">
                            <p>Are you sure!, to delete country?</p>
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
    <script src="{{ asset('admin/assets/pages/app-country-list.js') }}"></script>

    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/country-validation.js') }}"></script>


    <script>
        $(document).ready(function(){
            $('#edituser').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url("admin/country/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        // console.log(data);
                        $('#editid').val(data.id);
                        $('#edit-name').val(data.name);
                        $('#edit-arname').val(data.arname);
                        $('#edit-cont-code').val(data.country_code);
                        
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
            url : '{{ url('admin/country/check/exist') }}',
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