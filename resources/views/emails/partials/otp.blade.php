{{-- The OTP, the focus of a verification email: @include('emails.partials.otp', ['code' => ..., 'label' => optional])
padding-left = letter-spacing, so the digits stay centred. Twin file in both apps. --}}
@php($T = \App\Support\EmailTheme::class)
<table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:4px auto 22px auto;">
    <tr>
        <td align="center" class="email-otp-box" bgcolor="{{ $T::NAVY }}"
            style="background-color:{{ $T::NAVY }}; border-radius:12px; border-bottom:4px solid {{ $T::GOLD }}; padding:18px 34px;">
            <div style="margin:0 0 6px 0; font-family:{{ $T::FONT }}; font-size:12px; line-height:16px; letter-spacing:2px; text-transform:uppercase; font-weight:bold; color:{{ $T::GOLD }};">{{ $label ?? 'Your OTP' }}</div>
            <div class="email-otp" style="font-family:{{ $T::FONT }}; font-size:36px; line-height:44px; font-weight:bold; letter-spacing:10px; padding-left:10px; color:#ffffff;">{{ $code }}</div>
        </td>
    </tr>
</table>
