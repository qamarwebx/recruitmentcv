@extends('layout.admin.admin_layout')

@section('title','Employer+')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />

    @if (Auth::guard('admin')->user()->user_type == 2)

        @if (isset($perm) && $perm->delete_employer == 0)
        <style>
        .delemployer{
            display: none !important;
        }
        </style>
        @endif
    @endif

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
        <!-- Employer List Table -->
        @php
            $businessFilters = DB::table('visadetails')->select('businesstype')->groupBy('businesstype')->where('businesstype','!=','')->get();
            $partnerFilters = DB::table('visadetails as visaDetf1')->leftJoin('partners as partner','partner.id','=','visaDetf1.partner_office_id')->select('partner.id','partner.rec_off_name','partner.rec_office_arname')->groupBy('partner.id')->groupBy('partner.rec_off_name')->groupBy('partner.rec_office_arname')->where('visaDetf1.partner_office_id','!=','')->get();
            $professionFilters = DB::table('visadetails as visaDetf2')->leftJoin('professions as profession','profession.id','=','visaDetf2.proff_id')->select('profession.id','profession.eng_name','profession.ar_name')->groupBy('profession.id')->groupBy('profession.eng_name')->groupBy('profession.ar_name')->where('visaDetf2.proff_id','!=','')->get();

        @endphp
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Search Filter</h5>
                <div class="row">
                    {{-- <div class="col-md-3 mb-3 candidate-name-div" @if(isset($filter_user) && $filter_user->candidate_name_filter == 1) @else style="display:none" @endif>
                        <select name="" id="by-candidate" class="form-select select2">
                            <option value="">Select Candidate</option>
                            @foreach ($candidates as $candidate)
                                @php
                                    $candDet = DB::table('candidates')->where('id','=',$candidate->cand_id)->first();
                                    $countCand = DB::table('bookings')->where('cand_id','=',$candidate->cand_id)->count();
                                @endphp
                                @if ($candDet)
                                    <option value="{{ $candDet->id }}">{{ $candDet->cand_name.' ('.$countCand.')' }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 country-div" @if(isset($filter_user) && $filter_user->country_filter == 1) @else style="display:none" @endif>
                        <select name="" id="by-country" class="form-select select2">
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
                        <select name="" id="by-city" class="form-select select2">
                            <option value="">Select City</option>
                            @foreach ($cityfs as $cityf)
                                @php
                                    $cityf2 = DB::table('cities')->where('id','=',$cityf->city_id)->first();
                                @endphp
                                <option value="{{ $cityf2->id }}">{{ $cityf2->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 booking-status-div" @if(isset($filter_user) && $filter_user->booking_status_filter == 1) @else style="display:none" @endif>
                        <select name="" id="by-booking-status" class="form-select select2">
                            <option value="">Booking Status</option>
                            @foreach ($bookingsts as $bookingst)
                                @if ($bookingst->booking_status == 1)
                                    <option value="Confirm">Confirm</option>
                                @elseif($bookingst->booking_status == 2)
                                    <option value="Cancel">Cancel</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 booking-date-div" @if(isset($filter_user) && $filter_user->booking_date_filter == 1) @else style="display:none" @endif>
                        <input type="text" name="" id="by-booking-date" class="form-control createdate-picker" placeholder="Booking date...">
                    </div> --}}

                    <div class="col-md-3 mb-3">
                        <select name="" id="" class="form-select select2">
                            <option value="">Select Business</option>
                            @foreach ($businessFilters as $businessFilter)
                                <option value="{{ $businessFilter->businesstype }}">{{ $businessFilter->businesstype }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <select name="" id="" class="form-select select2">
                            <option value="">Select Partner</option>
                            @foreach ($partnerFilters as $partnerFilter)
                                <option value="{{ $partnerFilter->id }}">{{ $partnerFilter->rec_off_name.' ('.$partnerFilter->rec_office_arname.')' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <select name="" id="" class="form-select select2">
                            <option value="">Select Profession</option>
                            @foreach ($professionFilters as $professionFilter)
                                <option value="{{ $professionFilter->id }}">{{ $professionFilter->eng_name.' ('.$professionFilter->ar_name.')' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <select name="" id="issuing_authority2" class="form-select select2">
                            <option value="">Select Issuing Authority</option>
                                <option value="Mumbai">Mumbai</option>
                                <option value="New Delhi">New Delhi</option>


                        </select>
                    </div>

                </div>
            </div>
            {{-- <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Employer</th>
                            <th>City</th>
                            <th>Mobile</th>
                            <th>Candidate</th>
                            <th>Passport No</th>
                            <th>Reference No</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Country ID</th>
                            <th>City ID</th>
                            <th>Candidate ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div> 13/08/2024 --}}
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Employer</th>
                            <th>Partner</th>
                            <th>Business</th>
                            <th>Visa No</th>
                            <th>ID No</th>
                            <th>Profession</th>
                            <th>Issuing Authority</th>
                            <th>Visa Date</th>
                            <th>Cty Of Work</th>
                            <th>Salary</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
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
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
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
                                <label class="form-label" for="add-visa-received-date">Visa Received Date</label>
                                <input type="text" name="visa_received_date" id="add-visa-received-date" class="form-control" placeholder="Enter Visa Received Date...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-wakala-status" class="form-label">Wakala Status</label>
                                <select name="wakala_status" id="add-wakala-status" class="form-select select2" data-allow-clear="true" data-placeholder="Select Wakala Status">
                                    <option value="">Select Wakala Status</option>
                                    <option value="1">Wakala Completed</option>
                                    <option value="0">Wakala Not Completed</option>
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
                                                <select name="issuing_authority" id="add-issueing-authority" class="form-select select2" data-placeholder="Select Issuing Authority..." data-allow-clear="true">
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
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editEmployerVisa" aria-labelledby="editEmployerVisaLabel">
            <div class="offcanvas-header">
                <h5 id="editEmployerVisaLabel" class="offcanvas-title">Edit Employer</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editEmployerVisaValidation" action="{{ route('admin.employer.updateVisaDet') }}" method="POST">
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
                                <label class="form-label" for="edit-visa-received-date">Visa Received Date</label>
                                <input type="text" name="visa_received_date" id="edit-visa-received-date" class="form-control" placeholder="Enter Visa Received Date...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-wakala-status" class="form-label">Wakala Status</label>
                                <select name="wakala_status" id="edit-wakala-status" class="form-select select2" data-allow-clear="true" data-placeholder="Select Wakala Status">
                                    <option value="">Select Wakala Status</option>
                                    <option value="1">Wakala Completed</option>
                                    <option value="0">Wakala Not Completed</option>
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
                                                <select name="issuing_authority" id="edit-issueing-authority" class="form-select select2" data-placeholder="Select Issuing Authority..." data-allow-clear="true">
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
        <div class="modal fade" id="deleteemployer" aria-hidden="true" aria-labelledby="deleteemployerLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
                <form action="{{ route('admin.employer.delete') }}" method="GET">
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
        <div class="modal fade" id="activemp" aria-hidden="true" aria-labelledby="activempLabel" tabindex="-1">
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

        <!-- Inactive Employer Start -->
        <div class="modal fade" id="inactivemp" aria-hidden="true" aria-labelledby="inactivempLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
                <form action="{{ route('admin.employer.statusUpdate') }}" method="POST">
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
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/employer-visa-validation.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/custom/main.js') }}"></script> --}}

    <!-- Page JS -->
    {{-- <script src="{{ asset('admin/assets/pages/app-employer-list.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/pages/app-visadetail-list.js') }}"></script>


    <script>
        $('.visa-date').flatpickr();
    </script>

    <script>
        $(document).ready(function(){

            $('#deleteemployer').on("show.bs.modal",function(e){
                var deleteID = $(e.relatedTarget).data('id');

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
                    url: "{{ route('admin.employer.editVisaDet') }}",
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
                        $('#edit-partner').val(data.post.partner_office_id).change();
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
