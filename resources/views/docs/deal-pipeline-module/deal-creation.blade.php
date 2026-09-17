@extends('layout.docs.docs_layout')

@section('title', 'Deal Pipeline Module - Deal Creation')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.deal-pipeline-module') }}">Deal Pipeline Module</a>
            </li>
            <li class="breadcrumb-item active">
                Deal Creation
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Deal Creation
    </h2>

    <p class="text-muted mb-4">
        Deals can be created two ways: manually through <code>store()</code>, or automatically when a lead is
        qualified. Both always start in the <strong>Prospecting</strong> stage.
    </p>

    <!-- Manual -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Manual Creation &mdash; <code>store()</code></h5>
        </div>

        <div class="card-body">

            <p>
                <code>POST dealPipeline/store</code> always forces <code>deal_stage_id</code> to the "Prospecting"
                stage (looked up by name; the request fails with a 500 error if that stage doesn't exist in the
                database). It then branches based on the selected <strong>Business Type</strong>
                (<code>Business::find($request-&gt;add_business_id)-&gt;name</code>):
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr><th>If business is "Job seeker"</th><th>Otherwise ("common" deal)</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                Required: associate, care-of staff.<br>
                                Optional: job title, candidate name, passport no., country, city, amount, mobile,
                                WhatsApp, email, source, notes.
                            </td>
                            <td>
                                Required: care-of staff.<br>
                                Optional: name, job title (as free text, saved to <code>job_title_other</code>),
                                company name, country, city, mobile, WhatsApp, email, source, notes.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 mb-0">
                <code>created_by</code> is set to the logged-in admin. <code>update()</code> mirrors this exact
                dual-branch structure, additionally allowing <code>close_date</code> for Job seeker deals, and
                setting <code>modified_by</code>.
            </p>

        </div>

    </div>

    <!-- Auto from lead -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Automatic Creation &mdash; from a Qualified Lead</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                When a lead's qualification status is set to <strong>Lead Qualified</strong> (and "add to
                pipeline" is selected), <code>LeadController::transferLeadToPipeline()</code> creates a Job seeker
                deal directly, copying lead fields across. Full detail on this trigger lives on the Lead module's
                <a href="{{ route('docs.lead-module.qualification') }}">Qualification</a> page; the field-by-field
                copy mapping and its important caveats live on
                <a href="{{ route('docs.deal-pipeline-module.relationships') }}">Relationship to Leads &amp; Candidates</a>.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.deal-pipeline-module.stages') }}" class="btn btn-outline-secondary btn-sm">&larr; Pipeline Stages &amp; Kanban Board</a>
        <a href="{{ route('docs.deal-pipeline-module.settings') }}" class="btn btn-outline-primary btn-sm">Settings &amp; Lookups &rarr;</a>
    </div>

</div>

@endsection
