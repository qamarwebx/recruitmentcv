@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Settlement History')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    @include('admin.fund_advance.partials.subnav', ['active' => 'settlements'])

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.fund_advance.settlements.pending') }}">Pending Settlements</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.fund_advance.settlements.history') }}">Settlement History</a></li>
    </ul>

    <div class="card">
        <div class="card-header border-bottom d-flex flex-wrap align-items-center gap-2">
            <select id="by-status" class="form-select form-select-sm select2s" style="width:auto">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Reversed">Reversed</option>
            </select>
            <select id="by-settlement-type" class="form-select form-select-sm select2s" style="width:auto">
                <option value="">Settlement & Adjustment</option>
                <option value="settlement">Settlement Only</option>
                <option value="adjustment">Adjustment Only</option>
            </select>
        </div>
        <div class="card-datatable table-responsive contactpaginate">
            @include('admin.fund_advance.settlements.historyload')
        </div>
    </div>

    {{-- Reverse Modal --}}
    <div class="modal fade" id="reverseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="reverseForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Reverse Settlement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="reverse-id">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reversed_reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Reverse</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
<script>
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') } });

    $('.select2s').each(function () {
        $(this).wrap('<div class="position-relative"></div>').select2({ dropdownParent: $(this).parent(), width: '100%' });
    });

    function getFilterData() {
        return { page_list: $('#pagination_list').val(), status: $('#by-status').val(), settlement_type: $('#by-settlement-type').val() };
    }
    function reloadList() {
        $.get("{{ route('admin.fund_advance.settlements.history') }}", getFilterData(), function (data) { $('.contactpaginate').html(data); });
    }
    $('#by-status, #by-settlement-type').on('change', reloadList);
    $('body').on('click', '.pagination a', function (e) {
        e.preventDefault();
        $.get($(this).attr('href') + '&' + $.param(getFilterData())).done(function (data) { $('.contactpaginate').html(data); });
    });

    $('#reverseModal').on('show.bs.modal', function (e) {
        $('#reverse-id').val($(e.relatedTarget).data('id'));
    });
    $('#reverseForm').on('submit', function (e) {
        e.preventDefault();
        $.post("{{ route('admin.fund_advance.settlements.reverse') }}", $(this).serialize(), function (res) {
            toastr.success(res.res);
            window.location.reload();
        }).fail(function (xhr) {
            toastr.error(xhr.responseJSON && xhr.responseJSON.res ? xhr.responseJSON.res : 'Unable to reverse settlement.');
        });
    });
});
</script>
@endsection
