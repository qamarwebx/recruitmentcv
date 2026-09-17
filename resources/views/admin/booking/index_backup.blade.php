@extends('layout.admin.admin_layout')

@section('title','Booking')

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
        <div class="row">
            <div class="col-md-3 offset-md-9 mb-3">
                <input type="text" name="search_booking" id="search_booking" class="form-control" placeholder="Search...">
            </div>
        </div>
        <div class="row bookingchange">
            @include('admin.booking.load')
        </div>
        <!-- Add Visa Details Start-->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="addvisadetail" aria-labelledby="addvisadetailLabel">
            <div class="offcanvas-header">
                <h5 id="addvisadetailLabel" class="offcanvas-title">Add Visa Details</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="vaddvisadetail" action="{{ route('admin.visaStr') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <input type="hidden" name="booking_id" id="bookingID">
                        <input type="hidden" name="cand_id" id="cand_id">
                        <input type="hidden" name="user_id" id="user_id">
                        <label class="form-label" for="visa-number">Visa Number <span class="text-danger">*</span></label>
                        <input type="text" name="visa_no" class="form-control" id="visa-number" placeholder="Enter Visa No..."/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="id-number">ID Number</label>
                        <input type="text" name="id_no" class="form-control" id="id-number" placeholder="Enter ID No..."/>
                    </div>
                    @php
                        $professions = \App\Models\Profession::orderBy('id','DESC')->get();
                        $wcities = \App\Models\City::orderBy('name')->get();
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
                    <div class="mb-3">
                        <label class="form-label" for="employer-name">Employer Name <span class="text-danger">*</span></label>
                        <input type="text" name="employer_name" class="form-control" id="employer-name" placeholder="Enter Employer Name..."/>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="issueing-authority">Issuing Authority <span class="text-danger">*</span></label>
                        <select name="issuing_authority" id="issueing-authority" class="selec2 form-select" data-allow-clear="true">
                            <option value=""></option>
                            <option value="Driver">Mumbai</option>
                            <option value="House Driver">Delhi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="city-work">City of Work <span class="text-danger">*</span></label>
                        <select name="wpcity_id" id="city-work" class="form-control select2" data-allow-clear="true">
                            <option value="">Select</option>
                            @foreach ($wcities as $wcity)
                                <option value="{{ $wcity->id }}">{{ $wcity->name }}</option>
                            @endforeach
                        </select>
                        
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="salary">Salary <span class="text-danger">*</span></label>
                        <input type="text" name="salary" class="form-control" id="salary" placeholder="Enter salary..."/>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
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

                        <label class="form-label" for="amount">Amount</label>
                        <input type="text" name="amount" class="form-control" id="amount" placeholder="Enter Amount..."/>
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
                                    <p>We sent you a confirmation OTP. Please check your Email.</p>
                                    <label for="bookingOTP" class="form-label">Enter OTP</label>
                                    <input type="text" id="bookingOTP" name="email_code" class="form-control" oninput="checkOTP(this)" placeholder="Please enter OTP..." />
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
                        // console.log(data);
                        $('#otpSuccess').text("OTP Send your mail please check and enter");
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
                        $('#issueing-authority').val(data.issuing_authority).trigger('change');
                        $('#city-work').val(data.wpcity_id).trigger('change');
                        $('#salary').val(data.salary);
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
            $('#cancelBooking').on('show.bs.modal',function(e){
                var delID = $(e.relatedTarget).data('id');
                $('#cCandID').val(delID);

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#search_booking').on('input',function(){
                var search_booking = $(this).val();
                
                jQuery.ajax({
                    url: "{{ url('admin/booking') }}",
                    method: "get",
                    type: "html",
                    data:{
                        search_booking: search_booking
                    },
                    success: function(data){
                        $('.bookingchange').html(data);
                    }
                });
            });
        });
    </script>

@endsection