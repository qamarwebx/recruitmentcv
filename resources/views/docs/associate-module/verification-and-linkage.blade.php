@extends('layout.docs.docs_layout')

@section('title', 'Associate Module - Verification & Cross-Module Linkage')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.associate-module') }}">Associate Module</a>
            </li>
            <li class="breadcrumb-item active">
                Verification &amp; Cross-Module Linkage
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Verification &amp; Cross-Module Linkage
    </h2>

    <p class="text-muted mb-4">
        Associates go through an OTP verification flow despite never logging in themselves, and connect to both
        Candidates and Deal Pipeline — but not to any commission system.
    </p>

    <!-- OTP flow -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The OTP Verification Flow</h5>
        </div>

        <div class="card-body">

            <p>
                Unusual for a record with no login of its own: an associate's contact details go through the same
                kind of OTP verification a user account might. Staff trigger each step from the admin panel on the
                associate's behalf:
            </p>

            <ul class="mb-0">
                <li><code>primary_getotp</code> / <code>secondary_getotp</code> &mdash; sends an OTP via WhatsApp (Meta template <code>otpone</code>), storing the code and number in session.</li>
                <li><code>email_getotp</code> &mdash; sends an OTP via a dedicated mailable.</li>
                <li><code>*_otp_validate</code> &mdash; compares the submitted code against the session value.</li>
                <li><code>primmobverif($id)</code> &mdash; sets <code>pty_mobile_verifed = true</code>, and also <code>contact_verified = true</code> and <code>status = true</code>.</li>
                <li><code>secmobverif($id)</code> &mdash; sets <code>sec_mob_no_verified = true</code>; if the primary mobile is already verified, also flips <code>contact_verified</code> and <code>status</code> to <code>true</code>.</li>
                <li><code>emailverif($id)</code> &mdash; sets <code>pty_email_verified = true</code>; if mobile is already verified, also sets <code>contact_verified = true</code>.</li>
            </ul>

            <p class="mt-3 mb-0 text-muted">
                In effect, <code>contact_verified</code>/<code>status</code> become <code>true</code> once
                <strong>both mobile numbers</strong> are verified — per the actual conditionals, email verification
                is tracked but not required to flip the aggregate flag.
            </p>

        </div>

    </div>

    <!-- Candidate gate -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The Candidate "Confirmation" Gate</h5>
        </div>

        <div class="card-body">

            <p>
                Candidates carry <code>associate_id</code>, <code>associateconfirmby_id</code>,
                <code>associateconfirmby_status</code>, and <code>associate_confirm</code> (a free-text note). These
                are set by <code>CandidateController::candassocconfirmby()</code>
                (route <code>admin.candidate.assoc.confirmby</code>), which does two things at once:
            </p>

            <ol>
                <li>Writes an audit-log row to a separate table, <code>canassocconfirmbies</code> (model <code>Canassocconfirmby</code>) — a full history of every confirmation event, who did it, and when.</li>
                <li>Mirrors the latest confirmation onto the candidate row itself (<code>associateconfirmby_id</code>, <code>associateconfirmby_status = true</code>, <code>associate_confirm</code>).</li>
            </ol>

            <p>
                Critically, it's an <strong>admin staff member confirming</strong>, not the associate themselves
                (associates have no login to act through) — "confirmation" here means a staff member has verified
                something about the associate's involvement with this candidate, recorded as a free-text note.
            </p>

            <div class="alert alert-primary mb-0">
                <strong>What it's actually for:</strong> <code>associate_id != ''</code> and
                <code>associateconfirmby_status != '0'</code> are two of roughly 20 conditions checked across
                several update handlers to decide whether a candidate's <code>cv_execute</code> flag can be set
                <code>true</code> — i.e. whether the candidate's CV is fully ready to be executed/published. See
                the Candidate module's <a href="{{ route('docs.candidate-module.documents') }}">Documents</a> page
                for the full <code>cv_execute</code> eligibility checklist. In short: <strong>a candidate must have
                an associate assigned, and that assignment must be confirmed by staff, before the CV can be marked
                ready.</strong>
            </div>

            <p class="mt-3 mb-0 text-muted">
                Data-integrity gap: changing a candidate's associate later
                (<code>careoffupdate</code> or the dedicated <code>assocupdate</code> /
                <code>admin.candidate.update.assoc</code> endpoint) does <strong>not</strong> reset
                <code>associateconfirmby_status</code> — so a stale confirmation can remain flagged "confirmed"
                against a different associate than the one originally confirmed.
            </p>

        </div>

    </div>

    <!-- Deal pipeline -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Deal Pipeline: a Mandatory Attribution Field</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>deal_pipeline.associate_id</code> is the one genuinely FK-backed relationship on that table
                (see <a href="{{ route('docs.deal-pipeline-module.database') }}">Deal Pipeline: Database Structure</a>).
                It's <strong>required</strong> on both create and edit (validated
                <code>required|integer</code>) — every "Job seeker" deal must have an associate. It's used purely
                for filtering, reporting, and display (list filters, saved admin filter preferences, dropdown
                sourcing) — there's no commission or payout calculation attached to it (see below).
            </p>

        </div>

    </div>

    <!-- No commission -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">No Commission System</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Despite associates functioning as sourcing agents for both candidates and deals, there is
                <strong>no commission, payout, or ledger logic anywhere in the codebase</strong> tied to
                <code>associate_id</code> — a full search for "commission" across the entire application returns
                zero matches. The Associate module is purely an attribution record (who brought this in), not a
                financial-settlement feature.
            </p>

        </div>

    </div>

</div>

@endsection
