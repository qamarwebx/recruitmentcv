@extends('layout.user.arabic.layout')

@section('title', $website['company_name_ar'] ?? 'قمر انترناشيونال')

@section('content')
    <!-- Breadcrumb-->
    <div class="container mt-5 mb-md-4 pt-5">
        <nav class="mb-3 pt-md-3 ardir" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('ar.welcome') }}">منزل</a></li>
            <li class="breadcrumb-item active" aria-current="page">عن</li>
            </ol>
        </nav>
    </div>
    <!-- Page header-->

    <!-- Page header-->
    <section class="container mb-5 pb-2">
        <div class="row align-items-center justify-content-center">
            <!-- Hero content-->
            <div class="col-lg-4 col-md-5 col-sm-9 order-md-1 order-2 text-end ardir">
                <h1 class="mb-4 cv-head">معلومات عنا</h1>
                <p class="mb-4 pb-3 fs-lg">

                    @if (isset($frontwebsite) && $frontwebsite->about_us_ar != '')
                    {{ $frontwebsite->about_us_ar }}
                    @else
                    تقدم QAMR International حلولاً كاملة لإدارة الموارد البشرية وتعالج مواهبها الحيوية من خلال توفير عملية توظيف شاملة. نحن نعمل كجسر بين عملائنا والمرشحين.                        
                    @endif


                </p>
                <a class="btn btn-lg btn-primary" href="{{ route('ar.contact') }}">اتصل بنا</a>
            </div>
            <!-- Hero carousel-->
            <div class="col-lg-7 col-md-6 offset-md-1 col-12 order-md-2 order-1">
                <div class="tns-carousel-wrapper tns-controls-static tns-nav-outside">
                    <div class="tns-carousel-inner" data-carousel-options="{&quot;loop&quot;: true, &quot;gutter&quot;: 16}">
                    <div><img class="rounded-3" src="{{ asset('user/img/real-estate/about/hero/01.webp') }}" alt="Carousel image"></div>
                    <div><img class="rounded-3" src="{{ asset('user/img/real-estate/about/hero/02.webp') }}" alt="Carousel image"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Why choose us?-->
    <section class="container mb-2 mb-xl-5 pb-lg-4">
        <h2 class="h3 mb-4 cv-head ardir">لماذا أخترتنا؟</h2>
        <!-- Features carousel-->
        <div class="tns-carousel-wrapper tns-nav-outside">
            <div class="tns-carousel-inner" data-carousel-options="{&quot;loop&quot;: false, &quot;controls&quot;: false, &quot;responsive&quot;: {&quot;0&quot;:{&quot;items&quot;:1, &quot;gutter&quot;: 16},&quot;500&quot;:{&quot;items&quot;:2, &quot;gutter&quot;: 20},&quot;768&quot;:{&quot;items&quot;:3, &quot;gutter&quot;: 24}}}">
            <!-- Feature slide-->
                <div class="ardir">
                    <div class="card border-0">
                        <div class="card-body">
                            <svg class="mb-3" xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#fd5631">
                                <path d="M13.585 21.456a10.416 10.416 0 1 0 20.832 0c0-5.76-4.656-10.464-10.416-10.464s-10.416 4.704-10.416 10.464zm18.096 0c0 4.224-3.456 7.68-7.68 7.68s-7.68-3.456-7.68-7.68 3.456-7.68 7.68-7.68 7.68 3.408 7.68 7.68zm-10.225-.96a1.36 1.36 0 0 0-1.92 0 1.36 1.36 0 0 0 0 1.92l2.352 2.352c.24.24.624.384.96.384s.72-.144.96-.384l4.512-4.512a1.36 1.36 0 0 0 0-1.92 1.36 1.36 0 0 0-1.92 0l-3.552 3.552-1.392-1.392zM42 10.512C29.568 5.568 24.96 1.584 24.912 1.536c-.528-.48-1.296-.48-1.824 0C23.04 1.584 18.48 5.52 6 10.512c-.528.192-.864.72-.864 1.248 0 24.576 17.424 34.464 18.192 34.848.192.096.432.192.672.192a1.2 1.2 0 0 0 .672-.192c.72-.384 18.192-10.272 18.192-34.848 0-.528-.336-1.056-.864-1.248zM24 43.824C20.928 41.808 8.304 32.352 7.872 12.72 17.328 8.88 22.128 5.664 24 4.32c1.872 1.392 6.672 4.56 16.128 8.4C39.744 32.352 27.072 41.808 24 43.824z"></path>
                            </svg>
                            <h3 class="h5 card-title pb-1 cv-head">مهمة</h3>
                            <p class="card-text">لتوفير حلول توظيف لا مثيل لها تساعد عملائنا على أن يصبحوا أكثر إنتاجية وربحية</p>
                        </div>
                    </div>
                </div>
                <!-- Feature slide-->
                <div class="ardir">
                    <div class="card border-0">
                        <div class="card-body">
                            <svg class="mb-3" xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#fd5631">
                                <path d="M39.976 40.416l-5.667-26.529c-.098-.44-.391-.831-.782-1.026L20.531 6.217l-.928-4.202c-.195-.831-.977-1.368-1.808-1.173s-1.368.977-1.172 1.808l.879 4.202-9.136 11.335c-.293.342-.391.831-.293 1.27l5.618 26.529c.195.831.977 1.368 1.808 1.173l23.304-4.934a1.59 1.59 0 0 0 1.173-1.808zm-23.597 3.469L11.2 19.554l7.182-8.843.635 2.931c.195.831.977 1.368 1.808 1.172s1.368-.977 1.172-1.808l-.635-2.931 10.162 5.179 5.179 24.33-20.324 4.299zm7.963-17.149l-2.052.44a1.54 1.54 0 0 1-1.857-1.27c-.146-.831.44-1.612 1.27-1.759l2.052-.44c.684-.146 1.368.195 1.71.782.098.244.342.342.586.293l1.954-.44c.293-.049.489-.391.391-.684-.733-2.003-2.736-3.225-4.837-3.029l-.391-1.954c-.049-.293-.342-.489-.635-.391l-1.954.391c-.293.049-.489.342-.391.635l.391 1.954c-2.247.733-3.664 3.029-3.127 5.374.586 2.492 3.078 4.006 5.521 3.469l2.003-.44c.831-.195 1.661.293 1.857 1.124.244.879-.293 1.71-1.172 1.905l-2.101.44c-.684.147-1.368-.195-1.71-.782-.098-.195-.342-.342-.586-.293l-1.954.44c-.342.049-.488.391-.391.684.733 2.003 2.736 3.224 4.837 3.029l.391 1.905c.049.293.342.489.635.391l1.954-.391c.293-.049.489-.342.391-.635l-.391-1.905c2.247-.733 3.664-3.029 3.127-5.374-.538-2.492-3.029-4.006-5.521-3.469z"></path>
                            </svg>
                            <h3 class="h5 card-title pb-1 cv-head">رؤية</h3>
                            <p class="card-text">أن نكون معروفين عالميًا بشريك استشارات الموارد البشرية المؤثر والفعال والمبتكر</p>
                        </div>
                    </div>
                </div>
                <!-- Feature slide-->
                <div class="ardir">
                    <div class="card border-0">
                        <div class="card-body">
                            <svg class="mb-3" xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#fd5631">
                                <path d="M13.585 21.456a10.416 10.416 0 1 0 20.832 0c0-5.76-4.656-10.464-10.416-10.464s-10.416 4.704-10.416 10.464zm18.096 0c0 4.224-3.456 7.68-7.68 7.68s-7.68-3.456-7.68-7.68 3.456-7.68 7.68-7.68 7.68 3.408 7.68 7.68zm-10.225-.96a1.36 1.36 0 0 0-1.92 0 1.36 1.36 0 0 0 0 1.92l2.352 2.352c.24.24.624.384.96.384s.72-.144.96-.384l4.512-4.512a1.36 1.36 0 0 0 0-1.92 1.36 1.36 0 0 0-1.92 0l-3.552 3.552-1.392-1.392zM42 10.512C29.568 5.568 24.96 1.584 24.912 1.536c-.528-.48-1.296-.48-1.824 0C23.04 1.584 18.48 5.52 6 10.512c-.528.192-.864.72-.864 1.248 0 24.576 17.424 34.464 18.192 34.848.192.096.432.192.672.192a1.2 1.2 0 0 0 .672-.192c.72-.384 18.192-10.272 18.192-34.848 0-.528-.336-1.056-.864-1.248zM24 43.824C20.928 41.808 8.304 32.352 7.872 12.72 17.328 8.88 22.128 5.664 24 4.32c1.872 1.392 6.672 4.56 16.128 8.4C39.744 32.352 27.072 41.808 24 43.824z"></path>
                            </svg>
                            <h3 class="h5 card-title pb-1 cv-head">قيم</h3>
                            <p class="card-text">خلق بيئة إنتاجية ذاتية الاستدامة توفر أفضل الخبرات وفرص النمو لعملائنا وموظفينا على حدٍ سواء</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- How it works-->
    <section class="container mb-5 pb-2 pb-lg-4">
        <div class="row gy-4">
            <div class="col-md-5 col-12">
                <img class="d-block mx-auto" src="{{ asset('user/img/real-estate/illustrations/find.svg') }}" alt="Illustration">
            </div>
            <div class="col-lg-6 offset-lg-1 col-md-7 col-12 ardir">
                <h2 class="h3 mb-lg-5 mb-sm-4 cv-head">خدمات ذات قيمة مضافة للعملاء مع تجربة عملاء على مستوى عالمي</h2>
                <div class="steps steps-vertical">
                    <div class="step active">
                        <div class="step-progress"><span class="step-number">1</span></div>
                        <div class="step-label me-4 text-end">
                            <h3 class="h5 mb-2 pb-1 cv-head">تقديم الخدمة قبل المصلحة الذاتية:</h3>
                            <p class="mb-0">نحن نهتم بكل شيء قبل زيارتك حتى تتمكن من التركيز على عملك ونموك من خلال توفير وقتك الثمين.</p>
                        </div>
                    </div>
                    <div class="step active">
                        <div class="step-progress"><span class="step-number">2</span></div>
                        <div class="step-label me-4 text-end">
                            <h3 class="h5 mb-2 pb-1 cv-head">لطالما كنا في طليعة تقديم خدمات ذات قيمة مضافة:</h3>
                            <p class="mb-0">تزويد عملائنا بجميع المزايا والراحة</p>
                        </div>
                    </div>
                    <div class="step active">
                        <div class="step-progress"><span class="step-number">3</span></div>
                        <div class="step-label me-4 text-end">
                            <h3 class="h5 mb-2 pb-1 cv-head">تجربة مبهجة:</h3>
                            <p class="mb-0">مع الأخذ في الاعتبار الرحلة الشاملة من خلال بناء علاقة طويلة الأمد مع عملائنا.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
      
    <!-- ======= Gallery Section ======= -->
    <section id="gallery" class="gallery">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <div class="row main-after">
                    <div class="col-md-6 col-6 right-header">
                        <h2 class="cv-head">Gallery</h2>
                    </div>
                    <div class="col-md-6 col-6 left-header">
                        <h2 class="text-end cv-head">صالة عرض</h2>
                    </div>
                </div>
            </div>
            <div class="row galery-iltem" data-aos="fade-left ">
                <div class="item col-md-3 mb-3">
                    <a href="{{ asset('user/img/gallery/team.jpg') }}" class="fancybox" target="_blank" data-fancybox="gallary1">
                        <img class="img-fluid" src="{{ asset('user/img/gallery/team.jpg') }}">
                    </a>
                </div>

                <div class="item col-md-3 col-6 mb-3">
                    <a href="{{ asset('user/img/gallery/Recp.jpg') }}" class="fancybox" target="_blank" data-fancybox="gallary1">
                        <img class="img-fluid" src="{{ asset('user/img/gallery/Recp.jpg') }}">
                    </a>
                </div>

                <div class="item col-md-3 col-6 mb-3">
                    <a href="{{ asset('user/img/gallery/team3.JPG') }}" class="fancybox" target="_blank" data-fancybox="gallary1">
                        <img class="img-fluid" src="{{ asset('user/img/gallery/team3.JPG') }}">
                    </a>
                </div>

                <div class="item col-md-3 col-6 mb-3">
                    <a href="{{ asset('user/img/gallery/team4.JPG') }}" class="fancybox" target="_blank" data-fancybox="gallary1">
                        <img class="img-fluid" src="{{ asset('user/img/gallery/team4.JPG') }}">
                    </a>
                </div>

                <div class="item col-md-3 col-6 mb-3">
                    <a href="{{ asset('user/img/gallery/team5.JPG') }}" class="fancybox" target="_blank" data-fancybox="gallary1">
                        <img class="img-fluid" src="{{ asset('user/img/gallery/team5.JPG') }}">
                    </a>
                </div>

                <div class="item col-md-3 col-6 mb-3">
                    <a href="{{ asset('user/img/gallery/team8.JPG') }}" class="fancybox" target="_blank" data-fancybox="gallary1">
                        <img class="img-fluid" src="{{ asset('user/img/gallery/team8.JPG') }}">
                    </a>
                </div>

                <div class="item col-md-3 col-6 mb-3">
                    <a href="{{ asset('user/img/gallery/team6.JPG') }}" class="fancybox" target="_blank" data-fancybox="gallary1">
                        <img class="img-fluid" src="{{ asset('user/img/gallery/team6.JPG') }}">
                    </a>
                </div>

                <div class="item col-md-3 col-6 mb-3">
                    <a href="{{ asset('user/img/gallery/team7.JPG') }}" class="fancybox" target="_blank" data-fancybox="gallary1">
                        <img class="img-fluid" src="{{ asset('user/img/gallery/team7.JPG') }}">
                    </a>
                </div>
            </div>
        </div>
    </section><!-- End Gallery Section -->


@endsection