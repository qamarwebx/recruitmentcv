@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Settlement Report')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    @include('admin.fund_advance.partials.subnav', ['active' => 'reports'])
    @include('admin.fund_advance.reports._tabs', ['active' => 'settlements'])

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select select2s">
                        <option value="">All</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Reversed" {{ request('status') === 'Reversed' ? 'selected' : '' }}>Reversed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Settlement Type</label>
                    <select name="settlement_type" class="form-select select2s">
                        <option value="">All</option>
                        <option value="settlement" {{ request('settlement_type') === 'settlement' ? 'selected' : '' }}>Settlement/Recovery</option>
                        <option value="adjustment" {{ request('settlement_type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date Range</label>
                    <input type="text" name="date_range" class="form-control" value="{{ request('date_range') }}" placeholder="MM/DD/YYYY - MM/DD/YYYY">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Settlement Report ({{ $rows->total() }} records)</span>
            <div class="btn-group">
                <a class="btn btn-sm btn-label-secondary" href="{{ route('admin.fund_advance.reports.export', array_merge(['report' => 'settlements', 'format' => 'csv'], request()->query())) }}"><i class="ti ti-download"></i> CSV</a>
                <a class="btn btn-sm btn-label-secondary" href="{{ route('admin.fund_advance.reports.export', array_merge(['report' => 'settlements', 'format' => 'pdf'], request()->query())) }}"><i class="ti ti-file-type-pdf"></i> PDF</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th><th>Settlement No.</th><th>Original Transaction</th><th>Party/Employee</th>
                        <th class="text-end">Original Amount</th><th class="text-end">Settlement Amount</th><th>Payment Mode</th><th>Reference</th><th>Created By</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row->settlement_date->format('d-m-Y') }}</td>
                            <td>{{ $row->settlement_no }}</td>
                            <td>{{ optional($row->transaction)->transaction_no }}</td>
                            <td>{{ optional($row->transaction)->party_display_name }}</td>
                            <td class="text-end">{{ number_format(optional($row->transaction)->amount, 2) }}</td>
                            <td class="text-end">{{ number_format($row->amount, 2) }}</td>
                            <td>{{ $row->payment_mode }}</td>
                            <td>{{ $row->reference_no ?: '-' }}</td>
                            <td>{{ optional($row->creator)->name ?? '-' }}</td>
                            <td><span class="badge bg-label-{{ $row->status === 'Active' ? 'success' : 'secondary' }}">{{ $row->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center py-4">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rows->count() > 0)
            <div class="px-4 py-3">{{ $rows->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}</div>
        @endif
    </div>
</div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
    $(document).ready(function () {
        $('.select2s').each(function () {
            $(this).wrap('<div class="position-relative"></div>').select2({ dropdownParent: $(this).parent(), width: '100%' });
        });
    });
    </script>
@endsection
