{{-- RecruitmentCV notification email (App\Mail\RecruitmentNotificationMail, sent by App\Support\NotificationCenter).
Shared QAMAR HIRE layout. Variables: $subjectLine, $n (title, recipient_name, message, lines [[label, value]], action
[label, url], note, occurred_at, brand). Customer / team-member emails carry the partner's own brand (white-label):
the header and footer then show that brand instead of QAMAR HIRE. Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php
    $T = \App\Support\EmailTheme::class;
    $brand = $n['brand'] ?? ['name' => 'RecruitmentCV', 'logo' => null, 'site_url' => 'https://recruitmentcv.com'];
    // NotificationCenter::brand(): 'RecruitmentCV' = the platform itself; any other name = a partner's own website.
    $emailBrand = ($brand['name'] ?? 'RecruitmentCV') !== 'RecruitmentCV' ? $brand : null;
    $lines = array_values(array_filter((array) ($n['lines'] ?? []), fn ($l) => is_array($l) && isset($l[1]) && $l[1] !== '' && $l[1] !== null));
    $action = $n['action'] ?? null;
@endphp

@section('title', $subjectLine)
@section('preheader', $n['title'] ?? $subjectLine)

@section('content')
    <h1 style="{{ $T::H1 }}">{{ $n['title'] ?? $subjectLine }}</h1>
    @if (!empty($n['recipient_name']))
        <p style="{{ $T::P }}">Hello <span dir="auto" style="{{ $T::STRONG }}">{{ $n['recipient_name'] }}</span>,</p>
    @endif
    @if (!empty($n['message']))
        <p style="{{ $T::P }}">{{ $n['message'] }}</p>
    @endif
    @include('emails.partials.details', ['rows' => $lines])
    @if ($action && !empty($action[1]))
        @include('emails.partials.button', ['url' => $action[1], 'label' => $action[0], 'showLink' => true])
    @endif
    @if (!empty($n['note']))
        @include('emails.partials.note', ['text' => $n['note']])
    @endif
@endsection

@section('footer_note')
    {{ $n['occurred_at'] ?? '' }}@if (!$emailBrand && !empty($brand['site_url'])) &middot; <a href="{{ $brand['site_url'] }}" style="color:{{ $T::MUTED }};">{{ preg_replace('#^https?://#', '', $brand['site_url']) }}</a>@endif
@endsection
