@extends('layout.docs.docs_layout')

@section('title', 'Candidate Module - Candidate Creation')

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
                Candidate Creation
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Candidate Creation
    </h2>

    <p class="text-muted mb-4">
        How a candidate profile is created, and what happens automatically the moment it's saved.
    </p>

    <div class="alert alert-warning">
        <strong>Not a Lead/Deal conversion.</strong> Candidates are created directly by staff filling in a form
        &mdash; there is no coded "convert this Lead/Deal into a Candidate" action. See
        <a href="{{ route('docs.candidate-module.leads-deals') }}">Relationship to Leads &amp; Deals</a>.
    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">What Happens on <code>store()</code></h5>
        </div>

        <div class="card-body">

            <p>
                A staff member fills in the candidate form and submits to <code>POST candidate/store</code>, handled
                by <code>CandidateController::store()</code>. This single request does all of the following:
            </p>

            <ol class="mb-0">
                <li>
                    <strong>Upload initial files</strong><br>
                    <code>pass_file</code>, <code>lic_file</code>, <code>cv_file</code>, and <code>photo_file</code>
                    (if provided) are uploaded to <code>public/admin/assets/images/candidate</code> (or
                    <code>public_html/...</code>, depending on the <code>Basepathstatus</code> setting).
                </li>

                <li class="mt-3">
                    <strong>Generate a reference number</strong><br>
                    <code>reference_no</code> is generated sequentially (e.g. <code>RF124</code>) by parsing the
                    highest existing reference number, or reusing one from the
                    <code>candidatebackupreferences</code> recycle pool if available &mdash; that pool row is then
                    deleted.
                </li>

                <li class="mt-3">
                    <strong>Set initial status fields</strong><br>
                    <code>cand_status = '1'</code>, <code>candidate_current_status = "New Candidate"</code>,
                    <code>slug_text</code> is set to a random string.
                </li>

                <li class="mt-3">
                    <strong>Start the publish wizard</strong><br>
                    A <code>candpubsts</code> row is created with <code>stage_name = 'Passport Stage'</code> &mdash;
                    the candidate immediately enters stage 1 of 4. See
                    <a href="{{ route('docs.candidate-module.publish') }}">Publish Wizard</a>.
                </li>

                <li class="mt-3">
                    <strong>Start the deployment pipeline tracker</strong><br>
                    A <code>cand_statuses</code> row is created with <code>new_candidate = true</code>. See
                    <a href="{{ route('docs.candidate-module.status') }}">Status &amp; Deployment Pipeline</a>.
                </li>

                <li class="mt-3">
                    <strong>Log the activity</strong><br>
                    An <code>activities</code> row is written: "New candidate created". See
                    <a href="{{ route('docs.candidate-module.activity') }}">Activity Log</a>.
                </li>
            </ol>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.candidate-module.database') }}" class="btn btn-outline-secondary btn-sm">&larr; Database Structure</a>
        <a href="{{ route('docs.candidate-module.publish') }}" class="btn btn-outline-primary btn-sm">Publish Wizard &rarr;</a>
    </div>

</div>

@endsection
