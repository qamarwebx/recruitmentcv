{{-- Partner security code (App\Mail\SendOTPVerification with data['view'] = this template): Partner Login with OTP
by username / email ('login'), Forgot Password ('reset') and Partner Account -> Email change (default). Shared QAMAR
HIRE layout. Variables: $data['name'], $data['company'], $data['EmailOtp'], optional $data['purpose'] and
$data['expires_minutes']. --}}
@extends('emails.layouts.brand')
@php
    $T = \App\Support\EmailTheme::class;
    $name = $data['name'] ?? '';
    $company = $data['company'] ?? '';
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
@endphp

@section('title', $text['heading'])
@section('preheader', $text['preheader'])

@section('content')
    <h1 style="{{ $T::H1 }}">{{ $text['heading'] }}</h1>
    <p style="{{ $T::P }}">Dear <span dir="auto" style="{{ $T::STRONG }}">{{ $name }}</span>,</p>
    <p style="{{ $T::P }} margin-bottom:22px;">{{ $text['intro'] }}{!! $company !== '' ? ' (<span dir="auto" style="' . e($T::STRONG) . '">' . e($company) . '</span>)' : '' !!}.{{ $isReset ? ' Use the OTP below to set a new password.' : '' }}</p>
    @include('emails.partials.otp', ['code' => (string) ($data['EmailOtp'] ?? '')])
    @include('emails.partials.note', ['title' => 'Security note:', 'text' => 'This OTP is valid for ' . ($minutes > 0 ? $minutes . ' minutes' : 'a limited time') . ' and can be used only once. Do not share it with anyone - our team will never ask for it.'])
    <p style="{{ $T::SMALL }} word-break:normal;">{{ $text['ignore'] }}</p>
@endsection
