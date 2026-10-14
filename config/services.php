<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'facebook' => [
        'client_id' => '',
        'client_secret' => '',
        'redirect' => '',
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_CALLBACK_URL'),
    ],
    // Partner Website -> SMTP -> "Add SMTP With Google" (App\Support\GoogleSmtpOAuth):
    // Gmail SMTP with OAuth 2.0 / XOAUTH2. Its own OAuth client (Google Cloud:
    // Gmail scope https://mail.google.com/ on the consent screen, the redirect
    // URI below registered). Both apps need the same client id/secret - a saved
    // Google SMTP is refreshed with it whichever app sends. Unset = the button
    // explains that Google sign-in for SMTP is not set up, and saved Google
    // SMTPs are skipped (the next SMTP / default settings send instead).
    'google_smtp' => [
        'client_id' => env('GOOGLE_SMTP_CLIENT_ID'),
        'client_secret' => env('GOOGLE_SMTP_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_SMTP_REDIRECT_URI', 'https://recruitmentcv.com/google/smtp/callback'),
        'host' => env('GOOGLE_SMTP_HOST', 'smtp.gmail.com'),
        'port' => (int) env('GOOGLE_SMTP_PORT', 587),
        'encryption' => env('GOOGLE_SMTP_ENCRYPTION', 'tls'),
    ],
    'google_contact' => [
        'client_id' => env('GOOGLE_CONTACT_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CONTACT_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_CONTACT_REDIRECT_URI'),
    ],
    'google_drive' => [
        'client_id' => env('GOOGLE_DRIVE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_DRIVE_REDIRECT_URI'),
    ],
    'facebook_capi' => [
        'pixel_id' => env('FACEBOOK_PIXEL_ID'),
        'access_token' => env('FACEBOOK_ACCESS_TOKEN'),
    ],

    'email_qamr' => [
        'base_url' => env('EMAIL_QAMR_BASE_URL', 'https://email.qamr.in'),
    ],

    // Root domain partner websites live under ({sub}.{root}) - same env/key
    // name as the CRM's services.hostinger.recruitmentcv_domain. Read only
    // through App\Support\RecruitmentDomain.
    'hostinger' => [
        'recruitmentcv_domain' => env('HOSTINGER_RECRUITMENTCV_DOMAIN', 'recruitmentcv.com'),
    ],


];
