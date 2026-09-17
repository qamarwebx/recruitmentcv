@extends('layout.docs.docs_layout')

@section('title', 'Candidate Module - Status & Deployment Pipeline')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.candidate-module') }}">Candidate Module</a>
            </li>
            <li class="breadcrumb-item active">
                Status &amp; Deployment Pipeline
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Status &amp; Deployment Pipeline
    </h2>

    <p class="text-muted mb-4">
        A candidate has <strong>three parallel status concepts</strong>. They're easy to conflate, so each is
        documented separately below.
    </p>

    <div class="table-responsive mb-4">
        <table class="table table-bordered table-sm">
            <thead>
                <tr><th>Concept</th><th>Storage</th><th>Actively used?</th></tr>
            </thead>
            <tbody>
                <tr><td><strong>Deployment pipeline</strong></td><td><code>candidates.candidate_current_status</code> + boolean flags on <code>cand_statuses</code></td><td>Yes &mdash; this is the real, day-to-day pipeline.</td></tr>
                <tr><td>Legacy status lookup</td><td><code>candidates.cand_status</code> &rarr; <code>candidate_statuses</code></td><td>Largely superseded; mostly legacy routes.</td></tr>
                <tr><td>Publish Wizard</td><td><code>candpubsts</code></td><td>Yes, but orthogonal &mdash; see <a href="{{ route('docs.candidate-module.publish') }}">Publish Wizard</a>.</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Pipeline -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The Deployment Pipeline</h5>
        </div>

        <div class="card-body">

            <p>
                This is the real recruitment-to-deployment journey, tracked by
                <code>candidates.candidate_current_status</code> (a human-readable label) mirrored into boolean
                flags on the <strong><code>cand_statuses</code></strong> table (one row per candidate). In order:
            </p>

            <ol class="mb-0">
                <li>New Candidate</li>
                <li>Candidate Are Ready</li>
                <li>Published For Selection</li>
                <li>Selected</li>
                <li>Visa Received</li>
                <li>Passport In Embassy</li>
                <li>Visa Stamped</li>
                <li>Applied For Emigration</li>
                <li>Emigration Approved</li>
                <li>Waiting For Flight Ticket</li>
                <li>Ticket Confirmed</li>
                <li>Deployed &mdash; terminal, successful placement</li>
            </ol>

            <p class="mt-3 mb-0">
                Side/terminal statuses outside the main sequence: <strong>Cancelled</strong>, <strong>Hold</strong>,
                <strong>Visa Cancelled</strong>.
            </p>

        </div>

    </div>

    <!-- Endpoints -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">How a Status Change Happens</h5>
        </div>

        <div class="card-body">

            <p>
                Each pipeline step has its own dedicated POST endpoint (e.g.
                <code>candidate/status/newcandidateupdate</code>, <code>.../selected</code>,
                <code>.../visareceived</code>, <code>.../deployed</code>, <code>.../cancelled</code>,
                <code>.../hold</code>). Each one:
            </p>

            <ol class="mb-0">
                <li>Flips the matching boolean on the candidate's <code>cand_statuses</code> row.</li>
                <li>Updates <code>candidates.candidate_current_status</code> to the matching label.</li>
                <li>Writes an <code>activities</code> timeline row. See <a href="{{ route('docs.candidate-module.activity') }}">Activity Log</a>.</li>
            </ol>

            <p class="mt-3 mb-0">
                <strong>Going back a step:</strong> <code>candprevstatusGet()</code> implements "move to the previous
                status" via a switch on the current <code>candidate_current_status</code>.<br>
                <strong>Full reset:</strong> <code>candidate.resetstatus</code> (gated by the
                <code>candidate_reset_status</code> permission) forces <code>candidate_current_status</code> back to
                <code>"New Candidate"</code> and zeroes every flag on <code>cand_statuses</code>.
            </p>

        </div>

    </div>

    <!-- Legacy -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Legacy Status Lookup &mdash; <code>cand_status</code></h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>candidates.cand_status</code> is a real foreign key (<code>ON DELETE CASCADE</code>) to the
                <code>candidate_statuses</code> lookup table (<code>id, status, priority</code>), managed via
                <code>CandidateStatusController</code>. Most of its CRUD routes are commented out in
                <code>routes/web.php</code> in favor of the newer <code>candidate_current_status</code> system above
                &mdash; treat this as a largely superseded, parallel taxonomy rather than the source of truth.
            </p>

        </div>

    </div>

    <!-- Qualification equivalent -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">What's the Equivalent of a Lead's "Qualified" Flag?</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                There's no single boolean. The closest equivalents are <code>candidates.publish</code> (publicly
                listed/selectable), <code>candidates.verified</code>, <code>candidates.cv_execute</code> (an
                auto-computed profile-completeness flag gating CV PDF generation &mdash; see
                <a href="{{ route('docs.candidate-module.documents') }}">Documents</a>), and reaching
                <strong>"Deployed"</strong> in the pipeline above as the terminal "successfully placed" state.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.candidate-module.documents') }}" class="btn btn-outline-secondary btn-sm">&larr; Documents</a>
        <a href="{{ route('docs.candidate-module.transactions') }}" class="btn btn-outline-primary btn-sm">Transactions &amp; Finance &rarr;</a>
    </div>

</div>

@endsection
