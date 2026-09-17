<!DOCTYPE html>
<html lang="ar">
<head>
  <title>قمر انترناشيونال</title>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <!-- SEO Meta Tags-->
  <meta name="description" content="Finder - Directory &amp; Listings Bootstrap Template">
  <meta charset="utf-8"> 
  <meta name="keywords" content="bootstrap, business, directory, listings, e-commerce, car dealer, city guide, real estate, job board, user account, multipurpose, ui kit, html5, css3, javascript, gallery, slider, touch">
  <meta name="author" content="Createx Studio">
  <!-- Viewport-->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Favicon and Touch Icons-->
  <link href="{{ asset('user/img/favicon.png') }}" rel="icon" />

  <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
  <meta name="msapplication-TileColor" content="#766df4">
  <meta name="theme-color" content="#ffffff">

  <!-- Vendor Styles-->
  <link rel="stylesheet" media="screen" href="{{ asset('user/vendor/simplebar/dist/simplebar.min.css') }}" />
  <link rel="stylesheet" media="screen" href="{{ asset('user/vendor/nouislider/dist/nouislider.min.css') }}" />
  <link rel="stylesheet" media="screen" href="{{ asset('user/vendor/tiny-slider/dist/tiny-slider.css') }}" />
  <!-- Main Theme Styles + Bootstrap-->
  <link rel="stylesheet" media="screen" href="{{ asset('user/css/theme.min.css') }}">
  <link rel="stylesheet" href="{{ asset('user/css/errortheme.css') }}">
  <link rel="stylesheet" href="{{ asset('user/css/arcustom.css') }}">

  <link rel="stylesheet" href="{{ asset('user/vendor/select2/select2.css') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
  {{-- toastr --}}
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet" />
  <style>
    .box-thumb{
      /* width: 460px; */
      height: 250px;
      background-color: rgb(218, 218, 218);
    }
    .box-thumb img{
      object-fit: contain;
      width: 100%;
      height: 100%;
    }
  </style>
</head>
<!-- Body-->

