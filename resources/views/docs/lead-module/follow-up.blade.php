@extends('layout.docs.docs_layout')

@section('title', 'Lead Module - Follow-up')

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
                Follow-up
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Follow-up
    </h2>

    <p class="text-muted mb-4">
        How recruiters track communication with a candidate, and how the system knows a lead is going cold.
    </p>

    <div class="card mb-4">

        <div class="card-body">

            <p class="mb-0">
                Every follow-up interaction (a call, WhatsApp message, or general note) is saved as a row in the
                <code>leadnotes</code> table via <code>LeadController::addnotes()</code> &mdash; see
                <a href="{{ route('docs.lead-module.notes') }}">Lead Notes</a> for the table structure. There is no
                separate "schedule a follow-up for later" field &mdash; instead, the system tracks <em>recency</em>
                of contact and uses that to surface leads that are going cold.
            </p>

        </div>

    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Follow-up Flow</h5>
        </div>

        <div class="card-body">

            <ol class="mb-0">
                <li>
                    <strong>Open Assigned Lead</strong><br>
                    The recruiter opens a lead assigned to them (<code>leadassign_id</code> = their own admin id).
                </li>

                <li class="mt-3">
                    <strong>Contact the Candidate</strong><br>
                    The recruiter contacts the candidate via phone, WhatsApp, or email using the number/email stored on the lead.
                </li>

                <li class="mt-3">
                    <strong>Record a Note</strong><br>
                    The recruiter adds a note describing the conversation. This creates a new <code>leadnotes</code> row. See <a href="{{ route('docs.lead-module.notes') }}">Lead Notes</a>.
                </li>

                <li class="mt-3">
                    <strong>Timestamp Is Updated</strong><br>
                    Adding a note (or qualifying the lead) stamps <code>leads.staff_updated_at = now()</code>. This is what lets the CRM filter for "leads not followed up in the last N days".
                </li>

                <li class="mt-3">
                    <strong>Update Qualification (Optional)</strong><br>
                    If the interaction is conclusive, the recruiter updates the qualification status in the same step &mdash; see <a href="{{ route('docs.lead-module.qualification') }}">Qualification</a>.
                </li>

                <li class="mt-3">
                    <strong>Repeat Leads Get Re-checked</strong><br>
                    If the candidate submits the form again later (a repeat lead), the system checks whether the current owner has left a <code>leadnotes</code> entry for that lead in the <strong>last 7 days</strong>. If not, the lead is automatically reassigned to a different active admin so it doesn't stay stuck with someone who never followed up. See <a href="{{ route('docs.lead-module.duplicate') }}">Duplicate Check</a>.
                </li>

                <li class="mt-3">
                    <strong>Continue Recruitment Process</strong><br>
                    The recruiter keeps following up until the lead is marked qualified, not qualified, or not relevant.
                </li>
            </ol>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.lead-assignment') }}#reassignment" class="btn btn-outline-secondary btn-sm">&larr; Lead Reassignment</a>
        <a href="{{ route('docs.lead-module.notes') }}" class="btn btn-outline-primary btn-sm">Lead Notes &rarr;</a>
    </div>

</div>

@endsection
