@extends('layout.user.arabic.layout')

@section('title', $website['company_name_ar'] ?? 'قمر انترناشيونال')
    
@section('page-style')
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
                    <h1 class="display-4 mt-lg-5 mb-md-4 mb-3 pt-md-4 pb-lg-2 cv-head">Are you looking for a private driver from India ? </h1>
                <p class="position-relative lead"> This is the right place to recruit an experience private driver from India to Saudi Arabia.</p>
            </div>
            <div class="arabic-content text-end">
                <h1 class="cv-head">هل تبحث عن سائق خاص من الهند ؟</h1>
                <p class="position-relative lead">هذا هو المكان المناسب لتوظيف تجربة سائق خاص من الهند إلى
                المملكة
                العربية السعودية
                </p>
                <div class="desk-button">
                <a class="btn btn-primary btn-sm ms-2" href="{{ route('ar.resumes') }}">Find CV <span class="d-none d-sm-inline"
                    style="font-family: 'Tajawal';"> اطلب الان <i class="fi-arrow-long-right me-2"></i></span></a>
    
                <a class="btn btn-primary btn-sm ms-2" href="{{ route('ar.resumes') }}">Order Now <span class="d-none d-sm-inline"
                    style="font-family: 'Tajawal';"> اطلب الان <i class="fi-arrow-long-right me-2"></i></span></a>
                </div>
    
                <div class="mobile-button d-md-none">
                <a class="btn btn-primary btn-sm px-5 fs-sm" href="{{ route('ar.resumes') }}">Order Now </a>
                <a class="btn btn-primary btn-sm px-5 fs-sm" style="font-family: 'Tajawal';" href="{{ route('ar.resumes') }}"> اطلب
                    الان</a>
                </div>
            </div>
            </div>
        </div>
    </section>
      
      
      
    <!-- Top offers (carousel)-->
    @if ($posts->count() > 0)
        <section class="container mb-5 pb-md-4">
            <div class="d-flex align-items-center justify-content-between mb-3 ardir">
                <h2 class="h3 mb-0 cv-head">أعلى السير الذاتية</h2><a class="btn btn-link fw-normal p-0" href="{{ route('ar.resumes') }}">مشاهدة الكل <i class="fi-arrow-long-left ms-2"></i></a>
            </div>
            <div class="tns-carousel-wrapper tns-controls-outside-xxl tns-nav-outside tns-nav-outside-flush mx-n2">
                <div class="tns-carousel-inner row gx-4 mx-0 pt-3 pb-4" data-carousel-options="{&quot;items&quot;: 4, &quot;responsive&quot;: {&quot;0&quot;:{&quot;items&quot;:1},&quot;500&quot;:{&quot;items&quot;:2},&quot;768&quot;:{&quot;items&quot;:3},&quot;992&quot;:{&quot;items&quot;:4}}}">
                    <!-- Item-->
                    @foreach ($posts as $post)
                        @php
                            $total_exp = array_sum(explode(',',$post->experience));
                            $getAge = (date('Y') - date('Y',strtotime($post->dob)));
                        @endphp
                        <div class="col-sm-6 col-xl-4">
                            <div class="card shadow-sm card-hover border-0 h-100">
                                <div class="tns-carousel-wrapper card-img-top card-img-hover box-thumb">
                                    <a class="img-overlay" href="{{ route('ar.fullresume',$post->id) }}"></a>
                                    <div class="position-absolute start-0 top-0 pt-3 ps-3"><span class="d-table badge bg-success mb-1">تم التحقق</span><span class="d-table badge bg-info">جديد</span></div>
                                    <div class="content-overlay end-0 top-0 pt-3 pe-3">
                                        <button class="btn btn-icon btn-light btn-xs text-primary rounded-circle" type="button" data-bs-toggle="tooltip" data-bs-placement="left" title="أضف إلى قائمة الامنيات"><i class="fi-heart"></i></button>
                                    </div>
                                    @if ($post->photo_file != '')
                                        <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="Image">
                                    @else
                                        <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Image">
                                    @endif
                                </div>
                                @php
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
                                <div class="text-center name-candidate py-3 ardir">
                                    <div class="card-body position-relative pb-3">
                                        <h2 class="mb-2 cv-head">@if($post->arcand_name != '') {{ $post->arcand_name }} @else {{ $post->cand_name }} @endif</h2>
                                        <p class="fw-bold"><i class="fi-calendar mt-n1 me-1 lead align-middle"></i> {{ $getAge.' سنوات من العمر' }}</p>
                                        <div class="fw-bold">
                                            {{-- <i class="fi-briefcase mt-n1 me-2 lead align-middle opacity-70"></i>@if($total_exp != 0) {{ 'سنة '.$total_exp }} @else أعذب @endif --}}
                                            <i class="fi-briefcase mt-n1 me-2 lead align-middle opacity-70"></i> {{ $expper }}
                                            @if ($post->ar_name != '')
                                                <i class="fi-user mt-n1 me-2 lead align-middle opacity-70 mx-3"></i> {{ $post->ar_name }}
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <a class="btn btn-primary btn-sm ms-2 mb-3 px-5" href="{{ route('ar.fullresume',$post->id) }}"><i class="fi-cart me-2"></i> احجز الآن</a>
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