@extends('layout.user.arabic.layout')

@section('title','ملفي')

@section('page-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.min.css">
@endsection

@section('content')
    <div class="container pt-md-5 pb-lg-4 mt-5 mb-sm-2">
        <div class="row">
            <div class="col-lg-8 col-md-7 mb-5 ardir">
                <form action="{{ route('myprofile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
                    <h1 class="h2 cv-head">معلومات شخصية</h1>
                    <div class="row pb-2">
                        {{-- <div class="col-lg-9 col-sm-8 mb-4">
                            <textarea class="form-control" id="account-bio" rows="6" placeholder="Write your bio here. It will be displayed on your public profile."></textarea>
                        </div> --}}
                        {{-- <div class="col-lg-3 col-sm-4 mb-4">
                            <div class="filepond--root file-uploader bg-secondary filepond--hopper"
                                data-style-panel-layout="compact" 
                                data-style-button-remove-item-position="left"
                                data-style-button-process-item-position="right"
                                data-style-load-indicator-position="right"
                                data-style-progress-indicator-position="right"
                                data-style-button-remove-item-align="false" 
                                style="height: 160px;">
                                <a class="filepond--credits" aria-hidden="true" type="file" target="_blank" rel="noopener noreferrer" style="transform: translateY(152px);"></a>
                                <div class="filepond--drop-label my-4 px-4" style="transform: translate3d(0px, 0px, 0px); opacity: 1;">
                                    <label for="filepond--browser-xxt4ofnsd" id="filepond--drop-label-xxt4ofnsd">
                                        <i class="d-inline-block fi-camera-plus fs-2 text-muted my-2 mx-5 text-center"></i><br>
                                        <span class="fw-bold">Change picture</span>
                                        <input type="file" name="" id="">
                                    </label>
                                    
                                </div>
                                <div class="filepond--list-scroller" style="transform: translate3d(0px, 0px, 0px);">
                                    <ul class="filepond--list" role="list"></ul>
                                </div>
                                <div class="filepond--panel filepond--panel-root" data-scalable="true">
                                    <div class="filepond--panel-top filepond--panel-root"></div>
                                    <div class="filepond--panel-center filepond--panel-root" style="transform: translate3d(0px, 8px, 0px) scale3d(1, 1.44, 1);"></div>
                                    <div class="filepond--panel-bottom filepond--panel-root" style="transform: translate3d(0px, 152px, 0px);"></div>
                                </div>
                                <span class="filepond--assistant" id="filepond--assistant-xxt4ofnsd" role="status" aria-live="polite" aria-relevant="additions"></span>
                                <div class="filepond--drip"></div>
                                <fieldset class="filepond--data"></fieldset>
                            </div>
                        </div> --}}
                        <div class="col-lg-3 mb-4">
                            <span id="blah">
                                @if ($post->photo != '')
                                    <img src="{{ asset('user/img/avatars/'.$post->photo) }}" alt="avatars" style="width:150px;height:150px;" class="img-thumbnail">
                                @elseif($post->avatar_url != '')
                                <img src="{{ $post->avatar_url }}" alt="avatars" style="width:150px;height:150px;" class="img-thumbnail">

                                @else
                                    <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="avatar" style="width:150px;height:150px;" class="img-thumbnail">
                                @endif
                            </span>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <input type="file" name="photo" class="form-control" id="photo" onchange="readImg(this)">
                        </div>
                    </div>
    
                    <div class="border rounded-3 p-3 mb-4" id="personal-info">
    
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">الاسم الكامل</label>
                                    <div id="name-value">{{ $post->name }}</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#name-collapse" data-bs-toggle="collapse">يحرر &nbsp<i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="name-collapse" data-bs-parent="#personal-info">
                                <input class="form-control mt-3" name="name" type="text" data-bs-binded-element="#name-value" data-bs-unset-value="Not specified" value="{{ $post->name }}">
                            </div>
                        </div>
    
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">بريد إلكتروني</label>
                                    <div id="email-value">{{ $post->email }}</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#email-collapse" data-bs-toggle="collapse">يحرر &nbsp<i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="email-collapse" data-bs-parent="#personal-info">
                                <input class="form-control mt-3" name="email" type="email" data-bs-binded-element="#email-value" data-bs-unset-value="Not specified" disabled value="{{ $post->email }}">
                            </div>
                        </div>

    
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">رقم التليفون</label>
                                    <input type="hidden" id="hiddenMobileNo" value="@if($post->mobile_no != '') {{ $post->contcode.''.$post->mobile_no }} @else {{ '---' }} @endif">
                                    <input type="hidden" id="updateMobileCountryCode" name="updateMobileCountryCode" @if($post->contcode != '') value="{{ $post->contcode }}" @else value="91"  @endif>
                                    <input type="hidden" id="updateMobileCountryISC" name="updateMobileCountryISC" @if($post->conisocode != '') value="{{ $post->conisocode }}" @else value="in"  @endif>
                                    <div id="phone-value">@if($post->mobile_no != '') {{  $post->contcode.''.$post->mobile_no  }} @else {{ '---' }} @endif</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#" data-bs-toggle="modal" data-bs-target="#updateMobile">يحرر &nbsp<i class="fi-edit"></i></a>

                                    <!-- <a class="nav-link py-0" href="#phone-collapse" data-bs-toggle="collapse">يحرر &nbsp<i class="fi-edit"></i></a> -->
                                </div>
                            </div>
                            <div class="collapse" id="phone-collapse" data-bs-parent="#personal-info">
                                <input class="form-control mt-3" name="mobile_no" type="text" placeholder="Enter mobile no..." data-bs-binded-element="#phone-value" data-bs-unset-value="Not specified" disabled value="{{ $post->mobile_no }}">
                            </div>
                        </div>
    
                        {{-- <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">Company name</label>
                                    <div id="company-value">@if($post->company_name != '') {{ $post->company_name }} @else {{ '---' }} @endif</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#company-collapse" data-bs-toggle="collapse">يحرر &nbsp<i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="company-collapse" data-bs-parent="#personal-info">
                                <input class="form-control mt-3" name="company_name" type="text" data-bs-binded-element="#company-value" data-bs-unset-value="{{ $post->company_name }}" placeholder="Enter company name">
                            </div>
                        </div> --}}
    
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">دولة</label>
                                    <div id="country-value">@if($post->conname != '') {{ $post->conname }} @else {{ '---' }} @endif</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#country-collapse" data-bs-toggle="collapse">يحرر &nbsp<i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="country-collapse" data-bs-parent="#personal-info">
                                <select name="country_id" class="form-control mt-3">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" @if($country->id == $post->country_id) selected @endif>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                {{-- <input class="form-control mt-3" type="text" data-bs-binded-element="#country-value" data-bs-unset-value="{{ $post->company_name }}" placeholder="Enter company name"> --}}
                            </div>
                        </div>
    
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">مدينة</label>
                                    <div id="city-value">@if($post->citname != '') {{ $post->citname }} @else {{ '---' }} @endif</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#city-collapse" data-bs-toggle="collapse">يحرر &nbsp<i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="city-collapse" data-bs-parent="#personal-info">
                                <select name="city_id" class="form-control mt-3">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @if($city->id == $post->city_id) selected @endif>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                {{-- <input class="form-control mt-3" type="text" data-bs-binded-element="#country-value" data-bs-unset-value="{{ $post->company_name }}" placeholder="Enter company name"> --}}
                            </div>
                        </div>
    
                        <div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="pe-2">
                                    <label class="form-label fw-bold">عنوان</label>
                                    <div id="address-value">@if($post->address != '') {{ $post->address }} @else {{ '---' }} @endif</div>
                                </div>
                                <div class="me-n3" data-bs-toggle="tooltip" title="Edit">
                                    <a class="nav-link py-0" href="#address-collapse" data-bs-toggle="collapse">يحرر &nbsp<i class="fi-edit"></i></a>
                                </div>
                            </div>
                            <div class="collapse" id="address-collapse" data-bs-parent="#personal-info">
                                {{-- <input class="form-control mt-3" name="address" type="text" data-bs-binded-element="#address-value" data-bs-unset-value="{{ $post->address }}" placeholder="Enter address"> --}}
                                <textarea name="address" class="form-control mt-3" cols="30" rows="5" data-bs-binded-element="#address-value" data-bs-unset-value="{{ $post->address }}" placeholder="Enter address">{{ $post->address }}</textarea>
                            </div>
                        </div>
                    </div>
    
                    <div class="d-flex align-items-center justify-content-between border-top mt-4 pt-4 pb-1">
                        <button class="btn btn-primary btn-sm px-3 px-sm-4" type="submit">احفظ التغييرات</button>
                        {{-- <button class="btn btn-link btn-sm px-0" type="button"><i class="fi-trash me-2"></i>Delete account</button> --}}
                    </div>
                </form>
            </div>

            <aside class="col-lg-4 col-md-5 pe-xl-4 mb-5">
                @include('layout.user.arabic.myorder')
            </aside>
            <!-- Content-->



        </div>
    </div>

    <!-- Update Mobile No Modal Start -->
    <div class="modal fade" id="updateMobile" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered p-2 my-0 mx-auto" style="max-width: 500px;">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title title-padding">تحديث الهاتف المحمول</h5>
              <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-0 py-2 py-sm-0" id="signinbody2updt">
              <div class="row mx-0 align-items-center">
                <div class="px-4 pb-4 px-sm-5 pb-sm-4 pt-4 firstpopup2updt" id="firstpopup2updt">
                    @if(isset($post->mobile_verified_at) && $post->mobile_verified_at != '')
                        <div class="verified-section">
                            <p class="text-success mb-0">
                                <i class="fi-check-circle mb-1"></i>
                                تم التحقق من رقم الجوال <strong>{{ $post->mobile_no }}</strong> بنجاح.
                            </p>

                            <p class="mb-1 mt-1">
                                إذا كنت ترغب في تحديث رقم الجوال،
                                <a href="javascript:void(0);" id="changeMobileNumber" class="fw-semibold text-primary text-decoration-underline">
                                    اضغط هنا
                                </a>
                                للمتابعة.
                            </p>
                        </div>
                    @else
                        <form  id="updateValidationform" method="POST">
                            @csrf
                            <div class="mb-4 col-md-12">

                            <label class="form-label mb-2" for="update-mobile2">الرجاء إدخال رقم هاتفك المحمول (واتساب)</label>
                            <input type="hidden" name="country_code_update" class="country_code_update">
                            <input class="form-control telephoneupdate" type="text" name="mobile" value="{{ $post->mobile_no }}" id="update-mobile2" placeholder="55xxxxxxx">
                            
                            <span style="color: red;font-size:12px;" id="errorOTPMUpdate" class="errorOTPMUpdate"></span>
                            @error('mobile')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>
                            <button class="btn btn-primary btn-lg w-100" type="submit">يكمل</button>
                        </form>
                    @endif
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <!-- Update Mobile No Modal End -->

@endsection

@section('page-script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function(){
            var newUpdtCountryCode = $('#updateMobileCountryISC').val();
            var newUpdtCountryNumber = $('#updateMobileCountryCode').val();        
            $('#updateMobile').on('show.bs.modal',function(e){

                $('.telephoneupdate').intlTelInput({
                    // localizedCountries: true,
                    onlyCountries: ["sa","in","qa","ae","kw"],
                    preferredCountries: [ "sa","in"],
                    separateDialCode: true,
                    initialCountry: newUpdtCountryCode,
                
                }).on('countrychange',function(e,countryData){
                    $('.country_code_update').val(($(".telephoneupdate").intlTelInput("getSelectedCountryData").dialCode))
                });
            });

            // GET OTP to verify when change number
            $('#updateValidationform').submit(function(e){
                e.preventDefault();

                var mobileNo = $('#update-mobile2').val();
                var countryCode = $('.country_code_update').val();

                if (countryCode != '') {
                    var phonecountrycode = countryCode;

                } else {
                    var phonecountrycode = newUpdtCountryNumber;
                }

                if (mobileNo != '') {
                    getOTP(phonecountrycode,mobileNo);
                    $("#errorOTPMUpdate").text("");
                } else {
                    $("#errorOTPMUpdate").text("الرجاء إدخال رقم الهاتف المحمول....");

                }

            });

            // Generate OTP
            function getOTP(phonecountrycode,mobileNo){
                $.ajax({
                    type: 'POST',
                    url: '{{ route('mobile.getOTPVerification2') }}',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        mobile: mobileNo,
                        country_code: phonecountrycode,
                        page: 'en'
                    },
                    success: function (data) {

                        if (data.status == 'success') {
                            $('#firstpopup2updt').html('<p>تم إرسال OTP إلى رقم WhatsApp الخاص بك</p>');
                            var editMobileupdate = '<div class="text-center">+'+data.countryCode+'' + data.mobile + ' <input type="hidden" id="hiddenmobileupdate2" value="'+data.mobile+'"><input type="hidden" id="hiddenphonecodeupdate2" value="'+data.iso_code+'"><input type="hidden" id="hiddenphonecodeupdate2S" value="'+data.countryCode+'"><button id="editMobileBtnupdate2" class="btn btn-sm btn-outline-primary mt-2"><i class="fi-pencil"></i> يحرر</button></div>';
                            $('#firstpopup2updt').append(editMobileupdate);
                            $('#firstpopup2updt').append('<form id="formSecondPopupUpdate2" method="post"><div class="mb-4"><label class="form-label mb-2 label-colour" for="inputSecondContentupdate2">أدخل الرمز المكون من 4 أرقام</label><input class="form-control valid" type="text" name="otp" id="inputSecondContentupdate2" placeholder="أدخل كلمة المرور لمرة واحدة" required=""><span id="otpRespError" style="color:red"></span><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">يُقدِّم</button></form>');

                        } else {
                            // $('#errorOTPMUpdate').text(data.message);
                            alert(data.message);
                        }

                        // alert(data.message);
                    }
                });
            }

            // Handle edit button click event
            $(document).on('click', '#editMobileBtnupdate2', function() {
                // Display input field to edit mobile number
                var recMobileupdt = $('#hiddenmobileupdate2').val();
                var phonecode25updt = $("#hiddenphonecodeupdate2").val();
                var phonecode2S5updt = $('#hiddenphonecodeupdate2S').val();

                var editMobileInputupt = '<label class="form-label mb-2" for="update-mobile3">الرجاء إدخال رقمك (واتساب)</label>';
                editMobileInputupt += '<div class="input-group mb-3">';
                editMobileInputupt += '<input type="hidden" name="country_code_update2" class="country_code_update2" value="'+phonecode2S5updt+'">';
                editMobileInputupt += '<input class="form-control telephoneupdate2" type="text" name="mobile" id="update-mobile3" placeholder="أدخل رقم الهاتف المحمول" value="' + recMobileupdt + '">';
                editMobileInputupt += '</div><span style="color:red;font-size:12px;" id="errorOTPMUpdate2"></span>';
                editMobileInputupt +=  '<button id="resendOtpBtnupdate2" class="btn btn-primary mt-2 btn-lg w-100">إعادة إرسال OTP</button>';

                $('#firstpopup2updt').html(editMobileInputupt);

                $('.telephoneupdate2').intlTelInput({
                
                    // localizedCountries: true,
                    onlyCountries: ["sa","in","qa","ae","kw"],
                    preferredCountries: [ "sa","in"],
                    separateDialCode: true,
                    initialCountry: phonecode25updt,
            
                }).on('countrychange',function(e,countryData){
                    $('.country_code_update2').val(($(".telephoneupdate2").intlTelInput("getSelectedCountryData").dialCode))
                });
            });


            // Handle form submission for OTP validation
            $(document).on('submit', '#formSecondPopupUpdate2', function(event) {
                event.preventDefault();

                var otp = $('#inputSecondContentupdate2').val();

                $.ajax({
                    type: 'POST',
                    url: '{{ route("mobile.getOTPValidation") }}',
                    data:{
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        otp: otp
                    },
                    success: function(data) {
                        if (data.status == 'success') {

                                $('#firstpopup2updt').html('<p>تم إرسال رمز التحقق إلى واتساب</p>');
                                var editMobileupdate = '<div class="text-center">+'+data.countryCode+'' + data.mobile + ' <input type="hidden" id="hiddenmobileupdate2" value="'+data.mobile+'"><input type="hidden" id="hiddenphonecodeupdate2" value="'+data.iso_code+'"><input type="hidden" id="hiddenphonecodeupdate2S" value="'+data.countryCode+'"><button id="editMobileBtnupdate2" class="btn btn-sm btn-outline-primary mt-2"><i class="fi-pencil"></i> يحرر</button></div>';
                                $('#firstpopup2updt').append(editMobileupdate);
                                $('#firstpopup2updt').append('<form id="formSecondPopupUpdate2" method="post"><div class="mb-4"><label class="form-label mb-2 label-colour" for="inputSecondContentupdate2">أدخل الرمز المكون من 4 أرقام</label><input class="form-control valid" type="text" name="otp" id="inputSecondContentupdate2" placeholder="Enter OTP" required=""><span id="otpRespError" style="color:red"></span><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">Submit</button></form>');

                            // Swal.fire({
                            //     title: "Success!",
                            //     text: response.message,
                            //     icon: "success",
                            //     customClass:{
                            //         confirmButton: 'btn btn-primary btn-sm',
                                    
                            //     }
                            // }).then(function(){
                            //     window.location = "{{ route('myprofile') }}";
                            // });
                        } else {
                            $('#otpRespError').text(data.message);
                        }
                    }
                });

            });

            // Handle resend OTP button click event
            $(document).on('click', '#resendOtpBtnupdate2', function() {
                var editedMobileUpdt = $('#update-mobile3').val();
                var countryCodeupdt = $('.country_code_update2').val();

                if (editedMobileUpdt != '') {
                    getOTP(countryCodeupdt,editedMobileUpdt);
                    $('#errorOTPMUpdate2').text("");
                }else{
                    $('#errorOTPMUpdate2').text("الرجاء إدخال رقم الهاتف المحمول....");
                }
            });

        });


    </script>

    <script>
        function readImg(input){
            $('#blah').empty();
            for (var i = 0; i < input.files.length; i++) {
                if (input.files && input.files[i]) {
                    var reader = new FileReader();
                    reader.onload = function(e){
                        $($.parseHTML('<img style="width: 150;height: 150;" class="img-thumbnail">&nbsp&nbsp')).attr('src', e.target.result).appendTo('#blah');
                    };

                    reader.readAsDataURL(input.files[i]);
                }
            }
        }
    </script>

    <script>
        $(document).ready(function(){
            $('#changeMobileNumber').click(function () {

                $('.verified-section').remove();

                $('#firstpopup2updt').html(`
                    <form id="updateValidationform" method="POST">

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="mb-4 col-md-12">

                            <label class="form-label mb-2" for="update-mobile2">
                                يرجى إدخال رقم الواتساب الخاص بك
                            </label>

                            <input type="hidden" name="country_code_update" class="country_code_update">

                            <input
                                class="form-control telephoneupdate"
                                type="text"
                                name="mobile"
                                value=""
                                id="update-mobile2"
                                placeholder="55xxxxxxx">

                            <span
                                style="color:red;font-size:12px;"
                                id="errorOTPMUpdate"
                                class="errorOTPMUpdate">
                            </span>

                        </div>

                        <button class="btn btn-primary btn-lg w-100" type="submit">
                            Continue
                        </button>

                    </form>
                `);

                $('.telephoneupdate').intlTelInput({
                    // localizedCountries: true,
                    onlyCountries: ["sa","in","qa","ae","kw"],
                    preferredCountries: [ "sa","in"],
                    separateDialCode: true,
                    initialCountry: 'in',
                
                }).on('countrychange',function(e,countryData){
                    $('.country_code_update').val(($(".telephoneupdate").intlTelInput("getSelectedCountryData").dialCode))
                });

                $('#updateValidationform').submit(function(e){
                    e.preventDefault();

                    var mobileNo = $('#update-mobile2').val();
                    var countryCode = $('.country_code_update').val();

                    if (countryCode != '') {
                        var phonecountrycode = countryCode;

                    } else {
                        var phonecountrycode = '91';
                    }

                    if (mobileNo != '') {
                        getOTP(phonecountrycode,mobileNo);
                        $("#errorOTPMUpdate").text("");
                    } else {
                        $("#errorOTPMUpdate").text("Please Enter Mobile No....");

                    }


                });

                // Generate OTP
                function getOTP(phonecountrycode,mobileNo){
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('mobile.getOTPVerification2') }}',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            mobile: mobileNo,
                            country_code: phonecountrycode,
                            page: 'en'
                        },
                        success: function (data) {

                            if (data.status == 'success') {
                                $('#firstpopup2updt').html('<p>تم إرسال رمز التحقق إلى واتساب</p>');
                                var editMobileupdate = '<div class="text-center">+'+data.countryCode+'' + data.mobile + ' <input type="hidden" id="hiddenmobileupdate2" value="'+data.mobile+'"><input type="hidden" id="hiddenphonecodeupdate2" value="'+data.iso_code+'"><input type="hidden" id="hiddenphonecodeupdate2S" value="'+data.countryCode+'"><button id="editMobileBtnupdate2" class="btn btn-sm btn-outline-primary mt-2"><i class="fi-pencil"></i> يحرر</button></div>';
                                $('#firstpopup2updt').append(editMobileupdate);
                                $('#firstpopup2updt').append('<form id="formSecondPopupUpdate2" method="post"><div class="mb-4"><label class="form-label mb-2 label-colour" for="inputSecondContentupdate2">أدخل الرمز المكون من 4 أرقام</label><input class="form-control valid" type="text" name="otp" id="inputSecondContentupdate2" placeholder="Enter OTP" required=""><span id="otpRespError" style="color:red"></span><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">Submit</button></form>');

                            } else {
                                $('#errorOTPMUpdate').text(data.message);
                                // alert(data.message);
                            }

                            // alert(data.message);
                        }
                    });
                }

                // Handle edit button click event
                $(document).on('click', '#editMobileBtnupdate2', function() {
                    // Display input field to edit mobile number
                    var recMobileupdt = $('#hiddenmobileupdate2').val();
                    var phonecode25updt = $("#hiddenphonecodeupdate2").val();
                    var phonecode2S5updt = $('#hiddenphonecodeupdate2S').val();

                    var editMobileInputupt = '<label class="form-label mb-2" for="update-mobile3">Please Enter Your Number (Whatsapp)</label>';
                    editMobileInputupt += '<div class="input-group mb-3">';
                    editMobileInputupt += '<input type="hidden" name="country_code_update2" class="country_code_update2" value="'+phonecode2S5updt+'">';
                    editMobileInputupt += '<input class="form-control telephoneupdate2" type="text" name="mobile" id="update-mobile3" placeholder="Enter Mobile No" value="' + recMobileupdt + '">';
                    editMobileInputupt += '</div><span style="color:red;font-size:12px;" id="errorOTPMUpdate2"></span>';
                    editMobileInputupt +=  '<button id="resendOtpBtnupdate2" class="btn btn-primary mt-2 btn-lg w-100">Resend OTP</button>';

                    $('#firstpopup2updt').html(editMobileInputupt);

                    $('.telephoneupdate2').intlTelInput({
                    
                        // localizedCountries: true,
                        onlyCountries: ["sa","in","qa","ae","kw"],
                        preferredCountries: [ "sa","in"],
                        separateDialCode: true,
                        initialCountry: phonecode25updt,
                
                    }).on('countrychange',function(e,countryData){
                        $('.country_code_update2').val(($(".telephoneupdate2").intlTelInput("getSelectedCountryData").dialCode))
                    });
                });


                // Handle form submission for OTP validation

                $(document).on('submit', '#formSecondPopupUpdate2', function(event) {
                    event.preventDefault();

                    var otp = $('#inputSecondContentupdate2').val();

                    $.ajax({
                        type: 'POST',
                        url: '{{ route("mobile.getOTPValidation") }}',
                        data:{
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            otp: otp
                        },
                        success: function(response) {
                            if (response.status == 'success') {

                               $('.firstpopup2updt').html(
                                    '<p class="text-success fw-semibold mb-0">' +
                                    '<i class="bi bi-check-circle-fill me-1"></i> تم التحقق من رقم الجوال بنجاح.' +
                                    '</p>'
                                );

                                setTimeout(function () {
                                    window.location.href = "{{ route('ar.myprofile') }}";
                                }, 1000);

                            } else {
                                $('#otpRespError').text(response.message);
                            }
                        }
                    });

                });

                // Handle resend OTP button click event
                $(document).on('click', '#resendOtpBtnupdate2', function() {
                    var editedMobileUpdt = $('#update-mobile3').val();
                    var countryCodeupdt = $('.country_code_update2').val();

                    if (editedMobileUpdt != '') {
                        getOTP(countryCodeupdt,editedMobileUpdt);
                        $('#errorOTPMUpdate2').text("");
                    }else{
                        $("#errorOTPMUpdate2").text("الرجاء إدخال رقم الهاتف المحمول....");
                    }
                });


            });
        });
    </script>
@endsection

