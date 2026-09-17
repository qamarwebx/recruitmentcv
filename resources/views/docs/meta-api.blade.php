@extends('layout.docs.docs_layout')

@section('title','Meta Conversion API')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->

    <nav aria-label="breadcrumb" class="mb-3">

        <ol class="breadcrumb">

            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">
                    Documentation
                </a>
            </li>

            <li class="breadcrumb-item active">

                Meta Conversion API

            </li>

        </ol>

    </nav>

    <!-- Heading -->

    <div class="mb-4">

        <h2 class="fw-bold">

            Meta Conversion API

        </h2>

        <p class="text-muted">

            This document explains how Meta Conversion API is integrated
            with the CRM for Lead Tracking and Optimization.

        </p>

    </div>

    <!-- Overview -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Overview

            </h5>

        </div>

        <div class="card-body">

            <p>

                Every Lead event is sent to Meta using the Conversion API
                so Facebook Ads can optimize campaign performance.

            </p>

        </div>

    </div>

    <!-- Workflow -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Workflow

            </h5>

        </div>

        <div class="card-body">

<pre>
Candidate submits form
        │
        ▼
Lead Created
        │
        ▼
CRM Processes Lead
        │
        ▼
Build Payload
        │
        ▼
Hash User Data
        │
        ▼
Send Event
        │
        ▼
Receive Response
        │
        ▼
Save Activity Log
</pre>

        </div>

    </div>

    <!-- Events -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Supported Events

            </h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>CRM Status</th>

                        <th>Meta Event</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>Lead Created</td>

                        <td>Lead</td>

                    </tr>

                    <tr>

                        <td>Followed Up</td>

                        <td>LeadContacted</td>

                    </tr>

                    <tr>

                        <td>Call Not Connected</td>

                        <td>CallNotConnected</td>

                    </tr>

                    <tr>

                        <td>Qualified</td>

                        <td>QualifiedLead</td>

                    </tr>

                    <tr>

                        <td>Not Qualified</td>

                        <td>LeadNotQualified</td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Payload -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Payload Structure

            </h5>

        </div>

        <div class="card-body">

<pre>{
    "event_name": "...",
    "event_time": "...",
    "event_id": "...",

    "user_data": {
        "ph": "...",
        "em": "...",
        "fn": "...",
        "ln": "..."
    },

    "custom_data": {

        "lead_id": "...",

        "lead_status": "...",

        "platform": "website"

    }

}</pre>

        </div>

    </div>

    <!-- Security -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Security

            </h5>

        </div>

        <div class="card-body">

            <ul>

                <li>Phone Number SHA256 Hash</li>

                <li>Email SHA256 Hash</li>

                <li>Name SHA256 Hash</li>

                <li>External ID Hash</li>

            </ul>

        </div>

    </div>

    <!-- Logs -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Activity Logs

            </h5>

        </div>

        <div class="card-body">

            <ul>

                <li>Request Payload</li>

                <li>Response Body</li>

                <li>Error Response</li>

                <li>Lead Activity</li>

            </ul>

        </div>

    </div>

    <!-- Files -->

    <div class="card">

        <div class="card-header">

            <h5>

                Related Files

            </h5>

        </div>

        <div class="card-body">

            <ul>

                <li>MetaConversionService.php</li>

                <li>LeadController.php</li>

                <li>LeadActivityLog.php</li>

                <li>config/services.php</li>

            </ul>

        </div>

    </div>

</div>

@endsection