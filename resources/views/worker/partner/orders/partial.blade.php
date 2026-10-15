@php
    // What this account may see/do in Orders (a team member: what its
    // partner granted - App\Support\PartnerTeam; the partner: everything).
    // Every action is also checked server-side on its own route.
    $orderViews = \App\Support\PartnerTeam::orderViews();
    $can = fn ($action) => \App\Support\PartnerTeam::allowsSection('orders', $action);
    $canViewOrder = $can('view_order');
    $canVisa = $can('assign_visa');
    $canViewCandidate = $can('view_candidate');
    $canDownloadCv = $can('download_cv');
    $canCancel = $can('cancel_booking');
    $canPayment = $can('update_payment');
    // Update Payment trigger attributes (order-payment.js): the order's current payment, prefilled in the dialog.
    $paymentAttrs = function ($order) {
        $raw = trim((string) ($order->paid_amount ?? ''));
        return 'data-order-payment-trigger data-payment-url="' . e(route('worker.partner.orders.payment.store', $order->id)) . '" data-order-id="' . e($order->id)
            . '" data-order-ref="' . e($order->reference_no) . '" data-paid="' . ((int) $order->payment_status === 1 ? '1' : '0')
            . '" data-amount="' . e(preg_match('/^\d{1,10}(\.\d{1,2})?$/', $raw) ? $raw : '') . '" data-amount-label="' . e(\App\Support\OrderPipeline::amountLabel($order) ?? '') . '"';
    };
    // The CRM workflow rows of this page's candidates in one query (App\Support\OrderPipeline stages).
    \App\Support\OrderPipeline::preload($orders);
    // Download CV links go through PartnerPortalController::candidateCv
    // (refuses partners not fully verified, Partner::isFullyVerified()); the
    // direct-file fallback for an order without a candidate slug is only
    // offered to a fully verified partner, since a plain file link can't be checked.
    $cvFallbackAllowed = (bool) optional(\Illuminate\Support\Facades\Auth::guard('partner')->user())->isFullyVerified();
    $isAr = app()->getLocale() === 'ar';
    $bookedOn = function ($order) {
        if (!$order->booking_date) {
            return '---';
        }
        $date = \Illuminate\Support\Carbon::parse($order->booking_date);

        return $date->isToday() ? __('locale.Today') : $date->format('d M Y');
    };
@endphp
<div class="wp-listing-toolbar wp-orders-toolbar">
    {{-- Active / Deployed / Cancelled with their counts (same filtered
    query as the list - PartnerPortalController::orders()); order-filters.js
    reloads the results for the chosen tab, keeping filters and view mode. --}}
    <div class="wp-view-switch" role="tablist" aria-label="{{ __('locale.Order status') }}">
        @foreach (['active' => __('locale.Active'), 'deployed' => __('locale.Deployed'), 'cancelled' => __('locale.Cancelled')] as $state => $stateLabel)
            @continue (!array_key_exists($state, $orderCounts))
            <button type="button" class="wp-view-btn {{ $orderState === $state ? 'is-active' : '' }}" data-order-state-btn="{{ $state }}" aria-pressed="{{ $orderState === $state ? 'true' : 'false' }}">
                {{ $stateLabel }} <span class="wp-tab-count">{{ $orderCounts[$state] }}</span>
            </button>
        @endforeach
    </div>

    <div class="wp-listing-toolbar-actions">
        <span class="wp-sort-label">{{ __('locale.Sort') }}: <strong>{{ __('locale.Newest first') }}</strong></span>
        <div class="wp-view-switch" data-wp-view-switch role="tablist" aria-label="{{ __('locale.Order view') }}">
            @if (in_array('card', $orderViews, true))
            <button type="button" class="wp-view-btn" data-wp-view-btn="card" aria-pressed="true">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                {{ __('locale.Cards') }}
            </button>
            @endif
            @if (in_array('table', $orderViews, true))
            <button type="button" class="wp-view-btn" data-wp-view-btn="table" aria-pressed="false">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                {{ __('locale.Table') }}
            </button>
            @endif
        </div>
    </div>
</div>

