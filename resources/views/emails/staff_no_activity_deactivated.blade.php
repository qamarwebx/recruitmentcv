{{-- Staff account deactivated - no activity today (App\Mail\StaffNoActivityDeactivatedMail).
Shared QAMAR HIRE layout. Variables: $user, $todayDate, $todoIds. Task links unchanged. Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php
    $T = \App\Support\EmailTheme::class;
    // Same links as before (by APP_ENV).
    $baseUrl = env('APP_ENV') === 'production' ? 'https://qamarhire.test' : 'https://crm.qamarhire.com';
@endphp

@section('title', 'Account Deactivated')
@section('preheader', 'Your account has been temporarily deactivated due to inactivity.')

@section('content')
    <h1 style="{{ $T::H1 }} color:{{ $T::RED }};">Account Deactivated</h1>
    <p style="{{ $T::P }}">Dear <span dir="auto" style="{{ $T::STRONG }}">{{ $user->name }}</span>,</p>
    <p style="{{ $T::P }}">We noticed that there was <strong>no activity recorded today ({{ $todayDate }})</strong> in your assigned tasks.</p>
    @include('emails.partials.note', ['tone' => 'red', 'text' => 'As per company policy, due to inactivity your account has been temporarily deactivated.'])
    <h2 style="{{ $T::H2 }}">Your Assigned Tasks for Today</h2>
    @if (!empty($todoIds))
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0;">
            @foreach ($todoIds as $taskId)
                <tr>
                    <td style="padding:4px 0; font-family:{{ $T::FONT }}; font-size:14px; line-height:20px;">
                        &bull;&nbsp;<a href="{{ $baseUrl }}/admin/todo?open_todo_model=true&todo_model_task_id={{ $taskId }}" style="{{ $T::LINK }}">View Task #{{ $taskId }}</a>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <p style="{{ $T::P }}">No tasks assigned for today.</p>
    @endif
    <p style="{{ $T::P }}">If you believe this action is incorrect, kindly reach out to the system admin to reactivate your account.</p>
@endsection

@section('signoff')
    Thank you,<br><span style="{{ $T::STRONG }}">System Automation</span>
@endsection
