@extends('layout.admin.admin_layout')

@section('title','Flight Vendor List')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- Flight Vendor List Start -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div class="mb-1 float-start">
                            {{-- <label for="">Show</label> --}}
                            <select id="pagination_list" class="pagestyle form-select">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
                                <option value="1000">1000</option>
                            </select>
                        </div>
                        <div class="mb-1 float-end">
                            <button class="add-new mx-2 btn btn-sm btn-primary addcontact addcandidate" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Flight Vendor</span></button>
                        </div>
                        <div class="mb-1 float-end">
                            <input type="text" id="search_text" class="form-control" placeholder="Search...">
                        </div>
                    </div>
                    <div class="card-datatable table-responsive contactpaginate">
                        @include('admin.flight.vendor.load')
                    </div>
                </div>
            </div>
        </div>
        <!-- Flight Vendor List End -->


        <!-- Add New Flight Ticket Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Flight Vendor</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.flight.vendor_store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="add-vendor-name" class="form-label">Vendor Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="add-vendor-name" class="form-control" placeholder="Enter Vendor Name...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-contact-no" class="form-label">Contact Number</label>
                                <input type="text" name="contact_no" id="add-contact-no" class="form-control" placeholder="Enter Contact No...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-email" class="form-label">Email</label>
                                <input type="text" name="email" id="add-email" class="form-control" placeholder="Enter Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-website" class="form-label">Website</label>
                                <input type="text" name="website" id="add-website" class="form-control" placeholder="Enter Website...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="addnewcandidateBtn">Submit</button> --}}
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Add New Flight Ticket End -->

        <!-- Add New Flight Ticket Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasEditUser" aria-labelledby="offcanvasEditUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasEditUserLabel" class="offcanvas-title">Add Flight Vendor</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.flight.vendor_update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="edit_id" id="editID">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label for="edit-vendor-name" class="form-label">Vendor Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="edit-vendor-name" class="form-control" placeholder="Enter Vendor Name...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact-no" class="form-label">Contact Number</label>
                                <input type="text" name="contact_no" id="edit-contact-no" class="form-control" placeholder="Enter Contact No...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-email" class="form-label">Email</label>
                                <input type="text" name="email" id="edit-email" class="form-control" placeholder="Enter Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-website" class="form-label">Website</label>
                                <input type="text" name="website" id="edit-website" class="form-control" placeholder="Enter Website...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="addnewcandidateBtn">Submit</button> --}}
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Add New Flight Ticket End -->



    </div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/validation/flight-vendor-validation.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('#offcanvasEditUser').on('show.bs.offcanvas',function(e){
                var edit_id = $(e.relatedTarget).data('id');
                $('#editID').val(edit_id);

                $.ajax({
                    url: "{{ route('admin.flight.vendor_edit') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        id: edit_id
                    },
                    success: function(data){
                        $('#edit-vendor-name').val(data.name);
                        $('#edit-contact-no').val(data.contact_no);
                        $('#edit-email').val(data.email);
                        $('#edit-website').val(data.website);
                    }
                });
            });



        });
    </script>

@endsection

