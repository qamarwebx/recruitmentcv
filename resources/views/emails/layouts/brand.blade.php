{{-- Shared QAMAR HIRE email layout - header (logo on a navy band, gold
line), white card, sign-off and footer. Emails @extends it and fill:
  title (required), preheader (inbox preview text), content, and optionally
  signoff (default "Regards, QAMAR HIRE Team") and footer_note.
A white-label email (sent for a partner's own website) sets, before
@extends, $emailBrand = ['name' => ..., 'site_url' => ...]: its header and
footer then show that brand instead of QAMAR HIRE. Email-safe: tables +
inline CSS (App\Support\EmailTheme), no external CSS/JS; one small media
query for phones (ignored where unsupported). Twin file in both apps. --}}
@php
    $T = \App\Support\EmailTheme::class;
    $emailBrand = isset($emailBrand) && is_array($emailBrand) && filled($emailBrand['name'] ?? null) ? $emailBrand : null;
    $emailDir = ($emailDir ?? 'ltr') === 'rtl' ? 'rtl' : 'ltr';
    $emailLang = $emailLang ?? 'en';
@endphp
<!DOCTYPE html>
<html lang="{{ $emailLang }}" dir="{{ $emailDir }}" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', 'QAMAR HIRE')</title>
    <!--[if mso]>
    <style type="text/css">body, table, td, p, a, span, div, h1, h2 { font-family: Arial, Helvetica, sans-serif !important; }</style>
    <![endif]-->
    <style type="text/css">
        @media only screen and (max-width: 600px) {
            .email-outer { padding: 20px 10px !important; }
            .email-pad { padding-left: 22px !important; padding-right: 22px !important; }
            .email-head { padding: 22px 16px !important; }
            .email-logo { max-width: 180px !important; }
            .email-otp { font-size: 30px !important; letter-spacing: 7px !important; padding-left: 7px !important; }
            .email-otp-box { padding: 16px 22px !important; }
            .email-detail-label, .email-detail-value { display: block !important; width: auto !important; }
            .email-detail-label { padding-bottom: 0 !important; border-bottom: 0 !important; }
            .email-btn a { display: block !important; }
            .email-stack { display: block !important; width: 100% !important; padding-right: 0 !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:{{ $T::BG }}; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">
    @hasSection('preheader')
        {{-- Inbox preview text (hidden in the message body). --}}
        <div style="display:none; max-height:0; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px; color:{{ $T::BG }};">@yield('preheader')</div>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="{{ $T::BG }}" style="background-color:{{ $T::BG }};">
        <tr>
            <td align="center" class="email-outer" style="padding:32px 12px;">
                <!--[if mso]><table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff"
                       style="max-width:560px; width:100%; background-color:#ffffff; border:1px solid {{ $T::BORDER }}; border-radius:14px; border-collapse:separate;">

                    {{-- Header: navy band - the QAMAR HIRE logo on a white plate, or a partner's own brand name --}}
                    <tr>
                        <td align="center" class="email-head" bgcolor="{{ $T::NAVY }}" style="background-color:{{ $T::NAVY }}; border-radius:13px 13px 0 0; padding:26px 24px;">
                            @if ($emailBrand)
                                <span dir="auto" style="font-family:{{ $T::FONT }}; font-size:20px; line-height:28px; font-weight:bold; color:#ffffff;">{{ $emailBrand['name'] }}</span>
                            @else
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center">
                                    <tr>
                                        <td align="center" bgcolor="#ffffff" style="background-color:#ffffff; border-radius:10px; padding:12px 20px;">
                                            <img src="{{ $T::logoUrl() }}" width="200" alt="QAMAR HIRE" class="email-logo"
                                                 style="display:block; width:100%; max-width:200px; height:auto; margin:0 auto; border:0; outline:none; text-decoration:none; font-family:{{ $T::FONT }}; font-size:18px; font-weight:bold; color:{{ $T::NAVY }};">
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>
                    {{-- Gold accent line --}}
                    <tr>
                        <td height="4" bgcolor="{{ $T::GOLD }}" style="background-color:{{ $T::GOLD }}; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td class="email-pad" style="padding:32px 40px 6px 40px; font-family:{{ $T::FONT }}; font-size:15px; line-height:24px; color:{{ $T::TEXT }}; text-align:{{ $emailDir === 'rtl' ? 'right' : 'left' }};">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Sign-off --}}
                    <tr>
                        <td class="email-pad" style="padding:16px 40px 4px 40px; font-family:{{ $T::FONT }}; font-size:15px; line-height:22px; color:{{ $T::TEXT }}; text-align:{{ $emailDir === 'rtl' ? 'right' : 'left' }};">
                            @hasSection('signoff')
                                @yield('signoff')
                            @else
                                Regards,<br>
                                <span style="{{ $T::STRONG }}">{{ $emailBrand ? $emailBrand['name'] . ' Team' : 'QAMAR HIRE Team' }}</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:24px 32px 28px 32px; font-family:{{ $T::FONT }}; text-align:center;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr><td height="1" bgcolor="#e2e8f0" style="background-color:#e2e8f0; font-size:0; line-height:0;">&nbsp;</td></tr>
                            </table>
                            @hasSection('footer_note')
                                <p style="margin:16px 0 0 0; font-size:12px; line-height:18px; color:{{ $T::MUTED }};">@yield('footer_note')</p>
                            @endif
                            @if ($emailBrand)
                                <p style="margin:16px 0 4px 0; font-size:13px; line-height:20px; color:{{ $T::NAVY }}; font-weight:bold;">
                                    <span dir="auto">{{ $emailBrand['name'] }}</span>
                                    @if (filled($emailBrand['site_url'] ?? null))
                                        &middot; <a href="{{ $emailBrand['site_url'] }}" style="color:{{ $T::NAVY }}; text-decoration:none;">{{ preg_replace('#^https?://#', '', rtrim($emailBrand['site_url'], '/')) }}</a>
                                    @endif
                                </p>
                            @else
                                <p style="margin:16px 0 4px 0; font-size:13px; line-height:20px; color:{{ $T::NAVY }}; font-weight:bold;">QAMR International &middot; QAMAR HIRE</p>
                            @endif
                            <p style="margin:0; font-size:12px; line-height:18px; color:#94a3b8;">This is an automated message, please do not reply.</p>
                            <p style="margin:6px 0 0 0; font-size:12px; line-height:18px; color:#94a3b8;">&copy; {{ date('Y') }} {{ $emailBrand ? $emailBrand['name'] : 'QAMR International' }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>
