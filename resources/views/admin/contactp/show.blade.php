@extends('layout.admin.admin_layout')

@section('title','Contact View')

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
                                        @if ($post->office_logo != '')
                                            <img src="" alt="">
                                        @else
                                            <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar"/>
                                        @endif
                                    </div>
                                    <div>
                                        <h5 class="mb-1">{{ $post->office_eng_name }}</h5>
                                        <span>{{ $post->citname.', '.$post->contname }}</span>
                                    </div>

                                </div>
                                <div class="row mt-5">
                                    <div class="col-md-12">
                                        <button class="btn btn-sm btn-success mb-1" data-bs-toggle="offcanvas" data-bs-target="#campaignMessage"><i class="ti ti-brand-whatsapp ti-xs"></i> Send Message</button>

                                        @if (Auth::guard('admin')->user()->user_type == 1)
                                            <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="offcanvas" data-bs-target="#editContactplus" data-id="{{ $post->id }}"><i class="ti ti-pencil ti-xs"></i> Edit</button>
                                            <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#transferleadowner"><i class="ti ti-refresh ti-xs"></i> Transfer Lead Owner</button>
                                            <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#transfercareoff"><i class="ti ti-refresh ti-xs"></i> Transfer Careoff</button>

                                        @else
                                            @if (isset($permission))
                                                @if ($permission->full_access == 1)
                                                    <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="offcanvas" data-bs-target="#editContactplus" data-id="{{ $post->id }}"><i class="ti ti-pencil ti-xs"></i> Edit</button>
                                                    <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#transferleadowner"><i class="ti ti-refresh ti-xs"></i> Transfer Lead Owner</button>
                                                    <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#transfercareoff"><i class="ti ti-refresh ti-xs"></i> Transfer Careoff</button>

                                                @else
                                                    @if ($permission->edit_contactp == 1)
                                                        <button class="btn btn-sm btn-primary mb-1" data-bs-toggle="offcanvas" data-bs-target="#editContactplus" data-id="{{ $post->id }}"><i class="ti ti-pencil ti-xs"></i> Edit</button>
                                                    @endif
                                                    @if ($permission->lead_owner_transfer == 1)
                                                        <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#transferleadowner"><i class="ti ti-refresh ti-xs"></i> Transfer Lead Owner</button>
                                                    @endif
                                                    @if ($permission->careoff_transfer == 1)
                                                        <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#transfercareoff"><i class="ti ti-refresh ti-xs"></i> Transfer Careoff</button>
                                                    @endif

                                                @endif
                                            @endif
                                        @endif
                                        <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal" data-bs-target="#reminderCP"><i class="ti ti-alarm ti-xs"></i> Reminder</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Contact Status:</span>
                                    @if ($post->contstatus !='')
                                        <span class="badge bg-label-success">{{ $post->contstatus }}</span>
                                    @else
                                        <span class="badge bg-label-danger">None</span>
                                    @endif
                                </div> --}}

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Life Cycle Status:</span>
                                    @if ($post->lcsname != '')
                                        <a href="javascript:void(0);" class="badge bg-label-success" data-bs-toggle="modal" data-bs-target="#lifecyclestatus" data-id="{{ $post->id }}">{{ $post->lcsname }}</a>
                                    @else
                                        <a href="javascript:void(0);" class="badge bg-label-danger" data-bs-toggle="modal" data-bs-target="#lifecyclestatus" data-id="{{ $post->id }}">None</a>
                                    @endif

                                </div>

                                <div class="d-flex align-items-start gap-4 mt-1">
                                    <span class="fw-semibold me-25">Lead Stage:</span>
                                    @if ($post->lsname != '')
                                        <a href="javascript:void(0);" class="badge bg-label-success" data-bs-toggle="modal" data-bs-target="#lifecyclestatus" data-id="{{ $post->id }}">{{ $post->lsname }}</a>


                                    @else
                                        <a href="javascript:void(0);" class="badge bg-label-danger" data-bs-toggle="modal" data-bs-target="#lifecyclestatus" data-id="{{ $post->id }}">None</a>
                                    @endif

                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Business Type:</span>
                                    <span>@if($post->businesstype != '') {{ $post->businesstype }} @else {{ '---' }} @endif</span>
                                </div>

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Office Name Arabic:</span>
                                    <span>@if($post->office_ar_name != '') {{ $post->office_ar_name }} @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Office No:</span>
                                    <span>@if($post->office_no != '') {{ $post->office_no }} @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Office Email:</span>
                                    <span>@if($post->office_email != '') {{ $post->office_email }} @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Owner Name:</span>
                                    <span>@if($post->owner_name != '') {{ $post->owner_name }} @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Owner Contact No:</span>
                                    <span>@if($post->owner_contact != '') {{ $post->owner_contact }} <a href="https://wa.me/{{ $post->owner_contact }}" target="_blank"><i class="ti ti-brand-whatsapp ti-xs"></i></a> @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Owner Email:</span>
                                    <span>@if($post->owenr_email != '') {{ $post->owenr_email }} @else {{ '---' }} @endif</span>
                                </div> --}}

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Primary Concern Person:</span>
                                    <span class="copied_text">@if($post->prim_concern_name != '') {{ $post->prim_concern_name }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Primary Contact No:</span>
                                    <span>@if($post->prim_contact != '') {{ $post->prim_contact }} <a href="https://wa.me/{{ $post->prim_contact }}" target="_blank"><i class="ti ti-brand-whatsapp ti-xs"></i></a> @else {{ '---' }} @endif</span>

                                    {{-- <span>@if($post->prim_contact != '') {{ $post->prim_contact }} @else {{ '---' }} @endif</span> --}}
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Primary Email:</span>
                                    <span>@if($post->prim_email != '') {{ $post->prim_email }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Secondary Concern Person:</span>

                                    <span>@if($post->sec_concern_name != '') {{ $post->sec_concern_name }} @else {{ '---' }} @endif</span>
                                </div>

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Secondary Contact:</span>
                                    <span>@if($post->sec_contact != '') {{ $post->sec_contact }} <a href="https://wa.me/{{ $post->sec_contact }}" target="_blank"><i class="ti ti-brand-whatsapp ti-xs"></i></a> @else {{ '---' }} @endif</span>

                                    {{-- <span>@if($post->sec_contact != '') {{ $post->sec_contact }} @else {{ '---' }} @endif</span> --}}
                                </div>

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Secondary Email:</span>
                                    <span>@if($post->sec_email != '') {{ $post->sec_email }} @else {{ '---' }} @endif</span>
                                </div> --}}
                            </div>
                            <div class="col-md-4">
                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Concern Name3:</span>
                                    <span>@if($post->concern_name3 != '') {{ $post->concern_name3 }} @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Contact No3:</span>
                                    <span>@if($post->contact3 != '') {{ $post->contact3 }} <a href="https://wa.me/{{ $post->contact3 }}" target="_blank"><i class="ti ti-brand-whatsapp ti-xs"></i></a> @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Concern Name4:</span>
                                    <span>@if($post->concern_name4 != '') {{ $post->concern_name4 }} @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Contact No4:</span>
                                    <span>@if($post->contact4 != '') {{ $post->contact4 }} <a href="https://wa.me/{{ $post->contact4 }}" target="_blank"><i class="ti ti-brand-whatsapp ti-xs"></i></a> @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Concern Name5:</span>
                                    <span>@if($post->concern_name5 != '') {{ $post->concern_name5 }} @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Contact No5:</span>
                                    <span>@if($post->contact5 != '') {{ $post->contact5 }} <a href="https://wa.me/{{ $post->contact5 }}" target="_blank"><i class="ti ti-brand-whatsapp ti-xs"></i></a> @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Concern Name6:</span>
                                    <span>@if($post->concern_name6 != '') {{ $post->concern_name6 }} @else {{ '---' }} @endif</span>
                                </div> --}}

                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Contact No6:</span>
                                    <span>@if($post->contact6 != '') {{ $post->contact6 }} <a href="https://wa.me/{{ $post->contact6 }}" target="_blank"><i class="ti ti-brand-whatsapp ti-xs"></i></a> @else {{ '---' }} @endif</span>

                                </div> --}}

                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Group Name:</span>
                                    <span>@if($post->groupname != '') {{ $post->groupname }} @else {{ '---' }} @endif</span>
                                </div>



                                {{-- <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Life Cycle Status:</span>
                                    <span>@if($post->lcsname != '') {{ $post->lcsname }} @else {{ '---' }} @endif</span>
                                </div> --}}



                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Careoff:</span>
                                    <span>@if($post->careoffname != '') {{ $post->careoffname }} @else {{ '---' }} @endif</span>
                                </div>
                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Lead Owner:</span>
                                    <span>@if($post->leadownername != '') {{ $post->leadownername }} @else {{ '---' }} @endif</span>
                                </div>
                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Created By:</span>
                                    <span>{{ $post->uname }}</span>
                                </div>
                                <div class="d-flex align-items-start gap-4">
                                    <span class="fw-semibold me-25">Work Status:</span>
                                    @if ($post->status == 1)
                                        <a href="javascript:void(0);" class="badge bg-label-success">Active</a>
                                    @else
                                        <a href="javascript:void(0);" class="badge bg-label-danger">Inactive</a>
                                    @endif
                                </div>
                                <div class="d-flex align-items-start gap-4 mt-2">
                                    <span class="fw-semibold me-25">Whatsapp:</span>
                                    @if ($post->subscribe == 1)
                                        <a href="javascript:void(0);" class="badge bg-label-success" data-bs-toggle="modal" data-bs-target="#updateSubscribe">Subscribe</a>
                                    @else
                                        <a href="javascript:void(0);" class="badge bg-label-danger" data-bs-toggle="modal" data-bs-target="#updateSubscribe">Unsubsrcibe</a>
                                    @endif
                                </div>
                                <div class="d-flex align-items-start gap-4 mt-2">
                                    <span class="fw-semibold me-25">Status Update Date:</span>
                                    <span>@if($post->update_lead_status_date != '') {{ date('d-m-Y',strtotime($post->update_lead_status_date)) }} @else {{ '---' }} @endif</span>
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
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" id="nav-notes-tab" role="tabpanel">
                                <div class="row notes-div-2">
                                    <div class="col-md-4">
                                        <form action="{{ route('admin.contact.add.notes') }}" method="POST" id="notesValidation">
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
                                                            <td>{{ $contactnote->uname }}</td>
                                                            <td>{{ $contactnote->created_at }}</td>
                                                            <td>           
                                                                <a href="{{ route('admin.contact.delete.notes', $contactnote->id) }}"class="delete-note" data-id="{{ $contactnote->id }}"><i class="ti ti-trash ti-sm"></i></a>
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
                                    <div class="col-md-4">
                                        <div class="info-container">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Office Name:</span>
                                                    <span>@if($post->office_eng_name != '') {{ $post->office_eng_name }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Office Arabic Name:</span>
                                                    <span>@if($post->office_ar_name != '') {{ $post->office_ar_name }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Office No:</span>
                                                    <span>@if($post->office_no != '') {{ $post->office_no }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Office Email:</span>
                                                    <span>@if($post->office_email != '') {{ $post->office_email }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Owner Name:</span>
                                                    <span>@if($post->owner_name != '') {{ $post->owner_name }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Owner Contact No:</span>
                                                    <span>@if($post->owner_contact != '') {{ $post->owner_contact }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Owner Email:</span>
                                                    <span>@if($post->owenr_email != '') {{ $post->owenr_email }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Membership Number:</span>
                                                    <span>@if($post->membership_number != '') {{ $post->membership_number }} @else {{ '---' }} @endif</span>
                                                </li>
                                                @if($post->businesstype == 'Recruitment Agency')
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Licence Number:</span>
                                                    <span>@if($post->licence_number != '') {{ $post->licence_number }} @else {{ '---' }} @endif</span>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-container">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Primary Concern Name:</span>
                                                    <span>@if($post->prim_concern_name != '') {{ $post->prim_concern_name }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Primary Contact No:</span>
                                                    <span>@if($post->prim_contact != '') {{ $post->prim_contact }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Primary Email:</span>
                                                    <span>@if($post->prim_email != '') {{ $post->prim_email }} @else {{ '---' }} @endif</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Secondary Concern Name:</span>
                                                    <span>@if($post->sec_concern_name != '') {{ $post->sec_concern_name }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Secondary Contact No:</span>
                                                    <span>@if($post->sec_contact != '') {{ $post->sec_contact }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Secondary Email:</span>
                                                    <span>@if($post->sec_email != '') {{ $post->sec_email }} @else {{ '---' }} @endif</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Concern Name3:</span>
                                                    <span>@if($post->concern_name3 != '') {{ $post->concern_name3 }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Contact No3:</span>
                                                    <span>@if($post->contact3 != '') {{ $post->contact3 }} @else {{ '---' }} @endif</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-container">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Concern Name4:</span>
                                                    <span>@if($post->concern_name4 != '') {{ $post->concern_name4 }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Contact No4:</span>
                                                    <span>@if($post->contact4 != '') {{ $post->contact4 }} @else {{ '---' }} @endif</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Concern Name5:</span>
                                                    <span>@if($post->concern_name5 != '') {{ $post->concern_name5 }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Contact No5:</span>
                                                    <span>@if($post->contact5 != '') {{ $post->contact5 }} @else {{ '---' }} @endif</span>
                                                </li>

                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Concern Name6:</span>
                                                    <span>@if($post->concern_name6 != '') {{ $post->concern_name6 }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Contact No6:</span>
                                                    <span>@if($post->contact6 != '') {{ $post->contact6 }} @else {{ '---' }} @endif</span>
                                                </li>
                                                <li class="mb-2">
                                                    <span class="fw-semibold me-1">Created At:</span>
                                                    <span>@if($post->created_at != '') {{ $post->created_at }} @else {{ '---' }} @endif</span>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Contact Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="editContactplus" aria-labelledby="editContactplusLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editContactplusLabel" class="offcanvas-title">Edit Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form action="{{ route('admin.contact.update') }}" id="editUserForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <input type="hidden" name="editID" id="edit_ID" value="{{ $post->id }}">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-business-type" class="form-label">Business Type</label>
                                <select name="businesstype_id" id="edit-business-type" class="form-select select22" data-placeholder="Select Business Type" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($businesstypes as $businesstype)
                                        <option value="{{ $businesstype->id }}" @if($businesstype->id == $post->businesstype_id) selected @endif>{{ $businesstype->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-name-eng" class="form-label">Office Name (Eng) <span class="text-danger">*</span></label>
                                <input type="text" name="office_eng_name" id="edit-office-name-eng" value="{{ $post->office_eng_name }}" class="form-control" placeholder="Enter Office Name (eng)...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-name-ar" class="form-label">Office Name (Arabic)</label>
                                <input type="text" name="office_ar_name" id="edit-office-name-ar" value="{{ $post->office_ar_name }}" class="form-control" placeholder="Enter Office Name (arabic)...">
                            </div>
                        </div>
                        {{-- <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-no" class="form-label">Office Number</label>
                                <input type="text" name="office_no" id="edit-office-no" value="{{ $post->office_no }}" class="form-control" placeholder="Enter Office Contact No...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-office-email" class="form-label">Office Email</label>
                                <input type="text" name="office_email" id="edit-office-email" value="{{ $post->office_email }}" class="form-control" placeholder="Enter Office Email...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-name" class="form-label">Owner Name</label>
                                <input type="text" name="owner_name" id="edit-owner-name" value="{{ $post->owner_name }}" class="form-control" placeholder="Enter Owner Name...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-contact" class="form-label">Owner Contact</label>
                                <input type="text" name="owner_contact" id="edit-owner-contact" value="{{ $post->owner_contact }}" class="form-control" placeholder="Enter Owner Phone...">
                            </div>
                        </div>
                        <div class="col-md-6 showDIv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-owner-email" class="form-label">Owner Email</label>
                                <input type="text" name="owenr_email" id="edit-owner-email" value="{{ $post->owenr_email }}" class="form-control" placeholder="Enter Owner Email...">
                            </div>
                        </div> --}}

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-country-id">Country <span class="text-danger">*</span></label>
                                <select name="country_id" id="edit-country-id" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" @if($country->id == $post->country_id) selected @endif>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 showLicenceDiv2" style="display: none">
                            <div class="mb-3">
                                <label for="edit-licence-number" class="form-label">Licence Number <span class="text-danger">*</span></label>
                                <input type="text" name="licence_number" id="edit-licence-number" value="{{ $post->licence_number }}" class="form-control" placeholder="Enter Licence Number...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-city-id">City <span class="text-danger">*</span></label>
                                <select name="city_id" id="edit-city-id" class="form-select select22" data-placeholder="Select City" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @if($city->id == $post->city_id) selected @endif>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-person" class="form-label">Primary Concern Person Name</label>
                                <input type="text" name="prim_concern_name" id="edit-primary-person" value="{{ $post->prim_concern_name }}" class="form-control" placeholder="Enter Primary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-contact" class="form-label">Primary Contact No</label>
                                <input type="text" name="prim_contact" id="edit-primary-contact" value="{{ $post->prim_contact }}" class="form-control checkNoExistance" placeholder="Enter Primary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-primary-email" class="form-label">Primary Email</label>
                                <input type="text" name="prim_email" id="edit-primary-email" value="{{ $post->prim_email }}" class="form-control" placeholder="Enter Primary Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-person" class="form-label">Seconday Concern Person Name</label>
                                <input type="text" name="sec_concern_name" id="edit-secondary-person" value="{{ $post->sec_concern_name }}" class="form-control" placeholder="Enter Secondary Concern Person...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-contact" class="form-label">Seconday Contact No</label>
                                <input type="text" name="sec_contact" id="edit-secondary-contact" value="{{ $post->sec_contact }}" class="form-control checkNoExistance" placeholder="Enter Secondary Contact No...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-secondary-email" class="form-label">Seconday Email</label>
                                <input type="text" name="sec_email" id="edit-secondary-email" value="{{ $post->sec_email }}" class="form-control" placeholder="Enter Seconday Email...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person3" class="form-label">Concern Person Name 3</label>
                                <input type="text" name="concern_name3" id="edit-person3" value="{{ $post->concern_name3 }}" class="form-control" placeholder="Enter Concern Person 3...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact3" class="form-label">Contact No3</label>
                                <input type="text" name="contact3" id="edit-contact3" value="{{ $post->contact3 }}" class="form-control checkNoExistance" placeholder="Enter Contact No3...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person4" class="form-label">Concern Person Name 4</label>
                                <input type="text" name="concern_name4" id="edit-person4" value="{{ $post->concern_name4 }}" class="form-control" placeholder="Enter Concern Person4...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact4" class="form-label">Contact No4</label>
                                <input type="text" name="contact4" id="edit-contact4" value="{{ $post->contact4 }}" class="form-control checkNoExistance" placeholder="Enter Contact No4...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person5" class="form-label">Concern Person Name 5</label>
                                <input type="text" name="concern_name5" id="edit-person5" value="{{ $post->concern_name5 }}" class="form-control" placeholder="Enter Concern Person5...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact5" class="form-label">Contact No5</label>
                                <input type="text" name="contact5" id="edit-contact5" value="{{ $post->contact5 }}" class="form-control checkNoExistance" placeholder="Enter Contact No5...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-person6" class="form-label">Concern Person Name6</label>
                                <input type="text" name="concern_name6" id="edit-person6" value="{{ $post->concern_name6 }}" class="form-control checkNoExistance" placeholder="Enter Concern Person6...">
                                <em class="errorShowMob text-danger"></em>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact6" class="form-label">Contact No6</label>
                                <input type="text" name="contact6" id="edit-contact6" value="{{ $post->contact6 }}" class="form-control" placeholder="Enter Contact No6...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact7" class="form-label">Contact No7</label>
                                <input type="text" name="contact7" id="edit-contact7" value="{{ $post->contact7 }}" class="form-control" placeholder="Enter Contact No7...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact8" class="form-label">Contact No8</label>
                                <input type="text" name="contact8" id="edit-contact8" value="{{ $post->contact8 }}" class="form-control" placeholder="Enter Contact No8...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact9" class="form-label">Contact No9</label>
                                <input type="text" name="contact9" id="edit-contact9" value="{{ $post->contact9 }}" class="form-control" placeholder="Enter Contact No9...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact10" class="form-label">Contact No10</label>
                                <input type="text" name="contact10" id="edit-contact10" value="{{ $post->contact10 }}" class="form-control" placeholder="Enter Contact No10...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact11" class="form-label">Contact No11</label>
                                <input type="text" name="contact11" id="edit-contact11" value="{{ $post->contact11 }}" class="form-control" placeholder="Enter Contact No11...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-contact12" class="form-label">Contact No12</label>
                                <input type="text" name="contact12" id="edit-contact12" value="{{ $post->contact12 }}" class="form-control" placeholder="Enter Contact No12...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-careoff" class="form-label">Careoff <span class="text-danger">*</span></label>
                                <select name="careoff_id" id="edit-careoff" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($admins as $adminuser3)
                                        <option value="{{ $adminuser3->id }}" @if($post->careoff_id == $adminuser3->id) selected @endif>{{ $adminuser3->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-leadowner" class="form-label">Lead Owner <span class="text-danger">*</span></label>
                                <select name="leadowner_id" id="edit-leadowner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($admins as $adminuser4)
                                        <option value="{{ $adminuser4->id }}" @if($post->leadowner_id == $adminuser4->id) selected @endif>{{ $adminuser4->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button>
                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button> --}}
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>

                </form>
            </div>
        </div>
        <!-- Edit Contact End -->

        <!-- Send Message Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="campaignMessage" aria-labelledby="campaignMessageLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="campaignMessageLabel" class="offcanvas-title">Send Whatsapp</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="campaignMessageShow" action="{{ route('admin.contact.sendwhatsapp',$post->id) }}" method="POST" enctype="multipart/form-data">
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
                                <select name="contact_type_normal[]" id="send-template-contact-type" data-placeholder="Select Contact Type" class="form-select select2" multiple>
                                    {{-- <option value=""></option> --}}
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
                                    <option value="[Others]">Other</option>
                                    <option value="[Office Name (English)]">Office Name (English)</option>
                                    <option value="[Office Name (Arabic)]">Office Name (Arabic)</option>
                                    <option value="[Office Number]">Office Number</option>
                                    <option value="[Office Email]">Office Email</option>
                                    <option value="[Owner Name]">Owner Name</option>
                                    <option value="[Owner Contact]">Owner Contact</option>
                                    <option value="[Owner Email]">Owner Email</option>
                                    <option value="[Country]">Country</option>
                                    <option value="[City]">City</option>
                                    <option value="[Primary Concern Person]">Primary Concern Person</option>
                                    <option value="[Primary Contact No]">Primary Contact No</option>
                                    <option value="[Primary Email]">Primary Email</option>
                                    <option value="[Secondary Concern Person]">Secondary Concern Person</option>
                                    <option value="[Secondary Contact No]">Secondary Contact No</option>
                                    <option value="[Secondary Email]">Secondary Email</option>
                                    <option value="[Concern Person 3]">Concern Person 3</option>
                                    <option value="[Contact No 3]">Contact No 3</option>
                                    <option value="[Concern Person 4]">Concern Person 4</option>
                                    <option value="[Contact No 4]">Contact No 4</option>
                                    <option value="[Concern Person 5]">Concern Person 5</option>
                                    <option value="[Contact No 5]">Contact No 5</option>
                                    <option value="[Concer Person 6]">Concer Person 6</option>
                                    <option value="[Contact No 6]">Contact No 6</option>
                                    <option value="[Status]">Work Status</option>
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


        <!-- Update Lifecycle Status Start -->
        <div class="modal fade" id="lifecyclestatus" aria-hidden="true" aria-labelledby="lifecyclestatusLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="lifecyclestatusLabel">Lead Stage</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.stageUpdate') }}" method="POST" id="updateLeadStageValidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactID" id="contactID">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="update-lead-cycle-status" class="form-label">Life Cycle Status <span class="text-danger">*</span></label>
                                        <select name="lcs_id" id="update-lead-cycle-status" class="form-control select22" data-placeholder="Select Lead Cycle Status" data-allow-clear="true">
                                            <option value="">Select Lead Cycle Status</option>
                                            @foreach ($lcsds as $lcsd)
                                                <option value="{{ $lcsd->id }}">{{ $lcsd->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="update-lead-stage" class="form-label">Lead Stage <span class="text-danger">*</span></label>
                                        <select name="ls_id" id="update-lead-stage" class="form-control select22" data-placeholder="Select Lead Stage" data-allow-clear="true">

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="update-notes" class="form-label">Notes</label>
                                        <textarea name="notes" class="form-control" id="update-notes" cols="30" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="button" id="updateLeadStage">Update Lead Stage</button>
                            {{-- <button class="btn btn-sm btn-primary" type="submit">Update Lead Stage</button> --}}
                        </div>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <!-- Update Lifecycle Status End -->
        <!-- Transfer Lead Owner Start -->
        <div class="modal fade" id="transferleadowner" aria-hidden="true" aria-labelledby="transferleadownerLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="transferleadownerLabel">Transfer Lead Owner</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.transfer.leadowner') }}" method="POST" id="transferleadownervalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactID" value="{{ $post->id }}">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="current-lead-owner" class="form-label">Current Lead Owner <span class="text-danger">*</span></label>
                                        <input type="text" name="" id="current-lead-owner" class="form-control" value="{{ $post->leadownername }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="transfer-lead-owner" class="form-label">Transfer Lead Owner <span class="text-danger">*</span></label>
                                        <select name="leadowner_id" id="transfer-lead-owner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($leadownerlist as $leadowner)
                                                <option value="{{ $leadowner->id }}">{{ $leadowner->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            {{-- <button class="btn btn-sm btn-primary" type="submit">Transfer Lead Owner</button> --}}
                            <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button>
                        </div>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <!-- Transfer Lead Owner End -->

        <!-- Careoff Start -->
        <div class="modal fade" id="transfercareoff" aria-hidden="true" aria-labelledby="transfercareoffLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="transfercareoffLabel">Transfer Careoff</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.transfer.careoff') }}" method="POST" id="transfercareoffrvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactID" value="{{ $post->id }}">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="current-careoff" class="form-label">Current Careoff <span class="text-danger">*</span></label>
                                        <input type="text" name="" id="current-careoff" class="form-control" value="{{ $post->careoffname }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="transfer-careoff" class="form-label">Transfer Careoff <span class="text-danger">*</span></label>
                                        <select name="careoff_id" id="transfer-careoff" class="form-select select2" >
                                            <option value=""></option>
                                            @foreach ($careofflist as $careoff)
                                                <option value="{{ $careoff->id }}">{{ $careoff->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Transfer Careoff</button>
                        </div>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <!-- Careoff End -->
        <!-- Reminder Contactp -->
        <div class="modal fade" id="reminderCP" aria-hidden="true" aria-labelledby="reminderCPLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="reminderCPLabel">Created Reminder</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.addreminder') }}" method="POST" id="createReminder">
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

                <form action="{{ route('admin.contact.updatereminder') }}" method="POST" id="updateReminderValidation">
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
        <!-- Update Subscribe / Unsubscribe Start -->
        <div class="modal fade" id="updateSubscribe" aria-hidden="true" aria-labelledby="updateSubscribeLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="updateSubscribeLabel">Update Subscribe</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.contact.updatesubscribe') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <input type="hidden" name="contactID" value="{{ $post->id }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="subscribe" value="1" @if($post->subscribe == 1) checked @endif id="add-subscribe"/>
                                            <label class="form-check-label" for="add-subscribe">Subscribe</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="subscribe" value="0" @if($post->subscribe == 0) checked @endif id="add-unsubscribe"/>
                                            <label class="form-check-label" for="add-unsubscribe">Unscubscribe</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Update</button>
                        </div>
                    </div>
                </form>
              </div>
            </div>
        </div>
        <!-- Update Subscribe / Unsubsribe End -->
        <!-- Send Messaeg Stataus Track Start -->
        <div class="modal fade" id="sendmessagestatus" aria-hidden="true" aria-labelledby="sendmessagestatusLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="sendmessagestatusLabel">Message Send Status</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Mobile No</th>
                                                <th>Message Status</th>
                                                <th>Message Text</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dissendrecoredtable">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btm-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>



              </div>
            </div>
        </div>

        <!-- Send Message Status Track End -->
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

    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/contactp-validation.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('#edit-business-type').on('change',function(){
                var value = $(this).val();
                if (value == 2 || value == '') {
                    $('.showDIv2').hide();
                } else {
                    $('.showDIv2').show();
                }

                var typeName2 = $(this).find('option:selected').text().trim();
                if (typeName2 === 'Recruitment Agency') {
                    $('.showLicenceDiv2').show();
                    $('#edit-licence-number').prop('required', true);
                } else {
                    $('.showLicenceDiv2').hide();
                    $('#edit-licence-number').prop('required', false);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#editContactplus').on("show.bs.offcanvas",function(e){
                var editID = $(e.relatedTarget).data('id');

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url("admin/contact-list/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        // $('#edit_ID').val(data.id);
                        $('#edit-licence-number').val(data.licence_number);
                        $("#edit-business-type").val(data.businesstype_id).trigger('change');
                        $('#edit-office-name-eng').val(data.office_eng_name);
                        $('#edit-office-name-ar').val(data.office_ar_name);
                        // $('#edit-office-no').val(data.office_no);
                        // $('#edit-office-email').val(data.office_email);
                        // $('#edit-owner-name').val(data.owner_name);
                        // $('#edit-owner-contact').val(data.owner_contact);
                        // $('#edit-owner-email').val(data.owenr_email);
                        $('#edit-country-id').val(data.country_id).change();
                        $('#edit-city-id').val(data.city_id).change();
                        $('#edit-primary-person').val(data.prim_concern_name);
                        $('#edit-primary-contact').val(data.prim_contact);
                        $('#edit-primary-email').val(data.prim_email);
                        $('#edit-secondary-person').val(data.sec_concern_name);
                        $('#edit-secondary-contact').val(data.sec_contact);
                        $('#edit-secondary-email').val(data.sec_email);
                        $('#edit-person3').val(data.concern_name3);
                        $('#edit-contact3').val(data.contact3);
                        $('#edit-person4').val(data.concern_name4);
                        $('#edit-contact4').val(data.contact4);
                        $('#edit-person5').val(data.concern_name5);
                        $('#edit-contact5').val(data.contact5);
                        $('#edit-person6').val(data.concern_name6);
                        $('#edit-contact6').val(data.contact6);
                        $('#edit-contact7').val(data.contact7);
                        $('#edit-contact8').val(data.contact8);
                        $('#edit-contact9').val(data.contact9);
                        $('#edit-contact10').val(data.contact10);
                        $('#edit-contact11').val(data.contact11);
                        $('#edit-contact12').val(data.contact12);
                        $('#edit-careoff').val(data.careoff_id).change();
                        $('#edit-leadowner').val(data.leadowner_id).change();
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#sendmessagestatus').on('show.bs.modal',function(e){
                var sendcontactid = $(e.relatedTarget).data('id');
                jQuery.ajax({
                    url: "{{ route('admin.contact.getsendwhatsapplist') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        id: sendcontactid
                    },
                    success: function(data){
                        $('#dissendrecoredtable').html(data.res);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#updateReminder').on('show.bs.modal',function(e){
                var reminderID = $(e.relatedTarget).data('id');
                $('#reminder_id').val(reminderID);

                jQuery.ajax({
                    url: "{{ url('admin/contacts/add-reminder/edit') }}",
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
        });
    </script>

    <script>
        $(document).ready(function(){
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
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#send-template-personalise-class').on('change',function(){
                var partvalue = $(this).val();
                navigator.clipboard.writeText(partvalue);
                toastr['success']('Text copied - '+partvalue+'', 'Success', { hideDuration: 3000 });
            });

        });
    </script>

    <script>
        $(document).ready(function(){
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
        });
    </script>

<script>
    $(document).on('input','.checkNoExistance',function(){
        let mobileNo = $(this).val();
        let errorElement = $(this).closest('.mb-3').find('.errorShowMob');
        let EditID = $('#edit_ID').val();
        // alert(EditID);
        if (mobileNo != '') {
            jQuery.ajax({
                url: "{{ route('admin.contact.checkmobileNo') }}",
                method: "GET",
                type: "html",
                data:{
                    "mobile_no": mobileNo,
                    "edit_id": EditID
                },
                success: function(data){
                    console.log(data);
                    if (data.response_status == 1) {
                        $(errorElement).text(data.response_msg);
                    } else {
                        $(errorElement).text(data.response_msg);
                    }
                }
            });
        }else{
            $(errorElement).text('');
        }


    });
</script>

    <script>
        $(document).ready(function(){
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
        });
    </script>

    <script>
        $(document).ready(function(){
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
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#lifecyclestatus').on("show.bs.modal",function(e){
                var contactID = $(e.relatedTarget).data('id');
                $('#contactID').val(contactID);
                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url("admin/contact-list/edit") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": contactID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $("#update-lead-cycle-status").val(data.lcs_id).change();
                    }
                });
            });

            $('#update-lead-cycle-status').on('change',function(){
                var lcs_id = $(this).val();
                var contact_id = $("#contactID").val();

                $("#update-lead-stage").empty();

                jQuery.ajax({
                    url: "{{ url('admin/contacts/getstage') }}",
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

        });
    </script>

    <script>
        $(document).ready(function(){
            $('.flatpicker-date').flatpickr();
            $('.flatpicker-time').flatpickr({
                enableTime: true,
                noCalendar: true
            });
        });
    </script>

    <script>
        var rowMin4 = 1;
        var rowMax4 = 10;

        $(document).on('click','.addContactpDisp',function(){
            var html = "";
            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-contactp-variable'+rowMin4+'">Field Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_contactp[]" id="add-field-contactp-variable'+rowMin4+'" class="form-control fieldVariable select2"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_contactp'+rowMin4+'">Assign Variable Client <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_contactp[]" id="assign_var_contactp'+rowMin4+'" class="form-control assignVariableContactPOPT select2"><option value="">Select</option><option value="[Others]">Other</option><option value="[Office Name (English)]">Office Name (English)</option><option value="[Office Name (Arabic)]">Office Name (Arabic)</option><option value="[Office Number]">Office Number</option><option value="[Office Email]">Office Email</option><option value="[Owner Name]">Owner Name</option><option value="[Owner Contact]">Owner Contact</option><option value="[Owner Email]">Owner Email</option><option value="[Country]">Country</option><option value="[City]">City</option><option value="[Primary Concern Person]">Primary Concern Person</option><option value="[Primary Contact No]">Primary Contact No</option><option value="[Primary Email]">Primary Email</option><option value="[Secondary Concern Person]">Secondary Concern Person</option><option value="[Secondary Contact No]">Secondary Contact No</option><option value="[Secondary Email]">Secondary Email</option><option value="[Concern Person 3]">Concern Person 3</option><option value="[Contact No 3]">Contact No 3</option><option value="[Concern Person 4]">Concern Person 4</option><option value="[Contact No 4]">Contact No 4</option><option value="[Concern Person 5]">Concern Person 5</option><option value="[Contact No 5]">Contact No 5</option><option value="[Concer Person 6]">Concer Person 6</option><option value="[Contact No 6]">Contact No 6</option><option value="[Status]">Work Status</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';



            $('#dispContactp').append(html);

            rowMin4++;
            rowMax4--;
            if (rowMax4 == 1) {
                $('.addContactpDisp').prop('disabled',true);
            }else{
                $('.addContactpDisp').prop('disabled',false);
            }
        });
        $(document).on('click','.remove',function(){
            $(this).closest('.row').remove();
            rowMax4++;
            rowMin4--;
            if (rowMax4 == 0) {
                $('.addContactpDisp').prop('disabled',true);
            }else{
                $('.addContactpDisp').prop('disabled',false);
            }
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

            // Update Lead Status and Stage
            $(document).on('click','#updateLeadStage',function(e){
                e.preventDefault();
                const select22 = $('.select22');
                var formdata = $('#updateLeadStageValidation').serialize();
                var leadupdateForm = $(' #updateLeadStageValidation ');
                leadupdateForm.validate({
                    rules:{
                        lcs_id:{
                            required: true
                        },
                        ls_id:{
                            required: true
                        }
                    },
                    messages:{
                        lcs_id:{
                            required: "Please Select Life Cycle Status"
                        },
                        ls_id:{
                            required: "Please Select Lead Stage"
                        }
                    },

                });

                if (leadupdateForm.valid()) {
                    $.ajax({
                        url: "{{ route('admin.contact.stageUpdate') }}",
                        method: "POST",
                        data: formdata,
                        success: function(response){
                            toastr.success(response.success);
                            leadupdateForm[0].reset();
                            // $('.nav-form-short-load').load(" .nav-form-short-load");
                            // $('.datatables-users').DataTable().ajax.reload();
                            $('.notes-div-2').load(" .notes-div-2");
                            $('.top-content-div').load(" .top-content-div");
                            $('#lifecyclestatus').modal('hide');
                            if (select22.length) {
                                select22.each(function () {
                                    var $this = $(this);
                                    $this.wrap('<div class="position-relative"></div>').select2({
                                    //   placeholder: 'Select value',
                                    dropdownParent: $this.parent()
                                    });
                                });
                            }

                        }
                    });
                }

                return false;
            });

            // Edit New Contactp
            $(document).on('click','#editnewcontact',function(e){
                e.preventDefault();
                const select22 = $('.select22');
                var formdata = $('#editUserForm').serialize();
                var editnewcandidateForm = $(' #editUserForm ');

                editnewcandidateForm.validate({
                    rules:{
                        businesstype_id:{
                            required: true
                        },
                        country_id:{
                            required: true
                        },
                        licence_number:{
                            required: function(){
                                return $('#edit-business-type option:selected').text().trim() === 'Recruitment Agency';
                            }
                        },
                        careoff_id:{
                            required: true
                        },
                        leadowner_id:{
                            required: true
                        }
                    },
                    messages: {
                        businesstype_id:{
                            required: "Please Select Business Type"
                        },
                        country_id:{
                            required: "Please Select Country"
                        },
                        licence_number:{
                            required: "Please Enter Licence Number"
                        },
                        careoff_id:{
                            required: "Please Select Careoff"
                        },
                        leadowner_id:{
                            required: "Please Select Lead Owner"
                        }
                    },
                });

                if (editnewcandidateForm.valid()) {
                    $.ajax({
                        url: "{{ route('admin.contact.update') }}",
                        method: "POST",
                        data: formdata,
                        success: function(response){
                            toastr.success(response.success);
                            editnewcandidateForm[0].reset();
                            // $('.datatables-users').DataTable().ajax.reload();
                            $('#editContactplus').offcanvas('hide');
                            if (select22.length) {
                                select22.each(function () {
                                    var $this = $(this);
                                    $this.wrap('<div class="position-relative"></div>').select2({
                                    //   placeholder: 'Select value',
                                    dropdownParent: $this.parent()
                                    });
                                });
                            }
                        },
                        error: function(xhr){
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                var firstError = Object.values(errors)[0][0];
                                toastr.error(firstError);
                            } else {
                                toastr.error('Something went wrong. Please try again.');
                            }
                        }
                    });
                }
                return false;

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $(document).on('click','.copied_text',function(){
                var value = $(this).text();
                navigator.clipboard.writeText(value);
                toastr['success']('Text copied - '+value+'', 'Success', { hideDuration: 3000 });
            });
        });
    </script>

@endsection
