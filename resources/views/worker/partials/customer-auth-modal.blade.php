@php
    // Customer (users / `web` guard) login + register for the normal website
    // header - on the main domain and on every partner subdomain. Only reuses
    // the existing customer endpoints (generate-otp2 / validate-otp2 /
    // login-user2 / registeruser2 + customer Google). Which website the
    // customer belongs to is decided server-side (App\Support\CustomerSite),
    // never sent from here. Partner auth lives separately at /partner/login.
    $customerCountryCodeOrder = ['966', '91', '965', '974', '971'];
    $customerCountryFlags = ['966' => '🇸🇦', '91' => '🇮🇳', '965' => '🇰🇼', '974' => '🇶🇦', '971' => '🇦🇪'];
    $customerCountries = \App\Models\Country::whereIn('country_code', $customerCountryCodeOrder)->get()->keyBy('country_code');
    $customerCountryLabels = ['966' => __('locale.Saudi Arabia (Country Code)')];
    $customerAuthError = session('customer_auth_error');
    // Set by EnsureWorkerCustomerAuthenticated when a guest opens /account/*:
    // open this modal, then continue to that page after login. Server-side
    // value (getRequestUri), still limited to a same-host relative path.
    $customerAuthOpen = $customerAuthError || session('customer_auth_open');
    $customerAuthIntended = (string) session('customer_auth_intended', '');
    if (!str_starts_with($customerAuthIntended, '/') || str_starts_with($customerAuthIntended, '//')) {
        $customerAuthIntended = '';
    }
