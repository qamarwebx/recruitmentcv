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
    {{-- Search/filter controls only with Orders > Search/Filter (a team
    member; App\Support\PartnerTeam). The form itself stays - it carries
    order_state for the Active/Cancelled switch and pagination. --}}
    @php
        $canFilterOrders = \App\Support\PartnerTeam::allowsSection('orders', 'search_filter');
        $canAssignVisa = \App\Support\PartnerTeam::allowsSection('orders', 'assign_visa');
        // Needs attention = Active orders (not cancelled, no visa yet - the Active tab's
        // own rule): the newest one on this page is the next to act on.
        $attentionOrder = $orderState === 'active' ? $orders->first() : null;
        $attentionCount = $orderCounts['active'] ?? 0;
        $portalPartner = \Illuminate\Support\Facades\Auth::guard('partner')->user();
        $portalUrl = \App\Support\PartnerDomains::primaryUrl(optional($portalPartner)->domain);
    @endphp

    @if ($attentionOrder && $attentionCount > 0)
        <div class="wp-attention wp-fade-in" data-attention-banner data-attention-key="{{ $attentionOrder->id }}" role="status">
            <span class="wp-attention-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/></svg>
            </span>
            <div class="wp-attention-text">
                <strong>{{ trans_choice('locale.:count order needs your attention|:count orders need your attention', $attentionCount, ['count' => $attentionCount]) }}</strong>
                <span>{{ $attentionOrder->payment_status
                    ? __('locale.Order #:ref is waiting for visa assignment.', ['ref' => $attentionOrder->reference_no])
                    : __('locale.Order #:ref is waiting for visa assignment and payment confirmation.', ['ref' => $attentionOrder->reference_no]) }}</span>
            </div>
            @if ($canAssignVisa)
                <button type="button" class="w-btn w-btn-accent"
                    data-order-visa-trigger
                    data-visa-url="{{ route('worker.partner.orders.visa.store', $attentionOrder->id) }}"
                    data-employer-name="{{ $attentionOrder->employer_name }}"
                    data-employer-ar-name="{{ $attentionOrder->employer_ar_name }}"
                    data-visa-no="{{ $attentionOrder->visa_no }}"
                    data-id-no="{{ $attentionOrder->id_no }}"
                    data-proff-id="{{ $attentionOrder->visa_proff_id }}"
                    data-issuing-authority="{{ $attentionOrder->issuing_authority }}"
                    data-wpcity-id="{{ $attentionOrder->wpcity_id }}"
                    data-salary="{{ $attentionOrder->salary }}"
                    data-exp-sal="{{ $attentionOrder->exp_sal }}">{{ __('locale.Assign Visa now') }}</button>
            @endif
            <button type="button" class="wp-attention-close" data-attention-dismiss aria-label="{{ __('locale.Dismiss') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- KPI cards: the tab counts PartnerPortalController::orders() already computes. --}}
    <div class="wp-kpi-grid wp-fade-in">
        @if (array_key_exists('active', $orderCounts))
            <div class="wp-kpi is-dark">
                <div class="wp-kpi-head">
                    <span class="wp-kpi-label">{{ __('locale.Active orders') }}</span>
                    <span class="wp-kpi-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg></span>
                </div>
                <div class="wp-kpi-value"><strong>{{ $orderCounts['active'] }}</strong><span class="is-gold">{{ __('locale.awaiting visa') }}</span></div>
            </div>
        @endif
        @if (array_key_exists('deployed', $orderCounts))
            <div class="wp-kpi">
                <div class="wp-kpi-head">
                    <span class="wp-kpi-label">{{ __('locale.Deployed') }}</span>
                    <span class="wp-kpi-icon is-success"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></span>
                </div>
                <div class="wp-kpi-value"><strong>{{ $orderCounts['deployed'] }}</strong><span>{{ __('locale.visa assigned') }}</span></div>
            </div>
        @endif
        @if (array_key_exists('cancelled', $orderCounts))
            <div class="wp-kpi">
                <div class="wp-kpi-head">
                    <span class="wp-kpi-label">{{ __('locale.Cancelled') }}</span>
                    <span class="wp-kpi-icon is-critical"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M18 6 6 18M6 6l12 12"/></svg></span>
                </div>
                <div class="wp-kpi-value"><strong>{{ $orderCounts['cancelled'] }}</strong><span>{{ __('locale.cancelled orders') }}</span></div>
            </div>
        @endif
    </div>

    <form data-order-filter-form data-endpoint="{{ route('worker.partner.orders') }}" class="wp-filter-bar wp-filter-panel wp-fade-in" @unless ($canFilterOrders) hidden @endunless>
        {{-- Active Order / Cancelled Order (buttons in the results toolbar);
        sent with every search/filter/pagination request. --}}
        <input type="hidden" name="order_state" value="{{ $orderState }}" data-order-state-input>
        @if ($canFilterOrders)
        <div class="wp-filter-row">
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
        </div>
        <div class="wp-filter-row">
            <div class="wp-filter-date">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <input type="text" name="date_range" class="w-input wp-daterange" placeholder="{{ __('locale.Select date range...') }}" value="{{ request('date_range') }}" autocomplete="off">
            </div>
            {{-- Quick ranges: fill the same date_range field (MM/DD/YYYY - MM/DD/YYYY) and search. --}}
            <div class="wp-quick-dates" role="group" aria-label="{{ __('locale.Quick date ranges') }}">
                <button type="button" class="wp-chip" data-quick-date="0">{{ __('locale.Today') }}</button>
                <button type="button" class="wp-chip" data-quick-date="6">{{ __('locale.7 days') }}</button>
                <button type="button" class="wp-chip" data-quick-date="29">{{ __('locale.30 days') }}</button>
            </div>
            <div class="wp-filter-actions">
                <button type="button" class="w-btn w-btn-primary" data-filter-search>{{ __('locale.Apply filters') }}</button>
                <button type="button" class="w-btn w-btn-outline" data-filter-reset>{{ __('locale.Reset') }}</button>
            </div>
        </div>
        @endif
    </form>

    @include('worker.partials.hiring-limit-notice', ['hlClass' => 'wp-hiring-limit-page'])

    {{-- Update Payment success / error feedback (order-payment.js) - outside the AJAX-swapped grid. --}}
    <div class="w-form-alert" data-order-payment-feedback role="status" aria-live="polite" hidden style="margin-bottom:14px;"></div>

    <div class="w-loading" data-order-loading>
        <span class="w-spinner"></span> {{ __('locale.Loading...') }}
    </div>

    <div class="wp-orders-body">
        <div id="worker-orders-grid" class="wp-orders-main">
            @include('worker.partner.orders.partial', ['orders' => $orders, 'orderState' => $orderState])
        </div>

        <aside class="wp-orders-side">
            {{-- Recent activity: the newest orders on this list (existing booking data). --}}
            <section class="w-info-card wp-side-card">
                <div class="wp-side-head">
                    <h2>{{ __('locale.Recent activity') }}</h2>
                </div>
                @php $recentOrders = $orders->take(3); @endphp
                @forelse ($recentOrders as $recent)
                    @php
                        $recentName = (app()->getLocale() === 'ar' && !empty($recent->arcand_name)) ? $recent->arcand_name : ($recent->cand_name ?? '---');
                        $recentDate = $recent->booking_date ? \Illuminate\Support\Carbon::parse($recent->booking_date) : null;
                    @endphp
                    <div class="wp-feed-item">
                        @if ((int) $recent->booking_status === 2)
                            <span class="wp-feed-icon is-critical"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M18 6 6 18M6 6l12 12"/></svg></span>
                            <div><strong>{{ __('locale.Order #:ref cancelled', ['ref' => $recent->reference_no]) }}</strong><span>{{ $recentName }}</span></div>
                        @elseif ($recent->visa_status)
                            <span class="wp-feed-icon is-success"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg></span>
                            <div><strong>{{ __('locale.Visa assigned') }} · #{{ $recent->reference_no }}</strong><span>{{ $recentName }}</span></div>
                        @else
                            <span class="wp-feed-icon is-info"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18"/></svg></span>
                            <div><strong>{{ __('locale.Order #:ref created', ['ref' => $recent->reference_no]) }}</strong><span>{{ $recentDate ? ($recentDate->isToday() ? __('locale.Today') . ' · ' : '') . $recentDate->format('d M Y') : $recentName }}</span></div>
                        @endif
                    </div>
                    @if ($loop->first && (int) $recent->booking_status !== 2 && !$recent->visa_status)
                        <div class="wp-feed-item">
                            <span class="wp-feed-icon is-attention"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>
                            <div><strong>{{ __('locale.Awaiting visa assignment') }}</strong><span>{{ __('locale.Next action for you') }}</span></div>
                        </div>
                    @endif
                @empty
                    <p class="wp-side-empty">{{ __('locale.No recent activity yet.') }}</p>
                @endforelse
            </section>

            @if ($portalUrl)
                {{-- Share the partner's live website (where customers book) - same URL as the header. --}}
                <section class="wp-side-card wp-share-card">
                    <span class="wp-share-icon" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M12 12v5M9.5 14.5h5"/></svg></span>
                    <h3>{{ __('locale.Ready for more bookings') }}</h3>
                    <p>{{ __('locale.Share your website link - bookings made there land here automatically.') }}</p>
                    <button type="button" class="w-btn w-btn-outline" data-copy-text="{{ $portalUrl }}">
                        {{ __('locale.Copy portal link') }}
                        <span class="wp-live-url-copied" role="status" aria-live="polite">{{ __('locale.Copied') }}</span>
                    </button>
                </section>
            @endif
        </aside>
    </div>

    @if (\App\Support\PartnerTeam::allowsSection('orders', 'cancel_booking'))
    @include('worker.partials.cancel-order-modal')
    @endif
    {{-- Rendered here (outside #worker-orders-grid), not inside partial.blade.php,
    same reasoning as the Cancel Booking modal above it: that grid's innerHTML
    gets replaced wholesale on every AJAX filter/pagination refresh
    (order-filters.js), and order-vendor-init.js's Select2 init only runs
    once at page load - a modal living inside the swapped content would
    lose its Select2 instances (and duplicate itself) on every refresh. Only
    each card's own "Add Visa" trigger button lives in the swapped partial. --}}
    @if (\App\Support\PartnerTeam::allowsSection('orders', 'update_payment'))
    @include('worker.partner.orders.payment-modal')
    @endif
    @if ($canAssignVisa)
    @include('worker.partner.orders.add-visa-modal', ['professionOptions' => $professionOptions, 'allWorkCities' => $allWorkCities])
    @endif
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
    <script src="{{ asset('worker/js/order-payment.js') }}?v={{ @filemtime(public_path('worker/js/order-payment.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/order-actions-menu.js') }}?v={{ @filemtime(public_path('worker/js/order-actions-menu.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/orders-page.js') }}?v={{ @filemtime(public_path('worker/js/orders-page.js')) ?: time() }}"></script>
@endsection
