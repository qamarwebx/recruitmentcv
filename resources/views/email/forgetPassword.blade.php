{{-- Customer password reset link (UserLoginController - Mail::send('email.forgetPassword', ['token' => $token])).
Shared QAMAR HIRE layout (the Bootstrap stylesheet it used is not email-safe). Variable: $token. Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php
    $T = \App\Support\EmailTheme::class;
    $resetUrl = route('password.reset.form', $token);
@endphp

@section('title', 'Reset Password')
@section('preheader', 'Reset the password of your account.')

@section('content')
    <h1 style="{{ $T::H1 }}">Reset Password</h1>
    <p style="{{ $T::P }}">Hello!</p>
    <p style="{{ $T::P }}">You are receiving this email because we received a password reset request for your account.</p>
    @include('emails.partials.button', ['url' => $resetUrl, 'label' => 'Reset Password'])
    <p style="{{ $T::P }} margin-top:16px;">This password reset link will expire in 60 minutes.</p>
    <p style="{{ $T::P }}">If you did not request a password reset, no further action is required.</p>
    <p style="{{ $T::SMALL }}">If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: <a href="{{ $resetUrl }}" style="color:{{ $T::MUTED }};">{{ $resetUrl }}</a></p>
@endsection

@section('signoff')
    Regards,<br><span style="{{ $T::STRONG }}">Qamar International</span>
@endsection
