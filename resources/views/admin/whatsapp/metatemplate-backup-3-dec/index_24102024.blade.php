@extends('layout.admin.admin_layout')

@section('title','Whatsaapp Meta Template')

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
                            <th>Template Name</th>
                            {{-- <th>Subject Name</th> --}}
                            <th>Created By</th>
                            <th>Message Status</th>
                            <th>Public</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!--- Add Candidate Start --->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Template</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.whatsapp.metatemplateStore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-template-for" class="form-label">Template For <span class="text-danger">*</span></label>
                                <select name="template_for" id="add-template-for" class="form-select select2" data-allow-clear="true" data-placeholder="Select Template Fors">
                                    <option value=""></option>
                                    <option value="contactp">Contact+</option>
                                    <option value="associate">Associate</option>
                                    <option value="partner">Partner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="add-temp-name">Meta Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="template_name" id="add-temp-name" class="form-control" placeholder="Enter candidate name...">
                            </div>
                        </div>
                    </div>
                    <!-- Contatc+ Field Variable Start -->
                    {{-- <div class="row disviewcontactp" style="display: none">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="send-meta-template-field-variable" class="form-label">Field Variable <span class="text-danger">*</span></label>
                                <select name="meta_field_variable[]" id="send-meta-template-field-variable" class="form-select select2" data-allow-clear="clear" data-placeholder="Select Field Variable">
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
                                    <option value="field_6">Field 6</option>
                                    <option value="field_7">Field 7</option>
                                    <option value="field_8">Field 8</option>
                                    <option value="field_9">Field 9</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="send-meta-template-assign-variable" class="form-label">Assign Variable <span class="text-danger">*</span></label>
                                <select name="meta_assign_variable[]" id="send-meta-template-assign-variable" class="form-select select2" data-placeholder="Select Assign Variable" data-allow-clear="true">
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
                        </div>
                    </div> --}}
                    <div id="dispContactp" class="disviewcontactp" style="display: none"></div>
                    <div class="row disviewcontactp" id="dispAddContactpBtn" style="display: none">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-primary addContactpDisp float-end">Add Variable</button>
                        </div>
                    </div>
                    <!-- Contact+ Field Variable End -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-msg-whatsapp" class="form-label">Whatsapp Message</label>
                                <textarea name="msg_whatsapp" class="form-control" id="add-msg-whatsapp" cols="30" rows="6"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3" >
                                <label for="add-temp-file" class="form-label">Template File</label>
                                <input class="form-control template-file-input" name="photo" type="file" id="add-temp-file" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar"/>
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
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel">
            <div class="offcanvas-header">
                <h5 id="edituserLabel" class="offcanvas-title">Edit Template</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0 " id="editUserForm" action="{{ route('admin.whatsapp.metatemplateupdate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="edit_id" id="editid">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-template-for" class="form-label">Template For <span class="text-danger">*</span></label>
                                <select name="template_for" id="edit-template-for" class="form-select select2" data-allow-clear="true" data-placeholder="Select Template Fors">
                                    <option value=""></option>
                                    <option value="contactp">Contact+</option>
                                    <option value="associate">Associate</option>
                                    <option value="partner">Partner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="edit-temp-name">Meta Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="template_name" id="edit-temp-name" class="form-control" placeholder="Enter candidate name...">
                            </div>
                        </div>
                    </div>
                    <!-- Contatc+ Field Variable Start -->
                    {{-- <div class="row disviewcontactpe" style="display: none">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="send-meta-template-field-variable" class="form-label">Field Variable <span class="text-danger">*</span></label>
                                <select name="meta_field_variable[]" id="send-meta-template-field-variable" class="form-select select2" data-allow-clear="clear" data-placeholder="Select Field Variable">
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
                                    <option value="field_6">Field 6</option>
                                    <option value="field_7">Field 7</option>
                                    <option value="field_8">Field 8</option>
                                    <option value="field_9">Field 9</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="send-meta-template-assign-variable" class="form-label">Assign Variable <span class="text-danger">*</span></label>
                                <select name="meta_assign_variable[]" id="send-meta-template-assign-variable" class="form-select select2" data-placeholder="Select Assign Variable" data-allow-clear="true">
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
                        </div>
                    </div> --}}
                    <div id="responseValueContact" class="disviewcontactpe"></div>
                    <div id="dispContactpe" class="disviewcontactpe" style="display: none"></div>
                    <div class="row disviewcontactpe" id="dispAddContactpBtne" style="display: none">
                        <div class="col-md-12 mt-2">
                            <button type="button" class="btn btn-sm btn-primary addContactpDispe float-end">Add</button>
                        </div>
                    </div>
                    <!-- Contact+ Field Variable End -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-msg-whatsapp" class="form-label">Whatsapp Message</label>
                                <textarea name="msg_whatsapp" class="form-control" id="edit-msg-whatsapp" cols="30" rows="6"></textarea>
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
                    <form action="{{ route('admin.whatsapp.metatemplatePublicUpdate') }}" method="POST">
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
                    <form action="{{ route('admin.whatsapp.metatemplateStatusUpdate') }}" method="POST">
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
    <script src="{{ asset('admin/assets/pages/app-whatsapp-meta-template-list.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/validation/meta-template-validation.js') }}"></script>
    


    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-file-upload.js') }}"></script>
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
                    url : '{{ route("admin.whatsapp.metatemplateedit") }}',
                    method: "GET",
                    type: "html",
                    data: {
                        "id": editID,
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){
                        $('#editid').val(data.id);
                        $('#edit-template-for').val(data.template_for).trigger("change");
                        $('#edit-temp-name').val(data.template_name);
                        // $('#edit-subject-name').val(data.subject_name);
                        $('#edit-msg-whatsapp').val(data.whatsapp_message);
                        $('#edit-msg-whatsapp-arabic').val(data.msg_whatsapp_ar);
                        // $('#edit-email-message').val(data.msg_email);
                        // $('#edit-sms-message').val(data.msg_sms);
                        if (data.public == 1) {
                            // $('#edit-public').val(data.public).prop("checked",true);
                            $('#edit-public').prop("checked",true);
                        } else {
                            // $('#edit-public').val(data.public).prop("checked",false);
                            $('#edit-public').prop("checked",false);
                        }

                        if (data.whatsapp_file != '') {
                            var file_path = imgPath+'/'+data.whatsapp_file;
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

                        const meta_field_var = data.meta_field_var.split(",");
                        const meta_assign_var_name = data.meta_assign_ar.split(",");
                        var dynamic_assign_var = "";

                        for (let i = 0; i < meta_field_var.length; i++) {
                            // Meta Field Variable
                            if (meta_field_var[i] == 'header_image') {
                                var header_image_selected = 'selected';
                            } else {
                                var header_image_selected = '';
                            }
                            if (meta_field_var[i] == 'header_video') {
                                var header_video_selected = 'selected';
                            } else {
                                var header_video_selected = '';
                            }
                            if (meta_field_var[i] == 'header_document') {
                                var header_document_selected = 'selected';
                            } else {
                                var header_document_selected = '';
                            }
                            if (meta_field_var[i] == 'header_document_name') {
                                var header_document_name_selected = 'selected';
                            } else {
                                var header_document_name_selected = '';
                            }
                            if (meta_field_var[i] == 'field_1') {
                                var field_1_selected = 'selected';
                            } else {
                                var field_1_selected = '';
                            }

                            if (meta_field_var[i] == 'field_2') {
                                var field_2_selected = 'selected';
                            } else {
                                var field_2_selected = '';
                            }

                            if (meta_field_var[i] == 'field_3') {
                                var field_3_selected = 'selected';
                            } else {
                                var field_3_selected = '';
                            }

                            if (meta_field_var[i] == 'field_4') {
                                var field_4_selected = 'selected';
                            } else {
                                var field_4_selected = '';
                            }

                            if (meta_field_var[i] == 'field_5') {
                                var field_5_selected = 'selected';
                            } else {
                                var field_5_selected = '';
                            }

                            if (meta_field_var[i] == 'field_6') {
                                var field_6_selected = 'selected';
                            } else {
                                var field_6_selected = '';
                            }

                            if (meta_field_var[i] == 'field_7') {
                                var field_7_selected = 'selected';
                            } else {
                                var field_7_selected = '';
                            }

                            if (meta_field_var[i] == 'field_8') {
                                var field_8_selected = 'selected';
                            } else {
                                var field_8_selected = '';
                            }

                            if (meta_field_var[i] == 'field_9') {
                                var field_9_selected = 'selected';
                            } else {
                                var field_9_selected = '';
                            }

                            if (meta_field_var[i] == 'field_10') {
                                var field_10_selected = 'selected';
                            } else {
                                var field_10_selected = '';
                            }

                            // Assign Variable

                            if (meta_assign_var_name[i] == '[Others]') {
                                var Others_selected = 'selected';
                            } else {
                                var Others_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Office Name (English)]') {
                                var office_name_en_selected = 'selected';
                            } else {
                                var office_name_en_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Office Name (Arabic)]') {
                                var office_name_ar_selected = 'selected';
                            } else {
                                var office_name_ar_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Office Number]') {
                                var office_number_selected = 'selected';
                            } else {
                                var office_number_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Office Email]') {
                                var office_email_selected = 'selected';
                            } else {
                                var office_email_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Owner Name]') {
                                var owner_name_selected = 'selected';
                            } else {
                                var owner_name_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Owner Contact]') {
                                var owner_contact_selected = 'selected';
                            } else {
                                var owner_contact_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Owner Email]') {
                                var owner_email_selected = 'selected';
                            } else {
                                var owner_email_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Country]') {
                                var country_selected = 'selected';
                            } else {
                                var country_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[City]') {
                                var city_selected = 'selected';
                            } else {
                                var city_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Primary Concern Person]') {
                                var priamry_con_per_selected = 'selected';
                            } else {
                                var priamry_con_per_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Primary Contact No]') {
                                var primary_con_no_selected = 'selected';
                            } else {
                                var primary_con_no_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Primary Email]') {
                                var primary_email_selected = 'selected';
                            } else {
                                var primary_email_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Secondary Concern Person]') {
                                var secondary_con_person_selected = 'selected';
                            } else {
                                var secondary_con_person_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Secondary Contact No]') {
                                var secondary_con_selected = 'selected';
                            } else {
                                var secondary_con_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Secondary Email]') {
                                var secon_email_selected = 'selected';
                            } else {
                                var secon_email_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Concern Person 3]') {
                                var concern_per3_selected = 'selected';
                            } else {
                                var concern_per3_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Contact No 3]') {
                                var contact_no3_selected = 'selected';
                            } else {
                                var contact_no3_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Concern Person 4]') {
                                var concern_per4_selected = 'selected';
                            } else {
                                var concern_per4_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Contact No 4]') {
                                var contact_no4_selected = 'selected';
                            } else {
                                var contact_no4_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Concern Person 5]') {
                                var concern_per5_selected = 'selected';
                            } else {
                                var concern_per5_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Contact No 5]') {
                                var contact_no5_selected = 'selected';
                            } else {
                                var contact_no5_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Concer Person 6]') {
                                var concern_per6_selected = 'selected';
                            } else {
                                var concern_per6_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Contact No 6]') {
                                var contact_no6_selected = 'selected';
                            } else {
                                var contact_no6_selected = '';
                            }

                            if (meta_assign_var_name[i] == '[Status]') {
                                var status_selected = 'selected';
                            } else {
                                var status_selected = '';
                            }

                            dynamic_assign_var += "<div class='row'>";
                            dynamic_assign_var += "<div class='col-md-6'><div class='mb-3'>";
                            dynamic_assign_var += "<label class='form-label' for='edit-field-contactp-variable-dyn"+i+"'>Meta Field Variable</label>";
                            dynamic_assign_var += "<select name='field_var_contactp[]' id='edit-field-contactp-variable-dyn"+i+"' class='form-select fieldVariable select2'><option value=''>Select</option><option value='header_image' "+header_image_selected+">Header Image</option><option value='header_video' "+header_video_selected+">Header Video</option><option value='header_document' "+header_document_selected+">Header Document</option><option value='header_document_name' "+header_document_name_selected+">Header Document Name</option><option value='field_1' "+field_1_selected+">Field 1</option><option value='field_2' "+field_2_selected+">Field 2</option><option value='field_3' "+field_3_selected+">Field 3</option><option value='field_4' "+field_4_selected+">Field 4</option><option value='field_5' "+field_5_selected+">Field 5</option><option value='field_6' "+field_6_selected+">Field 6</option><option value='field_7' "+field_7_selected+">Field 7</option><option value='field_8' "+field_8_selected+">Field 8</option><option value='field_9' "+field_9_selected+">Field 9</option>";
                            dynamic_assign_var += "</select></div></div>";
                        
                            dynamic_assign_var += "<div class='col-md-6'><div class='mb-3'>";
                            dynamic_assign_var += "<label class='form-label' for='edit-assign-field-var-dyn"+i+"'>Assign Variable</label>";
                            dynamic_assign_var += "<select name='assign_var_contactp[]' id='edit-assign-field-var-dyn"+i+"' class='form-select assignVariableContactPOPT select2'><option value=''>Select</option><option value='[Others]' "+Others_selected+">File URL</option><option value='[Office Name (English)]' "+office_name_en_selected+">Office Name (English)</option><option value='[Office Name (Arabic)]' "+office_name_ar_selected+">Office Name (Arabic)</option><option value='[Office Number]' "+office_number_selected+">Office Number</option><option value='[Office Email]' "+office_email_selected+">Office Email</option><option value='[Owner Name]' "+owner_name_selected+">Owner Name</option><option value='[Owner Contact]' "+owner_contact_selected+">Owner Contact</option><option value='[Owner Email]' "+owner_email_selected+">Owner Email</option><option value='[Country]' "+country_selected+">Country</option><option value='[City]' "+city_selected+">City</option><option value='[Primary Concern Person]' "+priamry_con_per_selected+">Primary Concern Person</option><option value='[Primary Contact No]' "+primary_con_no_selected+">Primary Contact No</option><option value='[Primary Email]' "+primary_email_selected+">Primary Email</option><option value='[Secondary Concern Person]' "+secondary_con_person_selected+">Secondary Concern Person</option><option value='[Secondary Contact No]' "+secondary_con_selected+">Secondary Contact No</option><option value='[Secondary Email]' "+secon_email_selected+">Secondary Email</option><option value='[Concern Person 3]' "+concern_per3_selected+">Concern Person 3</option><option value='[Contact No 3]' "+contact_no3_selected+">Contact No 3</option><option value='[Concern Person 4]' "+concern_per4_selected+">Concern Person 4</option><option value='[Contact No 4]' "+contact_no4_selected+">Contact No 4</option><option value='[Concern Person 5]' "+concern_per5_selected+">Concern Person 5</option><option value='[Contact No 5]' "+contact_no5_selected+">Contact No 5</option><option value='[Concer Person 6]' "+concern_per6_selected+">Concer Person 6</option><option value='[Contact No 6]' "+contact_no6_selected+">Contact No 6</option><option value='[Status]' "+status_selected+">Work Status</option>";


                            dynamic_assign_var += "</select></div></div>";
                            dynamic_assign_var += "<div class='col-md-12'><button type='button' class='btn btn-sm btn-danger remove2 float-end'>Remove</button></div></div>"
                            dynamic_assign_var += "</div>";
                        }

                        $('#responseValueContact').html(dynamic_assign_var); 
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
                    url : '{{ route("admin.whatsapp.metatemplatePublic") }}',
                    method: "GET",
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
                    url : '{{ route("admin.whatsapp.metatemplateStatus") }}',
                    method: "GET",
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
        $(document).ready(function(){
            $('#add-template-for').on('change',function(){
                var templatefor = $(this).val();

                if (templatefor == 'contactp') {
                    $('.disviewcontactp').show();
                } else {
                    $('.disviewcontactp').hide();
                }

            });

            $("#edit-template-for").on('change',function(){
                var templatefore = $(this).val();

                if (templatefore == 'contactp') {
                    $(".disviewcontactpe").show();
                } else {
                    $(".disviewcontactpe").hide();
                }


            });
        });
    </script>

