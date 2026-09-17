@extends('layout.partner.partner_layout')
@section('title','View Candidate')
@section('page-style')
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
<link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
<link rel="stylesheet" href="{{ asset('user/intl-tel-input-master/build/css/intlTelInput.css') }}">
@endsection
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row mb-2">
            <div class="col-lg-12">
                <div class="">
                    {{-- <a href="{{ route('admin.candidate') }}" class="btn btn-sm btn-primary float-end">Back</a> --}}
                    <button class="btn btn-sm btn-primary float-end" id="closedButton">Close</button>
                </div>
            </div>
        </div>
        <div class="row" id="mainContainDiv">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                    <div>
                                        <img @if($post->photo_file != '') src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}"  @else src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" @endif alt="Avatar" class="d-block w-px-100 h-px-100 rounded" data-bs-toggle="modal" data-bs-target="#imageUpload" />
                                    </div>
                                    <div class="mt-0">

                                        <h5>{{ $post->cand_name }}</h5>
                                        <h6 style="margin-top: -15px;"><i class="ti ti-user-check"></i> @if(isset($occupation)) {{ $occupation->eng_name.' ('.$occupation->ar_name.')' }} @else {{ '---' }} @endif</h6>
                                        <h6 style="margin-top: -15px;"><i class="tf-icons ti ti-phone-call ti-xs me-1"></i> @if($post->mobile_no != '') {{ $post_data['mobile_no'] }} @else {{ '---' }} @endif</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8"></div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                @if (Auth::guard('partner')->user()->user_type == 1)

                                    <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#NewCandStatusUpdate" data-id="{{ $post->id }}"><i class="ti ti-edit-circle"></i> Status</button>

                                    <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal"  data-bs-target="#uploadDocs"><i class="ti ti-file-upload"></i> Upload Docs</button>

                                    <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#upvidlink"><i class="ti ti-brand-youtube"></i> Upload Video</button>

                                    @if ($post->cv_execute == 1)
                                        {{-- <a href="javascript:void(0)" id="candCvExecuteBtn" class="btn btn-sm btn-success"><i class="ti ti-file-upload"></i> CV Execute</a>                                     --}}
                                        @if($CalAmount == 0)
                                        <button class="btn btn-sm btn-warning mb-1 cv-execute-class" id="cvexecuteerro2"><i class="ti ti-file-upload"></i> CV Execute</button>
                                        @else
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#cvExecuteMULTI" class="btn btn-sm btn-success mb-1 cv-execute-class"><i class="ti ti-file-upload"></i> CV Execute</a>
                                        @endif
                                    @else
                                    <button class="btn btn-sm btn-warning mb-1 cv-execute-class" id="cvexecuteerro2"><i class="ti ti-file-upload"></i> CV Execute</button>
                                    @endif
                                    <button class="btn mb-1 btn-sm @if($post->publish == 1) btn-success @else btn-secondary @endif" data-bs-toggle="modal" data-bs-target="#updatePublished"><i class="ti ti-cloud-upload"></i> Publish</button>
                                    @if ($post->cv_execute == 1 && $post->cv_execute_file != '')
                                    <a href="{{ asset('admin/assets/images/pdf/'.$post->cv_execute_file) }}" class="btn btn-sm btn-info mb-1" download><i class="ti ti-download"></i> CV B2C</a>
                                    @else
                                    <button class="btn btn-sm btn-info mb-1" id="cvexecuteerro"><i class="ti ti-download"></i> CV B2C</button>
                                    @endif

                                    <button type="button" class="btn btn-sm btn-info mb-1" data-bs-toggle="modal" data-bs-target="#downloadcvcomp"><i class="ti ti-download"></i> CV B2B</button>
                                @else
                                    @if (isset($permission) && $permission->candidate_status == 1)
                                    <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#NewCandStatusUpdate" data-id="{{ $post->id }}"><i class="ti ti-edit-circle"></i> Status</button>
                                    @endif

                                    @if (isset($permission) && $permission->upload_docs == 1)
                                    <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#uploadDocs"><i class="ti ti-file-upload"></i> Upload Docs</button>
                                    @endif

                                    <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#upvidlink"><i class="ti ti-brand-youtube"></i> Upload Video</button>



                                    @if ($post->cv_execute == 1)
                                    {{-- <a href="javascript:void(0)" id="candCvExecuteBtn" class="btn btn-sm btn-success"><i class="ti ti-file-upload"></i> CV Execute</a>                                     --}}
                                    <!-- <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#cvExecuteMULTI" class="btn btn-sm btn-success mb-1 cv-execute-class"><i class="ti ti-file-upload"></i> CV Execute</a> -->
                                    @if($CalAmount == 0)
                                        <button class="btn btn-sm btn-warning mb-1 cv-execute-class" id="cvexecuteerro2"><i class="ti ti-file-upload"></i> CV Execute</button>
                                        @else
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#cvExecuteMULTI" class="btn btn-sm btn-success mb-1 cv-execute-class"><i class="ti ti-file-upload"></i> CV Execute</a>
                                        @endif
                                    @else
                                    <button class="btn btn-sm btn-warning mb-1 cv-execute-class" id="cvexecuteerro2"><i class="ti ti-file-upload"></i> CV Execute</button>
                                    @endif
                                    @if (isset($permission) && $permission->publish_candidate == 1)
                                    <button class="mb-1 btn btn-sm @if($post->publish == 1) btn-success @else btn-secondary @endif" data-bs-toggle="modal" data-bs-target="#updatePublished"><i class="ti ti-cloud-upload"></i> Publish</button>
                                    @endif

                                    @if (isset($permission) && $permission->cv_b2c == 1)
                                    @if ($post->cv_execute == 1 && $post->cv_execute_file != '')
                                        <a href="{{ asset('admin/assets/images/pdf/'.$post->cv_execute_file) }}" class="mb-1 btn btn-sm btn-info" download=""><i class="ti ti-download"></i> CV B2C</a>
                                    @else
                                        <button class="btn btn-sm btn-info mb-1" id="cvexecuteerro"><i class="ti ti-download"></i> CV B2C</button>
                                    @endif
                                    @endif
                                    @if (isset($permission) && $permission->cv_b2b == 1)
                                    <button type="button" class="btn btn-sm btn-info mb-1" data-bs-toggle="modal" data-bs-target="#downloadcvcomp"><i class="ti ti-download"></i> CV B2B</button>
                                    @endif

                                @endif
                                @if($empData)
                                    @if($empData['type'] == 'employerplus')
                                        <a href="{{ url('admin/employer-listp/add-visa-view/' . $empData['id']) }}"
                                        class="btn btn-sm btn-primary mb-1">
                                            <i class="ti ti-building ti-xs"></i> View Employer
                                        </a>
                                    @else($empData['type'] == 'employer')
                                        <a href="{{ url('admin/employer/add-visa-view/' . $empData['id']) }}"
                                        class="btn btn-sm btn-primary mb-1">
                                            <i class="ti ti-building ti-xs"></i> View Employer
                                        </a>
                                    @endif
                                @else
                                    <a class="btn btn-sm btn-primary mb-1 disabled">
                                        <i class="ti ti-building ti-xs"></i> View Employer
                                    </a>
                                @endif

                                <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#reminderCP"><i class="ti ti-alarm ti-xs"></i> Reminderd</button>

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
                            <i class="tf-icons ti ti-home ti-xs me-1"></i> Details
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-candidate-tab" aria-controls="nav-candidate-tab" aria-selected="false">
                            <i class="tf-icons ti ti-user-check ti-xs me-1"></i> Candidate
                            </button>
                        </li>
                        {{--
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-personal" aria-controls="navs-justified-personal" aria-selected="false">
                            <i class="tf-icons ti ti-user-check ti-xs me-1"></i> Personal
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-profile" aria-controls="navs-justified-profile" aria-selected="false">
                            <i class="tf-icons ti ti-file-check ti-xs me-1"></i> Passport
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-experience" aria-controls="navs-justified-experience" aria-selected="false">
                            <i class="tf-icons ti ti-files ti-xs me-1"></i> Experience & Skills
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-medical" aria-controls="navs-justified-medical" aria-selected="false">
                            <i class="tf-icons ti ti-files ti-xs me-1"></i> Medical
                            </button>
                        </li>
                        --}}
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-cand-payment-tab" aria-controls="nav-cand-payment-tab" aria-selected="false">
                            <i class="tf-icons ti ti-cash ti-xs me-1"></i> Payment
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-messages" aria-controls="navs-justified-messages" aria-selected="false">
                            <i class="tf-icons ti ti-file ti-xs me-1"></i> File
                            </button>
                        </li>
                        @if (Auth::guard('partner')->user()->user_type == 1 || (isset($permission) && $permission->cv_b2b == 1))
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-cvexecute-tab" aria-controls="nav-cvexecute-tab" aria-selected="false">
                                <i class="ti ti-file-upload"></i> B2B CV
                                </button>
                            </li>
                        @endif

                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-web-tab" aria-controls="nav-web-tab" aria-selected="false">
                            <i class="tf-icons ti ti-world ti-xs me-1"></i> Web
                            </button>
                        </li>
                        <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-reminder-tab" aria-controls="nav-reminder-tab" aria-selected="false">
                                    <i class="tf-icons ti ti-alarm ti-xs me-1"></i> Reminder
                                </button>
                        </li>
                        {{--
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-download-cv" aria-controls="nav-download-cv" aria-selected="false">
                            <i class="tf-icons ti ti-cloud-download ti-xs me-1"></i> Download CV
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-booking-hiring" aria-controls="nav-booking-hiring" aria-selected="true">
                            <i class="tf-icons ti ti-checklist ti-xs me-1"></i> Booking Details
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-cand-booking-limit" aria-controls="nav-cand-booking-limit" aria-selected="true">
                            <i class="tf-icons ti ti-ban ti-xs me-1"></i> Booking Limit
                            </button>
                        </li>
                        --}}
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-actvities" aria-controls="navs-justified-actvities" aria-selected="false">
                            <i class="tf-icons ti ti-timeline ti-xs me-1"></i> Activities
                            </button>
                        </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">
                        <div class="tab-pane fade show active" id="navs-justified-home" role="tabpanel">
                            <div class="row" id="nav-details-div">
                                @php
                                    if ($post->experience != '') {
                                        $total_exp = array_sum(explode(',',$post->experience));
                                    }else {
                                        $total_exp = "";
                                    }
                                @endphp
                                <div class="col-md-4">
                                    <div class="info-container">
                                    <ul class="list-unstyled">
                                        {{--
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Candidate Name:</span>
                                            <span>{{ $post->cand_name }}</span>
                                        </li>
                                        --}}
                                        {{--
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Marital Status:</span>
                                            <span>{{ $post->marital_status }}</span>
                                        </li>
                                        --}}
                                        {{--
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Occupation:</span>
                                            <span>@if(isset($occupation)) {{ $occupation->eng_name.' ('.$occupation->ar_name.')' }} @else {{ '---' }} @endif</span>
                                        </li>
                                        --}}
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Mobile No:</span>
                                            <span>@if($post->mobile_no != '') {{ $post_data['mobile_no'] }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Family Contact Number:</span>
                                            <span>@if($post->contact_no != '') {{ $post_data['contact_no'] }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Relative Contact Number:</span>
                                            <span>@if($post->relative_contact_no != '') {{ $post_data['relative_contact'] }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-1">
                                            <span class="fw-semibold me-1">Email:</span>
                                            <span>@if($post->email != '') {{ $post->email }}  @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Associate:</span>
                                            <span>@if($post->associate_id != '') {{ $post->associate->pty_full_name }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Associate Confirm By:</span>
                                            <span>@if($post->associateconfirmby_id != '') {{ $post->associateconfirmby->name }} @else <span class="text-danger">Not Confirm</span> @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Careoff:</span>
                                            <span>@if($post->careoff_id != '') {{ $careoffName->name }} @else '---' @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Created By:</span>
                                            <span>{{ $admin->name }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Created Date:</span>
                                            <span>{{ date("d-m-Y",strtotime($post->created_at)) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Sourcing Date</span>
                                            <span>@if($post->sourcing_date != '') {{ date('d-m-Y',strtotime($post->sourcing_date)) }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Reference Number:</span>
                                            <span>{{ $post->reference_no }}</span>
                                        </li>
                                    </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-container">
                                    <ul class="list-unstyled">
                                    </ul>
                                    </div>
                                </div>
                                <div class="col-md-12">

                                    <button class="float-end btn btn-sm btn-primary mb-2" data-bs-toggle="offcanvas" data-bs-target="#editDetails">Update Contact</button>

                                    {{-- @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_edit_careoff == 1)) --}}
                                    {{-- @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_edit_associate == 1) || (isset($permission) && $permission->cand_edit_associate == 1))
                                    <button class="float-end btn btn-sm btn-success mb-2 me-2" data-bs-toggle="offcanvas" data-bs-target="#editCareoff">Add / Edit</button>
                                    @endif  24-02-2025 --}}

                                    @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_add_edit == 1))
                                        <button class="float-end btn btn-sm btn-success mb-2 me-2" data-bs-toggle="offcanvas" data-bs-target="#editCareoff">Add / Edit</button>
                                    @endif

                                    <button class="float-end btn btn-sm btn-primary mb-2 me-2" data-bs-toggle="offcanvas" data-bs-target="#editconfirmby">Associate Confirm By</button>

                                    {{-- @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_edit_associate == 1))
                                    <button class="float-end btn btn-sm btn-success mb-2 me-2" data-bs-toggle="offcanvas" data-bs-target="#editAssociate">Edit Associate</button>
                                    @endif --}}


                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-candidate-tab" role="tabpanel">
                            <div class="nav-align-left mb-4">
                                <ul class="nav nav-pills me-3" role="tablist">
                                    <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-personal" aria-controls="navs-justified-personal" aria-selected="false">
                                    <i class="tf-icons ti ti-user-check ti-xs me-1"></i> Personal
                                    </button>
                                    </li>
                                    <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-experience" aria-controls="navs-justified-experience" aria-selected="false">
                                    <i class="tf-icons ti ti-files ti-xs me-1"></i> Experience & Skills
                                    </button>
                                    </li>
                                    <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-profile" aria-controls="navs-justified-profile" aria-selected="false">
                                    <i class="tf-icons ti ti-file-check ti-xs me-1"></i> Passport
                                    </button>
                                    </li>
                                    <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-musaned" aria-controls="navs-justified-musaned" aria-selected="false">
                                    <i class="tf-icons ti ti-files ti-xs me-1"></i> Musaned
                                    </button>
                                    </li>
                                    <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-justified-medical" aria-controls="navs-justified-medical" aria-selected="false">
                                    <i class="tf-icons ti ti-files ti-xs me-1"></i> Medical
                                    </button>
                                    </li>
                                    <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-justified-mofa-no" aria-controls="nav-justified-mofa-no" aria-selected="false">
                                        <i class="tf-icons ti ti-files ti-xs me-1"></i> Mofa No
                                    </button>
                                    </li>

                                    <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-justified-flight" aria-controls="nav-justified-flight" aria-selected="false">
                                        <i class="tf-icons ti ti-files ti-xs me-1"></i> Flight
                                    </button>
                                    </li>

                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="navs-justified-personal" role="tabpanel">
                                    <div class="row" id="nav-personal-div">
                                        <div class="col-md-12">
                                            <div class="info-container">
                                                <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Candidate Name:</span>
                                                    <span>{{ $post->cand_name }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Candidate Arabic Name:</span>
                                                    <span>@if($post->arcand_name != '') {{ $post->arcand_name }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Education:</span>
                                                    <span>@if($post->education_id != '') {{ $educationname->name }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Religion:</span>
                                                    {{-- <span>@if($post->religion != '') {{ $post->religion }} @else {{ '---' }} @endif</span> --}}
                                                    <span>@if(isset($religionname)) {{ $religionname->name }} @else {{ "---" }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Marital Status:</span>
                                                    <span>{{ $post->marital_status }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Occupation:</span>
                                                    <span>@if(isset($occupation)) {{ $occupation->eng_name.' ('.$occupation->ar_name.')' }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Expected Salary:</span>
                                                    <span>{{ $post->exp_sal }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Expected Work Location:</span>
                                                    <span>@if($post->expwp_id != '') {{ implode(",",$myexpwl) }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Age:</span>
                                                    <span>{{ $post->age }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Language Know:</span>
                                                    <span>{{ $post->lang_known }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Region:</span>
                                                    <span>@if(isset($candRegion)) {{ $candRegion->name }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">City:</span>
                                                    {{-- <span>@if(isset($candcity)) {{ $candcity->name }} @else {{ '---' }} @endif</span> --}}
                                                    <span>@if($post->candcity_text != '') {{ $post->candcity_text }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Embassy For:</span>
                                                    <span>
                                                    @if($post->embassy_for != '')
                                                    @if ($post->embassy_for == 1)
                                                    {{ 'MUMBAI VISA REQUIRED' }}
                                                    @endif
                                                    @if ($post->embassy_for == 2)
                                                    {{ 'DELHI VISA REQUIRED' }}
                                                    @endif
                                                    @else {{ '---' }} @endif
                                                    </span>
                                                </li>
                                                </ul>
                                            </div>
                                            <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editPersonal">Add / Edit</button>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="tab-pane fade" id="navs-justified-experience" role="tabpanel">
                                    <div class="row" id="nav-experience-div">
                                        <div class="col-md-12">
                                            <div class="info-container">
                                                <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Experience Region:</span>
                                                    <span>@if($post->gulfexperience == 1) {{ 'Indian Experience' }} @elseif($post->gulfexperience == 2) {{ 'Gulf Experience' }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Google Map:</span>
                                                    <span>@if($post->google_map == 1) Yes @else No @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Car known:</span>
                                                    <span>@if($post->carknown_id != '') {{ implode(',',$myCarkn) }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li>
                                                    <span class="fw-semibold me-1">Vehical Transmission:</span>
                                                    <span>
                                                        @if ($post->vehical_transmission != '')
                                                            {{ implode(",",$myVehtr) }}
                                                        @else
                                                            {{ '----' }}
                                                        @endif
                                                    </span>

                                                </li>
                                                @if ($post->experience != 0)
                                                @php
                                                $expls = explode(",",$post->experience);
                                                $professions = explode(",",$post->proff_id);
                                                $expcountries = explode(",",$post->expcountry_id);
                                                $expcities = explode(",",$post->expcity_id);
                                                // $expcities = explode(",",$post->expcity_id_text);
                                                @endphp
                                                @foreach ($expls as $key => $expl)
                                                @php
                                                $jobprof = DB::table('professions')->where('id','=',$professions[$key])->first();
                                                $expcountryl = DB::table('countries')->where('id','=',$expcountries[$key])->first();

                                                $expcityl = DB::table('expecworkcities')->where('id','=',$expcities[$key])->first();
                                                @endphp
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Experience (in year):</span>
                                                    <span>@if($expl != 0){{ $expl }} @else {{ 'Fresher' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Job Type:</span>
                                                    <span>@if(isset($jobprof)) {{ $jobprof->eng_name }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Experience (country name):</span>
                                                    <span>@if(isset($expcountryl)) {{ $expcountryl->name }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">City:</span>
                                                    <span>@if(isset($expcityl)) {{ $expcityl->name }} @else {{ '---' }} @endif</span>
                                                    {{-- <span>@if($post->expcity_id_text != '') {{ $expcities[$key] }} @else {{ '---' }} @endif</span> --}}
                                                </li>
                                                {{-- @if ($key != 0)
                                                <span class="text-primary">***</span>
                                                @endif --}}
                                                @endforeach
                                                @else
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Experience (in year):</span>
                                                    <span>Fresher</span>
                                                </li>
                                                @endif
                                                </ul>
                                            </div>
                                            <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editExperience">Add/ Edit</button>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                                    <div class="row" id="nav-passport-div">
                                        <div class="col-md-12">
                                            <div class="info-container">
                                                <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Passport No:</span>
                                                    <span>{{ $post->pass_no }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Passport Type:</span>
                                                    <span>{{ $post->pass_type }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Place of Issue:</span>
                                                    <span>@if(isset($poi)) {{ $poi->name }} @else {{ '---' }} @endif</span>
                                                    {{-- <span>@if($post->poi_text !='') {{ $post->poi_text }} @else {{ '---' }} @endif</span> --}}
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Date of Issue:</span>
                                                    <span>@if($post->doi != '') {{ date('d-m-Y',strtotime($post->doi)) }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Date of Expiry:</span>
                                                    <span>@if($post->doe != '') {{ date('d-m-Y',strtotime($post->doe)) }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Place of Birth:</span>
                                                    {{-- <span>@if(isset($placeofbirth)) {{ $placeofbirth->name }}  @else {{ '---' }} @endif</span> --}}
                                                    <span>@if($post->plb_text != '') {{ $post->plb_text }}  @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">DOB:</span>
                                                    <span>@if($post->dob != '') {{ date('d-m-Y',strtotime($post->dob)) }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Nationality:</span>
                                                    <span>@if(isset($nation)) {{ $nation->name }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Address:</span>
                                                    <span>@if($post->address != '') {{ $post->address }} @else {{ '---' }} @endif</span>
                                                </li>
                                                </ul>
                                            </div>
                                            <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editPassport">Add/Edit</button>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="tab-pane fade" id="navs-justified-musaned" role="tabpanel">
                                    <div class="row" id="nav-musaned-status-div" >
                                        <div class="col-md-12">
                                            <div class="info-container">
                                                <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Musaned Status:</span>
                                                    <span>
                                                    @if($post->musaned_status != '')
                                                    @if ($post->musaned_status == 1)
                                                    {{ 'Register' }}
                                                    @endif
                                                    @if ($post->musaned_status == 2)
                                                    {{ 'Not Register' }}
                                                    @endif
                                                    @if ($post->musaned_status == 3)
                                                    {{ 'Register at another office' }}
                                                    @endif
                                                    @else {{ '---' }} @endif
                                                    </span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Musaned Registration Date:</span>
                                                    <span>@if($post->musaned_reg_date != '') {{ date('d-m-Y',strtotime($post->musaned_reg_date)) }} @else {{ '---' }} @endif</span>
                                                </li>
                                                </ul>
                                            </div>
                                            <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editMusaned">Add/Edit</button>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="tab-pane fade" id="navs-justified-medical" role="tabpanel">
                                    <div class="row" id="nav-medical-div">
                                        @php
                                        if (($post->medical_examine_date != '' && $post->medical_expiry_date != '') || ($post->repeat_examine_date != '' && $post->medical_expiry_date != '')) {
                                            $diffdate = strtotime($post->medical_expiry_date) - time();
                                            $getDays = round($diffdate / (60 * 60 * 24));
                                            $days = $getDays." Days";
                                        }else{
                                            $days = "---";
                                        }

                                        // if ($post->medical_examine_date != '' && $post->medical_expiry_date != '') {
                                        //    $diffdate = strtotime($post->medical_expiry_date) - time();
                                        //    $getDays = round($diffdate / (60 * 60 * 24));
                                        //    $days = $getDays." Days";
                                        // } else {
                                        // $days = "---";
                                        // }
                                        @endphp
                                        <div class="col-md-12">
                                            <div class="info-container">
                                                <ul class="list-unstyled">
                                                @if ($post->medical_health_status != '')
                                                    <li class="mb-2">
                                                        <span class="fw-semibold me-1">Medical Status</span>
                                                        <span>{{$post->medical_health_status}}</span>
                                                    </li>
                                                @endif
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Medical Examine Date:</span>
                                                    <span>@if($post->medical_examine_date != '') {{ date('d-m-Y',strtotime($post->medical_examine_date)) }} @else {{ '---' }} @endif</span>
                                                </li>
                                                @if($post->medical_health_status == 'Repeat')
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Repeat Medical Examine Date:</span>
                                                    <span>@if($post->repeat_examine_date != '') {{ date('d-m-Y',strtotime($post->repeat_examine_date)) }} @else {{ '---' }} @endif</span>
                                                </li>
                                                @endif
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Medical Expiry Date:</span>
                                                    <span>@if($post->medical_expiry_date != '') {{ date('d-m-Y',strtotime($post->medical_expiry_date)) }} @else {{ '---' }} @endif</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Medical Expire (in Days):</span>
                                                    <span>{{ $days }}</span>
                                                </li>
                                                </ul>
                                            </div>
                                            <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editMedical">Add / Edit</button>
                                        </div>
                                    </div>
                                    </div>

                                    <div class="tab-pane fade" id="nav-justified-mofa-no" role="tabpanel">
                                    <div class="row" id="nav-mofa-div">

                                        <div class="col-md-12">
                                            <div class="info-container">
                                                <ul class="list-unstyled">

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Mofa No:</span>
                                                    <span>@if($post->mofa_no != '') {{ $post->mofa_no }} @else {{ '---' }} @endif</span>
                                                </li>


                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editMofaNo">Add / Edit</button>
                                        </div>
                                    </div>
                                    </div>

                                    <div class="tab-pane fade" id="nav-justified-flight" role="tabpanel">
                                    <div class="row" id="nav-flight-div">

                                        <div class="col-md-12">
                                            <div class="info-container">
                                                <ul class="list-unstyled">

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Fligt Date:</span>
                                                    <span>@if($post->flight_date != '') {{ $post->flight_date }} @else {{ '---' }} @endif</span>
                                                </li>


                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editFlight">Add / Edit</button>
                                        </div>
                                    </div>
                                    </div>

                                    <div class="tab-pane fade" id="navs-justified-embassy" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="info-container">
                                                <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Embassy For:</span>
                                                    <span>
                                                    @if($post->embassy_for != '')
                                                    @if ($post->embassy_for == 1)
                                                        {{ 'MUMBAI VISA REQUIRED' }}
                                                    @endif
                                                    @if ($post->embassy_for == 2)
                                                        {{ 'DELHI VISA REQUIRED' }}
                                                    @endif
                                                    @else {{ '---' }} @endif
                                                    </span>
                                                </li>
                                                </ul>
                                            </div>
                                            <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editEmbassy">Add/Edit</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--
                        <div class="tab-pane fade" id="navs-justified-personal" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-container">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Religion:</span>
                                            <span>@if($post->religion != '') {{ $post->religion }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Expected Salary:</span>
                                            <span>{{ $post->exp_sal }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Expected Work Location:</span>
                                            <span>@if($post->expwp_id != '') {{ implode(",",$myexpwl) }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Age:</span>
                                            <span>{{ $post->age }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Language Know:</span>
                                            <span>{{ $post->lang_known }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Region:</span>
                                            <span>@if(isset($candRegion)) {{ $candRegion->name }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">City:</span>
                                            <span>@if(isset($candcity)) {{ $candcity->name }} @else {{ '---' }} @endif</span>
                                        </li>
                                    </ul>
                                    </div>
                                    <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editPersonal">Add/Edit</button>
                                </div>
                            </div>
                        </div>
                        --}}
                        {{--
                        <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-container">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Passport No:</span>
                                            <span>{{ $post->pass_no }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Passport Type:</span>
                                            <span>{{ $post->pass_type }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Place of Issue:</span>
                                            <span>@if(isset($poi)) {{ $poi->name }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Date of Issue:</span>
                                            <span>@if($post->doi != '') {{ date('d-m-Y',strtotime($post->doi)) }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Date of Expiry:</span>
                                            <span>@if($post->doe != '') {{ date('d-m-Y',strtotime($post->doe)) }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Place of Birth:</span>
                                            <span>@if(isset($placeofbirth)) {{ $placeofbirth->name }}  @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">DOB:</span>
                                            <span>@if($post->dob != '') {{ date('d-m-Y',strtotime($post->dob)) }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Nationality:</span>
                                            <span>@if(isset($nation)) {{ $nation->name }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Address:</span>
                                            <span>@if($post->address != '') {{ $post->address }} @else {{ '---' }} @endif</span>
                                        </li>
                                    </ul>
                                    </div>
                                    <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editPassport">Edit</button>
                                </div>
                            </div>
                        </div>
                        --}}
                        {{--
                        <div class="tab-pane fade" id="navs-justified-experience" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-container">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Experience Region</span>
                                            <span>@if($post->gulfexperience == 1) {{ 'Indian Experience' }} @elseif($post->gulfexperience == 2) {{ 'Gulf Experience' }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Google Map:</span>
                                            <span>@if($post->google_map == 1) Yes @else No @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Car known:</span>
                                            <span>@if($post->carknown_id != '') {{ implode(',',$myCarkn) }} @else {{ '---' }} @endif</span>
                                        </li>
                                        @if ($post->experience != 0)
                                        @php
                                        $expls = explode(",",$post->experience);
                                        $professions = explode(",",$post->proff_id);
                                        $expcountries = explode(",",$post->expcountry_id);
                                        $expcities = explode(",",$post->expcity_id);
                                        @endphp
                                        @foreach ($expls as $key => $expl)
                                        @php
                                        $jobprof = DB::table('professions')->where('id','=',$professions[$key])->first();
                                        $expcountryl = DB::table('countries')->where('id','=',$expcountries[$key])->first();
                                        $expcityl = DB::table('cities')->where('id','=',$expcities[$key])->first();
                                        @endphp
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Experience (in year):</span>
                                            <span>@if($expl != 0){{ $expl }} @else {{ 'Fresher' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Job Type:</span>
                                            <span>@if(isset($jobprof)) {{ $jobprof->eng_name }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Experience (country name):</span>
                                            <span>@if(isset($expcountryl)) {{ $expcountryl->name }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">City:</span>
                                            <span>@if(isset($expcityl)) {{ $expcityl->name }} @else {{ '---' }} @endif</span>
                                        </li>
                                        @if ($key != 0)
                                        <span class="text-primary">***</span>
                                        @endif
                                        @endforeach
                                        @else
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Experience (in year):</span>
                                            <span>Fresher</span>
                                        </li>
                                        @endif
                                    </ul>
                                    </div>
                                    <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editExperience">Edit</button>
                                </div>
                            </div>
                        </div>
                        --}}
                        {{--
                        <div class="tab-pane fade" id="navs-justified-medical" role="tabpanel">
                            <div class="row">
                                @php
                                if ($post->medical_examine_date != '' && $post->medical_expiry_date != '') {
                                $diffdate = strtotime($post->medical_expiry_date) - time();
                                $getDays = round($diffdate / (60 * 60 * 24));
                                $days = $getDays." Days";
                                } else {
                                $days = "---";
                                }
                                @endphp
                                <div class="col-md-12">
                                    <div class="info-container">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Medical Examine Date:</span>
                                            <span>@if($post->medical_examine_date != '') {{ date('d-m-Y',strtotime($post->medical_examine_date)) }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Medical Expiry Date:</span>
                                            <span>@if($post->medical_expiry_date != '') {{ date('d-m-Y',strtotime($post->medical_expiry_date)) }} @else {{ '---' }} @endif</span>
                                        </li>
                                        <li class="mb-2">
                                            <span class="fw-semibold me-1">Medical Expire (in Days):</span>
                                            <span>{{ $days }}</span>
                                        </li>
                                    </ul>
                                    </div>
                                    <button class="float-end btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#editMedical">Add/Edit</button>
                                </div>
                            </div>
                        </div>
                        --}}
                        <div class="tab-pane fade" id="nav-cand-payment-tab" role="tabpanel">

                            <div class="row" id="nav-service-charge-div">
                                @if ($candsc->count() > 0)
                                    <div class="col-md-12 table-responsive" style="margin-bottom: 20px;">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>SERVICES CHARGES</th>
                                                    <th>Notes</th>
                                                    <th>GIVEN BY</th>
                                                    <th>CREATED BY</th>
                                                    <th>STATUS</th>
                                                    <th>GIVEN DATE</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($candsc as $value)
                                                @php
                                                    $is = 1;
                                                    $carbonDateTime = \Carbon\Carbon::parse($value->updated_at);
                                                    $activeService = DB::table('candsercharges')->where('id','=',$value->id)->where('status','=',1)->count();
                                                    $createBy = DB::table('admins')->where('id','=',$value->admin_id)->first();
                                                @endphp
                                                <tr>
                                                    <th>{{ $is++ }}</th>
                                                    <th>₹{{ $value->amount }}</th>
                                                    <th>
                                                        @if($value->approval_notes != '') 
                                                            {{ $value->approval_notes }} 
                                                        @else 
                                                            {{ $value->notes }} 
                                                        @endif
                                                    </th>
                                                    <th>
                                                        @foreach($users as $user) 
                                                            @if($user->id == $value->given_by) 
                                                                {{ $user->name }}  
                                                            @endif 
                                                        @endforeach
                                                    </th>
                                                    <th>{{ $createBy->name }}</th>
                                                    <th>
                                                        @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->updt_status_service_charge == 1))
                                                            @if($value->status == 1)
                                                                <span class="text-success" data-bs-toggle="modal" data-bs-target="#deactiveStatusModal" data-id="{{ $value->id }}">Approved</span>
                                                            @else
                                                                <span class="text-danger" data-bs-toggle="modal" data-bs-target="#activeStatusModal" data-id="{{ $value->id }}">Not Approved</span>
                                                            @endif
                                                        @else
                                                            @if($value->status == 1)
                                                                <span class="text-success">Approved</span>
                                                            @else
                                                                <span class="text-danger">Not Approved</span>
                                                            @endif
                                                        @endif
                                                    </th>
                                                    <th>{{ $carbonDateTime->format('Y-m-d') }}</th>
                                                    <th>
                                                        @if (Auth::guard('partner')->user()->user_type == 1)
                                                            <a href="#" data-bs-target="#UpdateServiceCharge" data-bs-toggle="offcanvas" data-id="{{$value->id}}">
                                                                <i class="ti ti-edit"></i>
                                                            </a>
                                                            <a href="#" id="servdelcandamt" class="servdelcandamt" data-id="{{$value->id}}">
                                                                <i class="ti ti-trash"></i>
                                                            </a>
                                                        @else
                                                            @if (isset($permission) && $permission->edit_service_charge == 1)
                                                                <a href="#" data-bs-target="#UpdateServiceCharge" data-bs-toggle="offcanvas" data-id="{{$value->id}}">
                                                                    <i class="ti ti-edit"></i>
                                                                </a>
                                                            @endif
                                                            @if (isset($permission) && $permission->delete_service_charge == 1)
                                                                <a href="#" id="servdelcandamt" class="servdelcandamt" data-id="{{$value->id}}">
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </th>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                            <div class="row" id="nav-cand-payment-div">
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Amount</th>
                                                <th>Payment Mode</th>
                                                <th>Transaction No</th>
                                                <th>Bank To</th>
                                                <th>Create By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $ip = 1; @endphp
                                            @foreach ($paycands as $paycand)
                                                @php
                                                    $bank_to = str_replace('_',' ',$paycand->bank_to);
                                                @endphp
                                                <tr>
                                                    <th>{{ $ip++ }}</th>
                                                    <th>₹{{ $paycand->amount }}</th>
                                                    <th>{{ $paycand->payment_mode }}</th>
                                                    <th>
                                                        {{ $paycand->txn_id }}
                                                        @if($paycand->payment_slip != '')
                                                            <span class="ti ti-photo" data-bs-toggle="modal" data-bs-target="#viewPaymentSlip" data-id="{{ $paycand->id }}"></span>
                                                        @endif
                                                    </th>
                                                    <th>{{ $bank_to }}</th>
                                                    <th>{{ $paycand->uname }}</th>
                                                    <th>
                                                        @if (Auth::guard('partner')->user()->user_type == 1)
                                                            <a href="#" data-bs-target="#editCandPayment" data-bs-toggle="offcanvas" data-id="{{ $paycand->id }}">
                                                                <i class="ti ti-edit"></i>
                                                            </a>
                                                            <a href="#" id="delcandamt" class="delcandamt" data-id="{{ $paycand->id }}">
                                                                <i class="ti ti-trash"></i>
                                                            </a>
                                                        @else
                                                            @if (isset($permission) && $permission->edit_cand_payment == 1)
                                                                <a href="#" data-bs-target="#editCandPayment" data-bs-toggle="offcanvas" data-id="{{ $paycand->id }}">
                                                                    <i class="ti ti-edit"></i>
                                                                </a>
                                                            @endif
                                                            @if (isset($permission) && $permission->delete_cand_payment == 1)
                                                                <a href="#" id="delcandamt" class="delcandamt" data-id="{{ $paycand->id }}">
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </th>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <th class="float-right" colspan="4">Total Amount Received: {{$PaytotalAmount}}</th>
                                                <th colspan="2">Balance Amount: ₹{{$CalAmount}}</th>
                                                <th></th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-12 mt-2">
                                    @if ($candsc->count() > 0)
                                        <button class="btn btn-sm btn-primary me-1 float-end" data-bs-toggle="offcanvas" data-bs-target="#addCandPayment">
                                            Add Payment
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-primary me-1 float-end" id="paymentError">
                                            Add Payment
                                        </button>
                                    @endif

                                    @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->add_service_charge == 1))
                                        <button class="btn btn-sm btn-success me-1 float-end" data-bs-toggle="offcanvas" data-bs-target="#addServiceCharge">
                                            Add Service Charge
                                        </button>
                                    @endif
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" id="navs-justified-messages" role="tabpanel">
                            <div class="row" id="nav-upload-file-div">
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

                                    @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#deletePhoto"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                    @endif
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
                                    @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#deletePassFile"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                    @endif
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
                                    @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#deletePassBackFile"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                    @endif
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
                                    @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#deleteLice"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                    @endif
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
                                    @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#deleteFullImg"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                    @endif
                                    </div>
                                </div>
                                @endif
                                @if ($filePhotos->count() > 0)
                                @foreach ($filePhotos as $filePhoto)
                                <div class="col-md-2">
                                    <span class="badge bg-label-secondary">{{ $filePhoto->label }}</span>
                                    <div class="mt-2">
                                    <a href="{{ asset('admin/assets/images/candidate/'.$filePhoto->filename) }}" target="_blank">
                                        <img src="{{ asset('admin/assets/images/candidate/'.$filePhoto->filename) }}" alt="{{ $post->filename }}" class="d-block w-px-100 h-px-100 rounded" />

                                    </a>
                                    </div>
                                    <div class="mt-1">
                                    <a href="{{ asset('admin/assets/images/candidate/'.$filePhoto->filename) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                    <a href="{{ asset('admin/assets/images/candidate/'.$filePhoto->filename) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                                    @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#deletecandFile" data-id="{{ $filePhoto->id }}"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                    @endif
                                    </div>
                                </div>
                                @endforeach
                                @endif
                                @if ($post->musaned_file != '')
                                <div class="col-md-2">
                                    <span class="badge bg-label-secondary">Musaned Copy</span>
                                    <div class="mt-2">
                                    <a href="{{ asset('admin/assets/images/candidate/'.$post->musaned_file) }}" target="_blank">
                                        <img src="{{ asset('admin/assets/images/candidate/'.$post->musaned_file) }}" alt="{{ $post->musaned_file }}" class="d-block w-px-100 h-px-100 rounded" />

                                    </a>
                                    </div>
                                    <div class="mt-1">
                                    <a href="{{ asset('admin/assets/images/candidate/'.$post->musaned_file) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                    <a href="{{ asset('admin/assets/images/candidate/'.$post->musaned_file) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                                    @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_delete_file == 1))
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#deleteMusanedFile"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a>
                                    @endif
                                    </div>
                                </div>
                                @endif

                                @if ($paycands->count() > 0)
                                    @foreach ($paycands as $paycand25)
                                    @if ($paycand25->payment_slip != '')
                                    <div class="col-md-2">
                                        <div class="mt-2">
                                            <a href="{{ asset('admin/assets/images/payment/candidate/'.$paycand25->payment_slip) }}" target="_blank">
                                                <img src="{{ asset('admin/assets/images/payment/candidate/'.$paycand25->payment_slip) }}" alt="{{ $paycand25->payment_slip }}" class="d-block w-px-100 h-px-100 rounded" />

                                            </a>
                                        </div>
                                        <div class="mt-1">
                                            <a href="{{ asset('admin/assets/images/payment/candidate/'.$paycand25->payment_slip) }}" target="_blank"><i class="tf-icons ti ti-eye ti-xs me-1"></i></a>
                                            <a href="{{ asset('admin/assets/images/payment/candidate/'.$paycand25->payment_slip) }}" download=""><i class="tf-icons ti ti-download ti-xs me-1"></i></a>
                                            {{-- <a href="#" data-bs-toggle="modal" data-bs-target="#deletecandFile" data-id="{{ $filePhoto->id }}"><i class="tf-icons ti ti-trash ti-xs me-1"></i></a> --}}
                                        </div>
                                    </div>
                                    @endif

                                    @endforeach
                                @endif

                                <div class="col-md-12">
                                    <button class="float-end btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocs2">Upload Docs</button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-cvexecute-tab" role="tabpanel">
                            <div class="row" id="nav-cvexecute-tab-div">
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Company Name</th>
                                            <th>CV Execute Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $ipc = 1;
                                        @endphp
                                        @foreach ($partners as $partner)
                                            @php
                                            // CV execute File
                                            $recCVex = DB::table('companycvexecutes')->where('partner_id','=',$partner->id)->where('cand_id','=',$post->id)->first();
                                            @endphp
                                            <tr>
                                                <th>{{ $ipc++ }}</th>
                                                <th>{{ $partner->rec_off_name }}</th>
                                                <th>
                                                @if (isset($recCVex))
                                                    @if ($recCVex->status == 1)
                                                        <span class="bg-label-success">Execute</span>
                                                    @else
                                                        <span class="bg-label-danger">Not Execute</span>
                                                    @endif
                                                @endif
                                                </th>
                                                <th>
                                                @if (isset($recCVex))
                                                    @if ($recCVex->status == 1)
                                                        <a href="{{ asset('admin/assets/images/pdf/partner/'.$recCVex->cv_file) }}" title="Download CV" target="_blank"><i class="ti ti-file-download"></i> Download CV</a>

                                                        <a href="javascript:void(0)" class="partnerExcuteCVBtn" id="partnerExcuteCVBtn" data-id="{{ $partner->id }}" title="Execute CV"><i class="ti ti-file-upload"></i> Execute CV</a>
                                                        {{-- <a href="{{ url('admin/candidate/cv/execute/'.$post->id.'/'.$partner->id) }}" title="Execute CV"><i class="ti ti-file-upload"></i> Execute CV</a> --}}
                                                        {{-- <a href="" title="Delete CV"><i class="ti ti-trash"></i></a> --}}
                                                    @else
                                                        <a href="#" id="errorDownload1" class="errorDownload1" title="Download CV"><i class="ti ti-file-download"></i> Download CV</a>
                                                        {{-- <a href="javascript:void(0)" class="partnerExcuteCVBtnError" id="partnerExcuteCVBtnError" data-id="{{ $partner->id }}" title="Execute CV"><i class="ti ti-file-upload"></i> Execute CV</a> --}}
                                                        <a href="javascript:void(0)" class="partnerExcuteCVBtn" id="partnerExcuteCVBtn" data-id="{{ $partner->id }}" title="Execute CV"><i class="ti ti-file-upload"></i> Execute CV</a>

                                                        {{-- <a href="{{ url('admin/candidate/cv/execute/'.$post->id.'/'.$partner->id) }}" title="Execute CV"><i class="ti ti-file-upload"></i> Execute CV</a> --}}
                                                        {{-- <a href="" title="Delete CV"><i class="ti ti-trash"></i></a> --}}
                                                    @endif
                                                @else
                                                    <a href="#" id="errorDownload2" class="errorDownload2" title="Download CV"><i class="ti ti-file-download"></i> Download CV</a>
                                                    <a href="javascript:void(0)" class="partnerExcuteCVBtn" id="partnerExcuteCVBtn" data-id="{{ $partner->id }}" title="Execute CV"><i class="ti ti-file-upload"></i> Execute CV</a>
                                                    {{-- <a href="{{ url('admin/candidate/cv/execute/'.$post->id.'/'.$partner->id) }}" title="Execute CV"><i class="ti ti-file-upload"></i> Execute CV</a>      --}}
                                                    {{-- <a href="" title="Delete CV"><i class="ti ti-trash"></i></a> --}}
                                                @endif
                                                </th>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-web-tab" role="tabpanel">
                            <div class="nav-align-left mb-4">
                                <ul class="nav nav-pills me-3" role="tablist">
                                    <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-download-cv" aria-controls="nav-download-cv" aria-selected="false">
                                    <i class="tf-icons ti ti-cloud-download ti-xs me-1"></i> Download CV
                                    </button>
                                    </li>
                                    <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-booking-hiring" aria-controls="nav-booking-hiring" aria-selected="true">
                                    <i class="tf-icons ti ti-checklist ti-xs me-1"></i> Booking Details
                                    </button>
                                    </li>
                                    <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-cand-booking-limit" aria-controls="nav-cand-booking-limit" aria-selected="true">
                                    <i class="tf-icons ti ti-ban ti-xs me-1"></i> Booking Limit
                                    </button>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="navs-download-cv" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Customer Name</th>
                                                    <th>Download Date</th>
                                                </tr>
                                                </thead>
                                                @if ($candDownloads)
                                                <tbody>
                                                @php
                                                $i = 1;
                                                @endphp
                                                @foreach ($candDownloads as $candDownload)
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $candDownload->uname }}</td>
                                                    <td>{{ date('d-m-Y h:i:s A',strtotime($candDownload->created_at)) }}</td>
                                                </tr>
                                                @endforeach
                                                </tbody>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-booking-hiring" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Reference No</th>
                                                    <th>Employer Name</th>
                                                    <th>Booking Status</th>
                                                    <th>Visa Status</th>
                                                    <th>Payment Status</th>
                                                    <th>Booking Date</th>
                                                </tr>
                                                </thead>
                                                @if (isset($candBooks))
                                                @foreach ($candBooks as $candBook)
                                                <tr>
                                                <td>{{ $candBook->reference_no }}</td>
                                                <td>{{ $candBook->uname }}</td>
                                                <td>
                                                    @if ($candBook->booking_status == 0)
                                                    <span class="badge bg-label-info">Pending</span>
                                                    @elseif ($candBook->booking_status == 1)
                                                    <span class="badge bg-label-success">Confirm</span>
                                                    @elseif ($candBook->booking_status == 2)
                                                    <span class="badge bg-label-danger">Cancel</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($candBook->visa_status == 0)
                                                    <span class="badge bg-label-danger">Pending</span>
                                                    @elseif ($candBook->visa_status == 1)
                                                    <span class="badge bg-label-success">Complete</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($candBook->payment_status == 0)
                                                    <span class="badge bg-label-danger">Pending</span>
                                                    @elseif ($candBook->payment_status == 1)
                                                    <span class="badge bg-label-success">Complete</span>
                                                    @endif
                                                </td>
                                                <td>{{ date('d-m-Y h:i:s A',strtotime($candBook->booking_date)) }}</td>
                                                </tr>
                                                @endforeach
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="tab-pane fade" id="nav-cand-booking-limit" role="tabpanel">
                                    <form action="{{ route('admin.booking.limit',$post->id) }}" method="POST" id="candBookinglimit">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="candlimit" class="form-label">Booking Limit <span class="text-danger">*</span></label>
                                                <input type="text" name="limit" id="candlimit" class="form-control" value="@if(isset($candcount)) {{ $candcount->limit }} @endif" placeholder="Enter booking limit...">
                                            </div>
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-reminder-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Title</th>
                                                    <th>Description</th>
                                                    <th>Reminder Type</th>
                                                    <th>Due Date</th>
                                                    <th>Careoff</th>
                                                    <th>Create By</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $ir = 1;
                                                @endphp
                                                @foreach ($reminders as $reminder)
                                                    <tr>
                                                        <td>{{ $ir++ }}</td>
                                                        <td>{{ $reminder->title }}</td>
                                                        <td>{{ $reminder->desc }}</td>
                                                        <td>{{ $reminder->todolabelName }}</td>
                                                        <td>{{ date('d-m-Y',strtotime($reminder->due_date)).' '.date('h:i A',strtotime($reminder->time)) }}</td>
                                                        <td>{{ $reminder->careoffName }}</td>
                                                        <td>{{ $reminder->adminName }}</td>
                                                        <td>
                                                            @if ($reminder->status == 1)
                                                                <span class="badge bg-label-success" data-bs-toggle="modal" data-bs-target="#updateReminder" data-id="{{ $reminder->id }}">Active</span>
                                                            @else
                                                                <span class="badge bg-label-danger">Deactive</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                        </div>
                            <div class="tab-pane fade" id="navs-justified-actvities" role="tabpanel">
                                <div class="nav-align-left mb-4">
                                    <ul class="nav nav-pills me-3" role="tablist">
                                        <li class="nav-item">
                                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#nav-confirmby-tab" aria-controls="nav-confirmby-tab" aria-selected="false">
                                                <i class="tf-icons ti ti-check ti-xs me-1"></i> Associate Confirm By
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-normal-activities" aria-controls="nav-normal-activities" aria-selected="false">
                                                <i class="tf-icons ti ti-checklist ti-xs me-1"></i> Activities
                                            </button>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="nav-confirmby-tab" role="tabpanel">
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>Associate</th>
                                                                    <th>Confirm By</th>
                                                                    <th>Date</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if ($assocconfirmbys->count() > 0)
                                                                    @foreach ($assocconfirmbys as $assocconfirmby2)
                                                                        <tr>
                                                                            <td>@if($assocconfirmby2->assoc_id != '') {{ $assocconfirmby2->assoc->pty_full_name }} @else {{ '---' }} @endif</td>
                                                                            <td>{{ $assocconfirmby2->confirmby->name }}</td>
                                                                            <td>{{ date('d-m-Y h:i:s',strtotime($assocconfirmby2->confirm_datetime)) }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="3" class="text-center">No Data Found</td>
                                                                    </tr>
                                                                @endif

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade show" id="nav-normal-activities" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @if ($timelines->count() > 0)
                                                        <ul class="timeline mt-3 mb-0">
                                                            @php
                                                                $totalTimeline = $timelines->count();
                                                            @endphp
                                                            @foreach ($timelines as $timeline)
                                                                @php
                                                                    if ($timeline->admin_id != '') {
                                                                        $aUser = DB::table('admins')->where('id','=',$timeline->admin_id)->first();
                                                                    }
                                                                    if ($timeline->user_id != '') {
                                                                        $cUser = DB::table('users as user')
                                                                        ->select('user.name as uname', 'user.photo')
                                                                        ->where('user.id', '=', $timeline->user_id)
                                                                        ->first();

                                                                        // Check if $cUser is null before accessing its properties
                                                                        if ($cUser) {
                                                                            // $cUser is not null, so you can safely access its properties
                                                                            $userName = $cUser->uname;
                                                                            $userPhoto = $cUser->photo;
                                                                        } else {
                                                                            // Handle the case where no user is found
                                                                            // For example, you can set default values or show an error message
                                                                            $userName = 'Unknown';
                                                                            $userPhoto = 'default.jpg';
                                                                        }
                                                                    } else {
                                                                        // Handle the case where $timeline->user_id is empty
                                                                        // For example, you can set default values or show an error message
                                                                        $userName = 'Unknown';
                                                                        $userPhoto = 'default.jpg';
                                                                    }
                                                                    if ($timeline->partner_id != '') {
                                                                        $pUser = DB::table('partners as partner')->where('id','=',$timeline->partner_id)->first();
                                                                    }
                                                                    $staeNum = rand(0,6);
                                                                    $states = ['primary','success','danger','warning','info','secondary','dark'];
                                                                    $state = $states[$staeNum];
                                                                    $borderNumber = $totalTimeline - 1;
                                                                    if ($borderNumber > 0) {
                                                                        $borderStyle = "pb-4 border-left-dashed";
                                                                    } else {
                                                                        $borderStyle = "pb-3 border-0";
                                                                    }
                                                                @endphp
                                                                <li class="timeline-item timeline-item-{{ $state.' '.$borderStyle }}">
                                                                    <span class="timeline-indicator timeline-indicator-{{ $state }}">
                                                                        <i class="ti {{ $timeline->icons }}"></i>
                                                                    </span>
                                                                    <div class="timeline-event">
                                                                        <div class="timeline-header border-bottom mb-3">
                                                                            <h6 class="mb-0">{{ $timeline->headline }}</h6>
                                                                            <span class="text-muted">{{ date('d-m-Y h:i A',strtotime($timeline->created_at)) }}</span>
                                                                        </div>
                                                                        <p>{{ $timeline->bodyMessage }}</p>
                                                                        <div class="d-flex justify-content-between flex-wrap gap-2">
                                                                            <div class="d-flex flex-wrap">
                                                                                @if ($timeline->admin_id != '')
                                                                                    <div class="avatar me-3">
                                                                                        <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle" />
                                                                                    </div>
                                                                                    <div>
                                                                                        @if ($aUser)
                                                                                            <p class="mb-0">{{ $aUser->name }}</p>
                                                                                        @else
                                                                                            <p class="mb-0">Unknown</p>
                                                                                        @endif
                                                                                    </div>
                                                                                @endif
                                                                                @if ($timeline->user_id != '')
                                                                                    <div class="avatar me-3">
                                                                                        @if ($userPhoto != '')
                                                                                        <img src="{{ asset('user/img/avatars',$userPhoto) }}" alt="Avatar" class="rounded-circle" />
                                                                                        @else
                                                                                        <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle" />
                                                                                        @endif
                                                                                    </div>
                                                                                    <div>
                                                                                        <p class="mb-0">{{ $userName }}</p>

                                                                                    </div>
                                                                                @endif
                                                                                @if ($timeline->partner_id != '')
                                                                                    <div class="avatar me-3">
                                                                                        <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle" />
                                                                                    </div>
                                                                                    <div>
                                                                                        <p class="mb-0">{{ $pUser->rec_off_name }}</p>

                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                @php
                                                                    $totalTimeline--;
                                                                @endphp
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                {{-- <div class="nav-align-left mb-4">
                                <ul class="nav nav-pills me-3" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#nav-care-off" aria-controls="nav-care-off" aria-selected="false">
                                        <i class="tf-icons ti ti-cloud-download ti-xs me-1"></i> Careoff
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-associate" aria-controls="nav-associate" aria-selected="true">
                                        <i class="tf-icons ti ti-checklist ti-xs me-1"></i> Associate
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-activities" aria-controls="nav-activities" aria-selected="true">
                                        <i class="tf-icons ti ti-ban ti-xs me-1"></i> Activities
                                        </button>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="nav-care-off" role="tabpanel"></div>
                                    <div class="tab-pane fade" id="nav-associate" role="tabpanel"></div>
                                    <div class="tab-pane fade" id="nav-activities" role="tabpanel"></div>
                                </div>
                                </div> --}}

                                {{-- 23-01-2025 --}}
                                {{-- <div class="row">
                                    <div class="col-md-6">
                                        @if ($timelines->count() > 0)
                                            <ul class="timeline mt-3 mb-0">
                                                @php
                                                    $totalTimeline = $timelines->count();
                                                @endphp
                                                @foreach ($timelines as $timeline)
                                                    @php
                                                        if ($timeline->admin_id != '') {
                                                            $aUser = DB::table('admins')->where('id','=',$timeline->admin_id)->first();
                                                        }
                                                        if ($timeline->user_id != '') {
                                                            $cUser = DB::table('users as user')
                                                            ->select('user.name as uname', 'user.photo')
                                                            ->where('user.id', '=', $timeline->user_id)
                                                            ->first();

                                                            // Check if $cUser is null before accessing its properties
                                                            if ($cUser) {
                                                                // $cUser is not null, so you can safely access its properties
                                                                $userName = $cUser->uname;
                                                                $userPhoto = $cUser->photo;
                                                            } else {
                                                                // Handle the case where no user is found
                                                                // For example, you can set default values or show an error message
                                                                $userName = 'Unknown';
                                                                $userPhoto = 'default.jpg';
                                                            }
                                                        } else {
                                                            // Handle the case where $timeline->user_id is empty
                                                            // For example, you can set default values or show an error message
                                                            $userName = 'Unknown';
                                                            $userPhoto = 'default.jpg';
                                                        }
                                                        if ($timeline->partner_id != '') {
                                                            $pUser = DB::table('partners as partner')->where('id','=',$timeline->partner_id)->first();
                                                        }
                                                        $staeNum = rand(0,6);
                                                        $states = ['primary','success','danger','warning','info','secondary','dark'];
                                                        $state = $states[$staeNum];
                                                        $borderNumber = $totalTimeline - 1;
                                                        if ($borderNumber > 0) {
                                                            $borderStyle = "pb-4 border-left-dashed";
                                                        } else {
                                                            $borderStyle = "pb-3 border-0";
                                                        }
                                                    @endphp
                                                    <li class="timeline-item timeline-item-{{ $state.' '.$borderStyle }}">
                                                        <span class="timeline-indicator timeline-indicator-{{ $state }}">
                                                            <i class="ti {{ $timeline->icons }}"></i>
                                                        </span>
                                                        <div class="timeline-event">
                                                            <div class="timeline-header border-bottom mb-3">
                                                                <h6 class="mb-0">{{ $timeline->headline }}</h6>
                                                                <span class="text-muted">{{ date('d-m-Y h:i A',strtotime($timeline->created_at)) }}</span>
                                                            </div>
                                                            <p>{{ $timeline->bodyMessage }}</p>
                                                            <div class="d-flex justify-content-between flex-wrap gap-2">
                                                                <div class="d-flex flex-wrap">
                                                                    @if ($timeline->admin_id != '')
                                                                        <div class="avatar me-3">
                                                                            <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle" />
                                                                        </div>
                                                                        <div>
                                                                            @if ($aUser)
                                                                                <p class="mb-0">{{ $aUser->name }}</p>
                                                                            @else
                                                                                <p class="mb-0">Unknown</p>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                    @if ($timeline->user_id != '')
                                                                        <div class="avatar me-3">
                                                                            @if ($userPhoto != '')
                                                                            <img src="{{ asset('user/img/avatars',$userPhoto) }}" alt="Avatar" class="rounded-circle" />
                                                                            @else
                                                                            <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle" />
                                                                            @endif
                                                                        </div>
                                                                        <div>
                                                                            <p class="mb-0">{{ $userName }}</p>

                                                                        </div>
                                                                    @endif
                                                                    @if ($timeline->partner_id != '')
                                                                        <div class="avatar me-3">
                                                                            <img src="{{ asset('admin/assets/img/avatars/avatar.jpg') }}" alt="Avatar" class="rounded-circle" />
                                                                        </div>
                                                                        <div>
                                                                            <p class="mb-0">{{ $pUser->rec_off_name }}</p>

                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @php
                                                        $totalTimeline--;
                                                    @endphp
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Upload video link start -->
        <!-- Modal -->
        <div class="modal fade" id="upvidlink" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Upload Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!-- Tabs -->
                    <ul class="nav nav-tabs" id="myTabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="link-tab" data-bs-toggle="tab" href="#video-upload">Video Upload</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="video-tab" data-bs-toggle="tab" href="#video-link">Introduction Video</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="test_video-tab" data-bs-toggle="tab" href="#test-video-link">Trade Test Video</a>
                        </li>
                    </ul>
                    <!-- Tab Content -->
                    <div class="tab-content mt-2">
                        <!-- Link Form Tab -->
                        <div class="tab-pane fade show active" id="video-upload">
                            <form action="/admin/candidate/videoFile/{{$post->id}}" method="POST" id="upvidlinkval" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label class="form-label" for="add-upload-pp3">Upload File</label>
                                        <input type="file" name="video_file" id="add-upload-pp3"  accept="video/*" class="form-control"  @if($post->video_file != '') value="{{ $post->video_file }}" @endif>
                                    </div>
                                </div>
                                </div>
                                <button type="button" class="btn btn-label-secondary mt-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary mt-3">Save changes</button>
                            </form>
                        </div>
                        <!-- Video Form Tab -->
                        <div class="tab-pane fade" id="video-link">
                            <form action="{{ route('admin.candidate.video.store',$post->id) }}" method="POST" id="upvidlinkval">
                                @csrf
                                <label for="add-video-link" class="form-label">Introduction Video</label>
                                <input class="form-control" type="text" id="add-video-link" @if($post->video_link != '') value="{{ $post->video_link }}" @endif name="video_link" placeholder="Enter video link..." />
                                <button type="submit" class="btn btn-primary mt-3">Save Video Link</button>
                            </form>
                        </div>
                        <!-- Test Video Tab -->
                        <div class="tab-pane fade" id="test-video-link">
                            <form action="{{ route('admin.candidate.testvideo.store',$post->id) }}" method="POST" id="uptestvidlinkval">
                                @csrf
                                <label for="add-test-video-link" class="form-label">Trade Test Video Link</label>
                                <input class="form-control" type="text" id="add-test-video-link" @if($post->trade_test_video_link != '') value="{{ $post->trade_test_video_link }}" @endif name="trade_test_video_link" placeholder="Enter video link..." />
                                <button type="submit" class="btn btn-primary mt-3">Save Video Link</button>
                            </form>
                        </div>
                    </div>
                    </div>
                    <!--  <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    </div> -->
                </div>
            </div>
        </div>
        <!-- Upload Video Link end -->
        <!-- Candidate Image Upload Modal Start-->
        <div class="modal fade" id="imageUpload" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Upload Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.photo.store',$post->id) }}" method="POST" id="photoValid" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                <label for="add-photo-file" class="form-label">Photo</label>
                                <input class="form-control" type="file" id="add-photo-file" name="file_photo" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Candidate Image Upload Modal End -->
        <!-- Execute Multiple CV with B2C and B2B Start-->
        <div class="modal fade" id="cvExecuteMULTI" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel125">CV Execute</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="POST" id="cvExecuteMultiple">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-12">
                                <input type="hidden" name="candcv_id" id="candcv_id" value="{{ $post->id }}">
                                <div class="mb-3">
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="cv_execute_for" id="cv_execute_for_b2c" checked value="1"/>
                                    <label class="form-check-label" for="cv_execute_for_b2c">B2C</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="cv_execute_for" id="cv_execute_for_b2b" value="0"/>
                                    <label class="form-check-label" for="cv_execute_for_b2b">B2B</label>
                                </div>
                            </div>
                                <span class="text-danger" id="errorPartner"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary cvExecuteMultipleBtn" id="cvExecuteMultipleBtn">Execute CV</button>
                        {{-- <button type="submit" class="btn btn-primary">Download Cv</button> --}}
                        {{-- <button type="button" class="btn btn-primary" id="downloadCandCV2">Download Cv</button> 10-07-2023 --}}
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Execute Multiple CV with B2C and B2B End-->
        <!-- Download CV as per company Start -->
        <div class="modal fade" id="downloadcvcomp" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel125">Download CV as per company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.download.cvcompany',$post->id) }}" method="POST" id="downloadCVvalidation">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            @php
                            $partners = DB::table('partners')->where('status','=',1)->get();
                            @endphp
                            <div class="col-md-12 mb-3">
                                <input type="hidden" name="candcv_id" id="candcv_id" value="{{ $post->id }}">
                                <label for="partner_id" class="form-label">Office Name <span class="text-danger">*</span></label>
                                <select name="partner_id" id="partner_id" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($partners as $partner)
                                    <option value="{{ $partner->id }}">{{ $partner->rec_off_name }}</option>
                                @endforeach
                                </select>
                                <span class="text-danger" id="errorPartner"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary downloadCandCVBtn" id="downloadCandCV">Download Cv</button>
                        {{-- <button type="submit" class="btn btn-primary" id="downloadCandCV">Download Cv</button> --}}
                        {{-- <button type="button" class="btn btn-primary" id="downloadCandCV2">Download Cv</button> 10-07-2023 --}}
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Download CV as per company End -->
        <!-- Candidate Docs Upload Modal Start -->
        <div class="modal fade" id="uploadDocs"  tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Upload Docs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.docs.store',$post->id) }}" method="POST" id="photoValid2" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                <div class="mb-2">
                                    @if ($post->photo_file != '')
                                    <img src="{{ asset('admin/assets/images/candidate/'.$post->photo_file) }}" alt="photo" class="d-block w-px-100 h-px-100 rounded" id="viewupphoto">
                                    @else
                                    <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="Photo" class="d-block w-px-100 h-px-100 rounded" id="viewupphoto">
                                    @endif
                                </div>
                                <label class="form-label" for="add-upload-photo2">Upload Photo</label>
                                <input type="file" name="photo_file" id="add-upload-photo2" class="form-control add-upload-photo2">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                <div class="mb-2">
                                    @if ($post->cv_file != '')
                                    <img src="{{ asset('admin/assets/images/candidate/'.$post->cv_file) }}" alt="photo" class="d-block w-px-100 h-px-100 rounded" id="viewcvfile">
                                    @else
                                    <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="Photo" class="d-block w-px-100 h-px-100 rounded" id="viewcvfile">
                                    @endif
                                </div>
                                <label class="form-label" for="add-upload-cv2">Upload Full Image</label>
                                <input type="file" name="cv_file" id="add-upload-cv2" class="form-control add-upload-cv2">
                                </div>
                            </div>



                            <div class="col-md-6">
                                <div class="mb-3">
                                <div class="mb-2">
                                    @if ($post->pass_file != '')
                                    <img src="{{ asset('admin/assets/images/candidate/'.$post->pass_file) }}" alt="photo" class="d-block w-px-100 h-px-100 rounded" id="viewpassfile">
                                    @else
                                    <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="Photo" class="d-block w-px-100 h-px-100 rounded" id="viewpassfile">
                                    @endif
                                </div>
                                <label class="form-label" for="add-upload-pp2">Upload Passport Front</label>
                                <input type="file" name="pass_file" id="add-upload-pp2" class="form-control add-upload-pp2">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                <div class="mb-2">
                                    @if ($post->pass_back_file != '')
                                    <img src="{{ asset('admin/assets/images/candidate/'.$post->pass_back_file) }}" alt="photo" class="d-block w-px-100 h-px-100 rounded" id="viewpassfileb">
                                    @else
                                    <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="Photo" class="d-block w-px-100 h-px-100 rounded" id="viewpassfileb">
                                    @endif
                                </div>
                                <label class="form-label" for="add-upload-ppb2">Upload Passport Back</label>
                                <input type="file" name="pass_back_file" id="add-upload-ppb2" class="form-control add-upload-ppb2">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                <div class="mb-2">
                                    @if ($post->lic_file != '')
                                    <img src="{{ asset('admin/assets/images/candidate/'.$post->lic_file) }}" alt="photo" class="d-block w-px-100 h-px-100 rounded" id="viewlicfile">
                                    @else
                                    <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="Photo" class="d-block w-px-100 h-px-100 rounded" id="viewlicfile">
                                    @endif
                                </div>
                                <label class="form-label" for="add-upload-lic2">Upload License</label>
                                <input type="file" name="lic_file" id="add-upload-lic2" class="form-control add-upload-lic2">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="editUploadBtn22">Save changes</button>
                        {{-- <button type="submit" class="btn btn-primary">Save changes</button> --}}
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Candidate Docs Upload Modal End -->
        <!-- Candidate Docs Upload Modal Start -->
        <div class="modal fade" id="uploadDocs2" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Upload File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.docs2.store',$post->id) }}" method="POST" id="photoValid22" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                <label for="add-label-file">File Type <span class="text-danger">*</span></label>
                                <input type="text" name="label" id="add-label-file" class="form-control" placeholder="Enter file type">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                <label class="form-label" for="add-upload-file-2">Upload File</label>
                                <input type="file" name="filename" id="add-upload-file-2" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        {{-- <button type="submit" class="btn btn-primary">Save changes</button> --}}
                        <button type="button" class="btn btn-primary" id="editUploadDocs2btn">Save changes</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Candidate Docs Upload Modal End -->
        <!-- Candidate Edit Page Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="edituserLabel" class="offcanvas-title">Edit Candidate</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.candidate.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                    $experience = explode(",",$post->experience);
                    $expconts = explode(",",$post->expcountry_id);
                    $expcits = explode(",",$post->expcity_id);
                    $proffsp = explode(",",$post->proff_id);
                    $langsk = explode(",",$post->lang_known);
                    $expwp = explode(",",$post->expwp_id);
                    $carknow1 = explode(",",$post->carknown_id);
                    $expcittext = explode(",",$post->expcity_id_text);
                    $ieexp = 0;
                    // dd($langsk);
                    @endphp
                    <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-associate-id" class="form-label">Associate <span class="text-danger">*</span></label>
                            <select name="associate_id" class="form-select select2" id="add-associate-id" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="73" @if($post->associate_id == 73) selected @endif>Direct Candidate</option>
                                @foreach ($associates as $associate2)
                                <option value="{{ $associate2->id }}" @if($associate2->id == $post->associate_id) selected @endif>{{ $associate2->pty_full_name.' ('.$associate2->pty_ag_name.')' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-care-off">Careoff <span class="text-danger">*</span></label>
                            <select name="careoff_id" id="add-care-off" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($careoffs as $careoff2)
                                <option value="{{ $careoff2->id }}" @if($careoff2->id == $post->careoff_id) selected @endif>{{ $careoff2->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <input type="hidden" name="editID" value="{{ $post->id }}">
                            <label class="form-label" for="add-cand-name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="cand_name" id="add-cand-name" class="form-control" value="{{ $post->cand_name }}" placeholder="Enter candidate name...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-cand-arname">Arabic Name <span class="text-danger">*</span></label>
                            <input type="text" name="arcand_name" id="add-cand-arname" class="form-control" value="{{ $post->arcand_name }}" placeholder="Enter candidate name...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-pass-no">Passport No <span class="text-danger">*</span></label>
                            <input type="text" name="pass_no" id="add-pass-no" class="form-control" value="{{ $post->pass_no }}" placeholder="Enter candidate name...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-pass-type">Passport Type <span class="text-danger">*</span></label>
                            <select name="pass_type" id="add-pass-type" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="ECNR" @if($post->pass_type == 'ECNR') selected @endif>ECNR</option>
                                <option value="ECR" @if($post->pass_type == 'ECR') selected @endif>ECR</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-dob">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" id="add-dob" value="{{ $post->dob }}" class="form-control" placeholder="Enter date of birth...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-doi">Date of Issue <span class="text-danger">*</span></label>
                            <input type="date" name="doi" id="add-doi" value="{{ $post->doi }}" class="form-control" placeholder="Enter date of issue...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-doe">Date of Expiry <span class="text-danger">*</span></label>
                            <input type="date" name="doe" id="add-doe" value="{{ $post->doe }}" class="form-control" placeholder="Enter date of expiry...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="form-label" for="add-place-of-issue">Place of Issue</label>
                            {{--
                            <select name="poi" id="add-place-of-issue" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($poiss as $pois)
                                <option value="{{ $pois->id }}" @if($pois->id == $post->poi) selected @endif>{{ $pois->name }}</option>
                                @endforeach
                            </select>
                            --}}
                            <input type="text" name="poi_text" id="add-place-of-issue" value="{{ $post->poi_text }}" class="form-control cityTypehead" placeholder="Type city here..">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-place-of-birth">Place of Birth</label>
                            {{--
                            <select name="plb_id" id="add-place-of-birth" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($cities as $cityplb)
                                <option value="{{ $cityplb->id }}" @if($cityplb->id == $post->plb_id) selected @endif>{{ $cityplb->name }}</option>
                                @endforeach
                            </select>
                            --}}
                            <input type="text" name="plb_text" id="add-place-of-birth" class="form-control cityTypehead" value="{{ $post->plb_text }}" placeholder="Type city here...">
                        </div>
                    </div>
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-age">Age <span class="text-danger">*</span></label>
                            <input type="text" name="age" id="add-age" value="{{ $post->age }}" class="form-control" placeholder="Enter age...">
                        </div>
                    </div>
                    --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-nationality-id">Nationality</label>
                            <select name="nation_id" id="add-nationality-id" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($countries as $nationid)
                                <option value="{{ $nationid->id }}" @if($nationid->id == $post->nation_id) selected @endif>{{ $nationid->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-region">Region</label>
                            <select name="region_id" id="add-region" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($regions as $region)
                                <option value="{{ $region->id }}" @if($region->id == $post->region_id) selected @endif>{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-cand-city">City</label>
                            {{--
                            <select name="candcity_id" id="add-cand-city" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($cities as $candcity)
                                <option value="{{ $candcity->id }}" @if($candcity->id == $post->candcity_id) @selected(true) @endif>{{ $candcity->name }}</option>
                                @endforeach
                            </select>
                            --}}
                            <input type="text" name="candcity_text" class="form-control cityTypehead" value="{{ $post->candcity_text }}" placeholder="Type city here...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-religion-id">Religion</label>
                            <select name="religion_id" id="add-religion-id" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                {{-- <option value="Muslim" @if($post->religion == 'Muslim') selected @endif>Muslim</option>
                                <option value="Hindu" @if($post->religion == 'Hindu') selected @endif>Hindu</option>
                                <option value="Christian" @if($post->religion == 'Christian') selected @endif>Christian</option>
                                <option value="Sikh" @if($post->religion == 'Sikh') selected @endif>Sikh</option> --}}
                                @foreach ($religions as $religion)
                                <option value="{{ $religion->id }}" @if($religion->id == $post->religion_id) selected @endif>{{ $religion->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-contact-number">Contact Number</label>
                            <input type="text" name="contact_no" id="add-contact-number" value="{{ $post->contact_no }}" class="form-control" placeholder="Enter contact number...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-marital-status">Marital Status <span class="text-danger">*</span></label>
                            <select name="marital_status" id="add-marital-status" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="Unmarried" @if($post->marital_status == 'Unmarried') selected @endif>Unmarried</option>
                                <option value="Married" @if($post->marital_status == 'Married') selected @endif>Married</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-language-known">Language Known <span class="text-danger">*</span></label>
                            <select name="lang_known[]" id="add-language-known" class="form-select select2" multiple>
                            <option value="English" @if(in_array('English',$langsk)) selected @endif>English</option>
                            <option value="Hindi" @if(in_array('Hindi',$langsk)) selected @endif>Hindi</option>
                            <option value="Urdu" @if(in_array('Urdu',$langsk)) selected @endif>Urdu</option>
                            <option value="Arabic" @if(in_array('Arabic',$langsk)) selected @endif>Arabic</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-expected-sal">Expected Salary <span class="text-danger">*</span></label>
                            <input type="text" name="exp_sal" id="add-expected-sal" value="{{ $post->exp_sal }}" class="form-control" placeholder="Enter expected salary...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-mob-number">Mobile Number</label>
                            <input type="text" name="mobile_no" value="{{ $post->mobile_no }}" id="add-mob-number" class="form-control" placeholder="Enter mobile number...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="form-label" for="add-occupation">Applied for, Occupation</label>
                            <select name="jobtype_id" id="add-occupation" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($jobtypes as $jobtype)
                                <option value="{{ $jobtype->id }}" @if($post->jobtype_id == $jobtype->id) selected @endif>{{ $jobtype->eng_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-address">Address</label>
                            <input type="text" class="form-control" id="add-address" value="{{ $post->address }}" name="address" placeholder="Enter address...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-expect-wkp">Expected Work Place</label>
                            <select name="expwp_id[]" id="add-expect-wkp" class="form-select select2" data-allow-clear="true" multiple>
                                <option value="">Select</option>
                                @foreach ($expworklocs as $expwkpc)
                                <option value="{{ $expwkpc->id }}" @if(in_array($expwkpc->id,$expwp)) selected @endif>{{ $expwkpc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="" class="form-label">Google Map</label>
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="google_map" id="add-google-map-yes" @if($post->google_map == 1) checked @endif value="1"/>
                                <label class="form-check-label" for="add-google-map-yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="google_map" id="add-google-map-no" @if($post->google_map == 0) checked @endif value="0"/>
                                <label class="form-check-label" for="add-google-map-no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-gulfexperience">Experience <span class="text-danger">*</span></label>
                            <select name="gulfexperience" id="add-gulfexperience" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="1" @if($post->gulfexperience == 1) selected @endif>Indian Experience</option>
                                <option value="2" @if($post->gulfexperience == 2) selected @endif>Gulf Experience</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-car-known">Car Known</label>
                            <select name="carknown_id[]" id="add-car-known" class="form-select select2" multiple>
                            @foreach ($cars as $car1)
                            <option value="{{ $car1->id }}" @if(in_array($car1->id,$carknow1)) selected @endif>{{ $car1->name }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    </div>
                    @foreach ($experience as $index => $value )
                    <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-exoinyear{{ $index }}">Experience (in year) <span class="text-danger">*</span></label>
                            <input type="text" name="experience[]" id="add-exoinyear{{ $index }}" value="{{ $value }}"  class="form-control" placeholder="Enter experience in year...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-job-type{{ $index }}">Job Type</label>
                            <select name="proff_id[]" id="add-job-type{{ $index }}" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($jobtypes as $jobtype)
                                <option value="{{ $jobtype->id }}" @if($jobtype->id == $proffsp[$index]) selected @endif>{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-expcountry_id{{ $index }}">Experience (country name)</label>
                            <select name="expcountry_id[]" id="add-expcountry_id{{ $index }}" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($countries as $country)
                                <option value="{{ $country->id }}" @if($country->id == $expconts[$index]) selected @endif>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="form-label" for="add-expcity_id{{ $index }}">City</label>
                            {{--
                            <select name="expcity_id[]" id="add-expcity_id{{ $index }}" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($expworkcities as $city)
                                <option value="{{ $city->id }}" @if($city->id == $expcits[$index]) selected @endif>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            --}}
                            <input type="text" name="expcity_id_text[]" id="add-expcity_id{{ $index }}" @if(count($expcittext) > $ieexp) value="{{ $expcittext[$index] }}" @endif class="form-control cityTypehead" placeholder="Type city here...">
                        </div>
                    </div>
                    @if ($index == 0)
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-primary float-end addExp">Add</button>
                    </div>
                    @endif
                    @if ($index != 0)
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-danger remove float-end">Remove</button>
                    </div>
                    @endif
                    </div>
                    @php
                    $ieexp++;
                    @endphp
                    @endforeach
                    <div id="dispEXP">
                    </div>
                    <div class="row">
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-upload-pp">Upload Passport</label>
                            <input type="file" name="pass_file" id="add-upload-pp" class="form-control">
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-upload-lic">Upload License</label>
                            <input type="file" name="lic_file" id="add-upload-lic" class="form-control">
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-upload-cv">Upload CV</label>
                            <input type="file" name="cv_file" id="add-upload-cv" class="form-control">
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-upload-photo">Upload Photo</label>
                            <input type="file" name="photo_file" id="add-upload-photo" class="form-control">
                        </div>
                    </div>
                    --}}
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Candidate Edit Page End-->
        <!-- Personal Edit Page Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editPersonal" aria-labelledby="editPersonalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editPersonalLabel" class="offcanvas-title">Edit Personal</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editPersonalForm" action="{{ route('admin.candidate.update.personal') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                    $langsk = explode(",",$post->lang_known);
                    $expwp = explode(",",$post->expwp_id);
                    @endphp
                    <div class="row">
                    <input type="hidden" name="editID" value="{{ $post->id }}">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-cand-name-det">Name <span class="text-danger">*</span></label>
                            <input type="text" name="cand_name" id="add-cand-name-det" class="form-control" value="{{ $post->cand_name }}" placeholder="Enter candidate name...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-cand-arname-det">Arabic Name <span class="text-danger">*</span></label>
                            <input type="text" name="arcand_name" id="add-cand-arname-det" class="form-control" value="{{ $post->arcand_name }}" placeholder="Enter candidate name...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-marital-status-det">Marital Status <span class="text-danger">*</span></label>
                            <select name="marital_status" id="add-marital-status-det" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="Unmarried" @if($post->marital_status == 'Unmarried') selected @endif>Unmarried</option>
                                <option value="Married" @if($post->marital_status == 'Married') selected @endif>Married</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-education-id" class="form-label">Education</label>
                            <select name="education_id" id="add-education-id" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($educations as $education)
                                <option value="{{ $education->id }}" @if($post->education_id == $education->id) selected @endif>{{ $education->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-regionp">Region</label>
                            <select name="region_id" id="add-regionp" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($regions as $region)
                                <option value="{{ $region->id }}" @if($region->id == $post->region_id) selected @endif>{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-cand-cityp">City</label>
                            {{--
                            <select name="candcity_id" id="add-cand-cityp" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($cities as $candcity)
                                <option value="{{ $candcity->id }}" @if($candcity->id == $post->candcity_id) @selected(true) @endif>{{ $candcity->name }}</option>
                                @endforeach
                            </select>
                            --}}
                            <input type="text" name="candcity_text" id="add-cand-cityp" class="form-control cityTypehead" value="{{ $post->candcity_text }}" placeholder="Type city here...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-religionp-id">Religion</label>
                            <select name="religion_id" id="add-religionp-id" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                {{-- <option value="Muslim" @if($post->religion == 'Muslim') selected @endif>Muslim</option>
                                <option value="Hindu" @if($post->religion == 'Hindu') selected @endif>Hindu</option>
                                <option value="Christian" @if($post->religion == 'Christian') selected @endif>Christian</option>
                                <option value="Sikh" @if($post->religion == 'Sikh') selected @endif>Sikh</option> --}}
                                @foreach ($religions as $religion2)
                                <option value="{{ $religion2->id }}" @if($religion2->id == $post->religion_id) selected @endif>{{ $religion2->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-language-knownp">Language Known</label>
                            <select name="lang_known[]" id="add-language-knownp" class="form-select select2" multiple>
                            <option value="English" @if(in_array('English',$langsk)) selected @endif>English</option>
                            <option value="Hindi" @if(in_array('Hindi',$langsk)) selected @endif>Hindi</option>
                            <option value="Urdu" @if(in_array('Urdu',$langsk)) selected @endif>Urdu</option>
                            <option value="Arabic" @if(in_array('Arabic',$langsk)) selected @endif>Arabic</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="form-label" for="add-occupation-det">Applied for, Occupation</label>
                            <select name="jobtype_id" id="add-occupation-det" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($jobtypes as $jobtype)
                                <option value="{{ $jobtype->id }}" @if($post->jobtype_id == $jobtype->id) selected @endif>{{ $jobtype->eng_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-expected-salp">Expected Salary <span class="text-danger">*</span></label>
                            <input type="text" name="exp_sal" id="add-expected-salp" value="{{ $post->exp_sal }}" class="form-control" placeholder="Enter expected salary...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-expect-wkpp">Expected Work Place</label>
                            <select name="expwp_id[]" id="add-expect-wkpp" class="form-select select2" data-allow-clear="true" multiple>
                                <option value="">Select</option>
                                @foreach ($expworklocs as $expwkpc)
                                <option value="{{ $expwkpc->id }}" @if(in_array($expwkpc->id,$expwp)) selected @endif>{{ $expwkpc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="form-label" for="add-embassy-det">Embassy For</label>
                            <select name="embassy" id="add-embassy-det" class="form-select select2" data-allow-clear="true">
                                <option value="">Select Embassy</option>
                                @foreach ($embassies as $embassy)
                                <option value="{{ $embassy->id}}" @if($post->embassy_for == $embassy->id) selected @endif>{{ $embassy->embassy }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-p-age" class="form-label">Age <span class="text-danger">*</span></label>
                            <input type="text" name="age" id="add-p-age" value="{{ $post->age }}" class="form-control" placeholder="Enter age....">
                        </div>
                    </div>
                    --}}
                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editPersonalBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Personal Edit Page End-->
        <!-- Passport Edit Page Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editPassport" aria-labelledby="editPassportLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editPassportLabel" class="offcanvas-title">Edit Passport</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editPassportForm" action="{{ route('admin.candidate.update.passport') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                    <input type="hidden" name="editID" value="{{ $post->id }}">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-pass-no-pp">Pass No <span class="text-danger">*</span></label>
                            <input type="text" name="pass_no" id="add-pass-no-pp" class="form-control" value="{{ $post->pass_no }}" placeholder="Enter candidate name...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-pass-type-pp">Passport Type <span class="text-danger">*</span></label>
                            <select name="pass_type" id="add-pass-type-pp" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="ECNR" @if($post->pass_type == 'ECNR') selected @endif>ECNR</option>
                                <option value="ECR" @if($post->pass_type == 'ECR') selected @endif>ECR</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-dob-det">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" id="add-dob-det" value="{{ $post->dob }}" class="form-control" placeholder="Enter date of birth...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-place-of-birth-det">Place of Birth</label>

                            {{-- <select name="plb_id" id="add-place-of-birth-det" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($cities as $cityplb)
                                <option value="{{ $cityplb->id }}" @if($cityplb->id == $post->plb_id) selected @endif>{{ $cityplb->name }}</option>
                                @endforeach
                            </select> --}}

                            <input type="text" name="plb_text" id="add-place-of-birth-det" class="form-control cityTypehead" placeholder="Type city here..." value="{{ $post->plb_text }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-nationality-id-det">Nationality</label>
                            <select name="nation_id" id="add-nationality-id-det" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($countries as $nationid)
                                <option value="{{ $nationid->id }}" @if($nationid->id == $post->nation_id) selected @endif>{{ $nationid->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-doi-pp">Date of Issue <span class="text-danger">*</span></label>
                            <input type="date" name="doi" id="add-doi-pp" value="{{ $post->doi }}" class="form-control" placeholder="Enter date of issue...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-doe-pp">Date of Expiry <span class="text-danger">*</span></label>
                            <input type="date" name="doe" id="add-doe-pp" value="{{ $post->doe }}" class="form-control" placeholder="Enter date of expiry...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="form-label" for="add-place-of-issue-pp">Place of Issue</label>

                            <select name="poi" id="add-place-of-issue-pp" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($poiss as $pois)
                                <option value="{{ $pois->id }}" @if($pois->id == $post->poi) selected @endif>{{ $pois->name }}</option>
                                @endforeach
                            </select>

                            {{-- <input type="text" name="poi_text" id="add-place-of-issue-pp" class="form-control cityTypehead" value="{{ $post->poi_text }}" placeholder="Type city..."> --}}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="add-address-det">Address</label>
                            <input type="text" class="form-control" id="add-address-det" value="{{ $post->address }}" name="address" placeholder="Enter address...">
                        </div>
                    </div>
                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editPassportBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Passport Edit Page End-->
        <!-- Experience Edit Page Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editExperience" aria-labelledby="editExperienceLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editExperienceLabel" class="offcanvas-title">Edit Experience</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editExperienceForm" action="{{ route('admin.candidate.update.experience') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                    $experience = explode(",",$post->experience);
                    $expconts = explode(",",$post->expcountry_id);
                    $expcits = explode(",",$post->expcity_id);
                    $proffsp = explode(",",$post->proff_id);
                    $langsk = explode(",",$post->lang_known);
                    $expwp = explode(",",$post->expwp_id);
                    $carknow2 = explode(",",$post->carknown_id);
                    $expcittext = explode(",",$post->expcity_id_text);
                    $transmission2 = explode(",",$post->vehical_transmission);
                    // dd($expcittext);
                    $iexp = 0;
                    @endphp
                    <input type="hidden" name="editID" value="{{ $post->id }}">
                    <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="add-gulfexperience-ex">Experience <span class="text-danger">*</span></label>
                            <select name="gulfexperience" id="add-gulfexperience-ex" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="1" @if($post->gulfexperience == 1) selected @endif>Indian Experience</option>
                                <option value="2" @if($post->gulfexperience == 2) selected @endif>Gulf Experience</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="add-car-known-ex">Car Known</label>
                            <select name="carknown_id[]" id="add-car-known-ex" class="form-select select2" multiple>
                            @foreach ($cars as $car2)
                            <option value="{{ $car2->id }}" @if(in_array($car2->id,$carknow2)) selected @endif>{{ $car2->name }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="add-vehical-transimission" class="form-label">Vehical Transmission</label>
                            <select name="vehical_transmission[]" id="add-vehical-transimission" class="form-select select2" multiple>
                                {{-- <option value="1">Manual</option> --}}
                                {{-- <option value="2">Automatic</option> --}}
                                @foreach ($vehtrans as $vehtran)
                                    <option value="{{ $vehtran->id }}" @if(in_array($vehtran->id,$transmission2)) selected @endif>{{ $vehtran->eng_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    </div>
                    @foreach ($experience as $index => $value )
                    <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-exoinyear-ex{{ $index }}">Experience (in year) <span class="text-danger">*</span></label>
                            <input type="text" name="experience[]" id="add-exoinyear-ex{{ $index }}" value="{{ $value }}"  class="form-control" placeholder="Enter experience in year...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-job-type-ex{{ $index }}">Job Type</label>
                            <select name="proff_id[]" id="add-job-type-ex{{ $index }}" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($jobtypes as $jobtype)
                                <option value="{{ $jobtype->id }}" @if($jobtype->id == $proffsp[$index]) selected @endif>{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-expcountry_id-ex{{ $index }}">Experience (country name)</label>
                            <select name="expcountry_id[]" id="add-expcountry_id-ex{{ $index }}" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($countries as $country)
                                <option value="{{ $country->id }}" @if($country->id == $expconts[$index]) selected @endif>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="form-label" for="add-expcity_id-ex{{ $index }}">City</label>

                            <select name="expcity_id[]" id="add-expcity_id-ex{{ $index }}" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($expworkcities as $city)
                                <option value="{{ $city->id }}" @if($city->id == $expcits[$index]) selected @endif>{{ $city->name }}</option>
                                @endforeach
                            </select>

                            {{-- <input type="text" name="expcity_id_text[]" id="add-expcity_id-ex{{ $index }}" @if (count($expcittext) > $iexp) value="{{ $expcittext[$index] }}" @endif class="form-control cityTypehead" placeholder="Type city here..."> --}}
                        </div>
                    </div>
                    @if ($index == 0)
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-primary float-end addExp2">Add</button>
                    </div>
                    @endif
                    @if ($index != 0)
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-danger remove2 float-end">Remove</button>
                    </div>
                    @endif
                    </div>
                    <?php $iexp++ ?>
                    @endforeach
                    <div id="dispEXP2" class="dispEXP2">
                    </div>
                    <div class="row">
                    <div class="col-md-6">
                        <label for="" class="form-label">Google Map</label>
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="google_map" id="add-google-map-yes" @if($post->google_map == 1) checked @endif value="1"/>
                                <label class="form-check-label" for="add-google-map-yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="google_map" id="add-google-map-no" @if($post->google_map == 0) checked @endif value="0"/>
                                <label class="form-check-label" for="add-google-map-no">No</label>
                            </div>
                        </div>
                    </div>
                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editExperienceBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Experience Edit Page End-->
        <!-- Medical Edit Page Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editMedical" aria-labelledby="editMedicalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editMedicalLabel" class="offcanvas-title">Edit Medical</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editMedicalForm" action="{{ route('admin.candidate.update.medical') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                    <input type="hidden" name="editID" value="{{ $post->id }}">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-medical-fitness-status" class="form-label">Medical Status <span class="text-danger">*</span></label>
                            <select name="medical_health_status" class="form-select select2" id="add-medical-fitness-status" data-allow-clear="true">
                                <option value="">Select Medical Status</option>
                                <option value="Fit" @if($post->medical_health_status == 'Fit') selected @endif>Fit</option>
                                <option value="Unfit" @if($post->medical_health_status == 'Unfit') selected @endif>Unfit</option>
                                <option value="Repeat" @if($post->medical_health_status == 'Repeat') selected @endif>Repeat</option>
                                <option value="Ready For Medical" @if($post->medical_health_status == 'Ready For Medical') selected @endif>Ready For Medical</option>
                                <option value="On Medical" @if($post->medical_health_status == 'On Medical') selected @endif>On Medical</option>
                                <option value="Waiting For Fitnes" @if($post->medical_health_status == 'Waiting For Fitnes') selected @endif>Waiting For Fitnes</option>
                                <option value="Cancelled By Candidate" @if($post->medical_health_status == 'Cancelled By Candidate') selected @endif>Cancelled By Candidate</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6" id="medical_examine_date" @if($post->medical_health_status != 'Fit' && $post->medical_examine_date == '') style="display: none" @endif>
                        <div class="mb-3">
                            <label class="form-label" for="add-medical-examine-date">Medical Examine Date <span class="text-danger">*</span></label>
                            <input type="text" name="medical_examine_date" id="add-medical-examine-date" value="{{ $post->medical_examine_date }}" class="form-control flatpickr-basic" placeholder="Enter date of birth...">
                        </div>
                    </div>
                    <div class="col-md-6" id="repeat_examine_date" @if($post->medical_health_status != 'Repeat' && $post->repeat_examine_date == '') style="display: none" @endif>
                        <div class="mb-3">
                            <label class="form-label" for="add-repeat-medical-examine-date">Remedical Date <span class="text-danger">*</span></label>
                            <input type="text" name="repeat_examine_date" id="add-repeat-medical-examine-date" value="{{ $post->repeat_examine_date }}" class="form-control flatpickr-basic" placeholder="Enter Remedical Examine Date">
                        </div>
                    </div>
                    <div class="col-md-6" id="medical_expiry_date" @if(($post->medical_health_status != 'Fit' && $post->medical_expiry_date == '') || ($post->medical_health_status != 'Repeat' && $post->medical_expiry_date == '')) style="display: none" @endif>
                        <div class="mb-3">
                            <label class="form-label" for="add-doi-medical-expiry-date">Medical Expiry Date <span class="text-danger">*</span></label>
                            <input type="text" name="medical_expiry_date" id="add-doi-medical-expiry-date" value="{{ $post->medical_expiry_date }}" class="form-control flatpickr-basic" placeholder="Enter date of issue...">
                        </div>
                    </div>

                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editMedicalBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Medical Edit Page End-->
        <!-- Musaned Edit Page Start-->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editMusaned" aria-labelledby="editMusanedLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editMusanedLabel" class="offcanvas-title">Edit Musaned</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editMusanedForm" action="{{ route('admin.candidate.update.musaned') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                    <input type="hidden" name="editMusanedID" id="editMusanedID" value="{{ $post->id }}">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="musaned_status" class="form-label">Musaned Status<span class="text-danger">*</span></label>
                            <select name="musaned_status" id="musaned_status" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="1" @if($post->musaned_status == 1) selected @endif>Register</option>
                                <option value="2" @if($post->musaned_status == 2) selected @endif>Not Register</option>
                                <option value="3" @if($post->musaned_status == 3) selected @endif>Register at another office</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-musaned-registration-date">Musaned Registration Date <span class="text-danger">*</span></label>
                            <input type="date" name="musaned_reg_date" id="add-musaned-registration-date" value="{{ $post->musaned_reg_date }}" class="form-control" placeholder="Enter musaned registration date...">
                        </div>
                    </div>
                    <div class="col-md-6 musCopyDisp">
                        <div class="mb-3">
                            <label class="form-label" for="add-musaned-copy">Musaned copy</label>
                            <input type="file" name="musaned_file" id="add-musaned-copy" class="form-control musnaedfile">
                        </div>
                    </div>
                    <div class="col-md-6 musCopyDisp">
                        <div class="mb-3">
                            @if ($post->musaned_file != '')
                            <img src="{{ asset('admin/assets/images/candidate/'.$post->musaned_file) }}" alt="musaned_file" class="d-block w-px-100 h-px-100 rounded" id="addmusanedcopy">
                            @else
                            <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="musaned_file" class="d-block w-px-100 h-px-100 rounded" id="addmusanedcopy">
                            @endif
                        </div>
                    </div>
                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editMusanedBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Musaned Edit Page End-->

        <!-- Candidate Edit Page Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editDetails" aria-labelledby="editDetailsLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editDetailsLabel" class="offcanvas-title">Edit Details</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editDetailsForm" action="{{ route('admin.candidate.update.details') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                    $langsk = explode(",",$post->lang_known);
                    // dd($langsk);
                    @endphp
                    <div class="row">
                    <input type="hidden" name="editID" value="{{ $post->id }}">
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-cand-name-det">Name <span class="text-danger">*</span></label>
                            <input type="text" name="cand_name" id="add-cand-name-det" class="form-control" value="{{ $post->cand_name }}" placeholder="Enter candidate name...">
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-pass-no-det">Pass No <span class="text-danger">*</span></label>
                            <input type="text" name="pass_no" id="add-pass-no-det" class="form-control" value="{{ $post->pass_no }}" placeholder="Enter candidate name...">
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-pass-type-det">Passport Type <span class="text-danger">*</span></label>
                            <select name="pass_type" id="add-pass-type-det" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="ECNR" @if($post->pass_type == 'ECNR') selected @endif>ECNR</option>
                                <option value="ECR" @if($post->pass_type == 'ECR') selected @endif>ECR</option>
                            </select>
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-dob-det">Date of Birth <span class="text-danger">*</span></label>
                            <input type="text" name="dob" id="add-dob-det" value="{{ $post->dob }}" class="form-control flatpickr-basic" placeholder="Enter date of birth...">
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-doi-det">Date of Issue <span class="text-danger">*</span></label>
                            <input type="text" name="doi" id="add-doi-det" value="{{ $post->doi }}" class="form-control flatpickr-basic" placeholder="Enter date of issue...">
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-doe-det">Date of Expiry <span class="text-danger">*</span></label>
                            <input type="text" name="doe" id="add-doe-det" value="{{ $post->doe }}" class="form-control flatpickr-basic" placeholder="Enter date of expiry...">
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="form-label" for="add-place-of-issue-det">Place of Issue</label>
                            <select name="poi" id="add-place-of-issue-det" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($poiss as $pois)
                                <option value="{{ $pois->id }}" @if($pois->id == $post->poi) selected @endif>{{ $pois->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-place-of-birth-det">Place of Birth</label>
                            <select name="plb_id" id="add-place-of-birth-det" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($cities as $cityplb)
                                <option value="{{ $cityplb->id }}" @if($cityplb->id == $post->plb_id) selected @endif>{{ $cityplb->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-nationality-id-det">Nationality</label>
                            <select name="nation_id" id="add-nationality-id-det" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($countries as $nationid)
                                <option value="{{ $nationid->id }}" @if($nationid->id == $post->nation_id) selected @endif>{{ $nationid->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-marital-status-det">Marital Status <span class="text-danger">*</span></label>
                            <select name="marital_status" id="add-marital-status-det" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="Unmarried" @if($post->marital_status == 'Unmarried') selected @endif>Unmarried</option>
                                <option value="Married" @if($post->marital_status == 'Married') selected @endif>Married</option>
                            </select>
                        </div>
                    </div>
                    --}}
                    <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-mob-number-det">Mobile Number</label>
                                <input type="text" name="mobile_no" value="{{ $post->mobile_no }}" id="add-mob-number-det" class="form-control add-mob-number-det" placeholder="Enter mobile number...">
                                <input type="hidden" name="mobile_no_dial_code" value="{{ $post->mobile_no_dial_code }}" id="mobile-no-dial-code-add-det" class="mobile-no-dial-code-add-det">
                            </div>
                    </div>
                    <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-contact-number-det">Family Contact Number</label>
                                <input type="text" name="contact_no" id="add-contact-number-det" value="{{ $post->contact_no }}" class="form-control add-contact-number-det" placeholder="Enter contact number...">
                                <input type="hidden" name="contact_no_dial_code" value="{{ $post->contact_no_dial_code }}" id="contact-no-dial-code-add-det" class="contact-no-dial-code-add-det">
                            </div>
                    </div>
                    <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-relative-number-det">Relative Contact Number</label>
                                <input type="text" name="relative_contact_no" id="add-relative-number-det" value="{{ $post->relative_contact_no }}" class="form-control add-relative-number-det" placeholder="Enter contact number...">
                                <input type="hidden" name="relative_contact_no_dial_code" value="{{ $post->relative_contact_no_dial_code }}" id="relative-contact-no-dial-code-add-det" class="relative-contact-no-dial-code-add-det">
                            </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-email">Email</label>
                            <input type="text" name="email" id="add-email" class="form-control" value="{{ $post->email }}" placeholder="Enter email...">
                        </div>
                    </div>
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="form-label" for="add-occupation-det">Applied for, Occupation</label>
                            <select name="jobtype_id" id="add-occupation-det" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($jobtypes as $jobtype)
                                <option value="{{ $jobtype->id }}" @if($post->jobtype_id == $jobtype->id) selected @endif>{{ $jobtype->eng_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    --}}
                    {{--
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-address-det">Address</label>
                            <input type="text" class="form-control" id="add-address-det" value="{{ $post->address }}" name="address" placeholder="Enter address...">
                        </div>
                    </div>
                    --}}
                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit" id="editDetailsbtn">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editDetailsbtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Candidate Edit Page End-->
        <!-- Update Confirm By Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editconfirmby" aria-labelledby="editconfirmbyLabel" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="offcanvas-header">
                    <h5 id="editconfirmbyLabel" class="offcanvas-title">Associate Confirm By</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                    <form class="add-new-user pt-0" id="editconfirmbyForm" action="{{ route('admin.candidate.assoc.confirmby') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="cand_id" value="{{ $post->id }}" id="assoccand_id">
                        <div class="row">
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ad-confirm-by-associate" class="form-label">Associate <span class="text-danger">*</span></label>
                                    <select name="associate_id" id="ad-confirm-by-associate" class="form-select select2" data-placeholder="Select Associate" data-allow-clear="true">
                                        <option value="">Select Associate</option>
                                        <option value="73" @if($post->associate_id == 73) selected @endif>Direct Candidate</option>
                                        @foreach ($associates as $associatecon)
                                            <option value="{{ $associatecon->id }}" @if($post->associate_id == $associatecon->id) selected @endif>{{ $associatecon->pty_full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="add-confirm-by-associate" class="form-label">Associate Confirm<span class="text-danger">*</span></label>
                                    <select name="associate_confirm" id="add-confirm-by-associate" class="form-select select2" data-placeholder="Select Assoicate Confirm" data-allow-clear="true">
                                        <option value="">Select</option>
                                        <option value="Direct Candidate" @if($post->associate_confirm == 'Direct Candidate') selected @endif>Direct Candidate</option>
                                        <option value="Associate" @if($post->associate_confirm == 'Associate') selected @endif>Associate</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- <button type="button" class="btn btn-sm btn-primary me-sm-3 me-1 data-submit" id="updateConfirmBy">Confirm By</button> --}}
                        <button type="submit" class="btn btn-sm btn-primary me-sm-3 me-1">Confirm By</button>
                        <button type="reset" class="btn btn-sm btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                    </form>

                    {{-- Get Confirm By Data --}}

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Associate</th>
                                            <th>Confirm By</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($assocconfirmbys->count() > 0)
                                            @foreach ($assocconfirmbys as $assocconfirmby)
                                                <tr>
                                                    <td>@if($assocconfirmby->associate_confirm != '') {{ $assocconfirmby->associate_confirm }} @else {{ '---' }} @endif</td>
                                                    <td>{{ $assocconfirmby->confirmby->name }}</td>
                                                    <td>{{ date('d-m-Y h:i:s',strtotime($assocconfirmby->confirm_datetime)) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="text-center">No Data Found</td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        <!-- Update Confirm By End -->
        <!-- Candidate Edit Careoff Start -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="editCareoff" aria-labelledby="editCareoffLabel">
            <div class="offcanvas-header">
                <h5 id="editCareoffLabel" class="offcanvas-title">Edit Careoff</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editCareoffForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                    <input type="hidden" name="editID" value="{{ $post->id }}">

                    <div class="col-md-12">
                        <div class="mb-3">
                        <label for="edit-associate-label" class="form-label">Associate <span class="text-danger">*</span></label>
                        <select name="associate_id" id="edit-associate-label" class="form-control select2">
                            <option value="">Select Associate</option>
                            <option value="73" @if($post->associate_id == '73') selected @endif>Direct Candidate</option>
                                @foreach ($associates as $associateEd)
                                    <option value="{{ $associateEd->id }}" @if($post->associate_id ==  $associateEd->id) selected @endif>{{ $associateEd->pty_full_name.' ('.$associateEd->pty_ag_name.')' }}</option>
                                @endforeach
                        </select>

                        </div>
                    </div>



                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="edit-careoff-label" class="form-label">Careoff</label>
                            <select name="careoff_id" id="edit-careoff-label" class="form-control select2">
                                <option value="">Select Careoff</option>
                                @foreach ($careoffs as $careoffEd)
                                    <option value="{{ $careoffEd->id }}" @if($careoffEd->id == $post->careoff_id) selected @endif>{{ $careoffEd->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if ((Auth::guard('partner')->user()->user_type == 1) || (isset($permission) && $permission->cand_sourcing_date == 1))
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-sourcing-date" class="form-label">Sourcing Date</label>
                                <input type="text" name="sourcing_date" id="edit-sourcing-date" class="form-control flatpicker-date" value="{{ $post->sourcing_date }}" placeholder="Enter Sourcing date...">
                            </div>
                        </div>
                    @endif




                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit" id="editDetailsbtn">Submit</button> --}}
                    <button type="button" class="btn btn-sm btn-primary me-sm-3 me-1 data-submit" id="editCareoffbtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Candidate Edit Careoff End -->

        <!-- Update Published Status Start -->
        <div class="modal fade" id="updatePublished" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Update Published Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.published.update',$post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="publish" id="published" @if($post->publish == 1) checked @endif value="1"/>
                                    <label class="form-check-label" for="published">Publish</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="publish" id="unpublished" @if($post->publish == 0) checked @endif value="0"/>
                                    <label class="form-check-label" for="unpublished">Unpublish</label>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Update Published Status end -->
        <!-- Deactive Service Charge Start -->
        <div class="modal fade" id="deactiveStatusModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Deactive Service Charge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="POST" id="deactiveStatusModalForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="candser_id" id="candser_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                <label for="deact-notes">Notes <span class="text-danger">*</span></label>
                                <textarea name="approval_notes" class="form-control" id="deact-notes" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p class="text-danger">Are ypu sure? To deactive Service charge!</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary btn-sm" id="deactbtnsubm">Yes, deactive it!</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Deactive Service Charge End -->
        <!-- Activate Service Charge Start -->
        <div class="modal fade" id="activeStatusModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Deactive Service Charge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="POST" id="activeStatusModalForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="candser_id2" id="candser_id2">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                <label for="deact-notes">Notes <span class="text-danger">*</span></label>
                                <textarea name="approval_notes" class="form-control" id="deact-notes" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p class="text-danger">Are ypu sure? To active Service charge!</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary btn-sm" id="actbtnsubm">Yes, active it!</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Activate Service Charge End -->
        <!-- Add Service Charge Page Start -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="addServiceCharge" aria-labelledby="addServiceChargeLabel">
            <div class="offcanvas-header">
                <h5 id="addServiceChargeLabel" class="offcanvas-title">Add Service Charge</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addServiceChargeForm" action="{{ route('admin.candscharge',$post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Given By <span class="text-danger">*</span></label>
                            <select name="given_by" id="given_by" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="add-amount">Service Charge <span class="text-danger">*</span></label>
                            <input type="text" name="amount" id="add-amount" value="" class="form-control" placeholder="Enter amount...">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="add-notes-charge" class="form-label">Notes</label>
                            <textarea name="notes" id="add-notes-charge" cols="30" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                    </div>
                    {{-- <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select select2" data-allow-clear="true">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Deactive</option>
                            </select>
                        </div>
                    </div>
                    </div> --}}
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editServiceChargeBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- end Service Charge Page End-->
        <!-- Update Service Charge Page Start -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="UpdateServiceCharge" aria-labelledby="UpdateServiceChargeLabel">
            <div class="offcanvas-header">
                <h5 id="UpdateServiceChargeLabel" class="offcanvas-title">Update Service Charge</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="UpdateServiceChargeForm" action="{{ route('admin.candscharge',$post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                    <input type="hidden" name="cand_id" id="cand_id">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="edit-given_by" class="form-label">Given By <span class="text-danger">*</span></label>
                            <select name="given_by" id="edit-given_by" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="edit-amount">Service Charge</label>
                            <input type="text" name="amount" id="edit-amount" value="" class="form-control" placeholder="Enter amount...">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="edit-notes" class="form-label">Notes</label>
                            <textarea name="notes" id="edit-notes" cols="30" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                    </div>
                    {{-- <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="edit-status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" id="edit-status" class="form-select select2" data-allow-clear="true">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="0">Deactive</option>
                            </select>
                        </div>
                    </div>
                    </div> --}}
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit" id="editSerChargeBtn2">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- End Update Service Charge Page End-->
        <!-- Add Payment Page Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="addCandPayment" aria-labelledby="addCandPaymentLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="addCandPaymentLabel" class="offcanvas-title">Add Payment</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addCandPaymentForm" action="{{ route('admin.candamtStr',$post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-amount-cand">Amount <span class="text-danger">*</span></label>
                            <input type="text" name="amount" id="add-amount-cand" class="form-control" placeholder="Enter amount...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-payment-mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                            <select name="payment_mode" id="add-payment-mode" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="netbanking">Net Banking</option>
                                <option value="upi">UPI</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-txn-id" class="form-label">Transaction ID <span class="text-danger">*</span></label>
                            <input type="text" name="txn_id" id="add-txn-id" class="form-control" placeholder="Enter Transaction number...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-bank-to" class="form-label">Bank To <span class="text-danger">*</span></label>
                            <select name="bank_to" id="add-bank-to" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="Khurshid_Khan">Khurshid Khan</option>
                                <option value="Juned_Khan">Juned Khan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-payment-slip-file" class="form-label">Payment Slip</label>
                            <input class="form-control" name="payment_slip" type="file" id="add-payment-slip-file" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar"/>
                    </div>
                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editadpaymentBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Add Payment Page End-->
        <!-- Edit Payment Page Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editCandPayment" aria-labelledby="editCandPaymentLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editCandPaymentLabel" class="offcanvas-title">Update Payment</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editCandPaymentForm" action="{{ route('admin.candamtUpdt') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                    <input type="hidden" name="paycand_id" id="paycand_id">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="edit-amount-cand">Amount<span class="text-danger">*</span></label>
                            <input type="text" name="amount" id="edit-amount-cand" class="form-control" placeholder="Enter amount...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit-payment-mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                            <select name="payment_mode" id="edit-payment-mode" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="netbanking">Net Banking</option>
                                <option value="upi">UPI</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit-txn-id" class="form-label">Transaction ID</label>
                            <input type="text" name="txn_id" id="edit-txn-id" class="form-control" placeholder="Enter Transaction number...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit-bank-to" class="form-label">Bank To <span class="text-danger">*</span></label>
                            <select name="bank_to" id="edit-bank-to" class="form-select select2" data-allow-clear="true">
                                <option value="">Select</option>
                                <option value="Khurshid_Khan">Khurshid Khan</option>
                                <option value="Juned_Khan">Juned Khan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit-payment-slip-file" class="form-label">Payment Slip</label>
                            <input class="form-control edit-payment-slip-file" name="payment_slip" type="file" id="edit-payment-slip-file" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3" id="dispIMG">
                            <img alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2"/>
                        </div>
                    </div>
                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1" id="editCadnPaymentBtn2">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Edit Payment Page End-->
        <!-- Payment Slip Show Start -->
        <div class="modal fade" id="viewPaymentSlip" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Payment Slip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div id="disViewSlip">

                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Paymemt Slip Show End -->
        <!-- Delete Photo Copy Start -->
        <div class="modal fade" id="deletePhoto" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.photoDel',$post->id) }}" method="POST" id="deletePhotoForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Are You sure delete the photo!</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-sm" id="deletePhotoBtn">Yes</button>
                        {{-- <button type="submit" class="btn btn-primary btn-sm">Yes</button> --}}
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Photo Copy End -->
        <!-- Delete Passport Copy Start -->
        <div class="modal fade" id="deletePassFile" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.passportDel',$post->id) }}" method="POST" id="deletePassForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Are You sure delete the Passport!</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-sm" id="deletePassFileBtn">Yes</button>
                        {{-- <button type="submit" class="btn btn-primary btn-sm">Yes</button> --}}
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Passport Copy End -->

        <!-- Delete Passport Back Copy Start -->
        <div class="modal fade" id="deletePassBackFile" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.passportBackDel',$post->id) }}" method="POST" id="deletePassBackForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Are You sure delete the Passport!</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-sm" id="deletePassBackFileBtn">Yes</button>
                        {{-- <button type="submit" class="btn btn-primary btn-sm">Yes</button> --}}
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Passport Copy End -->

        <!-- Delete License Copy Start -->
        <div class="modal fade" id="deleteLice" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.licenseDel',$post->id) }}" method="POST" id="deleteLiceForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Are You sure delete the License!</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-sm" id="deleteLiceBtn">Yes</button>
                        {{-- <button type="submit" class="btn btn-primary btn-sm">Yes</button> --}}
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete License Copy End -->
        <!-- Delete Full Size Image -->
        <div class="modal fade" id="deleteFullImg" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.fullimageDel',$post->id) }}" method="POST" id="deleteFullImgForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Are You sure delete the Full Image!</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-sm" id="deleteFullImgBtn">Yes</button>
                        {{-- <button type="submit" class="btn btn-primary btn-sm">Yes</button> --}}
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Full Size Image -->
        <!-- Delete Musaned Image Start -->
        <div class="modal fade" id="deleteMusanedFile" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.musanedDel',$post->id) }}" id="deleteMusanedFileForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Are You sure delete the Full Image!</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-sm" id="deleteMusanedbtn">Yes</button>
                        {{-- <button type="submit" class="btn btn-primary btn-sm">Yes</button> --}}
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Musaned Image End -->
        <!-- Delete Candidate FIle Start -->
        <div class="modal fade" id="deletecandFile" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.candfile') }}" method="POST" id="deletecandFileForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="filecand_id" id="filecand_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Are You sure delete the File!</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-sm" id="deleteCandFIleBtn">Yes</button>
                        {{-- <button type="submit" class="btn btn-primary btn-sm">Yes</button> --}}
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Candidate File End -->

        <!-- Update Status Modal Start -->
        <div class="modal fade" id="NewCandStatusUpdate" aria-hidden="true" aria-labelledby="NewCandStatusUpdateLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="NewCandStatusUpdateLabel">New Candidate</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="cand_id" id="candIDN" value="{{ $post->id }}">
                        <!-- Candidate Are Ready Status -->
                        <div class="row">

                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div>
                                            <h6 class="offcanvas-title">Candidate Name</h6>
                                            <span class="form-label" id="candidateName"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div>
                                            <h6 class="offcanvas-title">Passport No</h6>
                                            <span class="form-label" id="candPassport"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-4">
                                        <button type="button" class="btn btn-danger btn-sm cancelledButton" >Cancelled</button>
                                            <button type="button" class="btn btn-warning btn-sm holdButton" >Hold</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <form action="{{ route('admin.candidate.newcandidateupdate') }}" method="POST" id="candidateareready" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Candidate Are Ready</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="candidate_are_ready" id="candidate_are_ready" value="1"/>
                                                    <label class="form-check-label" for="candidate_are_ready">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="candidate_are_ready" id="candidate_are_ready_not" value="0"/>
                                                    <label class="form-check-label" for="candidate_are_ready_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton" >Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton" >Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Publish for Selection -->
                                <form action="{{ route('admin.candidate.publishforselectionupdate') }}" method="POST" id="publishedforselection" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Published For Selection</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="published_for_selection" id="published_for_selection" value="1"/>
                                                    <label class="form-check-label" for="published_for_selection">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="published_for_selection" id="published_for_selection_not" value="0"/>
                                                    <label class="form-check-label" for="published_for_selection_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Selected Status -->
                                <form action="{{ route('admin.candidate.selected') }}" method="POST" id="selectedStatus" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Selected</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="selected" id="selected" value="1"/>
                                                    <label class="form-check-label" for="selected">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="selected" id="selected_not" value="0"/>
                                                    <label class="form-check-label" for="selected_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Visa Received Status -->
                                <form action="{{ route('admin.candidate.visareceived') }}" method="POST" id="visareceived" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Visa Received</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="visa_received" id="visa_received" value="1"/>
                                                    <label class="form-check-label" for="visa_received">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="visa_received" id="visa_received_not" value="0"/>
                                                    <label class="form-check-label" for="visa_received_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Passport in Embassy Status -->
                                <form action="{{ route('admin.candidate.passportinembassy') }}" method="POST" id="passportinembassy" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Passport in Embassy</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="passport_in_embassy" id="passport_in_embassy" value="1"/>
                                                    <label class="form-check-label" for="passport_in_embassy">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="passport_in_embassy" id="passport_in_embassy_not" value="0"/>
                                                    <label class="form-check-label" for="passport_in_embassy_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Visa Stamped -->
                                <form action="{{ route('admin.candidate.visastamped') }}" method="POST" id="visastamped" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Visa Stamped</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="visa_stamped" id="visa_stamped" value="1"/>
                                                    <label class="form-check-label" for="visa_stamped">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="visa_stamped" id="visa_stamped_not" value="0"/>
                                                    <label class="form-check-label" for="visa_stamped_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Applied For Emigration -->
                                <form action="{{ route('admin.candidate.appliedforemigration') }}" method="POST" id="appliedforemigration" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Applied For Emigration</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="applied_for_emigration" id="applied_for_emigration" value="1"/>
                                                    <label class="form-check-label" for="applied_for_emigration">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="applied_for_emigration" id="applied_for_emigration_not" value="0"/>
                                                    <label class="form-check-label" for="applied_for_emigration_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Emigration Approved -->
                                <form action="{{ route('admin.candidate.emigrationapproved') }}" method="POST" id="emigrationapproved" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Emigration Approved</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="emigration_approved" id="emigration_approved" value="1"/>
                                                    <label class="form-check-label" for="emigration_approved">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="emigration_approved" id="emigration_approved_not" value="0"/>
                                                    <label class="form-check-label" for="emigration_approved_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Waiting for FLight Ticket -->
                                <form action="{{ route('admin.candidate.waitingforticket') }}" method="POST" id="waitingforflightticket" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Waiting For Flight Ticket</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="waiting_for_flight_ticket" id="waiting_for_flight_ticket" value="1"/>
                                                    <label class="form-check-label" for="waiting_for_flight_ticket">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="waiting_for_flight_ticket" id="waiting_for_flight_ticket_not" value="0"/>
                                                    <label class="form-check-label" for="waiting_for_flight_ticket_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Ticket Confirmed -->
                                <form action="{{ route('admin.candidate.ticketconfirmed') }}" method="POST" id="ticketconfirmed" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Ticket Confirmed</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="ticket_confirmed" id="ticket_confirmed" value="1"/>
                                                    <label class="form-check-label" for="ticket_confirmed">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="ticket_confirmed" id="ticket_confirmed_not" value="0"/>
                                                    <label class="form-check-label" for="ticket_confirmed_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Deployed Status -->
                                <form action="{{ route('admin.candidate.deployed') }}" method="POST" id="deployedStatus" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Deployed</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="deployed" id="deployed" value="1"/>
                                                    <label class="form-check-label" for="deployed">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="deployed" id="deployed_not" value="0"/>
                                                    <label class="form-check-label" for="deployed_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                            {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Cancelled Status -->
                                <form action="{{ route('admin.candidate.cancelled') }}" method="POST" id="cancelledStatus" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Cancelled</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="cancelled" id="cancelled" value="1"/>
                                                    <label class="form-check-label" for="cancelled">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="cancelled" id="cancelled_not" value="0"/>
                                                    <label class="form-check-label" for="cancelled_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Hold Status -->
                                <form action="{{ route('admin.candidate.hold') }}" method="POST" id="holdStatus" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Hold</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="hold" id="hold" value="1"/>
                                                    <label class="form-check-label" for="hold">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="hold" id="hold_not" value="0"/>
                                                    <label class="form-check-label" for="hold_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- Visa Cancelled Status -->
                                <form action="{{ route('admin.candidate.visacancelled') }}" method="POST" id="visacencelled" style="display: none">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="">Visa Cancelled</label>
                                            <div class="mb-3">
                                                <div class="form-check form-check-inline mt-3">
                                                    <input class="form-check-input" type="radio" name="visa_cencelled" id="visa_cencelled" value="1"/>
                                                    <label class="form-check-label" for="visa_cencelled">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="visa_cencelled" id="visa_cencelled_not" value="0"/>
                                                    <label class="form-check-label" for="visa_cencelled_not">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-sm float-end">Update Status</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-foo">

                    </div>
                </div>
            </div>
        </div>
        <!-- Update Status Modal End -->

        <!-- Update Mofa No Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editMofaNo" aria-labelledby="editMofaNoLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 id="editMofaNoLabel" class="offcanvas-title">Edit Mofa No</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="editMofaNoForm" action="{{ route('admin.candidate.update.mofa') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <input type="hidden" name="editID" value="{{ $post->id }}">

                    <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="add-mofa-no">Mofa No <span class="text-danger">*</span></label>
                        <input type="text" name="mofa_no" id="add-mofa-no" value="{{ $post->mofa_no }}" class="form-control" placeholder="Enter mofa no...">
                    </div>
                    </div>

                </div>
                {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editMofaBtn">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
        </div>
        <!-- Update Mofa No End -->

        <!-- Update Flight Start -->
        <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="editFlight" aria-labelledby="editFlightLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editFlightLabel" class="offcanvas-title">Edit Flight</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="editFlightForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                    <input type="hidden" name="editID" value="{{ $post->id }}">

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-flight-no">Flight Date <span class="text-danger">*</span></label>
                            <input type="text" name="flight_date" id="add-flight-no" value="{{ $post->flight_date }}" class="form-control" placeholder="Enter Flight...">
                        </div>
                    </div>

                    </div>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editflightBtn">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Update Flight End -->


        <!-- Reminder Candidate -->
        <div class="modal fade" id="reminderCP" aria-hidden="true" aria-labelledby="reminderCPLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="reminderCPLabel">Created Reminder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.candidate.reminder.store') }}" method="POST" id="createReminder">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="candidateID" value="{{ $post->id }}">
                                <input type="hidden" name="careoff_id" value="{{ $post->careoff_id }}">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="add-reminder-title" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="add-reminder-title" class="form-control" placeholder="Enter title...">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="add-reminder-desc" class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea name="desc" id="add-reminder-desc" class="form-control" cols="30" rows="5"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add-reminder-type" class="form-label">Reminder Type <span class="text-danger">*</span></label>
                                        <select name="todolabel_id" id="add-reminder-type" data-allow-clear="true" data-placeholder="Please Select Reminder Type" class="form-select select2" >
                                            <option value="">Select</option>
                                            @foreach ($todolabels as $todolabel)
                                                <option value="{{ $todolabel->id }}">{{ $todolabel->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add-reminder-due-date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                        <input type="text" name="due_date" id="add-reminder-due-date" placeholder="Select Due Date..." class="form-control flatpicker-date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add-reminder-time" class="form-label">Time <span class="text-danger">*</span></label>
                                        <input type="text" name="time" id="add-reminder-time" placeholder="Select Time" class="flatpicker-time form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Set Reminder</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Reminder Candidate -->

        <!-- Reminder Update Candidate Start -->
        <div class="modal fade" id="updateReminder" aria-hidden="true" aria-labelledby="updateReminderLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
            <div class="modal-content">
                <div class="modal-header pb-2">
                <h5 class="offcanvas-title" id="updateReminderLabel">Update Reminder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.candidate.reminder.update') }}" method="POST" id="updateReminderValidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="candID" value="{{ $post->id }}">
                                <input type="hidden" name="careoff_id" value="{{ $post->careoff_id }}">
                                <input type="hidden" name="reminder_id" id="reminder_id">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="create-new-reminder" class="form-label">Create New Reminder</label>
                                        <select name="new_reminder" id="create-new-reminder" class="form-select select2" data-placeholder="Select Reminder" data-allow-clear="true">
                                            <option value=""></option>
                                            <option value="Required">Required</option>
                                            <option value="Not Required">Not Required</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="update-reminder-title" class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="update-reminder-title" class="form-control" placeholder="Enter title...">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="update-reminder-desc" class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea name="desc" id="update-reminder-desc" class="form-control" cols="30" rows="5"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6 reminderDiv">
                                    <div class="mb-3">
                                        <label for="update-reminder-type" class="form-label">Reminder Type <span class="text-danger">*</span></label>
                                        <select name="todolabel_id" id="update-reminder-type" class="form-select select2" data-placeholder="Select Reminder Type">
                                            <option value=""></option>
                                            @foreach ($todolabels as $todolabel)
                                                <option value="{{ $todolabel->id }}">{{ $todolabel->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 reminderDiv">
                                    <div class="mb-3">
                                        <label for="update-reminder-due-date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                        <input type="text" name="due_date" id="update-reminder-due-date" class="form-control flatpicker-date">
                                    </div>
                                </div>
                                <div class="col-md-6 reminderDiv">
                                    <div class="mb-3">
                                        <label for="update-reminder-time" class="form-label">Time <span class="text-danger">*</span></label>
                                        <input type="text" name="time" id="update-reminder-time" class="flatpicker-time form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Set Reminder</button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
        <!-- Reminder Update Candidate End -->

</div>
@endsection
@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/additional-methods.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
{{-- <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script> --}}
<script src="{{ asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
<script src="{{ asset('admin/assets/pages/validation/candidate-validation.js') }}"></script>
<script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
<script src="{{ asset('admin/assets/js/main.js') }}"></script>
<script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/gh/jquery-form/form@4.3.0/dist/jquery.form.min.js"></script>

<script>
    $(document).ready(function(){
        $('#updateReminder').on('show.bs.modal',function(e){
            var reminderID = $(e.relatedTarget).data('id');
            $('#reminder_id').val(reminderID);

            jQuery.ajax({
                url: "{{ route('admin.candidate.reminder.edit') }}",
                method: "GET",
                type: "html",
                data:{
                    id: reminderID
                },
                success: function(data){
                    $("#update-reminder-title").val(data.title);
                    $("#update-reminder-desc").val(data.desc);
                    $("#update-reminder-type").val(data.todolabel_id).trigger('change');
                    $("#update-reminder-due-date").val(data.due_date);
                    $("#update-reminder-time").val(data.time);

                }
            });

        });
    });
</script>

<script>
    $(document).ready(function(){
        $('#create-new-reminder').on('change',function(){
            var value = $(this).val();

            if (value == 'Required') {
                $('.reminderDiv').show();
            } else if (value == 'Not Required') {
                $('.reminderDiv').hide();
            } else {
                $('.reminderDiv').show();
            }

        });
    });
</script>


<script>
   $(document).ready(function(){
      //  var table = $('.datatables-users').DataTable();

       $('#NewCandStatusUpdate').on('show.bs.modal',function(e){
           var editID = $(e.relatedTarget).data('id');
           $('#candIDN').val(editID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });

           jQuery.ajax({
               url: '{{ url("admin/candidate/status/get") }}',
               method: "POST",
               type: "html",
               data: {
                   "id": editID,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   // Candidate Are Ready
                   $('#candidateName').text(data.cand_name);
                   $('#candPassport').text(data.pass_no);
                   if (data.new_candidate == 1 && data.candidate_are_ready == 0 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("New Candidate");
                       $('#candidateareready').show();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.candidate_are_ready == 1) {
                           $('#candidate_are_ready').attr('checked',true).change();
                       } else {
                           $('#candidate_are_ready_not').attr('checked',true).change();
                       }
                   }

                   // Publish For Selection
                   if (data.candidate_are_ready == 1 && data.published_for_selection == 0 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("Candidate Are Ready");
                       $('#candidateareready').hide();
                       $('#publishedforselection').show();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.published_for_selection == 1) {
                           $('#published_for_selection').attr('checked',true).change();
                       } else {
                           $('#published_for_selection_not').attr('checked',true).change();
                       }
                   }
                   // Selected
                   if (data.published_for_selection == 1 && data.selected == 0 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("Published For Selection");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').show();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.selected == 1) {
                           $('#selected').attr('checked',true).change();
                       } else {
                           $('#selected_not').attr('checked',true).change();
                       }
                   }
                   // Visa Received
                   if (data.selected == 1 && data.visa_received == 0 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("Selected");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').show();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.visa_received == 1) {
                           $('#visa_received').attr('checked',true).change();
                       } else {
                           $('#visa_received_not').attr('checked',true).change();
                       }
                   }
                   // Passport in Embassy
                   if (data.visa_received == 1 && data.passport_in_embassy == 0 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("Visa Received");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').show();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.passport_in_embassy == 1) {
                           $('#passport_in_embassy').attr('checked',true).change();
                       } else {
                           $('#passport_in_embassy_not').attr('checked',true).change();
                       }
                   }
                   // Visa Stamped
                   if (data.passport_in_embassy == 1 && data.visa_stamped == 0 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("Passport in Embassy");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').show();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.visa_stamped == 1) {
                           $('#visa_stamped').attr('checked',true).change();
                       } else {
                           $('#visa_stamped_not').attr('checked',true).change();
                       }
                   }
                   // Applied For Emigration
                   if (data.visa_stamped == 1 && data.applied_for_emigration == 0 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("Visa Stamped");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').show();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.applied_for_emigration == 1) {
                           $('#applied_for_emigration').attr('checked',true).change();
                       } else {
                           $('#applied_for_emigration_not').attr('checked',true).change();
                       }
                   }
                   // Emigration Approved
                   if (data.applied_for_emigration == 1 && data.emigration_approved == 0 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("Applied For Emigration");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').show();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.emigration_approved == 1) {
                           $('#emigration_approved').attr('checked',true).change();
                       } else {
                           $('#emigration_approved_not').attr('checked',true).change();
                       }
                   }
                   // Waiting For Flight Ticket
                   if (data.emigration_approved == 1 && data.waiting_for_flight_ticket == 0 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("Emigration Approved");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').show();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.waiting_for_flight_ticket == 1) {
                           $('#waiting_for_flight_ticket').attr('checked',true).change();
                       } else {
                           $('#waiting_for_flight_ticket_not').attr('checked',true).change();
                       }
                   }
                   // Ticket Confirmed
                   if (data.waiting_for_flight_ticket == 1 && data.ticket_confirmed == 0 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("Waiting For Flight Ticket");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').show();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.ticket_confirmed == 1) {
                           $('#ticket_confirmed').attr('checked',true).change();
                       } else {
                           $('#ticket_confirmed_not').attr('checked',true).change();
                       }
                   }
                   // Deployed
                   if (data.ticket_confirmed == 1 && data.deployed == 0 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("Ticket Confirmed");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').show();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.deployed == 1) {
                           $('#deployed').attr('checked',true).change();
                       } else {
                           $('#deployed_not').attr('checked',true).change();
                       }
                   }
                   // Deployed
                   if (data.ticket_confirmed == 1 && data.deployed == 1 && data.hold == 0 && data.cancelled == 0) {
                       $('#NewCandStatusUpdateLabel').text("Deployed");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').show();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();
                       if (data.deployed == 1) {
                           $('#deployed').attr('checked',true).change();
                       } else {
                           $('#deployed_not').attr('checked',true).change();
                       }
                   }
                   // Cancelled
                   if (data.cancelled == 1) {
                       $('#NewCandStatusUpdateLabel').text("Cancelled");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').show();
                       $('#holdStatus').hide();
                       $('#visacencelled').hide();

                       $('#cancelled').attr('checked',true).change();

                   }
                   // Hold
                   if (data.hold == 1) {
                       $('#NewCandStatusUpdateLabel').text("Hold");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').show();
                       $('#visacencelled').hide();

                       $('#hold').attr('checked',true).change();

                   }
                   // Visa Cancelled
                   if (data.visa_cencelled == 1) {
                       $('#NewCandStatusUpdateLabel').text("Visa Cancelled");
                       $('#candidateareready').hide();
                       $('#publishedforselection').hide();
                       $('#selectedStatus').hide();
                       $('#visareceived').hide();
                       $('#passportinembassy').hide();
                       $('#visastamped').hide();
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').hide();
                       $('#cancelledStatus').hide();
                       $('#holdStatus').hide();
                       $('#visacencelled').show();

                       $('#visa_cencelled').attr('checked',true).change();

                   }

               }
           });
       });

       // Hold Status Onclick
       $('.holdButton').on('click',function(e){
           e.preventDefault();
           var candID = $('#candIDN').val();
           var newradiovalue = "1";
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/hold") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'hold': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   table.ajax.reload();
                   $('#NewCandStatusUpdate').modal('hide');

               }
           });
       });

       // Cancelled Status Onclick
       $('.cancelledButton').on('click',function(e){
           e.preventDefault();
           var candID = $('#candIDN').val();
           var newradiovalue = "1";
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/cancelled") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'cancelled': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   $('#NewCandStatusUpdate').modal('hide');
                   table.ajax.reload();
               }
           });

       });

       // Update Candidate Ready Status
       $(document).on('submit','#candidateareready',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="candidate_are_ready"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/newcandidateupdate") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'candidate_are_ready': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   if (data.candidate_are_ready == 1 && data.published_for_selection == 0) {
                       $('#candidateareready').hide();
                       $('#publishedforselection').show();
                       $('#NewCandStatusUpdateLabel').text("Candidate Are Ready");
                       if (data.published_for_selection == 1) {
                           $('#published_for_selection').attr('checked',true).change();
                       } else {
                           $('#published_for_selection_not').attr('checked',true).change();
                       }
                   }

                   table.ajax.reload();
               }
           });
       });
       // Update Published For Selection Status
       $(document).on('submit','#publishedforselection',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="published_for_selection"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/publishedforslection") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'published_for_selection': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   if (data.published_for_selection == 1 && data.selected == 0) {
                       $('#publishedforselection').hide();
                       $('#selectedStatus').show();
                       $('#NewCandStatusUpdateLabel').text("Published For Selection");
                       if (data.selected == 1) {
                           $('#selected').attr('checked',true).change();
                       } else {
                           $('#selected_not').attr('checked',true).change();
                       }
                   }
                   table.ajax.reload();
               }
           });
       });
       // Update Selected Status
       $(document).on('submit','#selectedStatus',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="selected"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/selected") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'selected': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   if (data.selected == 1 && data.visa_received == 0) {
                       $('#selectedStatus').hide();
                       $('#visareceived').show();
                       $('#NewCandStatusUpdateLabel').text("Selected");
                       if (data.visa_received == 1) {
                           $('#visa_received').attr('checked',true).change();
                       } else {
                           $('#visa_received_not').attr('checked',true).change();
                       }
                   }
                   table.ajax.reload();
               }
           });
       });
       // Update Visa Received Status
       $(document).on('submit','#visareceived',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="visa_received"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/visareceived") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'visa_received': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   if (data.visa_received == 1 && data.passport_in_embassy == 0) {
                       $('#visareceived').hide();
                       $('#passportinembassy').show();
                       $('#NewCandStatusUpdateLabel').text("Visa Received");
                       if (data.passport_in_embassy == 1) {
                           $('#passport_in_embassy').attr('checked',true).change();
                       } else {
                           $('#passport_in_embassy_not').attr('checked',true).change();
                       }
                   }
                   table.ajax.reload();
               }
           });
       });
       // Update Passport in Embassy Status
       $(document).on('submit','#passportinembassy',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="passport_in_embassy"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/passportinembassy") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'passport_in_embassy': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   if (data.passport_in_embassy == 1 && data.visa_stamped == 0) {
                       $('#passportinembassy').hide();
                       $('#visastamped').show();
                       $('#NewCandStatusUpdateLabel').text("Passport In Embassy");
                       if (data.visa_stamped == 1) {
                           $('#visa_stamped').attr('checked',true).change();
                       } else {
                           $('#visa_stamped_not').attr('checked',true).change();
                       }
                   }
                   table.ajax.reload();
               }
           });
       });
       // Update Visa Stamp Status
       $(document).on('submit','#visastamped',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="visa_stamped"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/visastamped") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'visa_stamped': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   if (data.visa_stamped == 1 && data.applied_for_emigration == 0) {
                       $('#visastamped').hide();
                       $('#appliedforemigration').show();
                       $('#NewCandStatusUpdateLabel').text("Visa Stamped");
                       if (data.applied_for_emigration == 1) {
                           $('#applied_for_emigration').attr('checked',true).change();
                       } else {
                           $('#applied_for_emigration_not').attr('checked',true).change();
                       }
                   }
                   table.ajax.reload();
               }
           });
       });
       // Update Applied For Emigration Status
       $(document).on('submit','#appliedforemigration',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="applied_for_emigration"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/appliedforemigration") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'applied_for_emigration': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   if (data.applied_for_emigration == 1 && data.emigration_approved == 0) {
                       $('#appliedforemigration').hide();
                       $('#emigrationapproved').show();
                       $('#NewCandStatusUpdateLabel').text("Applied For Emigration");
                       if (data.emigration_approved == 1) {
                           $('#emigration_approved').attr('checked',true).change();
                       } else {
                           $('#emigration_approved_not').attr('checked',true).change();
                       }
                   }
                   table.ajax.reload();
               }
           });
       });
       // Update Emigration Approved Status
       $(document).on('submit','#emigrationapproved',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="emigration_approved"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/emigrationapproved") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'emigration_approved': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   if (data.emigration_approved == 1 && data.waiting_for_flight_ticket == 0) {
                       $('#emigrationapproved').hide();
                       $('#waitingforflightticket').show();
                       $('#NewCandStatusUpdateLabel').text("Emigration Approved");
                       if (data.waiting_for_flight_ticket == 1) {
                           $('#waiting_for_flight_ticket').attr('checked',true).change();
                       } else {
                           $('#waiting_for_flight_ticket_not').attr('checked',true).change();
                       }
                   }
                   table.ajax.reload();
               }
           });
       });
       // Update Waiting For Flight Ticket Status
       $(document).on('submit','#waitingforflightticket',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="waiting_for_flight_ticket"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/waitingforticket") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'waiting_for_flight_ticket': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   if (data.waiting_for_flight_ticket == 1 && data.ticket_confirmed == 0) {
                       $('#waitingforflightticket').hide();
                       $('#ticketconfirmed').show();
                       $('#NewCandStatusUpdateLabel').text("Waiting For Flight Ticket");
                       if (data.ticket_confirmed == 1) {
                           $('#ticket_confirmed').attr('checked',true).change();
                       } else {
                           $('#ticket_confirmed_not').attr('checked',true).change();
                       }
                   }
                   table.ajax.reload();
               }
           });
       });
       // Update Ticket Confirmed Status
       $(document).on('submit','#ticketconfirmed',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="ticket_confirmed"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/ticketconfirmed") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'ticket_confirmed': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   if (data.ticket_confirmed == 1 && data.deployed == 0) {
                       $('#ticketconfirmed').hide();
                       $('#deployedStatus').show();
                       $('#NewCandStatusUpdateLabel').text("Ticket Confirmed");
                       if (data.deployed == 1) {
                           $('#deployed').attr('checked',true).change();
                       } else {
                           $('#deployed_not').attr('checked',true).change();
                       }
                   }
                   table.ajax.reload();
               }
           });
       });
       // Update Deployed Status
       $(document).on('submit','#deployedStatus',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="deployed"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/deployed") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'deployed': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   // if (data.deployed == 1 && data.cancelled == 0) {
                   //     $('#deployedStatus').hide();
                   //     $('#cancelledStatus').show();
                   //     $('#NewCandStatusUpdateLabel').text("Visa Received");
                   //     if (data.cancelled == 1) {
                   //         $('#cancelled').attr('checked',true).change();
                   //     } else {
                   //         $('#cancelled_not').attr('checked',true).change();
                   //     }
                   // }
                   $('#deployedStatus').hide();
                   $('#NewCandStatusUpdate').modal('hide');
                   table.ajax.reload();
               }
           });
       });
       // Update Cancelled Status
       $(document).on('submit','#cancelledStatus',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="cancelled"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/cancelled") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'cancelled': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   // if (data.cancelled == 1 && data.hold == 0) {
                   //     $('#cancelledStatus').hide();
                   //     $('#holdStatus').show();
                   //     $('#NewCandStatusUpdateLabel').text("Visa Received");
                   //     if (data.hold == 1) {
                   //         $('#hold').attr('checked',true).change();
                   //     } else {
                   //         $('#hold_not').attr('checked',true).change();
                   //     }
                   // }
                   $('#cancelledStatus').hide();
                   $('#NewCandStatusUpdate').modal('hide');
                   table.ajax.reload();
               }
           });
       });
       // Update Hold Status
       $(document).on('submit','#holdStatus',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="hold"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/hold") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'hold': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){

                   $('#holdStatus').hide();
                   $('#NewCandStatusUpdate').modal('hide');
                   table.ajax.reload();
               }
           });
       });
       // Update Visa Cancelled Status
       $(document).on('submit','#visacencelled',function(e){
           e.preventDefault();
           var newradiovalue = $('input[name="visa_cencelled"]:checked').val();
           var candID = $('#candIDN').val();
           // alert(candID);
           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });
           jQuery.ajax({
               url : '{{ url("admin/candidate/status/visacancelled") }}',
               method: "POST",
               type: "html",
               data:{
                   'cand_id': candID,
                   'visa_cencelled': newradiovalue,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){

                   $('#visacencelled').hide();
                       // $('#NewCandStatusUpdateLabel').text("Visa Received");
                   $('#NewCandStatusUpdate').modal('hide');

                   table.ajax.reload();
               }
           });
       });


   });
</script>

    <script>
        $(document).ready(function(){
            $('.add-mob-number-det').intlTelInput({

                // localizedCountries: true,
                onlyCountries: ["in","sa","qa","ae","kw"],
                preferredCountries: ["in","sa"],
                separateDialCode: true,
                initialCountry: "",


            }).on('countrychange',function(e,countryData){
                $('#mobile-no-dial-code-add-det').val(($(".add-mob-number-det").intlTelInput("getSelectedCountryData").dialCode))
            });

            $('.add-contact-number-det').intlTelInput({

                // localizedCountries: true,
                onlyCountries: ["in","sa","qa","ae","kw"],
                preferredCountries: ["in","sa"],
                separateDialCode: true,
                initialCountry: "",


            }).on('countrychange',function(e,countryData){
                $('#contact-no-dial-code-add-det').val(($(".add-contact-number-det").intlTelInput("getSelectedCountryData").dialCode))
            });

            $('.add-relative-number-det').intlTelInput({

                // localizedCountries: true,
                onlyCountries: ["in","sa","qa","ae","kw"],
                preferredCountries: ["in","sa"],
                separateDialCode: true,
                initialCountry: "",


            }).on('countrychange',function(e,countryData){
                $('#relative-contact-no-dial-code-add-det').val(($(".add-relative-number-det").intlTelInput("getSelectedCountryData").dialCode))
            });

        });
    </script>

<script>
   $(document).ready(function(){
      // Select 2
      var select = $('.select2');

      select.each(function (){
         var $this = $(this);
         $this.wrap('<div class="position-relative"></div>');
         $this.select2({
            // placeholder: "Select",
            dropdownParent: $this.parent()
         });
      });
      // Image display
      let showSlip = document.getElementById('uploadedAvatar');
      const slipfile = document.querySelector('#add-payment-slip-file');
      if (showSlip) {
         slipfile.onchange = () => {
            if (slipfile.files[0]) {
               showSlip.src = window.URL.createObjectURL(slipfile.files[0]);
            }
         };
      }

   });
</script>

<script>
   $("#add-flight-no").flatpickr();
   $('.flatpicker-date').flatpickr();
    $('.flatpicker-time').flatpickr({
        enableTime: true,
        noCalendar: true
    });
</script>

<script>
   $(document).ready(function(){
      var _token = $('meta[name="csrf-token"]').attr('content');

      // Form Validation Start
      jQuery.validator.addMethod('filesize',function(value,element,param){
         return this.optional(element) || (element.files[0].size <= param * 1000000)
      });

      $("#addCandPaymentForm").validate({
         rules:{
            amount:{
               required: true,
               number: true
            },
            payment_mode:{
               required: true,
            },
            txn_id:{
               required: true,
               remote:{
                  type: 'POST',
                  url: "{{ url('admin/candidate/check/transaction-number') }}",
                  data:{
                     txn_id: function (){
                        return $('#add-txn-id').val();
                     },
                     "_token": _token
                  }
               }
            },
            bank_to:{
               required: true
            },
            payment_slip:{
               extension: "jpeg|jpg|png",
               filesize: 0.5,

            }
         },
         messages:{
            amount:{
               required: "Please enter amount",
               number: "Please enter valid number"
            },
            payment_mode:{
               required: "Please select payment mode",
            },
            txn_id:{
               required: "Please enter transaction id",
               remote: "Transaction id already exists!"
            },
            bank_to:{
               required: "Please select Bank to"
            },
            payment_slip:{
               extension: "only jpeg/jpg/png file accepted",
               filesize: "Maximum filesize upto 512KB only",

            }
         },

      });

      $('#addServiceChargeForm').validate({
         rules:{
            amount:{
               required: true,
               number: true
            },
            given_by:{
               required: true,
            },

         },
         messages:{
            amount:{
               required: "Please enter amount",
               number: "Please enter valid number"
            },
            given_by:{
               required: "Please select given By",
            }
         },
      });

      $('#editCareoffForm').validate({
        rules:{
            associate_id:{
                required: true
            }
        },
        messages:{
            associate_id:{
                required: "Please select Associate"
            }
        }
      });
      // Form Validation End



      toastr.options = {
        "timeOut": 5000,
        "showDuration": 300,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
      };
      $('#editDetailsbtn').click(function(e){
         e.preventDefault();

         var formdata = $('#editDetailsForm').serialize();

         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.update.details") }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#editDetails').offcanvas('hide');
               // Load Section
               $('#nav-details-div').load(" #nav-details-div");
               $('#mainContainDiv').load(" #mainContainDiv");

            }
         });

      });

      $('#editCareoffbtn').click(function(e){
         e.preventDefault();

         var formdata = $('#editCareoffForm').serialize();

            $("#editCareoffForm").valid();

            if ($("#editCareoffForm").valid()) {
                $.ajax({
                    type: "POST",
                    url: '{{ route("admin.candidate.update.careoff") }}',
                    data: formdata,
                    success: function(response){
                        // Show Success Message
                        toastr.success(response.success);
                        // Hide Modal
                        $('#editCareoff').offcanvas('hide');
                        // Load Section
                        $('#nav-details-div').load(" #nav-details-div");
                        $('#mainContainDiv').load(" #mainContainDiv");

                    }
                });
            }



      });

      $('#editAssocbtn').click(function(e){
         e.preventDefault();

         var formdata = $('#editAssociateForm').serialize();

         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.update.assoc") }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#editAssociate').offcanvas('hide');
               // Load Section
               $('#nav-details-div').load(" #nav-details-div");
               $('#mainContainDiv').load(" #mainContainDiv");

            }
         });

      });

      $('#editPersonalBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#editPersonalForm').serialize();

         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.update.personal") }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#editPersonal').offcanvas('hide');
               // Load Section
               $('#nav-personal-div').load(" #nav-personal-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });

      $('#editExperienceBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#editExperienceForm').serialize();

         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.update.experience") }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#editExperience').offcanvas('hide');
               // Load Section
               $('#nav-experience-div').load(" #nav-experience-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });

      $('#editMusanedBtn').click(function(e){
         e.preventDefault();

         // var formdata = $('#editMusanedForm').serialize();

         var formData = new FormData();
         var _token =  "{{ csrf_token() }}";
         formData.append('editMusanedID',$('#editMusanedID').val());
         formData.append('musaned_status',$('#musaned_status').val());
         formData.append('musaned_reg_date',$('#add-musaned-registration-date').val());
         formData.append('musaned_file',$('#add-musaned-copy')[0].files[0]);
         formData.append('_token',_token);

         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.update.musaned") }}',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#editMusaned').offcanvas('hide');

               // Load Section
               $('#nav-musaned-status-div').load(" #nav-musaned-status-div");
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });

      $('#editMedicalBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#editMedicalForm').serialize();

         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.update.medical") }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#editMedical').offcanvas('hide');

               // Load Section
               $('#nav-medical-div').load(" #nav-medical-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });

      $('#editMofaBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#editMofaNoForm').serialize();

         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.update.mofa") }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#editMofaNo').offcanvas('hide');

               // Load Section
               $('#nav-mofa-div').load(" #nav-mofa-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });


      $('#editflightBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#editFlightForm').serialize();

         // alert(formdata);

         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.update.flight") }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#editFlight').offcanvas('hide');

               // Load Section
               $('#nav-flight-div').load(" #nav-flight-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });


      $('#editPassportBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#editPassportForm').serialize();

         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.update.passport") }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#editPassport').offcanvas('hide');

               // Load Section
               $('#nav-passport-div').load(" #nav-passport-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });



      $(document).on('click','#editadpaymentBtn',function(e){
         e.preventDefault();

         // var formdata = $('#addCandPaymentForm').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';

         $("#addCandPaymentForm").valid();


         var formData = new FormData();
         var _token =  "{{ csrf_token() }}";
         formData.append('amount',$('#add-amount-cand').val());
         formData.append('payment_mode',$('#add-payment-mode').val());
         formData.append('txn_id',$('#add-txn-id').val());
         formData.append('bank_to',$('#add-bank-to').val());
         formData.append('payment_slip',$('#add-payment-slip-file')[0].files[0]);
         formData.append('_token',_token);



         $.ajax({
            type: "POST",
            url: '{{ route("admin.candamtStr",$post->id) }}',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Reste Form
               $('#addCandPaymentForm')[0].reset();
               // Hide Modal
               $('#addCandPayment').offcanvas('hide');

               // Load Section
               $('#nav-cand-payment-div').load(" #nav-cand-payment-div");
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");

            }
         });
      });

      // $('#editadpaymentBtn').click(function(e){
      //    e.preventDefault();

      //    // var formdata = $('#addCandPaymentForm').serialize();

      //    // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';

      //    var formData = new FormData();
      //    var _token =  "{{ csrf_token() }}";
      //    formData.append('amount',$('#add-amount-cand').val());
      //    formData.append('payment_mode',$('#add-payment-mode').val());
      //    formData.append('txn_id',$('#add-txn-id').val());
      //    formData.append('bank_to',$('#add-bank-to').val());
      //    formData.append('payment_slip',$('#add-payment-slip-file')[0].files[0]);
      //    formData.append('_token',_token);


      //    $.ajax({
      //       type: "POST",
      //       url: '{{ route("admin.candamtStr",$post->id) }}',
      //       data: formData,
      //       processData: false,
      //       contentType: false,
      //       success: function(response){
      //          // Show Success Message
      //          toastr.success(response.success);
      //          // Reste Form
      //          $('#addCandPaymentForm')[0].reset();
      //          // Hide Modal
      //          $('#addCandPayment').offcanvas('hide');

      //          // Load Section
      //          $('#nav-cand-payment-div').load(" #nav-cand-payment-div");
      //          $('#nav-upload-file-div').load(" #nav-upload-file-div");
      //          $('#mainContainDiv').load(" #mainContainDiv");

      //       }
      //    });

      // });



      $('#editCadnPaymentBtn2').click(function(e){
         e.preventDefault();

         // var formdata = $('#editCandPaymentForm').serialize();

         var formData = new FormData();
         var _token =  "{{ csrf_token() }}";
         formData.append('paycand_id',$('#paycand_id').val());
         formData.append('amount',$('#edit-amount-cand').val());
         formData.append('payment_mode',$('#edit-payment-mode').val());
         formData.append('txn_id',$('#edit-txn-id').val());
         formData.append('bank_to',$('#edit-bank-to').val());
         formData.append('payment_slip',$('#edit-payment-slip-file')[0].files[0]);
         formData.append('_token',_token);

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';


         $.ajax({
            type: "POST",
            url: '{{ route("admin.candamtUpdt") }}',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#editCandPayment').offcanvas('hide');

               // Load Section
               $('#nav-cand-payment-div').load(" #nav-cand-payment-div");
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });

      $('#editServiceChargeBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#addServiceChargeForm').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';

         $('#addServiceChargeForm').valid();

         $.ajax({
            type: "POST",
            url: '{{ route("admin.candscharge",$post->id) }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Reset Form
               $('#given_by').val('').trigger('change');
               $('#addServiceChargeForm')[0].reset();

               // Hide Modal
               $('#addServiceCharge').offcanvas('hide');




               // Load Section
               $('#nav-service-charge-div').load(" #nav-service-charge-div");
               $('#nav-cand-payment-div').load(" #nav-cand-payment-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });

      $('#editSerChargeBtn2').click(function(e){
         e.preventDefault();

         var formdata = $('#UpdateServiceChargeForm').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';


         $.ajax({
            type: "POST",
            url: '{{ route("admin.candscharge",$post->id) }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#UpdateServiceCharge').offcanvas('hide');

               // Load Section
               $('#nav-service-charge-div').load(" #nav-service-charge-div");
               $('#nav-cand-payment-div').load(" #nav-cand-payment-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });

      $('#editUploadDocs2btn').click(function(e){
         e.preventDefault();

         // var formdata = $('#photoValid22').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';

         var formData = new FormData();
         var _token =  "{{ csrf_token() }}";
         formData.append('label',$('#add-label-file').val());
         formData.append('filename',$('#add-upload-file-2')[0].files[0]);
         formData.append('_token',_token);

         // console.log(formData.get('label'));
         // console.log(formData.get('filename'));
         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.docs2.store",$post->id) }}',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#photoValid22')[0].reset();
               $('#uploadDocs2').modal('hide');

               // Load Section
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");

            }
         });

      });

      $('#editUploadBtn22').click(function(e){
         e.preventDefault();

         // var formdata = $('#photoValid22').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';

         var formData = new FormData();
         var _token =  "{{ csrf_token() }}";
         formData.append('photo_file',$('#add-upload-photo2')[0].files[0]);
         formData.append('pass_file',$('#add-upload-pp2')[0].files[0]);
         formData.append('lic_file',$('#add-upload-lic2')[0].files[0]);
         formData.append('cv_file',$('#add-upload-cv2')[0].files[0]);
         formData.append('pass_back_file',$('#add-upload-ppb2')[0].files[0]);
         formData.append('_token',_token);

         // console.log(formData.get('label'));
         // console.log(formData.get('filename'));
         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.docs.store",$post->id) }}',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#photoValid2')[0].reset();
               $('#uploadDocs').modal('hide');

               // Load Section
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");

            }
         });

      });

      $('#deleteCandFIleBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#deletecandFileForm').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';


         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.candfile") }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#deletecandFile').modal('hide');

               // Load Section
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");

            }
         });

      });

      $('#deleteMusanedbtn').click(function(e){
         e.preventDefault();

         var formdata = $('#deleteMusanedFileForm').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';


         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.musanedDel",$post->id) }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#deleteMusanedFile').modal('hide');

               // Load Section
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");

            }
         });

      });

      $('#deleteFullImgBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#deleteFullImgForm').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';


         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.fullimageDel",$post->id) }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#deleteFullImg').modal('hide');

               // Load Section
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");

            }
         });

      });

      $('#deleteLiceBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#deleteLiceForm').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';


         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.licenseDel",$post->id) }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#deleteLice').modal('hide');

               // Load Section
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });

      $('#deletePassFileBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#deletePassForm').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';


         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.passportDel",$post->id) }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#deletePassFile').modal('hide');

               // Load Section
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });

      $('#deletePassBackFileBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#deletePassBackForm').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';


         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.passportBackDel",$post->id) }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#deletePassBackFile').modal('hide');

               // Load Section
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });

      $('#deletePhotoBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#deletePhotoForm').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';


         $.ajax({
            type: "POST",
            url: '{{ route("admin.candidate.photoDel",$post->id) }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               toastr.success(response.success);
               // Hide Modal
               $('#deletePhoto').modal('hide');

               // Load Section
               $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });

      });

      $(document).on('click','.partnerExcuteCVBtn', function(e){
         e.preventDefault();
         var partner_id = $(this).attr('data-id');

         var routeUrl = '{{ url("admin/candidate/cv/execute") }}/{{ $post->id }}/'+partner_id;
         $.ajax({
            type: "GET",
            url: routeUrl,
            // data: formdata,
            success: function(response){
               // Show Success Message
               if(response.success){
                  toastr.success(response.success);
               }else{
                  toastr.error(response.error);
               }

               // Hide Modal
               // $('#deletePhoto').modal('hide');

               // Load Section


               $('#nav-cvexecute-tab-div').load(" #nav-cvexecute-tab-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });
      });

      // $('.partnerExcuteCVBtn').click(function(e){
      //    e.preventDefault();
      //    var partner_id = $(this).attr('data-id');
      //    // var formdata = $('#deletePhotoForm').serialize();




      //    var routeUrl = '{{ url("admin/candidate/cv/execute") }}/{{ $post->id }}/'+partner_id;
      //    // alert(partner_id);

      //    $.ajax({
      //       type: "GET",
      //       url: routeUrl,
      //       // data: formdata,
      //       success: function(response){
      //          // Show Success Message
      //          if(response.success){
      //             toastr.success(response.success);
      //          }else{
      //             toastr.error(response.error);
      //          }

      //          // Hide Modal
      //          // $('#deletePhoto').modal('hide');

      //          // Load Section


      //          $('#nav-cvexecute-tab-div').load(" #nav-cvexecute-tab-div");
      //          $('#mainContainDiv').load(" #mainContainDiv");
      //       }
      //    });

      // });

      $(document).on('click','#candCvExecuteBtn',function(e){
         e.preventDefault();

         // var formdata = $('#deletePhotoForm').serialize();


         // alert(routeUrl);

         $.ajax({
            type: "GET",
            url: "{{ route('admin.candidate.cv.execute',$post->id) }}",
            // data: formdata,
            success: function(response){
               // Show Success Message
               if(response.success){
                  toastr.success(response.success);
               }else{
                  toastr.error(response.error);
               }

               // Hide Modal
               // $('#deletePhoto').modal('hide');

               // Load Section
               // $('#nav-cvexecute-tab-div').load(" #nav-cvexecute-tab-div");
               $('#mainContainDiv').load(" #mainContainDiv");
            }
         });
      });

      // $('#candCvExecuteBtn').click(function(e){
      //    e.preventDefault();

      //    // var formdata = $('#deletePhotoForm').serialize();


      //    // alert(routeUrl);

      //    $.ajax({
      //       type: "GET",
      //       url: "{{ route('admin.candidate.cv.execute',$post->id) }}",
      //       // data: formdata,
      //       success: function(response){
      //          // Show Success Message
      //          if(response.success){
      //             toastr.success(response.success);
      //          }else{
      //             toastr.error(response.error);
      //          }

      //          // Hide Modal
      //          // $('#deletePhoto').modal('hide');

      //          // Load Section
      //          // $('#nav-cvexecute-tab-div').load(" #nav-cvexecute-tab-div");
      //          $('#mainContainDiv').load(" #mainContainDiv");
      //       }
      //    });

      // });

      $('.downloadCandCVBtn').click(function(e){
         e.preventDefault();

         var formdata = $('#downloadCVvalidation').serialize();

         // var routeUrl = '{{ route("admin.candamtStr",$post->id) }}';


         $.ajax({
            type: "POST",
            url: '{{ route("admin.download.cvcompany",$post->id) }}',
            data: formdata,
            success: function(response){
               // Show Success Message
               if(response.success){
                  toastr.success(response.success);
               }else{
                  toastr.error(response.error);
               }
               // Hide Modal
               $('#downloadcvcomp').modal('hide');

               // Load Section
               // $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");
               $('#nav-cvexecute-tab-div').load(" #nav-cvexecute-tab-div");
            }
         });

      });

      $(document).on('click','.cvExecuteMultipleBtn',function(e){
         e.preventDefault();
         var formdata = $('#cvExecuteMultiple').serialize();


         $.ajax({
            type: "POST",
            url: "{{ route('admin.cvexecute.multiple') }}",
            data: formdata,
            success: function(response){
               // Show Success Message
               // console.log(response);
               if(response.success){
                  toastr.success(response.success);
               }else{
                  toastr.error(response.error);
               }

                // Hide Modal
                $('#cvExecuteMULTI').modal('hide');

               // Load Section
               // $('#nav-upload-file-div').load(" #nav-upload-file-div");
               $('#mainContainDiv').load(" #mainContainDiv");
               $('#nav-cvexecute-tab-div').load(" #nav-cvexecute-tab-div");

            }
         });
      });

   });
</script>

<script>
   $(document).ready(function(){
      $('#add-medical-fitness-status').on('change',function(){
         let statusValue = $(this).val();
         if (statusValue == 'Fit') {
            $('#medical_examine_date').show();
            $('#medical_expiry_date').show();
            $('#repeat_examine_date').hide();
         }else if(statusValue == 'Repeat'){
            $('#medical_examine_date').hide();
            $('#medical_expiry_date').show();
            $('#repeat_examine_date').show();
         }else{
            $('#medical_examine_date').hide();
            $('#medical_expiry_date').hide();
            $('#repeat_examine_date').hide();
         }
      });
   });
</script>

<script>
   $(document).ready(function(){
      $('#viewPaymentSlip').on('show.bs.modal',function(e){
         var paymentID = $(e.relatedTarget).data('id');
         jQuery.ajax({
            url:"{{ url('admin/candidate/candamt/paymentslip') }}",
            method: 'get',
            type: 'html',
            data: {
               // "_token": "{{ csrf_token() }}",
               id: paymentID,
            },
            success: function(data){
               // console.log(data);
               $('#disViewSlip').html("<img src='"+data+"' class='img-fluid'>");
            }
         });
      });
   });
</script>

<script>
   $(document).ready(function(){
       $('#deletecandFile').on('show.bs.modal',function(e){
         var editID = $(e.relatedTarget).data('id');
         // alert(editID);
         $('#filecand_id').val(editID);
       });
   });
</script>

<script>
   var ecount = {{ count($experience) }};
   var rowMin = ecount + 1;
   var rowMax = 5 - ecount;

   $(document).on('click','.addExp',function(){
       var html = '';
       html += '<div class="row"><div class="col-md-6"><div class="mb-3">';
       html += '<label class="form-label" for="edit-exoinyear'+rowMin+'">Experience (in year) <span class="text-danger">*</span></label>';
       html += '<input type="text" name="experience[]" id="edit-exoinyear'+rowMin+'" class="form-control" placeholder="Enter experience in year...">';
       html += '</div></div>';

       html += '<div class="col-md-6"><div class="mb-3">';
       html += '<label class="form-label" for="edit-job-type'+rowMin+'">Job Type</label>';
       html += '<select name="proff_id[]" id="edit-job-type'+rowMin+'" class="form-select select2"><option value="">Select</option>';
       html += '@foreach ($jobtypes as $jobtype)<option value="{{ $jobtype->id }}">{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>@endforeach';
       html += '</select></div></div>';

       html += '<div class="col-md-6"><div class="mb-3">';
       html += '<label class="form-label" for="edit-expcountry_id'+rowMin+'">Experience (country name)</label>';
       html += '<select name="expcountry_id[]" id="edit-expcountry_id'+rowMin+'" class="form-select select2">';
       html += '<option value="">Select</option>@foreach ($countries as $country)<option value="{{ $country->id }}">{{ $country->name }}</option>@endforeach';
       html += '</select></div></div>';

       // html += '<div class="col-md-6"><div class="mb-3">';
       // html += '<label for="form-label" for="edit-expcity_id'+rowMin+'">City</label>';
       // html += '<select name="expcity_id[]" id="edit-expcity_id'+rowMin+'" class="form-select select2"><option value="">Select</option>';
       // html += '@foreach ($cities as $city)<option value="{{ $city->id }}">{{ $city->name }}</option>@endforeach';
       // html += '</select></div></div>';

       html += '<div class="col-md-6"><div class="mb-3">';
       html += '<label for="form-label" for="edit-expcity_id-ex'+rowMin+'">City</label>';
       html += '<input type="text" name="expcity_id_text[]" id=edit-expcity_id-ex'+rowMin+'" class="form-control cityTypehead" placeholder="Type city here..."></div>';


       html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end">Remove</button></div>';

       $('#dispEXP').append(html);
       rowMin++;
       rowMax--;
       if (rowMax == 0) {
           $('.addExp').prop('disabled',true);
       }else{
           $('.addExp').prop('disabled',false);
       }


   });

   $(document).on('click','.remove',function(){
       $(this).closest('.row').remove();
       rowMax++;
       rowMin--;
       if (rowMax == 0) {
           $('.addExp').prop('disabled',true);
       }else{
           $('.addExp').prop('disabled',false);
       }
   });

</script>
<script>
   var ecount2 = {{ count($experience) }};
   var rowMin2 = ecount2 + 1;
   var rowMax2 = 5 - ecount2;

   $(document).on('click','.addExp2',function(){
       var html2 = '';
       html2 += '<div class="row"><div class="col-md-6"><div class="mb-3">';
       html2 += '<label class="form-label" for="edit-exoinyear-ex'+rowMin2+'">Experience (in year) <span class="text-danger">*</span></label>';
       html2 += '<input type="text" name="experience[]" id="edit-exoinyear-ex'+rowMin2+'" class="form-control" placeholder="Enter experience in year...">';
       html2 += '</div></div>';

       html2 += '<div class="col-md-6"><div class="mb-3">';
       html2 += '<label class="form-label" for="edit-job-type-ex'+rowMin2+'">Job Type</label>';
       html2 += '<select name="proff_id[]" id="edit-job-type-ex'+rowMin2+'" class="form-select select2"><option value="">Select</option>';
       html2 += '@foreach ($jobtypes as $jobtype)<option value="{{ $jobtype->id }}">{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>@endforeach';
       html2 += '</select></div></div>';

       html2 += '<div class="col-md-6"><div class="mb-3">';
       html2 += '<label class="form-label" for="edit-expcountry_id-ex'+rowMin2+'">Experience (country name)</label>';
       html2 += '<select name="expcountry_id[]" id="edit-expcountry_id-ex'+rowMin2+'" class="form-select select2">';
       html2 += '<option value="">Select</option>@foreach ($countries as $country)<option value="{{ $country->id }}">{{ $country->name }}</option>@endforeach';
       html2 += '</select></div></div>';

       html2 += '<div class="col-md-6"><div class="mb-3">';
       html2 += '<label for="form-label" for="edit-expcity_id-ex'+rowMin2+'">City</label>';
       html2 += '<select name="expcity_id[]" id="edit-expcity_id-ex'+rowMin2+'" class="form-select select2"><option value="">Select</option>';
       html2 += '@foreach ($expworkcities as $city)<option value="{{ $city->id }}">{{ $city->name }}</option>@endforeach';
       html2 += '</select></div></div>';

      //  html2 += '<div class="col-md-6"><div class="mb-3">';
      //  html2 += '<label for="form-label" for="edit-expcity_id-ex'+rowMin2+'">City</label>';
      //  html2 += '<input type="text" name="expcity_id_text[]" id=edit-expcity_id-ex'+rowMin2+'" class="form-control cityTypehead" placeholder="Type city here..."></div>';
       html2 += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove2 float-end">Remove</button></div>';

       $('#dispEXP2').append(html2);
       rowMin2++;
       rowMax2--;
       if (rowMax2 == 0) {
           $('.addExp2').prop('disabled',true);
       }else{
           $('.addExp2').prop('disabled',false);
       }


   });

   $(document).on('click','.remove2',function(){
       $(this).closest('.row').remove();
       rowMax2++;
       rowMin2--;
       if (rowMax2 == 0) {
           $('.addExp2').prop('disabled',true);
       }else{
           $('.addExp2').prop('disabled',false);
       }
   });


   $(document).on('input',function(){
       var textValue = $(this).val();
       // console.log(textValue);

       var substringMatcher2 = function (strs2) {
           return function findMatches(q2, cb2) {
               var matches2, substrRegex2;
               matches2 = [];
               substrRegex2 = new RegExp(q2, 'i');
               $.each(strs2, function (i, str2) {
                   if (substrRegex2.test(str2)) {
                       matches2.push(str2);
                   }
               });

               cb2(matches2);
           };
       };

       var cities2 = ['Mumbai','Pune'];

       $('.texttest').typeahead(
           {
               hint: true,
               highlight: true,
               minLength: 1
           },
           {
               name: 'cities2',
               source: substringMatcher2(cities2)
           }
       );


   });

</script>
<script>
   $(document).ready(function(){
       $('#publishedSts').on('click',function(){
           var cand_id = {{ $post->id }};

           Swal.fire({
               title: 'Are you sure?',
               text: "to publish candidate!",
               icon: 'success',
               showCancelButton: true,
               confirmButtonText: 'Yes, publish it!',
               customClass: {
                   confirmButton: 'btn btn-primary me-3',
                   cancelButton: 'btn btn-label-secondary'
               },
               buttonsStyling: false
           }).then(function (result) {
               if (result.value) {

                   jQuery.ajax({
                       url:"{{ url('admin/candidate/published/update') }}",
                       method: 'post',
                       type: 'html',
                       data: {
                           "_token": "{{ csrf_token() }}",
                           cand_id: cand_id,

                       },
                       success: function(data){
                           Swal.fire({
                               icon: 'success',
                               title: 'Published!',
                               text: 'Your file has been published.',
                               customClass: {
                                   confirmButton: 'btn btn-success'
                               }
                           });
                       }
                   });
               }
           });
       });
   });
</script>
<script>
   $(document).ready(function(){
       $(document).on('click','.delcandamt',function(e){
           var paycand_id2 = $(this).data('id');

           // alert(paycand_id2);

           Swal.fire({
               title: 'Are you sure?',
               text: 'To delete payment detail!',
               icon: 'success',
               showCancelButton: true,
               confirmButtonText: 'Yes, delete it!',
               customClass: {
                   confirmButton: 'btn btn-primary me-3',
                   cancelButton: 'btn btn-label-secondary'
               },
               buttonsStyling: false
           }).then(function(result){
               if (result.value) {
                  jQuery.ajax({
                     url:"{{ url('admin/candidate/candamt/delete') }}",
                     method: 'post',
                     type: 'html',
                     data: {
                        "_token": "{{ csrf_token() }}",
                        id: paycand_id2,
                     },
                     success: function(data){
                        Swal.fire({
                           icon: 'success',
                           title: 'Delete!',
                           text: 'Your payment data has been deleted.',
                           customClass: {
                              confirmButton: 'btn btn-success'
                           }
                        }).then(function(result){
                           //  location.reload();
                           $('#nav-service-charge-div').load(" #nav-service-charge-div");
                           $('#nav-cand-payment-div').load(" #nav-cand-payment-div");
                        });
                     }
                  });
               }
            });
         });
      });
</script>
<script>
   $(document).ready(function(){
      //  $('.servdelcandamt').on('click',function(e){
      //      var cand_id2 = $(this).data('id');

      //      // alert(paycand_id2);

      //      Swal.fire({
      //          title: 'Are you sure?',
      //          text: 'To delete Service detail!',
      //          icon: 'success',
      //          showCancelButton: true,
      //          confirmButtonText: 'Yes, delete it!',
      //          customClass: {
      //              confirmButton: 'btn btn-primary me-3',
      //              cancelButton: 'btn btn-label-secondary'
      //          },
      //          buttonsStyling: false
      //      }).then(function(result){
      //          if (result.value) {
      //              jQuery.ajax({
      //                  url:"{{ url('admin/candidate/service/delete') }}",
      //                  method: 'post',
      //                  type: 'html',
      //                  data: {
      //                      "_token": "{{ csrf_token() }}",
      //                      id: cand_id2,

      //                  },
      //                  success: function(data){
      //                      Swal.fire({
      //                          icon: 'success',
      //                          title: 'Delete!',
      //                          text: 'Your file has been deleted.',
      //                          customClass: {
      //                              confirmButton: 'btn btn-success'
      //                          }
      //                      }).then(function(result){
      //                          location.reload();
      //                      });
      //                  }
      //              });
      //          }
      //      });
      //  });
         // Delete Service Charge
       $(document).on('click','.servdelcandamt',function(e){
           var cand_id2 = $(this).data('id');

           // alert(paycand_id2);

           Swal.fire({
               title: 'Are you sure?',
               text: 'To delete Service detail!',
               icon: 'success',
               showCancelButton: true,
               confirmButtonText: 'Yes, delete it!',
               customClass: {
                   confirmButton: 'btn btn-primary me-3',
                   cancelButton: 'btn btn-label-secondary'
               },
               buttonsStyling: false
           }).then(function(result){
               if (result.value) {
                   jQuery.ajax({
                       url:"{{ url('admin/candidate/service/delete') }}",
                       method: 'post',
                       type: 'html',
                       data: {
                           "_token": "{{ csrf_token() }}",
                           id: cand_id2,

                       },
                       success: function(data){
                           Swal.fire({
                               icon: 'success',
                               title: 'Delete!',
                               text: 'Your service charge has been deleted.',
                               customClass: {
                                   confirmButton: 'btn btn-success'
                               }
                           }).then(function(result){
                              //  location.reload();
                              $('#nav-service-charge-div').load(" #nav-service-charge-div");
                              $('#nav-cand-payment-div').load(" #nav-cand-payment-div");
                           });
                       }
                   });
               }
           });
       });

      //  Deactibe Service Charge Status
      $(document).on('click','.deactiveStatus',function(e){
           var cand_id2 = $(this).data('id');

           // alert(paycand_id2);

           Swal.fire({
               title: 'Are you sure?',
               text: 'To deactive Service charge!',
               icon: 'success',
               showCancelButton: true,
               confirmButtonText: 'Yes, deactive it!',
               customClass: {
                   confirmButton: 'btn btn-primary me-3',
                   cancelButton: 'btn btn-label-secondary'
               },
               buttonsStyling: false
           }).then(function(result){
               if (result.value) {
                   jQuery.ajax({
                       url:"{{ url('admin/candidate/servicecharge/deactive') }}",
                       method: 'post',
                       type: 'html',
                       data: {
                           "_token": "{{ csrf_token() }}",
                           id: cand_id2,

                       },
                       success: function(data){
                           Swal.fire({
                               icon: 'success',
                               title: 'Deactive!',
                               text: 'Service charge has been deactivated',
                               customClass: {
                                   confirmButton: 'btn btn-success'
                               }
                           }).then(function(result){
                              //  location.reload();
                              $('#nav-service-charge-div').load(" #nav-service-charge-div");
                              $('#nav-cand-payment-div').load(" #nav-cand-payment-div");
                           });
                       }
                   });
               }
           });
      });
      // Active Service Charge Status
      $(document).on('click','.activeStatus',function(e){
         var cand_id2 = $(this).data('id');

         // alert(paycand_id2);

         Swal.fire({
            title: 'Are you sure?',
            text: 'To active Service charge!',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Yes, active it!',
            customClass: {
                  confirmButton: 'btn btn-primary me-3',
                  cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
         }).then(function(result){
            if (result.value) {
                  jQuery.ajax({
                     url:"{{ url('admin/candidate/servicecharge/active') }}",
                     method: 'post',
                     type: 'html',
                     data: {
                        "_token": "{{ csrf_token() }}",
                        id: cand_id2,

                     },
                     success: function(data){
                        Swal.fire({
                              icon: 'success',
                              title: 'Active!',
                              text: 'Service charge has been activated',
                              customClass: {
                                 confirmButton: 'btn btn-success'
                              }
                        }).then(function(result){
                           //  location.reload();
                           $('#nav-service-charge-div').load(" #nav-service-charge-div");
                           $('#nav-cand-payment-div').load(" #nav-cand-payment-div");
                        });
                     }
                  });
            }
         });
      });

   });
</script>
{{--Start Temporary Remove 10072023 --}}
{{-- <script>
   $(document).ready(function(){
       $('#downloadCandCV2').on('click',function(){
           var partnerID = $('#partner_id').val();
           var candcv_id = {{ $post->id }};

           if (partnerID != '') {
               var cvUrl = '{{ url("admin/download/candidate-cv") }}/'+partnerID+'/'+candcv_id;
               window.open(cvUrl,'_blank');
               $('#errorPartner').text('');
               $('#downloadCandCV2').attr('disabled',false);
           }else{
               $('#errorPartner').text('Please select office name!');
               $('#downloadCandCV2').attr('disabled',true);
           }
       });

       $('#partner_id').on('change',function(){
           var pID = $(this).val();
           if (pID != '') {
               $('#errorPartner').text('');
               $('#downloadCandCV2').attr('disabled',false);
           } else {
               $('#errorPartner').text('Please select office name!');
               $('#downloadCandCV2').attr('disabled',true);
           }
       });
   });
</script> --}}
{{--End Temporary Remove 10072023 --}}
<script>
   $(document).ready(function(){
       $('#cvexecuteerro').on('click',function(){
           toastr.options = {
               "timeOut": 5000,
               "showDuration": 300,
               "showEasing": "swing",
               "hideEasing": "linear",
               "showMethod": "fadeIn",
               "hideMethod": "fadeOut",
           };

           toastr.error('Cv is not execute due incomplete candidate details!');

       });
   });
</script>
<script>
   $(document).ready(function(){
       $('#cvexecuteerro2').on('click',function(){
           toastr.options = {
               "timeOut": 5000,
               "showDuration": 300,
               "showEasing": "swing",
               "hideEasing": "linear",
               "showMethod": "fadeIn",
               "hideMethod": "fadeOut",
           };

           toastr.error('Cv is not execute due incomplete candidate details!');
       });
   });
</script>
<script>
   $(document).on('click','.errorDownload1',function(){
      toastr.options = {
               "timeOut": 5000,
               "showDuration": 300,
               "showEasing": "swing",
               "hideEasing": "linear",
               "showMethod": "fadeIn",
               "hideMethod": "fadeOut",
           };

           toastr.error('Cv is not execute!');
   });
   // $(document).ready(function(){
   //     $('.errorDownload1').on('click',function(){
   //         toastr.options = {
   //             "timeOut": 5000,
   //             "showDuration": 300,
   //             "showEasing": "swing",
   //             "hideEasing": "linear",
   //             "showMethod": "fadeIn",
   //             "hideMethod": "fadeOut",
   //         };

   //         toastr.error('Cv is not execute!');

   //     });
   // });
</script>
<script>
   $(document).ready(function(){
      $(document).on('click','.errorDownload2',function(){
         toastr.options = {
               "timeOut": 5000,
               "showDuration": 300,
               "showEasing": "swing",
               "hideEasing": "linear",
               "showMethod": "fadeIn",
               "hideMethod": "fadeOut",
           };

           toastr.error('Cv is not execute!');
      });
      //  $('.errorDownload2').on('click',function(){
      //      toastr.options = {
      //          "timeOut": 5000,
      //          "showDuration": 300,
      //          "showEasing": "swing",
      //          "hideEasing": "linear",
      //          "showMethod": "fadeIn",
      //          "hideMethod": "fadeOut",
      //      };

      //      toastr.error('Cv is not execute!');

      //  });

       $(document).on('click','.partnerExcuteCVBtnError',function(){
         toastr.options = {
               "timeOut": 5000,
               "showDuration": 300,
               "showEasing": "swing",
               "hideEasing": "linear",
               "showMethod": "fadeIn",
               "hideMethod": "fadeOut",
           };

           toastr.error('Due to incomplete Cv, not execute!');
       });

      //  $('.partnerExcuteCVBtnError').on('click',function(){
      //      toastr.options = {
      //          "timeOut": 5000,
      //          "showDuration": 300,
      //          "showEasing": "swing",
      //          "hideEasing": "linear",
      //          "showMethod": "fadeIn",
      //          "hideMethod": "fadeOut",
      //      };

      //      toastr.error('Due to incomplete Cv, not execute!');

      //  });



       $(document).on('click','#paymentError',function(){
           toastr.options = {
               "timeOut": 5000,
               "showDuration": 300,
               "showEasing": "swing",
               "hideEasing": "linear",
               "showMethod": "fadeIn",
               "hideMethod": "fadeOut",
           };

           toastr.error('Service charge is not updated!');
       });

   });
</script>
<script>
   $(document).ready(function(){
       $('#editCandPayment').on('show.bs.offcanvas',function(e){
           var paycand_id = $(e.relatedTarget).data('id');

           var imgPath = "{{ asset('admin/assets/images/payment/candidate') }}";
           var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";

           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });

           jQuery.ajax({
               url : '{{ url("admin/candidate/addpayment/edit") }}',
               method: "POST",
               type: "html",
               data: {
                   "id": paycand_id,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   $('#paycand_id').val(data.id);
                   $('#edit-amount-cand').val(data.amount);
                   $('#edit-payment-mode').val(data.payment_mode).change();
                   $('#edit-txn-id').val(data.txn_id);
                   $('#edit-bank-to').val(data.bank_to).change();

                   if (data.payment_slip != '') {
                       var file_path = imgPath+'/'+data.payment_slip;
                       $('.uploadedAvatar2').attr("src",file_path);
                   } else {
                       // var file_path = blankImg+'/blank.jpeg';
                       $('.uploadedAvatar2').attr("src",blankImg);
                   }

                   // var html = "";
                   // if (data.payment_slip != '') {
                   //     var file_path = imgPath+'/'+data.payment_slip;
                   //     html += '<img src="'+file_path+'" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2">';
                   // } else {
                   //     // var file_path = blankImg+'/blank.jpeg';
                   //     html += '<img src="'+blankImg+'" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2">';
                   // }

                   // $('#dispIMG').html(html);
               }
           });
       });
   });

</script>

   <script>
      $(document).ready(function(){
         toastr.options = {
            "timeOut": 5000,
            "showDuration": 300,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
         };

         // Deactive Cand Serchare
         $('#deactiveStatusModal').on("show.bs.modal",function(e){
            var cand_ser_id = $(e.relatedTarget).data('id');

            $('#candser_id').val(cand_ser_id);

         });

         // Validate Form and then submit
         $('#deactiveStatusModalForm').validate({
            rules:{
               approval_notes:{
                  required: true
               }
            },
            messages:{
               approval_notes:{
                  required: "Please enter notes for reaseon"
               }
            },

         });

         $('#deactbtnsubm').click(function(e){
            e.preventDefault();
            $("#deactiveStatusModalForm").valid();
            var formdata = $('#deactiveStatusModalForm').serialize();
            $.ajax({
               type: "POST",
               url:"{{ url('admin/candidate/servicecharge/deactive') }}",
               data: formdata,
               success: function(response){
                  // Show Success Message
                  toastr.success(response.success);
                  // Hide Modal
                  $("#deactiveStatusModalForm")[0].reset();
                  $('#deactiveStatusModal').modal('hide');


                  // Load Section
                  $('#nav-service-charge-div').load(" #nav-service-charge-div");
                  $('#nav-cand-payment-div').load(" #nav-cand-payment-div");
               }
            });
         });

         // Active Cand Serchare
         $('#activeStatusModal').on("show.bs.modal",function(e){
            var cand_ser_id2 = $(e.relatedTarget).data('id');

            $('#candser_id2').val(cand_ser_id2);

         });

         // Validate Form and then submit
         $('#activeStatusModalForm').validate({
            rules:{
               approval_notes:{
                  required: true
               }
            },
            messages:{
               approval_notes:{
                  required: "Please enter notes for reaseon"
               }
            },

         });

         $('#actbtnsubm').click(function(e){
            e.preventDefault();
            $("#activeStatusModalForm").valid();
            var formdata = $('#activeStatusModalForm').serialize();
            $.ajax({
               type: "POST",
               url:"{{ url('admin/candidate/servicecharge/active') }}",
               data: formdata,
               success: function(response){
                  // Show Success Message
                  toastr.success(response.success);
                  // Hide Modal
                  $("#activeStatusModalForm")[0].reset();
                  $('#activeStatusModal').modal('hide');


                  // Load Section
                  $('#nav-service-charge-div').load(" #nav-service-charge-div");
                  $('#nav-cand-payment-div').load(" #nav-cand-payment-div");
               }
            });
         });
      });
   </script>

<script>
   $(document).ready(function(){
       $('#UpdateServiceCharge').on('show.bs.offcanvas',function(e){
           var cand_id = $(e.relatedTarget).data('id');

           $.ajaxSetup({
               headers:{
                   'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
               }
           });

           jQuery.ajax({
               url : '{{ url("admin/candidate/service/edit") }}',
               method: "POST",
               type: "html",
               data: {
                   "id": cand_id,
                   "_token": "{{ csrf_token() }}",
               },
               success: function(data){
                   $('#cand_id').val(data.id);
                   $('#edit-given_by').val(data.given_by);
                   $('#edit-amount').val(data.amount).change();
                   $('#edit-status').val(data.status).change();
                  $('#edit-notes').val(data.notes);

                   // var html = "";
                   // if (data.payment_slip != '') {
                   //     var file_path = imgPath+'/'+data.payment_slip;
                   //     html += '<img src="'+file_path+'" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2">';
                   // } else {
                   //     // var file_path = blankImg+'/blank.jpeg';
                   //     html += '<img src="'+blankImg+'" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2">';
                   // }

                   // $('#dispIMG').html(html);
               }
           });
       });
   });

</script>
{{-- <script>
   $(document).ready(function(){
       $('#embassy').on('change',function(){
           var statusval = $(this).val();
           if (statusval == 1) {
               $('#musDisp').show();
               $('.musCopyDisp').show();
           } else {
               $('#musDisp').hide();
               $('.musCopyDisp').hide();
           }
       });
   });
</script> --}}
<script>
   // Activate Bootstrap Tabs
   var linkTabs = new bootstrap.Tab(document.getElementById('link-tab'));
   linkTabs.show();

   var testVideoTabs = new bootstrap.Tab(document.getElementById('test_video-tab'));
   testVideoTabs.show();

   // Switch tabs on click
   $('#linkTabs a').on('click', function (e) {
       e.preventDefault();
       $(this).tab('show');
   });

   $('#testVideoTabs a').on('click', function (e) {
       e.preventDefault();
       $(this).tab('show');
   });
</script>
<!--   <script>
   // Get the time the button was displayed from the server
   var displayTime = new Date("{{ session('button_display_time') }}").getTime();

   // Calculate the time difference
   var currentTime = new Date().getTime();
   var timeDifference = currentTime - displayTime;

   // Set the timeout for 10 minutes
   var timeout = 10 * 60 * 1000;

   // Hide the button if 10 minutes have passed
   if (timeDifference > timeout) {
       document.getElementById('UpdateDeactive').style.display = 'none';
   }
   </script> -->

   <script>
      jQuery.validator.setDefaults({
        errorElement: "span",
         errorPlacement: function(error, element){
            if (element.parent().hasClass('input-group') || element.hasClass('select2') || element.attr('type') == 'checkbox') {
               error.insertAfter(element.parent());

            }else{
               error.insertAfter(element);
            }

            if (element.parent().hasClass('input-group')) {
               element.parent().addClass('is-invalid');
            }

         },
         highlight: function ( element, errorClass, validClass ) {
				$( element ).parents( ".mb-3" ).addClass( "is-invalid" ).removeClass( "is-valid" );
         },
         unhighlight: function (element, errorClass, validClass) {
            $( element ).parents( ".mb-3" ).addClass( "is-valid" ).removeClass( "is-invalid" );
			}
    });
   </script>

<script>
    $(document).ready(function(){
        $('#closedButton').on('click',function(){
            window.close();
        });
    });
</script>
@endsection
