@extends('layout.admin.admin_layout')

@section('title','Fund & Advance - Dashboard')

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
    <style>
        .fa-stat-card { border-radius: 10px; padding: 16px; height: 100%; }
        .fa-stat-card .fa-stat-label { font-size: 12px; opacity: .85; }
        .fa-stat-card .fa-stat-value { font-size: 22px; font-weight: 600; margin-top: 4px; }
    </style>
@endsection

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    @include('admin.fund_advance.partials.subnav', ['active' => 'dashboard'])

    <div class="card mb-3">
        <div class="card-body d-flex flex-wrap align-items-end gap-2">
            @php $currentRange = request('range', 'month'); @endphp
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary range-btn {{ $currentRange === 'today' ? 'active' : '' }}" data-range="today">Today</button>
                <button type="button" class="btn btn-sm btn-outline-primary range-btn {{ $currentRange === 'week' ? 'active' : '' }}" data-range="week">This Week</button>
                <button type="button" class="btn btn-sm btn-outline-primary range-btn {{ $currentRange === 'month' ? 'active' : '' }}" data-range="month">This Month</button>
                <button type="button" class="btn btn-sm btn-outline-primary range-btn {{ $currentRange === 'year' ? 'active' : '' }}" data-range="year">This Year</button>
                <button type="button" class="btn btn-sm btn-outline-primary range-btn {{ $currentRange === 'all' ? 'active' : '' }}" data-range="all">All Time</button>
            </div>
            <div>
                <input type="text" id="custom-range" class="form-control form-control-sm" placeholder="Custom range" autocomplete="off" value="{{ $currentRange === 'custom' ? request('start_date').' - '.request('end_date') : '' }}">
            </div>
            <div>
                <select id="filter-type" class="form-select form-select-sm select2f">
                    <option value="">All Types</option>
                    @foreach (['Fund','Advance','Loan','Receivable','Payable'] as $t)
                        <option value="{{ $t }}" {{ request('transaction_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:220px">
                <select id="filter-employee" class="form-select form-select-sm select2f">
                    <option value="">All Employees</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" {{ (string) request('party_id') === (string) $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-sm btn-primary" id="apply-dashboard-filter">Apply</button>
        </div>
    </div>

    <div class="row g-3" id="dashboard-stats">
        @php
            $cards = [
                ['key' => 'funds_given', 'label' => 'Funds Given', 'color' => '#7367F0'],
                ['key' => 'funds_received', 'label' => 'Funds Received', 'color' => '#28C76F'],
                ['key' => 'advances_given', 'label' => 'Advances Given', 'color' => '#FF9F43'],
                ['key' => 'advances_received', 'label' => 'Advances Received', 'color' => '#00CFE8'],
                ['key' => 'advance_outstanding', 'label' => 'Advance Outstanding', 'color' => '#EA5455'],
                ['key' => 'loans_given', 'label' => 'Loans Given', 'color' => '#7367F0'],
                ['key' => 'loan_recovery', 'label' => 'Loan Recovery', 'color' => '#28C76F'],
                ['key' => 'loan_outstanding', 'label' => 'Loan Outstanding', 'color' => '#EA5455'],
                ['key' => 'receivables', 'label' => 'Receivables', 'color' => '#00CFE8'],
                ['key' => 'payables', 'label' => 'Payables', 'color' => '#FF9F43'],
                ['key' => 'pending_settlements', 'label' => 'Pending Settlements', 'color' => '#6C757D', 'count' => true],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="col-md-4 col-lg-3">
                <div class="fa-stat-card" style="background: {{ $card['color'] }}1a; color: {{ $card['color'] }};">
                    <div class="fa-stat-label">{{ $card['label'] }}</div>
                    <div class="fa-stat-value" id="stat-{{ $card['key'] }}">
                        {{ !empty($card['count']) ? number_format($stats[$card['key']]) : number_format($stats[$card['key']], 2) }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script>
    $(document).ready(function () {
        $('.select2f').each(function () {
            $(this).wrap('<div class="position-relative"></div>').select2({ dropdownParent: $(this).parent() });
        });

        $('#custom-range').daterangepicker({ autoUpdateInput: false, locale: { cancelLabel: 'Clear' } });
        $('#custom-range').on('apply.daterangepicker', function (e, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            $('.range-btn').removeClass('active');
            loadStats();
        });

        $('.range-btn').on('click', function () {
            $('.range-btn').removeClass('active');
            $(this).addClass('active');
            $('#custom-range').val('');
            loadStats();
        });

        $('#apply-dashboard-filter').on('click', loadStats);

        function loadStats() {
            var range = $('.range-btn.active').data('range') || 'month';
            var custom = $('#custom-range').val();
            var params = { range: custom ? 'custom' : range, transaction_type: $('#filter-type').val(), party_type: $('#filter-employee').val() ? 'employee' : '', party_id: $('#filter-employee').val() };
            if (custom) {
                var parts = custom.split(' - ');
                params.start_date = parts[0];
                params.end_date = parts[1];
            }
            window.location = "{{ route('admin.fund_advance.dashboard') }}?" + $.param(params);
        }
    });
    </script>
@endsection
