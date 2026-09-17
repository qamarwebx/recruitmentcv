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

/* Responsive Button Layout */

.order-btn-group {
    justify-content: flex-start;
}

/* Desktop */
@media (min-width: 768px) {
    .order-btn-group {
        justify-content: flex-start;
    }
}

/* Mobile */
@media (max-width: 767px) {
    .order-btn-group {
        flex-direction: column;
    }

    .order-btn-group .btn {
        width: 100%;
    }
}

  </style>

<style>
#demo-switcher {
    height: auto !important;
    max-height: 60vh;
    width: 600px;
    margin: 20px auto;
    border-radius: 14px;
    left: 0;
    right: 0;
}

#demo-switcher .offcanvas-body {
    overflow-y: auto;
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
                                    <!-- <h3 class="h6 mb-2 fs-base">{{ $post->cand_name }}</a></h3> -->
                                    <h1 class="h6 mb-2 fs-base">@if($post->cand_name != '') {{ $post->cand_name }} @else {{ '---' }} @endif  
                                        <!-- <i class="fa fa-check-square text-primary" aria-hidden="true"></i> -->
                                        <img class="pb-1" src="{{ asset('admin/assets/images/candidate/cadidate-verified.svg') }}" height="15" width="15" alt="Thumbnail">
                                </h1>
                                    <div class="fw-bold d-flex text-center">
                                        <i class="fi-briefcase ms-1 fs-lg text-muted pe-2"></i>
                                        <span class="d-inline-block me-2 fs-sm">@if($total_exp != 0) {{ $total_exp.' Year' }} @else {{ 'Fresher' }} @endif</span>
                                        <i class="fas fa-passport ms-1 fs-lg text-muted pe-2"></i>
                                        <span class="d-inline-block me-2 fs-sm"> {{ substr_replace($post->pass_no,'**',-2) ?? '---'  }}</span>

                                        <i class="fi-car ms-1 fs-lg text-muted pe-2"></i>
                                        <span class="d-inline-block me-2 fs-sm">
                                          @if($post->jobtype_id != '')
                                            @foreach($professions as $profession)
                                              @if($post->jobtype_id == $profession->id)
                                                 {{ $profession->eng_name }} 
                                              @endif
                                            @endforeach
                                          @else 
                                            {{ '---' }}
                                          @endif
                                          
                                        </span>
                                        <i class="fi-cash ms-1 fs-lg text-muted pe-2"></i>
                                        <span class="d-inline-block me-2 fs-sm">{{ $post->exp_sal }}</span>
                                    </div>
                                    <div class="border-top pt-3 pb-1 mt-2 text-nowrap">
                                      @foreach($ordstatuses as $ordstatus)
                                        @if($post->ord_status_id == $ordstatus->id)
                                          <p class="mb-0"><strong>Order Status:</strong> {{$ordstatus->ord_status}}</p>
                                        @endif
                                      @endforeach
                                    </div>
                                    <div class="border-top pt-2 mt-3">
                                        <div class="row g-2">

                                        @if ($post->cv_execute == 1 && $post->cv_execute_file != '')
                                            <div class="col-6 col-md-auto">
                                                <a class="btn btn-sm btn-primary w-100"
                                                href="{{ asset('admin/assets/images/pdf/'.$post->cv_execute_file) }}" download>
                                                <i class="fi-download me-1"></i> Download CV
                                                </a>
                                            </div>                                  
                                        @endif

                                            <!-- View Candidate -->
                                            <div class="col-6 col-md-auto">
                                                <a href="{{ route('fullresume',$post->slug_text) }}"
                                                class="btn btn-primary btn-sm w-100">
                                                    View Candidate
                                                </a>
                                            </div>

                                            <!-- Order Status -->
                                            <div class="col-6 col-md-auto">
                                                <button type="button"
                                                        class="btn btn-primary btn-sm w-100"
                                                        onclick="openOrderDetails({{ $post->id }})">
                                                    Order Status
                                                </button>
                                            </div>

                                            <!-- Cancel -->
                                            <div class="col-6 col-md-auto">
                                                <button class="cancelBooking btn btn-danger btn-sm w-100"
                                                        data-id="{{ $post->id }}"
                                                        type="button">
                                                    Cancel
                                                </button>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>                        
                    @endforeach                
                @else
                    <p class="pt-1 mb-4">No Booking Order Found. Choose your resume <a href="{{ route('resumes') }}">here</a></p>
                @endif

            </div>
        </div>


        {{-- Order Details --}}
        <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered"> <!-- centered + large -->
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title">Order Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">

                        <!-- Loader -->
                        <div id="orderLoader" class="text-center py-5 d-none">
                            <div class="spinner-border text-primary"></div>
                        </div>

                        <!-- Content -->
                        <div id="orderContent" class="d-none">

                            <div class="row g-4">

                                <!-- Order Info -->
                                <div class="col-md-6">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-body">
                                            <h6 class="text-muted mb-3">Order Information</h6>

                                            {{-- <p><strong>Order ID:</strong> #<span id="orderId">-</span></p> --}}
                                            <p><strong>Reference No:</strong> <span id="referenceNo">-</span></p>
                                            <p><strong>Order Date:</strong> <span id="bookingDate">-</span></p>
                                            <p><strong>Work Location:</strong> <span id="workLocation">-</span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Info -->
                                <div class="col-md-6">
                                    <div class="card shadow-sm border-0 h-100">
                                        <div class="card-body">
                                            <h6 class="text-muted mb-3">Payment Details</h6>

                                            <p><strong>Amount:</strong> ₹ <span id="amount">-</span></p>

                                            <p><strong>Payment Status:</strong>
                                                <span id="paymentStatus" class="badge bg-secondary">-</span>
                                            </p>

                                            <p><strong>Visa Status:</strong>
                                                <span id="visaStatus" class="badge bg-secondary">-</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Booking Status -->
                                <div class="col-12">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-body text-center">

                                            <h6 class="text-muted mb-3">Booking Status</h6>

                                            <span id="bookingStatus"
                                                class="badge bg-primary fs-6 px-3 py-2">
                                                -
                                            </span>

                                            <p class="mt-3 mb-0">
                                                <strong>Created At:</strong>
                                                <span id="createdAt">-</span>
                                            </p>

                                        </div>
                                    </div>
                                </div>

                            </div>

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
    function openOrderDetails(id) {
            // ✅ show modal instead
            let modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
            modal.show();

            // Loader show
            document.getElementById('orderLoader').classList.remove('d-none');
            document.getElementById('orderContent').classList.add('d-none');

            fetch(`/my-order/details/${id}`)
                .then(response => response.json())
                .then(data => {

                    // document.getElementById('orderId').innerText = data.id ?? '-';
                    document.getElementById('referenceNo').innerText = data.reference_no ?? '-';
                    document.getElementById('bookingDate').innerText = data.booking_date ?? '-';
                    document.getElementById('workLocation').innerText = data.worklocation_name ?? '-';
                    document.getElementById('amount').innerText = data.amount ?? '-';
                    document.getElementById('createdAt').innerText = data.formatted_created_at ?? '-';

                    // Payment Status
                    let paymentBadge = document.getElementById('paymentStatus');
                    if (data.payment_status == 1) {
                        paymentBadge.innerText = 'Paid';
                        paymentBadge.className = 'badge bg-success';
                    } else {
                        paymentBadge.innerText = 'Pending';
                        paymentBadge.className = 'badge bg-warning';
                    }

                    // Visa Status
                    let visaBadge = document.getElementById('visaStatus');
                    if (data.visa_status == 1) {
                        visaBadge.innerText = 'Approved';
                        visaBadge.className = 'badge bg-success';
                    } else {
                        visaBadge.innerText = 'Processing';
                        visaBadge.className = 'badge bg-info';
                    }

                    // Booking Status
                    let bookingBadge = document.getElementById('bookingStatus');
                    if (data.booking_status == 1) {
                        bookingBadge.innerText = 'Confirmed';
                        bookingBadge.className = 'badge bg-success fs-6 px-3 py-2';
                    } else {
                        bookingBadge.innerText = 'Pending Confirmation';
                        bookingBadge.className = 'badge bg-warning fs-6 px-3 py-2';
                    }

                    // Hide loader
                    document.getElementById('orderLoader').classList.add('d-none');
                    document.getElementById('orderContent').classList.remove('d-none');

                })
                .catch(error => {
                    console.error(error);
                });
    }
  </script>


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