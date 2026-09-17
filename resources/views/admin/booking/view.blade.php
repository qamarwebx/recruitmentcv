@extends('layout.admin.admin_layout')

@section('title','View Order Detail')

@section('page-style')
    <style>
        @import url('https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
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

        /* Progress Bar Style Start */
        .wizard-progress{
            display: table;
            width: 100%;
            table-layout: fixed;
            position:relative;
  
            .step{
                display: table-cell;
                text-align: center;
                vertical-align: top;
                overflow: visible;
                position:relative;
                font-size: 14px;
                /* color: $baseColor; */
                font-weight: bold;
    
                &:not(:last-child):before{
                    content: '';
                    display:block;
                    position: absolute;
                    left: 50%;
                    top: 37px;
                    background-color: #ccc;
                    height: 6px;
                    width: 100%;
                }
    
                .node{
                    display: inline-block;
                    border: 2px solid #ccc;
                    background-color: #fff;
                    border-radius: 30px;
                    height: 30px;
                    width: 30px;
                    position: absolute;
                    top: 25px;
                    left: 50%;
                    margin-left: -18px;
                }
    
                &.complete{
                    &:before{
                        background-color: #3498db;
                    }
                    .node{
                        border-color: #fff;
                        background-color: #50C878;
                        color: #fff;
                        padding: 2px;
        
                        &:before{
                            font-family: FontAwesome;
                            content: "\f00c";
                        }
                    }
                }
    
                &.in-progress{
                    &:before{
                        background: #3498db;
                        background: -moz-linear-gradient(left,  #3498db 0%, #fff 100%);
                        background: -webkit-linear-gradient(left,  #3498db 0%, #fff 100%);
                        background: linear-gradient(to right,  #3498db 0%, #fff 100%);
                        filter: progid:DXImageTransform.Microsoft.gradient(     startColorstr='#{#3498db}', endColorstr='#{#fff}',GradientType=1 );
                    }
                    .node{
                        border-color: #3498db;
                    }
                }
            }
        }
        /* Progress Bar Style End */

        @media only screen and (max-width: 768px){

        }

    </style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        {{-- <div class="row mb-2">
            <div class="col-md-12">
                <a href="{{ route('admin.booking') }}" class="float-end btn btn-sm btn-primary">Back</a>
            </div>
        </div> --}}
        <div class="row mb-4">
            <div class="col-md-12 mb-3 d-none">
                <div class="card">
                    <div class="card-body">
                        <div class="progress-bar1">
                            <div class="progress-track"></div>
                            <div id="step1" class="progress-step">
                                <div class="progresstext">Order Placed</div>
                            </div>
                        </div>
                        <div class="progress-bar1">
                            <div class="progress-track"></div>
                            <div id="step1" class="progress-step">
                                <div class="progresstext">Order Confirmed</div>
                            </div>
                        </div>
                        <div class="progress-bar1">
                            <div class="progress-track"></div>
                            <div id="step1" class="progress-step">
                                <div class="progresstext">Visa Authorization</div>
                            </div>
                        </div>
                        <div class="progress-bar1">
                            <div class="progress-track"></div>
                            <div id="step1" class="progress-step">
                                <div class="progresstext">Visa Stamped</div>
                            </div>
                        </div>
                        <div class="progress-bar1">
                            <div class="progress-track"></div>
                            <div id="step1" class="progress-step">
                                <div class="progresstext">Immigartion cleared</div>
                            </div>
                        </div>
                        <div class="progress-bar1">
                            <div class="progress-track"></div>
                            <div id="step1" class="progress-step">
                                <div class="progresstext">Ticket confirmd</div>
                            </div>
                        </div>
                        <div class="progress-bar1">
                            <div class="progress-track"></div>
                            <div id="step1" class="progress-step">
                                <div class="progresstext">Deployed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body ">
                        <div class="wizard-progress  mb-5 mt-2">
                            <div class="step complete">
                                <div class="node"></div>
                                Order Placed
                            </div>
                            <div class="step complete">  
                                <div class="node"></div>
                                Order Confirmed
                            </div>
                            <div class="step in-progress">
                                <div class="node"></div>
                                Visa Authorization
                            </div>
                            <div class="step ">
                                <div class="node"></div>
                                Visa Stamped
                            </div>
                            <div class="step">
                                <div class="node"></div>
                                Immigartion cleared
                            </div>
                            <div class="step">
                                <div class="node"></div>
                                Ticket confirmd 
                            </div>
                            <div class="step">
                                <div class="node"></div>
                                Deployed 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-6 mb-4">
                <div class="card">
                    <div class="ribbon ribbon-left-side">
                        <span class="bg-sky">Employer Info</span>
                    </div>
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title m-0 me-2">
                            {{-- <h5 class="m-0 me-2">Employer Info</h5> --}}
                        </div>
                    </div>
                    <div class="card-body">

                        @if($post->booking_status == 2)

                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="alert alert-danger py-2 px-3 mb-3">
                                    <strong>Order Cancelled</strong><br>
                                    Cancelled by <b>{{ $post->booking_cancelled_by ?? 'Admin' }}</b> 
                                    at {{ \Carbon\Carbon::parse($post->booking_cancelled_at)->format('d M Y h:i A') }}
                                </div>
                            </div>
                        </div>

                        @endif
                        <div class="row">
                            <div class="col-md-12">
                                <p>Name<br><strong>{{ $post->cuname }}</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p>Mobile No<br><strong>{{ $post->mobile_no }}</strong></p>
                            </div>
                            <div class="col-md-3">
                                <p>City<br><strong>@if($post->citname != '') {{ $post->citname }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-3">
                                <p>Address<br><strong>@if($post->cuaddr != '') {{ $post->cuaddr }} @else {{ '---' }} @endif</strong></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Visa Details</h6>
                            </div>
                            <div class="col-md-4">
                                <p>Employer<br><strong>@if($post->employer_name != '') {{ $post->employer_name }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-4">
                                <p>Employer Arabic<br><strong>@if($post->employer_ar_name != '') {{ $post->employer_ar_name }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-4">
                                <p>ID No<br><strong>@if($post->id_no != '') {{ $post->id_no }} @else {{ '---' }} @endif</strong></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <p>Visa No<br><strong>@if($post->visa_no != '') {{ $post->visa_no }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-4">
                                <p>Submission Place<br><strong>@if($post->issuing_authority != '') {{ $post->issuing_authority }} @else {{ '---' }} @endif</strong></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Employment Details</h6>
                            </div>
                            <div class="col-md-3">
                                <p>Country<br><strong>@if($post->contname != '') {{ $post->contname }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-3">
                                <p>City of Work<br><strong>@if($post->expwname != '') {{ $post->expwname }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-3">
                                <p>Salary<br><strong>@if($post->exp_sal != '') {{ $post->exp_sal.' SAR' }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-3">
                                <p>Contract<br><strong>2 Years</strong></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Payment Details</h6>
                            </div>
                            <div class="col-md-3">
                                <p>Amount<br><strong>@if($post->amount != '') {{ $post->amount }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-3">
                                <p>Payment Confirm By<br><strong>@if($post->adminname != '') {{ $post->adminname }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-3">
                                <p>Payment Date & Time<br><strong>@if($post->created_at != '') {{ \Carbon\Carbon::parse($post->created_at)->format('d-m-Y H:i:s') }} @else {{ '---' }} @endif</strong></p>
                            </div>
                            <div class="col-md-3">
                                <p>Status<br><strong>@if($post->status == 1) <span class="text-success">Success</span> @elseif($post->status == 0) Failed @else {{ '---' }} @endif</strong></p>
                            </div>
                        </div>
                        <hr>

                        
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Order Details</h6>
                            </div>
                            <div class="col-md-3">
                                <p>Order Number<br><strong>{{ $post->reference_no }}</strong></p>
                            </div>
                            <div class="col-md-3">
                                <p>Order Date<br><strong>{{ date('d-m-Y',strtotime($post->booking_date)) }}</strong></p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Order Confirm By</h6>
                            </div>
                            <div class="col-md-4">
                                <p>Office Name<br><strong>@if($post->partneroffice != '') {{ $post->partneroffice }} @else --- @endif</strong></p>
                            </div>
                            <div class="col-md-4">
                                <p>Staff Name<br><strong>@if($post->partername != '') {{ $post->partername }} @else --- @endif</strong></p>
                            </div>
                            <div class="col-md-4">
                                <p>Confirm Date<br><strong>@if($post->partner_order_confirm_date != '') {{ date('d-m-Y',strtotime($post->partner_order_confirm_date)) }} @else --- @endif</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 mb-4">
                <div class="card ">
                    <div class="ribbon ribbon-left-side">
                        <span class="bg-purple">Candidate Info</span>
                    </div>
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title m-0 me-2">
                            {{-- <h5 class="m-0 me-2">Candidate Info</h5> --}}
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
                                            <div class="col-md-4">
                                                <p><strong>Reference No</strong><br>{{ $post->refNo }}</p>
                                             </div>
                                             <div class="col-md-4">
                                                <p><strong>Location</strong><br>India</p>
                                             </div>
                                             <div class="col-md-4">
                                                <p><strong>Salary</strong><br>{{ $post->exp_sal }}</p>
        
                                             </div>
                                             <div class="col-md-4">
                                                <p><strong>Experience</strong><br>@if($total_exp != 0) {{ $total_exp.' Year' }} @else {{ 'Fresher' }} @endif</p>
        
                                             </div>
                                             <div class="col-md-4">
                                                <p><strong>Exp Location</strong><br>{{ implode(',',$mycity) }}</p>
        
                                             </div>
                                             <div class="col-md-4">
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
    </div>
@endsection

@section('page-script')

@endsection