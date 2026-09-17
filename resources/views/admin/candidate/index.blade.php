@extends('layout.admin.admin_layout')


@section('title','Candidate')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('user/intl-tel-input-master/build/css/intlTelInput.css') }}">



    <style>

        /* styles.css */
        #progressWrapper {
            width: 100%;
            background-color: #f3f3f3;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        #progressBar {
            width: 0%;
            height: 20px;
            background-color: #4caf50;
            text-align: center;
            line-height: 20px;
            color: white;
        }
        .filter-indicator {
            position: absolute;
            top: 1px;
            right: 2px;
            width: 8px;
            height: 8px;
            background: #ffc107;
            border-radius: 50%;
        }

        .datatables-users th {
            text-transform: none !important;
        }

        .btn:not([class*=btn-label-]):not([class*=btn-outline-]) {
            box-shadow: none;
        }

        .updateworkcity {
            color: inherit;
        }
        .updateworkcity:hover {
            color: #7367f0;
        }
    </style>

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($perm) && $perm->add_candidate == 0)
        <style>
        .addcandidate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->edit_candidate == 0)
        <style>
        .edcandidate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->delete_candidate == 0)
        <style>
        .delcandidate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->publish_candidate == 0)
            <style>
                .pubcandidate{
                    display: none;
                }
            </style>
        @endif
    @endif

@endsection

