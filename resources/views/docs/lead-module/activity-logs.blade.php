@extends('layout.docs.docs_layout')

@section('title', 'Lead Module - Activity Logs')

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
                Activity Logs
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Activity Logs
    </h2>

    <p class="text-muted mb-4">
        The audit trail behind every lead &mdash; who changed what, and when.
    </p>

    <div class="card mb-4">

        <div class="card-body">

            <p class="mb-0">
                Every meaningful change to a lead is written to the <code>lead_activity_logs</code> table
                (model: <code>LeadActivityLog</code>) through one shared helper,
                <code>Helper::leadActivityLog($leadId, $activity, $adminId, $module)</code>. Each row stores
                <code>lead_id</code>, <code>admin_id</code> (who did it), a <code>module</code> tag (what kind of
                action), and an <code>activity</code> JSON payload with the details.
            </p>

        </div>

    </div>

    <!-- Table -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">What Gets Logged Automatically</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th><code>module</code></th>
                            <th>When it's written</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><code>lead_assign_action</code></td><td>Automatically, any time <code>leadassign_id</code> changes &mdash; on first assignment, cron reassignment, or manual reassignment. Fires from a model event on <code>Lead</code>, so it can never be skipped. See <a href="{{ route('docs.lead-assignment') }}">Lead Assignment</a>.</td></tr>
                        <tr><td><code>qualified_status</code></td><td>Whenever the qualification status (<code>is_qualified</code>) changes via <code>LeadController::qualified()</code>, recording the old and new status labels. See <a href="{{ route('docs.lead-module.qualification') }}">Qualification</a>.</td></tr>
                        <tr><td><code>meta_capi</code></td><td>After every Meta Conversions API send attempt, success or failure, including the response. See <a href="{{ route('docs.meta-api') }}">Meta Conversion API</a>.</td></tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Flow -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Activity Log Flow</h5>
        </div>

        <div class="card-body">

            <ol class="mb-0">
                <li>
                    <strong>Lead Action Performed</strong><br>
                    A user or the system performs an action on a lead &mdash; assignment, qualification, or a Meta API call.
                </li>

                <li class="mt-3">
                    <strong>Capture Activity Details</strong><br>
                    The relevant controller/model code calls <code>Helper::leadActivityLog()</code> with the module tag and a structured payload (e.g. old owner / new owner, or old status / new status).
                </li>

                <li class="mt-3">
                    <strong>Store Activity Log</strong><br>
                    A new <code>LeadActivityLog</code> row is created, linked to the lead and the acting admin.
                </li>

                <li class="mt-3">
                    <strong>Display Activity Timeline</strong><br>
                    <code>LeadController::leadActivity()</code> loads all entries for a lead (with the admin relation eager-loaded) and displays them in chronological order on the lead's Activity tab.
                </li>

                <li class="mt-3">
                    <strong>Support Auditing &amp; Reporting</strong><br>
                    Because assignment and qualification changes are logged unconditionally at the model/controller level, the activity log gives a reliable audit trail even for changes made through cron jobs, not just manual UI actions.
                </li>
            </ol>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.lead-module.qualification') }}" class="btn btn-outline-secondary btn-sm">&larr; Qualification</a>
        <a href="{{ route('docs.meta-api') }}" class="btn btn-outline-primary btn-sm">Meta Conversion API &rarr;</a>
    </div>

</div>

@endsection
