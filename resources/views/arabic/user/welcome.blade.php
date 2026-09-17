@extends('layout.user.arabic.layout')

@section('title', $website['company_name_ar'] ?? 'قمر انترناشيونال')
    
@section('page-style')
    <style>
        .box-thumb{
            height:250px;
            background:#dadada;
            border-radius:14px;
            overflow:hidden;
            position:relative;
        }

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

        /* Card hover effect */
        .robin_hood{
            border-radius:18px;
            transition:all .35s ease;
            overflow:hidden;
        }

        .robin_hood:hover{
            transform:translateY(-10px);
            box-shadow:0 25px 45px rgba(0,0,0,.12);
        }

        .robin_img img{
            transition:transform .4s ease;
        }

        .robin_hood:hover .robin_img img{
            transform:scale(1.06);
        }

        .robin_hood .btn{
            transition:all .25s ease;
        }

        /* MOBILE FIX */
        @media (max-width:575px){

            .box-thumb{
                height: fit-content;
                width: fit-content;
            }

            .robin_text{
                font-size:12px;
            }

            .name-candidate h2{
                font-size:14px;
            }

            .name-candidate .btn{
                font-size:12px;
                padding:6px 10px;
            }

            .name-candidate {
                padding: 10px !important;
            }

            .main-card {
                padding-left: 5px;
                padding-right: 5px;
            }

            
        }

        .card .name-candidate a {
            font-size:10px;
        }

    </style>
@endsection

