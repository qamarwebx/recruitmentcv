{{-- Admin account deactivated - no todo created in 24 hours (App\Mail\AdminNoTodoLast24HoursDeactivatedMail).
Shared QAMAR HIRE layout. Variables: $admin, $date. Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php($T = \App\Support\EmailTheme::class)

@section('title', 'Account Deactivated')
@section('preheader', 'Your account has been temporarily deactivated.')

@section('content')
    <h1 style="{{ $T::H1 }} color:{{ $T::RED }};">Account Deactivated</h1>
    <p style="{{ $T::P }}">Dear <span dir="auto" style="{{ $T::STRONG }}">{{ $admin->name ?? 'Admin' }}</span>,</p>
    <p style="{{ $T::P }}">We noticed that there was <strong>no todo Created in the last 24 hours</strong> ({{ $date }}).</p>
    @include('emails.partials.note', ['tone' => 'red', 'text' => 'As per company policy, your account has been temporarily deactivated.'])
    <p style="{{ $T::P }}">If you believe this is a mistake, please contact the system administrator.</p>
@endsection

@section('signoff')
    Regards,<br><span style="{{ $T::STRONG }}">System Automation</span>
@endsection
