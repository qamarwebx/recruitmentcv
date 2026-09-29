{{-- Edit + Unlink for one customer - used by the list and the detail page.
The server re-checks ownership on every submit; these ids/urls are never
trusted on their own. --}}
<button type="button" class="w-btn w-btn-outline w-btn-sm" data-customer-edit
    data-update-url="{{ route('worker.partner.customers.update', $customer->id) }}"
    data-customer="{{ json_encode([
        'name' => $customer->name,
        'email' => $customer->email,
        'country_code' => ltrim((string) $customer->country_code, '+') ?: '966',
        'mobile_no' => $customer->mobile_no,
        'company_name' => $customer->company_name,
        'address' => $customer->address,
    ]) }}">
    {{ __('locale.Edit') }}
</button>
<button type="button" class="w-btn w-btn-outline w-btn-sm wp-customer-unlink" data-customer-unlink
    data-unlink-url="{{ route('worker.partner.customers.unlink', $customer->id) }}">
    {{ __('locale.Unlink') }}
</button>
