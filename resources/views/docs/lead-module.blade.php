@extends('layout.docs.docs_layout')

@section('title', 'Lead Module')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <div class="mb-3">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('docs.index') }}">
                        Documentation
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    Lead Module
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-4">

        <h2 class="fw-bold">
            Lead Module
        </h2>

        <p class="text-muted">

            This document explains the complete Lead Management module including
            Lead Creation,
            Assignment,
            Follow-up,
            Qualification,
            Activity Logs,
            Filters,
            Export,
            APIs,
            Cron Jobs and Business Logic.

        </p>

    </div>

    <div class="row">

        <!-- Left Menu -->

        <div class="col-lg-3">

            <div class="card docs-toc">

                <div class="card-header">

                    <strong>Contents</strong>

                </div>

                <div class="list-group list-group-flush">

                    <a href="#overview" class="list-group-item">
                        1. Overview
                    </a>

                    <a href="{{ route('docs.lead-module.database') }}" class="list-group-item">
                        2. Database Structure
                    </a>

                    <a href="{{ route('docs.lead-module.creation') }}" class="list-group-item">
                        3. Lead Creation
                    </a>

                    <a href="{{ route('docs.lead-module.duplicate') }}" class="list-group-item">
                        4. Duplicate Check
                    </a>

                    <a href="#assignment" class="list-group-item">
                        5. Lead Assignment
                    </a>

                    <a href="{{ route('docs.lead-module.followup') }}" class="list-group-item">
                        6. Follow-up
                    </a>

                    <a href="{{ route('docs.lead-module.notes') }}" class="list-group-item">
                        7. Lead Notes
                    </a>

                    <a href="{{ route('docs.lead-module.qualification') }}" class="list-group-item">
                        8. Qualification
                    </a>

                    <a href="{{ route('docs.lead-module.activity') }}" class="list-group-item">
                        9. Activity Logs
                    </a>

                    <a href="#cron" class="list-group-item">
                        10. Cron Jobs
                    </a>

                    <a href="{{ route('docs.meta-api') }}" class="list-group-item">
                        11. Meta Conversion API
                    </a>

                    <a href="{{ route('docs.lead-module.whatsapp') }}" class="list-group-item">
                        12. WhatsApp Integration
                    </a>

                </div>

            </div>

        </div>

        <!-- Right Content -->

        <div class="col-lg-9">

            <!-- Overview -->

            <div class="card mb-4" id="overview">

                <div class="card-header">

                    <h5 class="mb-0">

                        Overview

                    </h5>

                </div>

                <div class="card-body">

                    <p>
                        The <strong>Lead Module</strong> is the entry point of the recruitment pipeline. A "lead" is a candidate
                        who has shown interest through the public website (<code>www.qamrjob.com</code>) by submitting a form with
                        their contact details and the job/service they are looking for. Every lead flows through the same
                        lifecycle:
                    </p>

                    <p>
                        <strong>Submitted</strong> → <strong>Saved &amp; enriched</strong> (IP/location, duplicate flag) →
                        <strong>Auto&#8209;assigned</strong> to a recruiter → <strong>Followed up</strong> (notes/calls) →
                        <strong>Qualified</strong> (Not Yet, Followed Up, Call Not Connected, Qualified, Not Qualified, Not Relevant)
                        → optionally <strong>converted into a Deal</strong> once marked "Lead Qualified".
                    </p>

                    <p class="mb-0">
                        All of this activity is captured automatically in the lead's <strong>Activity Log</strong>, so an
                        administrator can always answer "who touched this lead, and when?" without relying on staff memory.
                        Behind the scenes, a set of scheduled cron jobs keep unassigned leads moving to available staff even
                        when nobody is actively watching the CRM.
                    </p>

                </div>

            </div>

            <!-- Database -->

            <div class="card mb-4" id="database">

                <div class="card-header">

                    <h5 class="mb-0">

                        Database Structure

                    </h5>

                </div>

                <div class="card-body">

                    <p class="mb-0">
                        Every lead is a row in the <code>leads</code> table, plus a handful of related tables
                        (<code>leadnotes</code>, <code>lead_activity_logs</code>, <code>meta_lead_logs</code>, and
                        more). The full column-by-column reference now lives on its own page:
                        <a href="{{ route('docs.lead-module.database') }}">Read the Database Structure guide &rarr;</a>
                    </p>

                </div>

            </div>

            <!-- Lead Creation -->

            <div class="card mb-4" id="leadcreation">

                <div class="card-header">

                    <h5>

                        Lead Creation

                    </h5>

                </div>
                <div class="card-body">

                    <p class="mb-0">
                        Leads are created from the public website form, which posts to
                        <code>POST /api/leads/store</code>. The step-by-step creation flow, plus how duplicate/repeat
                        leads are detected and flagged, now live on their own pages:
                    </p>

                    <ul class="mt-2 mb-0">
                        <li><a href="{{ route('docs.lead-module.creation') }}">Lead Creation flow &rarr;</a></li>
                        <li><a href="{{ route('docs.lead-module.duplicate') }}">Duplicate Check &rarr;</a></li>
                    </ul>

                </div>
            </div>

            <!-- Assignment -->

            <div class="card mb-4" id="assignment">

                <div class="card-header">

                    <h5>

                        Lead Assignment

                    </h5>

                </div>

                <div class="card-body">

                    <p>
                        A lead becomes "assigned" the moment <code>leadassign_id</code> is set on its row. Assignment can
                        happen in two ways: immediately at creation time (for a brand new, non-repeat lead), or later via
                        one of three scheduled cron jobs that sweep up any lead still sitting with
                        <code>leadassign_id IS NULL</code>.
                    </p>

                    <div class="alert alert-primary">
                        <strong>Who Is Eligible</strong>
                    </div>

                    <p>A staff member (Admin) can receive an auto-assigned lead only when <strong>all</strong> of these are true:</p>

                    <ul>
                        <li><code>status = 1</code> &mdash; the account is active.</li>
                        <li><code>login_status = 1</code> &mdash; marked as currently available/logged in.</li>
                        <li><code>lead_assign_status = 1</code> &mdash; opted in to receive leads. Every admin is reset to
                            <code>0</code> automatically at <strong>09:00 daily</strong>, so staff (or a manager on their
                            behalf) must switch it back on each day.</li>
                        <li><code>role</code> contains <strong>"Candidate Source"</strong>.</li>
                    </ul>

                    <div class="alert alert-primary">
                        <strong>Lead Assignment Flow</strong>
                    </div>

                    <ol class="mb-0">
                        <li>
                            <strong>Lead Ready for Assignment</strong><br>
                            A lead qualifies for assignment the moment it's created (if not a repeat) or as soon as it's found with an empty <code>leadassign_id</code> by one of the cron jobs.
                        </li>

                        <li class="mt-3">
                            <strong>Identify Eligible Staff</strong><br>
                            The system builds the list of admins matching the eligibility rules above.
                        </li>

                        <li class="mt-3">
                            <strong>Find the Least-Loaded Staff Member</strong><br>
                            Each eligible admin's current lead count is compared, and the lead is given to whichever one has the fewest. Ties are broken randomly. See <a href="{{ route('docs.lead-assignment') }}">Lead Assignment</a> for how "current lead count" differs slightly between the create-time logic and the cron-based logic.
                        </li>

                        <li class="mt-3">
                            <strong>Assign Lead</strong><br>
                            <code>leadassign_id</code> is set to the selected admin's id and saved.
                        </li>

                        <li class="mt-3">
                            <strong>Create Assignment Activity</strong><br>
                            The <code>Lead</code> model automatically logs a <code>lead_assign_action</code> entry to the Activity Log whenever <code>leadassign_id</code> changes, recording the old and new owner.
                        </li>

                        <li class="mt-3">
                            <strong>Notify Assigned Staff</strong><br>
                            The continuous and night crons send a WhatsApp template (for any active auto-notification configured with <code>trigger_template_type = 'lead_assign'</code>) and an assignment email to the newly assigned staff member.
                        </li>

                        <li class="mt-3">
                            <strong>Lead Available for Follow-up</strong><br>
                            The assigned staff member can now view the lead, add notes, update the qualification status, and continue the recruitment process.
                        </li>
                    </ol>

                    <p class="mt-3 mb-0">
                        For the full breakdown of every assignment cron (schedules, exact queries, and edge cases like
                        overnight leads and reassignment of unfollowed leads), see the dedicated
                        <a href="{{ route('docs.lead-assignment') }}">Lead Assignment</a> page.
                    </p>

                </div>
            </div>

            <!-- Followup -->

            <div class="card mb-4" id="followup">

                <div class="card-header">

                    <h5>

                        Follow-up

                    </h5>

                </div>

                <div class="card-body">

                    <p class="mb-0">
                        Every follow-up interaction is saved as a note against the lead, and the system tracks
                        <em>recency</em> of contact to surface leads that are going cold. Full detail, plus how the
                        underlying <code>leadnotes</code> table works, now live on their own pages:
                    </p>

                    <ul class="mt-2 mb-0">
                        <li><a href="{{ route('docs.lead-module.followup') }}">Follow-up flow &rarr;</a></li>
                        <li><a href="{{ route('docs.lead-module.notes') }}">Lead Notes &rarr;</a></li>
                    </ul>

                </div>
            </div>

            <!-- Qualification -->

            <div class="card mb-4" id="qualification">

                <div class="card-header">

                    <h5>

                        Qualification

                    </h5>

                </div>

                <div class="card-body">

                    <p class="mb-0">
                        Qualification is stored as a single numeric code (1&ndash;5) on the lead, updated by the
                        recruiter after contacting the candidate. It can also trigger an automatic Deal creation.
                        Full statuses table and flow:
                        <a href="{{ route('docs.lead-module.qualification') }}">Read the Qualification guide &rarr;</a>
                    </p>

                </div>

            </div>

            <!-- Activity -->

            <div class="card mb-4" id="activity">

                <div class="card-header">

                    <h5>

                        Activity Logs

                    </h5>

                </div>

                <div class="card-body">

                    <p class="mb-0">
                        Every meaningful change to a lead (assignment, qualification, Meta API sends) is written
                        automatically to the <code>lead_activity_logs</code> table, giving a reliable audit trail.
                        <a href="{{ route('docs.lead-module.activity') }}">Read the Activity Logs guide &rarr;</a>
                    </p>

                </div>

            </div>

            <!-- Cron -->

            <div class="card mb-4" id="cron">

                <div class="card-header">

                    <h5>

                        Cron Jobs

                    </h5>

                </div>

                <div class="card-body">

                    <p>
                        These are the scheduled commands (defined in <code>app/Console/Kernel.php</code>) that keep the
                        Lead module moving without manual intervention. Times are server time.
                    </p>

                    <div class="alert alert-primary">
                        <strong>Active Lead Cron Jobs</strong>
                    </div>

                    <ol class="mb-0">

                        <li>
                            <strong>Reset Daily Opt-in &mdash; <code>auto:autoupdateleadassignstatus</code></strong><br>
                            Runs once at <strong>09:00</strong>. Turns <code>lead_assign_status</code> off for every admin, so staff must re-enable "receive leads" each day before they can be auto-assigned new work.
                        </li>

                        <li class="mt-3">
                            <strong>Continuous Assignment &mdash; <code>leads:assign-continuously</code></strong><br>
                            Runs <strong>every minute between 09:00 and 19:00</strong>. Finds every lead with no owner and assigns each one, one at a time, to whichever eligible admin currently has the fewest leads <em>for today</em>, rebalancing after each assignment. Also sends the WhatsApp/email assignment notification.
                        </li>

                        <li class="mt-3">
                            <strong>Overnight Catch-up &mdash; <code>leads:assign-pending-night</code></strong><br>
                            Runs once daily at <strong>10:30</strong>. Assigns any lead created between yesterday 19:00 and today 10:30 that the continuous cron didn't catch (because it only runs during the 09:00&ndash;19:00 window).
                        </li>

                        <li class="mt-3">
                            <strong>All-Contacts Sync &mdash; <code>sync:allcontact-to-lead</code></strong><br>
                            Runs once daily. Imports/syncs records from the All Contacts module into the Leads module.
                        </li>

                        <li class="mt-3">
                            <strong>Meta Scheduled Sends &mdash; <code>meta:run-scheduled-leads</code></strong><br>
                            Runs <strong>every minute</strong>. Processes any scheduled Meta WhatsApp campaign tasks tied to leads.
                        </li>

                        <li class="mt-3">
                            <strong>Automation Workflows</strong><br>
                            <code>automation:process-scheduled</code> and <code>automation:process-email-scheduled</code> run <strong>every minute</strong>, executing any WhatsApp/email automation rules that target leads.
                        </li>

                        <li class="mt-3">
                            <strong>Reschedule Reminders &mdash; <code>reminders:send-reschedule</code></strong><br>
                            Runs <strong>every minute</strong>, sending reminders for rescheduled follow-up tasks.
                        </li>

                    </ol>

                    <div class="alert alert-secondary mt-4 mb-0">
                        <strong>Currently disabled:</strong> two commands exist in the codebase but are commented out
                        in the scheduler, so they do <strong>not</strong> run &mdash;
                        <code>leads:reassign-unfollowed</code> (would reassign today's leads with no note from an active
                        admin) and <code>auto:autoleadassignstaff</code> (a legacy random-assignment command). They're
                        left in place for reference but have no effect unless re-enabled.
                    </div>

                </div>

            </div>

            <!-- Meta -->

            <div class="card mb-4" id="meta">

                <div class="card-header">

                    <h5>

                        Meta Integration

                    </h5>

                </div>

                <div class="card-body">

                    <p>
                        <strong>Important:</strong> leads are <em>not</em> imported from Facebook/Meta Lead Ads forms
                        &mdash; there is no inbound Meta webhook. Every lead in this system comes from the public website
                        form. "Meta" here refers to two <strong>outbound</strong> integrations:
                    </p>

                    <ul class="mb-0">
                        <li><a href="{{ route('docs.meta-api') }}">Meta Conversion API &rarr;</a> &mdash; reports lead/qualification events back to Facebook Ads for campaign optimization.</li>
                        <li><a href="{{ route('docs.lead-module.whatsapp') }}">WhatsApp Integration &rarr;</a> &mdash; sends templated WhatsApp messages (assignment notifications, campaigns) to candidates and staff.</li>
                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection