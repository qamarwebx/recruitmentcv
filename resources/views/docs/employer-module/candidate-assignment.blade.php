@extends('layout.docs.docs_layout')

@section('title', 'Employer Module - Candidate Assignment')

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
                Candidate Assignment
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Candidate Assignment
    </h2>

    <p class="text-muted mb-4">
        One shared method assigns a candidate to either an Employer or an Employer Plus record, distinguished by a
        request flag.
    </p>

    <!-- The method -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0"><code>assigncandtoemp()</code></h5>
        </div>

        <div class="card-body">

            <p>
                Route <code>admin.employer.assigncandtoemp</code>. Works for both tables via a
                <code>fromreq</code> request parameter:
            </p>

<pre><code>$isEmployerPlus = $request->fromreq == 'employerplus';
$employerModel  = $isEmployerPlus ? Employerplus::class : Employer::class;
$employerColumn = $isEmployerPlus ? 'emp_id' : 'emp2_id';</code></pre>

            <ol class="mb-0">
                <li>Finds the target employer row.</li>
                <li>Computes the remaining openings for the requested profession from the CSV-aligned <code>proff_id</code>/<code>openings</code> fields.</li>
                <li>Counts already-assigned <em>active</em> candidates for that profession (<code>Employercandidate::where($employerColumn, ...)-&gt;where('proff_id', ...)-&gt;where('status', 1)</code>).</li>
                <li>If a slot remains: creates a new <code>Employercandidate</code> row (<code>emp_id</code> or <code>emp2_id</code> set accordingly, plus <code>partneroffice_id</code> copied from the employer, <code>assignbystaff_id</code>, <code>assignbydate = now()</code>), and flips the candidate's <code>status = false</code> (also defaults <code>cand_payment_status</code> to <code>'Unpaid'</code> if empty).</li>
                <li>If no slot remains: returns a "Visa slot not available" error.</li>
            </ol>

        </div>

    </div>

    <!-- Note on Orders -->

    <div class="alert alert-info mb-4">
        This is the module's <strong>own</strong> assignment mechanism. It's distinct from the automatic
        assignment that happens inside <code>BookingController::visaStr()</code> when an Orders-created Employer
        row is first materialized — see <a href="{{ route('docs.orders-module.employer-assignment') }}">Orders: Employer &amp; Candidate Assignment</a>
        for that path.
    </div>

    <!-- Companion endpoints -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Companion Endpoints</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li><code>assigngetdata()</code> (route <code>admin.employer.assigngetdata</code>) — fetches candidate detail for a given <code>employercandidates</code> row.</li>
                <li><code>deassigngetdata()</code> (route <code>admin.employer.deassigncandidate</code>) — a soft "unassign": sets <code>Employercandidate.status = false</code> and releases the candidate (<code>Candidate.status = true</code>) so they become bookable/assignable again.</li>
            </ul>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.employer-module.visa-views') }}" class="btn btn-outline-secondary btn-sm">&larr; Visa Details Views</a>
        <a href="{{ route('docs.employer-module.payment') }}" class="btn btn-outline-primary btn-sm">Payment Status &amp; Invoicing &rarr;</a>
    </div>

</div>

@endsection
