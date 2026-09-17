@extends('layout.admin.admin_layout')

@section('title','Allowed IP')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />

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
        <div class="card">
            <div class="card-header border-bottom">
                <div class="mb-1 float-start">
                    <select id="pagination_list" class="pagestyle">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">150</option>
                        <option value="200">200</option>
                        <option value="250">250</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>
                </div>
                {{-- <div class="mb-1 float-end mx-2">
                    <button class="add-new btn btn-sm btn-primary addcontact addcandidate" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add IP Address</span></button>
                </div> --}}
                <div class="mb-1 float-end">
                    <input type="text" id="search_text" class="form-control" placeholder="Search...">
                </div>
            </div>
            <div class="card-datatable table-responsive contactpaginate">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>IP Address</th>
                            {{-- <th>User Agent</th> --}}
                            <th>Attempt Date</th>
                        </tr>
                    </thead>
                    <tbody class="listitem">
                        @if ($posts->count() > 0)
                            @foreach ($posts as $post)

                                <tr>
                                    <td>{{ $post->email }}</td>
                                    <td>{{ $post->ip_address }}</td>
                                    {{-- <td>{{ $post->user_agent }}</td> --}}
                                    <td>{{ $post->created_at }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10" class="text-center">No Data Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add IP Allowed IP Address Start -->
    <div class="offcanvas offcanvas-size-l offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add IP Address</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form action="{{ route('admin.allowedipaddress.store') }}" id="addUserForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="add-ip-address" class="form-label">IP Address <span class="text-danger">*</span></label>
                            <input type="text" name="ip_address" id="add-ip-address" placeholder="Please enter IP Address" class="form-control" autocomplete="off">
                        </div>
                    </div>
                </div>
                {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button> --}}
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>

            </form>
        </div>
    </div>
    <!-- Add IP Allowed IP Address End -->

    <!-- Add IP Allowed IP Address Start -->
    <div class="offcanvas offcanvas-size-l offcanvas-end" tabindex="-1" id="updateReg" aria-labelledby="updateRegLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 id="updateRegLabel" class="offcanvas-title">Edit IP Address</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form action="{{ route('admin.allowedipaddress.update') }}" id="editUserForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <input type="hidden" name="edit_id" id="editID">
                            <label for="edit-ip-address" class="form-label">IP Address <span class="text-danger">*</span></label>
                            <input type="text" name="ip_address" id="edit-ip-address" placeholder="Please enter IP Address" class="form-control" autocomplete="off">
                        </div>
                    </div>
                </div>
                {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button> --}}
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>

            </form>
        </div>
    </div>
    <!-- Add IP Allowed IP Address End -->

    <!-- Delete Contact Staff Start -->
    <div class="modal fade" id="deletescon" aria-hidden="true" aria-labelledby="deletesconLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="offcanvas-title" id="updateLstageLabel">Delete IP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.allowedipaddress.delete') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="delete_id" id="contactID2">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                               <div class="mb-3">
                                  <p class="text-danger">Are you sure to delete IP?</p>
                               </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                     </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete Contact Staff End -->

    <!-- Inactive Status Start -->

    <!-- Inactive Status End -->

@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/allowed-ip-address-validation.js') }}"></script>


    <script>
        $(document).ready(function(){
            $('#updateReg').on("show.bs.offcanvas",function(e){
                var editID = $(e.relatedTarget).data('id');
                $('#editID').val(editID);

                jQuery.ajax({
                    url : "{{ route('admin.allowedipaddress.edit') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        "id": editID,
                        // "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $("#edit-ip-address").val(data.ip_address);
                    }
                });
            });

            $('#deletescon').on("show.bs.modal",function(e){
                var deleteID = $(e.relatedTarget).data('id');
                $('#contactID2').val(deleteID);
            });

        });
    </script>

@endsection
