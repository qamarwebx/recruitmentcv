{{-- Customer order confirmation (App\Mail\CustomerBookingConfirmation). --}}
@extends('emails.customer.layout')

@php
    $align = $data['brand']['dir'] === 'rtl' ? 'right' : 'left';
    $details = array_filter([
        __('locale.Order Number') => $data['reference_no'],
        __('locale.Candidate') => $data['candidate_name'],
        __('locale.Profession') => $data['candidate_profession'] !== '---' ? $data['candidate_profession'] : null,
        __('locale.Age') => $data['candidate_age'] ? $data['candidate_age'] . ' ' . __('locale.years old') : null,
        __('locale.Work Location') => $data['work_city'],
        __('locale.Embassy') => $data['embassy'],
        __('locale.Recruitment Office') => $data['brand']['name'],
        __('locale.Booking Date') => $data['booking_date'],
    ], fn ($value) => filled($value));
@endphp

@section('preheader')
    {{ __('locale.Your order :reference for :candidate has been received.', ['reference' => $data['reference_no'], 'candidate' => $data['candidate_name']]) }}
@endsection

@section('body')
    <tr>
        <td class="email-card-pad" style="padding:30px 40px 6px 40px; font-family:Arial, Helvetica, sans-serif; color:#0f172a; text-align:{{ $align }};">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 16px auto;">
                <tr>
                    <td align="center" bgcolor="#ecfdf5" style="background-color:#ecfdf5; color:#047857; border-radius:999px; padding:6px 16px; font-size:12px; line-height:16px; font-weight:bold; letter-spacing:0.5px; text-transform:uppercase;">
                        &#10003; {{ __('locale.Order Confirmed') }}
                    </td>
                </tr>
            </table>
            <h1 style="margin:0 0 18px 0; font-size:22px; line-height:30px; font-weight:bold; color:#0f172a; text-align:center;">
                {{ __('locale.Thank you for your order!') }}
            </h1>
            <p style="margin:0 0 12px 0; font-size:15px; line-height:24px; color:#334155;">
                {{ __('locale.Dear') }} <span dir="auto" style="font-weight:bold; color:#0f172a;">{{ $data['customer_name'] }}</span>,
            </p>
            <p style="margin:0 0 22px 0; font-size:15px; line-height:24px; color:#334155;">
                {!! __('locale.We have received your order to hire :candidate. Our recruitment team will review it and contact you shortly.', ['candidate' => '<span dir="auto" style="font-weight:bold; color:#0f172a;">' . e($data['candidate_name']) . '</span>']) !!}
            </p>
        </td>
    </tr>

    {{-- Order details --}}
    <tr>
        <td class="email-card-pad" style="padding:0 40px; font-family:Arial, Helvetica, sans-serif;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f8fafc"
                   style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:10px;">
                <tr>
                    <td style="padding:6px 18px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            @foreach ($details as $label => $value)
                                <tr>
                                    <td class="email-detail-label" width="42%" valign="top"
                                        style="padding:10px 0; font-size:13px; line-height:20px; color:#64748b; text-align:{{ $align }};{{ !$loop->last ? ' border-bottom:1px solid #e2e8f0;' : '' }}">
                                        {{ $label }}
                                    </td>
                                    <td class="email-detail-value" valign="top" dir="auto"
                                        style="padding:10px 0; font-size:14px; line-height:20px; color:#0f172a; font-weight:bold; text-align:{{ $align }};{{ !$loop->last ? ' border-bottom:1px solid #e2e8f0;' : '' }}">
                                        {{ $value }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Buttons --}}
    <tr>
        <td align="center" class="email-card-pad" style="padding:24px 40px 4px 40px; font-family:Arial, Helvetica, sans-serif;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center">
                <tr>
                    <td class="email-btn" align="center" bgcolor="#1b74d1" style="background-color:#1b74d1; border-radius:8px; padding:0;">
                        <a href="{{ $data['orders_url'] }}" target="_blank"
                           style="display:inline-block; padding:12px 26px; font-size:14px; line-height:20px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:8px;">
                            {{ __('locale.View My Orders') }}
                        </a>
                    </td>
                </tr>
            </table>
            <p style="margin:14px 0 0 0; font-size:13px; line-height:20px;">
                <a href="{{ $data['candidate_url'] }}" target="_blank" style="color:#1b74d1; text-decoration:underline;">{{ __('locale.View Candidate Profile') }}</a>
            </p>
        </td>
    </tr>

    {{-- Next steps --}}
    <tr>
        <td class="email-card-pad" style="padding:24px 40px 8px 40px; font-family:Arial, Helvetica, sans-serif; text-align:{{ $align }};">
            <h2 style="margin:0 0 10px 0; font-size:16px; line-height:24px; font-weight:bold; color:#0f172a;">{{ __('locale.What happens next?') }}</h2>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                @foreach ([
                    __('locale.Our team reviews your order and confirms the candidate\'s availability.'),
                    __('locale.We contact you to explain the visa and documentation steps.'),
                    __('locale.You can follow the status of your order at any time from My Orders.'),
                ] as $i => $step)
                    <tr>
                        <td width="30" valign="top" style="padding:4px 0;">
                            <span style="display:inline-block; width:22px; height:22px; line-height:22px; border-radius:11px; background-color:#eff6ff; color:#1b74d1; font-size:12px; font-weight:bold; text-align:center;">{{ $i + 1 }}</span>
                        </td>
                        <td valign="top" style="padding:5px 0; font-size:14px; line-height:21px; color:#334155; text-align:{{ $align }};">{{ $step }}</td>
                    </tr>
                @endforeach
            </table>
            @if (!empty($data['brand']['whatsapp']))
                <p style="margin:16px 0 0 0; font-size:13px; line-height:20px; color:#64748b;">
                    {{ __('locale.Questions about your order?') }}
                    <a href="{{ $data['brand']['whatsapp'] }}" target="_blank" style="color:#1b74d1; text-decoration:underline;">{{ __('locale.Chat with us on WhatsApp') }}</a>
                </p>
            @endif
        </td>
    </tr>
@endsection
