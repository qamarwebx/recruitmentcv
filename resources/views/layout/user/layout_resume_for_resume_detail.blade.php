<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
  <meta charset="utf-8">
  <title>@yield('title')</title>
  <!-- SEO Meta Tags-->
  <meta name="description" content="Finder - Directory &amp; Listings Bootstrap Template">
  <meta name="keywords" content="bootstrap, business, directory, listings, e-commerce, car dealer, city guide, real estate, job board, user account, multipurpose, ui kit, html5, css3, javascript, gallery, slider, touch">
  <meta name="author" content="Createx Studio">
  <!-- Viewport-->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Favicon and Touch Icons-->
  @if($website['logo'] != '')
    <link href="{{ $website['logo'] }}" rel="icon" />
  @else
    <link href="{{ asset('user/img/favicon.png') }}" rel="icon" />
  @endif
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
  <meta name="msapplication-TileColor" content="#766df4">
  <meta name="theme-color" content="#ffffff">
  <!-- Vendor Styles-->
  <link rel="stylesheet" media="screen" href="{{ asset('user/vendor/simplebar/dist/simplebar.min.css') }}" />
  <link rel="stylesheet" media="screen" href="{{ asset('user/vendor/nouislider/dist/nouislider.min.css') }}" />
  <link rel="stylesheet" media="screen" href="{{ asset('user/vendor/tiny-slider/dist/tiny-slider.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
  <!-- Main Theme Styles + Bootstrap-->
  <link rel="stylesheet" media="screen" href="{{ asset('user/css/theme.min.css') }}">
  <link rel="stylesheet" href="{{ asset('user/css/errortheme.css') }}">
  <link rel="stylesheet" href="{{ asset('user/css/custom.css') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
  {{-- toastr --}}
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/fontawesome.css') }}">

  <link rel="stylesheet" href="{{ asset('user/intl-tel-input-master/build/css/intlTelInput.css') }}">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>
  <!-- <script src="https://unpkg.com/gsap@3/dist/gsap.min.js"></script> -->
  <!-- <script src="https://unpkg.com/gsap@3/dist/ScrollTrigger.min.js"></script> -->
<style>
  .form-select-sm {
        width: 80px; /* Adjust the width as needed */
    }
    .title-padding{
      padding-left: 26px;
    }
    .label-colour{
      color: grey;
      
    }
    .bottom-nav-item i{
      font-size: 20px !important;
    }
    /* For Samsung S8+ (360px width) */
    @media (max-width: 360px) {
        .website-logo {
            width: 145px;
        }
    }

    .robin_text .fi-calendar,.fi-briefcase {
      color: #646464 !important;
    }

    .bottom-nav-item a{
        text-decoration: none;
        color: #6a686e;
        font-weight: 600;
    }
</style>

  @yield('page-style')

</head>
<!-- Body-->

