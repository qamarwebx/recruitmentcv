@extends('layout.admin.admin_layout')

@section('title','Staff View')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="nav-align-top mb-4">
                    <ul class="nav nav-pills mb-3" role="tablist">
                      <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-home" aria-controls="navs-pills-top-home" aria-selected="true">
                            <i class="ti-xs ti ti-users me-1"></i> Account
                        </button>
                      </li>
                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-profile" aria-controls="navs-pills-top-profile" aria-selected="false">
                            <i class="ti-xs ti ti-lock me-1"></i> Security
                        </button>
                      </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                            <form action="{{ route('admin.staff.profile.upload',$post->id) }}" id="formAccountSettings" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="admin_idp" id="admin_idp" value="{{ $post->id }}">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    @if ($post->profile != '')
                                        <img src="{{ asset('admin/assets/img/avatars/'.$post->profile) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar"/>
                                    @else
                                        <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar"/>
                                    @endif
                                    <div class="button-wrapper">
                                        <label for="upload" class="btn btn-primary me-2 mb-3" tabindex="0">
                                            <span class="d-none d-sm-block">Upload new photo</span>
                                            <i class="ti ti-upload d-block d-sm-none"></i>
                                            <input type="file" name="photo" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg"/>
                                        </label>
                                        <button type="button" class="btn btn-label-secondary account-image-reset mb-3">
                                            <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                                            <span class="d-none d-sm-block">Reset</span>
                                        </button>
                                        <div class="text-muted">Allowed JPG, GIF or PNG. Max size of 350KB</div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="mb-3 col-md-3">
                                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control" value="{{ $post->name }}" placeholder="Enter full name...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="email" class="form-label">Personal E-mail</label>
                                        <input class="form-control" type="text" id="email" name="email" value="{{ $post->email }}" disabled/>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" id="username" class="form-control" value="{{ $post->username }}" placeholder="Enter username...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="phone" class="form-label">Personal Mobile</label>
                                        <input type="text" name="phone" id="phone" class="form-control" value="{{ $post->phone }}" placeholder="Enter phone number...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label class="form-label" for="add-doi-pp">Date of Birth</label>
                                        <input type="text" name="dob" id="dob" value="{{ $post->dob }}" class="form-control flatpickr-basic" placeholder="Enter date of birth...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label class="form-label" for="gender">Gender</label>
                                        <select name="gender" id="gender" class="select2 form-select" data-placeholder="Select Gender" data-allow-clear="true">
                                            <option value="">Select</option>
                                            <option value="Male" @if($post->gender == 'Male') selected @endif>Male</option>
                                            <option value="Female" @if($post->gender == 'Female') selected @endif>Female</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label class="form-label" for="religion">Religion</label>
                                        <select name="religion" id="religion" class="select2 form-select" data-allow-clear="true">
                                            <option value="">Select</option>
                                            <option value="Muslim" @if($post->religion == 'Muslim') selected @endif>Muslim</option>
                                            <option value="Hindu" @if($post->religion == 'Hindu') selected @endif>Hindu</option>
                                            <option value="Christian" @if($post->religion == 'Christian') selected @endif>Christian</option>
                                            <option value="Jain" @if($post->religion == 'Jain') selected @endif>Jain</option>
                                            <option value="Judaism" @if($post->religion == 'Judaism') selected @endif>Judaism</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label class="form-label" for="designation">Designation</label>
                                        <select name="designation" id="designation" class="select2 form-select" data-allow-clear="true">
                                            <option value="">Select</option>
                                            <option value="Admin" @if($post->designation == 'Admin') selected @endif>Admin</option>
                                            <option value="HR" @if($post->designation == 'HR') selected @endif>HR</option>
                                            <option value="Web Developer" @if($post->designation == 'Web Developer') selected @endif>Web Developer</option>
                                            <option value="Manager" @if($post->designation == 'Manager') selected @endif>Manager</option>
                                        </select>
                                    </div>
                                   @php
                                        $selectedRoles = [];

                                        if (!empty($post->role)) {
                                            $selectedRoles = json_decode($post->role, true) ?: [];
                                        }
                                    @endphp

                                    <div class="mb-3 col-md-3">
                                        <label class="form-label" for="role">Role</label>

                                        <select name="role[]" id="role" class="select2 form-select" multiple data-allow-clear="true">
                                            <option value="Candidate Source"
                                                {{ in_array('Candidate Source', $selectedRoles) ? 'selected' : '' }}>
                                                Candidate Source
                                            </option>

                                            <option value="Client Relation"
                                                {{ in_array('Client Relation', $selectedRoles) ? 'selected' : '' }}>
                                                Client Relation
                                            </option>

                                             <option value="Team Head"
                                                {{ in_array('Team Head', $selectedRoles) ? 'selected' : '' }}>
                                                Team Head
                                            </option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" name="address" id="address" class="form-control" value="{{ $post->address }}" placeholder="Enter address...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="regions" class="form-label">State</label>
                                        <select name="region_id" id="regions" class="form-select select2" data-allow-clear="true">
                                            <option value="">Select</option>
                                            @foreach ($regions as $region)
                                                <option value="{{ $region->id }}" @if($post->region_id == $region->id) selected @endif>{{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="cities" class="form-label">City</label>
                                        <select name="city_id" id="cities" class="form-select select2" data-allow-clear="true">
                                            <option value="">Select</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" @if($post->city_id == $city->id) selected @endif>{{ $city->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="pincode" class="form-label">Pincode</label>
                                        <input type="text" name="pincode" id="pincode" class="form-control" value="{{ $post->pincode }}" placeholder="Enter Pincode here...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="working-email" class="form-label">Work Email CRM Notification</label>
                                        <input type="text" name="working_email" id="working-email" class="form-control" value="{{ $post->working_email }}" placeholder="Enter Working Email...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="working-mobile-no" class="form-label">Work Mobile No CRM Notification</label>
                                        <input type="text" name="work_number" id="working-mobile-no" class="form-control" value="{{ $post->work_number }}" placeholder="Enter Working Number...">
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="update-care-no-1" class="form-label">Careoff No.01 (Marketing Call)</label>
                                        <input type="text" name="care_no_1" class="form-control" id="update-care-no-1" value="{{ $post->care_no_1 }}" placeholder="Enter Care No.01">
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="update-care-no-2" class="form-label">Careoff No.02 (Whatsup No.)</label>
                                        <input type="text" name="care_no_2" class="form-control" id="update-care-no-2" value="{{ $post->care_no_2 }}" placeholder="Enter Care No.02">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm me-2">Save Changes</button>
                                </div>
                            </form>
                        </div>
                      <div class="tab-pane fade" id="navs-pills-top-profile" role="tabpanel">
                        <form id="formAccountChangePassword" action="{{ route('admin.staff.password.change',$post->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="admin_idpp" id="admin_idpp" value="{{ $post->id }}">
                                <div class="mb-3 col-sm-6 form-password-toggle">
                                    <label class="form-label" for="newPassword">Password</label>
                                    <div class="input-group input-group-merge">
                                        <input class="form-control" type="password" id="newPassword" name="newPassword" placeholder="Enter new password..." required/>
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-6 form-password-toggle">
                                    <label class="form-label" for="confirmPassword">Confirm Password</label>
                                    <div class="input-group input-group-merge">
                                      <input class="form-control" type="password" name="confirmPassword" id="confirmPassword" placeholder="Enter confirm password..." required/>
                                      <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 text-end">
                                <button type="submit" class="btn btn-primary btn-sm me-2">Save Changes</button>
                            </div>
                        </form>
                        <br>
                        @if(auth()->user()->user_type == 1 && $post->user_type == 1 )
                        <form id="formAccountChangePassword" action="{{ route('admin.staff.password.change',$post->id) }}" method="POST">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="login_access_email_admin_id" id="login_access_email_admin_id" value="{{ $post->id }}">

                                <div class="mb-3 col-sm-6">
                                    <label class="form-label" for="login_access_email">Login Access Email Notifications</label>
                                    <div class="input-group input-group-merge">
                                        <input class="form-control" type="email" id="login_access_email" name="login_access_email" value="{{$post->login_access_email}}" placeholder="Enter Login Access Email..." required/>
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-mail"></i></span>
                                    </div>
                                </div>
                               
                            </div>
                            <div class="row">
                                <div class="mb-3 col-sm-6">
                                    <div class="mt-2 text-end">
                                        <button type="submit" class="btn btn-primary btn-sm me-2">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                      </div>
                    @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')


    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

    <!-- Validation Page -->
    <script src="{{ asset('admin/assets/pages/validation/staff-validation-2.js') }}?v={{ file_exists(public_path('admin/assets/pages/validation/staff-validation-2.js')) ? filemtime(public_path('admin/assets/pages/validation/staff-validation-2.js')) : time() }}"></script>
@endsection
