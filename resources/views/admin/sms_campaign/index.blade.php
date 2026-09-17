@extends('layout.admin.admin_layout')

@section('title','Campaign List')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/dropzone/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/quill/editor.css') }}" />

    <style>
        .pagestyle{
            height: calc(2.25rem + 2px);
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out
        }
        .pagestyle:focus{
            color: #6e6b7b;
            background-color: #fff;
            border-color: #7367f0;
            outline: 0;
            box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1);
        }
        .shortformmargin{
            /* margin-right: 195px; */
        }
    </style>


    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($permission) && $permission->add_sms_campaign == 0)
        <style>
            .add_sms_campaign{
                display: none !important;
            }
        </style>
        @endif
        @if (isset($permission) && $permission->view_sms_campaign == 0)
        <style>
        .view_sms_campaign{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($permission) && $permission->delete_sms_campaign == 0)
        <style>
        .delete_sms_campaign{
            display: none !important;
        }
        </style>
        @endif

    @endif

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>No of Send</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">{{ $campaig_rate['send'] }}</h4>
                        <span class="text-success">(+29%)</span>
                        </div>
                        <span>Total Message Sent</span>
                    </div>
                    <span class="badge bg-label-primary rounded p-2">
                        <i class="ti ti-user ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>No of Failed</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">{{ $campaig_rate['failed'] }}</h4>
                        <span class="text-danger">({{ round($campaig_rate['faild_ratio']) }}%)</span>
                        </div>
                        <span>Total Failed Message </span>
                    </div>
                    <span class="badge bg-label-danger rounded p-2">
                        <i class="ti ti-user-plus ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Process for Message</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">{{ $campaig_rate['success'] }}</h4>
                        <span class="text-success">({{round($campaig_rate['success_ratio'])}}%)</span>
                        </div>
                        <span>Last week analytics</span>
                    </div>
                    <span class="badge bg-label-success rounded p-2">
                        <i class="ti ti-user-check ti-sm"></i>
                    </span>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom">
                <div class="mb-1 float-start">
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

                {{-- <div class="px-3 float-start">
                    <button class="btn btn-xs btn-primary filterpanel" data-bs-toggle="modal" data-bs-target="#filterpanel"><i class="ti ti-filter"></i> Filter</button>
                </div> --}}

                <div class="mb-1 float-end mx-2">
                    <button class="add-new btn btn-sm btn-primary addcontact addcandidate add_sms_campaign" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">SMS Campaign</span></button>
                </div>

                <div class="mb-1 float-end">
                    <input type="text" id="search_text" class="form-control" placeholder="Search...">
                </div>
            </div>
            <div class="card-datatable table-responsive contactpaginate">
                @include('admin.sms_campaign.load')
            </div>
        </div>

    </div>

    <!--- Add SMS Campaign Start --->
    <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add SMS Campaign</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.smsCampaign.campaignStore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-audience" class="form-label">Audience <span class="text-danger">*</span></label>
                            <select name="audience" id="add-audience" class="form-select select2 add-audience" data-allow-clear="true" data-placeholder="Select Audience">
                                <option value=""></option>
                                <option value="Leads">Leads</option>
                                <option value="partner">Partner</option>
                                <option value="client">Client</option>
                                <option value="associate">Associate</option>
                                <option value="contactp">Contact Plus</option>
                                <option value="allcontact">Allcontact</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-sms-api-name" class="form-label">SMS API Name <span class="text-danger">*</span></label>
                            <select name="sms_api_id" id="add-sms-api-name" class="form-select select2" data-allow-clear="true" data-placeholder="Select API Name...">
                                <option value="">Select</option>
                                @foreach ($smsApiLists as $smsApiList)
                                    <option value="{{ $smsApiList->id }}">{{ $smsApiList->api_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-template-name" class="form-label">SMS Template <span class="text-danger">*</span></label>
                            <select name="sms_template_id" id="add-template-name" class="form-select select2 templateListDynamic" data-allow-clear="true" data-placeholder="Select SMS Template">
                                <option value="">Select</option>
                            </select>
                        </div>
                    </div>



                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="add-campaign-name">Campaign Name <span class="text-danger">*</span></label>
                            <input type="text" name="campaign_name" id="add-campaign-name" class="form-control" placeholder="Enter campaign name...">
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="add-sms-campaign-name">SMS Campaign Name <span class="text-danger">*</span></label>
                            <input type="text" name="sms_campaign_name" id="add-sms-campaign-name" class="form-control" placeholder="Enter campaign name...">
                        </div>
                    </div> --}}


                </div>
                <!-- Leads View Start -->
                <div class="row" id="disleadsview" style="display: none">

                    <!-- ✅ Is Qualified -->
                    <div class="col-md-3">
                        <div class="mb-2">
                            <label for="lead_is_qualified" class="form-label" style="font-size: 12px;">Is Qualified</label>
                            <select name="lead_is_qualified[]" 
                                    id="lead_is_qualified" 
                                    class="form-select form-select-sm select2 lead_is_qualified" 
                                    multiple 
                                    data-placeholder="Select Qualification Status">
                                <option value="1">Qualified</option>
                                <option value="0">Not Qualified</option>
                            </select>
                        </div>
                    </div>

                    <!-- ✅ Assign To -->
                    <div class="col-md-3">
                        <div class="mb-2">
                            <label for="leadassign_id" class="form-label" style="font-size: 12px;">Assign To</label>
                            <select name="leadassign_id[]" 
                                    id="leadassign_id" 
                                    class="form-select form-select-sm select2 leadassign_id" 
                                    multiple 
                                    data-placeholder="Select Assigned Admin">
                                @foreach ($careoffs as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- ✅ Contact Type -->
                    <div class="col-md-3">
                        <div class="mb-2">
                            <label for="lead_contact_type" class="form-label" style="font-size: 12px;">Contact Type <span class="text-danger">*</span></label>
                            <select name="lead_contact_type" 
                                    id="lead_contact_type" 
                                    class="form-select form-select-sm select2 lead_contact_type" 
                                    data-allow-clear="true" 
                                    data-placeholder="Select Contact Type...">
                                <option value="">All</option>
                                <option value="mob_no">Mobile Number</option>
                                <option value="whatsapp_no">WhatsApp Number</option>
                            </select>
                        </div>
                    </div>

                </div>
                <!-- Leads View End -->
                <!-- Allcontact View Start -->
                <div class="row" id="disallcontactview" style="display: none">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-allcontact-business-type" class="form-label">Business Type</label>
                            <select name="lead_type[]" id="add-allcontact-business-type" class="form-select select2 add-allcontact-business-type" multiple data-placeholder="Select Business Type">
                                <option value="Unknown">Other</option>
                                <option value="Job seeker">Job seeker</option>
                                <option value="Direct Wakala Candidate">Direct Wakala Candidate</option>
                                <option value="Agent without office">Agent without office </option>
                                <option value="Associate with office">Associate with office</option>
                                <option value="Company">Company</option>
                                <option value="Trade site Center">Trade site Center</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-allcontact-careoff" class="form-label">Careoff <span class="text-danger">*</span></label>
                            {{-- <select name="careoff_id2[]" id="add-allcontact-careoff" class="selectpicker w-100 add-allcontact-careoff" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Careoff">
                                @foreach ($careoffs as $careoff2)
                                    <option value="{{ $careoff2->id }}">{{ $careoff2->name }}</option>
                                @endforeach
                            </select> --}}
                            <select name="careoff_id2[]" id="add-allcontact-careoff" class="form-select select2 add-allcontact-careoff" multiple data-placeholder="Select Careoff">
                                @foreach ($careoffs as $careoff2)
                                    <option value="{{ $careoff2->id }}">{{ $careoff2->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-allcontact-groupm" class="form-label">Group <span class="text-danger">*</span></label>
                            <select name="groupmallc[]" id="add-allcontact-groupm" class="form-select select2 add-allcontact-groupm" multiple data-placeholder="Select Group">

                                @foreach ($groupallcs as $groupallc)
                                    <option value="{{ $groupallc->id }}">{{ $groupallc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-allcontact-country" class="form-group">Country</label>
                            <select name="country_id[]" id="add-allcontact-country" class="form-select select2 add-allcontact-country" multiple data-placeholder="Select Country">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-allcontact-mobile-country" class="form-group">Mobile Coutry Code</label>
                            <select name="country_code[]" id="add-allcontact-mobile-country" class="form-select select2 add-allcontact-mobile-country" multiple data-placeholder="Select Country Code">
                                <option value="91">India (+91)</option>
                                <option value="966">Saudi Arabia (+966)</option>
                                <option value="971">UAE (+971)</option>
                                <option value="974">Qatar (+974)</option>
                                <option value="965">Kuwait (+965)</option>
                                <option value="968">Oman (+968)</option>
                            </select>
                        </div>
                    </div>
                    {{-- <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-status-allcontact" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="allcontact_status[]" id="add-status-allcontact" class="form-select select2" multiple data-placeholder="Select Status...">
                                <option value=""></option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div> --}}
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-subscribe-allcontact" class="form-label">Subscribe <span class="text-danger">*</span></label>
                            <select name="allcontact_subscribe[]" id="add-subscribe-allcontact" class="form-select select2 add-subscribe-allcontact" multiple data-placeholder="Select Subscribe...">
                                <option value=""></option>
                                <option value="1">Subscribe</option>
                                <option value="0">Unsubscribe</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-allcontact-type" class="form-label">Contact Type <span class="text-danger">*</span></label>
                            <select name="allcontact_contact_type" id="add-allcontact-type" data-allow-clear="true" data-placeholder="Select Contact Type..." class="form-select select2 add-allcontact-type">
                                <option value=""></option>
                                <option value="1">All</option>
                                <option value="2">Owner Contact</option>
                                <option value="3">Primary Contact</option>
                                <option value="4">Secondary Contact</option>
                            </select>
                        </div>
                    </div>


                </div>
                <!-- Allcontact View End -->
                <!-- Dispaly No of Contacts where be selected Start -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <span class="text-success displayContactList"></span>
                    </div>
                </div>
                <!-- Dispaly No of Contacts where be selected End -->
                <!-- Contactp View Start -->
                <div class="row" id="discontactpview" style="display: none">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-business-type" class="form-label">Business Type</label>
                            <select name="business_type_contact[]" id="add-business-type" class="form-select select2 add-business-type" multiple data-placeholder="Select Business Type...">
                                <option value=""></option>
                                @foreach ($businesstypes as $businesstype)
                                    <option value="{{ $businesstype->id }}">{{ $businesstype->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- <div class="col-md-4">
                        <div class="mb-3">
                            <label for="add-status-contactp" class="form-label">Status</label>
                            <select name="contactp_status[]" id="add-status-contactp" class="form-select select2" multiple data-placeholder="Select Status...">
                                <option value=""></option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div> --}}
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-subscribe-contactp" class="form-label">Subscribe <span class="text-danger">*</span></label>
                            <select name="contactp_subscribe[]" id="add-subscribe-contactp" class="form-select select2 add-subscribe-contactp" multiple data-placeholder="Select Subscribe...">
                                <option value=""></option>
                                <option value="1">Subscribe</option>
                                <option value="0">Unsubscribe</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-contactp-contact-group" class="form-label">Group <span class="text-danger">*</span></label>
                            <select name="contact_group[]" id="add-contactp-contact-group" class="form-select select2 add-contactp-contact-group" data-placeholder="Select group" multiple>
                                <option value=""></option>
                                @foreach ($groupms as $groupm)
                                    <option value="{{ $groupm->id }}">{{ $groupm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-contactp-contact-type" class="form-label">Contact Type <span class="text-danger">*</span></label>
                            <select name="contactp_contact_type" id="add-contactp-contact-type" class="form-select select2 add-contactp-contact-type" data-placeholder="Select Contact Type..." data-allow-clear="true">
                                <option value=""></option>
                                <option value="1">All</option>
                                <option value="2">Owner Contact</option>
                                <option value="3">Primary Contact</option>
                                <option value="4">Secondary Contact</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-contactp-careoff" class="form-label">Careoff <span class="text-danger">*</span></label>
                            <select name="careoff_id[]" id="add-contactp-careoff" class="form-select2 select2 add-contactp-careoff" multiple data-placeholder="Select Careoff">

                                @foreach ($careoffs as $careoff)
                                    <option value="{{ $careoff->id }}">{{ $careoff->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- <div class="col-md-3">
                        <div class="mb-3">
                            <label for="add-contact-mobile-country" class="form-group">Mobile Coutry Code <span class="text-danger">*</span></label>
                            <select name="country_code2[]" id="add-contact-mobile-country" class="form-select select2" multiple data-placeholder="Select Country Code">
                                <option value="91">India (+91)</option>
                                <option value="966">Saudi Arabia (+966)</option>
                                <option value="971">UAE (+971)</option>
                                <option value="974">Qatar (+974)</option>
                                <option value="965">Kuwait (+965)</option>
                                <option value="968">Oman (+968)</option>
                            </select>
                        </div>
                    </div> --}}

                    {{-- <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-field-contactp-variable" class="form-label">Field Variable <span class="text-danger">*</span></label>
                            <select name="field_var_contactp[]" id="add-field-contactp-variable" class="form-select fieldVariable select2" data-placeholder="Select Field Variable" data-allow-clear="true">
                                <option value=""></option>
                                <option value="header_image">Header Image</option>
                                <option value="header_video">Header Video</option>
                                <option value="header_document">Header Document</option>
                                <option value="header_document_name">Header Document Name</option>
                                <option value="field_1">Field 1</option>
                                <option value="field_2">Field 2</option>
                                <option value="field_3">Field 3</option>
                                <option value="field_4">Field 4</option>
                                <option value="field_5">Field 5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-assign-var-contactp" class="form-label">Assign Contactp Variable <span class="text-danger">*</span></label>
                            <select name="assign_var_contactp[]" id="add-assign-var-contactp" class="form-select assignVariableContactPOPT select2" data-placeholder="Select Assign Variable..." data-allow-clear="true">
                                <option value=""></option>
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
                    </div> --}}
                </div>
                <div id="dispContactp"></div>
                {{-- <div class="row" id="dispAddContactpBtn" style="display: none">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-primary addContactpDisp float-end">Add</button>
                    </div>
                </div> --}}
                <!-- Contactp View End -->
                <!-- Associate View Start -->
                <div class="row" id="disassocview" style="display: none">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-status-assoc" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="assoc_status[]" id="add-status-assoc" class="form-select select2" multiple data-placeholder="Select Status...">
                                <option value=""></option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-contact-type-assoc">Contact Type <span class="text-danger">*</span></label>
                            <select name="assoc_contact_type" id="add-contact-type-assoc" class="form-select select2" data-allow-clear="true" data-placeholder="Select Contact Type...">
                                <option value=""></option>
                                <option value="1">All</option>
                                <option value="2">Primary</option>
                                <option value="3">Secondary</option>
                            </select>
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-field-variable-associate" class="form-label">Field Variable <span class="text-danger">*</span></label>
                            <select name="field_var_associate[]" id="add-field-variable-associate" class="form-select fieldVariable select2">
                                <option value="">Select</option>
                                <option value="header_image">Header Image</option>
                                <option value="header_video">Header Video</option>
                                <option value="header_document">Header Document</option>
                                <option value="header_document_name">Header Document Name</option>
                                <option value="field_1">Field 1</option>
                                <option value="field_2">Field 2</option>
                                <option value="field_3">Field 3</option>
                                <option value="field_4">Field 4</option>
                                <option value="field_5">Field 5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-personalise-class-associate" class="form-label">Assign Variable Associate <span class="text-danger">*</span></label>
                            <select name="assign_var_assoc[]" id="add-personalise-class-associate" class="form-select assignVariableContactPOPT select2" data-allow-clear="true" data-placeholder="Select Personalise Class">
                                <option value=""></option>
                                <option value="[Others]">Other</option>
                                <option value="[Name]">Name</option>
                                <option value="[Agency Name]">Agency Name</option>
                                <option value="[Email]">Email</option>
                                <option value="[Primary Mobile]">Primary Mobile</option>
                                <option value="[Secondary Mobile]">Secondary Mobile</option>
                                <option value="[Careoff]">Careoff</option>
                                <option value="[Address]">Address</option>
                                <option value="[City]">City</option>
                                <option value="[Country]">Country</option>
                            </select>
                        </div>
                    </div> --}}
                </div>
                <div id="dispAssoc"></div>
                {{-- <div class="row" id="dispAddAsocBtn" style="display: none">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-primary addAssocDisp float-end">Add</button>
                    </div>
                </div> --}}
                <!-- Associate View End -->
                <!-- Partner View Start -->
                <div class="row" id="dispartnerview" style="display: none">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-status-partner" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="partner_status[]" id="add-status-partner" class="form-select select2" multiple data-placeholder="Select Status...">
                                <option value=""></option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-contact-type-partner" class="form-label">Contact Type <span class="text-danger">*</span></label>
                            <select name="partner_contact_type" id="add-contact-type-partner" class="form-select select2" data-allow-clear="true" data-placeholder="Select Contact Type...">
                                <option value=""></option>
                                <option value="1">All</option>
                                <option value="2">Owner Contact</option>
                                <option value="3">Primary</option>
                                <option value="4">Secondary</option>
                            </select>
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-field-variable-partner" class="form-label">Field Variable <span class="text-danger">*</span></label>
                            <select name="field_var_partner[]" id="add-field-variable-partner" class="form-select fieldVariable select2">
                                <option value="">Select</option>
                                <option value="header_image">Header Image</option>
                                <option value="header_video">Header Video</option>
                                <option value="header_document">Header Document</option>
                                <option value="header_document_name">Header Document Name</option>
                                <option value="field_1">Field 1</option>
                                <option value="field_2">Field 2</option>
                                <option value="field_3">Field 3</option>
                                <option value="field_4">Field 4</option>
                                <option value="field_5">Field 5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-personalise-class-partner" class="form-label">Assign Variable Partner <span class="text-danger">*</span></label>
                            <select name="assign_var_partner[]" id="add-personalise-class-partner" class="form-select assignVariableContactPOPT select2" data-allow-clear="true" data-placeholder="Select Personalise Class">
                                <option value=""></option>
                                <option value="[Others]">Other</option>
                                <option value="[Recruitement Office Name (English)]">Recruitement Office Name (English)</option>
                                <option value="[Recruitment Office Name (Arabic)]">Recruitment Office Name (Arabic)</option>
                                <option value="[Owner Name ]">Owner Name</option>
                                <option value="[Username]">Username</option>
                                <option value="[Owner Contact Number]">Owner Contact Number</option>
                                <option value="[City]">City</option>
                                <option value="[Country]">Country</option>
                                <option value="[Primary Email]">Primary Email</option>
                                <option value="[Secondary Email]">Secondary Email</option>
                                <option value="[Office Number]">Office Number</option>
                                <option value="[Primary Mobile]">Primary Mobile</option>
                                <option value="[Secondary Mobile]">Secondary Mobile</option>
                            </select>
                        </div>
                    </div> --}}
                </div>
                <div id="dispPartner"></div>
                {{-- <div class="row" id="dispAddPartnerBtn" style="display: none">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-primary addPartnerDisp float-end">Add</button>
                    </div>
                </div> --}}
                <!-- Partner View End -->
                <!-- Client View Start -->
                <div class="row" id="disclientview" style="display: none">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="add-status-client" class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="client_status[]" id="add-status-client" class="form-select select2" multiple data-placeholder="Select Status...">
                                <option value=""></option>
                                <option value="1">Verified</option>
                                <option value="0">Not Verified</option>
                            </select>
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-field-variable-client" class="form-label">Field Variable <span class="text-danger">*</span></label>
                            <select name="field_var_client[]" id="add-field-variable-client" class="form-select fieldVariable select2">
                                <option value="">Select</option>
                                <option value="header_image">Header Image</option>
                                <option value="header_video">Header Video</option>
                                <option value="header_document">Header Document</option>
                                <option value="header_document_name">Header Document Name</option>
                                <option value="field_1">Field 1</option>
                                <option value="field_2">Field 2</option>
                                <option value="field_3">Field 3</option>
                                <option value="field_4">Field 4</option>
                                <option value="field_5">Field 5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-personalise-class-client" class="form-label">Assign Variable Client <span class="text-danger">*</span></label>
                            <select name="assign_var_client[]" id="add-personalise-class-client" class="form-select assignVariableContactPOPT select2" data-allow-clear="true" data-placeholder="Select Personalise Class">
                                <option value=""></option>
                                <option value="[Others]">Other</option>
                                <option value="[Name]">Name</option>
                                <option value="[Email]">Email</option>
                                <option value="[Mobile]">Mobile</option>
                                <option value="[Country]">Country</option>
                                <option value="[City]">City</option>
                                <option value="[Status]">Status</option>
                            </select>
                        </div>
                    </div> --}}
                </div>
                <div id="dispClient"></div>
                {{-- <div class="row" id="dispAddClientBtn" style="display: none">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-primary addClientDisp float-end">Add</button>
                    </div>
                </div> --}}
                <!-- Client View End -->
                <!-- Header Variable View Start -->
                <div class="row">

                    <div class="col-md-12" id="disp-header-image" style="display: none">
                        <div class="mb-3">
                            <label for="add-header-image" class="form-label">Header Image <span class="text-danger">*</span></label>
                            <input type="text" name="header_image" id="add-header-image" class="form-control" placeholder="Enter Header Image URL...">
                        </div>
                    </div>

                    <div class="col-md-12" id="disp-header-video" style="display: none">
                        <div class="mb-3">
                            <label for="add-header-video" class="form-label">Header Video <span class="text-danger">*</span></label>
                            <input type="text" name="header_video" id="add-header-video" class="form-control" placeholder="Enter Header Video URL...">
                        </div>
                    </div>

                    <div class="col-md-12" id="disp-header-document" style="display: none">
                        <div class="mb-3">
                            <label for="add-header-document" class="form-label">Header Document <span class="text-danger">*</span></label>
                            <input type="text" name="header_document" id="add-header-document" class="form-control" placeholder="Enter Header Document URL...">
                        </div>
                    </div>

                    <div class="col-md-12" id="disp-header-document-name" style="display: none">
                        <div class="mb-3">
                            <label for="add-header-document-name" class="form-label">Header Document Name <span class="text-danger">*</span></label>
                            <input type="text" name="header_document_name" id="add-header-document-name" class="form-control" placeholder="Enter Header Document Name...">
                        </div>
                    </div>
                </div>
                <!-- Header Variable View End -->
                <!-- Custom Message view Start-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="add-sms-message" class="form-label">SMS Message</label>
                            <textarea name="sms_msg" class="form-control" id="add-sms-message" cols="30" rows="6"></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <!-- <img alt="user-avatar" src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" class="d-block w-px-100 h-px-100 rounded uploadedAvatar22" id="uploadedAvatar22"/> -->
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-sch-type" class="form-label">Campaign Type <span class="text-danger">*</span></label>
                            <select name="sch_type" id="add-sch-type" class="form-select select2" data-allow-clear="true" data-placeholder="Select Campaign Type">
                                <option value="">Select</option>
                                <option value="Now">Now</option>
                                <option value="Scheduled">Scheduled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6" style="display: none" id="dispDateandTime">
                        <div class="mb-3">
                            <label for="add-scheduled-date-time" class="form-label">Date and Time <span class="text-danger">*</span></label>
                            <input type="text" name="date_and_time" id="add-scheduled-date-time" class="form-control flatpickr-datetime" placeholder="Enter date and time...">
                        </div>
                    </div>
                </div>
                <!-- Custom Message view End -->

                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>
    <!--- Add SMS Campaign End --->

    <!-- Filter Panel Start -->
    <div class="modal fade" id="filterpanel" aria-hidden="true" aria-labelledby="filterpanelLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="offcanvas-title" id="filterpanelLabel">Campaign Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <select id="by-careoff" multiple class="selectpicker w-100" multiple data-live-search="true" data-actions-box="true" data-style="default-btn" title="Select Careoff">
                                @foreach ($careoffs as $careofffilter)
                                    <option value="{{ $careofffilter->id }}">{{ $careofffilter->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">


                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-sm btn-primary applyfilter">Apply Filter</button> --}}
                    {{-- <button type="button" class="btn btn-warning btn-sm resetfilter">Reset Filter</button> --}}
                    {{-- <button type="button" class="btn btn-success btn-sm savetodoFilter">Save Filter</button> --}}
                </div>
            </div>
        </div>
    </div>
    <!-- Filter Panel End -->

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
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script>

    <script src="{{ asset('admin/assets/vendor/libs/dropzone/dropzone.js') }}"></script>

    <script src="{{ asset('admin/assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/quill.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/validation/sms-campaign-validation.js') }}"></script>

    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    <!-- <script src="{{ asset('admin/assets/js/forms-file-upload.js') }}"></script> -->


    <script>
        $(document).ready(function(){
         
            var dattimeepicker = $('.flatpickr-datetime');

            if (dattimeepicker) {
                dattimeepicker.flatpickr({
                    enableTime: true,
                    time_24hr: true,
                    dateFormat: "Y-m-d H:i"
                });
            }
           
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#add-sch-type').on('change',function(){
                var sctype = $(this).val();
                if (sctype == 'Scheduled') {
                    $('#dispDateandTime').show();
                }else{
                    $('#dispDateandTime').hide();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#add-sms-api-name').on('change',function(){

                var smsapi = $(this).val();
                var audience = $('#add-audience').val();

                if (audience != '') {
                    jQuery.ajax({
                        url : "{{ route('admin.smsCampaign.getList') }}" ,
                        method: "GET",
                        data: {
                            audience: audience,
                            sms_api_id: smsapi
                        },
                        success: function(data){
                            $('.templateListDynamic').html(data.res);
                            $('#add-template-name').val('').trigger('change');
                        }
                    });
                }

            });

            $('#add-audience').on('change',function(){
                var audience = $(this).val();
                var smsapi = $('#add-sms-api-name').val();

                jQuery.ajax({
                    url : "{{ route('admin.smsCampaign.getList') }}" ,
                    method: "GET",
                    data: {
                        audience: audience,
                        sms_api_id: smsapi
                    },
                    success: function(data){

                        $('.templateListDynamic').html(data.res);
                        $('#add-template-name').val('').trigger('change');
                    }
                });
                if (audience == 'Leads') {

                    $('#disleadsview').show();

                    $('#dispartnerview').hide();
                    $('#dispAddPartnerBtn').hide();
                    $('#disclientview').hide();
                    $('#dispClient').hide();
                    $('#dispAddClientBtn').hide();
                    $('#disassocview').hide();
                    $('#dispAssoc').hide();
                    $('#dispAddAsocBtn').hide();
                    $('#discontactpview').hide();
                    $('#dispContactp').hide();
                    $('#dispAddContactpBtn').hide();
                    $('#disallcontactview').hide();

                } else if (audience == 'partner') {
                    $('#disleadsview').hide();
                    $('#dispartnerview').show();
                    $('#disclientview').hide();
                    $('#disassocview').hide();
                    $('#discontactpview').hide();
                    $('#disallcontactview').hide();
                    $('#dispPartner').show();
                    $('#dispAddPartnerBtn').show();
                    $('#dispAssoc').hide();
                    $('#dispAddAsocBtn').hide();
                    $('#dispClient').hide();
                    $('#dispAddClientBtn').hide();
                    $('#dispContactp').hide();
                    $('#dispAddContactpBtn').hide();
                } else if (audience == 'client') {
                    $('#disleadsview').hide();
                    $('#dispartnerview').hide();
                    $('#disclientview').show();
                    $('#disassocview').hide();
                    $('#discontactpview').hide();
                    $('#disallcontactview').hide();
                    $('#dispPartner').hide();
                    $('#dispAddPartnerBtn').hide();
                    $('#dispAssoc').hide();
                    $('#dispAddAsocBtn').hide();
                    $('#dispClient').show();
                    $('#dispAddClientBtn').show();
                    $('#dispContactp').hide();
                    $('#dispAddContactpBtn').hide();
                } else if (audience == 'associate') {
                    $('#disleadsview').hide();
                    $('#dispartnerview').hide();
                    $('#disclientview').hide();
                    $('#disassocview').show();
                    $('#discontactpview').hide();
                    $('#disallcontactview').hide();

                    $('#dispPartner').hide();
                    $('#dispAddPartnerBtn').hide();
                    $('#dispAssoc').show();
                    $('#dispAddAsocBtn').show();
                    $('#dispClient').hide();
                    $('#dispAddClientBtn').hide();
                    $('#dispContactp').hide();
                    $('#dispAddContactpBtn').hide();
                } else if (audience == 'contactp') {
                    $('#disleadsview').hide();
                    $('#dispartnerview').hide();
                    $('#disclientview').hide();
                    $('#disassocview').hide();
                    $('#discontactpview').show();
                    $('#disallcontactview').hide();

                    $('#dispPartner').hide();
                    $('#dispAddPartnerBtn').hide();
                    $('#dispAssoc').hide();
                    $('#dispAddAsocBtn').hide();
                    $('#dispClient').hide();
                    $('#dispAddClientBtn').hide();
                    $('#dispContactp').show();
                    $('#dispAddContactpBtn').show();
                }else if (audience == 'allcontact') {
                    $('#disleadsview').hide();
                    $('#dispartnerview').hide();
                    $('#disclientview').hide();
                    $('#disassocview').hide();
                    $('#discontactpview').hide();
                    $('#disallcontactview').show();


                    $('#dispPartner').hide();
                    $('#dispAddPartnerBtn').hide();
                    $('#dispAssoc').hide();
                    $('#dispAddAsocBtn').hide();
                    $('#dispClient').hide();
                    $('#dispAddClientBtn').hide();
                    $('#dispContactp').hide();
                    $('#dispAddContactpBtn').hide();

                }else {
                    $('#disleadsview').hide();
                    $('#dispartnerview').hide();
                    $('#disclientview').hide();
                    $('#disassocview').hide();
                    $('#discontactpview').hide();
                    $('#disallcontactview').hide();

                    $('#dispPartner').hide();
                    $('#dispAddPartnerBtn').hide();
                    $('#dispAssoc').hide();
                    $('#dispAddAsocBtn').hide();
                    $('#dispClient').hide();
                    $('#dispAddClientBtn').hide();
                    $('#dispContactp').hide();
                    $('#dispAddContactpBtn').hide();

                    // Hide Display field
                    // $('#disp-header-image').hide();
                    // $('#disp-header-video').hide();
                    // $('#disp-header-document').hide();
                    // $('#disp-header-document-name').hide();

                }

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#add-template-name').on('change',function(){

                var template_value = $(this).val();

                var imgPath = "{{ asset('admin/assets/images/template') }}";
                var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";

                if (template_value != '') {

                    jQuery.ajax({
                        url: "{{ route('admin.smsCampaign.getListID') }}",
                        method: "GET",
                        data: {
                            id: template_value
                        },
                        success: function (data) {
                            
                            $('#add-sms-message').val(data.sms_message);

                            if (data.sms_url_type == 0) {
                                if (data.whatsapp_file != '') {
                                    var file_path = imgPath+'/'+data.whatsapp_file;
                                    $('.uploadedAvatar22').attr("src",file_path);
                                } else {
                                    $('.uploadedAvatar22').attr("src",blankImg);
                                }
                            }else if (data.sms_url_type == 1) {
                                if (data.static_url != '') {
                                    var file_path = data.static_url;
                                    $('.uploadedAvatar22').attr("src",file_path);
                                } else {
                                    $('.uploadedAvatar22').attr("src",blankImg);
                                }
                            }else{
                                if (data.whatsapp_file != '') {
                                    var file_path = imgPath+'/'+data.whatsapp_file;
                                    $('.uploadedAvatar22').attr("src",file_path);
                                } else {
                                    $('.uploadedAvatar22').attr("src",blankImg);
                                }
                            }
                        }
                    });

                } else {
                    $('.uploadedAvatar22').attr("src",blankImg);
                }
            });
        });
    </script>


    <script>
        var rowMin = 1;
        var rowMax = 10;

        $(document).on('click','.addPartnerDisp',function(){
            var html = "";
            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-variable-partner'+rowMin+'">Field Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_partner[]" id="add-field-variable-partner'+rowMin+'" class="form-control fieldVariable select2"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_partner'+rowMin+'">Assign Variable Partner <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_partner[]" id="assign_var_partner'+rowMin+'" class="form-control assignVariableContactPOPT select2"><option value="">Select</option><option value="[Others]">Other</option><option value="[Recruitement Office Name (English)]">Recruitement Office Name (English)</option><option value="[Recruitment Office Name (Arabic)]">Recruitment Office Name (Arabic)</option><option value="[Owner Name ]">Owner Name</option><option value="[Username]">Username</option><option value="[Owner Contact Number]">Owner Contact Number</option><option value="[City]">City</option><option value="[Country]">Country</option><option value="[Primary Email]">Primary Email</option><option value="[Secondary Email]">Secondary Email</option><option value="[Office Number]">Office Number</option><option value="[Primary Mobile]">Primary Mobile</option><option value="[Secondary Mobile]">Secondary Mobile</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';



            $('#dispPartner').append(html);

            rowMin++;
            rowMax--;
            if (rowMax == 1) {
                $('.addPartnerDisp').prop('disabled',true);
            }else{
                $('.addPartnerDisp').prop('disabled',false);
            }
        });
        $(document).on('click','.remove',function(){
            $(this).closest('.row').remove();
            rowMax++;
            rowMin--;
            if (rowMax == 0) {
                $('.addPartnerDisp').prop('disabled',true);
            }else{
                $('.addPartnerDisp').prop('disabled',false);
            }
        });
    </script>


    <script>
        var rowMin2 = 1;
        var rowMax2 = 10;

        $(document).on('click','.addAssocDisp',function(){
            var html = "";
            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-variable-associate'+rowMin2+'">Field Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_associate[]" id="add-field-variable-associate'+rowMin2+'" class="form-control fieldVariable select2"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_assoc'+rowMin2+'">Assign Variable Associate <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_assoc[]" id="assign_var_assoc'+rowMin2+'" class="form-control assignVariableContactPOPT select2"><option value="">Select</option><option value="[Others]">Other</option><option value="[Name]">Name</option><option value="[Agency Name]">Agency Name</option><option value="[Email]">Email</option><option value="[Primary Mobile]">Primary Mobile</option><option value="[Secondary Mobile]">Secondary Mobile</option><option value="[Careoff]">Careoff</option><option value="[Address]">Address</option><option value="[City]">City</option><option value="[Country]">Country</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';



            $('#dispAssoc').append(html);

            rowMin2++;
            rowMax2--;
            if (rowMax2 == 1) {
                $('.addAssocDisp').prop('disabled',true);
            }else{
                $('.addAssocDisp').prop('disabled',false);
            }
        });
        $(document).on('click','.remove',function(){
            $(this).closest('.row').remove();
            rowMax2++;
            rowMin2--;
            if (rowMax2 == 0) {
                $('.addAssocDisp').prop('disabled',true);
            }else{
                $('.addAssocDisp').prop('disabled',false);
            }
        });
    </script>

    <script>
        var rowMin3 = 1;
        var rowMax3 = 10;

        $(document).on('click','.addClientDisp',function(){
            var html = "";
            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-variable-client'+rowMin3+'">Field Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_client[]" id="add-field-variable-client'+rowMin3+'" class="form-control fieldVariable select2"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_client'+rowMin3+'">Assign Variable Client <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_client[]" id="assign_var_client'+rowMin3+'" class="form-control assignVariableContactPOPT select2"><option value="">Select</option><option value="[Others]">Other</option><option value="[Name]">Name</option><option value="[Email]">Email</option><option value="[Mobile]">Mobile</option><option value="[Country]">Country</option><option value="[City]">City</option><option value="[Status]">Status</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';



            $('#dispClient').append(html);

            rowMin3++;
            rowMax3--;
            if (rowMax3 == 1) {
                $('.addClientDisp').prop('disabled',true);
            }else{
                $('.addClientDisp').prop('disabled',false);
            }
        });
        $(document).on('click','.remove',function(){
            $(this).closest('.row').remove();
            rowMax3++;
            rowMin3--;
            if (rowMax3 == 0) {
                $('.addClientDisp').prop('disabled',true);
            }else{
                $('.addClientDisp').prop('disabled',false);
            }
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

        $(document).on('change','.assignVariableContactPOPT',function(){
            var fieldVariable = $(this).closest('.row').find('.fieldVariable').attr('id');
            var fieldVariableValue = $('#'+fieldVariable).val();
            var AssignVariable = $(this).val();
            // alert(AssignVariable);

            if (fieldVariableValue == 'header_image' && AssignVariable == '[Others]') {
                $("#disp-header-image").show();
            }

            if (fieldVariableValue == 'header_video' && AssignVariable == '[Others]') {
                $("#disp-header-video").show();
            }

            if (fieldVariableValue == 'header_document' && AssignVariable == '[Others]') {
                $("#disp-header-document").show();
            }

            if (fieldVariableValue == 'header_document_name' && AssignVariable == '[Others]') {
                $("#disp-header-document-name").show();
            }
        });

        // $(document).on('change','.fieldVariable',function(e){
        //     var fieldVar = $(this).val();
        //     if (fieldVar == "header_image") {
        //         $('#disp-header-image').show();
        //     }
        //     if (fieldVar == "header_video") {
        //         $('#disp-header-video').show();
        //     }
        //     if (fieldVar == "header_document") {
        //         $('#disp-header-document').show();
        //     }
        //     if (fieldVar == "header_document_name") {
        //         $('#disp-header-document-name').show();
        //     }
        // });
    </script>



    <script>
        $(document).ready(function() {
            function fetchContactList() {
                const audience = $('.add-audience').val();
                const careoff = $('.add-allcontact-careoff').val();
                const group = $('.add-allcontact-groupm').val();
                const country = $('.add-allcontact-country').val();
                const leadtype = $('.add-allcontact-business-type').val();
                const allcontsubs = $('.add-subscribe-allcontact').val();
                const allcontacttype = $('.add-allcontact-type').val();
                const allconmobcountry = $('.add-allcontact-mobile-country').val();

                const ladtypecontactp = $('.add-business-type').val();
                const subscribecontactp = $('.add-subscribe-contactp').val();
                const groupcontactp = $('.add-contactp-contact-group').val();
                const careoffcontactp = $('.add-contactp-careoff').val();
                const contactpconttype = $('.add-contactp-contact-type').val();

                const lead_is_qualified = $('.lead_is_qualified').val();
                const leadassign_id = $('.leadassign_id').val();
                const lead_contact_type = $('.lead_contact_type').val();
                
                $.ajax({
                    url: "{{ route('admin.smsCampaign.getsendList') }}",
                    method: "GET",
                    data: {
                        audience,
                        careoff,
                        group,
                        country,
                        leadtype,
                        allcontsubs,
                        ladtypecontactp,
                        subscribecontactp,
                        groupcontactp,
                        careoffcontactp,
                        allcontacttype,
                        allconmobcountry,
                        contactpconttype,
                        lead_is_qualified,
                        leadassign_id,
                        lead_contact_type
                    },
                    success: function(data) {
                        $('.displayContactList').text(data?.result || '');
                        // console.log(data);

                    }
                });
            }

            $('.lead_is_qualified,.leadassign_id,.lead_contact_type,.add-audience,.add-contactp-contact-type, .add-allcontact-type,.add-allcontact-business-type, .add-allcontact-mobile-country, .add-business-type, .add-subscribe-contactp, .add-contactp-contact-group, .add-contactp-careoff, .add-allcontact-careoff, .add-subscribe-allcontact, .add-allcontact-groupm, .add-allcontact-country').on('change', fetchContactList);
        });
    </script>

    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });

            function getFilterData() {
                return {
                    search_text: $('#search_text').val(),
                    page_list: $('#pagination_list').val(),
                }
            }

            function reloadTodoList() {
                $.ajax({
                    url: "{{ route('admin.smsCampaign.list') }}",
                    method: "GET",
                    type: "html",
                    data: getFilterData(),
                    success: function(data){
                        $('.contactpaginate').html(data);
                    }
                });
            }

            function bindFilterChange(selector) {
                $(selector).on('change input', function () {
                    reloadTodoList();
                });
            }

            bindFilterChange('#pagination_list');
            bindFilterChange('#search_text');


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

@endsection
