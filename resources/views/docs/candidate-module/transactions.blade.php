@extends('layout.docs.docs_layout')

@section('title', 'Candidate Module - Transactions & Finance')

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
                Transactions &amp; Finance
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Transactions &amp; Finance
    </h2>

    <p class="text-muted mb-4">
        How fees owed and payments received against a candidate are tracked, and how
        <code>candidates.cand_payment_status</code> gets computed.
    </p>

    <!-- Two tables -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Two Related Tables</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr><th>Table</th><th>What it stores</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>candsercharges</code></td>
                            <td>The <strong>fee owed</strong> for a candidate (<code>amount</code>, <code>status</code>, <code>given_by</code>). Only one row can be <code>status = 1</code> ("active") at a time &mdash; enforced in application code, not a DB constraint.</td>
                        </tr>
                        <tr>
                            <td><code>paymentcands</code></td>
                            <td>Each individual <strong>payment received</strong> against that fee (<code>amount</code>, <code>payment_mode</code>, <code>txn_id</code>, <code>bank_to</code>, <code>payment_slip</code>, <code>status</code>).</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Computation -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">How <code>cand_payment_status</code> Is Computed</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Every time a payment is created, edited, or deleted, the sum of that candidate's
                <code>paymentcands.amount</code> is compared against the amount on their currently active
                <code>candsercharges</code> row:
            </p>

            <ul class="mt-2 mb-0">
                <li><strong>sum &ge; active service charge amount</strong> &rarr; <code>candidates.cand_payment_status = "Paid"</code></li>
                <li><strong>sum &lt; active service charge amount</strong> &rarr; <code>candidates.cand_payment_status = "Partial Paid"</code></li>
            </ul>

        </div>

    </div>

    <!-- Payment slips -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Payment Slips &amp; Transaction Numbers</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li>Payment slip images are uploaded to <code>assets/images/payment/candidate</code>.</li>
                <li><code>candidate/check/transaction-number</code> checks a <code>txn_id</code> for duplicates before a payment is saved.</li>
            </ul>

        </div>

    </div>

    <!-- Routes -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Related Routes</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Route</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>candidate.transactionList</code></td><td>The transactions list screen (payments joined to candidate + admin names).</td></tr>
                        <tr><td><code>candamtStr</code>, <code>candamtEdit</code>, <code>candamtUpdt</code>, <code>deleteAmt</code></td><td>Add / edit / update / delete a payment.</td></tr>
                        <tr><td><code>candscharge</code>, <code>SercandamtEdit</code>, <code>SercandamtUpdt</code>, <code>deleteSerAmt</code></td><td>Add / edit / update / delete a service charge.</td></tr>
                        <tr><td>Deactivate / activate service charge</td><td>Switches which <code>candsercharges</code> row is the "active" one for a candidate.</td></tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.candidate-module.status') }}" class="btn btn-outline-secondary btn-sm">&larr; Status &amp; Deployment Pipeline</a>
        <a href="{{ route('docs.candidate-module.activity') }}" class="btn btn-outline-primary btn-sm">Activity Log &rarr;</a>
    </div>

</div>

@endsection
