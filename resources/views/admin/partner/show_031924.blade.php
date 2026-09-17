@extends('layout.admin.admin_layout')

@section('title','Partner View')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Partner Account Settings /</span> Account</h4>
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
                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-brand" aria-controls="navs-pills-top-brand" aria-selected="false">
                            <i class="ti-xs ti ti-photo"></i> Branding
                        </button>
                      </li>
                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-website-logo" aria-controls="navs-pills-website-logo" aria-selected="false">
                            <i class="ti-xs ti ti-photo"></i> Website Logo
                        </button>
                      </li>
                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-portal-info" aria-controls="navs-pills-portal-info" aria-selected="false">
                            <i class="ti-xs ti ti-lock me-1"></i> Portal Information
                        </button>
                      </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-pills-top-home" role="tabpanel">
                            <form action="{{ route('admin.partner.account.update',$post->id) }}" id="formAccountSettings" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="partner_ida" id="partner_ida" value="{{ $post->id }}">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    @if ($post->logo != '')
                                        <img src="{{ asset('admin/assets/images/partner/'.$post->logo) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar"/>                                
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
                                        <label for="owner_name" class="form-label">Owner Name <span class="text-danger">*</span></label>
                                        <input type="text" name="owner_name" id="owner_name" class="form-control" value="{{ $post->owner_name }}" placeholder="Enter full name...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input class="form-control" type="text" id="email" name="email" value="{{ $post->email }}" placeholder="Enter email..."/>
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
                            <form id="formAccountPersonal" action="{{ route('admin.partner.personal.update',$post->id) }}" method="POST">
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
                            <form id="formAccountChangePassword" action="{{ route('admin.partner.password.update',$post->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="partner_idpc" id="partner_idpc" value="{{ $post->id }}">
                                    @if (Auth::guard('admin')->user()->user_type != 1) 
                                        <div class="mb-3 col-md-4 form-password-toggle">
                                            <label class="form-label" for="currentPassword">Current Password</label>
                                            <div class="input-group input-group-merge">
                                                <input class="form-control" type="password" name="currentPassword" id="currentPassword" placeholder="Enter current password..."/>
                                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                                            </div>
                                        </div>
                                    @endif
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
                        <div class="tab-pane fade" id="navs-pills-top-brand" role="tabpanel">
                            <form action="{{ route('admin.partner.cvsetting',$post->id) }}" method="POST" id="partnercvsettupval" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-1">
                                        @if (isset($image1) && $image1->filename != '')
                                            <img src="{{ asset('admin/assets/images/cv_setting/'.$image1->filename) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar1"/>
                                        @else
                                            <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar1"/>
                                        @endif
                                    </div>
                                    <input type="hidden" name="pcvsetting1" @if(isset($image1)) value="{{ $image1->id }}"  @endif>
                                    <div class="col-md-3 mb-3">
                                        <label for="image1file" class="form-label">Image 1</label>
                                        <input class="form-control image1file" name="image1" type="file" id="image1file" />
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label" for="x-axis1">X Axis <span class="text-danger">*</span></label>
                                        @if (isset($image1) && $image1->x_axis != '')
                                            <input type="text" name="x_axis1" class="form-control" value="{{ $image1->x_axis }}" id="x-axis1" placeholder="Enter X Axis point...">                                        
                                        @else
                                            <input type="text" name="x_axis1" class="form-control" id="x-axis1" placeholder="Enter X Axis point...">
                                        @endif
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label" for="y-axis1">Y Axis <span class="text-danger">*</span></label>
                                        @if (isset($image1) && $image1->y_axis != '')
                                            <input type="text" name="y_axis1" class="form-control" value="{{ $image1->y_axis }}" id="y-axis1" placeholder="Enter Y Axis point...">
                                        @else
                                            <input type="text" name="y_axis1" class="form-control" id="y-axis1" placeholder="Enter Y Axis point...">
                                        @endif
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label" for="width-img1">Width</label>
                                        @if (isset($image1) && $image1->width != '')
                                            <input type="text" name="width1" class="form-control" value="{{ $image1->width }}" id="width-img1" placeholder="Enter image width...">                                        
                                        @else
                                            <input type="text" name="width1" class="form-control" id="width-img1" placeholder="Enter image width...">
                                        @endif
                                    </div>

                                    <div class="col-md-2">
                                        <label for="">Active / Deactive</label>
                                        <div class="mb-3">
                                            <div class="form-check form-check-inline mt-3">
                                                <input class="form-check-input" type="radio" name="status1" id="statusactive1" @if(isset($image1) && $image1->status == 1) checked @endif value="1"/>
                                                <label class="form-check-label" for="statusactive1">Active</label>
                                            </div>
                                            <div class="form-check form-check-inline mt-3">
                                                <input class="form-check-input" type="radio" name="status1" id="statusdeactive1" @if(isset($image1) && $image1->status == 0) checked @endif value="0"/>
                                                <label class="form-check-label" for="statusdeactive1">Deactive</label>
                                            </div>
                                        </div>


                                    </div>

                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-1">
                                        @if (isset($image2) && $image2->filename != '')
                                            <img src="{{ asset('admin/assets/images/cv_setting/'.$image2->filename) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar2"/>
                                        @else
                                            <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar2"/>
                                        @endif
                                    </div>
                                    <input type="hidden" name="pcvsetting2" @if(isset($image2)) value="{{ $image2->id }}"  @endif>
                                    <div class="col-md-3 mb-3">
                                        <label for="image2file" class="form-label">Image 2</label>
                                        <input class="form-control image2file" name="image2" type="file" id="image2file" />
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label" for="x-axis2">X Axis</label>
                                        @if (isset($image2) && $image2->x_axis != '')
                                            <input type="text" name="x_axis2" class="form-control" value="{{ $image2->x_axis }}" id="x-axis2" placeholder="Enter X Axis point...">                                        
                                        @else   
                                            <input type="text" name="x_axis2" class="form-control" id="x-axis2" placeholder="Enter X Axis point...">
                                        @endif
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label" for="y-axis2">Y Axis</label>
                                        @if (isset($image2) && $image2->y_axis != '')
                                            <input type="text" name="y_axis2" class="form-control" value="{{ $image2->y_axis }}" id="y-axis2" placeholder="Enter Y Axis point...">                                        
                                        @else
                                            <input type="text" name="y_axis2" class="form-control" id="y-axis2" placeholder="Enter Y Axis point...">
                                        @endif
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label" for="width-img2">Width</label>
                                        @if (isset($image2) && $image2->width != '')
                                            <input type="text" name="width2" class="form-control" value="{{ $image2->width }}" id="width-img2" placeholder="Enter image width...">                                        
                                        @else
                                            <input type="text" name="width2" class="form-control" id="width-img2" placeholder="Enter image width...">
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <label for="">Active / Deactive</label>
                                        <div class="mb-3">
                                            <div class="form-check form-check-inline mt-3">
                                                <input class="form-check-input" type="radio" name="status2" id="statusactive2" @if(isset($image2) && $image2->status == 1) checked @endif value="1"/>
                                                <label class="form-check-label" for="statusactive2">Active</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="status2" id="statusdeactive2" @if(isset($image2) && $image2->status == 0) checked @endif value="0"/>
                                                <label class="form-check-label" for="statusdeactive2">Deactive</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-primary float-end">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-website-logo" role="tabpanel">
                            <form action="{{ route('admin.partner.websitelogoupdt',$post->id) }}" method="POST" enctype="multipart/form-data" id="websitelogovalidation">
                                @csrf
                                <div class="row">
                                    <div class="col-md-2">
                                        @if ($post->website_logo)
                                            <img src="{{ asset('admin/assets/images/partner/'.$post->website_logo) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar3">
                                        @else
                                            <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar3"/>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="logoImages" class="form-label">Upload Logo</label>
                                        <input class="form-control logoImages" name="website_logo" type="file" id="logoImages" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-primary float-end">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-portal-info" role="tabpanel">
                            <form id="formAccountPortal" action="{{ route('admin.partner.portal.update',$post->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="partner_idp" id="partner_idp" value="{{ $post->id }}">
                                    <div class="mb-3 col-md-3">
                                        <label for="portal_rec_off_name" class="form-label">Recruitment Office Name (Display Only)<span class="text-danger">*</span></label>
                                        <input type="text" name="portal_rec_off_name" id="portal_rec_off_name" class="form-control" value="{{ $post->portal_rec_off_name }}" placeholder="Enter Recruitment Office Name...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="portal_rec_off_arname" class="form-label">Recruitment Arabic Office Name <span class="text-danger">*</span></label>
                                        <input type="text" name="portal_rec_off_arname" id="portal_rec_off_arname" class="form-control" value="{{ $post->portal_rec_off_arname }}" placeholder="Enter Recruitment Office Name...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="portal_cons_per_name" class="form-label">Consern Person Name <span class="text-danger">*</span></label>
                                        <input type="text" name="portal_cons_per_name" id="portal_cons_per_name" class="form-control" value="{{ $post->portal_cons_per_name }}" placeholder="Enter Recruitment Office Name...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="portal_email" class="form-label">Email</label>
                                        <input type="text" name="portal_email" id="portal_email" class="form-control" value="{{ $post->portal_email }}" placeholder="Enter Primary Email...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="portal_mobile_no_1" class="form-label">Mobile No 1</label>
                                        <input type="text" name="portal_mobile_no_1" id="portal_mobile_no_1" class="form-control" value="{{ $post->portal_mobile_no_1 }}" placeholder="Enter Mobile No 1...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="portal_mobile_no_2" class="form-label">Mobile No 2</label>
                                        <input type="text" name="portal_mobile_no_2" id="portal_mobile_no_2" class="form-control" value="{{ $post->portal_mobile_no_2 }}" placeholder="Enter Mobile No 2...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="portal_mobile_no_3" class="form-label">Mobile No 3</label>
                                        <input type="text" name="portal_mobile_no_3" id="portal_mobile_no_3" class="form-control" value="{{ $post->portal_mobile_no_3 }}" placeholder="Enter Mobile No 3...">
                                    </div></br>
                                     <!-- Add other input fields here -->
                                    <div class="mb-3 col-md-3">
                                        <textarea id="portal_add_for_cust" name="portal_add_for_cust" class="form-control" rows="2" placeholder="Enter Address For Customer...">{{ $post->portal_add_for_cust }}</textarea>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <textarea id="portal_add_disp_only" name="portal_add_disp_only" class="form-control" rows="2" placeholder="Enter Address Display Only...">{{ $post->portal_add_disp_only }}</textarea>
                                    </div>
                                    <div class="mb-3 mr-3 col-md-3">
                                        <textarea id="portal_ar_add_disp_only" name="portal_ar_add_disp_only" class="form-control" rows="2" placeholder="Enter Arabic Address Display Only...">{{ $post->portal_ar_add_disp_only }}</textarea>
                                    </div><br><br>
                                    <div class="mb-3 col-md-2 form-password-toggle">
                                        <input class="form-check-input" type="radio" name="portal_status" id="active"  @if($post->portal_status == 1) checked @endif value="1"/>
                                        <label class="form-check-label" for="active">Active</label>
                                    </div>
                                    <div class="mb-3 col-md-2 form-password-toggle">
                                        <input class="form-check-input" type="radio" name="portal_status" id="deactive" @if($post->portal_status == 0) checked @endif value="0"/>
                                        <label class="form-check-label" for="deactive">Deactive</label>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm me-2 float-end waves-effect waves-light">Save Changes</button>
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
    <script src="{{ asset('admin/assets/pages/validation/partner-validation.js') }}"></script>
@endsection