<body>
  <main class="page-wrapper">
    

    <!-- Update Mobile No Modal Start -->
    <div class="modal fade" id="updateMobile" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered p-2 my-0 mx-auto" style="max-width: 500px;">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title title-padding verifi-mobile-title">Please verify your mobile number to continue</h5>
              <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-0 py-2 py-sm-0" id="signinbody2updt">
              <div class="row mx-0 align-items-center">
                <div class="px-4 pb-4 px-sm-5 pb-sm-4 pt-4" id="firstpopup2updt">
                  <form  id="updateValidationform" method="POST">
                    @csrf
                    <div class="mb-4 col-md-12">
                        <input type="hidden" id="updateMobileCountryISC" name="updateMobileCountryISC"  value="in" >
                        <input type="hidden" id="updateMobileCountryCode" name="updateMobileCountryCode" value="91">

                      <label class="form-label mb-2" for="update-mobile2">Enter your whatsapp number</label>
                      <input type="hidden" name="country_code_update" class="country_code_update">
                      <input class="form-control telephoneupdate" type="text" name="mobile" value="{{ Auth::user()->mobile_no ?? '' }}" id="update-mobile2" placeholder="55xxxxxxx">
                      
                      <span style="color: red;font-size:12px;" id="errorOTPMUpdate" class="errorOTPMUpdate"></span>
                      @error('mobile')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                    <button class="btn btn-primary btn-lg w-100 btn-text" id="updateValidationBtn" type="submit">Continue</button>
                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <!-- Update Mobile No Modal End -->

    <!-- Sign In Modal start -->
    <div class="modal fade" id="signin-modal2" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg modal-dialog-centered p-2 my-0 mx-auto" style="max-width: 500px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title title-padding">Employer Login</h5>
            <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" id="clearForm" type="button" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body px-0 py-2 py-sm-0" id="signinbody2">
            <div class="row mx-0 align-items-center">
              <div class="px-4 pb-4 px-sm-5 pb-sm-4 pt-4" id="firstpopup2">
                <form  id="signinValidation2" method="POST">
                  @csrf
                  <div class="mb-4 col-md-12">
                    <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}" id="current_page_login2">
                    <input type="hidden" name="login_status" id="login_status">
                    <label class="form-label mb-2" for="signin-email2">Please enter your mobile number (Whatsapp)</label>
                    <div class="input-group">
                      {{-- <select name="countryCode" id="country-select2"  class="form-select form-select-sm">
                        @foreach (\App\Models\Otpcountrycode::orderBy('id','asc')->get() as $country)
                          <option value="{{ $country->id }},{{ $country->country_code }}">{{ $country->name }}</option>
                        @endforeach
                      </select> --}}
                      <input type="hidden" name="country_code_login" class="country_code_login">
                      <input class="form-control telephonelogin" type="text" name="mobile" id="signin-mobile2" placeholder="55xxxxxxx">
                      
                    </div>
                    <span style="color: red;font-size:12px;" id="errorOTPM"></span>
                    @error('mobile')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <button class="btn btn-primary btn-lg w-100" type="submit">Continue</button>
                  
                  <div class="d-flex align-items-center py-3 mb-3">
                    <hr class="w-100">
                    <div class="px-3">Or</div>
                    <hr class="w-100">
                  </div>
                  <a class="btn btn-outline-info w-100 mb-3" href="{{ route('googleLogin') }}"><i class="fi-google fs-lg me-1"></i>Sign in with Google</a>
                  <a class="btn btn-outline-info w-100 mb-3" href="{{ route('facbookLogin') }}"><i class="fi-facebook fs-lg me-1"></i>Sign in with Facebook</a>
                  <!-- <div class="mt-4">Don't have an account? <a href="#signup-modal" data-bs-toggle="modal" data-bs-dismiss="modal">Sign up here</a></div> -->
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sign In Modal End -->


    <!-- Sign In Modal-->
    <div class="modal fade" id="signin-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg modal-dialog-centered p-2 my-0 mx-auto" style="max-width: 500px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title title-padding">Employer Login</h5>
            <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"></button>

          </div>
          <div class="modal-body px-0 py-2 py-sm-0" id="signinbody">
            
            <div class="row mx-0 align-items-center">
              <div class="px-4 pb-4 px-sm-5 pb-sm-4 pt-4" id="firstpopup">
                <form  id="signinValidation" method="POST">
                  @csrf
                  <div class="mb-4">
                      <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}" id="current_page_login">
                      <label class="form-label mb-2" for="signin-email">Please enter your mobile number (Whatsapp)</label>
                      <div class="input-group">
                          <!-- Country dropdown -->
                          <select name="countryCode" id="country-select"  class="form-select form-select-sm">
                          @foreach (\App\Models\Otpcountrycode::orderBy('id','asc')->get() as $country)
                                  <option value="{{ $country->id }},{{ $country->country_code }}">{{ $country->name }}</option>
                              @endforeach
                          </select>
                          <!-- Phone number input -->
                          <input class="form-control" type="text" name="mobile" id="signin-mobile" placeholder="55xxxxxxx" required>
                      </div>
                      @error('mobile')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
                  <button class="btn btn-primary btn-lg w-100" type="submit">Continue</button>
                  <div class="d-flex align-items-center py-3 mb-3">
                    <hr class="w-100">
                    <div class="px-3">Or</div>
                    <hr class="w-100">
                  </div>
                  <a class="btn btn-outline-info w-100 mb-3" href="{{ route('googleLogin') }}"><i class="fi-google fs-lg me-1"></i>Sign in with Google</a>
                  <a class="btn btn-outline-info w-100 mb-3" href="{{ route('facbookLogin') }}"><i class="fi-facebook fs-lg me-1"></i>Sign in with Facebook</a>
                  <div class="mt-4">Don't have an account? <a href="#signup-modal" data-bs-toggle="modal" data-bs-dismiss="modal">Sign up here</a></div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Sign Up Modal-->
    <div class="modal fade" id="signup-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg modal-dialog-centered p-2 my-0 mx-auto" style="max-width: 500px;">
        <div class="modal-content">
          <div class="modal-body px-0 py-2 py-sm-0">
            <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"></button>
            <div class="row mx-0 align-items-center">
              <div class="px-4 pb-4 px-sm-5 pb-sm-5 pt-5">
                <form id="signupvalidation" action="{{ url('users/register-fresh') }}" method="POST">
                  @csrf
                  <div class="mb-4">
                    <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}">
                    <label class="form-label" for="signup-name">Full name</label>
                    <input class="form-control" type="text" name="name" id="signup-name" placeholder="Enter your full name">
                  </div>
                  <div class="mb-4">
                    <label class="form-label" for="signup-email">Email address</label>
                    <input class="form-control" type="email" name="email" id="signup-email" placeholder="Enter your email">
                  </div>
                  <div class="mb-4">
                    <label class="form-label" for="signup-password">Password <span class='fs-sm text-muted'>min. 8 char</span></label>
                    <div class="password-toggle">
                      <input class="form-control" type="password" name="password" id="signup-password" minlength="8">
                      <label class="password-toggle-btn" aria-label="Show/hide password">
                        <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                      </label>
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="form-label" for="signup-password-confirm">Confirm password</label>
                    <div class="password-toggle">
                      <input class="form-control" type="password" name="confirm_password" id="signup-password-confirm" minlength="8">
                      <label class="password-toggle-btn" aria-label="Show/hide password">
                        <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                      </label>
                    </div>
                  </div>
                  <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="agree" id="agree-to-terms">
                    <label class="form-check-label" for="agree-to-terms">By joining, I agree to the <a href='#'>Terms of use</a> and <a href='#'>Privacy policy</a></label>
                  </div>
                  <button class="btn btn-primary btn-lg w-100" type="submit">Sign up </button>
                  <div class="mt-4">Already have an Account? <a href="#signin-modal2" data-bs-toggle="modal" data-bs-dismiss="modal">Sign in here</a></div>
                  <div class="d-flex align-items-center py-3 mb-3">
                    <hr class="w-100">
                    <div class="px-3">Or</div>
                    <hr class="w-100">
                  </div>
                  <a class="btn btn-outline-info w-100 mb-3" href="{{ route('googleLogin') }}"><i class="fi-google fs-lg me-1"></i>Sign in with Google</a>
                  <a class="btn btn-outline-info w-100 mb-3" href="{{ route('facbookLogin') }}"><i class="fi-facebook fs-lg me-1"></i>Sign in with Facebook</a>

                </form>

              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Forgot Password Modal Start -->
    
    <div class="modal fade" id="forgot-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg modal-dialog-centered p-2 my-0 mx-auto" style="max-width: 500px;">
        <div class="modal-content">
          <div class="modal-body px-0 py-2 py-sm-0">
            <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"></button>
            <div class="row mx-0 align-items-center">
              <div class="px-4 pb-4 px-sm-5 pb-sm-5 pt-5">
                <form id="forgotpassvalidation" action="{{ route('password.forget.email') }}" method="POST">
                  @csrf
                  <div class="mb-4">
                    <label class="form-label" for="signup-email">Email address</label>
                    <input class="form-control" type="email" name="email" id="forgot-email" placeholder="Enter your email">
                  </div>
                  <button class="btn btn-primary btn-lg w-100" type="submit">Send Reset Password Link </button>
                
                </form>

              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Forgot Password Modal End-->

    @if (Auth::check())
      <div class="offcanvas offcanvas-end" id="demo-switcher">
        <div class="offcanvas-header d-block border-bottom">
          <div class="d-flex align-items-center justify-content-between">
            <h2 class="h5 mb-0">Your Order Status</h2>
            <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
          </div>
        </div>
        <div class="offcanvas-body">
          <!-- Inline steps: Vertical -->
          <div class="steps steps-vertical">
            <div class="step active">
              <div class="step-progress">
                <span class="step-progress-end"></span>
                <span class="step-number">1</span>
              </div>
              <div class="step-label">
                <h5 class="mb-2 pb-1">Step 1</h5>
                <p class="mb-0">Congratulations Step 1 has been completed</p>
              </div>
            </div>
            <div class="step active">
              <div class="step-progress">
                <span class="step-progress-end"></span>
                <span class="step-number">2</span>
              </div>
              <div class="step-label">
                <h5 class="mb-2 pb-1">Step 2</h5>
                <p class="mb-0">Congratulations Step 2 has been completed</p>
              </div>
            </div>
            <div class="step active">
              <div class="step-progress">
                <span class="step-progress-end"></span>
                <span class="step-number">3</span>
              </div>
              <div class="step-label">
                <h5 class="mb-2 pb-1">Step 3</h5>
                <p class="mb-0">Congratulations Step 3 has been completed</p>
              </div>
            </div>
            <div class="step">
              <div class="step-progress">
                <span class="step-progress-end"></span>
                <span class="step-number">4</span>
              </div>
              <div class="step-label">
                <h5 class="mb-2 pb-1">Step 4</h5>
                <p class="mb-0">Waiting</p>
              </div>
            </div>
            <div class="step">
              <div class="step-progress">
                <span class="step-progress-end"></span>
                <span class="step-number">5</span>
              </div>
              <div class="step-label">
                <h5 class="mb-2 pb-1">Step 5</h5>
                <p class="mb-0">Waiting</p>
              </div>
            </div>
            <div class="step">
              <div class="step-progress">
                <span class="step-progress-end"></span>
                <span class="step-number">6</span>
              </div>
              <div class="step-label">
                <h5 class="mb-2 pb-1">Step 6</h5>
                <p class="mb-0">Waiting</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif

    <!-- Navbar-->
    <header class="navbar navbar-expand-lg navbar-light bg-light fixed-top" data-scroll-header>
      <div class="container">

        <a class="navbar-brand me-2 me-xl-4" href="{{ route('welcome') }}">
         @if($website['logo'] != '')
            <img src="{{ $website['logo'] }}" width="80">
          @else
            <x-brand-logo mode="light" lang="en" class="d-block website-logo" width="200" alt="Qamr" />
          @endif

        </a>
        <!-- <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"><span
            class="navbar-toggler-icon"></span></button> -->

            <a class="langmob me-1" href="javascript:void(0)" id="arabiclanguagem" style="font-family: 'Tajawal', sans-serif !important"><i class="fi-globe fs-base me-1"></i>العربية</a>

        @if (Auth::check())
          
          <a class="d-md-none me-2" data-bs-toggle="offcanvas" data-bs-target="#filters-top">  
            @if (Auth::user()->photo != '')
              <img class="rounded-circle px-1" src="{{ asset('user/img/avatars/'.Auth::user()->photo) }}" width="40" alt="Annette Black">              
            
            @elseif(Auth::user()->avatar_url != '')
              <img class="rounded-circle px-1" src="{{ Auth::user()->avatar_url }}" width="40" alt="Annette Black">              

            @else
              <img class="rounded-circle px-1" src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" width="40"  alt="Annette Black">                  
            @endif
                    
            
          </a>

          <div class="dropdown d-none d-lg-block order-lg-3 my-n2 me-3">
              <a class="d-block py-2" href="#">

                @if (Auth::user()->photo != '')
                  <img class="rounded-circle" src="{{ asset('user/img/avatars/'.Auth::user()->photo) }}" width="40" alt="Annette Black">
                @elseif(Auth::user()->avatar_url != '')
                  <img class="rounded-circle" src="{{ Auth::user()->avatar_url }}" width="40" alt="Annette Black">
                @else
                  <img class="rounded-circle" src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" width="40" alt="Annette Black">                    
                @endif

              </a>
              <div class="dropdown-menu dropdown-menu-end">
                <div class="d-flex align-items-start border-bottom px-3 py-1 mb-2" style="width: 16rem;">

                  @if (Auth::user()->photo != '')
                    <img class="rounded-circle" src="{{ asset('user/img/avatars/'.Auth::user()->photo) }}" width="48" alt="Annette Black">
                  
                  @elseif (Auth::user()->avatar_url != '')
                    <img class="rounded-circle" src="{{ Auth::user()->avatar_url }}" width="48" alt="Annette Black">

                  @else
                    <img class="rounded-circle" src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" width="48" alt="Annette Black">
                      
                  @endif

                  <div class="ps-2">
                    <h6 class="fs-base mb-0">@if(Auth::check()) {{ Auth::user()->name }} @endif</h6>
                    <div class="fs-xs py-2">@if(Auth::user()->mobile_no != '') {{ Auth::user()->mobile_no }} <br> @endif{{ Auth::user()->email }}</div>
                  </div>
                </div>
                <a class="dropdown-item" href="{{ route('myorder') }}"><i class="fi-home opacity-60 me-2"></i>My Order</a>
                <a class="dropdown-item" href=""><i class="fi-heart opacity-60 me-2"></i>Wishlist</a>
                <a class="dropdown-item" href="{{ route('myprofile') }}"><i class="fi-user opacity-60 me-2"></i>Personal Info</a>
                <a class="dropdown-item" href=""><i class="fi-lock opacity-60 me-2"></i>Password &amp; Security</a>
                <a class="dropdown-item" href=""><i class="fi-bell opacity-60 me-2"></i>Notifications</a>
                <div class="dropdown-divider"></div>
                @if (Auth::check())

                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}">
                  <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();">Sign Out</a>
                </form>
                @endif
              </div>
          </div>          
        @else
          <a class="btn btn-primary btn-sm ms-2 order-lg-3" href="#signin-modal2" data-bs-toggle="modal"><i class="fi-user me-2"></i>Login</a>            
        @endif


        <div class="collapse navbar-collapse order-lg-2" id="navbarNav">
          <ul class="navbar-nav navbar-nav-scroll" style="max-height: 35rem;">
            <!-- Demos switcher-->
            <li class="nav-item dropdown {{ (request()->routeIs('welcome')) ?'active': '' }}"><a class="nav-link " href="{{ route('welcome') }}"> <i class="fi-home fs-base me-2"></i>
                Home <span class="d-none d-lg-block position-absolute top-50 end-0 translate-middle-y border-end" style="width: 1px; height: 30px;"></span></a>
            </li>
            <!-- Menu items-->


            <li class="nav-item dropdown {{ (request()->routeIs('resumes')) || (request()->routeIs('fullresume')) ?'active':'' }} me-lg-2"><a class="nav-link align-items-center pe-lg-4" href="{{ route('resumes') }}"
                role="button" aria-expanded="false"><i class="fi-file me-2"></i>All Workers CV</a>
            </li>
            {{-- <li class="nav-item dropdown {{ (request()->routeIs('resumes')) || (request()->routeIs('fullresume')) ?'active':'' }} me-lg-2"><a class="nav-link align-items-center pe-lg-4" href="{{ route('resumes') }}"
                role="button" aria-expanded="false"><i class="fi-file me-2"></i>CV Available</a>
            </li> --}}


            <li class="nav-item dropdown {{ (request()->routeIs('myorder')) ?'active':'' }} me-lg-2">
              @if (Auth::check())
                <a class="nav-link align-items-center pe-lg-4" href="{{ route('myorder') }}"><i class="fi-cart me-2"></i>My Order</a>
              @else
                <a class="nav-link align-items-center pe-lg-4" href="#signin-modal2" data-bs-toggle="modal" role="button" aria-expanded="false" ><i class="fi-cart me-2"></i>My Order</a>
              @endif
            </li>

            <li class="nav-item dropdown {{ (request()->routeIs('about')) ?'active':'' }}">
                <a class="nav-link" href="{{ route('about') }}"><i class="fi-info-circle fs-base me-2"></i>About Us </a>
            </li>
            {{-- <li class="nav-item dropdown {{ (request()->routeIs('service')) ?'active':'' }} me-lg-2"><a class="nav-link align-items-center pe-lg-4" href="{{ route('service') }}"
                role="button" aria-expanded="false"><i class="fi-layers me-2"></i>Services</a>
            </li> --}}
            <li class="nav-item dropdown {{ (request()->routeIs('contact')) ?'active':'' }}">
              <a class="nav-link" href="{{ route('contact') }}"><i class="fi-phone fs-base me-2"></i>Contact </a>
            </li>
            <li class="nav-item dropown">
              <a class="nav-link " href="javascript:void(0)" id="arabiclanguage" style="font-family: 'Tajawal', sans-serif !important"><i class="fi-globe fs-base me-2"></i>العربية</a>
            </li>
          </ul>
        </div>
      </div>
    </header>
    <!-- Page content-->
    <!-- Property cost calculator modal-->
    <div class="modal fade" id="form" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header d-block position-relative border-0 px-sm-5 px-4">
            <h3 class="h4 modal-title mt-4 text-center">Please enter your Details.</h3>
            <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body px-sm-5 px-4">
            <form class="needs-validation" novalidate>
              <div class="pt-2 mb-3">
                <label class="form-label fw-bold mb-2" for="property-address">Name</label>
                <input class="form-control" type="text" id="property-address" placeholder="Enter your Name" required>
              </div>
              <div class="pt-2 mb-4">
                <label class="form-label fw-bold mb-2" for="property-area">Email</label>
                <input class="form-control" type="text" id="property-area" placeholder="Enter your Email" required>
                <div class="invalid-feedback">Please enter your Details.</div>
              </div>
              <div class="pt-2 mb-4">
                <label class="form-label fw-bold mb-2" for="property-area">Contact Number</label>
                <input class="form-control" type="text" id="property-area" placeholder="Enter your Contact Number"
                  required>
                <div class="invalid-feedback">Please enter your Details.</div>
              </div><a href="#"></a>
              <button class="btn btn-primary d-block w-100 mb-4" type="submit">Submit and Next</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    @yield('content')

  </main>
  <div class="box-solid"></div>

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="footer-top">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-4 col-md-12 footer-info">
            <a href="index.html" class="d-flex align-items-center">
            @if($website['logo'] != '')
              <img src="{{ $website['logo'] }}" width="80">
            @else
                <x-brand-logo mode="light" lang="en" alt="Qamr" />
            @endif
            </a>
            <p>@if(!empty($website['company_name'])) {{ $website['company_name'] }} @else QAMR International @endif offers complete Human Resource Management solutions and addresses its vital talent by
              providing an inclusive recruitment process.</p>
            <div class="social-links mt-3">
              <a @if(isset($frontwebsite) && $frontwebsite->bottom_contact_us_twitter_link != '') href="{{ $frontwebsite->bottom_contact_us_twitter_link }}" @else href="https://twitter.com/qamrintl" @endif class="twitter" target="_blank"><i class="fi-twitter"></i></a>
              <a @if(isset($frontwebsite) && $frontwebsite->bottom_contact_us_fb_link != '') href="{{ $frontwebsite->bottom_contact_us_fb_link }}" @else href="https://www.facebook.com/qamrintl" @endif class="facebook" target="_blank"><i class="fi-facebook"></i></a>
              <a @if(isset($frontwebsite) && $frontwebsite->bottom_contact_us_instagram_link != '') href="{{ $frontwebsite->bottom_contact_us_instagram_link }}" @else href="https://www.instagram.com/qamrintl/" @endif class="instagram" target="_blank"><i class="fi-instagram"></i></a>
              <a @if(isset($frontwebsite) && $frontwebsite->bottom_contact_us_linkedin_link != '') href="{{ $frontwebsite->bottom_contact_us_linkedin_link }}" @else href="https://www.linkedin.com/company/13375661/admin/" @endif class="linkedin" target="_blank"><i class="fi-linkedin"></i></a>
            </div>
          </div>

          <div class="col-lg-2 offset-lg-1 col-6 footer-links">
            <h4>Useful Links</h4>
            <ul>
              <li><i class="fi-chevron-right"></i> <a href="{{ route('welcome') }}">Home</a></li>
              <li><i class="fi-chevron-right"></i> <a href="{{ route('about') }}">About us</a></li>
              <li><i class="fi-chevron-right"></i> <a href="{{ route('service') }}">Services</a></li>
              <li><i class="fi-chevron-right"></i> <a href="">Our Team</a></li>
              <li><i class="fi-chevron-right"></i> <a href="{{ route('contact') }}">Contact Us</a></li>
            </ul>
          </div>

          <div class="col-lg-2 col-6 footer-links">
            <h4>Our Services</h4>
            <ul>
              <li><i class="fi-chevron-right"></i> <a href="#">Sourcing</a></li>
              <li><i class="fi-chevron-right"></i> <a href="#">Shortlisting</a></li>
              <li><i class="fi-chevron-right"></i> <a href="#">Interviews</a></li>
              <li><i class="fi-chevron-right"></i> <a href="#">Selection</a></li>
              <li><i class="fi-chevron-right"></i> <a href="#">Deployment</a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
            <h4>Contact Us</h4>
            <p>
              {!! $website['company_address'] 
                  ? nl2br(e($website['company_address'])) 
                  : 'Naseem Mansion, Charnull,<br>Dongri, Mumbai, 400009<br>India' 
              !!}

              <br><br>

              <strong>Phone:</strong> 
              {{ $website['company_mobile'] ?: '+919004006272' }}

              <br>

              <strong>Email:</strong> 
              {{ $website['company_email'] ?: 'hr@qamrintl.com' }}

              <br>
          </p>
          </div>
        </div>
      </div>
      <section class="black-footer">
        <div class="container">
          <div class="bg-dark rounded-3">
            <div
              class="col-xxl-10 col-md-11 col-10 d-flex flex-md-row flex-column-reverse align-items-md-end align-items-center mx-auto px-0">
              <img class="flex-shrink-0 mt-md-n5 me-md-5" src="{{ asset('user/img/real-estate/illustrations/mobile2.svg') }}" width="240"
                alt="Finder mobile app">
              <div
                class="align-self-center d-flex flex-lg-row flex-column align-items-lg-center pt-md-3 pt-5 ps-xxl-4 text-md-start text-center">
                <div class="me-md-5">
                  <h3 class="text-light">متوفر الآن سيفيات لسائقين مـن الهند
                    سارع بتقديم
                    طلبك</h3>
                  <p class="mb-lg-0 text-light text-center">اضغط على زر الواتس اب لتطلب
                    الآن</p>
                  <div class=" order text-center">
                    <button type="button" class="btn btn-success"><i class="fi-whatsapp"></i>Whatsapp</button>
                    {{-- <a href="https://wa.me/message/AL4R37LTO6JMP1" target="_blank">
                      <button type="button" class="btn btn-success"><i class="fi-whatsapp"></i>Whatsapp</a></button> --}}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>


    <div class="container">
      <div class="copyright">
        &copy; Copyright <?= date('Y') ?> <strong><span>Qamr International</span></strong>. All Rights Reserved
      </div>
      <div class="credits">
      </div>
    </div>
  </footer><!-- End Footer -->

   <!-- Bottom Navigation Start New* -->
   <div class="bottom-nav d-lg-none bottom-nav-fixed w-100">
    {{-- @if (Auth::check())
      <div class="bottom-nav-item">
        <a href="{{ route('myorder') }}">
          <i class="fi-cart"></i>
          <p>My Order</p>
        </a>
      </div>
    @else
      <div class="bottom-nav-item">
        <a href="#signin-modal" data-bs-toggle="modal" role="button" aria-expanded="false">
          <i class="fi-cart"></i>
          <p>My Order</p>
        </a>
      </div>
    @endif --}}
    <div class="bottom-nav-item">
      <a href="{{ route('welcome') }}">
        <i class="fi-home"></i>
        <p>Home</p>
      </a>
    </div>

    <div class="bottom-nav-item">
      @if (Auth::check())
      <a href="{{ route('myorder') }}">
        <i class="fi-cart"></i>
        <p>My Order</p>
      </a>
      @else
        <a href="#signin-modal2" data-bs-toggle="modal" data-id="0" role="button" aria-expanded="false">
          <i class="fi-cart"></i>
          <p>My Order</p>
        </a>
      @endif

    </div>

    <div class="bottom-nav-item">
      <a href="{{ route('resumes') }}">
        <i class="fi-file"></i>
        <p>All Workers CV</p>
        {{-- <p>Hire Now</p> --}}
      </a>
    </div>
    {{-- @if (Route::is('about'))
      <div class="bottom-nav-item">
        <a href="{{ route('welcome') }}">
          <i class="fi-home"></i>
          <p>Home</p>
        </a>
      </div>
    @else
      <div class="bottom-nav-item">
        <a href="{{ route('about') }}">
          <i class="fi-info-circle"></i>
          <p>About Us</p>
        </a>
      </div>
    @endif --}}

    <div class="bottom-nav-item">
      <a href="#" data-bs-toggle="offcanvas" data-bs-target="#filters-top">
        <i class="fi-align-justify"></i>
        <p>Menu</p>
      </a>
    </div>
  </div>
  <!-- Bottom Navigation End New* -->

  
  <aside class="col-lg-4 col-xl-3 shadow-sm px-3 px-xl-4 px-xxl-5 pt-lg-2 d-md-none">
    <div class="offcanvas offcanvas-end offcanvas-collapse" id="filters-top">
        <div class="offcanvas-header d-flex d-lg-none align-items-center">
            <h2 class="h5 mb-0">@if(Auth::check()) {{ Auth::user()->name }} @endif</h2>
            <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body py-lg-4">

          <a class="dropdown-item border-bottom py-3" href="{{ route('myorder') }}"><i class="fi-home opacity-60 me-2"></i>My Order</a>

          <a class="dropdown-item border-bottom py-3" href=""><i class="fi-heart opacity-60 me-2"></i>Wishlist</a>

          <a class="dropdown-item border-bottom py-3" href="{{ route('myprofile') }}"><i class="fi-user opacity-60 me-2"></i>Personal Info</a>

          <a class="dropdown-item border-bottom py-3" href=""><i class="fi-lock opacity-60 me-2"></i>Password &amp; Security</a>

          <a class="dropdown-item border-bottom py-3" href=""><i class="fi-bell opacity-60 me-2"></i>Notifications</a>
          @if (Auth::check())

          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}">
            <a class="dropdown-item border-bottom py-3" href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();"><i class="fi-logout opacity-60 me-2"></i>Sign Out</a>
          </form>
          @endif
          {{-- <a class="dropdown-item border-bottom py-3" href="#"><i class="fi-logout opacity-60 me-2"></i>Sign Out</a> --}}
        </div>
    </div>
  </aside>