<script>
    var rowMin4 = 0;
    var rowMax4 = 10;

    $(document).on('click','.addContactpDisp',function(){
        var html = "";
        html += '<div class="row"><div class="col-md-6"><div class="form-group">';
        html += '<label class="" for="add-field-contactp-variable'+rowMin4+'">Meta Variable <span class="text-danger">*</span></label>';
        html += '<select name="field_var_contactp[]" id="add-field-contactp-variable'+rowMin4+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_1">Button 1</option><option value="button_2">Button 2</option><option value="button_3">Button 3</option><option value="button_4">Button 4</option><option value="button_5">Button 5</option>';
        html += '</select></div></div>';

        html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_contactp'+rowMin4+'">Contact+ Variable <span class="text-danger">*</span></label>';
        html += '<select name="assign_var_contactp[]" id="assign_var_contactp'+rowMin4+'" class="form-control assignVariableContactPOPT select2a"><option value="">Select</option><option value="[Others]">Static URL</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Office Name (English)]">Office Name (English)</option><option value="[Office Name (Arabic)]">Office Name (Arabic)</option><option value="[Office Number]">Office Number</option><option value="[Office Email]">Office Email</option><option value="[Owner Name]">Owner Name</option><option value="[Owner Contact]">Owner Contact</option><option value="[Owner Email]">Owner Email</option><option value="[Country]">Country</option><option value="[City]">City</option><option value="[Primary Concern Person]">Primary Concern Person</option><option value="[Primary Contact No]">Primary Contact No</option><option value="[Primary Email]">Primary Email</option><option value="[Secondary Concern Person]">Secondary Concern Person</option><option value="[Secondary Contact No]">Secondary Contact No</option><option value="[Secondary Email]">Secondary Email</option><option value="[Concern Person 3]">Concern Person 3</option><option value="[Contact No 3]">Contact No 3</option><option value="[Concern Person 4]">Concern Person 4</option><option value="[Contact No 4]">Contact No 4</option><option value="[Concern Person 5]">Concern Person 5</option><option value="[Contact No 5]">Contact No 5</option><option value="[Concer Person 6]">Concer Person 6</option><option value="[Contact No 6]">Contact No 6</option><option value="[Status]">Work Status</option>';
        html += '</select></div></div>';
        html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';
        
        

        $('#dispContactp').append(html);
        var select2a = $('.select2a');
        if (select2a.length) {
            select2a.each(function () {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    // placeholder: 'Select value',
                    dropdownParent: $this.parent()
                });
            });   
        }

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
    var rowMin5 = 0;
    var rowMax5 = 10;

    $(document).on('click','.addContactpDispe',function(){
        var html = "";
        html += '<div class="row"><div class="col-md-6"><div class="form-group">';
        html += '<label class="" for="add-field-contactp-variable'+rowMin5+'">Field Variable <span class="text-danger">*</span></label>';
        html += '<select name="field_var_contactp[]" id="add-field-contactp-variable'+rowMin5+'" class="form-control fieldVariable select2"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option>';
        html += '</select></div></div>';

        html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_contactp'+rowMin5+'">Assign Variable Client <span class="text-danger">*</span></label>';
        html += '<select name="assign_var_contactp[]" id="assign_var_contactp'+rowMin5+'" class="form-control assignVariableContactPOPT select2"><option value="">Select</option><option value="[Others]">Static URL</option><option value="[Office Name (English)]">Office Name (English)</option><option value="[Office Name (Arabic)]">Office Name (Arabic)</option><option value="[Office Number]">Office Number</option><option value="[Office Email]">Office Email</option><option value="[Owner Name]">Owner Name</option><option value="[Owner Contact]">Owner Contact</option><option value="[Owner Email]">Owner Email</option><option value="[Country]">Country</option><option value="[City]">City</option><option value="[Primary Concern Person]">Primary Concern Person</option><option value="[Primary Contact No]">Primary Contact No</option><option value="[Primary Email]">Primary Email</option><option value="[Secondary Concern Person]">Secondary Concern Person</option><option value="[Secondary Contact No]">Secondary Contact No</option><option value="[Secondary Email]">Secondary Email</option><option value="[Concern Person 3]">Concern Person 3</option><option value="[Contact No 3]">Contact No 3</option><option value="[Concern Person 4]">Concern Person 4</option><option value="[Contact No 4]">Contact No 4</option><option value="[Concern Person 5]">Concern Person 5</option><option value="[Contact No 5]">Contact No 5</option><option value="[Concer Person 6]">Concer Person 6</option><option value="[Contact No 6]">Contact No 6</option><option value="[Status]">Work Status</option>';
        html += '</select></div></div>';
        html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove2 float-end mb-2 mt-2">Remove</button></div></div>';
        
        

        $('#dispContactpe').append(html);

    
        rowMin5++;
        rowMax5--;
        if (rowMax5 == 1) {
            $('.addContactpDispe').prop('disabled',true);
        }else{
            $('.addContactpDispe').prop('disabled',false);
        }
    });
    $(document).on('click','.remove2',function(){
        $(this).closest('.row').remove();
        rowMax5++;
        rowMin5--;
        if (rowMax5 == 0) {
            $('.addContactpDispe').prop('disabled',true);
        }else{
            $('.addContactpDispe').prop('disabled',false);
        }
    });
</script>

@endsection

    




    

    

    

  