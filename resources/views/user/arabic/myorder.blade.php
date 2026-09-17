@extends('layout.user.arabic.layout')

@section('title','طلبي')

@section('page-style')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.min.css">

  <style>
    .box-thumb{
      /* width: 200px; */
      height: 180px;
      background-color: rgb(218, 218, 218);
    }
    .box-thumb img{
      object-fit: contain;
      width: 100%;
      height: 100%;
    }

    /* ============================= */
    /* MY ORDER CARD RESPONSIVE FIX */
    /* ============================= */

    /* Make horizontal card proper */
    .card-horizontal {
        display: flex;
        flex-direction: row;
        overflow: hidden;
        border-radius: 16px;
    }

    /* Image container */
    .card-horizontal .card-img-top {
        width: 260px;
        min-width: 260px;
        height: 220px;
        background: #f3f3f3;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    /* Image fit */
    .card-horizontal .card-img-top img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Card body spacing */
    .card-horizontal .card-body {
        padding: 1.5rem;
    }

    /* Buttons spacing */
    .card-horizontal .btn-xs {
        padding: 6px 14px;
        font-size: 14px;
        margin-right: 8px;
        margin-bottom: 8px;
    }

    /* ============================= */
    /* MOBILE FIX */
    /* ============================= */

    @media (max-width: 767px) {

        .card-horizontal {
            flex-direction: column;
        }

        .card-horizontal .card-img-top {
            width: 100%;
            min-width: 100%;
            height: 220px;
        }

        .card-horizontal .card-body {
            text-align: center;
        }

        .card-horizontal .fw-bold.d-flex {
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-horizontal .border-top {
            text-align: center;
        }

        .card-horizontal .btn-xs {
            width: 100%;
            margin-right: 0;
        }
    }

  </style>
@endsection

@section('content')
  <div class="container pt-md-5 pb-lg-4 mt-5 mb-sm-2">

    <div class="row">
      <div class="col-lg-8 col-md-7">
        <div class="d-flex align-items-center justify-content-between mb-3 mt-3 ardir">
            <h1 class="h2 mb-0 cv-head">طلباتي</h1>
            {{-- @if (count($posts) > 0)
                <a class="fw-bold text-decoration-none" href="#"><i class="fi-trash mt-n1 me-2"></i>Delete all</a>
            @endif --}}
        </div>
        {{-- <span class="fw-bold mb-4 pt-1">Here you can see your orders and edit them easily.</span> --}}
        <p class="pt-1 mb-4 fw-bold ardir">هنا يمكنك رؤية طلباتك وتعديلها بسهولة</p>

        @if (count($posts) > 0)
          @foreach ($posts as $post)
            @php
              $total_exp = array_sum(explode(',',$post->experience));
            @endphp
            <!-- Item-->
            <div class="card card-hover card-horizontal border-0 shadow-sm mb-4">
              {{-- <a class="card-img-top" href="" @if($post->photo_file != '') style="background-image:url({{ asset('admin/assets/images/candidate/'.$post->photo_file) }})"  @else style="background-image: url({{ asset('admin/assets/img/avatars/avatar.jpg') }});" @endif>
                  <div class="position-absolute start-0 top-0 pt-3 ps-3"></div>
              </a> --}}

              <div class="card-img-top box-thumb">
                @if ($post->photo_file != '')
                  <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="imageAvtar">
                @else
                  <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="imageAvtar">  
                @endif
              </div>

              <div class="card-body position-relative pb-3">
                  {{-- <div class="dropdown position-absolute zindex-5 top-0 end-0 mt-3 me-3">
                      <button class="btn btn-icon btn-light btn-xs rounded-circle shadow-sm" type="button" id="contextMenu1" data-bs-toggle="dropdown" aria-expanded="false"><i class="fi-dots-vertical"></i></button>
                      <ul class="dropdown-menu my-1" aria-labelledby="contextMenu1">
                          <li>
                              <button class="dropdown-item" type="button"><i class="fi-edit opacity-60 me-2"></i>Edit</button>
                          </li>
                          <li>
                              <button class="dropdown-item" type="button"><i class="fi-flame opacity-60 me-2"></i>Promote</button>
                          </li>
                          <li>
                              <button class="dropdown-item" type="button"><i class="fi-power opacity-60 me-2"></i>Deactivate</button>
                          </li>
                          <li>
                              <button class="dropdown-item cancelBooking" data-id="{{ $post->id }}" type="button"><i class="fi-trash opacity-60 me-2"></i>Cancel</button>
                          </li>
                      </ul>
                  </div> --}}
                <div class="text-center text-md-start">
                  <!-- <h3 class="h6 mb-2 fs-base cv-head">{{ $post->cand_name }}</a></h3> -->

                  <h1 class="h6 mb-2 fs-base">@if($post->arcand_name != '') {{ $post->arcand_name }} @else {{ '---' }} @endif 
                    <!-- <i class="fa fa-check-square text-primary" aria-hidden="true"></i> -->
                    <img class="pb-1" src="{{ asset('admin/assets/images/candidate/cadidate-verified.svg') }}"  width="20" alt="Thumbnail">
                    </h1>
                  {{-- <p class="mb-2 fs-sm text-muted">@if($post->address != '') {{ $post->address }} @else {{ '---' }} @endif</p> --}}
                  <div class="fw-bold d-flex text-center">
                    <i class="fi-briefcase ms-1 fs-lg text-muted pe-2"></i><span class="d-inline-block me-4 fs-sm"> @if($total_exp != 0) {{ $total_exp.' سنين' }} @else {{ 'أعذب' }} @endif</span>
                    <i class="fas fa-passport ms-1 fs-lg text-muted pe-2"></i>
                                        <span class="d-inline-block me-2 fs-sm"> {{ substr_replace($post->pass_no,'**',-2) ?? '---'  }}</span>
                                      
                    <i class="fi-car ms-1 fs-lg text-muted pe-2"></i>
                    <span class="d-inline-block me-4 fs-sm">
                      @if($post->jobtype_id != '')
                        @foreach($professions as $profession)
                          @if($post->jobtype_id == $profession->id)
                              {{ $profession->ar_name }} 
                          @endif
                        @endforeach
                      @else 
                      {{ '---' }}
                      @endif
                    </span>
                    <i class="fi-cash ms-1 fs-lg text-muted pe-2"></i>
                    <span class="d-inline-block me-4 fs-sm">{{ $post->exp_sal }}</span>
                  </div>
                  <div class="border-top pt-3 pb-1 mt-2 text-nowrap">
                    @foreach($ordstatuses as $ordstatus)
                      @if($post->ord_status_id == $ordstatus->id)
                         <p class="mb-0"><strong>حالة الطلب:</strong> {{$ordstatus->ar_status}}</p>
                      @endif
                    @endforeach
                  </div>
                  <div class="border-top pt-2 mt-3">
                      <div class="row g-2">

                          @if ($post->cv_execute == 1 && $post->cv_execute_file != '')
                              <div class="col-6 col-md-auto">
                                  <a class="btn btn-sm btn-primary w-100"
                                    href="{{ asset('admin/assets/images/pdf/'.$post->cv_execute_file) }}"
                                    download>
                                    <i class="fi-download me-1"></i> تحميل السيرة الذاتية
                                  </a>
                              </div>
                          @endif

                          <!-- عرض المرشح -->
                          <div class="col-6 col-md-auto">
                              <a href="{{ route('ar.fullresume',$post->slug_text) }}"
                                class="btn btn-primary btn-sm w-100">
                                عرض المرشح
                              </a>
                          </div>

                          <!-- حالة الطلب -->
                          <div class="col-6 col-md-auto">
                              <button type="button"
                                      class="btn btn-primary btn-sm w-100"
                                      data-bs-toggle="offcanvas"
                                      data-bs-target="#statusOffcanvas-{{ $post->id }}">
                                  حالة الطلب
                              </button>
                          </div>

                          <!-- يلغي -->
                          <div class="col-6 col-md-auto">
                              <button class="cancelBooking btn btn-danger btn-sm w-100"
                                      data-id="{{ $post->id }}"
                                      type="button">
                                  يلغي
                              </button>
                          </div>

                      </div>
                  </div>
                </div>
              </div>
            </div>                        
          @endforeach                
        @else
          <p class="pt-1 mb-4 ardir">لم يتم العثور على أمر الحجز. اختر سيرتك الذاتية <a href="{{ route('ar.resumes') }}">هنا</a></p>
        @endif
      </div>

      <aside class="col-lg-4 col-md-5 pe-xl-4 mb-5">
        @include('layout.user.arabic.myorder')
      </aside>
      <!-- Content-->
    </div>

    {{-- Order Status --}}
    <div class="offcanvas offcanvas-end" id="demo-switcher">
      <div class="offcanvas-header d-block border-bottom">
        <div class="d-flex align-items-center justify-content-between">
          <h2 class="h5 mb-0">Your Order Status</h2>
          <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
        </div>
      </div>
      <div class="offcanvas-body">
        <!-- Inline steps: Vertical -->
        <div class="steps steps-vertical">
          <div class="step active">
            <div class="step-progress">
              <span class="step-progress-end"></span>
              <span class="step-number">1</span>
            </div>
            <div class="step-label">
              <h5 class="mb-2 pb-1">Step 1</h5>
              <p class="mb-0">Congratulations Step 1 has been completed</p>
            </div>
          </div>
          <div class="step active">
            <div class="step-progress">
              <span class="step-progress-end"></span>
              <span class="step-number">2</span>
            </div>
            <div class="step-label">
              <h5 class="mb-2 pb-1">Step 2</h5>
              <p class="mb-0">Congratulations Step 2 has been completed</p>
            </div>
          </div>
          <div class="step active">
            <div class="step-progress">
              <span class="step-progress-end"></span>
              <span class="step-number">3</span>
            </div>
            <div class="step-label">
              <h5 class="mb-2 pb-1">Step 3</h5>
              <p class="mb-0">Congratulations Step 3 has been completed</p>
            </div>
          </div>
          <div class="step">
            <div class="step-progress">
              <span class="step-progress-end"></span>
              <span class="step-number">4</span>
            </div>
            <div class="step-label">
              <h5 class="mb-2 pb-1">Step 4</h5>
              <p class="mb-0">Waiting</p>
            </div>
          </div>
          <div class="step">
            <div class="step-progress">
              <span class="step-progress-end"></span>
              <span class="step-number">5</span>
            </div>
            <div class="step-label">
              <h5 class="mb-2 pb-1">Step 5</h5>
              <p class="mb-0">Waiting</p>
            </div>
          </div>
          <div class="step">
            <div class="step-progress">
              <span class="step-progress-end"></span>
              <span class="step-number">6</span>
            </div>
            <div class="step-label">
              <h5 class="mb-2 pb-1">Step 6</h5>
              <p class="mb-0">Waiting</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Candidate View --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="view-candidate" aria-labelledby="view-candidateLabel">
      <div class="offcanvas-header d-block border-bottom">
        <div class="d-flex align-items-center justify-content-between">
          <h2 class="h5 mb-0">Candidate Details</h2>
          <button class="btn-close" type="button" data-bs-dismiss="offcanvas"></button>
        </div>
      </div>
      <div class="offcanvas-body">
        <div class="row">
          <div class="col-md-12 candDetails">

          </div>
        </div>
      </div>
    </div>

  </div>
@endsection

@section('page-script')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.9/dist/sweetalert2.all.min.js"></script>

  <script>
    $(document).ready(function(){
      $('#view-candidate').on('show.bs.offcanvas',function(e){
        var cand_id = $(e.relatedTarget).data('id');
        var _token = $('meta[name="csrf-token"]').attr('content');
        // alert(_token);
          
        jQuery.ajax({
          url: "{{ route('getCandidate.detail') }}",
          method: "POST",
          type: "html",
          data:{
            "_token": _token,
            cand_id: cand_id,
          },
          success: function(data){
            // console.log(data);
            $('.candDetails').html(data.res);
          }
        });

      });
    });
  </script>

  <script>
    $(document).ready(function(){
      $('.cancelBooking').on('click',function(){
        var id = $(this).data('id');
        var _token = $('meta[name="csrf-token"]').attr('content');

        
        Swal.fire({
          title: 'هل أنت متأكد؟',
          text: "هل تريد إلغاء هذا المرشح؟",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: '! نعم ، قم بإلغائها',
          cancelButtonText: "يلغي",
          customClass: {
            confirmButton: 'btn btn-success btn-sm me-2',
            cancelButton: 'btn btn-secondary btn-sm '
          },
          buttonsStyling: false
        }).then(function(result){
          if (result.value) {
            jQuery.ajax({
              url: "{{ route('user.candidate.booking.cancel') }}",
              method: "post",
              type: "html",
              data:{
                "_token": _token,
                id: id
              },
              success: function(data){
                Swal.fire({
                  title: '! نجاح',
                  text: '! تم إلغاء المرشح',
                  icon: 'Success',
                  confirmButtonText: "نعم",
                  customClass: {
                    confirmButton: 'btn btn-success btn-sm'
                  },
                  buttonsStyling: false
                }).then(function(result){
                  location.reload();
                });
              }
            });    
          }
        });

      });
    });
  </script>

@endsection