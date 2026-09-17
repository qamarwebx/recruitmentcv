@extends('layout.docs.docs_layout')

@section('title', 'Employer Module - Payment Status & Invoicing')

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
                Payment Status &amp; Invoicing
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Payment Status &amp; Invoicing
    </h2>

    <div class="alert alert-warning">
        This entire page is <strong>Employer Plus only</strong> — the base <code>employers</code> table has no
        <code>payment_status</code> column and no equivalent endpoints.
    </div>

    <!-- Field -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The <code>payment_status</code> Field</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Read via <code>getpaymentstatus()</code> (route <code>admin.employer.getpaymentstatus</code>),
                written via <code>paymentstatusupdate()</code> (route
                <code>admin.employer.paymentstatusupdate</code>) — both operate only on
                <code>Employerplus.payment_status</code>.
            </p>

        </div>

    </div>

    <!-- Integration -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Invoicing Integration</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>InvoiceController</code>, <code>PartnerInvoiceController</code>,
                <code>PaymentController</code>, and <code>PartnerPaymentController</code> all join
                <code>employerpluses as emp on emp.id = empcand.emp_id</code> when reconciling payments, and update
                <code>Employerplus.payment_status</code> as part of that flow. This confirms Employer Plus &mdash;
                not the base <code>employers</code> table &mdash; is the one genuinely wired into the CRM's
                invoicing/payment lifecycle, consistent with it being the actively-used data pool overall (see
                <a href="{{ route('docs.employer-module.split') }}">Employer vs. Employer Plus</a>).
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.employer-module.assignment') }}" class="btn btn-outline-secondary btn-sm">&larr; Candidate Assignment</a>
        <a href="{{ route('docs.employer-module.work-agreement') }}" class="btn btn-outline-primary btn-sm">Work Agreement PDF &rarr;</a>
    </div>

</div>

@endsection
