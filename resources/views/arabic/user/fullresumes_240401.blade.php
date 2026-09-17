@extends('layout.user.arabic.layout')

@section('title', $website['company_name_ar'] ?? 'قمر انترناشيونال')

@section('page-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.min.css">
    <style>
        .box-slide{
            width: 460px;
            height: 259px;
            background-color: rgb(218, 218, 218);
        }
        .box-slide img{
            object-fit: contain;
            width: 100%;
            height: 100%;
        }
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
        .swal2-icon {
            margin: 0 auto 0!important;
        }
        .swal2-icon .swal2-icon-content {
        display: ruby-text!important;
        }
        
    </style>
@endsection

@section('content')
    @php
        if (Auth::check()) {
            $userbkc = App\Models\Booking::where('user_id','=',Auth::user()->id)->where('booking_status','!=',2)->count();
        }
    @endphp
    <section class="container mt-5 mb-lg-5 mb-4 pt-5 pb-lg-5">  
        <!-- Breadcrumb-->
        <nav class="mb-3 pt-md-3 ardir" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ar.welcome') }}">منزل</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ar.resumes') }}">سيرة ذاتية</a></li>
                <li class="breadcrumb-item active" aria-current="page">تفاصيل المرشح</li>
            </ol>
        </nav>
        <div class="row gy-5 pt-lg-2">
            <!-- Sidebar with details-->
            <div class="col-lg-5">
                <div class="d-flex flex-column">
                    <!-- Carousel with slides count-->
                    @if ($post->photo_file !='' || $post->pass_file !='' || $post->lic_file !='' || $post->video_link != ''|| $post->trade_test_video_link != '' || $video_id != '')
                    <div class="order-lg-1 order-2">
                        <div class="tns-carousel-wrapper">
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
                                    <div>
                                        <div class="ratio ratio-16x9">
                                            <iframe width="1131" height="636" src="{{ $post->video_link }}"
                                              title="House driver jobs in gulf" frameborder="0"
                                              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                              allowfullscreen></iframe>
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
                                    <img src="https://img.youtube.com/vi/{{ $video_id[4]}}/default.jpg" alt="Video Thumbnail" width="100" >
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
                    {{-- <div class="order-lg-2 order-1">
                        <h1 class="h2 mb-2 cv-head">@if($post->arcand_name != '') {{ $post->arcand_name }} @else {{ '---' }} @endif</h1>
                        <ul class="d-flex mb-4 pb-lg-2 list-unstyled">
                            <li class="me-3 pe-3 border-end">
                                <b class="me-1"></b><i class="fi-briefcase mt-n1 lead align-middle text-muted"></i> @if($total_exp != 0) {{ $total_exp.' سنين' }} @else أعذب @endif
                            </li>
                            <li class="me-3 pe-3 border-end">
                                <b class="me-1"></b><i class="fi-car mt-n1 lead align-middle text-muted"></i> @if(isset($job_type)) {{ $job_type->ar_name }} @else {{ '---' }} @endif
                            </li>
                        </ul>
                    </div> --}}
                </div>

                <!-- Changes on 22-08-2023 Start -->
                <div class="card mb-2 ardir">
                    <div class="card-body">
                        <div class="row ">
                            <div class="col-md-12">
                                <h1 class="h2">@if($post->arcand_name != '') {{ $post->arcand_name }} @else {{ '---' }} @endif</h1>
                            </div>
                        </div>
                        @php
                            if($post->gulfexperience == 1){
                                $expEng = 'التجربة الهندية ';
                            }elseif($post->gulfexperience == 2){
                                $expEng = 'سابق في الخارج ';
                            }else{
                                $expEng = '';
                            }
                        @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <li class="arexplsal"><i class="fi-briefcase"></i> <b>تجربة</b></li>
                                <li class="arexplsal">@if($total_exp != 0) {{ $total_exp.' سنين' }} @else أعذب @endif</li>
                            </div>
                            <div class="col-md-6">
                                <li class="arexplsal"><i class="fi-car"></i> <b>التقدم بطلب للحصول</b></li>
                                <li class="arexplsal">@if($post->jobtype_id != '') {{ $expEng.''.$job_type->ar_name }} @else {{ '---' }} @endif</li>
                            </div>
                        </div>

                        @php
                            $expected_location = implode(",",$myexpwp);
                        @endphp
                        <div class="row">
                            <div class="col-6">
                                <li class="arexplsal"><i class="fi-briefcase"></i> <b>مكان العمل المتوقع</b></li>
                                <li class="arexplsal">{{ $expected_location }}</li>
                            </div>
                            <div class="col-6">
                                <li class="arexplsal"><i class="fi-cash"></i> <b>الراتب المتوقع</b></li>
                                <li class="arexplsal">{{ $post->exp_sal.' الريال السعودي' }}</li>
                            </div>
                        </div>

                    </div>
                </div>
                
                

                @if ($post->cv_execute == 1 && $post->cv_execute_file != '')
                    @if (Auth::check())
                        <a class="btn btn-sm btn-primary mb-3" href="{{ asset('admin/assets/images/pdf/'.$post->cv_execute_file) }}" target="_blank"><i class="fi-download px-2"></i> تحميل السيرة الذاتية</a>                                
                    @else
                        <button type="button" class="btn btn-sm btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#signin-modal"><i class="fi-download px-2"></i> تحميل السيرة الذاتية</button>
                    @endif                                
                @endif

                @if (Auth::check())
                    @php
                        $candBkc = DB::table('bookings')->where('cand_id','=',$post->id)->where('booking_status','!=',2)->where('user_id','=',Auth::user()->id)->count();
                    @endphp

                    @if ($candBkc  == 0)
                        @if ($webconfig->booking_panel == 0)
                            <button type="button" class="btn btn-sm btn-success mb-3" data-bs-toggle="modal"
                                @if (Auth::user()->status == 1)
                                    @if($userbkc < $max_limit->max_booking_limit) 
                                        data-bs-target="#modal2Large" 
                                    @else 
                                        data-bs-target="#bkerror" 
                                    @endif
                                @else
                                    data-bs-target="#errorProfile"
                                @endif    
                            >
                                <i class="fi-cart px-2"></i> احجز الآن
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-success mb-3" id="mybutton" data-bs-toggle="modal"
                                @if (Auth::user()->status == 1)
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
                                @else
                                    data-bs-target="#errorProfile2"
                                @endif
                            >
                            <i class="fi-cart px-2"></i> احجز الآن 
                            </button>
                        @endif
                    @else
                        <a href="#" class="btn btn-sm btn-info">تم التعاقد معه بالفعل!</a>
                        <a href="{{ route('myorder') }}" class="btn btn-sm btn-success">عرض الحالة</a>
                    @endif

                @else
                    <button type="button" class="btn btn-sm btn-success mb-3" data-bs-toggle="modal" href="#signin-modal"><i class="fi-cart px-2"></i> احجز الآن </button>

                @endif
                

                <div class="card ardir">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h5>رسوم الخدمة والمغادرة</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mt-n2 mb-0">
                                            <li class="mt-2 mb-0">تكلفة الخدمة</li>
                                            <li class="mt-2 mb-0"><b>55000 الريال السعودي </b></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mt-n2 mb-0">
                                            <li class="mt-2 mb-0">رحيل</li>
                                            <li class="mt-2 mb-0"><b>15 Days </b></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                
                <!-- Changes on 22-08-2023 End -->

                @if($partner_count->count() == 1)
                    <div id="modalLarge3" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">لقد اخترت المرشح التالي</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ url('ar/resumes/details/booking/4') }}" method="POST" id="bookingsubForm4">
                                    @csrf
                                    <div class="modal-body text-dark">
                                        <input type="hidden" name="cand_id4" id="cand_id4" value="{{ $post->id }}"> <!-- Fix: Changed cand_id2 to cand_id4 -->
                                        <input type="hidden" name="user_id4" id="user_id4" value="@if(Auth::check()){{ Auth::user()->id }} @endif">
                                        <input type="hidden" name="partner_id4" id="partner_id4" value="{{$partner_count->partner_id}}">
                                        <p><b>اسم المرشح:</b> {{ $post->cand_name }}</p>
                                        <div class="d-flex"></div>
                                        <p><b>مكتب التوظيف:</b> <span class="recname4">{{$partner_count->portal_add_disp_only}}</span><span class="px-2 recaddr"></span></p>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="agreedsal4" class="form-check-label">{{ $post->exp_sal }} هل تقدم مرتب</label> <!-- Fix: Changed id and name to agreedsal4 -->
                                                <input type="checkbox" class="form-check-input" id="agreedsal4" name="agreedsal4"> <!-- Fix: Changed id and name to agreedsal4 -->
                                                <span id="errorToShow4"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="worklocation4" class="form-label">?هل تقدم موقع العمل المتوقع</label> <!-- Fix: Changed id and name to worklocation4 -->
                                                <select name="worklocation4" class="form-select" id="worklocation4"> <!-- Fix: Changed id and name to worklocation4 -->
                                                    <option value="">يختار</option>
                                                    @foreach ($expworkcities as $expworkcity1)
                                                        <option value="{{ $expworkcity1->id }}">{{ $expworkcity1->arname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm" id="submitButton4">Confirm Order</button>
                                        {{-- <button type="button" class="btn btn-primary btn-sm" id="booking_sub4">Submit</button> --}}
                                        {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalthank4">Submit</button> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>      
                @endif

                @if(isset($checkCand))
                    <div id="modalLarge2" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">لقد قمت باختيار المرشح التالي:</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ url('ar/resumes/details/booking/3') }}" method="POST" id="bookingsubForm3">
                                    @csrf
                                    <div class="modal-body text-dark">
                                        <input type="hidden" name="cand_id3" id="cand_id3" value="{{ $post->id }}"> <!-- Fix: Changed cand_id2 to cand_id3 -->
                                        <input type="hidden" name="user_id3" id="user_id3" value="@if(Auth::check()){{ Auth::user()->id }} @endif">
                                        <input type="hidden" name="partner_id3" id="partner_id3" value="{{$checkCand->partner_id}}">
                                        <p><b>اسم المرشح :</b> {{ $post->cand_name }}</p>
                                        <div class="d-flex"></div>
                                        {{-- <p><b>مكتب التوظيف : </b> <span class="recname3">{{$checkCand->partner->portal_ar_add_disp_only	}}</span><span class="px-2 recaddr"></span></p> --}}

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="agreedsal3" class="form-check-label">مرتب؟ {{ $post->exp_sal }}هل تقدم</label> <!-- Fix: Changed id and name to agreedsal3 -->
                                                <input type="checkbox" class="form-check-input" id="agreedsal3" name="agreedsal3"> <!-- Fix: Changed id and name to agreedsal3 -->
                                                <span id="errorToShow3"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="worklocation3" class="form-label">هل توفر موقع العمل المتوقع؟</label> <!-- Fix: Changed id and name to worklocation3 -->
                                                <select name="worklocation3" class="form-select" id="worklocation3"> <!-- Fix: Changed id and name to worklocation3 -->
                                                    <option value="">يختار</option>
                                                    @foreach ($expworkcities as $expworkcity1)
                                                        <option value="{{ $expworkcity1->id }}">{{ $expworkcity1->arname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">يلغي</button>
                                        <button type="submit" class="btn btn-primary btn-sm" id="submitButton3">أكد الطلب</button>
                                        {{-- <button type="button" class="btn btn-primary btn-sm" id="booking_sub3">Submit</button> --}}
                                        {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalthank3">Submit</button> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div id="modalLarge" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-center cv-head">اختر أقرب مكتب استقدام</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body modal-pad">
                                    <div class="overflow-auto">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="px-sm-5">مكتب التوظيف</th>
                                                    <th scope="col" class="text-center">مسافة</th>
                                                    <th scope="col" class="text-center">يختار</th>
                                                </tr>
                                            </thead>
                                            <tbody class="">
                                                @foreach ($partners as $partner)
                                                    <tr class="">
                                                        <td class="px-md-5">
                                                            {{ $partner->portal_rec_off_arname }} <br>
                                                            <span class="address-para"> {{ $partner->portal_ar_add_disp_only }} </span>
                                                        </td>

                                                        <td class="text-center pt-5 pt-md-4">100 كم</td>
                                                        <td class="text-center pt-5 pt-md-4">
                                                            <input type="radio" id="office{{ $partner->partner_id }}" name="partner_id" value="{{ $partner->partner_id }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer d-flex">
                                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">يلغي</button>
                                    <button type="button" class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#modalpad">الخطوة التالية<i class="fi-arrow-right px-2"></i> </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                                                    
            </div>
            <aside class="col-lg-7">
                <div class="ps-lg-5">
                    

                    <div class="card mb-4 ardir">
                        <div class="card-body">
                            <h5 class="mb-0 pb-3 cv-head"><i class="fi-user opacity-75"></i> تفاصيل شخصية</h5>
                            <div class="row">
                                <div class="col-md-3 col-xs-6 col-sm-6 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>الاسم الكامل</b></li>
                                        <li class="mt-2 mb-0">@if($post->arcand_name != '') {{ $post->arcand_name }} @else {{ '---' }} @endif</li>
                                    </ul>
                                </div>
                                <div class="col-md-3 col-xs-6 col-sm-6 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2  mb-0"><b>عمر</b></li>
                                        <li class="mt-2 mb-0">{{ $post->age.' سنين' }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-3 col-xs-6 col-sm-6 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>دِين</b></li>
                                        <li class="mt-2 mb-0">@if(isset($religionN)) {{ $religionN->name }} @else {{ '---' }} @endif </li>
                                    </ul>
                                </div>
                                <div class="col-md-3 col-xs-6 col-sm-6 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>الحالة الاجتماعية</b></li>
                                        <li class="mt-2 mb-0">@if($post->ar_marital_status != '') {{ $post->ar_marital_status }} @else {{ '---' }} @endif  </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-xs-6 col-sm-6 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>جنسية</b></li>
                                        <li class="mt-2 mb-0">@if(isset($nation)) {{ $nation->arname }} @else {{ '---' }} @endif </li>
                                    </ul>
                                </div>
                                <div class="col-md-3 col-xs-6 col-sm-6 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>منطقة</b></li>
                                        <li class="mt-2 mb-0">@if(isset($region)) {{ $region->arname }} @else {{ '---' }} @endif</li>
                                    </ul>
                                </div>
                                <div class="col-md-3 col-xs-6 col-sm-6 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>مكان الميلاد</b></li>
                                        {{-- <li class="mt-2 mb-0">@if(isset($plob)) {{ $plob->arname }} @else {{ '---' }} @endif</li> --}}
                                        <li class="mt-2 mb-0">@if($post->plb_text != '') {{ $post->plb_text }} @else {{ '---' }} @endif</li>
                                    </ul>
                                </div>
                                <div class="col-md-3 col-xs-6 col-sm-6 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>لغة</b></li>
                                        <li class="mt-2 mb-0">@if($post->ar_language != '') {{ $post->ar_language }}  @else {{ '---' }} @endif</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Work Expereience -->
                            <h5 class="mb-0 pb-3 cv-head"><i class="fi-briefcase opacity-75"></i> الخبرة العملية</h5>
                            @if ($post->experience != 0)
                                @php
                                    $exps = explode(',',$post->experience);
                                    $exprof = explode(',',$post->proff_id);
                                    $expcont = explode(',',$post->expcountry_id);
                                    $expcity = explode(',',$post->expcity_id);
                                    $expcitytext = explode(',',$post->expcity_id_text);
                                    $iarexp = 0;
                                @endphp
                                @foreach ($exps as $key => $exp)
                                    @php
                                        $profession = \App\Models\Profession::find($exprof[$key]);
                                        $countryn = \App\Models\Country::find($expcont[$key]);
                                        
                                    @endphp
                                    <div class="row mb-3">
                                        <div class="col-3">
                                            <ul class="list-unstyled mt-n2 mb-0">
                                                <li class="mt-2 mb-0"><b>وظيفة</b></li>
                                                <li class="mt-2 mb-0">@if(isset($profession)) {{ $profession->ar_name }} @else {{ '---' }} @endif</li>
                                            </ul>
                                        </div>
    
                                        <div class="col-3">
                                            <ul class="list-unstyled mt-n2 mb-0">
                                                <li class="mt-2 mb-0"><b>فترة</b></li>
                                                <li class="mt-2 mb-0">{{ $exp.' سنين' }}</li>
                                            </ul>
                                        </div>
    
    
                                        <div class="col-3">
                                            <ul class="list-unstyled mt-n2 mb-0">
                                                <li class="mt-2 mb-0"><b>دولة</b></li>
                                                <li class="mt-2 mb-0">@if(isset($countryn)) {{ $countryn->arname }} @else {{ '---' }} @endif </li>
                                            </ul>
                                        </div>
                                        <div class="col-3">
                                            <ul class="list-unstyled mt-n2 mb-0">
                                                <li class="mt-2 mb-0"><b>مدينة</b></li>
                                                <!-- <li class="mt-2 mb-0">@if(isset($cityn)){{ $cityn->arname }} @else {{ '---' }} @endif</li>  -->
                                                <li class="mt-2 mb-0">@if((isset($countryn)))@if(count($expcitytext) > $iarexp) {{ $expcitytext[$key] }} @else {{ '---' }} @endif @endif</li>
                                            </ul>
                                        </div>
                                    </div>   
                                    
                                    @php
                                        $iarexp++;
                                    @endphp
                                @endforeach
                            
                            @else
                                <div class="row">
                                    <div class="col-3">
                                        <ul class="list-unstyled mt-n2 mb-0">
                                            <li class="mt-2 mb-0"><b>وظيفة</b></li>
                                            <li class="mt-2 mb-0">{{ '---' }}</li>
                                        </ul>
                                    </div>
    
                                    <div class="col-3">
                                        <ul class="list-unstyled mt-n2 mb-0">
                                            <li class="mt-2 mb-0"><b>فترة</b></li>
                                            <li class="mt-2 mb-0">أعذب</li>
                                        </ul>
                                    </div>
    
    
                                    <div class="col-3">
                                        <ul class="list-unstyled mt-n2 mb-0">
                                            <li class="mt-2 mb-0"><b>دولة</b></li>
                                            <li class="mt-2 mb-0">{{ '---' }}</li>
                                        </ul>
                                    </div>
                                    <div class="col-3">
                                        <ul class="list-unstyled mt-n2 mb-0">
                                            <li class="mt-2 mb-0"><b>مدينة</b></li>
                                            <li class="mt-2 mb-0">{{ '---' }}</li>
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <!-- Education and Skill -->
                            <h5 class="mb-0 pb-3 mt-3 cv-head"><i style="font-size: 24px;" class="fi-education opacity-75"></i> التعليم والمهارات</h5>
                            <div class="row">
                                <div class="col-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>تعليم</b></li>
                                        <li class="mt-2 mb-0">@if(isset($education)) {{ $education->name }} @else {{ '---' }} @endif</li>
                                    </ul>
                                </div>
                                <div class="col-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>خرائط جوجل</b></li>
                                        <li class="mt-2 mb-0">@if($post->google_map == 1) {{ 'نعم' }} @else {{ 'لا' }} @endif </li>
                                    </ul>
                                </div>

                                @php
                                    $carlist = implode(",",$mycarknwon);
                                @endphp

                                <div class="col-6">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>السيارة معروفة</b></li>
                                        <li class="mt-2 mb-0">{{ $carlist }}</li>
                                    </ul>
                                </div>

                            </div>

                            <!-- Passport Details -->
                            @php
                                $newPass = substr_replace($post->pass_no,'**',-2);
                            @endphp
                            <h5 class="mb-0 pb-3 cv-head"><i class="fi-file opacity-75"> تفاصيل جواز السفر</i> </h5>
                            <div class="row">
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>رقم جواز السفر</b></li>
                                        <li class="mt-2 mb-0"><bdi>{{ $newPass }}</bdi></li>
                                    </ul>
                                </div>
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>نوع جواز السفر</b></li>
                                        <li class="mt-2 mb-0">{{ $post->pass_type }}</li>
                                    </ul>
                                </div>
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>تاريخ المسألة</b></li>
                                        <li class="mt-2 mb-0">@if($post->doi != '') {{ date('d/m/Y',strtotime($post->doi)) }} @else {{ '---' }} @endif</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>تاريخ الانتهاء</b></li>
                                        <li class="mt-2 mb-0">@if($post->doe != '') {{ date('d/m/Y',strtotime($post->doe)) }} @else {{ '---' }} @endif </li>
                                    </ul>
                                </div>
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>مكان الإصدار</b></li>
                                        <li class="mt-2 mb-0">@if($post->poi_text != '') {{ $post->poi_text }} @else {{ '---' }} @endif </li>
                                    </ul>
                                </div>
                                <div class="col-4">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0"><b>تاريخ الميلاد</b></li>
                                        <li class="mt-2 mb-0">@if($post->dob != '') {{ date('d/m/Y',strtotime($post->dob)) }} @else {{ '---' }} @endif</li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-6">
                        
                    </div>

                    @php
                        $ref_no = $booking + 1;
                    @endphp

                    <div id="modalthank" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
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

                    <div id="bkerror" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body py-0">
                                    <div class="text-center">
                                        <p class="my-2 text-danger"><b>لقد وصلت إلى الحد الأقصى للحجز</b></p>
                                    </div>
                                    <div class="text-center py-2">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">يغلق</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="errorProfile" class="modal fade ardir" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body py-0">
                                    <div class="text-center">
                                        <p class="my-2"><b>يرجى استكمال ملف التعريف الخاص بك</b></p>
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
                                                    <label for="">رقم الهاتف المحمول <span class="text-danger">*</span></label>
                                                    <input type="text" name="mobile_no" id="mobile_no" class="form-control" placeholder="أدخل رقم الجوال ...">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="">دولة <span class="text-danger">*</span></label>
                                                    <select name="country_id" id="country_id" class="form-control">
                                                        <option value="">يختار</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="">مدينة <span class="text-danger">*</span></label>
                                                    <select name="city_id" id="city_id" class="form-control">
                                                        <option value="">يختار</option>
                                                        @foreach ($cities as $city)
                                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center py-2">
                                            <button type="submit" class="btn btn-primary btn-sm">حفظ ثم التالي</button>
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">يغلق</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="errorProfile2" class="modal fade ardir" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body py-0">
                                    <div class="text-center">
                                        <p class="my-2"><b>يرجى استكمال ملف التعريف الخاص بك</b></p>
                                        {{-- <a href="{{ route('myprofile') }}">Click here...</a> --}}
                                    </div>
                                    <form action="{{ route('booking.profile.update') }}" id="profileDetailID2" method="POST">
                                        @csrf
                                        @php
                                            $countries = DB::table('countries')->orderBy('name','ASC')->get();
                                            $cities = DB::table('cities')->orderBy('name','ASC')->get();
                                        @endphp
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="">رقم الهاتف المحمول <span class="text-danger">*</span></label>
                                                    <input type="text" name="mobile_no" id="mobile_no" class="form-control" placeholder="أدخل رقم الجوال ...">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="">دولة <span class="text-danger">*</span></label>
                                                    <select name="country_id" id="country_id" class="form-control">
                                                        <option value="">يختار</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="">مدينة <span class="text-danger">*</span></label>
                                                    <select name="city_id" id="city_id" class="form-control">
                                                        <option value="">يختار</option>
                                                        @foreach ($cities as $city)
                                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center py-2">
                                            <button type="submit" class="btn btn-primary btn-sm">حفظ ثم التالي</button>
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">يغلق</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $cities1 = DB::table('cities')->get();
                    @endphp

                    <div id="modalpad" class="modal fade ardir" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title cv-head">لقد اخترت المرشح التالي</h5>
                                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                                </div>
                                <form action="{{ url('ar/resumes/details/booking/2') }}" method="POST" id="bookingsubForm2">
                                    @csrf
                                    <div class="modal-body text-dark">
                                        <input type="hidden" name="cand_id2" id="cand_id2" value="{{ $post->id }}">
                                        <input type="hidden" name="user_id2" id="user_id2" value="@if(Auth::check()){{ Auth::user()->id }} @endif">
                                        <input type="hidden" name="partner_id2" id="partner_id2">
                                        <p><b>اسم المرشح :</b> {{ $post->cand_name }}</p>
                                        {{-- <p><b>Reference Number :</b> <span id="#refNo">{{ $ref_no }}</span></p> --}}
                                        <div class="d-flex"></div>
                                        <p><b>مكتب التوظيف : </b> <span class="recname"></span><span class="px-2 recaddr"></span></p>
    
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="agreedsal" class="form-check-label">هل تقدم  مرتب {{ $post->exp_sal }} ?</label>
                                                {{-- <label for="agreedsal2" class="form-check-label">هل تقدم {{ $post->exp_sal }} مرتب?</label> --}}
                                                <input type="checkbox" class="form-check-input" id="agreedsal2" name="agreedsal2">
                                                <span id="errorToShow1"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="worklocation2" class="form-label">هل تقدم موقع العمل المتوقع?</label>
                                                <select name="worklocation2" class="form-select" id="worklocation2">
                                                    <option value="">Select</option>
                                                    {{-- @foreach ($cities1 as $citie2)
                                                        <option value="{{ $citie2->id }}">{{ $citie2->name }}</option>
                                                    @endforeach --}}
                                                    {{-- @foreach ($expwpf as $expwpf1)
                                                        <option value="{{ $expwpf1->id }}">{{ $expwpf1->name }}</option>
                                                    @endforeach --}}
                                                    @foreach ($expworkcities as $expworkcity1)
                                                        <option value="{{ $expworkcity1->id }}">{{ $expworkcity1->arname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">يلغي</button>
                                        <button type="submit" class="btn btn-primary btn-sm">أكد الطلب</button>
                                        {{-- <button type="button" class="btn btn-primary btn-sm" id="booking_sub2">Submit</button> --}}
                                        {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalthank">Submit</button> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
    
                    

                    <div id="modal2Large" class="modal fade ardir" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title cv-head">لقد اخترت المرشح التالي</h5>
                                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                                </div>
                                <form action="{{ url('ar/resumes/details/booking') }}" method="POST" id="bookingsubForm">
                                    @csrf
                                    <div class="modal-body text-dark">
                                        <input type="hidden" name="cand_id" id="cand_id" value="{{ $post->id }}">
                                        <input type="hidden" name="user_id" id="user_id" value="@if(Auth::check()){{ Auth::user()->id }} @endif">
                                        <input type="hidden" name="hoi" id="hoi" value="Qamr International">
                                        <p><b>اسم المرشح :</b> {{ $post->cand_name }}</p>
                                        {{-- <p><b>Reference Number :</b> <span id="#refNo">{{ $ref_no }}</span></p> --}}
                                        <div class="d-flex"></div>
                                        <p><b>مكتب التوظيف : </b> Qamr International.<span class="px-2">(Mumbai, India.)</span></p>


                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="agreedsal" class="form-check-label">هل تقدم  مرتب {{ $post->exp_sal }} ?</label>
                                                <input type="checkbox" class="form-check-input" id="agreedsal" name="agreedsal">
                                                <span id="errorToShow2"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="worklocation" class="form-label">هل تقدم موقع العمل المتوقع ?</label>
                                                <select name="worklocation" class="form-select" id="worklocation">
                                                    <option value="">يختار</option>
                                                    {{-- @foreach ($cities1 as $citie1)
                                                        <option value="{{ $citie1->id }}">{{ $citie1->name }}</option>
                                                    @endforeach --}}
                                                    {{-- @foreach ($expwpf as $expwpf2)
                                                        <option value="{{ $expwpf2->id }}">{{ $expwpf2->name }}</option>
                                                    @endforeach --}}
                                                    @foreach ($expworkcities as $expworkcity2)
                                                        <option value="{{ $expworkcity2->id }}">{{ $expworkcity2->arname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">يلغي</button>
                                        <button type="submit" class="btn btn-primary btn-sm">أكد الطلب</button>
                                        {{-- <button type="button" class="btn btn-primary btn-sm" id="booking_sub">Submit</button> --}}
                                        {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalthank">Submit</button> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            


                            {{-- @if ($post->cv_file != '')
                                <a class="btn btn-sm btn-primary w-100 mb-3" href="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" target="_blank"><i class="fi-download px-2"></i>Download CV</a>
                            @endif --}}
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </section>



    <!-- Recently viewed-->

    @if ($rel_posts->count() > 0)
        <section class="container mb-5 pb-md-4 d-none d-sm-block">
            <div class="d-flex align-items-center justify-content-between ardir mb-3">
                <h2 class="h3 mb-0 cv-head">السيرة الذاتية ذات الصلة</h2><a class="btn btn-link fw-normal p-0" href="{{ route('ar.resumes') }}">مشاهدة الكل<i class="fi-arrow-long-left ms-2"></i></a>
            </div>
            <div class="tns-carousel-wrapper tns-controls-outside-xxl tns-nav-outside tns-nav-outside-flush mx-n2">
                <div class="tns-carousel-inner row gx-4 mx-0 pt-3 pb-4" data-carousel-options="{&quot;items&quot;: 4, &quot;responsive&quot;: {&quot;0&quot;:{&quot;items&quot;:1},&quot;500&quot;:{&quot;items&quot;:2},&quot;768&quot;:{&quot;items&quot;:3},&quot;992&quot;:{&quot;items&quot;:4}}}">
                    <!-- Item-->
                    @foreach ($rel_posts as $rel_post)
                        @php
                            $total_exp = array_sum(explode(',',$post->experience));
                            if($total_exp != 0){
                                if ($total_exp < 1) {
                                    $expper = $total_exp." سنة ";
                                }else{
                                    $expper = $total_exp." سنين ";
                                }
                            }else{
                                $expper = 'أعذب';
                            }
                        @endphp
                        <div class="col-sm-6 col-xl-4">
                            <div class="card shadow-sm card-hover border-0 h-100">
                                <div class="tns-carousel-wrapper card-img-top card-img-hover box-thumb">
                                    <a class="img-overlay" href="{{ route('ar.fullresume',$rel_post->id) }}"></a>
                                    <div class="position-absolute start-0 top-0 pt-3 ps-3"><span class="d-table badge bg-success mb-1">تم التحقق</span><span class="d-table badge bg-info">جديد</span></div>
                                    <div class="content-overlay end-0 top-0 pt-3 pe-3">
                                        <button class="btn btn-icon btn-light btn-xs text-primary rounded-circle" type="button" data-bs-toggle="tooltip" data-bs-placement="left" title="أضف إلى قائمة الامنيات"><i class="fi-heart"></i></button>
                                    </div>
                                    @if ($rel_post->photo_file != '')
                                        <img src="{{ asset('admin/assets/images/candidate/'.$rel_post->photo_file) }}" alt="Image">
                                    @else
                                        <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Image">
                                    @endif
                                </div>
                                <div class="text-center name-candidate py-3 ardir">
                                    <div class="card-body position-relative pb-3">
                                        <h2 class="mb-2 cv-head">{{ $rel_post->cand_name }}</h2>
                                        <div class="fw-bold">
                                            <i class="fi-briefcase mt-n1 me-2 lead align-middle opacity-70"></i> {{ $expper }}
                                            @if ($rel_post->arname != '')
                                            <i class="fi-user mt-n1 me-2 lead align-middle opacity-70 mx-3"></i> {{ $rel_post->arname }}
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <a class="btn btn-primary btn-sm ms-2 mb-3 px-5" href="{{ route('ar.fullresume',$rel_post->id) }}"><i class="fi-cart me-2"></i> احجز الآن</a>
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
    

 
    <script>
        $(document).ready(function(){
            $('#booking_sub2').on('click',function(){
                let cand_id = $('#cand_id2').val();
                let partner_id = $('#partner_id2').val();
                let user_id = $('#user_id2').val();

                var _token = $('meta[name="csrf-token"]').attr('content');

                // console.log(_token);

                jQuery.ajax({
                    url: "{{ url('ar/resumes/details/booking/2') }}",
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
                    url: "{{ url('ar/resumes/details/booking') }}",
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
  <!--   <script>
        $(document).ready(function(){
            $('#booking_sub4').on('click',function(){
               
                let cand_id = $('#cand_id4').val();
                let partner_id = $('#partner_id4').val();
                let user_id = $('#user_id4').val();
                var _token = $('meta[name="csrf-token"]').attr('content');
                // console.log(_token);

                jQuery.ajax({
                    url: "{{ url('ar/resumes/details/booking/4') }}",
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
</script> -->

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
                        required: "الرجاء إدخال رقم الهاتف المحمول",
                        digits: "الرجاء إدخال رقم فقط"
                    },
                    country_id:{
                        required: "الرجاء تحديد الدولة"
                    },
                    city_id:{
                        required: "الرجاء تحديد المدينة"
                    }
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
                        required: "الرجاء إدخال رقم الهاتف المحمول",
                        digits: "الرجاء إدخال رقم فقط"
                    },
                    country_id:{
                        required: "الرجاء تحديد الدولة"
                    },
                    city_id:{
                        required: "الرجاء تحديد المدينة"
                    }
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
                //     $( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
                // },
                // unhighlight: function (element, errorClass, validClass) {
                //     $( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
                // }
            });

            $('#profileDetailID2').ajaxForm({
                complete: function (xhr) {
                    toastr.options.timeOut = 20000;
                    toastr.success(xhr.responseJSON);

                    $('#errorProfile2').modal('hide');
                    $('#modalLarge').modal('show');

                }
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
                    url: "{{ url('ar/resumes/details/booking/3') }}",
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
                        $('#booking_sub3').prop('disabled',true);
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
            $('#booking_sub4').on('click',function(){
                let cand_id = $('#cand_id4').val();
                let partner_id = $('#partner_id4').val();
                let user_id = $('#user_id4').val();

                var _token = $('meta[name="csrf-token"]').attr('content');

                // console.log(_token);

                jQuery.ajax({
                    url: "{{ url('ar/resumes/details/booking/4') }}",
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
                        $('.recname').text(data.rec_off_name+'.');
                        $('.recaddr').text('('+data.city+', '+data.country+')');
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
            },
            messages:{
                agreedsal:"يرجى التحقق من تقديم الراتب!",
                worklocation:{
                    required: "الرجاء تحديد المدينة",
                    remote: "هذا المرشح غير مؤهل للمدينة المختارة ، يرجى تجربة مرشحين مختلفين"
                },
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
            // highlight: function ( element, errorClass, validClass ) {
            //     $( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
            // },
            // unhighlight: function (element, errorClass, validClass) {
            //     $( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
            // }
        });

        $('#bookingsubForm').ajaxForm({
            complete: function (xhr) {
                // toastr.options.timeOut = 20000;
                // toastr.success(xhr.responseJSON);

                // $('#errorProfile').modal('hide');
                // $('#modal2Large').modal('show');

                Swal.fire({
                    title: "! نجاح",
                    text: xhr.responseJSON,
                    icon: "success",
                    confirmButtonText: 'نعم',
                    customClass:{
                        confirmButton: 'btn btn-success btn-sm',
                        
                    },
                    buttonsStyling: false
                }).then(function(){
                    window.location = "{{ route('ar.myorder') }}";
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
            },
            messages:{
                agreedsal2:"يرجى التحقق من تقديم الراتب!",
                worklocation2:{
                    required: "الرجاء تحديد المدينة",
                    remote: "هذا المرشح غير مؤهل للمدينة المختارة ، يرجى تجربة مرشحين مختلفين"
                },
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

     /*    $('#bookingsubForm2').ajaxForm({
            complete: function (xhr) {
                // toastr.options.timeOut = 20000;
                // toastr.success(xhr.responseJSON);

                // $('#errorProfile').modal('hide');
                // $('#modal2Large').modal('show');

                Swal.fire({
                    title: "! نجاح",
                    text: xhr.responseJSON,
                    confirmButtonText: 'نعم',
                    icon: "success",
                    customClass:{
                        confirmButton: 'btn btn-primary btn-sm',
                        
                    },
                    buttonsStyling: false

                }).then(function(){
                    window.location = "{{ route('ar.myorder') }}";
                });

            }
        });

    }); */

    $('#bookingsubForm2').ajaxForm({
                complete: function (xhr) {

                $('#modalpad .modal-body').hide();
                $('#modalpad .modal-title').hide();
                $('#modalpad .modal-footer').hide();

                var successMessage = $('<div class="swal2-icon swal2-success swal2-icon-show"><div class="swal2-icon-content">✓</div></div><br><h2 class="swal2-title" id="swal2-title" style="display: block;">نجاح!</h2> <br><div class="swal2-html-container" id="swal2-html-container">' + xhr.responseJSON + '</div><br><div class="text-center"><button class="swal2-confirm swal2-styled btn btn-sm btn-primary mb-2" type="button" style="display: inline-block;" aria-label="OK">نعم</button></div>');
                $('#modalpad .modal-content').append(successMessage);
                    
                    $('.swal2-confirm').on('click', function() {
                    location.reload();
                    });
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
                },
                messages:{
                agreedsal3:"يرجى التحقق من تقديم الراتب!",
                worklocation3:{
                    required: "الرجاء تحديد المدينة",
                    remote: "هذا المرشح غير مؤهل للمدينة المختارة ، يرجى تجربة مرشحين مختلفين"
                },
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
                beforeSubmit: function(arr, $form, options) {
                // Disable the submit button to prevent multiple submissions
                $('#submitButton3').prop('disabled', true);
                },
                complete: function (xhr) {
                    // Hide modal content
                    $('#modalLarge2 .modal-body').hide();
                    $('#modalLarge2 .modal-title').hide();
                    $('#modalLarge2 .modal-footer').hide();
                    
                    // Create success message
                  // Create success message
                    var successMessage3 = $('<div class="swal2-icon swal2-success swal2-icon-show"><div class="swal2-icon-content">✓</div></div><br><h2 class="swal2-title" id="swal2-title" style="display: block;">نجاح!</h2> <br><div class="swal2-html-container" id="swal3-html-container">' + xhr.responseJSON + '</div><br><div class="text-center"><button class="swal3-confirm swal2-styled btn btn-sm btn-primary mb-2" type="button" style="display: inline-block;" aria-label="OK"><i class="fi fi-br-check-circle"></i> نعم</button></div>');


                    // Append success message to modal content
                    $('#modalLarge2 .modal-content').append(successMessage3);

                    // Reload the page on OK button click
                    $('.swal3-confirm').on('click', function() {
                        location.reload();
                    });
                }
            });
        });
</script>

<script>
        $(document).ready(function(){
            $('#bookingsubForm4').validate({
                rules:{
                    agreedsal4:"required",
                    worklocation4:{
                        required: true,
                        remote: {
                            type: "POST",
                            url: '{{ url("resumes/details/get/expcitywork4") }}',
                            data: {
                                city_id: function(){
                                    return $('#worklocation4').val();
                                },
                                cand_id: function(){
                                    return $('input[name="cand_id4"]').val(); // Fix: Change cand_id2 to cand_id4
                                },
                                _token: "{{ csrf_token() }}"
                            }
                        }
                    },
                },
                messages:{
                agreedsal4:"يرجى التحقق من تقديم الراتب!",
                worklocation4:{
                    required: "الرجاء تحديد المدينة",
                    remote: "هذا المرشح غير مؤهل للمدينة المختارة ، يرجى تجربة مرشحين مختلفين"
                },
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
                beforeSubmit: function(arr, $form, options) {
                // Disable the submit button to prevent multiple submissions
                $('#submitButton4').prop('disabled', true);
                },
                complete: function (xhr) {
                    // Hide modal content
                    $('#modalLarge3 .modal-body').hide();
                    $('#modalLarge3 .modal-title').hide();
                    $('#modalLarge3 .modal-footer').hide();
                    
                    // Create success message
                  // Create success message
                    var successMessage4 = $('<div class="swal2-icon swal2-success swal2-icon-show"><div class="swal2-icon-content">✓</div></div><br><h2 class="swal2-title" id="swal2-title" style="display: block;">نجاح!</h2> <br><div class="swal2-html-container" id="swal4-html-container">' + xhr.responseJSON + '</div><br><div class="text-center"><button class="swal4-confirm swal2-styled btn btn-sm btn-primary mb-2" type="button" style="display: inline-block;" aria-label="OK"><i class="fi fi-br-check-circle"></i> نعم</button></div>');


                    // Append success message to modal content
                    $('#modalLarge3 .modal-content').append(successMessage4);

                    // Reload the page on OK button click
                    $('.swal4-confirm').on('click', function() {
                        location.reload();
                    });
                }
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
@endsection