@php
    $countries = \App\Models\Otpcountrycode::orderBy('id','asc')->get();
  @endphp
  <a class="btn-scroll-top" href="#top" data-scroll><span
      class="btn-scroll-top-tooltip text-muted fs-sm me-2">Top</span><i class="btn-scroll-top-icon fi-chevron-up">
    </i></a>
  <!-- Vendor scrits: js libraries and plugins-->
  <script src="{{ asset('user/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('user/vendor/simplebar/dist/simplebar.min.js') }}"></script>
  <script src="{{ asset('user/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js') }}"></script>
  <script src="{{ asset('user/vendor/nouislider/dist/nouislider.min.js') }}"></script>
  <script src="{{ asset('user/vendor/tiny-slider/dist/min/tiny-slider.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
  <!-- Main theme script-->
  <script src="{{ asset('user/js/theme.min.js') }}"></script>
  <script src="{{ asset('user/js/jquery.validate.min.js') }}"></script>
  {{-- <script src="{{ asset('user/js/page/formvalidation.js') }}"></script> --}}
    
  {{-- toastr js --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

  <script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>

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

               let btn = $('#updateValidationBtn');
                btn.prop('disabled', true);
                btn.find('.btn-text').text('Processing...');
                btn.find('.spinner-border').removeClass('d-none');

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
                          $('#firstpopup2updt').html('<p class="otpSentMsg">Verification code sent to your WhatsApp</p>');
                          var editMobileupdate = '<div class="text-center otpSentMsg">+'+data.countryCode+'' + data.mobile + ' <input type="hidden" id="hiddenmobileupdate2" value="'+data.mobile+'"><input type="hidden" id="hiddenphonecodeupdate2" value="'+data.iso_code+'"><input type="hidden" id="hiddenphonecodeupdate2S" value="'+data.countryCode+'"><button id="editMobileBtnupdate2" class="btn btn-sm btn-outline-primary mt-2"><i class="fi-pencil"></i> Edit</button></div>';
                          $('#firstpopup2updt').append(editMobileupdate);
                          $('#firstpopup2updt').append('<form id="formSecondPopupUpdate2" method="post"><div class="mb-4"><label class="form-label mb-2 label-colour" for="inputSecondContentupdate2">Enter the 4-digit code</label><input class="form-control valid" type="text" name="otp" id="inputSecondContentupdate2" placeholder="Enter OTP" required=""><span id="otpRespError" style="color:red"></span><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">Submit</button></form>');

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
                        $('.verifi-mobile-title').remove();
                        $('.otpSentMsg').remove();  
                        $('#formSecondPopupUpdate2').html('');
                        $('#formSecondPopupUpdate2').html(
                            '<p class="text-success fw-semibold mb-0">' +
                            '<i class="bi bi-check-circle-fill me-1"></i> Mobile number verified.' +
                            '</p>'
                        );

                        $("#updateMobile").modal("hide");
                        $("#modalLarge3").modal("show");
                          // Swal.fire({
                          //     title: "Success!",
                          //     text: response.message,
                          //     icon: "success",
                          //     customClass:{
                          //         confirmButton: 'btn btn-primary btn-sm',
                                  
                          //     }
                          // }).then(function(){
                          //     window.location.reload();
                          // });
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
                  $('#errorOTPMUpdate2').text("Please Enter Mobile No....");
              }
          });



      $('#forgotpassvalidation').validate({
        rules:{
          email: {
            required: true,
            email: true,
            remote: {
              type: "POST",
              url: "{{ url('/forgot/password/email/find') }}",
              data:{
                email: function(){
                  return $('#forgot-email').val();
                },
                _token: "{{ csrf_token() }}"
              }
            }
          },
        },
        messages:{
            email:{
              required: "Please enter email",
              email: "Please enter valid email",
              remote: "Email is not found, Please signup!"
            },
          },
          errorElement: "strong",
          errorPlacement: function(error, element){
            error.addClass( "invalid-feedbackNew" );
            if ( element.prop( "type" ) === "checkbox" ) {
              error.insertAfter( element.next( "label" ) );
            } else {
              error.insertAfter( element );
            }
          },
          highlight: function ( element, errorClass, validClass ) {
            $( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
          },
          unhighlight: function (element, errorClass, validClass) {
            $( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
          }

      });
    });
  </script>

