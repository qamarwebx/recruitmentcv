<!DOCTYPE html>
<html lang="ar-sa">
<head>
  <title>@yield('title')</title>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <!-- SEO Meta Tags-->
  <meta name="description" content="Finder - Directory &amp; Listings Bootstrap Template">
  <meta name="keywords" content="bootstrap, business, directory, listings, e-commerce, car dealer, city guide, real estate, job board, user account, multipurpose, ui kit, html5, css3, javascript, gallery, slider, touch">
  <meta name="author" content="Createx Studio">
  <!-- Viewport-->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Favicon and Touch Icons-->
  @if($website['logo_ar'] != '')
    <link href="{{ $website['logo_ar'] }}" rel="icon" />
  @else
    <link href="{{ asset('user/img/favicon.png') }}" rel="icon" />
  @endif
  <link rel="stylesheet" href="{{ asset('user/css/arcustom.css') }}">
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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  {{-- <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet"> --}}
  {{-- toastr --}}
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('admin/assets/vendor/fonts/fontawesome.css') }}">
  <link rel="stylesheet" href="{{ asset('user/intl-tel-input-master/build/css/intlTelInput.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>
  <script src="https://unpkg.com/gsap@3/dist/gsap.min.js"></script>
  <script src="https://unpkg.com/gsap@3/dist/ScrollTrigger.min.js"></script>
  @yield('page-style')

  <style>
    .title-padding{
          padding-right: 26px;
        }
        .bottom-nav-item i{
      font-size: 20px !important;
    }

    /* For Samsung S8+ (360px width) */
    @media (max-width: 360px) {
        .website-logo {
            width: 115px;
        }
        .socialbtn {
          font-size: 10px;
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

</head>
<!-- Body-->

<body>
  <main class="page-wrapper">

    <!-- Sign In Modal start -->
    <div class="modal fade" id="signin-modal2" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg modal-dialog-centered p-2 my-0 mx-auto" style="max-width: 500px;">
        <div class="modal-content">
          <div class="modal-header ardir">
            <button class="btn-close position-absolute top-0 start-0 mt-3 ms-3 closeSignModal" type="button" data-bs-dismiss="modal"></button>
            <h5 class="modal-title title-padding">تسجيل الدخول لصاحب العمل</h5>
          </div>
          <div class="modal-body px-0 py-2 py-sm-0" id="signinbody2">
            <div class="row mx-0 align-items-center">
              <div class="px-4 pb-4 px-sm-5 pb-sm-4 pt-4" id="firstpopup2">
                <form  id="signinValidation2" method="POST">
                  @csrf
                  <div class="mb-4">
                    <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}" id="current_page_login2">
                    <label class="form-label mb-2 float-end" for="signin-email">الرجاء إدخال رقم الجوال (واتس اب)</label>
                    
                    <div class="input-group">
                      {{-- <select name="countryCode" id="country-select2"  class="form-select form-select-sm">
                        @foreach (\App\Models\Otpcountrycode::orderBy('id','asc')->get() as $country)
                          <option value="{{ $country->id }},{{ $country->country_code }}">{{ $country->name }}</option>
                        @endforeach
                      </select> --}}
                      <input type="hidden" name="phone_code" class="country_code_login">
                      <input class="form-control telephonelogin" type="text" name="mobile" id="signin-mobile2" placeholder="55xxxxxxx">
                      
                    </div>
                    <span style="color: red;font-size:12px;" id="errorOTPM"></span>
                    @error('mobile')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                  <button class="btn btn-primary btn-lg w-100" type="submit">المتابعة</button>
                  <div class="d-flex align-items-center py-3 mb-3">
                    <hr class="w-100">
                    <div class="px-3">أو</div>
                    <hr class="w-100">
                  </div>
                  <a class="btn btn-outline-info w-100 mb-3 socialbtn" href="{{ route('googleLogin') }}"><i class="fi-google fs-lg me-1"></i>الدخول مع جوجل</a>
                  <a class="btn btn-outline-info w-100 mb-3 socialbtn" href="{{ route('facbookLogin') }}"><i class="fi-facebook fs-lg me-1"></i>قم بتسجيل الدخول باستخدام الفيسبوك</a>
                  <!-- <div class="mt-4 ardir">ليس لديك حساب؟ <a href="#signup-modal" data-bs-toggle="modal" data-bs-dismiss="modal">سجل هنا</a></div> -->
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
          <div class="modal-header ardir">
            
            <button class="btn-close position-absolute top-0 start-0 mt-3 ms-3" type="button" data-bs-dismiss="modal"></button>
            <h5 class="modal-title title-padding">تسجيل الدخول لصاحب العمل</h5>
          </div>
          <div class="modal-body px-0 py-2 py-sm-0" id="signinbody">
            {{-- <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"></button> --}}
            <div class="row mx-0 align-items-center">
              <div class="px-4 pb-4 px-sm-5 pb-sm-4 pt-4" id="firstpopup">
                <form  id="signinValidation" method="POST">
                  @csrf
                  <div class="mb-4">
                      <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}" id="current_page_login">
                      <label class="form-label mb-2 float-end" for="signin-email">الرجاء إدخال رقم الجوال (واتس اب)</label>
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
                  <button class="btn btn-primary btn-lg w-100" type="submit">المتابعة</button>
                  <div class="d-flex align-items-center py-3 mb-3">
                    <hr class="w-100">
                    <div class="px-3">أو</div>
                    <hr class="w-100">
                  </div>
                  <a class="btn btn-outline-info w-100 mb-3" href="{{ route('googleLogin') }}"><i class="fi-google fs-lg me-1"></i>الدخول مع جوجل</a>
                  <a class="btn btn-outline-info w-100 mb-3" href="{{ route('facbookLogin') }}"><i class="fi-facebook fs-lg me-1"></i>قم بتسجيل الدخول باستخدام الفيسبوك</a>
                  <!-- <div class="mt-4 ardir">ليس لديك حساب؟ <a href="#signup-modal" data-bs-toggle="modal" data-bs-dismiss="modal">سجل هنا</a></div> -->

                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Sign Up Modal-->
    <div class="modal fade ardir" id="signup-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
                    <label class="form-label" for="signup-name">الاسم الكامل</label>
                    <input class="form-control" type="text" name="name" id="signup-name" placeholder="أدخل اسمك الكامل">
                  </div>
                  <div class="mb-4">
                    <label class="form-label" for="signup-email">عنوان البريد الإلكتروني</label>
                    <input class="form-control" type="email" name="email" id="signup-email" placeholder="أدخل بريدك الإلكتروني">
                  </div>
                  <div class="mb-4">
                    <label class="form-label" for="signup-password">كلمة المرور <span class='fs-sm text-muted'>ثمانية أحرف على الأقل</span></label>
                    <div class="password-toggle">
                      <input class="form-control" type="password" name="password" id="signup-password" minlength="8">
                      <label class="password-toggle-btn" aria-label="Show/hide password">
                        <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                      </label>
                    </div>
                  </div>
                  <div class="mb-4">
                    <label class="form-label" for="signup-password-confirm">تأكيد كلمة المرور</label>
                    <div class="password-toggle">
                      <input class="form-control" type="password" name="confirm_password" id="signup-password-confirm" minlength="8">
                      <label class="password-toggle-btn" aria-label="Show/hide password">
                        <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                      </label>
                    </div>
                  </div>
                  <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="agree" id="agree-to-terms">
                    <label class="form-check-label" for="agree-to-terms">من خلال الانضمام ، أوافق على <a href='#'>شروط الاستخدام</a> و <a href='#'>سياسة الخصوصية</a></label>
                  </div>
                  <button class="btn btn-primary btn-lg w-100" type="submit">اشتراك </button>
                  <div class="mt-4">هل لديك حساب؟ <a class="myorderclosebtn" href="#signin-modal2" data-bs-toggle="modal" data-id="0" data-bs-dismiss="modal">تسجيل الدخول هنا</a></div>
                  <div class="d-flex align-items-center py-3 mb-3">
                    <hr class="w-100">
                    <div class="px-3">أو</div>
                    <hr class="w-100">
                  </div>
                  <a class="btn btn-outline-info w-100 mb-3" href="{{ route('googleLogin') }}"><i class="fi-google fs-lg me-1"></i>الدخول مع جوجل</a>
                  <a class="btn btn-outline-info w-100 mb-3" href="{{ route('facbookLogin') }}"><i class="fi-facebook fs-lg me-1"></i>قم بتسجيل الدخول باستخدام الفيسبوك</a>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Forgot Password Modal Start -->
    
    <div class="modal fade ardir" id="forgot-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
      <div class="modal-dialog modal-lg modal-dialog-centered p-2 my-0 mx-auto" style="max-width: 500px;">
        <div class="modal-content">
          <div class="modal-body px-0 py-2 py-sm-0">
            <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"></button>
            <div class="row mx-0 align-items-center">
              <div class="px-4 pb-4 px-sm-5 pb-sm-5 pt-5">
                <form id="forgotpassvalidation" action="{{ route('password.forget.email') }}" method="POST">
                  @csrf
                  <div class="mb-4">
                    <label class="form-label" for="signup-email">عنوان البريد الإلكتروني</label>
                    <input class="form-control" type="email" name="email" id="forgot-email" placeholder="أدخل بريدك الإلكتروني">
                  </div>
                  <button class="btn btn-primary btn-lg w-100" type="submit">إرسال رابط إعادة تعيين كلمة المرور </button>
                
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
            <h2 class="h5 mb-0 cv-head">حالة طلبك</h2>
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
    <header class="navbar navbar-expand-lg navbar-light bg-light fixed-top ardir headernav" data-scroll-header>
      <div class="container">
        <a class="navbar-brand me-3 me-xl-4" href="{{ route('ar.welcome') }}">
          @if($website['logo_ar'] != '')
            <img src="{{ $website['logo_ar'] }}" width="100">
          @else
            <x-brand-logo mode="light" lang="ar" class="d-block website-logo" width="150" alt="Qamr" />
          @endif

        </a>
        <!-- <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"><span
            class="navbar-toggler-icon"></span></button> -->

          
            <a class="langmob me-1" href="javascript:void(0)" id="englanguagem" style="font-family: 'Tajawal', sans-serif !important"><i class="fi-globe fs-base me-1"></i> English</a>

        @if (Auth::check())

          <a class="d-md-none me-2" data-bs-toggle="offcanvas" data-bs-target="#filters-top">  

            @if (Auth::user()->photo != '')
            <img class="rounded-circle px-1" src="{{ asset('user/img/avatars/'.Auth::user()->photo) }}" width="40" alt="Annette Black">              
            
            @elseif (Auth::user()->avatar_url != '')
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
                <img class="rounded-circle px-1" src="{{ Auth::user()->avatar_url }}" width="40"  alt="Annette Black">
                @else
                <img class="rounded-circle" src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" width="40" alt="Annette Black">                    
              @endif
            </a>
            <div class="dropdown-menu dropdown-menu-end">
              <div class="d-flex align-items-start border-bottom px-3 py-1 mb-2" style="width: 16rem;">
                
                @if (Auth::user()->photo != '')
                  <img class="rounded-circle" src="{{ asset('user/img/avatars/'.Auth::user()->photo) }}" width="48" alt="Annette Black">
                  @elseif(Auth::user()->avatar_url != '')
              <img class="rounded-circle px-1" src="{{ Auth::user()->avatar_url }}" width="40"  alt="Annette Black">
                @else
                  <img class="rounded-circle" src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" width="48" alt="Annette Black">
                @endif
                <div class="px-2">
                  <h6 class="fs-base mb-0">{{ Auth::user()->name }}</h6>
                  <div class="fs-xs py-2">@if(Auth::user()->mobile_no != '') {{ Auth::user()->country_code.Auth::user()->mobile_no }} <br> @endif{{ Auth::user()->email }}</div>
                </div>
              </div>
              <a class="dropdown-item" href="{{ route('ar.myorder') }}"><i class="fi-home opacity-60 me-2"></i> طلباتي </a>
              <a class="dropdown-item" href=""><i class="fi-heart opacity-60 me-2"></i> قائمة الرغبات</a>
              <a class="dropdown-item" href="{{ route('ar.myprofile') }}"><i class="fi-user opacity-60 me-2"></i> معلومات شخصية</a>
              <a class="dropdown-item" href=""><i class="fi-lock opacity-60 me-2"></i> كلمة المرور والأمان</a>
              <a class="dropdown-item" href=""><i class="fi-bell opacity-60 me-2"></i> إشعارات</a>
              <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}">
                  <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();"> خروج</a>
                </form>
            </div>
          </div>          
        @else
          <a class="btn btn-primary btn-xs ms-2 order-lg-3 specbtn myorderclosebtn" href="#signin-modal2" data-id="0" data-bs-toggle="modal"><i class="fa fa-sign-in me-2"></i> تسجيل الدخول</a>            
        @endif

        <div class="collapse navbar-collapse order-lg-2" id="navbarNav">
          <ul class="navbar-nav navbar-nav-scroll" style="max-height: 35rem;">
            <!-- Demos switcher-->
            <li class="nav-item dropdown {{ (request()->routeIs('ar.welcome')) ?'active': '' }}">
              <a class="nav-link " href="{{ route('ar.welcome') }}"> <i class="fi-home fs-base me-2"></i> منزل <span class="d-none d-lg-block position-absolute top-50 end-0 translate-middle-y border-end" style="width: 1px; height: 30px;"></span></a>
            </li>
            <!-- Menu items-->
            <li class="nav-item dropdown {{ (request()->routeIs('ar.resumes')) || (request()->routeIs('ar.fullresume')) ?'active':'' }} me-lg-2">
              <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i> كل السيرة الذاتية</a>
              {{-- <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i> توظيف الآن</a> --}}
            </li>
            {{-- <li class="nav-item dropdown {{ (request()->routeIs('resumes')) || (request()->routeIs('fullresume')) ?'active':'' }} me-lg-2"><a class="nav-link align-items-center pe-lg-4" href="{{ route('resumes') }}"
                role="button" aria-expanded="false"><i class="fi-file me-2"></i>CV Available</a>
            </li> --}}
            <li class="nav-item dropdown {{ (request()->routeIs('ar.myorder')) ?'active':'' }} me-lg-2">
              @if (Auth::check())
                <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.myorder') }}"><i class="fi-cart me-2"></i> طلباتي</a>
              @else
                <a class="nav-link align-items-center pe-lg-4 myorderclosebtn" href="#signin-modal2" data-id="0" data-bs-toggle="modal" role="button" aria-expanded="false" ><i class="fi-cart me-2"></i>طلباتي</a>
              @endif
            </li>

            <li class="nav-item dropdown {{ (request()->routeIs('ar.about')) ?'active':'' }}">
                <a class="nav-link" href="{{ route('ar.about') }}"><i class="fi-info-circle fs-base me-2"></i> معلومات عنا </a>
            </li>
            {{-- <li class="nav-item dropdown {{ (request()->routeIs('service')) ?'active':'' }} me-lg-2"><a class="nav-link align-items-center pe-lg-4" href="{{ route('service') }}"
                role="button" aria-expanded="false"><i class="fi-layers me-2"></i>Services</a>
            </li> --}}
            <li class="nav-item dropdown {{ (request()->routeIs('ar.contact')) ?'active':'' }}">
              <a class="nav-link" href="{{ route('ar.contact') }}"><i class="fi-phone fs-base me-2"></i> اتصال </a>
            </li>
            <li class="nav-item dropown">
              <a class="nav-link" href="javascript:void(0)" id="englanguage"><i class="fi-globe fs-base me-2"></i> English</a>
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
            <h3 class="h4 modal-title mt-4 text-center">الرجاء إدخال التفاصيل الخاصة بك</h3>
            <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>
          <div class="modal-body px-sm-5 px-4">
            <form class="needs-validation" novalidate>
              <div class="pt-2 mb-3">
                <label class="form-label fw-bold mb-2" for="property-address">اسم</label>
                <input class="form-control" type="text" id="property-address" placeholder="أدخل أسمك" required>
              </div>
              <div class="pt-2 mb-4">
                <label class="form-label fw-bold mb-2" for="property-area">بريد إلكتروني</label>
                <input class="form-control" type="text" id="property-area" placeholder="أدخل بريدك الإلكتروني" required>
                <div class="invalid-feedback">الرجاء إدخال التفاصيل الخاصة بك</div>
              </div>
              <div class="pt-2 mb-4">
                <label class="form-label fw-bold mb-2" for="property-area">رقم الاتصال</label>
                <input class="form-control" type="text" id="property-area" placeholder="أدخل رقم الاتصال الخاص بك"
                  required>
                <div class="invalid-feedback">الرجاء إدخال التفاصيل الخاصة بك</div>
              </div><a href="#"></a>
              <button class="btn btn-primary d-block w-100 mb-4" type="submit">إرسال والتالي</button>
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
          <a href="index.html" class=" d-flex align-items-center pb-1">
                @if($website['logo_ar'] != '')
                  <img src="{{ $website['logo_ar'] }}" width="100">
                @else
                  <x-brand-logo mode="light" lang="ar" alt="" />
                @endif
             

            </a>
            <p>تقدم @if(!empty($website['company_name'])) {{ $website['company_name_ar'] }} @else QAMR International @endif حلولاً كاملة لإدارة الموارد البشرية وتعالج مواهبها الحيوية من خلال توفير عملية توظيف شاملة.</p>
            <div class="social-links mt-3">
              <a @if(isset($frontwebsite) && $frontwebsite->bottom_contact_us_twitter_link != '') href="{{ $frontwebsite->bottom_contact_us_twitter_link }}" @else href="https://twitter.com/qamrintl" @endif class="twitter" target="_blank"><i class="fi-twitter"></i></a>
              <a @if(isset($frontwebsite) && $frontwebsite->bottom_contact_us_fb_link != '') href="{{ $frontwebsite->bottom_contact_us_fb_link }}" @else href="https://www.facebook.com/qamrintl" @endif class="facebook" target="_blank"><i class="fi-facebook"></i></a>
              <a @if(isset($frontwebsite) && $frontwebsite->bottom_contact_us_instagram_link != '') href="{{ $frontwebsite->bottom_contact_us_instagram_link }}" @else href="https://www.instagram.com/qamrintl/" @endif class="instagram" target="_blank"><i class="fi-instagram"></i></a>
              <a @if(isset($frontwebsite) && $frontwebsite->bottom_contact_us_linkedin_link != '') href="{{ $frontwebsite->bottom_contact_us_linkedin_link }}" @else href="https://www.linkedin.com/company/13375661/admin/" @endif class="linkedin" target="_blank"><i class="fi-linkedin"></i></a>
            </div>
          </div>

          <div class="col-lg-2 offset-lg-1 col-6 footer-links">
            <h4>روابط مفيدة</h4>
            <ul>
              <li><i class="fi-chevron-right"></i> <a href="{{ route('ar.welcome') }}">منزل</a></li>
              <li><i class="fi-chevron-right"></i> <a href="{{ route('ar.about') }}">معلومات عنا</a></li>
              <li><i class="fi-chevron-right"></i> <a href="{{ route('ar.service') }}">خدمات</a></li>
              <li><i class="fi-chevron-right"></i> <a href="">فريقنا</a></li>
              <li><i class="fi-chevron-right"></i> <a href="{{ route('ar.contact') }}">اتصل بنا</a></li>
            </ul>
          </div>

          <div class="col-lg-2 col-6 footer-links">
            <h4>خدماتنا</h4>
            <ul>
              <li><i class="fi-chevron-right"></i> <a href="#">المصادر</a></li>
              <li><i class="fi-chevron-right"></i> <a href="#">القائمة المختصرة</a></li>
              <li><i class="fi-chevron-right"></i> <a href="#">المقابلات</a></li>
              <li><i class="fi-chevron-right"></i> <a href="#">اختيار</a></li>
              <li><i class="fi-chevron-right"></i> <a href="#">تعيين</a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
            <h4>اتصل بنا</h4>
            <p>

              {!! !empty($website['company_address_ar']) 
                  ? nl2br(e($website['company_address_ar'])) 
                  : 'نسيم مانشن، شارنول،<br>دونغري، مومباي 400009<br>الهند' 
              !!}

              <br><br>

              <strong>هاتف:</strong>
              <span dir="ltr" style="display:inline-block;">
                  {{ !empty($website['company_mobile']) ? $website['company_mobile'] : '+919004006272' }}
              </span>

              <br>

              <strong>بريد إلكتروني:</strong>
              <span dir="ltr" style="display:inline-block;">
                  {{ !empty($website['company_email']) ? $website['company_email'] : 'hr@qamrintl.com' }}
              </span>

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
                    <button class="btn btn-success"><i class="fi-whatsapp"></i>واتس اب</button>
                    {{-- <a href="https://wa.me/message/AL4R37LTO6JMP1" target="_blank">
                      <button type="button" class="btn btn-success"><i class="fi-whatsapp"></i>واتس اب</a></button> --}}
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
        &copy; حقوق النشر <?= date('Y') ?> <strong><span>Qamr International</span></strong>. كل الحقوق محفوظة
      </div>
      <div class="credits">
      </div>
    </div>
  </footer><!-- End Footer -->

  {{-- <button class="btn btn-light btn-sm w-100 d-lg-none rounded-0 fixed-bottom text-dark fix" type="button">
    <i class="fi-cart py-2 me-2" style="font-size: 18px;">
      @if (Auth::check())
        <a class="bottom-menu" href="{{ route('ar.myorder') }}">
          <p class="my-0 pt-1" style="font-weight: 600; color: #000; font-size: 14px;">طلباتي</p>
        </a>    
      @else
        <a class="bottom-menu" href="#signin-modal" data-bs-toggle="modal" role="button" aria-expanded="false">
          <p class="my-0 pt-1" style="font-weight: 600; color: #000; font-size: 14px;">طلباتي</p>
        </a>
      @endif
    </i>

    <i class="fi-file py-2 mx-3" style="font-size: 18px;">
      <a class="bottom-menu" href="{{ route('ar.resumes') }}">
        <p class="my-0 pt-1" style="font-weight: 600; color: #000; font-size: 14px;">توظيف الآن</p>
      </a>
    </i>

    @if (Route::is('ar.about'))
    
    <i class="fi-home py-2 mx-3" style="font-size: 18px;">
      <a class="bottom-menu" href="{{ route('ar.welcome') }}">
        <p class="my-0 pt-1 active" style="font-weight: 600; color: #000; font-size: 14px;">منزل</p>
      </a>
    </i>
    @else
    <i class="fi-info-circle py-2 mx-3" style="font-size: 18px;">
      <a class="bottom-menu" href="{{ route('ar.about') }}">
        <p class="my-0 pt-1 active" style="font-weight: 600; color: #000; font-size: 14px;">معلومات عنا</p>
      </a>
    </i>     
    @endif



    <i class="fi-align-justify py-2 mx-3 me-1" style="font-size: 18px;">
      <a class="bottom-menu" data-bs-toggle="offcanvas" data-bs-target="#filters-top">
        <p class="my-0 pt-1" style="font-weight: 600; color: #000; font-size: 14px;">قائمة طعام</p>
      </a>
    </i>
  </button> --}}

  <!-- Arabic Mobile menu new start -->
  <div class="bottom-nav d-lg-none bottom-nav-fixed w-100">
    {{-- @if (Auth::check())
      <div class="bottom-nav-item">
        <a href="{{ route('ar.myorder') }}">
          <i class="fi-cart"></i>
          <p>طلباتي</p>
        </a>
      </div>    
    @else
      <div class="bottom-nav-item">
        <a href="#signin-modal" data-bs-toggle="modal" role="button" aria-expanded="false">
          <i class="fi-cart"></i>
          <p>طلباتي</p>
        </a>
      </div>
    @endif --}}
    <div class="bottom-nav-item">
      <a href="{{ route('ar.welcome') }}">
        <i class="fi-home"></i>
        <p>منزل</p>
      </a>
    </div>

    <div class="bottom-nav-item">
      @if (Auth::check())
        <a href="{{ route('ar.myorder') }}">
          <i class="fi-cart"></i>
          <p>طلباتي</p>
        </a>          
      @else
        <a href="#signin-modal2" class="myorderclosebtn" data-bs-toggle="modal" data-id="0" role="button" aria-expanded="false">
          <i class="fi-cart"></i>
          <p>طلباتي</p>
        </a>
      @endif

    </div>

    <div class="bottom-nav-item">
      <a href="{{ route('ar.resumes') }}">
        <i class="fi-file"></i>
        <p>كل السيرة الذاتية</p>
        {{-- <p>توظيف الآن</p> --}}
      </a>
    </div>
    {{-- @if (Route::is('ar.about'))
      <div class="bottom-nav-item">
        <a href="{{ route('ar.welcom') }}">
          <i class="fi-home"></i>
          <p>منزل</p>
        </a>
      </div>    
    @else
      <div class="bottom-nav-item">
        <a href="{{ route('ar.about') }}">
          <i class="fi-info-circle"></i>
          <p>معلومات عنا</p>
        </a>
      </div>        
    @endif --}}

    <div class="bottom-nav-item">
      <a href="#" data-bs-toggle="offcanvas" data-bs-target="#filters-top">
        <i class="fi-align-justify"></i>
        <p>قائمة طعام</p>
      </a>
    </div>
  </div>
  <!-- Arabic Mobile menu new End -->

  <aside class="d-md-none">
    <div class="offcanvas offcanvas-end offcanvas-collapse" id="filters-sidebar">
      <div class="offcanvas-header d-flex d-lg-none align-items-center">
        <h2 class="h5 mb-0 cv-head">القائمة الرئيسية</h2>
        <button class="btn-close close-main-menu-model" type="button" data-bs-dismiss="offcanvas"></button>
      </div>

      <div class="offcanvas-body py-lg-4">
        <ul class="navbar-nav navbar-nav-scroll" style="max-height: 35rem;">
          <!-- Demos switcher-->
          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link " href="{{ route('ar.welcome') }}"> <i class="fi-home fs-base me-2"></i> منزل <span class="d-none d-lg-block position-absolute top-50 end-0 translate-middle-y border-end" style="width: 1px; height: 30px;"></span></a>
          </li>
          <!-- Menu items-->

          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i>كل السيرة الذاتية</a>
            {{-- <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i>توظيف الآن</a> --}}
          </li>

          {{-- <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link align-items-center pe-lg-4" href="{{ route('resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i>CV Available</a>
          </li> --}}


          <li class="nav-item dropdown border-secondary border-2">
            @if (Auth::check())
              <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.myorder') }}"><i class="fi-cart me-2"></i>طلباتي</a>
            @else
              <a class="nav-link align-items-center pe-lg-4 myorderclosebtn" href="#signin-modal2" data-id="0" data-bs-toggle="modal" role="button" aria-expanded="false"><i class="fi-cart me-2"></i>طلباتي</a>
            @endif
          </li>

          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link" href="{{ route('ar.about') }}"><i class="fi-info-circle fs-base me-2"></i>معلومات عنا </a>
          </li>

          {{-- <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link align-items-center pe-lg-4" href="{{ route('service') }}" role="button" aria-expanded="false"><i class="fi-layers me-2"></i>Services</a>
          </li> --}}

          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link" href="{{ route('ar.contact') }}"> <i class="fi-phone fs-base me-2"></i> اتصال </a>
          </li>
          @if (Auth::check())
              <li class="nav-item dropdown border-secondary border-2">
                <a class="nav-link" href="{{ route('ar.myprofile') }}"><i class="fi-user fs-base me-2"></i> معلومات شخصية</a>
              </li>
          @endif
          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link" href="javascript:void(0)" id="englanguage2"><i class="fi-globe fs-base me-2"></i> English</a>
          </li>
        </ul>
      </div>
    </div>
  </aside>

  <aside class="col-lg-4 col-xl-3 shadow-sm px-3 px-xl-4 px-xxl-5 pt-lg-2 d-md-none">
    <div class="offcanvas offcanvas-end offcanvas-collapse" id="filters-top">
        <div class="offcanvas-header d-flex d-lg-none align-items-center">
            <h2 class="h5 mb-0 cv-head">@if(Auth::check()) {{ Auth::user()->name }} @endif</h2>
            <button class="btn-close close-main-menu-model" type="button" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body py-lg-4">

            <a class="dropdown-item border-bottom py-3" href="{{ route('ar.welcome') }}">
                <i class="fi-home opacity-60 me-2"></i>منزل 
            </a>
            @if (Auth::check())
            <a class="dropdown-item border-bottom py-3" href="{{ route('ar.myorder') }}">
              <i class="fi-cart opacity-60 me-2"></i>طلبي
            </a>
            @else
              <a class="dropdown-item border-bottom py-3 myorderclosebtn" href="#signin-modal2" data-id="1" data-bs-toggle="modal" role="button" aria-expanded="false">
                <i class="fi-cart opacity-60 me-2"></i>طلبي
              </a>
            @endif

            <a class="dropdown-item border-bottom py-3" href="">
                <i class="fi-heart opacity-60 me-2"></i>قائمة المفضلة
            </a>

            @if (Auth::check())
            <a class="dropdown-item border-bottom py-3" href="{{ route('ar.myprofile') }}">
                <i class="fi-user opacity-60 me-2"></i>الملف الشخصي
            </a>
            @else
              <a class="dropdown-item border-bottom py-3 myorderclosebtn" href="#signin-modal2" data-id="1" data-bs-toggle="modal" role="button" aria-expanded="false">
                <i class="fi-user opacity-60 me-2"></i>الملف الشخصي
              </a>
            @endif

            <a class="dropdown-item border-bottom py-3" href="">
                <i class="fi-lock opacity-60 me-2"></i>كلمة المرور والأمان
            </a>

            <a class="dropdown-item border-bottom py-3" href="">
                <i class="fi-bell opacity-60 me-2"></i>الإشعارات
            </a>

            @if (Auth::check())

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}">

                <a class="dropdown-item border-bottom py-3"
                  href="{{ route('logout') }}"
                  onclick="event.preventDefault();this.closest('form').submit();">
                    <i class="fi-logout opacity-60 me-2"></i>تسجيل الخروج
                </a>
            </form>

            @else

            <a class="dropdown-item border-bottom py-3 myorderclosebtn"
              href="#signin-modal2"
              data-id="0"
              data-bs-toggle="modal">
                <i class="fa fa-sign-in opacity-60 me-2"></i>تسجيل الدخول
            </a>

            @endif

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
              required: "الرجاء إدخال البريد الإلكتروني",
              email: "الرجاء إدخال بريد إلكتروني صحيح",
              remote: "لم يتم العثور على البريد الإلكتروني ، الرجاء التسجيل!"
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
            required: "الرجاء إدخال الاسم"
          },
          email:{
            required: "الرجاء إدخال البريد الإلكتروني",
            email: "الرجاء إدخال بريد إلكتروني صحيح",
            remote: "البريد الإلكتروني موجود بالفعل ، الرجاء تسجيل الدخول!"
          },
          password:{
            required: "الرجاء إدخال كلمة المرور",
            minlength: "الرجاء إدخال ثمانية أحرف على الأقل"
          },
          confirm_password:{
            required: "الرجاء إدخال كلمة المرور",
            minlength: "الرجاء إدخال ثمانية أحرف على الأقل",
            equalTo: "يجب أن تكون كلمة المرور هي نفسها المذكورة أعلاه"
          },
          agree: "الرجاء قبول الشروط والأحكام الخاصة بنا"
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
            required: "الرجاء إدخال البريد الإلكتروني",
            email: "الرجاء إدخال بريد إلكتروني صحيح",
            remote: "البريد الإلكتروني غير موجود في السجل الخاص بك ، الرجاء التسجيل!"
          },
          password:{
            required: "الرجاء إدخال كلمة المرور",
            minlength: "الرجاء إدخال ثمانية أحرف على الأقل",
            remote: "كلمة المرور التي أدخلتها غير صحيحة. حاول مرة اخرى"
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

      $(document).on('click','#englanguage',function(e){
        e.preventDefault();
        var url = "{{ url('/') }}";
        var current_url = window.location.pathname;
        var newurl = current_url.replace('/ar',"");

        window.location = url+newurl;

      });

      // $('#englanguage').on('click',function(){
      //   var url = "{{ url('/') }}";
      //   var current_url = window.location.pathname;
      //   var newurl = current_url.replace('/ar',"");

      //   window.location = url+newurl;

      // });
    });
  </script>

  <script>
    $(document).ready(function(){
      $(document).on('click','#englanguage2',function(e){
        e.preventDefault();
        var url = "{{ url('/') }}";

        var current_url = window.location.pathname;
        var newurl = current_url.replace('/ar',"");

        window.location = url+newurl;
      });

      // $('#englanguage2').on('click',function(){
      //   var url = "{{ url('/') }}";

      //   var current_url = window.location.pathname;
      //   var newurl = current_url.replace('/ar',"");

      //   window.location = url+newurl;

      // });
    });
  </script>

