@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Transactions')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    @include('admin.fund_advance.partials.subnav', ['active' => 'transactions'])

    <div class="card">
        <div class="card-header border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <select id="pagination_list" class="form-select form-select-sm" style="width:auto">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <button class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#filterpanel"><i class="ti ti-filter me-0 me-sm-1 ti-xs"></i> Filter</button>
                <input type="text" id="search_text" class="form-control form-control-sm" placeholder="Search transaction no, party, amount...">
            </div>
            <div class="d-flex align-items-center gap-2">
                @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && ($permission->full_access == 1 || $permission->fund_advance_export == 1)))
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-label-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="ti ti-download"></i> Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item export-link" data-format="csv" href="javascript:void(0)">CSV</a>
                            <a class="dropdown-item export-link" data-format="pdf" href="javascript:void(0)">PDF</a>
                        </div>
                    </div>
                @endif
                @if ((Auth::guard('admin')->user()->user_type == 1) || (isset($permission) && ($permission->full_access == 1 || $permission->fund_advance_create == 1)))
                    <button class="btn btn-sm btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddTransaction">
                        <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add Transaction</span>
                    </button>
                @endif
            </div>
        </div>
        <div class="card-datatable table-responsive contactpaginate">
            @include('admin.fund_advance.transactions.indexload')
        </div>
    </div>

    {{-- Filter Modal --}}
    <div class="modal fade" id="filterpanel" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Transactions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Type</label>
                            <select id="by-type" class="form-select select2f">
                                <option value="">All</option>
                                @foreach (['Fund','Advance','Loan','Receivable','Payable'] as $t)
                                    <option value="{{ $t }}" {{ optional($savedFilter)->transaction_type === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nature</label>
                            <select id="by-nature" class="form-select select2f">
                                <option value="">All</option>
                                <option value="Given" {{ optional($savedFilter)->transaction_nature === 'Given' ? 'selected' : '' }}>Given</option>
                                <option value="Received" {{ optional($savedFilter)->transaction_nature === 'Received' ? 'selected' : '' }}>Received</option>
                                <option value="Adjustment" {{ optional($savedFilter)->transaction_nature === 'Adjustment' ? 'selected' : '' }}>Adjustment</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Party Type</label>
                            <select id="by-party-type" class="form-select select2f">
                                <option value="">All</option>
                                <option value="partner" {{ optional($savedFilter)->party_type === 'partner' ? 'selected' : '' }}>Partner</option>
                                <option value="contact" {{ optional($savedFilter)->party_type === 'contact' ? 'selected' : '' }}>Contact</option>
                                <option value="employee" {{ optional($savedFilter)->party_type === 'employee' ? 'selected' : '' }}>Employee</option>
                                <option value="other" {{ optional($savedFilter)->party_type === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select id="by-status" class="form-select select2f">
                                <option value="">All</option>
                                <option value="Pending" {{ optional($savedFilter)->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Partially Settled" {{ optional($savedFilter)->status === 'Partially Settled' ? 'selected' : '' }}>Partially Settled</option>
                                <option value="Settled" {{ optional($savedFilter)->status === 'Settled' ? 'selected' : '' }}>Settled</option>
                                <option value="Cancelled" {{ optional($savedFilter)->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Mode</label>
                            <select id="by-payment-mode" class="form-select select2f">
                                <option value="">All</option>
                                <option value="Cash" {{ optional($savedFilter)->payment_mode === 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Bank Transfer" {{ optional($savedFilter)->payment_mode === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="UPI" {{ optional($savedFilter)->payment_mode === 'UPI' ? 'selected' : '' }}>UPI</option>
                                <option value="Cheque" {{ optional($savedFilter)->payment_mode === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="Card" {{ optional($savedFilter)->payment_mode === 'Card' ? 'selected' : '' }}>Card</option>
                                <option value="Other" {{ optional($savedFilter)->payment_mode === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Created By</label>
                            <select id="by-created-by" class="form-select select2f">
                                <option value="">All</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ (string) optional($savedFilter)->created_by === (string) $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date Range</label>
                            <input type="text" id="by-date-range" class="form-control" placeholder="Select date range" autocomplete="off" value="{{ optional($savedFilter)->date_range }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount Min</label>
                            <input type="number" id="by-amount-min" class="form-control" step="0.01" value="{{ optional($savedFilter)->amount_min }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount Max</label>
                            <input type="number" id="by-amount-max" class="form-control" step="0.01" value="{{ optional($savedFilter)->amount_max }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning btn-sm resetfilter">Reset Filter</button>
                    <button type="button" class="btn btn-success btn-sm apply_filters">Save Filter</button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.fund_advance.transactions.form_offcanvas')

    {{-- View Modal --}}
    <div class="modal fade" id="viewTransactionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Transaction Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="transactionViewContent">Loading...</div>
            </div>
        </div>
    </div>

    {{-- Cancel Modal --}}
    <div class="modal fade" id="cancelTransactionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="cancelTransactionForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="cancel-id">
                        <label class="form-label">Reason for cancellation <span class="text-danger">*</span></label>
                        <textarea name="cancel_reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Cancel Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>

    <script>
    $(document).ready(function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') } });

        $('.select2t').each(function () {
            $(this).wrap('<div class="position-relative"></div>').select2({ dropdownParent: $(this).parent() });
        });

        // Static-option selects inside the Add/Edit offcanvas (Type, Nature, Party
        // Type, Payment Mode). Offcanvas panels are hidden via visibility/transform,
        // not display:none, so Select2 can compute their width correctly even before
        // the panel is opened - safe to initialize right away, unlike #filterpanel.
        $('.select2s').each(function () {
            $(this).wrap('<div class="position-relative"></div>').select2({ dropdownParent: $(this).parent(), width: '100%' });
        });

        // Filter modal's dropdowns are initialized on shown.bs.modal (not document
        // ready) because the modal is display:none at page load - Select2 can't
        // compute a correct width against a hidden element. Matches the same
        // shown.bs.modal + dropdownParent pattern the Expense filter panel uses.
        $('body').on('shown.bs.modal', '#filterpanel', function () {
            $(this).find('.select2f').each(function () {
                var $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) return;
                $el.wrap('<div class="position-relative"></div>').select2({ dropdownParent: $el.parent() });
            });
        });

        $('#by-date-range').daterangepicker({ autoUpdateInput: false, locale: { cancelLabel: 'Clear' } });
        $('#by-date-range').on('apply.daterangepicker', function (e, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }).on('cancel.daterangepicker', function () { $(this).val(''); });

        $('.txn-date').flatpickr();

        function getFilterData() {
            return {
                page_list: $('#pagination_list').val(),
                search_text: $('#search_text').val(),
                transaction_type: $('#by-type').val(),
                transaction_nature: $('#by-nature').val(),
                party_type: $('#by-party-type').val(),
                status: $('#by-status').val(),
                payment_mode: $('#by-payment-mode').val(),
                created_by: $('#by-created-by').val(),
                date_range: $('#by-date-range').val(),
                amount_min: $('#by-amount-min').val(),
                amount_max: $('#by-amount-max').val(),
            };
        }

        function reloadList() {
            $.ajax({
                url: "{{ route('admin.fund_advance.transactions.list') }}",
                method: "GET",
                data: getFilterData(),
                success: function (data) { $('.contactpaginate').html(data); }
            });
        }

        $('#pagination_list, #search_text').on('change input', function () { reloadList(); });

        // Save Filter: persist the current selections for this admin (so they're
        // pre-filled on the next visit) and apply them to the list immediately -
        // same dual persist+apply behavior as the Leads list's "Save Filter".
        $('.apply_filters').on('click', function () {
            $.post("{{ route('admin.fund_advance.transactions.saveFilter') }}", getFilterData(), function (res) {
                toastr.success(res.message || 'Filter saved successfully!', 'Success', { timeOut: 2000 });
            }).fail(function () {
                toastr.error('Failed to save filter', 'Error');
            });
            reloadList();
        });

        // Reset Filter: clear every filter control, persist the now-blank state, and
        // reload - same as Leads' "Reset Filter" (which also posts to the save
        // endpoint rather than a separate reset endpoint).
        $('.resetfilter').on('click', function () {
            $('#by-type, #by-nature, #by-party-type, #by-status, #by-payment-mode, #by-created-by').val('').trigger('change');
            $('#by-date-range, #by-amount-min, #by-amount-max').val('');

            $.post("{{ route('admin.fund_advance.transactions.saveFilter') }}", getFilterData(), function (res) {
                toastr.success(res.message || 'Filter reset successfully!', 'Success', { timeOut: 2000 });
            }).fail(function () {
                toastr.error('Failed to reset filter', 'Error');
            });
            reloadList();
        });

        $('body').on('click', '.pagination a', function (e) {
            e.preventDefault();
            var url = $(this).attr('href') + '&' + $.param(getFilterData());
            $.ajax({ url: url }).done(function (data) { $('.contactpaginate').html(data); });
        });

        $('.export-link').on('click', function () {
            var format = $(this).data('format');
            var url = "{{ url('admin/fund-advance/transactions/export') }}/" + format + '?' + $.param(getFilterData());
            window.location = url;
        });

        // Type -> Nature dynamic options
        var natureOptions = {
            'Fund': ['Given', 'Received'],
            'Advance': ['Given', 'Received'],
            'Loan': ['Given', 'Received'],
            'Receivable': ['Given', 'Adjustment'],
            'Payable': ['Given', 'Adjustment'],
        };

        function refreshNatureOptions(prefix, selected) {
            var type = $('#' + prefix + '-type').val();
            var $nature = $('#' + prefix + '-nature');
            $nature.empty();
            (natureOptions[type] || []).forEach(function (n) {
                $nature.append(new Option(n, n, false, n === selected));
            });
            // Select2 doesn't observe <option> DOM changes on its own; a manual
            // change re-renders the box so it reflects the rebuilt option list.
            $nature.trigger('change');
        }

        $(document).on('change', '#add-type', function () { refreshNatureOptions('add'); });
        $(document).on('change', '#edit-type', function () { refreshNatureOptions('edit'); });

        function togglePartyFields(prefix) {
            var type = $('#' + prefix + '-party-type').val();
            $('#' + prefix + '-party-select-wrap').toggle(type === 'partner' || type === 'contact' || type === 'employee');
            $('#' + prefix + '-party-name-wrap').toggle(type === 'other');
        }
        $(document).on('change', '#add-party-type', function () { togglePartyFields('add'); loadPartyOptions('add'); });
        $(document).on('change', '#edit-party-type', function () { togglePartyFields('edit'); loadPartyOptions('edit'); });

        function loadPartyOptions(prefix, selectedId, selectedText) {
            var type = $('#' + prefix + '-party-type').val();
            var $select = $('#' + prefix + '-party-id');
            $select.empty();
            if (!type || type === 'other') return;
            if (selectedId && selectedText) {
                $select.append(new Option(selectedText, selectedId, true, true));
            }
            $.get("{{ route('admin.fund_advance.parties.search') }}", { party_type: type }, function (res) {
                res.results.forEach(function (r) {
                    if (String(r.id) !== String(selectedId)) $select.append(new Option(r.text, r.id, false, false));
                });
            });
        }

        refreshNatureOptions('add');
        togglePartyFields('add');

        // Add Transaction Submit
        $('#addTransactionForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('admin.fund_advance.transactions.store') }}",
                method: 'POST', data: formData, processData: false, contentType: false,
                success: function () { window.location.reload(); },
                error: function (xhr) { showErrors(xhr, 'add'); }
            });
        });

        // Edit offcanvas populate
        $('#offcanvasEditTransaction').on('show.bs.offcanvas', function (e) {
            var id = $(e.relatedTarget).data('id');
            $('#edit-id').val(id);
            $.get("{{ route('admin.fund_advance.transactions.edit') }}", { id: id }, function (res) {
                var t = res.transaction;
                $('#edit-transaction-date').val(t.transaction_date);
                $('#edit-type').val(t.transaction_type).trigger('change');
                setTimeout(function () { $('#edit-nature').val(t.transaction_nature).trigger('change'); }, 50);
                $('#edit-party-type').val(t.party_type).trigger('change');
                $('#edit-party-name').val(t.party_name);
                $('#edit-amount').val(t.amount);
                $('#edit-payment-mode').val(t.payment_mode).trigger('change');
                $('#edit-reference-no').val(t.reference_no);
                $('#edit-description').val(t.description);
                setTimeout(function () { loadPartyOptions('edit', t.party_id, t.party_name); }, 50);
            });
        });

        $('#editTransactionForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('admin.fund_advance.transactions.update') }}",
                method: 'POST', data: formData, processData: false, contentType: false,
                success: function () { window.location.reload(); },
                error: function (xhr) { showErrors(xhr, 'edit'); }
            });
        });

        function showErrors(xhr, prefix) {
            $('.' + prefix + '-error').text('');
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function (field, messages) {
                    $('#' + prefix + '-' + field.replace(/_/g, '-') + '-error').text(messages[0]);
                });
            } else {
                toastr.error('Something went wrong. Please try again.');
            }
        }

        // View
        $(document).on('click', '.viewTransactionBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('admin.fund_advance.transactions.view', ':id') }}".replace(':id', id);
            $('#viewTransactionModal').modal('show');
            $('#transactionViewContent').html('<p class="text-muted text-center my-3">Loading...</p>');
            $.get(url, function (res) { $('#transactionViewContent').html(res); });
        });

        // Cancel
        $('#cancelTransactionModal').on('show.bs.modal', function (e) {
            $('#cancel-id').val($(e.relatedTarget).data('id'));
        });
        $('#cancelTransactionForm').on('submit', function (e) {
            e.preventDefault();
            $.post("{{ route('admin.fund_advance.transactions.cancel') }}", $(this).serialize(), function (res) {
                toastr.success(res.res);
                window.location.reload();
            }).fail(function (xhr) {
                toastr.error(xhr.responseJSON && xhr.responseJSON.res ? xhr.responseJSON.res : 'Unable to cancel transaction.');
            });
        });
    });
    </script>
@endsection
