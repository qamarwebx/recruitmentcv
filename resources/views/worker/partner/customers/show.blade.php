@extends('worker.partner.layouts.portal')

@section('title', $customer->name ?: __('locale.Customer'))
@section('page-title', __('locale.Customer Details'))

@section('content')
    <a href="{{ route('worker.partner.customers') }}" class="w-form-back" style="margin:0 0 18px;display:inline-flex;align-items:center;gap:6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        {{ __('locale.Back to Customers') }}
    </a>

    <div class="w-info-card wp-fade-in">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            {{ __('locale.Customer Details') }}
        </h2>
        <div class="w-info-grid">
            <div class="w-info-item">
                <span>{{ __('locale.Full Name') }}</span>
                <strong>{{ $customer->name ?: '---' }} @include('worker.partner.customers.relation-badge')</strong>
            </div>
            <div class="w-info-item">
                <span>{{ __('locale.Mobile Number') }}</span>
                <strong>
                    {{ $customer->mobile_no ? '+' . ltrim((string) $customer->country_code, '+') . ' ' . $customer->mobile_no : '---' }}
                    @if ($customer->mobile_no)
                        @if ($customer->mobile_verified_at)
                            <span class="wp-badge is-success">{{ __('locale.Verified') }}</span>
                        @else
                            <span class="wp-badge is-warning">{{ __('locale.Not Verified') }}</span>
                        @endif
                    @endif
                </strong>
            </div>
            <div class="w-info-item">
                <span>{{ __('locale.Email') }}</span>
                <strong>{{ $customer->email ?: '---' }}</strong>
            </div>
            <div class="w-info-item">
                <span>{{ __('locale.Status') }}</span>
                <strong>
                    @if ((int) $customer->status === 1)
                        <span class="wp-badge is-success">{{ __('locale.Active') }}</span>
                    @else
                        <span class="wp-badge is-neutral">{{ __('locale.Inactive') }}</span>
                    @endif
                </strong>
            </div>
            <div class="w-info-item">
                <span>{{ __('locale.Company Name') }}</span>
                <strong>{{ $customer->company_name ?: '---' }}</strong>
            </div>
            <div class="w-info-item">
                <span>{{ __('locale.Address') }}</span>
                <strong>{{ $customer->address ?: '---' }}</strong>
            </div>
            <div class="w-info-item">
                <span>{{ __('locale.Registered On') }}</span>
                <strong>{{ $customer->created_at ? $customer->created_at->format('d M Y') : '---' }}</strong>
            </div>
        </div>
        @if ((int) $customer->partner_id === (int) $partnerId)
            <div class="wp-customer-actions" style="margin-top:18px;">
                @include('worker.partner.customers.row-actions', ['customer' => $customer, 'partnerId' => $partnerId])
            </div>
        @else
            <p class="wp-field-hint" style="margin:14px 0 0;">{{ __('locale.This customer registered on another website. You can view them and your orders only.') }}</p>
        @endif
    </div>

    <div class="w-info-card wp-fade-in">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
            {{ __('locale.Orders') }}
        </h2>

        @if ($orders->count() > 0)
            <div class="wp-table-scroll">
                <table class="wp-table">
                    <thead>
                        <tr>
                            <th>{{ __('locale.Order') }}</th>
                            <th>{{ __('locale.Candidate') }}</th>
                            <th>{{ __('locale.Booking Date') }}</th>
                            <th>{{ __('locale.Order Status') }}</th>
                            <th>{{ __('locale.Payment Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $isAr = app()->getLocale() === 'ar';
                                $candName = ($isAr && !empty($order->arcand_name)) ? $order->arcand_name : ($order->cand_name ?? '---');
                                $orderStatus = ($isAr && !empty($order->ar_status)) ? $order->ar_status : ($order->ord_status ?: '---');
                            @endphp
                            <tr>
                                <td><a href="{{ route('worker.partner.orders.show', $order->id) }}"><strong>#{{ $order->reference_no }}</strong></a></td>
                                <td>{{ $candName }}</td>
                                <td>{{ $order->booking_date ? \Illuminate\Support\Carbon::parse($order->booking_date)->format('d M Y') : '---' }}</td>
                                <td>
                                    @if ((int) $order->booking_status === 2)
                                        <span class="wp-badge is-neutral">{{ __('locale.Cancelled') }}</span>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="margin:0;color:var(--w-ink-500);">{{ __('locale.This customer has no orders with you yet.') }}</p>
        @endif
    </div>

    @include('worker.partner.customers.modals', ['countries' => $countries])
@endsection

@section('page-script')
    <script src="{{ asset('worker/js/partner-customers.js') }}?v={{ @filemtime(public_path('worker/js/partner-customers.js')) ?: time() }}"
        data-after-unlink-url="{{ route('worker.partner.customers') }}"></script>
@endsection
