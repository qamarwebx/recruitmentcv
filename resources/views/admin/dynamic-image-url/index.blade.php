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

@section('title','Dynamic Image URL')

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
                            <span class="d-none d-sm-inline-block">Add Image URL</span>
                        </button>
                    </div>
                @endif

                <div class="float-end">
                    <input type="text" id="search_text" class="form-control form-control-sm" placeholder="Search...">
                </div>
            </div>

            <div class="card-datatable table-responsive invoicepaginate">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>URL</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="listitem">
                        @if ($posts->count() > 0)
                            @foreach ($posts as $post)
                                <tr>

                                    <!-- Name -->
                                    <td>{{ $post->staff->name }}</td>

                                    <!-- URL -->
                                    <td>{{ $post->url }}</td>

                                    <!-- Actions -->
                                    <td>
                                            <a href="#" class="text-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="{{ $post->id }}">
                                                <i class="ti ti-edit"></i>
                                            </a>

                                            <a href="#"
                                                class="text-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="{{ $post->id }}">

                                                <i class="ti ti-trash"></i>
                                            </a>
                                    </td>

                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">No Data Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center px-4 pt-3">
                    @if ($posts->count() > 0)
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
                            {{ __('Showing 0 to 0 of 0 Entries') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{--Add Image URL Start --}}
        <div class="modal fade" id="offcanvasAddUser" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Image URL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.dynamic-image-url.store') }}" id="chaturlValidation" method="POST" enctype="multipart/form-data">
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
                                    <label for="add-image-url" class="form-label">Image Url <span class="text-danger">*</span></label>
                                    <input type="text" name="image_url" id="add-image-url" class="form-control" placeholder="Enter Image URL...">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-success btn-sm ">Add Image URL</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        {{--Add Image URL End --}}


        {{-- Edit Image URL Start --}}
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Edit Image URL</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.dynamic-image-url.update') }}" id="chaturlValidation2" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="edit_id" id="editchat_id">

                        <div class="modal-body">
                            <div class="row">

                                {{-- Staff --}}
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="edit-generate-staff-url" class="form-label">
                                            Staff <span class="text-danger">*</span>
                                        </label>

                                        <select name="staff_id" id="edit-generate-staff-url"
                                            class="form-select select2"
                                            data-placeholder="Select Staff"
                                            data-allow-clear="true">

                                            <option value="">Select Staff</option>

                                            @foreach ($staffs as $staff)
                                                <option value="{{ $staff->id }}">
                                                    {{ $staff->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="edit-image-url" class="form-label">Image Url <span class="text-danger">*</span></label>
                                        <input type="text" name="image_url" id="edit-image-url" class="form-control" placeholder="Enter Image URL...">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">
                                Close
                            </button>

                            <button type="submit" class="btn btn-success btn-sm">
                                Update Image URL
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        {{-- Edit Image URL End --}}


        {{-- Delete Image URL Start --}}
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">

            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Delete Image URL</h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.dynamic-image-url.delete') }}"
                        method="POST">

                        @csrf

                        <input type="hidden"
                            name="delete_id"
                            id="deletechat_id">

                        <div class="modal-body">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <p class="text-danger mb-0">
                                            Are you sure you want to delete this Image URL?
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">

                            <button type="button"
                                class="btn btn-label-secondary btn-sm"
                                data-bs-dismiss="modal">
                                Close
                            </button>

                            <button type="submit"
                                class="btn btn-danger btn-sm">
                                Delete
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </div>
        {{-- Delete Page End --}}



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

                            var image_url = '';
                            
                            if(data.base_path != '' && data.profile != '' && data.base_path != null && data.profile != null){
                                image_url = data.base_path +'/'+data.profile;
                            }
                            
                            $('#add-image-url').val(image_url);
                        }
                    });
                } else {
                    $('#add-working-url').val('');
                }

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

                            var image_url = '';
                            
                            if(data.base_path != '' && data.profile != '' && data.base_path != null && data.profile != null){
                                image_url = data.base_path +'/'+data.profile;
                            }
                            
                            $('#edit-image-url').val(image_url);
                        }
                    });
                } else {
                    $('#edit-working-url').val('');
                }

            });

            $('#editModal').on("show.bs.modal", function (e) {

                var edit_id = $(e.relatedTarget).data('id');

                $('#editchat_id').val(edit_id);

                $.ajax({
                    url: "{{ route('admin.dynamic-image-url.edit') }}",
                    method: "GET",
                    data: {
                        id: edit_id
                    },

                    success: function (data) {

                        // Staff
                        $('#edit-generate-staff-url')
                            .val(data.staff_id)
                            .trigger('change');

                        // Whatsapp Number
                        $('#edit-image-url').val(data.url);

                    }
                });

            });

            $('#deleteModal').on("show.bs.modal", function (e) {

                var delete_id = $(e.relatedTarget).data('id');

                $('#deletechat_id').val(delete_id);

            });


        });


    </script>

@endsection
