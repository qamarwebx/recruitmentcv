{{-- Test email (App\Mail\SendTestMail). Shared QAMAR HIRE layout.
Variables: $data['email'], $data['msgBody']. Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php($T = \App\Support\EmailTheme::class)

@section('title', 'Test Email')
@section('preheader', 'Test email from QAMAR HIRE.')

@section('content')
    <h1 style="{{ $T::H1 }}">Test Email</h1>
    <p style="{{ $T::P }}">Dear <span dir="auto" style="{{ $T::STRONG }}">{{ $data['email'] ?? '' }}</span>,</p>
    <p style="{{ $T::P }}">{!! nl2br(e($data['msgBody'] ?? '')) !!}</p>
@endsection
