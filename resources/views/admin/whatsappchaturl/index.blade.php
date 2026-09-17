@php
    $admin = Auth::guard('admin')->user();

    $canGenerateChatUrl =
        $admin->user_type == 1 ||
        (isset($permission) && (
            $permission->full_access == 1 ||
            $permission->add_whatsapp_url == 1
    ));

    $canEditWhatsappUrl =
        $admin->user_type == 1 ||
        (isset($permission) && (
            $permission->full_access == 1 ||
            $permission->edit_whatsapp_url == 1
        ));

    $canDeleteWhatsappUrl =
        $admin->user_type == 1 ||
        (isset($permission) && (
            $permission->full_access == 1 ||
            $permission->delete_whatsapp_url == 1
        ));

@endphp



@extends('layout.admin.admin_layout')

@section('title','Whatsaapp Chat URL')

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
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header py-3 px-4">
                <div class="float-start">
                    <select id="pagination_list" class="form-select form-select-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>
                </div>
               

                    @if ($canGenerateChatUrl)
                        <div class="float-end">
                            <button
                                class="add-new btn btn-sm btn-primary mx-1"
                                data-bs-toggle="modal"
                                data-bs-target="#offcanvasAddUser"
                            >
                                <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                                <span class="d-none d-sm-inline-block">Generate Chat URL</span>
                            </button>
                        </div>
                    @endif

                <div class="float-end">
                    <input type="text" id="search_text" class="form-control form-control-sm" placeholder="Search...">
                </div>
            </div>
            <!-- Invoice Card List End -->
            <div class="card-datatable table-responsive invoicepaginate">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th>Whatsapp Chat URL</th>
                            <th>Staff</th>
                            <th>Whatsapp Number</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="listitem">
                        @if ($posts->count() > 0)
                            @foreach ($posts as $post)
                                <tr>
                                    <td>{{ $post->chat_url }}</td>
                                    <td>{{ $post->staff->name }}</td>
                                    <td>{{ $post->staff->work_number }}</td>
                                    <td>@if($post->status == '1') {{ 'Active' }} @else {{ 'Inactive' }} @endif</td>
                                    <td>
                                        @if ($canEditWhatsappUrl)
                                        <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#editchaturl" data-id="{{ $post->id }}"><i class="ti ti-edit me-0 me-sm-1 ti-sm"></i></a>
                                        @endif
                                        @if ($canDeleteWhatsappUrl)
                                        <a href="#" class="text-danger" data-bs-toggle="modal" data-bs-target="#deletechaturl" data-id="{{ $post->id }}"><i class="ti ti-trash me-0 me-sm-1 ti-sm"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">No Data Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center px-4 pt-3">
                    @if (count($posts) > 0)
                        <div>

                            {{ __('Showing :start to :end of :total Entries', [
                                'start' => $posts->firstItem(),
                                'end' => $posts->lastItem(),
                                'total' => $posts->total(),
                            ]) }}

                        </div>
                        <div>
                            {{ $posts->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    @else
                        <div>

                            {{ __('Showing :start to :end of :total Entries', [
                                'start' => "0",
                                'end' => "0",
                                'total' => "0",
                            ]) }}

                        </div>

                    @endif

                </div>
            </div>
        </div>

        {{-- Generate Chat URL Start --}}
        <div class="modal fade" id="offcanvasAddUser" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Whatsapp Chat URL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.whatsapp.chatredirecturlStore') }}" id="chaturlValidation" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="add-generate-staff-url" class="form-label">Staff <span class="text-danger">*</span></label>
                                    <select name="staff_id" id="add-generate-staff-url" class="form-select select2" data-placeholder="Select Staff" data-allow-clear="true">
                                        <option value="">Select Staff</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="add-working-url" class="form-label">Working Mobile Number <span class="text-danger">*</span></label>
                                    <input type="number" name="working_mobile_no" id="add-working-url" class="form-control" placeholder="Enter Working Mobile Number...">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="add-calling-number" class="form-label">Calling Number <span class="text-danger">*</span></label>
                                    <input type="number" name="calling_number" id="add-calling-number" class="form-control" placeholder="Enter Calling Mobile Number...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-success btn-sm ">Generate URL</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        {{-- Generate Chat URL End --}}


        {{-- Edit Chat URL Start --}}
        <div class="modal fade" id="editchaturl" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Whatsapp Chat URL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.whatsapp.chatredirecturlUpdate') }}" id="chaturlValidation2" method="POST">
                    @csrf
                    <input type="hidden" name="edit_id" id="editchat_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit-generate-staff-url" class="form-label">Staff <span class="text-danger">*</span></label>
                                    <select name="staff_id" id="edit-generate-staff-url" class="form-select select2" data-placeholder="Select Staff" data-allow-clear="true">
                                        <option value="">Select Staff</option>
                                        @foreach ($staffs as $staff)
                                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit-working-url" class="form-label">Working Mobile Number <span class="text-danger">*</span></label>
                                    <input type="number" name="working_mobile_no" id="edit-working-url" class="form-control" placeholder="Enter Working Mobile Number...">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit-calling-number" class="form-label">Calling Number <span class="text-danger">*</span></label>
                                    <input type="number" name="calling_number" id="edit-calling-number" class="form-control" placeholder="Enter Calling Mobile Number...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-success btn-sm ">Generate URL</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        {{-- Edit Chat URL End --}}


        {{-- Delete Chat URL Start --}}
        <div class="modal fade" id="deletechaturl" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete Whatsapp Chat URL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.whatsapp.chatredirecturlDelete') }}" id="" method="POST">
                    @csrf
                    <input type="hidden" name="edit_id" id="deletechat_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <p class="text-danger">Are you sure to delete?</p>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-success btn-sm ">Delete</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        {{-- Delete Chat URL End --}}



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


    <!-- Page JS -->
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/whatsapp-chaturl-validation.js') }}"></script>

    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-file-upload.js') }}"></script>

    <script>
        var select2 = $('.select2');
        if (select2.length) {
            select2.each(function () {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    // placeholder: 'Select value',
                    dropdownParent: $this.parent()
                });
            });
        }

    </script>

    <script>
        $(document).ready(function(){
            $('#add-generate-staff-url').on('change',function(){
                var staff_id = $(this).val();

                if (staff_id != '') {
                    // Get Number from Staff ID
                    $.ajax({
                        url: "{{ route('admin.whatsapp.getstaffdet') }}",
                        method: "GET",
                        type: "html",
                        data: {
                            id: staff_id
                        },
                        success: function(data){
                            // console.log(data);

                            if (data.work_number != null) {
                                $('#add-working-url').val(data.work_number);
                            }else{
                                $('#add-working-url').val('');
                            }

                            if (data.calling_number != null) {
                                $('#add-calling-number').val(data.calling_number);
                            }else{
                                $('#add-calling-number').val('');
                            }

                        }
                    });
                } else {
                    $('#add-working-url').val('');
                }

            });

            $('#editchaturl').on("show.bs.modal",function(e){
                var edit_id = $(e.relatedTarget).data('id');

                $('#editchat_id').val(edit_id);

                $.ajax({
                    url: "{{ route('admin.whatsapp.editchatredirecturl') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        id: edit_id
                    },
                    success: function(data){
                        $('#edit-generate-staff-url').val(data.staff_id).trigger('change');
                    }
                });

            });

            $('#deletechaturl').on("show.bs.modal",function(e){
                var edit_id = $(e.relatedTarget).data('id');

                $('#deletechat_id').val(edit_id);

            });

            $('#edit-generate-staff-url').on('change',function(){
                var staff_id = $(this).val();

                if (staff_id != '') {
                    // Get Number from Staff ID
                    $.ajax({
                        url: "{{ route('admin.whatsapp.getstaffdet') }}",
                        method: "GET",
                        type: "html",
                        data: {
                            id: staff_id
                        },
                        success: function(data){
                            // console.log(data);

                            if (data.work_number != null) {
                                $('#edit-working-url').val(data.work_number);
                            }else{
                                $('#edit-working-url').val('');
                            }

                            if (data.calling_number != null) {
                                $('#edit-calling-number').val(data.calling_number);
                            }else{
                                $('#edit-calling-number').val('');
                            }

                        }
                    });
                } else {
                    $('#edit-working-url').val('');
                }

            });

        });
    </script>

@endsection
