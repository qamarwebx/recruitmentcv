@extends('layout.docs.docs_layout')

@section('title', 'Orders Module - Visa, Payment & Employer Creation')

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
                Visa, Payment &amp; Employer Creation
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Visa, Payment &amp; Employer Creation
    </h2>

    <div class="alert alert-danger">
        <strong>An order does not belong to a pre-existing Employer record.</strong> It's the other way around:
        completing the visa step on an order is what <em>creates</em> the Employer record. This is the single most
        important thing to understand about how this module connects to the rest of the CRM.
    </div>

    <!-- Visa step -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The Visa Step &mdash; <code>visaStr()</code></h5>
        </div>

        <div class="card-body">

            <p>
                When admin staff fill in visa/employer details for an order (<code>getVisa()</code> to fetch the
                form, <code>visaStr()</code> to save), two things happen:
            </p>

            <ol class="mb-0">
                <li>
                    A row is saved to <code>visadetails</code>: <code>booking_id</code>, <code>user_id</code>,
                    <code>cand_id</code>, <code>visa_no</code>, <code>id_no</code>, <code>proff_id</code>,
                    <code>employer_name</code>/<code>employer_ar_name</code>, <code>issuing_authority</code>,
                    <code>wpcity_id</code>, <code>salary</code>, <code>businesstype</code>,
                    <code>wakala_status</code>, <code>openings</code>.
                </li>
                <li class="mt-3">
                    <strong>A new <code>employers</code> row is inserted</strong> (or an existing one updated),
                    copying straight from the request/booking: <code>visadetails_id</code>, <code>booking_id</code>,
                    <code>user_id</code>, <code>cand_id</code>, <code>employer_name</code>, <code>proff_id</code>,
                    <code>admin_id</code>, <code>partneroffice_id</code>, <code>careoff_id</code>,
                    <code>wakala_status</code>, <code>openings</code>, <code>transfer_status</code>.
                </li>
            </ol>

            <p class="mt-3 mb-0">
                <code>bookings.visa_status</code> is flipped to <code>1</code>. As covered in
                <a href="{{ route('docs.orders-module.status') }}">Order Status &amp; Lifecycle</a>, if an order
                status has already been recorded at this point, the order auto-completes.
            </p>

        </div>

    </div>

    <!-- Two producers -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Two Different Ways the Same <code>employers</code> Table Gets Populated</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr><th>Path</th><th>How the row gets created</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Via an Order (this module)</strong></td>
                            <td>Automatic, as a side effect of <code>visaStr()</code> above. The row is tagged back to its originating order via <code>employers.booking_id</code>.</td>
                        </tr>
                        <tr>
                            <td>Via the standalone Employer module</td>
                            <td>Manual — staff directly create/edit employer records through <code>admin.employer.*</code> / <code>EmployerController</code>, with no booking involved at all.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 mb-0 text-muted">
                Both paths write to the same <code>employers</code> table, so a given employer record may or may not
                have a populated <code>booking_id</code> depending on which path created it. There is also a
                separate, parallel <code>Employerplus</code> model/table used by "employer-list-plus" admin screens
                — see <a href="{{ route('docs.orders-module.employer-assignment') }}">Employer &amp; Candidate Assignment</a>
                for how that fits in.
            </p>

        </div>

    </div>

    <!-- Payment -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Payment &mdash; <code>paymentStr()</code></h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>getPayment()</code>/<code>paymentStr()</code> add or update a <code>bookingpayments</code>
                row (<code>booking_id</code>, <code>user_id</code>, <code>cand_id</code>, <code>amount</code>,
                confirmation admin/partner ids). Recording a payment flips <code>bookings.payment_status</code> to
                <code>1</code>. Unlike <code>bookings.amount</code> (free text, effectively unused), this is where
                the real order value lives.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.orders-module.status') }}" class="btn btn-outline-secondary btn-sm">&larr; Order Status &amp; Lifecycle</a>
        <a href="{{ route('docs.orders-module.employer-assignment') }}" class="btn btn-outline-primary btn-sm">Employer &amp; Candidate Assignment &rarr;</a>
    </div>

</div>

@endsection
