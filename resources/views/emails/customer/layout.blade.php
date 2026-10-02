{{-- Shared layout for partner-branded customer emails (App\Support\
CustomerMail). Email-safe: tables + inline CSS, no external CSS/JS, same
approach as emails/partner-email-verification. Expects $data['brand']
(CustomerMail::brand()); children fill 'preheader' and 'body'. --}}
@php
    $brand = $data['brand'];
    $isRtl = $brand['dir'] === 'rtl';
    $align = $isRtl ? 'right' : 'left';
@endphp
<!DOCTYPE html>
<html lang="{{ $brand['lang'] }}" dir="{{ $brand['dir'] }}" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ $brand['name'] }}</title>
    <!--[if mso]>
    <style type="text/css">body, table, td, p, a, span { font-family: Arial, Helvetica, sans-serif !important; }</style>
    <![endif]-->
    <style type="text/css">
        @media only screen and (max-width: 600px) {
            .email-card-pad { padding-left: 20px !important; padding-right: 20px !important; }
            .email-detail-label, .email-detail-value { display: block !important; width: 100% !important; }
            .email-detail-label { padding-bottom: 2px !important; }
            .email-btn a { display: block !important; }
        }
    </style>
</head>
<body dir="{{ $brand['dir'] }}" style="margin:0; padding:0; background-color:#f1f5f9; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">
    {{-- Inbox preview text (hidden in the message body). --}}
    <div style="display:none; max-height:0; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px; color:#f1f5f9;">
        @yield('preheader')
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f1f5f9" style="background-color:#f1f5f9;">
        <tr>
            <td align="center" style="padding:32px 12px;">
                <!--[if mso]><table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" dir="{{ $brand['dir'] }}"
                       style="max-width:600px; width:100%; background-color:#ffffff; border:1px solid #e2e8f0; border-radius:14px;">
                    {{-- Logo --}}
                    <tr>
                        <td align="center" style="padding:28px 32px 20px 32px;">
                            <a href="{{ $brand['site_url'] }}" target="_blank" style="text-decoration:none;">
                                <img src="{{ $brand['logo'] }}" width="200" alt="{{ $brand['name'] }}"
                                     style="display:block; width:200px; max-width:100%; height:auto; border:0; outline:none; text-decoration:none;">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr><td height="3" bgcolor="#1b74d1" style="background-color:#1b74d1; font-size:0; line-height:0;">&nbsp;</td></tr>
                            </table>
                        </td>
                    </tr>

                    @yield('body')

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:26px 32px 28px 32px; font-family:Arial, Helvetica, sans-serif; text-align:center;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr><td height="1" bgcolor="#e2e8f0" style="background-color:#e2e8f0; font-size:0; line-height:0;">&nbsp;</td></tr>
                            </table>
                            <p style="margin:18px 0 4px 0; font-size:13px; line-height:20px; color:#0f172a; font-weight:bold;" dir="auto">{{ $brand['name'] }}</p>
                            <p style="margin:0; font-size:12px; line-height:18px;">
                                <a href="{{ $brand['site_url'] }}" target="_blank" style="color:#1b74d1; text-decoration:none;" dir="ltr">{{ preg_replace('#^https?://#', '', rtrim($brand['site_url'], '/')) }}</a>
                            </p>
                            <p style="margin:8px 0 0 0; font-size:12px; line-height:18px; color:#94a3b8;">{{ __('locale.This is an automated message, please do not reply.') }}</p>
                            <p style="margin:6px 0 0 0; font-size:12px; line-height:18px; color:#94a3b8;" dir="auto">&copy; {{ date('Y') }} {{ $brand['name'] }}. {{ __('locale.All rights reserved.') }}</p>
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>
