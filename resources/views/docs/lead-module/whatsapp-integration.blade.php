@extends('layout.docs.docs_layout')

@section('title', 'Lead Module - WhatsApp Integration')

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
                WhatsApp Integration
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        WhatsApp Integration
    </h2>

    <p class="text-muted mb-4">
        How the CRM messages candidates and staff through the Meta WhatsApp Business API.
    </p>

    <div class="alert alert-info">
        This is separate from the Meta <strong>Conversions API</strong> (CAPI), which reports lead events back to
        Facebook Ads for campaign optimization &mdash; see <a href="{{ route('docs.meta-api') }}">Meta Conversion API</a>.
        WhatsApp Integration is about outbound messages sent <em>to</em> the candidate or staff member.
    </div>

    <!-- Where it's used -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Where WhatsApp Messages Are Sent From</h5>
        </div>

        <div class="card-body">

            <ol class="mb-0">
                <li>
                    <strong>Lead Assignment Notification</strong><br>
                    When the continuous or overnight assignment cron assigns a lead, it sends a WhatsApp template for
                    any active auto-notification configured with <code>trigger_template_type = 'lead_assign'</code>,
                    via <code>Helper::sendWhatsappAssignTemplate()</code>. See <a href="{{ route('docs.lead-assignment') }}">Lead Assignment</a>.
                </li>

                <li class="mt-3">
                    <strong>Scheduled WhatsApp Campaigns</strong><br>
                    Marketing/recruitment campaigns targeting leads are sent through the connected Meta WhatsApp
                    Business Account, personalized using the lead's <code>submit_lead_from</code> geo data and the
                    assigned staff member's ("care of") contact details &mdash; handled by
                    <code>App\Jobs\SendMetaLeadJob</code>.
                </li>

                <li class="mt-3">
                    <strong>Automation Workflows</strong><br>
                    Admin-configured WhatsApp automation rules (e.g. "message a lead X hours after assignment") run
                    against leads as part of the automation engine.
                </li>
            </ol>

        </div>

    </div>

    <!-- Cron -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Related Cron Jobs</h5>
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
                            <td><code>meta:run-scheduled-leads</code></td>
                            <td>Every minute</td>
                            <td>Processes scheduled Meta WhatsApp campaign tasks tied to leads.</td>
                        </tr>
                        <tr>
                            <td><code>automation:process-scheduled</code></td>
                            <td>Every minute</td>
                            <td>Executes WhatsApp automation rules that target leads.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.meta-api') }}" class="btn btn-outline-secondary btn-sm">&larr; Meta Conversion API</a>
        <a href="{{ route('docs.cron-jobs') }}" class="btn btn-outline-primary btn-sm">Cron Jobs &rarr;</a>
    </div>

</div>

@endsection
