{{-- Todo reminder (App\Mail\TodoReminderMail - the todo reminder commands). Shared QAMAR HIRE layout.
Variables: $data['body_msg'] (the reminder text, escaped; line breaks kept). Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php($T = \App\Support\EmailTheme::class)

@section('title', 'Todo Reminder')
@section('preheader', 'You have a todo reminder.')

@section('content')
    <h1 style="{{ $T::H1 }}">Todo Reminder</h1>
    <p style="{{ $T::P }}">{!! nl2br(e($data['body_msg'] ?? '')) !!}</p>
@endsection
