@extends('worker.partner.layouts.portal')

@section('title', 'Orders')
@section('page-title', __('locale.Orders'))

@section('page-style')
    {{-- jQuery-dependent vendor widgets (bootstrap-daterangepicker, select2)
    matched to the admin Leads module as the reference implementation - kept
    scoped to this page only, not the shared portal layout, since the rest
    of the Worker Portal is vanilla JS with no jQuery/Bootstrap dependency. --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
    {{-- Must load AFTER the two vendor stylesheets above - see orders-vendor.css's
    own header comment for why (cascade order + most of the vendor styling being
    gated behind a Vuexy theme class this portal never adds). --}}
    <link rel="stylesheet" href="{{ asset('worker/css/orders-vendor.css') }}?v={{ @filemtime(public_path('worker/css/orders-vendor.css')) ?: time() }}">
@endsection

@section('content')
    <form data-order-filter-form data-endpoint="{{ route('worker.partner.orders') }}" class="wp-filter-bar wp-fade-in">
        {{-- Active Order / Cancelled Order (buttons in the results toolbar);
        sent with every search/filter/pagination request. --}}
        <input type="hidden" name="order_state" value="{{ $orderState }}" data-order-state-input>
        <div class="wp-filter-search">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="search" class="w-input" placeholder="{{ __('locale.Search by reference or candidate name') }}" value="{{ request('search') }}">
        </div>
        <select name="order_status" class="w-select select2" data-placeholder="{{ __('locale.All Statuses') }}" data-allow-clear="true">
            <option value=""></option>
            @foreach ($orderStatuses as $status)
                <option value="{{ $status->id }}" @selected(request('order_status') == $status->id)>{{ $status->display_status }}</option>
            @endforeach
        </select>
        <select name="payment_status" class="w-select select2" data-placeholder="{{ __('locale.Payment Status') }}" data-allow-clear="true">
            <option value=""></option>
            <option value="1" @selected(request('payment_status') === '1')>{{ __('locale.Paid') }}</option>
            <option value="0" @selected(request('payment_status') === '0')>{{ __('locale.Unpaid') }}</option>
        </select>
        <select name="profession_id" class="w-select select2" data-placeholder="{{ __('locale.All Professions') }}" data-allow-clear="true">
            <option value=""></option>
            @foreach ($professionOptions as $profession)
                <option value="{{ $profession->id }}" @selected(request('profession_id') == $profession->id)>{{ $profession->display_name }}</option>
            @endforeach
        </select>
        <select name="location_id" class="w-select select2" data-placeholder="{{ __('locale.Work Location') }}" data-allow-clear="true">
            <option value=""></option>
            @foreach ($locationOptions as $location)
                <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>{{ $location->display_name }}</option>
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
        @include('worker.partner.orders.partial', ['orders' => $orders, 'orderState' => $orderState])
    </div>

    @include('worker.partials.cancel-order-modal')
    {{-- Rendered here (outside #worker-orders-grid), not inside partial.blade.php,
    same reasoning as the Cancel Booking modal above it: that grid's innerHTML
    gets replaced wholesale on every AJAX filter/pagination refresh
    (order-filters.js), and order-vendor-init.js's Select2 init only runs
    once at page load - a modal living inside the swapped content would
    lose its Select2 instances (and duplicate itself) on every refresh. Only
    each card's own "Add Visa" trigger button lives in the swapped partial. --}}
    @include('worker.partner.orders.add-visa-modal', ['professionOptions' => $professionOptions, 'allWorkCities' => $allWorkCities])
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('worker/js/order-view-toggle.js') }}?v={{ @filemtime(public_path('worker/js/order-view-toggle.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/order-filters.js') }}?v={{ @filemtime(public_path('worker/js/order-filters.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/order-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/order-vendor-init.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/order-visa.js') }}?v={{ @filemtime(public_path('worker/js/order-visa.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/cancel-order.js') }}?v={{ @filemtime(public_path('worker/js/cancel-order.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/order-actions-menu.js') }}?v={{ @filemtime(public_path('worker/js/order-actions-menu.js')) ?: time() }}"></script>
@endsection
