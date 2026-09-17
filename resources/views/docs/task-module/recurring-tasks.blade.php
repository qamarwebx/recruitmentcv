@extends('layout.docs.docs_layout')

@section('title', 'Task Module - Recurring Tasks & Scheduling')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.task-module') }}">Task Module</a>
            </li>
            <li class="breadcrumb-item active">
                Recurring Tasks &amp; Scheduling
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Recurring Tasks &amp; Scheduling
    </h2>

    <p class="text-muted mb-4">
        <code>reminder_type</code> drives three genuinely different scheduling modes on a task, on top of the
        simple one-off case.
    </p>

    <div class="table-responsive mb-4">
        <table class="table table-bordered table-sm">
            <thead>
                <tr><th><code>reminder_type</code></th><th>Fields used</th><th>Behavior</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>OneTime</code></td>
                    <td><code>scheduled_date_time</code>, <code>scheduled_status</code></td>
                    <td>Fires once at the exact datetime. <code>scheduled_status</code> auto-flips to <code>false</code> after firing so it never repeats.</td>
                </tr>
                <tr>
                    <td><code>Recurring</code> &mdash; Daily</td>
                    <td><code>recurring_time</code> (JSON array)</td>
                    <td>Fires at every time listed in the array, every day &mdash; supports multiple reminders per day.</td>
                </tr>
                <tr>
                    <td><code>Recurring</code> &mdash; Weekly</td>
                    <td><code>recurring_weekdays</code> (JSON array), <code>recurring_time</code></td>
                    <td>Fires on the listed weekdays (e.g. Mon/Wed/Fri) at the given time(s).</td>
                </tr>
                <tr>
                    <td><code>Recurring</code> &mdash; Monthly</td>
                    <td><code>recurring_month_day</code>, <code>recurring_time</code></td>
                    <td>Fires on that day-of-month every month.</td>
                </tr>
                <tr>
                    <td><code>Recurring</code> &mdash; Yearly</td>
                    <td><code>recurring_year_month_day</code> (<code>MM-DD</code>), <code>recurring_time</code></td>
                    <td>Fires once a year on that month/day.</td>
                </tr>
                <tr>
                    <td><code>Custom</code></td>
                    <td><code>custom_start_date</code>, <code>custom_end_date</code>, <code>custom_time</code></td>
                    <td>Fires daily at one time, but only within the given date range window.</td>
                </tr>
                <tr>
                    <td><code>None</code> / <code>Periodic</code></td>
                    <td>&mdash;</td>
                    <td>No automatic reminder, or a looser periodic mode with limited dedicated logic.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="alert alert-info">
        The matching engine that actually evaluates all of this every minute lives in the
        <code>reminders:ManageTodoReminders</code> cron command &mdash; see
        <a href="{{ route('docs.task-module.reminders') }}">Reminder Delivery &amp; Cron Jobs</a> for exactly how
        each mode is matched against "right now."
    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Snoozing / Rescheduling</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Separately from the recurrence engine above, a dedicated "snooze" screen
                (<code>rescheduleTaskReminderView()</code> / <code>reminderRescheduleUpdate()</code>) writes
                directly to <code>reminder_before</code>/<code>reminder_at</code> to push a reminder to a specific
                future moment, picked up by its own cron
                (<code>reminders:send-reschedule</code> &mdash; see <a href="{{ route('docs.task-module.reminders') }}">Reminder Delivery &amp; Cron Jobs</a>).
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.task-module.creation') }}" class="btn btn-outline-secondary btn-sm">&larr; Task Creation &amp; Status Workflow</a>
        <a href="{{ route('docs.task-module.reminders') }}" class="btn btn-outline-primary btn-sm">Reminder Delivery &amp; Cron Jobs &rarr;</a>
    </div>

</div>

@endsection
