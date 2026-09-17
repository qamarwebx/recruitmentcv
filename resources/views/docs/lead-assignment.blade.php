@extends('layout.docs.docs_layout')

@section('title', 'Lead Assignment')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.lead-module') }}">Lead Module</a>
            </li>
            <li class="breadcrumb-item active">
                Lead Assignment
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Lead Assignment
    </h2>

    <p class="text-muted mb-4">
        This document explains, in detail, how leads are automatically distributed among active staff:
        who is eligible, exactly how "least loaded" is calculated, and which scheduled jobs perform the assignment.
    </p>

    <!-- Overview -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">
                Overview
            </h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Assignment simply means setting <code>leads.leadassign_id</code> to a staff member's admin id. There is
                no single "assignment engine" &mdash; instead there are <strong>two moments</strong> where it happens:
                (1) immediately when a brand-new lead is created, and (2) on a schedule, via cron jobs that sweep up any
                lead still left with an empty <code>leadassign_id</code>. Both paths use the same idea &mdash; give the
                lead to whichever eligible staff member currently has the fewest &mdash; but they measure "fewest"
                slightly differently, explained below.
            </p>

        </div>

    </div>

    <!-- Workflow -->

    <div class="card mb-4">

        <div class="card-header">

            <h5 class="mb-0">
                Workflow
            </h5>

        </div>

        <div class="card-body">

<pre>
Lead has no owner (leadassign_id = NULL)
    │
    ▼
Build list of eligible staff
  (status=1, login_status=1, lead_assign_status=1, role contains "Candidate Source")
    │
    ▼
Count each eligible staff member's current lead load
    │
    ▼
Pick the staff member with the fewest leads (ties broken randomly)
    │
    ▼
Set leadassign_id → save
    │
    ▼
Log "lead_assign_action" to the Activity Log (automatic, via the Lead model)
    │
    ▼
Send WhatsApp assignment template + assignment email
</pre>

        </div>

    </div>

    <!-- Business Rules -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Eligibility Rules

            </h5>

        </div>

        <div class="card-body">

            <p>A staff member can receive an auto-assigned lead only when <strong>all</strong> of the following are true on their <code>admins</code> record:</p>

            <ul>

                <li><code>status = 1</code> &mdash; the account is active.</li>

                <li><code>login_status = 1</code> &mdash; marked as currently logged in / available.</li>

                <li><code>lead_assign_status = 1</code> &mdash; opted in to receive leads.</li>

                <li><code>role</code> contains <strong>"Candidate Source"</strong>.</li>

            </ul>

            <div class="alert alert-warning mb-0">
                <strong>Daily reset:</strong> a scheduled job (<code>auto:autoupdateleadassignstatus</code>) turns
                <code>lead_assign_status</code> <strong>off for every admin at 09:00 every day</strong>. Staff must
                switch it back on (or a manager must enable it for them) before they can receive new auto-assigned
                leads that day. This prevents leads silently piling up on someone who is on leave or no longer active,
                without requiring anyone to remember to disable it manually.
            </div>

        </div>

    </div>

    <!-- How "least loaded" is measured -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                How "Least Loaded" Is Measured

            </h5>

        </div>

        <div class="card-body">

            <p>The exact definition of "current lead count" depends on which code path is doing the assigning:</p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Path</th>
                            <th>"Current load" counts</th>
                            <th>Tie-break</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Lead creation (<code>lead_store_new</code>)</td>
                            <td><strong>All-time</strong> total leads ever assigned to that admin</td>
                            <td>Random shuffle among eligible admins, first after sort wins</td>
                        </tr>
                        <tr>
                            <td><code>leads:assign-continuously</code> (every minute, 09:00&ndash;19:00)</td>
                            <td><strong>Today's</strong> leads only (<code>lead_date</code> = today), recalculated after each individual assignment so the count never goes stale mid-run</td>
                            <td>Lowest count wins; sorted ascending each iteration</td>
                        </tr>
                        <tr>
                            <td><code>leads:assign-pending-night</code> (daily 10:30)</td>
                            <td>Same as above &mdash; today's leads, live-recalculated per lead, processed in batches of 100</td>
                            <td>Lowest count wins</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mb-0">
                In practice this means a brand-new lead is balanced against everyone's <em>historical</em> workload,
                while the cron jobs balance against how many leads each person has received <em>today</em> &mdash; so
                daily distribution stays fair even if one staff member has a much larger all-time total.
            </p>

        </div>

    </div>

    <!-- Equal Distribution -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Equal Distribution Example

            </h5>

        </div>

        <div class="card-body">

