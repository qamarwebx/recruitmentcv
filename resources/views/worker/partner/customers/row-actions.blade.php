{{-- Edit + Unlink for one customer - used by the list and the detail page.
Only for a customer registered on this partner's website; an "Ordered here"
customer is view only. The server re-checks all of this on every submit
(PartnerCustomersController); these ids/urls/flags are never trusted. --}}
@if ((int) $customer->partner_id === (int) $partnerId)
<button type="button" class="w-btn w-btn-outline w-btn-sm" data-customer-edit
    data-update-url="{{ route('worker.partner.customers.update', $customer->id) }}"
    data-customer="{{ json_encode([
        'name' => $customer->name,
        'email' => $customer->email,
        'country_code' => ltrim((string) $customer->country_code, '+') ?: '966',
        'mobile_no' => $customer->mobile_no,
        'company_name' => $customer->company_name,
        'address' => $customer->address,
        // Mobile/email are the customer's own once they use the account.
        'login_locked' => $customer->isInUseByCustomer(),
    ]) }}">
    {{ __('locale.Edit') }}
</button>
<button type="button" class="w-btn w-btn-outline w-btn-sm wp-customer-unlink" data-customer-unlink
    data-unlink-url="{{ route('worker.partner.customers.unlink', $customer->id) }}">
    {{ __('locale.Unlink') }}
</button>
@endif
