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
        <h4 class="fw-bold py-3 mb-4 d-flex align-items-center">
            <span class="text-muted fw-light">Partner Account Settings /</span>&nbsp;Account
            @php
                $regStatusMap = [
                    0 => ['label' => 'Pending', 'class' => 'bg-label-warning'],
                    1 => ['label' => 'Approved', 'class' => 'bg-label-success'],
                    2 => ['label' => 'Rejected', 'class' => 'bg-label-danger'],
                ];
                $regStatus = $regStatusMap[(int) ($post->registration_status ?? 0)];
            @endphp
            <span class="badge {{ $regStatus['class'] }} ms-3" style="cursor:pointer;font-size:0.8rem;" data-bs-toggle="modal" data-bs-target="#changeRegistrationStatus" data-id="{{ $post->id }}" title="Worker portal registration status - click to change">
                Registration: {{ $regStatus['label'] }}
            </span>
        </h4>
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
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-cost" aria-controls="navs-pills-top-cost" aria-selected="true">
                            <i class="ti-xs ti ti-currency-rupee me-1"></i> Cost
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
                            <i class="ti-xs ti ti-photo me-1"></i> Branding
                        </button>
                      </li>
                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-domain" aria-controls="navs-pills-domain" aria-selected="false">
                            <i class="ti-xs ti ti-world me-1"></i> Domain
                        </button>
                      </li>

                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-website-logo" aria-controls="navs-pills-website-logo" aria-selected="false">
                            <i class="ti-xs ti ti-layout me-1"></i> Website
                        </button>
                      </li>

                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-sharedcv-info" aria-controls="navs-pills-sharedcv-info" aria-selected="false">
                            <i class="ti-xs ti ti-lock me-1"></i> Share CV
                        </button>
                      </li>

                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-portal-info" aria-controls="navs-pills-portal-info" aria-selected="false">
                            <i class="ti-xs ti ti-lock me-1"></i> Portal Information
                        </button>
                      </li>

                      <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-partnerwebsite" aria-controls="navs-pills-partnerwebsite" aria-selected="false">
                            <i class="ti-xs ti ti-world-www me-1"></i> RecruitmentCV Domain
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
                        <div class="tab-pane fade show" id="navs-pills-top-cost" role="tabpanel">
                            <div class="row" id="nav-service-charge-div">
                                <div class="col-md-12 table-responsive" style="margin-bottom: 20px;">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>SERVICES CHARGES</th>
                                                <th>Service Charge For</th>
                                                <th>GIVEN BY</th>
                                                <th>CREATED BY</th>
                                                <th>STATUS</th>
                                                <th>GIVEN DATE</th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $i = 1;
                                            @endphp
                                            @foreach ($partnerScs as $partnerSc)
                                                @php
                                                    // Status
                                                    $Status = $partnerSc->status;
                                                    $states = ['danger','success','warning', 'info', 'primary', 'secondary'];
                                                    $statusMsg = ['Inactive','Active'];
                                                    $textStatus = $statusMsg[$Status];
                                                    $state = $states[$Status];
                                                    $bstarget = ['#activemp','#inactivemp'];
                                                    $targetData = $bstarget[$Status];
                                                @endphp
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $partnerSc->service_charge }}</td>
                                                    <td>{{ $partnerSc->profession->eng_name }}</td>
                                                    <td>{{ $partnerSc->givenby->name }}</td>
                                                    <td>{{ $partnerSc->createby->name }}</td>
                                                    <td><span class="badge bg-label-{{ $state }}" data-bs-toggle="modal" data-bs-target="{{ $targetData }}" data-id="{{ $partnerSc->id }}">{{ $textStatus }}</span></td>
                                                    <td>{{ date('d-m-Y',strtotime($partnerSc->created_at)) }}</td>
                                                    <td>
                                                        {{-- <a href="javascript:;" class="text-body delemployer delete-record" data-bs-toggle="modal" data-id="{{ $partnerSc->id }}" data-bs-target="#deleteemployer" ><i class="ti ti-trash ti-sm mx-2"></i></a> --}}
                                                        <a href="javascript:;" class="text-body" data-bs-toggle="offcanvas" data-bs-target="#editServiceCharge" data-id="{{ $partnerSc->id }}"><i class="ti ti-edit ti-sm"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-sm btn-success me-1 float-end" data-bs-toggle="offcanvas" data-bs-target="#addServiceCharge">Add Service Charge</button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-top-info" role="tabpanel">
                            <form id="formAccountPersonal" action="{{ route('admin.partner.personal.update',$post->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="partner_idp" id="partner_idp" value="{{ $post->id }}">
                                    <div class="mb-3 col-md-3">
                                        <label for="rec_off_name" class="form-label">Recruitment Office Name Eng<span class="text-danger">*</span></label>
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
                                    <div class="mb-3 col-md-3">
                                        <textarea id="info_eng_address" name="info_eng_address" class="form-control" rows="2" placeholder="Enter Address For English...">{{ $post->info_eng_address }}</textarea>
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <textarea id="info_ar_address" name="info_ar_address" class="form-control" rows="2" placeholder="Enter Address For Arabic...">{{ $post->info_ar_address }}</textarea>
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
                        <!-- Domain -->
                        <div class="tab-pane fade" id="navs-pills-domain" role="tabpanel">

                            <form id="domainForm">
                                @csrf

                                <div class="row">

                                    <input type="hidden" name="partner_id" value="{{ $partnerId }}">
                                    <input type="hidden" name="domain_id" id="domain_id"
                                        value="{{ $partnerDomain->id ?? '' }}">

                                    <!-- Domain Input -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Domain Name</label>

                                        <div class="input-group">
                                            <input
                                                type="text"
                                                name="domain_name"
                                                id="domain_name"
                                                class="form-control"
                                                placeholder="example.com"
                                                value="{{ $partnerDomain->domain_name ?? '' }}"
                                                required>

                                            <button
                                                type="button"
                                                class="btn btn-warning"
                                                id="generateDnsBtn">
                                                Generate DNS
                                            </button>
                                        </div>
                                    </div>
                                @if(isset($partnerDomain->domain_name))
                                    <div class="col-md-6 mb-3">

                                        <label class="form-label fw-bold">
                                            Connected Domain
                                        </label>

                                        <div class="border rounded px-3 py-2 d-flex align-items-center bg-light">

                                            <i class="fas fa-globe text-success me-2"></i>

                                            <span class="fw-semibold">
                                                {{ $partnerDomain->domain_name }}
                                            </span>

                                            <i class="fas fa-circle-check text-success ms-auto me-3"></i>

                                            <a
                                                href="javascript:void(0)"
                                                class="text-danger"
                                                title="Remove Domain"
                                                onclick="deleteDomain('{{ $partnerDomain->id }}')">

                                                <i class="fas fa-trash-alt"></i>

                                            </a>

                                        </div>

                                    </div>
                                @endif
                                </div>

                                <!-- DNS Info -->
                                <div class="alert alert-warning mt-2 d-none" id="dnsInfoBox">

                                    <div class="fw-bold mb-2">
                                        📌 Please point your domain DNS
                                    </div>

                                    <table class="table table-bordered table-sm mb-0 bg-white">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Host</th>
                                                <th>Value</th>
                                                <th>TTL</th>
                                            </tr>
                                        </thead>

                                        <tbody id="dnsRecordsBody">

                                        </tbody>
                                    </table>

                                    <div class="mt-2 small text-muted">
                                        After updating DNS, propagation may take 5 minutes to 24 hours.
                                    </div>

                                </div>

                                <!-- Submit -->
                                <div class="text-end mt-3">
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-primary"
                                        id="domainSubmitBtn">

                                        {{ isset($partnerDomain) ? 'Update Domain' : 'Add Domain' }}

                                    </button>
                                </div>

                            </form>

                        </div>
                        <!-- website -->
                        <div class="tab-pane fade" id="navs-pills-website-logo" role="tabpanel">
                            <div class="row">

                                <!-- LEFT SIDE TABS -->
                                <div class="col-md-2">
                                    <div class="nav flex-column nav-pills" role="tablist">

                                        <!-- Logo Tab -->
                                        <button class="nav-link active d-flex align-items-center text-start"
                                            data-bs-toggle="tab"
                                            data-bs-target="#logo-tab"
                                            type="button">
                                            
                                            <i class="ti ti-photo me-2"></i>
                                            <span>Logo</span>
                                        </button>

                                        <!-- Address Tab -->
                                        <button type="button" 
                                            class="nav-link d-flex align-items-center text-start" 
                                            data-bs-toggle="tab" 
                                            data-bs-target="#navs-pills-address">
                                            
                                            <i class="ti ti-map-pin me-2"></i>
                                            <span>Address</span>
                                        </button>

                                    </div>
                                </div>

                                <!-- RIGHT SIDE CONTENT -->
                                <div class="col-md-10">
                                    <div class="tab-content">
                                        <!-- LOGO TAB -->
                                        <div class="tab-pane fade show active" id="logo-tab">
                                            <form id="logoForm" enctype="multipart/form-data">
                                                @csrf

                                                <input type="hidden" name="id" value="{{ $post->id }}">

                                                <div class="row">

                                                    <!-- English Logo -->
                                                    <div class="col-md-6 mb-4">
                                                        <div class="card p-4 text-center shadow-sm border-0">

                                                            <h6 class="mb-3">English Logo</h6>

                                                            <div class="logo-preview mb-3">
                                                                <img 
                                                                    src="{{ !empty($partnerDomain->website_logo) 
                                                                    ? asset('admin/assets/images/partner/'.$partnerDomain->website_logo) 
                                                                    : asset('admin/assets/img/avatars/blank.jpeg') }}"
                                                                    class="img-fluid rounded preview-en"
                                                                    style="width:120px;height:120px;object-fit:contain;background:#f5f5f5;padding:10px;">
                                                            </div>

                                                            <input type="file" name="website_logo" class="form-control input-en">
                                                        </div>
                                                    </div>

                                                    <!-- Arabic Logo -->
                                                    <div class="col-md-6 mb-4">
                                                        <div class="card p-4 text-center shadow-sm border-0">

                                                            <h6 class="mb-3">Arabic Logo</h6>

                                                            <div class="logo-preview mb-3">
                                                                <img 
                                                                    src="{{ !empty($partnerDomain->website_logo_ar) 
                                                                    ? asset('admin/assets/images/partner/'.$partnerDomain->website_logo_ar) 
                                                                    : asset('admin/assets/img/avatars/blank.jpeg') }}"
                                                                    class="img-fluid rounded preview-ar"
                                                                    style="width:120px;height:120px;object-fit:contain;background:#f5f5f5;padding:10px;">
                                                            </div>

                                                            <input type="file" name="website_logo_ar" class="form-control input-ar">
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary px-4">
                                                        Save Logos
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- Address TAB -->
                                        <div class="tab-pane fade" id="navs-pills-address" role="tabpanel">

                                            <form id="addressForm">
                                                @csrf

                                                <input type="hidden" name="id" value="{{ $partnerDomain->id ?? '' }}">

                                                <div class="row">

                                                    <!-- Company Name EN -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Company Name (EN)</label>
                                                        <input type="text" name="company_name" class="form-control"
                                                            placeholder="Enter company name"
                                                            value="{{ $partnerDomain->company_name ?? '' }}">
                                                    </div>

                                                    <!-- Company Name AR -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Company Name (AR)</label>
                                                        <input type="text" name="company_name_ar" class="form-control text-end"
                                                            placeholder="ادخل اسم الشركة"
                                                            value="{{ $partnerDomain->company_name_ar ?? '' }}">
                                                    </div>

                                                    <!-- Company Address EN -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Company Address (EN)</label>
                                                        <textarea name="company_address" class="form-control" rows="3"
                                                            placeholder="Enter address">{{ $partnerDomain->company_address ?? '' }}</textarea>
                                                    </div>

                                                    <!-- Company Address AR -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Company Address (AR)</label>
                                                        <textarea name="company_address_ar" class="form-control text-end" rows="3"
                                                            placeholder="ادخل العنوان">{{ $partnerDomain->company_address_ar ?? '' }}</textarea>
                                                    </div>

                                                    <!-- Mobile -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Mobile</label>
                                                        <input type="text" name="company_mobile" class="form-control"
                                                            value="{{ $partnerDomain->company_mobile ?? '' }}">
                                                    </div>

                                                    <!-- Email -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" name="company_email" class="form-control"
                                                            value="{{ $partnerDomain->company_email ?? '' }}">
                                                    </div>

                                                </div>

                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        Save Address
                                                    </button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-sharedcv-info" role="tabpanel">
                            <form id="formAccountPortalShare" action="{{ route('admin.partner.sharecv.update',$post->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="partner_idp" id="partner_idp" value="{{ $post->id }}">


                                    <div class="mb-3 col-md-3">
                                        <label for="sharecv_per_name1" class="form-label">Concern Person Name1 <span class="text-danger">*</span></label>
                                        <input type="text" name="sharecv_per_name1" id="sharecv_per_name1" class="form-control" value="{{ $post->sharecv_per_name1 }}" placeholder="Enter Consern Person Name...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="sharecv_per_mobile1" class="form-label">Mobile No 1</label>
                                        <input type="text" name="sharecv_per_mobile1" id="sharecv_per_mobile1" class="form-control" value="{{ $post->sharecv_per_mobile1 }}" placeholder="Enter Mobile No 1...">
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="sharecv_per_name2" class="form-label">Consern Person Name2 <span class="text-danger">*</span></label>
                                        <input type="text" name="sharecv_per_name2" id="sharecv_per_name2" class="form-control" value="{{ $post->sharecv_per_name2 }}" placeholder="Enter Consern Person Name...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="sharecv_per_mobile2" class="form-label">Mobile No 2</label>
                                        <input type="text" name="sharecv_per_mobile2" id="sharecv_per_mobile2" class="form-control" value="{{ $post->sharecv_per_mobile2 }}" placeholder="Enter Mobile No 2...">
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="sharecv_per_name3" class="form-label">Consern Person Name3 <span class="text-danger">*</span></label>
                                        <input type="text" name="sharecv_per_name3" id="sharecv_per_name3" class="form-control" value="{{ $post->sharecv_per_name3 }}" placeholder="Enter Consern Person Name...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="sharecv_per_mobile3" class="form-label">Mobile No 3</label>
                                        <input type="text" name="sharecv_per_mobile3" id="sharecv_per_mobile3" class="form-control" value="{{ $post->sharecv_per_mobile3 }}" placeholder="Enter Mobile No 3...">
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="sharecv_per_name4" class="form-label">Consern Person Name4 <span class="text-danger">*</span></label>
                                        <input type="text" name="sharecv_per_name4" id="sharecv_per_name4" class="form-control" value="{{ $post->sharecv_per_name4 }}" placeholder="Enter Consern Person Name...">
                                    </div>
                                    <div class="mb-3 col-md-3">
                                        <label for="sharecv_per_mobile4" class="form-label">Mobile No 4</label>
                                        <input type="text" name="sharecv_per_mobile4" id="sharecv_per_mobile4" class="form-control" value="{{ $post->sharecv_per_mobile4 }}" placeholder="Enter Mobile No 4...">
                                    </div>



                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm me-2 float-end waves-effect waves-light">Save Changes</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="navs-pills-portal-info" role="tabpanel">
                            <form id="formAccountPortal" action="{{ route('admin.partner.portal.update',$post->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="partner_idp" id="partner_idp" value="{{ $post->id }}">


                                    <div class="mb-3 col-md-4">
                                        <label for="portal_consern_person_name1" class="form-label">Consern Person Name1 <span class="text-danger">*</span></label>
                                        <input type="text" name="portal_consern_person_name1" id="portal_consern_person_name1" class="form-control" value="{{ $post->portal_consern_person_name1 }}" placeholder="Enter Consern Person Name...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_mobile_no_1_text" class="form-label">Mobile No 1 (Text)</label>
                                        <input type="text" name="portal_mobile_no_1_text" id="portal_mobile_no_1_text" class="form-control" value="{{ $post->portal_mobile_no_1_text }}" placeholder="Enter Mobile No 1...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_mobile_no_1" class="form-label">Mobile No 1 (Order Send to)</label>
                                        <input type="text" name="portal_mobile_no_1" id="portal_mobile_no_1" class="form-control" value="{{ $post->portal_mobile_no_1 }}" placeholder="Enter Mobile No 1...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_consern_person_name2" class="form-label">Consern Person Name2 <span class="text-danger">*</span></label>
                                        <input type="text" name="portal_consern_person_name2" id="portal_consern_person_name2" class="form-control" value="{{ $post->portal_consern_person_name2 }}" placeholder="Enter Consern Person Name...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_mobile_no_2_text" class="form-label">Mobile No 2 (Text)</label>
                                        <input type="text" name="portal_mobile_no_2_text" id="portal_mobile_no_2_text" class="form-control" value="{{ $post->portal_mobile_no_2_text }}" placeholder="Enter Mobile No 2...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_mobile_no_2" class="form-label">Mobile No 2 (Order Send to)</label>
                                        <input type="text" name="portal_mobile_no_2" id="portal_mobile_no_2" class="form-control" value="{{ $post->portal_mobile_no_2 }}" placeholder="Enter Mobile No 2...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_consern_person_name3" class="form-label">Consern Person Name3 <span class="text-danger">*</span></label>
                                        <input type="text" name="portal_consern_person_name3" id="portal_consern_person_name3" class="form-control" value="{{ $post->portal_consern_person_name3 }}" placeholder="Enter Consern Person Name...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_mobile_no_3_text" class="form-label">Mobile No 3 (Text)</label>
                                        <input type="text" name="portal_mobile_no_3_text" id="portal_mobile_no_3_text" class="form-control" value="{{ $post->portal_mobile_no_3_text }}" placeholder="Enter Mobile No 3...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_mobile_no_3" class="form-label">Mobile No 3 (Order Send to)</label>
                                        <input type="text" name="portal_mobile_no_3" id="portal_mobile_no_3" class="form-control" value="{{ $post->portal_mobile_no_3 }}" placeholder="Enter Mobile No 3...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_consern_person_name4" class="form-label">Consern Person Name4 <span class="text-danger">*</span></label>
                                        <input type="text" name="portal_consern_person_name4" id="portal_consern_person_name4" class="form-control" value="{{ $post->portal_consern_person_name4 }}" placeholder="Enter Consern Person Name...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_mobile_no_4_text" class="form-label">Mobile No 4 (Text)</label>
                                        <input type="text" name="portal_mobile_no_4_text" id="portal_mobile_no_4_text" class="form-control" value="{{ $post->portal_mobile_no_4_text }}" placeholder="Enter Mobile No 4...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_mobile_no_4" class="form-label">Mobile No 4 (Order Send to)</label>
                                        <input type="text" name="portal_mobile_no_4" id="portal_mobile_no_4" class="form-control" value="{{ $post->portal_mobile_no_4 }}" placeholder="Enter Mobile No 4...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_rec_off_name" class="form-label">Recruitment Office Name (Display Only)<span class="text-danger">*</span></label>
                                        <input type="text" name="portal_rec_off_name" id="portal_rec_off_name" class="form-control" value="{{ $post->portal_rec_off_name }}" placeholder="Enter Recruitment Office Name...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_rec_off_arname" class="form-label">Recruitment Arabic Office Name <span class="text-danger">*</span></label>
                                        <input type="text" name="portal_rec_off_arname" id="portal_rec_off_arname" class="form-control" value="{{ $post->portal_rec_off_arname }}" placeholder="Enter Recruitment Office Name...">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_email" class="form-label">Email</label>
                                        <input type="text" name="portal_email" id="portal_email" class="form-control" value="{{ $post->portal_email }}" placeholder="Enter Primary Email...">
                                    </div>
                                    <!-- Add other input fields here -->
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_add_for_cust" class="form-label">Address For Customer</label>
                                        <textarea id="portal_add_for_cust" name="portal_add_for_cust" class="form-control" rows="2" placeholder="Enter Address For Customer...">{{ $post->portal_add_for_cust }}</textarea>
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label for="portal_add_disp_only" class="form-label">Display Address (English)</label>
                                        <textarea id="portal_add_disp_only" name="portal_add_disp_only" class="form-control" rows="2" placeholder="Enter Address Display Only...">{{ $post->portal_add_disp_only }}</textarea>
                                    </div>
                                    <div class="mb-3 mr-3 col-md-4">
                                        <label for="portal_ar_add_disp_only" class="form-label">Display Address (Arabic)</label>
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
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label" for="partner_calling_number">Partner Calling Number</label>
                                        <input type="text"
                                            class="form-control"
                                            id="partner_calling_number"
                                            name="partner_calling_number"
                                            value="{{ old('partner_calling_number', $post->partner_calling_number ?? '') }}"
                                            placeholder="Enter Partner Calling Number">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label" for="partner_whatsapp_number">Partner WhatsApp Number</label>
                                        <input type="text"
                                            class="form-control"
                                            id="partner_whatsapp_number"
                                            name="partner_whatsapp_number"
                                            value="{{ old('partner_whatsapp_number', $post->partner_whatsapp_number ?? '') }}"
                                            placeholder="Enter Partner WhatsApp umber">
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="partner_careoff" class="form-label">Partner careoff</label>
                                        <select name="partner_careoff_id" id="partner_careoff_id" class="form-select select22" data-allow-clear="true">
                                            <option value="">Select</option>
                                            @foreach ($users as $careoffs)
                                                <option value="{{ $careoffs->id }}" @if($post->partner_careoff_id == $careoffs->id) selected @endif>{{ $careoffs->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm me-2 float-end waves-effect waves-light">Save Changes</button>
                                </div>
                            </form>
                        </div>

                        <!-- RecruitmentCV Domain - separate table/model/controller from the
                             "Domain"/"Website" tabs above (App\Models\PartnerWebsiteDomain,
                             not App\Models\Domain). Do not merge these. -->
                        <div class="tab-pane fade" id="navs-pills-partnerwebsite" role="tabpanel">
                            <form id="partnerWebsiteForm">
                                @csrf
                                <input type="hidden" name="id" id="pw_id" value="{{ $partnerWebsiteDomain->id ?? '' }}">
                                <input type="hidden" name="partner_id" value="{{ $post->id }}">

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label for="pw_domain" class="form-label">Subdomain</label>
                                        <div class="input-group">
                                            <input
                                                type="text"
                                                name="domain"
                                                id="pw_domain"
                                                class="form-control"
                                                placeholder="raha.recruitmentcv.com"
                                                value="{{ $partnerWebsiteDomain->domain ?? '' }}">
                                        </div>
                                        <div class="form-text">Must be a *.recruitmentcv.com subdomain, e.g. raha.recruitmentcv.com</div>
                                    </div>

                                    <div class="mb-3 col-md-3">
                                        <label for="pw_status" class="form-label">Status</label>
                                        <select name="status" id="pw_status" class="form-select">
                                            <option value="active" @if(($partnerWebsiteDomain->status ?? '') == 'active') selected @endif>Active</option>
                                            <option value="inactive" @if(($partnerWebsiteDomain->status ?? 'inactive') == 'inactive') selected @endif>Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="pwSubmitBtn">
                                        {{ isset($partnerWebsiteDomain) ? 'Update Domain' : 'Add Domain' }}
                                    </button>
                                </div>
                            </form>

                            <hr class="my-4">

                            <form id="partnerWebsiteLogoForm">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">English Logo</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <img
                                                src="{{ !empty($partnerWebsiteDomain->english_logo) ? asset('admin/assets/images/partnerwebsite/'.$partnerWebsiteDomain->english_logo) : asset('admin/assets/img/avatars/blank.jpeg') }}"
                                                class="pw-preview-en rounded" width="80" height="80" alt="English logo preview">
                                            <input type="file" class="form-control pw-input-en" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Arabic Logo</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <img
                                                src="{{ !empty($partnerWebsiteDomain->arabic_logo) ? asset('admin/assets/images/partnerwebsite/'.$partnerWebsiteDomain->arabic_logo) : asset('admin/assets/img/avatars/blank.jpeg') }}"
                                                class="pw-preview-ar rounded" width="80" height="80" alt="Arabic logo preview">
                                            <input type="file" class="form-control pw-input-ar" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light" id="pwLogoSubmitBtn" @if(empty($partnerWebsiteDomain)) disabled @endif>
                                        Save Logos
                                    </button>
                                    @if(empty($partnerWebsiteDomain))
                                        <div class="form-text">Save the subdomain above first.</div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Service Charge Page Start -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="addServiceCharge" aria-labelledby="addServiceChargeLabel">
        <div class="offcanvas-header">
            <h5 id="addServiceChargeLabel" class="offcanvas-title">Add Service Charge</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="addServiceChargeForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="partner_id" value="{{ $post->id }}">
                        <div class="mb-3">
                            <label for="add-givenby-id" class="form-label">Given By <span class="text-danger">*</span></label>
                            <select name="givenby_id" id="add-givenby-id" class="form-select select22" data-placeholder="Select Given By" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="add-profession-id" class="form-label">Profession <span class="text-danger">*</span></label>
                            <select name="profession_id" id="add-profession-id" class="form-select select22" data-placeholder="Select Profession" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($professions as $profession)
                                    <option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="add-amount">Service Charge <span class="text-danger">*</span></label>
                            <input type="text" name="service_charge" id="add-amount" value="" class="form-control" placeholder="Enter amount...">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="add-notes-charge" class="form-label">Notes</label>
                            <textarea name="notes" id="add-notes-charge" cols="30" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editServiceChargeBtn">Submit</button> --}}
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit" id="editServiceChargeBtn">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>
    <!-- end Service Charge Page End-->

    <!-- Add Service Charge Page Start -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="editServiceCharge" aria-labelledby="editServiceChargeLabel">
        <div class="offcanvas-header">
            <h5 id="editServiceChargeLabel" class="offcanvas-title">Add Service Charge</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="editServiceChargeForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="partner_id" value="{{ $post->id }}">
                        <input type="hidden" name="sc_id" id="editsc_id">
                        <div class="mb-3">
                            <label for="edit-givenby-id" class="form-label">Given By <span class="text-danger">*</span></label>
                            <select name="givenby_id" id="edit-givenby-id" class="form-select select22" data-placeholder="Select Given By" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($users as $user2)
                                <option value="{{ $user2->id }}">{{ $user2->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="edit-profession-id" class="form-label">Profession <span class="text-danger">*</span></label>
                            <select name="profession_id" id="edit-profession-id" class="form-select select22" data-placeholder="Select Profession" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($professions as $profession2)
                                    <option value="{{ $profession2->id }}">{{ $profession2->eng_name.' ('.$profession2->ar_name.')' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="edit-amount">Service Charge <span class="text-danger">*</span></label>
                            <input type="text" name="service_charge" id="edit-amount"  class="form-control" placeholder="Enter amount...">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="edit-notes-charge" class="form-label">Notes</label>
                            <textarea name="notes" id="edit-notes-charge" cols="30" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editServiceChargeBtn">Submit</button> --}}
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit" id="editeServiceChargeBtn">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>
    <!-- end Service Charge Page End-->



    <!-- Active Employer Start -->
    <div class="modal fade" id="activemp" aria-hidden="true" aria-labelledby="activempLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-l">
            <form action="{{ route('admin.partner.addsercharge.updateStatus') }}" method="POST" id="activeSerChargeForm">
                @csrf
                <input type="hidden" name="sc_id" id="activempEmpID">
                <input type="hidden" name="sc_status" value="1">
                <div class="modal-content">
                    <div class="modal-header pb-2">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Active Service Charge</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-danger">Are you sure you want to active this Service Charge?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal">No</button>
                        {{-- <button type="button" class="btn btn-sm btn-danger" id="confirmDelete">Yes</button> --}}
                        <button type="submit" class="btn btn-sm btn-danger" id="confirmDelete">Yes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Active Employer End -->

    <!-- Inactive Employer Start -->
    <div class="modal fade" id="inactivemp" aria-hidden="true" aria-labelledby="inactivempLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-l">
            <form action="{{ route('admin.partner.addsercharge.updateStatus') }}" method="POST" id="deactiveSerChargeForm">
                @csrf
                <input type="hidden" name="sc_id" id="inactivempEmpID">
                <input type="hidden" name="sc_status" value="0">
                <div class="modal-content">
                    <div class="modal-header pb-2">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Inactive Service Charge</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-danger">Are you sure you want to inactive this Service Charge?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal">No</button>
                        {{-- <button type="button" class="btn btn-sm btn-danger" id="confirmDelete">Yes</button> --}}
                        <button type="submit" class="btn btn-sm btn-danger" id="confirmDelete">Yes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Inactive Employer End -->

    <!-- Change Partner Registration Status Start -->
    <div class="modal fade" id="changeRegistrationStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
           <div class="modal-content">
              <div class="modal-header">
                 <h5 class="modal-title">Change Registration Status</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form action="{{ route('admin.partner.registrationstatusUpdate') }}" method="POST">
                 @csrf
                 <input type="hidden" name="partner_id" id="regstatus_id" value="{{ $post->id }}">
                 <div class="modal-body">
                    <div class="form-check form-check-inline mt-3">
                       <input class="form-check-input" type="radio" name="registration_status" id="pendingRegStatus" value="0" @if(($post->registration_status ?? 0) == 0) checked @endif/>
                       <label class="form-check-label" for="pendingRegStatus">Pending</label>
                    </div>
                    <div class="form-check form-check-inline">
                       <input class="form-check-input" type="radio" name="registration_status" id="approvedRegStatus" value="1" @if(($post->registration_status ?? 0) == 1) checked @endif/>
                       <label class="form-check-label" for="approvedRegStatus">Approved</label>
                    </div>
                    <div class="form-check form-check-inline">
                       <input class="form-check-input" type="radio" name="registration_status" id="rejectedRegStatus" value="2" @if(($post->registration_status ?? 0) == 2) checked @endif/>
                       <label class="form-check-label" for="rejectedRegStatus">Rejected</label>
                    </div>
                 </div>
                 <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Yes</button>
                 </div>
              </form>
           </div>
        </div>
    </div>
    <!-- Change Partner Registration Status End -->

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
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>

    <!-- Validation Page -->
    <script src="{{ asset('admin/assets/pages/validation/partner-validation.js') }}"></script>

    <script>
        var select2 = $('.select22');
        if (select2.length) {
            select2.each(function () {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>');
                $this.select2({
                    dropdownParent: $this.parent()
                });
            });
        }

    </script>

    <script>
        $(document).ready(function(){



            $('#editServiceCharge').on('show.bs.offcanvas',function(e){
                var edit_id = $(e.relatedTarget).data('id');

                $('#editsc_id').val(edit_id);

                jQuery.ajax({
                    url: "{{ route('admin.partner.addsercharge.edit') }}",
                    method: "get",
                    type: "html",
                    data: {
                        id: edit_id
                    },

                    success: function(data){

                        $('#edit-givenby-id').val(data.givenby_id).change();
                        $('#edit-profession-id').val(data.profession_id).change();
                        $('#edit-amount').val(data.service_charge);
                        $('#edit-notes-charge').val(data.notes);




                        var select2ae1 = $('.select22');
                        if (select2ae1.length) {
                            select2ae1.each(function () {
                                var $this = $(this);
                                // $this.select2({
                                $this.wrap('<div class="position-relative"></div>').select2({
                                    dropdownParent: $this.parent()
                                });
                            });
                        }
                    }
                });

            });

            $('#activemp').on('show.bs.modal',function(e){
                var edit_id = $(e.relatedTarget).data('id');

                $('#activempEmpID').val(edit_id);
            });

            $('#inactivemp').on('show.bs.modal',function(e){
                var edit_id = $(e.relatedTarget).data('id');

                $('#inactivempEmpID').val(edit_id);
            });


            // $('#activeSerChargeForm').on('submit',function(){
            //     let formData = new FormData(this);

            //     $.ajax({
            //             url: "{{ route('admin.partner.addsercharge.updateStatus') }}",
            //             method: "POST",
            //             type: "html",
            //             data: formData,
            //             processData: false,
            //             contentType: false,
            //             success: function(data){
            //                 if (data.status == 1) {
            //                     toastr['success'](data.respnseMsg, 'Success', { hideDuration: 3000 });
            //                     $('#activeSerChargeForm')[0].reset();
            //                     $('#activemp').modal('hide');
            //                     $('#nav-service-charge-div').load(' #nav-service-charge-div');

            //                 } else {
            //                     toastr['error'](data.respnseMsg, 'Error', { hideDuration: 3000 });
            //                 }
            //             }
            //         });


            // });

            // $('#deactiveSerChargeForm').on('submit',function(){
            //     let formData = new FormData(this);

            //     $.ajax({
            //             url: "{{ route('admin.partner.addsercharge.updateStatus') }}",
            //             method: "POST",
            //             type: "html",
            //             data: formData,
            //             processData: false,
            //             contentType: false,
            //             success: function(data){
            //                 if (data.status == 1) {
            //                     toastr['success'](data.respnseMsg, 'Success', { hideDuration: 3000 });
            //                     $('#deactiveSerChargeForm')[0].reset();
            //                     $('#inactivemp').modal('hide');
            //                     $('#nav-service-charge-div').load(' #nav-service-charge-div');

            //                 } else {
            //                     toastr['error'](data.respnseMsg, 'Error', { hideDuration: 3000 });
            //                 }
            //             }
            //         });


            // });



            // Add Service Charge
            $('#addServiceChargeForm').validate({
                rules: {
                    givenby_id: {
                        required: true
                    },
                    profession_id:{
                        required: true
                    },
                    service_charge:{
                        required: true,
                        digits: true
                    },

                },
                messages: {
                    givenby_id: {
                        required: "Select Given By"
                    },
                    profession_id:{
                        required: "Select Profession"
                    },
                    service_charge:{
                        required: "Please enter service charge",
                        digits: "Please enter valid amount"
                    },
                },
                submitHandler: function (form) {
                    let formData = new FormData(form);



                    $.ajax({
                        url: "{{ route('admin.partner.addsercharge') }}",
                        method: "POST",
                        type: "html",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data){
                            if (data.status == 1) {
                                toastr['success'](data.respnseMsg, 'Success', { hideDuration: 3000 });
                                $('#addServiceChargeForm')[0].reset();
                                $('#addServiceChargeForm select').val('').trigger('change');
                                $('#addServiceCharge').offcanvas('hide');
                                $('#nav-service-charge-div').load(' #nav-service-charge-div');

                            } else {
                                toastr['error'](data.respnseMsg, 'Error', { hideDuration: 3000 });
                            }
                        }
                    });

                }
            });


            // Edit Service Charge
            $('#editServiceChargeForm').validate({
                rules: {
                    givenby_id: {
                        required: true
                    },
                    profession_id:{
                        required: true
                    },
                    service_charge:{
                        required: true,
                        digits: true
                    },

                },
                messages: {
                    givenby_id: {
                        required: "Select Given By"
                    },
                    profession_id:{
                        required: "Select Profession"
                    },
                    service_charge:{
                        required: "Please enter service charge",
                        digits: "Please enter valid amount"
                    },
                },
                submitHandler: function (form) {
                    let formData = new FormData(form);



                    $.ajax({
                        url: "{{ route('admin.partner.addsercharge.update') }}",
                        method: "POST",
                        type: "html",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data){
                            if (data.status == 1) {
                                toastr['success'](data.respnseMsg, 'Success', { hideDuration: 3000 });
                                $('#editServiceChargeForm')[0].reset();
                                $('#editServiceChargeForm select').val('').trigger('change');
                                $('#editServiceCharge').offcanvas('hide');
                                $('#nav-service-charge-div').load(' #nav-service-charge-div');

                            } else {
                                toastr['error'](data.respnseMsg, 'Error', { hideDuration: 3000 });
                            }
                        }
                    });

                }
            });

        });
    </script>

    <script>
        $(document).ready(function () {

            $('input[name="domain_name"]').on('input', function () {
                this.value = this.value.toLowerCase();
            });

            $.validator.addMethod("validDomain", function (value, element) {
                return this.optional(element) || /^(?!https?:\/\/)([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/.test(value);
            }, "Please enter a valid domain (e.g. apnafly.com)");

            // Generate DNS
            $('#generateDnsBtn').on('click', function () {

                let domain = $('#domain_name').val();

                if (domain == '') {
                    toastr.error('Please enter domain name');
                    return;
                }

                $.ajax({

                    url: "{{ route('admin.domains.generate.dns') }}",
                    method: "POST",

                    data: {
                        _token: "{{ csrf_token() }}",
                        domain_name: domain
                    },

                    success: function (res) {

                        $('#dnsInfoBox').removeClass('d-none');

                        let rows = '';

                        $.each(res.data.dns_records, function (index, record) {

                            rows += `
                                <tr>
                                    <td>${record.type}</td>
                                    <td>${record.host}</td>
                                    <td><strong>${record.value}</strong></td>
                                    <td>${record.ttl}</td>
                                </tr>
                            `;
                        });

                        $('#dnsRecordsBody').html(rows);

                        toastr.success('DNS records generated successfully');

                    },

                    error: function (xhr) {

                        let message = 'Failed to generate DNS';

                        if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        toastr.error(message);
                    }

                });

            });

            // Validation
            $('#domainForm').validate({

                rules: {
                    domain_name: {
                        required: true,
                        validDomain: true
                    }
                },

                messages: {
                    domain_name: {
                        required: "Domain name is required",
                        validDomain: "Only domain allowed (no http/https)"
                    }
                },

                submitHandler: function (form) {

                    let formData = $(form).serialize();

                    let isUpdate = $('#domain_id').val() !== '';

                    $.ajax({

                        url: "{{ route('admin.domains.store') }}",
                        method: "POST",
                        data: formData,

                        success: function (res) {

                            toastr.success(res.message);

                            if (!isUpdate) {
                                // $('#domainForm')[0].reset();
                                $('#dnsInfoBox').addClass('d-none');
                            }

                        },

                        error: function (xhr) {

                            let errors = xhr.responseJSON.errors;

                            let msg = '';

                            $.each(errors, function (key, value) {
                                msg += value[0] + "<br>";
                            });

                            toastr.error(msg);

                        }

                    });

                }

            });

            $('#logoForm').on('submit', function (e) {
                e.preventDefault();

                let id = $('input[name="id"]').val();

                let fileEn = $('input[name="website_logo"]')[0].files[0];
                let fileAr = $('input[name="website_logo_ar"]')[0].files[0];

                let data = {
                    _token: "{{ csrf_token() }}",
                    id: id
                };

                let readers = [];

                // English logo
                if (fileEn) {
                    let reader = new FileReader();
                    readers.push(new Promise(resolve => {
                        reader.onload = function(e){
                            data.website_logo = e.target.result;
                            resolve();
                        }
                        reader.readAsDataURL(fileEn);
                    }));
                }

                // Arabic logo
                if (fileAr) {
                    let reader = new FileReader();
                    readers.push(new Promise(resolve => {
                        reader.onload = function(e){
                            data.website_logo_ar = e.target.result;
                            resolve();
                        }
                        reader.readAsDataURL(fileAr);
                    }));
                }

                Promise.all(readers).then(() => {

                    $.ajax({
                        url: "{{ route('admin.domains.websitelogoupdt',$post->id) }}",
                        method: "POST",
                        data: data,

                        success: function (res) {
                            toastr.success(res.message);
                        },

                        error: function (xhr) {
                            let errors = xhr.responseJSON.errors;
                            let msg = '';

                            $.each(errors, function (key, value) {
                                msg += value[0] + "<br>";
                            });

                            toastr.error(msg);
                        }
                    });

                });

            });

            $('#addressForm').on('submit', function (e) {
                e.preventDefault();

                let data = {
                    _token: "{{ csrf_token() }}",
                    id: $('input[name="id"]').val(),
                    company_name: $('input[name="company_name"]').val(),
                    company_name_ar: $('input[name="company_name_ar"]').val(),
                    company_address: $('textarea[name="company_address"]').val(),
                    company_address_ar: $('textarea[name="company_address_ar"]').val(),
                    company_mobile: $('input[name="company_mobile"]').val(),
                    company_email: $('input[name="company_email"]').val()
                };

                $.ajax({
                    url: "{{ route('admin.domains.address.update') }}",
                    method: "POST",
                    data: data,

                    success: function (res) {
                        toastr.success(res.message);
                    },

                    error: function (xhr) {

                        let errors = xhr.responseJSON?.errors;
                        let msg = '';

                        if (errors) {
                            $.each(errors, function (key, value) {
                                msg += value[0] + "<br>";
                            });
                        } else {
                            msg = "Something went wrong";
                        }

                        toastr.error(msg);
                    }
                });
            });

        });

        function deleteDomain(id, element)
        {
            if(!confirm('Remove connected domain?')){
                return;
            }

            let icon = $(element).find('i');

            $.ajax({

                url: "{{ route('admin.domains.delete') }}",

                method: "POST",

                data: {

                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: id
                },

                beforeSend: function(){

                    icon.removeClass('fa-trash-alt')
                        .addClass('fa-spinner fa-spin');

                },

                success: function(response){

                    icon.removeClass('fa-spinner fa-spin')
                        .addClass('fa-trash-alt');

                    if(response.success){

                        toastr.success(response.message);

                        location.reload();

                    }else{

                        toastr.error(response.message);

                    }

                },

                error: function(xhr){

                    icon.removeClass('fa-spinner fa-spin')
                        .addClass('fa-trash-alt');

                    let message='Something went wrong';

                    if(xhr.responseJSON?.message){

                        message=xhr.responseJSON.message;

                    }

                    toastr.error(message);

                }

            });

        }
    </script>

    <script>
        // RecruitmentCV Domain tab - deliberately isolated jQuery below (pw*
        // ids/classes only) so it can't collide with the existing Domain/
        // Website tab scripts elsewhere on this page.
        $('#partnerWebsiteForm').on('submit', function (e) {
            e.preventDefault();

            var $btn = $('#pwSubmitBtn');
            $btn.prop('disabled', true);

            $.ajax({
                method: 'POST',
                url: "{{ route('admin.partnerwebsite.store') }}",
                data: $(this).serialize(),
                success: function (res) {
                    toastr.success(res.message || 'Saved');
                    location.reload();
                },
                error: function (xhr) {
                    var message = 'Something went wrong';

                    if (xhr.responseJSON?.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON?.message) {
                        message = xhr.responseJSON.message;
                    }

                    toastr.error(message);
                    $btn.prop('disabled', false);
                }
            });
        });

        $('#partnerWebsiteLogoForm').on('submit', function (e) {
            e.preventDefault();

            var partnerId = {{ $post->id }};
            var $btn = $('#pwLogoSubmitBtn');
            var enFile = $('.pw-input-en')[0].files[0];
            var arFile = $('.pw-input-ar')[0].files[0];

            if (!enFile && !arFile) {
                toastr.error('Choose at least one logo to upload');
                return;
            }

            var readers = [];
            var payload = {
                _token: "{{ csrf_token() }}"
            };

            function readAsDataUrl(file, key) {
                return new Promise(function (resolve) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        payload[key] = e.target.result;
                        resolve();
                    };
                    reader.readAsDataURL(file);
                });
            }

            if (enFile) readers.push(readAsDataUrl(enFile, 'english_logo'));
            if (arFile) readers.push(readAsDataUrl(arFile, 'arabic_logo'));

            $btn.prop('disabled', true);

            Promise.all(readers).then(function () {
                $.ajax({
                    method: 'POST',
                    url: "{{ url('admin/partnerwebsite/logo/update') }}/" + partnerId,
                    data: payload,
                    success: function (res) {
                        toastr.success(res.message || 'Logos updated');

                        if (res.data?.english_logo) {
                            $('.pw-preview-en').attr('src', "{{ asset('admin/assets/images/partnerwebsite/') }}/" + res.data.english_logo + '?t=' + Date.now());
                        }
                        if (res.data?.arabic_logo) {
                            $('.pw-preview-ar').attr('src', "{{ asset('admin/assets/images/partnerwebsite/') }}/" + res.data.arabic_logo + '?t=' + Date.now());
                        }

                        $btn.prop('disabled', false);
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
