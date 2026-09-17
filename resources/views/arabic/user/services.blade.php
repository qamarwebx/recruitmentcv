@extends('layout.user.arabic.layout')

@section('title', $website['company_name'] ?? 'Qamr International')

@section('content')
    <!-- Page container-->
    <div class="container service-content mt-5 mb-md-4 py-5">
        <!-- Title + Breadcrumb-->
        <nav class="mb-3 pt-2 pt-lg-3" aria-label="Breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Services</li>
            </ol>
        </nav>
        <h1 class="text-center">Specialized in supplying experienced drivers to Saudi Arabia</h1>
        <h1 class="text-center">متخصصين في توريد السائقين ذو خبرة للسعودية</h1>

        <div class=" row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center"><i class="fi-ticket" style="color: #0335fd; font-size: 40px;"></i>
                        <h2 class="text-center">نوفر سي فيات الايدي العامل
                        بصفه مستعرة</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                <div class="card-body text-center"><i class="fi-plane" style="color: #ffa200; font-size: 40px;"></i>
                    <h2 class="text-center">حجر تذاكر الطيران فور انتهاء الإجراءات</h2>
                </div>
                </div>
            </div>


            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                <div class="card-body text-center"><i class="fi-wallet" style="color: #ff003c; font-size: 40px;"></i>
                    <h2 class="text-center">جودة العمالة مقارنة بالاأسعار تعتبر ممتازه</h2>
                </div>
                </div>
            </div>


            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                <div class="card-body text-center"><i class="fi-car" style="color: #e361ff; font-size: 40px;"></i>
                    <h2 class="text-center">نوفر سائقين ذو خبرة عاليه في دول الخليج</h2>
                </div>
                </div>
            </div>


            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                <div class="card-body text-center"><i class="fi-clock" style="color: #47aeff; font-size: 40px;"></i>
                    <h2 class="text-center">خدمة الاستفسارات 24/7
                    </h2>
                </div>
                </div>
            </div>


            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                <div class="card-body text-center"><i class="fi-award" style="color: #5f43fd; font-size: 40px;"></i>
                    <h2 class="h5 text-center py-1 mb-0">ثقه عملائنا مكسب لنا</h2>
                </div>
                </div>
            </div>


            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                <div class="card-body text-center"><i class="fi-edit" style="color: #29cc61; font-size: 40px;"></i>
                    <h2 class="text-center">اختيار العماله في دقه عاليه</h2>
                </div>
                </div>
            </div>


            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                <div class="card-body text-center"><i class="fi-like" style="color: #ff5828; font-size: 40px;"></i>
                    <h2 class="h5 text-center">نحن نضمن 90 يوم عمل من الوصول</h2>
                </div>
                </div>
            </div>


            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                <div class="card-body text-center"><i class="fi-briefcase" style="color: #ff00dd; font-size: 40px;"></i>
                    <h2 class="text-center">الراتب 1700 ريال</h2>
                </div>
                </div>
            </div>


        </div>
    </div>

    <!-- Find property-->
    <section class="container mb-5 pb-sm-3 pb-lg-4 arb">
        <div class="bg-secondary rounded-3">
        <div class="col-md-11 col-12 offset-md-1 p-md-0 p-2 d-flex align-items-center justify-content-between">
            <div class="me-md-5 px-2" style="max-width: 526px;">
            <h2 class="mb-md-4">
                Select the right candidates <br>
                with confidence.
            </h2>
            <p class="mb-4 pb-md-3 fs-lg">لتوفير حلول توظيف لا مثيل لها تساعد عملائنا على أن يصبحوا أكثر إنتاجية وربحية
            </p><a class="btn btn-lg btn-primary" href="#"><i class="fi-search me-2"></i>Find candidate <span> ابحث عن
                المرشح</span></a>
            </div>
            <div class="col-4 d-md-block d-none align-self-end px-0"><img class="mt-n5" src="{{ asset('user/img/real-estate/about/01.svg') }}"
                width="406" alt="Cover image"></div>
        </div>
        </div>
    </section>
@endsection