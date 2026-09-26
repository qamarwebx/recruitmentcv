@php
    // Same 5-country set/order/default as the Driver app's phone dropdown
    // (qamrjob.com/driver, intl-tel-input onlyCountries: sa/in/qa/ae/kw,
    // preferredCountries: in/sa) - preferred pair first, then the rest
    // alphabetically. Sourced from `countries` (already has all 5 active
    // rows with correct country_code, and is the table generateOtp2()
    // itself validates country_code against) rather than the 2-row
    // otpcountrycodes table, so no DB changes were needed and the shared
    // main-site OTP dropdown (which also reads otpcountrycodes) is untouched.
    $partnerCountryCodeOrder = ['966', '91', '965', '974', '971'];
    $partnerCountryFlags = [
        '966' => '🇸🇦',
        '91' => '🇮🇳',
        '965' => '🇰🇼',
        '974' => '🇶🇦',
        '971' => '🇦🇪',
    ];
    $partnerCountries = \App\Models\Country::whereIn('country_code', $partnerCountryCodeOrder)->get()->keyBy('country_code');

    // Display-only override for this phone selector specifically - the
    // Country model's own display_name (used for nationality/other Country
    // dropdowns site-wide) is left untouched, so this only affects how
    // Saudi Arabia is labeled here, not anywhere else the same countries
    // table is shown. +966 itself is unaffected either way, since the code
    // is always rendered separately from this label.
    $partnerCountryLabels = [
        '966' => __('locale.Saudi Arabia (Country Code)'),
    ];
