@extends('layout.user.layout_resume')

@section('title', $website['company_name'] ?? 'Qamr International')

@section('page-style')
  <style>
    .box-thumb{
        height: 250px;
        background-color: rgb(218, 218, 218);
    }
    .box-thumb img{
        object-fit: contain;
        width: 100%;
        height: 100%;
    }
    .robin_text .fi-calendar,.fi-briefcase {
      color: #646464 !important;
    }
    .bottom-nav-item a{
        text-decoration: none;
        color: #6a686e;
        font-weight: 600;
    }

    .card .name-candidate a {
      font-size: 10px !important;
    }

    /* Mobile card optimization */
    @media (max-width:575px){

      .robin_img img{
          height:140px;
          object-fit:cover;
      }

      /* Candidate name */
      .name-candidate h3{
          font-size:13px;
      }

      /* Info text */
      .robin_text{
          font-size:12px;
      }

      /* Buttons */
      .name-candidate .btn{
          font-size:12px;
          padding:6px 8px;
      }

      /* Reduce spacing */
      .name-candidate{
          padding:10px !important;
      }

      /* Icons */
      .name-candidate i{
          font-size:12px;
      }

      .tns-carousel-wrapper{
          height:140px;
          overflow:hidden;
          width: 80%;
      }

    }

  </style>

<script src="https://unpkg.com/gsap@3/dist/gsap.min.js"></script>
<script src="https://unpkg.com/gsap@3/dist/ScrollTrigger.min.js"></script>
@endsection

