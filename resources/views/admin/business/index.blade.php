@extends('layout.admin.admin_layout')

@section('title','Todo Label')

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

@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card">
            
            <div class="card-datatable table-responsive">
                <table class="datatables-users table border-top">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!--- Add Business Start --->
    <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Business Type</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="addNewUserForm" action="{{ route('admin.business.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="add-name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="add-name" class="form-control" placeholder="Enter Name...">
                        </div>
                    </div>

                </div>
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>
    <!--- Add Business End --->

    <!--- Edit Business Start --->
    <div class="offcanvas offcanvas-size-xxl offcanvas-end" tabindex="-1" id="edituser" aria-labelledby="edituserLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="offcanvas-header">
            <h5 id="edituserLabel" class="offcanvas-title">Edit Todo Label</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
            <form class="add-new-user pt-0" id="editUserForm" action="{{ route('admin.business.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit-name" class="form-control" placeholder="Enter Name...">
                        </div>
                    </div>

                </div>
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>
    <!--- Edit Business End --->

    <!--- Delete Business Start --->
    <div class="modal fade" id="deleteBusiness" aria-hidden="true" aria-labelledby="deleteBusinessLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-l">
            <form id="deleteBusinessForm" action="{{ route('admin.business.delete') }}" method="POST">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id">
                <div class="modal-content">
                    <div class="modal-header pb-2">
                        <h5 class="modal-title" id="deleteBusinessLabel">Remove Business?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger">Are you sure you want to remove this business?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-success" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-sm btn-danger" id="confirmDelete">Yes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--- Delete Business End --->




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
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/pickr/pickr.js') }}"></script>

    <script src="{{ asset('admin/assets/js/forms-pickers.js') }}"></script>

    <!-- Page JS -->
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

            // userView = 'app-user-view-account.html',

            userView = assetPath + 'admin/contact-list/view',
            userPublish = assetPath + 'admin/candidate/publish/stage',

        statusObj = {
            1: { title: 'Pending', class: 'bg-label-warning' },
            2: { title: 'Active', class: 'bg-label-success' },
            3: { title: 'Inactive', class: 'bg-label-secondary' }
            };


        // Users datatable
        if (dt_user_table.length) {
            var dt_user = dt_user_table.DataTable({
            // ajax: assetsPath + 'json/user-list.json', // JSON file to add data
            "ajax":{
                "url"   : assetPath +"admin/business/list/json",
                "type"  : "POST",
                "data"  : {"_token":$('meta[name="csrf-token"]').attr('content')}
            },
            rowId: function(a) {
                return 'businessRow_' + a.id; // <-- for removing row dynamically
            },
            columns: [
                // columns according to JSON
                { data: 'id' },
                { data: 'name' },
                { data: 'action' }
            ],
            columnDefs: [
                {
                // For Responsive
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
                targets: 1,
                render: function(data, type, full, meta){
                    return '<span class="text-capitalized">'+full['name']+'</span>'
                }
                },

                {
                // Actions
                targets: -1,
                title: 'Action',
                searchable: false,
                orderable: false,
                render: function (data, type, full, meta) {
                    var id = full['id'];
                    return (
                    '<div class="d-flex align-items-center">' +
                    '<a href="javascript:;" data-bs-toggle="offcanvas" data-bs-target="#edituser" data-id="'+id+'" class="text-body editcontact edcandidate"><i class="ti ti-edit ti-sm me-2"></i></a>' +
                    '<a href="javascript:;" class="text-body delcontact delcandidate" data-bs-toggle="modal" data-bs-target="#deleteBusiness" data-id="'+id+'"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
                    '</div>'
                    );
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
                text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Business Type</span>',
                className: 'add-new btn btn-primary btn-sm addcontact addcandidate mx-1',
                attr: {
                    'data-bs-toggle': 'offcanvas',
                    'data-bs-target': '#offcanvasAddUser'
                }
                },

            ],

            // For responsive popup
            responsive: {
                details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function (row) {
                    var data = row.data();
                    return 'Details of ' + data['owner_name'];
                    }
                }),
                type: 'column',
                renderer: function (api, rowIdx, columns) {
                    var data = $.map(columns, function (col, i) {
                    return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
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

                // Adding plan filter once table initialized
                this.api().columns(6).every(function () {
                var column = this;
                var select = $('#by-job-type').on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                });
                });

                this.api().columns(7).every(function () {
                var column = this;
                var select = $('#by-pass-type').on('change', function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                });
                });

            }
            });
        }

        // Delete Record
        // $('.datatables-users tbody').on('click', '.delete-record', function () {
        //   dt_user.row($(this).parents('tr')).remove().draw();
        // });

        // Filter form control to default size
        // ? setTimeout used for multilingual table initialization
        setTimeout(() => {
            $('.dataTables_filter .form-control').removeClass('form-control-sm');
            $('.dataTables_length .form-select').removeClass('form-select-sm');
        }, 300);
        });
    
    </script>
    <!-- <script src="{{ asset('admin/assets/pages/app-business-list.js') }}"></script> -->

    <!-- Page Validate Page -->
    <script src="{{ asset('admin/assets/pages/validation/todo-label-validation.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('#edituser').on('show.bs.offcanvas',function(e){
                var id = $(e.relatedTarget).data('id');
                $('#edit_id').val(id);
                jQuery.ajax({
                    url : "{{ route('admin.business.edit') }}",
                    method: "GET",
                    type: "html",
                    data:{
                        id: id
                    },
                    success: function(data){
                        $('#edit-name').val(data.name);
                    }
                });
            });

            $('#deleteBusiness').on('show.bs.modal', function(e){
                var id = $(e.relatedTarget).data('id');
                $('#delete_id').val(id);
            });


        });
    </script>

    <script>
    $(document).on('submit', '#deleteBusinessForm', function(e){
        e.preventDefault();

        var form = $(this);
        var deleteId = $('#delete_id').val();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response){
                if(response.status === 'error'){
                    toastr.error(response.message, 'Error');
                } else if(response.status === 'success'){
                    toastr.success(response.message, 'Success');
                    $('#deleteBusiness').modal('hide');
                    // Remove the deleted row from datatable if using rowId
                    $('#businessRow_' + deleteId).remove();
                }
            },
            error: function(xhr){
                toastr.error('Something went wrong', 'Error');
                console.error(xhr.responseText);
            }
        });
    });

    // Populate hidden input when modal opens
    $('#deleteBusiness').on('show.bs.modal', function(e){
        var id = $(e.relatedTarget).data('id');
        $('#delete_id').val(id);
    });
    </script>


@endsection
