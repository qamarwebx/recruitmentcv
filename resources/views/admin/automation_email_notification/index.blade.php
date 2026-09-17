@extends('layout.admin.admin_layout')

@section('title','Email Notification')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/tagify/tagify.css') }}" />
    {{-- <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/dropzone/dropzone.css') }}"> --}}
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
                            <th>Template For</th>
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
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="add-template-for" class="form-label">Template For <span class="text-danger">*</span></label>
                                <select name="template_for" id="add-template-for" class="form-control select2" data-placeholder="Select Template For" data-allow-clear="true">
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
                <form class="add-new-user pt-0 " id="editUserForm2" action="{{ route('admin.autometanotification.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="edit_id" id="editid">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-template-for" class="form-label">Template For <span class="text-danger">*</span></label>
                                <select name="template_for" id="edit-template-for" class="form-control select2" data-placeholder="Select Template For" data-allow-clear="true">
                                    <option value=""></option>
                                    <option value="contactp">Contact Plus</option>
                                    <option value="allcontact">Allcontact</option>
                                    <option value="associate">Associate</option>
                                    <option value="partner">Partner</option>
                                    <option value="employer">Employer</option>
                                    <option value="leads_employer">Leads Employer</option>
                                    <option value="leads_candidate">Leads Candidate</option>
                                    <option value="todo_notification">Todo Notification</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-meta-template" class="form-label">Meta Template <span class="text-danger">*</span></label>
                                <select name="metatemp_id" id="edit-meta-template" class="form-control select2 dynamicmetatemplistEdit" data-placeholder="Select Meta Template" data-allow-clear="true">

                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit-template-name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="meta_template_name" id="edit-template-name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit-msg-body" class="form-label">Message</label>
                                <textarea name="msg_email" class="form-control" id="edit-msg-body" cols="30" rows="6"></textarea>
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
                    <form action="{{ route('admin.autometanotification.status.update') }}" method="POST">
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
    {{-- <script src="{{ asset('admin/assets/js/forms-selects.js') }}"></script> --}}

    {{-- <script src="{{ asset('admin/assets/vendor/libs/dropzone/dropzone.js') }}"></script> --}}

    <script src="{{ asset('admin/assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/quill/quill.js') }}"></script>


    <script>
        /**
         * Page User List
         */

        'use strict';

        // Datatable (jquery)
        $(function () {
        let borderColor, bodyBg, headingColor;

        if (isDarkStyle) {
            borderColor = config.colors_dark.borderColor;
            bodyBg = config.colors_dark.bodyBg;
            headingColor = config.colors_dark.headingColor;
        } else {
            borderColor = config.colors.borderColor;
            bodyBg = config.colors.bodyBg;
            headingColor = config.colors.headingColor;
        }

        var assetPath = $('body').attr('data-asset-path');

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
                "url"   : assetPath +  "admin/email-automation/list/json",
                "type"  : "POST",
                "data"  : {"_token":$('meta[name="csrf-token"]').attr('content')}
            },

            // ✅ ONLY TEMPLATE_FOR COLUMN
            columns: [
                { data: 'template_for' }
            ],

            columnDefs: [
                {
                targets: 0,
                render: function(data, type, full, meta){
                
                    var audience = full['template_for'];
                
                    var template_for = {
                        contactp: {title: 'Contact Plus'},
                        allcontact: {title: 'Allcontact'},
                        associate: {title: 'Associate'},
                        partner: {title: 'Partner'},
                        todo: {title: 'Todo'},
                        employer: {title: 'Employer'},
                        leads_employer: {title: 'Leads Employer'},
                        leads_candidate: {title: 'Leads Candidate'},
                        todo_notification: {title: 'Todo Notification'},
                        null: {title: '---'}
                    };
                
                    let title = template_for[audience]?.title ?? audience;
                
                    // 🔵 Make clickable
                    return `
                    <a href="/admin/email-automation/template/${full.template_for}"
                        class="text-primary fw-bold">
                        ${title}
                    </a>
                    `;
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
                    // 🔥 kept EXACTLY SAME (not removed)
                    { extend: 'print', text: '<i class="ti ti-printer me-2"></i>Print', className: 'dropdown-item' },
                    { extend: 'csv', text: '<i class="ti ti-file-text me-2"></i>Csv', className: 'dropdown-item' },
                    { extend: 'excel', text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel', className: 'dropdown-item' },
                    { extend: 'pdf', text: '<i class="ti ti-file-code-2 me-2"></i>Pdf', className: 'dropdown-item' },
                    { extend: 'copy', text: '<i class="ti ti-copy me-2"></i>Copy', className: 'dropdown-item' }
                ]
                },

                // ⭐ Add New Template Button (UNCHANGED)
                {
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New Template</span>',
                className: 'add-new btn btn-primary btn-sm addtemplate',
                attr: {
                    'data-bs-toggle': 'offcanvas',
                    'data-bs-target': '#offcanvasAddUser'
                }
                }
            ],

            responsive: {
                details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                    var data = row.data();
                    return 'Details';
                    }
                }),
                type: 'column',
                renderer: function (api, rowIdx, columns) {
                    var data = $.map(columns, function (col, i) {
                    return col.title !== ''
                        ? '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                            '<td>' + col.title + ':' + '</td>' +
                            '<td>' + col.data + '</td>' +
                        '</tr>'
                        : '';
                    }).join('');
                    return data ? $('<table class="table"/><tbody />').append(data) : false;
                }
                }
            }
            });
        }

        // Keep the rest EXACTLY SAME
        setTimeout(() => {
            $('.dataTables_filter .form-control').removeClass('form-control-sm');
            $('.dataTables_length .form-select').removeClass('form-select-sm');
        }, 300);


        const fullToolbar = [
            [
            {
                font: []
            },
            {
                size: []
            }
            ],
            ['bold', 'italic', 'underline', 'strike'],
            [
            {
                color: []
            },
            {
                background: []
            }
            ],
            [
            {
                script: 'super'
            },
            {
                script: 'sub'
            }
            ],
            [
            {
                header: '1'
            },
            {
                header: '2'
            },
            'blockquote',
            'code-block'
            ],
            [
            {
                list: 'ordered'
            },
            {
                list: 'bullet'
            },
            {
                indent: '-1'
            },
            {
                indent: '+1'
            }
            ],
            [{ direction: 'rtl' }],
            ['link', 'image', 'video', 'formula'],
            ['clean']
        ];
        const mailText = $('#mail-text'),mailText2 = $('#mail-text2');
        if (mailText.length) {
            var mailTextEditor = new Quill('#mail-text',{
            bounds: '#mail-text',
            placeholder: 'Type Something...',
            modules: {
                formula: true,
                toolbar: fullToolbar
            },
            theme: 'snow'
            });

            mailTextEditor.on('text-change',function(delta, oldDelta, source){
            document.getElementById("quill_html_mail").value = mailTextEditor.root.innerHTML;
            });

        }

        if (mailText2.length) {
            var mailTextEditor2 = new Quill('#mail-text2',{
            bounds: '#mail-text2',
            placeholder: 'Type Something...',
            modules: {
                formula: true,
                toolbar: fullToolbar
            },
            theme: 'snow'
            });

            mailTextEditor2.on('text-change',function(delta, oldDelta, source){
            document.getElementById("quill_html_mail2").value = mailTextEditor2.root.innerHTML;
            });

        }


        });

    </script>

    
    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    <script src="{{ asset('admin/assets/pages/validation/automation-meta-notification-validation.js') }}"></script>



    <!-- Admin Main JS -->
    <script src="{{ asset('admin/assets/pages/adminmain.js') }}"></script>
    {{-- <script src="{{ asset('admin/assets/js/forms-file-upload.js') }}"></script> --}}

    <script>
        $(document).ready(function(){
            var select2 = $('.select2');
            if (select2.length) {
                select2.each(function () {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        // placeholder: 'Select value',
                        dropdownParent: $this.parent()
                    });
                });
            }



        });
    </script>

    <script>
        var rowMin25 = 0;
        var rowMax25 = 10;

        $(document).on('click','.addExp25',function(){
            var html25 = "";
            html25 += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html25 += '<label class="form-label" for="add-field-allcontact-variable'+rowMin25+'">Meta Variable <span class="text-danger">*</span></label>';
            html25 += '<select name="meta_field_name[]" id="add-field-allcontact-variable'+rowMin25+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html25 += '</select></div></div>';

            html25 += '<div class="col-md-6"><div class="form-group"><label class="form-label" for="assign_var_allcontact'+rowMin25+'">Allcontact Variable <span class="text-danger">*</span></label>';
            html25 += '<select name="assign_var_name[]" id="assign_var_allcontact'+rowMin25+'" class="form-control allcontactShowHide assignVariableContactPOPT select2a">
                <option value="">Select</option>
                <option value="[Others]">Image,Video,Document(Media Upload)</option>
                <option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option>
                <option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Dynamic URL</option>
                <option value="[Document Name]">Document Name</option>
                <option value="[OTP]">OTP</option>
                <option value="[Booking Date]">Booking Date</option>
                <option value="[Lead Customer Name]">Lead Customer Name</option>
                <option value="[Candidate Name]">Candidate Name</option>
                <option value="[Candidate Contact No]">Candidate Contact No</option>
                <option value="[Customer Name]">Customer Name</option>
                <option value="[Customer City]">Customer City</option>
                <option value="[Customer Phone No]">Customer Phone No</option>
                <option value="[Order Reference Number]">Order Reference Number</option>
                <option value="[Partner Name]">Partner Name</option>
                <option value="[Partner Mobile No]">Partner Mobile No</option>
                <option value="[Recruitment Office English]">Recruitment Office English</option>
                <option value="[Recruitment Office Arabic]">Recruitment Office Arabic</option>
                <option value="[Partner Staff Concern Name 1]">Partner Staff Concern Name 1</option>
                <option value="[Partner Staff Concern Mobile 1]">Partner Staff Concern Mobile 1</option>
                <option value="[Partner Staff Concern Name 2]">Partner Staff Concern Name 2</option>
                <option value="[Partner Staff Concern Mobile 2]">Partner Staff Concern Mobile 2</option>
                <option value="[Partner Staff Concern Name 3]">Partner Staff Concern Name 3</option>
                <option value="[Partner Staff Concern Mobile 3]">Partner Staff Concern Mobile 3</option>
                <option value="[Partner Address English]">Partner Address English</option>';
            html25 += '</select></div></div>';
            html25 += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end mb-2 mt-2">Remove</button></div></div>';

            $('#dispExp25').append(html25);

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

            rowMin25++;
            rowMax25--;
            if (rowMax25 == 1) {
                $('.addExp25').prop('disabled',true);
            }else{
                $('.addExp25').prop('disabled',false);
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
            rowMax25++;
            rowMin25--;
            if (rowMax25 == 0) {
                $('.addExp25').prop('disabled',true);
            }else{
                $('.addExp25').prop('disabled',false);
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

    </script>

    <script>
        var rowMin25E = 0;
        var rowMax25E = 10;

        $(document).on('click','.addExp25E',function(){
            var html25 = "";
            html25 += '<div class="row"><div class="col-md-6"><div class="form-group">';
            html25 += '<label class="form-label" for="edit-field-allcontact-variable'+rowMin25E+'">Meta Variable <span class="text-danger">*</span></label>';
            html25 += '<select name="meta_field_name[]" id="edit-field-allcontact-variable'+rowMin25E+'" class="form-control fieldVariable select2a"><option value="">Select</option><option value="header_image">Header Image</option><option value="header_video">Header Video</option><option value="header_document">Header Document</option><option value="header_document_name">Header Document Name</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option><option value="field_11">Field 11</option><option value="field_12">Field 12</option><option value="button_0">Button 1</option><option value="button_1">Button 2</option><option value="button_2">Button 3</option><option value="button_3">Button 4</option><option value="button_4">Button 5</option>';
            html25 += '</select></div></div>';

            html25 += '<div class="col-md-6"><div class="form-group"><label class="form-label" for="assign_var_allcontactE'+rowMin25E+'">Allcontact Variable <span class="text-danger">*</span></label>';
            html25 += '<select name="assign_var_name[]" id="assign_var_allcontactE'+rowMin25E+'" class="form-control allcontactShowHideE assignVariableContactPOPT select2a"><option value="[Others]">Image,Video,Document(Media Upload)</option><option value="[Dynamic Unsubscribe URL]">Dynamic Unsubscribe URL</option><option value="[Whatsapp Chat Dynamic URL]">Whatsapp Chat Dynamic URL</option><option value="[Document Name]">Document Name</option><option value="[OTP]">OTP</option><option value="[Booking Date]">Booking Date</option><option value="[Lead Customer Name]">Lead Customer Name</option><option value="[Candidate Name]">Candidate Name</option><option value="[Candidate Contact No]">Candidate Contact No</option><option value="[Customer Name]">Customer Name</option><option value="[Customer City]">Customer City</option><option value="[Customer Phone No]">Customer Phone No</option><option value="[Order Reference Number]">Order Reference Number</option><option value="[Partner Name]">Partner Name</option><option value="[Partner Mobile No]">Partner Mobile No</option><option value="[Recruitment Office English]">Recruitment Office English</option><option value="[Recruitment Office Arabic]">Recruitment Office Arabic</option><option value="[Partner Staff Concern Name 1]">Partner Staff Concern Name 1</option><option value="[Partner Staff Concern Mobile 1]">Partner Staff Concern Mobile 1</option><option value="[Partner Staff Concern Name 2]">Partner Staff Concern Name 2</option><option value="[Partner Staff Concern Mobile 2]">Partner Staff Concern Mobile 2</option><option value="[Partner Staff Concern Name 3]">Partner Staff Concern Name 3</option><option value="[Partner Staff Concern Mobile 3]">Partner Staff Concern Mobile 3</option><option value="[Partner Address English]">Partner Address English</option>';
            html25 += '</select></div></div>';
            html25 += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger removeE float-end mb-2 mt-2">Remove</button></div></div>';

            $('#dispExp25E').append(html25);

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

            rowMin25E++;
            rowMax25E--;
            if (rowMax25E == 1) {
                $('.addExp25E').prop('disabled',true);
            }else{
                $('.addExp25E').prop('disabled',false);
            }

        });

        $(document).on('click','.removeE',function(){

            let closestvalue = $(this).closest('.row').find('.allcontactShowHideE').val();

            if (closestvalue == '[Others]') {
                $('input[name="meta_url_type"]').prop('checked',false);
                $('#dispAddMetaURLTypeE').hide();

                $('#dispAddothersTextfieldE').hide();
                $('#dispAddFileMetaE').hide();
            }


            $(this).closest('.row').remove();
            rowMax25E++;
            rowMin25E--;
            if (rowMax25E == 0) {
                $('.addExp25E').prop('disabled',true);
            }else{
                $('.addExp25E').prop('disabled',false);
            }
        });

        $(document).on('change','.allcontactShowHideE',function(){
            var selectedallcontactvalue = $(this).val();
            if (selectedallcontactvalue == '[Others]') {
                $('#dispAddMetaURLTypeE').show();
            }

            if (selectedallcontactvalue == '[Document Name]') {
                $('#dispAddDocumentNameForAllE').show();
            }

        });

        $(document).on('change','.changeMetaRadioE',function(){
            var metaRadioValue = $(this).val();
            if (metaRadioValue == 0) {
                $('#dispAddFileMetaE').show();
                $('#dispAddothersTextfieldE').hide();
            }

            if (metaRadioValue == 1) {
                $('#dispAddFileMetaE').hide();
                $('#dispAddothersTextfieldE').show();
            }
        });

        $(document).on('click','.removeUrlE',function(){
            $('input[name="meta_url_type"]').prop('checked',false);
            $('#dispAddMetaURLTypeE').hide();
            $(this).closest('.col-md-12').hide();
        });
        $(document).on('click','.removeUrlfileE',function(){
            $('input[name="meta_url_type"]').prop('checked',false);
            $('#dispAddMetaURLTypeE').hide();
            $(this).closest('.row').hide();
        });

        $(document).on('click','.removeDocumentNameE', function(){
            $('#dispAddDocumentNameForAllE').hide();
            $(this).closest('.row').hide();
        });

    </script>





    {{-- <script>
        var rowMin = 0;
        var rowMax = 10;

        var select22;

        $(document).on('click','.addExp',function(){
            var html = "";
            html += '<div class="row"><div class="col-md-6"><div class="mb-3">';
            html += '<label class="form-label" for="meta_field_name'+rowMin+'">Field Variable <span class="text-danger">*</span></label>';
            html += '<select name="meta_field_name[]" id="meta_field_name'+rowMin+'" class="form-select select22"><option value="">Select</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="mb-3"><label class="form-label" for="assign_var_name'+rowMin+'">Field Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_name[]" id="assign_var_name'+rowMin+'" class="form-select select22"><option value="">Select</option><option value="otp">OTP</option><option value="booking_date">Booking Date</option><option value="candidate_name">Candidate Name</option><option value="candidate_contact_no">Candidate Contact No</option><option value="customer_name">Customer Name</option><option value="location">Customer City</option><option value="customer_phone_no">Customer Phone No</option><option value="order_reference_number">Order Reference Number</option><option value="partner_name">Partner Name</option><option value="partner_mobile_no">Partner Mobile No</option><option value="rec_office_name_eng">Recruitment Office English</option><option value="rec_office_name_ar">Recruitment Office Arabic</option><option value="partner_staff_concern_name_1">Partner Staff Concern Name 1</option><option value="partner_staff_concern_mobile_1">Partner Staff Concern Mobile 1</option><option value="partner_staff_concern_name_2">Partner Staff Concern Name 2</option><option value="partner_staff_concern_mobile_2">Partner Staff Concern Mobile 2</option><option value="partner_staff_concern_name_3">Partner Staff Concern Name 3</option><option value="partner_staff_concern_mobile_3">Partner Staff Concern Mobile 3</option><option value="partner_address_english">Partner Address English</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove float-end">Remove</button></div></div>';



            $('#dispEXP').append(html);


            rowMin++;
            rowMax--;
            if (rowMax == 1) {
                $('.addExp').prop('disabled',true);
            }else{
                $('.addExp').prop('disabled',false);
            }
        });
        $(document).on('click','.remove',function(){
            $(this).closest('.row').remove();
            rowMax++;
            rowMin--;
            if (rowMax == 0) {
                $('.addExp').prop('disabled',true);
            }else{
                $('.addExp').prop('disabled',false);
            }
        });
    </script> --}}

    {{-- <script>
        var rowMin2 = 0;
        var rowMax2 = 10;

        var select22;

        $(document).on('click','.addExp2',function(){
            var html = "";
            html += '<div class="row"><div class="col-md-6"><div class="mb-3">';
            html += '<label class="form-label" for="meta_field_name'+rowMin2+'">Field Variable <span class="text-danger">*</span></label>';
            html += '<select name="meta_field_name[]" id="meta_field_name'+rowMin2+'" class="form-select select22"><option value="">Select</option><option value="field_1">Field 1</option><option value="field_2">Field 2</option><option value="field_3">Field 3</option><option value="field_4">Field 4</option><option value="field_5">Field 5</option><option value="field_6">Field 6</option><option value="field_7">Field 7</option><option value="field_8">Field 8</option><option value="field_9">Field 9</option><option value="field_10">Field 10</option>';
            html += '</select></div></div>';

            html += '<div class="col-md-6"><div class="mb-3"><label class="form-label" for="assign_var_name'+rowMin2+'">Field Variable <span class="text-danger">*</span></label>';
            html += '<select name="assign_var_name[]" id="assign_var_name'+rowMin2+'" class="form-select select22"><option value="">Select</option><option value="otp">OTP</option><option value="booking_date">Booking Date</option><option value="candidate_name">Candidate Name</option><option value="candidate_contact_no">Candidate Contact No</option><option value="customer_name">Customer Name</option><option value="location">Customer City</option><option value="customer_phone_no">Customer Phone No</option><option value="order_reference_number">Order Reference Number</option><option value="partner_name">Partner Name</option><option value="partner_mobile_no">Partner Mobile No</option><option value="rec_office_name_eng">Recruitment Office English</option><option value="rec_office_name_ar">Recruitment Office Arabic</option><option value="partner_staff_concern_name_1">Partner Staff Concern Name 1</option><option value="partner_staff_concern_mobile_1">Partner Staff Concern Mobile 1</option><option value="partner_staff_concern_name_2">Partner Staff Concern Name 2</option><option value="partner_staff_concern_mobile_2">Partner Staff Concern Mobile 2</option><option value="partner_staff_concern_name_3">Partner Staff Concern Name 3</option><option value="partner_staff_concern_mobile_3">Partner Staff Concern Mobile 3</option><option value="partner_address_english">Partner Address English</option>';
            html += '</select></div></div>';
            html += '<div class="col-md-12"><button type="button" class="btn btn-sm btn-danger remove2 float-end">Remove</button></div></div>';



            $('#dispEXP2').append(html);


            rowMin2++;
            rowMax2--;
            if (rowMax2 == 1) {
                $('.addExp2').prop('disabled',true);
            }else{
                $('.addExp2').prop('disabled',false);
            }
        });
        $(document).on('click','.remove2',function(){
            $(this).closest('.row').remove();
            rowMax2++;
            rowMin2--;
            if (rowMax2 == 0) {
                $('.addExp2').prop('disabled',true);
            }else{
                $('.addExp2').prop('disabled',false);
            }
        });
    </script> --}}



    <script>
        $(document).ready(function(){
            $('#edituser').on('show.bs.offcanvas',function(e){
                var editID = $(e.relatedTarget).data('id');

                var imgPath = "{{ asset('admin/assets/images/template') }}";
                var blankImg = "{{ asset('admin/assets/img/avatars/blank.jpeg') }}";



                $('#editid').val(editID);

                $.ajaxSetup({
                    headers:{
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });



                jQuery.ajax({
                    url : '{{ route("admin.autometanotification.edit") }}',
                    method: "GET",
                    type: "json",
                    data: {
                        "id": editID,
                        // "_token": "{{ csrf_token() }}",
                    },
                    success: function(data){

                        // console.log('Edit data received:', data);

                        // Set template_for value and trigger change
                        $('#edit-template-for').val(data.template_for).trigger('change');

                        // Store the metatemp_id to set after dropdown is populated
                        $('#edit-meta-template').data('selected-value', data.metatemp_id);

                        // Set other fields
                        $('#edit-template-name').val(data.meta_template_name);
                        $('#edit-msg-body').val(data.meta_message_body);


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
        $('#add-template-for').on("change",function(){
            var template_for = $(this).val();

            jQuery.ajax({
                url: "{{ route('admin.metatemplate.getListAuto') }}",
                method: "GET",
                type: "html",
                data:{
                    template_for: template_for
                },
                success: function(data){
                    $('.dynamicmetatemplist').html(data.res);
                }
            });


        });

        $('#edit-template-for').on("change",function(){
            var template_for = $(this).val();
            var metaTemplateSelect = $('#edit-meta-template');

            // Clear current options and destroy select2
            metaTemplateSelect.empty().append('<option value="">Loading...</option>');

            jQuery.ajax({
                url: "{{ route('admin.metatemplate.getListAuto') }}",
                method: "GET",
                type: "json",
                data:{
                    template_for: template_for
                },
                success: function(data){
                    // $('.dynamicmetatemplistEdit').html(data.res);
                    if(data.res) {
                        metaTemplateSelect.html(data.res);
                    } else if(data.options) {
                        metaTemplateSelect.html(data.options);
                    } else {
                        // Handle if data is array of options
                        var options = '<option value="">Select Meta Template</option>';
                        if(Array.isArray(data)) {
                            data.forEach(function(item) {
                                options += '<option value="' + item.id + '">' + item.name + '</option>';
                            });
                        }
                        metaTemplateSelect.html(options);
                    }

                    // Set the previously selected value if exists
                    var selectedValue = metaTemplateSelect.data('selected-value');
                    if(selectedValue) {
                        metaTemplateSelect.val(selectedValue).trigger('change');
                        metaTemplateSelect.removeData('selected-value');
                    }
                }
            });


        });

        $('#add-meta-template').on("change", function(){
            var template_id = $(this).val();

            jQuery.ajax({
                url: "{{ route('admin.metatemplate.getListID') }}",
                method: 'GET',
                type: 'html',
                data:{
                    id: template_id
                },
                success: function(data){
                    // console.log(data);
                    $('#add-template-name').val(data.template_name);
                    $('#add-msg-body').val(data.whatsapp_message);
                }
            });

        });

        $('#edit-meta-template').on("change", function(){
            var template_id = $(this).val();

            jQuery.ajax({
                url: "{{ route('admin.metatemplate.getListID') }}",
                method: 'GET',
                type: 'html',
                data:{
                    id: template_id
                },
                success: function(data){
                    // console.log(data);
                    $('#edit-template-name').val(data.template_name);
                    $('#edit-msg-body').val(data.whatsapp_message);
                }
            });

        });
    });
</script>

<script>
    $(document).ready(function(){
        $('#statusChange').on('show.bs.modal',function(e){
            var tempchstID = $(e.relatedTarget).data('id');

            $('#tempchstID').val(tempchstID);

            jQuery.ajax({
                url : "{{ route('admin.autometanotification.getStatus') }}",
                method: "GET",
                type: "html",
                data: {
                    "id": tempchstID,
                },
                success: function(data){

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
