    <style>
        .glowbtn{
            -webkit-animation: glowing 1500ms infinite;
            -moz-animation: glowing 1500ms infinite;
            -o-animation: glowing 1500ms infinite;
            animation: glowing 1500ms infinite;
        }

        @-webkit-keyframes glowing {
            0% { background-color: #228B22; -webkit-box-shadow: 0 0 3px #228B22; }
            50% { background-color: #50C878; -webkit-box-shadow: 0 0 20px #50C878; }
            100% { background-color: #228B22; -webkit-box-shadow: 0 0 3px #228B22; }
        }

        @-moz-keyframes glowing {
            0% { background-color: #228B22; -moz-box-shadow: 0 0 3px #228B22; }
            50% { background-color: #50C878; -moz-box-shadow: 0 0 20px #50C878; }
            100% { background-color: #228B22; -moz-box-shadow: 0 0 3px #228B22; }
        }

        @-o-keyframes glowing {
            0% { background-color: #228B22; box-shadow: 0 0 3px #228B22; }
            50% { background-color: #50C878; box-shadow: 0 0 20px #50C878; }
            100% { background-color: #228B22; box-shadow: 0 0 3px #228B22; }
        }

        @keyframes glowing {
            0% { background-color: #228B22; box-shadow: 0 0 3px #228B22; }
            50% { background-color: #50C878; box-shadow: 0 0 20px #50C878; }
            100% { background-color: #228B22; box-shadow: 0 0 3px #228B22; }
        }
    </style>
        <!-- Popular Product -->
            @if($CheckVisa->count() > 0)
                @if ($posts->count() > 0)
                    @foreach ($posts as $post)
                        <div class="col-md-12 col-lg-12 mb-4">
                            <div class="card h-100">
                                @if ($post->booking_status == 1)
                                    <div class="ribbon ribbon-top-left">
                                        <span>Confirm</span>
                                    </div>
                                @endif
                                <div class="card-header d-flex justify-content-between">
                                    <div class="card-title m-0 me-2">
                                        <h5 class="m-0 me-2"></h5>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn p-0" type="button" id="popularProduct" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="popularProduct">
                                            <a class="dropdown-item" href="javascript:void(0);" @if($post->booking_status != 1) data-bs-toggle="modal" data-bs-target="#confirmBooking" data-id="{{ $post->id }}" @endif>{{ __('locale.Confirm Booking') }}</a>
                                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#addvisadetail" data-id="{{ $post->id }}">{{ __('locale.Add Visa Detail') }}</a>
                                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="offcanvas" data-bs-target="#addpayment" data-id="{{ $post->id }}">{{ __('locale.Payment') }}</a>
                                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#cancelBooking" data-id="{{ $post->id }}">{{ __('locale.Cancel Booking') }}</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="row mb-3">
                                                        <div class="col-md col-sm-6">
                                                            <div class="avatar avatar-lg">
                                                                @if ($post->photo != '')
                                                                    <img src="{{ asset('user/img/avatars/'.$post->photo) }}" alt="Avatars" class="rounded-circle">
                                                                @elseif($post->uavatar != '')
                                                                    <img src="{{ $post->uavatar }}" alt="Avatars" class="rounded-circle">
                                                                @else
                                                                    <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle">
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><a href="{{ route('partner.booking.show',$post->id) }}" target="_blank"><strong>{{ __('locale.Customer') }}</strong><br>{{ $post->cuname }}</a></p>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><strong>{{ __('locale.Mobile Number') }}</strong><br>@if($post->mobile_no != '') {{ $post->mobile_no }} @else {{ '---' }} @endif</p>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><strong>{{ __('locale.City of Work') }}</strong><br>@if($post->expwname != '') {{ $post->expwname }} @else {{ '---' }} @endif</p>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><strong>{{ __('locale.Order Number') }}</strong><br>{{$post->reference_no }}</p>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><strong>{{ __('locale.Order Date') }}</strong><br>{{ date('d/m/Y',strtotime($post->booking_date)) }}</p>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><strong>{{ __('locale.Order Status') }}</strong><br>{{$post->ord_status}}</p>
                                                        </div>
                                                    </div>

                                                    {{-- <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-lg">
                                                            @if ($post->photo != '')
                                                                <img src="{{ asset('user/img/avatars/'.$post->photo) }}" alt="Avatars" class="rounded-circle">
                                                            @else
                                                                <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle">
                                                            @endif
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>Customer</strong><br>{{ $post->cuname }}</p>
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>Mobile Number</strong><br>{{ $post->mobile_no }}</p>
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>City</strong><br>@if($post->expwname != '') {{ $post->expwname }} @else {{ '---' }} @endif</p>
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>Reference Number</strong><br>{{ $post->reference_no }}</p>
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>Booking Date</strong><br>{{ date('d/m/Y',strtotime($post->booking_date)) }}</p>
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>Status</strong><br>@if($post->booking_status == 1) Confirm @else Pending @endif</p>
                                                        </div>
                                                    </div> --}}

                                                    <div class="row">
                                                        <div class="col-md col-sm-6">
                                                            <div class="avatar avatar-lg">
                                                                @if ($post->photo_file != '' && file_exists(public_path().'/admin/assets/images/candidate/'.$post->photo_file))
                                                                    <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="Avatar" class="rounded-circle">
                                                                @else
                                                                    <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle">    
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><a href="{{ route('partner.booking.show',$post->id) }}" target="_blank"><strong>{{ __('locale.Candidate') }}</strong><br>{{ $post->cand_name }}</a></p>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><strong>{{ __('locale.Passport No') }}</strong><br>{{ $post->pass_no }}</p>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><strong>{{ __('locale.Occupation') }}</strong><br>@if($post->pengname != '') {{ $post->pengname }} @else {{ '---' }} @endif</p>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><strong>{{ __('locale.Reference Number') }}</strong><br>{{ $post->candref }}</p>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><strong>{{ __('locale.Order Date') }}</strong><br>{{ date('d/m/Y',strtotime($post->booking_date)) }}</p>
                                                        </div>
                                                        <div class="col-md col-sm-6">
                                                            <p><strong>{{ __('locale.Payment Status') }}</strong><br>@if($post->payment_status == 1) Paid @else Unpaid @endif</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- <div class="col-md-12 mt-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-lg">
                                                            @if ($post->photo_file != '')
                                                                <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="Avatar" class="rounded-circle">
                                                            @else
                                                                <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle">    
                                                            @endif
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>Candidate</strong><br>{{ $post->cand_name }}</p>
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>Passport No</strong><br>{{ $post->pass_no }}</p>
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>Occupation</strong><br>@if($post->job_type != '') {{ $post->job_type }} @else {{ '---' }} @endif</p>
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>Reference Number</strong><br>{{ $post->cand_id }}</p>
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>Booking Date</strong><br>{{ date('d/m/Y',strtotime($post->booking_date)) }}</p>
                                                        </div>
                                                        <div class="booking-list">
                                                            <p><strong>Payment Status</strong><br>@if($post->payment_status == 1) Paid @else Unpaid @endif</p>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <button type="button" class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#confirmBooking" data-id="{{ $post->id }}" @if($post->booking_status == 1) disabled @endif>{{ __('locale.Confirm Booking') }}</button><br>
                                            <button type="button" class="btn btn-sm btn-info mb-1" data-bs-toggle="offcanvas" data-bs-target="#addvisadetail" data-id="{{ $post->id }}">{{ __('locale.Add Visa Detail') }}</button><br>
                                            <a href="{{ route('partner.booking.show',$post->id) }}" class="btn btn-sm btn-info mb-1" target="_blank">{{ __('locale.View Full Details') }}</a>
                                            <button type="button" class="btn glowbtn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#updataOrderStatus" data-id="{{ $post->id }}">{{ __('locale.Update Order Status') }}</button>
                                            {{-- <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#addpayment" data-id="{{ $post->id }}">Payment</button> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>     
                    @endforeach                
                @else
                    <div class="col-md-12 col-lg-12 mb-4">
                        <p class="text-center">{{ __('locale.No Booking Found...') }}</p>
                    </div>
                @endif
            @else
                <div class="col-md-12 col-lg-12 mb-4">
                    <p class="text-center">{{ __('locale.No Booking Found...') }}</p>
                </div>
            @endif

        
            <!--/ Popular Product -->

            <!-- Pagination Start -->
            {{-- <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end">
                    <li class="page-item prev">
                        <a class="page-link" href="javascript:void(0);"><i class="ti ti-chevrons-left ti-xs"></i></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">2</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="javascript:void(0);">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">4</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">5</a>
                    </li>
                    <li class="page-item next">
                        <a class="page-link" href="javascript:void(0);"><i class="ti ti-chevrons-right ti-xs"></i></a>
                    </li>
                </ul>
            </nav> --}}
            <!-- Pagination End -->

            {{ $posts->links() }}