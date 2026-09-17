@extends('layout.docs.docs_layout')

@section('title', 'Orders Module - Order Creation')

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
                Order Creation
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Order Creation
    </h2>

    <p class="text-muted mb-4">
        Orders are placed from the <strong>public/customer-facing side</strong>, not the admin panel — a logged-in
        customer clicks "book" on a candidate's public profile.
    </p>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The Flow &mdash; <code>store()</code></h5>
        </div>

        <div class="card-body">

            <p>
                Route <code>POST resumes/details/booking</code>, under the <code>user</code> auth guard (a
                customer/employer front-end login, not admin). Handled by
                <code>BookingController::store()</code>:
            </p>

            <ol class="mb-0">
                <li>
                    <strong>Generate a reference number</strong> &mdash; the latest booking's <code>reference_no</code> + 1, seeded at 800.
                </li>

                <li class="mt-3">
                    <strong>Round-robin assignment (conditional)</strong> &mdash; if <code>Websiteconfig.order_receieved</code> is enabled, the new order is load-balanced across active Order Receiver Panel staff. See <a href="{{ route('docs.orders-module.receiver-panel') }}">Order Receiver Panel</a>.
                </li>

                <li class="mt-3">
                    <strong>Create the booking</strong> &mdash; <code>cand_id</code>, <code>user_id = Auth::user()-&gt;id</code>, <code>hoi</code>, <code>reference_no</code>, <code>booking_date = now()</code>, <code>worklocation</code>, <code>embassy_id</code>.
                </li>

                <li class="mt-3">
                    <strong>Enforce the booking limit</strong> &mdash; compares the candidate's active-booking count against <code>Websiteconfig.cand_booking_limit</code> (currently <strong>5</strong>). If reached, <code>candidates.status</code> is flipped to <code>false</code>, removing the candidate from further bookability (this can be overridden per candidate via <code>candidatebookinglimits</code>).
                </li>

                <li class="mt-3">
                    <strong>Log activity</strong> &mdash; an <code>Activity</code> timeline entry: "Candidate booked by …".
                </li>

                <li class="mt-3">
                    <strong>Notify</strong> &mdash; a WhatsApp "Order Confirmation" message is sent synchronously via a direct cURL call. See <a href="{{ route('docs.orders-module.cancellation-notifications') }}">Cancellation &amp; Notifications</a> for why this isn't queued despite queue jobs existing in the code.
                </li>

                <li class="mt-3">
                    <strong>Respond</strong> &mdash; a JSON "thank you, your order number is …" message.
                </li>
            </ol>

        </div>

    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Variants</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>store2</code>/<code>store3</code>/<code>store4</code> and Arabic equivalents
                (<code>storear</code>&ndash;<code>storear4</code>) exist for slightly different booking-flow steps
                (office-sourced vs. partner-sourced booking, different language) — all follow the same pattern
                against the same <code>bookings</code> table. A parallel <code>PartnerBookingController</code>
                serves the equivalent flow under the partner portal (<code>partner.booking.*</code> routes).
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.orders-module.database') }}" class="btn btn-outline-secondary btn-sm">&larr; Database Structure</a>
        <a href="{{ route('docs.orders-module.status') }}" class="btn btn-outline-primary btn-sm">Order Status &amp; Lifecycle &rarr;</a>
    </div>

</div>

@endsection
