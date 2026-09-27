@php
    // Set only when SocialLoginController::handlePartnerGoogleCallback()
    // redirected here for a first-time Google sign-in (no existing Partner
    // row for that email) - name/email are read from the same short-lived
    // server-side cache entry the token points to, never from the query
    // string itself, so this pre-fill can't be spoofed by editing the URL.
    // register() re-resolves the same cache entry independently at
    // submission time - this is display-only.
    $googleRegisterToken = request('register') && request('google_token') ? request('google_token') : null;
    $googleRegisterData = $googleRegisterToken
        ? \Illuminate\Support\Facades\Cache::get('partner_google_register:' . $googleRegisterToken)
        : null;
@endphp
<script src="{{ asset('worker/js/partner-auth.js') }}?v={{ @filemtime(public_path('worker/js/partner-auth.js')) ?: time() }}"
    data-generate-otp-url="{{ url('/generate-otp2') }}"
    data-validate-otp-url="{{ url('/validate-otp2') }}"
    data-login-url="{{ route('worker.partner.login') }}"
    data-register-url="{{ route('worker.partner.register') }}"
    data-register-pending-url="{{ route('worker.partner.register-pending') }}"
    data-check-mobile-url="{{ route('worker.partner.check-mobile') }}"
    data-verify-mobile-url="{{ route('worker.partner.mobile.verify') }}"
    data-open-login="{{ request('login') ? '1' : '0' }}"
    data-auth-message="{{ request('auth_message') }}"
    data-open-register="{{ $googleRegisterData ? '1' : '0' }}"
    data-google-token="{{ $googleRegisterData ? $googleRegisterToken : '' }}"
    data-google-name="{{ $googleRegisterData['name'] ?? '' }}"
    data-google-email="{{ $googleRegisterData['email'] ?? '' }}"
    data-i18n="{{ json_encode([
        'registerTitle' => __('locale.Register as Partner'),
        'registerSubtitle' => __('locale.Fill in your details below and verify your phone number to create a partner account.'),
        'loginTitle' => __('locale.Partner Login'),
        'loginSubtitle' => __('locale.Sign in with your phone number to continue.'),
        'signInWithGoogle' => __('locale.Sign in with Google'),
        'signUpWithGoogle' => __('locale.Sign up with Google'),
        'otpSubtitle' => __('locale.Enter the verification code to continue.'),
        'completeRegTitle' => __('locale.Complete Your Registration'),
        'completeRegSubtitle' => __('locale.We couldn\'t find an account for this number - just a few details to get started.'),
        'addMobileTitle' => __('locale.Verify Your Mobile Number'),
        'addMobileSubtitle' => __('locale.Add and verify a mobile number to continue.'),
        'errCompanyRequired' => __('locale.Please enter your company / recruitment office name.'),
        'errFullNameRequired' => __('locale.Please enter your full name.'),
        'errMobileRequired' => __('locale.Please enter your mobile number.'),
        'errMobileAlreadyRegistered' => __('locale.An account with this mobile number already exists.'),
        'sending' => __('locale.Sending…'),
        'sendOtp' => __('locale.Send OTP'),
        'errOtpNotSent' => __('locale.We could not send the OTP. Please try again in a moment.'),
        'errTechnical' => __('locale.A technical error occurred. Please try again shortly.'),
        'msgExistingAccount' => __('locale.An account already exists for this number - welcome back! Redirecting…'),
        'msgLoginSuccess' => __('locale.Login successful, redirecting…'),
        'msgPending' => __('locale.Your registration is pending approval.'),
        'msgRejected' => __('locale.Your registration has been rejected.'),
        'msgSomethingWrong' => __('locale.Something went wrong. Please try again.'),
        'msgMobileVerified' => __('locale.Mobile number verified!'),
        'errOtpRequired' => __('locale.Please enter the OTP.'),
        'verifying' => __('locale.Verifying…'),
        'verifyContinue' => __('locale.Verify & Continue'),
        'errOtpIncorrect' => __('locale.Incorrect OTP. Please try again.'),
        'errBothFields' => __('locale.Please fill in both fields.'),
        'submitting' => __('locale.Submitting…'),
        'register' => __('locale.Register'),
        'errCheckDetails' => __('locale.Please check the details and try again.'),
        'pendingTitle' => __('locale.Registration Submitted'),
    ]) }}"></script>