@section('content')

  <div class="container-fluid mt-5 pt-5 p-0">
    <div class="row g-0 mt-n3">
      <!-- Filters sidebar (Offcanvas on mobile)-->
      <aside class="col-md-3 border-top-lg border-end-lg shadow-sm px-3 px-xl-4 px-xxl-5 pt-lg-2 position-fixed desktop-filter">
        <div class="offcanvas offcanvas-start offcanvas-collapse" id="filters-sidebar">
          <div class="offcanvas-header d-flex d-lg-none align-items-center">
            <h2 class="h5 mb-0">Filters</h2>
            <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body py-lg-4">
            <div class="pb-0 mb-2">
              <h3 class="h6">Job Type</h3>
              <select name="proff_id" id="proff_id" class="form-select select2 mb-2">
                <option value="" selected>Choose Job Type</option>
                @foreach ($jobTypes as $jobType)
                  <option value="{{ $jobType->id }}">{{ $jobType->eng_name.' ('.$jobType->ar_name.')' }}</option>
                @endforeach
              </select>
            </div>
            <div class="pb-0 mb-2">
              <h3 class="h6">Experienced</h3>
              <select name="expcity_id" id="expcity_id" class="form-select select2 mb-2">
                <option value="" selected>Choose Experienced</option>
                <option value="1">Indian Experience (قد اشتغل في الهند فقط)</option>
                <option value="2">Gulf Experience (سبق له العمل)</option>
              </select>
            </div>
            <div class="pb-0 mb-2">
              <h3 class="h6">Work Place</h3>
              <select name="location_city_id" id="location_city_id" class="form-select select2 mb-2">
                <option value="" selected>Choose city</option>
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
              <h3 class="h6 pt-1">Age</h3>
              {{-- <div class="d-flex align-items-center">
                <input class="form-control w-100" name="minage" id="minage" type="number" placeholder="Min">
                <div class="mx-2">&mdash;</div>
                <input class="form-control w-100" type="number" id="maxage" name="maxage" placeholder="Max">
              </div> --}}
              <select name="age" id="ages" class="form-select select2 mb-2">
                <option value="" selected>Choose Age</option>
                <option value="22-25">22-25</option>
                <option value="26-30">25-30</option>
                <option value="31-35">30-35</option>
                <option value="36-40">35-40</option>
                <option value="41-45">40-45</option>
                <option value="46-50">45-50</option>
                <option value="51-55">50-60</option>
              </select>
            </div>
            <div class="pb-0 mb-2">
              <h3 class="h6">Religion</h3>
              <select name="religions" id="religions" class="form-select select2 mb-2">
                <option value="" selected>Choose Religion</option>
                @foreach ($religions as $religion)
                    <option value="{{ $religion->id }}">{{ $religion->name.' ('.$religion->arbname.')' }}</option>
                @endforeach
                {{-- <option value="Muslim">Muslim</option>
                <option value="Hindu">Hindu</option>
                <option value="Christian">Christian</option>
                <option value="Jain">Jain</option> --}}
              </select>
            </div>

            <div class=" py-4">
              <button class="btn btn-outline-primary px-2" type="button" id="reset"><i class="fi-rotate-right me-1"></i>Reset filters</button>
              <button class="btn btn-primary py-2 px-4 " type="button" id="search"><i class="fi-search me-1"></i>Search</button>
            </div>
          </div>
        </div>
      </aside>
      <!-- Page content-->
      
      
      <div class="col-lg-8 offset-lg-3 position-relative overflow-hidden pb-5 pt-2 px-3 px-xl-4 px-xxl-5" id="resumesfilter">
        @include('resume.filter')
      </div>
    </div>
  </div>

  </main>





  <!-- Mobile Menu link new Start -->
  <div class="bottom-nav d-lg-none bottom-nav-fixed w-100">
    <div class="bottom-nav-item">
      <a href="{{ route('welcome') }}">
        <i class="fi-home"></i>
        <p>Home</p>
      </a>
    </div>
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

      @if (Auth::check())
        <a href="{{ route('myorder') }}">
          <i class="fi-cart"></i>
          <p>My Order</p>
        </a>
      @else
        <a href="#signin-modal2" data-bs-toggle="modal" role="button" aria-expanded="false">
          <i class="fi-cart"></i>
          <p>My Order</p>
        </a>
      @endif

      
    </div>
    <div class="bottom-nav-item">
      <a href="#" data-bs-toggle="offcanvas" data-bs-target="#filter-bar">
        <i class="fi-filter"></i>
        <p>Filter</p>
      </a>
    </div>
    <div class="bottom-nav-item">
      <a href="#" data-bs-toggle="offcanvas" data-bs-target="#filters-top">
        <i class="fi-align-justify"></i>
        <p>Menu</p>
      </a>
    </div>
  </div>
  <!-- Mobile Menu Link New end -->

  <aside class="d-md-none">
    <div class="offcanvas offcanvas-end offcanvas-collapse" id="menu-sidebar">
      <div class="offcanvas-header d-flex d-lg-none align-items-center">
        <h2 class="h5 mb-0">Main Menu</h2>
        <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
      </div>

      <div class="offcanvas-body py-lg-4">
        <ul class="navbar-nav navbar-nav-scroll" style="max-height: 35rem;">
          <!-- Demos switcher-->

          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link" href="{{ route('welcome') }}"> <i class="fi-home fs-base me-2"></i> Home <span class="d-none d-lg-block position-absolute top-50 end-0 translate-middle-y border-end" style="width: 1px; height: 30px;"></span></a>
          </li>
          <!-- Menu items-->

          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link align-items-center pe-lg-4" href="{{ route('resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i>All Workers CV</a>
          </li>

          {{-- <li class="nav-item dropdown border-secondary border-2"><a class="nav-link align-items-center pe-lg-4"
              href="{{ route('resumes') }}" role="button" aria-expanded="false"><i class="fi-file me-2"></i>CV Available</a>
          </li> --}}


          <li class="nav-item dropdown border-secondary border-2">
            @if (Auth::check())
              <a class="nav-link align-items-center pe-lg-4" href="{{ route('myorder') }}"><i class="fi-cart me-2"></i>My Order</a>
            @else
              <a class="nav-link align-items-center pe-lg-4" href="#signin-modal2" data-bs-toggle="modal" role="button" aria-expanded="false"><i class="fi-cart me-2"></i>My Order</a>
            @endif
          </li>

          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link" href="{{ route('about') }}"><i class="fi-info-circle fs-base me-2"></i> About Us </a>
          </li>

          {{-- <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link align-items-center pe-lg-4" href="{{ route('service') }}" role="button" aria-expanded="false"><i class="fi-layers me-2"></i>Services</a>
          </li> --}}

          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link" href="{{ route('contact') }}"> <i class="fi-phone fs-base me-2"></i> Contacts </a>
          </li>
          @if (Auth::check())
          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link" href="{{ route('myprofile') }}"> <i class="fi-phone fs-base me-2"></i> Profile </a>
          </li>
          @endif
          <li class="nav-item dropdown border-secondary border-2">
            <a class="nav-link" href="javascript:void(0)" id="arabiclanguage2"><i class="fi-globe fs-base me-2"></i>العربية</a>
          </li>
        </ul>
      </div>
    </div>
  </aside>

  <aside class="col-lg-4 col-xl-3 shadow-sm px-3 px-xl-4 px-xxl-5 pt-lg-2 d-md-none">
    <div class="offcanvas offcanvas-end offcanvas-collapse" id="filters-top">
        <div class="offcanvas-header d-flex d-lg-none align-items-center">
            <h2 class="h5 mb-0">@if(Auth::check()) {{ Auth::user()->name }} @endif</h2>
            <button class="btn-close close-main-menu-model" type="button" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body py-lg-4">

        <a class="dropdown-item border-bottom py-3" href="{{ route('welcome') }}"><i class="fi-home opacity-60 me-2"></i>Home</a>

            @if (Auth::check())
              <a class="dropdown-item border-bottom py-3" href="{{ route('myorder') }}"><i class="fi-cart opacity-60 me-2"></i>My Order</a>
            @else
              <a class="dropdown-item border-bottom py-3 myorderclosebtn" href="#signin-modal2" data-id="1" data-bs-toggle="modal" role="button" aria-expanded="false"><i class="fi-cart opacity-60 me-2"></i>My Order</a>
            @endif

          <a class="dropdown-item border-bottom py-3" href=""><i class="fi-heart opacity-60 me-2"></i>Wishlist</a>

          @if (Auth::check())
            <a class="dropdown-item border-bottom py-3" href="{{ route('myprofile') }}"><i class="fi-user opacity-60 me-2"></i>Personal Info</a>
            @else

              <a class="dropdown-item border-bottom py-3 myorderclosebtn" href="#signin-modal2" data-id="1" data-bs-toggle="modal" role="button" aria-expanded="false"><i class="fi-user opacity-60 me-2"></i>Personal Info</a>
            @endif

          <a class="dropdown-item border-bottom py-3" href=""><i class="fi-lock opacity-60 me-2"></i>Password &amp; Security</a>

          <a class="dropdown-item border-bottom py-3" href=""><i class="fi-bell opacity-60 me-2"></i>Notifications</a>
          @if (Auth::check())
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <input type="hidden" name="current_page" value="{{ Request::getRequestUri() }}">
            <a class="dropdown-item border-bottom py-3" href="{{ route('logout') }}" onclick="event.preventDefault();this.closest('form').submit();"><i class="fi-logout opacity-60 me-2"></i>Sign Out</a>
          </form>
          @else
          <a class="dropdown-item border-bottom py-3 myorderclosebtn" href="#signin-modal2" data-id="0"  data-bs-toggle="modal"><i class="fa fa-sign-in opacity-60 me-2"></i>Login</a>
          @endif
        </div>
    </div>
  </aside>

  <aside class="col-lg-4 col-xl-3 border-top-lg border-end-lg shadow-sm px-3 px-xl-4 px-xxl-5 pt-lg-2 d-md-none">
    <div class="offcanvas offcanvas-start offcanvas-collapse" id="filter-bar">
      <div class="offcanvas-header d-flex d-lg-none align-items-center">
        <h2 class="h5 mb-0">Filters</h2>
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
          <h3 class="h6">Job Type</h3>
          <select class="form-select select2 mb-2" name="proff_id2" id="proff_id2">
            <option value="" selected>Choose</option>
            @foreach ($jobTypes as $jobTypef2)
              <option value="{{ $jobTypef2->id }}">{{ $jobTypef2->eng_name.' ('.$jobTypef2->ar_name.')' }}</option>
            @endforeach
          </select>
        </div>

        <div class="pb-0 mb-2">
          <h3 class="h6">Experienced</h3>
          <select name="expcity_id2" id="expcity_id2" class="form-select select2 mb-2">
            <option value="" selected>Choose city</option>
            <option value="1">Indian Experience (قد اشتغل في الهند فقط)</option>
            <option value="2">Gulf Experience (سبق له العمل)</option>
          </select>
        </div>

        <div class="pb-0 mb-2">
          <h3 class="h6">Work Place</h3>
          <select name="location_city_id2" id="location_city_id2" class="form-select select2 mb-2">
            <option value="" selected>Choose city</option>
            @foreach ($cities as $cityf)
              <option value="{{ $cityf->id }}">{{ $cityf->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="pb-0 mb-2">
          <h3 class="h6 pt-1">Age</h3>
          {{-- <div class="d-flex align-items-center">
            <input class="form-control w-100" type="number" name="minage2" id="minage2" placeholder="Min">
            <div class="mx-2">&mdash;</div>
            <input class="form-control w-100" type="number" name="maxage2" id="maxage2" placeholder="Max">
          </div> --}}
          <select name="age2" id="ages2" class="form-select select2 mb-2">
            <option value="" selected>Choose Age</option>
            <option value="22-25">22-25</option>
            <option value="26-30">25-30</option>
            <option value="31-35">30-35</option>
            <option value="36-40">35-40</option>
            <option value="41-45">40-45</option>
            <option value="46-50">45-50</option>
            <option value="51-55">50-60</option>
          </select>
        </div>

        <div class="pb-0 mb-2">
          <h3 class="h6">Religion</h3>
          <select name="religions2" id="religions2" class="form-select select2 mb-2">
            <option value="" selected>Choose Religion</option>
            @foreach ($religions as $religion2)
                <option value="{{ $religion2->id }}">{{ $religion2->name.' ('.$religion2->arbname.')' }}</option>
            @endforeach
            {{-- <option value="Muslim">Muslim</option>
            <option value="Hindu">Hindu</option>
            <option value="Christian">Christian</option>
            <option value="Jain">Jain</option> --}}
          </select>
        </div>

        <div class="row g-2" style="margin-top:25px;">

          <div class="col-12">
              <button class="btn btn-primary w-100 py-2" type="button" id="search2">
                  <i class="fi-search me-1"></i> Search
              </button>
          </div>

          <div class="col-12">
              <button class="btn btn-outline-primary w-100 py-2" type="button" id="reset2">
                  <i class="fi-rotate-right me-1"></i> Reset Filters
              </button>
          </div>

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
      $(document).on('click', '.offcanvas-backdrop', function () {
          $('.btn-close').trigger('click');
      });
  </script>

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

  <script>
    $(document).ready(function(){
      $('#reset').on('click',function(){
        // reset field
        $('#location_city_id').val('').change();
        $('#proff_id').val('').change();
        $('#expcity_id').val('').change();
        // $('#minage').val('');
        // $('#maxage').val('');
        $('#ages').val('').change();
        $('#religions').val('').change();

        // search based on
        let location_id = '';
        let expcity_id = '';
        // let minage = '';
        // let maxage = '';
        let proff_id = '';
        let ages = '';
        let religions = '';

        jQuery.ajax({
          url: "{{ url('resumes') }}",
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
            // minage: minage,
            // maxage: maxage,
            age: ages,
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
        // let minage = $('#minage').val();
        // let maxage = $('#maxage').val();
        let ages = $('#ages').val();
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


        // console.log(ages); 

        jQuery.ajax({
          url: "{{ url('resumes') }}",
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
            // minage: minage,
            // maxage: maxage,
            age: ages,
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
        // $('#minage2').val('');
        // $('#maxage2').val('');
        $('#ages2').val('').change();
        $('#religions2').val('').change();

        // search based on
        let location_id2 = '';
        let expcity_id2 = '';
        // let minage2 = '';
        // let maxage2 = '';
        let proff_id2 = '';
        let religions2 = '';
        let ages2 = '';


        jQuery.ajax({
          url: "{{ url('resumes') }}",
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
            // minage: minage2,
            // maxage: maxage2,
            proff_id: proff_id2,
            religions: religions2,
            age: ages2
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
        // let minage2 = $('#minage2').val();
        // let maxage2 = $('#maxage2').val();
        let ages2 = $('#ages2').val();
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
          url: "{{ url('resumes') }}",
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
            // minage: minage2,
            // maxage: maxage2,
            age: ages2,
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
          dropdownParent: $this.parent(),
        });
      });
    }
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

    $('.myorderclosebtn').click(function(){
        $('.close-main-menu-model').trigger('click');
      });

</script>

@endsection