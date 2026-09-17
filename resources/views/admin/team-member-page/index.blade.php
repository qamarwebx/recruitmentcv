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

@section('title','Team Member Page')

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
                            <span class="d-none d-sm-inline-block">Add Page</span>
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
                            <th>Profile</th>
                            <th>Name</th>
                            <th>WhatsApp Number</th>
                            <th>Calling Number</th>
                            <th>Profile</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="listitem">
                        @if ($posts->count() > 0)
                            @foreach ($posts as $post)
                                <tr>

                                   <!-- Profile Image -->
                                    <td>

                                    @if(!empty($post->image))

                                        <a href="{{ $post->base_path.'/'.$post->image }}" target="_blank">

                                            <img src="{{ $post->base_path.'/'.$post->image }}"
                                                class="rounded-circle"
                                                style="width:40px; height:40px; object-fit:cover;">

                                        </a>

                                    @else

                                        <img src="{{ asset('images/default-user.png') }}"
                                            class="rounded-circle"
                                            style="width:40px; height:40px; object-fit:cover;">

                                    @endif

                                    </td>

                                    <!-- Name -->
                                    <td>{{ $post->name }}</td>

                                    <!-- WhatsApp -->
                                    <td>{{ $post->whatsapp_number }}</td>

                                    <!-- Calling -->
                                    <td>{{ $post->calling_number }}</td>

                                    <!-- url -->
                                    <td><a target="_blank" href="{{'https://qamrjob.com/housedriver/'.$post->staff_id }}">{{'https://qamrjob.com/housedriver/'.$post->staff_id }}</a></td>

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

        {{--Add Page Start --}}
        <div class="modal fade" id="offcanvasAddUser" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.team-member-page.store') }}" id="chaturlValidation" method="POST" enctype="multipart/form-data">
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

                            <div class="col-md-10">
                                <div class="mb-3">
                                    <label for="add-profile-url" class="form-label">
                                        Profile <span class="text-danger">*</span>
                                    </label>

                                    <input type="file" name="profile_image" id="add-profile-url" class="form-control">
                                    <input type="hidden" name="existing_profile_image" id="existing-new-profile-url" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="mt-3 mb-3 text-center">

                                    <!-- Clickable Avatar -->
                                    <a id="profile-link" href="#" target="_blank" style="display:none;">
                                        <img id="add-profile-preview" 
                                            src="" 
                                            alt="Preview" 
                                            class="rounded-circle"
                                            style="width:60px; height:60px; object-fit:cover; cursor:pointer;">
                                    </a>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="add-working-url" class="form-label">Whatsapp Number <span class="text-danger">*</span></label>
                                    <input type="text" name="working_mobile_no" id="add-working-url" class="form-control" placeholder="Enter Whatsapp Number...">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="add-calling-number" class="form-label">Calling Number <span class="text-danger">*</span></label>
                                    <input type="text" name="calling_number" id="add-calling-number" class="form-control" placeholder="Enter Calling Mobile Number...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-success btn-sm ">Add Page</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        {{--Add Page End --}}


        {{-- Edit Page Start --}}
        <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Edit Page</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.team-member-page.update') }}" id="chaturlValidation2" method="POST" enctype="multipart/form-data">
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

                                {{-- Profile Image --}}
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <label for="edit-profile-url" class="form-label">
                                            Profile <span class="text-danger">*</span>
                                        </label>

                                        <input type="file"
                                            name="profile_image"
                                            id="edit-profile-url"
                                            class="form-control">

                                        <input type="hidden"
                                            name="existing_profile_image"
                                            id="existing-edit-profile-url"
                                            class="form-control">
                                    </div>
                                </div>

                                {{-- Profile Preview --}}
                                <div class="col-md-2">
                                    <div class="mt-3 mb-3 text-center">

                                        <a id="edit-profile-link"
                                            href="#"
                                            target="_blank"
                                            style="display:none;">

                                            <img id="edit-profile-preview"
                                                src=""
                                                alt="Preview"
                                                class="rounded-circle"
                                                style="width:60px; height:60px; object-fit:cover; cursor:pointer;">
                                        </a>

                                    </div>
                                </div>

                                {{-- Working Mobile --}}
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="edit-working-url" class="form-label">
                                            Whatsapp Number <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                            name="working_mobile_no"
                                            id="edit-working-url"
                                            class="form-control"
                                            placeholder="Enter Whatsapp Number...">
                                    </div>
                                </div>

                                {{-- Calling Number --}}
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="edit-calling-number" class="form-label">
                                            Calling Number <span class="text-danger">*</span>
                                        </label>

                                        <input type="text"
                                            name="calling_number"
                                            id="edit-calling-number"
                                            class="form-control"
                                            placeholder="Enter Calling Mobile Number...">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">
                                Close
                            </button>

                            <button type="submit" class="btn btn-success btn-sm">
                                Update Page
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        {{-- Edit Page End --}}


        {{-- Delete Page Start --}}
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">

            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Delete Page</h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.team-member-page.delete') }}"
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
                                            Are you sure you want to delete this page?
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
                           
                            if (data.profile) {
                                const img = document.getElementById('add-profile-preview');
                                const link = document.getElementById('profile-link');

                                const imageUrl = data.base_path + '/' + data.profile;

                                img.src = imageUrl;
                                link.href = imageUrl;

                                $('#existing-new-profile-url').val(data.profile);

                                link.style.display = 'inline-block';
                            }


                            if (data.care_no_1 != null) {
                                $('#add-working-url').val('+'+data.care_no_1);
                            }else{
                                $('#add-working-url').val('');
                            }

                            if (data.care_no_2 != null) {
                                $('#add-calling-number').val('+'+data.care_no_2);
                            }else{
                                $('#add-calling-number').val('');
                            }

                        }
                    });
                } else {
                    $('#add-working-url').val('');
                }

            });

            $('#editModal').on("show.bs.modal", function (e) {

                var edit_id = $(e.relatedTarget).data('id');

                $('#editchat_id').val(edit_id);

                $.ajax({
                    url: "{{ route('admin.team-member-page.edit') }}",
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
                        $('#edit-working-url')
                            .val(data.whatsapp_number);

                        // Calling Number
                        $('#edit-calling-number')
                            .val(data.calling_number);

                        // Existing Image
                        $('#existing-edit-profile-url')
                            .val(data.image);

                        // Image Preview
                        if (data.image) {

                            var imageUrl = data.base_path + '/' + data.image;

                            $('#edit-profile-preview')
                                .attr('src', imageUrl);

                            $('#edit-profile-link')
                                .attr('href', imageUrl)
                                .show();

                        } else {

                            $('#edit-profile-preview')
                                .attr('src', '');

                            $('#edit-profile-link')
                                .attr('href', '#')
                                .hide();
                        }
                    }
                });

            });

            $('#deleteModal').on("show.bs.modal", function (e) {

                var delete_id = $(e.relatedTarget).data('id');

                $('#deletechat_id').val(delete_id);

            });


        });


        document.getElementById('add-profile-url').addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                $('#existing-new-profile-url').val('');
                const reader = new FileReader();

                reader.onload = function(event) {
                    const img = document.getElementById('add-profile-preview');
                    const link = document.getElementById('profile-link');
                   
                    img.src = event.target.result;
                    link.href = event.target.result;
                    
                    link.style.display = 'inline-block';
                }

                reader.readAsDataURL(file);
            }
        });


        document.getElementById('edit-profile-url').addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                $('#existing-edit-profile-url').val('');
                const reader = new FileReader();

                reader.onload = function(event) {
                    const img = document.getElementById('edit-profile-preview');
                    const link = document.getElementById('edit-profile-link');
                   
                    img.src = event.target.result;
                    link.href = event.target.result;
                    
                    link.style.display = 'inline-block';
                }

                reader.readAsDataURL(file);
            }
        });



    </script>

@endsection
