@extends('layout.admin.admin_layout')

@section('title','Associate')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/pickr/pickr-themes.css') }}" />


@endsection

@if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->activedeactive_associate == 1))
    <script>
        var activePermi = "1";
    </script>
@else
    <script>
        var activePermi = "0";
    </script>
@endif

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-4 mb-4">


            <div class="col-sm-6 col-xl-3 new-candidate-div">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="mb-0 me-2">1000</h5>
                                <small>New Candidate</small>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-success rounded-pill p-2">
                                <i class="ti ti-server ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="col-sm-6 col-xl-3 ready-for-publish-div">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="mb-0 me-2">500</h5>
                                <small>Ready For Publish</small>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-success rounded-pill p-2">
                                <i class="ti ti-server ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            {{-- <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Session</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">21,459</h4>
                        <span class="text-success">(+29%)</span>
                        </div>
                        <span>Total Users</span>
                    </div>
                    <span class="badge bg-label-primary rounded p-2">
                        <i class="ti ti-user ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Paid Users</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">4,567</h4>
                        <span class="text-success">(+18%)</span>
                        </div>
                        <span>Last week analytics </span>
                    </div>
                    <span class="badge bg-label-danger rounded p-2">
                        <i class="ti ti-user-plus ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Active Users</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">19,860</h4>
                        <span class="text-danger">(-14%)</span>
                        </div>
                        <span>Last week analytics</span>
                    </div>
                    <span class="badge bg-label-success rounded p-2">
                        <i class="ti ti-user-check ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Pending Users</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">237</h4>
                        <span class="text-success">(+42%)</span>
                        </div>
                        <span>Last week analytics</span>
                    </div>
                    <span class="badge bg-label-warning rounded p-2">
                        <i class="ti ti-user-exclamation ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div> --}}
        </div>
        <!-- Users List Table -->
        @php
            $job_types = DB::table('candidates')->select('job_type')->groupBy('job_type')->where('job_type','!=','')->get();
            $createbys = DB::table('candidates')->select('admin_id')->groupBy('admin_id')->where('admin_id','!=','')->get();
            $religions = DB::table('candidates')->select('religion')->groupBy('religion')->where('religion','!=','')->get();
            // $regions = DB::table('candidates')->select('region_id')->groupBy('region_id')->where('region_id','!=','')->get();
            $citiesfs = DB::table('candidates')->select('candcity_id')->groupBy('candcity_id')->where('candcity_id','!=','')->get();
            $gulfexperiences = DB::table('candidates')->select('gulfexperience')->groupBy('gulfexperience')->where('gulfexperience','!=','')->get();
            $publishes = DB::table('candidates')->select('publish')->groupBy('publish')->where('publish','!=','')->get();
        @endphp
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Search Filter</h5>
                {{-- <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0"> --}}
                <div class="row">
                    {{-- <div class="col-md user_role"></div>
                    <div class="col-md user_plan"></div>
                    <div class="col-md user_status"></div> --}}

                    <div class="col-md-3 mb-3 pass-type-div" >
                        <select name="" id="by-pass-type" class="form-select select22">
                            <option value="">Passport Type</option>
                            <option value="ECNR">ECNR</option>
                            <option value="ECR">ECR</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 job-type-div">
                        <select name="" id="by-job-type" class="form-select select22">
                            <option value="">Job Type</option>
                            @foreach ($job_types as $job_type)
                                <option value="{{ $job_type->job_type }}">{{ $job_type->job_type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 create-by-div">
                        <select name="" id="by-create-by" class="form-select select22">
                            <option value="">Created By</option>
                            @foreach ($createbys as $createby)
                                @php
                                    $userF = DB::table('admins')->where('id','=',$createby->admin_id)->first();
                                @endphp
                                <option value="{{ $userF->id }}">{{ $userF->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 religion-div">
                        <select name="" id="by-religion" class="form-select select22">
                            <option value="">Religion</option>
                            @foreach ($religions as $religion)
                                <option value="{{ $religion->religion }}">{{ $religion->religion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 city-div">
                        <select name="" id="by-city" class="form-select select22">
                            <option value="">City</option>
                            @foreach ($citiesfs as $citiesf)
                                @php
                                    $cityF = DB::table('cities')->where('id','=',$citiesf->candcity_id)->first();
                                @endphp
                                <option value="{{ $cityF->id }}">{{ $cityF->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 region-div">
                        <select name="" id="by-region" class="form-select select22">
                            <option value="">Region</option>

                        </select>
                    </div>

                    <div class="col-md-3 mb-3 experience-region-div">
                        <select name="" id="by-experience-region" class="form-select select22">
                            <option value="">Experience Region</option>
                            @foreach ($gulfexperiences as $gulfexperience)
                                <option value="{{ $gulfexperience->gulfexperience }}">@if($gulfexperience->gulfexperience == 1) {{ 'Indian Experience' }} @elseif($gulfexperience->gulfexperience == 2) {{ 'Gulf Experience' }} @endif</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 publish-status-div">
                        <select name="" id="by-publish-status" class="form-select select22">
                            <option value="">Publish Status</option>
                            @foreach ($publishes as $publish)
                                <option value="{{ $publish->publish }}">@if($publish->publish == 1) {{ 'Publish' }} @else {{ 'Unpublish' }} @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 candidate-status-div">
                        <select name="" id="by-candidate-status" class="form-select select22">
                            <option value="">Candidate Status</option>

                        </select>
                    </div>
                    <div class="col-md-3 mb-3 created-date-div">
                        <input type="text" name="" id="by-created-date" class="form-control createdate-picker" placeholder="Created date...">
                    </div>
                    <div class="col-md-3 mb-3 medical-expiry-div">
                        <input type="text" name="" id="by-medical-expiry-date" class="form-control medicalexpiry-picker" placeholder="Medical expiry date...">
                    </div>

                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Agency Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>City</th>
                            <th>Careoff</th>
                            <th>Create By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!--- Add Candidate Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Associate</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.associate.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="add-name" class="form-control" placeholder="Enter name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-agnecy-name">Agency Name</label>
                                <input type="text" name="pty_ag_name" id="add-agnecy-name" class="form-control" placeholder="Enter agency name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-pty-email" class="form-label">Email</label>
                                <input type="text" name="pty_email" id="add-pty-email" class="form-control" placeholder="Enter party email...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-pty-mobile" class="form-label">Primary Mobile</label>
                                <input type="text" name="pty_mobile" id="add-pty-mobile" class="form-control" placeholder="Enter party mobile...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-sec-mob-no" class="form-label">Secondary Mobile</label>
                                <input type="text" name="sec_mob_no" id="add-sec-mob-no" class="form-control" placeholder="Enter secondary mobile no...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-care-off">Careoff</label>
                                <select name="careoff_id" id="add-care-off" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-address">Address</label>
                                <input type="text" class="form-control" id="add-address" name="address" placeholder="Enter address...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-country-id">Country</label>
                                <select name="country_id" id="add-country-id" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-region" class="form-label">Region</label>
                                <select name="region_id" id="add-region" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-city-id">City</label>
                                <select name="city_id" id="add-city-id" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Add Candidate End --->

        <!--- Edit Candidate Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel">
            <div class="offcanvas-header">
                <h5 id="edituserLabel" class="offcanvas-title">Edit Associate</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.associate.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" name="edit_ID" id="edit_ID">
                            <div class="mb-3">
                                <label class="form-label" for="edit-name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit-name" class="form-control" placeholder="Enter name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-agnecy-name">Agency Name</label>
                                <input type="text" name="pty_ag_name" id="edit-agnecy-name" class="form-control" placeholder="Enter candidate name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-pty-email" class="form-label">Email</label>
                                <input type="text" name="pty_email" id="edit-pty-email" class="form-control" placeholder="Enter Party email...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-pty-mobile" class="form-label">Primary Mobile</label>
                                <input type="text" name="pty_mobile" id="edit-pty-mobile" class="form-control" placeholder="Enter party mobile...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-sec-mob-no" class="form-label">Secondary Mobile</label>
                                <input type="text" name="sec_mob_no" id="edit-sec-mob-no" class="form-control" placeholder="Enter secondary mobile number...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-care-off">Careoff</label>
                                <select name="careoff_id" id="edit-care-off" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($users as $user2)
                                        <option value="{{ $user2->id }}">{{ $user2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-address">Address</label>
                                <input type="text" class="form-control" id="edit-address" name="address" placeholder="Enter address...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-country-id">Country</label>
                                <select name="country_id" id="edit-country-id" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country2)
                                        <option value="{{ $country2->id }}">{{ $country2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-region" class="form-label">Region</label>
                                <select name="region_id" id="edit-region" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region->id }}">{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-city-id">City</label>
                                <select name="city_id" id="edit-city-id" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city2)
                                        <option value="{{ $city2->id }}">{{ $city2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Candidate End --->

        <!-- Filter List Start-->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="filter" aria-labelledby="filterLabel">
            <div class="offcanvas-header">
                <h5 id="filterLabel" class="offcanvas-title">Add Filter</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <div class="row">
                    <div class="col-md-12">
                        <div class="all-check">
                            <div class="form-check mt-2" id="pass-type-f">
                                <input class="form-check-input" type="checkbox" name="pass-typef" value="1" id="pass-typef" @if(isset($filter_user) && $filter_user->pass_type_filter == 1) checked @endif />
                                <label class="form-check-label" for="pass-typef"> Passport Type</label>
                            </div>
                            <div class="form-check mt-2" id="job-type-f">
                                <input class="form-check-input" type="checkbox" name="job-typef" value="1" id="job-typef" @if(isset($filter_user) && $filter_user->job_type_filter == 1) checked @endif />
                                <label class="form-check-label" for="job-typef"> Occupation</label>
                            </div>
                            <div class="form-check mt-2" id="create-by-f">
                                <input class="form-check-input" type="checkbox" name="create-byf" value="1" id="create-byf" @if(isset($filter_user) && $filter_user->create_by_filter == 1) checked @endif />
                                <label class="form-check-label" for="create-byf"> Create By</label>
                            </div>
                            <div class="form-check mt-2" id="create-date-f">
                                <input class="form-check-input" type="checkbox" name="create-datef" value="1" id="create-datef" @if(isset($filter_user) && $filter_user->create_date_filter == 1) checked @endif />
                                <label class="form-check-label" for="create-datef"> Create Date</label>
                            </div>
                            <div class="form-check mt-2" id="religion-f">
                                <input class="form-check-input" type="checkbox" name="religionf" value="1" id="religionf" @if(isset($filter_user) && $filter_user->religion_filter == 1) checked @endif />
                                <label class="form-check-label" for="religionf"> Religion</label>
                            </div>
                            <div class="form-check mt-2" id="region-f">
                                <input class="form-check-input" type="checkbox" name="regionf" value="1" id="regionf" @if(isset($filter_user) && $filter_user->region_filter == 1) checked @endif />
                                <label class="form-check-label" for="regionf"> Region</label>
                            </div>
                            <div class="form-check mt-2" id="city-f">
                                <input class="form-check-input" type="checkbox" name="cityf" value="1" id="cityf" @if(isset($filter_user) && $filter_user->city_filter == 1) checked @endif />
                                <label class="form-check-label" for="cityf"> City</label>
                            </div>
                            <div class="form-check mt-2" id="experience-region-f">
                                <input class="form-check-input" type="checkbox" name="experience-regionf" value="1" id="experience-regionf" @if(isset($filter_user) && $filter_user->experience_region_filter == 1) checked @endif />
                                <label class="form-check-label" for="experience-regionf"> Experience Region</label>
                            </div>
                            <div class="form-check mt-2" id="medical-expiry-date-f">
                                <input class="form-check-input" type="checkbox" name="medical-expiry-datef" value="1" id="medical-expiry-datef" @if(isset($filter_user) && $filter_user->medical_expiry_date_filter == 1) checked @endif />
                                <label class="form-check-label" for="medical-expiry-datef"> Medical Expiry Date</label>
                            </div>
                            <div class="form-check mt-2" id="candidate-status-f">
                                <input class="form-check-input" type="checkbox" name="candidate-statusf" value="1" id="candidate-statusf" @if(isset($filter_user) && $filter_user->candidate_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="candidate-statusf"> Candidate Status</label>
                            </div>
                            <div class="form-check mt-2" id="publish-status-f">
                                <input class="form-check-input" type="checkbox" name="publish-statusf" value="1" id="publish-statusf" @if(isset($filter_user) && $filter_user->publish_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="publish-statusf"> Publish Status</label>
                            </div>
                            <div class="form-check mt-2" id="new-candidate-status-f">
                                <input class="form-check-input" type="checkbox" name="new-candidate-statusf" value="1" id="new-candidate-statusf" @if(isset($filter_user) && $filter_user->new_candidate_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="new-candidate-statusf"> New Candidate</label>
                            </div>
                            <div class="form-check mt-2" id="ready-for-published-status-f">
                                <input class="form-check-input" type="checkbox" name="ready-for-published-statusf" value="1" id="ready-for-published-statusf" @if(isset($filter_user) && $filter_user->ready_for_published_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="ready-for-published-statusf"> Ready for Published</label>
                            </div>
                            <div class="mt-3">
                                <a href="#" id="all-chk"><span class="badge bg-label-primary">Select all</span></a>
                                <a href="#" id="all-unchk"><span class="badge bg-label-primary">Unselect all</span></a>
                                <a href="#" id="default-chk"><span class="badge bg-label-primary">Basic</span></a>
                                <a href="#" id="update-chk"><span class="badge bg-label-primary">Update</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filter List End -->

        <!-- Delete Staff start -->
        <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.associate.delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="assoc_id" id="delStaffID">
                    <div class="modal-body">
                    <div class="row">
                        <div class="col mb-12">
                        <p>Are you sure!, to delete candidate?</p>
                        </div>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal"> Close</button>
                    <button type="submit" class="btn btn-danger btn-sm " id="disbtn">Delete</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Delete Staff end -->
        <!-- Update Published Status Start -->
        <div class="modal fade" id="publish" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel125">Update Published Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.candidate.bulk.published.update') }}" method="POST" id="updatepublishstatus">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="idsv" id="idsv">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="publish" id="published" value="1"/>
                                        <label class="form-check-label" for="published">Publish</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="publish" id="unpublished" value="0"/>
                                        <label class="form-check-label" for="unpublished">Unpublish</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p>Total publish is: <span id="countcan"></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Update Published Status end -->
        <!-- Download CV as per company Start -->
        <div class="modal fade" id="downloadcv" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel125">Download CV as per company</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="POST" id="downloadCVvalidation2">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                @php
                                    $partners = DB::table('partners')->get();
                                @endphp
                                <div class="col-md-12 mb-3">
                                    <input type="hidden" name="candcv_id" id="candcv_id" >
                                    <label for="partner_id" class="form-label">Office Name <span class="text-danger">*</span></label>
                                    <select name="partner_id" id="partner_id" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}">{{ $partner->rec_off_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger" id="errorPartner"></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            {{-- <button type="submit" class="btn btn-primary" id="downloadCandCV">Download Cv</button> --}}
                            <button type="button" class="btn btn-primary btn-sm" id="downloadCvbtn">Download CV</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Deactive Status Start --}}
        <div class="modal fade" id="deactiveStatus" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel125">Deactive Associate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.associate.deactiveStatus') }}" method="POST" id="updatepublishstatus">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="id" id="deactiveID">
                            <div class="col-md-12">
                                <p class="text-danger">Are You sure to deactive Associate!</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        {{-- Deactive Status End --}}

        {{-- Deactive Status Start --}}
        <div class="modal fade" id="activeStatus" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel125">Active Associate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.associate.activeStatus') }}" method="POST" id="updatepublishstatus">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="id" id="activeID">
                            <div class="col-md-12">
                                <p class="text-danger">Are You sure to active Associate!</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        {{-- Deactive Status End --}}
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
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/pickr/pickr.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script> --}}


    <!-- Page JS -->
    <script src="{{ asset('admin/assets/pages/app-associate-list.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script> --}}

    <script src="{{ asset('admin/assets/pages/validation/associate-validation.js') }}"></script>

    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>

    <script src="{{ asset('admin/assets/custom/main.js') }}"></script>


    <script>
        $(document).ready(function(){
            $('#deactiveStatus').on('show.bs.modal',function(e){
                var id = $(e.relatedTarget).data('id');

                $('#deactiveID').val(id);
            });

        });
    </script>

<script>
    $(document).ready(function(){
        $('#activeStatus').on('show.bs.modal',function(e){
            var id = $(e.relatedTarget).data('id');

            $('#activeID').val(id);
        });

    });
</script>

    <script>
        $(document).ready(function(){
            $('#edituser').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url("admin/associate-list/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#edit_ID').val(data.id);
                        $('#edit-name').val(data.pty_full_name);
                        $('#edit-agnecy-name').val(data.pty_ag_name);
                        $('#edit-pty-email').val(data.pty_email);
                        $('#edit-pty-mobile').val(data.pty_mobile);
                        $('#edit-sec-mob-no').val(data.sec_mob_no);
                        $('#edit-address').val(data.address);
                        $('#edit-care-off').val(data.careoff_id).change();
                        $('#edit-city-id').val(data.city_id).change();
                        $('#edit-country-id').val(data.country_id).change();
                        $('#edit-region').val(data.region_id).change();

                    }
                });
            });
        });
    </script>


    <script>
        $(document).ready(function(){

            $('#pass-typef').click(function(){
                $('.pass-type-div').toggle();
            });

            $('#job-typef').click(function(){
                $('.job-type-div').toggle();
            });

            $('#create-byf').click(function(){
                $('.create-by-div').toggle();
            });

            $('#religionf').click(function(){
                $('.religion-div').toggle();
            });

            $('#cityf').click(function(){
                $('.city-div').toggle();
            });

            $('#regionf').click(function(){
                $('.region-div').toggle();
            });

            $('#experience-regionf').click(function(){
                $('.experience-region-div').toggle();
            });

            $('#publish-statusf').click(function(){
                $('.publish-status-div').toggle();
            });

            $('#candidate-statusf').click(function(){
                $('.candidate-status-div').toggle();
            });

            $('#create-datef').click(function(){
                $('.created-date-div').toggle();
            });

            $('#medical-expiry-datef').click(function(){
                $('.medical-expiry-div').toggle();
            });

            $('#new-candidate-statusf').click(function(){
                $('.new-candidate-div').toggle();
            });

            $('#ready-for-published-statusf').click(function(){
                $('.ready-for-publish-div').toggle();
            });

            // Basic Select
            $('#default-chk').click(function(){
                if ($('#new-candidate-statusf:checkbox:checked').length > 0) {
                    $('#new-candidate-statusf').trigger('click');
                }

                if ($('#ready-for-published-statusf:checkbox:checked').length > 0) {
                    $('#ready-for-published-statusf').trigger('click');
                }

                if ($('#medical-expiry-datef:checkbox:checked').length > 0) {
                    $('#medical-expiry-datef').trigger('click');
                }

                if ($('#create-datef:checkbox:checked').length > 0) {
                    $('#create-datef').trigger('click');
                }

                if ($('#candidate-statusf:checkbox:checked').length > 0) {
                    $('#candidate-statusf').trigger('click');
                }

                if ($('#publish-statusf:checkbox:checked').length > 0) {
                    $('#publish-statusf').trigger('click');
                }

                if ($('#cityf:checkbox:checked').length > 0) {
                    $('#cityf').trigger('click');
                }

                if ($('#religionf:checkbox:checked').length > 0) {
                    $('#religionf').trigger('click');
                }

                if ($('#create-byf:checkbox:checked').length > 0) {
                    $('#create-byf').trigger('click');
                }


                if($('#pass-typef:checkbox:checked').length > 0){

                }else{
                    $('#pass-typef').trigger('click');
                }

                if($('#job-typef:checkbox:checked').length > 0){

                }else{
                    $('#job-typef').trigger('click');
                }

                if($('#regionf:checkbox:checked').length > 0){

                }else{
                    $('#regionf').trigger('click');
                }
                if($('#experience-regionf:checkbox:checked').length > 0){

                }else{
                    $('#experience-regionf').trigger('click');
                }
            });

            // All Select
            $('#all-chk').click(function(){
                $('input[name="pass-typef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="job-typef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="create-byf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="religionf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="cityf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="regionf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="experience-regionf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="publish-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="candidate-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="create-datef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="medical-expiry-datef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="new-candidate-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="ready-for-published-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });
            });

            // Uncheck All
            $('#all-unchk').click(function(){
                if($('#pass-typef:checkbox:checked').length > 0){
                    $('#pass-typef').trigger('click');
                }
                if($('#job-typef:checkbox:checked').length > 0){
                    $('#job-typef').trigger('click');
                }
                if($('#create-byf:checkbox:checked').length > 0){
                    $('#create-byf').trigger('click');
                }
                if($('#religionf:checkbox:checked').length > 0){
                    $('#religionf').trigger('click');
                }
                if($('#cityf:checkbox:checked').length > 0){
                    $('#cityf').trigger('click');
                }
                if($('#regionf:checkbox:checked').length > 0){
                    $('#regionf').trigger('click');
                }
                if($('#experience-regionf:checkbox:checked').length > 0){
                    $('#experience-regionf').trigger('click');
                }
                if($('#publish-statusf:checkbox:checked').length > 0){
                    $('#publish-statusf').trigger('click');
                }
                if($('#candidate-statusf:checkbox:checked').length > 0){
                    $('#candidate-statusf').trigger('click');
                }
                if($('#create-datef:checkbox:checked').length > 0){
                    $('#create-datef').trigger('click');
                }
                if($('#medical-expiry-datef:checkbox:checked').length > 0){
                    $('#medical-expiry-datef').trigger('click');
                }
                if($('#new-candidate-statusf:checkbox:checked').length > 0){
                    $('#new-candidate-statusf').trigger('click');
                }
                if($('#ready-for-published-statusf:checkbox:checked').length > 0){
                    $('#ready-for-published-statusf').trigger('click');
                }

            });

            // Update Checkbox
            $('#update-chk').click(function(){
                var pass_typef = $('#pass-typef:checked').val();
                var job_typef = $('#job-typef:checked').val();
                var create_byf = $('#create-byf:checked').val();
                var religionf = $('#religionf:checked').val();
                var cityf = $('#cityf:checked').val();
                var regionf = $('#regionf:checked').val();
                var experience_regionf = $('#experience-regionf:checked').val();
                var publish_statusf = $('#publish-statusf:checked').val();
                var candidate_statusf = $('#candidate-statusf:checked').val();
                var create_datef = $('#create-datef:checked').val();
                var medical_expiry_datef = $('#medical-expiry-datef:checked').val();
                var new_candidate_statusf = $('#new-candidate-statusf:checked').val();
                var ready_for_published_statusf = $('#ready-for-published-statusf:checked').val();

                jQuery.ajax({
                    url:"{{ url('admin/candidate/filterList/update') }}",
                    method: 'post',
                    type: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        pass_typef: pass_typef,
                        job_typef: job_typef,
                        create_byf: create_byf,
                        religionf: religionf,
                        cityf: cityf ,
                        regionf: regionf,
                        experience_regionf: experience_regionf ,
                        publish_statusf : publish_statusf ,
                        candidate_statusf : candidate_statusf ,
                        create_datef : create_datef ,
                        medical_expiry_datef : medical_expiry_datef ,
                        new_candidate_statusf : new_candidate_statusf ,
                        ready_for_published_statusf : ready_for_published_statusf ,
                    },
                    success: function(data){
                        if(data){
                            toastr['success']('Filter updated successfully', 'Success', { hideDuration: 3000 });
                            // $('#filter_final_div').load(location.href + ' #filter_final_div');
                        }
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
        $('#deleteStaff').on('show.bs.modal',function(e){
            var staff_id =  $(e.relatedTarget).data('id');
            // alert(staff_id);
            $('#delStaffID').val(staff_id);
            // check already exist in any table or nor
            $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
            });

            jQuery.ajax({
            url : '{{ url('admin/associate-list/candidate/check/exist') }}',
            method: "POST",
            type: "html",
            data: {
                "id": staff_id,
                "_token": "{{ csrf_token() }}",
            },
            success: function(data){
                if (data) {
                $('#disbtn').prop('disabled',true);
                } else {
                $('#disbtn').prop('disabled',false);
                }
            }
            });
        });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('.bulkpublish').on('click',function(e){
                var allVals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select atleast one checkbox");
                    location.reload();
                }else{
                    var count_id = allVals.length;
                    var join_selected_values = allVals.join(",");

                    $('#idsv').val(join_selected_values);
                    $('#countcan').text(count_id);

                }
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('.bulkdownloadcv').on('click',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                if (allselectedvals <= 0) {
                    alert("Please select atleast one checkbox");
                    location.reload();
                } else {
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    $('#candcv_id').val(join_all_selected_values);
                }

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#downloadCvbtn').on('click',function(){
                var partnerID = $('#partner_id').val();
                var candCVID = $('#candcv_id').val();

                const candIDs = candCVID.split(",");
                if (partnerID != '') {
                    for (let i = 0; i < candIDs.length; i++) {
                        // const element = array[i];
                        var cvUrl = '{{ url("admin/download/candidate-cv") }}/'+partnerID+'/'+candIDs[i];
                        window.open(cvUrl,'_blank');
                    }

                    $('#errorPartner').text('');
                    $('#downloadCvbtn').attr('disabled',false);
                } else {
                    $('#errorPartner').text('Please select office name!');
                    $('#downloadCvbtn').attr('disabled',true);
                }



                // var cvUrl = '{{ url("admin/singlecv/download") }}/'+partnerID
                // window.open(cvUrl,'_blank');

            });

            $('#partner_id').on('change',function(){
                var pID = $(this).val();
                if (pID != '') {
                    $('#errorPartner').text('');
                    $('#downloadCvbtn').attr('disabled',false);
                } else {
                    $('#errorPartner').text('Please select office name!');
                    $('#downloadCvbtn').attr('disabled',true);
                }
            });
        });
    </script>

@endsection
