@extends('layout.docs.docs_layout')

@section('title', 'Lead Module - Qualification')

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
                Qualification
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Qualification
    </h2>

    <p class="text-muted mb-4">
        How a recruiter records the outcome of a lead, and what happens automatically once it's marked qualified.
    </p>

    <div class="card mb-4">

        <div class="card-body">

            <p class="mb-0">
                Qualification is stored as a single numeric code in <code>leads.is_qualified</code> (1&ndash;5). It
                is updated through <code>LeadController::qualified()</code>, which validates the value, saves an
                optional note, and logs the change to the Activity Log.
            </p>

        </div>

    </div>

    <!-- Statuses -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Qualification Statuses</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th><code>is_qualified</code></th>
                            <th>Label</th>
                            <th>Meaning</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><code>NULL</code> / <code>0</code></td><td>Not Yet</td><td>Default state for every new lead &mdash; no decision made yet.</td></tr>
                        <tr><td><code>1</code></td><td>Followed Up</td><td>Contact has been made; still under evaluation.</td></tr>
                        <tr><td><code>2</code></td><td>Call Not Connected</td><td>Attempted to reach the candidate but couldn't; <code>call_not_connected_type</code> records why.</td></tr>
                        <tr><td><code>3</code></td><td>Lead Qualified</td><td>Candidate meets the requirements and is suitable to proceed.</td></tr>
                        <tr><td><code>4</code></td><td>Lead Not Qualified</td><td>Candidate does not meet the requirements.</td></tr>
                        <tr><td><code>5</code></td><td>Lead Not Relevant</td><td>Lead is out of scope entirely (wrong service, spam, duplicate intent, etc.).</td></tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Flow -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Lead Qualification Flow</h5>
        </div>

        <div class="card-body">

            <ol class="mb-0">
                <li>
                    <strong>Open Assigned Lead</strong><br>
                    The recruiter opens the assigned lead to begin the qualification process.
                </li>

                <li class="mt-3">
                    <strong>Review Candidate Information</strong><br>
                    Verify contact information, preferred service, experience, and licenses (<code>saudi_license</code>, <code>india_license</code>) already captured on the lead.
                </li>

                <li class="mt-3">
                    <strong>Contact the Candidate</strong><br>
                    Speak with the candidate to confirm interest, availability, and eligibility.
                </li>

                <li class="mt-3">
                    <strong>Set the Qualification Status</strong><br>
                    Choose one of the five statuses above. If choosing "Call Not Connected", also select the reason (<code>call_not_connected_type</code>). A free-text <code>qualified_reason</code> can be added for any status.
                </li>

                <li class="mt-3">
                    <strong>Save</strong><br>
                    The system updates <code>is_qualified</code>, <code>qualified_reason</code>, <code>call_not_connected_type</code>, and <code>staff_updated_at</code>; a note can optionally be logged at the same time (see <a href="{{ route('docs.lead-module.notes') }}">Lead Notes</a>). A <code>qualified_status</code> Activity Log entry is created recording the old and new status.
                </li>

                <li class="mt-3">
                    <strong>Automatic Deal Creation</strong><br>
                    If the status is set to <strong>Lead Qualified (3)</strong> and "add to pipeline" is selected, the system automatically creates a <strong>Deal Pipeline</strong> record (business: "Job seeker", stage: "Prospecting", owner: the lead's assigned staff) pre-filled with the candidate's details, so the recruiter doesn't have to re-enter them.
                </li>

                <li class="mt-3">
                    <strong>Meta Reporting</strong><br>
                    Each qualification status also maps to a Meta Conversions API event (e.g. status 3 → <code>QualifiedLead</code>), which helps the marketing team see which ad campaigns actually produce qualified candidates. See <a href="{{ route('docs.meta-api') }}">Meta Conversion API</a>.
                </li>
            </ol>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.lead-module.notes') }}" class="btn btn-outline-secondary btn-sm">&larr; Lead Notes</a>
        <a href="{{ route('docs.lead-module.activity') }}" class="btn btn-outline-primary btn-sm">Activity Logs &rarr;</a>
    </div>

</div>

@endsection
