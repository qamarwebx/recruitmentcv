{{-- Deal reminder (App\Mail\DealReminderMail). Shared QAMAR HIRE layout.
Variables: $user, $deal, $description. The View Deal link is unchanged. Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php
    $T = \App\Support\EmailTheme::class;
    // Same link as before (by APP_ENV).
    $dealUrl = (env('APP_ENV') === 'production' ? 'https://qamarhire.com' : 'https://crm.qamarhire.com') . '/admin/dealPipeline/list?open_deal_model=true&deal_model_id=' . $deal->id;
@endphp

@section('title', 'Deal Reminder')
@section('preheader', 'A reminder for your deal.')

@section('content')
    <h1 style="{{ $T::H1 }}">Deal Reminder</h1>
    <p style="{{ $T::P }}">Hi <span dir="auto" style="{{ $T::STRONG }}">{{ $user->name }}</span>,</p>
    <p style="{{ $T::P }}">This is a reminder for your deal. Please review the details below:</p>
    @include('emails.partials.details', ['rows' => [
        ['Deal', $deal->company ?? $deal->candidate ?? 'Deal Pipeline'],
        ['Description', $description],
        ['Created At', \Carbon\Carbon::parse($deal->created_at)->format('d M Y h:i A')],
    ]])
    <p style="{{ $T::P }}">Please make sure to follow up on this deal.</p>
    @include('emails.partials.button', ['url' => $dealUrl, 'label' => 'View Deal'])
@endsection
