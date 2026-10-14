{{-- Booking OTP (App\Mail\Otpsendnotifation - the booking OTP of the legacy booking flows). Shared QAMAR HIRE layout.
Variables: $data['name'], $data['email_code'], $data['reference'], $data['code_expired']. Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php($T = \App\Support\EmailTheme::class)

@section('title', 'Booking Notification')
@section('preheader', 'Your booking verification code.')

@section('content')
    <h1 style="{{ $T::H1 }}">Booking Verification</h1>
    <p style="{{ $T::P }}">Dear <span dir="auto" style="{{ $T::STRONG }}">{{ $data['name'] ?? '' }}</span>,</p>
    <p style="{{ $T::P }} margin-bottom:22px;">Please use the OTP below for booking reference no. <strong>{{ $data['reference'] ?? '' }}</strong>.</p>
    @include('emails.partials.otp', ['code' => (string) ($data['email_code'] ?? '')])
    @include('emails.partials.note', ['title' => 'Security note:', 'text' => 'This OTP will expire on ' . ($data['code_expired'] ?? '') . '. Please do not share it with anyone.'])
@endsection
