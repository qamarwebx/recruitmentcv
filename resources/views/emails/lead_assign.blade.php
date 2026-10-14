{{-- New lead assigned (App\Mail\LeadAssignMail). Shared QAMAR HIRE layout.
Variables: $admin, $lead. Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php
    $T = \App\Support\EmailTheme::class;
    $licenses = [];
    if (($lead->saudi_license ?? '') == 'yes') {
        $licenses[] = 'Saudi License';
    }
    if (($lead->india_license ?? '') == 'yes') {
        $licenses[] = 'Indian License';
    }
@endphp

@section('title', 'New Lead Assigned')
@section('preheader', 'A new lead has been assigned to you.')

@section('content')
    <h1 style="{{ $T::H1 }}">New Lead Assigned</h1>
    <p style="{{ $T::P }}">Hi <span dir="auto" style="{{ $T::STRONG }}">{{ $admin->name }}</span>,</p>
    <p style="{{ $T::P }}">A new lead has been assigned to you. Please review the details below.</p>
    @include('emails.partials.details', ['rows' => [
        ['Candidate Name', $lead->cand_name ?? '--'],
        ['Mobile Number', $lead->mob_no ?? '--'],
        ['WhatsApp Number', $lead->whatsapp_no ?? '--'],
        ['Job Title', $lead->required_service ?? '--'],
        ['Experience', $lead->experience ?? '--'],
        ['Expected Country', $lead->country ?? '--'],
        ['Driving License', count($licenses) ? implode(', ', $licenses) : '--'],
        ['Lead Date', optional($lead->lead_date)->format('d M Y h:i A') ?? '--'],
        ['Message', !empty($lead->message) ? $lead->message : null],
    ]])
    <p style="{{ $T::P }}">Please contact the candidate as soon as possible and update the CRM accordingly.</p>
@endsection
