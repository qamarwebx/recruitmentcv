@extends('worker.partner.layouts.portal')

@php
    $candName = (app()->getLocale() === 'ar' && !empty($order->arcand_name)) ? $order->arcand_name : ($order->cand_name ?? '---');
    $profession = (app()->getLocale() === 'ar' && !empty($order->profession_ar)) ? $order->profession_ar : ($order->profession_eng ?? '---');
    $orderStatus = (int) $order->booking_status === 2
        ? __('locale.Cancelled')
        : ((app()->getLocale() === 'ar' && !empty($order->ar_status)) ? $order->ar_status : ($order->ord_status ?: '---'));
    $workLocation = (app()->getLocale() === 'ar' && !empty($order->worklocation_ar)) ? $order->worklocation_ar : ($order->worklocation_eng ?: '---');
    $employerName = (app()->getLocale() === 'ar' && !empty($order->employer_ar_name)) ? $order->employer_ar_name : ($order->employer_name ?: '---');
    $imagePath = \App\Support\CandidatePhoto::url($order->photo_file);
@endphp

@section('title', 'Order #' . $order->reference_no)
@section('page-title', __('locale.Order') . ' #' . $order->reference_no)

@section('page-style')
    {{-- jQuery-dependent select2, scoped to this page only - same reasoning
    as the Orders listing page's own page-style section. --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')
    <a href="{{ route('worker.partner.orders') }}" class="w-form-back" style="margin:0 0 18px;display:inline-flex;align-items:center;gap:6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        {{ __('locale.Back to Orders') }}
    </a>

    <div class="w-details-grid wp-fade-in">
        <div class="w-gallery">
            <div class="w-gallery-main">
                <img src="{{ $imagePath }}" alt="{{ $candName }}" onerror="this.onerror=null;this.src='{{ \App\Support\CandidatePhoto::defaultUrl() }}';">
            </div>

            <div class="w-profile-card">
                <div class="w-profile-name">
                    <h1>{{ $candName }}</h1>
                </div>

                <div class="w-profile-tags">
                    @if ($profession && $profession !== '---')
                        <span class="w-tag">{{ $profession }}</span>
                    @endif
                </div>

                <div data-order-status-cell data-order-id="{{ $order->id }}">
                    <div class="wp-badge {{ (int) $order->booking_status === 2 ? 'is-neutral' : 'is-info' }}" style="margin-top:14px;width:fit-content;">{{ $orderStatus }}</div>
                </div>

                @php
                    $canVisa = \App\Support\PartnerTeam::allowsSection('orders', 'assign_visa');
                    $canCancel = \App\Support\PartnerTeam::allowsSection('orders', 'cancel_booking');
                @endphp
                @if ((int) $order->booking_status !== 2 && ($canVisa || $canCancel))
                    <div class="w-cta-row" style="margin-top:14px;">
                        @if ($canVisa)
                        <button type="button" class="w-btn w-btn-outline"
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
                            data-exp-sal="{{ $order->exp_sal }}">
                            {{ __('locale.Add Visa') }}
                        </button>
                        @endif
                        @if ($canCancel)
                        <button type="button" class="w-btn w-btn-outline" style="color:#dc2626;border-color:#fecaca;" data-cancel-order-trigger data-cancel-url="{{ route('worker.partner.orders.cancel', $order->id) }}" data-order-id="{{ $order->id }}">
                            {{ __('locale.Cancel Booking') }}
                        </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div class="w-info-card wp-fade-in">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
                    {{ __('locale.Order') }}
                </h2>
                <div class="w-info-grid">
                    <div class="w-info-item">
                        <span>{{ __('locale.Order') }}</span>
                        <strong>#{{ $order->reference_no }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Recruitment Office') }}</span>
                        <strong>{{ $partner->rec_off_name ?: $partner->owner_name ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Booking Date') }}</span>
                        <strong>{{ $order->booking_date ? \Illuminate\Support\Carbon::parse($order->booking_date)->format('d M Y') : '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Amount') }}</span>
                        <strong>{{ $order->amount ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Order Status') }}</span>
                        <strong>{{ $orderStatus }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Payment Status') }}</span>
                        <strong>{{ $order->payment_status ? __('locale.Paid') : __('locale.Unpaid') }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Visa Status') }}</span>
                        <strong>{{ $order->visa_status ? __('locale.Confirmed') : __('locale.Pending') }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Work Location') }}</span>
                        <strong>{{ $workLocation }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Employer Name') }}</span>
                        <strong>{{ $order->employer_name ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Mobile Number') }}</span>
                        <strong>{{ $order->mobile_no ?: '---' }}</strong>
                    </div>
                </div>
            </div>

            @if ($order->visa_no || $order->employer_name || $order->issuing_authority)
                <div class="w-info-card wp-fade-in">
                    <h2>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                        {{ __('locale.Visa & Employer Details') }}
                    </h2>
                    <div class="w-info-grid">
                        <div class="w-info-item">
                            <span>{{ __('locale.Visa Number') }}</span>
                            <strong>{{ $order->visa_no ?: '---' }}</strong>
                        </div>
                        <div class="w-info-item">
                            <span>{{ __('locale.Employer Name') }}</span>
                            <strong>{{ $employerName }}</strong>
                        </div>
                        <div class="w-info-item">
                            <span>{{ __('locale.Issuing Authority') }}</span>
                            <strong>{{ $order->issuing_authority ?: '---' }}</strong>
                        </div>
                    </div>
                </div>
            @endif
        </div>
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
@endsection
