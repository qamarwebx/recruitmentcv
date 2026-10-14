{{-- Assigned task reminder (App\Mail\AssignedTodoReminderMail). Shared QAMAR HIRE layout.
Variables: $assigneeName, $taskId, $taskTitle, $taskDescription, $reminderTypeLabel, $scheduledTime, $startDate,
$endDate. The View / Reschedule links are unchanged. Twin file in both apps. --}}
@extends('emails.layouts.brand')
@php
    $T = \App\Support\EmailTheme::class;
    // Same links as before (by APP_ENV).
    $taskBase = env('APP_ENV') === 'production' ? 'https://qamarhire.test' : 'https://crm.qamarhire.com';
@endphp

@section('title', 'Task Reminder')
@section('preheader', 'Reminder: ' . $taskTitle)

@section('content')
    <h1 style="{{ $T::H1 }}">Task Reminder</h1>
    <p style="{{ $T::P }}">Hi <span dir="auto" style="{{ $T::STRONG }}">{{ $assigneeName }}</span>,</p>
    <p style="{{ $T::P }}">This is a reminder for the task assigned to you. Please review the details below:</p>
    @include('emails.partials.details', ['rows' => [
        ['Task', $taskTitle],
        ['Description', $taskDescription],
        ['Reminder Type', $reminderTypeLabel],
        ['Time', $scheduledTime ?: null],
        ['Start Date', $startDate ?: null],
        ['End Date', $endDate ?: null],
    ]])
    <p style="{{ $T::P }}">Please make sure to complete this task on time.</p>
    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
        <tr>
            {{-- side by side; stacked on small phones (email-stack) --}}
            <td class="email-stack" style="padding:0 10px 8px 0;">@include('emails.partials.button', ['url' => $taskBase . '/admin/todo?open_todo_model=true&todo_model_task_id=' . $taskId, 'label' => 'View Task'])</td>
            <td class="email-stack" style="padding:0 0 8px 0;">@include('emails.partials.button', ['url' => $taskBase . '/admin/todo/reschedule_todo_task_id/' . $taskId, 'label' => 'Reschedule Task', 'color' => '#8a6417'])</td>
        </tr>
    </table>
@endsection
