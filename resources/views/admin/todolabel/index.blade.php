@extends('layout.admin.admin_layout')

@section('title','Todo Label')

@section('page-style')

    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/pickr/pickr-themes.css') }}" />

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Search Filter</h5>
                <div class="row">
                    <div class="col-md-3 mb-3 country-div">
                        <select name="" id="by-country" class="form-select select22">
                            <option value="">Select Country</option>

                        </select>
                    </div>
                    <div class="col-md-3 mb-3 city-div">
                        <select name="" id="by-city" class="form-select select22">
                            <option value="">Select City</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 status-div">
                        <select name="" id="by-status" class="form-select select22">
                            <option value="">Select Status</option>

                        </select>
                    </div>
                    <div class="col-md-3 mb-3 created-date-div">
                        <input type="text" name="" id="by-created-date" class="form-control createdate-picker" placeholder="Booking date...">
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!--- Add Candidate Start --->
    <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add New Todo Label</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.todolabel.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="add-name" class="form-control" placeholder="Enter Name...">
                        </div>
                    </div>

                </div>
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>
    <!--- Add Candidate End --->

    <!--- Edit Candidate Start --->
    <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 id="edituserLabel" class="offcanvas-title">Edit Todo Label</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.todolabel.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-name" class="form-control" placeholder="Enter Name...">
                        </div>
                    </div>

                </div>
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>
    <!--- Edit Candidate End --->

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
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/pickr/pickr.js') }}"></script>

    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('admin/assets/pages/app-todo-label-list.js') }}"></script>

    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/todo-label-validation.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('#edituser').on('show.bs.offcanvas',function(e){
                var id = $(e.relatedTarget).data('id');
                $('#edit_id').val(id);
                jQuery.ajax({
                    url : "{{ route('admin.todolabel.edit') }}",
                    method: "GET",
                    type: "html",
                    data:{
                        id: id
                    },
                    success: function(data){
                        $('#edit-name').val(data.name);
                    }
                });

            });
        });
    </script>

@endsection
