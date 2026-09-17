@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Employee Ledger')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    @include('admin.fund_advance.partials.subnav', ['active' => 'ledgers'])

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.fund_advance.ledgers.party') }}">Party Ledger</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.fund_advance.ledgers.employee') }}">Employee Ledger</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.fund_advance.ledgers.fund') }}">Fund Ledger</a></li>
    </ul>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Employee</label>
                    <select name="admin_id" class="form-select select2f" required>
                        <option value="">Select Employee</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ (string) $adminId === (string) $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date Range</label>
                    <input type="text" name="date_range" id="date-range" class="form-control" value="{{ request('date_range') }}" autocomplete="off">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">View Ledger</button>
                </div>
            </form>
        </div>
    </div>

    @if ($employee)
        <div class="row g-3 mb-3">
            <div class="col-md-2"><div class="card card-body"><small class="text-muted">Advances</small><h6>{{ number_format($summary['advances'], 2) }}</h6></div></div>
            <div class="col-md-2"><div class="card card-body"><small class="text-muted">Loans</small><h6>{{ number_format($summary['loans'], 2) }}</h6></div></div>
            <div class="col-md-2"><div class="card card-body"><small class="text-muted">Settlements</small><h6>{{ number_format($summary['settlements'], 2) }}</h6></div></div>
            <div class="col-md-2"><div class="card card-body"><small class="text-muted">Recoveries</small><h6>{{ number_format($summary['recoveries'], 2) }}</h6></div></div>
            <div class="col-md-2"><div class="card card-body"><small class="text-muted">Receivables/Payables</small><h6>{{ number_format($summary['receivables'] + $summary['payables'], 2) }}</h6></div></div>
            <div class="col-md-2"><div class="card card-body"><small class="text-muted">Net Outstanding</small><h6>{{ number_format($summary['net_outstanding'], 2) }}</h6></div></div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Ledger - {{ $employee->name }}</span>
                <a class="btn btn-sm btn-label-secondary" href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"><i class="ti ti-download"></i> Export CSV</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Date</th><th>Reference</th><th>Type</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead>
                    <tbody>
                        <tr class="table-light"><td colspan="5"><strong>Opening Balance</strong></td><td class="text-end"><strong>{{ number_format($openingBalance, 2) }}</strong></td></tr>
                        @forelse ($rows as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                                <td>{{ $row['reference'] }}</td>
                                <td>{{ $row['type'] }}</td>
                                <td class="text-end">{{ $row['debit'] ? number_format($row['debit'], 2) : '-' }}</td>
                                <td class="text-end">{{ $row['credit'] ? number_format($row['credit'], 2) : '-' }}</td>
                                <td class="text-end">{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4">No transactions in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info">Select an employee to view their ledger.</div>
    @endif
</div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script>
    $(document).ready(function () {
        $('.select2f').each(function () { $(this).wrap('<div class="position-relative"></div>').select2({ dropdownParent: $(this).parent() }); });
        $('#date-range').daterangepicker({ autoUpdateInput: false, locale: { cancelLabel: 'Clear' } });
        $('#date-range').on('apply.daterangepicker', function (e, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        }).on('cancel.daterangepicker', function () { $(this).val(''); });
    });
    </script>
@endsection
