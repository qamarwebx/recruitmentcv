@extends('layout.admin.admin_layout')

@section('title','All Contact')

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
        .form-check-inline{
            margin-right: 5px !important;
        }
    </style>

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row top-content-div">
                            <div class="col-md-4">
                                <div class="d-flex align-items-start gap-4">
                                    <div>
                                        @if ($post->profile_pic != '')
                                            <img src="" alt="">
                                        @else
                                            <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar"/>
                                        @endif
                                    </div>
                                    <div class="">
                                        <h5 class="mb-1">{{ $post->full_name }}</h5>
                                        <span>
                                            <i class="ti ti-map-pin"></i>
                                            @if($post->state_id != '')
                                                {{ $post->state->name }},
                                            @endif

                                            @if($post->city_id != '')
                                                {{ $post->city->name }},
                                            @endif
                                            @if ($post->country_id != '')
                                                {{ $post->country->name }}
                                            @endif
                                             </span>


                                        <div class="mt-2">
                                            @if ($post->fb_link == '')
                                                <a href="" target="_blank">
                                                    <div class="badge bg-label-primary" style="border-radius: 25px;">
                                                        <span><i class="ti ti-brand-facebook"></i></span>
                                                    </div>
                                                </a>
                                            @endif
                                            @if ($post->tw_link == '')
                                                <a href="" target="_blank">
                                                    <div class="badge bg-label-info" style="border-radius: 25px;">
                                                        <span><i class="ti ti-brand-twitter"></i></span>
                                                    </div>
                                                </a>
                                            @endif
                                            @if ($post->linkedin_link == '')
                                                <a href="" target="_blank">
                                                    <div class="badge bg-label-primary" style="border-radius: 25px;">
                                                        <span><i class="ti ti-brand-linkedin"></i></span>
                                                    </div>
                                                </a>
                                            @endif
                                            <div class="btn-group">
                                                <label class="dropdown-toggle lifecy-stage-label" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-brand-whatsapp ti-md"></i>
                                                </label>

                                                @php
                                                    // $fields = [
                                                    //     'secondary_no_wsp' => 'primary_con_dial_code',
                                                    //     'primary_no_wsp' => 'primary_no_wsp_dial_code',
                                                    //     'mobile_no1_wsp' => 'mobile_no1_wsp_dial_code',
                                                    //     'mobile_no2_wsp' => 'mobile_no2_wsp_dial_code',
                                                    // ];

                                                    $fields = [
                                                        'primary_no_wsp',
                                                        'secondary_no_wsp',
                                                        'mobile_no1_wsp',
                                                        'mobile_no2_wsp',
                                                    ];
                                                @endphp

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                    {{-- @foreach ($fields as $number => $dialcode)
                                                        @if ($post->$number != '')
                                                            @if (empty($post->$dialcode))
                                                                @php
                                                                    $contact_no = $post->$number;
                                                                @endphp
                                                            @else
                                                                @php
                                                                    $contact_no = $post->$dialcode.''.$post->$number;
                                                                @endphp
                                                            @endif
                                                            <a class="dropdown-item" href="https://wa.me/{{ $contact_no }}" target="_blank"> {{ $contact_no }}</a>

                                                        @endif
                                                    @endforeach --}}

                                                    @foreach ($fields as $number)
                                                        @if ($post->$number != '')

                                                            <a class="dropdown-item" href="https://wa.me/{{ $post->$number }}" target="_blank"> {{ $post->$number }}</a>

                                                        @endif
                                                    @endforeach

                                                    {{-- @if ($post->primary_no_wsp != '')
                                                        <a class="dropdown-item" href="https://wa.me/{{ $post->primary_no_wsp }}" target="_blank"> {{ $post->primary_no_wsp }}</a>
                                                    @endif
                                                    @if ($post->mobile_no1_wsp != '')
                                                        <a class="dropdown-item" href="https://wa.me/{{ $post->mobile_no1_wsp }}" target="_blank"> {{ $post->mobile_no1_wsp }}</a>
                                                    @endif
                                                    @if ($post->mobile_no2_wsp != '')
                                                        <a class="dropdown-item" href="https://wa.me/{{ $post->mobile_no2_wsp }}" target="_blank"> {{ $post->mobile_no2_wsp }}</a>
                                                    @endif
                                                    @if ($post->mobile_no3_wsp != '')
                                                        <a class="dropdown-item" href="https://wa.me/{{ $post->mobile_no3_wsp }}" target="_blank"> {{ $post->mobile_no3_wsp }}</a>
                                                    @endif --}}
                                                </div>
                                            </div>
                                        </div>

                                    </div>



                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <button class="btn btn-sm btn-success mb-1" data-bs-toggle="offcanvas" data-bs-target="#campaignMessage"><i class="ti ti-brand-whatsapp ti-xs"></i> Send Message</button>
                                        <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="offcanvas" data-bs-target="#updateReg" data-id="{{ $post->id }}"><i class="ti ti-pencil ti-xs"></i> Edit</button>
                                        {{-- <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#transferleadowner"><i class="ti ti-refresh ti-xs"></i> Transfer Lead Owner</button> --}}
                                        {{-- <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#transfercareoff"><i class="ti ti-refresh ti-xs"></i> Transfer Careoff</button> --}}
                                        <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#reminderCP"><i class="ti ti-alarm ti-xs"></i> Reminder</button>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md">
                                        <label for="">Business Type <span class="text-danger">*</span></label>
                                        <div class="btn-group">
                                            <label class="dropdown-toggle" data-bs-target="#updateReg" data-bs-toggle="offcanvas" data-id="{{ $post->id }}" aria-haspopup="true" aria-expanded="false">
                                                @if ($post->lead_type !='')
                                                    <span class="badge rounded-pill bg-label-success">{{ $post->lead_type }}</span>
                                                @else
                                                    <span>Select</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <label for="">Life Cycle Status</label>
                                        <div class="btn-group">
                                            <label class="dropdown-toggle" data-bs-target="#updatelcs" data-bs-toggle="modal" data-id="{{ $post->id }}" aria-haspopup="true" aria-expanded="false">
                                                @if ($post->lcs_id != '')
                                                    <span class="badge rounded-pill bg-label-success">{{ $post->lcs->name }}</span>
                                                @else
                                                    <span>Select</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>

                                    {{-- <div class="col-md">
                                        <label for="">Life Cycle Status</label>
                                        <div class="btn-group">
                                            <label class="dropdown-toggle lifecy-stage-label" type="button" id="dropdownlcs" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                @if ($post->lcs_id != '')
                                                    <span class="badge rounded-pill bg-label-success">{{ $post->lcs->name }}</span>
                                                @else
                                                    <span>Select</span>
                                                @endif
                                            </label>
                                            <div class="dropdown-menu" aria-labelledby="dropdownlcs">
                                                @foreach ($lifecycless as $lifecycles)
                                                    <a href="javascript:void(0);" class="dropdown-item lifecy-stage" data-id="{{ $lifecycles->id }}">{{ $lifecycles->name }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="col-md">
                                        <label for="">Lead Stage</label><br>
                                        <div class="btn-group">
                                            <label class="dropdown-toggle" data-bs-toggle="modal" data-bs-target="#updatelcs" data-id="{{ $post->id }}" aria-haspopup="true" aria-expanded="false">
                                                @if ($post->ls_id != '')
                                                    <span class="badge rounded-pill bg-label-success">{{ $post->ls->name }}</span>
                                                @else
                                                    <span>Select</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md">
                                        <label for="">Lead Stage</label><br>
                                        <div class="btn-group">
                                            <label class="dropdown-toggle llead-stage-label" type="button" id="dropdownls" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                @if ($post->ls_id != '')
                                                    <span class="badge rounded-pill bg-label-success">{{ $post->ls->name }}</span>
                                                @else
                                                    <span>Select</span>
                                                @endif
                                            </label>
                                            <div class="dropdown-menu" aria-labelledby="dropdownls">
                                                @foreach ($leadstages as $leadstage)
                                                    <a href="javascript:voide(0);" class="dropdown-item lead-stage" data-id="{{ $leadstage->id }}">{{ $leadstage->name }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="col-md">
                                        <label for="">Lead Priority</label><br>
                                        <div class="btn-group">
                                            <label class="dropdown-toggle lead-priority-label" type="button" id="dropdownleadp" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                @if ($post->lead_prority != '')
                                                    <span class="badge rounded-pill @if($post->lead_prority == 'Warm') bg-label-success @elseif($post->lead_prority == 'Cold') bg-label-info @elseif($post->lead_prority == 'Hot') bg-label-warning @endif">{{ $post->lead_prority }}</span>
                                                @else
                                                    <span>Select</span>
                                                @endif
                                            </label>
                                            <div class="dropdown-menu" aria-labelledby="dropdownleadp">

                                                <a href="javascript::voide(0);" class="dropdown-item lead-priority">Cold</a>
                                                <a href="javascript::voide(0);" class="dropdown-item lead-priority">Warm</a>
                                                <a href="javascript::voide(0);" class="dropdown-item lead-priority">Hot</a>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <label for="">Opt in <span class="text-danger"></span></label><br>
                                        <div class="btn-group">
                                            <label class="dropdown-toggle" data-bs-target="#editOptin" data-id="{{ $post->id }}" data-bs-toggle="modal" aria-haspopup="true" aria-expanded="false">
                                                @if ($post->optinout !='')
                                                    <span class="badge rounded-pill @if($post->optinout == '1') bg-label-success @else bg-label-warning @endif">@if($post->optinout == '1') Opt in @else Opt Out @endif</span>
                                                @else
                                                    <span>Select</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <ul class="nav nav-tabs nav-fill" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#nav-notes-tab" aria-controls="nav-notes-tab" aria-selected="true">
                                    <i class="tf-icons ti ti-home ti-xs me-1"></i> Notes
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-reminder-tab" aria-controls="nav-reminder-tab" aria-selected="false">
                                    <i class="tf-icons ti ti-user-check ti-xs me-1"></i> Reminder
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-details-tab" aria-controls="nav-details-tab" aria-selected="false">
                                    <i class="tf-icons ti ti-cash ti-xs me-1"></i> Details
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-careoff-tab" aria-controls="nav-careoff-tab" aria-selected="false">
                                    <i class="tf-icons ti ti-file ti-xs me-1"></i> Careoff
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-leadowner-tab" aria-controls="nav-leadowner-tab" aria-selected="false">
                                    <i class="tf-icons ti ti-file ti-xs me-1"></i> Lead Owner
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-whatsapp-tab" aria-controls="nav-whatsapp-tab" aria-selected="false">
                                    <i class="tf-icons ti ti-file ti-xs me-1"></i> Whatsapp / Mail / SMS
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#nav-file-tab" aria-controls="nav-file-tab" aria-selected="false">
                                    <i class="tf-icons ti ti-file ti-xs me-1"></i> Files
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" id="nav-notes-tab" role="tabpanel">
                                <div class="row notes-div-2">
                                    <div class="col-md-4">
                                        <form action="{{ route('admin.allcontact.addnotes') }}" method="POST" id="notesValidation">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" name="contactID" value="{{ $post->id }}">
                                                <div class="col-md-12">
                                                    <label for="add-notes-tab" class="form-label">Notes <span class="text-danger">*</span></label>
                                                    <textarea name="notes" id="add-notes-tab" cols="30" rows="5" class="form-control"></textarea>
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <div class="mb-3">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="conversation_type" value="Normal" id="add-normal-conversation"/>
                                                            <label class="form-check-label" for="add-normal-conversation">Normal</label>
                                                        </div>
                                                       <div class="form-check form-check-inline">
                                                          <input class="form-check-input" type="radio" name="conversation_type" value="Call" id="add-call-conversation"/>
                                                          <label class="form-check-label" for="add-call-conversation">Call</label>
                                                       </div>
                                                       <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="conversation_type" value="Whatsapp" id="add-whatsapp-conversation"/>
                                                            <label class="form-check-label" for="add-whatsapp-conversation">Whatsapp</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="conversation_type" value="Email" id="add-email-conversation"/>
                                                            <label class="form-check-label" for="add-email-conversation">Email</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="conversation_type" value="In-person" id="add-in-person-conversation"/>
                                                            <label class="form-check-label" for="add-in-person-conversation">In person</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                 <div class="col-md-12">
                                                    <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                                 </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="table table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Notes</th>
                                                        <th>Conversion</th>
                                                        <th>Created By</th>
                                                        <th>Created At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($contactnotes as $contactnote)
                                                        <tr>
                                                            <td>{{ $contactnote->notes }}</td>
                                                            <td>@if($contactnote->conversation_type != '') {{ $contactnote->conversation_type }} @else {{ '---' }} @endif</td>
                                                            <td>@if($contactnote->admin_id != '') {{ $contactnote->admin->name }} @else {{ '---' }} @endif</td>
                                                            <td>{{ $contactnote->created_at }}</td>
                                                            <td>
                                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#deletenotes" data-id="{{ $contactnote->id }}"><i class="ti ti-trash ti-sm"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-reminder-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
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
                                                        $i = 1;
                                                    @endphp
                                                    @foreach ($reminders2 as $reminder)
                                                        <tr>
                                                            <td>{{ $i++ }}</td>
                                                            <td>{{ $reminder->title }}</td>
                                                            <td>{{ $reminder->desc }}</td>
                                                            <td>{{ $reminder->reminder_type }}</td>
                                                            <td>{{ date('d M Y',strtotime($reminder->due_date)).' '.$reminder->time }}</td>
                                                            <td>{{ $reminder->careoffname }}</td>
                                                            <td>{{ $reminder->uname }}</td>
                                                            <td>
                                                                @if ($reminder->status == 1)
                                                                    <span class="badge bg-label-success" data-bs-toggle="modal" data-bs-target="#updateReminder" data-id="{{ $reminder->id }}">Active</span>
                                                                @else
                                                                    <span class="badge bg-label-danger">Inactive</span>
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
                            <div class="tab-pane fade" id="nav-details-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-container">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Full Name:</span>
                                                    <span>{{ $data['full_name'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Office Name</span>
                                                    <span>{{ $data['office_name'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Business Type:</span>
                                                    <span>{{ $data['lead_type'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Primary No (Whatsapp):</span>
                                                    <span>{{ $data['primary_no_wsp'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Secondary No:</span>
                                                    <span>{{ $data['secondary_no_wsp'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Mobile No.1:</span>
                                                    <span>{{ $data['mobile_no1_wsp'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Mobile No.2:</span>
                                                    <span>{{ $data['mobile_no2_wsp'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Mobile No.3:</span>
                                                    <span>{{ $data['mobile_no3_wsp'] }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-container">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Primary Email:</span>
                                                    <span>{{ $data['primary_email'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Email 1:</span>
                                                    <span>{{ $data['email0'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Email 2:</span>
                                                    <span>{{ $data['email1'] }}</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Email 3:</span>
                                                    <span>{{ $data['email2'] }}</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Industry:</span>
                                                    <span>{{ $data['industry'] }}</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">City:</span>
                                                    <span>{{ $data['city'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">State:</span>
                                                    <span>{{ $data['state'] }}</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Country:</span>
                                                    <span>{{ $data['country'] }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-container">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Lifecycle Status:</span>
                                                    <span>{{ $data['lifecycle'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Lead Stage:</span>
                                                    <span>{{ $data['leadstage'] }}</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Lead Priority:</span>
                                                    <span>{{ $data['lead_priority'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Subscribe (Optin):</span>
                                                    <span>{{ $data['subscribe'] }}</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Create By:</span>
                                                    <span>{{ $data['createBy'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Owner:</span>
                                                    <span>{{ $data['leadowner'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Careoff:</span>
                                                    <span>{{ $data['careoff'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Dalete By:</span>
                                                    <span>{{ $data['deleteby'] }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="info-container">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Source:</span>
                                                    <span>{{ $data['source'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Gorup:</span>
                                                    <span>{{ $data['groupname'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Status:</span>
                                                    <span>{{ $data['public_status'] }}</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">public:</span>
                                                    <span>{{ $data['allcontact_status'] }}</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Created At</span>
                                                    <span>
                                                        {{ $post->created_at ? date('d-m-Y h:i:s', strtotime($post->created_at)) : '---' }}
                                                    </span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Updated At</span>
                                                    <span>
                                                        {{ $post->updated_at ? date('d-m-Y h:i:s', strtotime($post->updated_at)) : '---' }}
                                                    </span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Staff Updated At</span>
                                                    <span>
                                                        {{ $post->staff_updated_date ? date('d-m-Y h:i:s', strtotime($post->staff_updated_date)) : '---' }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-careoff-tab" role="tabpanel">
                                <div class="row"></div>
                            </div>
                            <div class="tab-pane fade" id="nav-leadowner-tab" role="tabpanel">
                                <div class="row"></div>
                            </div>
                            <div class="tab-pane fade" id="nav-whatsapp-tab" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>For Whatsapp</th>
                                                        <th>Template Name</th>
                                                        <th>Campaign Type</th>
                                                        <th>Date Time</th>
                                                        <th>Status</th>
                                                        <th>Create By</th>
                                                        <th>Send Date</th>
                                                        <th>Track Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($sendcontactmsgs as $sendcontactmsg)
                                                        <tr>
                                                            <td>{{ $sendcontactmsg->for_whatsapp }}</td>
                                                            <td>
                                                                @if ($sendcontactmsg->for_whatsapp == 'meta_whatsapp')
                                                                    {{ $sendcontactmsg->metatempname }}
                                                                @else
                                                                    {{ $sendcontactmsg->ntemp_name }}
                                                                @endif
                                                            </td>
                                                            <td>@if($sendcontactmsg->campaign_type == 1) Now @else Scheduled @endif</td>
                                                            <td>@if($sendcontactmsg->date_time != '') {{ date('d M Y h:i',strtotime($sendcontactmsg->date_time)) }} @else {{ '---' }} @endif</td>
                                                            <td>@if($sendcontactmsg->status == 1) <span class="text-success">Run</span> @else <span class="text-danger">Stop</span> @endif</td>
                                                            <td>{{ $sendcontactmsg->adminname }}</td>
                                                            <td>{{ date('d M Y h:i A',strtotime($sendcontactmsg->created_at)) }}</td>
                                                            <td>
                                                                <span class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#sendmessagestatus" data-id="{{ $sendcontactmsg->id }}">Send Status</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-file-tab" role="tabpanel">
                                <div class="row">

                                    <!-- 🔵 LEFT SIDE (UPLOAD FORM) -->
                                    <div class="col-md-4">

                                        <form id="uploadFileForm" enctype="multipart/form-data">
                                            @csrf

                                            <input type="hidden" name="contact_id" value="{{ $post->id ?? '' }}">

                                            <div class="mb-3">
                                                <label class="form-label">File Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="file_name" placeholder="Enter file name" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Upload File <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control" name="file" required>
                                                <small class="text-muted">Max size: 2 MB</small>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Upload</button>

                                        </form>

                                    </div>

                                    <!-- 🟢 RIGHT SIDE (TABLE LIST) -->
                                    <div class="col-md-8">

                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table table-bordered">
                                                    <tr>
                                                        <th>File Name</th>
                                                        <th>Uploaded By</th>
                                                        <th>Uploaded At</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="fileTableBody">

                                                    @foreach($contactFiles as $file)
                                                    <tr id="fileRow_{{$file->id}}">
                                                        <td>{{ $file->file_name }}</td>
                                                        <td>{{ $file->uploader->name ?? '---' }}</td>
                                                        <td>{{ date('Y-m-d h:i A', strtotime($file->created_at)) }}</td>
                                                        <td>
                                                            <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank" class="btn btn-success btn-sm">Download</a>

                                                            <button class="btn btn-danger btn-sm deleteFileBtn"
                                                                data-id="{{ $file->id }}">
                                                                Delete
                                                            </button>
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


        <!-- Update Optin and Optout Start -->
        <div class="modal fade modal-danger text-left" id="editOptin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel123" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                    <div class="modal-header pb-2">
                        <h5 class="offcanvas-title" id="myModalLabel123">Update Opt-In</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="{{ route('admin.allcontact.optinupdate') }}" id="editOptinValidate">
                        @csrf

                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" name="id" class="form-control" id="editoptinID">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="subscribe" value="1" id="add-subscribe"/>
                                            <label class="form-check-label" for="add-subscribe">Optin</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="subscribe" value="0" id="add-unsubscribe"/>
                                            <label class="form-check-label" for="add-unsubscribe">Optout</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- Update Optin and Optout End -->


        <!-- Delete Notes Start -->
        <div class="modal fade modal-danger text-left" id="deletenotes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel123" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                    <div class="modal-header pb-2">
                        <h5 class="offcanvas-title" id="myModalLabel123">Delete Notes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="{{ route('admin.allcontact.deletenotes') }}" id="deletenotesValidate">
                        @csrf

                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" name="id" class="form-control" id="deletenotesID">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <p class="text-danger">Are You sure to delete?</p>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Notes End -->


        <!-- Send Message Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="campaignMessage" aria-labelledby="campaignMessageLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="campaignMessageLabel" class="offcanvas-title">Send Whatsapp</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="campaignMessageShow" action="{{ route('admin.allcontact.sendwhatsapp',$post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="send-whatsapp-type" class="form-label">For Whatsapp <span class="text-danger">*</span></label>
                                <select name="send_whatsapp_type" id="send-whatsapp-type" class="form-select select2" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="meta_whatsapp" selected>Meta Whatsapp</option>
                                    <option value="normal_whatsapp">Normal Whatsapp</option>
                                </select>
                            </div>
                        </div>
                        <!-- Meta Whatsapp Display Start -->
                        <div class="col-md-4 dismetawhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-name" class="form-label">Meta Template <span class="text-danger">*</span></label>
                                <select name="metatemplate_id" id="send-meta-template-name" class="form-select select2" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($metatemplates as $metatemplate)
                                        <option value="{{ $metatemplate->id }}">{{ $metatemplate->template_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 dismetawhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-type" class="form-label">Contact Type</label>
                                <select name="contact_type[]" id="send-meta-template-contact-type" class="form-select select2" multiple>
                                    <option value="all" selected>All</option>
                                    <option value="owner">Owner</option>
                                    <option value="Primary">Primary</option>
                                    <option value="Secondary">Secondary</option>
                                </select>
                            </div>
                        </div>

                        <!-- Meta Whatsapp Display End -->
                        <!-- Normal Whatsapp Display Start -->
                        <div class="col-md-4 disnormalwhatsapp" style="display: none">
                            <div class="mb-3">
                                <label for="send-template-name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                <select name="template_id" id="send-template-name" class="form-select select2" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($normaltemplates as $normaltemplate)
                                        <option value="{{ $normaltemplate->id }}">{{ $normaltemplate->template_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 disnormalwhatsapp" style="display: none">
                            <div class="mb-3">
                                <label class="form-label" for="send-template-whatsapp-api">Select Whatsapp API <span class="text-danger">*</span></label>
                                <select name="wapi_id_text[]" id="send-template-whatsapp-api" class="form-select select2" multiple data-placeholder="Select Whatsapp API...">
                                    <option value=""></option>
                                    @foreach ($wapis as $wapi)
                                        <option value="{{ $wapi->id }}">{{ $wapi->mobile_no }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 disnormalwhatsapp" style="display: none">
                            <div class="mb-3">
                                <label for="send-template-contact-type" class="form-label">Contact Type <span class="text-danger">*</span></label>
                                <select name="contact_type_normal" id="send-template-contact-type" class="form-select select2" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="all">All</option>
                                    <option value="owner">Owner</option>
                                    <option value="Primary">Primary</option>
                                    <option value="Secondary">Secondary</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 disnormalwhatsapp" style="display: none">
                            <div class="mb-3">
                                <label for="send-template-personalise-class" class="form-label">Personalise Name</label>
                                <select name="personalise_class" id="send-template-personalise-class" class="form-select select2" data-allow-clear="true" data-placeholder="Select Personalise Class">
                                    <option value=""></option>
                                    <option value="[Company]">Company</option>
                                    <option value="[Business Type]">Business Type</option>
                                    <option value="[Full Name]">Full Name</option>
                                    <option value="[Email]">Email</option>
                                    <option value="[Country]">Country</option>
                                    <option value="[City]">City</option>
                                    <option value="[Phone0]">Phone0</option>
                                    <option value="[Email0]">Email0</option>
                                    <option value="[Phone1]">Phone1</option>
                                    <option value="[Email1]">Email1</option>
                                    <option value="[Phone2]">Phone2</option>
                                    <option value="[Email2]">Email2</option>
                                    <option value="[Unsubscribe]">Unsubscribe</option>
                                </select>
                            </div>
                        </div>
                        <!-- Normal Whatsapp Display End -->
                    </div>
                    <!-- Meta Whatsapp Display Start -->

                    <div class="row dismetawhatsapp">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="send-meta-template-whatsapp-message" class="form-label">Whatsapp Message</label>
                                <textarea name="msg_whatsapp" id="send-meta-template-whatsapp-message" class="form-control" cols="30" rows="14"></textarea>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="send-meta-template-whatsapp-message-ar" class="form-label">Whatsapp Message Arabic</label>
                                <textarea name="msg_whatsapp_ar" id="send-meta-template-whatsapp-message-ar" class="form-control" cols="30" rows="5"></textarea>
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="mb-3">
                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar" id="uploadedAvatar"/>
                            </div>
                        </div>
                    </div>
                    <div class="row dismetawhatsapp">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-campaign-type" class="form-label">Campaign Type <span class="text-danger">*</span></label>
                                <select name="campaign_type" id="add-campaign-type" class="form-select select2" data-allow-clear="true" data-placeholder="Campaign Type...">
                                    <option value=""></option>
                                    <option value="1">Now</option>
                                    <option value="2">Scheduled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="disdateandtime" style="display: none">
                            <div class="mb-3">
                                <label for="add-date-and-time" class="form-label">Date and Time <span class="text-danger">*</span></label>
                                <input type="text" name="date_and_time" id="add-date-and-time" class="form-control flatpickr-datetime" placeholder="Enter date and time...">
                            </div>
                        </div>
                    </div>
                    <!-- Meta Whatsapp Display End -->
                    <!-- Normal Whatsapp Display Start -->
                    <div class="row disnormalwhatsapp" style="display: none">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="send-template-message" class="form-label">Whatsapp Message</label>
                                <textarea name="whatsapp_message" class="form-control" id="send-template-message" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="send-template-message-ar" class="form-label">Whatsapp Message Arabic</label>
                                <textarea name="msg_whatsapp_ar" class="form-control" id="send-template-message-ar" cols="30" rows="6"></textarea>
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2"/>
                            </div>
                        </div>
                    </div>
                    <div class="row disnormalwhatsapp" style="display: none">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-campaign-type2" class="form-label">Campaign Type <span class="text-danger">*</span></label>
                                <select name="campaign_type2" id="add-campaign-type2" class="form-select select2" data-allow-clear="true" data-placeholder="Campaign Type...">
                                    <option value=""></option>
                                    <option value="1">Now</option>
                                    <option value="2">Scheduled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="disdateandtime2" style="display: none">
                            <div class="mb-3">
                                <label for="add-date-and-time2" class="form-label">Date and Time <span class="text-danger">*</span></label>
                                <input type="text" name="date_and_time2" id="add-date-and-time2" class="form-control flatpickr-datetime2" placeholder="Enter date and time...">
                            </div>
                        </div>
                    </div>
                    <!-- Normal Whatsapp Display End -->
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!-- Send Message End -->

        <!-- Reminder Contactp -->
        <div class="modal fade" id="reminderCP" aria-hidden="true" aria-labelledby="reminderCPLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="reminderCPLabel">Created Reminder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.addreminder') }}" method="POST" id="createReminder">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactID" value="{{ $post->id }}">
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
                                        <select name="reminder_type" id="add-reminder-type" class="form-select select2" >
                                            <option value=""></option>
                                            <option value="Follow Up">Follow Up</option>
                                            <option value="Call Reminder">Call Reminder</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add-reminder-due-date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                        <input type="text" name="due_date" id="add-reminder-due-date" class="form-control flatpicker-date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add-reminder-time" class="form-label">Time <span class="text-danger">*</span></label>
                                        <input type="text" name="time" id="add-reminder-time" class="flatpicker-time form-control">
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
        <!-- Reminder Contactp -->

        <!-- Update Reminder Start -->
        <div class="modal fade" id="updateReminder" aria-hidden="true" aria-labelledby="updateReminderLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="updateReminderLabel">Update Reminder</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.updateaddreminder') }}" method="POST" id="updateReminderValidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactID" value="{{ $post->id }}">
                                <input type="hidden" name="careoff_id" value="{{ $post->careoff_id }}">
                                <input type="hidden" name="reminder_id" id="reminder_id">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="create-new-reminder" class="form-label">Create New Reminder</label>
                                        <select name="new_reminder" id="create-new-reminder" class="form-select select2" data-allow-clear="true">
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
                                        <select name="reminder_type" id="update-reminder-type" class="form-select select2" >
                                            <option value=""></option>
                                            <option value="Follow Up">Follow Up</option>
                                            <option value="Call Reminder">Call Reminder</option>
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
        <!-- Update Reminder End -->

        <!-- Edit Contact Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="updateReg" aria-labelledby="updateRegLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="updateRegLabel" class="offcanvas-title">Edit Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form action="{{ route('admin.allcontact.update') }}" id="editUserForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="editID" id="edit_ID" value="{{ $post->id }}">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="lead_type" class="form-label">Business Type</label>
                                <select name="lead_type" id="lead_type" class="form-control select2e lead_type" data-allow-clear="true" data-placeholder="Select Business Type">
                                    <option value="">Select</option>
                                    <option value="Unknown">Other</option>
                                    <option value="Job seeker">Job seeker</option>
                                    <option value="Regular Wakala Party">Regular Wakala Party</option>
                                    <option value="Direct Wakala Candidate">Direct Wakala Candidate</option>
                                    <option value="Agent without office">Agent without office </option>
                                    <option value="Associate with office">Associate with office</option>
                                    <option value="Company">Company</option>
                                    <option value="Trade site Center">Trade site Center</option>
                                  </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-lcs-id" class="form-label">Life Cycle Status</label>
                                <select name="lcs_id" id="edit-lcs-id" class="form-select select2e" data-allow-clear="true" data-placeholder="Select Life Cycle Status">
                                    <option value="">Select</option>
                                    @foreach ($lifecycless as $lcs2)
                                        <option value="{{ $lcs2->id }}">{{ $lcs2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-ls-id" class="form-label">Lead Stage</label>
                                <select name="ls_id" id="edit-ls-id" class="form-select select2e" data-allow-clear="true" data-placeholder="Select Lead Stage">
                                    <option value="">Select</option>
                                    @foreach ($leadstages as $ls2)
                                        <option value="{{ $ls2->id }}">{{ $ls2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4" id="full_name_div">
                            <div class="mb-3">
                                <label for="edit-full-name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" id="edit-full-name" placeholder="Please enter full name" class="form-control" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-4" id="job_title_div">
                            <div class="mb-3">
                              <label for="">Job Title</label>
                              <input type="text" name="job_title" id="job_title" class="form-control" placeholder="Enter Job Title">

                              <div class="job_title_list" id="job_title_list">

                              </div>
                            </div>
                        </div>

                        <div class="col-md-4" id="job_desg_div">
                            <div class="mb-3">
                              <label for="">Designation</label>
                              <input type="text" name="job_desg" id="job_desg" class="form-control" placeholder="Enter Designation">
                              <div id="job_desg_list" class="job_desg_list">

                              </div>
                            </div>
                        </div>

                        <div class="col-md-6" id="company_name_div">
                            <div class="mb-3">
                              <label for="edit-company-name">Company Name <span class="text-danger">*</span></label>
                              <input type="text" name="company_name" class="form-control" id="edit-company-name" placeholder="Enter Company Name">
                            </div>
                        </div>

                        <div class="col-md-4" id="indust_id_div">

                            <div class="mb-3">
                              <label for="edit-industry-id">Industry <span class="text-danger">*</span></label>
                              <select name="indust_id" class="form-control select2e" data-allow-clear="true" data-placeholder="Select Industry" id="edit-industry-id">
                                <option value="">Select</option>
                                @foreach ($industries as $industry)
                                  <option value="{{  $industry->id }}">{{  $industry->name}}</option>
                                @endforeach
                              </select>
                            </div>
                        </div>

                        <div class="col-md-4" id="office_name_div">
                            <div class="mb-3">
                                <label for="edit-agency-name">Agency Name <span class="text-danger">*</span></label>
                                <input type="text" name="office_name" id="edit-agency-name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-mobile-no">Primary Number (Whatsapp Number)
                                    <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>
                                <input type="text" name="primary_no_wsp" id="edit-mobile-no" class="form-control edit-mobile-no">
                                <input type="hidden" name="primary_no_wsp_dial_code" class="mobile-no-dial-code-ed" id="mobile-no-dial-code-ed">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-primary-no">Secondary Number
                                    <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>
                                <input type="text" name="secondary_no_wsp" id="edit-primary-no" class="form-control edit-primary-no">
                                <input type="hidden" name="secondary_no_wsp_dial_code" class="primary-con-dial-code-ed" id="primary-con-dial-code-ed">
                            </div>
                        </div>



                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-phone0">Mobile No.01
                                <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>
                                <input type="text" name="mobile_no1_wsp" id="edit-phone0" class="form-control edit-phone0">
                                <input type="hidden" name="mobile_no1_wsp_dial_code" class="phone0-dial-code-ed" id="phone0-dial-code-ed">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-phone1">Mobile No.02
                                <i class="ti ti-info-circle text-primary ms-1"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Number should be entered with country code, without leading 0 or + sign. Example: 919827063409">
                                    </i>
                                </label>
                                <input type="text" name="mobile_no2_wsp" id="edit-phone1" class="form-control edit-phone1">
                                <input type="hidden" name="mobile_no2_wsp_dial_code" class="phone1-dial-code-ed" id="phone1-dial-code-ed">
                            </div>
                        </div>

                        {{-- <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-phone2">Mobile No.04</label>
                                <input type="text" name="mobile_no3_wsp" id="edit-phone2" class="form-control edit-phone2">
                                <input type="hidden" name="mobile_no3_wsp_dial_code" class="phone2-dial-code-ed" id="phone2-dial-code-ed">
                            </div>
                        </div> --}}

                        <div class="col-md-4">
                            <div class="mb-3">

                            <label for="edit-email">Email 1</label>
                            <input type="text" name="email" id="edit-email" class="form-control">
                        </div>

                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-email0">Email 2</label>
                                <input type="text" name="email0" id="edit-email0" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-email1">Email 3</label>
                                <input type="text" name="email1" id="edit-email1" class="form-control">
                            </div>
                        </div>

                        {{-- <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-email2">Email 4</label>
                                <input type="text" name="email2" id="edit-email2" class="form-control">
                            </div>
                        </div> --}}



                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="edit-country-id">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="edit-country-id" class="form-select select2e" data-placeholder="Select Country" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="edit-city-id">City <span class="text-danger">*</span></label>
                                <select name="city_id" id="edit-city-id" class="form-select select2e" data-placeholder="Select City" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-careoff" class="form-label">Careoff <span class="text-danger">*</span></label>
                                <select name="careoff_id" id="edit-careoff" class="form-select select2e" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($admins as $adminuser3)
                                        <option value="{{ $adminuser3->id }}">{{ $adminuser3->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if (Auth::guard('admin')->user()->user_type == 1)
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit-leadowner" class="form-label">Lead Owner <span class="text-danger">*</span></label>
                                    <select name="leadowner_id" id="edit-leadowner" class="form-select select2e" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                        <option value="">Select</option>
                                        @foreach ($admins as $adminuser4)
                                            <option value="{{ $adminuser4->id }}">{{ $adminuser4->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-group-id" class="form-label">Group</label>
                                <select name="group_id" id="edit-group-id" class="form-select select2e" data-placeholder="Select Group" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($groupallcs as $groupallc)
                                        <option value="{{ $groupallc->id }}">{{ $groupallc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-12" id="descr_div">
                            <div class="mb-3">
                                <label for="edit-descr">Description</label>
                                <textarea name="descr" id="edit-descr" class="form-control" cols="30" rows="5"></textarea>
                            </div>

                        </div>

                    </div>
                    {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button> --}}
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>

                </form>
            </div>
        </div>
        <!-- Edit Contact End -->

        <!-- Update Stage Status Start -->
        <div class="modal fade" id="updatelcs" aria-hidden="true" aria-labelledby="updatelcsLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="updatelcsLabel">Update Lifecycle Status</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.getleadstageupdate') }}" method="POST" id="updateLeadStageValidation">
                    @csrf
                    <input type="hidden" name="allcontactID" id="allcontactID">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="update-lead-cycle-status" class="form-label">Life Cycle Status <span class="text-danger">*</span></label>
                                        <select name="lcs_id" id="update-lead-cycle-status" class="form-control select22" data-allow-clear="true" data-placeholder="Select Lead Cycle Status">
                                            <option value=""></option>
                                            @foreach ($lifecycless as $lcs)
                                                <option value="{{ $lcs->id }}">{{ $lcs->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="update-lead-stage" class="form-label">Lead Stage <span class="text-danger">*</span></label>
                                        <select name="ls_id" id="update-lead-stage" class="form-control select22" data-allow-clear="true" data-placeholder="Select Lead...">

                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            {{-- <button class="btn btn-primary" type="submit">Update Lead Stage</button> --}}
                            <button class="btn btn-primary" type="submit" id="leadStageUpdateBtn">Update Lifecycle Status</button>
                            {{-- <button class="btn btn-primary" type="button" id="leadStageUpdateBtn">Update Lifecycle Status</button> --}}
                        </div>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <!-- Update Stage Status End -->




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
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>
    <script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>
    <script src="{{ asset('admin/assets/pages/validation/allcontact-validation.js') }}"></script>

    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            const select2e = $('.select2e');

            if (select2e.length) {
                select2e.each(function(){
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        dropdownParent: $this.parent()
                    });
                });
            }

            // var select2 = $('.select2');
            // if (select2.length) {
            //     select2.each(function () {
            //         var $this = $(this);
            //         $this.wrap('<div class="position-relative"></div>').select2({
            //         //   placeholder: 'Select value',
            //             dropdownParent: $this.parent()
            //         });
            //     });
            // }

            $('.lifecy-stage').click(function(){
                // var ch_val = this.value;
                var ch_val = $(this).attr('data-id');
                var id = "{{ $post->id }}";

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to update lifecycle stage",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update it!',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ml-1'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('admin.allcontact.updatelifecyclestatus') }}",
                            method: 'post',
                            type: 'html',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                lcs_id: ch_val,
                                id: id
                            },
                            success: function(data){
                                Swal.fire({
                                    title: '',
                                    text: 'Life cycle stage updated successfully!',
                                    icon: 'success',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                }).then(function (result) {
                                    // $('.lifecy-stage-label').html(ch_val);
                                    location.reload();
                                });

                            }

                        });
                    }
                });

            });

            $('.lead-stage').click(function(){
                // var ch_val = this.value;
                var ch_val = $(this).attr('data-id');
                var id = "{{ $post->id }}";

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to update lead priority",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update it!',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-danger ml-1'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {

                        jQuery.ajax({
                            url: "{{ route('admin.allcontact.updateleadstage') }}",
                            method: 'post',
                            type: 'html',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                ls_id: ch_val,
                                id: id
                            },
                            success: function(data){
                                Swal.fire({
                                    title: '',
                                    text: 'Lead priority updated successfully!',
                                    icon: 'success',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                }).then(function (result) {
                                    location.reload();
                                });
                                // toastr['success']('Lead stage updated successfully', 'Success', { hideDuration: 3000 });
                            }
                        });
                    }
                });
            });

            $('.lead-priority').on('click',function(){
                var lead_priority = $(this).text();
                var allcontactID = "{{ $post->id }}";

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to update Lead Priority",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update it!',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then(function(result){

                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.allcontact.leadpriorityupdate') }}",
                            method: "POST",
                            type: "html",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                lead_priority: lead_priority,
                                id: allcontactID
                            },


                            // success: function($data){
                            //     Swal.fire({
                            //         title: 'Updated!',
                            //         text: 'Lead Priority updated successfully!',
                            //         icon: 'success',
                            //         customClass: {
                            //             confirmButton: 'btn btn-primary'
                            //         },
                            //         buttonsStyling: false
                            //     }).then(function(result){
                            //         location.reload();
                            //     });
                            // }

                            success: function($data){
                                Swal.fire({
                                    title: 'Updated!',
                                    text: 'Lead Priority updated successfully!',
                                    icon: 'success',
                                    timer: 2000, // 2 seconds
                                    showConfirmButton: false,
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false

                                });

                                // Reload after 2.2 seconds (slightly longer than timer)
                                setTimeout(function () {
                                    location.reload();
                                }, 2200);

                            }
                        });
                    }

                    // else if (result.dismiss === Swal.DismissReason.cancel || result.dismiss === Swal.DismissReason.backdrop || result.dismiss === Swal.DismissReason.esc) {
                    //     location.reload();
                    // }else{
                    //     location.reload();
                    // }


                });
            });

            $('#deletenotes').on("show.bs.modal",function(e){
                var notesID = $(e.relatedTarget).data('id');
                $('#deletenotesID').val(notesID);
            });

            $('#editOptin').on("show.bs.modal",function(e){
                var allcontactID = $(e.relatedTarget).data('id');
                $('#editoptinID').val(allcontactID);

                $.ajax({
                    url: "{{ route('admin.allcontact.getoptin') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        id: allcontactID
                    },
                    success: function(data){
                        if (data.optinout == 1) {
                            $('#add-subscribe').attr("checked",true);
                            $('#add-unsubscribe').attr("checked", false);
                        } else {
                            $('#add-subscribe').attr("checked",false);
                            $('#add-unsubscribe').attr("checked", true);
                        }
                    }
                });

            });

            $('#send-meta-template-name').on('change',function(){
                var tempID = $(this).val();

                var imgPath = "{{ asset('admin/assets/images/template') }}";
                var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";

                if (tempID != '') {
                    jQuery.ajax({
                        url: "{{ route('admin.whatsapp.metatemplateget') }}",
                        method: 'GET',
                        type: "html",
                        data: {
                            "id" : tempID
                        },
                        success: function(data){
                            $('#send-meta-template-whatsapp-message').val(data.whatsapp_message);
                            $('#send-meta-template-whatsapp-message-ar').val(data.msg_whatsapp_ar);
                            if (data.whatsapp_file != '') {
                                var file_path = imgPath+'/'+data.whatsapp_file;
                                $('.uploadedAvatar').attr("src",file_path);
                            } else {
                                $('.uploadedAvatar').attr("src",blankImg);
                            }
                        }
                    });
                }else{
                    $('#send-meta-template-whatsapp-message').val('');
                    $('#send-meta-template-whatsapp-message-ar').val('');
                    $('.uploadedAvatar').attr("src",blankImg);
                }



            });

            $('#send-template-name').on('change',function(){
                var tempID = $(this).val();
                var imgPath = "{{ asset('admin/assets/images/template') }}";
                var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";

                if (tempID != '') {
                    jQuery.ajax({
                        url : "{{ url('admin/whatsapp-campaign-list/template/get') }}",
                        method: "GET",
                        type: "html",
                        data: {
                            "id": tempID,
                            // "_token": "{{ csrf_token() }}",
                        },
                        success: function(data){
                            $('#send-template-message').val(data.msg_whatsapp);
                            $('#send-template-message-ar').val(data.msg_whatsapp_ar);
                            if (data.file != '') {
                                var file_path = imgPath+'/'+data.file;
                                $('.uploadedAvatar').attr("src",file_path);
                            } else {
                                $('.uploadedAvatar').attr("src",blankImg);
                            }
                        }
                    });
                } else {
                    $('#send-template-message').val('');
                    $('#send-template-message-ar').val('');
                    $('.uploadedAvatar2').attr("src",blankImg);
                }


            });

            $('#send-whatsapp-type').on('change',function(){
                var sendwhatsapptype = $(this).val();
                if (sendwhatsapptype == "meta_whatsapp") {
                    $('.dismetawhatsapp').show();
                    $('.disnormalwhatsapp').hide();
                } else if(sendwhatsapptype == "normal_whatsapp"){
                    $('.dismetawhatsapp').hide();
                    $('.disnormalwhatsapp').show();
                } else {
                    $('.dismetawhatsapp').hide();
                    $('.disnormalwhatsapp').hide();
                }
            });

            $('#send-template-personalise-class').on('change',function(){
                var partvalue = $(this).val();
                navigator.clipboard.writeText(partvalue);
                toastr['success']('Text copied - '+partvalue+'', 'Success', { hideDuration: 3000 });
            });

            $('#add-campaign-type').on('change',function(){
                var sctype = $(this).val();
                if (sctype == '2') {
                    $('#disdateandtime').show();
                }else{
                    $('#disdateandtime').hide();
                }
            });

            $('#add-campaign-type2').on('change',function(){
                var sctype = $(this).val();
                if (sctype == '2') {
                    $('#disdateandtime2').show();
                }else{
                    $('#disdateandtime2').hide();
                }
            });

            $('#add-whatsapp-template').on('change',function(){
                var tempValue = $(this).val();
                if (tempValue == 'custom_temp') {
                    $('#distempname').hide();
                    $('#add-template-name').val('').change();
                } else if (tempValue == 'template') {
                    $('#distempname').show();
                } else {
                    $('#distempname').hide();
                }
            })

            $('.flatpicker-date').flatpickr();
            $('.flatpicker-time').flatpickr({
                enableTime: true,
                noCalendar: true
            });

            $('#updateReminder').on('show.bs.modal',function(e){
                var reminderID = $(e.relatedTarget).data('id');
                $('#reminder_id').val(reminderID);

                $.ajax({
                    url: "{{ route('admin.allcontact.addreminder.edit') }}",
                    method: "GET",
                    type: "html",
                    data:{
                        id: reminderID
                    },
                    success: function(data){
                        $("#update-reminder-title").val(data.title);
                        $("#update-reminder-desc").val(data.desc);
                        $("#update-reminder-type").val(data.reminder_type).trigger('change');
                        $("#update-reminder-due-date").val(data.due_date);
                        $("#update-reminder-time").val(data.time);

                    }
                });

            });

            $('#lead_type').on('change',function(){
                var leadtype = $(this).val();
                if(leadtype == 'Job seeker'){
                $('#job_title_div').show();
                $('#indust_id_div').hide();
                $('#company_name_div').hide();
                }else if (leadtype == 'Company') {
                $('#job_title_div').hide();
                $('#indust_id_div').show();
                $('#company_name_div').show();
                }else{
                $('#job_title_div').hide();
                $('#indust_id_div').hide();
                $('#company_name_div').hide();
                }

            });

            // Trigger Status if avalable
            $('#updatelcs').on("show.bs.modal",function(e){
                var allcontactID = $(e.relatedTarget).data('id');
                $('#allcontactID').val(allcontactID);

                $.ajax({
                    url: "{{ route('admin.allcontact.edit') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        id: allcontactID
                    },
                    success: function(data){
                        console.log(data);

                        $('#update-lead-cycle-status').val(data.post.lcs_id).change();
                    }
                });
            });



            // change lead stage when click lifecycle status
            $('#update-lead-cycle-status').on('change',function(){
                var lcs_id = $(this).val();
                var contact_id = $("#allcontactID").val();
                $("#update-lead-stage").empty();
                $.ajax({
                    url: "{{ route('admin.allcontact.getleadstage') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        lcs_id: lcs_id,
                        id: contact_id
                    },
                    success: function(data){
                        $('#update-lead-stage').html(data.res);
                    }
                });
            });


            $('#updateReg').on("show.bs.offcanvas",function(e){
                var editID = $(e.relatedTarget).data('id');

                jQuery.ajax({
                    url : '{{ route("admin.allcontact.edit") }}',
                    method: "GET",
                    type: "html",
                    data: {
                        "id": editID,
                        // "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        
                        // $('#edit_ID').val(data.id);
                        $("#lead_type").val(data.post.lead_type).trigger('change');
                        $('#edit-lcs-id').val(data.post.lcs_id).trigger('change');
                        $('#edit-ls-id').val(data.post.ls_id).trigger('change');
                        $('#edit-full-name').val(data.post.full_name);
                        $('#job_title').val(data.post.job_title);
                        $('#edit-company-name').val(data.post.company_name);
                        $('#edit-industry-id').val(data.post.indust_id).trigger('change');
                        $('#edit-agency-name').val(data.post.office_name);
                        $('#edit-primary-no').val(data.post.secondary_no_wsp);
                        $('#edit-mobile-no').val(data.post.primary_no_wsp);
                        $('#edit-phone0').val(data.post.mobile_no1_wsp);
                        $('#edit-phone1').val(data.post.mobile_no2_wsp);
                        $('#edit-phone2').val(data.post.mobile_no3_wsp);
                        $('#edit-email').val(data.post.email);
                        $('#edit-email0').val(data.post.email0);
                        $('#edit-email1').val(data.post.email1);
                        $('#edit-email2').val(data.post.email2);
                        $('#edit-country-id').val(data.post.country_id).trigger('change');
                        $('#edit-city-id').val(data.post.city_id).trigger('change');
                        $('#edit-careoff').val(data.post.careoff_id).trigger('change');
                        $('#edit-leadowner').val(data.post.owner_id).trigger('change');
                        $('#edit-group-id').val(data.post.group_id).trigger('change');
                        $('#edit-descr').val(data.post.descr);

                        if (data.post.lead_type == 'Job seeker') {
                            $('#job_title_div').show();
                            $('#office_name_div').hide();
                            $('#job_desg_div').hide();
                            $('#company_name_div').hide();
                            $('#indust_id_div').hide();

                        }else if (data.post.lead_type == 'Company') {
                            $('#job_desg_div').show();
                            $('#company_name_div').show();
                            $('#indust_id_div').show();
                            $('#job_title_div').hied();
                            $('#indust_id_div').hide();
                        }else if (data.post.lead_type == 'Associate with office') {
                            $('#office_name_div').show();
                            $('#job_desg_div').hide();
                            $('#company_name_div').hide();
                            $('#indust_id_div').hide();
                            $('#job_title_div').hide();
                        }else{
                            $('#office_name_div').hide();
                            $('#job_desg_div').hide();
                            $('#company_name_div').hide();
                            $('#indust_id_div').hide();
                            $('#job_title_div').hide();
                        }

                        $('#primary-con-dial-code-ed').val(data.update_phones.secondary_no_wsp_dial_code);
                        $('#mobile-no-dial-code-ed').val(data.update_phones.primary_no_wsp_dial_code);
                        $('#phone0-dial-code-ed').val(data.update_phones.mobile_no1_wsp_dial_code);
                        $('#phone1-dial-code-ed').val(data.update_phones.mobile_no2_wsp_dial_code);
                        $('#phone2-dial-code-ed').val(data.update_phones.mobile_no3_wsp_dial_code);




                        // $('.edit-primary-no').intlTelInput({

                        //     // localizedCountries: true,
                        //     onlyCountries: ["sa","in","qa","ae","kw"],
                        //     preferredCountries: [ "in","sa"],
                        //     separateDialCode: true,
                        //     initialCountry: data.update_phones.secondary_no_wsp_iso_code,


                        // }).on('countrychange',function(e,countryData){
                        //     $('.primary-con-dial-code-ed').val(($(".edit-primary-no").intlTelInput("getSelectedCountryData").dialCode))
                        // });

                        // $('.edit-mobile-no').intlTelInput({

                        //     // localizedCountries: true,
                        //     onlyCountries: ["sa","in","qa","ae","kw"],
                        //     preferredCountries: [ "in","sa"],
                        //     separateDialCode: true,
                        //     initialCountry: data.update_phones.primary_no_wsp_iso_code,


                        // }).on('countrychange',function(e,countryData){
                        //     $('.mobile-no-dial-code-ed').val(($(".edit-mobile-no").intlTelInput("getSelectedCountryData").dialCode))
                        // });


                        // $('.edit-phone0').intlTelInput({

                        //     // localizedCountries: true,
                        //     onlyCountries: ["sa","in","qa","ae","kw"],
                        //     preferredCountries: [ "in","sa"],
                        //     separateDialCode: true,
                        //     initialCountry: data.update_phones.mobile_no1_wsp_iso_code,


                        // }).on('countrychange',function(e,countryData){
                        //     $('.phone0-dial-code-ed').val(($(".edit-phone0").intlTelInput("getSelectedCountryData").dialCode))
                        // });


                        // $('.edit-phone1').intlTelInput({

                        //     // localizedCountries: true,
                        //     onlyCountries: ["sa","in","qa","ae","kw"],
                        //     preferredCountries: [ "in","sa"],
                        //     separateDialCode: true,
                        //     initialCountry: data.update_phones.mobile_no2_wsp_iso_code,


                        // }).on('countrychange',function(e,countryData){
                        //     $('.phone1-dial-code-ed').val(($(".edit-phone1").intlTelInput("getSelectedCountryData").dialCode))
                        // });

                        // $('.edit-phone2').intlTelInput({

                        //     // localizedCountries: true,
                        //     onlyCountries: ["sa","in","qa","ae","kw"],
                        //     preferredCountries: [ "in","sa"],
                        //     separateDialCode: true,
                        //     initialCountry: data.update_phones.mobile_no3_wsp_iso_code,


                        // }).on('countrychange',function(e,countryData){
                        //     $('.phone2-dial-code-ed').val(($(".edit-phone2").intlTelInput("getSelectedCountryData").dialCode))
                        // });

                    }
                });
            });

            $(document).on('submit', '#uploadFileForm', function(e) {
                e.preventDefault();

                let form = this;
                let formData = new FormData(form);
                let btn = $(form).find('button[type="submit"]');

                // ✅ Disable button + loader
                btn.prop('disabled', true).text('Uploading...');

                $.ajax({
                    url: "{{ route('admin.allcontact.uploadFile') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function(res) {

                        if (!res || !res.data) {
                            console.error('Invalid response:', res);
                            toastr.error('Invalid response from server');
                            return;
                        }

                        toastr.success(res.message || 'File uploaded');

                        // ✅ Remove empty row message if exists
                        $('#fileTableBody .no-data').remove();

                        // ✅ Create new row
                        let row = `
                            <tr id="fileRow_${res.data.id}">
                                <td>${res.data.file_name}</td>
                                <td>${res.data.uploaded_by ?? '---'}</td>
                                <td>${res.data.uploaded_at}</td>
                                <td>
                                    <a href="${res.data.file_path}" download class="btn btn-success btn-sm">Download</a>
                                    <button class="btn btn-danger btn-sm deleteFileBtn" data-id="${res.data.id}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        `;

                        // ✅ Add row on top
                        $('#fileTableBody').prepend(row);

                        // ✅ Reset form
                        form.reset();

                    },

                    error: function(xhr) {

                        if (xhr.status === 422) {
                            toastr.error(xhr.responseJSON.message || 'Validation error');
                        } else {
                            toastr.error('Upload failed');
                        }

                    },

                    complete: function() {
                        // ✅ Enable button
                        btn.prop('disabled', false).text('Upload');
                    }
                });
            });

            $(document).on('click', '.deleteFileBtn', function() {

                let btn = $(this);
                let id = btn.data('id');
                let row = $('#fileRow_' + id);

                btn.prop('disabled', true).text('Deleting...');

                $.ajax({
                    url: "/admin/allcontact-list/delete-file/" + id,
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },

                    success: function(res) {

                        toastr.success(res.message || 'Deleted');

                        // ✅ Remove row
                        row.remove();

                        // ✅ If table empty → show message
                        if ($('#fileTableBody tr').length === 0) {
                            $('#fileTableBody').html(`
                                <tr class="no-data">
                                    <td colspan="4" class="text-center">No files found</td>
                                </tr>
                            `);
                        }
                    },

                    error: function() {
                        toastr.error('Delete failed');
                        btn.prop('disabled', false).text('Delete');
                    }

                });

            });

        });
</script>

@endsection
