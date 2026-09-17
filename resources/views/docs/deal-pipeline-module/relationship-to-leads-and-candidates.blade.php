@extends('layout.docs.docs_layout')

@section('title', 'Deal Pipeline Module - Relationship to Leads & Candidates')

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
                Relationship to Leads &amp; Candidates
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Relationship to Leads &amp; Candidates
    </h2>

    <p class="text-muted mb-4">
        Deal Pipeline sits at the intersection of Leads and Candidates, but the real database links are much
        thinner than the placement-pipeline concept suggests.
    </p>

    <!-- Leads -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Leads: a One-Time, One-Directional Copy</h5>
        </div>

        <div class="card-body">

            <p>
                Marking a lead <strong>Lead Qualified</strong> (with "add to pipeline" selected) calls
                <code>LeadController::transferLeadToPipeline()</code>, which creates a new deal:
                <code>business_id</code> = "Job seeker", <code>deal_stage_id</code> = "Prospecting",
                <code>associate_id</code> hardcoded to a fixed associate (id 73), and copies:
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Deal column</th><th>Copied from lead</th></tr></thead>
                    <tbody>
                        <tr><td><code>job_title_other</code></td><td><code>other_job_title</code></td></tr>
                        <tr><td><code>candidate</code></td><td><code>cand_name</code></td></tr>
                        <tr><td><code>company</code></td><td><code>company_name</code></td></tr>
                        <tr><td><code>contact</code></td><td><code>mob_no</code></td></tr>
                        <tr><td><code>contact_whatsapp</code></td><td><code>whatsapp_no</code></td></tr>
                        <tr><td><code>email</code></td><td><code>email</code></td></tr>
                        <tr><td><code>country</code></td><td><code>country</code></td></tr>
                        <tr><td><code>notes</code></td><td><code>qualified_reason</code></td></tr>
                        <tr><td><code>source</code></td><td><code>lead_source</code></td></tr>
                        <tr><td><code>care_of</code></td><td><code>leadassign_id</code></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-danger mb-0">
                <strong>There is no reverse pointer.</strong> <code>deal_pipeline</code> has no <code>lead_id</code>
                column at all. Once the copy happens, the link is untraceable except by matching free-text fields
                (name, mobile number, etc.) — there's no way to click from a deal back to "its" originating lead, or
                vice versa, and no ongoing sync if the lead is edited afterward.
            </div>

        </div>

    </div>

    <!-- Candidates -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Candidates: Coincidental Free-Text Overlap Only</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>deal_pipeline.candidate</code> and <code>deal_pipeline.passport_no</code> are plain varchar
                columns with <strong>zero database-level linkage</strong> to the <code>candidates</code> table
                (confirmed on both sides — see the Candidate module's own
                <a href="{{ route('docs.candidate-module.leads-deals') }}">Relationship to Leads &amp; Deals</a> page).
                The only place <code>passport_no</code> is actually used for matching is
                <code>checkPassport()</code>, which checks for duplicates <em>within <code>deal_pipeline</code>
                itself</em> — never against <code>candidates</code>. Any resemblance between a deal's
                <code>candidate</code>/<code>passport_no</code> and an actual candidate record is coincidental
                free-text overlap, not a real relationship.
            </p>

        </div>

    </div>

    <!-- Employer -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">No Link to Employer, Either</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Despite looking like a recruitment/placement pipeline, Deal Pipeline has <strong>no structural tie
                to the Employer module</strong> — searching the model, controller, and views for "employer" turns
                up nothing. The <code>company</code> field is plain free text, not linked to the separate
                <code>employers</code> table. In short: Deal Pipeline is connected to the rest of the CRM only by
                free-text overlap and the one-time Lead-qualification copy above — it is otherwise a siloed table.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.deal-pipeline-module.reminders') }}" class="btn btn-outline-secondary btn-sm">&larr; Reminders &amp; Cron Jobs</a>
        <a href="{{ route('docs.deal-pipeline-module.filters') }}" class="btn btn-outline-primary btn-sm">Filters &amp; Saved Views &rarr;</a>
    </div>

</div>

@endsection
