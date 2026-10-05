{{-- Add/Edit (one form, two modes) and Unlink confirmation - driven by
public/worker/js/partner-customers.js. --}}
<div class="w-modal-backdrop" data-customer-form-backdrop></div>
<div class="w-modal" data-customer-form-modal role="dialog" aria-modal="true" aria-labelledby="customerFormTitle"
    data-store-url="{{ route('worker.partner.customers.store') }}"
    data-title-add="{{ __('locale.Add Customer') }}"
    data-title-edit="{{ __('locale.Edit Customer') }}"
    data-saving-text="{{ __('locale.Saving…') }}"
    data-save-text="{{ __('locale.Save Changes') }}"
    data-error-text="{{ __('locale.Something went wrong. Please try again.') }}">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-customer-form-close aria-label="{{ __('locale.Close') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <div class="w-modal-body">
            <h3 id="customerFormTitle" class="w-modal-title" data-customer-form-title>{{ __('locale.Add Customer') }}</h3>
            <div class="w-form-alert" data-customer-form-alert hidden></div>

            <form data-customer-form novalidate>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Full Name') }} *</label>
                    <input type="text" class="w-input" name="name" maxlength="255" required>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Mobile Number') }} *</label>
                    <div class="w-form-phone-row">
                        <select class="w-select" name="country_code">
                            @foreach ($countries as $code => $label)
                                <option value="{{ $code }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <input type="tel" class="w-input" name="mobile_no" inputmode="numeric" maxlength="15" required>
                    </div>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Email') }}</label>
                    <input type="email" class="w-input" name="email" maxlength="255">
                </div>
                <div class="wp-field-hint" data-customer-locked-hint hidden style="margin:-6px 0 14px;">{{ __('locale.Only the customer can change their mobile number and email, from their own account.') }}</div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Company Name') }}</label>
                    <input type="text" class="w-input" name="company_name" maxlength="255">
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Address') }}</label>
                    <textarea class="w-input" name="address" rows="2" maxlength="1000"></textarea>
                </div>
                <button type="submit" class="w-btn w-btn-primary w-btn-block" data-customer-form-submit>{{ __('locale.Save Changes') }}</button>
            </form>
        </div>
    </div>
</div>

<div class="w-modal-backdrop" data-customer-unlink-backdrop></div>
<div class="w-modal" data-customer-unlink-modal role="dialog" aria-modal="true" aria-labelledby="customerUnlinkTitle">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-customer-unlink-close aria-label="{{ __('locale.Close') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <div class="w-modal-body">
            <h3 id="customerUnlinkTitle" class="w-modal-title">{{ __('locale.Unlink Customer') }}</h3>
            <p class="w-modal-subtitle">{{ __('locale.Remove this customer from your list?') }}</p>
            <div class="w-form-alert" data-customer-unlink-alert hidden></div>
            <div style="display:flex;gap:10px;margin-top:18px;">
                <button type="button" class="w-btn w-btn-outline w-btn-block" data-customer-unlink-close>{{ __('locale.No, Keep') }}</button>
                <button type="button" class="w-btn w-btn-danger w-btn-block" data-customer-unlink-confirm
                    data-default-text="{{ __('locale.Yes, Unlink') }}"
                    data-loading-text="{{ __('locale.Unlinking…') }}">
                    {{ __('locale.Yes, Unlink') }}
                </button>
            </div>
        </div>
    </div>
</div>
