{{-- Mobile + Email rows with Change buttons that open the shared
contact-change modal (no navigation). data-contact-value / data-contact-badge
are updated in place by customer-account.js after a verified change.
Shared by /account/profile and /account/security. Expects $customer. --}}
<div class="w-account-contact-row">
    <div>
        <span class="w-account-muted">{{ __('locale.Mobile Number') }}</span>
        <strong data-contact-value="mobile">{{ $customer->mobile_no ? '+' . ltrim((string) $customer->country_code, '+') . ' ' . $customer->mobile_no : '---' }}</strong>
        <span class="w-account-badge {{ $customer->mobile_verified_at ? 'is-success' : 'is-warning' }}" data-contact-badge="mobile" @unless ($customer->mobile_no) hidden @endunless>{{ $customer->mobile_verified_at ? __('locale.Verified') : __('locale.Not Verified') }}</span>
    </div>
    <button type="button" class="w-btn w-btn-outline w-btn-sm" data-contact-change="mobile">{{ __('locale.Change') }}</button>
</div>

<div class="w-account-contact-row">
    <div>
        <span class="w-account-muted">{{ __('locale.Email') }}</span>
        <strong data-contact-value="email">{{ $customer->email ?: '---' }}</strong>
        <span class="w-account-badge {{ $customer->email_verified_at ? 'is-success' : 'is-warning' }}" data-contact-badge="email" @unless ($customer->email) hidden @endunless>{{ $customer->email_verified_at ? __('locale.Verified') : __('locale.Not Verified') }}</span>
    </div>
    <button type="button" class="w-btn w-btn-outline w-btn-sm" data-contact-change="email">{{ __('locale.Change') }}</button>
</div>
