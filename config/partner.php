<?php

return [

    /*
    | Default Service Price (SAR) / Departure Days for a partner website's
    | Experience Type + Profession when the partner has saved no price of
    | its own (Partner Portal -> Price Update, partner candidate pages - see
    | App\Models\PartnerPrice). Intentionally separate from the CRM's
    | central recruitmentcv.com prices.
    */
    'default_price' => [
        'service_price' => env('PARTNER_DEFAULT_SERVICE_PRICE', 3000),
        'departure_days' => env('PARTNER_DEFAULT_DEPARTURE_DAYS', 15),
    ],

    /*
    | Partner OTP sending (POST /partner/otp/send - each request sends a
    | paid WhatsApp message). Counted per normalized mobile number AND per
    | requesting IP; either limit reached = HTTP 429.
    */
    'otp_send' => [
        'per_mobile' => env('PARTNER_OTP_MAX_PER_MOBILE', 5),
        'per_mobile_minutes' => env('PARTNER_OTP_MOBILE_WINDOW_MINUTES', 15),
        'per_ip' => env('PARTNER_OTP_MAX_PER_IP', 20),
        'per_ip_minutes' => env('PARTNER_OTP_IP_WINDOW_MINUTES', 60),
    ],

    /*
    | 6-digit codes emailed to a Partner (Login with OTP by username/email,
    | Forgot Password): validity and wrong-code attempts per code.
    */
    'email_code' => [
        'minutes' => env('PARTNER_EMAIL_CODE_MINUTES', 10),
        'attempts' => env('PARTNER_EMAIL_CODE_ATTEMPTS', 5),
    ],

    /*
    | Forgot Password: code checks per IP (on top of the per-code attempt
    | limit), and how long a verified code allows setting the new password.
    | Sending is limited by otp_send above (per email address + per IP).
    */
    'password_reset' => [
        'verify_per_ip' => env('PARTNER_RESET_VERIFY_MAX_PER_IP', 20),
        'verify_minutes' => env('PARTNER_RESET_VERIFY_WINDOW_MINUTES', 15),
        'update_minutes' => env('PARTNER_RESET_UPDATE_MINUTES', 10),
    ],

];
