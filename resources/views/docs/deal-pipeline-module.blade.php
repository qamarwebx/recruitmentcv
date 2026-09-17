@extends('layout.docs.docs_layout')

@section('title', 'Deal Pipeline Module')

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
                    Deal Pipeline Module
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-4">

        <h2 class="fw-bold">
            Deal Pipeline Module
        </h2>

        <p class="text-muted">
            This document explains the Deal Pipeline module (sidebar label "Deal Pipeline", routes
            <code>admin.dealPipeline.*</code>, table <code>deal_pipeline</code>): the Kanban/list pipeline, deal
            creation, its settings lookups, notes/files, reminder crons, and — importantly — how thin its real
            database links to Leads and Candidates actually are.
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

                    <a href="{{ route('docs.deal-pipeline-module.database') }}" class="list-group-item">
                        2. Database Structure
                    </a>

                    <a href="{{ route('docs.deal-pipeline-module.stages') }}" class="list-group-item">
                        3. Pipeline Stages &amp; Kanban Board
                    </a>

                    <a href="{{ route('docs.deal-pipeline-module.creation') }}" class="list-group-item">
                        4. Deal Creation
                    </a>

                    <a href="{{ route('docs.deal-pipeline-module.settings') }}" class="list-group-item">
                        5. Settings &amp; Lookups
                    </a>

                    <a href="{{ route('docs.deal-pipeline-module.notes-files') }}" class="list-group-item">
                        6. Notes &amp; Files
                    </a>

                    <a href="{{ route('docs.deal-pipeline-module.reminders') }}" class="list-group-item">
                        7. Reminders &amp; Cron Jobs
                    </a>

                    <a href="{{ route('docs.deal-pipeline-module.relationships') }}" class="list-group-item">
                        8. Relationship to Leads &amp; Candidates
                    </a>

                    <a href="{{ route('docs.deal-pipeline-module.filters') }}" class="list-group-item">
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
                        A <strong>deal</strong> represents a business opportunity moving through a 5-stage pipeline
                        (Prospecting &rarr; Discussion &rarr; Proposal Review &rarr; Closed Won / Closed Lost), shown
                        as either a Kanban board or a list. What fields a deal has depends on its
                        <strong>Business Type</strong>: "Job seeker" deals carry candidate/passport fields, every
                        other business type carries a simpler name/company field set.
                    </p>

                    <div class="alert alert-warning mb-0">
                        <strong>The database is looser than the concept suggests.</strong> Several columns that look
                        like foreign keys or typed fields are actually free text or varchar
                        (<code>candidate</code>, <code>passport_no</code>, <code>amount</code>,
                        <code>close_date</code>, <code>care_of</code>) — see
                        <a href="{{ route('docs.deal-pipeline-module.database') }}">Database Structure</a> — and the
                        module's ties to Leads and Candidates are much thinner than "placement pipeline" implies.
                        See <a href="{{ route('docs.deal-pipeline-module.relationships') }}">Relationship to Leads &amp; Candidates</a>
                        before assuming any automatic sync exists.
                    </div>

                </div>

            </div>

            <!-- Section cards -->

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Database Structure</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The <code>deal_pipeline</code> table, its related tables, and several columns that are
                        typed more loosely than their names suggest.
                        <a href="{{ route('docs.deal-pipeline-module.database') }}">Read the Database Structure guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Pipeline Stages &amp; Kanban Board</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The 5 live stages, and the three different ways a deal moves between them.
                        <a href="{{ route('docs.deal-pipeline-module.stages') }}">Read the Pipeline Stages &amp; Kanban Board guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Deal Creation</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The dual "Job seeker" vs. "common" field sets on manual creation, and how auto-creation from
                        a qualified lead works.
                        <a href="{{ route('docs.deal-pipeline-module.creation') }}">Read the Deal Creation guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Settings &amp; Lookups</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Business Type, Deal Stage, Recruit Status, and Job Title — including a naming collision with
                        an unrelated lookup elsewhere, and two real bugs worth knowing about.
                        <a href="{{ route('docs.deal-pipeline-module.settings') }}">Read the Settings &amp; Lookups guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Notes &amp; Files</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Dedicated notes and file-attachment tables, separate from the Lead module's equivalents.
                        <a href="{{ route('docs.deal-pipeline-module.notes-files') }}">Read the Notes &amp; Files guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Reminders &amp; Cron Jobs</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Per-deal reminders and a daily "this deal has gone stale" nag — including a caveat on each.
                        <a href="{{ route('docs.deal-pipeline-module.reminders') }}">Read the Reminders &amp; Cron Jobs guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Relationship to Leads &amp; Candidates</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        A one-time, one-directional copy from qualified leads with no reverse pointer, and zero real
                        linkage to Candidates or Employer.
                        <a href="{{ route('docs.deal-pipeline-module.relationships') }}">Read the Relationship to Leads &amp; Candidates guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Filters &amp; Saved Views</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Two different search scopes for list vs. Kanban, and per-admin saved filter/view persistence.
                        <a href="{{ route('docs.deal-pipeline-module.filters') }}">Read the Filters &amp; Saved Views guide &rarr;</a>
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
