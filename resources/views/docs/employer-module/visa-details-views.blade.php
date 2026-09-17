@extends('layout.docs.docs_layout')

@section('title', 'Employer Module - Visa Details Views')

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
                Visa Details Views
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Visa Details Views
    </h2>

    <div class="alert alert-warning">
        Despite the route names, <strong><code>admin.employer.visaDetshow</code> and
        <code>admin.employer.visaDetshowp</code> do not query the Orders module's <code>visadetails</code>
        table.</strong> They show the <code>employers</code>/<code>employerpluses</code> rows themselves, which
        carry their own copy of visa fields.
    </div>

    <!-- The show pages -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">What Each Page Actually Shows</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr><th>Route</th><th>Method</th><th>Queries</th><th>View</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>admin.employer.visaDetshow</code></td>
                            <td><code>VisaDetView($id)</code></td>
                            <td><code>employers</code>, joined to partners/bookings/users/candidates/professions/cities/admins</td>
                            <td><code>admin.employer.visa.show2</code></td>
                        </tr>
                        <tr>
                            <td><code>admin.employer.visaDetshowp</code></td>
                            <td><code>VisaDetViewp($id)</code></td>
                            <td><code>employerpluses</code>, same joins</td>
                            <td><code>admin.employer.visa.show</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mb-0">
                Both compute an "openings balance" per profession: total openings from the CSV-aligned
                <code>proff_id</code>/<code>openings</code> fields, minus the count of currently assigned
                candidates for that profession — this is what drives the "assign candidate" UI on the same page.
                See <a href="{{ route('docs.employer-module.assignment') }}">Candidate Assignment</a>.
            </p>

        </div>

    </div>

    <!-- The real visadetails table -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Where the Real <code>visadetails</code> Table Shows Up</h5>
        </div>

        <div class="card-body">

            <p>
                The Orders module's <code>visadetails</code> table (see
                <a href="{{ route('docs.orders-module.visa-payment') }}">Orders: Visa, Payment &amp; Employer Creation</a>)
                <strong>does</strong> appear on these same two Blade views, but for a different sub-purpose —
                candidate <em>pre-assignment</em> at the order level, separate from the employer-level assignment
                described on the next page:
            </p>

            <ul class="mb-0">
                <li><code>AddVisaCandidate()</code> / <code>RemoveVisaCandidate()</code> — set/clear <code>visadetails.cand_id</code>.</li>
                <li><code>VisaIndexJson()</code> / <code>VisaIndexpJson()</code> — AJAX DataTables feeds reading <code>visadetails</code>, filtered by <code>whereNotNull</code>/<code>whereNull('booking_id')</code> to match the base/plus split.</li>
                <li><code>empCheckVisa()</code> / <code>empedcheckVisa()</code> — a <strong>third</strong>, separate visa-number uniqueness check, this time against <code>visadetails</code> specifically (distinct from the <code>checkEmpVisa()</code>/<code>edcheckEmpVisa()</code> pair covered in <a href="{{ route('docs.employer-module.creation') }}">Employer Creation &amp; Editing</a>, which checks <code>Employer</code>/<code>Employerplus</code> instead).</li>
            </ul>

        </div>

    </div>

    <!-- Summary -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Three Separate "Visa Number" Checks</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Between this page and <a href="{{ route('docs.employer-module.creation') }}">Employer Creation &amp; Editing</a>,
                there are three distinct visa-number uniqueness checks in the codebase, each against a different
                table or table-pair (<code>Employer</code>+<code>Employerplus</code> combined, and
                <code>visadetails</code> separately) — worth knowing if you ever see a "visa number already exists"
                error and need to trace which check produced it.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.employer-module.creation') }}" class="btn btn-outline-secondary btn-sm">&larr; Employer Creation &amp; Editing</a>
        <a href="{{ route('docs.employer-module.assignment') }}" class="btn btn-outline-primary btn-sm">Candidate Assignment &rarr;</a>
    </div>

</div>

@endsection
