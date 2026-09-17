@extends('layout.docs.docs_layout')

@section('title', 'Contacts Module - Export & History')

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
                Export &amp; History
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Export &amp; History
    </h2>

    <p class="text-muted mb-4">
        Two independent export subsystems, each with its own history table and background job. Easy to confuse
        since both are triggered from the Contacts list screen.
    </p>

    <!-- CSV export -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">1. CSV Export &mdash; a Download Audit Trail</h5>
        </div>

        <div class="card-body">

            <p>
                <code>POST admin/allcontact/bulkexport</code> validates the requested columns against
                <code>ContactExportService::exportColumnMap()</code>, creates an <code>ExportAllContactHistory</code>
                row (table <code>export_all_contact_histories</code>: <code>admin_id</code>, <code>columns</code>
                (json), <code>contact_ids</code> (csv string), <code>is_all_export</code>, <code>status</code>,
                <code>total_records</code>, <code>exported_records</code>, <code>error_message</code>,
                <code>started_at</code>/<code>completed_at</code>), then dispatches
                <code>ExportAllContactHistoryJob</code>.
            </p>

            <p class="mb-0">
                The job (timeout 7200s) builds a query either restricted to the given <code>contact_ids</code>, or
                &mdash; if <code>is_all_export</code> &mdash; re-applies the admin's saved filter (see
                <a href="{{ route('docs.contacts-module.filters') }}">Filters &amp; Saved Views</a>) via the same
                scopes used by the list page. It streams results in chunks of 1,000 to a CSV file with a UTF-8 BOM
                header, updating progress (<code>pending</code> &rarr; <code>processing</code> &rarr;
                <code>completed</code>/<code>failed</code>) as it goes. History UI:
                <code>admin.contact_export_history</code>, scoped to the current admin unless they're a
                super-admin.
            </p>

        </div>

    </div>

    <!-- Email portal export -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">2. Email Qamr Portal Export &mdash; an External Subscriber Sync</h5>
        </div>

        <div class="card-body">

            <p>
                Handled by a <strong>separate controller</strong>, <code>AllContactEmailPortalController</code>.
                This exports contacts as email-marketing subscribers into an external mailing-list portal (service
                class <code>EmailQamrPortalService</code>, using an <code>api_token</code> + <code>list_uid</code>),
                keyed by the first non-empty email in priority order (<code>email</code> &rarr; <code>email0</code>
                &rarr; <code>email1</code> &rarr; <code>email2</code>).
            </p>

            <p>
                History table <code>export_all_contact_email_portal_histories</code> tracks
                <code>api_token</code> (encrypted and hidden), <code>list_uid</code>, <code>list_name</code>,
                <code>business_type</code>, <code>industry_ids</code>/<code>industry_names</code>,
                <code>filters</code> (json), <code>status</code>, and per-row
                <code>pending_count</code>/<code>success_count</code>/<code>failed_count</code>.
            </p>

            <p class="mb-0">
                The job (<code>ExportAllContactToEmailPortalJob</code>, timeout 7200s) reuses the same filter-scope
                map as the CSV export and the list JSON endpoint, chunks 200 contacts at a time, calls
                <code>EmailQamrPortalService::createSubscriber()</code> per contact with a 100ms throttle between
                calls, and samples up to 20 error messages for the history record.
            </p>

        </div>

    </div>

    <!-- Comparison -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Quick Comparison</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr><th></th><th>CSV Export</th><th>Email Qamr Portal Export</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Controller</td><td><code>AllContactController</code></td><td><code>AllContactEmailPortalController</code></td></tr>
                        <tr><td>Destination</td><td>A downloadable CSV file</td><td>An external email-marketing list</td></tr>
                        <tr><td>History table</td><td><code>export_all_contact_histories</code></td><td><code>export_all_contact_email_portal_histories</code></td></tr>
                        <tr><td>Job</td><td><code>ExportAllContactHistoryJob</code></td><td><code>ExportAllContactToEmailPortalJob</code></td></tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.contacts-module.lead-sync') }}" class="btn btn-outline-secondary btn-sm">&larr; Lead Sync</a>
        <a href="{{ route('docs.contacts-module.filters') }}" class="btn btn-outline-primary btn-sm">Filters &amp; Saved Views &rarr;</a>
    </div>

</div>

@endsection
