@extends('worker.partner.layouts.portal')

@php
    // Modals below (the live part computes its own copies - show-live.blade.php).
    $canVisa = \App\Support\PartnerTeam::allowsSection('orders', 'assign_visa');
    $canCancel = \App\Support\PartnerTeam::allowsSection('orders', 'cancel_booking');
@endphp

@section('title', 'Order #' . $order->reference_no)
@section('page-title', __('locale.Order') . ' #' . $order->reference_no)
@section('breadcrumb')
    <span>{{ __('locale.Workspace') }}</span>
    <svg class="wp-breadcrumbs-sep" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
    <a href="{{ route('worker.partner.orders') }}">{{ __('locale.Orders') }}</a>
    <svg class="wp-breadcrumbs-sep" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
    <span aria-current="page">#{{ $order->reference_no }}</span>
@endsection

@section('page-style')
    {{-- jQuery-dependent select2, scoped to this page only - same reasoning
    as the Orders listing page's own page-style section. --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')
    {{-- Order detail, kept in step with the CRM without a page reload: order-live.js asks
    PartnerPortalController::orderLive() every 30 s while the tab is visible and swaps in the
    re-rendered part only when the persisted data changed. --}}
    <p class="wp-live-status" data-order-live-status aria-live="polite" hidden></p>
    <div data-order-live data-live-url="{{ route('worker.partner.orders.show.live', $order->id) }}" data-version="{{ $liveVersion }}"
         data-text-updated="{{ __('locale.Updated with the latest order information.') }}" data-text-error="{{ __('locale.Could not refresh the order - showing the last loaded information. Retrying...') }}">
        @include('worker.partner.orders.show-live')
    </div>

    @if ($canCancel)
    @include('worker.partials.cancel-order-modal')
    @endif
    @if ($canVisa)
    @include('worker.partner.orders.add-visa-modal', ['professionOptions' => $professionOptions, 'allWorkCities' => $allWorkCities])
    @endif
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('worker/js/order-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/order-vendor-init.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/order-visa.js') }}?v={{ @filemtime(public_path('worker/js/order-visa.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/cancel-order.js') }}?v={{ @filemtime(public_path('worker/js/cancel-order.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/order-live.js') }}?v={{ @filemtime(public_path('worker/js/order-live.js')) ?: time() }}"></script>
@endsection
