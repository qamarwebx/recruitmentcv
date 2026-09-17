@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Fund Ledger')

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    @include('admin.fund_advance.partials.subnav', ['active' => 'ledgers'])

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.fund_advance.ledgers.party') }}">Party Ledger</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.fund_advance.ledgers.employee') }}">Employee Ledger</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.fund_advance.ledgers.fund') }}">Fund Ledger</a></li>
    </ul>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $start }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $end }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-2"><div class="card card-body"><small class="text-muted">Opening Balance</small><h6>{{ number_format($openingBalance, 2) }}</h6></div></div>
        <div class="col-md-2"><div class="card card-body"><small class="text-muted">Funds Received</small><h6 class="text-success">+ {{ number_format($fundsReceived, 2) }}</h6></div></div>
        <div class="col-md-2"><div class="card card-body"><small class="text-muted">Funds Given</small><h6 class="text-danger">- {{ number_format($fundsGiven, 2) }}</h6></div></div>
        <div class="col-md-2"><div class="card card-body"><small class="text-muted">Adjustments</small><h6>{{ number_format($adjustments, 2) }}</h6></div></div>
        <div class="col-md-2"><div class="card card-body"><small class="text-muted">Closing Balance</small><h6>{{ number_format($closingBalance, 2) }}</h6></div></div>
    </div>

    <div class="card">
        <div class="card-header">Fund Transactions ({{ \Carbon\Carbon::parse($start)->format('d-m-Y') }} to {{ \Carbon\Carbon::parse($end)->format('d-m-Y') }})</div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Date</th><th>Transaction No.</th><th>Party</th><th>Nature</th><th class="text-end">Amount</th><th>Created By</th></tr></thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ $row->transaction_date->format('d-m-Y') }}</td>
                            <td>{{ $row->transaction_no }}</td>
                            <td>{{ $row->party_display_name }}</td>
                            <td>{{ $row->transaction_nature }}</td>
                            <td class="text-end">{{ number_format($row->amount, 2) }}</td>
                            <td>{{ optional($row->creator)->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">No fund transactions in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
