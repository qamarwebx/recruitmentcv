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
  <link href="{{ asset('user/img/favicon.png') }}" rel="icon" />
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
  @yield('page-style')

</head>
<!-- Body-->

<body>
  <main class="page-wrapper">
    <!-- Sign In Modal-->
    <div class="modal fade" id="signin-modal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered p-2 my-0 mx-auto" style="max-width: 500px;">
        <div class="modal-content">
          <div class="modal-body px-0 py-2 py-sm-0" id="signinbody">
            <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"></button>
            <div class="row mx-0 align-items-center">
              <div class="px-4 pb-4 px-sm-5 pb-sm-5 pt-5" id="firstpopup">
                <form  id="signinValidation" method="POST">
                  @csrf
                  <div class="mb-4">
                      <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}" id="current_page_login">
                      <label class="form-label mb-2" for="signin-email">يرجى إدخال رقم واتساب الخاص بك</label>
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
                  <button class="btn btn-primary btn-lg w-100" type="submit">إرسال رمز التحقق</button>
                  <div class="mt-4">ليس لديك حساب؟ <a href="#signup-modal" data-bs-toggle="modal" data-bs-dismiss="modal">سجل هنا</a></div>
                  <div class="d-flex align-items-center py-3 mb-3">
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Sign Up Modal-->
    <div class="modal fade ardir" id="signup-modal" tabindex="-1" aria-hidden="true">
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
                  <div class="mt-4">هل لديك حساب؟ <a href="#signin-modal" data-bs-toggle="modal" data-bs-dismiss="modal">تسجيل الدخول هنا</a></div>
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
    
    <div class="modal fade ardir" id="forgot-modal" tabindex="-1" aria-hidden="true">
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
    <header class="navbar navbar-expand-lg navbar-light bg-light fixed-top ardir" data-scroll-header>
      <div class="container">
        <a class="navbar-brand me-3 me-xl-4" href="{{ route('ar.welcome') }}">
          <img class="d-block" src="{{ asset('user/img/logo/logo.png') }}" width="120" alt="Qamr">
        </a>
        <!-- <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"><span
            class="navbar-toggler-icon"></span></button> -->

          
            <a class="langmob me-1" href="javascript:void(0)" id="englanguagem" style="font-family: 'Tajawal', sans-serif !important"><i class="fi-globe fs-base me-1"></i> English</a>

        @if (Auth::check())
          @php
            $uprofile = DB::table('userprofiles')->where('user_id','=',Auth::user()->id)->first();
            
          @endphp
          <a class="d-md-none me-2" data-bs-toggle="offcanvas" data-bs-target="#filters-top">  
            @if (isset($uprofile) && $uprofile->photo != '')
              <img class="rounded-circle px-1" src="{{ asset('user/img/avatars/'.$uprofile->photo) }}" width="40" alt="Annette Black">              
              @elseif(Auth::user()->avatar_url != '')
              <img class="rounded-circle px-1" src="{{ Auth::user()->avatar_url }}" width="40"  alt="Annette Black">
              
              @else
              <img class="rounded-circle px-1" src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" width="40"  alt="Annette Black">                
            @endif          
              
            </a>

          <div class="dropdown d-none d-lg-block order-lg-3 my-n2 me-3">
            <a class="d-block py-2" href="#">
              @if (isset($uprofile) && $uprofile->photo != '')
                <img class="rounded-circle" src="{{ asset('user/img/avatars/'.$uprofile->photo) }}" width="40" alt="Annette Black">
                @elseif(Auth::user()->avatar_url != '')
                <img class="rounded-circle px-1" src="{{ Auth::user()->avatar_url }}" width="40"  alt="Annette Black">
                @else
                <img class="rounded-circle" src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" width="40" alt="Annette Black">                    
              @endif
            </a>
            <div class="dropdown-menu dropdown-menu-end">
              <div class="d-flex align-items-start border-bottom px-3 py-1 mb-2" style="width: 16rem;">
                
                @if (isset($uprofile) && $uprofile->photo != '')
                  <img class="rounded-circle" src="{{ asset('user/img/avatars/'.$uprofile->photo) }}" width="48" alt="Annette Black">
                  @elseif(Auth::user()->avatar_url != '')
              <img class="rounded-circle px-1" src="{{ Auth::user()->avatar_url }}" width="40"  alt="Annette Black">
                @else
                  <img class="rounded-circle" src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" width="48" alt="Annette Black">
                @endif
                <div class="px-2">
                  <h6 class="fs-base mb-0">{{ Auth::user()->name }}</h6>
                  <div class="fs-xs py-2">@if(isset($uprofile) && $uprofile->mobile_no != '') {{ $uprofile->mobile_no }} <br> @endif{{ Auth::user()->email }}</div>
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
          <a class="btn btn-primary btn-xs ms-2 order-lg-3 specbtn" href="#signin-modal" data-bs-toggle="modal"><i class="fi-user me-2"></i> تسجيل الدخول</a>            
        @endif

        <div class="collapse navbar-collapse order-lg-2" id="navbarNav">
          <ul class="navbar-nav navbar-nav-scroll" style="max-height: 35rem;">
            <!-- Demos switcher-->
            <li class="nav-item dropdown {{ (request()->routeIs('ar.welcome')) ?'active': '' }}">
              <a class="nav-link " href="{{ route('ar.welcome') }}"> <i class="fi-home fs-base me-2"></i> منزل <span class="d-none d-lg-block position-absolute top-50 end-0 translate-middle-y border-end" style="width: 1px; height: 30px;"></span></a>
            </li>
            <!-- Menu items-->
            <li class="nav-item dropdown {{ (request()->routeIs('ar.resumes')) || (request()->routeIs('ar.fullresume')) ?'active':'' }} me-lg-2">
              <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i> السيرة الذاتية لجميع العاملين</a>
            </li>
            {{-- <li class="nav-item dropdown {{ (request()->routeIs('resumes')) || (request()->routeIs('fullresume')) ?'active':'' }} me-lg-2"><a class="nav-link align-items-center pe-lg-4" href="{{ route('resumes') }}"
                role="button" aria-expanded="false"><i class="fi-file me-2"></i>CV Available</a>
            </li> --}}
            <li class="nav-item dropdown {{ (request()->routeIs('ar.myorder')) ?'active':'' }} me-lg-2">
              @if (Auth::check())
                <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.myorder') }}"><i class="fi-cart me-2"></i> طلباتي</a>
              @else
                <a class="nav-link align-items-center pe-lg-4" href="#signin-modal" data-bs-toggle="modal" role="button" aria-expanded="false" ><i class="fi-cart me-2"></i>طلباتي</a>
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
    <div class="modal fade" id="form" tabindex="-1">
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
            <a href="index.html" class="logo d-flex align-items-center">
              <img src="{{ asset('user/img/logo/logo.png') }}" alt="">
            </a>
            <p>تقدم QAMR International حلولاً كاملة لإدارة الموارد البشرية وتعالج مواهبها الحيوية من خلال توفير عملية توظيف شاملة.</p>
            <div class="social-links mt-3">
              <a href="https://twitter.com/qamrintl" class="twitter" target="_blank"><i class="fi-twitter"></i></a>
              <a href="https://www.facebook.com/qamrintl" class="facebook" target="_blank"><i class="fi-facebook"></i></a>
              <a href="https://www.instagram.com/qamrintl/" class="instagram" target="_blank"><i class="fi-instagram"></i></a>
              <a href="https://www.linkedin.com/company/13375661/admin/" class="linkedin" target="_blank"><i class="fi-linkedin"></i></a>
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
              Naseem Mansion, Charnull,<br>
              Dongri, Mumbai, 400009<br>
              India <br><br>
              <strong> Phone:</strong> +919004006272 <br>
              <strong> Email:</strong> hr@qamrintl.com <br>
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
                    <a href="https://wa.me/message/AL4R37LTO6JMP1" target="_blank">
                      <button type="button" class="btn btn-success"><i class="fi-whatsapp"></i>واتس اب</a></button>
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
        <p class="my-0 pt-1" style="font-weight: 600; color: #000; font-size: 14px;">السيرة الذاتية لجميع العاملين</p>
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
      <a class="bottom-menu" data-bs-toggle="offcanvas" data-bs-target="#filters-sidebar">
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
      <a href="{{ route('ar.resumes') }}">
        <i class="fi-cart"></i>
        <p>طلباتي</p>
      </a>
    </div>

    <div class="bottom-nav-item">
      <a href="{{ route('ar.resumes') }}">
        <i class="fi-file"></i>
        <p>توظيف الآن</p>
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
      <a href="#" data-bs-toggle="offcanvas" data-bs-target="#filters-sidebar">
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
        <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
      </div>

      <div class="offcanvas-body py-lg-4">
        <ul class="navbar-nav navbar-nav-scroll" style="max-height: 35rem;">
          <!-- Demos switcher-->
          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link " href="{{ route('ar.welcome') }}"> <i class="fi-home fs-base me-2"></i> منزل <span class="d-none d-lg-block position-absolute top-50 end-0 translate-middle-y border-end" style="width: 1px; height: 30px;"></span></a>
          </li>
          <!-- Menu items-->

          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i>توظيف الآن</a>
          </li>

          {{-- <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link align-items-center pe-lg-4" href="{{ route('resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i>CV Available</a>
          </li> --}}


          <li class="nav-item dropdown border-secondary border-2">
            @if (Auth::check())
              <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.myorder') }}"><i class="fi-cart me-2"></i>طلباتي</a>
            @else
              <a class="nav-link align-items-center pe-lg-4" href="#signin-modal" data-bs-toggle="modal" role="button" aria-expanded="false"><i class="fi-cart me-2"></i>طلباتي</a>
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
            <h2 class="h5 mb-0 cv-head">اسم المستخدم</h2>
            <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body py-lg-4">
            <a class="dropdown-item border-bottom py-3" href="{{ route('ar.myorder') }}"><i class="fi-home opacity-60 me-2"></i>طلباتي</a>

            <a class="dropdown-item border-bottom py-3" href=""><i class="fi-heart opacity-60 me-2"></i>قائمة الرغبات</a>

            <a class="dropdown-item border-bottom py-3" href="{{ route('ar.myprofile') }}"><i class="fi-user opacity-60 me-2"></i>معلومات شخصية</a>

            <a class="dropdown-item border-bottom py-3" href=""><i class="fi-lock opacity-60 me-2"></i>كلمة المرور والأمان</a>

            <a class="dropdown-item border-bottom py-3" href=""><i class="fi-bell opacity-60 me-2"></i>إشعار</a>

            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}">
              <a class="dropdown-item border-bottom py-3" href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();"><i class="fi-logout opacity-60 me-2"></i>خروج</a>
            </form>

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
      $('#englanguage').on('click',function(){
        var url = "{{ url('/') }}";
        var current_url = window.location.pathname;
        var newurl = current_url.replace('/ar',"");

        window.location = url+newurl;

      });
    });
  </script>

  <script>
    $(document).ready(function(){
      $('#englanguage2').on('click',function(){
        var url = "{{ url('/') }}";

        var current_url = window.location.pathname;
        var newurl = current_url.replace('/ar',"");

        window.location = url+newurl;

      });
    });
  </script>

