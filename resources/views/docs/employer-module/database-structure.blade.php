@extends('layout.docs.docs_layout')

@section('title', 'Employer Module - Database Structure')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.employer-module') }}">Employer Module</a>
            </li>
            <li class="breadcrumb-item active">
                Database Structure
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Database Structure
    </h2>

    <p class="text-muted mb-4">
        Two sidebar items — "Employer" and "Employer plus" — map to two nearly-identical tables managed by the
        <strong>same controller</strong>: <code>App\Models\Employer</code> (table <code>employers</code>) and
        <code>App\Models\Employerplus</code> (table <code>employerpluses</code>).
    </p>

    <div class="alert alert-warning">
        Read <a href="{{ route('docs.employer-module.split') }}">Employer vs. Employer Plus</a> before working with
        this module — the two tables look like duplicates but serve genuinely different purposes, and one of them
        holds almost no data in practice.
    </div>

    <!-- employers -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The <code>employers</code> Table</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>employer_name</code>, <code>employer_ar_name</code></td><td>English/Arabic employer name.</td></tr>
                        <tr><td><code>visa_no</code>, <code>id_no</code>, <code>issuing_authority</code>, <code>visa_date</code>, <code>visa_received_date</code>, <code>wakala_status</code></td><td>Visa identity/paperwork fields.</td></tr>
                        <tr><td><code>proff_id</code>, <code>openings</code></td><td>CSV-aligned profession ids and how many openings each has &mdash; index-matched, not a join table.</td></tr>
                        <tr><td><code>salary</code>, <code>businesstype</code>, <code>wpcity_id</code></td><td>Job details.</td></tr>
                        <tr><td><code>booking_id</code>, <code>user_id</code>, <code>cand_id</code>, <code>visadetails_id</code></td><td>Origin fields &mdash; <strong>always populated</strong> on real rows, since this table is auto-created from an order's visa step. See <a href="{{ route('docs.employer-module.split') }}">Employer vs. Employer Plus</a>.</td></tr>
                        <tr><td><code>admin_id</code>, <code>careoff_id</code>, <code>partner_id</code>, <code>partneroffice_id</code></td><td>Ownership.</td></tr>
                        <tr><td><code>status</code></td><td>Active/inactive toggle.</td></tr>
                        <tr><td><code>transfer_status</code></td><td>Present only on this table (not on <code>employerpluses</code>) &mdash; a step flag used within the Orders visa-transfer flow.</td></tr>
                        <tr><td><code>notes</code>, <code>mobile_no</code></td><td>Free text / contact.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-secondary mb-0">
                The live schema has drifted from its migration (<code>2024_11_18_..._create_employers_table.php</code>)
                &mdash; several columns including <code>visadetails_id</code> and <code>transfer_status</code> were
                added later without a corresponding migration file, the same undocumented-drift pattern seen in
                other modules' tables (e.g. <code>leads</code>, <code>candidates</code>).
            </div>

        </div>

    </div>

    <!-- employerpluses -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The <code>employerpluses</code> Table</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Identical column set to <code>employers</code>, with two differences:
            </p>

            <ul class="mt-2 mb-0">
                <li><strong>No <code>transfer_status</code> column.</strong></li>
                <li><strong>Has a <code>payment_status</code> column</strong> (varchar) not present on <code>employers</code> at all — the one distinctive field of this table, driving invoice/payment reconciliation. See <a href="{{ route('docs.employer-module.payment') }}">Payment Status &amp; Invoicing</a>.</li>
            </ul>

        </div>

    </div>

    <!-- Join table -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The <code>employercandidates</code> Join Table</h5>
        </div>

        <div class="card-body">

            <p>
                <code>id</code>, <code>emp_id</code> (&rarr; <code>employerpluses.id</code>), <code>cand_id</code>,
                <code>proff_id</code>, <code>assignbystaff_id</code>, <code>assignbypartner_id</code>,
                <code>assignbydate</code>, <code>status</code>, <code>emp2_id</code> (&rarr; <code>employers.id</code>),
                <code>partneroffice_id</code>, <code>partnersc</code>.
            </p>

            <p class="mb-0">
                A row uses <strong>exactly one</strong> of <code>emp_id</code> / <code>emp2_id</code>, never both
                &mdash; which column is set depends on which employer table the assignment came from. See
                <a href="{{ route('docs.employer-module.assignment') }}">Candidate Assignment</a>.
            </p>

        </div>

    </div>

    <!-- Other tables -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Other Related Tables</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Table</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>employeradminsavefilters</code></td><td>Per-admin saved filter state for the base Employer list.</td></tr>
                        <tr><td><code>employerplusadminsavefilters</code></td><td>Per-admin saved filter state for the Employer Plus list.</td></tr>
                        <tr><td><code>employefilterlists</code></td><td>Shared config — per-admin toggle for which filter columns are visible, not employer data itself.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-danger mb-0">
                <strong>Dead/unshipped: <code>employerpayments</code>.</strong> A migration
                (<code>2024_11_21_..._create_employerpayments_table.php</code>) and an empty model
                (<code>App\Models\Employerpayment</code>) both exist for a table meant to hold
                <code>booking_id</code>, <code>bookingpayment_id</code>, <code>empplus_id</code>, <code>emp_id</code>,
                <code>amount</code>, <code>payment_status</code>, <code>status</code> — but the table
                <strong>does not exist</strong> in the live database, and the model is never referenced by any
                controller. Treat this as planned-but-never-built functionality, not part of the live module.
            </div>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.employer-module') }}" class="btn btn-outline-secondary btn-sm">&larr; Overview</a>
        <a href="{{ route('docs.employer-module.split') }}" class="btn btn-outline-primary btn-sm">Employer vs. Employer Plus &rarr;</a>
    </div>

</div>

@endsection
