@extends('layout.user.layout')

@section('title', $website['company_name'] ?? 'Qamr International')

@section('page-style')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('user/intl-tel-input-master/build/css/intlTelInput.css') }}">

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
        .explsal{
            list-style: none
        }
        .expmar{
            margin-left: 10px;
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
            <div class="col-md-6">
                <nav class="mb-3 pt-md-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('resumes') }}">Resumes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Candidate Details</li>                
                    </ol>
                </nav>
            </div>
            <div class="col-md-6">
                <p class="mb-3 pt-md-3 fw-bold float-end">CV</p>
            </div>
        </div>

        
        <div class="row gy-5 pt-lg-2">
            <div class="col-lg-5">
                <div class="d-flex flex-column">
                    <!-- Carousel with slides count-->
                    @if ($post->photo_file !='' || $post->pass_file !='' || $post->lic_file !='' || $post->video_link != '')
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
                                  <div class="box-slide">
                                        <div class="ratio ratio-16x9">
                                            <iframe width="200" height="50" src="{{ $post->video_link }}" frameborder="0" allowfullscreen></iframe>
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
                        <h1 class="h2 mb-2">{{ $post->cand_name }}</h1>
                        <ul class="d-flex mb-4 pb-lg-2 list-unstyled">
                            <li class="me-3 pe-3 border-end">
                                <b class="me-1"></b><i class="fi-briefcase mt-n1 lead align-middle text-muted"></i> @if($total_exp != 0) {{ $total_exp }} Years Exp  @else Fresher @endif
                            </li>
                            <li class="me-3 pe-3 border-end">
                                <b class="me-1"></b><i class="fi-car mt-n1 lead align-middle text-muted"></i> @if($post->jobtype_id != '') {{ $proffesion2->eng_name }} @else {{ '---' }} @endif
                            </li>
                        </ul>
                    </div> --}}
                </div>

                {{-- @if (isset($overtext) && $overtext->status == 1)
                <h2 class="h5">Overview</h2>
                <div class="text-justice">
                    <p class="mb-4 pb-2">{{ $overtext->text_desc }}</p>
                </div>                    
                @endif --}}

                <!-- Changes on 22-08-2023 Start -->
                @php
                    $expected_location = implode(",",$myexpwp);
                @endphp
                 <!-----================= Exprience ============--------->
                 <div class="card border-0 mb-4">
                    <div class="card-body">
                        <h5 class="mb-0 pb-3"><i class="fi-briefcase opacity-75"></i> Employment Experience</h5>
                        @if ($post->experience != 0)
                        @php
                            $exps = explode(',',$post->experience);
                            $exprof = explode(',',$post->proff_id);
                            $expcont = explode(',',$post->expcountry_id);
                            $expcity = explode(',',$post->expcity_id);
                            $expcitytext = explode(",",$post->expcity_id_text);
                            $iexcp = 0;
                        @endphp
                        @foreach ($exps as $key => $exp)
                            <div class="row mb-3">
                                <div class="col-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Job</li>
                                        <li class="mt-2 mb-0"><b>@if(isset($proffesion2)) {{ $proffesion2->eng_name }} @else {{ '---' }} @endif </b></li>
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
                                        <li class="mt-2 mb-0"><b>@if(isset($nation)) {{ $nation->name }} @else {{ '---' }} @endif </b></li>
                                    </ul>
                                </div>
                                <div class="col-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">City</li>
                                        {{-- <li class="mt-2 mb-0"><b>@if(isset($cityn)){{ $cityn->name }} @else {{ '---' }} @endif</b></li> --}}
                                        <li class="mt-2 mb-0"><b>@if(count($expcitytext) > $iexcp) {{ $expcitytext[$key] }} @else {{ '---' }} @endif</b></li>
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
                                    <li class="mt-2 mb-0"><b>@if($post->poi_text != '') {{ $post->poi_text }} @else {{ '---' }} @endif </b></li>
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

                {{-- <div class="card border-0 bg-secondary my-4">
                    <div class="card-body">
                        <h5 class="mb-0 pb-3"><i class="fi-file opacity-75"></i> Passport Details</h5>
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
                                    <li class="mt-2 mb-0"><b>@if($post->poi_text != '') {{ $post->poi_text }} @else {{ '---' }} @endif </b></li>
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
                </div> --}}

            </div>
            <!-- Sidebar with details-->
            <aside class="col-lg-7">
                <div class="ps-lg-5">
                    <div class="d-flex align-items-center justify-content-between mb-3">
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
                    </div>
                    {{-- <h2 class="h3 mb-4 pb-2">@if($total_exp != 0) Experienced {{ $post->job_type }} @else Fresher {{ $post->job_type }} @endif</h2> --}}
                    {{-- @php
                        $expected_location = implode(",",$myexpwp);
                    @endphp --}}

                    {{-- <div class="row mb-2 expmar">
                        <div class="col-md-12">
                            <h1 class="h2">{{ $post->cand_name }}</h1>
                        </div>
                    </div>

                    <div class="row mb-2 expmar">
                        <div class="col-md-6">
                            <li class="explsal"><i class="fi-briefcase"></i> Experience</li>
                            <li class="explsal"><b>@if($total_exp != 0) {{ $total_exp }} Years Exp  @else Fresher @endif</b></li>
                        </div>
                        <div class="col-md-6">
                            <li class="explsal"><i class="fi-car"></i> Applied For</li>
                            <li class="explsal"><b>@if($post->jobtype_id != '') {{ $jobtype.''.$proffesion2->eng_name }} @else {{ '---' }} @endif</b></li>
                        </div>
                    </div>

                    <div class="row mb-2 expmar">
                        <div class="col-md-6">
                            <li class="explsal"><i class="fi-briefcase"></i> Expected Work place</li>
                            <li class="explsal"><b>{{ $expected_location }}</b></li>
                        </div>
                        <div class="col-md-6">
                            <li class="explsal"><i class="fi-cash"></i> Expected Salary</li>
                            <li class="explsal"><b>{{ $post->exp_sal }}</b></li>
                        </div>
                    </div> --}}
                    <!---=============== Personal Details ==========---------->
                    <div class="card-body" style="padding: 10px 0; margin-bottom:10px;">
                        <div class="row mb-2 expmar">
                            <div class="col-md-12">
                                <h1 class="h2">{{ $post->cand_name }}</h1>
                            </div>
                        </div>
                        <div class="row mb-2 expmar">
                            <div class="col-md-6">
                                <li class="explsal"><i class="fi-briefcase"></i> Experience</li>
                                <li class="explsal new"><b>@if($total_exp != 0) {{ $total_exp }} Years Exp  @else Fresher @endif</b></li>
                            </div>
                            <div class="col-md-6">
                                <li class="explsal"><i class="fi-car"></i> Applied For</li>
                                <li class="explsal"><b>@if($post->jobtype_id != '') {{ $jobtype.''.$proffesion2->eng_name }} @else {{ '---' }} @endif</b></li>
                            </div>
                        </div>
        
                        <div class="row mb-2 expmar">
                            <div class="col-md-6">
                                <li class="explsal"><i class="fi-briefcase"></i> Expected Work place</li>
                                <li class="explsal"><b>{{ $expected_location }}</b></li>
                            </div>
                            <div class="col-md-6">
                                <li class="explsal"><i class="fi-cash"></i> Expected Salary</li>
                                <li class="explsal"><b>{{ $post->exp_sal }}</b></li>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 mb-4">
                        <div class="card-body">
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

                                <div class="col-md-3 col-xs-4 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Place of Birth</li>
                                        {{-- <li class="mt-2 mb-0"><b>@if(isset($plob)) {{ $plob->name }} @else {{ '---' }} @endif</b></li> --}}
                                        <li class="mt-2 mb-0"><b>@if($post->plb_text != '') {{ $post->plb_text }} @else {{ '---' }} @endif</b></li>
                                    </ul>
                                </div>

                                <div class="col-md-3 col-xs-4 mb-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Language</li>
                                        <li class="mt-2 mb-0"><b>{{ $post->lang_known }}</b></li>
                                    </ul>
                                </div>
                                <h5 class="mb-0 pb-3 mt-3">Education and Skills</h5>
                        <div class="row">
                            <div class="col-3">
                                <ul class="list-unstyled mt-n2 mb-0">
                                    <li class="mt-2 mb-0">Education</li>
                                    <li class="mt-2 mb-0"><b>@if(isset($education)) {{ $education->name }} @else {{ '---' }} @endif </b></li>
                                </ul>
                            </div>
                            <div class="col-3">
                                <ul class="list-unstyled mt-n2 mb-0">
                                    <li class="mt-2 mb-0">Google Map</li>
                                    <li class="mt-2 mb-0"><b>@if($post->google_map == 1) {{ 'Yes' }} @else {{ 'No' }} @endif </b></li>
                                </ul>
                            </div>

                            {{-- <div class="col-3">
                                <ul class="list-unstyled mt-n2 mb-0">
                                    <li class="mt-2 mb-0">Period</li>
                                    <li class="mt-2 mb-0"><b>@if($total_exp != 0) {{ $total_exp }} Years @else Fresher @endif</b></li>
                                </ul>
                            </div> --}}


                            {{-- <div class="col-3">
                                <ul class="list-unstyled mt-n2 mb-0">
                                    <li class="mt-2 mb-0">Country</li>
                                    <li class="mt-2 mb-0"><b>@if($post->expcountry_id) {{ implode(',',$mycont) }} @else {{ '---' }} @endif </b></li>
                                </ul>
                            </div> --}}
                            {{-- <div class="col-3">
                                <ul class="list-unstyled mt-n2 mb-0">
                                    <li class="mt-2 mb-0">City</li>
                                    <li class="mt-2 mb-0"><b>Taif</b></li>
                                </ul>
                            </div> --}}
                            @php
                                $carlist = implode(",",$mycarknwon);
                            @endphp
                            <div class="col-6">
                                <ul class="list-unstyled mt-n2 mb-0">
                                    <li class="mt-2 mb-0">Vehicle Known</li>
                                    <li class="mt-2 mb-0"><b>{{ $carlist }}</b></li>
                                </ul>
                            </div>


                        </div>

                            </div>
                            {{-- <div class="row mt-4">
                                <div class="col-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Natinality</li>
                                        <li class="mt-2 mb-0"><b>@if(isset($nation)) {{ $nation->name }} @else {{ '---' }} @endif </b></li>
                                    </ul>
                                </div>
                                <div class="col-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Region</li>
                                        <li class="mt-2 mb-0"><b>@if(isset($region)) {{ $region->name }} @else {{ '---' }} @endif</b></li>
                                    </ul>
                                </div>

                                <div class="col-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Place of Birth</li>
                                        <li class="mt-2 mb-0"><b>@if(isset($plob)) {{ $plob->name }} @else {{ '---' }} @endif</b></li>
                                    </ul>
                                </div>

                                <div class="col-3">
                                    <ul class="list-unstyled mt-n2 mb-0">
                                        <li class="mt-2 mb-0">Language</li>
                                        <li class="mt-2 mb-0"><b>{{ $post->lang_known }}</b></li>
                                    </ul>
                                </div>
                            </div> --}}
                            <div class="row mt-4">
                                
                                
                            </div>
                        </div>
                    </div>
                   

                    <div class="col-md-9">
                        
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
                                        <p class="my-2 text-danger"><b>You have reached the maximum limit of booking.</b></p>
                                    </div>
                                    <div class="text-center py-2">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="errorProfile" class="modal fade" tabindex="-1" role="dialog">
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

                    <div id="errorProfile2" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body py-0">
                                    <div class="text-center">
                                        <p class="my-2"><b>Please complete your profile.</b></p>
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
                                                    <input type="hidden" name="phone_code" class="phone_code">
                                                    <label for="mobile_no" class="form-label">Mobile Number <span class="text-danger">*</span></label><br>
                                                    <input type="text" name="mobile_no" id="mobile_no" class="form-control telephone" placeholder="Enter Mobile Number...">
                                                    <span class="output"></span>
                                                </div>
                                                <div class="col-md-12">
                                                    <input type="checkbox" class="form-check-input" value="1" name="whatsapp_notification" id="agree-for-notification" checked>
                                                    <label for="agree-for-notification" class="form-check-label">I agree to receive whatsapp messages on my number</label>
                                                </div>
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
                    @endphp
                   
                    <div id="modalpad" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">You have selected the following candidate: </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ url('resumes/details/booking/2') }}" method="POST" id="bookingsubForm2">
                                    @csrf
                                    <div class="modal-body text-dark">
                                        <input type="hidden" name="cand_id2" id="cand_id2" value="{{ $post->id }}">
                                        <input type="hidden" name="user_id2" id="user_id2" value="@if(Auth::check()){{ Auth::user()->id }} @endif">
                                        <input type="hidden" name="partner_id2" id="partner_id2">
                                        <p><b>Candidate Name :</b> {{ $post->cand_name }}</p>
                                        {{-- <p><b>Reference Number :</b> <span id="#refNo">{{ $ref_no }}</span></p> --}}
                                        <div class="d-flex"></div>
                                        <p><b>Recruitment Office : </b> <span class="recname"></span><span class="px-2 recaddr"></span></p>
    
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="agreedsal2" class="form-check-label">Are you provide {{ $post->exp_sal }} salary?</label>
                                                <input type="checkbox" class="form-check-input" id="agreedsal2" name="agreedsal2">
                                                <span id="errorToShow1"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="worklocation2" class="form-label">Are you provide expected work location?</label>
                                                <select name="worklocation2" class="form-select" id="worklocation2">
                                                    <option value="">Select</option>
                                                    {{-- @foreach ($cities1 as $citie2)
                                                        <option value="{{ $citie2->id }}">{{ $citie2->name }}</option>
                                                    @endforeach --}}
                                                    {{-- @foreach ($expwpf as $expwpf1)
                                                        <option value="{{ $expwpf1->id }}">{{ $expwpf1->name }}</option>
                                                    @endforeach --}}
                                                    @foreach ($expworkcities as $expworkcity1)
                                                        <option value="{{ $expworkcity1->id }}">{{ $expworkcity1->name }}</option>
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
                   
    
                    

                    <div id="modal2Large" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">You have selected the following candidate: </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ url('resumes/details/booking') }}" method="POST" id="bookingsubForm">
                                    @csrf
                                    <div class="modal-body text-dark">
                                        <input type="hidden" name="cand_id" id="cand_id" value="{{ $post->id }}">
                                        <input type="hidden" name="user_id" id="user_id" value="@if(Auth::check()){{ Auth::user()->id }} @endif">
                                        <input type="hidden" name="hoi" id="hoi" value="Qamr International">
                                        <p><b>Candidate Name :</b> {{ $post->cand_name }}</p>
                                        {{-- <p><b>Reference Number :</b> <span id="#refNo">{{ $ref_no }}</span></p> --}}
                                        <div class="d-flex"></div>
                                        <p><b>Recruitment Office : </b> Qamr International.<span class="px-2">(Mumbai, India.)</span></p>
                                    
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="agreedsal" class="form-check-label">Are you provide {{ $post->exp_sal }} salary?</label>
                                                <input type="checkbox" class="form-check-input" id="agreedsal" name="agreedsal">
                                                <span id="errorToShow2"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="worklocation" class="form-label">Are you provide expected work location?</label>
                                                <select name="worklocation" class="form-select" id="worklocation">
                                                    <option value="">Select</option>
                                                    {{-- @foreach ($cities1 as $citie1)
                                                        <option value="{{ $citie1->id }}">{{ $citie1->name }}</option>
                                                    @endforeach --}}
                                                    {{-- @foreach ($expwpf as $expwpf2)
                                                        <option value="{{ $expwpf2->id }}">{{ $expwpf2->name }}</option>
                                                    @endforeach --}}
                                                    @foreach ($expworkcities as $expworkcity2)
                                                        <option value="{{ $expworkcity2->id }}">{{ $expworkcity2->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Confirm Order</button>
                                        {{-- <button type="button" class="btn btn-primary btn-sm" id="booking_sub">Submit</button> --}}
                                        {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalthank">Submit</button> --}}
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            @if ($post->cv_execute == 1 && $post->cv_execute_file != '')
                                @if (Auth::check())
                                    <a class="btn btn-lg btn-primary w-100 mb-3" href="{{ asset('admin/assets/images/pdf/'.$post->cv_execute_file) }}" target="_blank"><i class="fi-download px-2"></i> Download CV</a>                                
                                @else
                                    <button type="button" class="btn btn-lg btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#signin-modal"><i class="fi-download px-2"></i> Download CV</button>
                                @endif                                
                            @endif


                            {{-- @if ($post->cv_file != '')
                                <a class="btn btn-lg btn-primary w-100 mb-3" href="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" target="_blank"><i class="fi-download px-2"></i>Download CV</a>
                            @endif --}}
                        </div>
                        <div class="col-md-4">
                        @if(isset($checkCand))
                        <div id="modalLarge2" class="modal fade" tabindex="-1" role="dialog">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">You have selected the following candidate: </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ url('resumes/details/booking/3') }}" method="POST" id="bookingsubForm3">
                                                @csrf
                                                <div class="modal-body text-dark">
                                                    <input type="hidden" name="cand_id3" id="cand_id3" value="{{ $post->id }}"> <!-- Fix: Changed cand_id2 to cand_id3 -->
                                                    <input type="hidden" name="user_id3" id="user_id3" value="@if(Auth::check()){{ Auth::user()->id }} @endif">
                                                    <input type="hidden" name="partner_id3" id="partner_id3" value="{{$checkCand->partner_id}}">
                                                    <p><b>Candidate Name :</b> {{ $post->cand_name }}</p>
                                                    <div class="d-flex"></div>
                                                    <p><b>Recruitment Office : </b> <span class="recname3">{{$checkCand->partner->portal_add_disp_only}}</span><span class="px-2 recaddr"></span></p>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label for="agreedsal3" class="form-check-label">Are you providing {{ $post->exp_sal }} salary?</label> <!-- Fix: Changed id and name to agreedsal3 -->
                                                            <input type="checkbox" class="form-check-input" id="agreedsal3" name="agreedsal3"> <!-- Fix: Changed id and name to agreedsal3 -->
                                                            <span id="errorToShow3"></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="worklocation3" class="form-label">Are you providing expected work location?</label> <!-- Fix: Changed id and name to worklocation3 -->
                                                            <select name="worklocation3" class="form-select" id="worklocation3"> <!-- Fix: Changed id and name to worklocation3 -->
                                                                <option value="">Select</option>
                                                                @foreach ($expworkcities as $expworkcity1)
                                                                    <option value="{{ $expworkcity1->id }}">{{ $expworkcity1->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
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
                                    <div id="modalLarge" class="modal fade" tabindex="-1" role="dialog">
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
                                                                    <th scope="col" class="px-sm-5">Recruitment Office</th>
                                                                    <th scope="col" class="text-center">Distance</th>
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
                                                                        <td class="text-center pt-5 pt-md-4">100km</td>
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

                            @php
                                // $candBkc = DB::table('bookings')->where('cand_id','=',$post->id)->where('booking_status','!=',2)->count(); 20/06/2023
                            
                                // $candBkc = DB::table('bookings')->where('cand_id','=',$post->id)->where('booking_status','!=',2)->where('user_id','=',Auth::user()->id)->count();
                                // dd($candBkc);
                            @endphp
                            {{-- @if ($candBkc  == 0)
                                @if ($webconfig->booking_panel == 0)
                                    <button type="button" class="btn btn-lg btn-success w-100 mb-3" data-bs-toggle="modal" 
                                        @if(Auth::check() && Auth::user()->status == 1) 
                                            @if($userbkc < $max_limit->max_booking_limit) 
                                                data-bs-target="#modal2Large" 
                                            @else 
                                                data-bs-target="#bkerror" 
                                            @endif  
                                        @elseif(Auth::check() && Auth::user()->status == 0)
                                            data-bs-target="#errorProfile"
                                        @else 
                                            href="#signin-modal" 
                                        @endif> 
                                        Book Now 
                                    </button>                        
                                @else
                                    <button type="button" class="btn btn-lg btn-success w-100 mb-3" data-bs-toggle="modal" 
                                        @if(Auth::check() && Auth::user()->status == 1) 
                                            @if($userbkc < $max_limit->max_booking_limit) 
                                                data-bs-target="#modalLarge" 
                                            @else 
                                                data-bs-target="#bkerror" 
                                            @endif 
                                        @elseif(Auth::check() && Auth::user()->status == 0)
                                            data-bs-target="#errorProfile2"
                                        @else 
                                            href="#signin-modal" 
                                        @endif > 
                                        Book Now 
                                    </button>
                                @endif
                            @endif --}}

                            @if (Auth::check())
                                @php
                                    $candBkc = DB::table('bookings')->where('cand_id','=',$post->id)->where('booking_status','!=',2)->where('user_id','=',Auth::user()->id)->count();
                                @endphp

                                @if ($candBkc  == 0)
                                    @if ($webconfig->booking_panel == 0)
                                        <button type="button" class="btn btn-lg btn-success w-100 mb-3" data-bs-toggle="modal"
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
                                            <i class="fi-cart px-2"></i> Order Now
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-lg btn-success  mb-3" id="mybutton" data-bs-toggle="modal"
                                            @if (Auth::user()->status == 1)
                                                @if($userbkc < $max_limit->max_booking_limit) 
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
                                        <i class="fi-cart px-2"></i> Book Now
                                        </button>
                                    @endif
                                @else
                                    <a href="#" class="btn btn-sm btn-info">Already Hired!</a>
                                    <a href="{{ route('myorder') }}" class="btn btn-sm btn-success">View Status</a>
                                @endif

                            @else
                                <button type="button" class="btn btn-lg btn-success mb-3" data-bs-toggle="modal" href="#signin-modal"><i class="fi-cart px-2"></i> Order Now </button>
                            @endif
                            
                            <!-- <a class="btn btn-lg btn-primary w-100 mb-3" href="#form" data-bs-toggle="modal">Book Now</a> -->
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </section>



    <!-- Recently viewed-->

    @if ($rel_posts->count() > 0)
        <section class="container mb-5 pb-md-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="h3 mb-0">Related Resumes</h2><a class="btn btn-link fw-normal p-0" href="{{ route('resumes') }}">View all<i class="fi-arrow-long-right ms-2"></i></a>
            </div>
            <div class="tns-carousel-wrapper tns-controls-outside-xxl tns-nav-outside tns-nav-outside-flush mx-n2">
                <div class="tns-carousel-inner row gx-4 mx-0 pt-3 pb-4" data-carousel-options="{&quot;items&quot;: 4, &quot;responsive&quot;: {&quot;0&quot;:{&quot;items&quot;:1},&quot;500&quot;:{&quot;items&quot;:2},&quot;768&quot;:{&quot;items&quot;:3},&quot;992&quot;:{&quot;items&quot;:4}}}">
                    <!-- Item-->
                    @foreach ($rel_posts as $rel_post)
                        <div class="col-sm-6 col-xl-4">
                            <div class="card shadow-sm card-hover border-0 h-100">
                                <div class="tns-carousel-wrapper card-img-top card-img-hover box-thumb">
                                    <a class="img-overlay" href="{{ route('fullresume',$rel_post->id) }}"></a>
                                    <div class="position-absolute start-0 top-0 pt-3 ps-3"><span class="d-table badge bg-success mb-1">Verified</span><span class="d-table badge bg-info">New</span></div>
                                    <div class="content-overlay end-0 top-0 pt-3 pe-3">
                                        <button class="btn btn-icon btn-light btn-xs text-primary rounded-circle" type="button" data-bs-toggle="tooltip" data-bs-placement="left" title="Add to Wishlist"><i class="fi-heart"></i></button>
                                    </div>
                                    @if ($rel_post->photo_file != '')
                                        <img src="{{ asset('admin/assets/images/candidate/'.$rel_post->photo_file) }}" alt="Image">
                                    @else
                                        <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="image">
                                    @endif
                                </div>
                                <div class="text-center name-candidate py-3">
                                    <div class="card-body position-relative pb-3">
                                        <h2 class="mb-2">{{ $rel_post->cand_name }}</h2>
                                        <div class="fw-bold"><i class="fi-briefcase mt-n1 me-2 lead align-middle opacity-70"></i>@if($rel_post->experience != 0) {{ $rel_post->experience }} Years @else Fresher @endif <i class="fi-user mt-n1 me-2 lead align-middle opacity-70 mx-3"></i>{{ $rel_post->job_type }}</div>
                                    </div>
                                    <div>
                                        <a class="btn btn-primary btn-sm ms-2 mb-3 px-5" href="{{ route('fullresume',$rel_post->id) }}"><i class="fi-cart me-2"></i>Book Now</a>
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
    <script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>

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
                    agreedsal:"Please check the provided salary!",
                    worklocation:{
                        required: "Please select city",
                        remote: "This candidate is not eligible for selected city please try differenent candidate"
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
              
            });

            $('#bookingsubForm').ajaxForm({
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
                },
                messages:{
                    agreedsal2:"Please check the provided salary!",
                    worklocation2:{
                        required: "Please select city",
                        remote: "This candidate is not eligible for selected city please try differenent candidate"
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

            $('#bookingsubForm2').ajaxForm({
                beforeSubmit: function(arr, $form, options) {
                // Disable the submit button to prevent multiple submissions
                $('#submitButton2').prop('disabled', true);
                },
                complete: function (xhr) {

                $('#modalpad .modal-body').hide();
                $('#modalpad .modal-title').hide();
                $('#modalpad .modal-footer').hide();

                
                var successMessage = $('<div class="swal2-icon swal2-success swal2-icon-show"><div class="swal2-icon-content">✓</div></div><br><h2 class="swal2-title" id="swal2-title" style="display: block;">Success!</h2> <br><div class="swal2-html-container" id="swal2-html-container">' + xhr.responseJSON + '</div><br><div class="text-center"><button class="swal2-confirm swal2-styled btn btn-sm btn-primary mb-2" type="button" style="display: inline-block;" aria-label="OK">OK</button></div>');

                   $('#modalpad .modal-content').append(successMessage);

                    $('.swal2-confirm').on('click', function() {
                    // Refresh the page
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
                    agreedsal3:"Please check the provided salary!",
                    worklocation3:{
                        required: "Please select city",
                        remote: "This candidate is not eligible for selected city please try different candidate" // Fix: Corrected typo in message
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
                    var successMessage3 = $('<div class="swal2-icon swal2-success swal2-icon-show"><div class="swal2-icon-content">✓</div></div><br><h2 class="swal2-title" id="swal2-title" style="display: block;">Success!</h2> <br><div class="swal2-html-container" id="swal3-html-container">' + xhr.responseJSON + '</div><br><div class="text-center"><button class="swal3-confirm swal2-styled btn btn-sm btn-primary mb-2" type="button" style="display: inline-block;" aria-label="OK"><i class="fi fi-br-check-circle"></i> OK</button></div>');


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
            $('.telephone').intlTelInput({
                
                // localizedCountries: true,
                onlyCountries: ["in","sa","qa","om","kw","bh"],
                preferredCountries: [ "in","sa"],
                separateDialCode: true,
                initialCountry: ""
            }).on('countrychange',function(e,countryData){
                $('.phone_code').val(($(".telephone").intlTelInput("getSelectedCountryData").dialCode))
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