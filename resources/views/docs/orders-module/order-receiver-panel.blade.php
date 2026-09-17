@extends('layout.docs.docs_layout')

@section('title', 'Orders Module - Order Receiver Panel')

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
                Order Receiver Panel
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Order Receiver Panel
    </h2>

    <div class="alert alert-info">
        This feature is <strong>hidden from the sidebar</strong> (its menu entry is commented out in
        <code>admin_sidebar.blade.php</code>) but the routes, controller, and the logic that consumes it in order
        creation are all fully live. Don't assume "not in the menu" means "not running."
    </div>

    <!-- What it is -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">What It Does</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Lets an admin designate specific staff members as "order receivers" — a round-robin distribution
                list, stored in <code>orderrecivepanels</code> (<code>staff_id</code>, <code>email</code>,
                <code>mobile</code>, <code>status</code>, <code>assignby_id</code>). Managed at
                <code>admin.order.received.panel</code> (<code>BackEndController::orderrep</code>).
            </p>

        </div>

    </div>

    <!-- How it's consumed -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">How New Orders Get Load-Balanced</h5>
        </div>

        <div class="card-body">

            <p>
                Consumed directly inside <code>BookingController::store()</code> (see
                <a href="{{ route('docs.orders-module.creation') }}">Order Creation</a>): if
                <code>Websiteconfig.order_receieved</code> is enabled, every new order:
            </p>

            <ol class="mb-0">
                <li>Fetches all active <code>orderrecivepanels</code> rows.</li>
                <li>Compares each staff member's current count of <code>bookings.orderrecuser_id</code> (i.e. how many orders they already have).</li>
                <li>Assigns the new order to whichever active receiver currently has the fewest — the same "least-loaded" idea used by <a href="{{ route('docs.lead-assignment') }}">Lead Assignment</a>, applied here to incoming orders instead of leads.</li>
                <li>Stamps the winning staff id onto <code>bookings.orderrecuser_id</code>.</li>
            </ol>

        </div>

    </div>

    <!-- Routes -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Routes</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li><code>GET order-received-panel</code> &rarr; <code>admin.order.received.panel</code> (list)</li>
                <li><code>POST order-received-panel/json</code> (DataTables source, unnamed)</li>
                <li><code>POST order-received-panel/check</code> (unnamed)</li>
                <li><code>POST order-received-panel/update</code> &rarr; <code>admin.order.received.panel.update</code></li>
            </ul>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.orders-module.employer-assignment') }}" class="btn btn-outline-secondary btn-sm">&larr; Employer &amp; Candidate Assignment</a>
        <a href="{{ route('docs.orders-module.requirement') }}" class="btn btn-outline-primary btn-sm">Requirement Snippets &rarr;</a>
    </div>

</div>

@endsection
