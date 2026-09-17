@extends('layout.admin.admin_layout')

@section('title','Order')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($perm) && $perm->booking_confirm == 0)
            <style>
                .bkconfirm{
                    display: none !important;
                }
            </style>
        @endif
        @if (isset($perm) && $perm->add_visa_details == 0)
            <style>
                .addvsdet{
                    display: none !important;
                }
            </style>
        @endif
        @if (isset($perm) && $perm->add_payment == 0)
            <style>
                .addpayment{
                    display: none !important;
                }
            </style>
        @endif
        @if (isset($perm) && $perm->cancel_booking == 0)
            <style>
                .cancelbk{
                    display: none !important;
                }
            </style>
        @endif
        @if (isset($perm) && $perm->replace_candidate == 0)
            <style>
                .repcand{
                    display: none !important;
                }
            </style>
        @endif
    @endif

    <style>
        .booking-list {
            margin-left: 50px;
        }

        /* Ribbon Class Start */
        .ribbon {
            width: 150px;
            height: 150px;
            overflow: hidden;
            position: absolute;

        }
        .ribbon::before,
        .ribbon::after {
            position: absolute;
            z-index: -1;
            content: '';
            display: block;
            /* border: 5px solid #2980b9; */
            border: 5px solid #013803;
        }
        .ribbon span {
            position: absolute;
            display: block;
            width: 225px;
            padding: 15px 0;
            /* background-color: #3498db; */
            background-color: #0a8703;
            box-shadow: 0 5px 10px rgba(0,0,0,.1);
            color: #fff;
            font: 700 18px/1 'Lato', sans-serif;
            text-shadow: 0 1px 1px rgba(0,0,0,.2);
            text-transform: uppercase;
            text-align: center;
        }
        .ribbon-top-left {
            top: -10px;
            left: -10px;
        }
        .ribbon-top-left::before,
        .ribbon-top-left::after {
            border-top-color: transparent;
            border-left-color: transparent;
        }
        .ribbon-top-left::before {
            top: 0;
            right: 0;
        }
        .ribbon-top-left::after {
            bottom: 0;
            left: 0;
        }
        .ribbon-top-left span {
            right: -25px;
            top: 30px;
            transform: rotate(-45deg);
        }
        /* Ribbon Class End */

        /* ==============================
        BOOKING STATUS BAR
        Compact toolbar layout (same pattern as the Todo/Leads/Testimonial
        modules): one row, label inline to the left of its pills.
        ============================== */
        .booking-filterbar {
            padding: 8px 14px;
        }

        .booking-filterbar-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .booking-filterbar-label {
            flex: 0 0 auto;
            min-width: 74px;
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
            .booking-filterbar-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .booking-filterbar-label {
                min-width: 0;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">

        @php
            $bookingStatusOptions = [
                0 => ['label' => 'Pending',   'color' => '#FF9F43'],
                1 => ['label' => 'Confirmed', 'color' => '#28C76F'],
                2 => ['label' => 'Cancelled', 'color' => '#EA5455'],
            ];

            // $activeBookingStatus is passed in from the controller (the
            // real $request->bookingStatus for this response) so the
            // correct pill starts active instead of always defaulting to
            // "All" regardless of the applied filter.
            $activeBookingStatus = $activeBookingStatus ?? '';
        @endphp

        <!-- Booking Status Bar Start -->
        <div class="card mb-3">
            <div class="card-body booking-filterbar">
                <div class="booking-filterbar-row">
                    <span class="booking-filterbar-label">Status</span>
                    <div class="candidate-status-grid">
                        <div class="status-item">
                            <a href="javascript:void(0)"
                               class="status-btn bookingStatusCard{{ $activeBookingStatus === '' ? ' active' : '' }}"
                               style="border-color:#6C757D;color:#6C757D;--tsc-color:#6C757D;"
                               data-value="">
                                <span>All</span>
                                <strong>{{ number_format($bookingStatusSummary['total'] ?? 0) }}</strong>
                            </a>
                        </div>

                        @foreach ($bookingStatusOptions as $statusValue => $statusOption)
                            <div class="status-item">
                                <a href="javascript:void(0)"
                                   class="status-btn bookingStatusCard{{ $activeBookingStatus === (string) $statusValue ? ' active' : '' }}"
                                   style="border-color:{{ $statusOption['color'] }};color:{{ $statusOption['color'] }};--tsc-color:{{ $statusOption['color'] }};"
                                   data-value="{{ $statusValue }}">
                                    <span>{{ $statusOption['label'] }}</span>
                                    <strong>{{ number_format($bookingStatusSummary['statuses'][$statusValue] ?? 0) }}</strong>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="by-booking-status" class="search-filter" value="{{ $activeBookingStatus }}">
        <!-- Booking Status Bar End -->

        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>New Order</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">{{ $newOrder }}</h4>
                        <span class="text-success">(+18%)</span>
                        </div>
                        {{-- <span>Last week analytics </span> --}}
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
                        <span>Todays Order</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">{{ $todayOrder }}</h4>
                        <span class="text-danger">(-14%)</span>
                        </div>
                        {{-- <span>Last week analytics</span> --}}
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
                            <span>All Order</span>
                            <div class="d-flex align-items-center my-1">
                            <h4 class="mb-0 me-2">{{ $allOrder }}</h4>
                            <span class="text-success">(+29%)</span>
                            </div>
                            {{-- <span>Total Users</span> --}}
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
                        <span>Cancelled Order</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">{{ $cancelOrder }}</h4>
                        <span class="text-success">(+42%)</span>
                        </div>
                        {{-- <span>Last week analytics</span> --}}
                    </div>
                    <span class="badge bg-label-warning rounded p-2">
                        <i class="ti ti-user-exclamation ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div>
        </div>
        @php
            $customerFilter = DB::table('bookings')->select('user_id')->groupBy("user_id")->where('status','=','0')->where('visa_status','=','0')->get();
            $candidateFilter = DB::table('bookings')->select('cand_id')->groupBy("cand_id")->where('status','=','0')->where('visa_status','=','0')->get();
            $bookingRefFilter = DB::table('bookings')->select('reference_no')->groupBy("reference_no")->where('status','=','0')->where('visa_status','=','0')->get();
            $professionFilter = DB::table('bookings as booking')
                ->leftJoin('candidates as cand','cand.id','=','booking.cand_id')
                ->leftJoin('professions as proff','cand.jobtype_id','proff.id')
                ->groupBy('proff.id','proff.eng_name')
                ->select('proff.id','proff.eng_name')
                ->where('booking.status','=','0')->where('booking.visa_status','=','0')
                ->get();
            $bookingOrderStatus = DB::table('bookings as booking')
                ->leftjoin('order_statuses as ordStatus','booking.ord_status_id','=','ordStatus.id')
                ->groupBy('ordStatus.id','ordStatus.ord_status')
                ->select('ordStatus.id','ordStatus.ord_status')
                ->where('booking.status','=','0')->where('booking.visa_status','=','0')
                ->get();
            $bookingpaystatus = DB::table('bookings')->select('payment_status')->groupBy("payment_status")->where('status','=','0')->where('visa_status','=','0')->get();
            $CityFilter = DB::table('bookings as booking')
                ->leftjoin('expecworkcities as expwp','booking.worklocation','=','expwp.id')
                ->groupBy('expwp.id','expwp.name')
                ->select('expwp.id','expwp.name')
                ->where('booking.status','=','0')->where('booking.visa_status','=','0')
                ->where('booking.worklocation','!=','')
                ->get();
            $pertnerFilter = DB::table('bookings as booking')
                ->leftjoin('partners as partner','booking.partner_id','=','partner.id')
                ->groupBy('partner.id','partner.rec_off_name')
                ->select('partner.id','partner.rec_off_name')
                ->where('booking.status','=','0')->where('booking.visa_status','=','0')
                ->where('booking.partner_id','!=','')
                ->get();
        @endphp
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">Search Filter</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3 partner-div" @if(isset($bookingFilter) && $bookingFilter->partner_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-search-partner" class="form-select search-filter select22">
                            <option value="">Select Partner</option>
                            @foreach ($pertnerFilter as $pertnerFilter1)
                                <option value="{{ $pertnerFilter1->id }}">{{ $pertnerFilter1->rec_off_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3 customer-div" @if(isset($bookingFilter) && $bookingFilter->customer_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-search-customer" class="form-select search-filter select22">
                            <option value="">Select Customer</option>
                            @foreach ($customerFilter as $customerFilter1)
                                @php
                                    $customerName = DB::table('users')->where('id','=',$customerFilter1->user_id)->first();
                                @endphp
                                <option value="{{ $customerName->id ?? '' }}">{{ $customerName->name ?? ''}}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-3 mb-3 candidate-div">
                        <select name="" id="by-search-candidate" class="form-select search-filter select22">
                            <option value="">Select Candidate</option>
                            @foreach ($candidateFilter as $candidateFilt)
                                @php
                                    $candData = DB::table('candidates')->where('id','=',$candidateFilt->cand_id)->first();
                                @endphp
                                <option value="{{ $candData->id }}">{{ $candData->cand_name }}</option>
                            @endforeach

                        </select>
                    </div> --}}
                    {{-- <div class="col-md-3 mb-3 passport-no-div">
                        <select name="" id="by-search-pass-no" class="form-select search-filter select22">
                            <option value="">Select Passport No</option>
                            @foreach ($candidateFilter as $candidateFilt2)
                                @php
                                    $candData2 = DB::table('candidates')->where('id','=',$candidateFilt2->cand_id)->first();
                                @endphp
                                <option value="{{ $candData2->id }}">{{ $candData2->pass_no }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    <div class="col-md-3 mb-3 order-no-div" @if(isset($bookingFilter) && $bookingFilter->orderno_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-search-order-no" class="form-select search-filter select22">
                            <option value="">Select Order No</option>
                            @foreach ($bookingRefFilter as $bookingRefFilter1)
                                <option value="{{ $bookingRefFilter1->reference_no }}">{{ $bookingRefFilter1->reference_no }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-3 mb-3 reference-no-div">
                        <select name="" id="by-search-ref-no" class="form-select search-filter select22">
                            <option value="">Select Referrence No</option>
                            @foreach ($candidateFilter as $candidateFilt3)
                                @php
                                    $candData3 = DB::table('candidates')->where('id','=',$candidateFilt3->cand_id)->first();
                                @endphp
                                <option value="{{ $candData3->id }}">{{ $candData3->reference_no }}</option>
                            @endforeach
                        </select>
                    </div> --}}
                    <div class="col-md-3 mb-3 profession-div" @if(isset($bookingFilter) && $bookingFilter->profession_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-search-profession" class="form-select search-filter select22">
                            <option value="">Select Profession</option>
                            @foreach ($professionFilter as $professionFilter1)
                                <option value="{{ $professionFilter1->id }}">{{ $professionFilter1->eng_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 order-status-div" @if(isset($bookingFilter) && $bookingFilter->orderstatus_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-search-orderStatus" class="form-select search-filter select22">
                            <option value="">Select Order Status</option>
                            @foreach ($bookingOrderStatus as $bookingOrderSt)
                                <option value="{{ $bookingOrderSt->id }}">{{ $bookingOrderSt->ord_status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 payment-status-div" @if(isset($bookingFilter) && $bookingFilter->paymentstatus_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-search-paymentStatus" class="form-select search-filter select22">
                            <option value="">Select Payment Status</option>
                            @foreach ($bookingpaystatus as $bookingpayst)
                            <option value="{{ $bookingpayst->payment_status }}">@if($bookingpayst->payment_status == 1) Paid @else Unpaid @endif</option>

                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 city-div" @if(isset($bookingFilter) && $bookingFilter->city_filter == 1) @else style="display: none" @endif>
                        <select name="" id="by-search-city" class="form-select search-filter select22">
                            <option value="">Select City</option>
                            @foreach ($CityFilter as $CityFilter1)
                                <option value="{{ $CityFilter1->id }}">{{ $CityFilter1->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-3 mb-3 mobile-no-div">
                        <select name="" id="by-search-mobile-no" class="form-select search-filter select22">
                            <option value="">Select Mobile No</option>
                            @foreach ($customerFilter as $customerFilter2)
                                @php
                                    $customerMobile = DB::table('users')->where('id','=',$customerFilter2->user_id)->first();
                                @endphp
                                @if ($customerMobile->mobile_no != '')
                                    <option value="{{ $customerMobile->id }}">{{ $customerMobile->mobile_no}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div> --}}
                    <div class="col-md-3 mb-3 booking-date-div" @if(isset($bookingFilter) && $bookingFilter->bookingdate_filter == 1) @else style="display: none" @endif>
                        <input type="text" name="" id="by-search-booking-date" class="form-control search-filter datepicker" placeholder="Select Booking Date...">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 offset-md-9 mb-3">
                <input type="text" name="search_booking" id="search_booking" class="form-control" placeholder="Search...">
            </div>
        </div>
        <div class="row bookingchange">
            @include('admin.booking.load')
        </div>
        <!-- Add Visa Details Start-->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="addvisadetail" aria-labelledby="addvisadetailLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="addvisadetailLabel" class="offcanvas-title">Add Visa Details</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="vaddvisadetail" action="{{ route('admin.visaStr') }}" method="post">
                    @csrf
                    <input type="hidden" name="booking_id" id="bookingID">
                    <input type="hidden" name="cand_id" id="cand_id">
                    <input type="hidden" name="user_id" id="user_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="employer-name">Employer Name <span class="text-danger">*</span></label>
                                <input type="text" name="employer_name" class="form-control" id="employer-name" placeholder="Enter Employer Name..."/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="employer-ar-name" class="form-label">Employer Arabic Name</label>
                                <input type="text" name="employer_ar_name" id="employer-ar-name" class="form-control" placeholder="Enter Employer Arabic Name...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="visa-number">Visa Number <span class="text-danger">*</span></label>
                                <input type="text" name="visa_no" class="form-control" id="visa-number" placeholder="Enter Visa No..."/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="id-number">ID Number</label>
                                <input type="text" name="id_no" class="form-control" id="id-number" placeholder="Enter ID No..."/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @php
                                $professions = \App\Models\Profession::orderBy('id','DESC')->get();
                                $wcities = DB::table('expecworkcities')->orderBy('name')->get();
                            @endphp
                            <div class="mb-3">
                                <label class="form-label" for="profession">Profession <span class="text-danger">*</span></label>
                                <select name="proff_id" id="profession" class="select2 form-select" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($professions as $profession)
                                        <option value="{{ $profession->id }}">{{ $profession->eng_name.' ('.$profession->ar_name.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="issueing-authority">Issuing Authority <span class="text-danger">*</span></label>
                                <select name="issuing_authority" id="issueing-authority" class="selec2 form-select" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="Mumbai">Mumbai</option>
                                    <option value="New Delhi">New Delhi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="city-work">City of Work <span class="text-danger">*</span></label>
                                <select name="wpcity_id" id="city-work" class="form-control select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($wcities as $wcity)
                                        <option value="{{ $wcity->id }}">{{ $wcity->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="salary">Salary <span class="text-danger">*</span></label>
                                <input type="text" name="salary" class="form-control" id="salary" disabled placeholder="Enter salary..."/>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                        </div>

                    </div>


                </form>
            </div>
        </div>
        <!-- Add Visa Details End -->
        <!-- Add Payment Details Start -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="addpayment" aria-labelledby="addpaymentLabel">
            <div class="offcanvas-header">
                <h5 id="addpaymentLabel" class="offcanvas-title">Add Payment</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addpaymentDetail" action="{{ route('admin.paymentStr') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <input type="hidden" name="booking_id" id="pbookingID">
                        <input type="hidden" name="cand_id" id="pcandID">
                        <input type="hidden" name="user_id" id="puserID">

                        <label class="form-label" for="amount">Amount <span class="text-danger">*</span></label>
                        <input type="text" name="amount" class="form-control" id="amount" placeholder="Enter Amount..."/>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm me-sm-3 me-1 data-submit">Submit</button>
                </form>
            </div>
        </div>
        <!-- Add Payment Details End -->
         <!-- Add order Status Start -->
         <div class="offcanvas offcanvas-end" tabindex="-1" id="addorderstatus" aria-labelledby="addorderstatusLabel">
            <div class="offcanvas-header">
                <h5 id="addorderstatusLabel" class="offcanvas-title">Add Order Status</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addorderstatusDetail" action="{{ route('admin.orderstatusStr') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <input type="hidden" name="booking_id" id="sbookingID">
                        <input type="hidden" name="cand_id" id="scandID">
                        <input type="hidden" name="user_id" id="suserID">
                        <label for="partner_id" class="form-label">Order Status <span class="text-danger">*</span></label>
                        <select name="ord_id" id="ord_id" class="form-select select2" data-allow-clear="true">
                            <option value="">Select</option>
                            @foreach ($ordstatuses as $ordstatus)
                                <option value="{{ $ordstatus->id }}">{{ $ordstatus->ord_status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm me-sm-3 me-1 data-submit">Submit</button>
                </form>
            </div>
        </div>
        <!-- Add Payment Details End -->
        <!-- Confirm Modal Start -->
        <div class="modal fade" id="confirmBooking" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Booking Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.otpconfirm') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <input type="hidden" name="booking_id" id="otpBkID">
                                    <div id="otpEntryBlock">
                                        <p>We sent you a confirmation OTP. Please check your Email.</p>
                                        <label for="bookingOTP" class="form-label">Enter OTP</label>
                                        <input type="text" id="bookingOTP" name="email_code" class="form-control" oninput="checkOTP(this)" placeholder="Please enter OTP..." />
                                    </div>
                                    <p id="noCustomerNotice" style="display:none;">This order was placed directly by the partner and has no customer to verify by OTP - click Confirm below to proceed.</p>
                                    <span class="text-danger" id="otpError"></span>
                                    <span class="text-success" id="otpSuccess"></span>
                                    <span class="text-danger" id="otpRError"></span>
                                    <span class="text-success" id="otpRSuccess"></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btndisabled">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Confirm Modal End -->

        <!-- Cancel Modal Start -->
        <div class="modal fade" id="cancelBooking" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Booking Cancel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.booking.cancel') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <input type="hidden" name="booking_id" id="cCandID">
                                    <p>Are you sure to cancel booking?</p>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="checkbox" name="yes_send_notification" id="yes" value="1"/>
                                        <label class="form-check-label" for="yes">Yes, Send Notification</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Cancel Modal End -->
        <!-- Replace candidate Start -->
        <div class="modal fade" id="replaceBooking" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Replace Candidate</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.booking.repcand') }}" id="repcandval" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" name="rep_booking_id" id="repbk_id">
                                <input type="hidden" name="bkcand_id" id="bkcand_id">
                                <div class="col-md-6 mb-3">
                                    <label for="bk_cand_name" class="form-label">Candidate</label>
                                    <input type="text" name="bk_cand_name" id="bk_cand_name" class="form-control" disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="repcand_id" class="form-label">Replace to <span class="text-danger">*</span></label>
                                    <select name="repcand_id" id="repcand_id" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select Candidate</option>
                                        @foreach ($candidates as $candidate)
                                            <option value="{{ $candidate->id }}">{{ $candidate->cand_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm">Replace</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Replace candidate End -->
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
                            <div class="form-check mt-2" id="partner-f">
                                <input class="form-check-input" type="checkbox" name="partnerf" value="1" id="partnerf" @if(isset($bookingFilter) && $bookingFilter->partner_filter == 1) checked @endif />
                                <label class="form-check-label" for="partnerf"> Partner</label>
                            </div>
                            <div class="form-check mt-2" id="customer-f">
                                <input class="form-check-input" type="checkbox" name="customerf" value="1" id="customerf" @if(isset($bookingFilter) && $bookingFilter->customer_filter == 1) checked @endif />
                                <label class="form-check-label" for="customerf"> Customer</label>
                            </div>

                            {{-- <div class="form-check mt-2" id="candidate-f">
                                <input class="form-check-input" type="checkbox" name="candidatef" value="1" id="candidatef" />
                                <label class="form-check-label" for="candidatef"> Candidate</label>
                            </div> --}}
                            {{-- <div class="form-check mt-2" id="passport-no-f">
                                <input class="form-check-input" type="checkbox" name="passport-nof" value="1" id="passport-nof" />
                                <label class="form-check-label" for="passport-nof"> Passport No</label>
                            </div> --}}
                            <div class="form-check mt-2" id="order-no-f">
                                <input class="form-check-input" type="checkbox" name="order-nof" value="1" id="order-nof" @if(isset($bookingFilter) && $bookingFilter->orderno_filter == 1) checked @endif />
                                <label class="form-check-label" for="order-nof"> Order No</label>
                            </div>
                            {{-- <div class="form-check mt-2" id="reference-no-f">
                                <input class="form-check-input" type="checkbox" name="reference-nof" value="1" id="reference-nof"  />
                                <label class="form-check-label" for="reference-nof"> Reference No</label>
                            </div> --}}
                            <div class="form-check mt-2" id="profession-f">
                                <input class="form-check-input" type="checkbox" name="professionf" value="1" id="professionf" @if(isset($bookingFilter) && $bookingFilter->profession_filter == 1) checked @endif />
                                <label class="form-check-label" for="professionf"> Profession</label>
                            </div>
                            <div class="form-check mt-2" id="order-status-f">
                                <input class="form-check-input" type="checkbox" name="order-statusf" value="1" id="order-statusf" @if(isset($bookingFilter) && $bookingFilter->orderstatus_filter == 1) checked @endif />
                                <label class="form-check-label" for="order-statusf"> Order Status</label>
                            </div>
                            <div class="form-check mt-2" id="payment-status-f">
                                <input class="form-check-input" type="checkbox" name="payment-statusf" value="1" id="payment-statusf" @if(isset($bookingFilter) && $bookingFilter->paymentstatus_filter == 1) checked @endif />
                                <label class="form-check-label" for="payment-statusf"> Payment Status</label>
                            </div>
                            <div class="form-check mt-2" id="city-f">
                                <input class="form-check-input" type="checkbox" name="cityf" value="1" id="cityf" @if(isset($bookingFilter) && $bookingFilter->city_filter == 1) checked @endif />
                                <label class="form-check-label" for="cityf"> City</label>
                            </div>
                            {{-- <div class="form-check mt-2" id="mobile-no-f">
                                <input class="form-check-input" type="checkbox" name="mobile-nof" value="1" id="mobile-nof"  />
                                <label class="form-check-label" for="mobile-nof"> Mobile No</label>
                            </div> --}}
                            <div class="form-check mt-2" id="booking-date-f">
                                <input class="form-check-input" type="checkbox" name="booking-datef" value="1" id="booking-datef" @if(isset($bookingFilter) && $bookingFilter->bookingdate_filter == 1) checked @endif />
                                <label class="form-check-label" for="booking-datef"> Booking Date</label>
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
    </div>
@endsection

@section('page-script')
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

    <script src="{{ asset('admin/assets/js/cards-advance.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/booking-validation.js') }}"></script>

    <script src="{{ asset('admin/assets/custom/main.js') }}"></script>

    <script>
        function checkOTP(OTP){
            var Otp = OTP.value;
            var bookID = $('#otpBkID').val();
            // console.log(bookingID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            jQuery.ajax({
                url: "{{ url('admin/booking/check-otp') }}",
                method: "POST",
                type: "html",
                data: {
                    "_token": "{{ csrf_token() }}",
                    otp: Otp,
                    booking_id:bookID
                },
                success: function(data){
                    console.log(data);
                    if (data.type == 1) {
                        $('#otpRSuccess').text(data.message);
                        $('#otpSuccess').text('');
                        $('#otpError').text('');
                        $('#otpRError').text('');
                        $('.btndisabled').attr('disabled',false);
                    }

                    if (data.type == 2) {

                        $('#otpRError').text(data.error);
                        $('#otpError').text('');
                        $('#otpRSuccess').text('');
                        $('#otpSuccess').text('');
                        $('.btndisabled').attr('disabled',true);

                    }
                }
            });
        }
    </script>

    <script>
        $(document).ready(function(){
            $('#replaceBooking').on('show.bs.modal',function(e){
                var booking_id = $(e.relatedTarget).data('id');

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });



                jQuery.ajax({
                    url : '{{ url("admin/booking/getCandidate") }}',
                    method: "POST",
                    type: "HTML",
                    data:{
                        id: booking_id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data){
                        $('#repbk_id').val(data.bkID);
                        $('#bkcand_id').val(data.candID);
                        $('#bk_cand_name').val(data.cand_name);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#confirmBooking').on('show.bs.modal',function(e){
                var otpBk_id = $(e.relatedTarget).data('id');

                $('#otpBkID').val(otpBk_id);

                // Reset modal state from any previous booking it was opened for.
                $('#bookingOTP').val('');
                $('#otpEntryBlock').show();
                $('#noCustomerNotice').hide();
                $('#otpSuccess, #otpError, #otpRSuccess, #otpRError').text('');
                $('.btndisabled').attr('disabled', true);

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url('admin/booking/sendOTP') }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": otpBk_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if ($.trim(data) === 'no_customer') {
                            // Placed directly by a partner (Worker Portal
                            // "Hire Now") - no customer to verify by OTP,
                            // so skip straight to letting admin confirm.
                            $('#otpEntryBlock').hide();
                            $('#noCustomerNotice').show();
                            $('.btndisabled').attr('disabled', false);
                        } else {
                            $('#otpSuccess').text("OTP Send your mail please check and enter");
                        }
                    },
                    error: function(jqXHR,exception){
                        var msg = '';
                        if (jqXHR.status === 0) {
                            msg = 'Not connect.\n Verify Network.';
                        }else if (jqXHR.status == 404) {
                            msg = 'Requested page not found. [404]';
                        } else if (jqXHR.status == 500) {
                            msg = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                            msg = 'Requested JSON parse failed.';
                        } else if (exception === 'timeout') {
                            msg = 'Time out error.';
                        } else if (exception === 'abort') {
                            msg = 'Ajax request aborted.';
                        } else {
                            msg = 'Uncaught Error.\n' + jqXHR.responseText;
                        }

                        $('#otpError').html(msg);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#addvisadetail').on('show.bs.offcanvas',function(e){
                var booking_id = $(e.relatedTarget).data('id');
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url('admin/booking/getVisa') }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": booking_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#bookingID').val(data.bookingID);
                        $('#cand_id').val(data.CandID);
                        $('#user_id').val(data.userID);
                        $('#visa-number').val(data.visa_no);
                        $('#id-number').val(data.id_no);
                        $('#profession').val(data.proff_id).trigger('change');
                        if (data.employer_name != null) {
                            $('#employer-name').val(data.employer_name);
                        } else {
                            $('#employer-name').val(data.uname);
                        }
                        $('#employer-ar-name').val(data.employer_ar_name);
                        $('#issueing-authority').val(data.issuing_authority).trigger('change');
                        $('#city-work').val(data.wpcity_id).trigger('change');
                        $('#salary').val(data.exp_sal);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#addpayment').on('show.bs.offcanvas',function(e){
                var pbooking_id = $(e.relatedTarget).data('id');
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url('admin/booking/getPayment') }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": pbooking_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#pbookingID').val(data.bookingID);
                        $('#pcandID').val(data.CandID);
                        $('#puserID').val(data.userID);
                        $('#amount').val(data.amount)
                    }
                });

            });
        });
    </script>
     <script>
        $(document).ready(function(){
            $('#addorderstatus').on('show.bs.offcanvas',function(e){
                var sbooking_id = $(e.relatedTarget).data('id');
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url('admin/booking/getorderstatus') }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": sbooking_id,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){

                    $('#sbookingID').val(data.bookingID); // Set the value of the hidden field
                    $('#scandID').val(data.CandID);
                    $('#suserID').val(data.userID);
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#cancelBooking').on('show.bs.modal',function(e){
                var delID = $(e.relatedTarget).data('id');
                $('#cCandID').val(delID);

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $(document).on('change','.search-filter',function(){
                var partner = $('#by-search-partner').val();
                var customer = $('#by-search-customer').val();
                // var candName = $('#by-search-candidate').val();
                var orderNo = $('#by-search-order-no').val();
                var profession = $('#by-search-profession').val();
                var orderStatus = $('#by-search-orderStatus').val();
                var paymentStatus = $('#by-search-paymentStatus').val();
                var city = $('#by-search-city').val();
                var bookingDate = $('#by-search-booking-date').val();
                var search_booking =  $('#search_booking').val();
                var bookingStatus = $('#by-booking-status').val();


                jQuery.ajax({
                    url: "{{ url('admin/booking') }}",
                    method: "get",
                    type: "html",
                    data:{
                        partner: partner,
                        customer: customer,
                        // candName: candName,
                        orderNo: orderNo,
                        profession: profession,
                        orderStatus: orderStatus,
                        paymentStatus: paymentStatus,
                        city: city,
                        bookingDate: bookingDate,
                        search_booking: search_booking,
                        bookingStatus: bookingStatus
                    },
                    success: function(data){
                        console.log(data);
                        $('.bookingchange').html(data);
                    },
                    error: function(xhr){
                        console.log(xhr.responseJSON);
                    }
                });
            });

            // ===============================
            // Booking status bar -> apply matching filter
            // ===============================
            $(document).on('click', '.bookingStatusCard', function (e) {
                e.preventDefault();

                var $this = $(this);
                var value = $this.data('value');
                value = (value === undefined || value === null) ? '' : String(value);
                var wasActive = $this.hasClass('active');

                if (wasActive && !value) {
                    return; // "All" already active — avoid a duplicate request
                }

                $('.bookingStatusCard').removeClass('active');

                var effectiveValue = wasActive ? '' : value;
                if (!wasActive) {
                    $this.addClass('active');
                }

                // Triggering 'change' on this field (it carries the same
                // .search-filter class as every other filter control) reuses
                // the exact single delegated reload handler above — one
                // request, no separate AJAX path to keep in sync.
                $('#by-booking-status').val(effectiveValue).trigger('change');
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#search_booking').on('keyup', function(){
                var search_booking = $(this).val();
                var partner = $('#by-search-partner').val();
                var customer = $('#by-search-customer').val();
                // var candName = $('#by-search-candidate').val();
                var orderNo = $('#by-search-order-no').val();
                var profession = $('#by-search-profession').val();
                var orderStatus = $('#by-search-orderStatus').val();
                var paymentStatus = $('#by-search-paymentStatus').val();
                var city = $('#by-search-city').val();
                var bookingDate = $('#by-search-booking-date').val();
                var bookingStatus = $('#by-booking-status').val();

                jQuery.ajax({
                    url: "{{ url('admin/booking') }}",
                    method: "get",
                    type: "html",
                    data:{
                        customer: customer,
                        partner: partner,
                        // candName: candName,
                        orderNo: orderNo,
                        profession: profession,
                        orderStatus: orderStatus,
                        paymentStatus: paymentStatus,
                        city: city,
                        bookingDate: bookingDate,
                        search_booking: search_booking,
                        bookingStatus: bookingStatus
                    },
                    success: function(data){
                        console.log(data);
                        $('.bookingchange').html(data);
                    },
                    error: function(xhr){
                        console.log(xhr.responseJSON);
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function(){
            $('.datepicker').flatpickr();
        });
    </script>

    <!-- Filter Styling Start -->
    <script>
        $(document).ready(function(){
            $('#partnerf').click(function(){
                $('.partner-div').toggle();
            });

            $('#customerf').click(function(){
                $('.customer-div').toggle();
            });

            $('#order-nof').click(function(){
                $('.order-no-div').toggle();
            });

            $('#professionf').click(function(){
                $('.profession-div').toggle();
            });

            $('#order-statusf').click(function(){
                $('.order-status-div').toggle();
            });

            $('#payment-statusf').click(function(){
                $('.payment-status-div').toggle();
            });

            $('#cityf').click(function(){
                $('.city-div').toggle();
            });

            $('#booking-datef').click(function(){
                $('.booking-date-div').toggle();
            });

            // Basic Select
            $('#default-chk').click(function(){
                if ($('#partnerf:checkbox:checked').length > 0) {
                }else{
                    $('#partnerf').trigger('click');

                }

                if ($('#customerf:checkbox:checked').length > 0) {
                }else{
                    $('#customerf').trigger('click');

                }

                if ($('#professionf:checkbox:checked').length > 0) {
                }else{
                    $('#professionf').trigger('click');

                }

                if ($('#order-nof:checkbox:checked').length > 0) {
                    $('#order-nof').trigger('click');
                }

                if ($('#order-statusf:checkbox:checked').length > 0) {
                    $('#order-statusf').trigger('click');
                }

                if ($('#payment-statusf:checkbox:checked').length > 0) {
                    $('#payment-statusf').trigger('click');
                }

                if ($('#cityf:checkbox:checked').length > 0) {
                    $('#cityf').trigger('click');
                }

                if ($('#booking-datef:checkbox:checked').length > 0) {
                    $('#booking-datef').trigger('click');
                }


            });

            // All Select
            $('#all-chk').click(function(){
                $('input[name="partnerf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="customerf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="professionf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="order-nof"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="order-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="payment-statusf"]').each(function () {
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

                $('input[name="booking-datef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });
            });

            // Uncheck All

            $('#all-unchk').click(function(){
                if($('#partnerf:checkbox:checked').length > 0){
                    $('#partnerf').trigger('click');
                }
                if($('#customerf:checkbox:checked').length > 0){
                    $('#customerf').trigger('click');
                }
                if($('#professionf:checkbox:checked').length > 0){
                    $('#professionf').trigger('click');
                }
                if($('#order-nof:checkbox:checked').length > 0){
                    $('#order-nof').trigger('click');
                }
                if($('#order-statusf:checkbox:checked').length > 0){
                    $('#order-statusf').trigger('click');
                }
                if($('#payment-statusf:checkbox:checked').length > 0){
                    $('#payment-statusf').trigger('click');
                }
                if($('#cityf:checkbox:checked').length > 0){
                    $('#cityf').trigger('click');
                }
                if($('#booking-datef:checkbox:checked').length > 0){
                    $('#booking-datef').trigger('click');
                }
            });

            // Update Filter
            $('#update-chk').click(function(){
                var partnerf = $('#partnerf:checked').val();
                var customerf = $('#customerf:checked').val();
                var professionf = $('#professionf:checked').val();
                var ordernof = $('#order-nof:checked').val();
                var orderstatusf = $('#order-statusf:checked').val();
                var paymentstatusf = $('#payment-statusf:checked').val();
                var cityf = $('#cityf:checked').val();
                var bookingdatef = $('#booking-datef:checked').val();

                jQuery.ajax({
                    url:"{{ url('admin/booking/filterList/update') }}",
                    method: 'post',
                    type: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        partnerf: partnerf,
                        customerf: customerf,
                        professionf: professionf,
                        ordernof: ordernof,
                        orderstatusf: orderstatusf,
                        paymentstatusf: paymentstatusf,
                        cityf: cityf,
                        bookingdatef: bookingdatef,
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
    <!-- Filter Styling End -->

@endsection
