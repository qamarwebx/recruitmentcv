@extends('layout.docs.docs_layout')

@section('title', 'Orders Module')

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
                    Orders Module
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-4">

        <h2 class="fw-bold">
            Orders Module
        </h2>

        <p class="text-muted">
            This document explains the Orders module (sidebar label "Orders", routes <code>admin.booking.*</code>,
            table <code>bookings</code>): how a customer books a candidate, order status, and — critically — how an
            order's visa step is what actually creates an Employer record, not the other way around.
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

                    <a href="{{ route('docs.orders-module.database') }}" class="list-group-item">
                        2. Database Structure
                    </a>

                    <a href="{{ route('docs.orders-module.creation') }}" class="list-group-item">
                        3. Order Creation
                    </a>

                    <a href="{{ route('docs.orders-module.status') }}" class="list-group-item">
                        4. Order Status &amp; Lifecycle
                    </a>

                    <a href="{{ route('docs.orders-module.visa-payment') }}" class="list-group-item">
                        5. Visa, Payment &amp; Employer Creation
                    </a>

                    <a href="{{ route('docs.orders-module.employer-assignment') }}" class="list-group-item">
                        6. Employer &amp; Candidate Assignment
                    </a>

                    <a href="{{ route('docs.orders-module.receiver-panel') }}" class="list-group-item">
                        7. Order Receiver Panel
                    </a>

                    <a href="{{ route('docs.orders-module.requirement') }}" class="list-group-item">
                        8. Requirement Snippets
                    </a>

                    <a href="{{ route('docs.orders-module.cancellation-notifications') }}" class="list-group-item">
                        9. Cancellation &amp; Notifications
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
                        An <strong>order</strong> is placed from the public/customer side of the site — a logged-in
                        customer account books a specific candidate. From there, admin staff progress the order
                        through visa and payment steps until it's marked complete.
                    </p>

                    <div class="alert alert-warning mb-0">
                        <strong>Orders, Employers, and Deal Pipeline are three different things that can look
                        related.</strong> An order does not belong to a pre-existing Employer record — completing
                        an order's visa step is what <em>creates</em> the Employer record. Deal Pipeline is a
                        separate sales/BD pipeline with its own (much looser) connection to Leads and Candidates.
                        See <a href="{{ route('docs.orders-module.visa-payment') }}">Visa, Payment &amp; Employer Creation</a>
                        before assuming any of these three modules feed each other automatically.
                    </div>

                </div>

            </div>

            <!-- Section cards -->

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Database Structure</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The <code>bookings</code> table and its related tables — payments, visa details, status
                        history, notifications, and more.
                        <a href="{{ route('docs.orders-module.database') }}">Read the Database Structure guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Order Creation</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The public-facing booking flow: reference numbers, the booking limit, and round-robin staff
                        assignment.
                        <a href="{{ route('docs.orders-module.creation') }}">Read the Order Creation guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Order Status &amp; Lifecycle</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The status lookup table, how an order advances, and a real FK to Candidates that isn't
                        actually kept in sync.
                        <a href="{{ route('docs.orders-module.status') }}">Read the Order Status &amp; Lifecycle guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Visa, Payment &amp; Employer Creation</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Why completing an order's visa step is what actually creates the Employer record — the most
                        important fact about this module.
                        <a href="{{ route('docs.orders-module.visa-payment') }}">Read the Visa, Payment &amp; Employer Creation guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Employer &amp; Candidate Assignment</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The shared assignment ledger table this module feeds into, and how it's also fed by a
                        completely separate module.
                        <a href="{{ route('docs.orders-module.employer-assignment') }}">Read the Employer &amp; Candidate Assignment guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Order Receiver Panel</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        A round-robin staff-assignment feature that's live and running despite being hidden from
                        the sidebar.
                        <a href="{{ route('docs.orders-module.receiver-panel') }}">Read the Order Receiver Panel guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Requirement Snippets</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Not a per-order spec — a settings-level list of terms shown on public candidate CV pages.
                        <a href="{{ route('docs.orders-module.requirement') }}">Read the Requirement Snippets guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Cancellation &amp; Notifications</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Cancelling and replacing candidates on an order, OTP verification, and a scheduled queue
                        worker that currently has nothing to process.
                        <a href="{{ route('docs.orders-module.cancellation-notifications') }}">Read the Cancellation &amp; Notifications guide &rarr;</a>
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
