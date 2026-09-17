@extends('layout.admin.admin_layout')

@section('title','Expense For List')

@section('page-style')

    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/dropzone/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/editor.css') }}" />

    <style>
        .pagestyle{
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out
        }
        .pagestyle:focus{
            color: #6e6b7b;
            background-color: #fff;
            border-color: #7367f0;
            outline: 0;
            box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1);
        }
    </style>

@endsection

@section('content')

    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Users List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="mb-1 float-start">
                    {{-- <label for="">Show</label> --}}
                    <select id="pagination_list" class="pagestyle">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>
                </div>
                <div class="px-3 float-start">
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel"><i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter</button>
                </div>
                <div class="mb-1 float-end">

                    {{-- <div class="btn-group mx-2">
                        <button class="btn btn-primary btn-sm dropdown-toggle bulkactions" type="button" disabled id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Bulk Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfergroup"><i class="ti ti-users me-2"></i> Transfer To Group</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfercareoff"><i class="ti ti-user me-2"></i> Transfer To Careoff</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransferleadowner"><i class="ti ti-user me-2"></i> Transfer To Lead Owner</a>
                            <a class="dropdown-item bulcontactwhpsend" href="#" data-bs-toggle="offcanvas" data-bs-target="#bulkwhatsappsend"><i class="ti ti-brand-whatsapp me-2"></i> Send Whatsapp</a>
                            <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkdelete"><i class="ti ti-trash me-2"></i> Delete</a>
                        </div>
                    </div> --}}

                    <button class="add-new btn btn-sm btn-primary addcontact addcandidate mx-2" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Expense For</span></button>
                </div>

                <div class="mb-1 float-end">
                    <input type="text" id="search_text" class="form-control" placeholder="Search...">
                </div>
            </div>
            <div class="card-datatable table-responsive contactpaginate">
                @include('admin.expensefor.indexload')
            </div>
        </div>

        <!-- Add Expense Start -->
        <div class="offcanvas offcanvas-size-md offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Expensefor</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form action="{{ route('admin.expense.expensefor.store') }}" id="addUserForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">

                         <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="add-name" placeholder="Please enter expensefor" class="form-control" autocomplete="off">
                            </div>
                        </div>

                    </div>
                    {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button> --}}
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>

                </form>
            </div>
        </div>
        <!-- Add Expense End -->

        <!-- Add Expense Start -->
        <div class="offcanvas offcanvas-size-md offcanvas-end" tabindex="-1" id="updateReg" aria-labelledby="updateRegLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="updateRegLabel" class="offcanvas-title">Edit Expense Category</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form action="{{ route('admin.expense.expensefor.update') }}" id="addUserForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <input type="hidden" name="edit_id" id="edit_ID">
                         <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit-name" placeholder="Please enter category" class="form-control" autocomplete="off">
                            </div>
                        </div>

                    </div>
                    {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button> --}}
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>

                </form>
            </div>
        </div>
        <!-- Add Expense End -->

        <!-- Delete Expense Category Start -->
        <div class="modal fade" id="deletescon" aria-hidden="true" aria-labelledby="deletesconLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.expense.expensefor.delete') }}" id="deleteCatForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="cat_id" id="catID2">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                   <div class="mb-3">
                                      <p class="text-danger" id="response-text"></p>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            {{-- <button type="button" class="btn btn-danger btn-sm deletebtn" id="disbtn">Delete</button> --}}
                            <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Expense Category End -->
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
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script>

    <script src="{{ asset('admin/assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/quill.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/expense-for-validation.js') }}"></script>

    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-file-upload.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('#updateReg').on("show.bs.offcanvas",function(e){
                var editID = $(e.relatedTarget).data('id');
                $('#edit_ID').val(editID);


                jQuery.ajax({
                    url : '{{ route("admin.expense.expensefor.edit") }}',
                    method: "GET",
                    type: "html",
                    data: {
                        "id": editID,
                        // "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#edit-name').val(data.name);

                    }
                });
            });

            $('#deletescon').on("show.bs.modal",function(e){
                var delete_id = $(e.relatedTarget).data('id');
                $('#catID2').val(delete_id);

                $.ajax({
                    url: "{{ route('admin.expense.expensefor.getdatafordelet') }}",
                    method: "GET",
                    type: "html",
                    data:{
                        "id": delete_id
                    },
                    success: function(data){
                        console.log(data);

                        if (data.status == 1) {
                            $('#response-text').text(data.message);
                        }else{
                            $('#response-text').text("Are you sure to delete?");
                        }

                    }
                });

            });
        });
    </script>

@endsection
