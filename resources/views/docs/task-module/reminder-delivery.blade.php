@extends('layout.docs.docs_layout')

@section('title', 'Task Module - Reminder Delivery & Cron Jobs')

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
                Reminder Delivery &amp; Cron Jobs
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Reminder Delivery &amp; Cron Jobs
    </h2>

    <p class="text-muted mb-4">
        Three separate scheduled commands actually deliver task reminders, plus a longer list of commands that
        exist in the codebase but are switched off.
    </p>

    <!-- Main engine -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0"><code>reminders:ManageTodoReminders</code> &mdash; the Primary Engine</h5>
        </div>

        <div class="card-body">

            <p>
                Runs <strong>every minute</strong> (<code>app/Console/Commands/ManageTodoReminders.php</code>),
                logs to a dedicated <code>todo_reminder_logs</code> channel. Always excludes tasks with
                <code>task_status</code> of <code>Complete</code> or <code>Achieved</code>.
            </p>

            <p>Matches every recurrence mode described in <a href="{{ route('docs.task-module.recurring') }}">Recurring Tasks &amp; Scheduling</a> against the current minute:</p>

            <ul>
                <li><strong>OneTime</strong>: exact-minute match on <code>scheduled_date_time</code> with <code>scheduled_status = true</code>; fires once then flips <code>scheduled_status</code> off.</li>
                <li><strong>Recurring</strong>: Daily/Weekly/Monthly/Yearly, each compared to the current minute via <code>Carbon::isSameMinute()</code> (Weekly also checks <code>recurring_weekdays</code> via JSON containment).</li>
                <li><strong>Custom</strong>: within the <code>[custom_start_date, custom_end_date]</code> window and within 59 seconds of <code>custom_time</code> daily.</li>
            </ul>

            <p class="mb-0">
                For each matching task, <code>sendReminder()</code> loops every assignee in <code>assignto_id</code>
                and checks the <code>notification_type</code> JSON array:
            </p>

            <ul class="mt-2 mb-0">
                <li><strong>WhatsApp</strong> (if listed) &mdash; looks up a <code>Metawhatsappapi</code> row tagged for <code>todo_notification</code>, posts a template message (<code>reminder_2</code>) with a deep link back to the task. Also sends to <code>support_team_number</code> if set.</li>
                <li><strong>Email</strong> (if listed) &mdash; <code>AssignedTodoReminderMail</code> to the assignee's <code>working_email</code>.</li>
            </ul>

            <p class="mt-2 mb-0">
                Every attempt (success or failure) is logged to <code>todoreminderresponses</code>. There is
                <strong>no in-app notification channel</strong> here &mdash; only WhatsApp and email.
            </p>

        </div>

    </div>

    <!-- Legacy overlap -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0"><code>auto:todoreminderscheduled</code> &mdash; an Overlapping Legacy Command</h5>
        </div>

        <div class="card-body">

            <p>
                Also runs <strong>every minute</strong>, in parallel with <code>ManageTodoReminders</code>
                (<code>app/Console/Commands/Todoscheduledreminder.php</code>). Queries tasks where
                <code>task_status != 'New Task'</code>, <code>scheduled_date_time</code> equals the current minute,
                <code>is_completed = false</code>, <code>scheduled_status = 1</code>. For each assignee it tries a
                direct WhatsApp API send first, falling back to the Meta WhatsApp template API on failure, and
                unconditionally also sends an email via a different template (<code>TodoReminderMail</code>,
                distinct from <code>AssignedTodoReminderMail</code>).
            </p>

            <div class="alert alert-danger mb-0">
                <strong>Known redundancy:</strong> a task with both WhatsApp and email notification types, whose
                <code>scheduled_date_time</code> lands on a matching minute, can be picked up by
                <strong>both</strong> commands in the same run &mdash; resulting in duplicate WhatsApp messages
                and/or duplicate emails to the same assignee. This isn't a hypothetical edge case; the two commands'
                matching conditions genuinely overlap.
            </div>

        </div>

    </div>

    <!-- Reschedule -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0"><code>reminders:send-reschedule</code></h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Every minute (<code>app/Console/Commands/SendTodoRescheduleRemindersEmails.php</code>), finds tasks
                where <code>reminder_at</code> falls in the current minute and status isn't Complete/Achieved
                &mdash; fed by the snooze/reschedule feature. Emails each assignee via
                <code>AssignedTodoReminderMail</code>. No WhatsApp, and no explicit dedup flag beyond re-saving the
                record.
            </p>

        </div>

    </div>

    <!-- Dead commands -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Disabled Commands (Not Running)</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                The following command classes still exist in <code>app/Console/Commands/</code> but are
                <strong>commented out</strong> in <code>Kernel.php</code> &mdash; do not document these as live
                behavior: <code>TodoReminderUrgent</code>, <code>TodoReminderHigh</code>,
                <code>TodoReminderMedium</code>, <code>TodoReminderLow</code>, <code>TodoDueTaskReminder</code>,
                <code>TodoDueNotifyReminder</code>, <code>DeactivateAdminIfNoTodoLast24Hours</code>, plus dated
                <code>21-01-2025</code> variants of several of these.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.task-module.recurring') }}" class="btn btn-outline-secondary btn-sm">&larr; Recurring Tasks &amp; Scheduling</a>
        <a href="{{ route('docs.task-module.shared-sink') }}" class="btn btn-outline-primary btn-sm">Shared Reminder Sink &rarr;</a>
    </div>

</div>

@endsection
