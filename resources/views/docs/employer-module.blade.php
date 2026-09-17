@extends('layout.docs.docs_layout')

@section('title', 'Employer Module')

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
                    Employer Module
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-4">

        <h2 class="fw-bold">
            Employer Module
        </h2>

        <p class="text-muted">
            This document covers both sidebar items "Employer" (<code>admin.employer.*</code>) and "Employer plus"
            (<code>admin.employer.listp</code>) — two nearly-identical tables managed by one controller, with very
            different real-world usage.
        </p>

    </div>

    <div class="alert alert-danger">
        <strong>Start with <a href="{{ route('docs.employer-module.split') }}">Employer vs. Employer Plus</a>.</strong>
        This module is genuinely confusing — "Employer" and "Employer plus" look like a base feature and its
        upgrade, but they're actually two separate data pools, one of which (base Employer) holds almost no live
        data and has no independent creation path. Reading that page first will save you from several wrong
        assumptions the rest of this documentation would otherwise have to keep re-explaining.
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

                    <a href="{{ route('docs.employer-module.database') }}" class="list-group-item">
                        2. Database Structure
                    </a>

                    <a href="{{ route('docs.employer-module.split') }}" class="list-group-item">
                        3. Employer vs. Employer Plus
                    </a>

                    <a href="{{ route('docs.employer-module.creation') }}" class="list-group-item">
                        4. Employer Creation &amp; Editing
                    </a>

                    <a href="{{ route('docs.employer-module.visa-views') }}" class="list-group-item">
                        5. Visa Details Views
                    </a>

                    <a href="{{ route('docs.employer-module.assignment') }}" class="list-group-item">
                        6. Candidate Assignment
                    </a>

                    <a href="{{ route('docs.employer-module.payment') }}" class="list-group-item">
                        7. Payment Status &amp; Invoicing
                    </a>

                    <a href="{{ route('docs.employer-module.work-agreement') }}" class="list-group-item">
                        8. Work Agreement PDF
                    </a>

                    <a href="{{ route('docs.employer-module.filters') }}" class="list-group-item">
                        9. Filters, Permissions &amp; Routes
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

                    <p class="mb-0">
                        An <strong>employer</strong> record holds visa/vacancy details — employer name, visa
                        number, professions with opening counts, salary — and candidates get assigned into its open
                        slots. There are two tables for this (<code>employers</code> and
                        <code>employerpluses</code>), one controller managing both, and — in practice — one of them
                        (Employer Plus) is where almost all real data and functionality (payments, work agreements)
                        actually lives.
                    </p>

                </div>

            </div>

            <!-- Section cards -->

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Database Structure</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Both tables' columns, the shared join table, and a dead/unshipped payments table.
                        <a href="{{ route('docs.employer-module.database') }}">Read the Database Structure guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Employer vs. Employer Plus</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Why these look like duplicates but aren't, backed by live data — plus a confirmed bug where
                        "Add Employer" doesn't add an Employer.
                        <a href="{{ route('docs.employer-module.split') }}">Read the Employer vs. Employer Plus guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Employer Creation &amp; Editing</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The one real create form (Employer Plus only), and an edit handler that quietly falls back
                        between both tables.
                        <a href="{{ route('docs.employer-module.creation') }}">Read the Employer Creation &amp; Editing guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Visa Details Views</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Why "VisaDetShow" doesn't show the Orders module's <code>visadetails</code> table — and
                        where that table actually appears on the same page.
                        <a href="{{ route('docs.employer-module.visa-views') }}">Read the Visa Details Views guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Candidate Assignment</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        One shared method that assigns a candidate to either table, and how it differs from the
                        Orders module's own auto-assignment.
                        <a href="{{ route('docs.employer-module.assignment') }}">Read the Candidate Assignment guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Payment Status &amp; Invoicing</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Employer-Plus-only: the field and the invoicing integration that base Employer simply
                        doesn't have.
                        <a href="{{ route('docs.employer-module.payment') }}">Read the Payment Status &amp; Invoicing guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Work Agreement PDF</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Another Employer-Plus-only feature, registered under a URL prefix that suggests otherwise.
                        <a href="{{ route('docs.employer-module.work-agreement') }}">Read the Work Agreement PDF guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Filters, Permissions &amp; Routes</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Saved filters, permission gating, and two route/method names that don't do what they sound
                        like they do.
                        <a href="{{ route('docs.employer-module.filters') }}">Read the Filters, Permissions &amp; Routes guide &rarr;</a>
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
