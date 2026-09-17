@extends('layout.docs.docs_layout')

@section('title', 'Employer Module - Employer Creation & Editing')

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
                Employer Creation &amp; Editing
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Employer Creation &amp; Editing
    </h2>

    <p class="text-muted mb-4">
        There is exactly one real "create a new employer" form in the codebase, and it only ever writes to
        Employer Plus.
    </p>

    <!-- Create -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Creation &mdash; <code>empVisaStore()</code></h5>
        </div>

        <div class="card-body">

            <p>
                Route <code>admin.employer.storeVisaDet</code>. Always creates a <code>new Employerplus()</code>,
                with <code>admin_id</code> set from the logged-in admin. Fields captured: business type, visa
                number, id number, professions + openings (CSV, index-aligned), employer name (English/Arabic),
                issuing authority, visa dates, work city, salary, partner office, notes, care-off staff, wakala
                status. No <code>booking_id</code>/<code>user_id</code>/<code>cand_id</code> are set — confirming
                this path is genuinely independent of any order.
            </p>

            <div class="alert alert-warning mb-0">
                There is no equivalent creation path for the base <code>employers</code> table anywhere in the
                codebase. See <a href="{{ route('docs.employer-module.split') }}">Employer vs. Employer Plus</a> for
                the confirmed bug where the base screen's "Add Employer" button uses this exact same endpoint.
            </div>

        </div>

    </div>

    <!-- Edit -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Editing</h5>
        </div>

        <div class="card-body">

            <p>
                Employer Plus: <code>empVisaEditp()</code> (fetch) / <code>empVisaUpdateP()</code> (save) — a clean,
                dedicated pair operating only on <code>Employerplus</code>.
            </p>

            <p class="mb-0">
                Base Employer: <code>empVisaEdit()</code> (fetch) / <code>empVisaUpdate()</code> (save) — but
                <code>empVisaUpdate()</code> contains a fallback:
            </p>

<pre><code>$post = Employer::find($request->edit_id);
if (empty($post)) {
    $post = Employerplus::find($request->edit_id);
}</code></pre>

            <p class="mt-2 mb-0">
                So this one handler will transparently edit either table depending on which id matches — evidence
                the two tables' ids are treated as almost interchangeable at the code level, even though the UI only
                wires this from the base Employer edit modal.
            </p>

        </div>

    </div>

    <!-- Status / delete -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Status Toggle &amp; Deletion</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li><code>empStatusUpdate()</code> / <code>empStatusUpdatep()</code> — toggle the active/inactive <code>status</code> flag on each respective table.</li>
                <li><code>deleteEmployer()</code> / <code>deleteEmployerp()</code> — delete the employer row, and cascade-clean the matching <code>employercandidates</code> rows (by <code>emp2_id</code> / <code>emp_id</code> respectively), releasing every assigned candidate's <code>status</code> back to <code>true</code>.</li>
                <li><code>checkEmpVisa()</code> / <code>edcheckEmpVisa()</code> — validate <code>visa_no</code> uniqueness across <strong>both</strong> tables combined, not just the one being edited. See <a href="{{ route('docs.employer-module.split') }}">Employer vs. Employer Plus</a>.</li>
            </ul>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.employer-module.split') }}" class="btn btn-outline-secondary btn-sm">&larr; Employer vs. Employer Plus</a>
        <a href="{{ route('docs.employer-module.visa-views') }}" class="btn btn-outline-primary btn-sm">Visa Details Views &rarr;</a>
    </div>

</div>

@endsection
