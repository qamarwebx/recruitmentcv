@extends('layout.user.layout_resume_for_resume_detail')

@section('title', $website['company_name'] ?? 'Qamr International')

@section('page-style')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.min.css">
    {{-- <link rel="stylesheet" href="{{ asset('user/intl-tel-input-master/build/css/intlTelInput.css') }}"> --}}
    
  <style>
      /* Candidate button text size */
        .card .name-candidate a{
            font-size:10px !important;
        }

        .candidate-name{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .verified-badge{
            display:inline-flex;
            align-items:center;
            gap:4px;
            font-size:14px;
            color: #666276;
            font-weight:600;
        }

        .verified-badge img{
            width:18px;
            height:15px;
        }

        .verified-text {
            font-size: 10px;
            font-weight: 150;   /* thin */
            position: absolute;
            padding-left: 21px;
            padding-bottom: 0px;
            color:#666377;

        }
    /* ===================================== */
        /* IMAGE BOXES */
        /* ===================================== */

        .box-slide{
            width:460px;
            height:259px;
            background-color:#dadada;
        }

        .box-slide img{
            object-fit:contain;
            width:100%;
            height:100%;
        }

        .box-thumb{
            height:250px;
            background-color:#dadada;
        }

        .box-thumb img{
            object-fit:contain;
            width:100%;
            height:100%;
        }


        /* ===================================== */
        /* TEXT & LIST */
        /* ===================================== */

        .explsal{
            list-style:none;
        }

        .expmar{
            margin-left:10px;
        }

        .robin_text .fi-calendar,.fi-briefcase,.fi-education,.fi-file {
            color:#646464 !important;
        }


        /* ===================================== */
        /* GREEN GLOW BUTTON */
        /* ===================================== */

        .glowbtn{
            animation: glowing 1500ms infinite;
        }

        @keyframes glowing{
            0%{ background:#228B22; box-shadow:0 0 3px #228B22;}
            50%{ background:#50C878; box-shadow:0 0 20px #50C878;}
            100%{ background:#228B22; box-shadow:0 0 3px #228B22;}
        }


        /* ===================================== */
        /* RED GLOW BUTTON */
        /* ===================================== */

        .redglowbtn{
            animation:redglowing 1500ms infinite;
        }

        @keyframes redglowing{
            0%{ background:#FFA62F; box-shadow:0 0 3px #FFA62F;}
            50%{ background:#FFC96F; box-shadow:0 0 20px #FFC96F;}
            100%{ background:#FFA62F; box-shadow:0 0 3px #FFA62F;}
        }


        /* ===================================== */
        /* CARD STYLE */
        /* ===================================== */

        .shadow-sm{
            box-shadow:0 !important;
        }

        .robin_hood{
            box-shadow:none !important;
        }

        .card{
            background:#fff !important;
        }

        .card-body{
            background:#fff !important;
        }


        /* ===================================== */
        /* CHECKBOX STYLE */
        /* ===================================== */

        .exp_sal_checkbox{
            border:2px solid #0d6efd;
        }

        .exp_sal_checkbox:checked{
            background:#0d6efd;
            border-color:#0d6efd;
        }


        /* ===================================== */
        /* BOTTOM NAV */
        /* ===================================== */

        .bottom-nav-item a{
            text-decoration:none;
            color:#6a686e;
            font-weight:600;
        }


        /* ===================================== */
        /* RIBBON */
        /* ===================================== */

        .ribbon{
            position:absolute;
            top:30px;
            right:-66px;
            width: 214px;
            text-align:center;
            background:linear-gradient(45deg,#198754,#20c997);
            color:#fff;
            font-weight:700;
            font-size:14px;
            transform:rotate(45deg);
            letter-spacing:1px;
            z-index:10;
            padding:0px 0;
        }

        

        .custom-font-size{
            font-size:18px;
        }


        /* Order button container */

        .order-btn-container{
            display:flex;
            gap:10px;
            justify-content:flex-end;
        }

        /* Mobile layout */

        @media (max-width:768px){

            .order-btn-container{
                position:fixed;
                bottom:90px;
                left:10px;
                right:10px;
                z-index:999;

                /* remove white background */
                background:transparent;

                padding:0;
                border-radius:0;
                box-shadow:none;

                display:flex;
                gap:10px;
            }

            /* buttons left-right */
            .order-btn-container .btn{
                flex:1;
                white-space:nowrap;
            }

            /* smaller text for long button */
            .order-btn-container .btn-warning{
                font-size:13px;
            }

            .verified-text {
                font-size: 10px;
                font-weight: 150;   /* thin */
                position: absolute;
                padding-left: 21px;
                padding-bottom: 0px;
                color:#666377;

            }

        }


        /* ===================================== */
        /* SMALL MOBILE IMPROVEMENTS */
        /* ===================================== */

        @media (max-width:575px){

            .box-thumb{
                height:180px;
            }

            .box-slide{
                height:200px;
            }

        }


        /* Default (Desktop) */
        .download-cv-mobile{
            display: none;
        }

        .download-cv-desktop{
            display: block;
        }

        /* Mobile */
        @media (max-width: 768px) {

            .download-cv-mobile{
                display: block !important;
            }

            .download-cv-desktop{
                display: none !important;
            }

        }

        #worklocation4-error {
            color: orange;
            font-size: medium;
        }

  </style>
@endsection

@section('content')
    @php
        if (Auth::check()) {
            $userbkc = App\Models\Booking::where('user_id','=',Auth::user()->id)->where('booking_status','!=',2)->count();
        }

        if ($post->gulfexperience == 1) {
            $jobtype = "Indian Experience ";
        }elseif ($post->gulfexperience == 2) {
            $jobtype = "Ex-Abroad ";
        }else{
            $jobtype = "";
        }
    @endphp
    <section class="container mt-5 mb-lg-5 mb-4 pt-5 pb-lg-5">  
        <!-- Breadcrumb-->
        <div class="row">
            <div class="col-lg-5 d-none d-md-block d-lg-block">
                <nav class="mb-3 pt-md-3 " aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('resumes') }}">Resumes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Candidate Details</li>                
                    </ol>
                </nav>
                <div class="d-md-none d-lg-none mb-3 float-end">
                    <a href="{{ route('resumes') }}" class="btn btn-xs btn-outline-success">Back</a>
                </div>
            </div>
            
            <div class="col-lg-7 d-none d-md-block d-lg-block">
                <div class="ps-lg-5">
                    <p class="mb-3 pt-md-3 fw-bold">CV</p>
                </div>
            </div>

            {{-- Mobile View --}}
            <!-- <div class="col-xs-6 d-md-none d-lg-none">
                <p class="mb-3 fw-bold">CV</p>
            </div> -->

        </div>

        
        <div class="row gy-5 pt-lg-2 mainContentRow">
            <input type="hidden" id="postIDTEXT" value="{{ $post->id }}">
            <div class="col-lg-5">
                <div class="d-flex flex-column">
                    <!-- Carousel with slides count-->
                    @if ($post->photo_file !='' || $post->pass_file !='' || $post->lic_file !='' || $post->video_file != ''|| $post->video_link != ''|| $post->trade_test_video_link != '' || $video_id != '')
                        <div class="order-lg-1 order-2">
                            <div class="tns-carousel-wrapper tns-controls-static">
                                <div class="tns-slides-count text-light"><i class="fi-image fs-lg me-2"></i>
                                    <div class="ps-1">
                                        <span class="tns-current-slide fs-5 fw-bold"></span>
                                        <span class="fs-5 fw-bold">/</span>
                                        <span class="tns-total-slides fs-5 fw-bold"></span>
                                    </div>
                                </div>
                                <div class="tns-carousel-inner" data-carousel-options="{&quot;navAsThumbnails&quot;: true, &quot;navContainer&quot;: &quot;#thumbnails&quot;, &quot;gutter&quot;: 12, &quot;responsive&quot;: {&quot;0&quot;:{&quot;controls&quot;: false},&quot;500&quot;:{&quot;controls&quot;: true}}}">
                                    @if ($post->video_file != '')
                                    <div class="box-slide">
                                            <div class="ratio ratio-16x9">
                                                <video width="640" height="360" controls>
                                                    <source src="{{ asset('videos/' . $post->video_file) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($post->video_link != '')
                                    <div class="box-slide">
                                            <div class="ratio ratio-16x9">
                                                <iframe width="200" height="50" src="{{ $post->video_link }}" frameborder="0" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($post->trade_test_video_link != '')
                                    <div class="box-slide">
                                            <div class="ratio ratio-16x9">
                                                <iframe width="200" height="50" src="{{ $post->trade_test_video_link }}" frameborder="0" allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($post->photo_file !='')
                                        <div class="box-slide">
                                            <img class="rounded-3" src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="Image">
                                        </div>
                                    @endif
                                    @if ($post->pass_file !='')
                                        <div class="box-slide">
                                            <img class="rounded-3" src="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" alt="Image">
                                        </div>
                                    @endif
                                    @if ($post->lic_file !='')
                                        <div class="box-slide">
                                            <img class="rounded-3" src="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" alt="Image">
                                        </div>                
                                    @endif                                
                                    
                                </div>
                            </div>
                            <!-- Thumbnails nav-->
                            <ul class="tns-thumbnails mb-4" id="thumbnails">
                                @if ($post->video_file != '')
                                    <li class="tns-thumbnail">
                                        <video width="100" height="100" controls>
                                        <source src="{{ asset('videos/' . $post->video_file) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                        </video>
                                    </li>    
                                @endif
                                @if ($post->video_link != '')
                                    <li class="tns-thumbnail">
                                        <img src="https://img.youtube.com/vi/{{ $video_id[4]}}/default.jpg" alt="Video Thumbnail" width="100">
                                    </li>    
                                @endif
                                @if ($post->trade_test_video_link != '')
                                    <li class="tns-thumbnail">
                                        <img src="https://img.youtube.com/vi/{{ $test_video_id[4]}}/default.jpg" alt="Video Thumbnail" width="100">
                                    </li>    
                                @endif
                                @if ($post->photo_file != '')
                                    <li class="tns-thumbnail">
                                        <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="Thumbnail">
                                    </li>                                
                                @endif
                                @if ($post->pass_file != '')
                                    <li class="tns-thumbnail">
                                        <img src="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" alt="Thumbnail">
                                    </li>    
                                @endif
                                @if ($post->lic_file != '')
                                    <li class="tns-thumbnail">
                                        <img src="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" alt="Thumbnail">
                                    </li>
                                @endif   
                            </ul>
                        </div>
                    @endif
                </div>
                @php
                    $expected_location = implode(",",$myexpwp);
                @endphp

                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-1 mb-2 position-relative overflow-hidden">

                            <div class="ribbon ribbon-card">Hired</div>

                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-12">

                                        <h1 class="robin_text custom-font-size candidate-name">

                                            {{ $post->cand_name }}

                                            <span class="verified-badge">
                                                <img src="{{ asset('admin/assets/images/candidate/cadidate-verified.svg') }}" alt="Verified">
                                                <span class="verified-text">Verified</span>
                                            </span>

                                        </h1>

                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <li class="explsal"><i class="fi-briefcase"></i> Experience</li>&nbsp
                                        <li class="explsal new">
                                            <b>
                                                @if($total_exp != 0)
                                                    {{ $total_exp }} Years
                                                @else
                                                    Fresher
                                                @endif
                                            </b>
                                        </li>
                                    </div>

                                    <div class="col-md-6">
                                        <li class="explsal"><i class="fi-cash"></i> Expected Salary</li>&nbsp
                                        <li class="explsal">
                                                <b>{{ trim(str_ireplace('riyal', '', $post->exp_sal)) . ' SAR' }}</b>
                                        </li>                                    
                                    </div>

                                   
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <li class="explsal"><i class="fi-car"></i> Applied For</li>
                                        <li class="explsal">
                                            <b>
                                                @if($post->jobtype_id != '')
                                                    {{ $jobtype.''.$proffesion2->eng_name }}
                                                @else
                                                    ---
                                                @endif
                                            </b>
                                        </li>
                                    </div>

                                    <div class="col-md-6">
                                        <li class="explsal"><i class="fi-briefcase"></i> Expected Work place</li><br>
                                        <li class="explsal"><b>{{ $expected_location }}</b></li>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Download CV --}}
                <div class="row ordersection-div download-cv-mobile">
                    <div class="col-md-12 text-end">
                        @if ($post->cv_execute == 1 && $post->cv_execute_file != '')

                            @if (Auth::check())

                            <a class="btn btn-sm btn-primary"
                                    href="{{ asset('admin/assets/images/pdf/'.$post->cv_execute_file) }}"
                                    target="_blank">
                                        <i class="fi-download px-2"></i> Download CV
                                    </a>

                            @else

                            <button type="button"
                                            class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#signin-modal2">
                                        <i class="fi-download px-2"></i> Download CV
                                    </button>

                            @endif
                        @endif
                    </div>
                </div>


                <div class="row ordersection-div">
                    <div class="col-md-12">

                        <div class="order-btn-container">
                            <div class="download-cv-desktop">
                                {{-- Download CV --}}
                                @if ($post->cv_execute == 1 && $post->cv_execute_file != '')
                                    @if (Auth::check())
                                        <a class="btn btn-sm btn-primary"
                                        href="{{ asset('admin/assets/images/pdf/'.$post->cv_execute_file) }}"
                                        target="_blank">
                                            <i class="fi-download px-2"></i> Download CV
                                        </a>
                                    @else
                                        <button type="button"
                                                class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#signin-modal2">
                                            <i class="fi-download px-2"></i> Download CV
                                        </button>
                                    @endif
                                @endif
                            </div>


                            @if (Auth::check())

                                @php
                                $candBkc = DB::table('bookings')
                                    ->where('cand_id',$post->id)
                                    ->where('booking_status','!=',2)
                                    ->where('user_id',Auth::user()->id)
                                    ->count();
                                @endphp


                                <style>
                                        .ribbon-card {
                                            display:none;
                                        }
                                     </style>


                                @if ($candBkc == 0)

                                    <button type="button"
                                            onclick="window.history.back();"
                                            class="btn btn-sm btn-light"
                                            style="border-color:#07c98b;">
                                        <i class="fi-arrow-left me-1"></i> Back
                                    </button>


                                    @if ($webconfig->booking_panel == 0 && !empty(Auth::user()->mobile_verified_at))

                                        <button type="button"
                                                class="btn btn-sm btn-warning redglowbtn"
                                                data-bs-toggle="modal"

                                            @if (Auth::user()->status == 1)
                                                @if($userbkc < $max_limit->max_booking_limit)
                                                    data-bs-target="#modal2Large"
                                                @else
                                                    data-bs-target="#bkerror"
                                                @endif
                                            @elseif(empty(Auth::user()->mobile_verified_at))
                                                data-bs-target="#updateMobile"

                                            @else
                                                data-bs-target="#errorProfile"
                                            @endif
                                        >
                                            <i class="fi-cart px-2"></i> Continue with your order
                                        </button>

                                    @else

                                        <button type="button"
                                                class="btn btn-sm btn-warning redglowbtn"
                                                id="mybutton"
                                                data-bs-toggle="modal"

                                            @if (Auth::user()->status == 1 && !empty(Auth::user()->mobile_verified_at))

                                                @if($userbkc < $max_limit->max_booking_limit)

                                                    @if($partner_count->count() == 1)
                                                        data-bs-target="#modalLarge3"
                                                    @endif

                                                    @if(isset($checkCand))
                                                        data-bs-target="#modalLarge2"
                                                    @else
                                                        data-bs-target="#modalLarge"
                                                    @endif

                                                @else
                                                    data-bs-target="#bkerror"
                                                @endif

                                            @elseif(empty(Auth::user()->mobile_verified_at))
                                                data-bs-target="#updateMobile"

                                            @else
                                                data-bs-target="#errorProfile2"
                                            @endif
                                        >
                                            <i class="fi-cart px-2"></i> Continue with your order
                                        </button>

                                    @endif


                                @else

                                    <button type="button"
                                            onclick="window.history.back();"
                                            class="btn btn-sm btn-light"
                                            style="border-color:#07c98b;">
                                        <i class="fi-arrow-left me-1"></i> Back
                                    </button>

                                    <a href="{{ route('myorder') }}"
                                    class="btn btn-sm btn-success">
                                    View Status
                                    </a>

                                    <style>
                                        .ribbon-card {
                                            display:block;
                                        }
                                     </style>

                                @endif


                            @else

                                <button type="button"
                                        onclick="window.history.back();"
                                        class="btn btn-sm btn-light"
                                        style="border-color:#07c98b;">
                                    <i class="fi-arrow-left me-1"></i> Back
                                </button>

                                <button type="button"
                                        class="btn btn-sm btn-success glowbtn"
                                        data-bs-toggle="modal"
                                        href="#signin-modal2">
                                    <i class="fi-cart px-2"></i> Order Now
                                </button>
                                <style>
                                        .ribbon-card {
                                            display:none;
                                        }
                                     </style>
                            @endif

                        </div>

                    </div>
                </div>

            </div>
            <!-- Sidebar with details-->
            <aside class="col-lg-7">
                <div class="ps-lg-5">
                    {{-- <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="badge bg-success me-2 mb-2">Verified</span><span class="badge bg-info me-2 mb-2">New</span>
                        </div>
                        <div class="text-nowrap">
                            <button class="btn btn-icon btn-light-primary btn-xs shadow-sm rounded-circle ms-2 mb-2" type="button" data-bs-toggle="tooltip" title="Add to Wishlist"><i class="fi-heart"></i></button>
                            <div class="dropdown d-inline-block" data-bs-toggle="tooltip" title="Share">
                                <button class="btn btn-icon btn-light-primary btn-xs shadow-sm rounded-circle ms-2 mb-2" type="button" data-bs-toggle="dropdown"><i class="fi-share"></i></button>
                                <div class="dropdown-menu dropdown-menu-end my-1">
                                    <button class="dropdown-item" type="button"><i class="fi-facebook fs-base opacity-75 me-2"></i>Facebook</button>
                                    <button class="dropdown-item" type="button"><i class="fi-twitter fs-base opacity-75 me-2"></i>Twitter</button>
                                    <button class="dropdown-item" type="button"><i class="fi-instagram fs-base opacity-75 me-2"></i>Instagram</button>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <!---=============== Personal Details ==========---------->
                    <div class="card mb-4">
                        <div class="card-body" style="border-radius: 22px;">
                            <h5 class="mb-0 pb-3"><i class="fi-user opacity-75"></i> Personal Details</h5>
                            <div class="row">
                                <div class="col-md-3 col-xs-4 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Full Name</li>
                                        <li class="mt-2 mb-0"><b>{{ $post->cand_name }} </b></li>
                                    </ul>
                                </div>

                                <div class="col-md-3 col-xs-4 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Age</li>
                                        <li class="mt-2 mb-0"><b>{{ $post->age }} Years</b></li>
                                    </ul>
                                </div>

                                <div class="col-md-3 col-xs-4 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Religion</li>
                                        <li class="mt-2 mb-0"><b>@if(isset($religionN)) {{ $religionN->name }} @else {{ '---' }} @endif </b></li>
                                    </ul>
                                </div>
                                <div class="col-md-3 col-xs-4 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Marital Status</li>
                                        <li class="mt-2 mb-0"><b>{{ $post->marital_status }} </b></li>
                                    </ul>
                                </div>

                                
                                <div class="col-md-3 col-xs-4 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Natinality</li>
                                        <li class="mt-2 mb-0"><b>@if(isset($nation)) {{ $nation->name }} @else {{ '---' }} @endif </b></li>
                                    </ul>
                                </div>
                                <div class="col-md-3 col-xs-4 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Region</li>
                                        <li class="mt-2 mb-0"><b>@if(isset($region)) {{ $region->name }} @else {{ '---' }} @endif</b></li>
                                    </ul>
                                </div>

                                {{-- <div class="col-md-3 col-xs-4 mb-3"> --}}
                                    {{-- <ul class="list-unstyled mt-n2 mb-0"> --}}
                                        {{-- <li class="mt-2 mb-0">Place of Birth</li> --}}
                                        {{-- <li class="mt-2 mb-0"><b>@if(isset($plob)) {{ $plob->name }} @else {{ '---' }} @endif</b></li> --}}
                                        {{-- <li class="mt-2 mb-0"><b>@if($post->plb_text != '') {{ $post->plb_text }} @else {{ '---' }} @endif</b></li> --}}
                                    {{-- </ul> --}}
                                {{-- </div> --}}

                                <div class="col-md-3 col-xs-4 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Language</li>
                                        <li class="mt-2 mb-0"><b>{{ $post->lang_known }}</b></li>
                                    </ul>
                                </div>



                                
                                

                            </div>
                            <!-- Employement Experience -->
                            <h5 class="mb-0 pb-3"><i class="fi-briefcase opacity-75"></i> Employment Experience</h5>

                            @if ($post->experience != 0)
                                @php
                                    $exps = explode(',',$post->experience);
                                    $exprof = explode(',',$post->proff_id);
                                    $expcont = explode(',',$post->expcountry_id);
                                    $expcity = explode(',',$post->expcity_id);
                                    // $expcitytext = explode(",",$post->expcity_id_text);
                                    $iexcp = 0;
                                @endphp
                                @foreach ($exps as $key => $exp)
                                    @php
                                        $exp_country = DB::table('countries')->where('id','=',$expcont[$key])->first();
                                        $exp_prof = DB::table('professions')->where('id','=',$exprof[$key])->first();
                                        $exp_city = DB::table('expecworkcities')->where('id','=',$expcity[$key])->first();
                                    @endphp
                                    <div class="row mb-3">
                                        <div class="col-3">
                                            <ul class="list-unstyled mt-n2 mb-0">
                                                <li class="mt-2 mb-0">Job</li>
                                                <li class="mt-2 mb-0"><b>@if(isset($exp_prof)) {{ $exp_prof->eng_name }} @else {{ '---' }} @endif </b></li>
                                            </ul>
                                        </div>
        
                                        <div class="col-3">
                                            <ul class="list-unstyled mt-n2 mb-0">
                                                <li class="mt-2 mb-0">Period</li>
                                                <li class="mt-2 mb-0"><b>{{ $exp.' Years' }}</b></li>
                                            </ul>
                                        </div>
        
        
                                        <div class="col-3">
                                            <ul class="list-unstyled mt-n2 mb-0">
                                                <li class="mt-2 mb-0">Country</li>
                                                <li class="mt-2 mb-0"><b>@if(isset($exp_country)) {{ $exp_country->name }} @else {{ '---' }} @endif </b></li>
                                            </ul>
                                        </div>
                                        <div class="col-3">
                                            <ul class="list-unstyled mt-n2 mb-0">
                                                <li class="mt-2 mb-0">City</li>
                                                <li class="mt-2 mb-0"><b>@if(isset($exp_city)){{ $exp_city->name }} @else {{ '---' }} @endif</b></li>
                                                {{-- <li class="mt-2 mb-0"><b>@if(count($expcitytext) > $iexcp) {{ $expcitytext[$key] }} @else {{ '---' }} @endif</b></li> --}}
                                            </ul>
                                        </div>
                                    </div>
                                    @php
                                        $iexcp++;
                                    @endphp
                                @endforeach
                            
                            @else
                                <div class="row">
                                    <div class="col-3">
                                        <ul class="list-unstyled mt-n2 mb-0">
                                            <li class="mt-2 mb-0">Job</li>
                                            <li class="mt-2 mb-0"><b>{{ '---' }} </b></li>
                                        </ul>
                                    </div>
        
                                    <div class="col-3">
                                        <ul class="list-unstyled mt-n2 mb-0">
                                            <li class="mt-2 mb-0">Period</li>
                                            <li class="mt-2 mb-0"><b>Fresher</b></li>
                                        </ul>
                                    </div>
        
        
                                    <div class="col-3">
                                        <ul class="list-unstyled mt-n2 mb-0">
                                            <li class="mt-2 mb-0">Country</li>
                                            <li class="mt-2 mb-0"><b>{{ '---' }}</b></li>
                                        </ul>
                                    </div>
                                    <div class="col-3">
                                        <ul class="list-unstyled mt-n2 mb-0">
                                            <li class="mt-2 mb-0">City</li>
                                            <li class="mt-2 mb-0"><b>{{ '---' }}</b></li>
                                        </ul>
                                    </div>
        
        
        
                                </div>
                            @endif

                            <!-- Education and Skills -->
                            <h5 class="mb-0 pb-3 mt-3"><i style="font-size: 24px;" class="fi-education opacity-75"></i> Education and Skills</h5>
                            <div class="row">
                                <div class="col-2">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Education</li>
                                        <li class="mt-2 mb-0"><b>@if(isset($education)) {{ $education->name }} @else {{ '---' }} @endif </b></li>
                                    </ul>
                                </div>
                                <div class="col-2">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Google Map</li>
                                        <li class="mt-2 mb-0"><b>@if($post->google_map == 1) {{ 'Yes' }} @else {{ 'No' }} @endif </b></li>
                                    </ul>
                                </div>

                            
                                @php
                                    $carlist = implode(",",$mycarknwon);
                                @endphp
                                <div class="col-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Vehicle Known</li>
                                        <li class="mt-2 mb-0"><b>{{ $carlist }}</b></li>
                                    </ul>
                                </div>

                                <div class="col-5">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Vehicle Transmission</li>
                                        <li class="mt-2 mb-0"><b>@if($post->vehical_transmission != '') {{ implode(",",$myvehtrans) }}  @else {{ "----" }} @endif</b></li>
                                    </ul>
                                </div>

                            </div>

                            <!-- Passport Details -->
                            <h5 class="mb-0 pb-3"  style="margin-top: 20px"><i class="fi-file opacity-75"></i> Passport Details</h5>
                            @php
                                $newPass = substr_replace($post->pass_no,'**',-2);
                            @endphp
                            <div class="row">
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Passport Number</li>
                                        <li class="mt-2 mb-0"><b>{{ $newPass }} </b></li>
                                    </ul>
                                </div>
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Passport Type</li>
                                        <li class="mt-2 mb-0"><b>{{ $post->pass_type }} </b></li>
                                    </ul>
                                </div>
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Date Of Issue</li>
                                        <li class="mt-2 mb-0"><b>@if($post->doi != '') {{ date('d/m/Y',strtotime($post->doi)) }} @else {{ '---' }} @endif</b></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Date of Expiry</li>
                                        <li class="mt-2 mb-0"><b>@if($post->doe != '') {{ date('d/m/Y',strtotime($post->doe)) }} @else {{ '---' }} @endif </b></li>
                                    </ul>
                                </div>
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Place of Issue</li>
                                        <li class="mt-2 mb-0"><b>@if($post->poi != '') {{ $poi->name }} @else {{ '---' }} @endif </b></li>
                                    </ul>
                                </div>
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Date of Birth</li>
                                        <li class="mt-2 mb-0"><b>@if($post->dob != '') {{ date('d/m/Y',strtotime($post->dob)) }} @else {{ '---' }} @endif</b></li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                    <div class="row mt-3 mb-3">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body">
                                    <h5 class="mb-0 pb-3"><i class="fi-credit-card opacity-75"></i> Price & Departure</h5>
                                    <div class="row g-3">

                                        <!-- Price -->
                                        <div class="col-12 col-sm-6">
                                            <i class="fa-solid fa-sack-dollar me-2"></i> Price
                                            <div class="fw-bold">{{ $costCharge }}</div>
                                        </div>

                                        <!-- Departure -->
                                        <div class="col-12 col-sm-6">
                                            <i class="fas fa-plane-departure me-2"></i> Departure from India
                                            <div class="fw-bold">{{ $depDays }}</div>
                                        </div>

                                        <!-- Warranty -->
                                        <div class="col-12 col-sm-6">
                                            <i class="fa-solid fa-award"></i> Warranty
                                            <div class="fw-bold">90 Days</div>
                                        </div>

                                        <!-- Passport -->
                                        <div class="col-12 col-sm-6">
                                            <i class="fas fa-passport me-2"></i> Passport
                                            <div class="fw-bold">Passport in Office</div>
                                        </div>

                                        <!-- Medical -->
                                        <div class="col-12 col-sm-6">
                                            <i class="fas fa-file-medical me-2"></i> Medical Status
                                            <div class="fw-bold">Medically Fit</div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($bkRequirements->count() > 0)
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-0 pb-3"><i class="fi-alert-circle opacity-75"></i> Booking Requirement</h5>
                                    <div class="row">
                                        <div class="col-md">
                                            <ul>
                                                @foreach ($bkRequirements as $bkRequirement)
                                                    <li>{{ $bkRequirement->requirement_text }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>                       
                    @endif



                    <div class="col-md-9">
                        
                    </div>
                    
                    @php
                        $ref_no = $booking + 1;
                    @endphp

                    <div id="modalthank" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document" data-bs-backdrop="static" data-bs-keyboard="false">
                            <div class="modal-content">
                                <div class="modal-body py-0">
                                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                                        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                                    </svg>
                                    <div class="text-center">
                                        <p><b>Reference Number :</b> <span id="#refNo">{{ $ref_no }}</span></p>
                                        <p class="my-2"><b>Thank You ! You will be contacted soon...</b></p>
                                    </div>
                                    <div class="text-center py-2">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="bkerror" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body py-0">
                                    <div class="text-center">
                                        <p class="my-2 text-danger"><b>You have reached the maximum limit of booking.</b></p>
                                    </div>
                                    <div class="text-center py-2">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="errorProfile" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body py-0">
                                    <div class="text-center">
                                        <p class="my-2"><b>Please complete your profile.</b></p>
                                        {{-- <a href="{{ route('myprofile') }}">Click here...</a> --}}
                                    </div>
                                    <form action="{{ route('booking.profile.update') }}" id="profileDetailID" method="POST">
                                        @csrf
                                        @php
                                            $countries = DB::table('countries')->orderBy('name','ASC')->get();
                                            $cities = DB::table('cities')->orderBy('name','ASC')->get();
                                        @endphp
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <input type="hidden" name="phone_code" class="phone_code">
                                                    <label for="mobile_no" class="form-label">Mobile Number <span class="text-danger">*</span></label><br>
                                                    <input type="text" name="mobile_no" id="mobile_no" class="form-control telephone" placeholder="Enter Mobile Number...">
                                                    <span class="output"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <input type="checkbox" class="form-check-input" value="1" name="whatsapp_notification" id="agree-for-notification" checked>
                                                <label for="agree-for-notification" class="form-check-label">I agree to receive whatsapp messages on my number</label>
                                                
                                            </div>
                                            {{-- <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="">Mobile Number <span class="text-danger">*</span></label>
                                                    <input type="text" name="mobile_no" id="mobile_no" class="form-control" placeholder="Enter Mobile Number...">
                                                </div>
                                            </div> --}}
                                            {{-- <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="">Country <span class="text-danger">*</span></label>
                                                    <select name="country_id" id="country_id" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="">City <span class="text-danger">*</span></label>
                                                    <select name="city_id" id="city_id" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($cities as $city)
                                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> --}}
                                        </div>
                                        <div class="text-center py-2">
                                            <button type="submit" class="btn btn-primary btn-sm">Save and Next</button>
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="errorProfile2" class="modal fade" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">

                        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                            <div class="modal-content shadow-lg border-0">

                                <!-- Header -->
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        Access Restricted
                                    </h5>

                                    <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                    </button>
                                </div>

                                <!-- Body -->
                                <div class="modal-body text-center pt-2 pb-4">

                                    <div class="mb-3">
                                        <i class="bi bi-person-x-fill text-danger" style="font-size:40px;"></i>
                                    </div>

                                    <p class="mb-1 fw-semibold">
                                        You are not eligible to hire any candidate.
                                    </p>

                                    <p class="text-muted small mb-0">
                                        Kindly contact the administrator for further assistance.
                                    </p>

                                </div>

                            </div>
                        </div>
                    </div>

                    <div id="errorProfile2back" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Please complete your profile</h5>
                                    <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body py-0">
                                    {{-- <div class="text-center">
                                        <p class="my-2"><b>Please complete your profile.</b></p>
                                    </div> --}}
                                    <div id="firstMobilepopup">
                                        <form action="{{ route('booking.profile.update') }}" id="profileDetailID2" method="POST">
                                            @csrf
                                            @php
                                                $countries = DB::table('countries')->orderBy('name','ASC')->get();
                                                $cities = DB::table('cities')->orderBy('name','ASC')->get();
                                            @endphp
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <input type="hidden" name="phone_code" class="phone_code2">
                                                        <label for="mobile_no2" class="form-label">Mobile Number <span class="text-danger">*</span></label><br>
                                                        <input type="text" name="mobile_no" id="mobile_no2" class="form-control telephone2" placeholder="Enter Mobile Number...">
                                                        <span class="output"></span>
                                                    </div>
                                                    
                                                </div>

                                                {{-- <div class="col-md-12">
                                                    <input type="checkbox" class="form-check-input" value="1" name="whatsapp_notification" id="agree-for-notification2" checked>
                                                    <label for="agree-for-notification2" class="form-check-label">I agree to receive whatsapp messages on my number</label>
                                                </div> --}}
                                                {{-- <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Country <span class="text-danger">*</span></label>
                                                        <select name="country_id" id="country_id" class="form-control">
                                                            <option value="">Select</option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div> 
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">City <span class="text-danger">*</span></label>
                                                        <select name="city_id" id="city_id" class="form-control">
                                                            <option value="">Select</option>
                                                            @foreach ($cities as $city)
                                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div> --}}
                                            </div>
                                            <div class="float-end py-2">
                                                {{-- <div class="text-center py-2"> --}}
                                                {{-- <button type="button" class="btn btn-primary btn-sm" id="mobileVerification">Send OTP</button> --}}
                                                <button type="submit" class="btn btn-primary btn-sm" id="disableMobBtn">Continue</button>
                                                {{-- <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button> --}}
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="errorProfileEmail2" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Please complete your profile</h5>
                                    <button class="btn-close position-absolute top-0 end-0 mt-3 me-3" type="button" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body py-0">
                                    <div class="text-center">
                                        {{-- <p class="my-2"><b>Please complete your profile.</b></p> --}}
                                        {{-- <a href="{{ route('myprofile') }}">Click here...</a> --}}
                                    </div>
                                    <div id="firstEmailpopup">
                                        <form action="{{ route('booking.profile.emailUpdate') }}" id="profileDetailID2E" method="POST">
                                            @csrf
    
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="add-email" class="form-label">Email <span class="text-danger">*</span></label>
                                                        <input type="text" name="email" id="add-email" class="form-control" placeholder="Enter Email....">
                                                    </div>
                                                    {{-- <div class="col-md-12">
                                                        <input type="checkbox" class="form-check-input" value="1" name="whatsapp_notification" id="agree-for-notification" checked>
                                                        <label for="agree-for-notification" class="form-check-label">I agree to receive whatsapp messages on my number</label>
                                                    </div> --}}
                                                </div>
                                                {{-- <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">Country <span class="text-danger">*</span></label>
                                                        <select name="country_id" id="country_id" class="form-control">
                                                            <option value="">Select</option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div> 
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="">City <span class="text-danger">*</span></label>
                                                        <select name="city_id" id="city_id" class="form-control">
                                                            <option value="">Select</option>
                                                            @foreach ($cities as $city)
                                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div> --}}
                                            </div>
                                            <div class="py-2">
                                                {{-- <button type="submit" class="btn btn-primary btn-sm" >Send OTP</button> --}}
                                                <button type="submit" class="btn btn-primary btn-sm" >Save and Next</button>
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="errorProfileEmail" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body py-0">
                                    <div class="text-center">
                                        <p class="my-2"><b>Please complete your profile.</b></p>
                                        {{-- <a href="{{ route('myprofile') }}">Click here...</a> --}}
                                    </div>
                                    <form action="{{ route('booking.profile.emailUpdate') }}" id="profileDetailIDE" method="POST">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="add-email2" class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="text" name="email" id="add-email2" class="form-control" placeholder="Enter Email....">
                                                </div>
                                                {{-- <div class="col-md-12">
                                                    <input type="checkbox" class="form-check-input" value="1" name="whatsapp_notification" id="agree-for-notification" checked>
                                                    <label for="agree-for-notification" class="form-check-label">I agree to receive whatsapp messages on my number</label>
                                                </div> --}}
                                            </div>
                                            {{-- <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="">Country <span class="text-danger">*</span></label>
                                                    <select name="country_id" id="country_id" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="">City <span class="text-danger">*</span></label>
                                                    <select name="city_id" id="city_id" class="form-control">
                                                        <option value="">Select</option>
                                                        @foreach ($cities as $city)
                                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> --}}
                                        </div>
                                        <div class="text-center py-2">
                                            <button type="submit" class="btn btn-primary btn-sm">Save and Next</button>
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $cities1 = DB::table('cities')->get();
                        $embassies = DB::table('embassies')->orderBy('embassy')->get();
                    @endphp
                   
                    <div id="modalpad" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Please carefully review the hiring details before proceeding.</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ url('resumes/details/booking/2') }}" method="POST" id="bookingsubForm2">
                                    @csrf
                                    <div class="modal-body text-dark">
                                        <input type="hidden" name="cand_id2" id="cand_id2" value="{{ $post->id }}">
                                        <input type="hidden" name="user_id2" id="user_id2" value="@if(Auth::check()){{ Auth::user()->id }} @endif">
                                        <input type="hidden" name="partner_id2" id="partner_id2">
                                        <p><b>Name :</b> {{ $post->cand_name }}</p>
                                        {{-- <p><b>Reference Number :</b> <span id="#refNo">{{ $ref_no }}</span></p> --}}
                                        <div class="d-flex"></div>
                                        <p><b>Recruitment Office Address : </b> <span class="recname"></span><span class="px-2 recaddr"></span></p>
    
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="agreedsal2" class="form-check-label">Are you offering a salary of {{ $post->exp_sal }}?</label>
                                                <input type="checkbox" class="form-check-input" id="agreedsal2" name="agreedsal2">
                                                <span id="errorToShow1"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="worklocation2" class="form-label">In which city worker will be working?</label>
                                                <select name="worklocation2" class="form-select" id="worklocation2">
                                                    <option value="">Select</option>
                                                    @foreach ($expwpf as $expwpf1)
                                                        <option value="{{ $expwpf1->id }}">{{ $expwpf1->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm" id="submitButton2">Confirm Order</button>
                                        {{-- <button type="button" class="btn btn-primary btn-sm" id="booking_sub2">Submit</button> --}}
                                        {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalthank">Submit</button> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                   
                    <div id="modal2Large" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Please carefully review the hiring details before proceeding.</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ url('resumes/details/booking') }}" method="POST" id="bookingsubForm">
                                    @csrf
                                    <div class="modal-body text-dark">
                                        <input type="hidden" name="cand_id" id="cand_id" value="{{ $post->id }}">
                                        <input type="hidden" name="user_id" id="user_id" value="@if(Auth::check()){{ Auth::user()->id }} @endif">
                                        <input type="hidden" name="hoi" id="hoi" value="Qamr International">
                                        <p><b>Name :</b> {{ $post->cand_name }}</p>
                                        {{-- <p><b>Reference Number :</b> <span id="#refNo">{{ $ref_no }}</span></p> --}}
                                        <div class="d-flex"></div>
                                        <p><b>Recruitment Office Address : </b> Qamr International.<span class="px-2">(Mumbai, India.)</span></p>
                                    
                                        <div class="row">
                                            <div class="col-md-12">
                                                
                                                <label for="agreedsal" class="form-check-label">Are you offering a salary of  {{ $post->exp_sal }}?</label>
                                                <input type="checkbox" class="form-check-input" id="agreedsal" name="agreedsal">
                                                <span id="errorToShow2"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="worklocation" class="form-label">In which city worker will be working?</label>
                                                <select name="worklocation" class="form-select" id="worklocation">
                                                    <option value="">Select</option>
                                                    {{-- @foreach ($cities1 as $citie1)
                                                        <option value="{{ $citie1->id }}">{{ $citie1->name }}</option>
                                                    @endforeach --}}
                                                    @foreach ($expwpf as $expwpf2)
                                                        <option value="{{ $expwpf2->id }}">{{ $expwpf2->name }}</option>
                                                    @endforeach
                                                    {{-- @foreach ($expworkcities as $expworkcity2)
                                                        <option value="{{ $expworkcity2->id }}">{{ $expworkcity2->name }}</option>
                                                    @endforeach --}}
                                                </select>
                                            </div>
                                            {{-- <div class="col-md-6">
                                                <label for="embassyfor" class="form-label">Visa Embassy For Booking</label>
                                                <select name="embassy_for" id="embassyfor" class="form-select">
                                                    <option value="">Select</option>
                                                    @foreach ($embassies as $embassy)
                                                        <option value="{{ $embassy->id }}">{{ $embassy->embassy }}</option>
                                                    @endforeach
                                                </select>
                                            </div> --}}
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm" id="submitButton">Confirm Order</button>
                                        {{-- <button type="button" class="btn btn-primary btn-sm" id="booking_sub">Submit</button> --}}
                                        {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalthank">Submit</button> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 1 -->
                    @if($partner_count->count() == 1)
                    <div id="modalLarge3" class="modal fade" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content OrderModelClass shadow-lg border-0 rounded-4">

                                <!-- Header -->
                                <div class="modal-header bg-light border-bottom-0 rounded-top-4 px-4 py-3">
                                    <h5 class="modal-title fw-semibold">
                                        Please carefully review the hiring details before proceeding.
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <hr>

                                <form action="{{ url('resumes/details/booking/4') }}" method="POST" id="bookingsubForm4">
                                    @csrf

                                    <div class="modal-body text-dark px-4 py-4">

                                        <input type="hidden" name="cand_id4" id="cand_id4" value="{{ $post->id }}">
                                        <input type="hidden" name="user_id4" id="user_id4" value="@if(Auth::check()){{ Auth::user()->id }} @endif">
                                        <input type="hidden" name="partner_id4" id="partner_id4" value="{{$partner_count[0]->partner_id}}">

                                        <!-- Candidate Info Card -->
                                        <div class="card border-0 bg-light rounded-3 p-3 mb-4">
                                            <p class="mb-2">
                                                <b>Name :</b> {{ $post->cand_name }}
                                            </p>
                                            <p class="mb-2">
                                                <b>Passport Number:</b> {{ substr_replace($post->pass_no,'**',-2) }}
                                            </p>
                                            <p class="mb-2">
                                                <b>Service fee:</b> {{ $costCharge ?? 0 }}
                                            </p>
                                            
                                            <p class="mb-0">
                                                <b>Recruitment Office Address : </b>
                                                <span class="recname4 fw-semibold">{{$partner_count[0]->portal_add_disp_only}}</span>
                                                <span class="px-2 recaddr text-muted"></span>
                                            </p>
                                        </div>

                                        <div class="row g-4">
                                           <!-- Salary Agreement -->
                                            <div class="col-md-12">

                                                <div class="p-4 border rounded-4 bg-white shadow-sm">

                                                    <!-- Salary Section -->
                                                    <div class="mb-4 pb-3 border-bottom">

                                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                                                            <label for="agreedsal4" class="fw-semibold mb-0">
                                                                Are you offering a salary of {{ $post->exp_sal }}?
                                                            </label>

                                                            <div class="form-check d-flex align-items-center">
                                                                <input class="form-input me-2 exp_sal_checkbox"
                                                                    type="checkbox"
                                                                    id="agreedsal4"
                                                                    name="agreedsal4">
                                                                <label class="form-check-label mb-0" for="agreedsal4">
                                                                    i agree
                                                                </label>
                                                            </div>

                                                        </div>

                                                        <span id="errorToShow4" class="text-danger small mt-2 d-block"></span>

                                                    </div>


                                                   <!-- Work Location Section -->
                                                    <div class="row align-items-center">

                                                        <div class="col-md-6">
                                                            <label for="worklocation4" class="form-label fw-semibold mb-0">
                                                                In which city worker will be working?
                                                            </label>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <select name="worklocation4"
                                                                    class="form-select rounded-3"
                                                                    id="worklocation4">
                                                                <option value="">Select</option>
                                                                @foreach ($expwpf as $expwpf3)
                                                                    <option value="{{ $expwpf3->id }}" class="expwpf3_val">{{ $expwpf3->name }}</option>
                                                                @endforeach
                                                                @foreach ($notinexpwpf as $notinexpwpf_val)
                                                                    <option value="{{ $notinexpwpf_val->id }}" class="notinexpwpf_val">{{ $notinexpwpf_val->name }}</option>
                                                                @endforeach
                                                                
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <span class="worklocation4SpanMsg"></span>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <!-- Footer -->
                                    <div class="modal-footer border-top-0 px-4 pb-4">
                                        <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">
                                            Cancel
                                        </button>

                                        <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm" id="submitButton4">
                                            <span class="btn-text">Confirm Order</span>
                                            <span class="spinner-border spinner-border-sm ms-2 d-none"
                                                role="status"
                                                aria-hidden="true"></span>
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(isset($checkCand))
                        <div id="modalLarge2" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Please carefully review the hiring details before proceeding.</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ url('resumes/details/booking/3') }}" method="POST" id="bookingsubForm3">
                                        @csrf
                                        <div class="modal-body text-dark">
                                            <input type="hidden" name="cand_id3" id="cand_id3" value="{{ $post->id }}"> <!-- Fix: Changed cand_id2 to cand_id3 -->
                                            <input type="hidden" name="user_id3" id="user_id3" value="@if(Auth::check()){{ Auth::user()->id }} @endif">
                                            <input type="hidden" name="partner_id3" id="partner_id3" value="{{$checkCand->partner_id}}">
                                            <p><b>Name :</b> {{ $post->cand_name }}</p>
                                            <div class="d-flex"></div>
                                            <p><b>Recruitment Office Address : </b> <span class="recname3">@if($checkCand->portal_add_disp_only != '') {{$checkCand->portal_add_disp_only}} @else {{ '---' }} @endif</span><span class="px-2 recaddr"></span></p>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label for="agreedsal3" class="form-check-label">Are you offering a salary of {{ $post->exp_sal }}?</label> <!-- Fix: Changed id and name to agreedsal3 -->
                                                    <input type="checkbox" class="form-check-input" id="agreedsal3" name="agreedsal3"> <!-- Fix: Changed id and name to agreedsal3 -->
                                                    <span id="errorToShow3"></span>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="worklocation3" class="form-label">In which city worker will be working?</label> <!-- Fix: Changed id and name to worklocation3 -->
                                                    <select name="worklocation3" class="form-select" id="worklocation3"> <!-- Fix: Changed id and name to worklocation3 -->
                                                        <option value="">Select</option>
                                                        {{-- @foreach ($expworkcities as $expworkcity1)
                                                            <option value="{{ $expworkcity1->id }}">{{ $expworkcity1->name }}</option>
                                                        @endforeach --}}
                                                        @foreach ($expwpf as $expwpf4)
                                                            <option value="{{ $expwpf4->id }}">{{ $expwpf4->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                {{-- <div class="col-md-6">
                                                    <label for="embassyfor3" class="form-label">Visa Embassy For Booking </label>
                                                    <select name="embassy_for3" id="embassyfor3" class="form-select">
                                                        <option value="">Select</option>
                                                        @foreach ($embassies as $embassy)
                                                            <option value="{{ $embassy->id }}">{{ $embassy->embassy }}</option>
                                                        @endforeach
                                                    </select>
                                                </div> --}}
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary btn-sm" id="submitButton3">Confirm Order</button>
                                            {{-- <button type="button" class="btn btn-primary btn-sm" id="booking_sub3">Submit</button> --}}
                                            {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalthank3">Submit</button> --}}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div id="modalLarge" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-center">Choose Your Nearest Recruitment Office</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body modal-pad">
                                        <div class="overflow-auto">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="px-sm-5">Address of our recruitment office partner</th>
                                                        {{-- <th scope="col" class="text-center">Distance</th> --}}
                                                        <th scope="col" class="text-center">Select</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($partners as $partner)
                                                    
                                                        <tr>
                                                            <td class="px-md-5">
                                                                {{ $partner->portal_rec_off_name }}<br>
                                                                <span class="address-para">{{ $partner->portal_add_disp_only }}</span>
                                                            </td>
                                                            {{-- <td class="text-center pt-5 pt-md-4">100km</td> --}}
                                                            <td class="text-center pt-5 pt-md-4">
                                                                <input type="radio" id="office{{$partner->partner_id}}" name="partner_id" value=" {{$partner->partner_id}}">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex">
                                        <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#modalpad">Next Step<i class="fi-arrow-right px-2"></i> </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    
                </div>
            </aside>
        </div>
    </section>
    
   <!-- Related Resumes -->
@if ($rel_posts->count() > 0)
<section class="container mb-5 pb-md-4 d-none d-sm-block">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h3 mb-0">Related Resumes</h2>
        <a class="btn btn-link fw-normal p-0" href="{{ route('resumes') }}">
            View all <i class="fi-arrow-long-right ms-2"></i>
        </a>
    </div>

    <div class="tns-carousel-wrapper tns-controls-outside-xxl tns-nav-outside tns-nav-outside-flush mx-n2">

        <div class="tns-carousel-inner row gx-4 mx-0 pt-3 pb-4"
        data-carousel-options='{
        "items":4,
        "responsive":{
        "0":{"items":2},
        "500":{"items":2},
        "768":{"items":3},
        "992":{"items":4}
        }}'>

        @foreach ($rel_posts as $rel_post)

        @php
        $total_exp = array_sum(explode(',',$rel_post->experience));
        $getAge = (date('Y') - date('Y',strtotime($rel_post->dob)));

        if (!empty($rel_post->religion_id)) {
            $religion = App\Models\Religion::find($rel_post->religion_id);
        } else {
            $religion = null;
        }
        @endphp

        <div class="col-6 col-sm-4 col-xl-4">

            <div class="card border-1 robin_hood shadow-sm card-hover h-100">

                <!-- IMAGE -->
                <div class="robin_img card-img-hover box-thumb">

                    <div class="content-overlay end-0 top-0 pt-3 pe-3">
                        <button class="btn btn-icon btn-light btn-xs text-primary rounded-circle"
                        type="button"
                        data-bs-toggle="tooltip"
                        data-bs-placement="left"
                        title="Add to Wishlist">
                        <i class="fi-heart"></i>
                        </button>
                    </div>

                    <a href="{{ url('resumes/details/'.$rel_post->slug_text) }}"></a>

                    @if ($rel_post->photo_file != '')
                    <img src="{{ asset('admin/assets/images/candidate/'.$rel_post->photo_file) }}"
                    loading="lazy"
                    alt="Candidate Image">
                    @else
                    <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}"
                    loading="lazy"
                    alt="Candidate Image">
                    @endif

                </div>

                <!-- TEXT -->
                <div class="text-center name-candidate py-3">

                    <!-- NAME -->
                    <h3 class="robin_text fw-bold text-uppercase mb-1">
                        {{ $rel_post->cand_name }}
                        <img class="pb-1"
                        src="{{ asset('admin/assets/images/candidate/cadidate-verified.svg') }}"
                        height="15" width="15">
                    </h3>

                    <!-- AGE + RELIGION -->
                    @if($getAge)
                    <div class="d-flex justify-content-center align-items-center mb-2 robin_text">
                        <i class="fi-calendar me-2"></i>
                        <span>
                        Age {{ $getAge }}
                        @if(optional($religion)->name)
                        {{ $religion->name }}
                        @endif
                        </span>
                    </div>
                    @endif

                    <!-- EXPERIENCE + PROFESSION -->
                    <div class="d-flex justify-content-center gap-2 flex-wrap robin_text">

                        <div class="d-flex align-items-center">
                            <i class="fi-briefcase me-2"></i>
                            <span>
                            {{ $total_exp > 0 ? $total_exp.' Years Exp' : 'Fresher' }}
                            </span>
                        </div>

                        @if ($rel_post->pengname != '')
                        <div class="d-flex align-items-center">
                            <i class="fi-user me-2"></i>
                            <span>{{ $rel_post->pengname }}</span>
                        </div>
                        @endif

                    </div>

                    <!-- BUTTONS -->
                    <div class="mt-3 d-flex flex-column flex-md-row gap-2">

                        <a class="btn btn-sm btn-primary w-100"
                        href="{{ url('resumes/details/'.$rel_post->slug_text) }}">
                        <i class="fi-cart"></i> Order Now
                        </a>

                        <a class="btn btn-sm btn-outline-primary w-100"
                        href="{{ url('resumes/details/'.$rel_post->slug_text) }}">
                        <i class="fi-user"></i> View Full Details
                        </a>

                    </div>

                </div>

            </div>

        </div>

        @endforeach

        </div>
    </div>

</section>
@endif
@endsection

@section('page-script')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.all.min.js"></script>
    {{-- <script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script> --}}

    <script>
        $(document).ready(function(){
            $('#booking_sub2').on('click',function(){
                let cand_id = $('#cand_id2').val();
                let partner_id = $('#partner_id2').val();
                let user_id = $('#user_id2').val();

                var _token = $('meta[name="csrf-token"]').attr('content');

                // console.log(_token);

                jQuery.ajax({
                    url: "{{ url('resumes/details/booking/2') }}",
                    method: "post",
                    type: "html",
                    data:{
                        "_token": _token,
                        cand_id: cand_id,
                        partner_id: partner_id,
                        user_id: user_id
                    },
                    success: function(data){
                        $('#modalpad').modal('hide');
                        $('#booking_sub2').prop('disabled',true);
                        // $('#modalthank').modal('toggle');
                        Swal.fire({
                            title: "Success!",
                            text: "Thank You ! Your booking reference no is "+data.ref_no,
                            icon: "success",
                            customClass:{
                                confirmButton: 'btn btn-primary btn-sm',
                                
                            }
                        }).then(function(){
                            window.location = "{{ route('myorder') }}";
                        });
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#booking_sub').on('click',function(){
                let cand_id = $('#cand_id').val();
                let hoi = $('#hoi').val();
                let user_id = $('#user_id').val();

                var _token = $('meta[name="csrf-token"]').attr('content');

                // console.log(_token);

                jQuery.ajax({
                    url: "{{ url('resumes/details/booking') }}",
                    method: "post",
                    type: "html",
                    data:{
                        "_token": _token,
                        cand_id: cand_id,
                        hoi: hoi,
                        user_id: user_id
                    },
                    success: function(data){
                        $('#modal2Large').modal('hide');
                        $('#booking_sub').prop('disabled',true);
                        $('#modalthank').modal('toggle');
                        Swal.fire({
                            title: "Success!",
                            text: "Thank You ! Your booking reference no is "+data.ref_no,
                            icon: "success",
                            customClass:{
                                confirmButton: 'btn btn-primary btn-sm',
                                
                            }
                        }).then(function(){
                            window.location = "{{ route('myorder') }}";
                        });
                    }
                });

            });
        });
    </script>
     <script>
        $(document).ready(function(){
            $('#booking_sub3').on('click',function(){
                let cand_id = $('#cand_id3').val();
                let partner_id = $('#partner_id3').val();
                let user_id = $('#user_id3').val();

                var _token = $('meta[name="csrf-token"]').attr('content');
                // console.log(_token);

                jQuery.ajax({
                    url: "{{ url('resumes/details/booking/3') }}",
                    method: "post",
                    type: "html",
                    data:{
                        "_token": _token,
                        cand_id: cand_id,
                        partner_id: partner_id,
                        user_id: user_id
                    },
                    success: function(data){
                        $('#modalLarge2').modal('hide');
                        $('#booking_sub3').prop('disabled',true);
                        $('#modalthank3').modal('toggle');
                        Swal.fire({
                            title: "Success!",
                            text: "Thank You ! Your booking reference no is "+data.ref_no,
                            icon: "success",
                            customClass:{
                                confirmButton: 'btn btn-primary btn-sm',
                                
                            }
                        }).then(function(){
                            window.location = "{{ route('myorder') }}";
                        });
                    }
                });

            });
        });
    </script>

<script>
        $(document).ready(function(){
            $('#booking_sub4').on('click',function(){
                let cand_id = $('#cand_id4').val();
                let partner_id = $('#partner_id4').val();
                let user_id = $('#user_id4').val();

                var _token = $('meta[name="csrf-token"]').attr('content');
                // console.log(_token);

                jQuery.ajax({
                    url: "{{ url('resumes/details/booking/4') }}",
                    method: "post",
                    type: "html",
                    data:{
                        "_token": _token,
                        cand_id: cand_id,
                        partner_id: partner_id,
                        user_id: user_id
                    },
                    success: function(data){
                        $('#modalLarge3').modal('hide');
                        $('#booking_sub4').prop('disabled',true);
                        $('#modalthank4').modal('toggle');
                        Swal.fire({
                            title: "Success!",
                            text: "Thank You ! Your booking reference no is "+data.ref_no,
                            icon: "success",
                            customClass:{
                                confirmButton: 'btn btn-primary btn-sm',
                                
                            }
                        }).then(function(){
                            window.location = "{{ route('myorder') }}";
                        });
                    }
                });

            });
        });
</script>


    <script>
        $(document).ready(function(){
            $('#profileDetailID').validate({
                rules:{
                    mobile_no:{
                        required: true,
                        digits: true
                    },
                    country_id:{
                        required: true,
                    },
                    city_id:{
                        required: true
                    }
                },
                messages:{
                    mobile_no:{
                        required: "Please enter mobile number",
                        digits: "Please enter only number"
                    },
                    country_id:{
                        required: "Please select country"
                    },
                    city_id:{
                        required: "Please select city"
                    }
                },
                errorElement: "em",
                errorPlacement: function(error, element){
                    error.addClass( "invalid-feedback" );
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

            $('#profileDetailID').ajaxForm({
                complete: function (xhr) {
                    toastr.options.timeOut = 20000;
                    toastr.success(xhr.responseJSON);

                    $('#errorProfile').modal('hide');
                    
                    $('#modal2Large').modal('show');

                }
            });

        });
    </script>

    <script>
        $(document).ready(function(){
            $('#profileDetailID2').validate({
                rules:{
                    mobile_no:{
                        required: true,
                        digits: true,
                        remote:{
                            type: "GET",
                            url: "{{ url('profile/mobile/check') }}",
                            data:{
                                mobile_no: function(){
                                    return $('#mobile_no2').val();
                                }
                            }
                        }
                    },
                    country_id:{
                        required: true,
                    },
                    city_id:{
                        required: true
                    },
                },
                messages:{
                    mobile_no:{
                        required: "Please enter mobile number",
                        digits: "Please enter only number",
                        remote: "Mobile No is already exists!"
                    },
                    country_id:{
                        required: "Please select country"
                    },
                    city_id:{
                        required: "Please select city"
                    }
                },
                errorElement: "em",
                errorPlacement: function(error, element){
                    error.addClass( "invalid-feedback" );
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
                },

                
            });            

            // Send OTP
            function sendOTPV(country_code,mobile_no){
                $.ajax({
                    type: "POST",
                    url: "{{ route('booking.profile.otpandupdate') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        mobile: mobile_no,
                        countryCode: country_code,
                       
                    },
                    success: function(response) {
                        $('#firstMobilepopup').html('<p>OTP has been sent to your whatsapp number</p>');
                        var editMobileInput = '<div class="text-center"><input type="hidden" name="country_iso_code" id="country_iso_code" value="'+response.country_iso_code+'"><input type="hidden" name="mobile_no2" value="'+response.mobile_no+'" id="editmobile25"><input type="hidden" name="country_code2" value="'+response.countryCode+'" id="editCountryCode25"> '+response.countryCode+'' + response.mobile_no + '  <button id="editMobileBtnM" class="btn btn-sm btn-outline-primary mt-2"><i class="fi-pencil"></i> Edit</button></div>';
                        $('#firstMobilepopup').append(editMobileInput);
                        $('#firstMobilepopup').append('<form id="formSecondPopupM" method="post"><input type="hidden" name="otpLive" id="otpLive" value="'+response.otp+'"><div class="mb-4"><label class="form-label mb-2 label-colour" for="signin-email">Enter the 4-digit code</label><input class="form-control valid" type="text" name="otp" id="inputSecondContent" placeholder="Enter OTP" required=""><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">Submit</button></form>');
                    },
                    error: function(xhr, status, error) {
                        // alert('Failed to generate OTP. Please try again later.');
                        alert(xhr.responseText);
                    }
                });
            }

            // Edit Button Form
            $(document).on('click','#editMobileBtnM',function(){
                var mobileEdit = $('#editmobile25').val();
                var countryCode = $('#editCountryCode25').val();
                var isoCode = $('#country_iso_code').val();

                var editForm = '<div class="row"><div class="col-md-12"><div class="mb-3"><input type="hidden" name="phone_code" class="phone_code23"><label for="mobile_no23" class="form-label">Mobile Number <span class="text-danger">*</span></label><br><input type="text" name="mobile_no" id="mobile_no23" value="'+mobileEdit+'" class="form-control telephone23" placeholder="Enter Mobile Number..."><br><span class="errorMobile2525" style="color:red;font-size:12px;"></span></div></div></div><div class="float-end py-2"><button type="button" id="resendOtpBtn" class="btn btn-primary btn-sm">Resend OTP</button>';
                $('#firstMobilepopup').html(editForm);

                $('.telephone23').intlTelInput({
                
                    // localizedCountries: true,
                    onlyCountries: ["sa","in","qa","ae","kw"],
                    preferredCountries: ["sa", "in"],
                    separateDialCode: true,
                    initialCountry: isoCode,
                
                }).on('countrychange',function(e,countryData){
                    $('.phone_code23').val(($(".telephone23").intlTelInput("getSelectedCountryData").dialCode))
                });
                // Handle resend OTP button click event
                $(document).on('click', '#resendOtpBtn', function() {
                    var editMobileNumber = $('#mobile_no23').val();
                    var editcountryCode = $('.phone_code23').val();

                    if(editcountryCode != ''){
                        var newCountryCode = editcountryCode;
                    }else{
                        var newCountryCode = "91";
                    }

                    if (editMobileNumber != '') {

                        // Check Mobile No is Exists if Exists then disable button

                        $.ajax({
                            type: "GET",
                            url: "{{ url('profile/mobile/check2') }}",
                            data:{
                                mobile_no: editMobileNumber
                            },
                            success: function(response){
                                if (response.dataMsg == 'isnotavailable') {
                                    sendOTPV(newCountryCode,editMobileNumber);
                                    $('.errorMobile2525').text("");
                                }else{
                                    $('.errorMobile2525').text("Mobile no is already exists!");
                                }

                                    
                            }
                        });

                    }   

                    

                });
            });

            // Handle Submission
            $('#profileDetailID2').submit(function (event){
                event.preventDefault();


                var phone_code = $('.phone_code2').val();
                var mobile_no = $('#mobile_no2').val();

                if (phone_code != '') {
                    var country_code = phone_code; 
                } else {
                    var country_code = "91"; 
                }
                
                if (mobile_no != '') {

                    // Check Mobile No is Exists if Exists then disable button

                    $.ajax({
                        type: "GET",
                        url: "{{ url('profile/mobile/check2') }}",
                        data:{
                            mobile_no: mobile_no
                        },
                        success: function(response){
                            if (response.dataMsg == 'isnotavailable') {
                                sendOTPV(country_code,mobile_no);
                            }

                                
                        }
                    });

                    
                    
                }



                

            });

            // Handle OTP Send
            $(document).on('submit', '#formSecondPopupM', function(event) {
                event.preventDefault();
                var otp = $('#inputSecondContent').val();
                var liveOtp = $('#otpLive').val();
                if (otp == liveOtp) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("booking.profile.validate-otp2") }}',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            otp: otp
                        },
                        success: function(response) {
                            $("#errorProfile2").modal("hide");
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                customClass:{
                                    confirmButton: 'btn btn-primary btn-sm',
                                
                                }
                            }).then(function(){
                                window.location.reload();
                            });
                        }
                    }); 
                } else {
                    alert("Otp not Match");
                }
            });



            // $('#profileDetailID2').ajaxForm({
            //     complete: function (xhr) {
            //         toastr.options.timeOut = 20000;
            //         toastr.success(xhr.responseJSON);

            //         $('#errorProfile2').modal('hide');
            //         $('#modalLarge').modal('show');

            //     }
            // });

        });
    </script>

<script>
    $(document).ready(function(){
        $('#profileDetailID2E').validate({
            rules:{
                email:{
                    required: true,
                    email: true,
                    remote: {
                            type: "GET",
                            url: '{{ url("user/check/email") }}',
                            data: {
                                email: function(){
                                    return $('#add-email').val();
                                },

                            }
                        }
                },

            },
            messages:{
                email:{
                    required: "Please enter email",
                    email: "Please enter only valid email",
                    remote: "Email already exists!"
                },

            },
            errorElement: "em",
            errorPlacement: function(error, element){
                error.addClass( "invalid-feedback" );
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

        // Send OTP
        // function SendEmailOTP(EmailAddr){
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ route('booking.profile.emailotpandupdate') }}",
        //         data: {
        //             _token: $('meta[name="csrf-token"]').attr('content'),
        //             email: EmailAddr
        //         },
        //         success: function(data){

        //             $('#firstEmailpopup').html('<p>OTP has been sent to your email</p>');
        //             var editEmailInput = '<div class="text-center"><input type="hidden" value="'+data.email+'" id="editEmail25">'+ data.email + '  <button id="editEmailBtnM" class="btn btn-sm btn-outline-primary mt-2"><i class="fi-pencil"></i> Edit</button></div>';
        //             $('#firstEmailpopup').append(editEmailInput);
        //             $('#firstEmailpopup').append('<form id="formSecondPopupE" method="post"><input type="hidden" name="getemail" value="'+data.email+'" id="getEmail25"><input type="hidden" name="otpLive" id="otpLiveE" value="'+data.EmailOtp+'"><div class="mb-4"><label class="form-label mb-2 label-colour" for="signin-email">Enter the 4-digit code</label><input class="form-control valid" type="text" name="otp" id="inputSecondContentE" placeholder="Enter OTP" required=""><button class="btn btn-primary btn-lg w-100 mt-2" type="submit">Submit</button></form>');

        //         },  
        //         error: function(xhr, status, error){
        //             alert("Failed to generate OTP. Please try again later.")
        //         }
        //     });
        // }

        // Edit Button Form
        // $(document).on('click','#editEmailBtnM',function(){
        //     var emailEdit = $('#editEmail25').val();
               
        //     var editForm = '<div class="row"><div class="col-md-12"><div class="mb-3"><label for="add-emaile2" class="form-label">Email <span class="text-danger">*</span></label><input type="text" name="email" id="add-emaile2" value="'+emailEdit+'" class="form-control" placeholder="Enter Email...."></div> </div></div><div class="float-end py-2"><button type="button" id="resendEmailOtpBtn" class="btn btn-primary btn-sm">Resend OTP</button>';
        //     $('#firstEmailpopup').html(editForm);


        //     // Handle resend OTP button click event
        //     $(document).on('click', '#resendEmailOtpBtn', function() {
        //         var editEmail2 = $('#add-emaile2').val();

        //         SendEmailOTP(editEmail2);

        //     });
        // });

        
        // Handle Submission
        // $('#profileDetailID2E').submit(function(e){
        //     e.preventDefault();
        //     var EmailAddr = $('#add-email').val();
            
        //     SendEmailOTP(EmailAddr);
        // });


        // Handle OTP Send
        // $(document).on('submit', '#formSecondPopupE', function(event) {
        //     event.preventDefault();
        //     var otp = $('#inputSecondContentE').val();
        //     var email = $('#getEmail25').val();
        //     var liveOtp = $('#otpLiveE').val();
        //     if (otp == liveOtp) {
        //         $.ajax({
        //             type: 'POST',
        //             url: '{{ route("booking.profile.validate-otp2e") }}',
        //             data: {
        //                 _token: $('meta[name="csrf-token"]').attr('content'),
        //                 otp: otp,
        //                 email: email
        //             },
        //             success: function(response) {
        //                 $("#errorProfileEmail2").modal("hide");
        //                 Swal.fire({
        //                     title: "Success!",
        //                     text: response.message,
        //                     icon: "success",
        //                     customClass:{
        //                         confirmButton: 'btn btn-primary btn-sm',
                            
        //                     }
        //                 }).then(function(){
        //                     window.location.reload();
        //                 });
        //             }
        //         }); 
        //     } else {
        //         alert("Otp not Match");
        //     }
        // });


        $('#profileDetailID2E').ajaxForm({
            complete: function (xhr) {
                toastr.options.timeOut = 20000;
                toastr.success(xhr.responseJSON);

                $('#errorProfileEmail2').modal('hide');
                $('.ordersection-div').load(' .ordersection-div');
                $('#modalLarge').modal('show');

            }
        });

    });
</script>

<script>
    $(document).ready(function(){
        $('#profileDetailIDE').validate({
            rules:{
                email:{
                    required: true,
                    email: true,
                    remote: {
                            type: "GET",
                            url: '{{ url("user/check/email") }}',
                            data: {
                                email: function(){
                                    return $('#add-email2').val();
                                },

                            }
                        }
                },

            },
            messages:{
                email:{
                    required: "Please enter email",
                    email: "Please enter only valid email",
                    remote: "Email already exists!"
                },

            },
            errorElement: "em",
            errorPlacement: function(error, element){
                error.addClass( "invalid-feedback" );
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

        $('#profileDetailIDE').ajaxForm({
            complete: function (xhr) {
                toastr.options.timeOut = 20000;
                toastr.success(xhr.responseJSON);

                $('#errorProfileEmail').modal('hide');
                $('.ordersection-div').load(' .ordersection-div');
                $('#modalLarge').modal('show');

            }
        });

    });
</script>

    <script>
        $(document).ready(function(){
            $('input[type=radio][name=partner_id]').on("change",function(){
                var partner_id = $(this).val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('getpartner.detail') }}",
                    method: "POST",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        id: partner_id
                    },
                    success:function(data){
                        $('.recname').text(data.portal_add_disp_only+'.');
                        // $('.recaddr').text('('+data.city+', '+data.country+')');
                        $('#partner_id2').val(data.id);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#bookingsubForm').validate({
                rules:{
                    agreedsal:"required",
                    worklocation:{
                        required: true,
                        remote: {
                            type: "POST",
                            url: '{{ url("resumes/details/get/expcitywork") }}',
                            data: {
                                city_id: function(){
                                    return $('#worklocation').val();
                                },
                                cand_id: function(){
                                    return $('input[name="cand_id"]').val();
                                },
                                _token: "{{ csrf_token() }}"
                            }
                        }
                    },
                    embassy_for:{
                        required: true,
                        remote:{
                            type: "GET",
                            url: '{{ url("resumes/details/get/visaembassyfor") }}',
                            data: {
                                embassy_for: function(){
                                    return $('#embassyfor').val();
                                },
                                cand_id: function(){
                                    return $('input[name="cand_id"]').val(); // Fix: Change cand_id2 to cand_id3
                                },
                            }
                        }
                    }
                },
                messages:{
                    agreedsal:"Please check the provided salary!",
                    worklocation:{
                        required: "Please select city",
                        remote: "This candidate is not eligible for selected city please try differenent candidate"
                    },
                    embassy_for:{
                        required: "Please Select Visa Embassy",
                        remote: "This candidate is not available for selected visa embassy, please try different candidate"
                    }
                },
                errorElement: "strong",
                errorPlacement: function(error, element){
                    error.addClass( "invalid-feedbackNew" );
                    if ( element.prop( "type" ) === "checkbox" ) {
                        // error.insertAfter( element.next( "label" ) );
                        error.appendTo("#errorToShow2");
                    } else {
                        error.insertAfter( element );
                    }

                },
              
            });

            $('#bookingsubForm').ajaxForm({
                beforeSubmit: function(arr, $form, options) {
                // Disable the submit button to prevent multiple submissions
                    $('#submitButton').prop('disabled', true);
                },
                complete: function (xhr) {
                    Swal.fire({
                        title: "Success!",
                        text: xhr.responseJSON,
                        icon: "success",
                        customClass:{
                            confirmButton: 'btn btn-success btn-sm',
                            
                        },
                        buttonsStyling: false
                    }).then(function(){
                        window.location = "{{ route('myorder') }}";
                    });

                }
            });

        });
    </script>

    <script>
        $(document).ready(function(){
            $('#bookingsubForm2').validate({
                rules:{
                    agreedsal2:"required",
                    worklocation2:{
                        required: true,
                        remote: {
                            type: "POST",
                            url: '{{ url("resumes/details/get/expcitywork2") }}',
                            data: {
                                city_id: function(){
                                    return $('#worklocation2').val();
                                },
                                cand_id: function(){
                                    return $('input[name="cand_id2"]').val();
                                },
                                _token: "{{ csrf_token() }}"
                            }
                        }
                    },
                    embassy_for2:{
                        required: true,
                        remote:{
                            type: "GET",
                            url: '{{ url("resumes/details/get/visaembassyfor") }}',
                            data: {
                                embassy_for: function(){
                                    return $('#embassyfor2').val();
                                },
                                cand_id: function(){
                                    return $('input[name="cand_id2"]').val(); // Fix: Change cand_id2 to cand_id3
                                },
                            }
                        }
                    }
                },
                messages:{
                    agreedsal2:"Please check the provided salary!",
                    worklocation2:{
                        required: "Please select city",
                        remote: "This candidate is not eligible for selected city please try differenent candidate"
                    },
                    embassy_for2:{
                        required: "Please Select Visa Embassy",
                        remote: "This candidate is not available for selected visa embassy, please try different candidate"
                    }
                },
                errorElement: "strong",
                errorPlacement: function(error, element){
                    error.addClass( "invalid-feedbackNew" );
                    if ( element.prop( "type" ) === "checkbox" ) {
                        // error.insertAfter( element.next( "label" ) );
                        error.appendTo("#errorToShow1");
                    } else {
                        error.insertAfter( element );
                    }

                },
                // highlight: function ( element, errorClass, validClass ) {
                //     $( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
                // },
                // unhighlight: function (element, errorClass, validClass) {
                //     $( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
                // }
            });

            $('#bookingsubForm2').ajaxForm({
                beforeSubmit: function(arr, $form, options) {
                // Disable the submit button to prevent multiple submissions
                $('#submitButton2').prop('disabled', true);
                },
                complete: function (xhr) {

                    
                //01052024 console.log(xhr);

                // $('#modalpad .modal-body').hide();
                // $('#modalpad .modal-title').hide();
                // $('#modalpad .modal-footer').hide();

                $('#modalpad').modal('hide');

                Swal.fire({
                    title: "Success!",
                    text: xhr.responseJSON,
                    icon: "success",
                    customClass:{
                        confirmButton: 'btn btn-success btn-sm',
                        
                    },
                    buttonsStyling: false
                }).then(function(){
                    window.location = "{{ route('myorder') }}";
                });

                
                //01052024 var successMessage = $('<div class="swal2-icon swal2-success swal2-icon-show"><div class="swal2-icon-content">✓</div></div><br><h2 class="swal2-title" id="swal2-title" style="display: block;">Success!</h2> <br><div class="swal2-html-container" id="swal2-html-container">' + xhr.responseJSON + '</div><br><div class="text-center"><button class="swal2-confirm swal2-styled btn btn-sm btn-primary mb-2" type="button" style="display: inline-block;" aria-label="OK">OK</button></div>');

                //01052024    $('#modalpad .modal-content').append(successMessage);

                    // $('.swal2-confirm').on('click', function() {
                    // // Refresh the page
                    // location.reload();
                    // });
                }
            });

        });
    </script>

    <script>
        $(document).ready(function(){
            $('#bookingsubForm3').validate({
                rules:{
                    agreedsal3:"required",
                    worklocation3:{
                        required: true,
                        remote: {
                            type: "POST",
                            url: '{{ url("resumes/details/get/expcitywork3") }}',
                            data: {
                                city_id: function(){
                                    return $('#worklocation3').val();
                                },
                                cand_id: function(){
                                    return $('input[name="cand_id3"]').val(); // Fix: Change cand_id2 to cand_id3
                                },
                                _token: "{{ csrf_token() }}"
                            }
                        }
                    },
                    embassy_for3:{
                        required: true,
                        remote:{
                            type: "GET",
                            url: '{{ url("resumes/details/get/visaembassyfor") }}',
                            data: {
                                embassy_for: function(){
                                    return $('#embassyfor3').val();
                                },
                                cand_id: function(){
                                    return $('input[name="cand_id3"]').val(); // Fix: Change cand_id2 to cand_id3
                                },
                            }
                        }
                    }
                },
                messages:{
                    agreedsal3:"Please check the provided salary!",
                    worklocation3:{
                        required: "Please select city",
                        remote: "This candidate is not eligible for selected city please try different candidate" // Fix: Corrected typo in message
                    },
                    embassy_for3:{
                        required: "Please Select Visa Embassy",
                        remote: "This candidate is not available for selected visa embassy, please try different candidate"
                    }
                },
                errorElement: "strong",
                errorPlacement: function(error, element){
                    error.addClass( "invalid-feedbackNew" );
                    if ( element.prop( "type" ) === "checkbox" ) {
                        // error.insertAfter( element.next( "label" ) );
                        error.appendTo("#errorToShow3");
                    } else {
                        error.insertAfter( element );
                    }

                },
            });
            
            $('#bookingsubForm3').ajaxForm({

                beforeSubmit: function () {
                    $('#submitButton3').prop('disabled', true);
                },

                success: function (response) {

                    $('#modalLarge2').modal('hide');

                    // Generate unique AddToCart event ID
                    var eventId = 'addtocart_' + Date.now() + '_' + Math.random();

                    // Browser AddToCart Event
                    fbq('track', 'AddToCart', {
                        content_type: 'product',
                        content_name: 'Booking',
                        value: 1,
                        currency: 'INR'
                    }, {
                        eventID: eventId
                    });

                    // Send to backend (CAPI)
                    fetch("{{ route('admin.store.pageview') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            event_id: eventId,
                            event_name: 'AddToCart',
                            current_url: window.location.href,
                            referrer: document.referrer
                        })
                    });

                    Swal.fire({
                        title: "Success!",
                        text: response,
                        icon: "success",
                        customClass: {
                            confirmButton: 'btn btn-success btn-sm',
                        },
                        buttonsStyling: false
                    }).then(function () {
                        window.location = "{{ route('myorder') }}";
                    });
                },

                error: function () {
                    $('#submitButton3').prop('disabled', false);
                }
            });

            // $('#bookingsubForm3').ajaxForm({
            //     beforeSubmit: function(arr, $form, options) {
            //     // Disable the submit button to prevent multiple submissions
            //         $('#submitButton3').prop('disabled', true);
            //     },
            //     complete: function (xhr) {
            //         $('#modalLarge2').modal('hide');

            //         !function(f,b,e,v,n,t,s)
            //         {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            //         n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            //         if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            //         n.queue=[];t=b.createElement(e);t.async=!0;
            //         t.src=v;s=b.getElementsByTagName(e)[0];
            //         s.parentNode.insertBefore(t,s)}(window, document,'script',
            //         'https://connect.facebook.net/en_US/fbevents.js');

            //         fbq('init', '{{ config("services.facebook_capi.pixel_id") }}');

            //         // Generate unique event ID
            //         var eventId = 'pageview_' + Date.now() + '_' + Math.random();

            //         // Track browser PageView
            //         fbq('track', 'PageView', {}, {
            //             eventID: eventId
            //         });

            //         // Send event to backend
            //         fetch("{{ route('admin.store.pageview') }}", {
            //             method: "POST",
            //             headers: {
            //                 "Content-Type": "application/json",
            //                 "X-CSRF-TOKEN": "{{ csrf_token() }}"
            //             },
            //             body: JSON.stringify({
            //                 event_id: eventId,
            //                 current_url: window.location.href,
            //                 referrer: document.referrer
            //             })
            //         });


                    

            //         Swal.fire({
            //             title: "Success!",
            //             text: xhr.responseJSON,
            //             icon: "success",
            //             customClass:{
            //                 confirmButton: 'btn btn-success btn-sm',
                            
            //             },
            //             buttonsStyling: false
            //         }).then(function(){
            //             window.location = "{{ route('myorder') }}";
            //         });
            //         //01052024
            //         // Hide modal content
            //         // $('#modalLarge2 .modal-body').hide();
            //         // $('#modalLarge2 .modal-title').hide();
            //         // $('#modalLarge2 .modal-footer').hide();
                    
            //         // console.log(xhr);
            //         // Create success message
            //       // Create success message
            //     //   var successMessage3 = $('<div class="swal2-icon swal2-success swal2-icon-show"><div class="swal2-icon-content">✓</div></div><br><h2 class="swal2-title" id="swal2-title" style="display: block;">Success!</h2> <br><div class="swal2-html-container" id="swal3-html-container">' + xhr.responseJSON + '</div><br><div class="text-center"><button class="swal3-confirm swal2-styled btn btn-sm btn-primary mb-2" type="button" style="display: inline-block;" aria-label="OK"><i class="fi fi-br-check-circle"></i> OK</button></div>');
                    

            //         // Append success message to modal content
            //         // $('#modalLarge2 .modal-content').append(successMessage3);

            //         // Reload the page on OK button click
            //         // $('.swal3-confirm').on('click', function() {
            //         //     location.reload();
            //         // });
            //     }
            // });
        });
    </script>
     <script>
        $(document).ready(function(){
            $('#bookingsubForm4').validate({
                rules:{
                    agreedsal4:"required",
                    worklocation4:{
                        required: true,
                        // remote: {
                        //     type: "POST",
                        //     url: '{{ url("resumes/details/get/expcitywork4") }}',
                        //     data: {
                        //         city_id: function(){
                        //             return $('#worklocation4').val();
                        //         },
                        //         cand_id: function(){
                        //             return $('input[name="cand_id4"]').val(); // Fix: Change cand_id2 to cand_id4
                        //         },
                        //         _token: "{{ csrf_token() }}"
                        //     }
                        // }
                    },
                    embassy_for4:{
                        required: true,
                        remote:{
                            type: "GET",
                            url: '{{ url("resumes/details/get/visaembassyfor") }}',
                            data: {
                                embassy_for: function(){
                                    return $('#embassyfor4').val();
                                },
                                cand_id: function(){
                                    return $('input[name="cand_id4"]').val(); // Fix: Change cand_id2 to cand_id3
                                },
                            }
                        }
                    }
                },
                messages:{
                    agreedsal4:"Please check the provided salary!",
                    worklocation4:{
                        required: "Please select city",
                        // remote: "This candidate is not eligible for selected city please try different candidate" // Fix: Corrected typo in message
                    },
                    embassy_for4:{
                        required: "Please Select Visa Embassy",
                        remote: "This candidate is not available for selected visa embassy, please try different candidate"
                    }
                },
                errorElement: "strong",
                errorPlacement: function(error, element){
                    error.addClass( "invalid-feedbackNew" );
                    if ( element.prop( "type" ) === "checkbox" ) {
                        // error.insertAfter( element.next( "label" ) );
                        error.appendTo("#errorToShow4");
                    } else {
                        error.insertAfter( element );
                    }

                },
            });

            $('#bookingsubForm4').ajaxForm({
                beforeSubmit: function () {
                    let btn = $('#submitButton4');
                    btn.prop('disabled', true);
                    btn.find('.btn-text').text('Processing...');
                    btn.find('.spinner-border').removeClass('d-none');
                },
                success: function (response) {
                    
                    showSuccessUI(response);
                },
                error: function () {
                    let btn = $('#submitButton4');

                    btn.prop('disabled', false);
                    btn.find('.btn-text').text('Confirm Order');
                    btn.find('.spinner-border').addClass('d-none');

                    alert('Something went wrong. Please try again.');
                }
            });

            $('#worklocation4').on('change', function () {

                $.ajax({
                    type: "POST",
                    url: '{{ url("resumes/details/get/expcitywork4") }}',
                    data: {
                        city_id: $('#worklocation4').val(),
                        cand_id: $('input[name="cand_id4"]').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {

                        if (response === true || response == "true") {
                            $('.worklocation4SpanMsg').html('');
                            $('#worklocation4-error').remove();
                             $('#submitButton4').attr('disabled', false);

                        } else {
                            if ($('#worklocation4-error').length == 0) {

                                $('#submitButton4').attr('disabled', true);
                                $('.worklocation4SpanMsg').html('');
                                $('.worklocation4SpanMsg').html(
                                    '<label id="worklocation4-error" class="error" for="worklocation4">' +
                                    'The selected candidate is not eligible for the chosen visa embassy. ' +
                                    'Please click <a href="{{ route("resumes") }}">here</a> to find suitable candidates.' +
                                    '</label>'
                                );
                            } else {
                                $('#submitButton4').attr('disabled', true);
                                $('.worklocation4SpanMsg').html('');
                                $('.worklocation4SpanMsg').html(
                                    '<label id="worklocation4-error" class="error" for="worklocation4">' +
                                    'The selected candidate is not eligible for the chosen visa embassy. ' +
                                    'Please click <a href="{{ route("resumes") }}">here</a> to find suitable candidates.' +
                                    '</label>'
                                );
                            }
                        }

                    }
                });

            });
        });

        function showSuccessUI(orderNo) {

            let successHtml = `
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <span style="
                            display:inline-flex;
                            width:80px;
                            height:80px;
                            border-radius:50%;
                            border:4px solid #d4f4dd;
                            align-items:center;
                            justify-content:center;">
                            <svg width="40" height="40" viewBox="0 0 16 16" fill="#4CAF50">
                                <path d="M13.485 1.929a1 1 0 0 1 .07 1.414L6.5 10.5l-3.555-3.555a1 1 0 1 1 1.414-1.414L6.5 7.672l5.571-5.743a1 1 0 0 1 1.414-.07z"/>
                            </svg>
                        </span>
                    </div>

                    <h3 class="fw-bold">Success!</h3>

                    <p class="text-muted mt-2">
                        ${orderNo}
                    </p>

                     <button class="btn btn-success px-4 mt-3"
                        data-bs-dismiss="modal"
                        onclick="window.location.href='{{ route('myorder') }}'">
                        OK
                    </button>

                </div>
            `;

            $('.OrderModelClass').html(successHtml);
        }

    </script>
    <script>
        $(document).ready(function(){
            $('.telephone').intlTelInput({
                
                // localizedCountries: true,
                onlyCountries: ["in","sa","qa","ae","kw"],
                preferredCountries: [ "in","sa"],
                separateDialCode: true,
                initialCountry: "",
                
            }).on('countrychange',function(e,countryData){
                $('.phone_code').val(($(".telephone").intlTelInput("getSelectedCountryData").dialCode))
            });

            $('.telephone2').intlTelInput({
                
                // localizedCountries: true,
                onlyCountries: ["sa","in","qa","ae","kw"],
                preferredCountries: ["sa", "in"],
                separateDialCode: true,
                initialCountry: "",
                
            }).on('countrychange',function(e,countryData){
                $('.phone_code2').val(($(".telephone2").intlTelInput("getSelectedCountryData").dialCode))
            });

        });

    </script>


    

    
    @if (session()->has('modalcode') && session()->get('modalcode') == 1)

        @if (Auth::check())
            @php
                $candBkc2 = DB::table('bookings')->where('cand_id','=',$post->id)->where('booking_status','!=',2)->where('user_id','=',Auth::user()->id)->count();
            @endphp

            @if ($candBkc2  == 0)
                @if ($webconfig->booking_panel == 0)
                    
                    @if (Auth::user()->status == 1)
                        @if($userbkc < $max_limit->max_booking_limit) 
                            <script>
                                $(function() {
                                    $('#modal2Large').modal('show');
                                });
                            </script>
                        @else 
                            <script>
                                $(function() {
                                    $('#bkerror').modal('show');
                                });
                            </script>
                        @endif
                    @else
                        <script>
                            $(function() {
                                $('#errorProfile').modal('show');
                            });
                        </script>
                    @endif    
                    
                @else
                    @if (Auth::user()->status == 1)
                        @if($userbkc < $max_limit->max_booking_limit) 
                            <script>
                                $(function() {
                                    $('#modalLarge').modal('show');
                                });
                            </script>
                        @else 
                            <script>
                                $(function() {
                                    $('#bkerror').modal('show');
                                });
                            </script>
                        @endif 
                    @else
                        <script>
                            $(function() {
                                $('#errorProfile2').modal('show');
                            });
                        </script>
                    @endif
                    
                @endif
            @endif
        @endif

    @endif
   

@if(session('has_logged_in_before'))
        @if($userbkc < $max_limit->max_booking_limit) 
            <script>
                $(function() {
                    $('#modalLarge').modal('show');
                });
            </script>
        @endif
        @else 
        <script>
            $(function() {
                $('#modalLarge').modal('hide');
            });
        </script>
        @endif 


        {{-- <script>
            $('#modalLarge3').on('hidden.bs.modal', function (e) {
                // Refresh the page
                window.location.reload();
            });
            $('#modalLarge2').on('hidden.bs.modal', function (e) {
                // Refresh the page
                window.location.reload();
            });
            $('#modalLarge').on('hidden.bs.modal', function (e) {
                // Refresh the page
                window.location.reload();
            });
            $('#bkerror').on('hidden.bs.modal', function (e) {
                // Refresh the page
                window.location.reload();
            });
        </script> --}}

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
        </script>


@endsection