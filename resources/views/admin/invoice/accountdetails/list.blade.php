@extends('layout.admin.admin_layout')

@section('title')

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
                            <th>Bank Name</th>
                            <th>Account Number</th>
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
            <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Bank Details</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.account_details.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="add-name" class="form-control" placeholder="Enter Name...">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-bank-name" class="form-label">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="bank_name" id="add-bank-name" placeholder="Enter Bank Name...">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-account-number" class="form-label">Account Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="account_number" id="add-account-number" placeholder="Enter Account Number...">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-iban-account-number" class="form-label">IBAN Account Number</label>
                            <input type="text" class="form-control" name="iban_account_number" id="add-iban-account-number" placeholder="Enter IBAN Account Number...">
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
            <h5 id="edituserLabel" class="offcanvas-title">Edit Bank Details</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.account_details.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-name" class="form-control" placeholder="Enter Name...">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit-bank-name" class="form-label">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="bank_name" id="edit-bank-name" placeholder="Enter Bank Name...">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit-account-number" class="form-label">Account Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="account_number" id="edit-account-number" placeholder="Enter Account Number...">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit-iban-account-number" class="form-label">IBAN Account Number</label>
                            <input type="text" class="form-control" name="iban_account_number" id="edit-iban-account-number" placeholder="Enter IBAN Account Number...">
                        </div>
                    </div>

                </div>
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>
    <!--- Edit Candidate End --->

    <!-- Delete Staff Modal -->
    <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Delete Account Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.account_details.delete') }}" method="POST">
                    @csrf

                    <input type="hidden" name="id" id="delete_id">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <p>Are you sure you want to delete this record?</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">
                            Close
                        </button>

                        <button type="submit" class="btn btn-danger btn-sm" id="disbtn">
                            Delete
                        </button>
                    </div>

                </form>

            </div>
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
    {{-- <script src="{{ asset('admin/assets/pages/app-businesstype-list.js') }}"></script> --}}

    <script src="{{ asset('admin/assets/pages/app-account-details-list.js') }}"></script>

    <!-- Page Validate Page -->
    {{-- <script src="{{ asset('admin/assets/pages/validation/business-type-validation.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/pages/validation/account-details-validation.js') }}"></script>


    <script>
        $(document).ready(function(){
            $('#edituser').on('show.bs.offcanvas',function(e){
                var id = $(e.relatedTarget).data('id');
                $('#edit_id').val(id);
                jQuery.ajax({
                    url : "{{ route('admin.account_details.edit') }}",
                    method: "GET",
                    type: "html",
                    data:{
                        id: id
                    },
                    success: function(data){
                        $('#edit-name').val(data.name);
                        $('#edit-bank-name').val(data.bank_name);
                        $('#edit-account-number').val(data.account_number);
                        $('#edit-iban-account-number').val(data.iban_account_number);
                    }
                });

            });

             $('#deleteStaff').on('show.bs.modal', function (e) {

                var id = $(e.relatedTarget).data('id');

                $('#delete_id').val(id);

            });
        });
    </script>

@endsection
