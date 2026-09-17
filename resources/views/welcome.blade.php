@extends('layout.user.layout')

@section('title', $website['company_name'] ?? 'Qamr International')
    
@section('page-style')
    <style>
        /* Candidate button text size */
        .card .name-candidate a{
            font-size:10px !important;
        }

        /* Candidate image container */
        .box-thumb{
            height:250px;
            background:#dadada;
            border-radius:14px;
            overflow:hidden;
            position:relative;
        }

        /* Candidate image */
        .box-thumb img{
            width:100%;
            height:100%;
            object-fit:cover;
            display:block;
        }

        /* RTL support */
        .ardir{
            direction:rtl !important;
        }

        /* ---------- Feature strip ---------- */

        .feature-strip-modern{
            background:#f3f4f7;
            border-radius:30px;
        }

        .feature-strip-modern h5{
            font-size:16px;
            margin-bottom:2px;
        }

        .feature-strip-modern small{
            font-size:13px;
        }

        .icon-box-modern{
            width:42px;
            height:42px;
            border-radius:12px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#e9ecf5;
        }

        /* ---------- Divider Line ---------- */

        .border-md-start{
            position:relative;
        }

        /* Desktop vertical line */
        @media (min-width:768px){

            .border-md-start{
                padding-left:25px;
            }

            .border-md-start::before{
                content:"";
                position:absolute;
                left:0;
                top:15%;
                height:70%;
                width:2px;
                background:linear-gradient(to bottom,#0d6efd,#6ea8fe);
                border-radius:2px;
            }

        }

        /* Mobile horizontal line */
        @media (max-width:767.98px){

            .border-md-start{
                padding-top:15px;
                margin-top:15px;
            }

            .border-md-start::before{
                content:"";
                position:absolute;
                top:0;
                left:10%;
                width:80%;
                height:2px;
                background:linear-gradient(to right,#0d6efd,#6ea8fe);
                border-radius:2px;
            }

        }

        /* ---------- Candidate Card Mobile Optimization ---------- */

        @media (max-width:575px){

            /* Candidate image height */
            .box-thumb{
                height: fit-content;
                width: fit-content;
            }

            /* Image inside card */
            .box-thumb img{
                height:100%;
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

            /* Reduce card spacing */
            .name-candidate{
                padding:10px !important;
            }

            /* Icons size */
            .name-candidate i{
                font-size:12px;
            }

        }


    </style>
@endsection

@section('content')
    <!-- Hero-->
    <section class="container pt-5 my-5">
        <div class="row pt-0 pt-md-2 pt-lg-0">
            <div class="col-md-6 offset-md-1 order-md-2 mb-4 mb-lg-3">
                <lottie-player src="https://assets10.lottiefiles.com/packages/lf20_vlk9kqdu.json" background="transparent" speed="1" loop autoplay></lottie-player>
            </div>
    
            <div class="col-md-5 order-md-1 pt-xl-5 pe-lg-0 mb-3 text-md-start text-center">
                <div class="english-content">
                    <h1 class="display-4 mt-lg-5 mb-md-4 mb-3 pt-md-4 pb-lg-2">Recruit Indian House Driver with Valid Saudi License</h1>
                <p class="position-relative lead me-lg-n5">Recruit Indian driver under your visa and guarantee departure to Saudi Arabia Within 10-15 days only Explore our driver listings today and Order now!</p>
            </div>
            <div class="arabic-content text-start">
                <!-- <div class="desk-button"> -->

                <a class="btn btn-sm btn-primary ms-2" href="{{ route('resumes') }}">Order Now
                    <span class="d-none d-sm-inline" style="font-family: 'Tajawal';"></span>
                </a>
    
                <a class="btn btn-primary btn-sm ms-2 rbn" href="{{ route('resumes') }}">Browse Driver CVs <span class="d-none d-sm-inline"
                    style="font-family: 'Tajawal';"> </span></a>
                <!-- </div> -->
    
                <!-- <div class="mobile-button d-md-none">
                    <a class="btn btn-primary btn-sm px-5 fs-sm" href="{{ route('resumes') }}">Order Now </a>
                    <a class="btn btn-primary btn-sm px-5 fs-sm rbn" style="font-family: 'Tajawal';" href="{{ route('resumes') }}"> Browse Driver CVs</a>
                </div> -->
            </div>
            </div>
        </div>
    </section>

    <!-- ======= Second Content ======= -->
    <footer id="footer" class="footer mb-5" style="background-color: #f5f4f8; padding: 0;">

        <div class="container py-4">
            <div class="row">
                <div class="col-12">

                    <div class="feature-strip-modern rounded-5 py-3 px-4">

                        <div class="row align-items-center gy-3">

                            <!-- Licensed -->
                            <div class="col-12 col-md-3 d-flex align-items-center gap-3">
                                <div class="icon-box-modern">
                                    <i class="bi bi-shield-check text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Licensed</h6>
                                    <small class="text-muted">Recruitment Agency</small>
                                </div>
                            </div>

                            <!-- Transparent -->
                            <div class="col-12 col-md-3 d-flex align-items-center gap-3 border-md-start">
                                <div class="icon-box-modern">
                                    <i class="bi bi-clipboard-check text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Transparent</h6>
                                    <small class="text-muted">Process</small>
                                </div>
                            </div>

                            <!-- Verified -->
                            <div class="col-12 col-md-3 d-flex align-items-center gap-3 border-md-start">
                                <div class="icon-box-modern">
                                    <i class="bi bi-patch-check text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Verified</h6>
                                    <small class="text-muted">Candidates</small>
                                </div>
                            </div>

                            <!-- Drivers -->
                            <div class="col-12 col-md-3 d-flex align-items-center gap-3 border-md-start">
                                <div class="icon-box-modern">
                                    <i class="bi bi-people text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">5000+ Drivers</h6>
                                    <small class="text-muted">Deployed</small>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>

    </footer>

      
   <!-- Top offers (carousel)-->
    @if ($posts->count() > 0)
        <section class="container mb-5 pb-md-4">

            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h3 mb-0">Top Resumes</h2>
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

                @foreach ($posts as $post)

                @php
                $total_exp = array_sum(explode(',',$post->experience));
                $getAge = (date('Y') - date('Y',strtotime($post->dob)));

                if (!empty($post->religion_id)) {
                $religion = App\Models\Religion::find($post->religion_id);
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

                            <a href="{{ url('resumes/details/'.$post->slug_text) }}"></a>

                            @if ($post->photo_file != '')
                            <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}"
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

                            <h3 class="robin_text fw-bold text-uppercase mb-1">
                                {{ $post->cand_name }}
                                <img class="pb-1"
                                src="{{ asset('admin/assets/images/candidate/cadidate-verified.svg') }}"
                                height="15" width="15">
                            </h3>

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

                            <div class="d-flex justify-content-center gap-2 flex-wrap robin_text">

                                <div class="d-flex align-items-center">
                                    <i class="fi-briefcase me-2"></i>
                                    <span>
                                    {{ $total_exp > 0 ? $total_exp.' Years Exp' : 'Fresher' }}
                                    </span>
                                </div>

                                @if ($post->pengname != '')
                                <div class="d-flex align-items-center">
                                    <i class="fi-user me-2"></i>
                                    <span>{{ $post->pengname }}</span>
                                </div>
                                @endif

                            </div>

                            <!-- BUTTONS -->
                            <div class="mt-3 d-flex flex-column flex-md-row gap-2">

                                <a class="btn btn-sm btn-primary w-100"
                                href="{{ url('resumes/details/'.$post->slug_text) }}">
                                <i class="fi-cart"></i> Order Now
                                </a>

                                <a class="btn btn-sm btn-outline-primary w-100"
                                href="{{ url('resumes/details/'.$post->slug_text) }}">
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