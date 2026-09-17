@extends('layout.docs.docs_layout')

@section('title', 'Deal Pipeline Module - Reminders & Cron Jobs')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.deal-pipeline-module') }}">Deal Pipeline Module</a>
            </li>
            <li class="breadcrumb-item active">
                Reminders &amp; Cron Jobs
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Reminders &amp; Cron Jobs
    </h2>

    <p class="text-muted mb-4">
        Two scheduled commands: one for deal-specific reminders you set yourself, one that nags about deals nobody
        has touched in a while.
    </p>

    <!-- Deal reminders -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0"><code>reminders:ManageDealPipelineReminders</code> &mdash; Every Minute</h5>
        </div>

        <div class="card-body">

            <p>
                Command class <code>ManageDealPipelineReminder</code>. Every minute it queries
                <code>DealReminder::where('is_done',0)-&gt;where('is_notified',0)-&gt;where('reminder_at','&lt;=',now())</code>.
            </p>

            <p>For each due reminder:</p>

            <ol>
                <li>Loads the deal and the <strong>reminder's creator</strong> (<code>created_by</code>) &mdash; not necessarily the deal's <code>care_of</code> owner.</li>
                <li><strong>WhatsApp (primary channel):</strong> via a <code>Metawhatsappapi</code> config tagged for <code>todo_notification</code>, template <code>reminder_2</code>, populated with the admin's name, deal company/candidate, the reminder's free-text description, deal creation date, and a deep link back to the deal.</li>
                <li><strong>Email (secondary, non-blocking):</strong> to the admin's <code>working_email</code> if set, via a dedicated mail class, logged to a <code>deal_reminder_logs</code> channel.</li>
            </ol>

            <div class="alert alert-warning mb-0">
                <strong>A failed WhatsApp send is not retried.</strong> On success, the reminder is marked
                <code>is_notified=1, is_done=1, notified_at=now()</code>. On a WhatsApp exception, it's still marked
                <code>is_done=1</code> (just <code>is_notified=0</code>) &mdash; so the reminder is effectively
                abandoned rather than retried on the next run.
            </div>

        </div>

    </div>

    <!-- Stale deal notification -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0"><code>deals:notify-not-updated</code> &mdash; Daily at 09:00</h5>
        </div>

        <div class="card-body">

            <p>
                Command class <code>NotifyIfDealNotUpdated</code>. Finds every deal where
                <code>updated_at &lt;= now()-&gt;subDays(6)</code> &mdash; i.e. deals with no activity in 6+ days.
                For each, it skips if <code>care_of</code> is empty or that admin has no <code>working_email</code>,
                otherwise sends a single <strong>email-only</strong> notification (no WhatsApp) to the deal's
                assigned staff member.
            </p>

            <div class="alert alert-danger mb-0">
                <strong>No deduplication &mdash; this repeats daily.</strong> Unlike the reminder command above,
                there's no <code>is_notified</code>-style flag for the staleness check. A deal that stays untouched
                will trigger this email <strong>every single day at 9am</strong> until someone actually updates it
                (any edit, or adding a note, bumps <code>updated_at</code> and resets the 6-day clock &mdash; see
                <a href="{{ route('docs.deal-pipeline-module.notes-files') }}">Notes &amp; Files</a>).
            </div>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.deal-pipeline-module.notes-files') }}" class="btn btn-outline-secondary btn-sm">&larr; Notes &amp; Files</a>
        <a href="{{ route('docs.deal-pipeline-module.relationships') }}" class="btn btn-outline-primary btn-sm">Relationship to Leads &amp; Candidates &rarr;</a>
    </div>

</div>

@endsection
