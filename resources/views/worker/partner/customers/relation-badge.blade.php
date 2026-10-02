{{-- Why this partner sees the customer: registered on this partner's
website (users.partner_id) or only through an order with this partner
(PartnerCustomersController::visibleCustomers()). Expects $customer, $partnerId. --}}
@if ((int) $customer->partner_id === (int) $partnerId)
    <span class="wp-badge is-info">{{ __('locale.Registered here') }}</span>
@else
    <span class="wp-badge is-neutral">{{ __('locale.Ordered here') }}</span>
@endif
