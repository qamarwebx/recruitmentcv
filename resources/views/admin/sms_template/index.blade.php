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
        @if (isset($perm) && $perm->add_sms_template == 0)
        <style>
            .add_sms_template{
                display: none !important;
            }
        </style>
        @endif
        @if (isset($perm) && $perm->edit_sms_template == 0)
        <style>
        .edit_sms_template{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->delete_sms_template == 0)
        <style>
        .delete_sms_template{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->change_status_sms_template == 0)
        <style>
        .change_status_sms_template{
            display: none !important;
        }
        </style>
        @endif


    @endif


    @if (Auth::guard('admin')->user()->user_type == 1 || (isset($perm) && $perm->full_access == 1))
        <script>
            var disStatusTemp = '1';
            var disPublicTemp = '1';
        </script>
    @else

        @if (isset($perm) && $perm->change_status_sms_template == 0)
            <script>
                var disStatusTemp = '0';
            </script>
        @else
            <script>
                var disStatusTemp = '1';
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
                            <th>API</th>
                            <th>Template For</th>
                            <th>Careoff</th>
                            <th>Created By</th>
                            <th>Message Status</th>
                            <th>Public</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <!--- Add SMS Template  Start --->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Template</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.sms.templateStore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-template-for" class="form-label">Template For <span class="text-danger">*</span></label>
                                <select name="template_for" id="add-template-for" class="form-select select2" data-placeholder="Select Template For">
                                    <option value=""></option>
                                    <option value="leads">Leads</option>
                                    <option value="contactp">Contact Plus</option>
                                    <option value="allcontact">Allcontact</option>
                                    <option value="associate">Associate</option>
                                    <option value="partner">Partner</option>
                                    <option value="employer">Employer</option>
                                    <option value="leads_employer">Leads Employer</option>
                                    <option value="leads_candidate">Leads Candidate</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-template-used-for" class="form-label">Template Use For</label>
                                <select name="template_used_for" id="add-template-used-for" class="form-select select2">
                                    <option value=""></option>
                                    <option value="campaign">Campaign</option>
                                    <option value="auto_message">Auto Message</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-meta-api-name">API Name</label>
                                <select name="sms_api_id" id="add-meta-api-name" class="form-select select2" data-placeholder="Select API">
                                    <option value="">Select</option>
                                    @foreach ($metaAPILists as $metaAPIList)
                                        <option value="{{ $metaAPIList->id }}">{{ $metaAPIList->api_name.' ('.$metaAPIList->mobile_no.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-temp-name" class="form-label">Template Name</label>
                                <input type="text" name="template_name" id="add-temp-name" class="form-control" placeholder="Enter Template Name">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-template-type" class="form-label">Template Type</label>
                                <select name="template_type" id="add-template-type" class="form-control select2">
                                    <option value="">Select</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Utility">Utility</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-careoff" class="form-label">Careoff</label>
                                <select name="careoff_id" id="add-careoff" class="form-control select2">
                                    <option value="">Select</option>
                                    @foreach ($careoffs as $careoff)
                                        <option value="{{ $careoff->id }}">{{ $careoff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- File upload -->
                    <div class="row" id="dispAddFileForALL">
                        <div class="col-md-10">
                            <div class="mb-3">
                                <label for="add-temp-file2" class="form-label">Template File</label>
                                <input class="form-control" name="photo" type="file" id="add-temp-file2" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" class="w-px-100 h-px-100 rounded" id="uploadedAvatarf2"/>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-sms-message" class="form-label">SMS / Whatsapp Message</label>
                                <textarea name="sms_message" id="add-sms-message" class="form-control" cols="30" rows="6"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Arabic message -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-msg-ar" class="form-label">Message (Arabic)</label>
                                <textarea name="msg_sms_ar" id="add-msg-ar" class="form-control" cols="30" rows="6"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- URL type -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">SMS URL Type</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="sms_url_type" value="0" id="fileUpload">
                                    <label class="form-check-label" for="fileUpload">File Upload</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="sms_url_type" value="1" id="fileURL">
                                    <label class="form-check-label" for="fileURL">File URL</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Static URL -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-static-url-field" class="form-label">Media / Static URL</label>
                                <input type="text" name="static_url" id="add-static-url-field" class="form-control" placeholder="Enter URL">
                            </div>
                        </div>
                    </div>

                    <!-- Document name -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-document-name" class="form-label">Document Name</label>
                                <input type="text" name="document_name" id="add-document-name" class="form-control" placeholder="Enter Document Name">
                            </div>
                        </div>
                    </div>

                    <!-- Public -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" name="public" type="checkbox" id="add-public" value="1" />
                                <label class="form-check-label" for="add-public">Public</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Add SMS Template End --->

        <!--- Edit SMS Template Start --->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end" tabindex="-1" id="editSmsTemplate"
            aria-labelledby="editSmsTemplateLabel" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="offcanvas-header">
                <h5 id="editSmsTemplateLabel" class="offcanvas-title">Edit SMS Template</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
                <form class="edit-sms-template pt-0" id="editSmsTemplateForm"
                    action="{{ route('admin.sms.templateUpdate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id">

                    <div class="row">
                        <!-- Template For -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-template-for" class="form-label">Template For <span class="text-danger">*</span></label>
                                <select name="template_for" id="edit-template-for" class="form-select select2"
                                        data-placeholder="Select Template For">
                                    <option value=""></option>
                                    <option value="leads">Leads</option>
                                    <option value="contactp">Contact Plus</option>
                                    <option value="allcontact">Allcontact</option>
                                    <option value="associate">Associate</option>
                                    <option value="partner">Partner</option>
                                    <option value="employer">Employer</option>
                                    <option value="leads_employer">Leads Employer</option>
                                    <option value="leads_candidate">Leads Candidate</option>
                                </select>
                            </div>
                        </div>

                        <!-- Template Used For -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-template-used-for" class="form-label">Template Use For</label>
                                <select name="template_used_for" id="edit-template-used-for" class="form-select select2">
                                    <option value=""></option>
                                    <option value="campaign">Campaign</option>
                                    <option value="auto_message">Auto Message</option>
                                </select>
                            </div>
                        </div>

                        <!-- API -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-api-name" class="form-label">API Name</label>
                                <select name="sms_api_id" id="edit-api-name" class="form-select select2">
                                    <option value="">Select</option>
                                    @foreach ($metaAPILists as $metaAPIList)
                                        <option value="{{ $metaAPIList->id }}">
                                            {{ $metaAPIList->api_name.' ('.$metaAPIList->mobile_no.')' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Template Name -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-template-name" class="form-label">Template Name</label>
                                <input type="text" name="template_name" id="edit-template-name" class="form-control"
                                    placeholder="Enter Template Name">
                            </div>
                        </div>

                        <!-- Template Type -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-template-type" class="form-label">Template Type</label>
                                <select name="template_type" id="edit-template-type" class="form-select select2">
                                    <option value="">Select</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Utility">Utility</option>
                                </select>
                            </div>
                        </div>

                        <!-- Careoff -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-careoff" class="form-label">Careoff</label>
                                <select name="careoff_id" id="edit-careoff" class="form-select select2">
                                    <option value="">Select</option>
                                    @foreach ($careoffs as $careoff)
                                        <option value="{{ $careoff->id }}">{{ $careoff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="row">
                        <div class="col-md-10">
                            <div class="mb-3">
                                <label for="edit-temp-file" class="form-label">Template File</label>
                                <input class="form-control" name="photo" type="file" id="edit-temp-file" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" class="w-px-100 h-px-100 rounded"
                                id="uploadedAvatarEdit" alt="template-file-preview" />
                        </div>
                    </div>

                    <!-- SMS Message -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-sms-message" class="form-label">SMS / Whatsapp Message</label>
                                <textarea name="sms_message" id="edit-sms-message" class="form-control" cols="30" rows="6"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Arabic Message -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-msg-ar" class="form-label">Message (Arabic)</label>
                                <textarea name="msg_sms_ar" id="edit-msg-ar" class="form-control" cols="30" rows="6"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- URL Type -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">SMS URL Type</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="sms_url_type" value="0" id="edit-fileUpload">
                                    <label class="form-check-label" for="edit-fileUpload">File Upload</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="sms_url_type" value="1" id="edit-fileURL">
                                    <label class="form-check-label" for="edit-fileURL">File URL</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Static URL -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-static-url-field" class="form-label">Media / Static URL</label>
                                <input type="text" name="static_url" id="edit-static-url-field" class="form-control"
                                    placeholder="Enter URL">
                            </div>
                        </div>
                    </div>

                    <!-- Document Name -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-document-name" class="form-label">Document Name</label>
                                <input type="text" name="document_name" id="edit-document-name" class="form-control"
                                    placeholder="Enter Document Name">
                            </div>
                        </div>
                    </div>

                    <!-- Public -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" name="public" type="checkbox" id="edit-public" value="1" />
                                <label class="form-check-label" for="edit-public">Public</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit SMS Template End --->

        <!-- Delete SMS Template Start -->
        <div class="modal fade" id="deleteSmsTemplate" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.sms.templateDelete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="delete_id" id="delete_id">

                    <div class="modal-body">
                    <p>Are you sure you want to delete this template?</p>
                    </div>

                    <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger btn-sm" id="disbtn">Delete</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- Delete SMS Template End -->

        <!--- Activate SMS Template Start --->
        <div class="modal fade" id="activeStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Active Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.sms.activeStatus') }}" method="POST">
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
         <!--- Activate SMS Template End --->

        <!--- De-activate SMS Template Start --->
        <div class="modal fade" id="deactiveStatus" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Deactive Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.sms.deactiveStatus') }}" method="POST">
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
        <!-- De-activate SMS Template end -->

        <!--Publish Status Modal Start -->
        <div class="modal fade" id="publishSts" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Public Template</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.sms.templatePublicUpdate') }}" method="POST">
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
                    <form action="{{ route('admin.sms.templateStatusUpdate') }}" method="POST">
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
    <script src="{{ asset('admin/assets/pages/app-sms-template-list.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/validation/sms-template-validation.js') }}"></script>



    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    <script src="{{ asset('admin/assets/js/forms-file-upload.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#editSmsTemplate').on('show.bs.offcanvas', function (e) {
                var editID = $(e.relatedTarget).data('id');
                $('#edit_id').val(editID);

                var imgPath = "{{ asset('admin/assets/images/template') }}";
                var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route("admin.sms.templateEdit") }}',
                    method: 'GET',
                    data: {
                        id: editID,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        // ✅ Basic info
                        $('#edit-template-for').val(data.template_for).trigger('change');
                        $('#edit-template-used-for').val(data.template_used_for).trigger('change');
                        $('#edit-api-name').val(data.sms_api_id).trigger('change');
                        $('#edit-template-name').val(data.template_name);
                        $('#edit-template-type').val(data.template_type).trigger('change');
                        $('#edit-careoff').val(data.careoff_id).trigger('change');
                        $('#edit-sms-message').val(data.sms_message);
                        $('#edit-msg-ar').val(data.msg_sms_ar);
                        $('#edit-static-url-field').val(data.static_url);
                        $('#edit-document-name').val(data.document_name);

                        // ✅ Public checkbox
                        if (data.public == 1) {
                            $('#edit-public').prop('checked', true);
                        } else {
                            $('#edit-public').prop('checked', false);
                        }

                        // ✅ Display File preview
                        if (data.sms_url_type == '0') {
                            $('#edit-fileUpload').prop('checked', true);
                            if (data.sms_file && data.sms_file !== '') {
                                $('#uploadedAvatarEdit').attr('src', imgPath + '/' + data.sms_file);
                            } else {
                                $('#uploadedAvatarEdit').attr('src', blankImg);
                            }
                        } else if (data.sms_url_type == '1') {
                            $('#edit-fileURL').prop('checked', true);
                            $('#uploadedAvatarEdit').attr('src', blankImg);
                        }

                        // ✅ Show/hide document name section
                        if (data.document_name) {
                            $('#edit-document-name').closest('.row').show();
                        } else {
                            $('#edit-document-name').closest('.row').hide();
                        }

                        // ✅ Handle dynamic field & assign variables
                        let fieldVars = data.sms_field_var ? data.sms_field_var.split(',') : [];
                        let assignVars = data.sms_assign_ar ? data.sms_assign_ar.split(',') : [];

                        let dynamicHTML = '';
                        for (let i = 0; i < fieldVars.length; i++) {
                            let fieldVal = fieldVars[i] || '';
                            let assignVal = assignVars[i] || '';

                            dynamicHTML += `
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Field Variable</label>
                                            <input type="text" class="form-control" name="field_variable[]" value="${fieldVal}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Assign Variable</label>
                                            <input type="text" class="form-control" name="assign_variable[]" value="${assignVal}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-sm btn-danger remove2 float-end">Remove</button>
                                    </div>
                                </div>`;
                        }

                        if (dynamicHTML === '') {
                            dynamicHTML = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Field Variable</label>
                                            <input type="text" class="form-control" name="field_variable[]" placeholder="Enter Field Variable">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Assign Variable</label>
                                            <input type="text" class="form-control" name="assign_variable[]" placeholder="Enter Assign Variable">
                                        </div>
                                    </div>
                                </div>`;
                        }

                        // ✅ Display in your dynamic section (add this div in edit modal if missing)
                        $('#responseValueContact').html(dynamicHTML);
                    },
                    error: function (xhr) {
                        console.error('Error fetching SMS Template data:', xhr.responseText);
                        alert('Something went wrong while fetching the template data.');
                    }
                });
            });

            // Remove dynamic variable row
            $(document).on('click', '.remove2', function () {
                $(this).closest('.row').remove();
            });
        });
    </script>

    <script>
        $(document).on('click','.removeUrl',function(){
            $('input[name="meta_url_type"]').prop('checked',false);
            $('#dispAddMetaURLType').hide();
            $(this).closest('.col-md-12').hide();
        });
        $(document).on('click','.removeUrlfile',function(){
            $('input[name="meta_url_type"]').prop('checked',false);
            $('#dispAddMetaURLType').hide();
            $(this).closest('.row').hide();
        });

        $(document).on('click','.removeDocumentName', function(){
            $('#dispAddDocumentNameForAll').hide();
            $(this).closest('.row').hide();
        });

        $(document).on('click','.removeUrlEdit',function(){
            $('input[name="meta_url_type"]').prop('checked',false);
            $('#dispEditMetaURLType').hide();
            $(this).closest('.col-md-12').hide();
        });
        $(document).on('click','.removeUrlfileEdit',function(){
            $('input[name="meta_url_type"]').prop('checked',false);
            $('#dispEditMetaURLType').hide();
            $(this).closest('.row').hide();
        });

        $(document).on('click','.removeedDocumentName', function(){
            $('#dispEditDocumentNameForAll').hide();
            $(this).closest('.row').hide();
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
            $('#deleteSmsTemplate').on('show.bs.modal',function(e){
                var deleteId = $(e.relatedTarget).data('id');
                $('#delete_id').val(deleteId);
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
                    url : '{{ route("admin.sms.templatePublic") }}',
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
                    url : '{{ route("admin.sms.templateStatus") }}',
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
                    $('#dispAddFileForALL').hide();
                    $('.disviewallcontact').hide();
                    $('.disviewtodo').hide();
                    $('.dispviewEmployer').hide();
                    $('.disviewleadsEmployer').hide();
                    $('.disviewleadsCandidate').hide();
                }else if(templatefor == 'allcontact'){
                    $('.disviewcontactp').hide();
                    $('#dispAddFileForALL').hide();
                    $('.disviewallcontact').show();
                    $('.disviewtodo').hide();
                    $('.dispviewEmployer').hide();
                    $('.disviewleadsEmployer').hide();
                    $('.disviewleadsCandidate').hide();

                }else if (templatefor == 'todo') {
                    $('.disviewtodo').show();
                    $('.disviewcontactp').hide();
                    $('#dispAddFileForALL').hide();
                    $('.disviewallcontact').hide();
                    $('.dispviewEmployer').hide();
                    $('.disviewleadsEmployer').hide();
                    $('.disviewleadsCandidate').hide();

                }else if (templatefor == 'employer') {
                    $('.dispviewEmployer').show();
                    $('.disviewtodo').hide();
                    $('.disviewcontactp').hide();
                    $('#dispAddFileForALL').hide();
                    $('.disviewallcontact').hide();
                    $('.disviewleadsEmployer').hide();
                    $('.disviewleadsCandidate').hide();
                    }else if (templatefor == 'leads_employer') {
                    $('.dispviewEmployer').hide();
                    $('.disviewtodo').hide();
                    $('.disviewcontactp').hide();
                    $('#dispAddFileForALL').hide();
                    $('.disviewallcontact').hide();
                    $('.disviewleadsEmployer').show();
                    $('.disviewleadsCandidate').hide();
                }else if (templatefor == 'leads_candidate') {
                    $('.dispviewEmployer').hide();
                    $('.disviewtodo').hide();
                    $('.disviewcontactp').hide();
                    $('#dispAddFileForALL').hide();
                    $('.disviewallcontact').hide();
                    $('.disviewleadsEmployer').hide();
                    $('.disviewleadsCandidate').show();
                }else {
                    $('.disviewcontactp').hide();
                    $('#dispAddMetaURLType').hide();
                    $('#dispAddFileMeta').hide();
                    $('#dispAddothersTextfield').hide();
                    $('#dispAddFileForALL').show();
                    $('.disviewallcontact').hide();
                    $('.disviewtodo').hide();
                    $('.dispviewEmployer').hide();
                    $('.disviewleadsEmployer').hide();
                    $('.disviewleadsCandidate').hide();

                }

            });

            $("#edit-template-for").on('change',function(){
                var templatefore = $(this).val();

                if (templatefore == 'contactp') {
                    $(".disviewcontactpe").show();
                    $('#dispEditFileForALL').hide();
                    $('.disviewallcontacte').hide();
                    $('.dispviewEmployere').hide();
                    $('.disviewaleadsEmployere').hide();
                    $('.disviewaleadsCandidatee').hide();
                }else if (templatefore == 'allcontact') {
                    $(".disviewcontactpe").hide();
                    $('#dispEditFileForALL').hide();
                    $('.disviewallcontacte').show();
                    $('.dispviewEmployere').hide();
                    $('.disviewaleadsEmployere').hide();
                    $('.disviewaleadsCandidatee').hide();
                }else if (templatefore == 'employer') {
                    $('.dispviewEmployere').show();
                    $(".disviewcontactpe").hide();
                    $('#dispEditFileForALL').hide();
                    $('.disviewallcontacte').hide();
                    $('.disviewaleadsEmployere').hide();
                    $('.disviewaleadsCandidatee').hide();
                }
                else if (templatefore == 'leads_employer') {
                    $('.dispviewEmployere').hide();
                    $(".disviewcontactpe").hide();
                    $('#dispEditFileForALL').hide();
                    $('.disviewallcontacte').hide();
                    $('.disviewaleadsEmployere').show();
                    $('.disviewaleadsCandidatee').hide();
                }else if (templatefore == 'leads_candidate') {
                    $('.dispviewEmployere').hide();
                    $(".disviewcontactpe").hide();
                    $('#dispEditFileForALL').hide();
                    $('.disviewallcontacte').hide();
                    $('.disviewaleadsEmployere').hide();
                    $('.disviewaleadsCandidatee').show();
                }else {
                    $(".disviewcontactpe").hide();
                    $('#dispEditMetaURLType').hide();
                    $('#dispEditFileMeta').hide();
                    $('#dispEditothersTextfield').hide();
                    $('.disviewallcontacte').hide();
                    $('#dispEditFileForALL').show();
                    $('.dispviewEmployere').hide();
                    $('.disviewaleadsEmployere').hide();
                    $('.disviewaleadsCandidatee').hide();
                }


            });
        });
    </script>

    {{-- Contact Plus Start --}}
    <script>
        var rowMin4 = 0;
        var rowMax4 = 10;

        $(document).on('click','.addContactpDisp',function(){
            var html = "";
            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-contactp-variable'+rowMin4+'">Meta Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_contactp[]" id="add-field-contactp-variable'+rowMin4+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_contactp'+rowMin4+'">Contact Plus Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_contactp[]" id="assign_var_contactp'+rowMin4+'" class="form-control contactpShowHide assignVariableContactPOPT select2a"><option value="">Select</option><option value="[Others]">Image,Video,Document(Media Upload)</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Link</option><option value="[Document Name]">Document Name</option><option value="[Office Name (English)]">Office Name (English)</option><option value="[Office Name (Arabic)]">Office Name (Arabic)</option><option value="[Office Number]">Office Number</option><option value="[Office Email]">Office Email</option><option value="[Owner Name]">Owner Name</option><option value="[Owner Contact]">Owner Contact</option><option value="[Owner Email]">Owner Email</option><option value="[Country]">Country</option><option value="[City]">City</option><option value="[Primary Concern Person]">Primary Concern Person</option><option value="[Primary Contact No]">Primary Contact No</option><option value="[Primary Email]">Primary Email</option><option value="[Secondary Concern Person]">Secondary Concern Person</option><option value="[Secondary Contact No]">Secondary Contact No</option><option value="[Secondary Email]">Secondary Email</option><option value="[Concern Person 3]">Concern Person 3</option><option value="[Contact No 3]">Contact No 3</option><option value="[Concern Person 4]">Concern Person 4</option><option value="[Contact No 4]">Contact No 4</option><option value="[Concern Person 5]">Concern Person 5</option><option value="[Contact No 5]">Contact No 5</option><option value="[Concer Person 6]">Concer Person 6</option><option value="[Contact No 6]">Contact No 6</option><option value="[Status]">Work Status</option>';
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

            let closestvalue = $(this).closest('.row').find('.contactpShowHide').val();

            if (closestvalue == '[Others]') {
                $('input[name="meta_url_type"]').prop('checked',false);
                $('#dispAddMetaURLType').hide();

                $('#dispAddothersTextfield').hide();
                $('#dispAddFileMeta').hide();
            }


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

    {{-- Contact Plus End --}}

    {{-- Employer Start --}}
    <script>
        var rowMinEmployer = 0;
        var rowMaxEmployer = 10;

        $(document).on('click','.addEmployerDisp', function(){
            var html = "";

            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-employer-variable'+rowMinEmployer+'">Meta Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_employer[]" id="add-field-employer-variable'+rowMinEmployer+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_employer'+rowMinEmployer+'">Employer Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_employer[]" id="assign_var_employer'+rowMinEmployer+'" class="form-control employerShowHide assignVariableContactPOPT select2a"><option value="">Select</option><option value="[Others]">Image,Video,Document(Media Upload)</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Link</option><option value="[Document Name]">Document Name</option><option value="[Full Name]">Full Name</option><option value="[Company]">Company</option><option value="[Email]">Email</option><option value="[Country]">Country</option><option value="[City]">City</option><option value="[Mobile No]">Mobile No</option><option value="[Membership]">Membership</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';


            $('#dispEmployer').append(html);
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

            rowMinEmployer++;
            rowMaxEmployer--;

            if (rowMaxEmployer == 1) {
                $('.addEmployerDisp').prop('disabled',true);
            }else{
                $('.addEmployerDisp').prop('disabled',false);
            }
        });

        $(document).on('click','.remove',function(){

            let closestvalue = $(this).closest('.row').find('.employerShowHide').val();

            if (closestvalue == '[Others]') {
                $('input[name="meta_url_type"]').prop('checked',false);
                $('#dispAddMetaURLType').hide();

                $('#dispAddothersTextfield').hide();
                $('#dispAddFileMeta').hide();
            }


            $(this).closest('.row').remove();
            rowMaxEmployer++;
            rowMinEmployer--;
            if (rowMaxEmployer == 0) {
                $('.addEmployerDisp').prop('disabled',true);
            }else{
                $('.addEmployerDisp').prop('disabled',false);
            }
        });
    </script>

    <script>
        var rowMinEmployerE = 0;
        var rowMaxEmployerE = 10;

        $(document).on('click','.addEmployerDispe', function(){
            var html = "";

            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="edit-field-employer-variable'+rowMinEmployerE+'">Meta Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_employer[]" id="edit-field-employer-variable'+rowMinEmployerE+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="edit-assign_var_employer'+rowMinEmployerE+'">Employer Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_employer[]" id="edit-assign_var_employer'+rowMinEmployerE+'" class="form-control employerShowHideEdit assignVariableContactPOPT select2a"><option value="">Select</option><option value="[Others]">Image,Video,Document(Media Upload)</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Link</option><option value="[Document Name]">Document Name</option><option value="[Full Name]">Full Name</option><option value="[Company]">Company</option><option value="[Email]">Email</option><option value="[Country]">Country</option><option value="[City]">City</option><option value="[Mobile No]">Mobile No</option><option value="[Membership]">Membership</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';


            $('#dispEmployere').append(html);
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

            rowMinEmployerE++;
            rowMaxEmployerE--;

            if (rowMaxEmployerE == 1) {
                $('.addEmployerDispe').prop('disabled',true);
            }else{
                $('.addEmployerDispe').prop('disabled',false);
            }
        });

        $(document).on('click','.remove',function(){

            let closestvalue = $(this).closest('.row').find('.employerShowHideEdit').val();

            if (closestvalue == '[Others]') {
                $('input[name="meta_url_type"]').prop('checked',false);
                $('#dispEditMetaURLType').hide();

                $('#dispEditothersTextfield').hide();
                $('#dispEditFileMeta').hide();
            }


            $(this).closest('.row').remove();
            rowMaxEmployerE++;
            rowMinEmployerE--;
            if (rowMaxEmployerE == 0) {
                $('.addEmployerDispe').prop('disabled',true);
            }else{
                $('.addEmployerDispe').prop('disabled',false);
            }
        });
    </script>

    {{-- Employer End --}}

    {{-- Leads Employer Start --}}
    <script>
        var rowMinLeadsEmployer = 0;
        var rowMaxLeadsEmployer = 10;

        $(document).on('click', '.addLeadsEmployerDisp', function(){
            var html = "";

            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-leads-employer-variable'+rowMinLeadsEmployer+'">Meta Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_leads_employer[]" id="add-field-leads-employer-variable'+rowMinLeadsEmployer+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_leads_employer'+rowMinLeadsEmployer+'">Leads Employer Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_leads_employer[]" id="assign_var_leads_employer'+rowMinLeadsEmployer+'" class="form-control leadsEmployerShowHide assignVariableContactPOPT select2a"><option value="">Select</option><option value="[Others]">Image,Video,Document(Media Upload)</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Link</option><option value="[Document Name]">Document Name</option><option value="[Company]">Company</option><option value="[Full Name]">Full Name</option><option value="[Email]">Email</option><option value="[Country]">Country</option><option value="[Mobile No]">Mobile No</option><option value="[Whatsapp No]">Whatsapp No</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';

            $('#dispLeadsEmployer').append(html);

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

            rowMinLeadsEmployer++;
            rowMaxLeadsEmployer--;
            if (rowMaxLeadsEmployer == 1) {
                $('.addLeadsEmployerDisp').prop('disabled',true);
            }else{
                $('.addLeadsEmployerDisp').prop('disabled',false);
            }

        });

        $(document).on('click','.remove',function(){
            let closestvalue = $(this).closest('.row').find('.leadsEmployerShowHide').val();

            if (closestvalue == '[Others]') {
                $('input[name="meta_url_type"]').prop('checked',false);
                $('#dispAddMetaURLType').hide();

                $('#dispAddothersTextfield').hide();
                $('#dispAddFileMeta').hide();
            }


            $(this).closest('.row').remove();
            rowMaxLeadsEmployer++;
            rowMinLeadsEmployer--;
            if (rowMaxLeadsEmployer == 0) {
                $('.addLeadsEmployerDisp').prop('disabled',true);
            }else{
                $('.addLeadsEmployerDisp').prop('disabled',false);
            }
        });
    </script>
    {{-- Leads Employer End --}}

    {{-- Leads Candidate Start --}}
    <script>
        var rowMinLeadsCandidate = 0;
        var rowMaxLeadsCandidate = 10;

        $(document).on('click', '.addLeadsCandidateDisp', function(){
            var html = "";

            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-leads-candidate-variable'+rowMinLeadsCandidate+'">Meta Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_leads_candidate[]" id="add-field-leads-candidate-variable'+rowMinLeadsCandidate+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_leads_candidate'+rowMinLeadsCandidate+'">Leads Candidate Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_leads_candidate[]" id="assign_var_leads_candidate'+rowMinLeadsCandidate+'" class="form-control leadsCandidateShowHide assignVariableContactPOPT select2a"><option value="">Select</option><option value="[Others]">Image,Video,Document(Media Upload)</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Link</option><option value="[Document Name]">Document Name</option><option value="[Company]">Company</option><option value="[Full Name]">Full Name</option><option value="[Email]">Email</option><option value="[Country]">Country</option><option value="[Mobile No]">Mobile No</option><option value="[Whatsapp No]">Whatsapp No</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';

            $('#dispLeadsCandidate').append(html);

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

            rowMinLeadsCandidate++;
            rowMaxLeadsCandidate--;
            if (rowMaxLeadsCandidate == 1) {
                $('.addLeadsCandidateDisp').prop('disabled',true);
            }else{
                $('.addLeadsCandidateDisp').prop('disabled',false);
            }

        });

        $(document).on('click','.remove',function(){
            let closestvalue = $(this).closest('.row').find('.leadsCandidateShowHide').val();

            if (closestvalue == '[Others]') {
                $('input[name="meta_url_type"]').prop('checked',false);
                $('#dispAddMetaURLType').hide();

                $('#dispAddothersTextfield').hide();
                $('#dispAddFileMeta').hide();
            }


            $(this).closest('.row').remove();
            rowMaxLeadsCandidate++;
            rowMinLeadsCandidate--;
            if (rowMaxLeadsCandidate == 0) {
                $('.addLeadsCandidateDisp').prop('disabled',true);
            }else{
                $('.addLeadsCandidateDisp').prop('disabled',false);
            }
        });
    </script>
    {{-- Leads Candidate End --}}



    {{-- Allcontact Start --}}


    <script>
        var rowMinall4 = 0;
        var rowMaxall4 = 10;

        $(document).on('click','.addAllContactDisp',function(){
            var html = "";
            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-allcontact-variable'+rowMinall4+'">Meta Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_allcontact[]" id="add-field-allcontact-variable'+rowMinall4+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_allcontact'+rowMinall4+'">Allcontact Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_allcontact[]" id="assign_var_allcontact'+rowMinall4+'" class="form-control allcontactShowHide assignVariableContactPOPT select2a"><option value="">Select</option><option value="[Others]">Image,Video,Document(Media Upload)</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Link</option><option value="[Document Name]">Document Name</option><option value="[Careoff]">Careoff</option><option value="[Careoff Contact No.1]">Careoff Contact No.1</option><option value="[Careoff Contact No.2]">Careoff Contact No.2</option><option value="[Careoff Call Marketing]">Careoff Call Marketing</option><option value="[Company]">Company</option><option value="[Business Type]">Business Type</option><option value="[Full Name]">Full Name</option><option value="[Email]">Email</option><option value="[Country]">Country</option><option value="[City]">City</option><option value="[Phone0]">Phone0</option><option value="[Email0]">Email0</option><option value="[Phone1]">Phone1</option><option value="[Email1]">Email1</option><option value="[Phone2]">Phone2</option><option value="[Email2]">Email2</option><option value="[Membership]">Membership</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';



            $('#dispAllContact').append(html);
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

            rowMinall4++;
            rowMaxall4--;
            if (rowMaxall4 == 1) {
                $('.addAllContactDisp').prop('disabled',true);
            }else{
                $('.addAllContactDisp').prop('disabled',false);
            }
        });
        $(document).on('click','.remove',function(){

            let closestvalue = $(this).closest('.row').find('.allcontactShowHide').val();

            if (closestvalue == '[Others]') {
                $('input[name="meta_url_type"]').prop('checked',false);
                $('#dispAddMetaURLType').hide();

                $('#dispAddothersTextfield').hide();
                $('#dispAddFileMeta').hide();
            }


            $(this).closest('.row').remove();
            rowMaxall4++;
            rowMinall4--;
            if (rowMaxall4 == 0) {
                $('.addAllContactDisp').prop('disabled',true);
            }else{
                $('.addAllContactDisp').prop('disabled',false);
            }
        });

    </script>

    {{-- Allcontact End --}}


    <script>
        // Add Template
        $(document).on('change','.contactpShowHide',function(){
            var selectedVale = $(this).val();
            if (selectedVale == '[Others]') {
                $('#dispAddMetaURLType').show();
            }

            if (selectedVale == '[Document Name]') {
                $('#dispAddDocumentNameForAll').show();
            }
        });

        $(document).on('change','.allcontactShowHide',function(){
            var selectedallcontactvalue = $(this).val();
            if (selectedallcontactvalue == '[Others]') {
                $('#dispAddMetaURLType').show();
            }

            if (selectedallcontactvalue == '[Document Name]') {
                $('#dispAddDocumentNameForAll').show();
            }

        });

        $(document).on('change','.employerShowHide', function(){
            var selectedallcontactvalue = $(this).val();
            if (selectedallcontactvalue == '[Others]') {
                $('#dispAddMetaURLType').show();
            }

            if (selectedallcontactvalue == '[Document Name]') {
                $('#dispAddDocumentNameForAll').show();
            }
        });

        $(document).on('change','.leadsEmployerShowHide', function(){
            var selectedallcontactvalue = $(this).val();
            if (selectedallcontactvalue == '[Others]') {
                $('#dispAddMetaURLType').show();
            }

            if (selectedallcontactvalue == '[Document Name]') {
                $('#dispAddDocumentNameForAll').show();
            }
        });

        $(document).on('change','.leadsCandidateShowHide', function(){
            var selectedallcontactvalue = $(this).val();
            if (selectedallcontactvalue == '[Others]') {
                $('#dispAddMetaURLType').show();
            }

            if (selectedallcontactvalue == '[Document Name]') {
                $('#dispAddDocumentNameForAll').show();
            }
        });


        $(document).on('change','.changeMetaRadio',function(){
            var metaRadioValue = $(this).val();
            if (metaRadioValue == 0) {
                $('#dispAddFileMeta').show();
                $('#dispAddothersTextfield').hide();
            }

            if (metaRadioValue == 1) {
                $('#dispAddFileMeta').hide();
                $('#dispAddothersTextfield').show();
            }
        });

        // Edit Template

        $(document).on('change','.contactpShowHideEdit',function(){
            var selectedVale = $(this).val();

            if (selectedVale == '[Others]') {
                $('#dispEditMetaURLType').show();
            }

            if (selectedVale == '[Document Name]') {
                $('#dispEditDocumentNameForAll').show();
            }

        });

        $(document).on('change','.allcontactShowHideEdit',function(){
            var selectedallcontactvalue = $(this).val();
            if (selectedallcontactvalue == '[Others]') {
                $('#dispEditMetaURLType').show();
            }

            if (selectedallcontactvalue == '[Document Name]') {
                $('#dispEditDocumentNameForAll').show();
            }

        });

        $(document).on('change','.leadsEmployerShowHideEdit',function(){
            var selectedallcontactvalue = $(this).val();
            if (selectedallcontactvalue == '[Others]') {
                $('#dispEditMetaURLType').show();
            }

            if (selectedallcontactvalue == '[Document Name]') {
                $('#dispEditDocumentNameForAll').show();
            }

        });

        $(document).on('change','.leadsCandidateShowHideEdit',function(){
            var selectedallcontactvalue = $(this).val();
            if (selectedallcontactvalue == '[Others]') {
                $('#dispEditMetaURLType').show();
            }

            if (selectedallcontactvalue == '[Document Name]') {
                $('#dispEditDocumentNameForAll').show();
            }

        });

        $(document).on('change','.employerShowHideEdit', function(){
            var selectedallcontactvalue = $(this).val();
            if (selectedallcontactvalue == '[Others]') {
                $('#dispEditMetaURLType').show();
            }

            if (selectedallcontactvalue == '[Document Name]') {
                $('#dispEditDocumentNameForAll').show();
            }
        });

        $(document).on('change','.changeMetaRadioEd',function(){
            var metaRadioValue = $(this).val();
            if (metaRadioValue == 0) {
                $('#dispEditFileMeta').show();
                $('#dispEditothersTextfield').hide();
            }

            if (metaRadioValue == 1) {
                $('#dispEditFileMeta').hide();
                $('#dispEditothersTextfield').show();
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
            html += '<select name="field_var_contactp[]" id="add-field-contactp-variable'+rowMin5+'" class="form-control fieldVariable select2ae"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_contactp'+rowMin5+'">Contact Plus Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_contactp[]" id="assign_var_contactp'+rowMin5+'" class="form-control contactpShowHideEdit assignVariableContactPOPT select2ae"><option value="">Select</option><option value="[Others]">Image,Video,Document(Media Upload)</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Link</option><option value="[Document Name]">Document Name</option><option value="[Office Name (English)]">Office Name (English)</option><option value="[Office Name (Arabic)]">Office Name (Arabic)</option><option value="[Office Number]">Office Number</option><option value="[Office Email]">Office Email</option><option value="[Owner Name]">Owner Name</option><option value="[Owner Contact]">Owner Contact</option><option value="[Owner Email]">Owner Email</option><option value="[Country]">Country</option><option value="[City]">City</option><option value="[Primary Concern Person]">Primary Concern Person</option><option value="[Primary Contact No]">Primary Contact No</option><option value="[Primary Email]">Primary Email</option><option value="[Secondary Concern Person]">Secondary Concern Person</option><option value="[Secondary Contact No]">Secondary Contact No</option><option value="[Secondary Email]">Secondary Email</option><option value="[Concern Person 3]">Concern Person 3</option><option value="[Contact No 3]">Contact No 3</option><option value="[Concern Person 4]">Concern Person 4</option><option value="[Contact No 4]">Contact No 4</option><option value="[Concern Person 5]">Concern Person 5</option><option value="[Contact No 5]">Contact No 5</option><option value="[Concer Person 6]">Concer Person 6</option><option value="[Contact No 6]">Contact No 6</option><option value="[Status]">Work Status</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove2 float-end mb-2 mt-2">Remove</button></div></div>';



            $('#dispContactpe').append(html);

            var select2ae = $('.select2ae');
            if (select2ae.length) {
                select2ae.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        // placeholder: 'Select value',
                        dropdownParent: $this.parent()
                    });
                });
            }

            rowMin5++;
            rowMax5--;
            if (rowMax5 == 1) {
                $('.addContactpDispe').prop('disabled',true);
            }else{
                $('.addContactpDispe').prop('disabled',false);
            }
        });
        $(document).on('click','.remove2',function(){
            let closestvalue = $(this).closest('.row').find('.contactpShowHideEdit').val();

            if (closestvalue == '[Others]') {
                $('input[name="meta_url_type"]').prop('checked',false);
                $('#dispEditMetaURLType').hide();

                $('#dispEditothersTextfield').hide();
                $('#dispEditFileMeta').hide();
            }

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

    {{-- Leads Employer Start --}}
    <script>
        var rowMinLeadsEmployere = 0;
        var rowMaxLeadsEmployere = 10;

        $(document).on('click','.addLeadsEmployerDispe',function(){
            var html = "";
            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-leads-employer-variable'+rowMinLeadsEmployere+'">Meta Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_leads_employer[]" id="add-field-leads-employer-variable'+rowMinLeadsEmployere+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_leads_employer'+rowMinLeadsEmployere+'">Allcontact Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_leads_employer[]" id="assign_var_leads_employer'+rowMinLeadsEmployere+'" class="form-control leadsEmployerShowHideEdit assignVariableContactPOPT select2a"><option value="">Select</option><option value="[Others]">Image,Video,Document(Media Upload)</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Link</option><option value="[Document Name]">Document Name</option><option value="[Company]">Company</option><option value="[Full Name]">Full Name</option><option value="[Email]">Email</option><option value="[Country]">Country</option><option value="[Mobile No]">Mobile No</option><option value="[Whatsapp No]">Whatsapp No</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';



            $('#dispLeadsEmployere').append(html);
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

            rowMinLeadsEmployere++;
            rowMaxLeadsEmployere--;
            if (rowMaxLeadsEmployere == 1) {
                $('.addLeadsEmployerDispe').prop('disabled',true);
            }else{
                $('.addLeadsEmployerDispe').prop('disabled',false);
            }
        });
        $(document).on('click','.remove',function(){

            let closestvalue = $(this).closest('.row').find('.leadsEmployerShowHideEdit').val();

            if (closestvalue == '[Others]') {
                $('input[name="meta_url_type"]').prop('checked',false);
                $('#dispAddMetaURLType').hide();

                $('#dispAddothersTextfield').hide();
                $('#dispAddFileMeta').hide();
            }


            $(this).closest('.row').remove();
            rowMaxLeadsEmployere++;
            rowMinLeadsEmployere--;
            if (rowMaxLeadsEmployere == 0) {
                $('.addLeadsEmployerDispe').prop('disabled',true);
            }else{
                $('.addLeadsEmployerDispe').prop('disabled',false);
            }
        });

    </script>

    {{-- Leads Employer End --}}

    {{-- Leads Candidate Start --}}
    <script>
        var rowMinLeadsCandidatee = 0;
        var rowMaxLeadsCandidatee = 10;

        $(document).on('click','.addLeadsCandidateDispe',function(){
            var html = "";
            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-leads-candidate-variable'+rowMinLeadsCandidatee+'">Meta Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_leads_candidate[]" id="add-field-leads-candidate-variable'+rowMinLeadsCandidatee+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_leads_candidate'+rowMinLeadsCandidatee+'">Allcontact Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_leads_candidate[]" id="candidate'+rowMinLeadsCandidatee+'" class="form-control leadsCandidateShowHideEdit assignVariableContactPOPT select2a"><option value="">Select</option><option value="[Others]">Image,Video,Document(Media Upload)</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Link</option><option value="[Document Name]">Document Name</option><option value="[Company]">Company</option><option value="[Full Name]">Full Name</option><option value="[Email]">Email</option><option value="[Country]">Country</option><option value="[Mobile No]">Mobile No</option><option value="[Whatsapp No]">Whatsapp No</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';



            $('#dispLeadsCandidatee').append(html);
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

            rowMinLeadsCandidatee++;
            rowMaxLeadsCandidatee--;
            if (rowMaxLeadsCandidatee == 1) {
                $('.addLeadsCandidateDispe').prop('disabled',true);
            }else{
                $('.addLeadsCandidateDispe').prop('disabled',false);
            }
        });
        $(document).on('click','.remove',function(){

            let closestvalue = $(this).closest('.row').find('.leadsCandidateShowHideEdit').val();

            if (closestvalue == '[Others]') {
                $('input[name="meta_url_type"]').prop('checked',false);
                $('#dispAddMetaURLType').hide();

                $('#dispAddothersTextfield').hide();
                $('#dispAddFileMeta').hide();
            }


            $(this).closest('.row').remove();
            rowMaxLeadsCandidatee++;
            rowMinLeadsCandidatee--;
            if (rowMaxLeadsCandidatee == 0) {
                $('.addLeadsCandidateDispe').prop('disabled',true);
            }else{
                $('.addLeadsCandidateDispe').prop('disabled',false);
            }
        });

    </script>

    {{-- Leads Candidate End --}}


    {{-- Allcontact Start --}}
    <script>
        var rowMinalle4 = 0;
        var rowMaxalle4 = 10;

        $(document).on('click','.addAllContactDispe',function(){
            var html = "";
            html += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html += '<label class="" for="add-field-allcontact-variable'+rowMinalle4+'">Meta Variable <span class="text-danger">*</span></label>';
            html += '<select name="field_var_allcontact[]" id="add-field-allcontact-variable'+rowMinalle4+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="form-group"><label class="" for="assign_var_allcontact'+rowMinalle4+'">Allcontact Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_allcontact[]" id="assign_var_allcontact'+rowMinalle4+'" class="form-control allcontactShowHideEdit assignVariableContactPOPT select2a"><option value="">Select</option><option value="[Others]">Image,Video,Document(Media Upload)</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Link</option><option value="[Document Name]">Document Name</option><option value="[Careoff]">Careoff</option><option value="[Careoff Contact No.1]">Careoff Contact No.1</option><option value="[Careoff Contact No.2]">Careoff Contact No.2</option><option value="[Careoff Call Marketing]">Careoff Call Marketing</option><option value="[Company]">Company</option><option value="[Business Type]">Business Type</option><option value="[Full Name]">Full Name</option><option value="[Email]">Email</option><option value="[Country]">Country</option><option value="[City]">City</option><option value="[Phone0]">Phone0</option><option value="[Email0]">Email0</option><option value="[Phone1]">Phone1</option><option value="[Email1]">Email1</option><option value="[Phone2]">Phone2</option><option value="[Email2]">Email2</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';



            $('#dispAllContacte').append(html);
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

            rowMinalle4++;
            rowMaxalle4--;
            if (rowMaxalle4 == 1) {
                $('.addAllContactDispe').prop('disabled',true);
            }else{
                $('.addAllContactDispe').prop('disabled',false);
            }
        });
        $(document).on('click','.remove',function(){

            let closestvalue = $(this).closest('.row').find('.allcontactShowHideEdit').val();

            if (closestvalue == '[Others]') {
                $('input[name="meta_url_type"]').prop('checked',false);
                $('#dispAddMetaURLType').hide();

                $('#dispAddothersTextfield').hide();
                $('#dispAddFileMeta').hide();
            }


            $(this).closest('.row').remove();
            rowMaxalle4++;
            rowMinalle4--;
            if (rowMaxalle4 == 0) {
                $('.addAllContactDispe').prop('disabled',true);
            }else{
                $('.addAllContactDispe').prop('disabled',false);
            }
        });

    </script>

    {{-- Allcontact End --}}

@endsection