<script>
  $(document).ready(function(){
    $('#englanguagem').on('click',function(){
      var url = "{{ url('/') }}";

      var current_url = window.location.pathname;
      var newurl = current_url.replace('/ar',"");

      window.location = url+newurl;

    });
  });
</script>
<script>
 $(document).ready(function() {
    var mobile; 
    $('#signin-modal .modal-title').show();
    $('#signin-modal .modal-footer').show();
  
    function sendOTP(mobile, countryCode) {
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
                var editMobileInput = '' + mobile + '  <button id="editMobileBtn" class="btn btn-outline-primary mt-2">يحرر</button>';
                //var editMobileInput = '<span id="editMobileBtn" class="edit_style">' + mobile + ' <u>Edit</u></span>';
                $('#firstpopup').append(editMobileInput);
                $('#firstpopup').append('<form id="formSecondPopup" method="post"><div class="mb-4"><label class="form-label mb-2" for="signin-email">كلمة السر لمرة واحدة</label><input class="form-control valid" type="text" name="otp" id="inputSecondContent" placeholder="أدخل كلمة المرور لمرة واحدة" required=""><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">يُقدِّم</button></form>');
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
       var editMobileInput = '<label class="form-label mb-2" for="signin-email">الرجاء إدخال رقمك (واتس اب)</label><div class="input-group mb-3"><select name="country_code" class="form-select form-select-sm">@foreach ($countries as $country)<option value="{{ $country->id }},{{ $country->country_code }}">{{ $country->name }}</option>@endforeach</select><input class="form-control" type="text" name="mobile" id="edited-mobile" placeholder="Enter Mobile No" value="' + mobile + '" required></div>';
       $('#firstpopup').html(editMobileInput + '<button id="resendOtpBtn" class="btn btn-primary mt-2 btn-lg w-100">إعادة إرسال مكتب المدعي العام</button>');

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

</body>

</html>