@endphp
<div class="w-modal-backdrop" data-partner-auth-backdrop></div>
<div class="w-modal" data-partner-auth-modal role="dialog" aria-modal="true" aria-labelledby="partnerAuthTitle">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-partner-auth-close aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <h3 id="partnerAuthTitle" class="w-modal-title" data-partner-auth-title>{{ __('locale.Partner Login') }}</h3>
            <p class="w-modal-subtitle" data-partner-auth-subtitle>{{ __('locale.Sign in with your phone number to continue.') }}</p>

            <div class="w-form-alert" data-partner-auth-alert hidden></div>

            {{-- Step 1: mobile number (+ company/full name when registering) --}}
            <div data-partner-step="mobile">
                <div data-partner-register-fields hidden>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Full Name') }}</label>
                        <input type="text" class="w-input" placeholder="{{ __('locale.Full Name') }}" data-partner-reg-fullname>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Company / Recruitment Office Name') }}</label>
                        <input type="text" class="w-input" placeholder="e.g. Al Noor Recruitment Office" data-partner-reg-company>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Email') }}</label>
                        <input type="email" class="w-input" placeholder="{{ __('locale.Email') }}" data-partner-reg-email>
                    </div>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Mobile Number') }}</label>
                    <div class="w-form-phone-row">
                        <select class="w-select" data-partner-country-code>
                            @foreach ($partnerCountryCodeOrder as $code)
                                @continue(!isset($partnerCountries[$code]))
                                <option value="{{ $code }}">{{ $partnerCountryFlags[$code] }} {{ $partnerCountryLabels[$code] ?? trim($partnerCountries[$code]->display_name) }} +{{ $code }}</option>
                            @endforeach
                        </select>
                        <input type="tel" class="w-input" placeholder="{{ __('locale.Mobile Number') }}" data-partner-mobile inputmode="numeric">
                    </div>
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block" data-partner-send-otp>{{ __('locale.Send OTP') }}</button>

                <div class="w-auth-divider" data-partner-google-wrap>
                    <span>{{ __('locale.OR') }}</span>
                </div>
                {{--
                    route(), not a hardcoded qamarhire.com URL: this project's
                    own /partner-google/start (SocialLoginController::
                    redirectToGooglePartner()) runs the whole OAuth round trip
                    on THIS domain - configDriver() builds the Google
                    redirect_uri from route('google.callback'), which resolves
                    to https://recruitmentcv.com/auth/google/callback when the
                    request arrives here, so there is no cross-domain hop to
                    force (contrast with the source qamarhire.com codebase's
                    copy of this same modal, which still must hardcode the
                    qamarhire.com host - see that file for why).
                --}}
                {{-- Label text is mode-dependent ("Sign in"/"Sign up with
                Google" - login vs register), swapped by setMode() in
                partner-auth.js via this span, never the icon/link itself.
                Server-rendered default matches the modal's own default
                mode (login). --}}
                <a href="{{ route('partner.google.start') }}" class="w-btn w-btn-outline w-btn-block w-btn-google" data-partner-google-wrap>
                    <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.67-.22-2.45H12v4.64h6.48a5.54 5.54 0 0 1-2.4 3.63v3h3.88c2.27-2.09 3.56-5.17 3.56-8.82Z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.07 7.93-2.91l-3.88-3c-1.08.72-2.45 1.15-4.05 1.15-3.11 0-5.75-2.1-6.69-4.92H1.3v3.09A12 12 0 0 0 12 24Z"/><path fill="#FBBC05" d="M5.31 14.32a7.2 7.2 0 0 1 0-4.64V6.59H1.3a12 12 0 0 0 0 10.82l4.01-3.09Z"/><path fill="#EA4335" d="M12 4.75c1.76 0 3.35.61 4.6 1.8l3.44-3.44C17.94 1.19 15.24 0 12 0A12 12 0 0 0 1.3 6.59l4.01 3.09C6.25 6.86 8.89 4.75 12 4.75Z"/></svg>
                    <span data-partner-google-label>{{ __('locale.Sign in with Google') }}</span>
                </a>

                <p class="w-form-switch" data-partner-switch-to-register>
                    {{ __("locale.Don't have an account?") }} <a href="javascript:void(0)">{{ __('locale.Register as Partner') }}</a>
                </p>
                <p class="w-form-switch" data-partner-switch-to-login hidden>
                    {{ __('locale.Already have an account?') }} <a href="javascript:void(0)">{{ __('locale.Login') }}</a>
                </p>
            </div>

            {{-- Step 2: OTP verification --}}
            <div data-partner-step="otp" hidden>
                <p class="w-form-hint">{{ __('locale.Enter the 4-digit code sent to') }} <strong data-partner-mobile-display></strong></p>
                <div class="w-form-row">
                    <input type="text" class="w-input w-input-otp" maxlength="4" inputmode="numeric" placeholder="••••" data-partner-otp>
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block" data-partner-verify-otp>{{ __('locale.Verify & Continue') }}</button>
                <div class="w-form-resend">
                    <span data-partner-resend-timer>{{ __('locale.Resend available in') }} <strong data-partner-countdown>60</strong>s</span>
                    <a href="javascript:void(0)" data-partner-resend-btn hidden>{{ __('locale.Resend OTP') }}</a>
                </div>
                <button type="button" class="w-form-back" data-partner-back-to-mobile>&larr; {{ __('locale.Change number') }}</button>
            </div>

            {{-- Step 3: registration (fallback - only when someone tried to Login with an unregistered number) --}}
            <div data-partner-step="register" hidden>
                <p class="w-form-hint">{{ __("locale.We couldn't find an account for this number. Register as a partner below.") }}</p>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Full Name') }}</label>
                    <input type="text" class="w-input" data-partner-full-name>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Company / Recruitment Office Name') }}</label>
                    <input type="text" class="w-input" data-partner-company-name>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Email') }}</label>
                    <input type="email" class="w-input" placeholder="{{ __('locale.Email') }}" data-partner-email>
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block" data-partner-register-submit>{{ __('locale.Register') }}</button>
            </div>

            {{-- Step 4: pending approval (after a successful first-time
            registration - reached from either Step 1's Register form or
            Step 3's fallback form, both call the same submitRegistration()).
            Replaces the form entirely (via showStep(), same as every other
            step) instead of layering a small alert on top of a still-visible,
            still-submittable form - the message is the only thing left in
            the modal, so it reads as prominent/full-width, and there's
            nothing left to resubmit. Modal is left open; only the existing
            X button (outside .w-modal-body, unaffected by any of this)
            closes it. --}}
            <div data-partner-step="pending" hidden>
                <div class="w-form-alert is-success" data-partner-pending-message></div>
                <a href="https://wa.me/919004006272" target="_blank" rel="noopener" class="w-btn w-btn-outline w-btn-block">
                    {{ __('locale.Contact Support') }}
                </a>
            </div>
        </div>
    </div>
</div>
