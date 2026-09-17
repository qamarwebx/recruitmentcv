@extends('layout.docs.docs_layout')

@section('title', 'Orders Module - Employer & Candidate Assignment')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.orders-module') }}">Orders Module</a>
            </li>
            <li class="breadcrumb-item active">
                Employer &amp; Candidate Assignment
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Employer &amp; Candidate Assignment
    </h2>

    <p class="text-muted mb-4">
        The <code>employercandidates</code> table is a generic assignment ledger shared by <strong>two different
        employer concepts</strong> in this codebase — the Orders module is only one of its two producers.
    </p>

    <!-- Two producers -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Two FK Columns, Two Different Sources</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr><th>Column</th><th>Points to</th><th>Populated by</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>emp_id</code></td>
                            <td><code>Employerplus</code> (table <code>employerplus</code>)</td>
                            <td>The separate "Employer List Plus" admin screens — a manual employer/vacancy management flow, independent of Orders.</td>
                        </tr>
                        <tr>
                            <td><code>emp2_id</code></td>
                            <td><code>Employer</code> (table <code>employers</code>)</td>
                            <td><strong>This module</strong> — automatically, inside <code>BookingController::visaStr()</code>. See <a href="{{ route('docs.orders-module.visa-payment') }}">Visa, Payment &amp; Employer Creation</a>.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 mb-0 text-muted">
                Note: the <code>Employercandidate</code> model has no <code>emp2()</code> relation defined even
                though <code>emp2_id</code> is the column actually populated by the Orders flow — it's only ever
                queried raw (<code>where('emp2_id', ...)</code>).
            </p>

        </div>

    </div>

    <!-- Full chain -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The Full Order-Driven Chain</h5>
        </div>

        <div class="card-body">

<pre>users (customer)
    │  places an order
    ▼
bookings
    │  visa/employer details added (visaStr)
    ▼
employers  (emp2_id side, employers.booking_id = the order)
    │
    ▼
employercandidates  (emp2_id = employers.id, cand_id = the candidate)</pre>

            <p class="mt-3 mb-0">
                This is the mechanism that finally links a specific candidate to the employer who will receive
                them, as a downstream consequence of the order's visa step — distinct from the sales/BD
                <a href="{{ route('docs.deal-pipeline-module') }}">Deal Pipeline</a> and distinct from the
                standalone Employer-Plus module, which populates the same ledger table through the other
                (<code>emp_id</code>) path.
            </p>

        </div>

    </div>

    <!-- Row fields -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Other Columns on <code>employercandidates</code></h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li><code>proff_id</code> &mdash; the profession/role for this assignment.</li>
                <li><code>assignbystaff_id</code>, <code>assignbypartner_id</code> &mdash; who made the assignment (admin staff vs. a partner-side flow).</li>
                <li><code>assignbydate</code> &mdash; when.</li>
                <li><code>status</code> &mdash; active/inactive.</li>
                <li><code>partneroffice_id</code> &mdash; copied from the order's <code>partner_id</code> when applicable.</li>
                <li><code>partnersc</code> &mdash; free text.</li>
            </ul>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.orders-module.visa-payment') }}" class="btn btn-outline-secondary btn-sm">&larr; Visa, Payment &amp; Employer Creation</a>
        <a href="{{ route('docs.orders-module.receiver-panel') }}" class="btn btn-outline-primary btn-sm">Order Receiver Panel &rarr;</a>
    </div>

</div>

@endsection
