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

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($perm) && $perm->add_template == 0)
        <style>
        .addtemplate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->edit_template == 0)
        <style>
        .edtemplate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->delete_template == 0)
        <style>
        .deltemplate{
            display: none !important;
        }
        </style>
        @endif
    @endif

    @if (Auth::guard('admin')->user()->user_type == 2)
        @if (isset($permission) && $permission->meta_whatsapp_campaign_add == 0)
            <style>
                .disaddcamp{
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
                        <span>No of Faied</span>
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
        <!-- Users List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Search Filter</h5>
                <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Campaign Name</th>
                            <th>Template Name</th>
                            <th>Audience</th>
                            <th>Careoff</th>
                            <th>Sent Date</th>
                            <th>Status</th>
                            <th>Message Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!--- Add Candidate Start --->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Whatsapp Campaign</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.metawhatsapp.campaignStore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-audience" class="form-label">Audience <span class="text-danger">*</span></label>
                                <select name="audience" id="add-audience" class="form-select select2 add-audience" data-allow-clear="true" data-placeholder="Select Audience">
                                    <option value=""></option>
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
                                <label for="add-meta-api-name" class="form-label">Meta API Name <span class="text-danger">*</span></label>
                                <select name="metaapi_id" id="add-meta-api-name" class="form-select select2" data-allow-clear="true" data-placeholder="Select API Name...">
                                    <option value="">Select</option>
                                    @foreach ($metapaiLists as $metapaiList)
                                        <option value="{{ $metapaiList->id }}">{{ $metapaiList->api_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-template-name" class="form-label">Meta Template <span class="text-danger">*</span></label>
                                <select name="metatemplate_id" id="add-template-name" class="form-select select2 templateListDynamic" data-allow-clear="true" data-placeholder="Select Meta Template">

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
                                <label class="form-label" for="add-meta-campaign-name">Meta Campaign Name <span class="text-danger">*</span></label>
                                <input type="text" name="meta_campaign_name" id="add-meta-campaign-name" class="form-control" placeholder="Enter campaign name...">
                            </div>
                        </div> --}}


                    </div>
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
                                <select name="country_code[]" id="add-allcontact-mobile-country" class="form-select select2" multiple data-placeholder="Select Country Code">
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
                                <select name="allcontact_contact_type" id="add-allcontact-type" data-allow-clear="true" data-placeholder="Select Contact Type..." class="form-select select2">
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
                                <select name="contactp_contact_type" id="add-contactp-contact-type" class="form-select select2" data-placeholder="Select Contact Type..." data-allow-clear="true">
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
                                <label for="add-whatsapp-message" class="form-label">Whatsapp Message</label>
                                <textarea name="whs_msg" class="form-control" id="add-whatsapp-message" cols="30" rows="6"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <img alt="user-avatar" src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" class="d-block w-px-100 h-px-100 rounded uploadedAvatar22" id="uploadedAvatar22"/>
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
        <!--- Add Candidate End --->

        <!--- Edit Candidate Start --->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="edituserLabel" class="offcanvas-title">Edit Candidate</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0 " id="editUserForm" action="{{ route('admin.template.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="edit_id" id="editid">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-temp-name">Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="template_name" id="edit-temp-name" class="form-control" placeholder="Enter candidate name...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-pass-type">Personalise Class</label>
                                <select name="" id="edit-pass-type" class="form-select select2" data-allow-clear="true">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-msg-whatsapp" class="form-label">Whatsapp Message</label>
                                <textarea name="msg_whatsapp" class="form-control" id="edit-msg-whatsapp" cols="30" rows="6"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-msg-whatsapp-arabic" class="form-label">Whatsapp Message Arabic</label>
                                <textarea name="msg_whatsapp_ar" class="form-control" id="edit-msg-whatsapp-arabic" cols="30" rows="6"></textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="edit-subject-name">Email Subject Name</label>
                                <input type="text" name="subject_name" id="edit-subject-name" class="form-control" placeholder="Enter candidate name...">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-email-message" class="form-label">Email Message</label>
                                <div id="mail-text2"></div>
                                {{-- <input type="hidden" name="textmsg" class="maildbtext" id="edit-email-message"> --}}
                                <input type="hidden" name="msg_email" id="quill_html_mail2">
                                {{-- <textarea name="msg_email" class="form-control" id="edit-email-message" cols="30" rows="6"></textarea> --}}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-sms-message" class="form-label">SMS Message</label>
                                <textarea name="msg_sms" class="form-control" id="edit-sms-message" cols="30" rows="6"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3" >
                                <label for="edit-temp-file" class="form-label">Template File</label>
                                <input class="form-control template-file-input-edit" name="photo" type="file" id="edit-temp-file" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3" id="dispIMG">
                                <img alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2"/>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" name="public" type="checkbox" id="edit-public" value="1" />
                                    <label class="form-check-label" for="edit-public">Public</label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Candidate End --->
        <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.template.delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="proff_ids" id="delStaffID">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-12">
                                <p>Are you sure!, to delete template?</p>
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

        <!--- Edit Candidate End --->
        <div class="modal fade" id="activeStatus" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Active Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.template.activeSt') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tempactID" id="tempactID">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-12">
                                <p>Are you sure!, to active template?</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-success btn-sm ">Active</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        <!-- Delete Staff end -->

        <div class="modal fade" id="deactiveStatus" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Deactive Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.template.deactiveSt') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tempdeactID" id="tempdeactID">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p>Are you sure!, to deactive template?</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                        <button type="submit" class="btn btn-danger btn-sm">Deactive</button>
                    </div>
                </form>
            </div>
            </div>
        </div>
        <!-- Delete Staff end -->


        <!--Publish Status Modal Start -->
        <div class="modal fade" id="publishSts" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Public Template</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.template.publish') }}" method="POST">
                        @csrf
                        <input type="hidden" name="temppubID" id="temppubID">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input" type="radio" name="publish_st" id="pubactive" value="1"/>
                                            <label class="form-check-label" for="pubactive">Public</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="publish_st" id="pubdeactive" value="0"/>
                                            <label class="form-check-label" for="pubdeactive">Unpublic</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                            <button type="submit" class="btn btn-danger btn-sm">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--Publish Status Modal End -->


        <!--Change Status Modal Start -->
        <div class="modal fade" id="statusChange" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Status change</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.template.changestatus') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tempchstID" id="tempchstID">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input" type="radio" name="status" id="changestact" value="1"/>
                                            <label class="form-check-label" for="changestact">Active</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="changestdeact" value="0"/>
                                            <label class="form-check-label" for="changestdeact">Deactive</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal"> Close</button>
                            <button type="submit" class="btn btn-danger btn-sm">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--Change Status Modal End -->

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
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script>

    <script src="{{ asset('admin/assets/vendor/libs/dropzone/dropzone.js') }}"></script>

    <script src="{{ asset('admin/assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/quill.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('admin/assets/pages/app-meta-whatsapp-campaign-list.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/validation/meta-whatsapp-campaign-validation.js') }}"></script>



    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-file-upload.js') }}"></script>
    {{-- <script>
        $(document).ready(function(){
            $('#add-template-name').on('change',function(){
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
                            $('#add-whatsapp-message').val(data.msg_whatsapp);
                            $('#add-whatsapp-message-ar').val(data.msg_whatsapp_ar);
                            if (data.file != '') {
                                var file_path = imgPath+'/'+data.file;
                                $('.uploadedAvatar').attr("src",file_path);
                            } else {
                                $('.uploadedAvatar').attr("src",blankImg);
                            }
                        }
                    });
                } else {
                    $('#add-whatsapp-message').val('');
                    $('#add-whatsapp-message-ar').val('');
                    $('.uploadedAvatar').attr("src",blankImg);
                }


            });
        });
    </script> --}}
    {{-- <script>
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
    </script> --}}
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
            $('#add-meta-api-name').on('change',function(){
                var metaapi = $(this).val();
                var audience = $('#add-audience').val();

                if (audience != '') {
                    jQuery.ajax({
                        url : "{{ route('admin.metatemplate.getList') }}" ,
                        method: "GET",
                        data: {
                            audience: audience,
                            metaapi_id: metaapi
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
                var metaapi = $('#add-meta-api-name').val();



                jQuery.ajax({
                    url : "{{ route('admin.metatemplate.getList') }}" ,
                    method: "GET",
                    data: {
                        audience: audience,
                        metaapi_id: metaapi
                    },
                    success: function(data){
                        $('.templateListDynamic').html(data.res);
                        $('#add-template-name').val('').trigger('change');
                    }
                });

                if (audience == 'partner') {
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
                        url: "{{ route('admin.metatemplate.getListID') }}",
                        method: "GET",
                        data: {
                            id: template_value
                        },
                        success: function (data) {
                            // console.log(data);
                            $('#add-whatsapp-message').val(data.whatsapp_message);

                            if (data.meta_url_type == 0) {
                                if (data.whatsapp_file != '') {
                                    var file_path = imgPath+'/'+data.whatsapp_file;
                                    $('.uploadedAvatar22').attr("src",file_path);
                                } else {
                                    $('.uploadedAvatar22').attr("src",blankImg);
                                }
                            }else if (data.meta_url_type == 1) {
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
        $(document).ready(function(){
            $('#add-personalise-class-associate').on('change',function(){
                var assovalue = $(this).val();
                navigator.clipboard.writeText(assovalue);
                toastr['success']('Text copied - '+assovalue+'', 'Success', { hideDuration: 3000 });
            });

            $('#add-personalise-class-partner').on('change',function(){
                var partvalue = $(this).val();
                navigator.clipboard.writeText(partvalue);
                toastr['success']('Text copied - '+partvalue+'', 'Success', { hideDuration: 3000 });
            });

            $('#add-personalise-class-client').on('change',function(){
                var clientvalue = $(this).val();
                navigator.clipboard.writeText(clientvalue);
                toastr['success']('Text copied - '+clientvalue+'', 'Success', { hideDuration: 3000 });
            });

        });





    </script>

    {{-- <script>
        $(document).ready(function(){
            $('#edituser').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');

                var imgPath = "{{ asset('admin/assets/images/template') }}";
                var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });



                jQuery.ajax({
                    url : '{{ url('admin/template/edit') }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#editid').val(data.id);
                        $('#edit-temp-name').val(data.template_name);
                        $('#edit-subject-name').val(data.subject_name);
                        $('#edit-msg-whatsapp').val(data.msg_whatsapp);
                        $('#edit-msg-whatsapp-arabic').val(data.msg_whatsapp_ar);
                        // $('#edit-email-message').val(data.msg_email);
                        $('#edit-sms-message').val(data.msg_sms);
                        if (data.public == 1) {
                            // $('#edit-public').val(data.public).prop("checked",true);
                            $('#edit-public').prop("checked",true);
                        } else {
                            // $('#edit-public').val(data.public).prop("checked",false);
                            $('#edit-public').prop("checked",false);
                        }

                        if (data.file != '') {
                            var file_path = imgPath+'/'+data.file;
                            $('.uploadedAvatar2').attr("src",file_path);
                        } else {
                            $('.uploadedAvatar2').attr("src",blankImg);
                        }

                        var mailDesc = data.msg_email;
                        var quill_editor = $('#mail-text2 .ql-editor');
                        quill_editor[0].innerHTML = mailDesc;

                        // var html = "";
                        // if (data.file != '') {
                        //     var file_path = imgPath+'/'+data.file;
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
    </script> --}}



    <script>
        $(document).ready(function(){
            $('#add-pass-type').on('change',function(){
                // var $temp = $("<input>");
                // $("body").append($temp);
                // var element = $(this).val();
                // $temp.val(element).select();
                // document.execCommand("copy");
                // $temp.remove();
                // // $('.toast-placement-copy .toast').toast('show')
                // toastr['success']('Text copied - '+element+'', 'Success', { hideDuration: 3000 });

                var value = $(this).val();
                navigator.clipboard.writeText(value);
                toastr['success']('Text copied - '+value+'', 'Success', { hideDuration: 3000 });
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#edit-pass-type').on('change',function(){
                // var $temp = $("<input>");
                // $("body").append($temp);
                // var element = $(this).val();
                // $temp.val(element).select();
                // document.execCommand("copy");
                // $temp.remove();
                // // $('.toast-placement-copy .toast').toast('show')
                // toastr['success']('Text copied - '+element+'', 'Success', { hideDuration: 3000 });

                var value = $(this).val();
                navigator.clipboard.writeText(value);
                toastr['success']('Text copied - '+value+'', 'Success', { hideDuration: 3000 });
            });
        });
    </script>



    <script>
        $(document).ready(function(){
            $('#deleteStaff').on('show.bs.modal',function(e){
                var staff_id =  $(e.relatedTarget).data('id');
                // alert(staff_id);
                $('#delStaffID').val(staff_id);

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#activeStatus').on('show.bs.modal',function(e){
                var tempact_id = $(e.relatedTarget).data('id');
                $('#tempactID').val(tempact_id);
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#deactiveStatus').on('show.bs.modal',function(e){
                var tempdeact_id = $(e.relatedTarget).data('id');
                $('#tempdeactID').val(tempdeact_id);
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#publishSts').on('show.bs.modal',function(e){
                var temppubID = $(e.relatedTarget).data('id');

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url('admin/template/get/public') }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": temppubID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#temppubID').val(data.id);
                        if (data.public == 1) {
                            $('#pubactive').prop("checked",true);
                        } else {
                            $('#pubdeactive').prop("checked",true);
                        }
                    }
                });

            });
        });
    </script>

    <script>
        $(document).ready(function(){
            $('#statusChange').on('show.bs.modal',function(e){
                var tempchstID = $(e.relatedTarget).data('id');

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ url('admin/template/get/status') }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": tempchstID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#tempchstID').val(data.id);
                        if (data.status == 1) {
                            $('#changestact').prop("checked",true);
                        } else {
                            $('#changestdeact').prop("checked",true);
                        }
                    }
                });

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

                const ladtypecontactp = $('.add-business-type').val();
                const subscribecontactp = $('.add-subscribe-contactp').val();
                const groupcontactp = $('.add-contactp-contact-group').val();
                const careoffcontactp = $('.add-contactp-careoff').val();


                $.ajax({
                    url: "{{ route('admin.metawhatsapp.getsendList') }}",
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
                        careoffcontactp
                    },
                    success: function(data) {
                        $('.displayContactList').text(data?.result || '');
                    }
                });
            }

            $('.add-audience, .add-allcontact-business-type, .add-business-type, .add-subscribe-contactp, .add-contactp-contact-group, .add-contactp-careoff, .add-allcontact-careoff, .add-subscribe-allcontact, .add-allcontact-groupm, .add-allcontact-country').on('change', fetchContactList);
        });
    </script>

@endsection
