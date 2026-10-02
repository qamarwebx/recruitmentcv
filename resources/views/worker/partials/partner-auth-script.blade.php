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
{{-- Shared CSRF token/retry helper (loads once; also used by the other auth script). --}}
<script src="{{ asset('worker/js/worker-csrf.js') }}?v={{ @filemtime(public_path('worker/js/worker-csrf.js')) ?: time() }}"></script>
{{-- Password show/hide (worker.partials.password-toggle) - modal and Partner Account. --}}
<script src="{{ asset('worker/js/password-toggle.js') }}?v={{ @filemtime(public_path('worker/js/password-toggle.js')) ?: time() }}"></script>
<script src="{{ asset('worker/js/partner-auth.js') }}?v={{ @filemtime(public_path('worker/js/partner-auth.js')) ?: time() }}"
    data-generate-otp-url="{{ route('worker.partner.otp.send') }}"
    data-validate-otp-url="{{ route('worker.partner.otp.verify') }}"
    data-login-url="{{ route('worker.partner.login') }}"
    data-register-url="{{ route('worker.partner.register') }}"
    data-register-pending-url="{{ route('worker.partner.register-pending') }}"
    data-check-mobile-url="{{ route('worker.partner.check-mobile') }}"
    data-verify-mobile-url="{{ route('worker.partner.mobile.verify') }}"
    data-password-login-url="{{ route('worker.partner.login.password') }}"
    data-email-otp-send-url="{{ route('worker.partner.login.email-otp.send') }}"
    data-email-otp-verify-url="{{ route('worker.partner.login.email-otp.verify') }}"
    data-password-reset-send-url="{{ route('worker.partner.password-reset.send') }}"
    data-password-reset-verify-url="{{ route('worker.partner.password-reset.verify') }}"
    data-password-reset-update-url="{{ route('worker.partner.password-reset.update') }}"
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
        'loginSubtitle' => __('locale.Sign in to your partner account'),
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
        'errSessionExpired' => __('locale.Your session has expired. Please refresh the page and try again.'),
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
        'continueLabel' => __('locale.Continue'),
        'signingIn' => __('locale.Signing in…'),
        'errPasswordRequired' => __('locale.Please enter your password.'),
        'errPasswordMin' => __('locale.The password must be at least 8 characters.'),
        'identifierMobile' => __('locale.Mobile Number'),
        'identifierAccount' => __('locale.Username or Email address'),
        'errIdentifierRequired' => __('locale.Please enter your username or email address.'),
        'otpHintMobile' => __('locale.Enter the 4-digit code sent to'),
        'otpHintEmail' => __('locale.If this account has a verified email address, we sent a 6-digit code to it.'),
        'changeNumber' => __('locale.Change number'),
        'changeAccount' => __('locale.Change username or email'),
        'forgotTitle' => __('locale.Forgot Password'),
        'forgotSubtitle' => __('locale.Enter your registered Partner email address.'),
        'resetTitle' => __('locale.Set New Password'),
        'resetSubtitle' => __('locale.Choose a new password for your partner account.'),
        'resetDoneTitle' => __('locale.Password Updated'),
        'sendResetOtp' => __('locale.Send verification code'),
        'otpHintReset' => __('locale.If this email is registered, we\'ve sent a 6-digit OTP to'),
        'changeEmail' => __('locale.Change email'),
        'backToLogin' => __('locale.Back to login'),
        'cancel' => __('locale.Cancel'),
        'continueToLogin' => __('locale.Continue to Login'),
        'backToAccount' => __('locale.Back to My Account'),
        'verifyOtp' => __('locale.Verify OTP'),
        'updatePassword' => __('locale.Update Password'),
        'updating' => __('locale.Updating…'),
        'errEmailRequired' => __('locale.Please enter a valid email address.'),
        'errPasswordConfirmRequired' => __('locale.Please confirm your new password.'),
        'errPasswordMismatch' => __('locale.The passwords do not match.'),
    ]) }}"></script>
