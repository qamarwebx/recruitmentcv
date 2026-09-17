@extends('layout.admin.admin_layout')

@section('title','Associate')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        @php
            if ($post->city_id != '') {
                $city_name = $post->city->name;
            } else {
                $city_name = "---";
            }

            if ($post->country_id != '') {
                $country_name = $post->country->name;
            } else {
                $country_name = "---";
            }


            $assoc_status = $post->status;
            $status_badge = ["danger","success"];
            $statue_badge_d = $status_badge[$assoc_status];
            $status_title = ["Inactive","Active"];
            $status_title_d = $status_title[$assoc_status];
            $status_data_target = ["#statusactive","#statusinactive"];
            $status_data_target_d = $status_data_target[$assoc_status];

            $assoc_contact = $post->contact_verified;
            $contact_title = ["Not Verified","Verified"];
            $contact_data_target = ["#contactverified","#contactnotverified"];
            $contact_badge_d = $status_badge[$assoc_contact];
            $contact_title_d = $contact_title[$assoc_contact];
            $contact_data_target_d = $contact_data_target[$assoc_contact];


        @endphp
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row top-content-div">
                            <div class="col-md-4">
                                <div class="d-flex align-items-start gap-4">
                                    <div>
                                        @if ($post->office_logo != '')
                                            <img src="" alt="">
                                        @else
                                            <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar"/>
                                        @endif
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $post->pty_full_name }}</h5>
                                        <span>{{ $city_name.', '.$country_name }}</span>
                                    </div>

                                </div>
                                <div class="row mt-5">
                                    <div class="col-md-12">
                                        <button class="btn btn-sm btn-success mb-1" data-bs-toggle="offcanvas" data-bs-target="#campaignMessage"><i class="ti ti-brand-whatsapp ti-xs"></i> Send Message</button>
                                        {{-- <button class="btn btn-sm btn-primary mb-1"><i class="ti ti-refresh ti-xs"></i> Generate Member ID</button> --}}
                                        @if ($post->membership != '')
                                            <a href="javascript::void(0);" class="btn btn-sm btn-secondary mb-1"><i class="ti ti-refresh ti-xs"></i> Generate Member ID</a>
                                        @else
                                            <a href="{{ route('admin.associate.generate_memberid',$post->id) }}" class="btn btn-sm btn-primary mb-1"><i class="ti ti-refresh ti-xs"></i> Generate Member ID</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Contact Status:</span>
                                    @if ($post->contstatus !='')
                                        <span class="badge bg-label-success">{{ $post->contstatus }}</span>
                                    @else
                                        <span class="badge bg-label-danger">None</span>
                                    @endif
                                </div> --}}

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Associate Name:</span>
                                    <span>@if($post->pty_full_name != '') {{ $post->pty_full_name }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Agency Name:</span>
                                    <span class="copied_text">@if($post->pty_ag_name != '') {{ $post->pty_ag_name }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Primary Contact No:</span>
                                    <span>@if($post->pty_mobile != '') {{ $post->pty_mobile }} <a href="https://wa.me/{{ $post->pty_mobile }}" target="_blank"><i class="ti ti-brand-whatsapp ti-xs"></i></a> @else {{ '---' }} @endif</span>

                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Secondary Contact:</span>
                                    <span>@if($post->sec_mob_no != '') {{ $post->sec_mob_no }} <a href="https://wa.me/{{ $post->sec_mob_no }}" target="_blank"><i class="ti ti-brand-whatsapp ti-xs"></i></a> @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Primary Email:</span>
                                    <span>@if($post->pty_email != '') {{ $post->pty_email }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Membership:</span>
                                    <span>@if($post->membership != '') {{ $post->membership }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Careoff:</span>
                                    <span>@if($post->careoff_id != '') {{ $post->careoff->name }} @else {{ '---' }} @endif</span>
                                </div>
                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Created By:</span>
                                    <span>{{ $post->admin->name }}</span>
                                </div>

                            </div>
                            <div class="col-md-4">

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Primary Mobile No:</span>
                                    @if ($post->pty_mobile_verifed == '1')
                                        <span class="badge bg-label-success">Verified</span>
                                    @else
                                        <span class="badge bg-label-danger" data-bs-toggle="modal" data-bs-target="#verified-prim-mobile">Not Verified</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-start gap-4 mt-2">
                                    <span class="fw-semibold me-25">Secondary Mobile No:</span>
                                    @if ($post->sec_mob_no_verified == '1')
                                        <span class="badge bg-label-success">Verified</span>
                                    @else
                                        <span class="badge bg-label-danger" data-bs-toggle="modal" data-bs-target="#verified-sec-mobile">Not Verified</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-start gap-4 mt-2">
                                    <span class="fw-semibold me-25">Email Verified:</span>
                                    @if ($post->pty_email_verified == '1')
                                        <span class="badge bg-label-success">Verified</span>
                                    @else
                                        <span class="badge bg-label-danger" data-bs-toggle="modal" data-bs-target="#verified-email">Not Verified</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-start gap-4 mt-2">
                                    <span class="fw-semibold me-25">Status:</span>

                                    <a href="javascript:void(0);" @if(Auth::guard('admin')->user()->user_type == 1) data-bs-toggle="modal" data-bs-target="{{ $status_data_target_d }}" @endif  class="badge bg-label-{{ $statue_badge_d }}">{{ $status_title_d }}</a>

                                </div>
                                <div class="d-flex align-items-start gap-4 mt-2">
                                    <span class="fw-semibold me-25">Contact:</span>
                                    <a href="javascript:void(0);" @if(Auth::guard('admin')->user()->user_type == 1) data-bs-toggle="modal" data-bs-target="{{ $contact_data_target_d }}" @endif class="badge bg-label-{{ $contact_badge_d }}">{{ $contact_title_d }}</a>
                                </div>



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Verify Primary Mobile Number Start -->
        <div class="modal fade" id="verified-prim-mobile" aria-hidden="true" aria-labelledby="verified-prim-mobileLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="verified-prim-mobileLabel">Verify Primary Mobile</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.associate.primmobverif',$post->id) }}" method="POST" id="verified-prim-mobileValidation">
                    @csrf
                    <div class="card" id="send-primary-mobile-otp-form">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="primary-mobile-no-verification-text" class="form-label">Primary Mobile No. <span class="text-danger">*</span></label>
                                    <input type="text" name="pty_mobile" id="primary-mobile-no-verification-text" placeholder="Enter Mobile Number" class="form-control" value="{{ $post->pty_mobile }}">
                                    <span class="text-danger error-primary-mob-otp"></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" id="primary-send-otp-btn" type="button">Send OTP</button>
                        </div>
                    </div>

                    <div class="card" id="submit-primary-mobile-otp-form" style="display: none">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="hidden" name="primary_mobile_no" id="primary_mobile_no">
                                        <label for="primary-mobile-no-otp-text" class="form-label">OTP<span class="text-danger">*</span></label>
                                        <input type="text" name="pty_mobile_otp" id="primary-mobile-no-otp-text" placeholder="Enter OTP" class="form-control">
                                        <span class="text-danger error-primary-mob-otp"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-sm btn-success" id="primary-resend-otp-btn">Resend OTP</button>
                            <button class="btn btn-sm btn-primary" type="submit">Submit OTP</button>
                        </div>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <!-- Verify Primary Mobile Number End -->


        <!-- Verify Secondary Mobile Number Start -->
        <div class="modal fade" id="verified-sec-mobile" aria-hidden="true" aria-labelledby="verified-sec-mobileLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="verified-sec-mobileLabel">Verify Secondary Mobile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.associate.secmobverif',$post->id) }}" method="POST" id="verified-secon-mobileValidation">
                    @csrf
                    <div class="card" id="send-secondary-mobile-otp-form">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="secondary-mobile-no-verification-text" class="form-label">Secondary Mobile No. <span class="text-danger">*</span></label>
                                    <input type="text" name="sec_mob_no" id="secondary-mobile-no-verification-text" placeholder="Enter Mobile Number" class="form-control" value="{{ $post->sec_mob_no }}">
                                    <span class="text-danger error-secondary-mob-otp"></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" id="secondary-send-otp-btn" type="button">Send OTP</button>
                        </div>
                    </div>

                    <div class="card" id="submit-secondary-mobile-otp-form" style="display: none">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="hidden" name="secondary_mobile_no" id="secondary_mobile_no">
                                        <label for="secondary-mobile-no-otp-text" class="form-label">OTP<span class="text-danger">*</span></label>
                                        <input type="text" name="sec_mob_no_otp" id="secondary-mobile-no-otp-text" placeholder="Enter OTP" class="form-control">
                                        <span class="text-danger error-secondary-mob-otp"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-sm btn-success" id="secondary-resend-otp-btn">Resend OTP</button>
                            <button class="btn btn-sm btn-primary" type="submit">Submit OTP</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Verify Secondary Mobile Number End -->


        <!-- Verify Email Start -->
        <div class="modal fade" id="verified-email" aria-hidden="true" aria-labelledby="verified-emailLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="verified-emailLabel">Verify Email</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.associate.emailverif',$post->id) }}" method="POST" id="verified-emailValidation">
                    @csrf
                    <div class="card" id="send-email-otp-form">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="email-verification-text" class="form-label">Email. <span class="text-danger">*</span></label>
                                    <input type="text" name="pty_email" id="email-verification-text" placeholder="Enter Email" class="form-control" value="{{ $post->pty_email }}">
                                    <span class="text-danger error-email-otp"></span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" id="email-send-otp-btn" type="button">Send OTP</button>
                        </div>
                    </div>

                    <div class="card" id="submit-email-otp-form" style="display: none">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="hidden" name="pty_email" id="pty_email">
                                        <label for="pty_email-otp-text" class="form-label">OTP<span class="text-danger">*</span></label>
                                        <input type="text" name="pty_email_otp" id="pty_email-otp-text" placeholder="Enter OTP" class="form-control">
                                        <span class="text-danger error-email-otp"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-sm btn-success" id="email-resend-otp-btn">Resend OTP</button>
                            <button class="btn btn-sm btn-primary" type="submit">Submit OTP</button>
                        </div>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <!-- Verify Email End -->

        <!-- Status Active Start -->
        <div class="modal fade" id="statusactive" aria-hidden="true" aria-labelledby="statusactiveLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="statusactiveLabel">Status Active</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.associate.show.active',$post->id) }}" method="POST" id="statusactiveValidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>Are you sure to active the status?</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-danger">No</button>
                            <button class="btn btn-sm btn-primary" type="submit">Yes</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Status Active End -->

        <!-- Status Inactive Start -->
        <div class="modal fade" id="statusinactive" aria-hidden="true" aria-labelledby="statusinactiveLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="statusinactiveLabel">Status Inactive</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.associate.show.inactive',$post->id) }}" method="POST" id="statusinactiveValidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>Are you sure to Inactive the status?</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-danger">No</button>
                            <button class="btn btn-sm btn-primary" type="submit">Yes</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Status Inactive End -->

        <!-- Contact Verified Start -->
        <div class="modal fade" id="contactverified" aria-hidden="true" aria-labelledby="contactverifiedLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="contactverifiedLabel">Contact Verified</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.associate.show.verified',$post->id) }}" method="POST" id="contactverifiedValidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>Are you sure to verify the contact?</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-danger">No</button>
                            <button class="btn btn-sm btn-primary" type="submit">Yes</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Contact Verified End -->

        <!-- Contact Not Verify Start -->
        <div class="modal fade" id="contactnotverified" aria-hidden="true" aria-labelledby="contactnotverifiedLabel" tabindex="-1">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="contactnotverifiedLabel">Contact Not Verified</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.associate.show.notverified',$post->id) }}" method="POST" id="contactnotverifiedValidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>Are you sure to Not Verify the contact?</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-danger">No</button>
                            <button class="btn btn-sm btn-primary" type="submit">Yes</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Contact Not Verify End -->


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
    <script src="{{ asset('admin/assets/plugins/additional-methods.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/associate-validation.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    <script src="{{ asset('admin/assets/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/jquery-form/form@4.3.0/dist/jquery.form.min.js"></script>

    <script>
        $(document).ready(function(){

            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            // Primary Mobile Validation

            $(document).on('click','#primary-send-otp-btn',function(e){
                var mobile_no = $('#primary-mobile-no-verification-text').val();
                var number_valid = new RegExp(/^\+?[0-9(),.-]+$/);
                let initial_no = "91";
                var isValid = true;
                var total_no = mobile_no.length;

                // Get starting 2 values
                var getTwoNo = mobile_no.substring(0,2);

                if (mobile_no != '') {
                    if (mobile_no.match(number_valid)) {
                        $('.error-primary-mob-otp').text('');

                        jQuery.ajax({
                            url: "{{ route('admin.associate.primary_getotp') }}",
                            method: "POST",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                mobile_no: mobile_no
                            },
                            success: function(response){
                                console.log(response);

                                if (response.status == 1) {
                                    $('.error-primary-mob-otp').text('');

                                    $('#send-primary-mobile-otp-form').hide();
                                    $('#submit-primary-mobile-otp-form').show();
                                    $('#primary_mobile_no').val(response.data.phone_number);

                                } else {
                                    $('.error-primary-mob-otp').text(response.response_msg);
                                    $('#send-primary-mobile-otp-form').show();
                                    $('#submit-primary-mobile-otp-form').hide();
                                }

                            }
                        });

                    }else{
                        $('.error-primary-mob-otp').text('Please enter only digits');
                    }
                } else {
                    $('.error-primary-mob-otp').text('Please enter mobile No..');
                }

            });

            $(document).on('click','#primary-resend-otp-btn',function(e){
                $('#send-primary-mobile-otp-form').show();
                $('#submit-primary-mobile-otp-form').hide();
            });


            // Secondary Mobile Validation
            $(document).on('click','#secondary-send-otp-btn',function(e){
                var mobile_no = $('#secondary-mobile-no-verification-text').val();
                var number_valid = new RegExp(/^\+?[0-9(),.-]+$/);
                // var total_no = mobile_no.length;

                if (mobile_no != '') {
                    if (mobile_no.match(number_valid)) {
                        $('.error-secondary-mob-otp').text('');

                        jQuery.ajax({
                            url: "{{ route('admin.associate.secondary_getotp') }}",
                            method: "POST",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                mobile_no: mobile_no
                            },
                            success: function(response){
                                console.log(response);

                                if (response.status == 1) {
                                    $('.error-secondary-mob-otp').text('');

                                    $('#send-secondary-mobile-otp-form').hide();
                                    $('#submit-secondary-mobile-otp-form').show();
                                    $('#secondary_mobile_no').val(response.data.phone_number);

                                } else {
                                    $('.error-secondary-mob-otp').text(response.response_msg);
                                    $('#send-secondary-mobile-otp-form').show();
                                    $('#submit-secondary-mobile-otp-form').hide();
                                }

                            }
                        });

                    }else{
                        $('.error-secondary-mob-otp').text('Please enter only digits');
                    }
                } else {
                    $('.error-secondary-mob-otp').text('Please enter mobile No..');
                }

            });

            $(document).on('click','#secondary-resend-otp-btn',function(e){
                $('#send-secondary-mobile-otp-form').show();
                $('#submit-secondary-mobile-otp-form').hide();
            });


            // Email OTP Validation
            $(document).on('click','#email-send-otp-btn',function(e){
                var email = $('#email-verification-text').val();
                var email_valid = new RegExp(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
                // var total_no = mobile_no.length;

                if (email != '') {
                    if (email.match(email_valid)) {
                        $('.error-email-otp').text('');

                        jQuery.ajax({
                            url: "{{ route('admin.associate.email_getotp') }}",
                            method: "POST",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                email: email
                            },
                            success: function(response){
                                console.log(response);

                                if (response.status == 1) {
                                    $('.error-email-otp').text('');

                                    $('#send-email-otp-form').hide();
                                    $('#submit-email-otp-form').show();
                                    $('#pty_email').val(response.data.name);

                                } else {
                                    $('.error-email-otp').text(response.response_msg);
                                    $('#send-email-otp-form').show();
                                    $('#submit-email-otp-form').hide();
                                }

                            }
                        });

                    }else{
                        $('.error-email-otp').text('Please enter valid email');
                    }
                } else {
                    $('.error-email-otp').text('Please enter Email..');
                }

            });

            $(document).on('click','#email-resend-otp-btn',function(e){
                $('#send-email-otp-form').show();
                $('#submit-email-otp-form').hide();
            });

        });
    </script>

@endsection
