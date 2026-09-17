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

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Session</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">21,459</h4>
                        <span class="text-success">(+29%)</span>
                        </div>
                        <span>Total Users</span>
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
                        <span>Paid Users</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">4,567</h4>
                        <span class="text-success">(+18%)</span>
                        </div>
                        <span>Last week analytics </span>
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
                        <span>Active Users</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">19,860</h4>
                        <span class="text-danger">(-14%)</span>
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
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>Pending Users</span>
                        <div class="d-flex align-items-center my-1">
                        <h4 class="mb-0 me-2">237</h4>
                        <span class="text-success">(+42%)</span>
                        </div>
                        <span>Last week analytics</span>
                    </div>
                    <span class="badge bg-label-warning rounded p-2">
                        <i class="ti ti-user-exclamation ti-sm"></i>
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
                            <th>Audience</th>
                            <th>Message</th>
                            <th>User Name</th>
                            <th>Sent</th>
                            <th>Status</th>
                            <th>Stop / Run</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!--- Add Candidate Start --->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Whatsapp Campaign</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.whatsapp.campaign.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-audience" class="form-label">Audience <span class="text-danger">*</span></label>
                                <select name="audience" id="add-audience" class="form-select select2" data-allow-clear="true" data-placeholder="Select Audience">
                                    <option value=""></option>
                                    <option value="partner">Partner</option>
                                    <option value="client">Client</option>
                                    <option value="associate">Associate</option>
                                    <option value="contactp">Contact+</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="add-campaign-name">Campaign Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="add-campaign-name" class="form-control" placeholder="Enter campaign name...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="add-select-whatsapp-api">Select Whatsapp API <span class="text-danger">*</span></label>
                                <select name="wapi_id_text[]" id="add-select-whatsapp-api" class="form-select select2" multiple data-placeholder="Select Whatsapp API...">
                                    <option value=""></option>
                                    @foreach ($wapis as $wapi)
                                        <option value="{{ $wapi->id }}">{{ $wapi->mobile_no }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-whatsapp-template" class="form-label">Whatsapp Template <span class="text-danger">*</span></label>
                                <select name="wtemp" id="add-whatsapp-template" class="form-select select2" data-allow-clear="true" data-placeholder="Select Whatsapp Template...">
                                    <option value=""></option>
                                    <option value="custom_temp">Custom</option>
                                    <option value="template">Template</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="distempname" style="display: none">
                            <div class="mb-3">
                                <label for="add-template-name" class="form-label">Template Name <span>*</span></label>
                                <select name="temp_id" id="add-template-name" class="form-select select2" data-allow-clear="true" data-placeholder="Select Template name...">
                                    <option value=""></option>
                                    @foreach ($wtemps as $wtemp)
                                        <option value="{{ $wtemp->id }}">{{ $wtemp->template_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Contact+ View Start -->
                    <div class="row" id="disacontactpview" style="display: none">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-status-contactp" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="contactp_status[]" id="add-status-contactp" class="form-select select2" multiple data-placeholder="Select Status...">
                                    <option value=""></option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-contact-type-contactp" class="form-label">Contact Type <span class="text-danger">*</span></label>
                                <select name="contactp_contact_type" id="add-contact-type-contactp" data-placeholder="Select Contact Type..." data-allow-clear="true" class="form-select select2">
                                    <option value=""></option>
                                    <option value="1">All</option>
                                    <option value="2">Owner Contact</option>
                                    <option value="3">Primary Contact</option>
                                    <option value="4">Secondary Contact</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-contactp-personalise-class" class="form-label">Contact Personalise Class</label>
                                <select name="" id="add-contactp-personalise-class" class="form-select select2" data-placeholder="Select Personalise Class..." data-allow-clear="true">
                                    <option value=""></option>
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
                    </div>
                    <!-- Contact+ View End -->
                    <!-- Associate View Start -->
                    <div class="row" id="disassocview" style="display: none">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-status-assoc" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="assoc_status[]" id="add-status-assoc" class="form-select select2" multiple data-placeholder="Select Status...">
                                    <option value=""></option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-personalise-class-associate">Personalise Name</label>
                                <select name="" id="add-personalise-class-associate" class="form-select select2" data-allow-clear="true" data-placeholder="Select Personalise Class">
                                    <option value=""></option>
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
                        </div>
                    </div>
                    <!-- Associate View End -->
                    <!-- Partner View Start -->
                    <div class="row" id="dispartnerview" style="display: none">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-status-partner" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="partner_status[]" id="add-status-partner" class="form-select select2" multiple data-placeholder="Select Status...">
                                    <option value=""></option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-personalise-class-partner" class="form-label">Personalise Name</label>
                                <select name="" id="add-personalise-class-partner" class="form-select select2" data-allow-clear="true" data-placeholder="Select Personalise Class">
                                    <option value=""></option>
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
                        </div>
                    </div>
                    <!-- Partner View End -->
                    <!-- Client View Start -->
                    <div class="row" id="disclientview" style="display: none">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-status-client" class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="client_status[]" id="add-status-client" class="form-select select2" multiple data-placeholder="Select Status...">
                                    <option value=""></option>
                                    <option value="1">Verified</option>
                                    <option value="0">Not Verified</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-personalise-class-client" class="form-label">Personalise Name</label>
                                <select name="" id="add-personalise-class-client" class="form-select select2" data-allow-clear="true" data-placeholder="Select Personalise Class">
                                    <option value=""></option>
                                    <option value="[Name]">Name</option>
                                    <option value="[Email]">Email</option>
                                    <option value="[Mobile]">Mobile</option>
                                    <option value="[Country]">Country</option>
                                    <option value="[City]">City</option>
                                    <option value="[Status]">Status</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Client View End -->
                    <!-- Custom Message view Start-->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-whatsapp-message" class="form-label">Whatsapp Message</label>
                                <textarea name="whs_msg" class="form-control" id="add-whatsapp-message" cols="30" rows="6"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-whatsapp-message-ar" class="form-label">Whatsapp Message Arabic</label>
                                <textarea name="whs_msg_ar" class="form-control" id="add-whatsapp-message-ar" cols="30" rows="6"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Custom Message view End -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3" >
                                <label for="add-template-file" class="form-label">Template File</label>
                                <input name="temp_file" class="form-control template-file-input" type="file" id="add-template-file" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded uploadedAvatar" id="uploadedAvatar"/>
                            </div>
                        </div>
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
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Add Candidate End --->

        <!--- Edit Candidate Start --->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel">
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
    <script src="{{ asset('admin/assets/pages/app-whatsapp-campaign-list.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/validation/whatsapp-campaign-validation.js') }}"></script>
    


    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-file-upload.js') }}"></script>
    <script>
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
        });
    </script>
    <script>
        $(document).ready(function(){
            $('#add-audience').on('change',function(){
                var audience = $(this).val();
                if (audience == 'partner') {
                    $('#dispartnerview').show();
                    $('#disclientview').hide();
                    $('#disassocview').hide();
                    $('#disacontactpview').hide();
                } else if (audience == 'client') {
                    $('#dispartnerview').hide();
                    $('#disclientview').show();
                    $('#disassocview').hide();
                    $('#disacontactpview').hide();
                } else if (audience == 'associate') {
                    $('#dispartnerview').hide();
                    $('#disclientview').hide();
                    $('#disassocview').show();
                    $('#disacontactpview').hide();
                } else if (audience == 'contactp') {
                    $('#dispartnerview').hide();
                    $('#disclientview').hide();
                    $('#disassocview').hide();
                    $('#disacontactpview').show();                    
                } else {
                    $('#dispartnerview').hide();
                    $('#disclientview').hide();
                    $('#disassocview').hide();
                    $('#disacontactpview').hide();
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

            $('#add-contactp-personalise-class').on('change',function(){
                var contactPvalue = $(this).val();
                navigator.clipboard.writeText(contactPvalue);
                toastr['success']('Text copied - '+contactPvalue+'', 'Success', { hideDuration: 3000 });
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



@endsection