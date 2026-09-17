@extends('layout.docs.docs_layout')

@section('title', 'Lead Module - Lead Notes')

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
                Lead Notes
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Lead Notes
    </h2>

    <p class="text-muted mb-4">
        The <code>leadnotes</code> table &mdash; the record of every call, message, or comment logged against a lead.
    </p>

    <!-- Table -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Table Structure</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Column</th>
                            <th>Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>lead_id</code></td><td>Which lead this note belongs to.</td></tr>
                        <tr><td><code>admin_id</code></td><td>Which staff member wrote the note.</td></tr>
                        <tr><td><code>notes</code></td><td>The free-text content of the note/call summary.</td></tr>
                        <tr><td><code>conversation_type</code></td><td>How contact was made (e.g. call, WhatsApp).</td></tr>
                        <tr><td><code>is_qualified</code></td><td>Optional &mdash; the qualification status at the time this note was added, when the note was created alongside a qualification update.</td></tr>
                        <tr><td><code>call_not_connected_type</code></td><td>Optional &mdash; reason captured when the note corresponds to a failed call attempt.</td></tr>
                        <tr><td><code>created_at</code>, <code>updated_at</code></td><td>Standard timestamps &mdash; <code>created_at</code> is what "did they follow up today / in the last 7 days" checks are based on.</td></tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 mb-0">
                Model: <code>App\Models\Leadnote</code>. Related on the lead side via
                <code>Lead::notes()</code> &mdash; <code>hasMany(Leadnote::class, 'lead_id')</code>.
            </p>

        </div>

    </div>

    <!-- How created -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">How a Note Gets Created</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li>Manually, when a recruiter adds a note from the lead screen &mdash; <code>LeadController::addnotes()</code>.</li>
                <li>Automatically, as a side effect of updating the qualification status via <code>LeadController::qualified()</code>, when a note is included with that update.</li>
            </ul>

        </div>

    </div>

    <!-- Why it matters -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Why Notes Matter Beyond the Timeline</h5>
        </div>

        <div class="card-body">

            <p>
                Notes aren't just a log &mdash; the CRM actively reads <code>leadnotes.created_at</code> to make
                assignment decisions:
            </p>

            <ul class="mb-0">
                <li>
                    <strong>Repeat-lead reassignment:</strong> when a candidate resubmits the form, the system checks
                    for a <code>leadnotes</code> entry from the current owner in the <strong>last 7 days</strong>. No
                    note in that window means the lead is reassigned to a different active admin. See
                    <a href="{{ route('docs.lead-module.duplicate') }}">Duplicate Check</a>.
                </li>
                <li>
                    <strong>Follow-up recency filters:</strong> combined with <code>leads.staff_updated_at</code>,
                    notes are how the CRM answers "which of my leads haven't been touched recently?" See
                    <a href="{{ route('docs.lead-module.followup') }}">Follow-up</a>.
                </li>
            </ul>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.lead-module.followup') }}" class="btn btn-outline-secondary btn-sm">&larr; Follow-up</a>
        <a href="{{ route('docs.lead-module.qualification') }}" class="btn btn-outline-primary btn-sm">Qualification &rarr;</a>
    </div>

</div>

@endsection
