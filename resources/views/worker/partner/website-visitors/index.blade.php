@extends('worker.partner.layouts.portal')

@section('title', __('locale.Website Visitor'))
@section('page-title', __('locale.Website Visitor'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/apex-charts/apex-charts.css') }}">
    {{-- Same vendor widgets + overrides as the Orders page. --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('worker/css/orders-vendor.css') }}?v={{ @filemtime(public_path('worker/css/orders-vendor.css')) ?: time() }}">
@endsection

@section('content')
    @php
        $activeFilterCount = count(array_filter($filters));
    @endphp

    {{-- Same AJAX list as Orders (order-filters.js: [data-order-filter-form]
    + #worker-orders-grid + [data-order-loading]). The filter modal's fields
    belong to this form through form="visitorFilterForm". --}}
    <form id="visitorFilterForm" data-order-filter-form data-endpoint="{{ route('worker.partner.website-visitors') }}" class="wp-filter-bar wp-fade-in">
        <div class="wp-filter-search">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="search" class="w-input" placeholder="{{ __('locale.Search page, IP, browser, customer...') }}" value="{{ $search }}">
        </div>
        <button type="button" class="w-btn w-btn-primary w-btn-sm" data-filter-search>{{ __('locale.Search') }}</button>
        <button type="button" class="w-btn w-btn-outline w-btn-sm" data-visitor-filter-open>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-inline-end:5px;"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
            {{ __('locale.Filter') }}
            <span class="wp-filter-badge" data-visitor-filter-badge @if ($activeFilterCount === 0) hidden @endif>{{ $activeFilterCount }}</span>
        </button>
    </form>

    {{-- Website Visitor Filter (Employer filter modal markup; same filters as
    the CRM's, without Website/Partner - always this partner's website). --}}
    <div class="w-modal-backdrop" data-visitor-filter-backdrop></div>
    <div class="w-modal" data-visitor-filter-modal role="dialog" aria-modal="true" aria-labelledby="visitorFilterTitle">
        <div class="w-modal-dialog has-fixed-sections" style="max-width:640px;">
            <button type="button" class="w-modal-close" data-visitor-filter-close aria-label="{{ __('locale.Close') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
            <div class="w-modal-fixed-top">
                <h3 id="visitorFilterTitle" class="w-modal-title">{{ __('locale.Website Visitor Filter') }}</h3>
            </div>
            <div class="w-modal-scroll-area">
                <div class="w-form-alert" data-visitor-filter-alert hidden></div>
                <div class="w-form-grid wp-filter-modal-grid">
                    <div class="w-form-row">
                        <label class="w-form-label" for="by_custom_date">{{ __('locale.Date') }}</label>
                        <input type="text" name="by_custom_date" id="by_custom_date" form="visitorFilterForm" class="w-input wp-daterange" autocomplete="off"
                               placeholder="{{ __('locale.Select date range...') }}" value="{{ $filters['by_custom_date'] }}" data-visitor-filter-field>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label" for="by_visitor">{{ __('locale.Visitor') }}</label>
                        <select name="by_visitor" id="by_visitor" form="visitorFilterForm" class="w-select" data-visitor-filter-field>
                            <option value="">{{ __('locale.All Visitors') }}</option>
                            <option value="customers" @selected($filters['by_visitor'] === 'customers')>{{ __('locale.Logged-in customers') }}</option>
                            <option value="guests" @selected($filters['by_visitor'] === 'guests')>{{ __('locale.Guests') }}</option>
                        </select>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label" for="by_device">{{ __('locale.Device') }}</label>
                        <select name="by_device" id="by_device" form="visitorFilterForm" class="w-select" data-visitor-filter-field>
                            <option value="">{{ __('locale.All Devices') }}</option>
                            @foreach ($devices as $value => $label)
                                <option value="{{ $value }}" @selected($filters['by_device'] === $value)>{{ __('locale.' . $label) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label" for="by_browser">{{ __('locale.Browser') }}</label>
                        <select name="by_browser" id="by_browser" form="visitorFilterForm" class="w-select" data-visitor-filter-field>
                            <option value="">{{ __('locale.All Browsers') }}</option>
                            @foreach ($browsers as $browser)
                                <option value="{{ $browser }}" @selected($filters['by_browser'] === $browser)>{{ $browser }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="w-modal-fixed-bottom wp-filter-modal-actions">
                <button type="button" class="w-btn w-btn-outline" data-visitor-filter-reset data-url="{{ route('worker.partner.website-visitors.filter.reset') }}">
                    {{ __('locale.Reset Filter') }}
                </button>
                <button type="button" class="w-btn w-btn-outline" data-visitor-filter-save data-url="{{ route('worker.partner.website-visitors.filter.save') }}"
                        data-default-text="{{ __('locale.Save Filter') }}" data-loading-text="{{ __('locale.Saving…') }}">
                    {{ __('locale.Save Filter') }}
                </button>
                <button type="button" class="w-btn w-btn-primary" data-visitor-filter-apply>{{ __('locale.Apply Filter') }}</button>
            </div>
        </div>
    </div>

    <div class="w-loading" data-order-loading>
        <span class="w-spinner"></span> {{ __('locale.Loading...') }}
    </div>

    <div id="worker-orders-grid">
        @include('worker.partner.website-visitors.partial', ['visits' => $visits, 'uniqueVisitors' => $uniqueVisitors])
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('worker/js/order-filters.js') }}?v={{ @filemtime(public_path('worker/js/order-filters.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/order-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/order-vendor-init.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/partner-website-visitors.js') }}?v={{ @filemtime(public_path('worker/js/partner-website-visitors.js')) ?: time() }}"></script>
    {{-- Chart View (project's vendored ApexCharts). --}}
    <script src="{{ asset('admin/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('worker/js/partner-visitor-charts.js') }}?v={{ @filemtime(public_path('worker/js/partner-visitor-charts.js')) ?: time() }}"
        data-visitor-chart-i18n="{{ json_encode(['visits' => __('locale.Visits'), 'uniques' => __('locale.Unique visitors')]) }}"></script>
@endsection
