@extends('worker.account.layout')

@section('title', __('locale.My Orders'))

@section('account-content')
    <div class="w-account-card">
        <h2>{{ __('locale.My Orders') }}</h2>

        @if ($orders->count() > 0)
            <div class="w-account-list">
                @foreach ($orders as $order)
                    @php
                        $isAr = app()->getLocale() === 'ar';
                        $candName = ($isAr && !empty($order->arcand_name)) ? $order->arcand_name : ($order->cand_name ?? '---');
                        $profession = ($isAr && !empty($order->profession_ar)) ? $order->profession_ar : ($order->profession_eng ?? '---');
                        $orderStatus = ($isAr && !empty($order->ar_status)) ? $order->ar_status : ($order->ord_status ?: '---');
                        $isCancelled = (int) $order->booking_status === 2;
                    @endphp
                    <div class="w-account-item">
                        <img class="w-account-item-photo" src="{{ \App\Support\CandidatePhoto::url($order->photo_file) }}" alt="{{ $candName }}" onerror="this.onerror=null;this.src='{{ \App\Support\CandidatePhoto::defaultUrl() }}';">
                        <div class="w-account-item-main">
                            <div class="w-account-item-title">
                                @if ($order->candidate_slug)
                                    <a href="{{ route('worker.resume.details', $order->candidate_slug) }}">{{ $candName }}</a>
                                @else
                                    {{ $candName }}
                                @endif
                                <span class="w-account-item-ref">#{{ $order->reference_no }}</span>
                            </div>
                            <div class="w-account-item-sub">
                                {{ $profession }} · {{ __('locale.Booking Date') }}: {{ $order->booking_date ? \Illuminate\Support\Carbon::parse($order->booking_date)->format('d M Y') : '---' }}
                            </div>
                            <div class="w-account-badges">
                                @if ($isCancelled)
                                    <span class="w-account-badge is-neutral">{{ __('locale.Cancelled') }}</span>
                                @else
                                    <span class="w-account-badge is-info">{{ $orderStatus }}</span>
                                @endif
                                <span class="w-account-badge {{ $order->payment_status ? 'is-success' : 'is-warning' }}">
                                    {{ __('locale.Payment Status') }}: {{ $order->payment_status ? __('locale.Paid') : __('locale.Unpaid') }}
                                </span>
                                <span class="w-account-badge {{ $order->visa_status ? 'is-success' : 'is-warning' }}">
                                    {{ __('locale.Visa Status') }}: {{ $order->visa_status ? __('locale.Confirmed') : __('locale.Pending') }}
                                </span>
                            </div>
                        </div>
                        @unless ($isCancelled)
                            <div class="w-account-item-actions">
                                <button type="button" class="w-btn w-btn-outline w-btn-sm w-account-danger" data-order-cancel data-order-id="{{ $order->id }}">
                                    {{ __('locale.Cancel Order') }}
                                </button>
                            </div>
                        @endunless
                    </div>
                @endforeach
            </div>

            {{ $orders->links('worker.partials.pagination') }}
        @else
            <div class="w-account-empty">
                <h3>{{ __('locale.No orders yet.') }}</h3>
                <p>{{ __('locale.Orders you place will appear here.') }}</p>
                <a href="{{ route('worker.resumes') }}" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Browse Resumes') }}</a>
            </div>
        @endif
    </div>

    {{-- Cancel confirmation - posts to the existing /booking/cancel endpoint
    (DashboardController::cancelBooking, limited to the customer's own orders). --}}
    <div class="w-modal-backdrop" data-order-cancel-backdrop></div>
    <div class="w-modal" data-order-cancel-modal role="dialog" aria-modal="true" aria-labelledby="orderCancelTitle"
        data-cancel-url="{{ route('user.candidate.booking.cancel') }}">
        <div class="w-modal-dialog">
            <button type="button" class="w-modal-close" data-order-cancel-close aria-label="{{ __('locale.Close') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
            <div class="w-modal-body">
                <h3 id="orderCancelTitle" class="w-modal-title">{{ __('locale.Cancel Order') }}</h3>
                <p class="w-modal-subtitle">{{ __('locale.Are you sure you want to cancel this order?') }}</p>
                <div class="w-form-alert" data-order-cancel-alert hidden></div>
                <div style="display:flex;gap:10px;margin-top:18px;">
                    <button type="button" class="w-btn w-btn-outline w-btn-block" data-order-cancel-close>{{ __('locale.No, Keep') }}</button>
                    <button type="button" class="w-btn w-btn-danger w-btn-block" data-order-cancel-confirm
                        data-default-text="{{ __('locale.Yes, Cancel') }}"
                        data-loading-text="{{ __('locale.Cancelling…') }}"
                        data-error-text="{{ __('locale.Something went wrong. Please try again.') }}">
                        {{ __('locale.Yes, Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('worker/js/customer-account.js') }}?v={{ @filemtime(public_path('worker/js/customer-account.js')) ?: time() }}"></script>
@endsection