<script>
  $(document).ready(function(){

      $('.myorderclosebtn').click(function(){
        $('.close-main-menu-model').trigger('click');
      });

    $(document).on('click','#englanguagem',function(e){
      e.preventDefault();
      var url = "{{ url('/') }}";

      var current_url = window.location.pathname;
      var newurl = current_url.replace('/ar',"");

      window.location = url+newurl;
    });

    // $('#englanguagem').on('click',function(){
    //   var url = "{{ url('/') }}";

    //   var current_url = window.location.pathname;
    //   var newurl = current_url.replace('/ar',"");

    //   window.location = url+newurl;

    // });
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

      function startOtpTimer() {
          var timeLeft = 60;

          $('#countdown').text(timeLeft);
          $('#otpTimer').show();
          $('#resendOtpWrapper').hide();

          var timerInterval = setInterval(function () {
              timeLeft--;

              $('#countdown').text(timeLeft);

              if (timeLeft <= 0) {
                  clearInterval(timerInterval);
                  $('#otpTimer').hide();
                  $('#resendOtpWrapper').show();
              }
          }, 1000);
      }

      $(document).on('click', '#resendOtpBtn', function () {


        let mobile_no = $('#hiddenmobile2').val();
        let phoneCode = $('#hiddenphonecode2S').val();

        sendOTP2(mobile_no,phoneCode);
        // alert(mobile_no +' '+phoneCode);
        // Call your resend AJAX here
        // console.log('Resend OTP clicked');

        // Restart timer after resend
        startOtpTimer();
      });

    // Send OTP
    function sendOTP2(mobile_no,phoneCode2){
      // var finalCountryCode = country_code.split(",");
      var finalCountryCode = phoneCode2;
      $.ajax({
        type: "POST",
        url: "{{ route('generate-otp2') }}",
        data: {
          _token: $('meta[name="csrf-token"]').attr('content'),
          mobile: mobile_no,
          country_code: phoneCode2
        },
        success: function(response){
          // console.log(response);
          if (response.message == 'responsesuccess') {
            $('#firstpopup2').html('<p>تم التحقق من رقم الجوال!</p>');
            $.ajax({
              type: 'POST',
              url: '{{ route("login-user2") }}',
              data: {
                _token: $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                if (response.status === 'new_user') {
                  $('#firstpopup2').append('<form id="registerForm2" method="post"><div class="mb-4"><label class="form-label mb-2">أدخل اسمك الكامل</label><input class="form-control valid" type="text" name="name" id="inputThirdContent2" placeholder="أدخل الاسم" required=""><br><span style="color:red;font-size:13px;" id="registrationNameerror"></span><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">يُقدِّم</button></form>');
                } else {
                  loginUser2();
                }
              }

            });
          }
          // else if (response.message == 'otpsend') {
          //   $('#firstpopup2').html('<p class="ardir">تم إرسال OTP إلى رقم الواتساب الخاص بك</p>');
          //   var editMobileInput = '<div class="text-center ardir">'+response.countryCode+'' + response.mobile + '+  <input type="hidden" id="hiddenmobile2" value="'+response.mobile+'"><input type="hidden" id="hiddenphonecode2" value="'+response.iso_code+'"><input type="hidden" id="hiddenphonecode2S" value="'+response.countryCode+'"><button id="editMobileBtn2" class="btn btn-sm btn-outline-primary mt-2"><i class="fi-pencil"></i> يحرر</button></div>';
          //   $('#firstpopup2').append(editMobileInput);
          //   $('#firstpopup2').append('<form id="formSecondPopup2" method="post" class="ardir"><div class="mb-4"><label class="form-label mb-2 label-colour" for="signin-email">أدخل الرمز المكون من 4 أرقام</label><input class="form-control valid" type="text" name="otp" id="inputSecondContent2" placeholder="أدخل كلمة المرور لمرة واحدة" required=""><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">تسجيل الدخول</button></form>');

          // }
          else if (response.message == 'otpsend') {

              $('#firstpopup2').html('<p class="ardir">تم إرسال OTP إلى رقم الواتساب الخاص بك</p>');

              var editMobileInput = `
                  <div class="text-center ardir">
                      +${response.countryCode}${response.mobile}
                      <input type="hidden" id="hiddenmobile2" value="${response.mobile}">
                      <input type="hidden" id="hiddenphonecode2" value="${response.iso_code}">
                      <input type="hidden" id="hiddenphonecode2S" value="${response.countryCode}">
                      <button id="editMobileBtn2" class="btn btn-sm btn-outline-primary mt-2">
                          <i class="fi-pencil"></i> يحرر
                      </button>
                  </div>
              `;

              $('#firstpopup2').append(editMobileInput);

              var otpForm = `
                  <form id="formSecondPopup2" method="post" class="ardir">
                      <div class="mb-4">
                          <label class="form-label mb-2 label-colour">
                              أدخل الرمز المكون من 4 أرقام
                          </label>

                          <input class="form-control valid"
                                type="text"
                                name="otp"
                                id="inputSecondContent2"
                                placeholder="أدخل كلمة المرور لمرة واحدة"
                                required>

                          <!-- Timer -->
                          <div id="otpTimer" class="text-center mt-2 text-muted">
                              يمكنك إعادة الإرسال خلال <span id="countdown">60</span> ثانية
                          </div>

                          <!-- Resend -->
                          <div id="resendOtpWrapper" class="text-center mt-2" style="display:none;">
                              <a href="javascript:void(0);" id="resendOtpBtn">
                                  إعادة إرسال OTP
                              </a>
                          </div>

                          <button class="btn btn-primary btn-lg w-100 mt-2" type="submit">
                              تسجيل الدخول
                          </button>
                      </div>
                  </form>
              `;

              $('#firstpopup2').append(otpForm);

              startOtpTimer();
          }
          else if(response.message == 'responsefailed'){
            alert("لم يتم التحقق من رقم هاتفك المحمول، يرجى المحاولة بشكل مختلف!");
          }else if (response.message == 'otpnotsend') {
            alert("Your Mobile number is not verified,please try to different!");
          }else if (response.message == 'systemerror') {
            alert("بسبب خطأ فني، يرجى المحاولة بعد مرور بعض الوقت.")
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
        // var editMobileInput = '<label class="form-label mb-2 ardir" for="signin-email">الرجاء إدخال رقمك (واتس اب)</label><div class="input-group mb-3"><select name="country_code" class="form-select form-select-sm">@foreach ($countries as $country)<option value="{{ $country->id }},{{ $country->country_code }}">{{ $country->name }}</option>@endforeach</select><input class="form-control" type="text" name="mobile" id="edited-mobile" placeholder="أدخل رقم الجوال" value="' + recMobile + '"></div><span style="color:red;font-size:12px;" id="errorOTPM2"></span>';
        // var editMobileInput = '<div class="mb-4 float-end"><label class="form-label mb-2" for="signin-email">الرجاء إدخال رقمك (واتس اب)</label><div class="input-group mb-3"><input type="hidden" name="phone_code" class="phone_code_login2" value="'+phonecode2S5+'"><input class="form-control telephonelogin2" type="text" name="mobile" id="edited-mobile" placeholder="أدخل رقم الجوال" value="' + recMobile + '"></div><span style="color:red;font-size:12px;" id="errorOTPM2"></span></div>';
        var editMobileInput = '<label class="form-label mb-2 float-end" for="signin-email">الرجاء إدخال رقمك (واتس اب)</label><div class="input-group mb-3"><input type="hidden" name="phone_code" class="phone_code_login2" value="'+phonecode2S5+'"><input class="form-control telephonelogin2" type="text" name="mobile" id="edited-mobile" placeholder="أدخل رقم الجوال" value="' + recMobile + '"><span style="color:red;font-size:12px;" id="errorOTPM2"></span></div>';
        editMobileInput +=  '<button id="resendOtpBtn2" class="btn btn-primary mt-2 btn-lg w-100">إعادة إرسال كلمة المرور لمرة واحدة</button>';
        editMobileInput += '<div class="d-flex align-items-center py-3 mb-3"><hr class="w-100"><div class="px-3">Or</div><hr class="w-100"></div>';
        editMobileInput += '<a class="btn btn-outline-info w-100 mb-3" href="'+googleroute+'"><i class="fi-google fs-lg me-1"></i>الدخول مع جوجل</a><a class="btn btn-outline-info w-100 mb-3" href="'+facebookroute+'"><i class="fi-facebook fs-lg me-1"></i>قم بتسجيل الدخول باستخدام الفيسبوك</a>';
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
        
    });

    // Handle resend OTP button click event
    $(document).on('click', '#resendOtpBtn2', function() {
            var editedMobile = $('#edited-mobile').val();
            // var countryCode = $('select[name="country_code"]').val();
            var countryCode = $('.phone_code_login2').val();
            if (editedMobile == '') {
              $('#errorOTPM2').text('الرجاء إدخال رقم الجوال');
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
        var phoneCode2 = country_code;
      } else {
        var phoneCode2 = "966";        
      }

      if(mobile_no == ''){
        $('#errorOTPM').text('الرجاء إدخال رقم الجوال');
      }else{
        $('#errorOTPM').text('');
        sendOTP2(mobile_no,phoneCode2);
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
            // window.location.reload();         

            // console.log(response);  
            
             
             if (loginStatus == 1) {
               $("#signin-modal2").modal("hide");
               // Refresh Navbar and Content Area
               $('.headernav').load(" .headernav");
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
               window.location.reload();                
             }
            
          },
          error: function(xhr, status, error) {
              console.error(error);
              alert('Failed to login user. Please try again.');
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
                $('#firstpopup2').html('<p class="ardir">شكرا لتقريركم. لقد تم التحقق من صحة OTP بنجاح.</p>');
                $.ajax({
                  type: 'POST',
                  url: '{{ route("login-user2") }}',
                  data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                  },
                  success: function(response) {
                    if (response.status === 'new_user') {
                      $('#firstpopup2').append('<form id="registerForm2" method="post"><div class="row mb-4 ardir"><div class="col-md-12"><label class="form-label mb-2">أدخل اسمك الكامل</label><input class="form-control valid" type="text" name="name" id="inputThirdContent2" placeholder="أدخل الاسم" required=""><br><span style="color:red;font-size:13px;" id="registrationNameerror"></span></div><div class="col-md-12"><label for="" class="form-label mb2">بريد إلكتروني (خياري)</label><input type="text" name="email" id="inputEmailContent" placeholder="أدخل البريد الإلكتروني" class="form-control inputEmailContent"><span class="emailErrorCheck" style="color:red"></span></div><div class="col-md-12"><button class="btn btn-primary btn-lg w-100 mt-2 disablebtnemail" type="submit">يُقدِّم</button></div></div></form>');
                    } else {
                      loginUser2();
                    }
                  }

                });
              } else {
                alert("مكتب المدعي العام غير مطابق! حاول مختلفة");
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
          $('#registrationNameerror').text('الرجاء إدخال الاسم');
        }

    });

  });
</script>


<script>
 $(document).ready(function() {
    var mobile; 
    $('#signin-modal .modal-title').show();
    $('#signin-modal .modal-footer').show();
  
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
                $('#firstpopup').html('<p>تم إرسال كلمة المرور لمرة واحدة إلى رقم واتس اب الخاص بك</p>');
                var editMobileInput = '<div class="text-center">+'+finalCountryCode[1]+'' + mobile + '  <button id="editMobileBtn" class="btn btn-sm btn-outline-primary mt-2"><i class="fi-pencil"></i> يحرر</button></div>';
                //var editMobileInput = '<span id="editMobileBtn" class="edit_style">' + mobile + ' <u>Edit</u></span>';
                $('#firstpopup').append(editMobileInput);
                $('#firstpopup').append('<form id="formSecondPopup" method="post"><div class="mb-4"><label class="form-label mb-2" for="signin-email">أدخل الرمز المكون من 4 أرقام</label><input class="form-control valid" type="text" name="otp" id="inputSecondContent" placeholder="أدخل كلمة المرور لمرة واحدة" required=""><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">تسجيل الدخول</button></form>');
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
        var countryCode = $('#country-select').val();
        sendOTP(mobile, countryCode);
    });

    // Handle edit button click event
    $(document).on('click', '#editMobileBtn', function() {
        // Display input field to edit mobile number
          var googleroute = "{{ route('googleLogin') }}";
          var faceRoute = "{{ route('facbookLogin') }}";
       var editMobileInput = '<label class="form-label mb-2 float-end" for="signin-email">الرجاء إدخال رقمك (واتس اب)</label><div class="input-group mb-3"><select name="country_code" class="form-select form-select-sm">@foreach ($countries as $country)<option value="{{ $country->id }},{{ $country->country_code }}">{{ $country->name }}</option>@endforeach</select><input class="form-control" type="text" name="mobile" id="edited-mobile" placeholder="Enter Mobile No" value="' + mobile + '" required></div>';
          editMobileInput += '<button id="resendOtpBtn" class="btn btn-primary mt-2 btn-lg w-100">أعد إرسال الرمز</button>';
          editMobileInput += '<div class="d-flex align-items-center py-3 mb-3"><hr class="w-100"><div class="px-3">أو</div><hr class="w-100"></div>';
          editMobileInput += '<a class="btn btn-outline-info w-100 mb-3" href="'+googleroute+'"><i class="fi-google fs-lg me-1"></i>الدخول مع جوجل</a><a class="btn btn-outline-info w-100 mb-3" href="'+faceRoute+'"><i class="fi-facebook fs-lg me-1"></i>قم بتسجيل الدخول باستخدام الفيسبوك</a><div class="mt-4 ardir">ليس لديك حساب؟ <a href="#signup-modal" data-bs-toggle="modal" data-bs-dismiss="modal">سجل هنا</a></div>';

       $('#firstpopup').html(editMobileInput);

        // Handle resend OTP button click event
        $(document).on('click', '#resendOtpBtn', function() {
            var editedMobile = $('#edited-mobile').val();
            var countryCode = $('select[name="country_code"]').val();
            sendOTP(editedMobile, countryCode);
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
                $('#firstpopup').html('<p>شكرا لتقريركم. لقد تم التحقق من صحة OTP بنجاح.</p>');
                $.ajax({
                    type: 'POST',
                    url: '{{ route("login-user") }}',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'new_user') {
                            $('#firstpopup').append('<form id="registerForm" method="post"><div class="mb-4"><label class="form-label mb-2">أدخل اسمك الكامل</label><input class="form-control valid" type="text" name="name" id="inputThirdContent" placeholder="أدخل الاسم" required=""><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">يُقدِّم</button></form>');
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
            initialCountry: ""
        }).on('countrychange',function(e,countryData){
            $('.country_code_login').val(($(".telephonelogin").intlTelInput("getSelectedCountryData").dialCode))
        });
  });
</script>

{{-- <script>
  $(document).ready(function(){
    $('#signin-modal2').on('hidden.bs.modal', function () {
        $(this).find('.modal-body').html(''); 
        $(this).find('.modal-body').load(' #signinbody2');

    });
  });
</script> --}}

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
          $('.emailErrorCheck').text('البريد الالكتروني موجود بالفعل');
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