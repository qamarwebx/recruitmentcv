@extends('layout.partner.partner_layout')

@section('title','Profile')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
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
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-info" aria-controls="navs-pills-top-info" aria-selected="false"> 
                            <i class="ti-xs ti ti-info-circle me-1"></i> Information
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
                            <form action="{{ route('partner.partner.account.update',$post->id) }}" id="formAccountSettings" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="partner_ida" id="partner_ida" value="{{ $post->id }}">
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
                                        <label for="owner_name" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" name="owner_name" id="owner_name" class="form-control" value="{{ $post->owner_name }}" placeholder="Enter full name...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input class="form-control" type="text" id="email" name="email" value="{{ $post->email }}" placeholder="Enter email..." disabled/>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" name="username" id="username" class="form-control" value="{{ $post->username }}" placeholder="Enter username...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="owner_mobile_no" class="form-label">Mobile</label>
                                        <input type="text" name="owner_mobile_no" id="owner_mobile_no" class="form-control" value="{{ $post->owner_mobile_no }}" placeholder="Enter phone number...">
                                    </div>


                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm me-2">Save Changes</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-top-info" role="tabpanel">
                            <form id="formAccountPersonal" action="{{ route('partner.partner.personal.update',$post->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="partner_idp" id="partner_idp" value="{{ $post->id }}">
                                    <div class="mb-3 col-md-3">
                                        <label for="rec_off_name" class="form-label">Recruitment Office Name <span class="text-danger">*</span></label>
                                        <input type="text" name="rec_off_name" id="rec_off_name" class="form-control" value="{{ $post->rec_off_name }}" placeholder="Enter Recruitment Office Name...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="rec_office_arname" class="form-label">Recruitment Arabic Office Name</label>
                                        <input class="form-control" type="text" id="rec_office_arname" name="rec_office_arname" value="{{ $post->rec_office_arname }}" placeholder="Enter Recruitment Arabic Office Name..."/>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="countries" class="form-label">Country</label>
                                        <select name="country_id" id="countries" class="form-select select2" data-allow-clear="true">
                                            <option value="">Select</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}" @if($post->country_id == $country->id) selected @endif>{{ $country->name }}</option>
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
                                        <label for="primary_email" class="form-label">Primary Email</label>
                                        <input type="text" name="primary_email" id="primary_email" class="form-control" value="{{ $post->primary_email }}" placeholder="Enter Primary Email...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="secondary_email" class="form-label">Secondary Email</label>
                                        <input type="text" name="secondary_email" id="secondary_email" class="form-control" value="{{ $post->secondary_email }}" placeholder="Enter Secondary Email...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="office_no" class="form-label">Office No</label>
                                        <input type="text" name="office_no" id="office_no" class="form-control" value="{{ $post->office_no }}" placeholder="Enter Office No...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="primary_mob" class="form-label">Primary Mobile</label>
                                        <input type="text" name="primary_mob" id="primary_mob" class="form-control" value="{{ $post->primary_mob }}" placeholder="Enter Primary Mobile...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="secondary_mob" class="form-label">Secondary Mobile</label>
                                        <input type="text" name="secondary_mob" id="secondary_mob" class="form-control" value="{{ $post->secondary_mob }}" placeholder="Enter Secondary Mobile...">
                                    </div>

                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm me-2">Save Changes</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-top-profile" role="tabpanel">
                            <form id="formAccountChangePassword" action="{{ route('partner.partner.password.update',$post->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="partner_idpc" id="partner_idpc" value="{{ $post->id }}">
                                    <div class="mb-3 col-md-4 form-password-toggle">
                                        <label class="form-label" for="currentPassword">Current Password</label>
                                        <div class="input-group input-group-merge">
                                        <input class="form-control" type="password" name="currentPassword" id="currentPassword" placeholder="Enter current password..."/>
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-password-toggle">
                                        <label class="form-label" for="newPassword">New Password</label>
                                        <div class="input-group input-group-merge">
                                        <input class="form-control" type="password" id="newPassword" name="newPassword" placeholder="Enter new password..."/>
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-password-toggle">
                                        <label class="form-label" for="confirmPassword">Confirm New Password</label>
                                        <div class="input-group input-group-merge">
                                        <input class="form-control" type="password" name="confirmPassword" id="confirmPassword" placeholder="Enter confirm password..."/>
                                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm me-2">Save Changes</button>
                                </div>
                            </form>
                        </div>
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
    <script src="{{ asset('admin/assets/pages/validation/partner/partner-validation.js') }}"></script>
@endsection