@section('content')
    <!-- Hero-->
    <section class="container pt-5 my-5 pb-lg-4">
        <div class="row pt-0 pt-md-2 pt-lg-0">
            <div class="col-md-6 offset-md-1 order-md-2 mb-4 mb-lg-3">
                <lottie-player src="https://assets10.lottiefiles.com/packages/lf20_vlk9kqdu.json" background="transparent" speed="1" loop autoplay></lottie-player>
            </div>
    
            <div class="col-md-5 order-md-1 pt-xl-5 pe-lg-0 mb-3 text-md-start text-center">
                <div class="english-content">
                    {{-- <h1 class="display-4 mt-lg-5 mb-md-4 mb-3 pt-md-4 pb-lg-2 cv-head">Recruit Indian House Driver with Valid Saudi License? </h1> --}}
                {{-- <p class="position-relative lead">Recruit Indian driver under your visa and guarantee departure to Saudi Arabia Within 10-15 days only Explore our driver listings today and Order now!</p> --}}
            </div>
            <div class="arabic-content text-end">
                <h1 class="cv-head ardir">توظيف سائق خاص هندي برخصة سعودية سارية المفعول</h1>
                <p class="position-relative lead ardir">"قم بتوظيف سائق هندي بموجب تأشيرتك وضمان المغادرة إلى المملكة العربية السعودية خلال 10-15 يومًا فقط، استكشف قوائم السائقين لدينا اليوم واطلب الآن!"</p>
                <div class="desk-button">
                <a class="btn btn-primary btn-sm ms-2" href="{{ route('ar.resumes') }}"><span class="d-none d-sm-inline"
                    style="font-family: 'Tajawal';"> اختر السيرة الذاتية <i class="fi-arrow-long-right me-2"></i></span></a>
    
                <a class="btn btn-primary btn-sm ms-2 rbn" href="{{ route('ar.resumes') }}"><span class="d-none d-sm-inline"
                    style="font-family: 'Tajawal';"> اطلب الان <i class="fi-arrow-long-right me-2"></i></span></a>
                </div>
    
                <div class="mobile-button d-md-none">
                <a class="btn btn-primary btn-sm px-5 fs-sm" href="{{ route('ar.resumes') }}">اختر السيرة الذاتية </a>
                <a class="btn btn-primary btn-sm px-5 fs-sm rbn" style="font-family: 'Tajawal';" href="{{ route('ar.resumes') }}"> اطلب
                    الان</a>
                </div>
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
                                    <h6 class="mb-0 fw-bold">مرخص</h6>
                                    <small class="text-muted">وكالة توظيف</small>
                                </div>
                            </div>

                            <!-- Transparent -->
                            <div class="col-12 col-md-3 d-flex align-items-center gap-3 border-md-start">
                                <div class="icon-box-modern">
                                    <i class="bi bi-clipboard-check text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">شفاف</h6>
                                    <small class="text-muted">عملية</small>
                                </div>
                            </div>

                            <!-- Verified -->
                            <div class="col-12 col-md-3 d-flex align-items-center gap-3 border-md-start">
                                <div class="icon-box-modern">
                                    <i class="bi bi-patch-check text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">تم التحقق منه</h6>
                                    <small class="text-muted">مرشحين</small>
                                </div>
                            </div>

                            <!-- Drivers -->
                            <div class="col-12 col-md-3 d-flex align-items-center gap-3 border-md-start">
                                <div class="icon-box-modern">
                                    <i class="bi bi-people text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">أكثر من 5000 سائق</h6>
                                    <small class="text-muted">تم النشر</small>
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

        <div class="d-flex align-items-center justify-content-between mb-3 ardir">
            <h2 class="h3 mb-0 cv-head">أعلى السير الذاتية</h2>
            <a class="btn btn-link fw-normal p-0" href="{{ route('ar.resumes') }}">
                مشاهدة الكل <i class="fi-arrow-long-left ms-2"></i>
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

            if($total_exp != 0){
                if ($total_exp < 1) {
                    $expper = $total_exp." سنة تجربة";
                }else{
                    $expper = $total_exp." سنين تجربة";
                }
            }else{
                $expper = 'أعذب';
            }
            @endphp

            <div class="col-6 col-sm-6 col-xl-4 main-card">

                <div class="card robin_hood shadow-sm card-hover border-1 h-100">

                    <!-- IMAGE -->
                    <div class="robin_img card-img-hover box-thumb">

                        <a href="{{ url('ar/resumes/details/'.$post->slug_text) }}"></a>

                        <div class="content-overlay end-0 top-0 pt-3 pe-3">
                            <button class="btn btn-icon btn-light btn-xs text-primary rounded-circle"
                            type="button"
                            data-bs-toggle="tooltip"
                            data-bs-placement="left"
                            title="أضف إلى قائمة الامنيات">
                            <i class="fi-heart"></i>
                            </button>
                        </div>

                        @if ($post->photo_file != '')
                        <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}"
                        loading="lazy"
                        alt="Image">
                        @else
                        <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}"
                        loading="lazy"
                        alt="Image">
                        @endif

                    </div>

                    <!-- TEXT AREA -->
                    <div class="text-center name-candidate py-3 ardir">

                        <!-- NAME -->
                        <h3 class="robin_text fw-bold mb-1">

                            @if($post->arcand_name != '')
                                {{ $post->arcand_name }}
                            @else
                                {{ $post->cand_name }}
                            @endif

                            <img class="pb-1"
                            src="{{ asset('admin/assets/images/candidate/cadidate-verified.svg') }}"
                            height="15" width="15">
                        </h3>

                        <!-- AGE -->
                        <div class="d-flex justify-content-center align-items-center mb-2 robin_text">
                            <div class="align-items-center">
                                <i class="fi-calendar ms-2"></i>

                                <span>
                                    {{ $getAge }} سنوات من العمر
                                    @if(optional($religion)->name)
                                        {{ $religion->arbname }}
                                    @endif
                                </span>
                            </div>

                        </div>

                        <!-- EXPERIENCE + PROFESSION -->
                        <div class="d-flex justify-content-center gap-2 flex-wrap robin_text">

                            <div class="d-flex align-items-center">
                                <i class="fi-briefcase me-2"></i>
                                <span style="padding-right:5px;">{{ $expper }}</span>
                            </div>

                            @if ($post->arname != '')
                            <div class="d-flex align-items-center">
                                <i class="fi-user me-2"></i>
                                <span style="padding-right:5px;">{{ $post->arname }}</span>
                            </div>
                            @endif

                        </div>
                        

                        <!-- BUTTONS -->
                        <div class="mt-3 d-flex flex-column flex-md-row gap-2">

                            <a class="btn btn-sm btn-primary w-100"
                            href="{{ url('ar/resumes/details/'.$post->slug_text) }}">
                            <i class="fi-cart"></i> اطلب الان
                            </a>

                            <a class="btn btn-sm btn-outline-primary w-100"
                            href="{{ url('ar/resumes/details/'.$post->slug_text) }}">
                            <i class="fi-user"></i> تفاصيل كاملة
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