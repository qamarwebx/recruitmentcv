@extends('layout.docs.docs_layout')

@section('title', 'Lead Module - Lead Creation')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.lead-module') }}">Lead Module</a>
            </li>
            <li class="breadcrumb-item active">
                Lead Creation
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Lead Creation
    </h2>

    <p class="text-muted mb-4">
        How a form submission on the public website turns into a row in the <code>leads</code> table.
    </p>

    <div class="card mb-4">

        <div class="card-body">

            <p class="mb-0">
                Leads are created from the public website form, which posts to <code>POST /api/leads/store</code>
                (handled by <code>LeadController::lead_store_new()</code>). The process always <strong>creates a
                new row</strong> &mdash; it never silently overwrites an older lead &mdash; but it does detect and
                flag repeats so staff and reporting can tell them apart. See
                <a href="{{ route('docs.lead-module.duplicate') }}">Duplicate Check</a> for the full detail on that.
            </p>

        </div>

    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Lead Creation Flow</h5>
        </div>

        <div class="card-body">

            <ol class="mb-0">
                <li>
                    <strong>Lead Submitted</strong><br>
                    The candidate submits the form on <code>www.qamrjob.com</code> with name, mobile number, and the service they're looking for.
                </li>

                <li class="mt-3">
                    <strong>Optional Pre-check</strong><br>
                    Before submitting, the frontend can call <code>POST /api/leads/checkexists</code> to look up an existing lead by mobile number, so the form can switch into "update" mode instead of always creating fresh.
                </li>

                <li class="mt-3">
                    <strong>Capture IP &amp; Location</strong><br>
                    The submitter's IP is resolved through a GeoIP lookup and stored as JSON in the <code>submit_lead_from</code> column: <code>{from, ip, country, region, city}</code>. The full raw request is also saved in <code>lead_request_data</code> for audit purposes.
                </li>

                <li class="mt-3">
                    <strong>Duplicate / Repeat Check</strong><br>
                    The system checks whether a lead with the same <code>mob_no</code> (or, on an update-mode submit, the same <code>whatsapp_no</code>) already exists and flags the new row accordingly. Full detail: <a href="{{ route('docs.lead-module.duplicate') }}">Duplicate Check</a>.
                </li>

                <li class="mt-3">
                    <strong>Save Lead &amp; Set Defaults</strong><br>
                    The lead is saved with <code>lead_status_text = "New"</code>, <code>lead_date = now()</code>, and <code>lead_source</code> defaulting to <code>www.qamrjob.com</code>. Qualification (<code>is_qualified</code>) is left empty, i.e. "Not Yet".
                </li>

                <li class="mt-3">
                    <strong>Automatic Lead Assignment</strong><br>
                    <strong>Only for a genuinely new (non-repeat) lead</strong>, and only if the global auto-assign switch is on: the system picks the eligible staff member with the fewest total leads and sets <code>leadassign_id</code> immediately. See <a href="{{ route('docs.lead-assignment') }}">Lead Assignment</a> for the exact rules &mdash; repeat leads are <em>not</em> assigned at this step; they are picked up later by the assignment cron jobs.
                </li>

                <li class="mt-3">
                    <strong>Create Activity Log</strong><br>
                    Whenever <code>leadassign_id</code> changes (including this first assignment), a <code>lead_assign_action</code> entry is written to <code>lead_activity_logs</code> automatically. See <a href="{{ route('docs.lead-module.activity') }}">Activity Logs</a>.
                </li>

                <li class="mt-3">
                    <strong>Trigger Meta Integration (conditional)</strong><br>
                    For leads matching the KSA driving-license criteria, a Meta Conversions API "Lead" event is sent to Facebook for ad optimization, guarded by the <code>meta_lead_sent</code> flag so it only fires once. See <a href="{{ route('docs.meta-api') }}">Meta Conversion API</a>.
                </li>

                <li class="mt-3">
                    <strong>Lead Available for Follow-up</strong><br>
                    The lead is now visible in the CRM and ready for follow-up notes, qualification, and further processing. See <a href="{{ route('docs.lead-module.followup') }}">Follow-up</a>.
                </li>
            </ol>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.lead-module.database') }}" class="btn btn-outline-secondary btn-sm">&larr; Database Structure</a>
        <a href="{{ route('docs.lead-module.duplicate') }}" class="btn btn-outline-primary btn-sm">Duplicate Check &rarr;</a>
    </div>

</div>

@endsection
