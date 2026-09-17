@extends('layout.admin.admin_layout')

@section('title','Employer+')


@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">

    @if (Auth::guard('admin')->user()->user_type == 2)

        @if (isset($perm) && $perm->delete_employer == 0)
        <style>
        .delemployer{
            display: none !important;
        }
        </style>
        @endif
    @endif
    <style>
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
    EMPLOYER+ STATUS BAR
    Compact toolbar layout (same pattern as the Todo/Booking/Deal Pipeline
    modules): one row per group, label inline to the left of its pills.
    ============================== */
    .employer-filterbar {
        padding: 8px 14px;
    }

    .employer-filterbar-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .employer-filterbar-row + .employer-filterbar-row {
        border-top: 1px solid var(--bs-border-color, rgba(0, 0, 0, .06));
        margin-top: 5px;
        padding-top: 5px;
    }

    .employer-filterbar-label {
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
        .employer-filterbar-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .employer-filterbar-label {
            min-width: 0;
        }
    }
    </style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        {{-- <div class="row g-4 mb-4">
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
        </div> --}}

        @php
            $employerStatusOptions = [
                0 => ['label' => 'Inactive', 'color' => '#EA5455'],
                1 => ['label' => 'Active',   'color' => '#28C76F'],
            ];
            $employerPaymentStatusOptions = [
                'Paid'       => '#28C76F',
                'Unpaid'     => '#EA5455',
                'To Collect' => '#FF9F43',
            ];
        @endphp

        <!-- Employer+ Status Bar Start -->
        <div class="card mb-3">
            <div class="card-body employer-filterbar">

                <div class="employer-filterbar-row">
                    <span class="employer-filterbar-label">Status</span>
                    <div class="candidate-status-grid">
                        <div class="status-item">
                            <a href="javascript:void(0)" class="status-btn employerStatusCard active"
                               style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;"
                               data-group="status" data-value="">
                                <span>All</span>
                                <strong>{{ number_format($employerPlusStatusSummary['total'] ?? 0) }}</strong>
                            </a>
                        </div>
                        @foreach ($employerStatusOptions as $statusValue => $statusOption)
                            <div class="status-item">
                                <a href="javascript:void(0)" class="status-btn employerStatusCard"
                                   style="border-color:{{ $statusOption['color'] }};color:{{ $statusOption['color'] }};--tsc-color:{{ $statusOption['color'] }};"
                                   data-group="status" data-value="{{ $statusValue }}">
                                    <span>{{ $statusOption['label'] }}</span>
                                    <strong>{{ number_format($employerPlusStatusSummary['statuses'][$statusValue] ?? 0) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="employer-filterbar-row">
                    <span class="employer-filterbar-label">Payment Status</span>
                    <div class="candidate-status-grid">
                        <div class="status-item">
                            <a href="javascript:void(0)" class="status-btn employerStatusCard active"
                               style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;"
                               data-group="payment" data-value="">
                                <span>All</span>
                                <strong>{{ number_format($employerPlusStatusSummary['total'] ?? 0) }}</strong>
                            </a>
                        </div>
                        @foreach ($employerPaymentStatusOptions as $paymentValue => $paymentColor)
                            <div class="status-item">
                                <a href="javascript:void(0)" class="status-btn employerStatusCard"
                                   style="border-color:{{ $paymentColor }};color:{{ $paymentColor }};--tsc-color:{{ $paymentColor }};"
                                   data-group="payment" data-value="{{ $paymentValue }}">
                                    <span>{{ $paymentValue }}</span>
                                    <strong>{{ number_format($employerPlusStatusSummary['paymentStatuses'][$paymentValue] ?? 0) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
        <input type="hidden" id="by-status" value="">
        <input type="hidden" id="by-payment-status" value="">
        <!-- Employer+ Status Bar End -->

        <!-- Employer List Table -->
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
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel">
                        <i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter                         
                        <span class="filter-indicator d-none"></span>
                    </button>
                </div>

                <div class="float-end">
                    <button class="add-new btn btn-sm btn-primary mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Employer</span></button>
                </div>
                <div class="float-end">
                    <input type="text" id="search_text" class="form-control form-control-sm" placeholder="Search...">
                </div>
            </div>

            <div class="card-datatable table-responsive employerpaginate">
                @include('admin.employer.load')
            </div>


        </div>

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
                            <div class="form-check mt-2" id="booking-status-f">
                                <input class="form-check-input" type="checkbox" name="booking-statusf" value="1" id="booking-statusf" @if(isset($filter_user) && $filter_user->booking_status_filter == 1) checked @endif />
                                <label class="form-check-label" for="booking-statusf"> Booking Status</label>
                            </div>
                            <div class="form-check mt-2" id="booking-date-f">
                                <input class="form-check-input" type="checkbox" name="booking-datef" value="1" id="booking-datef" @if(isset($filter_user) && $filter_user->booking_date_filter == 1) checked @endif />
                                <label class="form-check-label" for="booking-datef"> Booking Date</label>
                            </div>
                            <div class="form-check mt-2" id="candidate-name-f">
                                <input class="form-check-input" type="checkbox" name="candidate-namef" value="1" id="candidate-namef" @if(isset($filter_user) && $filter_user->candidate_name_filter == 1) checked @endif />
                                <label class="form-check-label" for="candidate-namef"> Candidate Name</label>
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

        <!-- Add Employer Canvas Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Employer</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addEmployerVisa" action="{{ route('admin.employer.storeVisaDet') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-visa-from">Business <span class="text-danger">*</span></label>
                                <select name="businesstype" id="add-visa-from" class="form-select select2" data-placeholder="Select Visa From...." data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="B2B Online">B2B Online</option>
                                    <option value="B2B Offline">B2B Offline</option>
                                    <option value="B2C Online">B2C Online</option>
                                    <option value="B2C Offline">B2C Offline</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-partner">Partner</label>
                                <select name="partner_office_id" id="add-partner" class="form-select select2" data-placeholder="Select Partner...." data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->rec_off_name.' ('.$partner->rec_office_arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-careoff-id" class="form-label">Careoff</label>
                                <select name="careoff_id" id="add-careoff-id" class="form-select select2" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-employer-name">Employer Name</label>
                                <input type="text" name="employer_name" id="add-employer-name" class="form-control" placeholder="Enter Employer Name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-employer-ar-name">Employer Name (Arabic) <span class="text-danger">*</span></label>
                                <input type="text" name="employer_ar_name" id="add-employer-ar-name" class="form-control" placeholder="Enter Employer Name (Arabic)...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-visa-no">Visa No <span class="text-danger">*</span></label>
                                <input type="text" name="visa_no" id="add-visa-no" class="form-control" placeholder="Enter Visa No...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-id-no">ID No <span class="text-danger">*</span></label>
                                <input type="text" name="id_no" id="add-id-no" class="form-control" placeholder="Enter ID No...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-visa-date">Visa Date</label>
                                <input type="text" name="visa_date" id="add-visa-date" class="form-control" placeholder="Enter Visa Date...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-visa-received-date">Visa Received Date <span class="text-danger">*</span></label>
                                <input type="text" name="visa_received_date" id="add-visa-received-date" class="form-control date-mask" placeholder="Enter Visa Received Date...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-wakala-status" class="form-label">Wakala Status</label>
                                <select name="wakala_status" id="add-wakala-status" class="form-select select2" data-allow-clear="true" data-placeholder="Select Wakala Status">
                                    <option value="">Select Wakala Status</option>
                                    <option value="Wakala Completed">Wakala Completed</option>
                                    <option value="Wakala Not Completed">Wakala Not Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="">
                                <table class="table table-bordered table-condensed" id="dispItemApp">
                                    <tr>
                                        <th>Issuing Authority <span class="text-danger">*</span></th>
                                        <th>Profession <span class="text-danger">*</span></th>
                                        <th>Opening <span class="text-danger">*</span></th>
                                        <th>
                                            <button type="button" class="btn btn-sm btn-primary float-end addVisaDelegation mb-1">Add</button>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="mb-3">
                                                <select name="issuing_authority" id="add-issueing-authority" class="form-select select2" data-placeholder="Select City" data-allow-clear="true">
                                                    <option value=""></option>
                                                    <option value="Mumbai">Mumbai</option>
                                                    <option value="New Delhi">New Delhi</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-3">
                                                <select name="proff_id[]" id="add-proff-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession...">
                                                    <option value=""></option>
                                                    @foreach ($professions as $profession)
                                                        <option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="mb-3">
                                                <input type="text" name="openings[]" id="add-visa-openings" class="form-control" placeholder="Enter number of vacancies">
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>

                                </table>
                            </div>

                        </div>

                        {{-- <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="add-proff-id">Profession <span class="text-danger">*</span></label>
                                <select name="proff_id[]" id="add-proff-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession...">
                                    <option value=""></option>
                                    @foreach ($professions as $profession)
                                        <option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="add-issueing-authority">Issuing Authority <span class="text-danger">*</span></label>
                                <select name="issuing_authority" id="add-issueing-authority" class="form-select select2" data-placeholder="Select Visa Issuing Authority..." data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="Mumbai">Mumbai</option>
                                    <option value="New Delhi">New Delhi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-visa-openings" class="form-label">Opening <span class="text-danger">*</span></label>
                                <input type="text" name="openings[]" id="add-visa-openings" class="form-control" placeholder="Enter number of vacancies">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-primary float-end addVisaDelegation mb-1">Add Visa</button>
                        </div> --}}
                    </div>
                    <div class="dispVisaDelegation"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-wpcity-id">City of Work <span class="text-danger">*</span></label>
                                <select name="wpcity_id" id="add-wpcity-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select City of Work...">
                                    <option value=""></option>
                                    @foreach ($expworkcities as $expworkcity)
                                        <option value="{{ $expworkcity->id }}">{{ $expworkcity->name.' ('.$expworkcity->arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-salary">Monthly Salary</label>
                                <input type="text" name="salary" id="add-salary" class="form-control" placeholder="Enter Salary...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-notes">Notes</label>
                                <input type="text" name="notes" id="add-notes" class="form-control" placeholder="Enter Notes...">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Add Employer Canvas End -->

        <!-- Edit Employer Canvas Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editEmployerVisa" aria-labelledby="editEmployerVisaLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editEmployerVisaLabel" class="offcanvas-title">Edit Employer</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editEmployerVisaValidation" action="{{ route('admin.employer.updateVisaDetP') }}" method="POST">
                    @csrf
                    <input type="hidden" name="edit_id" id="editID">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-visa-from">Business <span class="text-danger">*</span></label>
                                <select name="businesstype" id="edit-visa-from" class="form-select select2" data-placeholder="Select Visa From...." data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="B2B Online">B2B Online</option>
                                    <option value="B2B Offline">B2B Offline</option>
                                    <option value="B2C Online">B2C Online</option>
                                    <option value="B2C Offline">B2C Offline</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-partner">Partner</label>
                                <select name="partner_office_id" id="edit-partner" class="form-select select2" data-placeholder="Select Partner...." data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->rec_off_name.' ('.$partner->rec_office_arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-careoff-id" class="form-label">Careoff</label>
                                <select name="careoff_id" id="edit-careoff-id" class="form-select select2" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-employer-name">Employer Name</label>
                                <input type="text" name="employer_name" id="edit-employer-name" class="form-control" placeholder="Enter Employer Name...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-employer-ar-name">Employer Name (Arabic) <span class="text-danger">*</span></label>
                                <input type="text" name="employer_ar_name" id="edit-employer-ar-name" class="form-control" placeholder="Enter Employer Name (Arabic)...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-visa-no">Visa No <span class="text-danger">*</span></label>
                                <input type="text" name="visa_no" id="edit-visa-no" class="form-control" placeholder="Enter Visa No...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-id-no">ID No <span class="text-danger">*</span></label>
                                <input type="text" name="id_no" id="edit-id-no" class="form-control" placeholder="Enter ID No...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-visa-date">Visa Date</label>
                                <input type="text" name="visa_date" id="edit-visa-date" class="form-control" placeholder="Enter Visa Date...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-visa-received-date">Visa Received Date <span class="text-danger">*</span></label>
                                <input type="text" name="visa_received_date" id="edit-visa-received-date" class="form-control date-mask2" placeholder="Enter Visa Received Date...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-wakala-status" class="form-label">Wakala Status</label>
                                <select name="wakala_status" id="edit-wakala-status" class="form-select select2" data-allow-clear="true" data-placeholder="Select Wakala Status">
                                    <option value="">Select Wakala Status</option>
                                    <option value="Wakala Completed">Wakala Completed</option>
                                    <option value="Wakala Not Completed">Wakala Not Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <div class="">
                                <table class="table table-bordered table-condensed" id="dispItemAppEd">
                                    <tr>
                                        <th>Issuing Authority <span class="text-danger">*</span></th>
                                        <th>Profession <span class="text-danger">*</span></th>
                                        <th>Opening <span class="text-danger">*</span></th>
                                        <th>
                                            <button type="button" class="btn btn-sm btn-primary float-end addEditVisaDelegation mb-1">Add</button>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="mb-3">
                                                <select name="issuing_authority" id="edit-issueing-authority" class="form-select select2" data-placeholder="Select City" data-allow-clear="true">
                                                    <option value=""></option>
                                                    <option value="Mumbai">Mumbai</option>
                                                    <option value="New Delhi">New Delhi</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="mb-3">
                                                <select name="proff_id[]" id="edit-proff-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession...">
                                                    <option value=""></option>
                                                    @foreach ($professions as $profession)
                                                        <option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="mb-3">
                                                <input type="text" name="openings[]" id="edit-visa-openings" class="form-control" placeholder="Enter number of vacancies">
                                            </div>
                                        </td>
                                        <td></td>
                                    </tr>

                                </table>
                            </div>

                        </div>

                        {{-- <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="edit-proff-id">Profession <span class="text-danger">*</span></label>
                                <select name="proff_id[]" id="edit-proff-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession...">
                                    <option value=""></option>
                                    @foreach ($professions as $profession)
                                        <option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="edit-issueing-authority">Issuing Authority <span class="text-danger">*</span></label>
                                <select name="issuing_authority[]" id="edit-issueing-authority" class="form-select select2" data-placeholder="Select Visa Issuing Authority..." data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="Mumbai">Mumbai</option>
                                    <option value="New Delhi">New Delhi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-visa-openings" class="form-label">Opening <span class="text-danger">*</span></label>
                                <input type="text" name="openings[]" id="edit-visa-openings" class="form-control" placeholder="Enter number of vacancies">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-primary float-end mb-1 addEditVisaDelegation">Add Visa</button>
                        </div> --}}
                    </div>
                    <div class="dispVisaDelegationEdit"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-wpcity-id">City of Work <span class="text-danger">*</span></label>
                                <select name="wpcity_id" id="edit-wpcity-id" class="form-select select2" data-allow-clear="true" data-placeholder="Select City of Work...">
                                    <option value=""></option>
                                    @foreach ($expworkcities as $expworkcity)
                                        <option value="{{ $expworkcity->id }}">{{ $expworkcity->name.' ('.$expworkcity->arname.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-salary">Monthly Salary</label>
                                <input type="text" name="salary" id="edit-salary" class="form-control" placeholder="Enter Salary...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-notes">Notes</label>
                                <input type="text" name="notes" id="edit-notes" class="form-control" placeholder="Enter Notes...">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Edit Employer Canvas End -->

        <!-- Employer Delet Start -->
        <div class="modal fade" id="deleteemployer" aria-hidden="true" aria-labelledby="deleteemployerLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <form action="{{ route('admin.employer.deletep') }}" method="GET">
                    @csrf
                    <input type="hidden" name="delete_id" id="delEmpID">
                    <div class="modal-content">
                        <div class="modal-header pb-2">
                            <h5 class="offcanvas-title" id="updateLstageLabel">Remove Employer</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <p class="text-danger">Are you sure you want to remove this employer?</p>
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
        <!-- Employer Delete End -->

        <!-- Active Employer Start -->
        <div class="modal fade" id="activemp" aria-hidden="true" aria-labelledby="activempLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <form action="{{ route('admin.employer.statusUpdate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="emp_id" id="activempEmpID">
                    <input type="hidden" name="emp_status" value="1">
                    <div class="modal-content">
                        <div class="modal-header pb-2">
                            <h5 class="offcanvas-title" id="updateLstageLabel">Active Employer</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <p class="text-danger">Are you sure you want to active this employer?</p>
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

        <!-- Update Payment Status Start -->
        <div class="modal fade" id="updatePaymentStatus" aria-hidden="true" aria-labelledby="updatePaymentStatusLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <form action="{{ route('admin.employer.paymentstatusupdate') }}" id="updatePaymentStatusIDvalid" method="POST">
                    @csrf
                    <input type="hidden" name="emp_id" id="updatePaymentStatusID">
                    <div class="modal-content">
                        <div class="modal-header pb-2">
                            <h5 class="offcanvas-title" id="updateLstageLabel">Update Payment Status</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="add-update-payment-status" class="form-label">Payment Status</label>
                                        <select name="payment_status" data-allow-clear="true" data-placeholder="Select Payment Status" id="add-update-payment-status" class="form-select select2">
                                            <option value=""></option>
                                            <option value="To Collect">To Collect</option>
                                            <option value="Unpaid">Unpaid</option>
                                            <option value="Paid">Paid</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal">Close</button>
                            {{-- <button type="button" class="btn btn-sm btn-danger" id="confirmDelete">Yes</button> --}}
                            <button type="submit" class="btn btn-sm btn-primary updateStatusBtn" id="confirmDelete">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Update Payment Status End -->

        <!-- Inactive Employer Start -->
        <div class="modal fade" id="inactivemp" aria-hidden="true" aria-labelledby="inactivempLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <form action="{{ route('admin.employer.statusUpdatep') }}" method="POST">
                    @csrf
                    <input type="hidden" name="emp_id" id="inactivempEmpID">
                    <input type="hidden" name="emp_status" value="0">
                    <div class="modal-content">
                        <div class="modal-header pb-2">
                            <h5 class="offcanvas-title" id="updateLstageLabel">Inactive Employer</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <p class="text-danger">Are you sure you want to inactive this employer?</p>
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

        <!-- Filter Panel Start -->
        <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="filterpanelLabel">Employer Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                {{-- <select id="by-profession" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Profession"> --}}
                                <select id="by-profession" class="form-select filterData select22f" multiple data-placeholder="Select Profession">
                                    {{-- <option value="">Select Department</option> --}}
                                    @foreach ($professions as $professionfilter)
                                        <option value="{{ $professionfilter->id }}" @if(isset($saveEmployeradminsavefilter) && in_array($professionfilter->id,explode(",",$saveEmployeradminsavefilter->proff_id))) selected @endif>{{ $professionfilter->eng_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-visa-issuing-authority" class="form-select filterData select22f" multiple data-placeholder="Select Visa Issuing Authority">
                                    <option value="Mumbai" @if(isset($saveEmployeradminsavefilter) && in_array("Mumbai",explode(",",$saveEmployeradminsavefilter->issuing_authority))) selected @endif>Mumbai</option>
                                    <option value="New Delhi" @if(isset($saveEmployeradminsavefilter) && in_array("New Delhi",explode(",",$saveEmployeradminsavefilter->issuing_authority))) selected @endif>New Delhi</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-city-work" class="selectpicker w-100" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select City of Work">
                                    {{-- <select id="by-city-work" class="form-select filterData select22f" multiple data-placeholder="Select City of Work"> --}}
                                    {{-- <option value="">Select Todo Label</option> --}}
                                    @foreach ($cityworkfilters as $cityworkfilter)
                                        <option value="{{ $cityworkfilter->wpcity_id }}" @if(isset($saveEmployeradminsavefilter) && in_array($cityworkfilter->wpcity_id,explode(",",$saveEmployeradminsavefilter->wpcity_id))) selected @endif>{{ $cityworkfilter->expworkname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-businesstype" class="form-select filterData select22f" multiple data-placeholder="Select Business Type">
                                    {{-- <option value="">Select Priority</option> --}}
                                    @foreach ($businesstypeFilters as $businesstypeFilter)
                                        <option value="{{ $businesstypeFilter->businesstype }}" @if(isset($saveEmployeradminsavefilter) && in_array($businesstypeFilter->businesstype,explode(",",$saveEmployeradminsavefilter->businesstype))) selected @endif>{{ $businesstypeFilter->businesstype }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-created-by" class="selectpicker w-100" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Created By">
                                    {{-- <select id="by-created-by" class="form-select filterData select22f" multiple data-placeholder="Select Created By"> --}}
                                    {{-- <option value="">Select Status</option> --}}
                                    @foreach ($createByfilters as $createByfilter)
                                        <option value="{{ $createByfilter->admin_id }}" @if(isset($saveEmployeradminsavefilter) && in_array($createByfilter->admin_id,explode(",",$saveEmployeradminsavefilter->createbyadmin))) selected @endif>{{ $createByfilter->adminName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-careoff" class="selectpicker w-100" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Careoff">
                                    {{-- <select id="by-careoff" class="form-select filterData select22f" multiple data-placeholder="Select Careoff"> --}}
                                    {{-- <option value="">Select Status</option> --}}
                                    @foreach ($careofffilters as $careofffilter)
                                        <option value="{{ $careofffilter->id }}" @if(isset($saveEmployeradminsavefilter) && in_array($careofffilter->id,explode(",",$saveEmployeradminsavefilter->careoff))) selected @endif>{{ $careofffilter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-wakalastatus" class="form-select filterData select22f" multiple data-placeholder="Select Wakala Status">
                                    {{-- <option value="">Select Status</option> --}}
                                    @foreach ($wakalastatusFilters as $wakalastatusFilter)
                                        <option value="{{ $wakalastatusFilter->wakala_status }}" @if(isset($saveEmployeradminsavefilter) && in_array($wakalastatusFilter->wakala_status,explode(",",$saveEmployeradminsavefilter->wakala_status))) selected @endif>{{ $wakalastatusFilter->wakala_status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select id="by-created-by-partner" class="selectpicker w-100" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Created By Partner">
                                    {{-- <select id="by-created-by-partner" class="form-select filterData select22f" multiple data-placeholder="Select Created By Partner"> --}}
                                    {{-- <option value="">Select Status</option> --}}
                                    @foreach ($createByPartnerfilters as $createByPartnerfilter)
                                        <option value="{{ $createByPartnerfilter->partner_id }}">{{ $createByPartnerfilter->partnerName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-">
                                <select id="by-partner-office" class="selectpicker w-100 filterData" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Select Partner Office">
                                    @foreach ($partnerofficefilters as $partnerofficefilter)
                                        <option value="{{ $partnerofficefilter->partneroffice_id }}" @if(isset($saveEmployeradminsavefilter) && in_array($partnerofficefilter->partneroffice_id,explode(",",$saveEmployeradminsavefilter->partneroffice))) selected @endif>{{ $partnerofficefilter->partnerName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <input type="text" id="visa-date-range-picket" name="visa_date_range" class="form-control bsdatpicket" @if(isset($saveEmployeradminsavefilter)) value="{{ $saveEmployeradminsavefilter->visa_date_range }}" @endif placeholder="Visa Date Range Picker" />
                            </div>

                            <div class="col-md-4 mb-3">
                                <input type="text" id="visa-received-date-range-picket" name="visa_received_date_range" class="form-control bsdatpicket" @if(isset($saveEmployeradminsavefilter)) value="{{ $saveEmployeradminsavefilter->visa_received_date_range }}" @endif placeholder="Visa Received Date Range Picker" />
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="button" class="btn btn-sm btn-primary applyfilter">Apply Filter</button> --}}
                        <button type="button" class="btn btn-warning btn-sm resetfilter">Reset Filter</button>
                        <button type="button" class="btn btn-success btn-sm savetodoFilter">Save Filter</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filter Panel End -->

    </div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/employer-visa-validation.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/custom/main.js') }}"></script> --}}

    <!-- Page JS -->
    {{-- <script src="{{ asset('admin/assets/pages/app-employer-list.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin/assets/pages/app-visadetail-list.js') }}"></script> --}}

    <script>

        function updateEmployerFilterIndicator() {

            let isFiltered =
                // Profession
                ($('#by-profession').val() && $('#by-profession').val().length > 0) ||

                // Visa Issuing Authority
                ($('#by-visa-issuing-authority').val() && $('#by-visa-issuing-authority').val().length > 0) ||

                // City of Work
                ($('#by-city-work').val() && $('#by-city-work').val().length > 0) ||

                // Business Type
                ($('#by-businesstype').val() && $('#by-businesstype').val().length > 0) ||

                // Created By
                ($('#by-created-by').val() && $('#by-created-by').val().length > 0) ||

                // Careoff
                ($('#by-careoff').val() && $('#by-careoff').val().length > 0) ||

                // Wakala Status
                ($('#by-wakalastatus').val() && $('#by-wakalastatus').val().length > 0) ||

                // Created By Partner
                ($('#by-created-by-partner').val() && $('#by-created-by-partner').val().length > 0) ||

                // Partner Office
                ($('#by-partner-office').val() && $('#by-partner-office').val().length > 0) ||

                // Status / Payment Status (status bar)
                !!$('#by-status').val() ||
                !!$('#by-payment-status').val() ||

                // Visa Date ranges
                $('#visa-date-range-picket').val() ||
                $('#visa-received-date-range-picket').val();

            if (isFiltered) {
                $('.filterpanel .filter-indicator')
                    .removeClass('d-none')
                    .addClass('d-block');
            } else {
                $('.filterpanel .filter-indicator')
                    .removeClass('d-block')
                    .addClass('d-none');
            }
        }


        $(document).ready(function(){

            updateEmployerFilterIndicator();

            var selectPicker = $('.selectpicker');

            if (selectPicker.length) {
                selectPicker.selectpicker();
            }
        });
    </script>

    <script>
        $('.visa-date').flatpickr();
        var select2 = $('.select2');
        var dateMask = $('.date-mask');
        var dateMask2 = $('.date-mask2');
        if (select2.length) {
            select2.each(function () {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>');
                $this.select2({
                    dropdownParent: $this.parent()
                });
            });
        }

        if (dateMask) {
            new Cleave(dateMask, {
                date: true,
                delimiter: '-',
                datePattern: ['Y', 'm', 'd']
            });
        }

        if (dateMask2) {
            new Cleave(dateMask2, {
                date: true,
                delimiter: '-',
                datePattern: ['Y', 'm', 'd']
            });
        }

        $('body').on('shown.bs.modal', '#filterpanel', function() {
            $(this).find('.select22f').each(function() {

                $(this).select2({
                    dropdownParent: $(this).parent()

                });
            });
        });

        $('#updatePaymentStatusIDvalid').validate({
            rules:{
                payment_status:{
                    required: true
                }
            },
            messages:{
                payment_status:{
                    required: "Please select payment status"
                }
            }
        });
    </script>

<script>

    $(document).on('click','.resetfilter',function(){
        // Filter Data Blank
        if ($('#by-profession').val() != '') {
            $('#by-profession').val('').trigger('change');
        }

        if ($('#by-visa-issuing-authority').val() != '') {
            $('#by-visa-issuing-authority').val('').trigger('change');
        }

        if ($('#by-city-work').val() != '') {
            $('#by-city-work').selectpicker('deselectAll');
        }

        if ($('#by-businesstype').val() != '') {
            $('#by-businesstype').val('').trigger('change');
        }

        if ($('#by-created-by').val() != '') {
            $('#by-created-by').selectpicker('deselectAll');
        }

        if ($('#by-careoff').val() != '') {
            $('#by-careoff').selectpicker('deselectAll');
        }

        if ($('#by-wakalastatus').val() != '') {
            $('#by-wakalastatus').val('').trigger('change');
        }

        if ($('#by-created-by-partner').val() != '') {
            $('#by-created-by-partner').val('').trigger('change');
        }

        if ($('#by-partner-office').val() != '') {
            $('#by-partner-office').selectpicker('deselectAll');
        }

        if ($('#by-status').val() != '') {
            $('#by-status').val('').trigger('change');
        }

        if ($('#by-payment-status').val() != '') {
            $('#by-payment-status').val('').trigger('change');
        }

        // Reset the Employer+ status bar back to "All" for both groups.
        $('.employerStatusCard').removeClass('active');
        $('.employerStatusCard[data-value=""]').addClass('active');


        if ($('#visa-date-range-picket').val() != '') {
            $('#visa-date-range-picket').trigger('cancel.daterangepicker');
        }

        if ($('#visa-received-date-range-picket').val() != '') {
            $('#visa-received-date-range-picket').trigger('cancel.daterangepicker');
        }

        updateEmployerFilterIndicator();

        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ route('admin.employer.resetfilterP') }}",
            method: "POST",
            type: "html",
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(data){
                if(data){

                    toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                    // Reset All field

                }
            }
        });

    });

    $(document).on('click','.savetodoFilter',function(){
        var by_profession = $('#by-profession').val();
        var by_visa_issuing_authority = $('#by-visa-issuing-authority').val();
        var by_city_work = $('#by-city-work').val();
        var by_businesstype = $('#by-businesstype').val();
        var by_created_by = $('#by-created-by').val();
        var by_careoff = $('#by-careoff').val();
        var by_wakalastatus = $('#by-wakalastatus').val();
        var by_created_by_partner = $('#by-created-by-partner').val();
        var by_partner_office = $('#by-partner-office').val();
        var visa_date_range_picket = $('#visa-date-range-picket').val();
        var visa_received_date_range_picket = $('#visa-received-date-range-picket').val();



        $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });

        jQuery.ajax({
            url: "{{ route('admin.employer.savefilterP') }}",
            method: "POST",
            type: "html",
            data: {
                "_token": "{{ csrf_token() }}",
                by_profession: by_profession,
                by_visa_issuing_authority: by_visa_issuing_authority,
                by_city_work: by_city_work,
                by_businesstype: by_businesstype,
                by_created_by: by_created_by,
                by_careoff: by_careoff,
                by_wakalastatus: by_wakalastatus,
                by_created_by_partner: by_created_by_partner,
                by_partner_office: by_partner_office,
                visa_date_range_picket: visa_date_range_picket,
                visa_received_date_range_picket: visa_received_date_range_picket
            },
            success: function(data){
                if(data){
                    toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                    updateEmployerFilterIndicator();

                }
            }
        });
    });
</script>

    <!-- Seatch Filter Start Here -->
    <script>
        $(document).ready(function(){
            // Common AJAX setup for CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });



            // Function to retrieve all filter values
            function getFilterData() {
                return {
                    page_list: $('#pagination_list').val(),
                    search_text: $('#search_text').val(),
                    by_profession: $('#by-profession').val(),
                    by_visa_issuing_authority: $('#by-visa-issuing-authority').val(),
                    by_city_work: $('#by-city-work').val(),
                    by_businesstype: $('#by-businesstype').val(),
                    by_created_by: $('#by-created-by').val(),
                    by_careoff: $('#by-careoff').val(),
                    by_wakalastatus: $('#by-wakalastatus').val(),
                    by_created_by_partner: $('#by-created-by-partner').val(),
                    by_partner_office: $('#by-partner-office').val(),
                    by_status: $('#by-status').val(),
                    by_payment_status: $('#by-payment-status').val(),
                    visa_date_range_picket: $('#visa-date-range-picket').val(),
                    visa_received_date_range_picket: $('#visa-received-date-range-picket').val()
                };
            }

            // Function to reload todo list based on filter data
            function reloadTodoList() {

                updateEmployerFilterIndicator();


                $.ajax({
                    url: "{{ route('admin.employer.listp') }}",
                    method: "GET",
                    dataType: "html",
                    data: getFilterData(),
                    success: function (data) {
                        $('.employerpaginate').html(data);
                    }
                });
            }
            // Generic event handler for change and input events on filter elements
            function bindFilterChange(selector) {
                $(selector).on('change input', function () {

                    reloadTodoList();


                });
            }
            // Bind change/input events to all filter elements
            bindFilterChange('#pagination_list');
            bindFilterChange('#search_text');
            bindFilterChange('#by-profession');
            bindFilterChange('#by-visa-issuing-authority');
            bindFilterChange('#by-city-work');
            bindFilterChange('#by-businesstype');
            bindFilterChange('#by-created-by');
            bindFilterChange('#by-careoff');
            bindFilterChange('#by-wakalastatus');
            bindFilterChange('#by-created-by-partner');
            bindFilterChange('#by-partner-office');
            bindFilterChange('#by-status');
            bindFilterChange('#by-payment-status');

            // ===============================
            // Employer+ status bar -> apply matching filter
            //
            // Each pill belongs to one group (status / payment) via
            // data-group. Clicking only clears/sets pills within that SAME
            // group — the other group's selection is left alone — so
            // Status + Payment Status combine (AND together) instead of
            // one click resetting the other.
            // ===============================
            $(document).on('click', '.employerStatusCard', function (e) {
                e.preventDefault();

                var $this = $(this);
                var group = $this.data('group');
                var value = $this.data('value');
                value = (value === undefined || value === null) ? '' : String(value);
                var wasActive = $this.hasClass('active');

                if (wasActive && !value) {
                    return; // "All" already active — avoid a duplicate request
                }

                $('.employerStatusCard[data-group="' + group + '"]').removeClass('active');

                var effectiveValue = wasActive ? '' : value;
                if (!wasActive) {
                    $this.addClass('active');
                }

                var selector = group === 'status' ? '#by-status' : '#by-payment-status';

                // Plain hidden inputs, no selectpicker/select2 involved —
                // .trigger('change') fires the bound reload exactly once.
                $(selector).val(effectiveValue).trigger('change');
            });


            // Date Range Picker for Start and End Dates with Apply and Cancel Event Handling
            // function bindDatePicker(selector) {
            //     $(selector).on('apply.daterangepicker', function (ev, picker) {
            //         $(this).val(picker.startDate.format('YYYY-MM-DD'));
            //         reloadTodoList();
            //     }).on('cancel.daterangepicker', function () {
            //         $(this).val('');
            //         reloadTodoList();
            //     });
            // }

            function bindDateRangePicker(selector) {
                $(selector).on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    reloadTodoList();
                }).on('cancel.daterangepicker', function () {
                    $(this).val('');
                    reloadTodoList();
                });
            }

            // Bind date range picker to the relevant fields
            // bindDatePicker('input[name="todo_start_date"]');
            // bindDatePicker('input[name="todo_end_date"]');
            bindDateRangePicker('input[name="visa_date_range"]');
            bindDateRangePicker('input[name="visa_received_date_range"]');

        });
    </script>
    <!-- Search Filter End Here -->

<script>
    $(document).ready(function(){
        // var bsRangePickerBasic = $('#complete-task-date-range-picket');
        var bsRangePickerBasic = $('.bsdatpicket');
        // var singledatepicket = $('.singledatepicker');
        // Basic
        if (bsRangePickerBasic.length) {
            bsRangePickerBasic.daterangepicker({
                // todayHighlight: true,
                opens: isRtl ? 'left' : 'right',
                autoUpdateInput: false,

                locale: {
                    cancelLabel: 'Clear'
                }
            });
        }
        // Single Datepicket
        // if (singledatepicket.length) {
        //     singledatepicket.daterangepicker({
        //         // todayHighlight: true,
        //         opens: isRtl ? 'left' : 'right',
        //         autoUpdateInput: false,
        //         singleDatePicker: true,
        //         locale: {
        //             cancelLabel: 'Clear',
        //             format: 'YYYY-MM-DD'
        //         }
        //     });
        // }


    });
</script>

    <script>
        $(document).ready(function(){

            $('#deleteemployer').on("show.bs.modal",function(e){
                var deleteID = $(e.relatedTarget).data('id');

                // alert(deleteID);

                $('#delEmpID').val(deleteID);
            });

            $('#activemp').on("show.bs.modal",function(e){
                var deleteID = $(e.relatedTarget).data('id');

                $('#activempEmpID').val(deleteID);
            });

            $('#inactivemp').on("show.bs.modal",function(e){
                var deleteID = $(e.relatedTarget).data('id');

                $('#inactivempEmpID').val(deleteID);
            });

            $('#updatePaymentStatus').on('show.bs.modal',function(e){
                var updateID = $(e.relatedTarget).data('id');

                $('#updatePaymentStatusID').val(updateID);

                // Get Update Status
                $.ajax({
                    url: "{{ route('admin.employer.getpaymentstatus') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        id: updateID
                    },
                    success: function(data){
                        $('#add-update-payment-status').val(data.payment_status).change();
                        if (data.payment_status == 'Paid') {
                            $('#add-update-payment-status').prop('disabled',true);
                            $('.updateStatusBtn').prop('disabled',true);
                        } else {
                            $('.updateStatusBtn').prop('disabled',false);
                            $('#add-update-payment-status').prop('disabled',false);

                        }
                    }
                });

            });

        });
    </script>

    <script>
        let rowMin = 0;
        let rowMax = 1000;

        // Add Visa Delegation row
        // $(document).on('click','.addVisaDelegation',function(){
        //     var html = '';
        //         html += '<div class="row">';
        //         html += '<div class="col-md-4"><div class="mb-3">';
        //         html += '<label class="form-label" for="add-proff-id'+rowMin+'">Profession <span class="text-danger">*</span></label><select name="proff_id[]" id="add-proff-id'+rowMin+'" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession..."><option value=""></option>@foreach ($professions as $profession)<option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>@endforeach</select></div></div>';
        //         html += '<div class="col-md-4"><div class="mb-3">';
        //         html += '<label class="form-label" for="add-issueing-authority'+rowMin+'">Issuing Authority <span class="text-danger">*</span></label><select name="issuing_authority[]" id="add-issueing-authority'+rowMin+'" class="form-select select2" data-placeholder="Select Visa Issuing Authority..." data-allow-clear="true"><option value=""></option><option value="Mumbai">Mumbai</option><option value="New Delhi">New Delhi</option></select></div></div>';
        //         html += '<div class="col-md-4"><div class="mb-3">';
        //         html += '<label for="add-visa-openings'+rowMin+'" class="form-label">Opening <span class="text-danger">*</span></label><input type="text" name="openings[]" id="add-visa-openings'+rowMin+'" class="form-control" placeholder="Enter number of vacancies"></div></div>';
        //         html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger float-end remove">Remove</button></div></div>';



        //     $('.dispVisaDelegation').append(html);
        //     initializeSelect2();
        //     rowMin++;
        //     rowMax--;
        //     $('.addVisaDelegation').prop('disabled', rowMax <= 1);
        // });

        // Table Row Start
        $(document).on('click','.addVisaDelegation',function(){
            var html = '';
                html += '<tr>';

                html += '<td></td>';

                html += '<td><div class="mb-3">';
                html += '<select name="proff_id[]" id="add-proff-id'+rowMin+'" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession..."><option value=""></option>@foreach ($professions as $profession)<option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>@endforeach</select></div></td>';

                html += '<td><div class="mb-3">';
                html += '<input type="text" name="openings[]" id="add-visa-openings'+rowMin+'" class="form-control" placeholder="Enter number of vacancies"></div></td>';
                html += '<td><button type="button" class="btn btn-sm btn-danger float-end remove">Remove</button></td></tr>';


            $('#dispItemApp').append(html);
            initializeSelect2();
            rowMin++;
            rowMax--;
            $('.addVisaDelegation').prop('disabled', rowMax <= 1);
        });


        // Initialize Select2
        function initializeSelect2() {
            $('.select2').each(function () {
                const $this = $(this);
                // $this.select2({
                $this.wrap('<div class="position-relative"></div>').select2({
                    dropdownParent: $this.parent()
                });
            });
        }

        // Remove Visa Delegation row
        $(document).on('click', '.remove', function () {

            $(this).closest('tr').remove();
            // $(this).closest('.row').remove();

            rowMin++;
            rowMin--;
            $('.addVisaDelegation').prop('disabled', rowMax === 0);
        });

    </script>

    <script>
        let rowMin2 = 0;
        let rowMax2 = 1000;

        // Add Visa Delegation row
        // $(document).on('click','.addEditVisaDelegation',function(){
        //     var html = '';
        //         html += '<div class="row">';
        //         html += '<div class="col-md-4"><div class="mb-3">';
        //         html += '<label class="form-label" for="add-edit-proff-id'+rowMin2+'">Profession <span class="text-danger">*</span></label><select name="proff_id[]" id="add-edit-proff-id'+rowMin2+'" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession..."><option value=""></option>@foreach ($professions as $profession)<option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>@endforeach</select></div></div>';
        //         html += '<div class="col-md-4"><div class="mb-3">';
        //         html += '<label class="form-label" for="add-edit-issueing-authority'+rowMin2+'">Issuing Authority <span class="text-danger">*</span></label><select name="issuing_authority[]" id="add-edit-issueing-authority'+rowMin2+'" class="form-select select2" data-placeholder="Select Visa Issuing Authority..." data-allow-clear="true"><option value=""></option><option value="Mumbai">Mumbai</option><option value="New Delhi">New Delhi</option></select></div></div>';
        //         html += '<div class="col-md-4"><div class="mb-3">';
        //         html += '<label for="add-edit-visa-openings'+rowMin2+'" class="form-label">Opening <span class="text-danger">*</span></label><input type="text" name="openings[]" id="add-edit-visa-openings'+rowMin2+'" class="form-control" placeholder="Enter number of vacancies"></div></div>';
        //         html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger float-end remove2">Remove</button></div>'
        //         html += '</div>'

        //     $('.dispVisaDelegationEdit').append(html);
        //     initializeSelect2();
        //     rowMin2++;
        //     rowMax2--;
        //     $('.addEditVisaDelegation').prop('disabled', rowMax2 <= 1);
        // });

        $(document).on('click','.addEditVisaDelegation',function(){
            var html = '';
                html += '<tr>';
                html += '<td></td>';
                html += '<td><div class="mb-3">';
                html += '<select name="proff_id[]" id="add-edit-proff-id'+rowMin2+'" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession..."><option value=""></option>@foreach ($professions as $profession)<option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>@endforeach</select></div></td>';
                html += '<td><div class="mb-3">';
                html += '<input type="text" name="openings[]" id="add-edit-visa-openings'+rowMin2+'" class="form-control" placeholder="Enter number of vacancies"></div></td>';
                html += '<td><button type="button" class="btn btn-sm btn-danger float-end remove2">Remove</button></td>'
                html += '</tr>';

            $('#dispItemAppEd').append(html);
            initializeSelect2();
            rowMin2++;
            rowMax2--;
            $('.addEditVisaDelegation').prop('disabled', rowMax2 <= 1);
        });

        // Initialize Select2
        function initializeSelect2() {
            $('.select2').each(function () {
                const $this = $(this);
                // $this.select2({
                $this.wrap('<div class="position-relative"></div>').select2({
                    dropdownParent: $this.parent()
                });
            });
        }

        // Remove Visa Delegation row
        $(document).on('click', '.remove2', function () {

            // $(this).closest('.row').remove();
            $(this).closest('tr').remove();

            rowMin++;
            rowMin--;
            $('.addEditVisaDelegation').prop('disabled', rowMax === 0);
        });

    </script>

    <script>
        $(document).ready(function(){
            $('#editEmployerVisa').on('show.bs.offcanvas',function(e){
                var edit_id = $(e.relatedTarget).data('id');

                $('#editID').val(edit_id);

                jQuery.ajax({
                    url: "{{ route('admin.employer.editVisaDetp') }}",
                    method: "get",
                    type: "html",
                    data: {
                        id: edit_id
                    },
                    // success: function(data){

                    //     $('#edit-visa-from').val(data.post.businesstype).change();
                    //     $('#edit-partner').val(data.post.partner_office_id).change();
                    //     $('#edit-careoff-id').val(data.post.careoff_id).change();
                    //     $('#edit-employer-name').val(data.post.employer_name);
                    //     $('#edit-employer-ar-name').val(data.post.employer_ar_name);
                    //     $('#edit-visa-no').val(data.post.visa_no);
                    //     $('#edit-id-no').val(data.post.id_no);
                    //     $('#edit-visa-date').val(data.post.visa_date);
                    //     $('#edit-visa-received-date').val(data.post.visa_received_date);
                    //     // $('#edit-proff-id').val(data.proff_id).change();
                    //     // $('#edit-issueing-authority').val(data.issuing_authority).change();
                    //     $('#edit-wpcity-id').val(data.post.wpcity_id).change();
                    //     $('#edit-wakala-status').val(data.post.wakala_status).change();
                    //     $('#edit-salary').val(data.post.salary);
                    //     $('#edit-notes').val(data.post.notes);

                    //     const proff_ids = data.post.proff_id.split(",");
                    //     const issuing_authos = data.post.issuing_authority.split(",");
                    //     if (data.post.openings != null) {
                    //         var visa_openings = data.post.openings.split(",");
                    //         $('#edit-visa-openings').val(visa_openings[0]);
                    //     }else{
                    //         $('#edit-visa-openings').val(null);
                    //     }



                    //     $('#edit-proff-id').val(proff_ids[0]).change();
                    //     $('#edit-issueing-authority').val(issuing_authos[0]).change();


                    //     // display dynamic created visa delegation work on tomorrow
                    //     var dynamic_visa_delegation_var = "";

                    //     for (let i = 1; i < proff_ids.length; i++) {
                    //         if (issuing_authos[i] == 'Mumbai') {
                    //             var mumbaiselected = "selected";
                    //         } else {
                    //             var mumbaiselected = "";
                    //         }
                    //         if (issuing_authos[i] == 'New Delhi') {
                    //             var delhiselected = "selected";
                    //         } else {
                    //             var delhiselected = "";
                    //         }

                    //         if (data.post.openings != null) {
                    //             var visaOpenings = visa_openings[i];
                    //         } else {
                    //             var visaOpenings = "";
                    //         }



                    //         dynamic_visa_delegation_var += '<div class="row">';
                    //         dynamic_visa_delegation_var += '<div class="col-md-4"><div class="mb-3">';
                    //         dynamic_visa_delegation_var += '<label class="form-label" for="edit-proff-id'+i+'">Profession <span class="text-danger">*</span></label><select name="proff_id[]" id="edit-proff-id'+i+'" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession..."><option value=""></option>';

                    //         // fetch profession with selected
                    //         data.professions.forEach(function(profession){
                    //             if (profession.id == proff_ids[i]) {
                    //                 var selectedProf = "selected";
                    //             }else{
                    //                 var selectedProf = "";
                    //             }

                    //             dynamic_visa_delegation_var += '<option value="'+profession.id+'" '+selectedProf+'>'+profession.eng_name+' ('+profession.ar_name+')</option>';

                    //         });

                    //         dynamic_visa_delegation_var += '</select></div></div>';
                    //         dynamic_visa_delegation_var += '<div class="col-md-4"><div class="mb-3">';
                    //         dynamic_visa_delegation_var += '<label class="form-label" for="edit-issueing-authority'+i+'">Issuing Authority <span class="text-danger">*</span></label><select name="issuing_authority[]" id="edit-issueing-authority'+i+'" class="form-select select2" data-placeholder="Select Visa Issuing Authority..." data-allow-clear="true"><option value=""></option><option value="Mumbai" '+mumbaiselected+'>Mumbai</option><option value="New Delhi" '+delhiselected+'>New Delhi</option></select></div></div>';

                    //         dynamic_visa_delegation_var += '<div class="col-md-4"><div class="mb-3">';
                    //         dynamic_visa_delegation_var += '<label for="edit-visa-openings'+i+'" class="form-label">Opening <span class="text-danger">*</span></label><input type="text" name="openings[]" id="edit-visa-openings'+i+'" value="'+visaOpenings+'" class="form-control" placeholder="Enter number of vacancies"></div></div>';
                    //         dynamic_visa_delegation_var += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger float-end remove2e">Remove</button></div></div>';


                    //     }

                    //     $('.dispVisaDelegationEdit').html(dynamic_visa_delegation_var);

                    //     var select2ae1 = $('.select2');
                    //     if (select2ae1.length) {
                    //         select2ae1.each(function () {
                    //             var $this = $(this);
                    //             $this.wrap('<div class="position-relative"></div>').select2({
                    //                 dropdownParent: $this.parent()
                    //             });
                    //         });
                    //     }
                    // }
                    success: function(data){

                        $('#edit-visa-from').val(data.post.businesstype).change();
                        $('#edit-partner').val(data.post.partneroffice_id).change();
                        $('#edit-careoff-id').val(data.post.careoff_id).change();
                        $('#edit-employer-name').val(data.post.employer_name);
                        $('#edit-employer-ar-name').val(data.post.employer_ar_name);
                        $('#edit-visa-no').val(data.post.visa_no);
                        $('#edit-id-no').val(data.post.id_no);
                        $('#edit-visa-date').val(data.post.visa_date);
                        $('#edit-visa-received-date').val(data.post.visa_received_date);
                        // $('#edit-proff-id').val(data.proff_id).change();
                        // $('#edit-issueing-authority').val(data.issuing_authority).change();
                        $('#edit-wpcity-id').val(data.post.wpcity_id).change();
                        $('#edit-wakala-status').val(data.post.wakala_status).change();
                        $('#edit-salary').val(data.post.salary);
                        $('#edit-notes').val(data.post.notes);

                        const proff_ids = data.post.proff_id.split(",");
                        const issuing_authos = data.post.issuing_authority.split(",");
                        if (data.post.openings != null) {
                            var visa_openings = data.post.openings.split(",");
                            $('#edit-visa-openings').val(visa_openings[0]);
                        }else{
                            $('#edit-visa-openings').val(null);
                        }




                        $('#edit-proff-id').val(proff_ids[0]).change();
                        $('#edit-issueing-authority').val(issuing_authos[0]).change();


                        // display dynamic created visa delegation work on tomorrow
                        var dynamic_visa_delegation_var = "";

                        for (let i = 1; i < proff_ids.length; i++) {

                            if (data.post.openings != null) {
                                var visaOpenings = visa_openings[i];
                            } else {
                                var visaOpenings = "";
                            }


                            dynamic_visa_delegation_var += '<tr>';
                            dynamic_visa_delegation_var += '<td></td>';
                            dynamic_visa_delegation_var += '<td><div class="mb-3">';
                            dynamic_visa_delegation_var += '<select name="proff_id[]" id="edit-proff-id'+i+'" class="form-select select2" data-allow-clear="true" data-placeholder="Select Profession..."><option value=""></option>';

                            // fetch profession with selected
                            data.professions.forEach(function(profession){
                                if (profession.id == proff_ids[i]) {
                                    var selectedProf = "selected";
                                }else{
                                    var selectedProf = "";
                                }

                                dynamic_visa_delegation_var += '<option value="'+profession.id+'" '+selectedProf+'>'+profession.eng_name+' ('+profession.ar_name+')</option>';

                            });

                            dynamic_visa_delegation_var += '</select></div></td>';

                            dynamic_visa_delegation_var += '<td><div class="mb-3">';
                            dynamic_visa_delegation_var += '<input type="text" name="openings[]" id="edit-visa-openings'+i+'" value="'+visaOpenings+'" class="form-control" placeholder="Enter number of vacancies"></div></td>';
                            dynamic_visa_delegation_var += '<td><button type="button" class="btn btn-sm btn-danger float-end remove2e">Remove</button></td></tr>';


                        }

                        // $('.dispVisaDelegationEdit').html(dynamic_visa_delegation_var);
                        $('#dispItemAppEd').append(dynamic_visa_delegation_var);
                        var select2ae1 = $('.select2');
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

                // remove button

                $(document).on('click','.remove2e',function(){
                    // $(this).closest('.row').remove();
                    $(this).closest('tr').remove();
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

            $('#booking-statusf').click(function(){
                $('.booking-status-div').toggle();
            });

            $('#booking-datef').click(function(){
                $('.booking-date-div').toggle();
            });

            $('#candidate-namef').click(function(){
                $('.candidate-name-div').toggle();
            });

            // Basic Select
            $('#default-chk').click(function(){
                if ($('#booking-datef:checkbox:checked').length > 0) {
                    $('#booking-datef').trigger('click');
                }

                if ($('#candidate-namef:checkbox:checked').length > 0) {
                    $('#candidate-namef').trigger('click');
                }


                if($('#countryf:checkbox:checked').length > 0){

                }else{
                    $('#countryf').trigger('click');
                }

                if($('#cityf:checkbox:checked').length > 0){

                }else{
                    $('#cityf').trigger('click');
                }

                if($('#booking-statusf:checkbox:checked').length > 0){

                }else{
                    $('#booking-statusf').trigger('click');
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

                $('input[name="booking-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="booking-datef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="candidate-namef"]').each(function () {
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
                if($('#booking-statusf:checkbox:checked').length > 0){
                    $('#booking-statusf').trigger('click');
                }
                if($('#booking-datef:checkbox:checked').length > 0){
                    $('#booking-datef').trigger('click');
                }

                if($('#candidate-namef:checkbox:checked').length > 0){
                    $('#candidate-namef').trigger('click');
                }

            });

            // Update Checkbox
            $('#update-chk').click(function(){
                var countryf = $('#countryf:checked').val();
                var cityf = $('#cityf:checked').val();
                var booking_statusf = $('#booking-statusf:checked').val();
                var booking_datef = $('#booking-datef:checked').val();
                var candidate_namef = $('#candidate-namef:checked').val();


                jQuery.ajax({
                    url:"{{ url('admin/employer/filterList/update') }}",
                    method: 'post',
                    type: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        countryf: countryf,
                        cityf: cityf,
                        booking_statusf: booking_statusf,
                        booking_datef: booking_datef,
                        candidate_namef: candidate_namef,
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
