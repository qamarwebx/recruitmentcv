{{-- Order source badge (Orders card + table): an order placed by a customer on
     qamarhire.com - bookings.order_source = 'qamarhire', written by qamarhire.com's
     checkout when the order is created (App\Support\OrderSource there). Orders from
     this website or with no recorded source show nothing. Display only. --}}
@if (($order->order_source ?? null) === 'qamarhire')
    @once
        <style>
            .wp-source-badge{display:inline-flex;align-items:center;gap:5px;margin-top:6px;padding:3px 9px;border-radius:999px;background:#e8f5ee;border:1px solid #b9e0ca;color:#0b6b3a;font-size:.72rem;font-weight:600;line-height:1.3;white-space:nowrap}
            .wp-source-badge svg{flex:none;color:#c99112}
            .wp-ocard-ref{display:flex;flex-direction:column;align-items:flex-end}
            [dir=rtl] .wp-ocard-ref{align-items:flex-start}
            .wp-orders-table .wp-source-badge{display:flex;width:max-content}
        </style>
    @endonce
    <span class="wp-source-badge" data-order-source="qamarhire" title="{{ __('locale.This order was placed by a customer on qamarhire.com.') }}">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l1.5-5h15L21 9"/><path d="M4 9v11h16V9"/><path d="M9 20v-6h6v6"/></svg>
        {{ __('locale.Ordered from QamarHire.com') }}
    </span>
@endif
