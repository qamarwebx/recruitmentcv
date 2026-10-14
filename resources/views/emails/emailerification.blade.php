{{-- Email OTP verification (App\Mail\SendOTPVerification's default view):
the customer email-change code and the associate email OTP. Shared QAMAR HIRE
layout (emails.layouts.brand). Variables: $data['name'], $data['EmailOtp'],
optional $data['expires_minutes'] (only when the sending flow has a fixed
validity). Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php
    $T = \App\Support\EmailTheme::class;
    $name = trim((string) ($data['name'] ?? ''));
    $minutes = (int) ($data['expires_minutes'] ?? 0);
@endphp

@section('title', 'Email Verification')
@section('preheader', 'Your QAMAR HIRE email verification code.')

@section('content')
    <h1 style="{{ $T::H1 }}">Email Verification</h1>
    <p style="{{ $T::P }}">Dear <span dir="auto" style="{{ $T::STRONG }}">{{ $name !== '' ? $name : 'User' }}</span>,</p>
    <p style="{{ $T::P }} margin-bottom:22px;">We received a request to verify your email address. Please use the OTP below to complete your verification.</p>
    @include('emails.partials.otp', ['code' => (string) ($data['EmailOtp'] ?? '')])
    @include('emails.partials.note', ['title' => 'Security note:', 'text' => 'This OTP is valid ' . ($minutes > 0 ? 'for ' . $minutes . ' minutes' : 'for a limited time only') . '. Please do not share it with anyone.'])
    <p style="{{ $T::SMALL }} word-break:normal;">If you did not request this verification, you can safely ignore this email.</p>
@endsection