<body>
  <main class="page-wrapper">
    <!-- Sign In Modal-->
    <div class="modal fade ardir" id="signin-modal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered p-2 my-0 mx-auto" style="max-width: 500px;">
        <div class="modal-content">
          <div class="modal-body px-0 py-2 py-sm-0">
            <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"></button>
            <div class="row mx-0 align-items-center">
              <div class="px-4 pb-4 px-sm-5 pb-sm-5 pt-5">
                <form action="{{ route('users.login') }}" id="signinValidation" method="POST">
                  @csrf
                  <div class="mb-4">
                    <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}">
                    <label class="form-label mb-2" for="signin-email">عنوان البريد الإلكتروني</label>
                    <input class="form-control" type="email" name="email" id="signin-email" placeholder="أدخل بريدك الإلكتروني" required>
                  </div>
                  <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <label class="form-label mb-0" for="signin-password">كلمة المرور</label><a class="fs-sm" href="#forgot-modal" data-bs-toggle="modal" data-bs-dismiss="modal">هل نسيت كلمة السر؟</a>
                    </div>
                    <div class="password-toggle">
                      <input class="form-control" name="password" type="password" id="signin-password" placeholder="أدخل كلمة المرور" required>
                      <label class="password-toggle-btn" aria-label="Show/hide password">
                        <input class="password-toggle-check" type="checkbox"><span class="password-toggle-indicator"></span>
                      </label>
                    </div>
                  </div>
                  <button class="btn btn-primary btn-lg w-100" type="submit">تسجيل الدخول</button>
                  <div class="mt-4">ليس لديك حساب؟ <a href="#signup-modal" data-bs-toggle="modal" data-bs-dismiss="modal">سجل هنا</a></div>
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

    <!-- Navbar-->
    <header class="navbar navbar-expand-lg navbar-light bg-light fixed-top ardir" data-scroll-header>
      <div class="container">
        <a class="navbar-brand me-3 me-xl-4" href="{{ route('ar.welcome') }}">
            <img class="d-block" src="{{ asset('user/img/logo/logo.png') }}" width="160" alt="Qamr">
        </a>
        <!-- <a class="btn btn-outline-primary btn-sm d-lg-none filter-dot" type="button" data-bs-toggle="offcanvas"
          data-bs-target="#filters-sidebar"><i class="fi-filter-alt-horizontal"></i></a> -->

         

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
              <img class="rounded-circle px-1" src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" width="40" alt="Annette Black">                
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
                  <h6 class="fs-base mb-0 cv-head">{{ Auth::user()->name }}</h6>
                  <div class="fs-xs py-2">@if(isset($uprofile) && $uprofile->mobile_no != '') {{ $uprofile->mobile_no }} <br> @endif{{ Auth::user()->email }}</div>
                </div>
              </div>
              <a class="dropdown-item" href="{{ route('ar.myorder') }}"><i class="fi-home opacity-60 me-2"></i> طلباتي</a>
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
          <a class="btn btn-primary btn-sm ms-2 order-lg-3" href="#signin-modal" data-bs-toggle="modal"><i class="fi-user me-2"></i>تسجيل الدخول</a>            
        @endif

        <div class="collapse navbar-collapse order-lg-2" id="navbarNav">
          <ul class="navbar-nav navbar-nav-scroll" style="max-height: 35rem;">
            <!-- Demos switcher-->
            <li class="nav-item dropdown">
                <a class="nav-link" href="{{ route('ar.welcome') }}"> 
                    <i class="fi-home fs-base me-2"></i> منزل <span class="d-none d-lg-block position-absolute top-50 end-0 translate-middle-y border-end" style="width: 1px; height: 30px;"></span>
                </a>
            </li>
            <!-- Menu items-->
            <li class="nav-item {{ (request()->routeIs('ar.resumes')) ?'active':'' }} dropdown me-lg-2">
              <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i>توظيف الآن</a>
            </li>
            {{-- <li class="nav-item dropdown me-lg-2 {{ (request()->routeIs('resumes')) ?'active':'' }}">
              <a class="nav-link align-items-center pe-lg-4" href="{{ route('resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i>CV Available</a>
            </li> --}}
            <li class="nav-item dropdown me-lg-2">
              @if (Auth::check())
                <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.myorder') }}"><i class="fi-cart me-2"></i> طلباتي</a>
              @else
                <a class="nav-link align-items-center pe-lg-4" href="#signin-modal" data-bs-toggle="modal" role="button" aria-expanded="false"><i class="fi-cart me-2"></i>طلباتي</a>
              @endif
            </li>

            <li class="nav-item dropdown">
              <a class="nav-link" href="{{ route('ar.about') }}"><i class="fi-info-circle fs-base me-2"></i> معلومات عنا </a>
            </li>
            {{-- <li class="nav-item dropdown me-lg-2">
              <a class="nav-link align-items-center pe-lg-4" href="{{ route('service') }}" role="button" aria-expanded="false"><i class="fi-layers me-2"></i>Services</a>
            </li> --}}
            <li class="nav-item dropdown">
              <a class="nav-link" href="{{ route('ar.contact') }}"> <i class="fi-phone fs-base me-2"></i> اتصال </a>
            </li>

            <li class="nav-item dropown">
              <a class="nav-link" href="javascript:void(0)" id="englanguage"><i class="fi-globe fs-base me-2"></i> English</a>
            </li>

          </ul>
        </div>
      </div>
    </header>



    <div class="container-fluid mt-5 pt-5 p-0">
      <div class="row g-0 mt-n3">
        
        <!-- Page Content -->
        <div class="col-lg-8  position-relative overflow-hidden pb-5 pt-2 px-3 px-xl-4 px-xxl-5" id="resumesfilter">
          @include('arabic.user.resume.filter')
        </div>

        <!-- Filters sidebar (Offcanvas on mobile)-->
        <aside class="col-lg-3 offset-lg-9 border-top-lg border-start-lg shadow-sm px-3 px-xl-4 px-xxl-5 pt-lg-2 position-fixed desktop-filter">
          <div class="offcanvas offcanvas-start offcanvas-collapse" id="filters-sidebar">
            <div class="offcanvas-header d-flex d-lg-none align-items-center">
              <h2 class="h5 mb-0 cv-head">المرشحات</h2>
              <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body py-lg-4 ardir">
              <div class="pb-0 mb-2">
                <h3 class="h6 cv-head">نوع الوظيفة</h3>
                <select name="proff_id" id="proff_id" class="form-select select2 mb-2">
                  <option value="" selected>اختر نوع الوظيفة</option>
                  @foreach ($jobTypes as $jobType)
                    <option value="{{ $jobType->id }}">{{ $jobType->ar_name.' ('.$jobType->eng_name.')' }}</option>
                  @endforeach
                </select>
              </div>
              <div class="pb-0 mb-2">
                <h3 class="h6 cv-head">ذوي الخبرة</h3>
                <select name="expcity_id" id="expcity_id" class="form-select select2 mb-2">
                  <option value="" selected>اختر المدينة</option>
                  <option value="1"> قد اشتغل في الهند فقط (Indian Experience)</option>
                  <option value="2">سبق له العمل (Gulf Experience)</option>
                </select>
              </div>
              <div class="pb-0 mb-2">
                <h3 class="h6 cv-head">مكان العمل</h3>
                <select name="location_city_id" id="location_city_id" class="form-select select2 mb-2">
                  <option value="" selected>اختر المدينة</option>
                  @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                  @endforeach
                </select>
              </div>
              {{-- <div class="pb-0 mb-2">
                <h3 class="h6">Experience Required</h3>
                <div class="overflow-auto" data-simplebar data-simplebar-auto-hide="false" style="height: 9.5rem;">
                  <div class="form-check">
                    <input class="form-check-input" name="fresher" type="checkbox" id="fresher" value="0">
                    <label class="form-check-label fs-sm" for="fresher">Fresher</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" name="1_2years" type="checkbox" id="1_2years" value="12">
                    <label class="form-check-label fs-sm" for="1_2years">1-2 Years</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" name="2_5years" type="checkbox" id="2_5years" value="25">
                    <label class="form-check-label fs-sm" for="2_5years">2-5 Years</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" name="5_10years" type="checkbox" id="5_10years" value="510">
                    <label class="form-check-label fs-sm" for="5_10years">5-10 Years</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" name="10pyears" type="checkbox" id="10pyears" value="10">
                    <label class="form-check-label fs-sm" for="10pyears">10+ Years</label>
                  </div>

                </div>
              </div> --}}




              <div class="pb-0 mb-2">
                <h3 class="h6 pt-1 cv-head">عمر</h3>
                <div class="d-flex align-items-center">
                  <input class="form-control w-100" name="minage" id="minage" type="number" placeholder="الحد الأدنى">
                  <div class="mx-2">&mdash;</div>
                  <input class="form-control w-100" type="number" id="maxage" name="maxage" placeholder="أقصى">
                </div>
              </div>
              <div class="pb-0 mb-2">
                <h3 class="h6 cv-head">دِين</h3>
                <select name="religions" id="religions" class="form-select select2 mb-2">
                  <option value="" selected>اختر الدين</option>
                  @foreach ($religions as $religion)
                      <option value="{{ $religion->id }}">{{ $religion->arbname.' ('.$religion->name.')' }}</option>
                  @endforeach
                  {{-- <option value="Muslim">Muslim</option>
                  <option value="Hindu">Hindu</option>
                  <option value="Christian">Christian</option>
                  <option value="Jain">Jain</option> --}}
                </select>
              </div>

              <div class=" py-4">
                <button class="btn btn-primary py-2 px-4 mb-2" type="button" id="search"><i class="fi-search me-1"></i> يبحث</button>
                <button class="btn btn-outline-primary px-2" type="button" id="reset"><i class="fi-rotate-right me-1"></i> إعادة تعيين المرشحات</button>
              </div>
            </div>
          </div>
        </aside>
        
        

      </div>
    </div>

  </main>



  <!-- Mobile Menu Link -->
  {{-- <button class="btn btn-light btn-sm w-100 d-lg-none rounded-0 fixed-bottom text-dark fix" type="button">


    <i class="fi-home py-2 pe-2" style="font-size: 18px;">
      <a class="bottom-menu" href="{{ route('ar.welcome') }}">
        <p class="my-0 pt-1" style="font-weight: 600; color: #000; font-size: 14px;">منزل</p>
      </a>
    </i>

    <i class="fi-cart py-2 mx-4 me-3" style="font-size: 18px;">
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

    <i class="fi-filter py-2 mx-4" style="font-size: 18px;">
      <a class="bottom-menu" data-bs-toggle="offcanvas" data-bs-target="#filter-bar">
        <p class="my-0 pt-1" style="font-weight: 600; color: #000; font-size: 14px;">منقي</p>
      </a>
    </i>

    <i class="fi-align-justify py-2 mx-3 me-0 px-2 me-0" style="font-size: 18px;">
      <a class="bottom-menu" data-bs-toggle="offcanvas" data-bs-target="#menu-sidebar">
        <p class="my-0 pt-1" style="font-weight: 600; color: #000; font-size: 14px;">قائمة طعام</p>
      </a>
    </i>
  </button> --}}
  <!-- Mobile Menu Link -->

  <!-- Mobile Menu link New Start -->
  <div class="bottom-nav d-lg-none bottom-nav-fixed w-100">
    <div class="bottom-nav-item">
      <a href="{{ route('ar.welcome') }}">
        <i class="fi-home"></i>
        <p>منزل</p>
      </a>
    </div>
    {{-- @if (Auth::check())
      <div class="bottom-nav-item">
        <a href="{{ route('myorder') }}">
          <i class="fi-cart"></i>
          <p>My Orders</p>
        </a>
      </div>    
    @else
      <div class="bottom-nav-item">
        <a href="#signin-modal" data-bs-toggle="modal" role="button" aria-expanded="false">
          <i class="fi-cart"></i>
          <p>My Orders</p>
        </a>
      </div>
    @endif --}}
    
    <div class="bottom-nav-item">
      <a href="{{ route('ar.resumes') }}">
        <i class="fi-file"></i>
        <p>طلباتي</p>
      </a>
    </div>
    <div class="bottom-nav-item">
      <a data-bs-toggle="offcanvas" data-bs-target="#filter-bar">
        <i class="fi-filter"></i>
        <p>منقي</p>
      </a>
    </div>
    <div class="bottom-nav-item">
      <a href="#" data-bs-toggle="offcanvas" data-bs-target="#menu-sidebar">
        <i class="fi-align-justify"></i>
        <p>قائمة طعام</p>
      </a>
    </div>
  </div>
  <!-- Mobile Menu Link New End -->

  <aside class="d-md-none">
    <div class="offcanvas offcanvas-end offcanvas-collapse" id="menu-sidebar">
      <div class="offcanvas-header d-flex d-lg-none align-items-center">
        <h2 class="h5 mb-0 cv-head">القائمة الرئيسية</h2>
        <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
      </div>

      <div class="offcanvas-body py-lg-4">
        <ul class="navbar-nav navbar-nav-scroll" style="max-height: 35rem;">
          <!-- Demos switcher-->

          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link" href="{{ route('ar.welcome') }}"> <i class="fi-home fs-base me-2"></i> منزل <span class="d-none d-lg-block position-absolute top-50 end-0 translate-middle-y border-end" style="width: 1px; height: 30px;"></span></a>
          </li>
          <!-- Menu items-->

          <li class="nav-item dropdown border-secondary border-2"><a class="nav-link align-items-center pe-lg-4"
              href="{{ route('ar.resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i>توظيف الآن</a>
          </li>

          {{-- <li class="nav-item dropdown border-secondary border-2"><a class="nav-link align-items-center pe-lg-4"
              href="{{ route('resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i>CV Available</a>
          </li> --}}


          <li class="nav-item dropdown border-secondary border-2">
            @if (Auth::check())
              <a class="nav-link align-items-center pe-lg-4" href="{{ route('ar.myorder') }}"><i class="fi-cart me-2"></i>طلباتي</a>
            @else
              <a class="nav-link align-items-center pe-lg-4" href="#signin-modal" data-bs-toggle="modal" role="button" aria-expanded="false"><i class="fi-cart me-2"></i>طلباتي</a>
            @endif
          </li>

          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link" href="{{ route('ar.about') }}"><i class="fi-info-circle fs-base me-2"></i> معلومات عنا </a>
          </li>

          {{-- <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link align-items-center pe-lg-4" href="{{ route('service') }}" role="button" aria-expanded="false"><i class="fi-layers me-2"></i>Services</a>
          </li> --}}

          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link" href="{{ route('ar.contact') }}"> <i class="fi-phone fs-base me-2"></i> جهات الاتصال </a>
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

            <a class="dropdown-item border-bottom py-3" href=""><i class="fi-bell opacity-60 me-2"></i>إشعارات</a>
            @if (Auth::check())

            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}">
              <a class="dropdown-item border-bottom py-3" href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();"><i class="fi-logout opacity-60 me-2"></i>خروج</a>
            </form>
            @endif

            {{-- <a class="dropdown-item border-bottom py-3" href="#"><i class="fi-logout opacity-60 me-2"></i>Sign Out</a> --}}
        </div>
    </div>
