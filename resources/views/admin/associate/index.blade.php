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

    <style>
    .filterpanel {
        position: relative;
    }

    .filter-indicator {
        position: absolute;
        top: 1px;
        right: 2px;
        width: 8px;
        height: 8px;
        background: #ffc107;
        border-radius: 50%;
    }

    /* ==============================
    ASSOCIATE STATUS BAR
    Compact toolbar layout (same pattern as the Todo/Employer/Booking
    modules): one row per group, label inline to the left of its pills.
    ============================== */
    .associate-filterbar {
        padding: 8px 14px;
    }

    .associate-filterbar-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .associate-filterbar-row + .associate-filterbar-row {
        border-top: 1px solid var(--bs-border-color, rgba(0, 0, 0, .06));
        margin-top: 5px;
        padding-top: 5px;
    }

    .associate-filterbar-label {
        flex: 0 0 auto;
        min-width: 96px;
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: var(--bs-secondary-color, #6c757d);
    }

    .candidate-status-grid {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        flex: 1 1 auto;
        min-width: 0;
    }

    .status-item {
        flex: 0 0 auto;
    }

    .status-btn {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        min-height: 24px;
        font-size: 11px;
        line-height: 1;
        white-space: nowrap;
        border: 1px solid;
        border-radius: 4px;
        text-decoration: none;
        font-weight: 500;
    }

    .status-btn strong {
        font-size: 13px;
        line-height: 1;
    }

    .status-btn:hover {
        background: rgba(115, 103, 240, 0.05);
        text-decoration: none;
    }

    .status-btn.active {
        background: var(--tsc-color, #7367f0);
    }

    .status-btn.active span,
    .status-btn.active strong {
        color: #fff !important;
    }

    @media (max-width: 575px) {
        .associate-filterbar-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .associate-filterbar-label {
            min-width: 0;
        }
    }
    </style>

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

        </div>

        @php
            $associateStatusOptions = [
                0 => ['label' => 'Inactive', 'color' => '#EA5455'],
                1 => ['label' => 'Active',   'color' => '#28C76F'],
            ];
            $associateContactVerifiedOptions = [
                0 => ['label' => 'Not Verified', 'color' => '#EA5455'],
                1 => ['label' => 'Verified',     'color' => '#28C76F'],
            ];
        @endphp

        <!-- Associate Status Bar Start -->
        <div class="card mb-3" id="associateStatusBar">
            <div class="card-body associate-filterbar">

                <div class="associate-filterbar-row">
                    <span class="associate-filterbar-label">Status</span>
                    <div class="candidate-status-grid">
                        <div class="status-item">
                            <a href="javascript:void(0)" class="status-btn associateStatusCard active"
                               style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;"
                               data-group="status" data-value="">
                                <span>All</span>
                                <strong>{{ number_format($associateStatusSummary['total'] ?? 0) }}</strong>
                            </a>
                        </div>
                        @foreach ($associateStatusOptions as $statusValue => $statusOption)
                            <div class="status-item">
                                <a href="javascript:void(0)" class="status-btn associateStatusCard"
                                   style="border-color:{{ $statusOption['color'] }};color:{{ $statusOption['color'] }};--tsc-color:{{ $statusOption['color'] }};"
                                   data-group="status" data-value="{{ $statusValue }}">
                                    <span>{{ $statusOption['label'] }}</span>
                                    <strong>{{ number_format($associateStatusSummary['statuses'][$statusValue] ?? 0) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="associate-filterbar-row">
                    <span class="associate-filterbar-label">Contact</span>
                    <div class="candidate-status-grid">
                        <div class="status-item">
                            <a href="javascript:void(0)" class="status-btn associateStatusCard active"
                               style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;"
                               data-group="contact" data-value="">
                                <span>All</span>
                                <strong>{{ number_format($associateStatusSummary['total'] ?? 0) }}</strong>
                            </a>
                        </div>
                        @foreach ($associateContactVerifiedOptions as $verifiedValue => $verifiedOption)
                            <div class="status-item">
                                <a href="javascript:void(0)" class="status-btn associateStatusCard"
                                   style="border-color:{{ $verifiedOption['color'] }};color:{{ $verifiedOption['color'] }};--tsc-color:{{ $verifiedOption['color'] }};"
                                   data-group="contact" data-value="{{ $verifiedValue }}">
                                    <span>{{ $verifiedOption['label'] }}</span>
                                    <strong>{{ number_format($associateStatusSummary['contactVerified'][$verifiedValue] ?? 0) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
        <input type="hidden" id="by-status" value="">
        <input type="hidden" id="by-contact-verified" value="">
        <!-- Associate Status Bar End -->

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
                <div class="px-3 float-start">
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel"><i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter <span class="filter-indicator d-none"></span></button>

                    <div class="btn-group mx-2">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="associateOptionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <div class="dropdown-menu" aria-labelledby="associateOptionDropdown">
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" name="associate_status_bar_toggle" value="1" id="associate_status_bar_toggle" class="switch-input" checked />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Status Bar</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="float-end">
                    <button class="add-new btn btn-sm btn-primary addcandidate mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Associate</span></button>
                </div>
                <div class="float-end">
                    <input type="text" id="search_text" class="form-control form-control-sm" placeholder="Search...">
                </div>
            </div>
            <div class="card-datatable table-responsive assoicateloadpaginate">
                @include('admin.associate.load')
            </div>
        </div>

        <!--- Add Candidate Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
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
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
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

        <!-- Associate Filter Panel Start -->
        <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="filterpanelLabel">Associate Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <select id="by-country" class="form-select filterData select22f" multiple data-placeholder="Select Country">
                                    @foreach ($countries as $countryFilter)
                                        <option value="{{ $countryFilter->id }}" @if(isset($saveadminfilter) && in_array($countryFilter->id, $saveadminfilter->by_country_array)) selected @endif>{{ $countryFilter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <select id="by-city" class="form-select filterData select22f" multiple data-placeholder="Select City">
                                    @foreach ($cities as $cityFilter)
                                        <option value="{{ $cityFilter->id }}" @if(isset($saveadminfilter) && in_array($cityFilter->id, $saveadminfilter->by_city_array)) selected @endif>{{ $cityFilter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <select id="by-region" class="form-select filterData select22f" multiple data-placeholder="Select Region">
                                    @foreach ($regions as $regionFilter)
                                        <option value="{{ $regionFilter->id }}" @if(isset($saveadminfilter) && in_array($regionFilter->id, $saveadminfilter->by_region_array)) selected @endif>{{ $regionFilter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <select id="by-created-by" class="form-select filterData select22f" multiple data-placeholder="Select Created By">
                                    @foreach ($users as $createdByFilter)
                                        <option value="{{ $createdByFilter->id }}" @if(isset($saveadminfilter) && in_array($createdByFilter->id, $saveadminfilter->by_created_by_array)) selected @endif>{{ $createdByFilter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <select id="by-careoff" class="form-select filterData select22f" multiple data-placeholder="Select Care Of">
                                    @foreach ($users as $careoffFilter)
                                        <option value="{{ $careoffFilter->id }}" @if(isset($saveadminfilter) && in_array($careoffFilter->id, $saveadminfilter->by_careoff_array)) selected @endif>{{ $careoffFilter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning btn-sm associateResetFilter">Reset Filter</button>
                        <button type="button" class="btn btn-success btn-sm associateSaveFilter">Save Filter</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Associate Filter Panel End -->

        <!-- Delete Staff start -->
        <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
        <div class="modal fade" id="publish" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
        <div class="modal fade" id="downloadcv" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
        <div class="modal fade" id="deactiveStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
        <div class="modal fade" id="activeStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
    {{-- <script src="{{ asset('admin/assets/pages/app-associate-list.js') }}"></script> --}}
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

    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Function to retrieve all filter values
            function getFilterData() {
                return {
                    page_list: $('#pagination_list').val(),
                    search_text: $('#search_text').val(),
                    by_status: $('#by-status').val(),
                    by_contact_verified: $('#by-contact-verified').val(),
                    by_country: $('#by-country').val(),
                    by_city: $('#by-city').val(),
                    by_region: $('#by-region').val(),
                    by_created_by: $('#by-created-by').val(),
                    by_careoff: $('#by-careoff').val(),
                };
            }

            // Lazily init select2 on the filter modal's multi-selects only
            // once it is visible — sizing them while hidden breaks their
            // dropdown width (same pattern as Employer's filter panel).
            $('body').on('shown.bs.modal', '#filterpanel', function () {
                $(this).find('.select22f').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({ dropdownParent: $(this).parent() });
                    }
                });
            });

            function updateAssociateFilterIndicator() {
                var hasFilter =
                    ($('#by-country').val() && $('#by-country').val().length > 0) ||
                    ($('#by-city').val() && $('#by-city').val().length > 0) ||
                    ($('#by-region').val() && $('#by-region').val().length > 0) ||
                    ($('#by-created-by').val() && $('#by-created-by').val().length > 0) ||
                    ($('#by-careoff').val() && $('#by-careoff').val().length > 0);

                $('.filterpanel .filter-indicator').toggleClass('d-none', !hasFilter);
            }

            // Only the filter-panel's own fields — status bar (by-status /
            // by-contact-verified) is a separate, unrelated mechanism and
            // is not part of the saved filter, matching Leads' saveFilter scope.
            function getAssociateSaveFilterData() {
                return {
                    by_country: $('#by-country').val(),
                    by_city: $('#by-city').val(),
                    by_region: $('#by-region').val(),
                    by_created_by: $('#by-created-by').val(),
                    by_careoff: $('#by-careoff').val(),
                };
            }

            // Function to reload todo list based on filter data
            function reloadTodoList() {
                $.ajax({
                    url: "{{ route('admin.associate') }}",
                    method: "GET",
                    dataType: "html",
                    data: getFilterData(),
                    success: function (data) {
                        $('.assoicateloadpaginate').html(data);
                    }
                });
            }

            var suppressFilterReload = false;

            // Generic event handler for change and input events on filter elements
            function bindFilterChange(selector) {
                $(selector).on('change input', function () {
                    if (suppressFilterReload) {
                        return;
                    }
                    updateAssociateFilterIndicator();
                    reloadTodoList();
                });
            }

            // Bind change/input events to all filter elements
            bindFilterChange('#pagination_list');
            bindFilterChange('#search_text');
            bindFilterChange('#by-status');
            bindFilterChange('#by-contact-verified');
            bindFilterChange('#by-country');
            bindFilterChange('#by-city');
            bindFilterChange('#by-region');
            bindFilterChange('#by-created-by');
            bindFilterChange('#by-careoff');

            // Initial check on page load — a saved filter is pre-selected
            // server-side (Blade `selected` attributes), so the dot must
            // reflect that immediately without waiting for a click.
            updateAssociateFilterIndicator();

            $(document).on('click', '.associateResetFilter', function () {
                var selectors = ['#by-country', '#by-city', '#by-region', '#by-created-by', '#by-careoff'];
                var hadFilter = selectors.some(function (selector) {
                    return $(selector).val() && $(selector).val().length > 0;
                });

                if (!hadFilter) {
                    return; // nothing selected — avoid a pointless request
                }

                // Clear every field (still triggering 'change' so select2's
                // own display updates) while suppressing bindFilterChange's
                // per-field reload, then reload exactly once at the end.
                suppressFilterReload = true;
                selectors.forEach(function (selector) {
                    $(selector).val(null).trigger('change');
                });
                suppressFilterReload = false;

                updateAssociateFilterIndicator();
                reloadTodoList();

                // Persist the cleared state too, same as Leads' Reset Filter.
                $.post("{{ route('admin.associate.saveFilter') }}", getAssociateSaveFilterData())
                    .done(function (res) {
                        toastr.success(res.message || 'Filter reset successfully!', 'Success', { timeOut: 2000 });
                    })
                    .fail(function () {
                        toastr.error('Failed to reset filter', 'Error');
                    });
            });

            $(document).on('click', '.associateSaveFilter', function () {
                $.post("{{ route('admin.associate.saveFilter') }}", getAssociateSaveFilterData())
                    .done(function (res) {
                        toastr.success(res.message || 'Filter saved successfully!', 'Success', { timeOut: 2000 });
                    })
                    .fail(function () {
                        toastr.error('Failed to save filter', 'Error');
                    });

                reloadTodoList();
                updateAssociateFilterIndicator();
            });

            // ===============================
            // Associate status bar -> apply matching filter (single-select
            // / radio-button behavior: clicking a pill clears/sets only
            // pills in its own group before applying, so Status + Contact
            // combine (AND together) instead of one click resetting the
            // other).
            // ===============================
            $(document).on('click', '.associateStatusCard', function (e) {
                e.preventDefault();

                var $this = $(this);
                var group = $this.data('group');
                var value = $this.data('value');
                value = (value === undefined || value === null) ? '' : String(value);
                var wasActive = $this.hasClass('active');

                if (wasActive && !value) {
                    return; // "All" already active — avoid a duplicate request
                }

                $('.associateStatusCard[data-group="' + group + '"]').removeClass('active');

                var effectiveValue = wasActive ? '' : value;
                if (!wasActive) {
                    $this.addClass('active');
                }

                var selector = group === 'status' ? '#by-status' : '#by-contact-verified';

                // Plain hidden input, no selectpicker/select2 involved —
                // .trigger('change') fires the bound reload exactly once.
                $(selector).val(effectiveValue).trigger('change');
            });


            // Pagination Request
            $('body').on('click','.pagination a',function(e){
                e.preventDefault();
                var url = $(this).attr('href');
                var url_data = getFilterData();
                var finalURL = url + "&" + $.param(url_data);;
                // alert(finalURL);

                getPaginations(finalURL);
                window.history.pushState("", url);

            });

            function getPaginations(finalURL){
                $.ajax({
                    url : finalURL
                }).done(function(data){
                    $('.assoicateloadpaginate').html(data);

                }).fail(function(){

                    alert("Something gone wrong!")
                });
            }
        });
    </script>

    <script>
        // Status Bar toggle: shows/hides the Associate Status Bar. Persisted
        // client-side (same pattern as All Contact/Leads/Todo/Deal Pipeline)
        // so the choice survives reloads; display is forced with
        // !important so nothing else on this page can silently override it.
        (function () {
            function setAssociateStatusBarVisible(visible) {
                var bar = document.getElementById('associateStatusBar');
                if (!bar) return;
                if (visible) {
                    bar.style.removeProperty('display');
                } else {
                    bar.style.setProperty('display', 'none', 'important');
                }
            }

            var stored = null;
            try { stored = localStorage.getItem('associate_status_bar_visible'); } catch (e) {}
            var visible = stored === null ? true : stored === '1';
            $('#associate_status_bar_toggle').prop('checked', visible);
            setAssociateStatusBarVisible(visible);

            $(document).on('change', '#associate_status_bar_toggle', function () {
                var isVisible = $(this).is(':checked');
                setAssociateStatusBarVisible(isVisible);
                try { localStorage.setItem('associate_status_bar_visible', isVisible ? '1' : '0'); } catch (e) {}
            });
        })();
    </script>

@endsection
