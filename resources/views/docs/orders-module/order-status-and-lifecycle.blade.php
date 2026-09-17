@extends('layout.docs.docs_layout')

@section('title', 'Orders Module - Order Status & Lifecycle')

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
                Order Status &amp; Lifecycle
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Order Status &amp; Lifecycle
    </h2>

    <p class="text-muted mb-4">
        <code>admin.orderStatus</code> manages a small lookup list that turns out to be shared with the Candidate
        module's <code>cand_status</code>-style FK — but not in the way you'd expect.
    </p>

    <!-- Table -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The <code>order_statuses</code> Table</h5>
        </div>

        <div class="card-body">

            <p>
                <code>id</code>, <code>ord_status</code> (English label), <code>ar_status</code> (Arabic label),
                <code>admin_id</code>, <code>status</code> (active flag), timestamps. Managed via
                <code>OrderStatusController</code> at <code>admin.orderStatus</code>.
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>id</th><th>ord_status</th></tr></thead>
                    <tbody>
                        <tr><td>1</td><td>Processing</td></tr>
                        <tr><td>2</td><td>Payment</td></tr>
                        <tr><td>3</td><td>Completed</td></tr>
                    </tbody>
                </table>
            </div>

            <p class="mb-0 text-muted">
                In practice most live orders sit at "Processing" — status is rarely advanced further in this
                dataset.
            </p>

        </div>

    </div>

    <!-- Advancing -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">How an Order's Status Advances</h5>
        </div>

        <div class="card-body">

            <p>
                Admin staff pick a status from the order detail view; <code>BookingController::orderstatusStr()</code>:
            </p>

            <ol class="mb-0">
                <li>Upserts a row in <code>bookingorderstatuses</code> (the audit/history table), keyed by <code>booking_id</code>.</li>
                <li>Updates <code>bookings.ord_status_id</code> and <code>bookings.payconfirm_admin_id</code>.</li>
                <li>If <code>bookings.visa_status == 1</code> at this point, also auto-flips <code>booking_status = 1</code> and <code>status = true</code> &mdash; the order auto-completes once both a visa and a status have been recorded.</li>
                <li>Logs an <code>Activity</code> timeline entry.</li>
            </ol>

        </div>

    </div>

    <!-- Shared with candidates -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">A Real FK, But Not a Live Sync</h5>
        </div>

        <div class="card-body">

            <p>
                This is the same <code>order_statuses</code> table referenced by the Candidate module's
                <code>candidates.order_status</code> column &mdash; and unlike most cross-module references in this
                app, that one is a <strong>real, DB-enforced foreign key</strong> (<code>ON DELETE CASCADE</code>).
            </p>

            <div class="alert alert-warning mb-0">
                <strong>But it's a shared vocabulary, not an active sync.</strong> There is no controller code that
                updates <code>candidates.order_status</code> when <code>orderstatusStr()</code> changes
                <code>bookings.ord_status_id</code> (or vice versa), and the <code>Candidate</code> model has no
                Eloquent relation for this column at all &mdash; it's enforced only at the database layer. The two
                columns happen to draw from the same lookup list, but changing one does not change the other.
            </div>

        </div>

    </div>

    <!-- Bug -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">A Real Routing Bug</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>POST orderStatus/delete</code> (named <code>admin.orderStatus.delete</code>) is bound to
                <strong><code>ProfessionController@delete</code></strong>, not <code>OrderStatusController</code>
                &mdash; almost certainly a copy/paste mistake in the route definitions. Deleting an order status
                through this route will not behave as expected.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.orders-module.creation') }}" class="btn btn-outline-secondary btn-sm">&larr; Order Creation</a>
        <a href="{{ route('docs.orders-module.visa-payment') }}" class="btn btn-outline-primary btn-sm">Visa, Payment &amp; Employer Creation &rarr;</a>
    </div>

</div>

@endsection
