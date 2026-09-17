@extends('layout.docs.docs_layout')

@section('title', 'Cron Jobs')

@section('content')

<div class="container-fluid">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>

            <li class="breadcrumb-item active">
                Cron Jobs
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">

        Cron Jobs

    </h2>

    <p class="text-muted mb-4">

        This page documents all scheduled background tasks used in the CRM.

    </p>

    <!-- Timeline -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>Cron Timeline</h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Time</th>

                        <th>Command</th>

                        <th>Purpose</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>10:30 AM - 07:00 PM</td>

                        <td>leads:assign-unassigned</td>

                        <td>Assign all new unassigned leads.</td>

                    </tr>

                    <tr>

                        <td>02:30 PM</td>

                        <td>leads:reassign-unfollowed</td>

                        <td>Reassign today's unfollowed leads.</td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Every Minute Cron -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Lead Assignment Cron

            </h5>

        </div>

        <div class="card-body">

            <h6>Command</h6>

<pre>
php artisan leads:assign-unassigned
</pre>

            <h6 class="mt-4">

                Process

            </h6>

<pre>
Fetch Active Staff
        │
        ▼
Find Unassigned Leads
        │
        ▼
Count Assigned Leads
        │
        ▼
Least Loaded Staff
        │
        ▼
Assign Lead
        │
        ▼
WhatsApp
        │
        ▼
Email
        │
        ▼
Activity Log
</pre>

        </div>

    </div>

    <!-- Reassignment -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Lead Reassignment Cron

            </h5>

        </div>

        <div class="card-body">

            <h6>

                Command

            </h6>

<pre>
php artisan leads:reassign-unfollowed
</pre>

            <h6 class="mt-4">

                Workflow

            </h6>

<pre>
Today's Leads
        │
        ▼
Find Leads Without Follow-up
        │
        ▼
Find Active Staff
        │
        ▼
Calculate Lead Count
        │
        ▼
Equal Redistribution
        │
        ▼
Notification
</pre>

        </div>

    </div>

    <!-- Scheduler -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Laravel Scheduler

            </h5>

        </div>

        <div class="card-body">

<pre>
* * * * * php artisan schedule:run
</pre>

            <p class="mt-3">

                Laravel Scheduler executes all scheduled commands automatically.

            </p>

        </div>

    </div>

    <!-- Logs -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Log Channels

            </h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Channel</th>

                        <th>Description</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>lead_assign_10_to_7</td>

                        <td>Lead assignment logs.</td>

                    </tr>

                    <tr>

                        <td>lead_followup_reassign</td>

                        <td>Lead reassignment logs.</td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Related Files -->

    <div class="card">

        <div class="card-header">

            <h5>

                Related Files

            </h5>

        </div>

        <div class="card-body">

            <ul>

                <li>app/Console/Kernel.php</li>

                <li>AssignUnassignedLeads.php</li>

                <li>ReassignUnfollowedLeads.php</li>

                <li>config/logging.php</li>

            </ul>

        </div>

    </div>

</div>

@endsection