@extends('layout.user.layout')

@section('title','My Order')

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
  </style>
@endsection

@section('content')
    <div class="container pt-md-5 pb-lg-4 mt-5 mb-sm-2">

        <div class="row">

            <aside class="col-lg-4 col-md-5 pe-xl-4 mb-5">
                @include('layout.user.myorder')
            </aside>
            <!-- Content-->

            <div class="col-lg-8 col-md-7">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h1 class="h2 mb-0">My Orders</h1>
                    {{-- @if (count($posts) > 0)
                        <a class="fw-bold text-decoration-none" href="#"><i class="fi-trash mt-n1 me-2"></i>Delete all</a>
                    @endif --}}
                </div>
                {{-- <span class="fw-bold mb-4 pt-1">Here you can see your orders and edit them easily.</span> --}}
                <p class="pt-1 mb-4 fw-bold">Here you can see your orders and edit them easily.</p>

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
                                    <h3 class="h6 mb-2 fs-base">{{ $post->cand_name }}</a></h3>
                                    
                                    {{-- <p class="mb-2 fs-sm text-muted">@if($post->address != '') {{ $post->address }} @else {{ '---' }} @endif</p> --}}
                                    <div class="fw-bold d-flex text-center">
                                        <i class="fi-briefcase ms-1 fs-lg text-muted pe-2"></i><span class="d-inline-block me-4 fs-sm"> @if($total_exp != 0) {{ $total_exp.' Year' }} @else {{ 'Fresher' }} @endif</span>
                                        <i class="fi-car ms-1 fs-lg text-muted pe-2"></i>
                                        <span class="d-inline-block me-4 fs-sm">@if($post->job_type != '') {{ $post->job_type }} @else {{ '---' }} @endif</span>
                                        <i class="fi-cash ms-1 fs-lg text-muted pe-2"></i>
                                        <span class="d-inline-block me-4 fs-sm">{{ $post->exp_sal }}</span>
                                    </div>
                                    <div class="border-top pt-5 pb-2 mt-3 text-nowrap">
                                        {{-- <button type="button" class="btn btn-primary btn-xs" data-bs-toggle="offcanvas" data-bs-target="#view-candidate" data-id="{{ $post->cand_id }}">View Candidate</button> --}}
                                        <a href="{{ route('fullresume',$post->cand_id) }}" class="btn btn-primary btn-xs">View Candidate</a>
                                        <button type="button" class="btn btn-primary btn-xs" data-bs-toggle="offcanvas" data-bs-target="#demo-switcher">Order Status</button>
                                        <button class="cancelBooking btn btn-primary btn-xs" data-id="{{ $post->id }}" type="button">Cancel</button>
                                    </div>
                                </div>

                            </div>
                        </div>                        
                    @endforeach                
                @else
                    <p class="pt-1 mb-4">No Booking Order Found.</p>
                @endif

            </div>
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
                      console.log(data);
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
          title: 'Are you sure?',
          text: "Do you want to cancel this candidate?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, cancel it!',
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
                  title: 'Success!',
                  text: 'Candidate is canceled!',
                  icon: 'Success',
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