<script>
  $(document).ready(function(){
    $('#signin-modal2 .modal-title').show();
    $('#signin-modal2 .modal-footer').show();


    // Get Login Status
    var loginStatus;
    var cand_idLogin = $('#postIDTEXT').val();
    
    $('#signin-modal2').on("show.bs.modal",function(e){
      loginStatus = $(e.relatedTarget).data('id');
    });


    

    // Send OTP
    function sendOTP2(mobile_no,phoneCode){
      // var finalCountryCode = country_code.split(",");
      var finalCountryCode = phoneCode;
      $.ajax({
        type: "POST",
        url: "{{ route('generate-otp2') }}",
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          mobile: mobile_no,
          country_code: phoneCode,
        },
        success: function(response){
          // console.log(response);
          if (response.message == 'responsesuccess') {
            $('#firstpopup2').html('<p>Mobile Number has been verified!</p>');
            $.ajax({
              type: 'POST',
              url: '{{ route("login-user2") }}',
              data: {
                _token: $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                if (response.status === 'new_user') {
                  $('#firstpopup2').append('<form id="registerForm2" method="post"><div class="mb-4"><label class="form-label mb-2">Enter Your Full Name</label><input class="form-control valid" type="text" name="name" id="inputThirdContent2" placeholder="Enter Name" required=""><br><span style="color:red;font-size:13px;" id="registrationNameerror"></span><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">Submit</button></form>');
                } else {
                  loginUser2();
                }
              }

            });
          }else if (response.message == 'otpsend') {
            $('#firstpopup2').html('<p>Verification code sent to your WhatsApp</p>');
            var editMobileInput = '<div class="text-center">+'+response.countryCode+'' + response.mobile + '  <input type="hidden" id="hiddenmobile2" value="'+response.mobile+'"><input type="hidden" id="hiddenphonecode2" value="'+response.iso_code+'"><input type="hidden" id="hiddenphonecode2S" value="'+response.countryCode+'"><button id="editMobileBtn2" class="btn btn-sm btn-outline-primary mt-2"><i class="fi-pencil"></i> Edit</button></div>';
            $('#firstpopup2').append(editMobileInput);
            $('#firstpopup2').append('<form id="formSecondPopup2" method="post"><div class="mb-4"><label class="form-label mb-2 label-colour" for="signin-email">Enter the 4-digit code</label><input class="form-control valid" type="text" name="otp" id="inputSecondContent2" placeholder="Enter OTP" required=""><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">Login</button></form>');

          }else if(response.message == 'responsefailed'){
            alert("Your Mobile number is not verified,please try to different!");
          }else if (response.message == 'otpnotsend') {
            alert("Your Mobile number is not verified,please try to different!");
          }else if (response.message == 'systemerror') {
            alert("Due to technical error, please try after some time.")
          }else{
            alert("Due to technical error, please try after some time.")

          }
        },
        error: function(xhr, status, error){
          console.log(xhr);
        }
      });
    }

    // Handle edit button click event
    $(document).on('click', '#editMobileBtn2', function() {
        // Display input field to edit mobile number
        var recMobile = $('#hiddenmobile2').val();
        var phonecode25 = $("#hiddenphonecode2").val();
        var phonecode2S5 = $('#hiddenphonecode2S').val();
        var googleroute = "{{ route('googleLogin') }}";
        var facebookroute = "{{ route('facbookLogin') }}";
        // var editMobileInput = '<label class="form-label mb-2" for="signin-email">Please Enter Your Number (Whatsapp)</label><div class="input-group mb-3"><select name="country_code" class="form-select form-select-sm">@foreach ($countries as $country)<option value="{{ $country->id }},{{ $country->country_code }}">{{ $country->name }}</option>@endforeach</select><input class="form-control" type="text" name="mobile" id="edited-mobile2" placeholder="Enter Mobile No" value="' + recMobile + '"></div><span style="color:red;font-size:12px;" id="errorOTPM2"></span>';
        var editMobileInput = '<label class="form-label mb-2" for="signin-email">Please Enter Your Number (Whatsapp)</label><div class="input-group mb-3"><input type="hidden" name="phone_code" class="phone_code_login2" value="'+phonecode2S5+'"><input class="form-control telephonelogin2" type="text" name="mobile" id="edited-mobile2" placeholder="Enter Mobile No" value="' + recMobile + '"></div><span style="color:red;font-size:12px;" id="errorOTPM2"></span>';
        editMobileInput +=  '<button id="resendOtpBtn2" class="btn btn-primary mt-2 btn-lg w-100">Resend OTP</button>';
        editMobileInput += '<div class="d-flex align-items-center py-3 mb-3"><hr class="w-100"><div class="px-3">Or</div><hr class="w-100"></div>';
        editMobileInput += '<a class="btn btn-outline-info w-100 mb-3" href="'+googleroute+'"><i class="fi-google fs-lg me-1"></i>Sign in with Google</a><a class="btn btn-outline-info w-100 mb-3" href="'+facebookroute+'"><i class="fi-facebook fs-lg me-1"></i>Sign in with Facebook</a>';
        $('#firstpopup2').html(editMobileInput);

        $('.telephonelogin2').intlTelInput({
              
              // localizedCountries: true,
              onlyCountries: ["sa","in","qa","ae","kw"],
              preferredCountries: [ "sa","in"],
              separateDialCode: true,
              initialCountry: phonecode25,
          
          }).on('countrychange',function(e,countryData){
              $('.phone_code_login2').val(($(".telephonelogin2").intlTelInput("getSelectedCountryData").dialCode))
          });

        // // Handle resend OTP button click event
        // $(document).on('click', '#resendOtpBtn2', function() {
        //     var editedMobile = $('#edited-mobile2').val();
        //     var countryCode = $('select[name="country_code"]').val();
        //     if (editedMobile == '') {
        //       $('#errorOTPM2').text('Please enter Mobile No.');
        //     } else {
        //       $('#errorOTPM2').text('');
        //       sendOTP2(editedMobile, countryCode);

        //     }
        // });
    });

    // Handle resend OTP button click event
    $(document).on('click', '#resendOtpBtn2', function() {
            var editedMobile = $('#edited-mobile2').val();
            
            // var countryCode = $('select[name="country_code"]').val();
            var countryCode = $('.phone_code_login2').val();
            if (editedMobile == '') {
              $('#errorOTPM2').text('Please enter Mobile No.');
            } else {
              $('#errorOTPM2').text('');
              sendOTP2(editedMobile, countryCode);

            }
        });

    // Validate Form
    $('#signinValidation2').submit(function(event){
      event.preventDefault();
      var mobile_no = $('#signin-mobile2').val();
      var country_code = $('.country_code_login').val();

      if (country_code != '') {
        var phoneCode = country_code;
      } else {
        var phoneCode = '966';
      }

      if(mobile_no == ''){
        $('#errorOTPM').text('Please enter Mobile No.');
      }else{
        $('#errorOTPM').text('');
        sendOTP2(mobile_no,phoneCode);
      }

    });

    // Function to handle user login
    function loginUser2() {
      $.ajax({
          type: 'POST',
          url: '{{ route("login-user2") }}',
          data: {
              _token: $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            // console.log(response);  
            
           
            if (loginStatus == 1) {
              $("#signin-modal2").modal("hide");
              // Refresh Navbar and Content Area
              $('.headernav').load(" .headernav")
              $('.ordersection-div').load(" .ordersection-div");
              
              // Show Modal As Per Ajax Request
              
              jQuery.ajax({
                type: "POST",
                url: "{{ route('checkloginorderstatus') }}",
                data:{
                  cand_id: cand_idLogin,
                  _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response2){

                  console.log(response2);
                  if (response2.data_status == 'hire') {
                    window.location.reload();
                  }

                  if (response2.data_status == 'modalLarge3') {
                    $("#modalLarge3").modal("show");
                  }

                  if (response2.data_status == 'modalLarge') {
                    $("#modalLarge").modal("show");
                  }

                  if (response2.data_status == 'modalLarge2') {
                    $("#modalLarge2").modal("show");
                  }

                  if (response2.data_status == 'bkerror') {
                    $("#bkerror").modal("show");
                  }

                  if (response2.data_status == 'errorProfile2') {
                    $("#errorProfile2").modal("show");
                  }

                  // if (response2.data_status == 'modalLarge3') {
                  //   $("#modalLarge3").modal("show");
                  // }else if(response2.data_status == 'modalLarge2'){
                  //   $("#modalLarge2").modal("show");

                  // }else if(response2.data_status == 'modalLarge'){
                  //   $("#modalLarge2").modal("show");
                    
                  // }else if(response2.data_status == 'bkerror'){
                  //   $("#bkerror").modal("show");

                  // }else if(response2.data_status == 'errorProfile2'){
                  //   $("#errorProfile2").modal("show");

                  // } else{
                  //   window.location.reload();
                  // }
                }
              });
            } else {

              $("#signin-modal2").modal("hide");
              $("#modalLarge3").modal("show");       
            }


                                         
          },
          error: function(xhr, status, error) {
              console.error(xhr);
              // alert('Failed to login user. Please try again.');
              alert(xhr.responseText);
          }
      });
    }


    // Handle form submission for OTP validation
    $(document).on('submit', '#formSecondPopup2', function(event) {
        event.preventDefault();

        var otp = $('#inputSecondContent2').val();
        
        $.ajax({
            type: 'POST',
            url: '{{ route("validate-otp2") }}',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                otp: otp
            },
            success: function(response) {

              if (response.message == 'success') {
                $('#firstpopup2').html('<p>Thank you for your submission. OTP has been validated successfully.</p>');
                $.ajax({
                  type: 'POST',
                  url: '{{ route("login-user2") }}',
                  data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                  },
                  success: function(response) {
                    if (response.status === 'new_user') {
                      $('#firstpopup2').append('<form id="registerForm2" method="post"><div class="row mb-4"><div class="col-md-12"><label class="form-label mb-2">Enter Your Full Name</label><input class="form-control valid" type="text" name="name" id="inputThirdContent2" placeholder="Enter Name" required=""><br><span style="color:red;font-size:13px;" id="registrationNameerror"></span></div><div class="col-md-12"><label for="" class="form-label mb-2">Email (Optional)</label><input type="text" name="email" id="inputEmailContent" class="form-control inputEmailContent" placeholder="Enter Email"><span class="emailErrorCheck" style="color:red"></span></div><div class="col-md-12"><button class="btn btn-primary btn-lg w-100 mt-2 disablebtnemail" type="submit">Submit</button></div></div></form>');
                    } else {
                      loginUser2();
                    }
                  }

                });
              } else {
                alert("Otp not match! try different");
              }
               
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Failed to validate OTP. Please try again.');
            }
        });
    });

    // Submit the registration form for new user
    $(document).on('submit', '#registerForm2', function(event) {
        event.preventDefault();
        var name = $('#inputThirdContent2').val();
        var email = $('#inputEmailContent').val();
          if (name != '') {
            $('#registrationNameerror').text('');
            $.ajax({
              type: 'POST',
              url: '{{ route("registeruser2") }}',
              data: {
                  _token: $('meta[name="csrf-token"]').attr('content'),
                  name: name,
                  email: email
              },
              success: function(response) {
                  console.log(response);
                  loginUser2();
              },
              error: function(xhr, status, error) {
                  console.error(error);
                  alert('Failed to register user. Please try again.');
              }
          });            
        } else {
          $('#registrationNameerror').text('Please enter name...');
        }

    });

  });
