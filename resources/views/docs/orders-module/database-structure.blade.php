@extends('layout.docs.docs_layout')

@section('title', 'Orders Module - Database Structure')

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
                Database Structure
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Database Structure
    </h2>

    <p class="text-muted mb-4">
        Sidebar label "Orders" maps to routes <code>admin.booking.*</code>, model
        <code>App\Models\Booking</code>, table <code>bookings</code>.
    </p>

    <div class="alert alert-info">
        An "order" here is a customer placing a booking request for a specific candidate &mdash; conceptually
        distinct from <a href="{{ route('docs.deal-pipeline-module') }}">Deal Pipeline</a> (a sales/BD pipeline) and
        from the standalone Employer module (manual employer/vacancy management). See
        <a href="{{ route('docs.orders-module.visa-payment') }}">Visa, Payment &amp; Employer Creation</a> for how
        these actually connect.
    </div>

    <!-- Identity -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Identity &amp; Parties</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>user_id</code></td><td>The ordering customer &mdash; a row in the <code>users</code> table (a front-end customer login), <strong>not</strong> the <code>employers</code> table and not <code>admins</code>.</td></tr>
                        <tr><td><code>cand_id</code></td><td>The candidate being booked.</td></tr>
                        <tr><td><code>partner_id</code></td><td>Recruitment partner/agency office, if the order came through the partner portal.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Descriptive -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Reference &amp; Descriptive</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>reference_no</code></td><td>Order number, generated incrementally starting at 800 (e.g. BK801, BK802…).</td></tr>
                        <tr><td><code>hoi</code></td><td>"House of…" / household info field passed from the store form.</td></tr>
                        <tr><td><code>amount</code></td><td>Free-text amount field &mdash; legacy/unused in the current flow. The real amount lives in <code>bookingpayments</code>.</td></tr>
                        <tr><td><code>worklocation</code></td><td>FK-by-convention to <code>expecworkcities.id</code>.</td></tr>
                        <tr><td><code>embassy_id</code></td><td>Embassy reference.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Status Tracking</h5></div>
        <div class="card-body">
            <p class="text-muted">All plain ints &mdash; no DB-level enum or FK constraint on any of these.</p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>booking_status</code></td><td>0 = new/pending, 1 = confirmed/complete (set once visa + payment are both done), 2 = cancelled.</td></tr>
                        <tr><td><code>status</code></td><td>Boolean-ish "closed" flag &mdash; true once the order is finalized or cancelled.</td></tr>
                        <tr><td><code>visa_status</code></td><td>Becomes 1 once visa/employer details are entered.</td></tr>
                        <tr><td><code>payment_status</code></td><td>Becomes 1 once a payment is recorded.</td></tr>
                        <tr><td><code>ord_status_id</code></td><td>FK-by-convention to <code>order_statuses.id</code>. See <a href="{{ route('docs.orders-module.status') }}">Order Status &amp; Lifecycle</a>.</td></tr>
                        <tr><td><code>booking_cancelled_by</code>, <code>booking_cancelled_at</code></td><td>Cancellation audit.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Confirmation -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Confirmation &amp; Audit</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>payconfirm_admin_id</code>, <code>payconfirm_partner_id</code></td><td>Who confirmed payment.</td></tr>
                        <tr><td><code>partner_orderconfirmby_id</code>, <code>admin_orderconfirmby_id</code>, <code>partner_order_confirm_date</code></td><td>Order confirmation audit.</td></tr>
                        <tr><td><code>orderrecuser_id</code></td><td>The staff member auto-assigned via the Order Receiver Panel round-robin. See <a href="{{ route('docs.orders-module.receiver-panel') }}">Order Receiver Panel</a>.</td></tr>
                        <tr><td><code>created_at</code>, <code>updated_at</code></td><td>Standard timestamps.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Related tables -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Related Tables</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Table</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>bookingpayments</code></td><td>Payment record(s) for a booking.</td></tr>
                        <tr><td><code>bookingorderstatuses</code></td><td>History/audit of order-status changes.</td></tr>
                        <tr><td><code>visadetails</code></td><td>Visa/employer contract details attached to a booking. See <a href="{{ route('docs.orders-module.visa-payment') }}">Visa, Payment &amp; Employer Creation</a>.</td></tr>
                        <tr><td><code>employers</code></td><td>Auto-materialized from a booking's visa step &mdash; a downstream artifact of an order, not a pre-existing record the order belongs to.</td></tr>
                        <tr><td><code>replacebookingcands</code></td><td>Log of candidate-swap events on a booking.</td></tr>
                        <tr><td><code>bookingnotifications</code></td><td>OTP/verification codes sent for a booking.</td></tr>
                        <tr><td><code>candidatebookinglimits</code></td><td>Per-candidate override of the global booking limit.</td></tr>
                        <tr><td><code>bookingfilters</code>, <code>partnerbookingfilters</code></td><td>Per-staff / per-partner saved UI filter toggles for the Orders list.</td></tr>
                        <tr><td><code>order_statuses</code></td><td>The status master list. See <a href="{{ route('docs.orders-module.status') }}">Order Status &amp; Lifecycle</a>.</td></tr>
                        <tr><td><code>orderrecivepanels</code></td><td>Staff assigned to receive new orders round-robin.</td></tr>
                        <tr><td><code>requirement_info</code></td><td>Bilingual requirement-text snippets. See <a href="{{ route('docs.orders-module.requirement') }}">Requirement Snippets</a>.</td></tr>
                        <tr><td><code>employercandidates</code></td><td>Candidate &harr; employer assignment ledger, shared with a separate module. See <a href="{{ route('docs.orders-module.employer-assignment') }}">Employer &amp; Candidate Assignment</a>.</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="alert alert-secondary mt-3 mb-0">
                A structurally identical but <strong>unused, dead-code duplicate</strong> table
                <code>orderstatus</code> (singular) also exists &mdash; no model or controller references it.
            </div>
        </div>
    </div>

    <!-- Model -->
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Model &mdash; <code>app/Models/Booking.php</code></h5></div>
        <div class="card-body">
            <p class="mb-0">
                Very thin: <code>protected $guarded = [];</code>, no casts, no scopes, no model events. Only two
                relationships are defined &mdash; <code>partner()</code> and <code>workLocation()</code>
                (&rarr; <code>Expecworkcity</code> via <code>worklocation</code>). Everything else (candidate,
                user, city, profession, cancelling admin) is joined manually via raw <code>DB::table()</code> query
                builder in the controller rather than Eloquent relations — there's no <code>hasMany</code> back to
                <code>bookingpayments</code>, <code>visadetails</code>, or <code>bookingorderstatuses</code> either.
            </p>
        </div>
    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.orders-module') }}" class="btn btn-outline-secondary btn-sm">&larr; Overview</a>
        <a href="{{ route('docs.orders-module.creation') }}" class="btn btn-outline-primary btn-sm">Order Creation &rarr;</a>
    </div>

</div>

@endsection
