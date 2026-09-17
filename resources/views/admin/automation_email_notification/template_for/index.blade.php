@extends('layout.admin.admin_layout')

@section('title','Email Notification')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />

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

    <style>
        .preview-email-body {
            padding: 6px 10px; /* top-bottom 6px, left-right 10px */
            border-radius: 5px;
        }

    </style>

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
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-2">
                        Templates for: <span class="text-primary">{{ ucfirst($template_for ?? 'All') }}</span>
                    </h5>

                    <!-- 🔵 Back Button -->
                    <a href="{{ route('admin.emailautomation.list') }}" class="btn btn-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>

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
                            <th>Email Template Name</th>
                            <th>Status</th>
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
                <form class="add-new-user pt-0" id="addNewUserForm2" action="{{ route('admin.emailautomation.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- Pass template_for as hidden input --}}
                    <input type="hidden" name="template_for" value="{{ $template_for }}">
                    <input type="hidden" name="template_table_name" id="add-template-table-name-for-hidden" value="{{ $tableName }}">

                    <div class="row">
                        {{-- removed template_for dropdown (since page is category-specific) --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-email-template" class="form-label">Meta Template <span class="text-danger">*</span></label>
                                <select name="emailtemp_id" id="add-email-template" class="form-control select2 dynamicemailtemplist" data-placeholder="Select Meta Template" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach ($templates as $t)
                                    <option value="{{ $t->id }}">{{ $t->template_name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-template-name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="email_template_name" id="add-template-name" class="form-control">
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add-trigger-template-type" class="form-label">Trigger<span class="text-danger">*</span></label>
                                <select name="trigger_template_type" id="add-trigger-template-type" class="form-control select2" data-placeholder="Select Trigger Type" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="add_new_entry_added">Add New Entry Added</option>
                                    <option value="wait">Wait</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 trigger-time-div" style="display: none;">
                            <div class="mb-3">
                                <label for="add-trigger-template-time" class="form-label">Time (In number)<span class="text-danger">*</span></label>
                                <input type="number" name="trigger_template_time" id="add-trigger-template-time" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3 trigger-time-div" style="display: none;">
                            <div class="mb-3">
                                <label for="add-trigger-template-time-type" class="form-label">Type<span class="text-danger">*</span></label>
                                <select name="trigger_template_time_type" id="add-trigger-template-time-type" class="form-control select2" data-placeholder="Select Trigger Type" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="minute">minute</option>
                                    <option value="hour">hour</option>
                                    <option value="day">day</option>
                                    <option value="week">week</option>
                                    <option value="month">month</option>
                                    <option value="year">year</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 trigger-time-div" style="display: none;">
                            <div class="mb-3">
                                <label for="field-not-completed" class="form-label">Feild Not Completed</label>
                                <select name="field_not_completed[]" id="field-not-completed" class="form-control select2" multiple data-placeholder="Select Fields">
                                    @foreach($columns as $field)
                                        <option value="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Email Body</label>
                                <p id="AddEmailBodyPreview" class="preview-email-body"></p>
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
                <form class="add-new-user pt-0 " id="editUserForm2" action="{{ route('admin.emailautomation.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="edit_id" id="editid">
                    <input type="hidden" name="template_for" id="edit-template-for-hidden" value="{{ $template_for }}">
                    <input type="hidden" name="template_table_name" id="edit-template-table-name-for-hidden" value="{{ $tableName }}">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-email-template" class="form-label">Email Template <span class="text-danger">*</span></label>
                                <select name="emailtemp_id" id="edit-email-template" class="form-control select2 dynamicemailtemplistEdit" data-placeholder="Select Meta Template" data-allow-clear="true">
                                        <option value=""></option>
                                        @foreach ($templates as $t)
                                            <option value="{{ $t->id }}">{{ $t->template_name }}</option>
                                        @endforeach    
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-template-name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="email_template_name" id="edit-template-name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit-trigger-template-type" class="form-label">Trigger<span class="text-danger">*</span></label>
                                <select name="trigger_template_type" id="edit-trigger-template-type" class="form-control select2" data-placeholder="Select Trigger Type" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="add_new_entry_added">Add New Entry Added</option>
                                    <option value="wait">Wait</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3 edit-trigger-time-div" style="display: none;">
                            <div class="mb-3">
                                <label for="edit-trigger-template-time" class="form-label">Time<span class="text-danger">*</span></label>
                                <input type="number" name="trigger_template_time" id="edit-trigger-template-time" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-3 edit-trigger-time-div" style="display: none;">
                            <div class="mb-3">
                                <label for="edit-trigger-template-time-type" class="form-label">Time <span class="text-danger">*</span></label>
                                <select name="trigger_template_time_type" id="edit-trigger-template-time-type" class="form-control select2" data-placeholder="Select Trigger Type" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="minute">minute</option>
                                    <option value="hour">hour</option>
                                    <option value="day">day</option>
                                    <option value="week">week</option>
                                    <option value="month">month</option>
                                    <option value="year">year</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 edit-trigger-time-div" style="display: none;">
                            <div class="mb-3">
                                <label for="edit-field-not-completed" class="form-label">Feild Not Completed</label>
                                <select name="field_not_completed[]" id="edit-field-not-completed" class="form-control select2" multiple data-placeholder="Select Fields">
                                    @foreach($columns as $field)
                                        <option value="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Email Body</label>
                                <p id="EditEmailBodyPreview" class="preview-email-body"></p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
        <!--- Edit Candidate End --->

        <!-- Delete Template Modal Start -->
        <div class="modal fade" id="deleteTemplate" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Delete Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.emailautomation.destroy') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="del_temp_id">
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
        <!-- Delete Template Modal End -->

         <!--Change Status Modal Start -->
         <div class="modal fade" id="statusChange" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Status change</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.emailautomation.status.update') }}" method="POST">
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

        <!-- Publish / status modals remain unchanged -->
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

        <!-- other modals left as in your original file... -->

    </div>
@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('admin/assets/plugins/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>

    <script>
        window.metaTemplateFor = {!! json_encode($template_for) !!};
    </script>

    <script>
        /**
         * Page User List (template-wise)
         */

        'use strict';

        // Datatable (jquery)
        $(function () {
        let borderColor, bodyBg, headingColor;

        if (typeof isDarkStyle !== 'undefined' && isDarkStyle) {
            borderColor = config.colors_dark.borderColor;
            bodyBg = config.colors_dark.bodyBg;
            headingColor = config.colors_dark.headingColor;
        } else {
            borderColor = config.colors ? config.colors.borderColor : '#e9ecef';
            bodyBg = config.colors ? config.colors.bodyBg : '#fff';
            headingColor = config.colors ? config.colors.headingColor : '#5e5873';
        }

        var assetPath = $('body').attr('data-asset-path') || '/';
        // template_for passed from Blade as window.metaTemplateFor
        var templateFor = window.metaTemplateFor || '';

        // Variable declaration for table
        var dt_user_table = $('.datatables-users'),
            metaedit = assetPath + 'admin/auto-email-notification/edit',
            userView = assetPath + 'admin/candidate/view',
            userPublish = assetPath + 'admin/candidate/publish/stage',

            statusObj = {
                1: { title: 'Pending', class: 'bg-label-warning' },
                2: { title: 'Active', class: 'bg-label-success' },
                3: { title: 'Inactive', class: 'bg-label-secondary' }
            };

        // Users datatable
        if (dt_user_table.length) {
            var dt_user = dt_user_table.DataTable({
            "ajax":{
                "url"   : assetPath +  "admin/email-automation/template/list/json",
                "type"  : "POST",
                "data"  : function(d){
                d._token = $('meta[name="csrf-token"]').attr('content');
                d.template_for = templateFor;
                }
            },

            columns: [
                { data: 'id' }, //0 control
                { data: 'template_name' }, //1
                { data: 'email_template_name' }, //2
                { data: 'status' }, //3
                { data: 'action' } //4
            ],
            columnDefs: [
                {
                // For Responsive control column (empty)
                className: 'control',
                searchable: false,
                orderable: false,
                responsivePriority: 2,
                targets: 0,
                render: function (data, type, full, meta) {
                    return '';
                }
                },
                {
                // Template name
                targets: 1,
                responsivePriority: 4,
                render: function (data, type, full, meta) {
                    return "<span class='text-truncate d-flex align-items-center'>" + (full['template_name'] ?? '') + '</span>';
                }
                },
                {
                // Meta template name
                targets: 2,
                render: function (data, type, full, meta) {
                    return "<span class='text-truncate d-flex align-items-center'>" + (full['email_template_name'] ?? '') + '</span>';
                }
                },
                {
                // Status
                targets: 3,
                render: function(data,type,full,meta){
                    var id = full['autoemailnotifications_id'];
                    var $status = full['status'];

                    var statusobj = {
                    1: { title: 'Active', class: 'bg-label-success' },
                    0: { title: 'Inactive', class: 'bg-label-warning' },
                    };

                    // guard
                    if (typeof statusobj[$status] === 'undefined') {
                    return '<span class="badge bg-label-secondary">Unknown</span>';
                    }

                    return (
                    '<span data-bs-toggle="modal" data-bs-target="#statusChange" data-id="'+id+'" class="badge ' +
                    statusobj[$status].class +
                    '" text-capitalized>' +
                    statusobj[$status].title +
                    '</span>'
                    );
                }
                },
                {
                // Actions
                targets: -1,
                title: 'Actions',
                searchable: false,
                orderable: false,
                render: function (data, type, full, meta) {
                    var id = full['autoemailnotifications_id'];
                    var status = full['status'];
                    return `
                    <div class="d-flex align-items-center">
                    
                        <!-- Three dots dropdown -->
                        <div class="dropdown">
                            <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical ti-sm mx-1"></i>
                            </a>
                    
                            <div class="dropdown-menu dropdown-menu-end">
                    
                                <!-- Edit -->
                                <a href="javascript:;" 
                                class="dropdown-item edtemplate"
                                data-bs-toggle="offcanvas" 
                                data-bs-target="#edituser" 
                                data-id="${id}">
                                    <i class="ti ti-edit ti-sm me-1"></i> Edit
                                </a>
                    
                                <!-- Delete -->
                                <a href="javascript:;" 
                                class="dropdown-item deltemplate"
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteTemplate"
                                data-id="${id}">
                                    <i class="ti ti-trash ti-sm me-1"></i> Delete
                                </a>
                    
                            </div>
                        </div>
                    
                    </div>`;
                    
                }
                }
            ],
            order: [[0, 'desc']],
            dom:
                '<"row me-2"' +
                '<"col-md-2"<"me-3"l>>' +
                '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>' +
                '>t' +
                '<"row mx-2"' +
                '<"col-sm-12 col-md-6"i>' +
                '<"col-sm-12 col-md-6"p>' +
                '>',
            language: {
                sLengthMenu: '_MENU_',
                search: '',
                searchPlaceholder: 'Search..'
            },
            // Buttons with Dropdown
            buttons: [
                {
                extend: 'collection',
                className: 'btn btn-label-secondary btn-sm dropdown-toggle mx-3',
                text: '<i class="ti ti-screen-share me-1 ti-xs"></i>Export',
                buttons: [
                    {
                    extend: 'print',
                    text: '<i class="ti ti-printer me-2" ></i>Print',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [1, 2, 3],
                        format: {
                        body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            var el = $.parseHTML(inner);
                            var result = '';
                            $.each(el, function (index, item) {
                            if (item.classList !== undefined && item.classList.contains('user-name')) {
                                result = result + item.lastChild.firstChild.textContent;
                            } else if (item.innerText === undefined) {
                                result = result + item.textContent;
                            } else result = result + item.innerText;
                            });
                            return result;
                        }
                        }
                    },
                    customize: function (win) {
                        $(win.document.body)
                        .css('color', headingColor)
                        .css('border-color', borderColor)
                        .css('background-color', bodyBg);
                        $(win.document.body)
                        .find('table')
                        .addClass('compact')
                        .css('color', 'inherit')
                        .css('border-color', 'inherit')
                        .css('background-color', 'inherit');
                    }
                    },
                    {
                    extend: 'csv',
                    text: '<i class="ti ti-file-text me-2" ></i>Csv',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [1, 2, 3],
                        format: {
                        body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            var el = $.parseHTML(inner);
                            var result = '';
                            $.each(el, function (index, item) {
                            if (item.classList !== undefined && item.classList.contains('user-name')) {
                                result = result + item.lastChild.firstChild.textContent;
                            } else if (item.innerText === undefined) {
                                result = result + item.textContent;
                            } else result = result + item.innerText;
                            });
                            return result;
                        }
                        }
                    }
                    },
                    {
                    extend: 'excel',
                    text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [1, 2, 3],
                        format: {
                        body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            var el = $.parseHTML(inner);
                            var result = '';
                            $.each(el, function (index, item) {
                            if (item.classList !== undefined && item.classList.contains('user-name')) {
                                result = result + item.lastChild.firstChild.textContent;
                            } else if (item.innerText === undefined) {
                                result = result + item.textContent;
                            } else result = result + item.innerText;
                            });
                            return result;
                        }
                        }
                    }
                    },
                    {
                    extend: 'pdf',
                    text: '<i class="ti ti-file-code-2 me-2"></i>Pdf',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [1, 2, 3],
                        format: {
                        body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            var el = $.parseHTML(inner);
                            var result = '';
                            $.each(el, function (index, item) {
                            if (item.classList !== undefined && item.classList.contains('user-name')) {
                                result = result + item.lastChild.firstChild.textContent;
                            } else if (item.innerText === undefined) {
                                result = result + item.textContent;
                            } else result = result + item.innerText;
                            });
                            return result;
                        }
                        }
                    }
                    },
                    {
                    extend: 'copy',
                    text: '<i class="ti ti-copy me-2" ></i>Copy',
                    className: 'dropdown-item',
                    exportOptions: {
                        columns: [1, 2, 3],
                        format: {
                        body: function (inner, coldex, rowdex) {
                            if (inner.length <= 0) return inner;
                            var el = $.parseHTML(inner);
                            var result = '';
                            $.each(el, function (index, item) {
                            if (item.classList !== undefined && item.classList.contains('user-name')) {
                                result = result + item.lastChild.firstChild.textContent;
                            } else if (item.innerText === undefined) {
                                result = result + item.textContent;
                            } else result = result + item.innerText;
                            });
                            return result;
                        }
                        }
                    }
                    }
                ]
                },
                {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New Template</span>',
                className: 'add-new btn btn-primary btn-sm addtemplate',
                attr: {
                    'data-bs-toggle': 'offcanvas',
                    'data-bs-target': '#offcanvasAddUser'
                }
                }
            ],
            // For responsive popup
            responsive: {
                details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                    var data = row.data();
                    return 'Details of ' + (data['template_name'] || 'Template');
                    }
                }),
                type: 'column',
                renderer: function (api, rowIdx, columns) {
                    var data = $.map(columns, function (col, i) {
                    return col.title !== ''
                        ? '<tr data-dt-row="' +
                            col.rowIndex +
                            '" data-dt-column="' +
                            col.columnIndex +
                            '">' +
                            '<td>' +
                            col.title +
                            ':' +
                            '</td> ' +
                            '<td>' +
                            col.data +
                            '</td>' +
                            '</tr>'
                        : '';
                    }).join('');

                    return data ? $('<table class="table"/><tbody />').append(data) : false;
                }
                }
            },
            initComplete: function () {
                // Adding role filter once table initialized (keeps your original behavior)
                this.api().columns(1).every(function () {
                var column = this;
                var select = $(
                    '<select id="UserRole" class="form-select text-capitalize"><option value=""> Select Candidate </option></select>'
                ).appendTo('.user_role').on('change', function () {
                    column.search(this.value).draw();
                });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
                });

                // Adding plan filter once table initialized
                this.api().columns(2).every(function () {
                var column = this;
                var select = $(
                    '<select id="UserPlan" class="form-select text-capitalize"><option value=""> Select Passport No </option></select>'
                ).appendTo('.user_plan').on('change', function () {
                    column.search(this.value).draw();
                });

                column.data().unique().sort().each(function (d, j) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });
                });

            }
            });
        }

        // Filter form control to default size
        setTimeout(() => {
            $('.dataTables_filter .form-control').removeClass('form-control-sm');
            $('.dataTables_length .form-select').removeClass('form-select-sm');
        }, 300);

        });
    </script>
    <script src="{{ asset('admin/assets/pages/validation/auto-meta-notification-validation.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    <script>
        $(document).ready(function(){

            // Initialize Select2
            var select2 = $('.select2');
            if (select2.length) {
                select2.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        dropdownParent: $this.parent()
                    });
                });
            }

            // ==============================
            // Helper: Format Email Preview
            // ==============================
            const LoadEmail = ({ email_body = '', email_body_bg = '#FFFFFF', attachment_url = '' }) => {
    
                // Prepare styled body (same as before)
                let styledBody = email_body
                    .replace(/<table/gi, "<table style='border-collapse:collapse;width:100%;border:2px solid #000'")
                    .replace(/<th/gi, "<th style='border:1px solid #000;padding:8px;background:#000;color:#fff;text-align:left'")
                    .replace(/<td/gi, "<td style='border:1px solid #000;padding:8px'");
                
                // Attachment Section (if file exists)
                let attachmentBlock = '';
                if (attachment_url && attachment_url.trim() !== '') {
                    attachmentBlock = `
                        <div style="margin-top: 15px; padding:10px; background:#f7f7f7; border:1px dashed #999; border-radius:5px;">
                            <strong>📎 Attachment:</strong><br>
                            <a href="${attachment_url}" download style="color:#007bff; text-decoration: underline;">
                                Click here to download attachment
                            </a>
                        </div>
                    `;
                }

                // Final HTML returned
                return `
                    <div style="background:${email_body_bg}; padding:20px;">
                        ${styledBody}
                        ${attachmentBlock}
                    </div>`;
            };


            // ==============================
            // AJAX Helper
            // ==============================
            const ajaxGET = (url, data = {}, success) => {
                $.get(url, data).done(success).fail(err => console.error(err));
            };


            // ==============================
            // Add Template Change Handler
            // ==============================
            $('#add-email-template').on("change", function () {
                const template_id = this.value;
                if (!template_id) return;

                ajaxGET(`{{ url('admin/email-campaign-list/get/id') }}?id=${template_id}`, {}, data => {
                    $('#add-template-name').val(data.template_name);
                    $("#AddEmailBodyPreview").html(LoadEmail(data));
                });
            });


            // ==============================
            // Delete Template Modal
            // ==============================
            $('#deleteTemplate').on('show.bs.modal', e =>
                $('#del_temp_id').val($(e.relatedTarget).data('id'))
            );


            // ==============================
            // Status Change Modal
            // ==============================
            $('#statusChange').on('show.bs.modal', e => {
                const id = $(e.relatedTarget).data('id');
                $('#tempchstID').val(id);

                ajaxGET("{{ route('admin.emailautomation.getStatus') }}", { id }, data => {
                    $('#changestact').prop("checked", data.status == 1);
                    $('#changestdeact').prop("checked", data.status != 1);
                });
            });


            // ==============================
            // Trigger Handler (Add Form)
            // ==============================
            $('#add-trigger-template-type').on('change', function () {
                const isWait = this.value === 'wait';
                $('.trigger-time-div').toggle(isWait);

                if (!isWait)
                    $('#add-trigger-template-time, #add-trigger-template-time-type').val('').trigger('change');
            });


            // ==============================
            // EDIT OFFCANVAS — Load Template
            // ==============================
            $('#edituser').on('show.bs.offcanvas', function (e) {

                const id = $(e.relatedTarget).data('id');
                const url = "{{ route('admin.emailautomation.templateFor.edit', ':id') }}".replace(':id', id);

                ajaxGET(url, { id }, data => {

                    // Basic Fields
                    $('#editid').val(data.id);
                    $('#edit-email-template').val(data.emailtemp_id).trigger('change');
                    $('#edit-template-name').val(data.template_name);

                    // Trigger Fields
                    const isWait = data.trigger_template_type === 'wait';
                    $('#edit-trigger-template-type').val(data.trigger_template_type).trigger('change');
                    $('#edit-trigger-time-div').toggle(isWait);

                    if (isWait) {
                        $('#edit-trigger-template-time').val(data.trigger_template_time);
                        $('#edit-trigger-template-time-type').val(data.trigger_template_time_type).trigger('change');
                        $('#edit-field-not-completed').val(JSON.parse(data.field_not_completed)).trigger('change');
                    } else {
                        $('#edit-trigger-template-time, #edit-trigger-template-time-type, #edit-field-not-completed')
                            .val('').trigger('change');
                    }
                });
            });


            // ==============================
            // Load Template into Edit Preview
            // ==============================
            const loadEmailTemplateData = id =>
                ajaxGET("{{ route('admin.emailautomation.templateFor.getListID') }}", { id }, data =>
                    $("#EditEmailBodyPreview").html(LoadEmail(data))
                );

            $('#edit-email-template').on("change", function () {
                if (this.value) loadEmailTemplateData(this.value);
            });


            // ==============================
            // Trigger Handler (Edit Form)
            // ==============================
            $('#edit-trigger-template-type').on('change', function () {
                const isWait = this.value === 'wait';
                $('.edit-trigger-time-div').toggle(isWait);

                if (!isWait)
                    $('#edit-trigger-template-time, #edit-trigger-template-time-type, #edit-field-not-completed')
                        .val('').trigger('change');
            });


        });
    </script>

@endsection
