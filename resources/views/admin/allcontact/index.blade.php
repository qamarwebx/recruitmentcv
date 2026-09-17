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

        .pagestyle{
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            /* color: #495057; */
            /* background-color: #fff; */
            background-clip: padding-box;
            /* border: 1px solid #ced4da; */
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out
        }

        .pagestyle:focus{
            /* color: #6e6b7b; */
            /* background-color: #fff; */
            /* border-color: #7367f0; */
            outline: 0;
            /* box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1); */
        }
        .shortformmargin{
            /* margin-right: 195px; */
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
    </style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="card mb-3 hideshowaddmodule" id="hideshowaddmodule" @if(isset($allcontactsaveadminfilter) && $allcontactsaveadminfilter->short_form_code == 1) @else style="display: none" @endif>
            <div class="card-body">
                <form action="{{ route('admin.allcontact.uploadstore') }}" id="shortcontactsaveValidation" method="POST" class="shortform-div">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-short-fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" id="add-short-fullname" class="form-control" placeholder="Enter Full Name...">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-short-mobile-number" class="form-label">Primary Number (Whatsapp) <span class="text-danger">*</span></label>
                                <input type="text" name="primary_no_wsp" id="add-short-mobile-number" class="form-control add-short-mobile-no" placeholder="Enter mobile number">
                                <input type="hidden" name="primary_no_wsp_dial_code" id="mobile-no-dial-code-short">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="add_short_lead_type" class="form-label">Business Type</label>
                                <select name="lead_type" id="add_short_lead_type" class="form-control select2e lead_type" data-allow-clear="true" data-placeholder="Select Business Type">
                                    <option value="">Select</option>
                                    <option value="Unknown">Other</option>
                                    <option value="Job seeker" selected>Job seeker</option>
                                    <option value="Direct Wakala Candidate">Direct Wakala Candidate</option>
                                    <option value="Agent without office">Agent without office </option>
                                    <option value="Associate with office">Associate with office</option>
                                    <option value="Company">Company</option>
                                    <option value="Trade site Center">Trade site Center</option>
                                    <option value="HIRING US">HIRING US</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="add-short-source" class="form-label">Source</label>
                                <select name="source" id="add-short-source" class="form-control select2e source" data-allow-clear="true" data-placeholder="Select Source">
                                    <option value="">Select</option>
                                    <option value="Whats Air" selected>Whats Air</option>
                                    <option value="Office Visit">Office Visit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="mb-3 mt-3">
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" name="send_whatsapp" type="checkbox" id="add-short-send-whatsapp" value="1" />
                                    <label class="form-check-label" for="add-short-send-whatsapp">Msg</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="mb-3 mt-4">
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                        </div>
                    </div> --}}
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom">
                <div class="mb-1 float-start">
                    {{-- <label for="">Show</label> --}}
                    <select id="pagination_list" class="form-select form-select-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">150</option>
                        <option value="200">200</option>
                        <option value="250">250</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>
                    </select>
                </div>
                <div class="px-3 float-start">
                    {{-- <a href="{{ route('admin.allcontact.updatewrongcountry') }}" class="btn btn-sm btn-primary">Update Number</a> --}}
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel">
                        <i class="ti ti-filter"></i> Filter                     
                        <span class="filter-indicator d-none"></span>
                    </button>
                    {{-- <button class="btn btn-xs btn-primary" id="disableshortcode"><i class="ti ti-x"></i> Disable Short Form</button> --}}
                    {{-- <button class="btn btn-xs btn-primary d-none" id="enableshortcode"><i class="ti ti-checkbox"></i> Enable Short Form</button> --}}

                    <div class="btn-group mx-2">
                        <button class="btn btn-primary btn-sm dropdown-toggle bulkactions" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Option
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulkimport"><i class="ti ti-refresh me-0 me-sm-1 ti-xs"></i> Import Contact</a>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#checkMobileModal"><i class="ti ti-phone ti-xs"></i> Check Mobile</a>

                            <div class="dropdown-item">
                                <label class="switch switch-square">
                                    <input type="checkbox" name="short_form_code" value="1" id="short_form_code2" class="switch-input" @if(isset($allcontactsaveadminfilter) && $allcontactsaveadminfilter->short_form_code == 1) checked @endif />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Short Form</span>
                                </label>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="mb-1 float-end">
                    <div class="btn-group mx-2">
                        <button class="btn btn-primary btn-sm dropdown-toggle bulkactions" type="button" disabled id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Bulk Action
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1))
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfergroup"><i class="ti ti-users me-2"></i> Transfer To Group</a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfercareoff"><i class="ti ti-user me-2"></i> Transfer To Careoff</a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransferleadowner"><i class="ti ti-user me-2"></i> Transfer To Lead Owner</a>
                                <a class="dropdown-item bulcontactwhpsend" href="#" data-bs-toggle="offcanvas" data-bs-target="#bulkwhatsappsend"><i class="ti ti-brand-whatsapp me-2"></i> Send Whatsapp</a>
                                <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkdelete"><i class="ti ti-trash me-2"></i> Delete</a>
                                <a href="javascript:" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkcountryupdate"><i class="ti ti-flag me-2"></i> Country Update</a>
                                <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkcountrycodeupdate"><i class="ti ti-flag me-2"></i> Country Code Update</a>
                                <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkcountrycodeupdatefield"><i class="ti ti-flag me-2"></i> Country Code Update Field</a>

                            @elseif (isset($permission) && $permission->full_access == 0)

                                @if ($permission->allcontact_bulk_transfer_group == 1)
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfergroup"><i class="ti ti-users me-2"></i> Transfer To Group</a>
                                @endif

                                @if ($permission->allcontact_bulk_transfer_careoff == 1)
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransfercareoff"><i class="ti ti-user me-2"></i> Transfer To Careoff</a>
                                @endif

                                @if ($permission->allcontact_bulk_transfer_leadowner == 1)
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bulktransferleadowner"><i class="ti ti-user me-2"></i> Transfer To Lead Owner</a>
                                @endif

                                @if ($permission->allcontact_bulk_send_whatsapp == 1)
                                    <a class="dropdown-item bulcontactwhpsend" href="#" data-bs-toggle="offcanvas" data-bs-target="#bulkwhatsappsend"><i class="ti ti-brand-whatsapp me-2"></i> Send Whatsapp</a>
                                @endif

                                @if ($permission->allcontact_bulk_delete == 1)
                                    <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkdelete"><i class="ti ti-trash me-2"></i> Delete</a>
                                @endif

                                @if ($permission->allcontact_bulk_country_update == 1)
                                    <a href="javascript:" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkcountryupdate"><i class="ti ti-flag me-2"></i> Country Update</a>
                                @endif

                                @if ($permission->allcontact_bulk_country_code_update == 1)
                                    <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#bulkcountrycodeupdate"><i class="ti ti-flag me-2"></i> Country Code Update</a>
                                @endif


                            @endif


                        </div>
                    </div>
                    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1))
                        <button class="add-new btn btn-sm btn-primary addcontact addcandidate" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Contact</span></button>
                    @elseif (isset($permission) && $permission->full_access == 0)
                        @if ($permission->allcontact_add == 1)
                            <button class="add-new btn btn-sm btn-primary addcontact addcandidate" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Contact</span></button>
                        @endif
                    @endif
                </div>

                {{-- <div class="float-end mx-2">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bulkimport"><i class=""></i><i class="ti ti-refresh me-0 me-sm-1 ti-xs"></i> Import</button>
                </div> --}}

                <div class="mb-1 float-end">
                    <input type="text" id="search_text" class="form-control" placeholder="Search...">
                </div>
            </div>
            <div class="card-datatable table-responsive contactpaginate">
                @include('admin.allcontact.load')
            </div>
        </div>

        <!-- Update Stage Status Start -->
        <div class="modal fade" id="editStatus" aria-hidden="true" aria-labelledby="editStatusLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
              <div class="modal-content">
                <div class="modal-header pb-2">
                  <h5 class="offcanvas-title" id="editStatusLabel">Update Lifecycle Status</h5>
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
                                            @foreach ($lifecyclestatus as $lcs)
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

        <!-- Bulk Group Transfer Start -->
        <div class="modal fade" id="bulktransfergroup" aria-hidden="true" aria-labelledby="bulktransfergroupLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransfergroupLabel">Transfer Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.bulkgrouptransfer') }}" method="POST" id="bulktransfergroupvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDGRPTR" id="contactIDGRPTR">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <p id="totlaTransferGrp" class="text-success"></p>
                                        <label for="transfer-groupm" class="form-label">Transfer Group <span class="text-danger">*</span></label>
                                        <select name="group_id" id="transfer-groupm" class="form-select select22" data-placeholder="Select Group" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($groupms as $groupm)
                                                <option value="{{ $groupm->id }}">{{ $groupm->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <p id="respMessageGroupTransfer"></p>
                                    <p id="respGroupLimit"></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary dibtngrp" type="submit">Transfer Group</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Group Transfer End -->

        <!-- Bulk Country Code Update Start -->
        <div class="modal fade" id="bulkcountryupdate" aria-hidden="true" aria-labelledby="bulkcountryupdateLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulkcountryupdateLabel">Update Country</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.countryupdt.querycountry') }}" method="POST" id="bulkcountryupdatevalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDGRPTRC" id="contactIDGRPTRC">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="add-transfer-country-query" class="form-label">Select Country Query <span>*</span></label>
                                        <select name="country_query" id="add-transfer-country-query" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                            <option value="">Select</option>
                                            <option value="India">India</option>
                                            <option value="Saudi Arabia">Saudi Arabia</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="add-transfer-country-update" class="form-label">Select Country <span class="text-danger">*</span></label>
                                        <select name="country_id" id="add-transfer-country-update" class="form-select select22" data-placeholder="Select Country" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($countries as $countrytr)
                                                <option value="{{ $countrytr->id }}">{{ $countrytr->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Update</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Country Code Update End -->

        <!-- Bulk Country Code Updated From Bulk Start -->
        <div class="modal fade" id="bulkcountrycodeupdate" aria-hidden="true" aria-labelledby="bulkcountrycodeupdateLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulkcountrycodeupdateLabel">Update Country Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.bulkupdatecountrycode') }}" method="POST" id="bulkcountrycodeupdatevalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDGRPTRC2" id="contactIDGRPTRC2">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <p id="getListBulkUpdate" class="text-primary"></p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Update</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Country Code Updated From Bulk Start -->

        <!-- Bulk Country Code Update Field From Bulk Start -->
        <div class="modal fade" id="bulkcountrycodeupdatefield" aria-hidden="true" aria-labelledby="bulkcountrycodeupdatefieldLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulkcountrycodeupdatefieldLabel">Update Country Code Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.bulkupdatecountrycodefield') }}" method="POST" id="bulkcountrycodeupdatefieldvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDGRPTRC25" id="contactIDGRPTRC25">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <p id="getListBulkUpdate25" class="text-primary"></p>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Update</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Country Code Update Field From Bulk End -->

        <!-- Bulk Send Whatsapp Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="bulkwhatsappsend" aria-labelledby="bulkwhatsappsendLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="bulkwhatsappsendLabel" class="offcanvas-title">Send Bulk Whatsapp</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="bulksendwhatsapp" action="{{ route('admin.allcontact.bulksendwhatsapp') }}" method="POST" >
                    @csrf
                    <div class="row">
                        <input type="hidden" id="contactpID2" name="contactpID">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="send-whatsapp-type" class="form-label">For Whatsapp <span class="text-danger">*</span></label>
                                <select name="send_whatsapp_type" id="send-whatsapp-type" class="form-select select22" data-placeholder="Select For Whatsapp" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="meta_whatsapp">Meta Whatsapp</option>
                                    <option value="normal_whatsapp" selected>Normal Whatsapp</option>
                                </select>
                            </div>
                        </div>
                        <!-- Meta Whatsapp Display Start -->
                        <div class="col-md-4 dismetawhatsapp" style="display: none">
                            <div class="mb-3">
                                <label for="send-meta-template-name" class="form-label">Meta Template <span class="text-danger">*</span></label>
                                <select name="metatemplate_id" id="send-meta-template-name" class="form-select select22" data-placeholder="Select Meta Template" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($metatemplates as $metatemplate)
                                        <option value="{{ $metatemplate->id }}">{{ $metatemplate->template_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Meta Whatsapp Display End -->
                        <!-- Normal Whatsapp Display Start -->
                        <div class="col-md-4 disnormalwhatsapp">
                            <div class="mb-3">
                                <label for="send-template-name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                <select name="template_id" id="send-template-name" class="form-select select22" data-placeholder="Select Template Name" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($normaltemplates as $normaltemplate)
                                        <option value="{{ $normaltemplate->id }}">{{ $normaltemplate->template_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 disnormalwhatsapp">
                            <div class="mb-3">
                                <label class="form-label" for="send-template-whatsapp-api">Select Whatsapp API <span class="text-danger">*</span></label>
                                <select name="wapi_id_text[]" id="send-template-whatsapp-api" class="form-select select22"  multiple data-placeholder="Select Whatsapp API...">
                                    <option value=""></option>
                                    @foreach ($wapis as $wapi)
                                        <option value="{{ $wapi->id }}">{{ $wapi->mobile_no }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Common Element Start -->
                        <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-type" class="form-label">Contact Type <span class="text-danger">*</span></label>
                                <select name="contact_type[]" id="send-meta-template-contact-type" class="form-select select22" data-placeholder="Select Contact Type" multiple>
                                    <option value="all" selected>All</option>
                                    <option value="owner">Owner</option>
                                    <option value="Primary">Primary</option>
                                    <option value="Secondary">Secondary</option>
                                </select>
                            </div>
                        </div>
                        {{-- 04-01-2024 <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="contact_status[]" id="send-meta-template-contact-status" class="form-select select22" multiple data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div> --}}
                        <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-subscribe" class="form-label">Subscribe <span class="text-danger">*</span></label>
                                <select name="subscribe[]" id="send-meta-template-subscribe" class="form-select select22" multiple data-placeholder="Select Subscribe">
                                    <option value=""></option>
                                    <option value="1">Subscribe</option>
                                    <option value="0">Unsubscribe</option>
                                </select>
                            </div>
                        </div>
                        <!-- Common Element End -->


                        <div class="col-md-4 disallforwhatsapp">
                            <div class="mb-3">
                                <label for="send-meta-template-contact-group" class="form-label">Group</label>
                                <select name="contact_group_id[]" id="send-meta-template-contact-group" class="form-select select22" multiple data-placeholder="Select Group...">
                                    <option value="">Select Group</option>
                                    @foreach ($groupms as $groupm)
                                        <option value="{{ $groupm->id }}">{{ $groupm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 disnormalwhatsapp">
                            <div class="mb-3">
                                <label for="send-template-personalise-class" class="form-label">Personalise Name</label>
                                <select name="personalise_class" id="send-template-personalise-class" class="form-select select22" data-allow-clear="true" data-placeholder="Select Personalise Class">
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

                    <div class="row dismetawhatsapp" style="display: none">
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
                    <div class="row dismetawhatsapp" style="display: none">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-campaign-type" class="form-label">Campaign Type <span class="text-danger">*</span></label>
                                <select name="campaign_type" id="add-campaign-type" class="form-select select22" data-allow-clear="true" data-placeholder="Campaign Type...">
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
                    <div class="row disnormalwhatsapp">
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
                    <div class="row disnormalwhatsapp">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-campaign-type2" class="form-label">Campaign Type <span class="text-danger">*</span></label>
                                <select name="campaign_type2" id="add-campaign-type2" class="form-select select22" data-allow-clear="true" data-placeholder="Campaign Type...">
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
        <!-- Bulk Send Whatsapp End -->

        <!-- Bulk Transfer Lead Owner Start -->
        <div class="modal fade" id="bulktransferleadowner" aria-hidden="true" aria-labelledby="bulktransferleadownerLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransferleadownerLabel">Transfer Lead Owner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.bulkleadownertransfer') }}" method="POST" id="bulktransferleadownevalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDLTR" id="contactIDLTRB">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="transfer-lead-owner" class="form-label">Transfer Lead Owner <span class="text-danger">*</span></label>
                                        <select name="leadowner_id" id="transfer-lead-owner" class="form-select select22" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($adminusers as $adminuserl)
                                                <option value="{{ $adminuserl->id }}">{{ $adminuserl->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Transfer Lead Owner</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Transfer Lead Owner End -->

        <!-- Bulk Transfer Careoff Start -->
        <div class="modal fade" id="bulktransfercareoff" aria-hidden="true" aria-labelledby="bulktransfercareoffLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                <div class="modal-header pb-2">
                    <h5 class="offcanvas-title" id="bulktransfercareoffLabel">Transfer Careoff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.allcontact.bulkcareofftransfer') }}" method="POST" id="bulktransfercareoffvalidation">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="contactIDCTR" id="contactIDCTR">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="transfer-careoff" class="form-label">Transfer Careoff <span class="text-danger">*</span></label>
                                        <select name="careoff_id" id="transfer-careoff" class="form-select select22" data-placeholder="Select Careoff" data-allow-clear="true">
                                            <option value=""></option>
                                            @foreach ($adminusers as $adminuser2)
                                                <option value="{{ $adminuser2->id }}">{{ $adminuser2->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Transfer Careoff</button>
                            {{-- <button class="btn btn-sm btn-primary" type="button" id="transferLeadOwnerBtn">Transfer Lead Owner</button> --}}
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Bulk Transfer Careoff End -->

        <!-- Edit Contact Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="updateReg" aria-labelledby="updateRegLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="updateRegLabel" class="offcanvas-title">Edit Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form action="{{ route('admin.allcontact.update') }}" id="editUserForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="editID" id="edit_ID">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="lead_type" class="form-label">Business Type</label>
                                <select name="lead_type" id="lead_type" class="form-control select2e lead_type" data-allow-clear="true" data-placeholder="Select Business Type">
                                    <option value="">Select</option>
                                    <option value="Unknown">Other</option>
                                    <option value="Job seeker">Job seeker</option>
                                    <option value="Direct Wakala Candidate">Direct Wakala Candidate</option>
                                    <option value="Agent without office">Agent without office </option>
                                    <option value="Associate with office">Associate with office</option>
                                    <option value="Company">Company</option>
                                    <option value="HIRING US">HIRING US</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-lcs-id" class="form-label">Life Cycle Status</label>
                                <select name="lcs_id" id="edit-lcs-id" class="form-select select2e" data-allow-clear="true" data-placeholder="Select Life Cycle Status">
                                    <option value="">Select</option>
                                    @foreach ($lifecyclestatus as $lcs2)
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
                                <input type="text" name="job_title" id="job_title" required class="form-control" placeholder="Enter Job Title">

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
                                <label for="edit-mobile-no" class="form-label">Primary Number (Whatsapp Number)</label>
                                <input type="text" name="primary_no_wsp" id="edit-mobile-no" class="form-control edit-mobile-no">
                                <input type="hidden" name="primary_no_wsp_dial_code" class="mobile-no-dial-code-ed" id="mobile-no-dial-code-ed">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-primary-no" class="form-label">Secondary Number</label>
                                <input type="text" name="secondary_no_wsp" id="edit-primary-no" class="form-control edit-primary-no">
                                <input type="hidden" name="secondary_no_wsp_dial_code" class="primary-con-dial-code-ed" id="primary-con-dial-code-ed">
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-phone0" class="form-label">Mobile No.01</label>
                                <input type="text" name="mobile_no1_wsp" id="edit-phone0" class="form-control edit-phone0">
                                <input type="hidden" name="mobile_no1_wsp_dial_code" class="phone0-dial-code-ed" id="phone0-dial-code-ed">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-phone1" class="form-label">Mobile No.02</label>
                                <input type="text" name="mobile_no2_wsp" id="edit-phone1" class="form-control edit-phone1">
                                <input type="hidden" name="mobile_no2_wsp_dial_code" class="phone1-dial-code-ed" id="phone1-dial-code-ed">
                            </div>
                        </div>

                        {{-- <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-phone2" class="form-label">Mobile No.04</label>
                                <input type="text" name="mobile_no3_wsp" id="edit-phone2" class="form-control edit-phone2">
                                <input type="hidden" name="mobile_no3_wsp_dial_code" class="phone2-dial-code-ed" id="phone2-dial-code-ed">
                            </div>
                        </div> --}}

                        <div class="col-md-4">
                            <div class="mb-3">

                            <label for="edit-email" class="form-label">Email 1</label>
                            <input type="text" name="email" id="edit-email" class="form-control">
                        </div>

                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-email0" class="form-label">Email 2</label>
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
                                <label class="form-label" for="edit-country-id">Country</label>
                                <select name="country_id" id="edit-country-id" class="form-select select2e"  data-placeholder="Select Country" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="edit-city-id">City</label>
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
                                    <option value=""></option>
                                    @foreach ($groupms as $groupm25)
                                        <option value="{{ $groupm25->id }}">{{ $groupm25->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Source</label>
                                    <select name="edit_source" id="edit_source" class="form-select select2e" data-placeholder="Select Source" data-allow-clear="true">
                                            <option value="">Select Source</option>
                                            @foreach ($globalSourceType as $source)
                                                <option value="{{ $source->id }}">{{ $source->name }}</option>
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

        <!-- Add Contact Start -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Contact</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form action="{{ route('admin.allcontact.store') }}" id="addUserForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add_lead_type" class="form-label">Business Type</label>
                                <select name="lead_type" id="add_lead_type" class="form-control select2e lead_type" data-allow-clear="true" data-placeholder="Select Business Type">
                                    <option value="">Select</option>
                                    <option value="Unknown">Other</option>
                                    <option value="Job seeker" selected>Job seeker</option>
                                    <option value="Direct Wakala Candidate">Direct Wakala Candidate</option>
                                    <option value="Agent without office">Agent without office </option>
                                    <option value="Associate with office">Associate with office</option>
                                    <option value="Company">Company</option>
                                    <option value="Trade site Center">Trade site Center</option>
                                    <option value="HIRING US">HIRING US</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-lcs-id" class="form-label">Life Cycle Status</label>
                                <select name="lcs_id" id="add-lcs-id" class="form-select select2e" data-allow-clear="true" data-placeholder="Select Life Cycle Status">
                                    <option value="">Select</option>
                                    @foreach ($lifecyclestatus as $lcs2)
                                        <option value="{{ $lcs2->id }}" @if($lcs2->id == 1) selected @endif>{{ $lcs2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-ls-id" class="form-label">Lead Stage</label>
                                <select name="ls_id" id="add-ls-id" class="form-select select2e" data-allow-clear="true" data-placeholder="Select Lead Stage">
                                    <option value="">Select</option>
                                    @foreach ($leadstages as $ls2)
                                        <option value="{{ $ls2->id }}" @if($ls2->id == 1) selected @endif>{{ $ls2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4" id="full_name_div_add">
                            <div class="mb-3">
                                <label for="add-full-name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" id="add-full-name" placeholder="Please enter full name" class="form-control" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-4" id="job_title_div_add">
                            {{-- <div class="col-md-4" id="job_title_div_add" style="display: none"> --}}
                            <div class="mb-3">
                                <label for="add_job_title" class="form-label">Job Title</label>
                                <input type="text" name="job_title" id="add_job_title" required class="form-control" placeholder="Enter Job Title">

                                <div class="job_title_list" id="job_title_list">

                                </div>
                            </div>
                        </div>

                        <div class="col-md-4" id="job_desg_div_add" style="display: none">
                            <div class="mb-3">
                                <label for="add_job_desg" class="form-label">Designation</label>
                                <input type="text" name="job_desg" id="add_job_desg" class="form-control" placeholder="Enter Designation">
                                <div id="job_desg_list" class="job_desg_list">

                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" id="company_name_div_add" style="display: none">
                            <div class="mb-3">
                                <label for="add-company-name">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control" id="add-company-name" placeholder="Enter Company Name">
                            </div>
                        </div>

                        <div class="col-md-4" id="indust_id_div_add" style="display: none">

                            <div class="mb-3">
                                <label for="add-industry-id">Industry <span class="text-danger">*</span></label>
                                <select name="indust_id" class="form-control select2e" data-allow-clear="true" data-placeholder="Select Industry" id="add-industry-id">
                                    <option value="">Select</option>
                                @foreach ($industries as $industry)
                                    <option value="{{  $industry->id }}">{{  $industry->name}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4" id="office_name_div_add" style="display: none">
                            <div class="mb-3">
                                <label for="add-agency-name">Agency Name <span class="text-danger">*</span></label>
                                <input type="text" name="office_name" id="add-agency-name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-mobile-no" class="form-label">Primary Number (Whatsapp Number)</label>
                                <input type="text" name="primary_no_wsp" id="add-mobile-no" class="form-control add-mobile-no">
                                <input type="hidden" name="primary_no_wsp_dial_code" id="mobile-no-dial-code">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-primary-no" class="form-label">Secondary Number</label>
                                <input type="text" name="secondary_no_wsp" id="add-primary-no" class="form-control add-primary-no">
                                <input type="hidden" name="secondary_no_wsp_dial_code" id="primary-con-dial-code">
                            </div>
                        </div>



                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-phone0" class="form-label">Mobile No.01</label>
                                <input type="text" name="mobile_no1_wsp" id="add-phone0" class="form-control add-phone0">
                                <input type="hidden" name="mobile_no1_wsp_dial_code" id="phone0-dial-code">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-phone1" class="form-label">Mobile No.02</label>
                                <input type="text" name="mobile_no2_wsp" id="add-phone1" class="form-control add-phone1">
                                <input type="hidden" name="mobile_no2_wsp_dial_code" id="phone1-dial-code">

                            </div>
                        </div>

                        {{-- <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-phone2" class="form-label">Mobile No.04</label>
                                <input type="text" name="mobile_no3_wsp" id="add-phone2" class="form-control add-phone2">
                                <input type="hidden" name="mobile_no3_wsp_dial_code" id="phone2-dial-code">
                            </div>
                        </div> --}}

                        <div class="col-md-4">
                            <div class="mb-3">

                            <label for="add-email" class="form-label">Email 1</label>
                            <input type="text" name="email" id="add-email" class="form-control">
                        </div>

                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-email0" class="form-label">Email 2</label>
                                <input type="text" name="email0" id="add-email0" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-email1" class="form-label">Email 3</label>
                                <input type="text" name="email1" id="add-email1" class="form-control">
                            </div>
                        </div>

                        {{-- <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-email2" class="form-label">Email 4</label>
                                <input type="text" name="email2" id="add-email2" class="form-control">
                            </div>
                        </div> --}}



                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="add-country-id">Country</label>
                                <select name="country_id" id="add-country-id" class="form-select select2e" data-placeholder="Select Country" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="add-city-id">City</label>
                                <select name="city_id" id="add-city-id" class="form-select select2e" data-placeholder="Select City" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-careoff" class="form-label">Careoff <span class="text-danger">*</span></label>
                                <select name="careoff_id" id="add-careoff" class="form-select select2e" data-placeholder="Select Careoff" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($admins as $adminuser3)
                                        <option value="{{ $adminuser3->id }}" @if(Auth::guard('admin')->user()->id == $adminuser3->id) selected @endif>{{ $adminuser3->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-leadowner" class="form-label">Lead Owner <span class="text-danger">*</span></label>
                                <select name="leadowner_id" id="add-leadowner" class="form-select select2e" data-placeholder="Select Lead Owner" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($admins as $adminuser4)
                                        <option value="{{ $adminuser4->id }}" @if(Auth::guard('admin')->user()->id == $adminuser4->id) selected @endif>{{ $adminuser4->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Source</label>
                                    <select name="ins_source" id="ins_source" class="form-select select2e" data-placeholder="Select Source" data-allow-clear="true">
                                            <option value="">Select Source</option>
                                            @foreach ($globalSourceType as $source)
                                                <option value="{{ $source->id }}">{{ $source->name }}</option>
                                            @endforeach
                                    </select>                                
                                </div>
                            </div>

                        <div class="col-md-12" id="descr_div">
                            <div class="mb-3">
                                <label for="add-descr" class="form-label">Description</label>
                                <textarea name="descr" id="add-descr" class="form-control" cols="30" rows="5"></textarea>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <div class="mb-3 mt-3">
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" name="send_whatsapp" type="checkbox" id="add-send-whatsapp" value="1" />
                                    <label class="form-check-label" for="add-send-whatsapp">Send Msg</label>
                                </div>
                            </div>
                        </div>

                    </div>
                    {{-- <button type="button" class="btn btn-primary me-sm-3 me-1 data-submit" id="editnewcontact">Submit</button> --}}
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>

                </form>
            </div>
        </div>
        <!-- Add Contact End -->

        <!-- Filter Panel Start -->
        <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="filterpanelLabel">Allcontact Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <select id="by-business-type" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Business Type">
                                    @foreach ($leadtypefilters as $leadtypefilter)
                                        <option value="{{ $leadtypefilter->lead_type }}" @if(isset($allcontactsaveadminfilter) && in_array($leadtypefilter->lead_type,explode(",",$allcontactsaveadminfilter->lead_type))) selected @endif>{{ $leadtypefilter->lead_type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-careoff-id" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Careoff">
                                    @foreach ($careofffilters as $careofffilter)
                                        <option value="{{ $careofffilter->id }}" @if(isset($allcontactsaveadminfilter) && in_array($careofffilter->id,explode(",",$allcontactsaveadminfilter->careoff_id))) selected @endif>{{ $careofffilter->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-create-by" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Create By">
                                @foreach ($createbyfilters as $item)
                                    <option value="{{ $item->user_id }}"
                                        @if(isset($allcontactsaveadminfilter) && in_array($item->user_id, explode(",", $allcontactsaveadminfilter->user_id))) selected @endif>
                                        
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-groupby-id" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Group">
                                    <!-- <option value="gb0">Null</option> -->
                                    @foreach ($groupfilters as $groupfilter)
                                        @if(isset($groupfilter->group_id))
                                        <option value="{{ $groupfilter->group_id }}" @if(isset($allcontactsaveadminfilter) && in_array($groupfilter->group_id,explode(",",$allcontactsaveadminfilter->group_id))) selected @endif>{{ $groupfilter->group->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-lifecycle-status" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Lifecycle Status">
                                    @foreach ($lifecyclestfilters as $lifecyclestfilter)
                                        <option value="{{ $lifecyclestfilter->lcs_id }}" @if(isset($allcontactsaveadminfilter) && in_array($lifecyclestfilter->lcs_id,explode(",",$allcontactsaveadminfilter->lcs_id))) selected @endif>{{ $lifecyclestfilter->lcs->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-lead-stage" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Lead Stage">
                                    @foreach ($leadstagefilters as $leadstagefilter)
                                        <option value="{{ $leadstagefilter->ls_id }}" @if(isset($allcontactsaveadminfilter) && in_array($leadstagefilter->ls_id,explode(",",$allcontactsaveadminfilter->ls_id))) selected @endif>{{ $leadstagefilter->ls->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-lead-priority" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Lead Priority">
                                    @foreach ($leadpriofilters as $leadpriofilter)
                                        <option value="{{ $leadpriofilter->lead_prority }}" @if(isset($allcontactsaveadminfilter) && in_array($leadpriofilter->lead_prority,explode(",",$allcontactsaveadminfilter->lead_prority))) selected @endif>{{ $leadpriofilter->lead_prority }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-industry" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Industry">
                                    @foreach ($industryfilters as $industryfilter)
                                        <option value="{{ $industryfilter->indust_id }}" @if(isset($allcontactsaveadminfilter) && in_array($industryfilter->indust_id,explode(",",$allcontactsaveadminfilter->indust_id))) selected @endif>{{ $industryfilter->indust->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <select id="by-state-id" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select State">
                                    @foreach ($statefilters as $statefilter)
                                        {{-- <option value="{{ $statefilter->state_id }}" @if(isset($allcontactsaveadminfilter) && in_array($statefilters->state_id,explode(",",$allcontactsaveadminfilter->state_id))) selected @endif>{{ $statefilter->state->name }}</option> --}}
                                        <option value="{{ $statefilter->state_id }}">{{ $statefilter->state->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-country-id" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Country">
                                    @foreach ($countryfilters as $countryfilter)
                                        <option value="{{ $countryfilter->country_id }}" @if(isset($allcontactsaveadminfilter) && in_array($countryfilter->country_id,explode(",",$allcontactsaveadminfilter->country_id))) selected @endif>{{ $countryfilter->country->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @php
                                $conversationTypes = isset($allcontactsaveadminfilter) && $allcontactsaveadminfilter->conversation_type
                                    ? explode(',', $allcontactsaveadminfilter->conversation_type)
                                    : [];

                            @endphp


                            <div class="col-md-4 mb-3">
                                <select id="by-conversation-type" multiple class="selectpicker w-100" data-live-search="true" data-actions-box="true" data-style="default-btn" title="Conversation Type">
                                    <option value="Normal" {{ in_array('Normal', $conversationTypes) ? 'selected' : '' }}>Normal</option>
                                    <option value="Call" {{ in_array('Call', $conversationTypes) ? 'selected' : '' }}>Call</option>
                                    <option value="Whatsapp" {{ in_array('Whatsapp', $conversationTypes) ? 'selected' : '' }}>Whatsapp</option>
                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <input name="by_created_date" type="text" id="by-created-date" @if(isset($allcontactsaveadminfilter)) value="{{ $allcontactsaveadminfilter->by_created_date }}" @endif class="form-control bsdatpicket" placeholder="Created Date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <input name="by_updated_date" type="text" id="by-updated-date" @if(isset($allcontactsaveadminfilter)) value="{{ $allcontactsaveadminfilter->by_updated_date }}" @endif class="form-control bsdatpicket" placeholder="Updated Date...">
                            </div>

                            <div class="col-md-4 mb-3">
                                <select name="followup_before" id="by-followup-before"
                                    class="selectpicker w-100"
                                    data-actions-box="true"
                                    data-live-search="true"
                                    data-style="default-btn"
                                    title="Select followup before">
                                    <option value="">Select followup before</option>
                                    <option value="1" {{ ($allcontactsaveadminfilter->followup_before ?? '') == 1 ? 'selected' : '' }}>Today</option>
                                    <option value="3" {{ ($allcontactsaveadminfilter->followup_before ?? '') == 3 ? 'selected' : '' }}>3 Days</option>
                                    <option value="7" {{ ($allcontactsaveadminfilter->followup_before ?? '') == 7 ? 'selected' : '' }}>7 Days</option>
                                    <option value="14" {{ ($allcontactsaveadminfilter->followup_before ?? '') == 14 ? 'selected' : '' }}>14 Days</option>
                                    <option value="30" {{ ($allcontactsaveadminfilter->followup_before ?? '') == 30 ? 'selected' : '' }}>1 Month</option>

                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <select id="by-city-id" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select City">
                                    @foreach ($cityfilters as $cityfilter)
                                        <option value="{{ $cityfilter->city_id }}" @if(isset($allcontactsaveadminfilter) && in_array($cityfilter->city_id,explode(",",$allcontactsaveadminfilter->city_id))) selected @endif>{{ $cityfilter->city->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="col-md-4 mb-3">
                                <select id="by-assignee-id" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Assignee">
                                    @foreach ($assigneefilters as $assigneefilter)
                                        <option value="{{ $assigneefilter->assign_id }}" @if(isset($allcontactsaveadminfilter) && in_array($assigneefilter->assign_id,explode(",",$allcontactsaveadminfilter->assign_id))) selected @endif>{{ $assigneefilter->assign->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}


                            @if (Auth::guard('admin')->user()->user_type == 1)
                            <div class="col-md-4 mb-3">
                                <select id="by-lead-owner" class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Lead Owner">
                                    @foreach ($leadownerfilters as $leadownerfilter)
                                        <option value="{{ $leadownerfilter->owner_id }}" @if(isset($allcontactsaveadminfilter) && in_array($leadownerfilter->owner_id,explode(",",$allcontactsaveadminfilter->owner_id))) selected @endif>{{ $leadownerfilter->owner->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="col-md-4 mb-3">
                                <select id="by-coutry-dial-code"
                                    class="selectpicker w-100"
                                    multiple
                                    data-live-search="true"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select Country">

                                    <option value="91"
                                        {{ isset($allcontactsaveadminfilter) && in_array('91', array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code ?? ''))) ? 'selected' : '' }}>
                                        India (+91)
                                    </option>

                                    <option value="966"
                                        {{ isset($allcontactsaveadminfilter) && in_array('966', array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code ?? ''))) ? 'selected' : '' }}>
                                        Saudi Arabia (+966)
                                    </option>
                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <select id="by-coutry-dial-code-number"
                                    class="selectpicker w-100"
                                    multiple
                                    data-live-search="true"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select Country Mobile Code">

                                    <option value="91"
                                        {{ isset($allcontactsaveadminfilter) && in_array('91', array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code_number ?? ''))) ? 'selected' : '' }}>
                                        India (+91)
                                    </option>

                                    <option value="966"
                                        {{ isset($allcontactsaveadminfilter) && in_array('966', array_map('trim', explode(',', $allcontactsaveadminfilter->country_dial_code_number ?? ''))) ? 'selected' : '' }}>
                                        Saudi Arabia (+966)
                                    </option>
                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <select id="by-exlude-coutry-mobile-code"
                                    class="selectpicker w-100"
                                    multiple
                                    data-live-search="true"
                                    data-actions-box="true"
                                    data-style="default-btn"
                                    title="Select Exclude Country Mobile Code">

                                    <option value="91"
                                        {{ isset($allcontactsaveadminfilter) && in_array('91', array_map('trim', explode(',', $allcontactsaveadminfilter->exlude_country_mobile_code ?? ''))) ? 'selected' : '' }}>
                                        India (+91)
                                    </option>

                                    <option value="966"
                                        {{ isset($allcontactsaveadminfilter) && in_array('966', array_map('trim', explode(',', $allcontactsaveadminfilter->exlude_country_mobile_code ?? ''))) ? 'selected' : '' }}>
                                        Saudi Arabia (+966)
                                    </option>
                                </select>
                            </div>


                            {{-- <div class="col-md-4 mb-3">
                                <label class="switch switch-square">
                                    <input type="checkbox" name="short_form_code" id="short_form_code" class="switch-input" @if(isset($allcontactsaveadminfilter) && $allcontactsaveadminfilter->short_form_code == 1) checked @endif />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"><i class="ti ti-check"></i></span>
                                        <span class="switch-off"><i class="ti ti-x"></i></span>
                                    </span>
                                    <span class="switch-label">Short Form</span>
                                </label>
                            </div> --}}

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

        <!-- Bulk Delete Start -->
        <div class="modal fade" id="bulkdelete" aria-hidden="true" aria-labelledby="bulkdeleteLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.allcontact.bulk.delete') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="contactids" id="delcontactIDS">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="mb-3">
                                      <p class="text-danger">Are you sure to delete contact?</p>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Bulk Delete End -->

        <!-- Delete Contact Staff Start -->
        <div class="modal fade" id="deletescon" aria-hidden="true" aria-labelledby="deletesconLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Delete Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.allcontact.delete') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="contactID" id="contactID2">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="mb-3">
                                      <p class="text-danger">Are you sure to delete contact?</p>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                         </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete Contact Staff End -->

        <!--- Start Bulk Import Contact Start -->
        <div class="modal fade" id="bulkimport" aria-hidden="true" aria-labelledby="bulkimportLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    
                    <div class="modal-header">
                        <h5 class="offcanvas-title" id="updateLstageLabel">Import Contact</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="importValidationAllcontact" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-body">
                            <div class="row">
                                
                                <!-- CSV Upload -->
                                <div class="col-md-10 mb-3">
                                    <label for="add-allcontact-csv-data" class="form-label">Upload CSV File</label>
                                    <input type="file" name="allcontact_csv_data" id="add-allcontact-csv-data" class="form-control" accept=".csv">
                                </div>
                                <div class="col-md-2 mt-4">
                                    <button type="button" class="btn btn-primary btn-sm" id="uploadCSVBtn">Upload CSV</button>                                
                                </div>


                                <!-- 🔽 Dropdown Section (first 3 replaced with real DB-driven ones) -->
                                    <div class="col-md-4 6_dropdowns" style="display:none;">
                                        <div class="mb-3">
                                            <label for="bulk_lead_type" class="form-label">Business Type<span class="text-danger">*</span></label>
                                            <select name="lead_type" id="bulk_lead_type" class="form-control select2e lead_type" data-allow-clear="true" data-placeholder="Select Business Type">
                                                <option value="">Select</option>
                                                <option value="Unknown">Other</option>
                                                <option value="Job seeker">Job seeker</option>
                                                <option value="Direct Wakala Candidate">Direct Wakala Candidate</option>
                                                <option value="Agent without office">Agent without office </option>
                                                <option value="Associate with office">Associate with office</option>
                                                <option value="Company">Company</option>
                                                <option value="Trade site Center">Trade site Center</option>
                                                <option value="HIRING US">HIRING US</option>
                                            </select>
                                            <span class="text-danger error-display-business-type" style="display:none;"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-4 6_dropdowns" style="display:none;">
                                        <div class="mb-3">
                                            <label for="bulk-lcs-id" class="form-label">Life Cycle Status</label>
                                            <select name="lcs_id" id="bulk-lcs-id" class="form-select select2e" data-allow-clear="true" data-placeholder="Select Life Cycle Status">
                                                <option value="not_required">Not Required</option>
                                                @foreach ($lifecyclestatus as $lcs2)
                                                    <option value="{{ $lcs2->id }}">{{ $lcs2->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 6_dropdowns" style="display:none;">
                                        <div class="mb-3">
                                            <label for="bulk-ls-id" class="form-label">Lead Stage</label>
                                            <select name="ls_id" id="bulk-ls-id" class="form-select select2e" data-allow-clear="true" data-placeholder="Select Lead Stage">
                                                <option value="not_required">Not Required</option>
                                                @foreach ($leadstages as $ls2)
                                                    <option value="{{ $ls2->id }}">{{ $ls2->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 6_dropdowns" style="display:none;">
                                        <div class="mb-3">
                                            <label for="bulk-careoff" class="form-label">Careoff</label>
                                            <select name="careoff_id" id="bulk-careoff" class="form-select select2e" data-placeholder="Select Careoff" data-allow-clear="true">
                                                <option value="not_required">Not Required</option>
                                                @foreach ($admins as $adminuser3)
                                                    <option value="{{ $adminuser3->id }}" @if(Auth::guard('admin')->user()->id == $adminuser3->id) selected @endif>{{ $adminuser3->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 6_dropdowns" style="display:none;">
                                        <div class="mb-3">
                                            <label for="bulk-group-id" class="form-label">Group</label>
                                            <select name="group_id" id="bulk-group-id" class="form-select select2e" data-placeholder="Select Group" data-allow-clear="true">
                                            <option value="not_required">Not Required</option>
                                                @foreach ($groupms as $groupm25)
                                                    <option value="{{ $groupm25->id }}">{{ $groupm25->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 6_dropdowns" style="display:none;">
                                        <div class="mb-3">
                                            <label for="bulk-source" class="form-label">Source</label>
                                            <select name="source" id="add_source" class="form-select select2e" data-allow-clear="true" data-placeholder="Select Source">
                                                <option value="">Select Source</option>
                                                @foreach ($globalSourceType as $source)
                                                    <option value="{{ $source->id }}">{{ $source->name }}</option>
                                                @endforeach
                                            </select>     
                                            <!-- <option value="not_required">Not Required</option>
                                            <option value="Direct">Direct</option>
                                            <option value="Facebook">Facebook</option>
                                            <option value="Instagram">Instagram</option>
                                            <option value="Google">Google</option>
                                            <option value="LinkedIn">LinkedIn</option>
                                            <option value="Quora">Quora</option>
                                            <option value="Twitter">Twitter</option>
                                            <option value="Bing">Bing</option>
                                            <option value="Reddit">Reddit</option> -->
                                        </div>
                                    </div>

                                <!-- Error Display -->
                                <div class="col-md-12">
                                    <span class="text-danger error-display" style="display:none;"></span>
                                </div>

                                <!-- Preview Table -->
                                <div class="col-md-12 mt-3">
                                    <p class="error-message text-danger" style="display:none;"></p>
                                    <table id="previewTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 50%;">CSV Header</th>
                                                <th style="width: 50%;">DB List Field</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamically filled via JS -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Progress Bar -->
                                <div class="col-md-12 mt-2">
                                    <div class="progress" style="height: 20px; display:none;" id="importProgressBarContainer">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                            role="progressbar" style="width: 0%;" id="importProgressBar">
                                            0%
                                        </div>
                                    </div>
                                </div>


                                <!-- Duplicate / Result Display -->
                                <div class="col-md-12 mt-2">
                                    <div class="duplicate-display" style="display:none;"></div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                Close
                            </button>
                           
                            <button type="button" class="btn btn-primary btn-sm" id="importCSVData" style="display:none;">
                                Import Data
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!-- Start Bulk Import Contact End -->

        <!-- Check Mobile Number Modal Start -->
        <div class="modal fade" id="checkMobileModal" tabindex="-1" aria-labelledby="checkMobileModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">

            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="checkMobileModalLabel">Check Mobile Number</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row">

                            <!-- Input -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Enter Mobile Number</label>
                                <input type="text" id="check-mobile-input" class="form-control"
                                    placeholder="Type mobile number...">
                                <small class="text-danger" id="mobile-check-error" style="display:none;"></small>
                            </div>

                            <!-- Result Table -->
                            <div class="col-md-12">
                                <table class="table table-bordered" id="mobileResultTable" style="display:none;">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Created By</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>

        </div>
        <!-- Check Mobile Number Modal End -->


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
    <!-- <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script> -->
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/pickr/pickr.js') }}"></script>
    <!-- <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script> -->
    <script src="{{ asset('user/intl-tel-input-master/build/js/intlTelInput-jquery.min.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/validation/all-contact-validation.js') }}"></script>
    @if(session('isModelShow') == true)
        <script>
            $(document).ready(function(){

                var contactId = "{{ session('isModelShowId') }}";

                // create a temporary element with the same attributes
                var fakeButton = $('<a>')
                    .attr('data-bs-toggle', 'modal')
                    .attr('data-bs-target', '#editStatus')
                    .attr('data-id', contactId);

                // manually trigger the modal event with relatedTarget
                $('#editStatus').trigger({
                    type: 'show.bs.modal',
                    relatedTarget: fakeButton[0]
                });

                var modal = new bootstrap.Modal(document.getElementById('editStatus'));
                modal.show();

                $.ajax({
                    url: "{{ route('admin.allcontact.edit') }}",
                    method: "GET",
                    type: "html",
                    data: {
                        id: contactId
                    },
                    success: function(data){
                        
                            var lcs_id = data.post.lcs_id;
                            var contact_id = contactId;
                            $("#allcontactID").val(contact_id);
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

                        $('#update-lead-cycle-status').val(data.post.lcs_id).change();


                    }
                });

                               

            });
        </script>
    @endif
    <script>

        
        $(document).ready(function(){
            let csvData = [];
            let csvHeaders = [];


            $('#uploadCSVBtn').on('click',function(){
                let formData = new FormData();
                formData.append("csv_file", $("#add-allcontact-csv-data")[0].files[0]);

                $.ajax({
                    url: "{{ route('admin.allcontact.csv.import.preview') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    success: function(data){
                        csvHeaders = data.headers;
                        csvData = data.rows; // Store CSV rows for later use
                        postData = data.post;

                        let tableRows = "";

                        // console.log(csvHeaders);
                        // console.log(csvData);
                        
                        $(".6_dropdowns").show();


                        csvHeaders.forEach((col, index) => {
                            tableRows += `
                                <tr>
                                    <td>${col}</td>
                                    <td>
                                        <select class="field-mapping form-select" data-index="${index}">
                                            <option value="">Not Required</option>
                                            <option value="lead_type">Business Type</option>
                                            <option value="full_name">Full Name</option>
                                            <option value="primary_no_wsp">Primary Mobile No</option>
                                            <option value="secondary_no_wsp">Secondary Mobile Number</option>
                                            <option value="mobile_no1_wsp">Mobile No 1</option>
                                            <option value="mobile_no2_wsp">Mobile No 2</option>
                                            <option value="mobile_no3_wsp">Mobile No 3</option>
                                            <option value="email">Email</option>
                                            <option value="email0">Email 1</option>
                                            <option value="email1">Email 2</option>
                                            <option value="email2">Email 3</option>
                                            <option value="state_id">State</option>
                                            <option value="city_id">City</option>
                                            <option value="company_name">Company Name</option>
                                            <option value="full_address">Full Address</option>
                                            <option value="country_id">Country</option>
                                        </select>
                                    </td>
                                </tr>`;
                        });

                        $("#previewTable tbody").html(tableRows);
                        $("#importCSVData").show();
                        $('#uploadCSVBtn').hide();
                        $('.error-display').hide();
                        $('.error-display-business-type').hide();

                    }
                });

            });

            $('#importCSVData').on('click', function () {

                let mappedFields = {};
                let selectedFields = [];
                let selectedCount = 0;
                let hasDuplicate = false;

                $(".field-mapping").each(function () {
                    let index = $(this).data("index");
                    let field = $(this).val();

                    if (field) {
                        if (selectedFields.includes(field)) {
                            hasDuplicate = true;
                        }
                        selectedFields.push(field);
                        mappedFields[field] = index;
                        selectedCount++;
                    }
                });


                if ($('#bulk_lead_type').val() == '') {
                    $(".error-display-business-type").text("Business type is required.").show();
                    return;
                }  
                else if (selectedCount < 3) {
                    $(".error-display-business-type").hide();
                    $(".error-display").text("Please select at least three fields.").show();
                    return;
                } 
                else if (hasDuplicate) {
                    $(".error-display-business-type").hide();
                    $(".error-display").text("Each field must be unique. Please correct duplicates.").show();
                    return;
                } else {
                    $(".error-display").hide();
                    $(".error-display-business-type").hide();
                }

                // Reset progress bar
                $("#importProgressBar").css("width", "0%").text("0%");
                $("#importProgressBarContainer").show();
                $(".duplicate-display").hide().html('');

                // Simulate smooth progress (fake until response)
                let progress = 0;
                const interval = setInterval(() => {
                    if (progress < 95) { // cap at 95% until response
                        progress += Math.floor(Math.random() * 5) + 1; // random increment
                        if (progress > 95) progress = 95;
                        $("#importProgressBar").css("width", progress + "%").text(progress + "%");
                    }
                }, 200);

               
                console.log( csvData.length);
                
                // Proceed with import AJAX
                $.ajax({
                    url: "{{ route('admin.allcontact.csv.upload') }}",
                    type: "POST",
                    data: {
                        mapped_fields: mappedFields,
                        csv_data: csvData,
                        lead_type: $('#bulk_lead_type').val(),
                        lcs_id: $('#bulk-lcs-id').val(),
                        ls_id: $('#bulk-ls-id').val(),
                        careoff_id: $('#bulk-careoff').val(),
                        group_id: $('#bulk-group-id').val(),
                        source: $('#add_source').val(),
                    },
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    beforeSend: function () {
                        $("#importCSVData").prop("disabled", true).text("Importing...");
                    },
                    success: function (response) {
                        clearInterval(interval);
                        $("#importProgressBar").css("width", "100%").text("100%");
                        $("#importCSVData").prop("disabled", false).text("Import Data");

                        setTimeout(() => {
                            $("#importProgressBarContainer").fadeOut();
                        }, 800);

                        let msg = `
                            <div class="alert alert-success mt-2">
                                ✅ File processed successfully.<br>
                                <strong>Inserted:</strong> ${response.imported_count} records<br>
                                <strong>Duplicates:</strong> ${response.duplicate_count} records
                            </div>
                        `;

                        if (response.duplicates && response.duplicates.length > 0) {
                            msg += `
                                <div class="alert alert-warning mt-2">
                                    <strong>Duplicate Entries Found:</strong>
                                    <ul style="max-height:150px; overflow-y:auto; padding-left: 20px;">
                                        ${response.duplicates.map(d => `<li>${d}</li>`).join('')}
                                    </ul>
                                </div>
                            `;
                        }

                        $(".duplicate-display").html(msg).fadeIn();
                    },
                    error: function (xhr) {
                        clearInterval(interval);
                        $("#importProgressBar").css("width", "100%").addClass("bg-danger").text("Failed");
                        $("#importCSVData").prop("disabled", false).text("Import Data");

                        let errMsg = "Something went wrong during import.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }

                        $(".duplicate-display").html(`
                            <div class="alert alert-danger mt-2">
                                ❌ ${errMsg}
                            </div>
                        `).fadeIn();
                    }
                });
            });

            $('#bulkimport').on('show.bs.modal', function (e) {
                // Clear file input
                $('#add-allcontact-csv-data').val('');

                // Hide 6 dropdown
                $('.6_dropdowns').hide();

                // Hide and reset preview table
                $('#previewTable tbody').empty();

                // Hide progress bar
                $('#importProgressBarContainer').hide();
                $('#importProgressBar').css('width', '0%').text('0%');

                // Hide messages and errors
                $('.error-display').hide().text('');
                $('.error-display-business-type').hide().text('');
                $('.duplicate-display').hide().html('');

                // Reset buttons
                $('#uploadCSVBtn').show();
                $('#importCSVData').hide();

            });

            // Prevent duplicate selections dynamically
            $(document).on("change", ".field-mapping", function () {
                let selectedOptions = [];
                $(".field-mapping").each(function () {
                    let val = $(this).val();
                    if (val) selectedOptions.push(val);
                });

                $(".field-mapping").each(function () {
                    let currentVal = $(this).val();
                    $(this).find("option").each(function () {
                        if ($(this).val() && $(this).val() !== currentVal) {
                            $(this).prop("disabled", selectedOptions.includes($(this).val()));
                        }
                    });
                });
            });

        });
    </script>

    <script>
        $(document).ready(function(){
            // Select22
            var select22 = $('.select22');
            if (select22.length) {
                select22.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                    //   placeholder: 'Select value',
                    dropdownParent: $this.parent()
                    });
                });
            }

            const select2e = $('.select2e');

            if (select2e.length) {
                select2e.each(function(){
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        dropdownParent: $this.parent()
                    });
                });
            }

            $('body').on('shown.bs.modal', '#filterpanel', function() {
                $(this).find('.select22f').each(function() {

                    $(this).select2({
                        dropdownParent: $(this).parent()

                    });
                });
            });

            var selectPicker = $('.selectpicker');

            if (selectPicker.length) {
                selectPicker.selectpicker();
            }

            // Trigger Status if avalable
            $('#editStatus').on("show.bs.modal",function(e){
                var allcontactID = $(e.relatedTarget).data('id') || $('#editStatus').data('id');
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
                            // $('#update-lead-cycle-status').val(data.post.lcs_id).change();

                            $('#update-lead-cycle-status').val(data.post.lcs_id).trigger('change');
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

            // Update Prority Type
            $('.lead-priority').on('click',function(){
                var lead_priority = $(this).text();
                var allcontactID = $(this).data('id');


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
                                    timer: 1000, // 1 seconds
                                    showConfirmButton: false,
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false

                                });

                                // Reload after 2.2 seconds (slightly longer than timer)
                                setTimeout(function () {
                                    location.reload();
                                }, 1100);

                            }
                        });
                    }

                });
            });

            // Update Optin and Optout
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




            $('#lead_type').on('change',function(){
                var leadtype = $(this).val();
                if(leadtype == 'Job seeker'){
                $('#job_title_div').show();
                $('#job_title').show();
                $('#indust_id_div').hide();
                $('#company_name_div').hide();
                }else if(leadtype == 'HIRING US'){
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

            $('#add_lead_type').on('change',function(){
                var leadtype = $(this).val();
                if(leadtype == 'Job seeker'){
                    $('#job_title_div_add').show();
                    $('#indust_id_div_add').hide();
                    $('#company_name_div_add').hide();
                    $('#office_name_div_add').hide();
                }else if(leadtype == 'HIRING US'){
                    $('#job_title_div_add').show();
                    $('#indust_id_div_add').hide();
                    $('#company_name_div_add').hide();
                    $('#office_name_div_add').hide();
                }else if (leadtype == 'Company') {
                    $('#job_title_div_add').hide();
                    $('#office_name_div_add').hide();
                    $('#indust_id_div_add').show();
                    $('#company_name_div_add').show();
                }else if (leadtype == 'Associate with office') {
                    $('#job_title_div_add').hide();
                    $('#company_name_div_add').hide();
                    $('#indust_id_div_add').hide();
                    $('#office_name_div_add').show();
                }else{
                    $('#job_title_div_add').hide();
                    $('#indust_id_div_add').hide();
                    $('#company_name_div_add').hide();
                    $('#office_name_div_add').hide();
                }

            });

            $('#deletescon').on("show.bs.modal",function(e){
                var deleteID = $(e.relatedTarget).data('id');
                $('#contactID2').val(deleteID);
            });

        });
    </script>

    <script>
        $(document).ready(function(){
            $('#updateReg').on("show.bs.offcanvas", function (e) {
                const editID = $(e.relatedTarget).data('id');
                const staff_id = "{{ Auth::guard('admin')->user()->id }}";
                $('#edit_ID').val(editID);

                $.ajax({
                    url: '{{ route("admin.allcontact.edit") }}',
                    method: "GET",
                    data: { id: editID },
                    success: function (data) {
                        const post = data.post;
                        const phones = data.update_phones;

                        // Basic field assignments
                        $('#lead_type').val(post.lead_type).trigger('change');
                        $('#edit-lcs-id').val(post.lcs_id).trigger('change');
                        $('#edit-ls-id').val(post.ls_id).trigger('change');
                        $('#edit-full-name').val(post.full_name);
                        $('#job_title').val(post.job_title);
                        $('#edit-company-name').val(post.company_name);
                        $('#edit-industry-id').val(post.indust_id).trigger('change');
                        $('#edit-agency-name').val(post.office_name);
                        $('#edit-primary-no').val(phones.secondary_no_wsp);
                        $('#edit-mobile-no').val(phones.primary_no_wsp);
                        $('#edit-phone0').val(phones.mobile_no1_wsp);
                        $('#edit-phone1').val(phones.mobile_no2_wsp);
                        $('#edit-phone2').val(phones.mobile_no3_wsp);
                        $('#edit-email').val(post.email);
                        $('#edit-email0').val(post.email0);
                        $('#edit-email1').val(post.email1);
                        $('#edit-email2').val(post.email2);
                        $('#edit-country-id').val(post.country_id).trigger('change');
                        $('#edit-city-id').val(post.city_id).trigger('change');
                        $('#edit-careoff').val(post.careoff_id).trigger('change');
                        $('#edit-leadowner').val(post.owner_id ?? staff_id).trigger('change');
                        $('#edit-group-id').val(data.group_id).trigger('change');
                        $('#edit-descr').val(post.descr);

                        $('#edit_source').val(post.source_id).trigger('change');

                        // Lead type conditional fields
                        toggleLeadTypeFields(post.lead_type);

                        // Dial code fields
                        $('#primary-con-dial-code-ed').val(phones.secondary_no_wsp_dial_code);
                        $('#mobile-no-dial-code-ed').val(phones.primary_no_wsp_dial_code);
                        $('#phone0-dial-code-ed').val(phones.mobile_no1_wsp_dial_code);
                        $('#phone1-dial-code-ed').val(phones.mobile_no2_wsp_dial_code);
                        $('#phone2-dial-code-ed').val(phones.mobile_no3_wsp_dial_code);

                        // Initialize intlTelInput for all phone fields
                        initIntlTelInput('.edit-primary-no', phones.secondary_no_wsp_iso_code, '.primary-con-dial-code-ed');
                        initIntlTelInput('.edit-mobile-no', phones.primary_no_wsp_iso_code, '.mobile-no-dial-code-ed');
                        initIntlTelInput('.edit-phone0', phones.mobile_no1_wsp_iso_code, '.phone0-dial-code-ed');
                        initIntlTelInput('.edit-phone1', phones.mobile_no2_wsp_iso_code, '.phone1-dial-code-ed');
                        initIntlTelInput('.edit-phone2', phones.mobile_no3_wsp_iso_code, '.phone2-dial-code-ed');
                    }
                });
            });

            /**
             * Show/Hide sections based on lead_type
            */

            function toggleLeadTypeFields(lead_type) {
                const show = (id) => $(id).show();
                const hide = (id) => $(id).hide();

                // Hide all optional fields first
                hide('#job_title_div');
                hide('#office_name_div');
                hide('#job_desg_div');
                hide('#company_name_div');
                hide('#indust_id_div');

                if (lead_type === 'Job seeker') {
                    show('#job_title_div');
                }else if (lead_type === 'HIRING US') {
                    show('#job_title_div');
                } else if (lead_type === 'Company') {
                    show('#job_desg_div');
                    show('#company_name_div');
                    show('#indust_id_div');
                } else if (lead_type === 'Associate with office') {
                    show('#office_name_div');
                }
            }

            /**
             * Initialize intlTelInput plugin for a phone field
            */

            function initIntlTelInput(selector, initialCountryISO, dialCodeTarget) {
                const input = $(selector);
                if (!input.length) return;

                input.intlTelInput('destroy');

                input.intlTelInput({
                    onlyCountries: ["sa", "in", "qa", "ae", "kw"],
                    preferredCountries: ["in", "sa"],
                    separateDialCode: true,
                    initialCountry: initialCountryISO
                }).on('countrychange', function () {
                    const dialCode = input.intlTelInput("getSelectedCountryData").dialCode;
                    $(dialCodeTarget).val(dialCode);
                });
            }

        });
    </script>


    <script>
        $(document).ready(function(){

            const allowedCountries = ["sa", "in", "qa", "ae", "kw"];
            const preferredCountries = ["in", "sa"];

            function initializeIntlTelInput(selector, dialCodeInputId){
                const $input = $(selector);

                $input.intlTelInput({
                    onlyCountries: allowedCountries,
                    preferredCountries: preferredCountries,
                    separateDialCode: true,
                    initialCountry: "",
                }).on('countrychange', function(){
                    const dialCode = $input.intlTelInput("getSelectedCountryData").dialCode;
                    $(dialCodeInputId).val(dialCode);
                });
            }

            // Initialize all fields with corresponding dial code input IDs
          //  initializeIntlTelInput('.add-short-mobile-no', '#mobile-no-dial-code-short');
            initializeIntlTelInput('.add-primary-no', '#primary-con-dial-code');
            initializeIntlTelInput('.add-mobile-no', '#mobile-no-dial-code');
            initializeIntlTelInput('.add-phone0', '#phone0-dial-code');
            initializeIntlTelInput('.add-phone1', '#phone1-dial-code');
            initializeIntlTelInput('.add-phone2', '#phone2-dial-code');


        });
    </script>

    <script>
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

        $('#bulktransfergroup').on('show.bs.modal',function(e){
            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });


            if (allselectedvals <= 0) {

            }else{
                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");

                $("#totlaTransferGrp").text("Total Transfer "+countc_id);
                $('#contactIDGRPTR').val(join_all_selected_values);
            }
        });

        $('#bulkcountryupdate').on('show.bs.modal',function(e){
            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });

            if (allselectedvals <= 0) {

            }else{
                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");

                $('#contactIDGRPTRC').val(join_all_selected_values);
            }
        });

        $('#bulkcountrycodeupdate').on('show.bs.modal',function(e){
            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });
            if (allselectedvals <= 0) {

            }else{
                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");

                $('#getListBulkUpdate').text(countc_id+" contacts will be update, are you sure?");
                $('#contactIDGRPTRC2').val(join_all_selected_values);
            }

        });

        $('#bulkcountrycodeupdatefield').on('show.bs.modal', function(e){
            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });

            if (allselectedvals <= 0) {

            } else {
                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");

                $('#getListBulkUpdate25').text(countc_id+" contacts will be update, are you sure?");
                $('#contactIDGRPTRC25').val(join_all_selected_values);
            }
        });

        $('#bulktransferleadowner').on('show.bs.modal',function(e){
            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });




            if (allselectedvals <= 0) {
                // alert("Please select atleast one checkbox");
                // location.reload();
            } else {
                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");

                $('#contactIDLTRB').val(join_all_selected_values);


            }

        });

        $("#bulktransfercareoff").on('show.bs.modal',function(e){
            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });



            if (allselectedvals <= 0) {

            }else{
                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");

                $('#contactIDCTR').val(join_all_selected_values);
            }

        });

        $('#bulkwhatsappsend').on('show.bs.offcanvas',function(e){
            var allselectedvals = [];
            $('.dt-checkboxes:checked').each(function(){
                allselectedvals.push($(this).attr('data-id'));
            });

            if (allselectedvals <= 0) {
                // alert("Please select atleast one checkbox");
                // location.reload();
            } else {
                var countc_id = allselectedvals.length;
                var join_all_selected_values = allselectedvals.join(",");

                $('#contactpID2').val(join_all_selected_values);
            }

        });

        // $("#transfer-groupm").on("change",function(){
        //     var groupValue = $(this).val();
        //     var totalsend = $("#contactIDGRPTR").val();

        //     if (groupValue != '') {
        //         jQuery.ajax({
        //             url: "{{ route('admin.allcontact.checkgroupLimit') }}",
        //             method: "GET",
        //             type: "html",
        //             data:{
        //                 "grpID": groupValue,
        //                 "totalSend": totalsend
        //             },
        //             success: function(data){

        //                 $("#respMessageGroupTransfer").text(data.message);
        //                 $('#respGroupLimit').text(data.grouplimit);
        //                 if (data.status == 1) {
        //                     $(".dibtngrp").attr("disabled",false);
        //                 } else {
        //                     $(".dibtngrp").attr("disabled",true);
        //                 }

        //             }
        //         });
        //     } else {
        //         $("#respMessageGroupTransfer").text("");
        //         $('#respGroupLimit').text("");
        //         $(".dibtngrp").attr("disabled",false);
        //     }
        // });

        $('#send-whatsapp-type').on('change',function(){
            var sendwhatsapptype = $(this).val();
            if (sendwhatsapptype == "meta_whatsapp") {
                $('#send-template-name').val('').trigger('change');
                $('.dismetawhatsapp').show();
                $('.disnormalwhatsapp').hide();
                $('.disallforwhatsapp').show();
            } else if(sendwhatsapptype == "normal_whatsapp"){
                $('#send-meta-template-name').val('').trigger('change');
                $('.dismetawhatsapp').hide();
                $('.disnormalwhatsapp').show();
                $('.disallforwhatsapp').show();
            } else {
                $('#send-meta-template-name').val('').trigger('change');
                $('#send-template-name').val('').trigger('change');
                $('.dismetawhatsapp').hide();
                $('.disnormalwhatsapp').hide();
                $('.disallforwhatsapp').hide();
            }
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
                var template_id = $(this).val();

                var imgPath = "{{ asset('admin/assets/images/template') }}";
                var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";

                if (template_id != '') {
                    jQuery.ajax({
                        url : "{{ url('admin/whatsapp-campaign-list/template/get') }}",
                        method: "GET",
                        type: "html",
                        data: {
                            "id": template_id,
                            // "_token": "{{ csrf_token() }}",
                        },
                        success: function(data){


                            $('#send-template-message').val(data.msg_whatsapp);
                            $('#send-template-message-ar').val(data.msg_whatsapp_ar);
                            if (data.file != '') {
                                var file_path = imgPath+'/'+data.file;
                                $('.uploadedAvatar2').attr("src",file_path);
                            } else {
                                $('.uploadedAvatar2').attr("src",blankImg);
                            }
                        }
                    });
                } else {
                    $('#send-template-message').val('');
                    $('#send-template-message-ar').val('');
                    $('.uploadedAvatar2').attr("src",blankImg);
                }
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

        $('#send-template-personalise-class').on('change',function(){
            var partvalue = $(this).val();
            navigator.clipboard.writeText(partvalue);
            toastr['success']('Text copied - '+partvalue+'', 'Success', { hideDuration: 3000 });
        });

        $('#bulkdelete').on('show.bs.modal',function(e){
                var allselectedvals = [];
                $('.dt-checkboxes:checked').each(function(){
                    allselectedvals.push($(this).attr('data-id'));
                });


                if (allselectedvals <= 0) {

                }else{
                    var countc_id = allselectedvals.length;
                    var join_all_selected_values = allselectedvals.join(",");

                    // $("#totlaTransferGrp").text("Total Transfer "+countc_id);
                    $('#delcontactIDS').val(join_all_selected_values);
                }

            });


    </script>


    <!-- Bulk Send Whatsapp End -->
    <script>
        $(document).ready(function(){
            var bsRangePickerBasic = $('.bsdatpicket');
            // var singledatepicket = $('.singledatepicker');
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
            // if (singledatepicket.length) {
            //     singledatepicket.daterangepicker({
            //         // todayHighlight: true,
            //         opens: isRtl ? 'left' : 'right',
            //         autoUpdateInput: false,
            //         singleDatePicker: true,
            //         locale: {
            //             cancelLabel: 'Clear',
            //             format: 'YYYY-MM-DD'
            //         }
            //     });
            // }

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

                    state_id: $('#by-state-id').val(),
                    country_id: $('#by-country-id').val(),
                    city_id: $('#by-city-id').val(),
                    followup_before: $('#by-followup-before').val(),
                    conversation_type: $('#by-conversation-type').val(),
                    lcs_id: $('#by-lifecycle-status').val(),
                    ls_id: $('#by-lead-stage').val(),
                    businesstype_id: $('#by-business-type').val(),
                    lead_priority: $("#by-lead-priority").val(),
                    lead_owner: $('#by-lead-owner').val(),
                    industries: $('#by-industry').val(),
                    country_dial_code: $('#by-coutry-dial-code').val(),
                    country_dial_code_number: $('#by-coutry-dial-code-number').val(),
                    exlude_country_mobile_code: $('#by-exlude-coutry-mobile-code').val(),
                    short_form_code: $('#short_form_code').is(':checked') ?"1":"0",
                    // send_tag: $('#by-send-tag').val(),
                    // send_date: $('#by-send-date').val(),
                    // created_at: $('#by-created-date').val(),
                    careoff: $('#by-careoff-id').val(),
                    created_by: $('#by-create-by').val(),
                    group_id: $('#by-groupby-id').val(),
                    created_at: $('#by-created-date').val(),
                    updated_at: $('#by-updated-date').val()
                }
            }

            // Function to reload todo list based on filter data
            function reloadTodoList() {
                $.ajax({
                    url: "{{ route('admin.allcontact.list') }}",
                    method: "GET",
                    type: "html",
                    data: getFilterData(),
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });

                updateAllContactFilterIndicator();
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
            bindFilterChange('#by-state-id');
            bindFilterChange('#by-country-id');
            bindFilterChange('#by-city-id');
            bindFilterChange('#by-followup-before');
            bindFilterChange('#by-conversation-type');
            bindFilterChange('#by-lifecycle-status');
            bindFilterChange('#by-lead-stage');
            bindFilterChange('#by-business-type');
            bindFilterChange('#by-lead-priority');
            bindFilterChange('#by-lead-owner');
            bindFilterChange('#by-coutry-dial-code');
            bindFilterChange('#by-coutry-dial-code-number');
            bindFilterChange('#by-exlude-coutry-mobile-code');
            bindFilterChange('#by-industry');
            // bindFilterChange('#by-send-tag');
            // bindFilterChange('#by-send-date');
            bindFilterChange('#by-created-date');
            bindFilterChange('#by-updated-date');
            bindFilterChange('#by-create-by');
            bindFilterChange('#by-groupby-id');
            bindFilterChange('#by-careoff-id');

            // Date Range Picker for Start and End Dates with Apply and Cancel Event Handling
            function bindDateRangePicker(selector) {
                $(selector).on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    reloadTodoList();
                }).on('cancel.daterangepicker', function () {
                    $(this).val('');
                    reloadTodoList();
                });
            }

            // function bindDatePicker(selector) {
            //     $(selector).on('apply.daterangepicker', function (ev, picker) {
            //         $(this).val(picker.startDate.format('YYYY-MM-DD'));
            //         reloadTodoList();
            //     }).on('cancel.daterangepicker', function () {
            //         $(this).val('');
            //         reloadTodoList();
            //     });
            // }

            bindDateRangePicker('input[name="by_created_date"]');
            bindDateRangePicker('input[name="by_updated_date"]');

            // bindDatePicker('input[name="created_date_filter"]');
            // bindDatePicker('input[name="send_date_filter"]');


            // Save Admin Filter
            $(document).on('click','.savetodoFilter',function(){
                var state_id = $('#by-state-id').val();
                var country2 = $('#by-country-id').val();
                var city2 = $('#by-city-id').val();
                var by_followup_before = $('#by-followup-before').val();
                var conversation_type = $('#by-conversation-type').val();
                var groupID = $('#by-groupby-id').val();
                var lcs = $('#by-lifecycle-status').val();
                var ls = $('#by-lead-stage').val();
                var businesstype = $('#by-business-type').val();
                var lead_priority = $('#by-lead-priority').val();
                var lead_owner = $('#by-lead-owner').val();
                var country_dial_code = $('#by-coutry-dial-code').val();
                var country_dial_code_number = $('#by-coutry-dial-code-number').val();
                var exlude_country_mobile_code = $('#by-exlude-coutry-mobile-code').val();
                var industries = $('#by-industry').val();
                var careoff = $('#by-careoff-id').val();
                var by_created_date = $('#by-created-date').val();
                var by_updated_date = $('#by-updated-date').val();
                var short_form_code = $('#short_form_code').is(':checked') ?"1":"0";
                //     var sendtag = $('#by-send-tag').val();
                //     var senddate = $('#by-send-date').val();
                //     var createddate = $('#by-created-date').val();
                //     var updatestatusdate = $('#by-update-status-date').val();
                var createdBy = $('#by-create-by').val();


                jQuery.ajax({
                    url: "{{ route('admin.allcontact.saveadminfilter') }}",
                    method: "POST",
                    type: "html",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        state_id: state_id,
                        country_id: country2,
                        city_id: city2,
                        followup_before: by_followup_before,
                        conversation_type: conversation_type,
                        group_id: groupID,
                        lcs_id: lcs,
                        ls_id: ls,
                        businesstype_id: businesstype,
                        lead_priority: lead_priority,
                        lead_owner: lead_owner,
                        industry_id: industries,
                        careoff: careoff,
                        created_by: createdBy,
                        by_created_date: by_created_date,
                        by_updated_date: by_updated_date,
                        country_dial_code: country_dial_code,
                        country_dial_code_number: country_dial_code_number,
                        exlude_country_mobile_code: exlude_country_mobile_code,
                        short_form_code: short_form_code,
                    },
                    success: function(data){
                        if(data){
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                });
            });
           
            // Reset Admin Filter
            $(document).on('click', '.resetfilter', function () {

                $('.selectpicker').selectpicker('deselectAll');

                if ($('#by-business-type').val() != '') {
                    $('#by-business-type').val('').trigger('change');
                }

                if ($('#by-careoff-id').val() != '') {
                    $('#by-careoff-id').val('').trigger('change');
                }

                if ($('#by-create-by').val() != '') {
                    $('#by-create-by').val('').trigger('change');
                }

                if ($('#by-groupby-id').val() != '') {
                    $('#by-groupby-id').val('').trigger('change');
                }

                if ($('#by-lifecycle-status').val() != '') {
                    $('#by-lifecycle-status').val('').trigger('change');
                }

                if ($('#by-lead-stage').val() != '') {
                    $('#by-lead-stage').val('').trigger('change');
                }

                if ($('#by-lead-priority').val() != '') {
                    $('#by-lead-priority').val('').trigger('change');
                }

                if ($('#by-industry').val() != '') {
                    $('#by-industry').val('').trigger('change');
                }

                if ($('#by-state-id').val() != '') {
                    $('#by-state-id').val('').trigger('change');
                }

                if ($('#by-country-id').val() != '') {
                    $('#by-country-id').val('').trigger('change');
                }

                if ($('#by-city-id').val() != '') {
                    $('#by-city-id').val('').trigger('change');
                }

                if ($('#by-followup-before').val() != '') { 
                    $('#by-followup-before').val('').trigger('change');
                }

                if ($('#by-conversation-type').val() != '') {
                    $('#by-conversation-type').val('').trigger('change');
                }
                
                if ($('#by-lead-owner').val() != '') {
                    $('#by-lead-owner').val('').trigger('change');
                }

                if ($('#by-coutry-dial-code').val() != '') {
                    $('#by-coutry-dial-code').val('').trigger('change');
                }

                if ($('#by-coutry-dial-code-number').val() != '') {
                    $('#by-coutry-dial-code-number').val('').trigger('change');
                }

                if ($('#by-exlude-coutry-mobile-code').val() != '') {
                    $('#by-exlude-coutry-mobile-code').val('').trigger('change');
                }

                if ($('#by-created-date').val() != '') {
                    $('#by-created-date').trigger('cancel.daterangepicker');
                }

                if ($('#by-updated-date').val() != '') {
                    $('#by-updated-date').trigger('cancel.daterangepicker');
                }

                // Reset Short Form
                $('#short_form_code').prop('checked', false);

                $.ajax({
                    url: "{{ route('admin.allcontact.resetadminfilter') }}",
                    method: "POST",
                    type: "html",
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function (data) {
                        if (data) {
                            toastr['success'](data.res, 'Success', { hideDuration: 3000 });
                        }
                    }
                });

                updateAllContactFilterIndicator();

            });

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
                    $('.contactpaginate').html(data);

                }).fail(function(){

                    alert("Something gone wrong!")
                });
            }

        });
    </script>

    {{-- <script>
        document.getElementById("disableshortcode").addEventListener("click", function() {
            this.classList.add("d-none");
            document.getElementById("enableshortcode").classList.remove("d-none");
            document.getElementById("hideshowaddmodule").classList.add("d-none");
        });

        document.getElementById("enableshortcode").addEventListener("click", function() {
            this.classList.add("d-none");
            document.getElementById("disableshortcode").classList.remove("d-none");
            document.getElementById("hideshowaddmodule").classList.remove("d-none");
        });
    </script> --}}


    <script>
        $(document).ready(function(){
            $('.switch-input').on('change',function(){
                if($(this).is(':checked')){
                    // document.getElementById("hideshowaddmodule").classList.remove("d-none");
                    $('.hideshowaddmodule').show();
                }else{
                    // document.getElementById("hideshowaddmodule").classList.add("d-none");
                    $('.hideshowaddmodule').hide();
                }
            });

            $('#short_form_code2').on('change',function(){
                var short_form_code = $(this).is(':checked') ?'1':'0';

                jQuery.ajax({
                    url: "{{ route('admin.allcontact.saveshortformcode') }}",
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

    <script>
        $(document).ready(function(){
            $("#check-mobile-input").on("keyup", function () {
                let mobile = $(this).val();
                $("#mobileResultTable").hide();
                $("#mobileResultTable tbody").html("");

                if (mobile.length < 5) return; // Start checking after 5 digits

                $.ajax({
                    url: "{{ route('admin.checkMobileDetails') }}",
                    method: "POST",
                    data: {
                        mobile: mobile,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {

                        if (!response.status) {
                            $("#mobile-check-error").text(response.message).show();
                            return;
                        }

                        $("#mobile-check-error").hide();

                        let row = `
                            <tr>
                                <td>${response.data.name}</td>
                                <td>${response.data.mobile_in}</td>
                                <td>${response.data.created_by}</td>
                                <td>${response.data.created_at}</td>
                            </tr>
                        `;

                        $("#mobileResultTable tbody").html(row);
                        $("#mobileResultTable").show();
                    }
                });
            });
        });
    </script>

    <script>
        // =====================================================================
        // ALL CONTACT FILTER INDICATOR (SHOW / HIDE DOT)
        // =====================================================================
        function updateAllContactFilterIndicator() {

            let selectFields = [
                '#by-business-type',
                '#by-careoff-id',
                '#by-create-by',
                '#by-groupby-id',
                '#by-lifecycle-status',
                '#by-lead-stage',
                '#by-lead-priority',
                '#by-industry',
                '#by-state-id',
                '#by-country-id',
                '#by-city-id',
                '#by-followup-before',
                '#by-conversation-type',
                '#by-lead-owner',
                '#by-coutry-dial-code',
                '#by-coutry-dial-code-number',
                '#by-exlude-coutry-mobile-code'
            ];

            let isFiltered = false;

            // ✅ Check all select fields
            selectFields.forEach(function(selector) {
                if ($(selector).length && $(selector).val() && $(selector).val().length > 0) {
                    isFiltered = true;
                }
            });

            // ✅ Check date fields
            if ($('#by-created-date').val() ||
                $('#by-updated-date').val()) {
                isFiltered = true;
            }

            // ✅ Check short form switch
            if ($('#short_form_code').is(':checked')) {
                isFiltered = true;
            }

            // ✅ Show / Hide indicator
            if (isFiltered) {
                $('.filterpanel .filter-indicator')
                    .removeClass('d-none')
                    .addClass('d-block');
            } else {
                $('.filterpanel .filter-indicator')
                    .removeClass('d-block')
                    .addClass('d-none');
            }
        }

        $(document).ready(function () {


            // =====================================================================
            // TRIGGERS (Bootstrap Select + Inputs + Checkbox)
            // =====================================================================
            $('.selectpicker').on('changed.bs.select', function () {
                updateAllContactFilterIndicator();
            });

            $('#by-created-date, #by-updated-date').on('change keyup', function () {
                updateAllContactFilterIndicator();
            });

            // =====================================================================
            // INITIAL LOAD (SAVED FILTER SUPPORT)
            // =====================================================================
            updateAllContactFilterIndicator();

        });
    </script>

@endsection
