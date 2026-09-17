@extends('layout.admin.admin_layout')

@section('title','Whatsaapp Template')

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
        @if (isset($perm) && $perm->add_whatsapp_template == 0)
            <style>
                .addtemplate{
                    display: none !important;
                }
            </style>
        @endif
        @if (isset($perm) && $perm->edit_whatsapp_template == 0)
            <style>
                .edtemplate{
                    display: none !important;
                }
            </style>
        @endif
        @if (isset($perm) && $perm->delete_whatsapp_template == 0)
            <style>
                .deltemplate{
                    display: none !important;
                }
            </style>
        @endif
    @endif

    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($perm) && $perm->full_access == 1))
        <script>
            var distatus = '1';
            var dispublic = '1';
        </script>
    @else
        @if (isset($perm) && $perm->status_whatsapp_template == 1)
            <script>
                var distatus = '1';
            </script>
        @else
            <script>
                var distatus = '0';
            </script>
        @endif

        @if (isset($perm) && $perm->public_whatsapp_template == 0)
            <script>
                var dispublic = '0';
            </script>
        @else
            <script>
                var dispublic = '1';
            </script>
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
                            <th>Template Name</th>
                            <th>Template For</th>
                            {{-- <th>Subject Name</th> --}}
                            <th>Created By</th>
                            <th>Careoff</th>
                            <th>Message Status</th>
                            <th>Public</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!--- Add Candidate Start --->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Template</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.whatsapp.templateStore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4" >
                            <div class="mb-3">
                                <label for="add-template-audience" class="form-label">Audience <span class="text-danger">*</span></label>
                                <select name="audience" id="add-template-audience" class="form-select select2" data-allow-clear="true" data-placeholder="Select Audience">
                                    <option value="">Select</option>
                                    <option value="Partner">Partner</option>
                                    <option value="Client">Client</option>
                                    <option value="Associate">Associate</option>
                                    <option value="Contact+">Contact Plus</option>
                                    <option value="Allcontact">Allcontact</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-template-name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="template_name" id="add-template-name" class="form-control" placeholder="Enter Template Name...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-careoff" class="form-label">Careoff</label>
                                <select name="careoff_id" id="add-careoff" class="form-control select2" data-placeholder="Please Select Careoff" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($careoffs as $careoff)
                                        <option value="{{ $careoff->id }}">{{ $careoff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Contact + Variable Start -->
                        <div class="col-md-4" id="dispContactpVariable" style="display: none">
                            <div class="mb-3">
                                <label for="add-contactp-variable" class="form-label">Contact Plus Variable</label>
                                <select name="" id="add-contactp-variable" class="form-select select2" data-placeholder="Select Variable" data-allow-clear="true">
                                    <option value="">Select</option>
                                    <option value="[Company]">Company</option>
                                    <option value="[Office Name (English)]">Office Name (English)</option>
                                    <option value="[Office Name (Arabic)]">Office Name (Arabic)</option>
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
                                    <option value="[Status]">Status</option>
                                    <option value="[Unsubscribe]">Unsubscribe</option>
                                    <option value="[Membership]">Membership</option>
                                </select>
                            </div>
                        </div>
                        <!-- Contact + Variable End -->
                        <!-- Partner Variable Start -->
                        <div class="col-md-4" id="dispPartnerVariable" style="display: none">
                            <div class="mb-3">
                                <label for="add-partner-variable" class="form-label">Partner Variable</label>
                                <select name="" id="add-partner-variable" class="form-select select2" data-placeholder="Select Variable" data-allow-clear="true">
                                    <option value="">Select</option>
                                    <option value="[Recruitement Office Name (English)]">Recruitement Office Name (English)</option>
                                    <option value="[Recruitment Office Name (Arabic)]">Recruitment Office Name (Arabic)</option>
                                    <option value="[Owner Name]">Owner Name</option>
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
                        <!-- Partner Variable End -->
                        <!-- Client Variable Start -->
                        <div class="col-md-4" id="dispClientVariable" style="display: none">
                            <div class="mb-3">
                                <label for="add-client-variable" class="form-label">Client Variable</label>
                                <select name="" id="add-client-variable" class="form-select select2" data-placeholder="Select Variable" data-allow-clear="true">
                                    <option value="">Select</option>
                                    <option value="[Name]">Name</option>
                                    <option value="[Email]">Email</option>
                                    <option value="[Mobile]">Mobile</option>
                                    <option value="[Country]">Country</option>
                                    <option value="[City]">City</option>
                                    <option value="[Status]">Status</option>
                                </select>
                            </div>
                        </div>
                        <!-- Client Variable End -->
                        <!-- Associate Variable Start -->
                        <div class="col-md-4" id="dispAssocVariable" style="display: none">
                            <div class="mb-3">
                                <label for="add-associate-variable" class="form-label">Associate Variable</label>
                                <select name="" id="add-associate-variable" class="form-select select2" data-placeholder="Select Variable" data-allow-clear="true">
                                    <option value="">Select</option>
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
                        <!-- Associate Variable End -->
                        <!-- Associate Variable Start -->
                        <div class="col-md-4" id="dispAllcontVariable" style="display: none">
                            <div class="mb-3">
                                <label for="add-allcontact-variable" class="form-label">Allcontact Variable</label>
                                <select name="" id="add-allcontact-variable" class="form-select select2" data-placeholder="Select Allcontact" data-allow-clear="true">
                                    <option value="">Select</option>
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
                                    <option value="[Membership]">Membership</option>
                                </select>
                            </div>
                        </div>
                        <!-- Associate Variable End -->
                        <div class="col-md-6" >
                            <div class="mb-3">
                                <label for="add-msg-whatsapp" class="form-label">Whatsapp Message</label>
                                <textarea name="msg_whatsapp" class="form-control" id="add-msg-whatsapp" cols="30" rows="6"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-msg-whatsapp-arabic" class="form-label">Whatsapp Message Arabic</label>
                                <textarea name="msg_whatsapp_ar" class="form-control" id="add-msg-whatsapp-arabic" cols="30" rows="6"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" >
                                <label for="add-temp-file" class="form-label">Template File</label>
                                <input class="form-control template-file-input" name="photo" type="file" id="add-temp-file" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" id="dispIMG">
                                <img alt="user-avatar" src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" class="d-block w-px-100 h-px-100 rounded uploadedAvatar" id="uploadedAvatar"/>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" name="public" type="checkbox" id="add-public" value="1" />
                                    <label class="form-check-label" for="add-public">Public</label>
                                </div>
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
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="edituserLabel" class="offcanvas-title">Edit Template</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0 " id="editUserForm" action="{{ route('admin.whatsapp.templateUpdate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="edit_id" id="editid">

                    <div class="row">
                        <div class="col-md-4" >
                            <div class="mb-3">
                                <label for="edit-template-audience" class="form-label">Audience <span class="text-danger">*</span></label>
                                <select name="audience" id="edit-template-audience" class="form-select select2" data-allow-clear="true" data-placeholder="Select Audience">
                                    <option value="">Select</option>
                                    <option value="Partner">Partner</option>
                                    <option value="Client">Client</option>
                                    <option value="Associate">Associate</option>
                                    <option value="Contact+">Contact Plus</option>
                                    <option value="Allcontact">Allcontact</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-template-name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="template_name" id="edit-template-name" class="form-control" placeholder="Enter Template Name...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-careoff" class="form-label">Careoff</label>
                                <select name="careoff_id" id="edit-careoff" class="form-control select2" data-placeholder="Please Select Careoff" data-allow-clear="true">
                                    <option value="">Select</option>
                                    @foreach ($careoffs as $careoff2)
                                        <option value="{{ $careoff2->id }}">{{ $careoff2->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Contact + Variable Start -->
                        <div class="col-md-4" id="dispContactpVariableEdit" style="display: none">
                            <div class="mb-3">
                                <label for="edit-contactp-variable" class="form-label">Contact Plus Variable</label>
                                <select name="" id="edit-contactp-variable" class="form-select select2" data-placeholder="Select Variable" data-allow-clear="true">
                                    <option value="">Select</option>
                                    <option value="[Company]">Company</option>
                                    <option value="[Office Name (English)]">Office Name (English)</option>
                                    <option value="[Office Name (Arabic)]">Office Name (Arabic)</option>
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
                                    <option value="[Status]">Status</option>
                                    <option value="[Unsubscribe]">Unsubscribe</option>
                                    <option value="[Membership]">Membership</option>
                                </select>
                            </div>
                        </div>
                        <!-- Contact + Variable End -->

                        <!-- Contact + Variable Start -->
                        <div class="col-md-4" id="dispAllContactpVariableEdit" style="display: none">
                            <div class="mb-3">
                                <label for="edit-allcontactp-variable" class="form-label">Allcontact Variable</label>
                                <select name="" id="edit-allcontactp-variable" class="form-select select2" data-placeholder="Select Variable" data-allow-clear="true">
                                    <option value="">Select</option>
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
                                    <option value="[Membership]">Membership</option>
                                </select>
                            </div>
                        </div>
                        <!-- Contact + Variable End -->

                        <!-- Partner Variable Start -->
                        <div class="col-md-4" id="dispPartnerVariableEdit" style="display: none">
                            <div class="mb-3">
                                <label for="edit-partner-variable" class="form-label">Partner Variable</label>
                                <select name="" id="edit-partner-variable" class="form-select select2" data-placeholder="Select Variable" data-allow-clear="true">
                                    <option value="">Select</option>
                                    <option value="[Recruitement Office Name (English)]">Recruitement Office Name (English)</option>
                                    <option value="[Recruitment Office Name (Arabic)]">Recruitment Office Name (Arabic)</option>
                                    <option value="[Owner Name]">Owner Name</option>
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
                        <!-- Partner Variable End -->
                        <!-- Client Variable Start -->
                        <div class="col-md-4" id="dispClientVariableEdit" style="display: none">
                            <div class="mb-3">
                                <label for="edit-client-variable" class="form-label">Client Variable</label>
                                <select name="" id="edit-client-variable" class="form-select select2" data-placeholder="Select Variable" data-allow-clear="true">
                                    <option value="">Select</option>
                                    <option value="[Name]">Name</option>
                                    <option value="[Email]">Email</option>
                                    <option value="[Mobile]">Mobile</option>
                                    <option value="[Country]">Country</option>
                                    <option value="[City]">City</option>
                                    <option value="[Status]">Status</option>
                                </select>
                            </div>
                        </div>
                        <!-- Client Variable End -->
                        <!-- Associate Variable Start -->
                        <div class="col-md-4" id="dispAssocVariableEdit" style="display: none">
                            <div class="mb-3">
                                <label for="edit-associate-variable" class="form-label">Associate Variable</label>
                                <select name="" id="edit-associate-variable" class="form-select select2" data-placeholder="Select Variable" data-allow-clear="true">
                                    <option value="">Select</option>
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
                        <!-- Associate Variable End -->
                        <div class="col-md-6" >
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
                        <div class="col-md-6">
                            <div class="mb-3" >
                                <label for="edit-temp-file" class="form-label">Template File</label>
                                <input class="form-control template-file-input-edit" name="photo" type="file" id="edit-temp-file" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" id="dispIMG">
                                <img alt="user-avatar" src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" class="d-block w-px-100 h-px-100 rounded uploadedAvatar2" id="uploadedAvatar2"/>
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
        <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.whatsapp.templateDel') }}" method="POST">
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
        <div class="modal fade" id="activeStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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

        <div class="modal fade" id="deactiveStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
        <div class="modal fade" id="publishSts" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
        <div class="modal fade" id="statusChange" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Status change</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.whatsapp.updateGetStatus') }}" method="POST">
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
    {{-- <script src="{{ asset('admin/assets/pages/app-template-list.js') }}"></script> --}}
    <script src="{{ asset('admin/assets/pages/app-whatsapp-template-list.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/validation/template-validation.js') }}"></script>



    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-file-upload.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('#add-template-audience').on('change',function(){
                let audienceValue = $(this).val();
                if (audienceValue == 'Partner') {
                    $('#dispContactpVariable').hide();
                    $('#dispPartnerVariable').show();
                    $('#dispClientVariable').hide();
                    $('#dispAssocVariable').hide();
                    $('#dispAllcontVariable').hide();
                }else if (audienceValue == 'Client') {
                    $('#dispContactpVariable').hide();
                    $('#dispPartnerVariable').hide();
                    $('#dispClientVariable').show();
                    $('#dispAssocVariable').hide();
                    $('#dispAllcontVariable').hide();
                }else if (audienceValue == 'Associate') {
                    $('#dispContactpVariable').hide();
                    $('#dispPartnerVariable').hide();
                    $('#dispClientVariable').hide();
                    $('#dispAssocVariable').show();
                    $('#dispAllcontVariable').hide();
                }else if (audienceValue == 'Contact+') {
                    $('#dispContactpVariable').show();
                    $('#dispPartnerVariable').hide();
                    $('#dispClientVariable').hide();
                    $('#dispAssocVariable').hide();
                    $('#dispAllcontVariable').hide();
                }else if(audienceValue == 'Allcontact'){
                    $('#dispContactpVariable').hide();
                    $('#dispPartnerVariable').hide();
                    $('#dispClientVariable').hide();
                    $('#dispAssocVariable').hide();
                    $('#dispAllcontVariable').show();
                }else{
                    $('#dispContactpVariable').hide();
                    $('#dispPartnerVariable').hide();
                    $('#dispClientVariable').hide();
                    $('#dispAssocVariable').hide();
                    $('#dispAllcontVariable').hide();
                }
            });


            $('#edit-template-audience').on('change',function(){
                let audienceValue = $(this).val();
                if (audienceValue == 'Partner') {
                    $('#dispContactpVariableEdit').hide();
                    $('#dispPartnerVariableEdit').show();
                    $('#dispClientVariableEdit').hide();
                    $('#dispAssocVariableEdit').hide();
                    $('#dispAllContactpVariableEdit').hide();
                }else if (audienceValue == 'Client') {
                    $('#dispContactpVariableEdit').hide();
                    $('#dispPartnerVariableEdit').hide();
                    $('#dispClientVariableEdit').show();
                    $('#dispAssocVariableEdit').hide();
                    $('#dispAllContactpVariableEdit').hide();
                }else if (audienceValue == 'Associate') {
                    $('#dispContactpVariableEdit').hide();
                    $('#dispPartnerVariableEdit').hide();
                    $('#dispClientVariableEdit').hide();
                    $('#dispAssocVariableEdit').show();
                    $('#dispAllContactpVariableEdit').hide();
                }else if (audienceValue == 'Contact+') {
                    $('#dispContactpVariableEdit').show();
                    $('#dispPartnerVariableEdit').hide();
                    $('#dispClientVariableEdit').hide();
                    $('#dispAssocVariableEdit').hide();
                    $('#dispAllContactpVariableEdit').hide();

                }else if(audienceValue == 'Allcontact'){
                    $('#dispContactpVariableEdit').hide();
                    $('#dispPartnerVariableEdit').hide();
                    $('#dispClientVariableEdit').hide();
                    $('#dispAssocVariableEdit').hide();
                    $('#dispAllContactpVariableEdit').show();
                }else{
                    $('#dispContactpVariableEdit').hide();
                    $('#dispPartnerVariableEdit').hide();
                    $('#dispClientVariableEdit').hide();
                    $('#dispAssocVariableEdit').hide();
                    $('#dispAllContactpVariableEdit').hide();
                }
            });

        });
    </script>

    <script>
        $(document).ready(function(){
            $('#add-associate-variable').on('change',function(){
                var assovalue = $(this).val();
                if (assovalue != '') {
                    navigator.clipboard.writeText(assovalue);
                    toastr['success']('Text copied - '+assovalue+'', 'Success', { hideDuration: 3000 });
                }

            });

            $('#add-partner-variable').on('change',function(){
                var partvalue = $(this).val();
                if (partvalue != '') {
                    navigator.clipboard.writeText(partvalue);
                    toastr['success']('Text copied - '+partvalue+'', 'Success', { hideDuration: 3000 });
                }
            });

            $('#add-client-variable').on('change',function(){
                var clientvalue = $(this).val();
                if (clientvalue != '') {
                    navigator.clipboard.writeText(clientvalue);
                    toastr['success']('Text copied - '+clientvalue+'', 'Success', { hideDuration: 3000 });
                }
            });

            $('#add-contactp-variable').on('change',function(){
                var contactPvalue = $(this).val();
                if (contactPvalue != '') {
                    navigator.clipboard.writeText(contactPvalue);
                    toastr['success']('Text copied - '+contactPvalue+'', 'Success', { hideDuration: 3000 });
                }
            });

            $('#add-allcontact-variable').on('change',function(){
                var allcontactPvalue = $(this).val();
                if (allcontactPvalue != '') {
                    navigator.clipboard.writeText(allcontactPvalue);
                    toastr['success']('Text copied - '+allcontactPvalue+'', 'Success', { hideDuration: 3000 });
                }
            });

        });
    </script>

    <script>
        $(document).ready(function(){
            $('#edit-associate-variable').on('change',function(){
                var assovalue = $(this).val();
                if (assovalue != '') {
                    navigator.clipboard.writeText(assovalue);
                    toastr['success']('Text copied - '+assovalue+'', 'Success', { hideDuration: 3000 });
                }

            });

            $('#edit-partner-variable').on('change',function(){
                var partvalue = $(this).val();
                if (partvalue != '') {
                    navigator.clipboard.writeText(partvalue);
                    toastr['success']('Text copied - '+partvalue+'', 'Success', { hideDuration: 3000 });
                }
            });

            $('#edit-client-variable').on('change',function(){
                var clientvalue = $(this).val();
                if (clientvalue != '') {
                    navigator.clipboard.writeText(clientvalue);
                    toastr['success']('Text copied - '+clientvalue+'', 'Success', { hideDuration: 3000 });
                }
            });

            $('#edit-contactp-variable').on('change',function(){
                var contactPvalue = $(this).val();
                if (contactPvalue != '') {
                    navigator.clipboard.writeText(contactPvalue);
                    toastr['success']('Text copied - '+contactPvalue+'', 'Success', { hideDuration: 3000 });
                }
            });

            $('#edit-allcontactp-variable').on('change',function(){
                var allcontact = $(this).val();
                if (allcontact != '') {
                    navigator.clipboard.writeText(allcontact);
                    toastr['success']('Text copied - '+allcontact+'', 'Success', { hideDuration: 3000 });
                }
            });



        });
    </script>




    <script>
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
                    url : '{{ url('admin/whatsapp-template-list/edit') }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#editid').val(data.id);
                        $('#edit-template-name').val(data.template_name);
                        $('#edit-template-audience').val(data.audience).trigger('change');
                        // $('#edit-subject-name').val(data.subject_name);
                        $('#edit-msg-whatsapp').val(data.msg_whatsapp);
                        $('#edit-msg-whatsapp-arabic').val(data.msg_whatsapp_ar);

                        $('#edit-careoff').val(data.careoff_id).change();

                        // $('#edit-email-message').val(data.msg_email);
                        // $('#edit-sms-message').val(data.msg_sms);
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

                        // var mailDesc = data.msg_email;
                        // var quill_editor = $('#mail-text2 .ql-editor');
                        // quill_editor[0].innerHTML = mailDesc;

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
    </script>


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

                // alert(tempchstID);

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                jQuery.ajax({
                    url : '{{ route("admin.whatsapp.getStatus") }}',
                    method: "POST",
                    type: "html",
                    data: {
                        "id": tempchstID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#tempchstID').val(data.id);
                        // console.log(data);

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