</script>



  <script>
    $(document).ready(function(){
      $('#signupvalidation').validate({
        rules:{
          name:{
            required: true
          },
          email:{
            required: true,
            email: true,
            remote:{
              type: "POST",
              url: "{{ url('/check/signup/email') }}",
              data:{
                email: function(){
                  return $('#signup-email').val();
                },
                _token: "{{ csrf_token() }}"
              }
            }
          },
          password:{
            required: true,
            minlength: 8
          },
          confirm_password:{
            required: true,
            minlength: 8,
            equalTo: '#signup-password'
          },
          agree: "required"
        },
        messages:{
          name:{
            required: "Please enter name"
          },
          email:{
            required: "Please enter email",
            email: "Please enter valid email",
            remote: "Email is already exists, Please login!"
          },
          password:{
            required: "Please enter password",
            minlength: "please enter minimum 8 character"
          },
          confirm_password:{
            required: "Please enter password",
            minlength: "please enter minimum 8 character",
            equalTo: "Password Should be same as above"
          },
          agree: "Please accept our terms and condition"
        },
        errorElement: "strong",
        errorPlacement: function(error, element){
          error.addClass( "invalid-feedbackNew" );

          if ( element.prop( "type" ) === "checkbox" ) {
						error.insertAfter( element.next( "label" ) );
					} else {
						error.insertAfter( element );
					}

        },
        // highlight: function ( element, errorClass, validClass ) {
				// 	$( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
				// },
				// unhighlight: function (element, errorClass, validClass) {
				// 	$( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
				// }
      });
    });
  </script>

  <script>
    $(document).ready(function(){
      $('#signinValidation').validate({
        rules:{
          email:{
            required: true,
            email: true,
            remote:{
              type: "POST",
              url: "{{ url('/check/login/email') }}",
              data:{
                email: function(){
                  return $('#signin-email').val();
                },
                _token: "{{ csrf_token() }}"
              }
            }
          },
          password:{
            required: true,
            minlength: 8,
            remote:{
              type: "POST",
              url: "{{ url('/check/login/password') }}",
              data:{
                email: function(){
                  return $('#signin-email').val();
                },
                password: function(){
                  return $('#signin-password').val(); 
                },
                _token: "{{ csrf_token() }}"
              }
            }
          },
        },
        messages:{
          email:{
            required: "Please enter email",
            email: "Please enter valid email",
            remote: "Email is not found our record,Please signup!"
          },
          password:{
            required: "Please enter password",
            minlength: "please enter minimum 8 character",
            remote: "The password you entered is incorrect. please try again"
          },
        },
        errorElement: "strong",
        errorPlacement: function(error, element){
          error.addClass( "invalid-feedbackNew" );

          if ( element.prop( "type" ) === "checkbox" ) {
						error.insertAfter( element.next( "label" ) );
					} else {
						error.insertAfter( element );
					}

        },
        // highlight: function ( element, errorClass, validClass ) {
				// 	$( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
				// },
				// unhighlight: function (element, errorClass, validClass) {
				// 	$( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
				// }
      });
    });
  </script>

  @yield('page-script')

  {{-- @if(session()->has('message'))
    <div class="alert alert-success">
      {{ session()->get('message') }}
    </div>
  @endif --}}


  <script>
    $(document).ready(function() {
      toastr.options.timeOut = 20000;
      @if (Session::has('errorMsg'))
        toastr.error('{{ Session::get('errorMsg') }}');
      @elseif(Session::has('message'))
        toastr.success('{{ Session::get('message') }}');
      @endif
    });
  </script>

  <script>
    $(document).ready(function(){
      $('#arabiclanguage').on('click',function(){
        var url = "{{ url('/') }}";
        var current_url = window.location.pathname;
        // var new_url = 'ar'+current_url;

        if (current_url == '/') {
          var new_url = 'ar';  
        } else {
          var new_url = 'ar'+current_url;  
        }

        window.location = url+'/'+new_url;

      });
    });
  </script>

