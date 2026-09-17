@extends('layout.admin.admin_layout')

@section('title','Front End Website')

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
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-home" aria-controls="navs-left-home" aria-selected="false">
                                Website Logo
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-about-us" aria-controls="navs-left-about-us" aria-selected="false">
                                About Us
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-contact-us" aria-controls="navs-left-contact-us" aria-selected="false">
                                Contact Us
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-left-bottom-contact-us" aria-controls="navs-left-bottom-contact-us" aria-selected="false">
                                Bottom Contact Us
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="navs-left-home">
                            <form action="{{ route('admin.frontwebsiteconfig.uploadlogo') }}" method="POST" enctype="multipart/form-data" id="uploadlogofrontwebsite">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="add-english-logo">Upload Logo</label>
                                            <input type="file" name="english_logo" id="add-english-logo" class="form-control english_logo">
                                        </div>
                                        <div>
                                            @if (isset($post) && $post->english_logo != '')
                                                <img src="{{ asset('user/img/logo/'.$post->english_logo) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadenglogo"/>                                            
                                            @else
                                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadenglogo"/>                                                
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="add-arabic-logo">Upload Arabic Logo</label>
                                            <input type="file" name="arabic_logo" id="add-arabic-logo" class="form-control arabic_logo">
                                        </div>
                                        <div>
                                            @if (isset($post) && $post->arabic_logo != '')
                                                <img src="{{ asset('user/img/logo/'.$post->arabic_logo) }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadarlogo"/>                                            
                                            @else
                                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadarlogo"/>                                                
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <button type="submit" class="btn btn-sm btn-primary">Upload Logo</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="navs-left-about-us">
                            <form action="{{ route('admin.frontwebsiteconfig.aboutusstore') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="add-about-us-eng" class="form-label">About Us</label>
                                            <textarea name="about_us_eng" class="form-control" id="add-about-us-eng" cols="30" rows="10">@if(isset($post) && $post->about_us_eng != '') {{ $post->about_us_eng }} @endif</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="add-about-us-ar" class="form-label">About Us Arabic</label>
                                            <textarea name="about_us_ar" class="form-control" id="add-about-us-ar" cols="30" rows="10">@if(isset($post) && $post->about_us_ar != '') {{ $post->about_us_ar }} @endif</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="navs-left-contact-us">
                            <form action="{{ route('admin.frontwebsiteconfig.contactusstore') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="add-contact-us-eng" class="form-label">Get in touch!</label>
                                            <textarea name="contact_us_eng" class="form-control" id="add-contact-us-eng" cols="30" rows="3">@if(isset($post) && $post->contact_us_eng != '') {{ $post->contact_us_eng }} @endif</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="add-contact-us-ar" class="form-label">Get in touch! Arabic</label>
                                            <textarea name="contact_us_ar" class="form-control" id="add-contact-us-ar" cols="30" rows="3">@if(isset($post) && $post->contact_us_ar != '') {{ $post->contact_us_ar }} @endif</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="add-contact-us-email" class="form-label">Drop us a line (Email)</label>
                                            <input type="text" name="contact_us_email" id="add-contact-us-email" class="form-control" @if(isset($post) && $post->contact_us_email) value="{{ $post->contact_us_email }}" @endif placeholder="Please enter Email...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="add-contact-us-phone" class="form-label">Call Us any time (Phone)</label>
                                            <input type="text" name="contact_us_phone" id="add-contact-us-phone" class="form-control" @if(isset($post) && $post->contact_us_phone) value="{{ $post->contact_us_phone }}" @endif placeholder="Please enter Phone Number...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="add-contact-us-location" class="form-label">Our Office (Location)</label>
                                            <input type="text" name="contact_us_location" id="add-contact-us-location" class="form-control" @if(isset($post) && $post->contact_us_location) value="{{ $post->contact_us_location }}" @endif placeholder="Please enter location...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="add-contact-us-location-ar" class="form-label">Our Office Arabic (Location)</label>
                                            <input type="text" name="contact_us_location_ar" id="add-contact-us-location-ar" class="form-control" @if(isset($post) && $post->contact_us_location_ar) value="{{ $post->contact_us_location_ar }}" @endif placeholder="Please enter location...">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade show" id="navs-left-bottom-contact-us">
                            <form action="{{ route('admin.frontwebsiteconfig.contactusbottomstore') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="add-contact-us-bottom-address" class="form-label">Office Address (Eng)</label>
                                            <input type="text" name="bottom_contact_us_addr" id="add-contact-us-bottom-address" @if(isset($post) && $post->bottom_contact_us_addr) value="{{ $post->bottom_contact_us_addr }}" @endif class="form-control" placeholder="Please enter office address...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="add-contact-us-bottom-address-arabic" class="form-label">Office Address (Arabic)</label>
                                            <input type="text" name="bottom_contact_us_addr_arabic" id="add-contact-us-bottom-address-arabic" @if(isset($post) && $post->bottom_contact_us_addr_arabic) value="{{ $post->bottom_contact_us_addr_arabic }}" @endif class="form-control" placeholder="Please enter office address...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="add-contact-email-us-email" class="form-label">Email</label>
                                            <input type="text" name="bottom_contact_us_email" id="add-contact-email-us-email" @if(isset($post) && $post->bottom_contact_us_email) value="{{ $post->bottom_contact_us_email }}" @endif class="form-control" placeholder="Please enter Email...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="add-contact-us-bottom-phone" class="form-label">Phone</label>
                                            <input type="text" name="bottom_contact_us_phone" id="add-contact-us-bottom-phone" @if(isset($post) && $post->bottom_contact_us_phone) value="{{ $post->bottom_contact_us_phone }}" @endif class="form-control" placeholder="Please enter Phone Number...">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="add-contact-us-bottom-fb-page" class="form-label">Facebook Page Link </label>
                                            <input type="text" name="bottom_contact_us_fb_link" id="add-contact-us-bottom-fb-page" class="form-control" placeholder="Enter Facebook Page Link..." @if(isset($post) && $post->bottom_contact_us_fb_link) value="{{ $post->bottom_contact_us_fb_link }}" @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="add-contact-us-bottom-twitter-x-page" class="form-label">Twitter Page Link</label>
                                            <input type="text" name="bottom_contact_us_twitter_link" id="add-contact-us-bottom-twitter-x-page" class="form-control" placeholder="Enter Twitter Page Link..." @if(isset($post) && $post->bottom_contact_us_twitter_link) value="{{ $post->bottom_contact_us_twitter_link }}" @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="add-contact-us-bottom-instagram-page" class="form-label">Instagram Page Link</label>
                                            <input type="text" name="bottom_contact_us_instagram_link" id="add-contact-us-bottom-instagram-page" class="form-control" placeholder="Enter Instagram Page Link..." @if(isset($post) && $post->bottom_contact_us_instagram_link) value="{{ $post->bottom_contact_us_instagram_link }}" @endif>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="add-contact-us-bottom-linkedin-page" class="form-label">Linkedin Page Link</label>
                                            <input type="text" name="bottom_contact_us_linkedin_link" id="add-contact-us-bottom-linkedin-page" class="form-control" placeholder="Enter Linkedin Page Link..." @if(isset($post) && $post->bottom_contact_us_linkedin_link) value="{{ $post->bottom_contact_us_linkedin_link }}" @endif>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-sm btn-primary">Submit</button>
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
    <script src="{{ asset('admin/assets/pages/validation/frontendwebsiteconf.js') }}"></script>

    <script>
        (function(){
            // Update or Reset image of logo
            let englishlogo = document.getElementById('uploadenglogo');
            const fileInput1 = document.querySelector('.english_logo');
            let arabiclogo = document.getElementById('uploadarlogo');
            const fileInput2 = document.querySelector('.arabic_logo');

            if (englishlogo) {
                fileInput1.onchange = () => {
                if (fileInput1.files[0]) {
                    englishlogo.src = window.URL.createObjectURL(fileInput1.files[0]);
                }
                };
            }

            if (arabiclogo) {
                fileInput2.onchange = () => {
                if (fileInput2.files[0]) {
                    arabiclogo.src = window.URL.createObjectURL(fileInput2.files[0]);
                }
                };
            }
        })();
    </script>

@endsection