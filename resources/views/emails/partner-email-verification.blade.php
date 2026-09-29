{{-- Partner Account -> Email change verification code (SendOTPVerification
with data['view'] = this template). Email-safe: tables + inline CSS, no
external CSS/JS. Variables: $data['name'], $data['company'], $data['EmailOtp']. --}}
@php
    $name = $data['name'] ?? '';
    $company = $data['company'] ?? '';
    $otp = $data['EmailOtp'] ?? '';
    $logo = asset('user/img/logo/logo_english.png');
@endphp
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>Email Verification</title>
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
        Your QAMR International email verification code.
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
                            <img src="{{ $logo }}" width="220" alt="QAMR International"
                                 style="display:block; width:220px; max-width:100%; height:auto; border:0; outline:none; text-decoration:none;">
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
                                Email Verification
                            </h1>
                            <p style="margin:0 0 12px 0; font-size:15px; line-height:24px; color:#334155;">
                                Dear <span dir="auto" style="font-weight:bold; color:#0f172a;">{{ $name }}</span>,
                            </p>
                            <p style="margin:0 0 22px 0; font-size:15px; line-height:24px; color:#334155;">
                                Please use the OTP below to verify the new email address for your QamarHire partner account{!! $company !== '' ? ' (<span dir="auto" style="font-weight:bold; color:#0f172a;">' . e($company) . '</span>)' : '' !!}.
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
                                        <strong>Security note:</strong> This OTP is valid for a limited time. Do not share it with anyone.
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:18px 0 0 0; font-size:13px; line-height:20px; color:#64748b;">
                                If you did not request this change, you can ignore this email. Your current email address will stay unchanged.
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
