<?php

namespace App\Support;

/**
 * "Enter the OTP sent to your WhatsApp number ending in 3401." - the one
 * description of where a Partner OTP went, for every Partner OTP step
 * (Partner Login / Register / add or change mobile - partner-auth.js;
 * email login code and Forgot Password - same modal; Account -> Change
 * Email - partner-account-email.js). The texts are here (English / Arabic
 * via the locale files); public/worker/js/otp-destination.js picks one by
 * channel and fills in the MASKED destination (last 4 digits / email
 * domain only) from the number or address the code was actually sent to.
 * Only wording - OTP generation, sending, expiry, resend and verification
 * are untouched.
 */
final class OtpDestination
{
    /** Texts for otp-destination.js (merged into each OTP script's data-i18n). */
    public static function texts(): array
    {
        return [
            'otpSentWhatsapp' => __('locale.Enter the OTP sent to your WhatsApp number ending in :last4.'),
            'otpSentWhatsappPlain' => __('locale.Enter the OTP sent to your WhatsApp number.'),
            'otpSentSms' => __('locale.Enter the OTP sent to your mobile number ending in :last4.'),
            'otpSentSmsPlain' => __('locale.Enter the OTP sent to your mobile number.'),
            'otpSentEmail' => __('locale.Enter the OTP sent to your email ending in :domain.'),
            'otpSentEmailPlain' => __('locale.Enter the OTP sent to your email.'),
            // Login code / Forgot Password: the server never confirms that an
            // account exists, so these stay conditional.
            'otpSentEmailIfRegistered' => __('locale.If this email is registered, the OTP has been sent to your email ending in :domain.'),
            'otpSentEmailAccount' => __('locale.If this account has a verified email address, the OTP has been sent to it.'),
            'otpNotReceived' => __("locale.Didn't receive the OTP?"),
        ];
    }
}
