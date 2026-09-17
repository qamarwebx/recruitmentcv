@extends('layout.docs.docs_layout')

@section('title', 'Candidate Module - Activity Log')

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
                Activity Log
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Activity Log
    </h2>

    <p class="text-muted mb-4">
        The candidate's equivalent of the Lead module's Activity Log &mdash; a single flat timeline of everything
        that happens to a candidate profile.
    </p>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The <code>activities</code> Table</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr><th>Column</th><th>Purpose</th></tr>
                    </thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>cand_id</code></td><td>Which candidate this entry belongs to. No DB foreign key constraint, and the <code>Activity</code> model declares no <code>candidate()</code> relationship &mdash; it's queried by hand: <code>Activity::where('cand_id', $id)</code>.</td></tr>
                        <tr><td><code>headline</code></td><td>Short title, e.g. "New candidate created".</td></tr>
                        <tr><td><code>bodyMessage</code></td><td>Longer narrative string, built inline in the controller at each call site.</td></tr>
                        <tr><td><code>icons</code></td><td>A Tabler icon class (<code>ti ti-*</code>) shown next to the entry.</td></tr>
                        <tr><td><code>admin_id</code>, <code>user_id</code>, <code>partner_id</code></td><td>Who performed the action &mdash; whichever actor type applies.</td></tr>
                        <tr><td><code>activity_type</code></td><td>Category tag for the entry.</td></tr>
                        <tr><td><code>created_at</code>, <code>updated_at</code></td><td>Standard timestamps &mdash; <code>created_at</code> drives the chronological timeline.</td></tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 mb-0">
                Model: <code>App\Models\Activity</code> (<code>protected $guarded = [];</code>).
            </p>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Unlike Leads: No Separate Notes Table</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Leads split follow-up interactions (<code>leadnotes</code>) from system-generated audit events
                (<code>lead_activity_logs</code>). Candidates don't make that split &mdash; every significant action
                (creation, document upload, status change, publish-stage transition, payment) writes directly into
                the same single <code>activities</code> table. There's no separate "candidate notes" model.
            </p>

        </div>

    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">How Entries Get Created</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                There's no model event or observer doing this automatically (unlike the Lead model's
                <code>booted()</code> hook). Every call site in <code>CandidateController.php</code> manually
                instantiates and saves an <code>Activity</code> row &mdash; this happens dozens of times throughout
                the controller, covering creation, every publish-wizard stage change, every deployment-pipeline
                status change, and every document upload / payment action.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.candidate-module.transactions') }}" class="btn btn-outline-secondary btn-sm">&larr; Transactions &amp; Finance</a>
        <a href="{{ route('docs.candidate-module.leads-deals') }}" class="btn btn-outline-primary btn-sm">Relationship to Leads &amp; Deals &rarr;</a>
    </div>

</div>

@endsection
