@extends('layout.docs.docs_layout')

@section('title', 'Employer Module - Employer vs. Employer Plus')

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
                Employer vs. Employer Plus
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Employer vs. Employer Plus
    </h2>

    <p class="text-muted mb-4">
        Read this page first. "Employer" and "Employer plus" look like a base feature and its upgraded sibling —
        they're actually two separate data pools serving different purposes, and one of them is barely used.
    </p>

    <!-- Data proof -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">What the Data Actually Shows</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr><th>Table</th><th>Live rows</th><th><code>booking_id</code></th></tr>
                    </thead>
                    <tbody>
                        <tr><td><code>employers</code></td><td>~1</td><td>Always populated</td></tr>
                        <tr><td><code>employerpluses</code></td><td>~363</td><td>Always <code>NULL</code></td></tr>
                    </tbody>
                </table>
            </div>

            <p class="mb-0">
                In other words: <strong>"Employer plus" is the real, actively-used employer roster</strong>,
                manually maintained by staff. <strong>Base "Employer"</strong> is almost entirely a passive
                read/edit view of the handful of records auto-materialized by the
                <a href="{{ route('docs.orders-module.visa-payment') }}">Orders module's visa step</a>.
            </p>

        </div>

    </div>

    <!-- Two systems -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Two Systems, One Controller</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr><th></th><th>Employer (base)</th><th>Employer Plus</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Table</td><td><code>employers</code></td><td><code>employerpluses</code></td></tr>
                        <tr><td>Model</td><td><code>Employer</code></td><td><code>Employerplus</code></td></tr>
                        <tr><td>List route</td><td><code>admin.employer</code></td><td><code>admin.employer.listp</code></td></tr>
                        <tr><td>List query</td><td><code>whereNotNull('booking_id')</code></td><td><code>whereNull('booking_id')</code></td></tr>
                        <tr><td>How records are created</td><td>Auto-created only, via an order's visa step</td><td>Manually, via a real create form</td></tr>
                        <tr><td>Payment lifecycle</td><td>None</td><td><code>payment_status</code>, integrated with Invoicing</td></tr>
                        <tr><td>Join column on <code>employercandidates</code></td><td><code>emp2_id</code></td><td><code>emp_id</code></td></tr>
                    </tbody>
                </table>
            </div>

            <p class="mb-0 text-muted">
                Both flows are implemented in the same <code>EmployerController</code>, with parallel method names
                (e.g. <code>indexp()</code>, <code>empVisaEditp()</code>, <code>deleteEmployerp()</code> — the
                trailing "p" marks the Employer Plus variant throughout).
            </p>

        </div>

    </div>

    <!-- Confirmed bug -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">A Real, Confirmed Bug: "Add Employer" Creates an Employer Plus Record</h5>
        </div>

        <div class="card-body">

            <p>
                The base Employer screen still ships an "Add Employer" button and form. It posts to
                <code>admin.employer.storeVisaDet</code>, which is handled by <code>empVisaStore()</code> —
                and that method <strong>always creates a <code>new Employerplus()</code>, never a
                <code>new Employer()</code></strong>, regardless of which screen the form was opened from.
            </p>

            <div class="alert alert-danger mb-0">
                Clicking "Add Employer" from the base Employer screen silently creates an Employer Plus record
                (with <code>booking_id = null</code>). Because the base Employer list is filtered to
                <code>whereNotNull('booking_id')</code>, that new record will <strong>never appear back on the
                screen it was created from</strong> — it shows up on the Employer Plus list instead. There is
                genuinely no create path that writes to <code>employers</code> anywhere in the codebase except
                inside <code>BookingController::visaStr()</code>.
            </div>

        </div>

    </div>

    <!-- Shared namespace -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">One Place They're Treated as the Same Thing</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Visa number uniqueness is checked <strong>across both tables combined</strong>
                (<code>checkEmpVisa()</code> counts matches in both <code>Employerplus</code> and
                <code>Employer</code>) — so despite being physically separate tables with separate purposes, visa
                numbers are meant to be a single shared namespace. Similarly, <code>empVisaUpdate()</code> (the base
                Employer's edit handler) falls back to looking up an <code>Employerplus</code> row if the id isn't
                found in <code>Employer</code> — a sign the two ID spaces are treated as almost interchangeable at
                the code level, even though the UI keeps them on separate screens.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.employer-module.database') }}" class="btn btn-outline-secondary btn-sm">&larr; Database Structure</a>
        <a href="{{ route('docs.employer-module.creation') }}" class="btn btn-outline-primary btn-sm">Employer Creation &amp; Editing &rarr;</a>
    </div>

</div>

@endsection
