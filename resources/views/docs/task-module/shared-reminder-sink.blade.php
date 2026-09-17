@extends('layout.docs.docs_layout')

@section('title', 'Task Module - Shared Reminder Sink')

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
                Shared Reminder Sink
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Shared Reminder Sink
    </h2>

    <p class="text-muted mb-4">
        The Task module is genuinely dual-purpose: a standalone task list, <em>and</em> a hidden write-target for
        other modules' "Add Reminder" features.
    </p>

    <div class="alert alert-warning">
        <strong>There is no live link back to the source record.</strong> When another module writes a duplicate
        <code>todos</code> row, it's a one-way copy at creation time &mdash; there is no <code>candidate_id</code> /
        <code>contact_id</code> column on <code>todos</code>, and no polymorphic relation. Editing the reminder
        later in one place does not update the other.
    </div>

    <!-- Modules that duplicate -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Modules That Duplicate Into <code>todos</code></h5>
        </div>

        <div class="card-body">

            <p>
                When a reminder is added on one of these records, the controller writes to
                <strong>two tables at once</strong>: the module's own dedicated reminder table, and a duplicate
                <code>Todo</code> row (<code>task_status</code> hard-coded <code>"New Task"</code>,
                <code>reminder_cycle = "Low"</code>) &mdash; so the reminder also shows up in the Task module's own
                List/Kanban UI and gets picked up by the Todo reminder crons.
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr><th>Module</th><th>Its own reminder table</th><th>Controller methods</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Candidate</td><td><code>candidatereminders</code></td><td><code>CandidateController::candreminderstore()</code> / <code>candreminderUpdate()</code></td></tr>
                        <tr><td>Contacts (All Contacts)</td><td><code>allcontactreminders</code></td><td><code>AllContactController::addreminder()</code> / <code>updateaddreminder()</code></td></tr>
                        <tr><td>Contact Plus</td><td><code>contactreminders</code></td><td><code>ContactpController::contactaddreminder()</code> / <code>contactreminderupdate()</code></td></tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Leads is the exception -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The Notable Exception: Leads</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>LeadController.php</code> has <strong>zero</strong> references to <code>Todo</code> or
                <code>Todolabel</code>. Lead follow-ups and reminders (see the Lead module's
                <a href="{{ route('docs.lead-module.followup') }}">Follow-up</a> and
                <a href="{{ route('docs.lead-module.notes') }}">Lead Notes</a> pages) are handled entirely through
                their own separate mechanism and never touch the Task module at all. If you're looking for a lead's
                reminders inside the Task list, they won't be there.
            </p>

        </div>

    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">What This Means in Practice</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li>A "reminder" added from a Candidate, Contact, or Contact Plus record is really <strong>two separate database writes</strong> that can drift out of sync if one is edited without the other.</li>
                <li>Anyone building on top of the Task module should expect a mix of genuine, standalone tasks and these auto-duplicated reminders in the same <code>todos</code> table &mdash; with no column to reliably tell them apart other than the hard-coded <code>reminder_cycle = "Low"</code> convention used by the duplicating code.</li>
                <li>Leads are the odd one out — don't assume every CRM record type feeds this table.</li>
            </ul>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.task-module.reminders') }}" class="btn btn-outline-secondary btn-sm">&larr; Reminder Delivery &amp; Cron Jobs</a>
        <a href="{{ route('docs.task-module.assignment') }}" class="btn btn-outline-primary btn-sm">Assignment &amp; Permissions &rarr;</a>
    </div>

</div>

@endsection
