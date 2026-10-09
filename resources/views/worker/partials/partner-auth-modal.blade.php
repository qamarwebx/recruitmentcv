@php
    // Country-code list: App\Support\CountryCodeOptions (shared with the
    // customer account Change Mobile modal) via worker.partials.country-code-select.

    // Inline = rendered as the body of the dedicated /partner/login page
    // (PartnerAuthController::loginPage) instead of an overlay - same markup
    // and same partner-auth.js flow, just without the backdrop/close button.
    $inline = $inline ?? false;
@endphp
@unless ($inline)
    <div class="w-modal-backdrop" data-partner-auth-backdrop></div>
@endunless
<div class="w-modal{{ $inline ? ' is-inline' : '' }}" data-partner-auth-modal
    @if ($inline)
        data-partner-auth-inline
        data-inline-redirect="{{ $inlineRedirect ?? '' }}"
        data-inline-mode="{{ ($inlineMode ?? 'login') === 'register' ? 'register' : 'login' }}"
    @else
        role="dialog" aria-modal="true"
    @endif
    aria-labelledby="partnerAuthTitle">
    <div class="w-modal-dialog">
        @unless ($inline)
            <button type="button" class="w-modal-close" data-partner-auth-close aria-label="Close">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        @endunless

        <div class="w-modal-body">
            {{-- /partner/login page: the language switcher (same partial the
            public header used) sits on the right of the title. --}}
            @if ($inline)
                <div class="w-partner-auth-title-row">
            @endif
            <h3 id="partnerAuthTitle" class="w-modal-title" data-partner-auth-title>{{ __('locale.Partner Login') }}</h3>
            @if ($inline)
                    @include('worker.partials.language-switcher', ['switchRoute' => 'worker.lang.switch'])
                </div>
            @endif
            <p class="w-modal-subtitle" data-partner-auth-subtitle>{{ __('locale.Sign in to your partner account') }}</p>

            <div class="w-form-alert" data-partner-auth-alert hidden></div>

            {{-- Step 1: mobile number (+ company/full name when registering) --}}
            <div data-partner-step="mobile">
                {{-- Register-only fields, split around the shared Mobile Number row:
                Full Name, Company, [Mobile], Email, Password (with show/hide). --}}
                <div data-partner-register-fields hidden>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Full Name') }}</label>
                        <input type="text" class="w-input" placeholder="{{ __('locale.Full Name') }}" data-partner-reg-fullname>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Company / Recruitment Office Name') }}</label>
                        <input type="text" class="w-input" placeholder="e.g. Al Noor Recruitment Office" data-partner-reg-company>
                    </div>
                </div>
                {{-- Identifier: in Login, username / email address by default or the
                mobile number; Register / add-mobile always the mobile number -
                switched by the link below without a reload (partner-auth.js:
                identifierType). Rendered in the Login default state. --}}
                <div class="w-form-row w-auth-identifier" data-partner-identifier-row>
                    <label class="w-form-label" data-partner-identifier-label>{{ __('locale.Username or Email address') }}</label>
                    <div class="w-form-phone-row" data-partner-mobile-wrap hidden>
                        @include('worker.partials.country-code-select', ['attr' => 'data-partner-country-code'])
                        <input type="tel" class="w-input" placeholder="{{ __('locale.Enter mobile number') }}" data-partner-mobile inputmode="numeric" autocomplete="tel-national">
                    </div>
                    <div class="w-input-icon" data-partner-account-wrap>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 6.5 9 6.5 9-6.5"/></svg>
                        <input type="text" class="w-input" placeholder="{{ __('locale.Enter username or email address') }}" data-partner-account autocomplete="username" autocapitalize="off" spellcheck="false">
                    </div>
                    <button type="button" class="w-auth-identifier-toggle" data-partner-identifier-toggle
                        data-label-to-account="{{ __('locale.Use a username or email address instead') }}"
                        data-label-to-mobile="{{ __('locale.Use a mobile number instead') }}">{{ __('locale.Use a mobile number instead') }}</button>
                </div>
                <div data-partner-register-fields hidden>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Email') }}</label>
                        <input type="email" class="w-input" placeholder="{{ __('locale.Email') }}" data-partner-reg-email>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Password') }}</label>
                        <div class="w-input-password">
                            <input type="password" class="w-input" autocomplete="new-password" placeholder="{{ __('locale.Password') }}" data-partner-reg-password>
                            @include('worker.partials.password-toggle')
                        </div>
                    </div>
                </div>
                {{-- Login with Password is the default (partner-auth.js: loginMethod). --}}
                <div class="w-form-row" data-partner-password-row>
                    <label class="w-form-label">{{ __('locale.Password') }}</label>
                    <input type="password" class="w-input" autocomplete="current-password" placeholder="{{ __('locale.Enter your password') }}" data-partner-password>
                    <button type="button" class="w-auth-forgot-link" data-partner-forgot-open>{{ __('locale.Forgot Password?') }}</button>
                </div>
                {{-- Login only: password (above) -> method choice -> Continue.
                Register/add-mobile always use the mobile number + OTP
                (partner-auth.js hides the method choice and password row there). --}}
                <div class="w-auth-login-using" data-partner-login-method>
                    <span class="w-form-label">{{ __('locale.Login using') }}</span>
                    <div class="w-auth-radios" role="radiogroup">
                        <label class="w-auth-radio">
                            <input type="radio" name="partner_login_method" value="password" data-partner-method checked>
                            <span class="w-auth-radio-mark" aria-hidden="true"></span>
                            <span class="w-auth-radio-text"><strong>{{ __('locale.Password') }}</strong></span>
                        </label>
                        <label class="w-auth-radio">
                            <input type="radio" name="partner_login_method" value="otp" data-partner-method>
                            <span class="w-auth-radio-mark" aria-hidden="true"></span>
                            <span class="w-auth-radio-text"><strong>{{ __('locale.OTP') }}</strong> <small data-partner-otp-destination
                                data-label-mobile="{{ __('locale.(Send code to mobile)') }}"
                                data-label-account="{{ __('locale.(Send code to email)') }}">{{ __('locale.(Send code to email)') }}</small></span>
                        </label>
                    </div>
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block w-btn-continue" data-partner-send-otp hidden>{{ __('locale.Continue') }}</button>
                <button type="button" class="w-btn w-btn-primary w-btn-block w-btn-continue" data-partner-password-login>{{ __('locale.Continue') }}</button>

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
                {{-- "Enter the OTP sent to your WhatsApp number ending in 3401." -
                masked destination by channel, set by partner-auth.js
                (otp-destination.js / App\Support\OtpDestination). --}}
                <p class="w-form-hint" data-partner-otp-hint>{{ __('locale.Enter the OTP sent to your WhatsApp number.') }}</p>
                <div class="w-form-row">
                    <input type="text" class="w-input w-input-otp" maxlength="4" inputmode="numeric" autocomplete="one-time-code" placeholder="••••" data-partner-otp>
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block" data-partner-verify-otp>{{ __('locale.Verify & Continue') }}</button>
                <div class="w-form-resend">
                    <span>{{ __("locale.Didn't receive the OTP?") }}</span>
                    <span data-partner-resend-timer>{{ __('locale.Resend available in') }} <strong data-partner-countdown>60</strong>s</span>
                    <a href="javascript:void(0)" data-partner-resend-btn hidden>{{ __('locale.Resend OTP') }}</a>
                </div>
                <button type="button" class="w-form-back" data-partner-back-to-mobile>&larr; <span data-partner-back-label>{{ __('locale.Change number') }}</span></button>
            </div>

            {{-- Forgot Password ("Forgot Password?" under the Login password):
            registered email -> the shared OTP step above (partner-auth.js
            otpChannel 'reset') -> new password -> done. Server side:
            PartnerAuthController::passwordReset*(); nothing here logs in. --}}
            <div data-partner-step="forgot" hidden>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Email Address') }}</label>
                    <div class="w-input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 6.5 9 6.5 9-6.5"/></svg>
                        <input type="email" class="w-input" placeholder="{{ __('locale.Enter your registered email address') }}" data-partner-forgot-email autocomplete="email" autocapitalize="off" spellcheck="false">
                    </div>
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block" data-partner-forgot-send>{{ __('locale.Send verification code') }}</button>
                <button type="button" class="w-form-back" data-partner-forgot-back>&larr; <span data-partner-forgot-back-label>{{ __('locale.Back to login') }}</span></button>
            </div>

            <div data-partner-step="forgot-password" hidden>
                <div class="w-form-alert is-success">{{ __('locale.OTP verified successfully.') }}</div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.New Password') }}</label>
                    <div class="w-input-password">
                        <input type="password" class="w-input" autocomplete="new-password" placeholder="{{ __('locale.New Password') }}" data-partner-reset-password>
                        @include('worker.partials.password-toggle')
                    </div>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Confirm Password') }}</label>
                    <div class="w-input-password">
                        <input type="password" class="w-input" autocomplete="new-password" placeholder="{{ __('locale.Confirm Password') }}" data-partner-reset-password-confirm>
                        @include('worker.partials.password-toggle')
                    </div>
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block" data-partner-reset-submit>{{ __('locale.Update Password') }}</button>
            </div>

            <div data-partner-step="forgot-done" hidden>
                <div class="w-form-alert is-success">{{ __('locale.Password updated successfully.') }}</div>
                <p class="w-form-hint">{{ __('locale.You can now log in with your new password.') }}</p>
                <button type="button" class="w-btn w-btn-primary w-btn-block w-btn-continue" data-partner-reset-done>{{ __('locale.Continue to Login') }}</button>
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
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Password') }}</label>
                    <div class="w-input-password">
                        <input type="password" class="w-input" autocomplete="new-password" placeholder="{{ __('locale.Password') }}" data-partner-password-new>
                        @include('worker.partials.password-toggle')
                    </div>
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
                {{-- RecruitmentCV/Qamr support: the central WhatsApp settings
                (CRM -> Website -> WhatsApp), same as the Partner Portal button -
                never the registering partner's own number. The number is shown
                as "+<code> <number>" (PhoneNumber::international(), as elsewhere);
                hidden when none is set. --}}
                @php
                    $supportWhatsapp = \App\Models\PartnerPageContent::effectiveWhatsapp(null);
                    $supportDigits = preg_replace('/\D+/', '', (string) ($supportWhatsapp['number'] ?? ''));
                    $supportNumber = $supportDigits !== '' ? \App\Support\PhoneNumber::international(null, $supportDigits) : '';
                    if ($supportNumber !== '' && !str_starts_with($supportNumber, '+')) {
                        $supportNumber = '+' . $supportDigits;
                    }
                @endphp
                <a href="{{ $supportWhatsapp['link'] }}" target="_blank" rel="noopener" class="w-btn w-btn-outline w-btn-block w-support-contact" data-partner-support-contact>
                    <svg class="w-support-contact-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 14v-3a8 8 0 0 1 16 0v3"/><path d="M18 19c0 1.1-.9 2-2 2h-2"/><rect x="2.5" y="13" width="4" height="6" rx="1.5"/><rect x="17.5" y="13" width="4" height="6" rx="1.5"/></svg>
                    <span class="w-support-contact-text">
                        <span>{{ __('locale.Contact Support') }}</span>
                        @if ($supportNumber !== '')
                            <span class="w-support-contact-number" dir="ltr">{{ $supportNumber }}</span>
                        @endif
                    </span>
                </a>
                {{-- Shown after a completed registration (the partner is signed in;
                Hire Now / Download CV wait for approval). partner-auth.js sets the link. --}}
                <a href="#" class="w-btn w-btn-primary w-btn-block" data-partner-pending-dashboard hidden style="margin-top:10px;">
                    {{ __('locale.Go to Dashboard') }}
                </a>
            </div>
        </div>
    </div>
</div>