@if ($orders->count() > 0)
    @if (in_array('card', $orderViews, true))
    <div class="wp-order-grid wp-ocard-list wp-fade-in" data-wp-view-panel="card">
        @foreach ($orders as $order)
            @php
                $candName = ($isAr && !empty($order->arcand_name)) ? $order->arcand_name : ($order->cand_name ?? '---');
                $profession = ($isAr && !empty($order->profession_ar)) ? $order->profession_ar : ($order->profession_eng ?? '---');
                $orderStatus = ($isAr && !empty($order->ar_status)) ? $order->ar_status : ($order->ord_status ?: '---');
                $pipeline = \App\Support\OrderPipeline::for($order);
                $isCancelled = $pipeline['cancelled'];
                $cvUrl = ($canDownloadCv && $order->cv_execute == 1 && $order->cv_execute_file != '' && ($order->candidate_slug || $cvFallbackAllowed))
                    ? ($order->candidate_slug ? route('worker.partner.candidates.cv', $order->candidate_slug) : asset('admin/assets/images/pdf/' . $order->cv_execute_file)) : null;
                $visaAttrs = 'data-order-visa-trigger data-visa-url="' . e(route('worker.partner.orders.visa.store', $order->id)) . '" data-employer-name="' . e($order->employer_name) . '" data-employer-ar-name="' . e($order->employer_ar_name)
                    . '" data-visa-no="' . e($order->visa_no) . '" data-id-no="' . e($order->id_no) . '" data-proff-id="' . e($order->visa_proff_id) . '" data-issuing-authority="' . e($order->issuing_authority)
                    . '" data-wpcity-id="' . e($order->wpcity_id) . '" data-salary="' . e($order->salary) . '" data-exp-sal="' . e($order->exp_sal) . '"';
            @endphp
            <article class="wp-order-card wp-ocard">
                <header class="wp-ocard-head">
                    <span class="wp-avatar is-lg">
                        @if (!empty($order->photo_file))
                            <img src="{{ \App\Support\CandidatePhoto::url($order->photo_file) }}" alt="" onerror="this.remove();">
                        @endif
                        <span class="wp-avatar-initials">{{ \App\Support\OrderPipeline::initials($order->cand_name) }}</span>
                        <span class="wp-avatar-check" aria-hidden="true"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4"><path d="M20 6 9 17l-5-5"/></svg></span>
                    </span>
                    <div class="wp-ocard-title">
                        <div class="wp-ocard-name">
                            <h3>{{ $candName }}</h3>
                            <span class="wp-pill-verified">{{ __('locale.Verified') }}</span>
                        </div>
                        <div class="wp-ocard-sub">
                            <span class="is-strong">{{ $profession }}</span>
                            <span class="wp-dot-sep" aria-hidden="true"></span>
                            <span>{{ __('locale.Booked') }} {{ $bookedOn($order) }}</span>
                        </div>
                    </div>
                    <div class="wp-ocard-ref">
                        <span class="wp-ref-pill">{{ __('locale.Order') }} #{{ $order->reference_no }}</span>
                        @include('worker.partner.orders.source-badge', ['order' => $order])
                    </div>
                </header>

                @include('worker.partner.orders.pipeline', ['pipeline' => $pipeline])

                <div class="wp-order-card-badges wp-ocard-badges" data-order-status-cell data-order-id="{{ $order->id }}">
                    @if ($isCancelled)
                        <span class="wp-badge is-danger">{{ __('locale.Cancelled') }}</span>
                    @else
                        <span class="wp-badge is-info">{{ $orderStatus }}</span>
                    @endif
                    @if ($order->payment_status)
                        <span class="wp-badge is-success">{{ __('locale.Paid') }}</span>
                    @else
                        <span class="wp-badge is-warning">{{ __('locale.Unpaid') }}</span>
                    @endif
                    @if ($order->visa_status)
                        <span class="wp-badge is-success">{{ __('locale.Visa assigned') }}</span>
                    @else
                        <span class="wp-badge is-attention">{{ __('locale.Visa pending') }}</span>
                    @endif
                </div>

                <dl class="wp-ocard-meta">
                    <div>
                        <dt>{{ __('locale.Booking Date') }}</dt>
                        <dd>{{ $order->booking_date ? \Illuminate\Support\Carbon::parse($order->booking_date)->format('d M Y') : '---' }}</dd>
                    </div>
                    <div>
                        <dt>{{ __('locale.Amount') }}</dt>
                        @php $orderAmount = \App\Support\OrderPipeline::amountLabel($order); @endphp
                        <dd class="{{ $orderAmount !== null ? '' : 'is-muted' }}">@if ($orderAmount !== null)<span dir="ltr">{{ $orderAmount }}</span>@else—@endif</dd>
                    </div>
                    <div>
                        <dt>{{ __('locale.Employer') }}</dt>
                        <dd>
                            @if ($order->employer_name)
                                {{ ($isAr && $order->employer_ar_name) ? $order->employer_ar_name : $order->employer_name }}
                            @elseif ($canVisa && !$isCancelled)
                                <button type="button" class="wp-link-btn" {!! $visaAttrs !!}>{{ __('locale.Assign employer') }}</button>
                            @else
                                <span class="is-muted">{{ __('locale.Not assigned') }}</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt>{{ __('locale.Mobile') }}</dt>
                        <dd class="{{ $order->mobile_no ? '' : 'is-muted' }}">{{ $order->mobile_no ?: __('locale.Not provided') }}</dd>
                    </div>
                </dl>

                <footer class="wp-order-card-actions wp-ocard-actions">
                    @if ($canViewOrder)
                        <a href="{{ route('worker.partner.orders.show', $order->id) }}" class="w-btn w-btn-primary">
                            {{ __('locale.View order') }}
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    @endif
                    @if ($canVisa && !$isCancelled)
                        <button type="button" class="w-btn w-btn-accent" {!! $visaAttrs !!}>{{ __('locale.Assign Visa') }}</button>
                    @endif
                    @if ($canViewCandidate && $order->candidate_slug)
                        <a href="{{ route('worker.partner.candidates.show', $order->candidate_slug) }}" class="w-btn w-btn-outline">{{ __('locale.View Candidate') }}</a>
                    @endif
                    @if ($cvUrl)
                        <a href="{{ $cvUrl }}" target="_blank" class="w-btn w-btn-outline">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3v12M7 10l5 5 5-5M5 21h14"/></svg>
                            {{ __('locale.Download CV') }}
                        </a>
                    @endif
                    @if ($canPayment && !$isCancelled)
                        <button type="button" class="w-btn w-btn-outline" {!! $paymentAttrs($order) !!}>{{ __('locale.Update Payment') }}</button>
                    @endif
                    @if ($canCancel && !$isCancelled)
                        <button type="button" class="w-btn w-btn-danger wp-ocard-cancel" data-cancel-order-trigger data-cancel-url="{{ route('worker.partner.orders.cancel', $order->id) }}" data-order-id="{{ $order->id }}">
                            {{ __('locale.Cancel') }}
                        </button>
                    @endif
                </footer>
            </article>
        @endforeach
    </div>
    @endif

    @if (in_array('table', $orderViews, true))
    <div class="wp-table-wrap wp-fade-in" data-wp-view-panel="table" {{ in_array('card', $orderViews, true) ? 'hidden' : '' }}>
        <div class="wp-table-scroll">
            <table class="wp-table wp-orders-table">
                <thead>
                    <tr>
                        <th>{{ __('locale.Order') }}</th>
                        <th>{{ __('locale.Candidate') }}</th>
                        <th>{{ __('locale.Employer') }}</th>
                        <th>{{ __('locale.Pipeline') }}</th>
                        <th>{{ __('locale.Status') }}</th>
                        <th>{{ __('locale.Payment') }}</th>
                        <th>{{ __('locale.Amount') }}</th>
                        <th>{{ __('locale.Booked') }}</th>
                        <th><span class="wp-visually-hidden">{{ __('locale.Actions') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        @php
                            $candName = ($isAr && !empty($order->arcand_name)) ? $order->arcand_name : ($order->cand_name ?? '---');
                            $profession = ($isAr && !empty($order->profession_ar)) ? $order->profession_ar : ($order->profession_eng ?? '---');
                            $orderStatus = ($isAr && !empty($order->ar_status)) ? $order->ar_status : ($order->ord_status ?: '---');
                            $pipeline = \App\Support\OrderPipeline::for($order);
                            $isCancelled = $pipeline['cancelled'];
                            $menuViewCandidate = $canViewCandidate && $order->candidate_slug;
                            $menuCv = $canDownloadCv && $order->cv_execute == 1 && $order->cv_execute_file != '' && ($order->candidate_slug || $cvFallbackAllowed);
                            $menuCancel = $canCancel && !$isCancelled;
                            $menuPayment = $canPayment && !$isCancelled;
                        @endphp
                        <tr>
                            <td>
                                <span class="wp-cell-title">#{{ $order->reference_no }}</span>
                                @include('worker.partner.orders.source-badge', ['order' => $order])
                            </td>
                            <td>
                                <div class="wp-cell-person">
                                    <span class="wp-avatar">
                                        @if (!empty($order->photo_file))
                                            <img src="{{ \App\Support\CandidatePhoto::url($order->photo_file) }}" alt="" onerror="this.remove();">
                                        @endif
                                        <span class="wp-avatar-initials">{{ \App\Support\OrderPipeline::initials($order->cand_name) }}</span>
                                    </span>
                                    <div>
                                        @if ($menuViewCandidate)
                                            <a href="{{ route('worker.partner.candidates.show', $order->candidate_slug) }}" class="wp-cell-title wp-cell-link">{{ $candName }}@include('worker.partials.candidate-verified-icon')</a>
                                        @else
                                            <span class="wp-cell-title">{{ $candName }}@include('worker.partials.candidate-verified-icon')</span>
                                        @endif
                                        <span class="wp-cell-sub">{{ $profession }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($order->employer_name)
                                    {{ ($isAr && $order->employer_ar_name) ? $order->employer_ar_name : $order->employer_name }}
                                @elseif ($canVisa && !$isCancelled)
                                    <button type="button" class="wp-link-btn"
                                        data-order-visa-trigger
                                        data-visa-url="{{ route('worker.partner.orders.visa.store', $order->id) }}"
                                        data-employer-name="{{ $order->employer_name }}"
                                        data-employer-ar-name="{{ $order->employer_ar_name }}"
                                        data-visa-no="{{ $order->visa_no }}"
                                        data-id-no="{{ $order->id_no }}"
                                        data-proff-id="{{ $order->visa_proff_id }}"
                                        data-issuing-authority="{{ $order->issuing_authority }}"
                                        data-wpcity-id="{{ $order->wpcity_id }}"
                                        data-salary="{{ $order->salary }}"
                                        data-exp-sal="{{ $order->exp_sal }}">{{ __('locale.Assign employer') }}</button>
                                @else
                                    <span class="is-muted">&mdash;</span>
                                @endif
                            </td>
                            <td>@include('worker.partner.orders.pipeline', ['pipeline' => $pipeline, 'mini' => true])</td>
                            <td data-order-status-cell data-order-id="{{ $order->id }}">
                                @if ($isCancelled)
                                    <span class="wp-badge is-danger">{{ __('locale.Cancelled') }}</span>
                                @else
                                    <span class="wp-badge is-info">{{ $orderStatus }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($order->payment_status)
                                    <span class="wp-badge is-success">{{ __('locale.Paid') }}</span>
                                @else
                                    <span class="wp-badge is-warning">{{ __('locale.Unpaid') }}</span>
                                @endif
                            </td>
                            @php $orderAmount = \App\Support\OrderPipeline::amountLabel($order); @endphp
                            <td class="{{ $orderAmount !== null ? '' : 'is-muted' }}">@if ($orderAmount !== null)<span dir="ltr">{{ $orderAmount }}</span>@else—@endif</td>
                            <td>
                                @if ($order->booking_date)
                                    <span class="wp-cell-title is-plain">{{ \Illuminate\Support\Carbon::parse($order->booking_date)->format('d M Y') }}</span>
                                    @if (\Illuminate\Support\Carbon::parse($order->booking_date)->isToday())<span class="wp-cell-sub">{{ __('locale.Today') }}</span>@endif
                                @else
                                    ---
                                @endif
                            </td>
                            <td>
                                <div class="wp-row-actions">
                                    @if ($canViewOrder)
                                        <a href="{{ route('worker.partner.orders.show', $order->id) }}" class="wp-icon-btn" aria-label="{{ __('locale.View order') }}" title="{{ __('locale.View order') }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                    @endif
                                    @if ($menuViewCandidate || $menuCv || $menuCancel || $menuPayment)
                                    <div class="wp-dropdown-wrap">
                                        <button type="button" class="wp-icon-btn" data-order-actions-trigger data-order-id="{{ $order->id }}" aria-label="{{ __('locale.Actions') }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                                        </button>
                                        <div class="wp-dropdown" data-order-actions-menu="{{ $order->id }}">
                                            @if ($canViewOrder)
                                                <a href="{{ route('worker.partner.orders.show', $order->id) }}" class="wp-menu-item">{{ __('locale.View Order') }}</a>
                                            @endif
                                            @if ($menuViewCandidate)
                                                <a href="{{ route('worker.partner.candidates.show', $order->candidate_slug) }}" class="wp-menu-item">{{ __('locale.View Candidate') }}</a>
                                            @endif
                                            @if ($menuCv)
                                                <a href="{{ $order->candidate_slug ? route('worker.partner.candidates.cv', $order->candidate_slug) : asset('admin/assets/images/pdf/' . $order->cv_execute_file) }}" target="_blank" class="wp-menu-item">{{ __('locale.Download CV') }}</a>
                                            @endif
                                            @if ($menuPayment)
                                                <button type="button" class="wp-menu-item" {!! $paymentAttrs($order) !!}>{{ __('locale.Update Payment') }}</button>
                                            @endif
                                            @if ($menuCancel)
                                                <button type="button" class="wp-menu-item is-danger" data-cancel-order-trigger data-cancel-url="{{ route('worker.partner.orders.cancel', $order->id) }}" data-order-id="{{ $order->id }}">
                                                    {{ __('locale.Cancel Booking') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="wp-table-foot">
            <span>{{ __('locale.Showing :from-:to of :total orders', ['from' => $orders->firstItem(), 'to' => $orders->lastItem(), 'total' => $orders->total()]) }}</span>
        </div>
    </div>
    @endif

    {{ $orders->links('worker.partials.pagination') }}
@else
    <div class="wp-empty">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
        <h3>{{ __('locale.No orders found') }}</h3>
        <p>{{ __('locale.Orders linked to your account will appear here.') }}</p>
    </div>
@endif