@endphp
<div class="w-modal-backdrop" data-customer-auth-backdrop></div>
<div class="w-modal" data-customer-auth-modal role="dialog" aria-modal="true" aria-labelledby="customerAuthTitle"
    data-send-url="{{ url('/generate-otp2') }}"
    data-verify-url="{{ url('/validate-otp2') }}"
    data-login-url="{{ route('login-user2') }}"
    data-register-url="{{ route('registeruser2') }}"
    data-register-check-url="{{ route('worker.customer.register.check') }}"
    data-auto-open="{{ $customerAuthOpen ? '1' : '0' }}"
    data-auto-message="{{ $customerAuthError }}"
    data-after-login-url="{{ $customerAuthIntended }}"
    data-i18n="{{ json_encode([
        'loginTitle' => __('locale.Customer Login'),
        'loginSubtitle' => __('locale.Sign in or create an account with your mobile number.'),
        'otpSubtitle' => __('locale.Enter the verification code to continue.'),
        'registerTitle' => __('locale.Complete Your Registration'),
        'registerSubtitle' => __('locale.We couldn\'t find an account for this number - just a few details to get started.'),
        'errMobileRequired' => __('locale.Please enter your mobile number.'),
        'errOtpRequired' => __('locale.Please enter the OTP.'),
        'errFullNameRequired' => __('locale.Please enter your full name.'),
        'errOtpNotSent' => __('locale.We could not send the OTP. Please try again in a moment.'),
        'errOtpIncorrect' => __('locale.Incorrect OTP. Please try again.'),
        'errTechnical' => __('locale.A technical error occurred. Please try again shortly.'),
        'errSessionExpired' => __('locale.Your session has expired. Please refresh the page and try again.'),
        'errCheckDetails' => __('locale.Please check the details and try again.'),
        'sending' => __('locale.Sending…'),
        'sendOtp' => __('locale.Send OTP'),
        'verifying' => __('locale.Verifying…'),
        'verifyContinue' => __('locale.Verify & Continue'),
        'submitting' => __('locale.Submitting…'),
        'register' => __('locale.Register'),
        'msgLoginSuccess' => __('locale.Login successful, redirecting…'),
        'signUpTitle' => __('locale.Sign Up'),
        'signUpSubtitle' => __('locale.Create your account with your name, email and mobile number.'),
        'signUp' => __('locale.Sign Up'),
        'signInWithGoogle' => __('locale.Sign in with Google'),
        'signUpWithGoogle' => __('locale.Sign up with Google'),
        'errEmailRequired' => __('locale.Please enter a valid email address.'),
    ]) }}">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-customer-auth-close aria-label="{{ __('locale.Close') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <h3 id="customerAuthTitle" class="w-modal-title" data-customer-auth-title>{{ __('locale.Customer Login') }}</h3>
            <p class="w-modal-subtitle" data-customer-auth-subtitle>{{ __('locale.Sign in or create an account with your mobile number.') }}</p>

            <div class="w-form-alert" data-customer-auth-alert hidden></div>

            {{-- Step 1: mobile number --}}
            <div data-customer-step="mobile">
                {{-- Sign Up mode only (same modal, same OTP flow; the account
                is created by the existing registeruser2 once the code is
                verified). --}}
                <div data-customer-signup-fields hidden>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Full Name') }}</label>
                        <input type="text" class="w-input" placeholder="{{ __('locale.Full Name') }}" data-customer-signup-name maxlength="255">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Email') }}</label>
                        <input type="email" class="w-input" placeholder="{{ __('locale.Email') }}" data-customer-signup-email maxlength="255">
                    </div>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Mobile Number') }}</label>
                    <div class="w-form-phone-row">
                        <select class="w-select" data-customer-country-code>
                            @foreach ($customerCountryCodeOrder as $code)
                                @continue(!isset($customerCountries[$code]))
                                <option value="{{ $code }}">{{ $customerCountryFlags[$code] }} {{ $customerCountryLabels[$code] ?? trim($customerCountries[$code]->display_name) }} +{{ $code }}</option>
                            @endforeach
                        </select>
                        <input type="tel" class="w-input" placeholder="{{ __('locale.Mobile Number') }}" data-customer-mobile inputmode="numeric">
                    </div>
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block" data-customer-send-otp>{{ __('locale.Send OTP') }}</button>

                <div class="w-auth-divider"><span>{{ __('locale.OR') }}</span></div>
                <a href="{{ route('googleLogin') }}" class="w-btn w-btn-outline w-btn-block w-btn-google">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.67-.22-2.45H12v4.64h6.48a5.54 5.54 0 0 1-2.4 3.63v3h3.88c2.27-2.09 3.56-5.17 3.56-8.82Z"/><path fill="#34A853" d="M12 24c3.24 0 5.95-1.07 7.93-2.91l-3.88-3c-1.08.72-2.45 1.15-4.05 1.15-3.11 0-5.75-2.1-6.69-4.92H1.3v3.09A12 12 0 0 0 12 24Z"/><path fill="#FBBC05" d="M5.31 14.32a7.2 7.2 0 0 1 0-4.64V6.59H1.3a12 12 0 0 0 0 10.82l4.01-3.09Z"/><path fill="#EA4335" d="M12 4.75c1.76 0 3.35.61 4.6 1.8l3.44-3.44C17.94 1.19 15.24 0 12 0A12 12 0 0 0 1.3 6.59l4.01 3.09C6.25 6.86 8.89 4.75 12 4.75Z"/></svg>
                    <span data-customer-google-label>{{ __('locale.Sign in with Google') }}</span>
                </a>

                <p class="w-form-switch" data-customer-switch-to-signup>
                    {{ __("locale.Don't have an account?") }} <a href="javascript:void(0)">{{ __('locale.Sign Up') }}</a>
                </p>
                <p class="w-form-switch" data-customer-switch-to-login hidden>
                    {{ __('locale.Already have an account?') }} <a href="javascript:void(0)">{{ __('locale.Login') }}</a>
                </p>
            </div>

            {{-- Step 2: OTP --}}
            <div data-customer-step="otp" hidden>
                {{-- "Enter the OTP sent to your WhatsApp number ending in 3401." (otp-destination.js). --}}
                <p class="w-form-hint" data-customer-otp-hint>{{ __('locale.Enter the OTP sent to your WhatsApp number.') }}</p>
                <div class="w-form-row">
                    <input type="text" class="w-input w-input-otp" maxlength="4" inputmode="numeric" placeholder="••••" data-customer-otp>
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block" data-customer-verify-otp>{{ __('locale.Verify & Continue') }}</button>
                <div class="w-form-resend">
                    <span>{{ __("locale.Didn't receive the OTP?") }}</span>
                    <span data-customer-resend-timer>{{ __('locale.Resend available in') }} <strong data-customer-countdown>60</strong>s</span>
                    <a href="javascript:void(0)" data-customer-resend-btn hidden>{{ __('locale.Resend OTP') }}</a>
                </div>
                <button type="button" class="w-form-back" data-customer-back>&larr; {{ __('locale.Change number') }}</button>
            </div>

            {{-- Step 3: new customer - only reached after the OTP is verified --}}
            <div data-customer-step="register" hidden>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Full Name') }}</label>
                    <input type="text" class="w-input" placeholder="{{ __('locale.Full Name') }}" data-customer-name>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Email') }}</label>
                    <input type="email" class="w-input" placeholder="{{ __('locale.Email') }}" data-customer-email>
                </div>
                <button type="button" class="w-btn w-btn-primary w-btn-block" data-customer-register>{{ __('locale.Register') }}</button>
            </div>
        </div>
    </div>
</div>
{{-- Shared CSRF token/retry helper (loads once; also used by the other auth script). --}}
<script src="{{ asset('worker/js/worker-csrf.js') }}?v={{ @filemtime(public_path('worker/js/worker-csrf.js')) ?: time() }}"></script>
@include('worker.partials.otp-destination-script')
<script src="{{ asset('worker/js/customer-auth.js') }}?v={{ @filemtime(public_path('worker/js/customer-auth.js')) ?: time() }}"></script>
