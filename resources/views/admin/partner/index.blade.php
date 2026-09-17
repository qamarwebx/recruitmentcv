@extends('layout.admin.admin_layout')

@section('title','Partner')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($perm) && $perm->add_partner == 0)
        <style>
        .addpartner{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->edit_partner == 0)
        <style>
        .edpartner{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->delete_partner == 0)
        <style>
        .delpartner{
            display: none !important;
        }
        </style>
        @endif
    @endif

    <style>
        /* Summary count pills — same design as the other admin list pages' status bars */
        .candidate-status-wrapper {
            width: 100%;
        }

        .candidate-status-card {
            background: var(--bs-card-bg);
            padding: 10px;
            border-radius: 8px;
        }

        .candidate-status-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .status-item {
            flex: 0 0 auto;
        }

        .status-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            min-height: 28px;
            font-size: 11px;
            white-space: nowrap;
            border: 1px solid;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
        }

        .status-btn strong {
            font-size: 16px;
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

        .partner-statusbar-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--bs-secondary-color, #6c757d);
            min-width: 120px;
        }

        .partner-statusbar-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .partner-statusbar-row + .partner-statusbar-row {
            margin-top: 10px;
        }

        @media (max-width: 991px) {
            .status-item {
                flex: 0 0 calc(25% - 10px);
            }
        }

        @media (max-width: 767px) {
            .status-item {
                flex: 0 0 calc(50% - 10px);
            }

            .partner-statusbar-row {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
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
            </div>
        </div>

        <!-- Partner Status Bar Start -->
        <div class="card mb-3" id="partnerStatusBar">
            <div class="card-body">
                <div class="candidate-status-wrapper">
                    <div class="candidate-status-card">

                        <div class="partner-statusbar-row">
                            <span class="partner-statusbar-label">Status</span>
                            <div class="candidate-status-grid">
                                <div class="status-item">
                                    <a href="javascript:;" class="status-btn partnerStatusCard"
                                       style="border-color:#28C76F;color:#28C76F;--tsc-color:#28C76F;"
                                       data-status="1">
                                        <span>Active</span>
                                        <strong>{{ number_format($partnerStatusSummary['status']['active'] ?? 0) }}</strong>
                                    </a>
                                </div>
                                <div class="status-item">
                                    <a href="javascript:;" class="status-btn partnerStatusCard"
                                       style="border-color:#EA5455;color:#EA5455;--tsc-color:#EA5455;"
                                       data-status="0">
                                        <span>Inactive</span>
                                        <strong>{{ number_format($partnerStatusSummary['status']['inactive'] ?? 0) }}</strong>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="partner-statusbar-row">
                            <span class="partner-statusbar-label">Portal Status</span>
                            <div class="candidate-status-grid">
                                <div class="status-item">
                                    <span class="status-btn" style="border-color:#28C76F;color:#28C76F;">
                                        <span>Active</span>
                                        <strong>{{ number_format($partnerStatusSummary['portal_status']['active'] ?? 0) }}</strong>
                                    </span>
                                </div>
                                <div class="status-item">
                                    <span class="status-btn" style="border-color:#EA5455;color:#EA5455;">
                                        <span>Inactive</span>
                                        <strong>{{ number_format($partnerStatusSummary['portal_status']['inactive'] ?? 0) }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="partner-statusbar-row">
                            <span class="partner-statusbar-label">Registration Status</span>
                            <div class="candidate-status-grid">
                                <div class="status-item">
                                    <span class="status-btn" style="border-color:#FF9F43;color:#FF9F43;">
                                        <span>Pending</span>
                                        <strong>{{ number_format($partnerStatusSummary['registration_status']['pending'] ?? 0) }}</strong>
                                    </span>
                                </div>
                                <div class="status-item">
                                    <span class="status-btn" style="border-color:#28C76F;color:#28C76F;">
                                        <span>Approved</span>
                                        <strong>{{ number_format($partnerStatusSummary['registration_status']['approved'] ?? 0) }}</strong>
                                    </span>
                                </div>
                                <div class="status-item">
                                    <span class="status-btn" style="border-color:#EA5455;color:#EA5455;">
                                        <span>Rejected</span>
                                        <strong>{{ number_format($partnerStatusSummary['registration_status']['rejected'] ?? 0) }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- Partner Status Bar End -->

        <!-- Users List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="d-flex justify-content-between align-items-start flex-wrap">
                    <h5 class="card-title mb-3">Search Filter</h5>

                    <div class="btn-group mx-1 mb-3">
                        <button class="btn btn-primary btn-xs dropdown-toggle" type="button" id="partnerOptionBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="partnerOptionBtn">
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" value="1" class="switch-input partner-status-bar-switch" checked />
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
                <div class="row">
                    <div class="col-md-3 mb-3 country-div" @if(isset($filter_user) && $filter_user->country_filter == 1) @else style="display:none" @endif>
                        <select name="" id="by-country" class="form-select select22">
                            <option value="">Select Country</option>
                            @foreach ($countryfs as $countryf)
                                @php
                                    $countryf2 = DB::table('countries')->where('id','=',$countryf->country_id)->first();
                                @endphp
                                <option value="{{ $countryf2->id }}">{{ $countryf2->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 city-div" @if(isset($filter_user) && $filter_user->city_filter == 1) @else style="display:none" @endif>
                        <select name="" id="by-city" class="form-select select22">
                            <option value="">Select City</option>
                            @foreach ($cityfs as $cityf)
                                @php
                                    $cityf2 = DB::table('cities')->where('id','=',$cityf->city_id)->first();
                                @endphp
                                <option value="{{ $cityf2->id }}">{{ $cityf2->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 status-div" @if(isset($filter_user) && $filter_user->status_filter == 1) @else style="display:none" @endif>
                        <select name="" id="by-status" class="form-select select22">
                            <option value="">Select Status</option>
                            @foreach ($statusfs as $statusf)
                                <option value="{{ $statusf->status }}">@if($statusf->status == 1) Active @else Inactive @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 created-date-div" @if(isset($filter_user) && $filter_user->created_date_filter == 1) @else style="display:none" @endif>
                        <input type="text" name="" id="by-created-date" class="form-control createdate-picker" placeholder="Booking date...">
                    </div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th>Office Name</th>
                            <th>Owner Name</th>
                            <th>Owner Contact</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>Registration Request</th>
                            <th>Portal Status</th>
                            <th>Partner Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!--- Add Partner Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Partner</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.partner.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-recoff-name">Recruitement Office Name (English) <span class="text-danger">*</span></label>
                                <input type="text" name="rec_off_name" id="add-recoff-name" class="form-control" placeholder="Enter office name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-recoff-arname">Recruitment Office Name (Arabic) <span class="text-danger">*</span></label>
                                <input type="text" name="rec_office_arname" id="add-recoff-arname" class="form-control" placeholder="Enter office name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-owner-name">Owner Name <span class="text-danger">*</span></label>
                                <input type="text" name="owner_name" id="add-owner-name" class="form-control" placeholder="Enter owner name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-owner-mob-no">Owner Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="owner_mobile_no" id="add-owner-mob-no" class="form-control" placeholder="Enter owner contact number...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-city">City <span class="text-danger">*</span></label>
                                <select name="city_id" id="add-city" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                {{-- <input type="text" name="city" id="add-city" class="form-control" placeholder="Enter city name..."> --}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-country">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="add-country" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                {{-- <input type="text" name="country" id="add-country" class="form-control" placeholder="Enter country name..."> --}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-primary_email">Primary Email <span class="text-danger">*</span></label>
                                <input type="text" name="primary_email" id="add-primary_email" class="form-control" placeholder="Enter email...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-secondary-email">Secondary Email</label>
                                <input type="text" name="secondary_email" id="add-secondary-email" class="form-control" placeholder="Enter email...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-office_no">Office Number</label>
                                <input type="text" name="office_no" id="add-office_no" class="form-control" placeholder="Enter office number...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-primary-mob">Primary Mobile</label>
                                <input type="text" name="primary_mob" id="add-primary-mob" class="form-control" placeholder="Enter contact number...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-secondary-mob">Secondary Mobile</label>
                                <input type="text" name="secondary_mob" id="add-secondary-mob" class="form-control" placeholder="Enter mobile number...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-customer-no" class="form-label">Customer No</label>
                                <input type="text" name="customer_no" id="add-customer-no" class="form-control" placeholder="Enter Customer No">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Add Partner End --->

        <!--- Edit Partner Start --->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="edituserLabel" class="offcanvas-title">Edit Partner</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.partner.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="hidden" name="edit_id" id="editid">
                                <label class="form-label" for="edit-recoff-name">Recruitement Office Name (English) <span class="text-danger">*</span></label>
                                <input type="text" name="rec_off_name" id="edit-recoff-name" class="form-control" placeholder="Enter office name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-recoff-arname">Recruitment Office Name (Arabic) <span class="text-danger">*</span></label>
                                <input type="text" name="rec_office_arname" id="edit-recoff-arname" class="form-control" placeholder="Enter office name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-owner-name">Owner Name <span class="text-danger">*</span></label>
                                <input type="text" name="owner_name" id="edit-owner-name" class="form-control" placeholder="Enter owner name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-owner-mob-no">Owner Contact Number <span class="text-danger">*</span></label>
                                <input type="text" name="owner_mobile_no" id="edit-owner-mob-no" class="form-control" placeholder="Enter owner contact number...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-city">City <span class="text-danger">*</span></label>
                                <select name="city_id" id="edit-city" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city2)
                                        <option value="{{ $city2->id }}">{{ $city2->name }}</option>
                                    @endforeach
                                </select>
                                {{-- <input type="text" name="city" id="edit-city" class="form-control" placeholder="Enter city name..."> --}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-country">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="edit-country" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country2)
                                        <option value="{{ $country2->id }}">{{ $country2->name }}</option>
                                    @endforeach
                                </select>
                                {{-- <input type="text" name="country" id="edit-country" class="form-control" placeholder="Enter country name..."> --}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-primary_email">Primary Email <span class="text-danger">*</span></label>
                                <input type="text" name="primary_email" id="edit-primary_email" class="form-control" placeholder="Enter email...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-secondary-email">Secondary Email</label>
                                <input type="text" name="secondary_email" id="edit-secondary-email" class="form-control" placeholder="Enter email...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-office_no">Office Number</label>
                                <input type="text" name="office_no" id="edit-office_no" class="form-control" placeholder="Enter office number...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-primary-mob">Primary Mobile</label>
                                <input type="text" name="primary_mob" id="edit-primary-mob" class="form-control" placeholder="Enter contact number...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-secondary-mob">Secondary Mobile</label>
                                <input type="text" name="secondary_mob" id="edit-secondary-mob" class="form-control" placeholder="Enter mobile number...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-customer-no" class="form-label">Customer No</label>
                                <input type="text" name="customer_no" id="edit-customer-no" class="form-control" placeholder="Enter Customer No">
                            </div>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Partner End --->

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
                            <div class="form-check mt-2" id="country-f">
                                <input class="form-check-input" type="checkbox" name="countryf" value="1" id="countryf" @if(isset($filter_user) && $filter_user->country_filter == 1) checked @endif />
                                <label class="form-check-label" for="countryf"> Country</label>
                            </div>
                            <div class="form-check mt-2" id="city-f">
                                <input class="form-check-input" type="checkbox" name="cityf" value="1" id="cityf" @if(isset($filter_user) && $filter_user->city_filter == 1) checked @endif />
                                <label class="form-check-label" for="cityf"> City</label>
                            </div>
                            <div class="form-check mt-2" id="status-f">
                                <input class="form-check-input" type="checkbox" name="statusf" value="1" id="statusf" @if(isset($filter_user) && $filter_user->status_filter == 1) checked @endif />
                                <label class="form-check-label" for="statusf"> Status</label>
                            </div>
                            <div class="form-check mt-2" id="created-date-f">
                                <input class="form-check-input" type="checkbox" name="created-datef" value="1" id="created-datef" @if(isset($filter_user) && $filter_user->created_date_filter == 1) checked @endif />
                                <label class="form-check-label" for="created-datef"> Created Date</label>
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

        <!-- Delete Partner Start -->
        <div class="modal fade" id="deletePartner" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel1">Change Status</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="{{ route('admin.partner.delete') }}" method="POST" enctype="multipart/form-data">
                     @csrf
                     <input type="hidden" name="partner_id" id="deletePartnerID">
                     <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                               <div class="mb-3">
                                  <p class="text-danger">Are you sure to delete partner?</p>
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
        <!-- Delete Partner End -->

        <!-- Change Partner Status Start -->
        <div class="modal fade" id="changeStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel1">Change Status</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="{{ route('admin.partner.statusupdate') }}" method="POST" enctype="multipart/form-data">
                     @csrf
                     <input type="hidden" name="partner_id" id="ptstatus_id">
                     <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                               <div class="mb-3">
                                  <div class="form-check form-check-inline mt-3">
                                     <input class="form-check-input" type="radio" name="status" id="activeStatus" value="1"/>
                                     <label class="form-check-label" for="activeStatus">Active</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                     <input class="form-check-input" type="radio" name="status" id="inactiveStatus" value="0"/>
                                     <label class="form-check-label" for="inactiveStatus">Inactive</label>
                                  </div>
                               </div>
                            </div>
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
        <!-- Change Partner Status End -->

        <!-- Change Partner Portal Status Start -->
        <div class="modal fade" id="changePortalStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel1">Change Portal Status</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="{{ route('admin.partner.portalstatusUpdate') }}" method="POST" enctype="multipart/form-data">
                     @csrf
                     <input type="hidden" name="partner_id" id="pptstatus_id">
                     <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                               <div class="mb-3">
                                  <div class="form-check form-check-inline mt-3">
                                     <input class="form-check-input" type="radio" name="portal_status" id="activepStatus" value="1"/>
                                     <label class="form-check-label" for="activepStatus">Active</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                     <input class="form-check-input" type="radio" name="portal_status" id="inactivepStatus" value="0"/>
                                     <label class="form-check-label" for="inactivepStatus">Deactive</label>
                                  </div>
                               </div>
                            </div>
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
        <!-- Change Partner Portal Status End -->

        <!-- Change Partner Registration Status Start -->
        <div class="modal fade" id="changeRegistrationStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title">Change Registration Status</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form id="changeRegistrationStatusForm" action="{{ route('admin.partner.registrationstatusUpdate') }}" method="POST">
                     @csrf
                     <input type="hidden" name="partner_id" id="regstatus_id">
                     <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                               <div class="mb-3">
                                  <div class="form-check form-check-inline mt-3">
                                     <input class="form-check-input" type="radio" name="registration_status" id="pendingRegStatus" value="0"/>
                                     <label class="form-check-label" for="pendingRegStatus">Pending</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                     <input class="form-check-input" type="radio" name="registration_status" id="approvedRegStatus" value="1"/>
                                     <label class="form-check-label" for="approvedRegStatus">Approved</label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                     <input class="form-check-input" type="radio" name="registration_status" id="rejectedRegStatus" value="2"/>
                                     <label class="form-check-label" for="rejectedRegStatus">Rejected</label>
                                  </div>
                               </div>
                            </div>
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
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}?v={{ @filemtime(public_path('admin/assets/js/forms-pickers.js')) ?: time() }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('admin/assets/pages/app-partner-list.js') }}?v={{ @filemtime(public_path('admin/assets/pages/app-partner-list.js')) ?: time() }}"></script>

    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/partner-validation.js') }}?v={{ @filemtime(public_path('admin/assets/pages/validation/partner-validation.js')) ?: time() }}"></script>

    <script>
        $(document).ready(function(){

            $('#deletePartner').on('show.bs.modal',function(e){
                var partner_id = $(e.relatedTarget).data('id');
                $('#deletePartnerID').val(partner_id);

                jQuery.ajax({
                    url: '{{ url("admin/partner/checkpartnerexists") }}',
                    method: "GET",
                    type: "html",
                    data:{
                        id: partner_id
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

            $('#changeStatus').on('show.bs.modal',function(e){
                var partner_id = $(e.relatedTarget).data('id');
                $('#ptstatus_id').val(partner_id);
                jQuery.ajax({
                    url: '{{ url("admin/partner/get/data") }}',
                    method: "GET",
                    type: "html",
                    data:{
                        id: partner_id
                    },
                    success: function(data){
                        if (data.status == 1) {
                            $('#activeStatus').attr('checked',true);
                        } else {
                            $('#inactiveStatus').attr('checked',true);
                        }
                    }
                });
            });

            $('#changePortalStatus').on('show.bs.modal',function(e){
                var partner_id = $(e.relatedTarget).data('id');
                $('#pptstatus_id').val(partner_id);
                jQuery.ajax({
                    url: '{{ url("admin/partner/get/data") }}',
                    method: "GET",
                    type: "html",
                    data:{
                        id: partner_id
                    },
                    success: function(data){
                        if (data.portal_status == 1) {
                            $('#activepStatus').attr('checked',true);
                        } else {
                            $('#inactivepStatus').attr('checked',true);
                        }
                    }
                });
            });

            $('#changeRegistrationStatus').on('show.bs.modal',function(e){
                var partner_id = $(e.relatedTarget).data('id');
                $('#regstatus_id').val(partner_id);
                jQuery.ajax({
                    url: '{{ url("admin/partner/get/data") }}',
                    method: "GET",
                    type: "html",
                    data:{
                        id: partner_id
                    },
                    success: function(data){
                        $('#pendingRegStatus, #approvedRegStatus, #rejectedRegStatus').prop('checked', false);
                        if (data.registration_status == 1) {
                            $('#approvedRegStatus').prop('checked',true);
                        } else if (data.registration_status == 2) {
                            $('#rejectedRegStatus').prop('checked',true);
                        } else {
                            $('#pendingRegStatus').prop('checked',true);
                        }
                    }
                });
            });

            // Status Bar toggle: shows/hides the Partner Status Bar.
            // Persisted client-side so the choice survives reloads and
            // keeps working across the DataTable's own client-side
            // filtering/redraw, since that never re-renders this bar's
            // container. display is forced with !important (and bound via
            // delegation) so nothing else on this page can silently
            // override it.
            (function(){
                function setPartnerStatusBarVisible(visible){
                    var bar = document.getElementById('partnerStatusBar');
                    if (!bar) return;
                    if (visible) {
                        bar.style.removeProperty('display');
                    } else {
                        bar.style.setProperty('display', 'none', 'important');
                    }
                }

                var stored = null;
                try { stored = localStorage.getItem('partner_status_bar_visible'); } catch(e) {}
                var visible = stored === null ? true : stored === '1';
                $('.partner-status-bar-switch').prop('checked', visible);
                setPartnerStatusBarVisible(visible);

                $(document).on('change', '.partner-status-bar-switch', function(){
                    var isVisible = $(this).is(':checked');
                    setPartnerStatusBarVisible(isVisible);
                    try { localStorage.setItem('partner_status_bar_visible', isVisible ? '1' : '0'); } catch(e) {}
                });
            })();

            // Status summary cards -> apply the matching #by-status filter
            // (same select the "Search Filter" row already uses; app-partner-list.js
            // listens for its change event to redraw the client-side-filtered table).
            $(document).on('click', '.partnerStatusCard', function(e){
                e.preventDefault();
                var $this = $(this);
                var wasActive = $this.hasClass('active');

                $('.partnerStatusCard').removeClass('active');

                if (wasActive) {
                    $('#by-status').val('').trigger('change');
                } else {
                    $this.addClass('active');
                    $('#by-status').val($this.data('status')).trigger('change');
                }
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
                    url : '{{ url('admin/partner/edit') }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#editid').val(data.id);
                        $('#edit-recoff-name').val(data.rec_off_name);
                        $('#edit-recoff-arname').val(data.rec_office_arname);
                        $('#edit-owner-name').val(data.owner_name);
                        $('#edit-owner-mob-no').val(data.owner_mobile_no);
                        $('#edit-city').val(data.city_id).trigger('change');
                        $('#edit-country').val(data.country_id).trigger('change');
                        $('#edit-primary_email').val(data.primary_email);
                        $('#edit-secondary-email').val(data.secondary_email);
                        $('#edit-office_no').val(data.office_no);
                        $('#edit-primary-mob').val(data.primary_mob);
                        $('#edit-secondary-mob').val(data.secondary_mob);
                        $('#edit-customer-no').val(data.customer_no);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function(){

            $('#countryf').click(function(){
                $('.country-div').toggle();
            });

            $('#cityf').click(function(){
                $('.city-div').toggle();
            });

            $('#statusf').click(function(){
                $('.status-div').toggle();
            });

            $('#created-datef').click(function(){
                $('.created-date-div').toggle();
            });

            // Basic Select
            $('#default-chk').click(function(){
                if ($('#created-datef:checkbox:checked').length > 0) {
                    $('#created-datef').trigger('click');
                }


                if($('#countryf:checkbox:checked').length > 0){

                }else{
                    $('#countryf').trigger('click');
                }

                if($('#cityf:checkbox:checked').length > 0){

                }else{
                    $('#cityf').trigger('click');
                }

                if($('#statusf:checkbox:checked').length > 0){

                }else{
                    $('#statusf').trigger('click');
                }
            });

            // All Select
            $('#all-chk').click(function(){
                $('input[name="countryf"]').each(function () {
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

                $('input[name="statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="created-datef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });
            });

            // Uncheck All
            $('#all-unchk').click(function(){
                if($('#countryf:checkbox:checked').length > 0){
                    $('#countryf').trigger('click');
                }
                if($('#cityf:checkbox:checked').length > 0){
                    $('#cityf').trigger('click');
                }
                if($('#statusf:checkbox:checked').length > 0){
                    $('#statusf').trigger('click');
                }
                if($('#created-datef:checkbox:checked').length > 0){
                    $('#created-datef').trigger('click');
                }

            });

            // Update Checkbox
            $('#update-chk').click(function(){
                var countryf = $('#countryf:checked').val();
                var cityf = $('#cityf:checked').val();
                var statusf = $('#statusf:checked').val();
                var created_datef = $('#created-datef:checked').val();


                jQuery.ajax({
                    url:"{{ url('admin/partner/filterList/update') }}",
                    method: 'post',
                    type: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        countryf: countryf,
                        cityf: cityf,
                        statusf: statusf,
                        created_datef: created_datef,
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

@endsection
