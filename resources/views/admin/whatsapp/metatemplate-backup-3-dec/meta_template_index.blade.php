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
        @if (isset($perm) && $perm->add_meta_whatsapp_template == 0)
        <style>
            .addtemplate{
                display: none !important;
            }
        </style>
        @endif
        @if (isset($perm) && $perm->edit_meta_whatsapp_template == 0)
        <style>
        .edtemplate{
            display: none !important;
        }
        </style>
        @endif
        @if (isset($perm) && $perm->delete_meta_whatsapp_template == 0)
        <style>
        .deltemplate{
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

        @if (isset($perm) && $perm->status_meta_whatsapp_template == 0)
            <script>
                var disStatusTemp = '0';
            </script>
        @else
            <script>
                var disStatusTemp = '1';
            </script>
        @endif

        @if (isset($perm) && $perm->public_meta_whatsapp_template == 0)
            <script>
                var disPublicTemp = '0';
            </script>
        @else
            <script>
                var disPublicTemp = '1';
            </script>
        @endif

    @endif


@endsection



@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- header box Start -->
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
        <!-- header box End -->

        <!-- Meta Template List Table Start -->
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
                            <th>Meta Name</th>
                            <th>Audience</th>
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
        <!-- Meta Template List Table End -->

        <!-- ADD TEMPLATE OFFCANVAS -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end"
            tabindex="-1"
            id="offcanvasAddUser"
            aria-labelledby="offcanvasAddUserLabel"
            data-bs-backdrop="static"
            data-bs-keyboard="false">

            <!-- Header -->
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Template</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
            </div>

            <!-- Body -->
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">

                <!-- Add Template Form -->
                <form id="addNewUserForm"
                    action="{{ route('admin.whatsapp.metatemplateStore') }}"
                    method="POST"
                    enctype="multipart/form-data">

                    @csrf

                    <!-- Template Basic Information -->
                    <div class="row">

                        <!-- Template For -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-template-for" class="form-label">Template For <span class="text-danger">*</span></label>
                                <select name="template_for" id="add-template-for"
                                        class="form-select select2"
                                        data-placeholder="Select Template For">
                                    <option value=""></option>
                                    <option value="contactpluses">Contact Plus</option>
                                    <option value="allcontacts">Allcontact</option>
                                    <option value="associates">Associate</option>
                                    <option value="partners">Partner</option>
                                    <option value="employers">Employer</option>
                                    <option value="leads">Leads</option>
                                </select>
                            </div>
                        </div>

                        <!-- Template Used For -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-template-used-for" class="form-label">Template Use For <span class="text-danger">*</span></label>
                                <select name="template_used_for" id="add-template-used-for" class="form-select select2" data-placeholder="Select Template Used For">
                                    <option value=""></option>
                                    <option value="campaign">Campaign</option>
                                    <option value="auto_message">Auto Message</option>
                                </select>
                            </div>
                        </div>

                        <!-- Meta API Name -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-meta-api-name">Meta API Name <span class="text-danger">*</span></label>
                                <select name="metaapi_id" id="add-meta-api-name"
                                        class="form-select select2"
                                        data-placeholder="Select Meta API Name">
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
                                <label for="add-temp-name" class="form-label">Meta Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="template_name" id="add-temp-name"
                                    class="form-control" placeholder="Enter template name...">
                            </div>
                        </div>

                        <!-- Template Type -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-template-type" class="form-label">Template Type <span class="text-danger">*</span></label>
                                <select name="template_type" id="add-template-type"
                                        class="form-control select2"
                                        data-placeholder="Select Template Type">
                                    <option value="">Select</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Utility">UTILITY</option>
                                </select>
                            </div>
                        </div>

                        <!-- Careoff -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="add-careoff" class="form-label">Careoff</label>
                                <select name="careoff_id" id="add-careoff"
                                        class="form-control select2"
                                        data-placeholder="Select Careoff">
                                    <option value="">Select</option>
                                    @foreach ($careoffs as $careoff)
                                        <option value="{{ $careoff->id }}">{{ $careoff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Dynamic Variable Fields -->
                    <div id="dispVars" class="dispVars" style="display:none;"></div>

                    <!-- Add Variable Button Block -->
                    <div class="row dispAddVarsBtnBlock" id="dispAddVarsBtnBlock" style="display:none;">
                        <div class="col-md-12">
                            <button type="button"
                                    class="btn btn-sm btn-primary DispAddVarBtn float-end">
                                Add Variable
                            </button>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12" id="dispAddMetaURLType" style="display: none">
                            <div class="mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input changeMetaRadio" type="radio" name="meta_url_type" value="0" id="addFileUpload"/>
                                    <label class="form-check-label" for="addFileUpload">File Upload</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input changeMetaRadio" type="radio" name="meta_url_type" value="1" id="addFileURL"/>
                                    <label class="form-check-label" for="addFileURL">File URL</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" id="dispAddothersTextfield" style="display: none">
                            <div class="mb-3">
                                <label for="add-static-url-field" class="form-label">Media URL<span class="text-danger">*</span></label>
                                <input type="text" name="static_url" id="add-static-url-field" class="form-control" placeholder="Enter text or Link">
                                <button type="button" class="mt-1 btn btn-sm btn-danger removeUrl float-end">Remove</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="dispAddFileForALL" style="display: none">
                        <div class="col-md-10">
                            <div class="mb-3" >
                                <label for="add-temp-file2" class="form-label">Template File</label>
                                <input class="form-control template-file-input2" name="photo" type="file" id="add-temp-file2" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatarf2"/>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dispAddDocumentNameForAll" style="display: none">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-document-name" class="form-label">Document Name</label>
                                <input type="text" name="document_name" placeholder="Enter Document Name..." id="add-document-name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <button type="button" class="mt-1 btn btn-sm btn-danger removeDocumentName float-end">Remove</button>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dispAddFileMeta" style="display: none">
                        <div class="col-md-10">
                            <div class="mb-3" >
                                <label for="add-temp-file" class="form-label">Template File</label>
                                <input class="form-control template-file-input" name="photo" type="file" id="add-temp-file" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <img src="{{ asset('admin/assets/img/avatars/blank.jpeg') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar"/>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <button type="button" class="mt-1 btn btn-sm btn-danger removeUrlfile float-end">Remove</button>
                            </div>
                        </div>
                    </div>


                    <!-- WhatsApp Message -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="add-msg-whatsapp" class="form-label">WhatsApp Message</label>
                                <textarea name="msg_whatsapp" id="add-msg-whatsapp"
                                        class="form-control" cols="30" rows="6"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Public Checkbox -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" name="public" type="checkbox" id="add-public" value="1">
                                <label class="form-check-label" for="add-public">Public</label>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>

                </form>
            </div>
        </div>
        <!-- END ADD TEMPLATE -->

        <!-- EDIT TEMPLATE OFFCANVAS -->
        <div class="offcanvas offcanvas-size-xxxl offcanvas-end"
            tabindex="-1"
            id="editMetaTemplate"
            aria-labelledby="editMetaTemplateLabel"
            data-bs-backdrop="static"
            data-bs-keyboard="false">

            <!-- Header -->
            <div class="offcanvas-header">
                <h5 id="editMetaTemplateLabel" class="offcanvas-title">Edit Template</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
            </div>

            <!-- Body -->
            <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">

                <!-- Edit Template Form -->
                <form id="editMetaTemplateForm"
                    action="{{ route('admin.whatsapp.metatemplateupdate') }}"
                    method="POST"
                    enctype="multipart/form-data">

                    @csrf
                    <input type="hidden" name="edit_id" id="editid">

                    <div class="row">

                        <!-- Template For -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-template-for" class="form-label">Template For <span class="text-danger">*</span></label>
                                <select name="template_for" id="edit-template-for"
                                        class="form-select select2"
                                        data-placeholder="Select Template For">
                                        <option value=""></option>
                                        <option value="contactpluses">Contact Plus</option>
                                        <option value="allcontacts">Allcontact</option>
                                        <option value="associates">Associate</option>
                                        <option value="partners">Partner</option>
                                        <option value="employers">Employer</option>
                                        <option value="leads">Leads</option>
                                </select>
                            </div>
                        </div>

                        <!-- Template Used For -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-template-used-for" class="form-label">Template Use For <span class="text-danger">*</span></label>
                                <select name="template_used_for" id="edit-template-used-for" class="form-select select2" data-placeholder="Select Template Used For">
                                    <option value=""></option>
                                    <option value="campaign">Campaign</option>
                                    <option value="auto_message">Auto Message</option>
                                </select>
                            </div>
                        </div>

                        <!-- Meta API Name -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-meta-api-name">Meta API Name <span class="text-danger">*</span></label>
                                <select name="metaapi_id" id="edit-meta-api-name"
                                        class="form-select select2"
                                        data-placeholder="Select Meta API Name">
                                    <option value="">Select</option>
                                    @foreach ($metaAPILists as $metaAPIList)
                                        <option value="{{ $metaAPIList->id }}">{{ $metaAPIList->api_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Template Name -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-temp-name" class="form-label">Meta Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="template_name" id="edit-temp-name"
                                    class="form-control" placeholder="Enter candidate name...">
                            </div>
                        </div>

                        <!-- Template Type -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-template-type" class="form-label">Template Type <span class="text-danger">*</span></label>
                                <select name="template_type" id="edit-template-type"
                                        class="form-control select2"
                                        data-placeholder="Select Template Type">
                                    <option value="">Select</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Utility">UTILITY</option>
                                </select>
                            </div>
                        </div>

                        <!-- Careoff -->
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="edit-careoff" class="form-label">Careoff</label>
                                <select name="careoff_id" id="edit-careoff"
                                        class="form-control select2"
                                        data-placeholder="Select Careoff">
                                    <option value="">Select</option>
                                    @foreach ($careoffs as $careoff)
                                        <option value="{{ $careoff->id }}">{{ $careoff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Leads Employer Dynamic Fields -->
                    <div id="responseValueLeadsEmployer" class="disviewaleadsEmployere"></div>
                    <div id="dispLeadsEmployere" class="disviewaleadsEmployere" style="display:none;"></div>

                    <div id="editVars"></div>

                    <div class="row" id="editAddVarBtnRow" style="display:none;">
                        <div class="col-md-12 mt-2">
                            <button type="button" class="btn btn-sm btn-primary" id="editAddVarBtn">
                                Add Variable
                            </button>
                        </div>
                    </div>

                    <!-- WhatsApp Message -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-msg-whatsapp" class="form-label">WhatsApp Message</label>
                                <textarea name="msg_whatsapp" id="edit-msg-whatsapp"
                                        class="form-control" cols="30" rows="6"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Public Checkbox -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-check-inline mt-3">
                                <input class="form-check-input" name="public" type="checkbox" id="edit-public" value="1">
                                <label class="form-check-label" for="edit-public">Public</label>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>

                </form>
            </div>
        </div>
        <!-- END EDIT TEMPLATE -->

        <!-- Delete Template Start --->
        <div class="modal fade" id="deleteStaff" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.whatsapp.metatemplateDelete') }}" method="POST">
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
        <!-- Delete Template End -->

        <!--- Active Status Start --->
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
        <!-- Active Status End --->

        <!--- Active Status Start --->
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
        <div class="modal fade" id="statusChange" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
    <!-- <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script> -->

    <script src="{{ asset('admin/assets/pages/validation/meta-template-validation.js') }}"></script>

    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>

    <script>
       function cleanText(val) {
            return String(val).replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }


        $(document).ready(function(){

            // -------------------------------------------------------------------------------------------------------------------------------
            // -------------------------------------------------------------------------------------------------------------------------------
            //                                                           Add Template
            // -------------------------------------------------------------------------------------------------------------------------------
            // -------------------------------------------------------------------------------------------------------------------------------

            let rowIndex = 0;

            // ---------------------------------------------
            // Template Dropdown Change (ADD Mode)
            // ---------------------------------------------
            $('#add-template-for').on('change', function () {

                // Clear rows
                $('#dispVars').html('').hide();

                // Hide Add Variable button wrapper
                $('#dispAddVarsBtnBlock').hide();

                // Reset counter
                rowIndex = 0;

                let templateFor = $(this).val();

                // If selected → show the container + button
                if (templateFor !== "") {
                    $('#dispVars').show();
                    $('#dispAddVarsBtnBlock').show();
                }
            });


            // ---------------------------------------------
            // ADD VARIABLE (ADD Mode)
            // ---------------------------------------------
            $(document).on('click', '.DispAddVarBtn', function () {

                let templateFor = $('#add-template-for').val();

                if (templateFor === "") {
                    alert("Please select Template For first.");
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.whatsapp.getTemplateVariables') }}",
                    type: "POST",
                    data: {
                        template_for: templateFor,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {

                        // Ensure response arrays exist
                        let metaFields = response.meta_fields ?? [];
                        let tableFields = response.table_fields ?? [];

                        rowIndex++;

                        let html = `
                            <div class="row mb-3 variableRow" id="row_${rowIndex}">

                                <!-- META VARIABLE -->
                                <div class="col-md-6">
                                    <label class="form-label">Meta Variable *</label>
                                   <select name="field_variable[]" class="form-select select2a">

                                    <!-- STATIC OPTIONS -->
                                    <option value="header_image">Header Image</option>
                                    <option value="header_video">Header Video</option>
                                    <option value="header_document">Header Document</option>
                                    <option value="header_document_name">Header Document Name</option>
                                    <option value="button_0">Button 1</option>
                                    <option value="button_1">Button 2</option>
                                    <option value="button_2">Button 3</option>
                                    <option value="button_3">Button 4</option>
                                    <option value="button_4">Button 5</option>

                                    <!-- DYNAMIC OPTIONS FROM metaFields -->
                                    ${metaFields.map(f => `<option value="${f}">${f}</option>`).join('')}
                                </select>

                                </div>

                                <!-- TEMPLATE TYPE VARIABLE -->
                                <div class="col-md-6">
                                   <label class="form-label">${cleanText(templateFor)} Variable *</label>
                                    <select name="assign_variable[]" class="form-select select2a">

                                        <!-- STATIC OPTIONS -->
                                        <option value="Others">Image,Video,Document(Media Upload)</option>
                                        <option value="Dynamic_Unsubscribe_URL">Dynamic Unsubscribe URL</option>
                                        <option value="Whatsapp_Chat_and_Call_Link">Whatsapp Chat Dynamic URL</option>
                                        <option value="Document_Name">Document Name</option>

                                        <!-- DYNAMIC OPTIONS FROM metaFields -->
                                        ${tableFields.map(f => `<option value="${f}">${f}</option>`).join('')}
                                    </select>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <button type="button"
                                            class="btn btn-danger btn-sm removeVarRow"
                                            data-id="${rowIndex}">
                                        Remove
                                    </button>
                                </div>

                            </div>
                        `;

                        $('#dispVars').append(html).show();

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

                    }
                });
            });


            // ---------------------------------------------
            // REMOVE VARIABLE ROW (ADD MODE)
            // ---------------------------------------------
            $(document).on("click", ".removeVarRow", function () {
                let id = $(this).data("id");
                $("#row_" + id).remove();
            });

            // -------------------------------------------------------------------------------------------------------------------------------
            // -------------------------------------------------------------------------------------------------------------------------------
            //                                                          Edit / Update Template
            // -------------------------------------------------------------------------------------------------------------------------------
            // -------------------------------------------------------------------------------------------------------------------------------

            let editRowIndex = 0;
            let editLoading = false;

            // ---------------------------------------------
            // EDIT Template Dropdown Change
            // ---------------------------------------------
            $('#edit-template-for').on('change', function () {

                if (editLoading) return;

                $('#editVars').html('').hide();
                $('#editAddVarBtnRow').hide();
                editRowIndex = 0;

                let templateFor = $(this).val();

                if (templateFor !== "") {
                    $('#editVars').show();
                    $('#editAddVarBtnRow').show();
                }
            });


            // ---------------------------------------------
            // EDIT Modal Open
            // ---------------------------------------------
            $('#editMetaTemplate').on('show.bs.offcanvas', function (e) {

                let editID = $(e.relatedTarget).data('id');
                $('#editid').val(editID);

                $("#editVars").html("");
                $("#editAddVarBtnRow").hide();
                editRowIndex = 0;
                editLoading = true;

                $.ajax({
                    url: '{{ route("admin.whatsapp.metatemplateedit") }}',
                    type: "GET",
                    data: { id: editID },

                    success: function (res) {

                        $('#edit-temp-name').val(res.template_name);
                        $('#edit-msg-whatsapp').val(res.whatsapp_message);

                        $('#edit-template-for').val(res.template_for).trigger('change');
                        $('#edit-template-used-for').val(res.template_used_for).trigger('change');
                        $('#edit-meta-api-name').val(res.metaapi_id).trigger('change');
                        $('#edit-template-type').val(res.template_type).trigger('change');
                        $('#edit-careoff').val(res.careoff_id).trigger('change');

                        $('#edit-public').prop('checked', res.public == 1);

                        let templateFor = res.template_for;

                        if (res.field_variable && res.assign_variable) {

                            let fields = res.field_variable.split(",");
                            let assigns = res.assign_variable.split(",");

                            // 🔥 GET META + TABLE FIELDS
                            $.ajax({
                                url: "{{ route('admin.whatsapp.getTemplateVariables') }}",
                                type: "POST",
                                data: {
                                    template_for: templateFor,
                                    _token: "{{ csrf_token() }}"
                                },

                                success: function (response) {

                                    // Load saved rows
                                    fields.forEach((f, i) => {
                                        appendEditRow(templateFor, response.meta_fields, response.table_fields, f, assigns[i]);
                                    });

                                    $("#editAddVarBtnRow").show();
                                },

                                complete: function () {
                                    editLoading = false;
                                }
                            });
                        } 
                        else {
                            editLoading = false;
                        }
                    }
                });
            });


            // ---------------------------------------------
            // FUNCTION TO APPEND ROW
            // ---------------------------------------------
            function appendEditRow(templateFor, metaFields, tableFields, selectedMeta = "", selectedAssign = "") {

                editRowIndex++;
                let html = `
                <div class="row mb-3 variableRow" id="edit_row_${editRowIndex}">
                    
                    <!-- META VARIABLE -->
                    <div class="col-md-6">
                        <label class="form-label">Meta Variable *</label>
                        <select name="field_variable[]" class="form-select select2a">

                            <!-- STATIC OPTIONS -->
                            <option value="header_image" ${selectedMeta == "header_image" ? "selected" : ""}>Header Image</option>
                            <option value="header_video" ${selectedMeta == "header_video" ? "selected" : ""}>Header Video</option>
                            <option value="header_document" ${selectedMeta == "header_document" ? "selected" : ""}>Header Document</option>
                            <option value="header_document_name" ${selectedMeta == "header_document_name" ? "selected" : ""}>Header Document Name</option>
                            <option value="button_0" ${selectedMeta == "button_0" ? "selected" : ""}>Button 1</option>
                            <option value="button_1" ${selectedMeta == "button_1" ? "selected" : ""}>Button 2</option>
                            <option value="button_2" ${selectedMeta == "button_2" ? "selected" : ""}>Button 3</option>
                            <option value="button_3" ${selectedMeta == "button_3" ? "selected" : ""}>Button 4</option>
                            <option value="button_4" ${selectedMeta == "button_4" ? "selected" : ""}>Button 5</option>

                            <!-- DYNAMIC OPTIONS -->
                            ${metaFields.map(m =>
                                `<option value="${m}" ${m == selectedMeta ? "selected" : ""}>${m}</option>`
                            ).join('')}
                        </select>
                    </div>

                    <!-- TEMPLATE VARIABLE -->
                    <div class="col-md-6">
                        <label class="form-label">${cleanText(templateFor)} Variable *</label>
                        <select name="assign_variable[]" class="form-select select2a">

                            <!-- STATIC OPTIONS -->
                            <option value="Others" ${selectedAssign == "Others" ? "selected" : ""}>Image/Video/Document</option>
                            <option value="Dynamic_Unsubscribe_URL" ${selectedAssign == "Dynamic_Unsubscribe_URL" ? "selected" : ""}>Dynamic Unsubscribe URL</option>
                            <option value="Whatsapp_Chat_and_Call_Link" ${selectedAssign == "Whatsapp_Chat_and_Call_Link" ? "selected" : ""}>Whatsapp Chat & Call Link</option>
                            <option value="Document_Name" ${selectedAssign == "Document_Name" ? "selected" : ""}>Document Name</option>

                            <!-- DYNAMIC OPTIONS -->
                            ${tableFields.map(t =>
                                `<option value="${t}" ${t == selectedAssign ? "selected" : ""}>${t}</option>`
                            ).join('')}
                        </select>
                    </div>

                    <div class="col-md-12 mt-2">
                        <button type="button"
                                class="btn btn-danger btn-sm removeEditVarRow"
                                data-id="${editRowIndex}">
                            Remove
                        </button>
                    </div>

                </div>
                `;

                $("#editVars").append(html).show();

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
            }


            // ---------------------------------------------
            // ADD VARIABLE (EDIT MODE → AJAX call REQUIRED)
            // ---------------------------------------------
            $(document).on("click", "#editAddVarBtn", function () {

                let templateFor = $('#edit-template-for').val();

                if (templateFor === "") {
                    alert("Please select Template For first.");
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.whatsapp.getTemplateVariables') }}",
                    type: "POST",
                    data: {
                        template_for: templateFor,
                        _token: "{{ csrf_token() }}"
                    },

                    success: function (response) {
                        appendEditRow(templateFor, response.meta_fields, response.table_fields);
                    }
                });
            });


            // ---------------------------------------------
            // REMOVE ROW
            // ---------------------------------------------
            $(document).on("click", ".removeEditVarRow", function () {
                let id = $(this).data("id");
                $("#edit_row_" + id).remove();
            });

            // -------------------------------------------------------------------------------------------------------------------------------
            // -------------------------------------------------------------------------------------------------------------------------------
            //                                                          Delete Template
            // -------------------------------------------------------------------------------------------------------------------------------
            // -------------------------------------------------------------------------------------------------------------------------------


            // ------------------------------------------------------
            // DELETE STAFF MODAL → Set staff ID in hidden input
            // ------------------------------------------------------
            $('#deleteStaff').on('show.bs.modal', function(e) {
                var staff_id = $(e.relatedTarget).data('id');
                $('#delStaffID').val(staff_id);
            });


            // ------------------------------------------------------
            // ACTIVATE TEMPLATE MODAL → Set template ID
            // ------------------------------------------------------
            $('#activeStatus').on('show.bs.modal', function(e) {
                var tempact_id = $(e.relatedTarget).data('id');
                $('#tempactID').val(tempact_id);
            });


            // ------------------------------------------------------
            // DEACTIVATE TEMPLATE MODAL → Set template ID
            // ------------------------------------------------------
            $('#deactiveStatus').on('show.bs.modal', function(e) {
                var tempdeact_id = $(e.relatedTarget).data('id');
                $('#tempdeactID').val(tempdeact_id);
            });


            // ------------------------------------------------------
            // PUBLISH / UNPUBLISH TEMPLATE MODAL
            // Loads template publish status via AJAX
            // ------------------------------------------------------
            $('#publishSts').on('show.bs.modal', function(e) {

                var temppubID = $(e.relatedTarget).data('id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route("admin.whatsapp.metatemplatePublic") }}',
                    method: "GET",
                    data: {
                        id: temppubID,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        $('#temppubID').val(data.id);

                        // Check the right radio button based on current publish status
                        if (data.public == 1) {
                            $('#pubactive').prop("checked", true);
                        } else {
                            $('#pubdeactive').prop("checked", true);
                        }
                    }
                });

            });


            // ------------------------------------------------------
            // STATUS CHANGE MODAL (Activate/Deactivate Entire Template)
            // Loads current status via AJAX
            // ------------------------------------------------------
            $('#statusChange').on('show.bs.modal', function(e) {

                var tempchstID = $(e.relatedTarget).data('id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route("admin.whatsapp.metatemplateStatus") }}',
                    method: "GET",
                    data: {
                        id: tempchstID,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {

                        $('#tempchstID').val(data.id);

                        // Set correct radio selection based on current status
                        if (data.status == 1) {
                            $('#changestact').prop("checked", true);
                        } else {
                            $('#changestdeact').prop("checked", true);
                        }
                    }
                });

            });


        });
    </script>



@endsection
