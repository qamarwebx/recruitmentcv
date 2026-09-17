{{--
    Shared filter bar + table + export controls for the Outstanding/Advances/Loans/
    Funds/Transactions reports. Expects: $rows (paginated), $employees, $reportKey,
    $reportRoute (route name), $showTypeFilter (bool).
--}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            @if ($showTypeFilter)
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="transaction_type" class="form-select select2s">
                        <option value="">All</option>
                        @foreach (['Fund','Advance','Loan','Receivable','Payable'] as $t)
                            <option value="{{ $t }}" {{ request('transaction_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-2">
                <label class="form-label">Nature</label>
                <select name="transaction_nature" class="form-select select2s">
                    <option value="">All</option>
                    <option value="Given" {{ request('transaction_nature') === 'Given' ? 'selected' : '' }}>Given</option>
                    <option value="Received" {{ request('transaction_nature') === 'Received' ? 'selected' : '' }}>Received</option>
                    <option value="Adjustment" {{ request('transaction_nature') === 'Adjustment' ? 'selected' : '' }}>Adjustment</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Party Type</label>
                <select name="party_type" class="form-select select2s">
                    <option value="">All</option>
                    <option value="partner" {{ request('party_type') === 'partner' ? 'selected' : '' }}>Partner</option>
                    <option value="contact" {{ request('party_type') === 'contact' ? 'selected' : '' }}>Contact</option>
                    <option value="employee" {{ request('party_type') === 'employee' ? 'selected' : '' }}>Employee</option>
                    <option value="other" {{ request('party_type') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Created By</label>
                <select name="created_by" class="form-select select2s">
                    <option value="">All</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" {{ (string) request('created_by') === (string) $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search_text" class="form-control" value="{{ request('search_text') }}" placeholder="Transaction no, party...">
            </div>
            <div class="col-md-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route($reportRoute) }}" class="btn btn-label-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ ucfirst($reportKey) }} Report ({{ $rows->total() }} records)</span>
        <div class="btn-group">
            <a class="btn btn-sm btn-label-secondary" href="{{ route('admin.fund_advance.reports.export', array_merge(['report' => $reportKey, 'format' => 'csv'], request()->query())) }}"><i class="ti ti-download"></i> CSV</a>
            <a class="btn btn-sm btn-label-secondary" href="{{ route('admin.fund_advance.reports.export', array_merge(['report' => $reportKey, 'format' => 'pdf'], request()->query())) }}"><i class="ti ti-file-type-pdf"></i> PDF</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th><th>Transaction No.</th><th>Party/Employee</th><th>Type</th><th>Nature</th>
                    <th class="text-end">Amount</th><th class="text-end">Settled</th><th class="text-end">Outstanding</th><th>Status</th><th>Created By</th>
                </tr>
            </thead>
            <tbody>
                @php $statusColors = ['Pending' => 'warning', 'Partially Settled' => 'info', 'Settled' => 'success', 'Cancelled' => 'secondary']; @endphp
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row->transaction_date->format('d-m-Y') }}</td>
                        <td>{{ $row->transaction_no }}</td>
                        <td>{{ $row->party_display_name }}</td>
                        <td>{{ $row->transaction_type }}</td>
                        <td>{{ $row->transaction_nature }}</td>
                        <td class="text-end">{{ number_format($row->amount, 2) }}</td>
                        <td class="text-end">{{ number_format($row->settled_amount ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($row->outstanding, 2) }}</td>
                        <td><span class="badge bg-label-{{ $statusColors[$row->computed_status] ?? 'secondary' }}">{{ $row->computed_status }}</span></td>
                        <td>{{ optional($row->creator)->name ?? '-' }}</td>
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

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
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
