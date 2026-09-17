@extends('layout.admin.admin_layout')

@section('title','All Contact Email Export History')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />

    <style>
        .filterpanel {
            position: relative;
        }

        .filter-indicator {
            position: absolute;
            top: 1px;
            right: 2px;
            width: 8px;
            height: 8px;
            background: #ffc107;
            border-radius: 50%;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Contact Email Export History</h5>
            </div>
            <div class="card-datatable table-responsive contactpaginate">
                @include('admin.allcontact_email_portal_export_history.load')
            </div>
        </div>

    </div>

    <!-- Rendered by JS into the DataTable's own length/pagination control area,
         matching the existing Contact Plus / Leads placement convention. -->
    <div id="acehToolbarTemplate" class="d-none">
        <div class="custom-toolbar px-3 float-start">
            <button class="btn btn-xs btn-primary filterpanel" type="button" data-bs-toggle="modal" data-bs-target="#acehFilterModal">
                <i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter
                <span class="filter-indicator d-none"></span>
            </button>

            <div class="btn-group mx-2">
                <button class="btn btn-xs btn-primary dropdown-toggle" type="button" id="acehOptionsBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-settings me-0 me-sm-1 ti-xs"></i> Options
                </button>
                <div class="dropdown-menu" aria-labelledby="acehOptionsBtn">
                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#acehShowHideColumnsModal">
                        Show / Hide Columns
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="acehBulkTemplate" class="d-none">
        <div class="bulk-action-toolbar me-2">
            <button type="button" class="btn btn-danger btn-sm bulkactions" id="bulkDeleteBtn" data-bs-toggle="modal" data-bs-target="#bulkDeleteHistoryModal" disabled>
                <i class="ti ti-trash me-1"></i>Delete Selected
            </button>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="acehFilterModal" aria-hidden="true" aria-labelledby="acehFilterModalLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="offcanvas-title" id="acehFilterModalLabel">Filter Export History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="aceh_status">Status</label>
                            <select id="aceh_status" class="form-select select22f" multiple data-placeholder="Select Status">
                                @foreach ($statusOptions as $statusOption)
                                    <option value="{{ $statusOption }}" @if(in_array($statusOption, $savedStatuses)) selected @endif>
                                        {{ ucwords(str_replace('_', ' ', $statusOption)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if ($isSuperAdmin)
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="aceh_admin">Admin</label>
                                <select id="aceh_admin" class="form-select select22f" multiple data-placeholder="Select Admin">
                                    @foreach ($adminOptions as $adminOption)
                                        <option value="{{ $adminOption->id }}" @if(in_array($adminOption->id, $savedAdminIds)) selected @endif>
                                            {{ $adminOption->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="aceh_list_name">List Name</label>
                            <input type="text" id="aceh_list_name" class="form-control" placeholder="Search list name..." value="{{ optional($savedFilter)->list_name }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="aceh_business_type">Business Type</label>
                            <select id="aceh_business_type" class="form-select select22f" multiple data-placeholder="Select Business Type">
                                @foreach ($businessTypeOptions as $businessTypeOption)
                                    <option value="{{ $businessTypeOption }}" @if(in_array($businessTypeOption, $savedBusinessTypes)) selected @endif>
                                        {{ $businessTypeOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="aceh_created_date">Created Date</label>
                            <input name="created_date" type="text" id="aceh_created_date" class="form-control bsdatpicket" value="{{ optional($savedFilter)->created_date }}" placeholder="Created Date Range...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning btn-sm" id="acehResetFilterBtn">Reset Filter</button>
                    <button type="button" class="btn btn-success btn-sm" id="acehSaveFilterBtn">Save Filter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Show / Hide Columns Modal -->
    <div class="modal fade" id="acehShowHideColumnsModal" tabindex="-1" aria-labelledby="acehShowHideColumnsModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="acehShowHideColumnsModalLabel">Show / Hide Columns</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="acehColumnToggles" class="row g-2">
                        <!-- Column toggle checkboxes will be dynamically injected here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Summary Modal -->
    <div class="modal fade" id="acehErrorModal" tabindex="-1" aria-labelledby="acehErrorModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="acehErrorModalLabel">Error Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre id="acehErrorModalBody" class="mb-0" style="white-space: pre-wrap; word-break: break-word;"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Confirm Modal -->
    <div class="modal fade" id="bulkDeleteHistoryModal" aria-hidden="true" aria-labelledby="bulkDeleteHistoryModalLabel" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="offcanvas-title" id="bulkDeleteHistoryModalLabel">Delete Export History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger mb-0">Are you sure you want to delete the selected export history record(s)?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteHistoryConfirmBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>

    <script>
        var acehTable;
        var acehLastCheckedIndex = null;

        function acehGetFilterData() {
            return {
                status: $('#aceh_status').val(),
                filter_admin_id: $('#aceh_admin').length ? $('#aceh_admin').val() : null,
                list_name: $('#aceh_list_name').val(),
                business_type: $('#aceh_business_type').val(),
                created_date: $('#aceh_created_date').val()
            };
        }

        function acehUpdateFilterIndicator() {
            var isFiltered =
                ($('#aceh_status').val() && $('#aceh_status').val().length > 0) ||
                ($('#aceh_admin').length && $('#aceh_admin').val() && $('#aceh_admin').val().length > 0) ||
                $('#aceh_list_name').val() ||
                ($('#aceh_business_type').val() && $('#aceh_business_type').val().length > 0) ||
                $('#aceh_created_date').val();

            if (isFiltered) {
                $('.filterpanel .filter-indicator').removeClass('d-none').addClass('d-block');
            } else {
                $('.filterpanel .filter-indicator').removeClass('d-block').addClass('d-none');
            }
        }

        function acehInitializeColumnToggles() {
            var $toggleContainer = $('#acehColumnToggles');

            if (!$toggleContainer.length || !acehTable) {
                return;
            }

            $toggleContainer.html('');

            acehTable.columns().every(function (index) {
                var column = this;
                var columnName = $(column.header()).text().trim();

                if (!columnName) {
                    return;
                }

                var stored = localStorage.getItem('ac_exp_hist_col_' + index);
                var isVisible = stored === null ? true : stored === 'true';

                column.visible(isVisible);

                $toggleContainer.append(
                    '<div class="form-check mb-2 col-md-6">' +
                        '<input class="form-check-input aceh-toggle-column" type="checkbox" id="aceh_col_' + index + '" data-column="' + index + '" ' + (isVisible ? 'checked' : '') + '>' +
                        '<label class="form-check-label" for="aceh_col_' + index + '">' + columnName + '</label>' +
                    '</div>'
                );
            });

            $(document).off('change', '.aceh-toggle-column').on('change', '.aceh-toggle-column', function () {
                var columnIndex = $(this).data('column');
                var visible = $(this).is(':checked');

                acehTable.column(columnIndex).visible(visible);
                localStorage.setItem('ac_exp_hist_col_' + columnIndex, visible);
            });
        }

        // Reapply the stored per-column visibility on every redraw so it survives
        // pagination/filter/search redraws (DataTables can otherwise reset a
        // column's visible state on its own internal recalculations).
        function acehApplyStoredColumnVisibility() {
            if (!acehTable) {
                return;
            }

            acehTable.columns().every(function (index) {
                var stored = localStorage.getItem('ac_exp_hist_col_' + index);

                if (stored !== null) {
                    this.visible(stored === 'true', false);
                }
            });
        }

        $(function () {

            acehTable = $('.datatables-users').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ordering: false,
                destroy: true,
                pageLength: 10,

                ajax: {
                    url: "{{ route('admin.email_qamr_portal.allcontact.history.json') }}",
                    type: "GET",
                    data: function (d) {
                        var filters = acehGetFilterData();
                        d.status = filters.status;
                        d.filter_admin_id = filters.filter_admin_id;
                        d.list_name = filters.list_name;
                        d.business_type = filters.business_type;
                        d.created_date = filters.created_date;
                    }
                },

                columns: [
                    { data: 'checkbox', name: 'checkbox', searchable: false, orderable: false },
                    { data: 'id', name: 'id' },
                    { data: 'admin_name', name: 'admin_name' },
                    { data: 'list_name', name: 'list_name' },
                    { data: 'list_uid', name: 'list_uid' },
                    { data: 'business_type', name: 'business_type' },
                    { data: 'industry_names', name: 'industry_names' },
                    { data: 'total_records', name: 'total_records' },
                    { data: 'progress', name: 'progress', orderable: false, searchable: false },
                    { data: 'status', name: 'status' },
                    { data: 'started_at', name: 'started_at' },
                    { data: 'completed_at', name: 'completed_at' },
                    { data: 'last_refreshed_at', name: 'last_refreshed_at' },
                    { data: 'error_message', name: 'error_message', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],

                initComplete: function () {

                    acehInitializeColumnToggles();

                    // Remove "Show" and "entries" text, keep just the length select
                    $('#acehTable_length label').contents().filter(function () {
                        return this.nodeType === 3;
                    }).remove();

                    var $length = $('#acehTable_length');

                    if (!$length.find('.custom-toolbar').length) {
                        $length.append($('#acehToolbarTemplate').html());
                    }

                    $length.css({
                        display: 'flex',
                        'flex-direction': 'row',
                        'align-items': 'center',
                        'flex-wrap': 'wrap',
                        gap: '10px'
                    });

                    $length.find('label').css({ order: 1, margin: 0 });
                    $length.find('.custom-toolbar').css({ order: 2 });

                    if (!$('#acehTable_filter .bulk-action-toolbar').length) {
                        $('#acehTable_filter label').after($('#acehBulkTemplate').html());
                    }

                    $('#acehTable_filter').css({
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'end',
                        gap: '10px'
                    });

                    acehUpdateFilterIndicator();
                },

                drawCallback: function () {
                    $('.checkboxSelectAll').prop('checked', false).prop('indeterminate', false);
                    $('.bulkactions').prop('disabled', true);
                    acehLastCheckedIndex = null;
                    acehApplyStoredColumnVisibility();
                }
            });

        });

        $('#acehShowHideColumnsModal').on('shown.bs.modal', function () {
            acehInitializeColumnToggles();
        });

        $('body').on('shown.bs.modal', '#acehFilterModal', function () {
            $(this).find('.select22f').each(function () {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        dropdownParent: $(this).parent()
                    });
                }
            });
        });

        $(function () {
            var $dateRange = $('.bsdatpicket');

            if ($dateRange.length) {
                $dateRange.daterangepicker({
                    opens: (typeof isRtl !== 'undefined' && isRtl) ? 'left' : 'right',
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });
            }

            $dateRange.on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            }).on('cancel.daterangepicker', function () {
                $(this).val('');
            });
        });

        $('#acehSaveFilterBtn').on('click', function () {
            var data = acehGetFilterData();
            data._token = "{{ csrf_token() }}";

            $.post("{{ route('admin.email_qamr_portal.allcontact.history.filter.save') }}", data, function (res) {
                toastr.success(res.message || 'Filter saved successfully!');
            }).fail(function () {
                toastr.error('Failed to save filter.');
            });

            acehTable.ajax.reload(null, false);
            acehUpdateFilterIndicator();
            $('#acehFilterModal').modal('hide');
        });

        $('#acehResetFilterBtn').on('click', function () {
            $('#aceh_status').val(null).trigger('change');
            $('#aceh_admin').val(null).trigger('change');
            $('#aceh_list_name').val('');
            $('#aceh_business_type').val(null).trigger('change');
            $('#aceh_created_date').val('');

            $.post("{{ route('admin.email_qamr_portal.allcontact.history.filter.reset') }}", { _token: "{{ csrf_token() }}" }, function (res) {
                toastr.success(res.message || 'Filter reset successfully!');
            }).fail(function () {
                toastr.error('Failed to reset filter.');
            });

            acehTable.ajax.reload(null, false);
            acehUpdateFilterIndicator();
            $('#acehFilterModal').modal('hide');
        });

        $(document).on('click', '.reload-email-qamr-export', function () {
            var id = $(this).data('id');
            var $tr = $(this).closest('tr');

            $.ajax({
                url: '/admin/email-qamr-portal/allcontact/export-history/' + id + '/refresh',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        acehTable.row($tr).data(response.row).draw(false);
                        toastr.success('Row refreshed.');
                    } else {
                        toastr.error(response.message || 'Unable to refresh this record.');
                    }
                },
                error: function () {
                    toastr.error('Unable to refresh this record.');
                }
            });
        });

        $(document).on('click', '.view-error-summary', function () {
            $('#acehErrorModalBody').text($(this).data('error') || '');
            $('#acehErrorModal').modal('show');
        });

        $(document).on('click', '.delete-email-qamr-export', function () {

            if (!confirm('Are you sure you want to delete this export history record?')) {
                return;
            }

            let id = $(this).data('id');

            $.ajax({
                url: '/admin/email-qamr-portal/allcontact/export-history/' + id,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    acehTable.ajax.reload(null, false);
                    toastr.success(response.message);
                },
                error: function () {
                    toastr.error('Something went wrong.');
                }
            });

        });

        // Select all / per-row checkbox toggling, same convention used across
        // the admin's other DataTables bulk-action pages (Contact Plus, All Contact).
        $(document).on('click', '.checkboxSelectAll', function () {
            var listcheckitem = $('.listitem :checkbox');
            var isMasterChecked = $(this).is(':checked');

            listcheckitem.prop('checked', isMasterChecked);
            $('.bulkactions').prop('disabled', !isMasterChecked || listcheckitem.length === 0);
        });

        $(document).on('change', '.listitem :checkbox', function () {
            var listcheckitem = $('.listitem :checkbox');
            var masterCheck = $('.checkboxSelectAll');
            var totalItems = listcheckitem.length;
            var checkedItems = listcheckitem.filter(':checked').length;

            if (totalItems === checkedItems && totalItems > 0) {
                masterCheck.prop('indeterminate', false).prop('checked', true);
                $('.bulkactions').prop('disabled', false);
            } else if (checkedItems > 0) {
                masterCheck.prop('indeterminate', true);
                $('.bulkactions').prop('disabled', false);
            } else {
                masterCheck.prop('indeterminate', false).prop('checked', false);
                $('.bulkactions').prop('disabled', true);
            }
        });

        // Shift+click a row's checkbox to select every row between it and the
        // last-clicked one (feeds the bulk-delete above).
        $(document).on('click', '.listitem :checkbox', function (e) {
            var checkboxes = $('.listitem :checkbox').toArray();
            var currentIndex = checkboxes.indexOf(this);

            if (e.shiftKey && acehLastCheckedIndex !== null && acehLastCheckedIndex !== currentIndex) {
                var start = Math.min(acehLastCheckedIndex, currentIndex);
                var end = Math.max(acehLastCheckedIndex, currentIndex);
                var isChecked = $(this).is(':checked');

                for (var i = start; i <= end; i++) {
                    $(checkboxes[i]).prop('checked', isChecked);
                }

                $(this).trigger('change');
            }

            acehLastCheckedIndex = currentIndex;
        });

        $('#bulkDeleteHistoryConfirmBtn').on('click', function () {
            var ids = [];

            $('.dt-checkboxes:checked').each(function () {
                ids.push($(this).data('id'));
            });

            if (!ids.length) {
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('admin.email_qamr_portal.allcontact.history.bulk_delete') }}",
                type: 'POST',
                data: {
                    ids: ids,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    $('#bulkDeleteHistoryModal').modal('hide');
                    acehTable.ajax.reload(null, false);
                    toastr.success(response.message);
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong.';
                    toastr.error(msg);
                },
                complete: function () {
                    $btn.prop('disabled', false);
                }
            });
        });
    </script>
@endsection
