{{-- Customer email changed confirmation (App\Mail\CustomerEmailChanged),
sent to the NEW address after the OTP was verified. --}}
@extends('emails.customer.layout')

@php
    $align = $data['brand']['dir'] === 'rtl' ? 'right' : 'left';
@endphp

@section('preheader')
    {{ __('locale.Your email address has been changed') }}
@endsection

@section('body')
    <tr>
        <td class="email-card-pad" style="padding:30px 40px 6px 40px; font-family:Arial, Helvetica, sans-serif; color:#0f172a; text-align:{{ $align }};">
            <h1 style="margin:0 0 18px 0; font-size:22px; line-height:30px; font-weight:bold; color:#0f172a; text-align:center;">
                {{ __('locale.Email Address Updated') }}
            </h1>
            <p style="margin:0 0 12px 0; font-size:15px; line-height:24px; color:#334155;">
                {{ __('locale.Dear') }} <span dir="auto" style="font-weight:bold; color:#0f172a;">{{ $data['customer_name'] }}</span>,
            </p>
            <p style="margin:0 0 22px 0; font-size:15px; line-height:24px; color:#334155;">
                {{ __('locale.The email address on your account was successfully changed. From now on we will send your notifications to this address.') }}
            </p>
        </td>
    </tr>

    <tr>
        <td class="email-card-pad" style="padding:0 40px; font-family:Arial, Helvetica, sans-serif;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f8fafc"
                   style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:10px;">
                <tr>
                    <td style="padding:6px 18px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td class="email-detail-label" width="42%" valign="top" style="padding:10px 0; font-size:13px; line-height:20px; color:#64748b; border-bottom:1px solid #e2e8f0; text-align:{{ $align }};">{{ __('locale.New Email Address') }}</td>
                                <td class="email-detail-value" valign="top" dir="ltr" style="padding:10px 0; font-size:14px; line-height:20px; color:#0f172a; font-weight:bold; border-bottom:1px solid #e2e8f0; text-align:{{ $align }}; word-break:break-all;">{{ $data['new_email'] }}</td>
                            </tr>
                            <tr>
                                <td class="email-detail-label" width="42%" valign="top" style="padding:10px 0; font-size:13px; line-height:20px; color:#64748b; text-align:{{ $align }};">{{ __('locale.Date & Time') }}</td>
                                <td class="email-detail-value" valign="top" dir="ltr" style="padding:10px 0; font-size:14px; line-height:20px; color:#0f172a; font-weight:bold; text-align:{{ $align }};">{{ $data['changed_at'] }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td class="email-card-pad" style="padding:22px 40px 8px 40px; font-family:Arial, Helvetica, sans-serif; text-align:{{ $align }};">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td bgcolor="#fff7ed" style="background-color:#fff7ed; border-{{ $align }}:4px solid #f59e0b; border-radius:6px; padding:12px 14px; font-size:13px; line-height:20px; color:#7c2d12; text-align:{{ $align }};">
                        <strong>{{ __('locale.Security note:') }}</strong>
                        {{ __('locale.If you did not make this change, please contact us immediately and change your account password.') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td align="center" class="email-card-pad" style="padding:20px 40px 4px 40px; font-family:Arial, Helvetica, sans-serif;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center">
                <tr>
                    <td class="email-btn" align="center" bgcolor="#1b74d1" style="background-color:#1b74d1; border-radius:8px; padding:0;">
                        <a href="{{ $data['account_url'] }}" target="_blank"
                           style="display:inline-block; padding:12px 26px; font-size:14px; line-height:20px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:8px;">
                            {{ __('locale.Go to My Account') }}
                        </a>
                    </td>
                </tr>
            </table>
            @if (!empty($data['brand']['whatsapp']))
                <p style="margin:14px 0 0 0; font-size:13px; line-height:20px; color:#64748b;">
                    {{ __('locale.Need help?') }}
                    <a href="{{ $data['brand']['whatsapp'] }}" target="_blank" style="color:#1b74d1; text-decoration:underline;">{{ __('locale.Chat with us on WhatsApp') }}</a>
                </p>
            @endif
        </td>
    </tr>
@endsection
