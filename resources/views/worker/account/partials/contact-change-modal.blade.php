{{-- Change mobile / email modal - shared by /account/profile and
/account/security (customer-account.js). Existing send-code + verify-code
endpoints (DashboardController); the new value is saved only after its code
is verified server-side. Expects $customer. --}}
<div class="w-modal-backdrop" data-contact-backdrop></div>
<div class="w-modal" data-contact-modal role="dialog" aria-modal="true" aria-labelledby="contactChangeTitle"
    data-mobile-send-url="{{ route('booking.profile.otpandupdate') }}"
    data-mobile-verify-url="{{ app()->getLocale() === 'ar' ? route('booking.profile.arvalidate-otp2') : route('booking.profile.validate-otp2') }}"
    data-email-send-url="{{ route('booking.profile.emailotpandupdate') }}"
    data-email-verify-url="{{ app()->getLocale() === 'ar' ? route('booking.profile.arvalidate-otp2e') : route('booking.profile.validate-otp2e') }}"
    data-title-mobile="{{ __('locale.Change Mobile Number') }}"
    data-title-email="{{ __('locale.Change Email') }}"
    data-err-required="{{ __('locale.Please check the details and try again.') }}"
    data-err-not-sent="{{ __('locale.We could not send the OTP. Please try again in a moment.') }}"
    data-err-technical="{{ __('locale.A technical error occurred. Please try again shortly.') }}"
    data-err-otp="{{ __('locale.Incorrect OTP. Please try again.') }}"
    data-sending="{{ __('locale.Sending…') }}"
    data-send="{{ __('locale.Send Code') }}"
    data-verifying="{{ __('locale.Verifying…') }}"
    data-verify="{{ __('locale.Verify & Continue') }}"
    data-verified-label="{{ __('locale.Verified') }}">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-contact-close aria-label="{{ __('locale.Close') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <div class="w-modal-body">
            <h3 id="contactChangeTitle" class="w-modal-title" data-contact-title></h3>
            <div class="w-form-alert" data-contact-alert hidden></div>

            <div data-contact-step="enter">
                <div class="w-form-row" data-contact-field="mobile">
                    <label class="w-form-label">{{ __('locale.Mobile Number') }}</label>
                    <div class="w-form-phone-row">
                        {{-- Same country-code select + Select2 upgrade as Partner Login. --}}
                        @include('worker.partials.country-code-select', [
                            'attr' => 'data-contact-country',
                            'selected' => ltrim((string) $customer->country_code, '+'),
                            'searchable' => true,
                        ])
                        <input type="tel" class="w-input" data-contact-mobile inputmode="numeric" maxlength="15">
                    </div>
                </div>
                <div class="w-form-row" data-contact-field="email">
                    <label class="w-form-label">{{ __('locale.Email') }}</label>
                    <input type="email" class="w-input" data-contact-email maxlength="255">
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block" data-contact-send>{{ __('locale.Send Code') }}</button>
            </div>

            <div data-contact-step="verify" hidden>
                {{-- "Enter the OTP sent to your WhatsApp number ending in 3401." /
                     "...your email ending in @example.com." (otp-destination.js). --}}
                <p class="w-form-hint" data-contact-target></p>
                <div class="w-form-row">
                    <input type="text" class="w-input w-input-otp" maxlength="4" inputmode="numeric" placeholder="••••" data-contact-otp>
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block" data-contact-verify>{{ __('locale.Verify & Continue') }}</button>
                <div class="w-form-resend">
                    <span>{{ __("locale.Didn't receive the OTP?") }}</span>
                    <a href="javascript:void(0)" data-contact-resend>{{ __('locale.Resend OTP') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
