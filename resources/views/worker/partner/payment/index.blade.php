@extends('worker.partner.layouts.portal')

@section('title', __('locale.Payment'))
@section('page-title', __('locale.Payment'))

@section('page-style')
    {{-- Same vendor widgets + overrides as the Orders page filter bar. --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('worker/css/orders-vendor.css') }}?v={{ @filemtime(public_path('worker/css/orders-vendor.css')) ?: time() }}">
@endsection

@section('content')
    {{-- Same AJAX filter bar as Orders: order-filters.js drives any
    [data-order-filter-form] + #worker-orders-grid + [data-order-loading]. --}}
    <form data-order-filter-form data-endpoint="{{ route('worker.partner.payment') }}" class="wp-filter-bar wp-fade-in">
        <div class="wp-filter-search">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="search" class="w-input" placeholder="{{ __('locale.Search by invoice number or employer') }}" value="{{ request('search') }}">
        </div>
        <select name="employer" class="w-select select2" data-placeholder="{{ __('locale.All Employers') }}" data-allow-clear="true">
            <option value=""></option>
            @foreach ($employers as $employer)
                <option value="{{ $employer }}" @selected(request('employer') === $employer)>{{ $employer }}</option>
            @endforeach
        </select>
        <select name="payment_status" class="w-select select2" data-placeholder="{{ __('locale.All Statuses') }}" data-allow-clear="true">
            <option value=""></option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ __('locale.' . $status) }}</option>
            @endforeach
        </select>
        <input type="text" name="date_range" class="w-input wp-daterange" placeholder="{{ __('locale.Select date range...') }}" value="{{ request('date_range') }}" autocomplete="off">
        <button type="button" class="w-btn w-btn-primary w-btn-sm" data-filter-search>{{ __('locale.Search') }}</button>
        <button type="button" class="w-btn w-btn-outline w-btn-sm" data-filter-reset>{{ __('locale.Reset') }}</button>
    </form>

    <div class="w-loading" data-order-loading>
        <span class="w-spinner"></span> {{ __('locale.Loading...') }}
    </div>

    <div id="worker-orders-grid">
        @include('worker.partner.payment.partial', ['invoices' => $invoices])
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('worker/js/order-filters.js') }}?v={{ @filemtime(public_path('worker/js/order-filters.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/order-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/order-vendor-init.js')) ?: time() }}"></script>
@endsection
