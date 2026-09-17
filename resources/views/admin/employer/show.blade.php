@extends('layout.admin.admin_layout')

@section('title','Employer Booking')

@section('page-style')
    <style>
        .booking-list {
            margin-left: 10px;
        }
        /* Ribbon style Start */
        .ribbon{
            width: 200px;
            height: 150px;
            overflow: hidden;
            position: absolute;
        }
        /* .ribbon::before,
        .ribbon::after {
            position: absolute;
            z-index: -1;
            content: '';
            display: block;
            border: 5px solid #2980b9;
        } */
        .ribbon span{

            position: absolute;
            display: block;
            width: 200px;
            padding: 5px 0;
            /* background-color: #3498db; */
            box-shadow: 0 5px 10px rgba(0,0,0,.1);
            /* color: #fff; */
            /* font: 700 18px/1 'Lato', sans-serif; */
            text-shadow: 0 1px 1px rgba(0,0,0,.2);
            text-transform: uppercase;
            text-align: center;

        }
        .bg-sky{
            background-color: #3498db;
            color: #fff;
        }
        .bg-purple{
            background-color: #7442f3;
            color: #fff;
        }
        .ribbon-left-side{
            top: -15px;
            left: 30px;
        }
        .ribbon-left-side::before,
        .ribbon-left-side::after{
            border-top-color: transparent;
            border-left-color: transparent;
        }
        /* Ribbon style end */
    </style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row mb-2">
            <div class="col-md-12">
                <a href="{{ route('admin.employer') }}" class="float-end btn btn-sm btn-primary">Back</a>
            </div>
        </div>
        {{-- <div class="row">
            <div class="col-md-6 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="ribbon ribbon-left-side">
                        <span class="bg-sky">Employer Info</span>
                    </div>
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title m-0 me-2">

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p>Name<br><strong>{{ $post->cuname }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p>Address<br><strong>@if($post->cuaddr != '') {{ $post->cuaddr }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-3">
                                <p>Visa No<br><strong>@if($post->visa_no != '') {{ $post->visa_no }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-3">
                                <p>Mobile No<br><strong>{{ $post->mobile_no }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="ribbon ribbon-left-side">
                        <span class="bg-purple">Candidate Info</span>
                    </div>
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title m-0 me-2">

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-2 mb-2">
                                        <div class="avatar avatar-lg">
                                            @if ($post->photo_file != '')
                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="Avatar" class="rounded-circle">
                                            @else
                                                <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p><strong>{{ $post->cand_name }}</strong><br><strong>Passport No - {{ $post->pass_no }}</strong></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md col-sm-6">
                                                <p><strong>Location</strong><br>India</p>
                                            </div>
                                            <div class="col-md col-sm-6">
                                                <p><strong>Salary</strong><br>{{ $post->exp_sal }}</p>
                                            </div>
                                            <div class="col-md col-sm-6">
                                                <p><strong>Experience</strong><br>@if($total_exp != 0) {{ $total_exp.' Year' }} @else {{ 'Fresher' }} @endif</p>
                                            </div>
                                            <div class="col-md col-sm-6">
                                                <p><strong>Exp Location</strong><br>{{ implode(',',$mycity) }}</p>
                                            </div>
                                            <div class="col-md col-sm-6">
                                                <p><strong>Marital Status</strong><br>{{ $post->marital_status }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-users table border-top">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employer Name</th>
                                    <th>City</th>
                                    <th>Mobile</th>
                                    <th>Candidate Name</th>
                                    <th>Passport No</th>
                                    <th>Reference No</th>
                                    <th>Booking Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $ieb = 1;
                                @endphp
                                @foreach ($relBookings as $relBooking)
                                    <tr>
                                        <td>{{ $ieb++ }}</td>
                                        <td>{{ $relBooking->cuname }}</td>
                                        <td>{{ $relBooking->city }}</td>
                                        <td>{{ $relBooking->mobile_no }}</td>
                                        <td>{{ $relBooking->cand_name }}</td>
                                        <td>{{ $relBooking->pass_no }}</td>
                                        <td>{{ $relBooking->reference_no }}</td>
                                        <td>{{ $relBooking->booking_date }}</td>
                                        <td>
                                            @if ($relBooking->booking_status == 1)
                                                <span class="badge bg-label-success">Confirm</span>
                                            @elseif($relBooking->booking_status == 2)
                                                <span class="badge bg-label-danger">Cancel</span>
                                            @else
                                                <span class="badge bg-label-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="row" id="mainContainDiv">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    <div>
                                        @if ($post->avatar_url != '')
                                            <img src="{{ $post->avatar_url }}" alt="Avatar" class="d-block w-px-100 h-px-100 rounded">
                                        @elseif($post->photo != '')
                                            <img src="{{ asset('user/img/avatars/'.$post->photo) }}" alt="Avatar" class="d-block w-px-100 h-px-100 rounded">
                                        @else
                                            <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="Avatar" class="d-block w-px-100 h-px-100 rounded">
                                        @endif
                                    </div>
                                    <div>
                                        <h5 style="margin-top: -35px;">{{ $post->cuname }}</h5>
                                        {{-- <h6 style="margin-top: -15px;"><i class="ti ti-user-check"></i> @if(isset($occupation)) {{ $occupation->eng_name.' ('.$occupation->ar_name.')' }} @else {{ '---' }} @endif</h6> --}}
                                        <h6 style="margin-top: -15px;"><i class="tf-icons ti ti-phone-call ti-xs me-1"></i> @if($post->mobile_no != '') {{ $post->mobile_no }} @else {{ '---' }} @endif</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#upvidlink"><i class="ti ti-brand-youtube"></i> Edit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <div class="row mt-4">
            <div class="col-xl-12">
               <div class="card mb-3">
                  <div class="card-header">
                     <ul class="nav nav-tabs nav-fill" role="tablist">
                        <li class="nav-item">
                           <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-home" aria-controls="navs-justified-home" aria-selected="true">
                           <i class="tf-icons ti ti-home ti-xs me-1"></i> Visa Details
                           </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-candidate-file-tab" aria-controls="nav-candidate-file-tab" aria-selected="false">
                                <i class="tf-icons ti ti-file ti-xs me-1"></i> Candidate FIle
                            </button>
                        </li>
                        <li class="nav-item">
                           <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-candidate-tab" aria-controls="nav-candidate-tab" aria-selected="false">
                           <i class="tf-icons ti ti-user-check ti-xs me-1"></i> Candidate
                           </button>
                        </li>

                        <li class="nav-item">
                           <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-cand-payment-tab" aria-controls="nav-cand-payment-tab" aria-selected="false">
                           <i class="tf-icons ti ti-cash ti-xs me-1"></i> Order
                           </button>
                        </li>
                     </ul>
                  </div>
                  <div class="card-body">
                     <div class="tab-content p-0">
                        <div class="tab-pane fade show active" id="navs-justified-home" role="tabpanel">
                           <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Employer</th>
                                                    <th>Employer Arabic Name</th>
                                                    <th>ID No</th>
                                                    <th>Visa No</th>
                                                    <th>Visa Issue</th>
                                                    <th>City of Work</th>
                                                    <th>Visa Profession</th>
                                                    <th>Monthly Salary</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>@if($post->employer_name != '') {{ $post->employer_name }} @else {{ '---' }} @endif</td>
                                                    <td>@if($post->employer_ar_name != '') {{ $post->employer_ar_name }} @else {{ '---' }} @endif</td>
                                                    <td>@if($post->id_no != '') {{ $post->id_no }} @else {{ '---' }} @endif</td>
                                                    <td>@if($post->visa_no != '') {{ $post->visa_no }} @else {{ '---' }} @endif</td>
                                                    <td>@if($post->issuing_authority != '') {{ $post->issuing_authority }} @else {{ '---' }} @endif</td>
                                                    <td>{{ $post->worklocationname }}</td>
                                                    <td>@if($post->vpengname != '') {{ $post->vpengname }} @else {{ '---' }} @endif</td>
                                                    <td>@if($post->exp_sal != '') {{ $post->exp_sal.' SAR' }} @else {{ '---' }} @endif</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                           </div>
                        </div>



                        <div class="tab-pane fade" id="nav-candidate-file-tab" role="tabpanel">
                            <div class="row">
                                @if ($post->photo_file != '')
                                    <div class="col-md-2">
                                        <span class="badge bg-label-secondary">Photo</span>
                                        <div class="mt-2">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" target="_blank">
                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="{{ $post->photo_file }}" class="d-block w-px-100 h-px-100 rounded" />

                                            </a>
                                        </div>
                                        <div class="mt-1">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>

                                            {{-- @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#deletePhoto"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                            @endif --}}
                                        </div>
                                    </div>
                                @endif
                                @if ($post->pass_file != '')
                                    <div class="col-md-2">
                                        <span class="badge bg-label-secondary">Passport Front Copy</span>
                                        <div class="mt-2">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" target="_blank">
                                            <img src="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" alt="{{ $post->pass_file }}" class="d-block w-px-100 h-px-100 rounded" />

                                            </a>
                                        </div>
                                        <div class="mt-1">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                                            {{-- @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#deletePassFile"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                            @endif --}}
                                        </div>
                                    </div>
                                @endif
                                @if ($post->pass_back_file != '')
                                    <div class="col-md-2">
                                        <span class="badge bg-label-secondary">Passport Back Copy</span>
                                        <div class="mt-2">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_back_file) }}" target="_blank">
                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->pass_back_file) }}" alt="{{ $post->pass_back_file }}" class="d-block w-px-100 h-px-100 rounded" />

                                            </a>
                                        </div>
                                        <div class="mt-1">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_back_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->pass_back_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                                            {{-- @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#deletePassBackFile"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                            @endif --}}
                                        </div>
                                    </div>
                                @endif
                                @if ($post->lic_file != '')
                                    <div class="col-md-2">
                                        <span class="badge bg-label-secondary">License Copy</span>
                                        <div class="mt-2">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" target="_blank">
                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" alt="{{ $post->lic_file }}" class="d-block w-px-100 h-px-100 rounded" />

                                            </a>
                                        </div>
                                        <div class="mt-1">
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                            <a href="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                                            {{-- @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#deleteLice"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                            @endif --}}
                                        </div>
                                    </div>
                                @endif
                                @if ($post->cv_file != '')
                                    <div class="col-md-2">
                                    <span class="badge bg-label-secondary">Full Size Image</span>
                                    <div class="mt-2">
                                        <a href="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" target="_blank">
                                            <img src="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" alt="{{ $post->cv_file }}" class="d-block w-px-100 h-px-100 rounded" />

                                        </a>
                                    </div>
                                    <div class="mt-1">
                                        <a href="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                        <a href="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                                        {{-- @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#deleteFullImg"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                        @endif --}}
                                    </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-candidate-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Candidate</th>
                                                    <th>Passport No</th>
                                                    <th>DOB</th>
                                                    <th>Experience</th>
                                                    <th>Profession</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $post->cand_name }}</td>
                                                    <td>{{ $post->pass_no }}</td>
                                                    <td>{{ date('d-m-Y',strtotime($post->candob)) }}</td>
                                                    <td>{{ $post->overall_exp.' Years' }}</td>
                                                    <td>{{ $post->pengname }}</td>

                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-cand-payment-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Employer</th>
                                                    <th>City</th>
                                                    <th>Mobile</th>
                                                    <th>Candidate</th>
                                                    <th>Passport No</th>
                                                    <th>Order No</th>
                                                    <th>Order Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $ieb = 1;
                                                @endphp
                                                @foreach ($relBookings as $relBooking)
                                                    <tr>
                                                        <td>{{ $ieb++ }}</td>
                                                        <td>{{ $relBooking->cuname }}</td>
                                                        <td>{{ $relBooking->city }}</td>
                                                        <td>{{ $relBooking->mobile_no }}</td>
                                                        <td>{{ $relBooking->cand_name }}</td>
                                                        <td>{{ $relBooking->pass_no }}</td>
                                                        <td>{{ $relBooking->reference_no }}</td>
                                                        <td>{{ date('d-m-Y',strtotime($relBooking->booking_date)) }}</td>
                                                        <td>
                                                            @if ($relBooking->booking_status == 1)
                                                                <span class="badge bg-label-success">Confirm</span>
                                                            @elseif($relBooking->booking_status == 2)
                                                                <span class="badge bg-label-danger">Cancel</span>
                                                            @else
                                                                <span class="badge bg-label-warning">Pending</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                     </div>
                  </div>
               </div>
            </div>




         </div>

    </div>
@endsection

@section('page-script')

@endsection
