@extends('layout.docs.docs_layout')

@section('title', 'Contacts Module')

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
                    Contacts Module
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-4">

        <h2 class="fw-bold">
            Contacts Module
        </h2>

        <p class="text-muted">
            This document explains the Contacts module (sidebar label "Contacts", routes <code>admin.allcontact.*</code>,
            table <code>allcontacts</code>): creation, Google sync, groups, the read-only cross-reference to Leads,
            export/history, and filtering.
        </p>

    </div>

    <div class="alert alert-warning">
        <strong>Not the same as "Contact Plus".</strong> The sidebar has a separate module literally called
        "Contact Plus" (<code>admin.contact.*</code>, table <code>contactplus</code>). This documentation covers
        <strong>Contacts</strong> only.
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

                    <a href="{{ route('docs.contacts-module.database') }}" class="list-group-item">
                        2. Database Structure
                    </a>

                    <a href="{{ route('docs.contacts-module.creation') }}" class="list-group-item">
                        3. Contact Creation
                    </a>

                    <a href="{{ route('docs.contacts-module.google-sync') }}" class="list-group-item">
                        4. Google Contacts Sync
                    </a>

                    <a href="{{ route('docs.contacts-module.groups') }}" class="list-group-item">
                        5. Groups
                    </a>

                    <a href="{{ route('docs.contacts-module.lead-sync') }}" class="list-group-item">
                        6. Lead Sync
                    </a>

                    <a href="{{ route('docs.contacts-module.export') }}" class="list-group-item">
                        7. Export &amp; History
                    </a>

                    <a href="{{ route('docs.contacts-module.filters') }}" class="list-group-item">
                        8. Filters &amp; Saved Views
                    </a>

                    <a href="{{ route('docs.contacts-module.lifecycle') }}" class="list-group-item">
                        9. Lifecycle Status
                    </a>

                    <a href="#cron" class="list-group-item">
                        10. Cron Jobs
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
                        A <strong>contact</strong> is a general-purpose address-book entry &mdash; not necessarily a
                        recruitment lead. Contacts arrive three ways: manual entry, CSV import, or pulling from a
                        staff member's Google account. Each contact carries its own lifecycle status, lead stage,
                        group, and source classification, independent of the Lead module's own equivalents.
                    </p>

                    <p class="mb-0">
                        A daily cron job cross-references contacts against leads by phone number, purely for
                        reporting &mdash; <strong>it does not merge or promote data between the two modules</strong>.
                        See <a href="{{ route('docs.contacts-module.lead-sync') }}">Lead Sync</a> for why that
                        matters.
                    </p>

                </div>

            </div>

            <!-- Section cards -->

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Database Structure</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The <code>allcontacts</code> table and its related tables (notes, reminders, files, groups,
                        saved filters, export history).
                        <a href="{{ route('docs.contacts-module.database') }}">Read the Database Structure guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Contact Creation</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Manual entry and CSV import, plus the automatic WhatsApp trigger fired on creation.
                        <a href="{{ route('docs.contacts-module.creation') }}">Read the Contact Creation guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Google Contacts Sync</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The manual, admin-initiated OAuth pull from a Google account, and how it dedupes contacts.
                        <a href="{{ route('docs.contacts-module.google-sync') }}">Read the Google Contacts Sync guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Groups</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Named, capacity-limited buckets of contacts used mainly for WhatsApp campaign sizing.
                        <a href="{{ route('docs.contacts-module.groups') }}">Read the Groups guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Lead Sync</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The daily cron that cross-references Contacts and Leads by phone number &mdash; and why it's
                        a report, not a promotion pipeline.
                        <a href="{{ route('docs.contacts-module.lead-sync') }}">Read the Lead Sync guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Export &amp; History</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Two separate export systems: CSV download history, and an external email-marketing
                        subscriber sync.
                        <a href="{{ route('docs.contacts-module.export') }}">Read the Export &amp; History guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Filters &amp; Saved Views</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The full set of list-screen filter scopes, per-admin saved filters, and a caveat on the
                        generic custom-filter builder.
                        <a href="{{ route('docs.contacts-module.filters') }}">Read the Filters &amp; Saved Views guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Lifecycle Status</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Shared master data between Contacts and Contact Plus &mdash; not Leads &mdash; and managed
                        from a different module's controller than you'd expect.
                        <a href="{{ route('docs.contacts-module.lifecycle') }}">Read the Lifecycle Status guide &rarr;</a>
                    </p>
                </div>
            </div>

            <!-- Cron -->

            <div class="card mb-4" id="cron">

                <div class="card-header">
                    <h5 class="mb-0">Cron Jobs</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr><th>Command</th><th>Schedule</th><th>Purpose</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>sync:allcontact-to-lead</code></td>
                                    <td>Daily</td>
                                    <td>Cross-references contacts and leads by phone number. See <a href="{{ route('docs.contacts-module.lead-sync') }}">Lead Sync</a>.</td>
                                </tr>
                                <tr>
                                    <td><code>auto:bulkallcontactsend</code></td>
                                    <td>Every minute</td>
                                    <td>Polls <code>allcontactsendwhatsapps</code> for scheduled campaigns (<code>campaign_type = 2</code>) due in the current minute, and dispatches the actual send job (normal WhatsApp API or Meta WhatsApp, depending on configuration).</td>
                                </tr>
                                <tr>
                                    <td><code>queue:work --queue=orderSend,orderCancel,default</code></td>
                                    <td>Every minute</td>
                                    <td>Generic queue worker that also processes this module's dispatched jobs (<code>SendAllcontactautomessage</code>, <code>ProcessCsvImport</code>, <code>ExportAllContactHistoryJob</code>, <code>ExportAllContactToEmailPortalJob</code>, and the bulk WhatsApp send jobs) unless they're pinned to another queue.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p class="mt-3 mb-0 text-muted">
                        Google Contacts sync and both export flows (CSV, email portal) are on-demand only &mdash;
                        triggered by an admin click, not scheduled.
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