</aside>

  <aside class="col-lg-4 col-xl-3 border-top-lg border-end-lg shadow-sm px-3 px-xl-4 px-xxl-5 pt-lg-2 d-md-none">
    <div class="offcanvas offcanvas-start offcanvas-collapse ardir" id="filter-bar">
      <div class="offcanvas-header d-flex d-lg-none align-items-center">
        <h2 class="h5 mb-0 cv-head">المرشحات</h2>
        <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
      </div>

      <div class="offcanvas-body py-lg-4">
        
        {{-- <div class="pb-0 mb-2">
          <h3 class="h6">Experience Required</h3>
          <div class="overflow-auto" data-simplebar data-simplebar-auto-hide="false" style="height: 9.5rem;">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="fresher2" id="fresher2" value="0">
              <label class="form-check-label fs-sm" for="fresher2">Fresher</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="1_2years2" id="1_2years2" value="12">
              <label class="form-check-label fs-sm" for="1_2years">1-2 Years</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="2_5years2" id="2_5years2" value="25">
              <label class="form-check-label fs-sm" for="2_5years">2-5 Years</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="5_10years2" id="5_10years2" value="510">
              <label class="form-check-label fs-sm" for="5_10years">5-10 Years</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="10pyears2" id="10pyears2" value="10">
              <label class="form-check-label fs-sm" for="10pyears">10+ Years</label>
            </div>

          </div>
        </div> --}}
        <div class="pb-0 mb-2">
          <h3 class="h6 cv-head">نوع الوظيفة</h3>
          <select class="form-select select2 mb-2" name="proff_id2" id="proff_id2">
            <option value="" selected>يختار</option>
            @foreach ($jobTypes as $jobTypef2)
              <option value="{{ $jobTypef2->id }}">{{ $jobTypef2->ar_name.' ('.$jobTypef2->eng_name.')' }}</option>
            @endforeach
          </select>
        </div>

        <div class="pb-0 mb-2">
          <h3 class="h6 cv-head">ذوي الخبرة</h3>
          <select name="expcity_id2" id="expcity_id2" class="form-select select2 mb-2">
            <option value="" selected>اختر المدينة</option>
            <option value="1">قد اشتغل في الهند فقط (Indian Experience)</option>
            <option value="2">سبق له العمل (Gulf Experience)</option>
          </select>
        </div>

        <div class="pb-0 mb-2">
          <h3 class="h6 cv-head">مكان العمل</h3>
          <select name="location_city_id2" id="location_city_id2" class="form-select select2 mb-2">
            <option value="" selected>اختر المدينة</option>
            @foreach ($cities as $cityf)
              <option value="{{ $cityf->id }}">{{ $cityf->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="pb-0 mb-2">
          <h3 class="h6 pt-1 cv-head">عمر</h3>
          <div class="d-flex align-items-center">
            <input class="form-control w-100" type="number" name="minage2" id="minage2" placeholder="الحد الأدنى">
            <div class="mx-2">&mdash;</div>
            <input class="form-control w-100" type="number" name="maxage2" id="maxage2" placeholder="أقصى">
          </div>
        </div>

        <div class="pb-0 mb-2">
          <h3 class="h6 cv-head">دِين</h3>
          <select name="religions2" id="religions2" class="form-select select2 mb-2">
            <option value="" selected>اختر الدين</option>
            @foreach ($religions as $religion2)
                <option value="{{ $religion2->id }}">{{ $religion2->arbname.' ('.$religion2->name.')' }}</option>
            @endforeach
            {{-- <option value="Muslim">Muslim</option>
            <option value="Hindu">Hindu</option>
            <option value="Christian">Christian</option>
            <option value="Jain">Jain</option> --}}
          </select>
        </div>


        <div class=" py-4">
          <button class="btn btn-outline-primary px-2" type="button" id="reset2"><i class="fi-rotate-right me-1"></i> إعادة تعيين المرشحات</button>
          <button class="btn btn-primary py-2 px-4 " type="button" id="search2"><i class="fi-search me-1"></i> يبحث</button>
        </div>
      </div>
    </div>
  </aside>

  <!-- Filters sidebar toggle button (mobile)-->

  <!-- Back to top button-->
  <a class="btn-scroll-top" href="#top" data-scroll><span class="btn-scroll-top-tooltip text-muted fs-sm me-2">Top</span><i class="btn-scroll-top-icon fi-chevron-up"></i></a>
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
  <script src="{{ asset('user/vendor/select2/select2.full.min.js') }}"></script>
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
            remote: "البريد الإلكتروني غير موجود في سجلنا ، الرجاء التسجيل!"
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

  <script>
    $(document).ready(function(){
      $('#reset').on('click',function(){
        // reset field
        $('#location_city_id').val('').change();
        $('#proff_id').val('').change();
        $('#expcity_id').val('').change();
        $('#minage').val('');
        $('#maxage').val('');
        $('#religions').val('').change();

        // search based on
        let location_id = '';
        let expcity_id = '';
        let minage = '';
        let maxage = '';
        let proff_id = '';
        let religions = '';

        jQuery.ajax({
          url: "{{ url('ar/resumes') }}",
          method: "get",
          type: 'html',
          data:{
            location_id: location_id,
            // fresher: fresher,
            // year1_2: year1_2,
            // year2_5: year2_5,
            // year5_10: year5_10,
            // year10: year10,
            // finalExp: finalExp,
            expcity_id: expcity_id,
            minage: minage,
            maxage: maxage,
            proff_id: proff_id,
            religions: religions,
          },
          success: function(data){

            $('#resumesfilter').html(data);
          } 
        });


      });
    });
  </script>

  <script>
    $(document).ready(function(){
      $('#search').on('click',function(){
        let location_id = $('#location_city_id').val();
        let fresher = $('#fresher').is(":checked");
        let year1_2 = $('#1_2years').is(":checked");
        let year2_5 = $('#2_5years').is(":checked");
        let year5_10 = $('#5_10years').is(":checked");
        let year10 = $('#10pyears').is(":checked");
        let expcity_id = $('#expcity_id').val();
        let minage = $('#minage').val();
        let maxage = $('#maxage').val();
        let proff_id = $('#proff_id').val();
        let religions = $('#religions').val();

        var finalExp = [];

        if (fresher == true) {
          finalExp.push(0)
        }
        if (year1_2 == true) {
          finalExp.push(1,2)
        }
        if (year2_5 == true) {
          finalExp.push(2,3,4,5)
        }
        if (year5_10 == true) {
          finalExp.push(5,6,7,8,9,10)
        }
        if (year10 == true) {
          for (let i = 10; i <= 50; i++) {
            finalExp.push(i);            
          }

        }


        console.log(finalExp); 

        jQuery.ajax({
          url: "{{ url('ar/resumes') }}",
          method: "get",
          type: 'html',
          data:{
            location_id: location_id,
            // fresher: fresher,
            // year1_2: year1_2,
            // year2_5: year2_5,
            // year5_10: year5_10,
            // year10: year10,
            finalExp: finalExp,
            expcity_id: expcity_id,
            minage: minage,
            maxage: maxage,
            proff_id: proff_id,
            religions: religions
          },
          success: function(data){

            $('#resumesfilter').html(data);
          } 
        });
      });
    });
  </script>

  <script>
    $(document).ready(function(){
      $('#reset2').on('click',function(){
        // reset field
        $('#location_city_id2').val('').change();
        $('#proff_id2').val('').change();
        $('#expcity_id2').val('').change();
        $('#minage2').val('');
        $('#maxage2').val('');
        $('#religions2').val('').change();

        // search based on
        let location_id2 = '';
        let expcity_id2 = '';
        let minage2 = '';
        let maxage2 = '';
        let proff_id2 = '';
        let religions2 = '';



        jQuery.ajax({
          url: "{{ url('ar/resumes') }}",
          method: "get",
          type: 'html',
          data:{
            location_id: location_id2,
            // fresher: fresher,
            // year1_2: year1_2,
            // year2_5: year2_5,
            // year5_10: year5_10,
            // year10: year10,
            // finalExp: finalExp,
            expcity_id: expcity_id2,
            minage: minage2,
            maxage: maxage2,
            proff_id: proff_id2,
            religions: religions2
          },
          success: function(data){

            $('#resumesfilter').html(data);
          } 
        });


      });
    });
  </script>

  <script>
    $(document).ready(function(){
      $('#search2').on('click',function(){
        let location_id2 = $('#location_city_id2').val();
        let fresher2 = $('#fresher2').is(":checked");
        let year1_22 = $('#1_2years2').is(":checked");
        let year2_52 = $('#2_5years2').is(":checked");
        let year5_102 = $('#5_10years2').is(":checked");
        let year102 = $('#10pyears2').is(":checked");
        let expcity_id2 = $('#expcity_id2').val();
        let minage2 = $('#minage2').val();
        let maxage2 = $('#maxage2').val();
        let proff_id2 = $('#proff_id2').val();
        let religions2 = $('#religions2').val();

        var finalExp2 = [];

        if (fresher2 == true) {
          finalExp2.push(0)
        }
        if (year1_22 == true) {
          finalExp2.push(1,2)
        }
        if (year2_52 == true) {
          finalExp2.push(2,3,4,5)
        }
        if (year5_102 == true) {
          finalExp2.push(5,6,7,8,9,10)
        }
        if (year102 == true) {
          for (let i = 10; i <= 50; i++) {
            finalExp2.push(i);            
          }

        }


        // console.log(finalExp2); 

        jQuery.ajax({
          url: "{{ url('ar/resumes') }}",
          method: "get",
          type: 'html',
          data:{
            location_id: location_id2,
            // fresher: fresher,
            // year1_2: year1_2,
            // year2_5: year2_5,
            // year5_10: year5_10,
            // year10: year10,
            finalExp: finalExp2,
            expcity_id: expcity_id2,
            minage: minage2,
            maxage: maxage2,
            proff_id: proff_id2,
            religions: religions2
          },
          success: function(data){

            $('#resumesfilter').html(data);
          } 
        });
      });
    });
  </script>

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
  $(function () {
    var select2 = $('.select2');
    // For all Select2
    if (select2.length) {
      select2.each(function () {
        var $this = $(this);
        $this.wrap('<div class="position-relative"></div>');
        $this.select2({
          dir: "rtl",
          dropdownParent: $this.parent(),
        });
      });
    }
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

</body>

</html>