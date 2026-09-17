@extends('layout.user.layout')

@section('title', $website['company_name'] ?? 'Qamr International')

@section('content')

    <!-- Page content-->
    <!-- Breadcrumb-->
    <div class="container mt-5 mb-md-4 pt-5">
        <nav class="mb-3 pt-md-3" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Contact us</li>
          </ol>
        </nav>
    </div>
    <!-- Hero-->
    <section class="container mb-5 pb-2 pb-md-4 pb-lg-5">
        <div class="row align-items-md-start align-items-center gy-4">
            <div class="col-lg-5 col-md-6">
                <div class="mx-md-0 mx-auto mb-md-5 mb-4 pb-md-4 text-md-start text-center" style="max-width: 416px;">
                    <h1 class="mb-4">Get in touch!</h1>
                    <p class="mb-0 fs-lg text-muted">@if(isset($frontwebsite) && $frontwebsite->contact_us_eng != '') {{ $frontwebsite->contact_us_eng }} @else Fill out the form and out team will try to get back to you within 24 hours. @endif</p>
                </div>
                <img class="d-block mx-auto" src="{{ asset('user/img/real-estate/illustrations/contact.svg') }}" alt="Illustration">
            </div>
            <div class="col-md-6 offset-lg-1">
                <div class="card border-0 bg-secondary p-sm-3 p-2">
                    <div class="card-body m-1">
                        <form class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label class="form-label" for="c-name">Full Name</label>
                                <input class="form-control form-control-lg" id="c-name" type="text" required>
                                <div class="invalid-tooltip mt-1">Please, enter your name</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="c-email">Your Email</label>
                                <input class="form-control form-control-lg" id="c-email" type="email" required>
                                <div class="invalid-tooltip mt-1">Please, enter your email</div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="c-message">Message</label>
                                <textarea class="form-control form-control-lg" id="c-message" rows="4" placeholder="Leave your message" required></textarea>
                                <div class="invalid-tooltip mt-1">Please, type your message</div>
                            </div>
                            <div class="pt-sm-2 pt-1">
                                <button class="btn btn-lg btn-primary w-sm-auto w-100" type="submit">Submit form</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact cards-->
    <section class="container mb-5 pb-2 pb-md-4 pb-lg-5">
    <div class="row g-4">
        <!-- Item-->
        <div class="col-md-4">
            <a class="icon-box card card-hover h-100" href="mailto:example@email.com">
                <div class="card-body">
                    <div class="icon-box-media text-primary rounded-circle shadow-sm mb-3"><i class="fi-mail custom-fi"></i></div>
                    <span class="d-block mb-1 text-body">Drop us a line</span>
                    <h3 class="h4 icon-box-title mb-0 opacity-90">@if(isset($frontwebsite) && $frontwebsite->contact_us_email != '') {{ $frontwebsite->contact_us_email }} @else info@qamrintl.com @endif</h3>
                </div>
            </a>
        </div>
        <!-- Item-->
        <div class="col-md-4">
            <a class="icon-box card card-hover h-100" href="tel:4065550120">
                <div class="card-body">
                    <div class="icon-box-media text-primary rounded-circle shadow-sm mb-3"><i class="fi-device-mobile custom-fi"></i></div>
                    <span class="d-block mb-1 text-body">Call us any time</span>
                    <h3 class="h4 icon-box-title mb-0 opacity-90">@if(isset($frontwebsite) && $frontwebsite->contact_us_phone != '') {{ $frontwebsite->contact_us_phone }} @else +91 9004448485 @endif</h3>
                </div>
            </a>
        </div>
        <!-- Item-->
        <div class="col-md-4">
            <a class="icon-box card card-hover h-100" href="#">
                <div class="card-body">
                    <div class="icon-box-media text-primary rounded-circle shadow-sm mb-3"><i class="fi-map-pin custom-fi"></i></div>
                    <span class="d-block mb-1 text-body">Our Office</span>
                    <h3 class="h4 icon-box-title mb-0 opacity-90">@if(isset($frontwebsite) && $frontwebsite->contact_us_location != '') {{ $frontwebsite->contact_us_location }} @else Mumbai, India @endif</h3>
                </div>
            </a>
        </div>
    </div>
    </section>
  
  
  
    <!-- ======= Team Section ======= -->
    <section id="branch" class="branch">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <div class="row">
                    <div class="col-md-6 col-6 right-header">
                        <h2 class="">Location</h2>
                    </div>
                    <!-- <div class="col-md-6 col-6 left-header">
                        <h2 class="text-end">موقعنا</h2>
                    </div> -->
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row" data-aos="fade-left ">
                <div class="col-lg col-md col-12 gx-4">
                    <div class="member" data-aos="zoom-in" data-aos-delay="400">
                        <div class="member-info-first">
                            <img src="{{ asset('user/img/ksa.png') }}" class="img-fluid" alt="">
                            <h4>Al Riyadh and Al Qassim</h4>
                            <!-- <span>الرياض و القصيم</span> -->
                        </div>
                    </div>
                </div>

                <div class="col-lg col-md col-6">
                    <div class="member" data-aos="zoom-in" data-aos-delay="100">
                        <div class="member-info">
                            <img src="{{ asset('user/img/Mumbai.png') }}" class="img-fluid" alt="">
                            <h4>Mumbai</h4>
                            <!-- <span>مومباي</span> -->
                        </div>
                    </div>
                </div>

                <div class="col-lg col-md col-6">
                    <div class="member" data-aos="zoom-in" data-aos-delay="200">
                        <div class="member-info">
                            <img src="{{ asset('user/img/delhi.png') }}" class="img-fluid" alt="">
                            <h4>New Delhi</h4>
                            <!-- <span>نيو دلهي</span> -->
                        </div>
                    </div>
                </div>

                <div class="col-lg col-md col-6">
                    <div class="member" data-aos="zoom-in" data-aos-delay="300">
                        <div class="member-info">
                            <img src="{{ asset('user/img/lucknow.png') }}" class="img-fluid" alt="">
                            <h4>Lucknow </h4>
                            <!-- <span>لكناو</span> -->
                        </div>
                    </div>
                </div>

                <div class="col-lg col-md col-6">
                    <div class="member" data-aos="zoom-in" data-aos-delay="400">
                        <div class="member-info">
                            <img src="{{ asset('user/img/hydr.png') }}" class="img-fluid" alt="">
                            <h4>Hyderabad </h4>
                            <!-- <span>حيدر أباد</span> -->
                        </div>
                    </div>
                </div>
        </div>
    </div>
    </section><!-- End branch Section -->

@endsection