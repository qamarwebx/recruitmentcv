{{-- Partner Activity alert to CRM staff (App\Mail\PartnerActivityAlert). Shared QAMAR HIRE layout.
Variables: $alert (event, subject, activity, partner_*, team_member, candidate_*, occurred_at). Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php
    $T = \App\Support\EmailTheme::class;
    $accent = ['candidate_viewed' => '#2563eb', 'hire_now' => '#16a34a', 'download_cv' => '#7c3aed'][$alert['event']] ?? '#2563eb';
    $heading = [
        'candidate_viewed' => 'A partner is viewing a candidate',
        'hire_now' => 'A partner wants to hire a candidate',
        'download_cv' => 'A partner downloaded a candidate CV',
    ][$alert['event']] ?? 'Partner Activity Alert';
@endphp

@section('title', $alert['subject'])
@section('preheader', $heading)

@section('content')
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 12px auto;">
        <tr><td bgcolor="{{ $accent }}" style="background-color:{{ $accent }}; border-radius:999px; padding:4px 12px; font-family:{{ $T::FONT }}; font-size:11px; line-height:16px; font-weight:bold; letter-spacing:0.5px; color:#ffffff;">PARTNER ACTIVITY ALERT</td></tr>
    </table>
    <h1 style="{{ $T::H1 }} margin-bottom:8px;">{{ $heading }}</h1>
    <p style="{{ $T::P }} text-align:center;">{{ $alert['activity'] }}</p>
    @include('emails.partials.details', ['rows' => [
        ['Partner', $alert['partner_name']],
        ['Mobile', $alert['partner_mobile']],
        ['City', $alert['partner_city']],
        ['Team Member', $alert['team_member'] ?: null],
        ['Candidate', $alert['candidate_name']],
        ['Candidate ID', $alert['candidate_ref']],
        ['Activity', $alert['activity']],
        ['Date / Time', $alert['occurred_at']],
    ]])
    @include('emails.partials.button', ['url' => $alert['candidate_url'], 'label' => 'View Candidate', 'showLink' => true])
@endsection

@section('footer_note')
    You receive this because you are a Partner Notification recipient (CRM &rarr; Website &rarr; Partner Notification).
@endsection
