@extends('layout.docs.docs_layout')

@section('title', 'Contacts Module - Lead Sync')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.contacts-module') }}">Contacts Module</a>
            </li>
            <li class="breadcrumb-item active">
                Lead Sync
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Lead Sync
    </h2>

    <p class="text-muted mb-4">
        The <code>sync:allcontact-to-lead</code> cron job that cross-references Contacts against Leads by phone
        number.
    </p>

    <div class="alert alert-warning">
        <strong>This is a read-only cross-reference report, not a data-promotion pipeline.</strong> It does
        <em>not</em> copy contacts into the <code>leads</code> table, and nothing in the codebase uses its output to
        automatically create or update actual lead records. Unlike Candidates (which have <a href="{{ route('docs.candidate-module.leads-deals') }}">no link at all</a> to Leads), Contacts and Leads
        <strong>do</strong> have a real, coded connection &mdash; but it stops at "here's which of your contacts are
        also leads," it doesn't merge or sync data between them.
    </div>

    <!-- Mechanics -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">How It Works</h5>
        </div>

        <div class="card-body">

            <p>
                Command: <code>app/Console/Commands/SyncAllContactsToLead.php</code>, signature
                <code>sync:allcontact-to-lead</code>, scheduled <strong>daily</strong>.
            </p>

            <p>Each run:</p>

            <ol>
                <li>
                    Finds a high-water mark: <code>$lastContactId = MAX(allcontact_id)</code> already recorded in
                    <code>sync_allcontact_to_lead</code>, then processes the next batch of up to
                    <strong>1,000</strong> contact ids above that mark.
                </li>

                <li class="mt-3">
                    Matches each batch contact's <code>primary_no_wsp</code> against
                    <strong>either</strong> <code>leads.whatsapp_no</code> <strong>or</strong>
                    <code>leads.mob_no</code>, by exact string equality &mdash; no normalization of dial codes or
                    formatting, so mismatched formats between the two tables silently fail to match.
                </li>

                <li class="mt-3">
                    If a contact's number matches more than one lead, only the <strong>lowest <code>lead_id</code></strong>
                    (<code>MIN</code>) is kept &mdash; one row per contact.
                </li>

                <li class="mt-3">
                    Inserts the result into <code>sync_allcontact_to_lead</code> (<code>allcontact_id</code>,
                    <code>lead_id</code>, <code>primary_no_wsp</code>).
                </li>
            </ol>

            <div class="alert alert-primary mb-0">
                <strong>Idempotent per contact, not re-checked.</strong> Once a contact id has a row in
                <code>sync_allcontact_to_lead</code>, it is never reprocessed by later runs &mdash; even if the
                lead-side data changes afterward (e.g. a new matching lead is created later), the mapping stays as
                it was first computed.
            </div>

        </div>

    </div>

    <!-- Reporting -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Where the Result Is Shown</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>GET admin/sync-contact-to-lead</code> (<code>BackEndController::sync_contact_to_lead</code>)
                renders a simple DataTables list reading straight from <code>sync_allcontact_to_lead</code>
                &mdash; contact id, lead id, and the matching phone number. That's the entire output of this
                feature: a report, not an automation.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.contacts-module.groups') }}" class="btn btn-outline-secondary btn-sm">&larr; Groups</a>
        <a href="{{ route('docs.contacts-module.export') }}" class="btn btn-outline-primary btn-sm">Export &amp; History &rarr;</a>
    </div>

</div>

@endsection
