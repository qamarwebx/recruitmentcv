{{-- Deal not updated for 6 days (App\Mail\DealNotUpdatedMail). Shared QAMAR HIRE layout.
Variables: $deal. Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php($T = \App\Support\EmailTheme::class)

@section('title', 'Deal Not Updated')
@section('preheader', 'A deal has not been updated for the last 6 days.')

@section('content')
    <h1 style="{{ $T::H1 }}">Deal Not Updated</h1>
    <p style="{{ $T::P }}">Hello,</p>
    <p style="{{ $T::P }}">The following deal has not been updated for the last 6 days:</p>
    @include('emails.partials.details', ['rows' => [
        ['Deal ID', $deal->id],
        ['Title', $deal->job_title ?: $deal->job_title_other ?: $deal->deal_name ?: $deal->company ?: 'N/A'],
        ['Last Updated', $deal->updated_at],
    ]])
    <p style="{{ $T::P }}">Please take necessary action.</p>
    @include('emails.partials.button', ['url' => url('/admin/dealPipeline/list'), 'label' => 'Click Here to View Deals'])
@endsection

@section('signoff')
    Regards,<br><span style="{{ $T::STRONG }}">System Automation</span>
@endsection
