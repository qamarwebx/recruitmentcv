@extends('layout.docs.docs_layout')

@section('title', 'Candidate Module - Relationship to Leads & Deals')

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
                Relationship to Leads &amp; Deals
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Relationship to Leads &amp; Deals
    </h2>

    <div class="alert alert-warning">
        <strong>There is no database link between Leads/Deals and Candidates.</strong> This surprises people coming
        from the Lead module, so it's worth stating plainly.
    </div>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">What Was Checked</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li>The <code>leads</code> table has no <code>candidate_id</code> column. Its only candidate-adjacent field is <code>leads.candidate_updated_at</code>, which is just a plain timestamp (alongside <code>staff_updated_at</code>) &mdash; bookkeeping, not a relationship.</li>
                <li>The <code>deal_pipeline</code> table has a <code>candidate</code> column, but it's a plain <code>varchar(255)</code> free-text field (plus a free-text <code>passport_no</code>) &mdash; not an integer foreign key to <code>candidates.id</code>.</li>
                <li>The <code>candidates</code> table has no <code>lead_id</code> or <code>deal_id</code> column at all.</li>
                <li>Searching every Lead/Deal controller (<code>LeadController</code>, <code>DealPipelineController</code>, <code>DealStagecontroller</code>, <code>DealNoteController</code>, <code>DealFileController</code>) for <code>new Candidate(</code> or <code>Candidate::create(</code> turns up nothing.</li>
            </ul>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">What This Means in Practice</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Turning a promising Lead or a won Deal into a Candidate is a <strong>manual process</strong>: a
                recruiter reads the Lead/Deal, then separately opens <a href="{{ route('docs.candidate-module.creation') }}">Candidate Creation</a>
                and fills in a fresh candidate profile by hand, typically after the deal has progressed far enough
                (e.g. qualified/closing) to justify starting the recruitment-to-deployment pipeline. Nothing in the
                system automatically carries data across, and there is no way to click from a Lead or Deal record
                straight to "its" Candidate record, because that link doesn't exist in the schema.
            </p>

        </div>

    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">See Also</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li><a href="{{ route('docs.lead-module') }}">Lead Module</a> &mdash; where a candidate's journey typically starts.</li>
                <li><a href="{{ route('docs.lead-module.qualification') }}">Lead Qualification</a> &mdash; qualifying a lead can auto-create a Deal Pipeline record, but that stops short of creating a Candidate.</li>
            </ul>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.candidate-module.activity') }}" class="btn btn-outline-secondary btn-sm">&larr; Activity Log</a>
        <a href="{{ route('docs.candidate-module') }}" class="btn btn-outline-primary btn-sm">Back to Overview &rarr;</a>
    </div>

</div>

@endsection