@section('content')

        <!-- Status Bar Start -->
        <div class="container-fluid flex-grow-1 hideshowaddmodule" style="margin-top: 10px;" id="hideshowaddmodule" @if(isset($Candidateadminsavefilter) && $Candidateadminsavefilter->short_form_code == 1) @else style="display: none" @endif>
            <div class="card py-3 px-4">
                <div class="row">
                @php
                    $statusOrder = [
                        'New Candidate',
                        'Candidate Are Ready',
                        'Published For Selection',
                        'Selected',
                        'Visa Received',
                        'Passport In Embassy',
                        'Visa Stamped',
                        'Applied For Emigration',
                        'Emigration Approved',
                        'Waiting For Flight Ticket',
                        'Ticket Confirmed',
                        'Deployed',
                        'Done',
                        'Hold',
                        'Cancelled',
                        'Visa Cancelled',
                    ];
               
                    $borderColors = [
                        '#7367f0',
                        '#28c76f',
                        '#00bad1',
                        '#ff9f43',
                    ];
                @endphp


                @foreach ($statusOrder as $status)
                    @if(isset($statusCounts[$status]))
                        @php
                            $color = $borderColors[$loop->index % count($borderColors)];
                        @endphp

                        <div class="col-sm-3 col-xl-2 mb-2">
                            <a href="#"
                            class="btn btn-xs w-100 status-filter-card"
                            data-status="{{ $status }}"
                            data-color="{{ $color }}"
                            style="border: 1px solid {{ $color }}; color: {{ $color }};">
                                {{ $status }} &nbsp;&nbsp;
                                <b style="font-size: 1.3em;">{{ $statusCounts[$status] }}</b>
                            </a>
                        </div>
                    @endif
                @endforeach

                </div>
            </div>
        </div>
        <!-- Status Bar End -->
        <div class="container-fluid flex-grow-1 container-p-y">
            <!-- Users List Table -->
            <div class="card">
                <div class="card-header py-3 px-4">
                    <div class="float-start">
                        <select id="pagination_list" class="form-select form-select-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                            <option value="1000">1000</option>
                        </select>
                    </div>
                    <div class="px-3 float-start">
                        <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel"><i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter <span class="filter-indicator d-none"></span></button>
                    </div>
                    <!-- Option Button -->
                    <div class="btn-group mx-1">
                        <button class="btn btn-primary btn-xs dropdown-toggle optionBtnCustom" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i style="height:18px;"></i> Option
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" name="short_form_code" value="1" id="short_form_code" class="switch-input option-switch-input" @if(isset($Candidateadminsavefilter) && $Candidateadminsavefilter->short_form_code == 1) checked @endif />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Status Bar</span>
                                </label>
                            </div>

                        </div>
                    </div>
                    <div class="float-end">
                        <button class="add-new btn btn-sm btn-primary addcandidate mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New Candidate</span></button>
                        <button class="add-new btn btn-sm btn-primary bulkpublish mx-1" data-bs-toggle="modal" data-bs-target="#publish"><i class="ti ti-cloud-upload me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Publish</span></button>
                        <button class="add-new btn btn-sm btn-primary bulksharedcv mx-1" data-bs-toggle="modal" data-bs-target="#sharedCV"><i class="ti ti-download me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Shared CV</span></button>
                    </div>
                    <div class="float-end">
                        <input type="text" id="search_text" class="form-control form-control-sm" placeholder="Search...">
                    </div>
                </div>
                <div class="card-datatable table-responsive candloadpaginate">
                    @include('admin.candidate.candload')
                </div>
            </div>
            <!-- View Model Candidate Details-->
            <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="exampleModalToggleLabel">New Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start align-items-sm-center gap-4">
                                                    <div>
                                                        <img src="https://crm.qamarhire.com/admin/assets/images/candidate/19.jpg" alt="Avatar" class="d-block w-px-100 h-px-100 rounded" data-bs-toggle="modal" data-bs-target="#imageUpload">
                                                    </div>
                                                    <div>
                                                        <h6 class="offcanvas-title">Candidate Name</h6>
                                                        <span class="form-label">Ramu</span>
                                                        <h6 class="offcanvas-title">CAROFF</h6>
                                                        <span class="form-label">RUQAIYA</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="offcanvas-title">Candidate Name</h6>
                                                <span class="form-label">Ramu</span>
                                                <h6 class="offcanvas-title">CAROFF</h6>
                                                <span class="form-label">RUQAIYA</span>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="offcanvas-title">Candidate Name</h6>
                                                <span class="form-label">Ramu</span>
                                                <h6 class="offcanvas-title">CAROFF</h6>
                                                <span class="form-label">RUQAIYA</span>
                                            </div>
                                        </div>
                                        <!-- Secend Row-->
                                        <div class="row mt-2">
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start align-items-sm-center gap-4">

                                                        <h6 class="offcanvas-title">Candidate Name</h6>
                                                        <span class="form-label">Ramu</span>
                                                        <h6 class="offcanvas-title">CAROFF</h6>
                                                        <span class="form-label">RUQAIYA</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="offcanvas-title">Candidate Name</h6>
                                                <span class="form-label">Ramu</span>
                                                <h6 class="offcanvas-title">CAROFF</h6>
                                                <span class="form-label">RUQAIYA</span>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="offcanvas-title">Candidate Name</h6>
                                                <span class="form-label">Ramu</span>
                                                <h6 class="offcanvas-title">CAROFF</h6>
                                                <span class="form-label">RUQAIYA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button class="btn btn-primary" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">Save and Next</button>
                    </div>
                </div>
                </div>
            </div>
            <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalToggleLabel2">Modal 2</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Hide this modal and show the first with the button below.
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">Back to first</button>
                    </div>
                    </div>
                </div>
            </div>
            <!--- Add Candidate Start --->
            <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="offcanvas-header">
                    <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Candidate</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                    <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.candidate.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add-associate-id" class="form-label">Associate <span class="text-danger">*</span></label>
                                    <select name="associate_id" class="form-select select2" id="add-associate-id" data-allow-clear="true">
                                        <option value="">Select</option>
                                        <option value="73">Direct Candidate</option>
                                        @foreach ($associates as $associate)
                                            <option value="{{ $associate->id }}">{{ $associate->pty_full_name.' ('.$associate->pty_ag_name.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-care-off">Careoff <span class="text-danger">*</span></label>
                                    <select name="careoff_id" id="add-care-off" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($careoffs as $careoff)
                                            <option value="{{ $careoff->id }}" @if(Auth::guard('admin')->user()->id == $careoff->id) selected @endif>{{ $careoff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-cand-name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="cand_name" id="add-cand-name" class="form-control" placeholder="Enter candidate name...">
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-cand-arname">Arabic Name <span class="text-danger">*</span></label>
                                    <input type="text" name="arcand_name" id="add-cand-arname" class="form-control" placeholder="Enter candidate name...">
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-pass-no">Passport No <span class="text-danger">*</span></label>
                                    <input type="text" name="pass_no" id="add-pass-no" class="form-control" oninput="check_pass(this)" placeholder="Enter candidate name...">
                                    <small id="errorPassportNo" class="text-danger"></small>
                                    <div class="demo-inline-spacing" id="duplipass" style="display: none">
                                        <div class="form-check form-check-inline mt-3">
                                            <input type="checkbox" class="form-check-input" value="1" id="repeatPass">
                                            <label class="form-check-label" for="repeatPass">Proceed</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-pass-type">Passport Type <span class="text-danger">*</span></label>
                                    <select name="pass_type" id="add-pass-type" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        <option value="ECNR">ECNR</option>
                                        <option value="ECR">ECR</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-dob">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="dob" id="add-dob" class="form-control" placeholder="Enter date of birth...">
                                    {{-- <input type="text" name="dob" id="add-dob" class="form-control flatpickr-basic" placeholder="Enter date of birth..."> --}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-doi">Date of Issue <span class="text-danger">*</span></label>
                                    <input type="date" name="doi" id="add-doi" class="form-control" placeholder="Enter date of issue...">
                                    {{-- <input type="text" name="doi" id="add-doi" class="form-control flatpickr-basic" placeholder="Enter date of issue..."> --}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-doe">Date of Expiry <span class="text-danger">*</span></label>
                                    <input type="date" name="doe" id="add-doe" class="form-control" placeholder="Enter date of expiry...">
                                    {{-- <input type="text" name="doe" id="add-doe" class="form-control flatpickr-basic" placeholder="Enter date of expiry..."> --}}
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add-sourcing-date" class="form-label">Sourcing Date</label>
                                    <input type="text" name="sourcing_date" id="add-sourcing-date" class="form-control flatpickr-date" placeholder="Enter Sourcing Date...">
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-place-of-issue">Place of Issue</label>
                                    <select name="poi" id="add-place-of-issue" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($pois as $poi)
                                            <option value="{{ $poi->id }}">{{ $poi->name }}</option>
                                        @endforeach
                                    </select>
                                    {{-- <input type="text" name="poi_text" id="add-place-of-issue" class="form-control cityTypehead" placeholder="Type city here..."> --}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-place-of-birth">Place of Birth</label>
                                    {{-- <select name="plb_id" id="add-place-of-birth" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($cities as $cityplb)
                                            <option value="{{ $cityplb->id }}">{{ $cityplb->name }}</option>
                                        @endforeach
                                    </select> --}}
                                    <input type="text" name="plb_text" id="add-place-of-birth" class="form-control cityTypehead" placeholder="Type city here...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-nationality-id">Nationality</label>
                                    <select name="nation_id" id="add-nationality-id" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($countries as $nationid)
                                            <option value="{{ $nationid->id }}">{{ $nationid->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-contact-number">Contact Number</label>
                                    <input type="text" name="contact_no" id="add-contact-number" class="form-control" placeholder="Enter contact number...">
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-mob-number">Mobile Number</label>
                                    <input type="text" name="mobile_no" id="add-mob-number" class="form-control add-mob-number" placeholder="Enter mobile number...">
                                    <input type="hidden" name="mobile_no_dial_code" id="mobile-no-dial-code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-marital-status">Marital Status <span class="text-danger">*</span></label>
                                    <select name="marital_status" id="add-marital-status" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        <option value="Unmarried">Unmarried</option>
                                        <option value="Married">Married</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="form-label" for="add-occupation">Applied for, Occupation</label>
                                    <select name="jobtype_id" id="add-occupation" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($jobtypes as $jobtype)
                                            <option value="{{ $jobtype->id }}">{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-address">Address</label>
                                    <input type="text" class="form-control" id="add-address" name="address" placeholder="Enter address...">
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-upload-photo">Upload Photo</label>
                                    <input type="file" name="photo_file" id="add-upload-photo" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-upload-pp">Upload Passport</label>
                                    <input type="file" name="pass_file" id="add-upload-pp" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-upload-lic">Upload License</label>
                                    <input type="file" name="lic_file" id="add-upload-lic" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="add-upload-cv">Upload Full Size (420x150)</label>
                                    <input type="file" name="cv_file" id="add-upload-cv" class="form-control">
                                </div>
                            </div>
                        </div> --}}
                        <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit" id="addSubmBtn">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                    </form>
                </div>
            </div>
            <!--- Add Candidate End --->

            <!--- Edit Candidate Start --->
            <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="offcanvas-header">
                    <h5 id="edituserLabel" class="offcanvas-title">Edit Candidate</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                    <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.candidate.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="editID" id="edit_ID">
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-associate-id" class="form-label">Associate</label>
                                    <select name="associate_id" class="form-select select2" id="edit-associate-id" data-allow-clear="true">
                                        <option value="">Select</option>
                                        <option value="7">Direct Candidate</option>
                                        @foreach ($associates as $associate2)
                                            <option value="{{ $associate2->id }}">{{ $associate2->pty_full_name.' ('.$associate2->pty_ag_name.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-care-off">Careoff</label>
                                    <select name="careoff_id" id="edit-care-off" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($careoffs as $careoff2)
                                            <option value="{{ $careoff2->id }}">{{ $careoff2->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-cand-name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="cand_name" id="edit-cand-name" class="form-control" placeholder="Enter candidate name...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-cand-arname">Arabic Name <span class="text-danger">*</span></label>
                                    <input type="text" name="arcand_name" id="edit-cand-arname" class="form-control" placeholder="Enter candidate name...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-pass-no">Pass No <span class="text-danger">*</span></label>
                                    <input type="text" name="pass_no" id="edit-pass-no" class="form-control" placeholder="Enter candidate name...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-pass-type">Passport Type <span class="text-danger">*</span></label>
                                    <select name="pass_type" id="edit-pass-type" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        <option value="ECNR">ECNR</option>
                                        <option value="ECR">ECR</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-dob">Date of Birth <span class="text-danger">*</span></label>
                                    {{-- <input type="text" name="dob" id="edit-dob" class="form-control flatpickr-basic" placeholder="Enter date of birth..."> --}}
                                    <input type="date" name="dob" id="edit-dob" class="form-control" placeholder="Enter date of birth...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-doi">Date of Issue <span class="text-danger">*</span></label>
                                    {{-- <input type="text" name="doi" id="edit-doi" class="form-control flatpickr-basic" placeholder="Enter date of issue..."> --}}
                                    <input type="date" name="doi" id="edit-doi" class="form-control" placeholder="Enter date of issue...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-doe">Date of Expiry <span class="text-danger">*</span></label>
                                    {{-- <input type="text" name="doe" id="edit-doe" class="form-control flatpickr-basic" placeholder="Enter date of expiry..."> --}}
                                    <input type="date" name="doe" id="edit-doe" class="form-control" placeholder="Enter date of expiry...">
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-sourcing-date" class="form-label">Sourcing Date</label>
                                    <input type="text" name="sourcing_date" id="edit-sourcing-date" class="form-control flatpickr-date" placeholder="Enter Sourcing Date...">
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-place-of-issue">Place of Issue</label>
                                    <select name="poi" id="edit-place-of-issue" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($pois as $poi)
                                            <option value="{{ $poi->id }}">{{ $poi->name }}</option>
                                        @endforeach
                                    </select>
                                    {{-- <input type="text" name="poi_text" id="edit-place-of-issue" class="form-control cityTypehead" placeholder="Type city here..."> --}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-place-of-birth">Place of Birth</label>
                                    {{-- <select name="plb_id" id="edit-place-of-birth" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($cities as $cityplb)
                                            <option value="{{ $cityplb->id }}">{{ $cityplb->name }}</option>
                                        @endforeach
                                    </select> --}}
                                    <input type="text" name="plb_text" id="edit-place-of-birth" class="form-control cityTypehead" placeholder="Type city here...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-nationality-id">Nationality</label>
                                    <select name="nation_id" id="edit-nationality-id" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($countries as $nationid)
                                            <option value="{{ $nationid->id }}">{{ $nationid->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-contact-number">Contact Number</label>
                                    <input type="text" name="contact_no" id="edit-contact-number" class="form-control" placeholder="Enter contact number...">
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-mob-number">Mobile Number</label>
                                    <input type="text" name="mobile_no" id="edit-mob-number" class="form-control edit-mob-number" placeholder="Enter mobile number...">
                                    <input type="hidden" name="mobile_no_dial_code" id="mobile-no-dial-code-ed" class="mobile-no-dial-code-ed">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-marital-status">Marital Status <span class="text-danger">*</span></label>
                                    <select name="marital_status" id="edit-marital-status" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        <option value="Unmarried">Unmarried</option>
                                        <option value="Married">Married</option>
                                    </select>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-mob-number">Mobile Number</label>
                                    <input type="text" name="mobile_no" id="edit-mob-number" class="form-control" placeholder="Enter mobile number...">
                                </div>
                            </div> --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="form-label" for="edit-occupation">Applied for, Occupation</label>
                                    <select name="jobtype_id" id="edit-occupation" class="form-select select2" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($jobtypes as $jobtype)
                                            <option value="{{ $jobtype->id }}">{{ $jobtype->eng_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="edit-address">Address</label>
                                    <input type="text" class="form-control" id="edit-address" name="address" placeholder="Enter address...">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                    </form>
                </div>
            </div>
            <!--- Edit Candidate End --->

            <!-- Filter List Start-->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="filter" aria-labelledby="filterLabel">
                <div class="offcanvas-header">
                    <h5 id="filterLabel" class="offcanvas-title">Add Filter</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="all-check">
                                <div class="form-check mt-2" id="pass-type-f">
                                    <input class="form-check-input" type="checkbox" name="pass-typef" value="1" id="pass-typef" @if(isset($filter_user) && $filter_user->pass_type_filter == 1) checked @endif />
                                    <label class="form-check-label" for="pass-typef"> Passport Type</label>
                                </div>
                                <div class="form-check mt-2" id="job-type-f">
                                    <input class="form-check-input" type="checkbox" name="job-typef" value="1" id="job-typef" @if(isset($filter_user) && $filter_user->job_type_filter == 1) checked @endif />
                                    <label class="form-check-label" for="job-typef"> Occupation</label>
                                </div>
                                <div class="form-check mt-2" id="careoff-f">
                                    <input class="form-check-input" type="checkbox" name="careofff" value="1" id="careofff" @if(isset($filter_user) && $filter_user->careoff_filter == 1) checked @endif />
                                    <label class="form-check-label" for="careofff"> Careoff</label>
                                </div>
                                <div class="form-check mt-2" id="create-by-f">
                                    <input class="form-check-input" type="checkbox" name="create-byf" value="1" id="create-byf" @if(isset($filter_user) && $filter_user->create_by_filter == 1) checked @endif />
                                    <label class="form-check-label" for="create-byf"> Create By</label>
                                </div>
                                <div class="form-check mt-2" id="create-date-f">
                                    <input class="form-check-input" type="checkbox" name="create-datef" value="1" id="create-datef" @if(isset($filter_user) && $filter_user->create_date_filter == 1) checked @endif />
                                    <label class="form-check-label" for="create-datef"> Create Date</label>
                                </div>

                                <div class="form-check mt-2" id="sourcing-date-range-f">
                                    <input class="form-check-input" type="checkbox" name="sourcing-date-rangef" value="1" id="sourcing-date-rangef" @if(isset($filter_user) && $filter_user->sourcing_date_filter == 1) checked @endif />
                                    <label class="form-check-label" for="sourcing-date-rangef"> Sourcing Date</label>
                                </div>

                                <div class="form-check mt-2" id="religion-f">
                                    <input class="form-check-input" type="checkbox" name="religionf" value="1" id="religionf" @if(isset($filter_user) && $filter_user->religion_filter == 1) checked @endif />
                                    <label class="form-check-label" for="religionf"> Religion</label>
                                </div>
                                <div class="form-check mt-2" id="region-f">
                                    <input class="form-check-input" type="checkbox" name="regionf" value="1" id="regionf" @if(isset($filter_user) && $filter_user->region_filter == 1) checked @endif />
                                    <label class="form-check-label" for="regionf"> Region</label>
                                </div>
                                <div class="form-check mt-2" id="city-f">
                                    <input class="form-check-input" type="checkbox" name="cityf" value="1" id="cityf" @if(isset($filter_user) && $filter_user->city_filter == 1) checked @endif />
                                    <label class="form-check-label" for="cityf"> City</label>
                                </div>
                                <div class="form-check mt-2" id="experience-region-f">
                                    <input class="form-check-input" type="checkbox" name="experience-regionf" value="1" id="experience-regionf" @if(isset($filter_user) && $filter_user->experience_region_filter == 1) checked @endif />
                                    <label class="form-check-label" for="experience-regionf"> Experience Region</label>
                                </div>
                                <div class="form-check mt-2" id="medical-expiry-date-f">
                                    <input class="form-check-input" type="checkbox" name="medical-expiry-datef" value="1" id="medical-expiry-datef" @if(isset($filter_user) && $filter_user->medical_expiry_date_filter == 1) checked @endif />
                                    <label class="form-check-label" for="medical-expiry-datef"> Medical Expiry Date</label>
                                </div>

                                <div class="form-check mt-2" id="candidate-status-f">
                                    <input class="form-check-input" type="checkbox" name="candidate-statusf" value="1" id="candidate-statusf" @if(isset($filter_user) && $filter_user->candidate_status_filter == 1) checked @endif />
                                    <label class="form-check-label" for="candidate-statusf"> Candidate Status</label>
                                </div>
                                <div class="form-check mt-2" id="publish-status-f">
                                    <input class="form-check-input" type="checkbox" name="publish-statusf" value="1" id="publish-statusf" @if(isset($filter_user) && $filter_user->publish_status_filter == 1) checked @endif />
                                    <label class="form-check-label" for="publish-statusf"> Publish Status</label>
                                </div>
                                <div class="form-check mt-2" id="new-candidate-status-f">
                                    <input class="form-check-input" type="checkbox" name="new-candidate-statusf" value="1" id="new-candidate-statusf" @if(isset($filter_user) && $filter_user->new_candidate_status_filter == 1) checked @endif />
                                    <label class="form-check-label" for="new-candidate-statusf"> New Candidate</label>
                                </div>
                                <div class="form-check mt-2" id="ready-for-published-status-f">
                                    <input class="form-check-input" type="checkbox" name="ready-for-published-statusf" value="1" id="ready-for-published-statusf" @if(isset($filter_user) && $filter_user->ready_for_published_status_filter == 1) checked @endif />
                                    <label class="form-check-label" for="ready-for-published-statusf"> Ready for Published</label>
                                </div>
                                <div class="form-check mt-2" id="cand-medical-status-f">
                                    <input class="form-check-input" type="checkbox" name="cand-medical-statusf" value="1" id="cand-medical-statusf" @if(isset($filter_user) && $filter_user->medical_health_status_filter == 1) checked @endif />
                                    <label class="form-check-label" for="cand-medical-statusf"> Medical Status</label>
                                </div>
                                <div class="form-check mt-2" id="cand-payment-status-f">
                                    <input class="form-check-input" type="checkbox" name="cand-payment-statusf" value="1" id="cand-payment-statusf" @if(isset($filter_user) && $filter_user->cand_payment_status_filter == 1) checked @endif />
                                    <label class="form-check-label" for="cand-payment-statusf"> Payment Status</label>
                                </div>
                                <div class="mt-3">
                                    <a href="#" id="all-chk"><span class="badge bg-label-primary">Select all</span></a>
                                    <a href="#" id="all-unchk"><span class="badge bg-label-primary">Unselect all</span></a>
                                    <a href="#" id="default-chk"><span class="badge bg-label-primary">Basic</span></a>
                                    <a href="#" id="update-chk"><span class="badge bg-label-primary">Update</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filter List End -->

            <!-- Delete Staff start -->
            <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel1">Delete candidate</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.delete') }}" method="POST">
                        @csrf
                        <input type="hidden" name="staff_id" id="delStaffID">
                        <div class="modal-body">
                        <div class="row">
                            <div class="col mb-12">
                            <p>Are you sure!, to delete candidate?</p>
                            </div>
                        </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-danger btn-sm " id="disbtn">Delete</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <!-- Delete Staff end -->
            <!-- Update Work City Start -->
            <div class="modal fade" id="updateWorkCity" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateWorkCityLabel">Update Work City</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.updateWorkCity') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cand_id" id="workCityCandID">
                        <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="workCityWpcityID">City of Work <span class="text-danger">*</span></label>
                                <select name="wpcity_id" id="workCityWpcityID" class="form-select" data-allow-clear="true" data-placeholder="Select City of Work...">
                                    <option value=""></option>
                                    @foreach ($expworklocs as $expwkpc)
                                        <option value="{{ $expwkpc->id }}">{{ $expwkpc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <!-- Update Work City end -->
            <!-- Update Published Status Start -->
            <div class="modal fade" id="publish" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel125">Update Published Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.candidate.bulk.published.update') }}" method="POST" id="updatepublishstatus">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" name="idsv" id="idsv">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input" type="radio" name="publish" id="published" value="1"/>
                                            <label class="form-check-label" for="published">Publish</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="publish" id="unpublished" value="0"/>
                                            <label class="form-check-label" for="unpublished">Unpublish</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p>Total publish is: <span id="countcan"></span></p>
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
            <!-- Shared CV as per company Start -->
            <div class="modal fade" id="sharedCV" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel125">Share CV as per company</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.cvmultisharepartner') }}" method="POST" id="sharedCVvalidation2">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    @php
                                        $partners = DB::table('partners')->where('status','=',1)->get();

                                    @endphp
                                    <div class="col-md-12 mb-3">
                                        <input type="hidden" name="candcvs_id" id="candcvs_id" >
                                        <label for="partner_id2" class="form-label">Office Name <span class="text-danger">*</span></label>
                                        <select name="partner_id" id="partner_id2" class="form-select select2 partner_id2" data-allow-clear="true">
                                            <option value="">Select</option>
                                            @foreach ($partners as $partner)
                                                <option value="{{ $partner->id }}">{{ $partner->rec_off_name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger error-class" id="error-partner"></span>
                                        {{-- <span class="text-danger" id="errorPartner"></span> --}}
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check form-check-inline mt-3">
                                                <input class="form-check-input" type="radio" name="cv_purpose" id="shared_cv_bulk" value="1"/>
                                                <label class="form-check-label" for="shared_cv_bulk">Share CV</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="cv_purpose" id="download_cv_bulk" value="0"/>
                                                <label class="form-check-label" for="download_cv_bulk">Download CV</label>
                                            </div>
                                        </div>
                                        <span class="text-danger error-class" id="error-cvpurpose"></span>

                                    </div>
                                    <div class="col-md-12" id="disShareTo" style="display: none">
                                        <div class="mb-3">
                                            <div class="form-check form-check-inline mt-3">
                                                <input class="form-check-input" type="radio" name="share_to" id="shared_cv_to_staff" value="1"/>
                                                <label class="form-check-label" for="shared_cv_to_staff">To Staff</label>
                                            </div>
                                            <div class="form-check form-check-inline mt-3">
                                                <input class="form-check-input" type="radio" name="share_to" id="share_cv_to_me" value="2">
                                                <label class="form-check-label" for="share_cv_to_me">To Me</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="share_to" id="shared_cv_to_partner" value="0"/>
                                                <label class="form-check-label" for="shared_cv_to_partner">To Partner</label>
                                            </div>
                                        </div>
                                        <span class="text-danger error-class" id="error-shareto"></span>
                                    </div>
                                    <div class="col-md-12" id="disStaffList" style="display: none">
                                        <div class="mb-3">
                                            <label for="staff_id2" class="form-label">Staff <span class="text-danger">*</span></label>
                                            <select name="staff_id2[]" id="staff_id2" class="form-select select2" multiple data-allow-clear="true">
                                                <option value="">Select</option>
                                                @foreach ($careoffs as $careoff2)
                                                    <option value="{{ $careoff2->id }}">{{ $careoff2->name.' ('.$careoff2->phone.')' }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-class" id="error-staff2"></span>

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div id="executeData"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="progress-wrapper" style="display: none">
                                            <div id="progress-label-new">0%</div>
                                            <div class="progress progress-bar-primary">
                                                <div class="progress-bar"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                {{-- <button type="submit" class="btn btn-primary" id="downloadCandCV">Download Cv</button> --}}
                                {{-- <button type="button" class="btn btn-primary btn-sm" id="downloadCvbtn">Download CV</button> 15072023--}}
                                <button type="button" id="shareCVButton" class="btn btn-primary btn-sm shareCVButton">Submit</button>
                                {{-- <button type="submit" class="btn btn-primary btn-sm">Submit</button> --}}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Shared CV as per company End -->
            <!-- Download CV as per company Start -->
            <div class="modal fade" id="downloadcv" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel125">Download CV as per company</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.cvmultiplepartexec') }}" method="POST" id="downloadCVvalidation2">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    @php
                                        $partners = DB::table('partners')->where('status','=',1)->get();
                                    @endphp
                                    <div class="col-md-12 mb-3">
                                        <input type="hidden" name="candcv_id" id="candcv_id" >
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
                                <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                {{-- <button type="submit" class="btn btn-primary" id="downloadCandCV">Download Cv</button> --}}
                                {{-- <button type="button" class="btn btn-primary btn-sm" id="downloadCvbtn">Download CV</button> 15072023--}}
                                <button type="submit" class="btn btn-primary btn-sm">Execute CV</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Update Status Modal Start -->
            <div class="modal fade" id="NewCandStatusUpdate" aria-hidden="true" aria-labelledby="NewCandStatusUpdateLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="offcanvas-title" id="NewCandStatusUpdateLabel">New Candidate</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="cand_id" id="candIDN">
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
                                        <div class="col-md-12 mt-4 candhbtn">
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
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
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
                                            <div class="col-md-12" id="recruitmentPartnerPublishedWrap" style="display: none">
                                                <div class="mb-3">
                                                    <label class="form-label" for="recruitment_partner_published">
                                                        Recruitment Partner
                                                    </label>
                                                    <select class="form-select"
                                                            name="partneroffice_id"
                                                            id="recruitment_partner_published">
                                                        <option value="">Select Recruitment Partner</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                                {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
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
                                            <div class="col-md-12" id="recruitmentPartnerSelectedWrap" style="display: none">
                                                <div class="mb-3">
                                                    <label class="form-label" for="recruitment_partner_selected">
                                                        Recruitment Partner
                                                    </label>
                                                    <select class="form-select"
                                                            name="partneroffice_id"
                                                            id="recruitment_partner_selected">
                                                        <option value="">Select Recruitment Partner</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                                {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Visa Received Status -->
                                    <form action="{{ route('admin.candidate.visareceived') }}" method="POST" id="visareceived" style="display: none">

                                        @csrf

                                        <input type="hidden" name="visaeditid" id="visaeditid">
                                        <input type="hidden" name="fromreq" id="fromreq" value="employerplus">
                                        <input type="hidden" name="proff_id" id="proff_id">
                                     
                                        <div class="row mt-10">

                                            {{-- Visa Received --}}
                                            <div class="col-md-12">
                                                <label for="">Visa Received</label>

                                                <div class="mb-3">
                                                    <div class="form-check form-check-inline mt-3">
                                                        <input class="form-check-input"
                                                            type="radio"
                                                            name="visa_received"
                                                            id="visa_received"
                                                            value="1">

                                                        <label class="form-check-label" for="visa_received">
                                                            Yes
                                                        </label>
                                                    </div>

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input"
                                                            type="radio"
                                                            name="visa_received"
                                                            id="visa_received_not"
                                                            value="0">

                                                        <label class="form-check-label" for="visa_received_not">
                                                            No
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>


                                            {{-- Recruitment Partner --}}
                                            <div class="col-md-4 visaReceivedConditionalField" style="display: none">
                                                <div class="mb-3">
                                                    <label class="form-label" for="recruitment_partner">
                                                        Recruitment Partner <span class="text-danger">*</span>
                                                    </label>

                                                    <select class="form-select"
                                                            name="recruitment_partner"
                                                            id="recruitment_partner">
                                                        <option value="">Select Recruitment Partner</option>
                                                    </select>
                                                </div>
                                            </div>

                                           {{-- Employer --}}
                                            <div class="col-md-4 visaReceivedConditionalField" style="display: none">
                                                <div class="mb-3">
                                                    <label class="form-label" for="employer">
                                                        Employer <span class="text-danger">*</span>
                                                    </label>

                                                    <select class="form-select"
                                                            name="employer"
                                                            id="employer">
                                                        <option value="">Select Employer</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Profession --}}
                                            <div class="col-md-4 visaReceivedConditionalField" style="display: none">
                                                <div class="mb-3">
                                                    <label class="form-label" for="profession">
                                                        Profession <span class="text-danger">*</span>
                                                    </label>

                                                    <select class="form-select"
                                                            name="profession"
                                                            id="profession">
                                                        <option value="">Select Profession</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Buttons --}}
                                            <div class="col-md-12">


                                                <button type="submit"
                                                        class="btn btn-primary btn-sm float-end updateStatusBtn VisaReceivedBtn">
                                                    Update Status
                                                </button>

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
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
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
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
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
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Emigration Approved -->
                                    <form action="{{ route('admin.candidate.emigrationapproved') }}" method="POST" id="emigrationapproved" style="display: none" enctype="multipart/form-data">
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
                                            <div class="col-md-12" id="emigrationPdfWrapper" style="display: none">
                                                <div class="mb-3">
                                                    <label class="form-label" for="emigration_pdf">
                                                        Emigration PDF <span class="text-danger">* Required</span>
                                                    </label>
                                                    <div id="emigrationPdfExisting" class="mb-2" style="display: none">
                                                        <i class="ti ti-file-text"></i>
                                                        <a href="#" id="emigrationPdfExistingLink" target="_blank">View uploaded Emigration PDF</a>
                                                        <small class="text-muted d-block">A PDF is already on file — upload a new one only if you want to replace it.</small>
                                                    </div>
                                                    <input type="file" name="emigration_pdf" id="emigration_pdf" class="form-control" accept="application/pdf,.pdf">
                                                    <small class="form-text text-muted">Only PDF files are allowed.</small>
                                                    <div class="text-danger mt-1" id="emigrationPdfError" style="display:none"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                                {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- Waiting for FLight Ticket -->
                                    <form action="{{ route('admin.candidate.waitingforticket') }}" method="POST" id="waitingforflightticket" style="display: none" enctype="multipart/form-data">
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
                                            <div class="col-md-12" id="flightFieldsWrapper" style="display: none">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="flight_ticket_pdf">
                                                                Flight Ticket PDF <span class="text-danger">* Required</span>
                                                            </label>
                                                            <div id="flightTicketExisting" class="mb-2" style="display: none">
                                                                <i class="ti ti-file-text"></i>
                                                                <a href="#" id="flightTicketExistingLink" target="_blank">View uploaded Flight Ticket</a>
                                                                <small class="text-muted d-block">A ticket PDF is already on file — upload a new one only if you want to replace it.</small>
                                                            </div>
                                                            <input type="file" name="flight_ticket_pdf" id="flight_ticket_pdf" class="form-control" accept="application/pdf,.pdf">
                                                            <small class="form-text text-muted">Only PDF files are allowed.</small>
                                                            <div class="text-danger mt-1" id="flightTicketPdfError" style="display:none"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="flight_from_city">
                                                                From City <span class="text-danger">*</span>
                                                            </label>
                                                            <select class="form-select select2" name="flight_from_city" id="flight_from_city" data-allow-clear="true">
                                                                <option value="">Select From City</option>
                                                                @foreach ($cities as $flightCity)
                                                                    <option value="{{ $flightCity->id }}">{{ $flightCity->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="flight_to_city">
                                                                To City <span class="text-danger">*</span>
                                                            </label>
                                                            <select class="form-select select2" name="flight_to_city" id="flight_to_city" data-allow-clear="true">
                                                                <option value="">Select To City</option>
                                                                @foreach ($cities as $flightCity)
                                                                    <option value="{{ $flightCity->id }}">{{ $flightCity->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label" for="flight_datetime">
                                                                Flight Date &amp; Time <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" name="flight_datetime" id="flight_datetime" class="form-control" placeholder="Select flight date &amp; time" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                {{-- <button type="button" class="btn btn-danger btn-sm cancelledButton">Cancelled</button> --}}
                                                {{-- <button type="button" class="btn btn-warning btn-sm holdButton">Hold</button> --}}
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
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
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
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
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
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
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
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
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
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
                                                <button type="submit" class="btn btn-primary btn-sm float-end updateStatusBtn">Update Status</button>
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

            <!-- Filter Panel Start -->
            <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="offcanvas-title" id="filterpanelLabel">Candidate Filter</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>


                        <div class="modal-body">
                            <div class="row">
                                
                                <div class="col-md-4 mb-3 careoff-div">
                                    <select name="" id="by-careoff" class="selectpicker w-100" data-actions-box="true" data-live-search="true" data-style="default-btn" title="Careoff" multiple>
                                        @foreach ($careoffFilters as $careoffFilter)
                                            <option value="{{ $careoffFilter->id }}" @if(isset($Candidateadminsavefilter) && in_array($careoffFilter->id,explode(",",$Candidateadminsavefilter->careoff_id))) selected @endif>{{ $careoffFilter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3 cand-status-div">
                                    <select name="" id="by-cand-status" class="selectpicker w-100" data-style="btn-default" title="Candidate Status" multiple data-live-search="true" data-actions-box="true">
                                        @foreach ($final_cand_status as $final_cand_stat)
                                            <option value="{{ $final_cand_stat->status }}" @if(isset($Candidateadminsavefilter) && in_array($final_cand_stat->status,explode(",",$Candidateadminsavefilter->cand_status))) selected @endif> {{ $final_cand_stat->status }}</option>
                                        @endforeach
                                    </select>

                                </div>


                                <div class="col-md-4 mb-3 job-type-div">
                                    <select name="" id="by-job-type" class="form-select select22f" multiple data-placeholder="Job Title">
                                        {{-- <option value="">Job Type</option> --}}
                                        @foreach ($job_types as $job_type)
                                            <option value="{{ $job_type->id }}" @if(isset($Candidateadminsavefilter) && in_array($job_type->id,explode(",",$Candidateadminsavefilter->jobtype_id))) selected @endif>{{ $job_type->eng_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3 salary-filter-div">
                                    <select name="" id="by-salary" class="selectpicker w-100" data-actions-box="true" data-live-search="true" data-style="default-btn" title="Salary" multiple>
                                      @foreach ($salaryFilters as $salaryFilter)
                                            <option value="{{ $salaryFilter }}"
                                                @if(isset($Candidateadminsavefilter) && in_array($salaryFilter, explode(',', $Candidateadminsavefilter->exp_sal ?? '')))
                                                    selected
                                                @endif>
                                                {{ $salaryFilter }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3 pass-type-div">
                                    <select name="" id="by-pass-type" class="form-select filterData select22f" multiple data-placeholder="Passport Type">
                                        {{-- <option value="">Passport Type</option> --}}
                                        <option value="ECNR" @if(isset($Candidateadminsavefilter) && in_array("ECNR",explode(",",$Candidateadminsavefilter->pass_type))) selected @endif>ECNR</option>
                                        <option value="ECR" @if(isset($Candidateadminsavefilter) && in_array("ECR",explode(",",$Candidateadminsavefilter->pass_type))) selected @endif>ECR</option>
                                    </select>
                                </div>



                                <div class="col-md-4 mb-3 create-by-div" >
                                    <select name="" id="by-create-by" class="selectpicker w-100" multiple data-actions-box="true" data-live-search="true" data-style="default-btn" title="Created By">
                                        {{-- <select name="" id="by-create-by" class="form-select select22f" multiple data-placeholder="Created By"> --}}
                                        {{-- <option value="">Created By</option> --}}
                                        @foreach ($createbys as $createby)
                                            <option value="{{ $createby->id }}" @if(isset($Candidateadminsavefilter) && in_array($createby->id,explode(",",$Candidateadminsavefilter->createby_id))) selected @endif>{{ $createby->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3 religion-div">
                                    <select name="" id="by-religion" class="form-select select22f" data-placeholder="Religion" multiple>
                                        {{-- <option value="">Religion</option> --}}
                                        @foreach ($religions as $religion)
                                            <option value="{{ $religion->id }}" @if(isset($Candidateadminsavefilter) && in_array($religion->id,explode(",",$Candidateadminsavefilter->religion_id))) selected @endif>{{ $religion->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3 city-div">
                                    <select name="" id="by-city" class="selectpicker w-100" data-live-search="true" data-actions-box="true" data-style="default-btn" title="City" multiple>
                                        {{-- <select name="" id="by-city" class="form-select select22f" data-placeholder="City" multiple> --}}
                                        {{-- <option value="">City</option> --}}
                                        @foreach ($citiesfs as $citiesf)

                                            <option value="{{ $citiesf->candcity_text }}" @if(isset($Candidateadminsavefilter) && in_array($citiesf->candcity_text,explode(",",$Candidateadminsavefilter->city))) selected @endif>{{ $citiesf->candcity_text }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3 region-div">
                                    <select name="" id="by-region" class="selectpicker w-100" data-style="default-btn" data-actions-box="true" data-live-search="true" title="Region" multiple>
                                        {{-- <select name="" id="by-region" class="form-select select22f" data-placeholder="Region" multiple> --}}
                                        {{-- <option value="">Region</option> --}}
                                        @foreach ($regions as $region)
                                            <option value="{{ $region->id }}" @if(isset($Candidateadminsavefilter) && in_array($region->id,explode(",",$Candidateadminsavefilter->region_id))) selected @endif>{{ $region->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3 experience-region-div">
                                    <select name="" id="by-experience-region" class="form-select select22f" data-placeholder="Experience Region" multiple>
                                        {{-- <option value="">Experience Region</option> --}}
                                        @foreach ($gulfexperiences as $gulfexperience)
                                            <option value="{{ $gulfexperience->gulfexperience }}" @if(isset($Candidateadminsavefilter) && in_array($gulfexperience->gulfexperience,explode(",",$Candidateadminsavefilter->experience_region))) selected @endif>@if($gulfexperience->gulfexperience == 1) {{ 'Indian Experience' }} @elseif($gulfexperience->gulfexperience == 2) {{ 'Gulf Experience' }} @endif</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3 publish-status-div">
                                    <select name="" id="by-publish-status" class="form-select select22f" data-placeholder="Publish Status" multiple>
                                        {{-- <option value="">Publish Status</option> --}}
                                        @foreach ($publishes as $publish)
                                            <option value="{{ $publish->publish }}" @if(isset($Candidateadminsavefilter) && in_array($publish->publish,explode(",",$Candidateadminsavefilter->publish_status))) selected @endif>@if($publish->publish == 1) {{ 'Publish' }} @else {{ 'Unpublish' }} @endif</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-4 mb-3 created-date-div">
                                    <input type="text" name="craete_date_range" id="by-created-date" @if(isset($Candidateadminsavefilter)) value="{{ $Candidateadminsavefilter->craete_date_range }}" @endif class="form-control createdate-picker bsdatpicket" placeholder="Created date with range...">
                                </div>
                                <div class="col-md-4 mb-3 medical-expiry-div">
                                    <input type="text" name="medical_expiry_date_filter" @if(isset($Candidateadminsavefilter)) value="{{ $Candidateadminsavefilter->medical_expiry_date_filter }}" @endif id="by-medical-expiry-date" class="form-control singledatepicker" placeholder="Medical expiry date...">
                                </div>

                                <div class="col-md-4 mb-3 sourcing-date-div">
                                    <input type="text" id="by-sourcing-date" name="sourcing_date_range" @if(isset($Candidateadminsavefilter)) value="{{ $Candidateadminsavefilter->sourcing_date_range }}" @endif class="form-control bsdatpicket" placeholder="Enter sourcing date range..." />
                                </div>

                                
                                <div class="col-md-4 mb-3 cand-medical-status-div">
                                    <select name="" id="by-cand-medical-status" class="selectpicker w-100" data-style="default-btn" data-actions-box="true" data-live-search="true" title="Medical Status" multiple>

                                        @foreach ($medicalStatus as $medicalSt)
                                            <option value="{{ $medicalSt->medical_health_status }}" @if(isset($Candidateadminsavefilter) && in_array($medicalSt->medical_health_status,explode(",",$Candidateadminsavefilter->cand_medical_status))) selected @endif>{{ $medicalSt->medical_health_status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3 cand-payment-status-div" >
                                    <select name="" id="by-cand-payment-status" class="form-select select22f" data-placeholder="Payment Status" multiple>
                                        @foreach ($paymentStatusFilters as $paymentStatusFilter)
                                            <option value="{{ $paymentStatusFilter->cand_payment_status }}" @if(isset($Candidateadminsavefilter) && in_array($paymentStatusFilter->cand_payment_status,explode(",",$Candidateadminsavefilter->cand_payment_status))) selected @endif>{{ $paymentStatusFilter->cand_payment_status }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3 cand-expect-location-div">
                                    <select name="" id="expwp_id" class="selectpicker w-100" data-live-search="true" data-actions-box="true" data-style="default-btn" title="Expected Work Location" multiple>
                                        {{-- <select name="" id="expwp_id" class="form-select select22f" data-placeholder="Expected Work Location" multiple> --}}
                                        @foreach ($expworklocs as $expwkpc)
                                            <option value="{{ $expwkpc->id }}"  @if(isset($Candidateadminsavefilter) && in_array($expwkpc->id,explode(",",$Candidateadminsavefilter->department_id))) selected @endif>{{ $expwkpc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            {{-- <button type="button" class="btn btn-sm btn-primary applyfilter">Apply Filter</button> --}}
                            <button type="button" class="btn btn-warning btn-sm resetfilter">Reset Filter</button>
                            <button type="button" class="btn btn-success btn-sm savetodoFilter">Save Filter</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filter Panel End -->


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
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script> --}}


    @if (Auth::guard('admin')->user()->user_type == 1)
        <script>
            var cand_status = '1';

        </script>
    @else
        @if (isset($perm) && $perm->candidate_status == 1)
            <script>
                var cand_status = '1';

            </script>
        @else
            <script>
                var cand_status = '0';

            </script>
        @endif
    @endif

    <!-- Page JS -->
    {{-- <script src="{{ asset('admin/assets/pages/app-candidate-list.js') }}"></script> --}}
    {{-- <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script> --}}

    <script src="{{ asset('admin/assets/pages/validation/candidate-validation.js') }}"></script>

    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>

    {{-- <script src="{{ asset('admin/assets/custom/main.js') }}"></script> --}}

    <!--  -->
    <script>
        $(document).ready(function(){
            // Show/Hide Add Module based on Switch
            $('.option-switch-input').on('change',function(){
               
                if($(this).is(':checked')){
                    $('.hideshowaddmodule').show();
                }else{
                    $('.hideshowaddmodule').hide();
                }

                var short_form_code = $(this).is(':checked') ?'1':'0';
                jQuery.ajax({
                    url: "{{ route('admin.candidate.saveshortformcode') }}",
                    method: "POST",
                    type: "html",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        short_form_code: short_form_code
                    },
                    success: function(data){
                        if(data){
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                }); 

            });
            
        });
    </script>
    <!--  -->

    <script>
        $(document).ready(function(){
            var selectPicker = $('.selectpicker');

            if (selectPicker.length) {
                selectPicker.selectpicker();
            }

            $('.select22').select2();

            var bsRangePickerBasic = $('.bsdatpicket');
            var singledatepicket = $('.singledatepicker');
            // Basic
            if (bsRangePickerBasic.length) {
                bsRangePickerBasic.daterangepicker({
                    // todayHighlight: true,
                    opens: isRtl ? 'left' : 'right',
                    autoUpdateInput: false,

                    locale: {
                        cancelLabel: 'Clear'
                    }
                });
            }
            // Single Datepicket
            if (singledatepicket.length) {
                singledatepicket.daterangepicker({
                    // todayHighlight: true,
                    opens: isRtl ? 'left' : 'right',
                    autoUpdateInput: false,
                    singleDatePicker: true,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'YYYY-MM-DD'
                    }
                });
            }

            $('body').on('shown.bs.modal', '#filterpanel', function() {
                $(this).find('.select22f').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        return;
                    }

                    $(this).select2({
                        dropdownParent: $(this).parent()

                    });
                });
            });

            $('.bsdatpicket').daterangepicker({
                autoUpdateInput: false,
                opens: 'right',
                locale: { cancelLabel: 'Clear' }
            }).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                updateFilterIndicator();
            }).on('cancel.daterangepicker', function() {
                $(this).val('');
                updateFilterIndicator();
            });

            var singledatepicket = $('.singledatepicker');

            if (singledatepicket.length) {
                singledatepicket.daterangepicker({
                    opens: isRtl ? 'left' : 'right',
                    autoUpdateInput: false,
                    singleDatePicker: true,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'YYYY-MM-DD'
                    }
                })
                .on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    updateFilterIndicator();
                })
                .on('cancel.daterangepicker', function () {
                    $(this).val('');
                    updateFilterIndicator();
                });
            }




            // $('input[name="sourcing_date_range"]').on('apply.daterangepicker', function(ev, picker) {
            //     $(this).val(picker.startDate.format('YYYY/MM/DD') + ' to ' + picker.endDate.format('YYYY/MM/DD'));
            // });

            // $('input[name="sourcing_date_range"]').on('cancel.daterangepicker', function(ev, picker) {
            //     $(this).val('');
            // });

        });
    </script>
    <script>
        $(document).on('click','.reloadModal',function(event){
            event.preventDefault();
            var pertner_id = $('.partner_id2').val();

            $(".partner_id2").trigger("change");


        });
    </script>

    <script>
        $(document).ready(function(){
            $('.flatpickr-date').flatpickr();
        });
    </script>

    <script>
        $(document).ready(function(){
            // Common AJAX setup for CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });



            // Function to retrieve all filter values
            function getFilterData() {
                return {
                    page_list: $('#pagination_list').val(),
                    search_text: $('#search_text').val(),
                    pass_type: $('#by-pass-type').val(),
                    jobtype_id: $('#by-job-type').val(),
                    careoff_id: $('#by-careoff').val(),
                    salary: $('#by-salary').val(),
                    createby_id: $('#by-create-by').val(),
                    religion_id: $('#by-religion').val(),
                    city: $('#by-city').val(),
                    region_id: $('#by-region').val(),
                    experience_region: $('#by-experience-region').val(),
                    publish_status: $('#by-publish-status').val(),
                    craete_date_range: $('#by-created-date').val(),
                    medical_expiry_date_filter: $('#by-medical-expiry-date').val(),
                    sourcing_date_range: $('#by-sourcing-date').val(),
                    cand_status: $('#by-cand-status').val(),
                    cand_medical_status: $('#by-cand-medical-status').val(),
                    cand_payment_status: $('#by-cand-payment-status').val(),
                    expwp_id: $('#expwp_id').val(),
                };
            }

            // Function to reload todo list based on filter data
            function reloadTodoList() {
                $.ajax({
                    url: "{{ route('admin.candidate') }}",
                    method: "GET",
                    dataType: "html",
                    data: getFilterData(),
                    success: function (data) {
                        $('.candloadpaginate').html(data);
                    }
                });
            }
            // Generic event handler for change and input events on filter elements
            function bindFilterChange(selector) {
                $(selector).on('change input', function () {
                    reloadTodoList();
                });
            }
            // Bind change/input events to all filter elements
            bindFilterChange('#pagination_list');
            bindFilterChange('#search_text');
            bindFilterChange('#by-pass-type');
            bindFilterChange('#by-job-type');
            bindFilterChange('#by-careoff');
            bindFilterChange('#by-salary');
            bindFilterChange('#by-create-by');
            bindFilterChange('#by-religion');
            bindFilterChange('#by-region');
            bindFilterChange('#by-experience-region');
            bindFilterChange('#by-publish-status');
            bindFilterChange('#by-cand-status');
            bindFilterChange('#by-cand-medical-status');
            bindFilterChange('#by-cand-payment-status');
            bindFilterChange('#expwp_id');

            // Date Range Picker for Start and End Dates with Apply and Cancel Event Handling
            function bindDatePicker(selector) {
                $(selector).on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    reloadTodoList();
                }).on('cancel.daterangepicker', function () {
                    $(this).val('');
                    reloadTodoList();
                });
            }

            function bindDateRangePicker(selector) {
                $(selector).on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    reloadTodoList();
                }).on('cancel.daterangepicker', function () {
                    $(this).val('');
                    reloadTodoList();
                });
            }

            // Bind date range picker to the relevant fields
            bindDatePicker('input[name="medical_expiry_date_filter"]');
            bindDateRangePicker('input[name="craete_date_range"]');
            bindDateRangePicker('input[name="sourcing_date_range"]');

            // Quick status filter cards (hideshowaddmodule) - click to filter by candidate status
            $(document).on('click', '.status-filter-card', function (e) {
                e.preventDefault();

                var $this = $(this);
                var status = $this.data('status');
                var color = $this.data('color');

                if ($this.hasClass('active')) {
                    $this.removeClass('active').css({ background: '', color: color });

                    $('#by-cand-status').selectpicker('val', []).trigger('change');
                } else {
                    $('.status-filter-card').removeClass('active').each(function () {
                        $(this).css({ background: '', color: $(this).data('color') });
                    });

                    $this.addClass('active').css({ background: color, color: '#fff' });

                    $('#by-cand-status').selectpicker('val', [status]).trigger('change');
                }
            });

            // Save Filter
            $(document).on('click','.savetodoFilter',function(){
                var pass_type = $('#by-pass-type').val();
                var jobtype_id = $('#by-job-type').val();
                var careoff_id = $('#by-careoff').val();
                var salary = $('#by-salary').val();
                var createby_id = $('#by-create-by').val();
                var religion_id = $('#by-religion').val();
                var city = $('#by-city').val();
                var region_id = $('#by-region').val();
                var experience_region = $('#by-experience-region').val();
                var publish_status = $('#by-publish-status').val();
                var craete_date_range = $('#by-created-date').val();
                var medical_expiry_date_filter = $('#by-medical-expiry-date').val();
                var sourcing_date_range = $('#by-sourcing-date').val();
                var cand_status = $('#by-cand-status').val();
                var cand_medical_status = $('#by-cand-medical-status').val();
                var cand_payment_status = $('#by-cand-payment-status').val();
                var expwp_id = $('#expwp_id').val();

                jQuery.ajax({
                    url: "{{ route('admin.candidate.saveadminfilter') }}",
                    method: "POST",
                    type: "html",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        pass_type:pass_type,
                        salary:salary,
                        jobtype_id: jobtype_id,
                        careoff_id: careoff_id,
                        createby_id: createby_id,
                        religion_id: religion_id,
                        city: city,
                        region_id: region_id,
                        experience_region: experience_region,
                        publish_status: publish_status,
                        craete_date_range: craete_date_range,
                        medical_expiry_date_filter: medical_expiry_date_filter,
                        sourcing_date_range: sourcing_date_range,
                        cand_status: cand_status,
                        cand_medical_status: cand_medical_status,
                        cand_payment_status: cand_payment_status,
                        expwp_id: expwp_id
                    },
                    success: function(data){
                        if(data){
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                });

                updateFilterIndicator();

            });

            // Reset Filter
            $(document).on('click','.resetfilter',function(){

                // $('#by-cand-status').selectpicker('deselectAll');
                $('.selectpicker').selectpicker('deselectAll');

                if ($('#by-pass-type').val() != '') {
                    $('#by-pass-type').val('').trigger('change');
                }

                if ($('#by-job-type').val() != '') {
                    $('#by-job-type').val('').trigger('change');
                }

                if ($('#by-careoff').val() != '') {
                    $('#by-careoff').val('').trigger('change');
                }

                if ($('#by-salary').val() != '') {
                    $('#by-salary').val('').trigger('change');
                }

                if ($('#by-create-by').val() != '') {
                    $('#by-create-by').val('').trigger('change');
                }

                if ($('#by-religion').val() != '') {
                    $('#by-religion').val('').trigger('change');
                }

                if ($('#by-city').val() != '') {
                    $('#by-city').val('').trigger('change');
                }

                if ($('#by-region').val() != '') {
                    $('#by-region').val('').trigger('change');
                }

                if ($('#by-experience-region').val() != '') {
                    $('#by-experience-region').val('').trigger('change');
                }

                if ($('#by-publish-status').val() != '') {
                    $('#by-publish-status').val('').trigger('change');
                }

                if ($('#by-created-date').val() != '') {
                    $('#by-created-date').trigger('cancel.daterangepicker');
                }

                if ($('#by-medical-expiry-date').val() != '') {
                    $('#by-medical-expiry-date').trigger('cancel.daterangepicker');
                }

                if ($('#by-sourcing-date').val() != '') {
                    $('#by-sourcing-date').trigger('cancel.daterangepicker');
                }

                // if ($('#by-cand-status').val() != '') {
                //     $('#by-cand-status').val('').trigger('change');
                // }

                if ($('#by-cand-medical-status').val() != '') {
                    $('#by-cand-medical-status').val('').trigger('change');
                }

                if ($('#by-cand-payment-status').val() != '') {
                    $('#by-cand-payment-status').val('').trigger('change');
                }

                if ($('expwp_id').val() != '') {
                    $('expwp_id').val('').trigger('change');
                }


                jQuery.ajax({
                    url: "{{ route('admin.candidate.resetadminfilter') }}",
                    method: "POST",
                    type: "html",
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        if(data){
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                });
                
                updateFilterIndicator();

            });

            // Pagination Request
            $('body').on('click','.pagination a',function(e){
                e.preventDefault();
                var url = $(this).attr('href');
                var url_data = getFilterData();
                var finalURL = url + "&" + $.param(url_data);;
                // alert(finalURL);

                getPaginations(finalURL);
                window.history.pushState("", url);

            });

            function getPaginations(finalURL){
                $.ajax({
                    url : finalURL
                }).done(function(data){
                    $('.candloadpaginate').html(data);

                }).fail(function(){

                    alert("Something gone wrong!")
                });
            }

            $('.add-mob-number').intlTelInput({

                // localizedCountries: true,
                onlyCountries: ["in","sa","qa","ae","kw"],
                preferredCountries: ["in","sa"],
                separateDialCode: true,
                initialCountry: "",


            }).on('countrychange',function(e,countryData){
                $('#mobile-no-dial-code').val(($(".add-mob-number").intlTelInput("getSelectedCountryData").dialCode))
            });



        });
    </script>

    <!-- Pagination Page Reload Start -->
    {{-- <script>
        $(function(){
          $('body').on('click','.pagination a',function(e){
            e.preventDefault();
            var url = $(this).attr('href');



            getPaginations(url);
            window.history.pushState("", url);
          });

          function getPaginations(url){


            $.ajax({
              url : url
            }).done(function(data){
              $('.candloadpaginate').html(data);

            }).fail(function(){

              alert("Something gone wrong!")
            });
          }
        });
    </script> --}}
    <!-- Pagination Page Reload End -->

    <script>
        $(document).on('change','.partner_id2',function(event){
            event.preventDefault();
            var pertner_id = $(this).val();
            var ids = $("#candcvs_id").val();

            // Get ALl Candidate Executed and Non Executed List
            jQuery.ajax({
                url: "{{ route('admin.candidate.getCVData') }}",
                method: "GET",
                type: "html",
                data: {
                    ids: ids,
                    pertner_id: pertner_id
                },
                success: function(response){
                    
                    $("#executeData").html(response.res);
                    if(response.cv_status == 0){
                        $('.shareCVButton').attr("disabled",true);
                    }else{
                        $('.shareCVButton').attr("disabled",false);
                    }
                }
            });

        });
    </script>

    <script>
        toastr.options = {
            "timeOut": 5000,
            "showDuration": 300,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
        };
        $(document).on('click','.shareCVButton',function(event){
            event.preventDefault();

            // Form Validation
            // Check Flag is true
            var isValid = true;
            $('.error-class').text('');

            var partner_id = $('#partner_id2').val();
            // var cv_purpose = $('input[name="cv_purpose"]:checked').val();
            var cv_purpose = $('input[name="cv_purpose"]').is(":checked");

            if (partner_id === '') {
                $('#error-partner').text("Please Select Partner...");
                isValid = false;
            }

            if(cv_purpose == true){
                var cv_purpose_value = $('input[name="cv_purpose"]:checked').val();
                if (cv_purpose_value == 1) {
                    var shared_to = $('input[name="share_to"]').is(":checked");

                    if(shared_to === true){
                        var shared_to_value = $('input[name="share_to"]:checked').val();
                        if(shared_to_value == 1){
                            var staff_id2 = $('#staff_id2').val();

                            if (staff_id2 === '') {
                                $('#error-staff2').text("Please Select Staff...");
                                isValid = false;
                            }

                        }
                    }else{
                        $('#error-shareto').text("Please Select Share To...");
                        isValid = false;
                    }

                }
            }else{
                $('#error-cvpurpose').text("Please Select CV Purpose...");
                isValid = false;
            }




            var formdata = $('#sharedCVvalidation2').serialize();

            if (isValid) {
                // Disable Button
                $('#shareCVButton').attr("disabled",true);

                // Progress Bar Start
                $('.progress-wrapper').show();
                updateProgressBar(0); // Initialize the progress bar to 0%

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.cvmultisharepartner') }}",
                    data: formdata,
                    xhr: function(){
                        var xhr = new window.XMLHttpRequest();
                        // Upload progress
                        xhr.upload.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                updateProgressBar(percentComplete * 100);
                            }
                        }, false);
                        // Download progress
                        xhr.addEventListener("progress", function(evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                updateProgressBar(percentComplete * 100);
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response){
                        if(response.cv_purpose == 0){

                            if(response.total_files > 0){
                                const files = response.files;

                                files.forEach(function(fileUrl){
                                    const a = document.createElement('a');
                                    a.href = fileUrl;
                                    a.target = '_blank';
                                    a.download = fileUrl.split('/').pop();
                                    document.body.appendChild(a);
                                    a.click();
                                    document.body.removeChild(a);
                                });

                                toastr.success(response.message);

                            }else{
                                toastr.success(response.message);
                            }

                        }

                        if(response.cv_purpose == 1){
                            toastr.success(response.message);
                        }

                        updateProgressBar(100); // Update to 100% on success
                        // Handle the response
                        setTimeout(function() {
                            $(".progress-wrapper").hide();
                            updateProgressBar(0); // Reset progress bar
                        }, 2000);

                        // Form Reset
                        $('#shareCVButton').attr("disabled",false);


                        // Hide Modal
                        $('#sharedCV').modal('hide');


                    },
                    error: function(xhr){
                        toastr.error(xhr.responseText);
                        $("#progress-label-new").text("Error!");
                        $('#shareCVButton').attr("disabled",false);
                        setTimeout(function() {
                            $(".progress-wrapper").hide();
                            updateProgressBar(0); // Reset progress bar
                        }, 2000);
                    }
                });
            }



        });

        function updateProgressBar(value) {
            $(".progress-bar").width(value + '%');
            $("#progress-label-new").text(Math.round(value) + '%');
        }

    </script>


    <script>
        $(document).ready(function(){
            $('[name="cv_purpose"]').on("change",function(){
                var value = $(this).val();
                if (value == '1') {
                    $('#disShareTo').show();

                } else {
                    $('[name="share_to"]').prop("checked",false);
                    $('#disShareTo').hide();
                    $('#disStaffList').hide();
                }

            });

            $('[name="share_to"]').on("change",function(){
                var value = $(this).val();

                if (value == '1') {
                    $('#disStaffList').show();

                } else {
                    $('#disStaffList').hide();

                }

            });
        });
    </script>


    <script>
        $(document).ready(function(){
            // $('#by-created-date').flatpickr();
            // $('#by-created-date').flatpickr({
            //     mode: "range"
            // });
            // $('#by-medical-expiry-date').flatpickr();

            // $('.createdate-picker').on('change',function(){
            //     var newDate = $(this).val();
            //     var newSplitDate = newDate.split("to");
            //     var startDate = newSplitDate[0];
            //     var endDate = newSplitDate[1];


            //     var ObbjecStartDate = new Date(startDate);
            //     var ObbjecendDate = new Date(endDate);


            //     if (!isNaN(ObbjecStartDate) && !isNaN(ObbjecendDate)) {
            //         var timeDifference = ObbjecendDate.getTime() - ObbjecStartDate.getTime();
            //         var dayDifference = timeDifference / (1000 * 3600 * 24);

            //         let year,month,day,finalDay;

            //         let date = new Date(ObbjecStartDate);

            //         let datesArray = [];
            //         datesArray.push(startDate);
            //         for (let i = 0; i < dayDifference; i++) {
            //             // Increment the date by one day
            //             date.setDate(date.getDate() + 1);

            //             year = date.getFullYear();
            //             month = (date.getMonth() + 1).toString().padStart(2, '0');
            //             day = date.getDate().toString().padStart(2, '0');

            //             finalDay = year+"-"+month+"-"+day;


            //             datesArray.push(finalDay);
            //         }

            //         console.log(datesArray);
            //     }
            // });


        });
    </script>

    <script>
        function check_pass(pass){
            var pass_no = pass.value;
            var _token = $('meta[name="csrf-token"]').attr('content');
            jQuery.ajax({
                url: "{{ url('admin/candidate/check/pass-no/new') }}",
                method: "POST",
                type: "html",
                data: {
                    "_token": "{{ csrf_token() }}",
                    pass_no: pass_no
                },
                success: function(result){
                    if (result) {
                        $("#errorPassportNo").text('Passport no "'+pass_no+'" already exists');
                        $("#duplipass").show();
                        $("#addSubmBtn").prop('disabled',true);
                    } else {
                        $("#errorPassportNo").text('');
                        $("#duplipass").hide();
                        $("#addSubmBtn").prop('disabled',false);
                    }
                }
            });
        }

    </script>
    <script>
        $(document).ready(function(){
            $('#repeatPass').on('change',function(){
                let value = $(this).is(':checked');

                if (value == true) {
                $('#addSubmBtn').prop('disabled',false);
                }else{
                $('#addSubmBtn').prop('disabled',true);
                }

            });
        });
    </script>
    <script>
        $(document).ready(function(){
            // var table = $('.datatables-users').DataTable();

            // Whether the candidate currently shown in the modal already has an
            // "emegartion pdf" Candidatefile on record (read from candstatusGet).
            var emigrationHasExistingPdf = false;

            // Whether the candidate currently shown in the modal already has a
            // "flight ticket" Candidatefile on record (read from candstatusGet).
            var flightHasExistingTicket = false;

            // Refreshes the candidate list/badges after a stage/status update without a full page reload.
            // Mirrors the existing reloadTodoList() pattern used for filter changes (same route + target).
            function reloadCandidateList() {
                $.ajax({
                    url: '{{ route("admin.candidate") }}',
                    method: "GET",
                    dataType: "html",
                    data: {
                        page_list: $('#pagination_list').val()
                    },
                    success: function (data) {
                        $('.candloadpaginate').html(data);
                    }
                });
            }

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
                        console.log(data);
                        // Candidate Are Ready
                        $('#candidateName').text(data.cand_name);
                        $('#candPassport').text(data.pass_no);

                        // Recruitment Partner preselect (Published For Selection, Selected & Visa
                        // Received stages share the same candidate/partner relationship:
                        // cand_statuses.partneroffice_id)
                        $.each(['#recruitment_partner_selected', '#recruitment_partner_published', '#recruitment_partner'], function (_, selector) {
                            var $select = $(selector);
                            if (!$select.length) {
                                return;
                            }
                            $select.empty().append(new Option('Select Recruitment Partner', '', false, false));
                            if (data.partneroffice_id && data.partneroffice_name) {
                                $select.append(new Option(data.partneroffice_name, data.partneroffice_id, true, true));
                                $select.val(data.partneroffice_id).trigger('change');
                            } else {
                                $select.val('').trigger('change');
                            }
                        });

                        // Emigration PDF state (Emigration Approved stage)
                        emigrationHasExistingPdf = !!data.has_emigration_pdf;
                        $('#emigration_pdf').val('');
                        $('#emigrationPdfError').hide().text('');
                        if (emigrationHasExistingPdf && data.emigration_pdf_url) {
                            $('#emigrationPdfExistingLink').attr('href', data.emigration_pdf_url);
                            $('#emigrationPdfExisting').show();
                        } else {
                            $('#emigrationPdfExisting').hide();
                            $('#emigrationPdfExistingLink').attr('href', '#');
                        }
                        $('#emigration_pdf').prop('required', !emigrationHasExistingPdf);

                        // Flight details state (Waiting For Flight Ticket stage)
                        flightHasExistingTicket = !!data.has_flight_ticket;
                        $('#flight_ticket_pdf').val('');
                        $('#flightTicketPdfError').hide().text('');
                        if (flightHasExistingTicket && data.flight_ticket_url) {
                            $('#flightTicketExistingLink').attr('href', data.flight_ticket_url);
                            $('#flightTicketExisting').show();
                        } else {
                            $('#flightTicketExisting').hide();
                            $('#flightTicketExistingLink').attr('href', '#');
                        }
                        $('#flight_ticket_pdf').prop('required', !flightHasExistingTicket);
                        $('#flight_from_city').val(data.flight_from_city ? data.flight_from_city : '').trigger('change');
                        $('#flight_to_city').val(data.flight_to_city ? data.flight_to_city : '').trigger('change');
                        if (document.getElementById('flight_datetime') && document.getElementById('flight_datetime')._flatpickr) {
                            document.getElementById('flight_datetime')._flatpickr.setDate(data.flight_datetime || null, true);
                        } else {
                            $('#flight_datetime').val(data.flight_datetime || '');
                        }

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

                            $('.candhbtn').each(function () {
                                this.style.setProperty('margin-top', '7.5rem', 'important');
                            });
                        }
                        else{
                            $('.candhbtn').each(function () {
                                this.style.setProperty('margin-top', '1.5rem', 'important');
                            });
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

                var $submitBtn = $(this).find('.updateStatusBtn');

                // Prevent double-click / double submission while a request is in flight
                if ($submitBtn.prop('disabled')) {
                    return;
                }
                $submitBtn.prop('disabled', true);

                var newradiovalue = $('input[name="published_for_selection"]:checked').val();
                var candID = $('#candIDN').val();
                var partnerId = $('#recruitment_partner_published').val();
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
                        'partneroffice_id': partnerId,
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

                            // Carry forward the Recruitment Partner picked in Published For
                            // Selection into the Selected stage's dropdown (same underlying
                            // cand_statuses.partneroffice_id value, just entering the modal
                            // without a reload in between).
                            var $selectedPartner = $('#recruitment_partner_selected');
                            if (partnerId) {
                                var partnerText = $('#recruitment_partner_published option:selected').text();
                                $selectedPartner.empty().append(new Option(partnerText, partnerId, true, true));
                                $selectedPartner.val(partnerId).trigger('change');
                            } else {
                                $selectedPartner.empty().append(new Option('Select Recruitment Partner', '', false, false));
                                $selectedPartner.val('').trigger('change');
                            }
                        }
                        reloadCandidateList();
                    },
                    error: function (xhr) {
                        let message = 'Something went wrong. Please try again.';

                        if (xhr.responseJSON) {
                            message = xhr.responseJSON.message || xhr.responseJSON.error || message;
                        } else if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                message = response.message || response.error || message;
                            } catch (e) {
                                // Keep default message
                            }
                        }

                        alert(message);
                        console.error('Published For Selection Status Error:', xhr.responseText);
                    },
                    complete: function () {
                        $submitBtn.prop('disabled', false);
                    }
                });
            });

            // Toggle Recruitment Partner dropdown based on Published For Selection Yes/No
            $(document).on('change', '#publishedforselection input[name="published_for_selection"]', function () {
                var $wrap = $('#recruitmentPartnerPublishedWrap');
                var $partner = $('#recruitment_partner_published');

                if ($(this).val() == '1') {
                    $wrap.show();
                } else {
                    $wrap.hide();
                    // Clear any leftover validation error styling now that the field is hidden/not required
                    $partner.next('.select2-container').find('.select2-selection').css('border-color', '');
                    $partner.next('.select2-container').next('.text-danger').remove();
                }
            });

            // Toggle Recruitment Partner dropdown based on Selected Yes/No
            $(document).on('change', '#selectedStatus input[name="selected"]', function () {
                var $wrap = $('#recruitmentPartnerSelectedWrap');
                var $partner = $('#recruitment_partner_selected');

                if ($(this).val() == '1') {
                    $wrap.show();
                } else {
                    $wrap.hide();
                    // Clear any leftover validation error styling now that the field is hidden/not required
                    $partner.next('.select2-container').find('.select2-selection').css('border-color', '');
                    $partner.next('.select2-container').next('.text-danger').remove();
                }
            });

            // Update Selected Status
            $(document).on('submit','#selectedStatus',function(e){
                e.preventDefault();

                var $submitBtn = $(this).find('.updateStatusBtn');

                // Prevent double-click / double submission while a request is in flight
                if ($submitBtn.prop('disabled')) {
                    return;
                }

                var newradiovalue = $('input[name="selected"]:checked').val();
                var candID = $('#candIDN').val();
                var partnerId = $('#recruitment_partner_selected').val();

                // Recruitment Partner is required only when Selected = Yes
                if (newradiovalue == '1' && !validateSelect2($('#recruitment_partner_selected'), 'Please select Recruitment Partner.')) {
                    return;
                }

                $submitBtn.prop('disabled', true);
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
                        'partneroffice_id': partnerId,
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
                        reloadCandidateList();
                    },
                    error: function (xhr) {
                        let message = 'Something went wrong. Please try again.';

                        if (xhr.responseJSON) {
                            message = xhr.responseJSON.message || xhr.responseJSON.error || message;
                        } else if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                message = response.message || response.error || message;
                            } catch (e) {
                                // Keep default message
                            }
                        }

                        alert(message);
                        console.error('Selected Status Error:', xhr.responseText);
                    },
                    complete: function () {
                        $submitBtn.prop('disabled', false);
                    }
                });
            });

            // Update Visa Received Status
            $(document).on('submit','#visareceived',function(e){
                e.preventDefault();

                var $form = $(this);
                var $submitBtn = $form.find('.VisaReceivedBtn');

                // Prevent double-click / double submission while a request is in flight
                if ($submitBtn.prop('disabled')) {
                    return;
                }
                $submitBtn.prop('disabled', true);

                var newradiovalue = $('input[name="visa_received"]:checked').val();
                var candID = $('#candIDN').val();
                var visaeditid = $('#visaeditid').val();
                var fromreq = $('#fromreq').val();
                var proff_id = $('#proff_id').val();
                var partnerId = $('#recruitment_partner').val();

                // alert(candID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({

                    url: '{{ url("admin/candidate/status/visareceived") }}',

                    method: "POST",

                    data: {
                        cand_id: candID,
                        visa_received: newradiovalue,
                        visaeditid: visaeditid,
                        fromreq: fromreq,
                        proff_id: proff_id,
                        partneroffice_id: partnerId,
                        _token: "{{ csrf_token() }}",
                    },

                    success: function (data) {

                        // Laravel returned an error
                        if (data.status == 0) {
                            alert(data.message || 'Something went wrong.');
                            return;
                        }

                        // Controller wraps the updated CandStatus record in `data.data`
                        var candStatus = data.data || {};

                        if (candStatus.visa_received == 1 && candStatus.passport_in_embassy == 0) {

                            $('#visareceived').hide();

                            $('#passportinembassy').show();

                            $('#NewCandStatusUpdateLabel').text("Visa Received");

                            if (candStatus.passport_in_embassy == 1) {

                                $('#passport_in_embassy').prop('checked', true).change();

                            } else {

                                $('#passport_in_embassy_not').prop('checked', true).change();
                            }
                        } else if (candStatus.visa_received == 1) {
                            $('#visa_received').prop('checked', true).change();
                        } else {
                            $('#visa_received_not').prop('checked', true).change();
                        }

                        reloadCandidateList();
                    },

                    error: function (xhr) {

                        let message = 'Something went wrong. Please try again.';

                        // Laravel JSON response
                        if (xhr.responseJSON) {

                            message =
                                xhr.responseJSON.message ||
                                xhr.responseJSON.error ||
                                message;

                        } else if (xhr.responseText) {

                            try {

                                const response = JSON.parse(xhr.responseText);

                                message =
                                    response.message ||
                                    response.error ||
                                    message;

                            } catch (e) {
                                // Keep default message
                            }
                        }

                        alert(message);

                        console.error('Visa Received Error:', xhr.responseText);
                    },

                    complete: function () {
                        $submitBtn.prop('disabled', false);
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

            // Toggle the Emigration PDF field based on the Emigration Approved radio
            $(document).on('change', 'input[name="emigration_approved"]', function () {
                var isYes = $('input[name="emigration_approved"]:checked').val() == '1';
                if (isYes) {
                    $('#emigrationPdfWrapper').show();
                    $('#emigration_pdf').prop('required', !emigrationHasExistingPdf);
                } else {
                    $('#emigrationPdfWrapper').hide();
                    $('#emigration_pdf').prop('required', false);
                    $('#emigrationPdfError').hide().text('');
                }
            });

            // Update Emigration Approved Status
            $(document).on('submit','#emigrationapproved',function(e){
                e.preventDefault();

                var $submitBtn = $(this).find('.updateStatusBtn');

                // Prevent double-click / double submission while a request is in flight
                if ($submitBtn.prop('disabled')) {
                    return;
                }

                var newradiovalue = $('input[name="emigration_approved"]:checked').val();
                var candID = $('#candIDN').val();
                var $fileInput = $('#emigration_pdf');
                var hasNewFile = $fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0;

                // Client-side checks improve UX only — the server re-validates independently.
                $('#emigrationPdfError').hide().text('');
                if (newradiovalue == '1' && !emigrationHasExistingPdf && !hasNewFile) {
                    $('#emigrationPdfError').text('Emigration PDF is required when Emigration Approved is Yes.').show();
                    return;
                }
                if (hasNewFile && !/\.pdf$/i.test($fileInput[0].files[0].name)) {
                    $('#emigrationPdfError').text('Only PDF files are allowed.').show();
                    return;
                }

                $submitBtn.prop('disabled', true);

                var formData = new FormData();
                formData.append('cand_id', candID);
                formData.append('emigration_approved', newradiovalue);
                formData.append('_token', "{{ csrf_token() }}");
                if (hasNewFile) {
                    formData.append('emigration_pdf', $fileInput[0].files[0]);
                }

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/emigrationapproved") }}',
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
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
                        reloadCandidateList();
                    },
                    error: function (xhr) {
                        var message = 'Something went wrong. Please try again.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors && xhr.responseJSON.errors.emigration_pdf) {
                                message = xhr.responseJSON.errors.emigration_pdf[0];
                            } else {
                                message = xhr.responseJSON.message || message;
                            }
                        }

                        $('#emigrationPdfError').text(message).show();
                        alert(message);
                        console.error('Emigration Approved Error:', xhr.responseText);
                    },
                    complete: function () {
                        $submitBtn.prop('disabled', false);
                    }
                });
            });

            // Update Waiting For Flight Ticket Status
            // Toggle the Flight fields based on the Waiting For Flight Ticket radio
            $(document).on('change', 'input[name="waiting_for_flight_ticket"]', function () {
                var isYes = $('input[name="waiting_for_flight_ticket"]:checked').val() == '1';
                if (isYes) {
                    $('#flightFieldsWrapper').show();
                    $('#flight_ticket_pdf').prop('required', !flightHasExistingTicket);
                    $('#flight_from_city').prop('required', true);
                    $('#flight_to_city').prop('required', true);
                    $('#flight_datetime').prop('required', true);
                } else {
                    $('#flightFieldsWrapper').hide();
                    $('#flight_ticket_pdf').prop('required', false);
                    $('#flight_from_city').prop('required', false);
                    $('#flight_to_city').prop('required', false);
                    $('#flight_datetime').prop('required', false);
                    $('#flightTicketPdfError').hide().text('');
                }
            });

            $(document).on('submit','#waitingforflightticket',function(e){
                e.preventDefault();

                var $submitBtn = $(this).find('.updateStatusBtn');

                if ($submitBtn.prop('disabled')) {
                    return;
                }

                var newradiovalue = $('input[name="waiting_for_flight_ticket"]:checked').val();
                var candID = $('#candIDN').val();
                var $fileInput = $('#flight_ticket_pdf');
                var hasNewFile = $fileInput.length && $fileInput[0].files && $fileInput[0].files.length > 0;
                var fromCity = $('#flight_from_city').val();
                var toCity = $('#flight_to_city').val();
                var flightDateTime = $('#flight_datetime').val();

                // Client-side checks improve UX only — the server re-validates independently.
                $('#flightTicketPdfError').hide().text('');
                if (newradiovalue == '1') {
                    if (!fromCity || !toCity || !flightDateTime || (!flightHasExistingTicket && !hasNewFile)) {
                        $('#flightTicketPdfError').text('From City, To City, Flight Date & Time and Flight Ticket PDF are all required.').show();
                        return;
                    }
                    if (hasNewFile && !/\.pdf$/i.test($fileInput[0].files[0].name)) {
                        $('#flightTicketPdfError').text('Only PDF files are allowed.').show();
                        return;
                    }
                }

                $submitBtn.prop('disabled', true);

                var formData = new FormData();
                formData.append('cand_id', candID);
                formData.append('waiting_for_flight_ticket', newradiovalue);
                formData.append('flight_from_city', fromCity || '');
                formData.append('flight_to_city', toCity || '');
                formData.append('flight_datetime', flightDateTime || '');
                formData.append('_token', "{{ csrf_token() }}");
                if (hasNewFile) {
                    formData.append('flight_ticket_pdf', $fileInput[0].files[0]);
                }

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                jQuery.ajax({
                    url : '{{ url("admin/candidate/status/waitingforticket") }}',
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
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
                        reloadCandidateList();
                    },
                    error: function (xhr) {
                        var message = 'Something went wrong. Please try again.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                var firstError = Object.values(xhr.responseJSON.errors)[0];
                                message = Array.isArray(firstError) ? firstError[0] : (xhr.responseJSON.message || message);
                            } else {
                                message = xhr.responseJSON.message || message;
                            }
                        }

                        $('#flightTicketPdfError').text(message).show();
                        alert(message);
                        console.error('Waiting For Flight Ticket Error:', xhr.responseText);
                    },
                    complete: function () {
                        $submitBtn.prop('disabled', false);
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
            $('#edituser').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url("admin/candidate/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#edit_ID').val(data.id);
                        $('#edit-cand-name').val(data.cand_name);
                        $('#edit-cand-arname').val(data.arcand_name);
                        $('#edit-pass-no').val(data.pass_no);
                        $('#edit-pass-type').val(data.pass_type).change();
                        $('#edit-dob').val(data.dob);
                        $('#edit-doi').val(data.doi);
                        $('#edit-doe').val(data.doe);
                        $('#edit-sourcing-date').val(data.sourcing_date);
                        $('#edit-place-of-issue').val(data.poi).change();
                        // $('#edit-place-of-birth').val(data.plb_id).change();
                        // $('#edit-place-of-issue').val(data.poi_text).change();
                        $('#edit-place-of-birth').val(data.plb_text).change();
                        $('#edit-nationality-id').val(data.nation_id).change();
                        $('#edit-contact-number').val(data.contact_no);
                        $('#edit-marital-status').val(data.marital_status).change();
                        $('#edit-mob-number').val(data.mobile_no);
                        $('#mobile-no-dial-code-ed').val(data.mobile_no_dial_code);
                        $('#edit-occupation').val(data.jobtype_id).change();
                        $('#edit-address').val(data.address);
                        $('#edit-care-off').val(data.careoff_id).change();
                        $('#edit-associate-id').val(data.associate_id).change();

                        $('.edit-mob-number').intlTelInput({

                            // localizedCountries: true,
                            onlyCountries: ["in","sa","qa","ae","kw"],
                            preferredCountries: ["in","sa"],
                            separateDialCode: true,
                            initialCountry: "",


                        }).on('countrychange',function(e,countryData){
                            $('#mobile-no-dial-code-ed').val(($(".edit-mob-number").intlTelInput("getSelectedCountryData").dialCode))
                        });

                    }
                });
            });
        });
    </script>

    <script>
        var rowMin = 1;
        var rowMax = 4;

        $(document).on('click','.addExp',function(){
            var html = '';
            html += '<div class="row"><div class="col-md-6"><div class="mb-3">';
            html += '<label class="form-label" for="add-exoinyear'+rowMin+'">Experience (in year) <span class="text-danger">*</span></label>';
            html += '<input type="text" name="experience[]" id="add-exoinyear'+rowMin+'" class="form-control" placeholder="Enter experience in year...">';
            html += '</div></div>';

            html += '<div class="col-md-6"><div class="mb-3">';
            html += '<label class="form-label" for="add-job-type'+rowMin+'">Job Type</label>';
            html += '<select name="proff_id[]" id="add-job-type'+rowMin+'" class="form-select select25"><option value="">Select</option>';
            html += '@foreach ($jobtypes as $jobtype)<option value="{{ $jobtype->id }}">{{ $jobtype->eng_name.' ('.$jobtype->ar_name.')' }}</option>@endforeach';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="mb-3">';
            html += '<label class="form-label" for="add-expcountry_id'+rowMin+'">Experience (country name)</label>';
            html += '<select name="expcountry_id[]" id="add-expcountry_id'+rowMin+'" class="form-select select2">';
            html += '<option value="">Select</option>@foreach ($countries as $country)<option value="{{ $country->id }}">{{ $country->name }}</option>@endforeach';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="mb-3">';
            html += '<label for="form-label" for="add-expcity_id'+rowMin+'">City</label>';
            html += '<select name="expcity_id[]" id="add-expcity_id'+rowMin+'" class="form-select select2"><option value="">Select</option>';
            html += '@foreach ($cities as $city)<option value="{{ $city->id }}">{{ $city->name }}</option>@endforeach';
            html += '</select></div></div>';

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
        $(document).ready(function(){

            $('#pass-typef').click(function(){
                $('.pass-type-div').toggle();
            });

            $('#job-typef').click(function(){
                $('.job-type-div').toggle();
            });

            $('#create-byf').click(function(){
                $('.create-by-div').toggle();
            });

            $('#careofff').click(function(){
                $('.careoff-div').toggle();
            });

            $('#religionf').click(function(){
                $('.religion-div').toggle();
            });

            $('#cityf').click(function(){
                $('.city-div').toggle();
            });

            $('#regionf').click(function(){
                $('.region-div').toggle();
            });

            $('#experience-regionf').click(function(){
                $('.experience-region-div').toggle();
            });

            $('#publish-statusf').click(function(){
                $('.publish-status-div').toggle();
            });

            $('#candidate-statusf').click(function(){
                $('.cand-status-div').toggle();
            });

            $('#create-datef').click(function(){
                $('.created-date-div').toggle();
            });

            $('#sourcing-date-rangef').click(function(){
                $('.sourcing-date-div').toggle();
            });


            $('#medical-expiry-datef').click(function(){
                $('.medical-expiry-div').toggle();
            });

            $('#new-candidate-statusf').click(function(){
                $('.new-candidate-div').toggle();
            });

            $('#ready-for-published-statusf').click(function(){
                $('.ready-for-publish-div').toggle();
            });

            $('#cand-medical-statusf').click(function(){
                $('.cand-medical-status-div').toggle();
            });

            $('#cand-payment-statusf').click(function(){
                $('.cand-payment-status-div').toggle();
            });


             $('#cand-expect-location-statusf').click(function(){
                $('.cand-expect-location-div').toggle();
            });

            // Basic Select
            $('#default-chk').click(function(){
                if ($('#new-candidate-statusf:checkbox:checked').length > 0) {
                    $('#new-candidate-statusf').trigger('click');
                }

                if ($('#ready-for-published-statusf:checkbox:checked').length > 0) {
                    $('#ready-for-published-statusf').trigger('click');
                }

                if ($('#cand-medical-statusf:checkbox:checked').length > 0) {
                    $('#cand-medical-statusf').trigger('click');
                }

                // start here
                 if ($('#cand-expect-location-statusf:checkbox:checked').length > 0) {
                    $('#cand-expect-location-statusf').trigger('click');
                }
                // end here

                if ($('#cand-payment-statusf:checkbox:checked').length > 0) {
                    $('#cand-payment-statusf').trigger('click');
                }

                if ($('#medical-expiry-datef:checkbox:checked').length > 0) {
                    $('#medical-expiry-datef').trigger('click');
                }

                if ($('#create-datef:checkbox:checked').length > 0) {
                    $('#create-datef').trigger('click');
                }

                if ($('#sourcing-date-rangef:checkbox:checked').length > 0) {
                    $('#sourcing-date-rangef').trigger('click');
                }

                if ($('#candidate-statusf:checkbox:checked').length > 0) {
                    $('#candidate-statusf').trigger('click');
                }

                if ($('#publish-statusf:checkbox:checked').length > 0) {
                    $('#publish-statusf').trigger('click');
                }

                if ($('#cityf:checkbox:checked').length > 0) {
                    $('#cityf').trigger('click');
                }

                if ($('#religionf:checkbox:checked').length > 0) {
                    $('#religionf').trigger('click');
                }

                if ($('#create-byf:checkbox:checked').length > 0) {
                    $('#create-byf').trigger('click');
                }

                if ($('#careofff:checkbox:checked').length > 0) {
                    $('#careofff').trigger('click');
                }


                if($('#pass-typef:checkbox:checked').length > 0){

                }else{
                    $('#pass-typef').trigger('click');
                }

                if($('#job-typef:checkbox:checked').length > 0){

                }else{
                    $('#job-typef').trigger('click');
                }

                if($('#regionf:checkbox:checked').length > 0){

                }else{
                    $('#regionf').trigger('click');
                }
                if($('#experience-regionf:checkbox:checked').length > 0){

                }else{
                    $('#experience-regionf').trigger('click');
                }
            });

            // All Select
            $('#all-chk').click(function(){
                $('input[name="pass-typef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="job-typef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="create-byf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="careofff"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="religionf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="cityf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="regionf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="experience-regionf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="publish-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="candidate-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="create-datef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="sourcing-date-rangef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="medical-expiry-datef"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="new-candidate-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="ready-for-published-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="cand-medical-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });

                $('input[name="cand-payment-statusf"]').each(function () {
                    if ($(this).prop('checked')) {

                    }else{
                        $(this).trigger('click');
                    }
                });
            });

            // Uncheck All
            $('#all-unchk').click(function(){
                if($('#pass-typef:checkbox:checked').length > 0){
                    $('#pass-typef').trigger('click');
                }
                if($('#job-typef:checkbox:checked').length > 0){
                    $('#job-typef').trigger('click');
                }
                if($('#create-byf:checkbox:checked').length > 0){
                    $('#create-byf').trigger('click');
                }
                if($('#careofff:checkbox:checked').length > 0){
                    $('#careofff').trigger('click');
                }
                if($('#religionf:checkbox:checked').length > 0){
                    $('#religionf').trigger('click');
                }
                if($('#cityf:checkbox:checked').length > 0){
                    $('#cityf').trigger('click');
                }
                if($('#regionf:checkbox:checked').length > 0){
                    $('#regionf').trigger('click');
                }
                if($('#experience-regionf:checkbox:checked').length > 0){
                    $('#experience-regionf').trigger('click');
                }
                if($('#publish-statusf:checkbox:checked').length > 0){
                    $('#publish-statusf').trigger('click');
                }
                if($('#candidate-statusf:checkbox:checked').length > 0){
                    $('#candidate-statusf').trigger('click');
                }
                if($('#create-datef:checkbox:checked').length > 0){
                    $('#create-datef').trigger('click');
                }

                if($('#sourcing-date-rangef:checkbox:checked').length > 0){
                    $('#sourcing-date-rangef').trigger('click');
                }

                if($('#medical-expiry-datef:checkbox:checked').length > 0){
                    $('#medical-expiry-datef').trigger('click');
                }
                if($('#new-candidate-statusf:checkbox:checked').length > 0){
                    $('#new-candidate-statusf').trigger('click');
                }
                if($('#ready-for-published-statusf:checkbox:checked').length > 0){
                    $('#ready-for-published-statusf').trigger('click');
                }
                if($('#cand-medical-statusf:checkbox:checked').length > 0){
                    $('#cand-medical-statusf').trigger('click');
                }
                if($('#cand-payment-statusf:checkbox:checked').length > 0){
                    $('#cand-payment-statusf').trigger('click');
                }

            });

            // Update Checkbox
            $('#update-chk').click(function(){
                var pass_typef = $('#pass-typef:checked').val();
                var job_typef = $('#job-typef:checked').val();
                var careofff = $('#careofff:checked').val();
                var create_byf = $('#create-byf:checked').val();
                var religionf = $('#religionf:checked').val();
                var cityf = $('#cityf:checked').val();
                var regionf = $('#regionf:checked').val();
                var experience_regionf = $('#experience-regionf:checked').val();
                var publish_statusf = $('#publish-statusf:checked').val();
                var candidate_statusf = $('#candidate-statusf:checked').val();
                var create_datef = $('#create-datef:checked').val();
                var sourcing_date_rangef = $('#sourcing-date-rangef:checked').val();
                var medical_expiry_datef = $('#medical-expiry-datef:checked').val();
                var new_candidate_statusf = $('#new-candidate-statusf:checked').val();
                var ready_for_published_statusf = $('#ready-for-published-statusf:checked').val();
                var cand_medical_statusf = $('#cand-medical-statusf:checked').val();
                var cand_payment_statusf = $('#cand-payment-statusf:checked').val();

                jQuery.ajax({
                    url:"{{ url('admin/candidate/filterList/update') }}",
                    method: 'post',
                    type: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        pass_typef: pass_typef,
                        job_typef: job_typef,
                        careofff: careofff,
                        create_byf: create_byf,
                        religionf: religionf,
                        cityf: cityf ,
                        regionf: regionf,
                        experience_regionf: experience_regionf ,
                        publish_statusf : publish_statusf ,
                        candidate_statusf : candidate_statusf ,
                        create_datef : create_datef ,
                        sourcing_date_rangef: sourcing_date_rangef,
                        medical_expiry_datef : medical_expiry_datef ,
                        new_candidate_statusf : new_candidate_statusf ,
                        ready_for_published_statusf : ready_for_published_statusf ,
                        cand_medical_statusf : cand_medical_statusf ,
                        cand_payment_statusf: cand_payment_statusf
                    },
                    success: function(data){
                        if(data){
                            toastr['success']('Filter updated successfully', 'Success', { hideDuration: 3000 });
                            // $('#filter_final_div').load(location.href + ' #filter_final_div');
                        }
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
        $('#updateWorkCity').on('show.bs.modal',function(e){
            var candId = $(e.relatedTarget).data('id');
            var wpcityId = $(e.relatedTarget).data('wpcity-id');
            $('#workCityCandID').val(candId);
            $('#workCityWpcityID').val(wpcityId);
        });

        // Select2 must be (re)initialized after the modal is fully shown, with
        // dropdownParent set to the modal itself - otherwise the dropdown panel
        // is positioned before the modal is visible and renders detached/behind it.
        $('#updateWorkCity').on('shown.bs.modal',function(e){
            var $select = $('#workCityWpcityID');
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.select2({
                dropdownParent: $('#updateWorkCity'),
                width: '100%'
            });
        });
        });
    </script>

    <script>
        $(document).ready(function(){
        $('#deleteStaff').on('show.bs.modal',function(e){
            var staff_id =  $(e.relatedTarget).data('id');
            // alert(staff_id);
            $('#delStaffID').val(staff_id);
            // check already exist in any table or nor
            $.ajaxSetup({
            headers:{
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
            });

            jQuery.ajax({
                url : '{{ url('admin/candidate/check/exist') }}',
                method: "POST",
                type: "html",
                data: {
                    "id": staff_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data){
                    if (data) {
                    $('#disbtn').prop('disabled',true);
                    } else {
                    $('#disbtn').prop('disabled',false);
                    }
                }
            });
        });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('.bulkpublish').on('click',function(e){
                var allVals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allVals.push($(this).attr('data-id'));
                });

                if (allVals.length <= 0) {
                    alert("Please select atleast one checkbox");
                    location.reload();
                }else{
                    var count_id = allVals.length;
                    var join_selected_values = allVals.join(",");

                    $('#idsv').val(join_selected_values);
                    $('#countcan').text(count_id);

                }
            });
        });
    </script>

    <script>
        $(document).ready(function(){

            $('#sharedCV').on('show.bs.modal',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });

                if (allselectedvals <= 0) {
                    alert("Please select atleast one checkbox");
                    location.reload();
                } else {
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    $('#candcvs_id').val(join_all_selected_values);
                }



            });

            // $('.bulksharedcv').on('click',function(e){
            //     var allselectedvals = [];
            //     $('.dt-checkboxes:checked').each(function(){
            //         allselectedvals.push($(this).attr('data-id'));
            //     });

            //     if (allselectedvals <= 0) {
            //         alert("Please select atleast one checkbox");
            //         location.reload();
            //     } else {
            //         var countc_id = allselectedvals.length;
            //         var join_all_selected_values = allselectedvals.join(",");

            //         $('#candcvs_id').val(join_all_selected_values);
            //     }

            // });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('.bulkdownloadcv').on('click',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });


                if (allselectedvals <= 0) {
                    alert("Please select atleast one checkbox");
                    location.reload();
                } else {
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    $('#candcv_id').val(join_all_selected_values);
                }

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#downloadCvbtn').on('click',function(){
                var partnerID = $('#partner_id').val();
                var candCVID = $('#candcv_id').val();

                const candIDs = candCVID.split(",");
                if (partnerID != '') {
                    for (let i = 0; i < candIDs.length; i++) {
                        // const element = array[i];
                        var cvUrl = '{{ url("admin/download/candidate-cv") }}/'+partnerID+'/'+candIDs[i];
                        window.open(cvUrl,'_blank');
                    }

                    $('#errorPartner').text('');
                    $('#downloadCvbtn').attr('disabled',false);
                } else {
                    $('#errorPartner').text('Please select office name!');
                    $('#downloadCvbtn').attr('disabled',true);
                }



                // var cvUrl = '{{ url("admin/singlecv/download") }}/'+partnerID
                // window.open(cvUrl,'_blank');

            });

            // $('#partner_id').on('change',function(){
            //     var pID = $(this).val();
            //     if (pID != '') {
            //         $('#errorPartner').text('');
            //         $('#downloadCvbtn').attr('disabled',false);
            //     } else {
            //         $('#errorPartner').text('Please select office name!');
            //         $('#downloadCvbtn').attr('disabled',true);
            //     }
            // }); 15072023
        });
    </script>

    <script>
        $(document).ready(function(){
            // Click Event on Master Check
            $(document).on('click','.checkboxSelectAll',function(){
                var listcheckitem = $('.listitem :checkbox');
                var isMasterChecked = $(this).is(":checked");

                // row checked list
                var checkedList = listcheckitem.length;

                // alert(checkedList);

                if(isMasterChecked){
                    if (checkedList > 0) {
                        $('.bulkactions').prop("disabled",false);
                    } else {
                        $('.bulkactions').prop("disabled",true);
                    }

                }else{
                    $('.bulkactions').prop("disabled",true);
                }
                listcheckitem.prop("checked", isMasterChecked);
            });

            // Change Event on each item checkbox
            $(document).on('change','.listitem :checkbox',function(){
                var listcheckitem = $('.listitem :checkbox');
                var masterCheck = $('.checkboxSelectAll');
                // Total Checkboxes in list
                var totalItems = listcheckitem.length;
                // Total Checked Checkboxes in list
                var checkedItems = listcheckitem.filter(":checked").length;
                //If all are checked
                if (totalItems == checkedItems) {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", true);
                    $('.bulkactions').prop("disabled",false);
                }
                // Not all but only some are checked
                else if (checkedItems > 0 && checkedItems < totalItems) {
                    masterCheck.prop("indeterminate", true);
                    $('.bulkactions').prop("disabled",false);
                }
                //If none is checked
                else {
                    masterCheck.prop("indeterminate", false);
                    masterCheck.prop("checked", false);
                    $('.bulkactions').prop("disabled",true);
                }
            });
        });

        $(document).on('change', 
            '#by-pass-type, #by-salary, #by-job-type, #by-careoff, #by-create-by, #by-religion, #by-city, #by-region, #by-experience-region, #by-publish-status, #by-cand-status, #by-cand-medical-status, #by-cand-payment-status, #expwp_id',
            function () {
                updateFilterIndicator();
            }
        );

        $(document).on('change keyup',
            '#by-created-date, #by-medical-expiry-date, #by-sourcing-date',
            function () {
                updateFilterIndicator();
            }
        );

        $(document).ready(function () {
            updateFilterIndicator();
        });



        function updateFilterIndicator() {

            let isFiltered =
                ($('#by-pass-type').val() && $('#by-pass-type').val().length > 0) ||
                ($('#by-salary').val() && $('#by-salary').val().length > 0) ||
                ($('#by-job-type').val() && $('#by-job-type').val().length > 0) ||
                ($('#by-careoff').val() && $('#by-careoff').val().length > 0) ||
                ($('#by-create-by').val() && $('#by-create-by').val().length > 0) ||
                ($('#by-religion').val() && $('#by-religion').val().length > 0) ||
                ($('#by-city').val() && $('#by-city').val().length > 0) ||
                ($('#by-region').val() && $('#by-region').val().length > 0) ||
                ($('#by-experience-region').val() && $('#by-experience-region').val().length > 0) ||
                ($('#by-publish-status').val() && $('#by-publish-status').val().length > 0) ||
                ($('#by-cand-status').val() && $('#by-cand-status').val().length > 0) ||
                ($('#by-cand-medical-status').val() && $('#by-cand-medical-status').val().length > 0) ||
                ($('#by-cand-payment-status').val() && $('#by-cand-payment-status').val().length > 0) ||
                ($('#expwp_id').val() && $('#expwp_id').val().length > 0) ||

                // Date fields (text inputs)
                ($('#by-created-date').val() && $('#by-created-date').val().trim() !== '') ||
                ($('#by-medical-expiry-date').val() && $('#by-medical-expiry-date').val().trim() !== '') ||
                ($('#by-sourcing-date').val() && $('#by-sourcing-date').val().trim() !== '');

            if (isFiltered) {
                $('.filterpanel .filter-indicator')
                    .removeClass('d-none')
                    .addClass('d-inline-block');
            } else {
                $('.filterpanel .filter-indicator')
                    .removeClass('d-inline-block')
                    .addClass('d-none');
            }
        }

    $(document).ready(function () {

        const $recruitmentPartner = $('#recruitment_partner');
        const $employer = $('#employer');
        const $profession = $('#profession');

        const $visaEditId = $('#visaeditid');
        const $fromReq = $('#fromreq');
        const $proffId = $('#proff_id');


        // Required elements
        if (
            !$recruitmentPartner.length ||
            !$employer.length ||
            !$profession.length
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Select2 Dropdown Parent
        |--------------------------------------------------------------------------
        */
        const $modal = $recruitmentPartner.closest('.modal');

        const dropdownParent = $modal.length
            ? $modal
            : $(document.body);


        /*
        |--------------------------------------------------------------------------
        | Initialize Select2
        |--------------------------------------------------------------------------
        */
        function initSelect2($element, placeholder, disabled = false) {

            $element.select2({
                placeholder: placeholder,
                allowClear: true,
                width: '100%',
                dropdownParent: dropdownParent
            });

            $element.prop('disabled', disabled);
        }


        /*
        |--------------------------------------------------------------------------
        | Reset Select2
        |--------------------------------------------------------------------------
        */
        function resetSelect2($element, placeholder) {

            $element
                .empty()
                .append(
                    new Option(
                        placeholder,
                        '',
                        false,
                        false
                    )
                )
                .val(null)
                .trigger('change')
                .prop('disabled', true);
        }


        /*
        |--------------------------------------------------------------------------
        | Set Loading
        |--------------------------------------------------------------------------
        */
        function setLoading($element) {

            $element
                .empty()
                .append(
                    new Option(
                        'Loading...',
                        '',
                        false,
                        false
                    )
                )
                .val(null)
                .trigger('change')
                .prop('disabled', true);
        }


        /*
        |--------------------------------------------------------------------------
        | Load Select Options
        |--------------------------------------------------------------------------
        */
        function loadOptions(options) {

            const {
                $element,
                url,
                data,
                placeholder,
                emptyText
            } = options;


            setLoading($element);


            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                data: data,

                success: function (response) {

                    const results = response.results || [];

                    $element.empty();


                    if (response.success && results.length > 0) {

                        $element.append(
                            new Option(
                                placeholder,
                                '',
                                false,
                                false
                            )
                        );


                        $.each(results, function (_, item) {

                            $element.append(
                                new Option(
                                    item.text,
                                    item.id,
                                    false,
                                    false
                                )
                            );

                        });


                    } else {

                        $element.append(
                            new Option(
                                emptyText,
                                '',
                                false,
                                false
                            )
                        );
                    }


                    $element
                        .val(null)
                        .trigger('change')
                        .prop('disabled', false);
                },


                error: function (xhr) {

                    console.error(
                        `Failed to load ${placeholder}:`,
                        xhr.responseText
                    );


                    $element
                        .empty()
                        .append(
                            new Option(
                                `Unable to load ${placeholder.toLowerCase()}`,
                                '',
                                false,
                                false
                            )
                        )
                        .val(null)
                        .trigger('change')
                        .prop('disabled', false);
                }
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Recruitment Partner - Select2 AJAX
        |--------------------------------------------------------------------------
        | Reusable initializer so every Recruitment Partner dropdown in this modal
        | (Published For Selection, Selected, Visa Received) shares one AJAX source.
        */
        function initRecruitmentPartnerSelect2($element) {

            if (!$element || !$element.length) {
                return;
            }

            var $elModal = $element.closest('.modal');
            var elDropdownParent = $elModal.length ? $elModal : dropdownParent;

            $element.select2({

                placeholder: 'Select Recruitment Partner',
                allowClear: true,
                width: '100%',
                minimumInputLength: 0,
                dropdownParent: elDropdownParent,

                ajax: {
                    url: "{{ route('admin.recruitment-partners.search') }}",
                    type: 'GET',
                    dataType: 'json',
                    delay: 300,
                    cache: true,

                    data: function (params) {

                        return {
                            search: params.term || '',
                            page: params.page || 1
                        };
                    },

                    processResults: function (data, params) {

                        params.page = params.page || 1;

                        return {
                            results: data.results || [],

                            pagination: {
                                more: data.pagination?.more || false
                            }
                        };
                    }
                }
            });
        }

        initRecruitmentPartnerSelect2($recruitmentPartner);
        initRecruitmentPartnerSelect2($('#recruitment_partner_selected'));
        initRecruitmentPartnerSelect2($('#recruitment_partner_published'));


        /*
        |--------------------------------------------------------------------------
        | Flight From/To City - Select2 (server-rendered options, no AJAX)
        |--------------------------------------------------------------------------
        */
        $('#flight_from_city, #flight_to_city').select2({
            placeholder: 'Select City',
            allowClear: true,
            width: '100%',
            dropdownParent: dropdownParent
        });


        /*
        |--------------------------------------------------------------------------
        | Flight Date & Time - flatpickr
        |--------------------------------------------------------------------------
        */
        if ($('#flight_datetime').length) {
            $('#flight_datetime').flatpickr({
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                time_24hr: true
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Employer - Select2
        |--------------------------------------------------------------------------
        */
        initSelect2(
            $employer,
            'Select Employer',
            true
        );


        /*
        |--------------------------------------------------------------------------
        | Profession - Select2
        |--------------------------------------------------------------------------
        */
        initSelect2(
            $profession,
            'Select Profession',
            true
        );


        /*
        |--------------------------------------------------------------------------
        | Recruitment Partner Change
        |--------------------------------------------------------------------------
        */
        $recruitmentPartner.on('change', function () {

            const partnerId = $(this).val();


            // Reset hidden fields
            $visaEditId.val('');
            $proffId.val('');


            // Reset Employer
            resetSelect2(
                $employer,
                'Select Employer'
            );


            // Reset Profession
            resetSelect2(
                $profession,
                'Select Profession'
            );


            // No partner selected
            if (!partnerId) {
                return;
            }


            // Load Employers
            loadOptions({

                $element: $employer,

                url: "{{ route('admin.employers.by.partner') }}",

                data: {
                    partner_id: partnerId
                },

                placeholder: 'Select Employer',

                emptyText: 'No employer found'
            });

        });


        /*
        |--------------------------------------------------------------------------
        | Employer Change
        |--------------------------------------------------------------------------
        */
        $employer.on('change', function () {

            const employerId = $(this).val();


            // Set visaeditid = employerpluses.id
            $visaEditId.val(employerId || '');


            // Reset profession ID
            $proffId.val('');


            // Reset Profession
            resetSelect2(
                $profession,
                'Select Profession'
            );


            // No employer selected
            if (!employerId) {
                return;
            }


            // Load Professions
            loadOptions({

                $element: $profession,

                url: "{{ route('admin.professions.by.employer') }}",

                data: {
                    employer_id: employerId
                },

                placeholder: 'Select Profession',

                emptyText: 'No profession found'
            });

        });


        /*
        |--------------------------------------------------------------------------
        | Profession Change
        |--------------------------------------------------------------------------
        */
        $profession.on('change', function () {

            const professionId = $(this).val();

            // Set proff_id = selected profession ID
            $proffId.val(professionId || '');

        });


        /*
        |--------------------------------------------------------------------------
        | Visa Received - Toggle Recruitment Partner / Employer / Profession
        |--------------------------------------------------------------------------
        | Hidden and not required by default (Visa Received = No). Shown and
        | required only when Visa Received = Yes.
        */
        function toggleVisaReceivedFields(isYes) {

            const $conditionalSelects = $recruitmentPartner.add($employer).add($profession);

            if (isYes) {

                $('.visaReceivedConditionalField').show();
                $conditionalSelects.prop('required', true);

            } else {

                $('.visaReceivedConditionalField').hide();
                $conditionalSelects.prop('required', false);

                // Clear any leftover validation error styling since the fields are no longer required
                $conditionalSelects.each(function () {

                    $(this)
                        .next('.select2-container')
                        .find('.select2-selection')
                        .css('border-color', '');

                    $(this)
                        .next('.select2-container')
                        .next('.text-danger')
                        .remove();
                });
            }
        }

        $(document).on('change', '#visareceived input[name="visa_received"]', function () {
            toggleVisaReceivedFields($(this).val() == '1');
        });


        /*
        |--------------------------------------------------------------------------
        | Select2 Validation
        |--------------------------------------------------------------------------
        */
        window.validateSelect2 = function ($field, message) {

            const value = $field.val();

            // Remove previous error
            $field
                .next('.select2-container')
                .find('.select2-selection')
                .css('border-color', '');

            $field
                .next('.select2-container')
                .next('.text-danger')
                .remove();


            if (!value) {

                $field
                    .next('.select2-container')
                    .find('.select2-selection')
                    .css('border-color', '#ea5455');


                $field
                    .next('.select2-container')
                    .after(
                        $('<span>', {
                            class: 'text-danger',
                            text: message
                        })
                    );

                return false;
            }


            return true;
        }


        /*
        |--------------------------------------------------------------------------
        | Visa Received - Submit Validation
        |--------------------------------------------------------------------------
        */
        $(document).on('click', '.VisaReceivedBtn', function (e) {

            let isValid = true;

            // Recruitment Partner, Employer & Profession are required only when Visa Received = Yes
            const visaReceivedYes = $('input[name="visa_received"]:checked').val() == '1';

            const fields = [
                {
                    selector: '#recruitment_partner',
                    message: 'Please select Recruitment Partner.'
                },
                {
                    selector: '#employer',
                    message: 'Please select Employer.'
                },
                {
                    selector: '#profession',
                    message: 'Please select Profession.'
                }
            ];


            if (visaReceivedYes) {

                $.each(fields, function (_, field) {

                    const valid = validateSelect2(
                        $(field.selector),
                        field.message
                    );

                    if (!valid) {
                        isValid = false;
                    }
                });
            }


            /*
            |--------------------------------------------------------------------------
            | Set Form Values
            |--------------------------------------------------------------------------
            */
            if (isValid && visaReceivedYes) {

                // Employer ID
                $visaEditId.val(
                    $employer.val() || ''
                );


                // Employer type
                $fromReq.val('employerplus');


                // Profession ID
                $proffId.val(
                    $profession.val() || ''
                );

            } else if (isValid) {

                // Visa Received = No: these fields are not applicable
                $visaEditId.val('');
                $proffId.val('');
            }


            /*
            |--------------------------------------------------------------------------
            | Stop Form Submit
            |--------------------------------------------------------------------------
            */
            if (!isValid) {

                e.preventDefault();

                return false;
            }

        });


        /*
        |--------------------------------------------------------------------------
        | Remove Validation Error On Change
        |--------------------------------------------------------------------------
        */
        $('#recruitment_partner, #employer, #profession')
            .on('change', function () {

                const $select = $(this);

                $select
                    .next('.select2-container')
                    .find('.select2-selection')
                    .css('border-color', '');

                $select
                    .next('.select2-container')
                    .next('.text-danger')
                    .remove();

            });

    });
                        

    </script>


@endsection
