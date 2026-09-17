@extends('layout.docs.docs_layout')

@section('title', 'Orders Module - Cancellation & Notifications')

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
                Cancellation &amp; Notifications
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Cancellation &amp; Notifications
    </h2>

    <p class="text-muted mb-4">
        How an order gets cancelled or its candidate replaced, how customers verify their identity, and why the
        scheduled queue worker for order notifications currently has nothing to process.
    </p>

    <!-- Cancel -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Cancellation &mdash; <code>cancel()</code></h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Sets <code>booking_status = 2</code>, <code>status = true</code>,
                <code>booking_cancelled_by = 'Admin'</code>, <code>booking_cancelled_at = now()</code>. Releases the
                candidate back to publishable/<code>status = true</code> if they're still under the booking limit.
                Sends a WhatsApp cancellation notice via the Meta Cloud API (falling back to the normal WhatsApp API
                on failure), logging to <code>Metawhatsapplog</code>/<code>Normalwhatsapplogs</code>.
            </p>

        </div>

    </div>

    <!-- Replace -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Candidate Replacement &mdash; <code>replacecandidate()</code></h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Swaps the candidate on an existing booking: logs the swap to <code>replacebookingcands</code>
                (old <code>cand_id</code>, new <code>repcand_id</code>, who did it), and updates
                <code>bookingpayments.cand_id</code> and <code>visadetails.cand_id</code> to keep those related
                records pointed at the new candidate.
            </p>

        </div>

    </div>

    <!-- OTP -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">OTP Verification</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>sendOTP()</code>/<code>checkOTP()</code>/<code>confirmOTP()</code> issue and verify one-time
                codes tied to <code>bookingnotifications</code> (<code>email_code</code>,
                <code>whatsapp_code</code>, <code>message_code</code>, <code>code_expired</code>) — used to confirm
                a customer's identity at key points in the booking flow.
            </p>

        </div>

    </div>

    <!-- Vestigial queue -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">The Vestigial Notification Queue</h5>
        </div>

        <div class="card-body">

            <p>
                <code>Kernel.php</code> schedules a generic queue worker every minute:
            </p>

<pre><code>queue:work --queue=orderSend,orderCancel,default --stop-when-empty</code></pre>

            <p>
                These <code>orderSend</code>/<code>orderCancel</code> queues were designed to carry
                <code>App\Jobs\SendBookingWelcome</code>, <code>SendBookingArWelcome</code>, and
                <code>App\Jobs\CancelBookingOrder</code> — an asynchronous notification design.
            </p>

            <div class="alert alert-warning mb-0">
                <strong>But every dispatch of those jobs is commented out</strong> in the live
                <code>BookingController.php</code> (<code>store()</code> and <code>cancel()</code>). The actual
                notification logic was replaced with synchronous inline <code>curl_exec()</code> calls to the
                WhatsApp/Meta APIs instead. This means the scheduled queue worker above currently has
                <strong>no live producer feeding it for Orders</strong> — it's a vestigial schedule entry left over
                from an earlier async-notification implementation, still running every minute for no purpose from
                this module.
            </div>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.orders-module.requirement') }}" class="btn btn-outline-secondary btn-sm">&larr; Requirement Snippets</a>
        <a href="{{ route('docs.orders-module') }}" class="btn btn-outline-primary btn-sm">Back to Overview &rarr;</a>
    </div>

</div>

@endsection
