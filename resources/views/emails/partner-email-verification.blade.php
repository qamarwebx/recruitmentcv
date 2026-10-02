{{-- Partner Account -> Email change verification code (SendOTPVerification
with data['view'] = this template). Email-safe: tables + inline CSS, no
external CSS/JS. Variables: $data['name'], $data['company'], $data['EmailOtp'], optional
$data['purpose'] ('login' / 'reset') and $data['expires_minutes']. --}}
@php
    $name = $data['name'] ?? '';
    $company = $data['company'] ?? '';
    $otp = $data['EmailOtp'] ?? '';
    // 'login' = Partner Login with OTP by username/email; 'reset' = Forgot
    // Password; default = email change.
    $purpose = $data['purpose'] ?? null;
    $isLogin = $purpose === 'login';
    $isReset = $purpose === 'reset';
    $minutes = (int) ($data['expires_minutes'] ?? 0);
    $text = [
        'preheader' => $isReset ? 'Your partner password reset code.' : ($isLogin ? 'Your partner login code.' : 'Your QAMR International email verification code.'),
        'heading' => $isReset ? 'Password Reset' : ($isLogin ? 'Login Verification' : 'Email Verification'),
        'intro' => $isReset ? 'We received a request to reset the password of your partner account' : ($isLogin ? 'Please use the OTP below to sign in to your partner account' : 'Please use the OTP below to verify the new email address for your QamarHire partner account'),
        'ignore' => $isReset ? 'If you did not request a password reset, you can ignore this email - your current password stays unchanged and nobody can change it without this code.' : ($isLogin ? 'If you did not try to sign in, you can ignore this email - your account stays secure.' : 'If you did not request this change, you can ignore this email. Your current email address will stay unchanged.'),
    ];
    // Main RecruitmentCV logo for every partner's security email - never a
    // partner's own logo or subdomain: always the configured main domain.
    // Logo_eng_dark_email.png = Logo_eng_dark.webp's artwork flattened onto
    // white (this card's background), 657x120 (same 5.48:1 ratio): no
    // transparency, so clients/proxies that drop alpha (which turned the
    // WebP's transparent areas into black/green/red blocks) and Outlook
    // (no WebP support) all show it correctly.
    // ?v = file time, so clients/proxies never reuse an older cached copy.
    $logo = 'https://' . \App\Support\RecruitmentDomain::root() . '/user/img/logo/Logo_eng_dark_email.png?v=' . (@filemtime(public_path('user/img/logo/Logo_eng_dark_email.png')) ?: 1);
@endphp
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $text['heading'] }}</title>
    <!--[if mso]>
    <style type="text/css">body, table, td, p, a, span { font-family: Arial, Helvetica, sans-serif !important; }</style>
    <![endif]-->
    <style type="text/css">
        @media only screen and (max-width: 600px) {
            .email-card-pad { padding-left: 20px !important; padding-right: 20px !important; }
            .email-otp { font-size: 28px !important; letter-spacing: 6px !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">
    {{-- Inbox preview text (hidden in the message body). --}}
    <div style="display:none; max-height:0; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px; color:#f1f5f9;">
        {{ $text['preheader'] }}
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f1f5f9" style="background-color:#f1f5f9;">
        <tr>
            <td align="center" style="padding:32px 12px;">
                <!--[if mso]><table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff"
                       style="max-width:560px; width:100%; background-color:#ffffff; border:1px solid #e2e8f0; border-radius:14px;">
                    {{-- Logo --}}
                    <tr>
                        <td align="center" style="padding:30px 32px 22px 32px;">
                            <img src="{{ $logo }}" width="220" alt="RecruitmentCV"
                                 style="display:block; width:100%; max-width:220px; height:auto; margin:0 auto; border:0; outline:none; text-decoration:none;">
                        </td>
                    </tr>
                    {{-- Brand bar (logo red / blue) --}}
                    <tr>
                        <td style="padding:0 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="50%" height="3" bgcolor="#e5262d" style="background-color:#e5262d; font-size:0; line-height:0;">&nbsp;</td>
                                    <td width="50%" height="3" bgcolor="#1b74d1" style="background-color:#1b74d1; font-size:0; line-height:0;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-card-pad" style="padding:30px 40px 8px 40px; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
                            <h1 style="margin:0 0 18px 0; font-size:22px; line-height:30px; font-weight:bold; color:#0f172a; text-align:center;">
                                {{ $text['heading'] }}
                            </h1>
                            <p style="margin:0 0 12px 0; font-size:15px; line-height:24px; color:#334155;">
                                Dear <span dir="auto" style="font-weight:bold; color:#0f172a;">{{ $name }}</span>,
                            </p>
                            <p style="margin:0 0 22px 0; font-size:15px; line-height:24px; color:#334155;">
                                {{ $text['intro'] }}{!! $company !== '' ? ' (<span dir="auto" style="font-weight:bold; color:#0f172a;">' . e($company) . '</span>)' : '' !!}.{{ $isReset ? ' Use the OTP below to set a new password.' : '' }}
                            </p>
                        </td>
                    </tr>

                    {{-- OTP box --}}
                    <tr>
                        <td align="center" class="email-card-pad" style="padding:0 40px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center">
                                <tr>
                                    <td align="center" bgcolor="#eff6ff"
                                        style="background-color:#eff6ff; border:2px dashed #1b74d1; border-radius:12px; padding:16px 30px;">
                                        <div style="margin:0 0 6px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:16px; letter-spacing:1px; text-transform:uppercase; color:#1b74d1; font-weight:bold;">
                                            Your OTP
                                        </div>
                                        <div class="email-otp" style="font-family:'Courier New', Courier, monospace; font-size:34px; line-height:42px; font-weight:bold; letter-spacing:8px; color:#0f172a;">{{ $otp }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Security note --}}
                    <tr>
                        <td class="email-card-pad" style="padding:24px 40px 8px 40px; font-family:Arial, Helvetica, sans-serif;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td bgcolor="#fff7ed" style="background-color:#fff7ed; border-left:4px solid #f59e0b; border-radius:6px; padding:12px 14px; font-size:13px; line-height:20px; color:#7c2d12;">
                                        <strong>Security note:</strong> This OTP is valid for {{ $minutes > 0 ? $minutes . ' minutes' : 'a limited time' }} and can be used only once. Do not share it with anyone - our team will never ask for it.
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:18px 0 0 0; font-size:13px; line-height:20px; color:#64748b;">
                                {{ $text['ignore'] }}
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:26px 32px 28px 32px; font-family:Arial, Helvetica, sans-serif; text-align:center;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr><td height="1" bgcolor="#e2e8f0" style="background-color:#e2e8f0; font-size:0; line-height:0;">&nbsp;</td></tr>
                            </table>
                            <p style="margin:18px 0 4px 0; font-size:13px; line-height:20px; color:#0f172a; font-weight:bold;">QAMR International &middot; QamarHire</p>
                            <p style="margin:0; font-size:12px; line-height:18px; color:#94a3b8;">This is an automated message, please do not reply.</p>
                            <p style="margin:6px 0 0 0; font-size:12px; line-height:18px; color:#94a3b8;">&copy; {{ date('Y') }} QAMR International. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>