<script>
    $(document).ready(function(){
      $('#arabiclanguage2').on('click',function(){
        var url = "{{ url('/') }}";
        var current_url = window.location.pathname;
        // var new_url = 'ar'+current_url;

        if (current_url == '/') {
          var new_url = 'ar';  
        } else {
          var new_url = 'ar'+current_url;  
        }

        window.location = url+'/'+new_url;

      });
    });
  </script>
  <script>
    
    $(document).ready(function(){
      $('#arabiclanguagem').on('click',function(){
        var url = "{{ url('/') }}";
        var current_url = window.location.pathname;
        // var new_url = 'ar'+current_url;

        if (current_url == '/') {
          var new_url = 'ar';  
        } else {
          var new_url = 'ar'+current_url;  
        }

        window.location = url+'/'+new_url;

      });
    });
</script>

<script>
 $(document).ready(function() {
    var mobile; // Define mobile variable outside the scope
    var countryCode; // define countrycode 25042024
    $('#signin-modal .modal-title').show();
    $('#signin-modal .modal-footer').show();

    // Function to send OTP
    function sendOTP(mobile, countryCode) {
      var finalCountryCode = countryCode.split(",");
        $.ajax({
            type: 'POST',
            url: '{{ route("generate-otp") }}',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                mobile: mobile,
                countryCode: countryCode 
            },
            success: function(response) {
                // Display HTML message with the mobile number and edit button
                $('#firstpopup').html('<p>Verification code sent to your WhatsApp</p>');
                var editMobileInput = '<div class="text-center">+'+finalCountryCode[1]+'' + mobile + '  <button id="editMobileBtn" class="btn btn-sm btn-outline-primary mt-2"><i class="fi-pencil"></i> Edit</button></div>';
                //var editMobileInput = '<span id="editMobileBtn" class="edit_style">' + mobile + ' <u>Edit</u></span>';
                $('#firstpopup').append(editMobileInput);
                $('#firstpopup').append('<form id="formSecondPopup" method="post"><div class="mb-4"><label class="form-label mb-2 label-colour" for="signin-email">Enter the 4-digit code</label><input class="form-control valid" type="text" name="otp" id="inputSecondContent" placeholder="Enter OTP" required=""><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">Login</button></form>');
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Failed to generate OTP. Please try again later.');
            }
        });
    }

    // Handle form submission to send OTP
    $('#signinValidation').submit(function(event) {
        event.preventDefault();
        mobile = $('#signin-mobile').val(); // Assign value to the global mobile variable
        // var countryCode = $('#country-select').val();
        countryCode = $('#country-select').val();
        sendOTP(mobile, countryCode);
    });

    // Handle edit button click event
    $(document).on('click', '#editMobileBtn', function() {
        // Display input field to edit mobile number
        var countryFinalCode = countryCode.split(",");
        var editMobileInput = '<label class="form-label mb-2" for="signin-email">Please Enter Your Number (Whatsapp)</label><div class="input-group mb-3"><select name="country_code" class="form-select form-select-sm">@foreach ($countries as $country)<option value="{{ $country->id }},{{ $country->country_code }}" @if($country->country_code == "'+countryFinalCode[1]+'") selected @endif>{{ $country->name }}</option>@endforeach</select><input class="form-control" type="text" name="mobile" id="edited-mobile" placeholder="Enter Mobile No" value="' + mobile + '" required></div>';
    
        var googleroute = "{{ route('googleLogin') }}";
          var facebookroute = "{{ route('facbookLogin') }}";
        var editMobileInput = '<label class="form-label mb-2" for="signin-email">Please Enter Your Number (Whatsapp)</label><div class="input-group mb-3"><select name="country_code" class="form-select form-select-sm">@foreach ($countries as $country)<option value="{{ $country->id }},{{ $country->country_code }}">{{ $country->name }}</option>@endforeach</select><input class="form-control" type="text" name="mobile" id="edited-mobile" placeholder="Enter Mobile No" value="' + mobile + '" required></div>';
        editMobileInput +=  '<button id="resendOtpBtn" class="btn btn-primary mt-2 btn-lg w-100">Resend OTP</button>';
          editMobileInput += '<div class="d-flex align-items-center py-3 mb-3"><hr class="w-100"><div class="px-3">Or</div><hr class="w-100"></div>';
            editMobileInput += '<a class="btn btn-outline-info w-100 mb-3" href="'+googleroute+'"><i class="fi-google fs-lg me-1"></i>Sign in with Google</a><a class="btn btn-outline-info w-100 mb-3" href="'+facebookroute+'"><i class="fi-facebook fs-lg me-1"></i>Sign in with Facebook</a>';
          $('#firstpopup').html(editMobileInput);



        // Handle resend OTP button click event
        $(document).on('click', '#resendOtpBtn', function() {
            var editedMobile = $('#edited-mobile').val();
            var editcountryCode = $('select[name="country_code"]').val();
            sendOTP(editedMobile, editcountryCode);
        });
    });

    // Handle form submission for OTP validation
    $(document).on('submit', '#formSecondPopup', function(event) {
        event.preventDefault();

        var otp = $('#inputSecondContent').val();
        $.ajax({
            type: 'POST',
            url: '{{ route("validate-otp") }}',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                otp: otp
            },
            success: function(response) {
                $('#firstpopup').html('<p>Thank you for your submission. OTP has been validated successfully.</p>');
                $.ajax({
                    type: 'POST',
                    url: '{{ route("login-user") }}',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'new_user') {
                            $('#firstpopup').append('<form id="registerForm" method="post"><div class="mb-4"><label class="form-label mb-2">Enter Your Full Name</label><input class="form-control valid" type="text" name="name" id="inputThirdContent" placeholder="Enter Name" required=""><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">Submit</button></form>');
                        } else {
                            loginUser();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('Failed to login user. Please try again.');
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Failed to validate OTP. Please try again.');
            }
        });
    });

     // Function to handle user login
     function loginUser() {
        $.ajax({
            type: 'POST',
            url: '{{ route("login-user") }}',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Failed to login user. Please try again.');
            }
        });
    }

    // Submit the registration form for new user
    $(document).on('submit', '#registerForm', function(event) {
        event.preventDefault();
        var name = $('#inputThirdContent').val();
        $.ajax({
            type: 'POST',
            url: '{{ route("registeruser") }}',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                name: name
            },
            success: function(response) {
                console.log(response);
                loginUser();
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Failed to register user. Please try again.');
            }
        });
    });

});
</script>

