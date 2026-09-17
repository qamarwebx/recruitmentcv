@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Pending Settlements')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    @include('admin.fund_advance.partials.subnav', ['active' => 'settlements'])

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.fund_advance.settlements.pending') }}">Pending Settlements</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.fund_advance.settlements.history') }}">Settlement History</a></li>
    </ul>

    <div class="card">
        <div class="card-header border-bottom d-flex flex-wrap align-items-center gap-2">
            <select id="by-type" class="form-select form-select-sm select2s" style="width:auto">
                <option value="">All Types</option>
                @foreach (['Fund','Advance','Loan','Receivable','Payable'] as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
            <input type="text" id="search_text" class="form-control form-control-sm" placeholder="Search transaction no, party...">
        </div>
        <div class="card-datatable table-responsive contactpaginate">
            @include('admin.fund_advance.settlements.pendingload')
        </div>
    </div>

    {{-- Settlement / Adjustment Modal --}}
    <div class="modal fade" id="settleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="settleForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="settleModalTitle">Record Settlement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="transaction_id" id="settle-transaction-id">
                        <div class="mb-3">
                            <strong id="settle-transaction-label"></strong><br>
                            <span class="text-muted">Outstanding: <span id="settle-outstanding"></span></span>
                        </div>
                        <div class="mb-3">
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="settlement_type" id="settle-type-settlement" value="settlement" checked>
                                <label class="btn btn-outline-primary" for="settle-type-settlement">Settlement</label>
                                <input type="radio" class="btn-check" name="settlement_type" id="settle-type-adjustment" value="adjustment">
                                <label class="btn btn-outline-primary" for="settle-type-adjustment">Adjustment</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="text" name="settlement_date" class="form-control settle-date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="settle-amount" class="form-control" required>
                            <div class="invalid-feedback d-block text-danger small settle-amount-error"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="form-select select2f" required>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="UPI">UPI</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Card">Card</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reference No.</label>
                            <input type="text" name="reference_no" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Attachment</label>
                            <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script>
    $(document).ready(function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') } });
        $('.settle-date').flatpickr();

        // Page-level filter select - always visible, safe to init immediately.
        $('.select2s').each(function () {
            $(this).wrap('<div class="position-relative"></div>').select2({ dropdownParent: $(this).parent(), width: '100%' });
        });

        // Modal's Payment Mode select - deferred to shown.bs.modal since the modal
        // is display:none at page load (same reasoning as the Transactions filter
        // panel: Select2 can't compute a correct width against a hidden element).
        $('body').on('shown.bs.modal', '#settleModal', function () {
            $(this).find('.select2f').each(function () {
                var $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) return;
                $el.wrap('<div class="position-relative"></div>').select2({ dropdownParent: $el.parent(), width: '100%' });
            });
        });

        function getFilterData() {
            return { page_list: $('#pagination_list').val(), transaction_type: $('#by-type').val(), search_text: $('#search_text').val() };
        }
        function reloadList() {
            $.get("{{ route('admin.fund_advance.settlements.pending') }}", getFilterData(), function (data) { $('.contactpaginate').html(data); });
        }
        $('#by-type, #search_text').on('change input', reloadList);
        $('body').on('click', '.pagination a', function (e) {
            e.preventDefault();
            $.get($(this).attr('href') + '&' + $.param(getFilterData())).done(function (data) { $('.contactpaginate').html(data); });
        });

        $(document).on('click', '.settleBtn', function () {
            $('#settle-transaction-id').val($(this).data('id'));
            $('#settle-transaction-label').text($(this).data('label'));
            $('#settle-outstanding').text($(this).data('outstanding'));
            $('#settle-amount').attr('max', $(this).data('outstanding'));
            $('#settleModal').modal('show');
        });

        $('#settleForm').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('admin.fund_advance.settlements.store') }}",
                method: 'POST', data: formData, processData: false, contentType: false,
                success: function (res) { toastr.success(res.res); window.location.reload(); },
                error: function (xhr) {
                    $('.settle-amount-error').text('');
                    if (xhr.status === 422 && xhr.responseJSON) {
                        var msg = xhr.responseJSON.res || (xhr.responseJSON.errors && Object.values(xhr.responseJSON.errors)[0][0]);
                        $('.settle-amount-error').text(msg);
                    } else {
                        toastr.error('Something went wrong.');
                    }
                }
            });
        });
    });
    </script>
@endsection