<table class="table table-bordered">

<thead>

<tr>

<th>User</th>

<th>Current Leads</th>

</tr>

</thead>

<tbody>

<tr>

<td>Admin A</td>

<td>15</td>

</tr>

<tr>

<td>Admin B</td>

<td>12</td>

</tr>

<tr>

<td>Admin C</td>

<td>18</td>

</tr>

<tr>

<td>Admin D</td>

<td>10</td>

</tr>

</tbody>

</table>

<p class="mt-3">

The next lead will always be assigned to <strong>Admin D</strong>
because it currently has the lowest number of leads.

</p>

        </div>

    </div>

    <!-- Reassignment for repeat leads -->

    <div class="card mb-4" id="reassignment">

        <div class="card-header">

            <h5>

                Reassignment for Repeat Leads

            </h5>

        </div>

        <div class="card-body">

            <p class="mb-0">
                When a candidate submits the form again (a <strong>repeat lead</strong>, detected by matching mobile/
                WhatsApp number), the system checks whether the current owner has logged a follow-up note for that lead
                in the <strong>last 7 days</strong>. If not, the lead is reassigned to a different random <em>active</em>
                admin (<code>status = 1</code> and <code>login_status = 1</code> &mdash; note this check does
                <strong>not</strong> require <code>lead_assign_status</code>, unlike the rules above). This keeps a
                repeat candidate from being stuck with a recruiter who never actually followed up the first time.
            </p>

        </div>

    </div>

    <!-- Cron -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Related Cron Jobs

            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Command</th>
                            <th>Schedule</th>
                            <th>Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>auto:autoupdateleadassignstatus</code></td>
                            <td>Daily at 09:00</td>
                            <td>Resets every admin's <code>lead_assign_status</code> to off.</td>
                        </tr>
                        <tr>
                            <td><code>leads:assign-continuously</code></td>
                            <td>Every minute, 09:00&ndash;19:00</td>
                            <td>Assigns any unassigned lead to today's least-loaded eligible admin; sends WhatsApp + email notifications.</td>
                        </tr>
                        <tr>
                            <td><code>leads:assign-pending-night</code></td>
                            <td>Daily at 10:30</td>
                            <td>Catches leads created overnight (yesterday 19:00 &ndash; today 10:30), outside the continuous window.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-secondary mb-0">
                <strong>Disabled (kept for reference, not scheduled):</strong>
                <code>leads:reassign-unfollowed</code> &mdash; would daily reassign today's leads that have no follow-up
                note from an active "Candidate Source" admin; and <code>auto:autoleadassignstaff</code> &mdash; a legacy
                command that randomly assigns all unassigned leads based on all-time lead counts.
            </div>

        </div>

    </div>

    <!-- Code -->

    <div class="card">

        <div class="card-header">

            <h5>

                Related Files

            </h5>

        </div>

        <div class="card-body">

<ul>

<li><code>app/Console/Commands/AssignLeadsContinusallyAt10To7.php</code> &mdash; continuous assignment (09:00&ndash;19:00)</li>

<li><code>app/Console/Commands/AssignPendingNightLeads.php</code> &mdash; overnight catch-up assignment (10:30)</li>

<li><code>app/Console/Commands/Autoupdateleadassignstatus.php</code> &mdash; daily opt-in reset (09:00)</li>

<li><code>app/Console/Commands/ReassignUnfollowedLeads.php</code> &mdash; unfollowed-lead reassignment (currently disabled)</li>

<li><code>app/Console/Commands/Autoleadassignstaff.php</code> &mdash; legacy random assignment (currently disabled)</li>

<li><code>app/Http/Controllers/LeadController.php</code> &mdash; create-time assignment (<code>lead_store_new</code>), repeat-lead reassignment (<code>leadAssignedProcess</code>), staff opt-in toggle (<code>userleadassignstatus</code>, <code>deactiveassigneelead</code>)</li>

<li><code>app/Models/Lead.php</code> &mdash; assignment relation and automatic activity logging on <code>leadassign_id</code> change</li>

<li><code>app/Models/Admin.php</code> &mdash; <code>status</code>, <code>login_status</code>, <code>lead_assign_status</code>, <code>role</code> fields used for eligibility</li>

<li><code>app/Helpers/Helper.php</code> &mdash; <code>leadActivityLog()</code>, WhatsApp/email notification senders</li>

</ul>

        </div>

    </div>

</div>

@endsection