<script>
  $(document).ready(function(){
    $('.telephonelogin').intlTelInput({
              
      // localizedCountries: true,
      onlyCountries: ["sa","in","qa","ae","kw"],
      preferredCountries: [ "sa","in"],
      separateDialCode: true,
      initialCountry: "",
      
    }).on('countrychange',function(e,countryData){
      $('.country_code_login').val(($(".telephonelogin").intlTelInput("getSelectedCountryData").dialCode))
    });
  });
</script>

<script>
  $(document).on('input','.inputEmailContent',function(e){
    var email = $(this).val();
    $.ajax({
      type: 'GET',
      url: '{{ url("user/check/email2") }}',
      data: {
        email: email
      },
      success: function(response) {
        if(response.EmailStatus == '0'){
          $('.disablebtnemail').attr('disabled',true);
          $('.emailErrorCheck').text('Email already exists');
        }else{
          $('.disablebtnemail').attr('disabled',false);
          $('.emailErrorCheck').text('');
        }
      },
    });
  });
</script>

<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');

    fbq('init', '{{ config("services.facebook_capi.pixel_id") }}');

    // Generate unique event ID
    var eventId = 'pageview_' + Date.now() + '_' + Math.random();

    // Track browser PageView
    fbq('track', 'PageView', {}, {
        eventID: eventId
    });

    // Send event to backend
    fetch("{{ route('admin.store.pageview') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            event_id: eventId,
            current_url: window.location.href,
            referrer: document.referrer
        })
    });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {

      if (typeof gsap === "undefined") return;

      gsap.registerPlugin(ScrollTrigger);

      // ==========================================
      // Animate ALL Sections (Fade + Slide Up)
      // ==========================================
      gsap.utils.toArray("section").forEach(section => {

          gsap.from(section, {
              y: 60,
              opacity: 0,
              duration: 1,
              ease: "power3.out",
              scrollTrigger: {
                  trigger: section,
                  start: "top 85%",
                  toggleActions: "play none none none"
              }
          });

      });

      // ==========================================
      // Animate All Cards
      // ==========================================
      gsap.utils.toArray(".card").forEach(card => {

          gsap.from(card, {
              y: 50,
              opacity: 0,
              duration: 0.8,
              ease: "power2.out",
              scrollTrigger: {
                  trigger: card,
                  start: "top 90%",
                  toggleActions: "play none none none"
              }
          });

      });

      // ==========================================
      // Animate Steps (Slide From Left)
      // ==========================================
      gsap.utils.toArray(".step").forEach(step => {

          gsap.from(step, {
              x: -50,
              opacity: 0,
              duration: 0.8,
              ease: "power2.out",
              scrollTrigger: {
                  trigger: step,
                  start: "top 90%",
                  toggleActions: "play none none none"
              }
          });

      });

      // ==========================================
      // Animate Gallery Items
      // ==========================================
      gsap.utils.toArray(".gallery .item").forEach(item => {

          gsap.from(item, {
              scale: 0.9,
              opacity: 0,
              duration: 0.6,
              ease: "power2.out",
              scrollTrigger: {
                  trigger: item,
                  start: "top 90%",
                  toggleActions: "play none none none"
              }
          });

      });

      // ==========================================
      // Subtle Image Parallax Zoom (Optional)
      // ==========================================
      gsap.utils.toArray(".box-thumb img").forEach(img => {

          gsap.to(img, {
              scale: 1.1,
              scrollTrigger: {
                  trigger: img,
                  scrub: true
              }
          });

      });

  });
</script>




</body>
</html>