@extends('layout.docs.docs_layout')

@section('title', 'Task Module')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <div class="mb-3">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('docs.index') }}">
                        Documentation
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    Task Module
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-4">

        <h2 class="fw-bold">
            Task Module
        </h2>

        <p class="text-muted">
            This document explains the Task module (sidebar label "Task", routes <code>admin.todo.*</code>, table
            <code>todos</code>): task creation and its status workflow, recurring/scheduled reminders, delivery
            crons, its hidden role as a shared reminder sink for other modules, and assignment/permissions.
        </p>

    </div>

    <div class="row">

        <!-- Left Menu -->

        <div class="col-lg-3">

            <div class="card docs-toc">

                <div class="card-header">
                    <strong>Contents</strong>
                </div>

                <div class="list-group list-group-flush">

                    <a href="#overview" class="list-group-item">
                        1. Overview
                    </a>

                    <a href="{{ route('docs.task-module.database') }}" class="list-group-item">
                        2. Database Structure
                    </a>

                    <a href="{{ route('docs.task-module.creation') }}" class="list-group-item">
                        3. Task Creation &amp; Status Workflow
                    </a>

                    <a href="{{ route('docs.task-module.recurring') }}" class="list-group-item">
                        4. Recurring Tasks &amp; Scheduling
                    </a>

                    <a href="{{ route('docs.task-module.reminders') }}" class="list-group-item">
                        5. Reminder Delivery &amp; Cron Jobs
                    </a>

                    <a href="{{ route('docs.task-module.shared-sink') }}" class="list-group-item">
                        6. Shared Reminder Sink
                    </a>

                    <a href="{{ route('docs.task-module.assignment') }}" class="list-group-item">
                        7. Assignment &amp; Permissions
                    </a>

                    <a href="{{ route('docs.task-module.labels') }}" class="list-group-item">
                        8. Labels &amp; Departments
                    </a>

                    <a href="{{ route('docs.task-module.filters') }}" class="list-group-item">
                        9. Filters &amp; Saved Views
                    </a>

                </div>

            </div>

        </div>

        <!-- Right Content -->

        <div class="col-lg-9">

            <!-- Overview -->

            <div class="card mb-4" id="overview">

                <div class="card-header">
                    <h5 class="mb-0">Overview</h5>
                </div>

                <div class="card-body">

                    <p>
                        The Task module is a general-purpose staff to-do list with List and Kanban views, its own
                        recurring-reminder engine (one-time, daily, weekly, monthly, yearly, or a custom date-range
                        window), and WhatsApp/email delivery via scheduled cron jobs.
                    </p>

                    <div class="alert alert-warning mb-0">
                        <strong>It's also a hidden write-target for other modules.</strong> When staff add a
                        reminder from a Candidate, Contact, or Contact Plus record, the controller silently writes a
                        duplicate row into this module's <code>todos</code> table too &mdash; with no live link back
                        to the source record. Leads are the one exception; Lead reminders never touch this table.
                        See <a href="{{ route('docs.task-module.shared-sink') }}">Shared Reminder Sink</a> before
                        assuming every task you see here was created directly through the Task UI.
                    </div>

                </div>

            </div>

            <!-- Section cards -->

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Database Structure</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The <code>todos</code> table and its related tables — labels, departments, activity log,
                        notes, reminder delivery log, saved filters.
                        <a href="{{ route('docs.task-module.database') }}">Read the Database Structure guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Task Creation &amp; Status Workflow</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        How a task is created, the free-text status pipeline (New Task &rarr; ... &rarr; Complete),
                        and a known bug in bulk status updates.
                        <a href="{{ route('docs.task-module.creation') }}">Read the Task Creation &amp; Status Workflow guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Recurring Tasks &amp; Scheduling</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The six reminder modes — OneTime, Daily/Weekly/Monthly/Yearly recurring, and Custom date
                        ranges — plus snoozing.
                        <a href="{{ route('docs.task-module.recurring') }}">Read the Recurring Tasks &amp; Scheduling guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Reminder Delivery &amp; Cron Jobs</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The three active reminder-delivery crons (including a known duplicate-send overlap) and the
                        list of disabled legacy commands.
                        <a href="{{ route('docs.task-module.reminders') }}">Read the Reminder Delivery &amp; Cron Jobs guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Shared Reminder Sink</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Why Candidate, Contact, and Contact Plus reminders end up in this table too — and why Lead
                        reminders don't.
                        <a href="{{ route('docs.task-module.shared-sink') }}">Read the Shared Reminder Sink guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Assignment &amp; Permissions</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Multi-staff assignment via a comma-separated id list, and the dedicated Task permission set.
                        <a href="{{ route('docs.task-module.assignment') }}">Read the Assignment &amp; Permissions guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Labels &amp; Departments</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Two simple lookup lists, presented separately in Settings but managed by the same
                        controller as the rest of the module.
                        <a href="{{ route('docs.task-module.labels') }}">Read the Labels &amp; Departments guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Filters &amp; Saved Views</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        List vs. Kanban, the full set of filter scopes, and per-admin saved filter persistence.
                        <a href="{{ route('docs.task-module.filters') }}">Read the Filters &amp; Saved Views guide &rarr;</a>
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
