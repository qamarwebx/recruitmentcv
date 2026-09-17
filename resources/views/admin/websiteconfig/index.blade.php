@extends('layout.admin.admin_layout')

@section('title','Website Configuration')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <h6 class="text-muted">Website Configuration</h6>
                  <div class="nav-align-left nav-tabs-shadow">
                    <ul class="nav nav-tabs" role="tablist">
                      <li class="nav-item">
                        {{-- <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-home" aria-controls="navs-left-home" aria-selected="false">
                          Booking Panel
                        </button> --}}
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-limit" aria-controls="navs-left-limit" aria-selected="false">
                            Customer Booking Limit
                        </button>
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-left-candlimit" aria-controls="nav-left-candlimit" aria-selected="false">
                            Candidate Booking Limit
                        </button>
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-google-auth" aria-controls="nav-google-auth" aria-selected="false">
                            Google Auth Detail
                        </button>
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-facebook-auth" aria-controls="nav-facebook-auth" aria-selected="false">
                            Facebook Auth Detail
                        </button>
                        {{-- <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-otp-verification" aria-controls="nav-otp-verification" aria-selected="false">
                            OTP Verification
                        </button> --}}
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-whatsapp-chat-num" aria-controls="nav-whatsapp-chat-num" aria-selected="false">
                            Whatsapp Number
                        </button>
                      </li>
                    </ul>
                    <div class="tab-content">
                        {{-- <div class="tab-pane fade show active" id="navs-left-home">
                            <form action="{{ route('admin.webconfigStr') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="">Order Received</label>
                                        <div class="mb-3">
                                            <div class="form-check form-check-inline mt-2">
                                                <input type="radio" class="form-check-input" name="order_receieved" id="order_receieved1" @if(isset($post) && $post->order_receieved == 1) checked @endif value="1">
                                                <label for="order_receieved1" class="form-check-label">Enabled</label>
                                            </div>
                                            <div class="form-check form-check-inline mt-2">
                                                <input type="radio" class="form-check-input" name="order_receieved" id="order_receieved2" @if(isset($post) && $post->order_receieved == 0) checked @endif value="0">
                                                <label for="order_receieved2" class="form-check-label">Disabled</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="">Partner</label>
                                        <div class="mb-3">
                                            <div class="form-check form-check-inline mt-2">
                                                <input class="form-check-input" type="radio" name="booking_panel" id="inlineRadio1" @if(isset($post) && $post->booking_panel == 1) checked @endif value="1"/>
                                                <label class="form-check-label" for="inlineRadio1">Enabled</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="booking_panel" id="inlineRadio2" @if(isset($post) && $post->booking_panel == 0) checked @endif value="0"/>
                                                <label class="form-check-label" for="inlineRadio2">Disabled</label>
                                            </div>
                                        </div>
                                        <span class="text-danger mt-2" id="error-booking-panel"></span>
                                    </div>
                                    <div class="col-md-12">

                                        <button type="submit" class="btn btn-sm btn-primary" id="enabledBooking">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div> --}}
                        <div class="tab-pane fade show active" id="navs-left-limit">
                            <form action="{{ route('admin.webconfigStr') }}" id="maximumBL" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="max_booking_limit" class="form-label">Customer Booking Limit <span>*</span></label>
                                            <input type="text" name="max_booking_limit" id="max_booking_limit" value="{{ $post->max_booking_limit }}" class="form-control" placeholder="Enter number...">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="nav-left-candlimit">
                            <form action="{{ route('admin.webconfigStr') }}" id="maximumCBL" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="max_cand_booking_limit" class="form-label">Candidate Booking Limit <span>*</span></label>
                                            <input type="text" name="max_cand_booking_limit" class="form-control" id="max_cand_booking_limit" value="{{ $post->cand_booking_limit }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="nav-google-auth">
                            <form action="{{ route('admin.googleauthupdated') }}" method="POST" id="googleauthvalidation">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="google_auth_id" id="google_auth_id" value="@if(isset($googleauth)) {{ $googleauth->id }} @endif">
                                    <input type="hidden" name="type" value="google">
                                    <div class="col-md-6 mb-3">
                                        <label for="google_client_id" class="form-label">Client ID <span class="text-danger">*</span></label>
                                        <input type="text" name="google_client_id" class="form-control" value="@if(isset($googleauth)) {{ $googleauth->client_id }} @endif" id="google_client_id" placeholder="Enter Google Client ID ...">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="google_client_secret" class="form-label">Client Secret <span class="text-danger">*</span></label>
                                        <input type="text" name="google_client_secret" id="google_client_secret" value="@if(isset($googleauth)) {{ $googleauth->client_secret }} @endif" class="form-control" placeholder="Enter Google Secret Key...">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="google_callback_url" class="form-label">Callback URL <span class="text-danger">*</span></label>
                                        {{-- <input type="text" name="callback_url" id="google_callback_url" @if(isset($googleauth)) value="{{ $googleauth->callback_url }}" @endif class="form-control" placeholder="Enter Callback URL..."> --}}
                                        <p class="english-google-url-copy">{{ url('auth/google/callback') }} <i class="ti ti-copy"></i></p>
                                        <p class="arabic-google-url-copy">{{ url('ar/auth/google') }} <i class="ti ti-copy"></i></p>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="nav-facebook-auth">
                            <form action="{{ route('admin.facebookauthupdated') }}" method="POST" id="facebookauthvalidation">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="facebook_auth_id" id="facebook_auth_id" value="@if(isset($facebookauth)) {{ $facebookauth->id }} @endif">
                                    <input type="hidden" name="type" value="facebook">
                                    <div class="col-md-6 mb-3">
                                        <label for="facebook_client_id" class="form-label">Client ID <span class="text-danger">*</span></label>
                                        <input type="text" name="facebook_client_id" class="form-control" value="@if(isset($facebookauth)) {{ $facebookauth->client_id }} @endif" id="facebook_client_id" placeholder="Enter Facebook Client ID ...">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="facebook_client_secret" class="form-label">Client Secret <span class="text-danger">*</span></label>
                                        <input type="text" name="facebook_client_secret" id="facebook_client_secret" value="@if(isset($facebookauth)) {{ $facebookauth->client_secret }} @endif" class="form-control" placeholder="Enter Facebook Secret Key...">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="facebook_callback_url" class="form-label">Callback URL <span class="text-danger">*</span></label>
                                        {{-- <input type="text" name="callback_url" id="facebook_callback_url" @if(isset($facebookauth)) value="{{ $facebookauth->callback_url }}" @endif class="form-control" placeholder="Enter Callback URL..."> --}}
                                        <p class="english-facebook-url-copy">{{ url('auth/facebook/callback') }}<i class="ti ti-copy"></i></p>
                                        <p class="arabic-facebook-url-copy">{{ url('ar/auth/facebook') }}<i class="ti ti-copy"></i></p>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        {{-- <div class="tab-pane fade show" id="nav-otp-verification">
                            <div class="row">
                                <form action="{{ route('admin.webconfigStr') }}" method="POST">
                                    @csrf
                                    <div class="col-md-12">
                                        <label for="">OTP Verification</label>
                                        <div class="mb-3">
                                            <div class="form-check form-check-inline mt-2">
                                                <input type="radio" class="form-check-input" name="otp_verification" id="otp_verification1" @if(isset($post) && $post->otp_verification == 1) checked @endif value="1">
                                                <label for="otp_verification1" class="form-check-label">Enabled</label>
                                            </div>
                                            <div class="form-check form-check-inline mt-2">
                                                <input type="radio" class="form-check-input" name="otp_verification" id="otp_verification2" @if(isset($post) && $post->otp_verification == 0) checked @endif value="0">
                                                <label for="otp_verification2" class="form-check-label">Disabled</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="">Meta Response</label>
                                        <div class="mb-3">
                                            <div class="form-check form-check-inline mt-2">
                                                <input type="radio" class="form-check-input" name="meta_response" id="meta_response1" @if(isset($post) && $post->meta_response == 1) checked @endif value="1">
                                                <label for="meta_response1" class="form-check-label">Enabled</label>
                                            </div>
                                            <div class="form-check form-check-inline mt-2">
                                                <input type="radio" class="form-check-input" name="meta_response" id="meta_response2" @if(isset($post) && $post->meta_response == 0) checked @endif value="0">
                                                <label for="meta_response2" class="form-check-label">Disabled</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">

                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div> --}}
                        <div class="tab-pane fade show" id="nav-whatsapp-chat-num">
                            <form action="{{ route('admin.webconfigStr') }}" method="POST" id="whatsappnumberval">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="add-whatsapp-number" class="form-label"> Whatsapp Number <span class="text-danger">*</span></label>
                                            <input type="text" name="whatsapp_number" class="form-control" id="add-whatsapp-number" @if($post->whatsapp_number != '') value="{{ $post->whatsapp_number }}" @endif  placeholder="Please enter whatsapp number">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </div>
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
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/validation/websiteconf.js') }}"></script>


    <script>
        $(document).ready(function(){
            $('.english-google-url-copy').on('click',function(){
                var value = $(this).text();
                navigator.clipboard.writeText(value);
                toastr['success']('Text copied - '+value+'', 'Success', { hideDuration: 3000 });
            });

            $('.arabic-google-url-copy').on('click',function(){
                var value = $(this).text();
                navigator.clipboard.writeText(value);
                toastr['success']('Text copied - '+value+'', 'Success', { hideDuration: 3000 });
            });

            $('.english-facebook-url-copy').on('click',function(){
                var value = $(this).text();
                navigator.clipboard.writeText(value);
                toastr['success']('Text copied - '+value+'', 'Success', { hideDuration: 3000 });
            });

            $('.arabic-facebook-url-copy').on('click',function(){
                var value = $(this).text();
                navigator.clipboard.writeText(value);
                toastr['success']('Text copied - '+value+'', 'Success', { hideDuration: 3000 });
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('input[type=radio][name="order_receieved"]').on('click',function(){
                var orderRec = $(this).val();
                var bkpanel = $('#inlineRadio2').is(":checked");

                // alert(bkpanel);

                if (orderRec == 0 && bkpanel == true) {
                    $('#error-booking-panel').text('Please enabled atleast on panel!');
                    $('#enabledBooking').attr('disabled',true);
                } else {
                    $('#error-booking-panel').text('');
                    $('#enabledBooking').attr('disabled',false);
                }

            });
        });

        $(document).ready(function(){
            $('input[type=radio][name="booking_panel"]').on('click',function(){
                var bookingPanel = $(this).val();
                var orderPanel = $('#order_receieved2').is(":checked");

                // alert(bkpanel);

                if (bookingPanel == 0 && orderPanel == true) {
                    $('#error-booking-panel').text('Please enabled atleast on panel!');
                    $('#enabledBooking').attr('disabled',true);
                } else {
                    $('#error-booking-panel').text('');
                    $('#enabledBooking').attr('disabled',false);
                }

            });
        });
    </script>
@endsection
