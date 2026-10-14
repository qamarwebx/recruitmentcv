{{-- Customer email changed confirmation (App\Mail\CustomerEmailChanged), sent to the NEW address after the
email-change OTP was verified. Shared QAMAR HIRE layout (emails.layouts.brand); English / Arabic by the customer's
site language ($data['brand']['lang'] / ['dir']). Variables: $data['customer_name'], $data['new_email']. --}}
@extends('emails.layouts.brand')
@php
    $T = \App\Support\EmailTheme::class;
    $emailLang = $data['brand']['lang'] ?? 'en';
    $emailDir = ($data['brand']['dir'] ?? 'ltr') === 'rtl' ? 'rtl' : 'ltr';
    $newEmail = '<span dir="ltr" style="' . e($T::STRONG) . ' word-break:break-all;">' . e($data['new_email'] ?? '') . '</span>';
@endphp

@section('title', __('locale.Your Email Address Has Been Changed'))
@section('preheader', __('locale.Your Email Address Has Been Changed'))

@section('content')
    <p style="{{ $T::P }}">{{ __('locale.Dear') }} <span dir="auto" style="{{ $T::STRONG }}">{{ $data['customer_name'] ?? '' }}</span>,</p>
    <p style="{{ $T::P }}">{!! __('locale.Your email address has been successfully changed to :email.', ['email' => $newEmail]) !!}</p>
    @include('emails.partials.note', ['text' => __('locale.If you did not make this change, please contact our support team immediately.')])
@endsection

@section('signoff')
    {{ __('locale.Regards,') }}<br><span style="{{ $T::STRONG }}">{{ __('locale.QAMAR HIRE Team') }}</span>
@endsection
