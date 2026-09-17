@extends('layout.docs.docs_layout')

@section('title', 'Lead Creation')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                Lead Module
            </li>
            <li class="breadcrumb-item active">
                Lead Creation
            </li>
        </ol>
    </nav>

    <!-- Heading -->

    <div class="mb-4">

        <h2 class="fw-bold">
            Lead Creation
        </h2>

        <p class="text-muted">
            This page explains the complete Lead Creation process from form submission to database storage.
        </p>

    </div>

    <!-- Overview -->

    <div class="card mb-4">

        <div class="card-header">
            <h5>Overview</h5>
        </div>

        <div class="card-body">

            <p>

                A lead can be created from multiple sources like Website,
                Landing Pages, API, Manual Entry or any external integration.

            </p>

        </div>

    </div>

    <!-- Lead Flow -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Lead Creation Flow

            </h5>

        </div>

        <div class="card-body">

<pre>

Candidate submits form

        │

        ▼

Request Validation

        │

        ▼

Duplicate Check

        │

        ▼

Capture IP & Location

        │

        ▼

Store Lead

        │

        ▼

Auto Assignment

        │

        ▼

Send WhatsApp

        │

        ▼

Send Email

        │

        ▼

Meta Conversion API

        │

        ▼

Lead Activity Log

</pre>

        </div>

    </div>

    <!-- Validation -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Validation

            </h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Field</th>

                        <th>Description</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>Name</td>

                        <td>Candidate Name</td>

                    </tr>

                    <tr>

                        <td>Mobile Number</td>

                        <td>Unique Contact Number</td>

                    </tr>

                    <tr>

                        <td>Email</td>

                        <td>Optional</td>

                    </tr>

                    <tr>

                        <td>City</td>

                        <td>Candidate City</td>

                    </tr>

                    <tr>

                        <td>State</td>

                        <td>Candidate State</td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Business Logic -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Business Logic

            </h5>

        </div>

        <div class="card-body">

            <ul>

                <li>Validate request.</li>

                <li>Check duplicate mobile number.</li>

                <li>Capture IP address.</li>

                <li>Detect Country, State and City.</li>

                <li>Save lead.</li>

                <li>Auto assign lead.</li>

                <li>Trigger WhatsApp Notification.</li>

                <li>Trigger Email Notification.</li>

                <li>Send Meta Conversion Event.</li>

                <li>Create Lead Activity Log.</li>

            </ul>

        </div>

    </div>

    <!-- Controller -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Main Controller

            </h5>

        </div>

        <div class="card-body">

<pre>

LeadController

↓

lead_store()

↓

lead_store_new()

</pre>

        </div>

    </div>

    <!-- Related Models -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Related Models

            </h5>

        </div>

        <div class="card-body">

            <ul>

                <li>Lead.php</li>

                <li>Admin.php</li>

                <li>Leadnote.php</li>

                <li>LeadActivityLog.php</li>

            </ul>

        </div>

    </div>

    <!-- Notifications -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Notifications

            </h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Notification</th>

                        <th>Purpose</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>WhatsApp</td>

                        <td>Notify Assigned Staff</td>

                    </tr>

                    <tr>

                        <td>Email</td>

                        <td>Lead Assignment Email</td>

                    </tr>

                    <tr>

                        <td>Meta API</td>

                        <td>Facebook Conversion Tracking</td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Related Pages -->

    <div class="card">

        <div class="card-header">

            <h5>

                Continue Reading

            </h5>

        </div>

        <div class="card-body">

            <div class="list-group">

                <a href="{{ route('docs.lead.duplicate') }}"
                   class="list-group-item list-group-item-action">

                    Duplicate Lead Detection

                </a>

                <a href="{{ route('docs.lead.assignment') }}"
                   class="list-group-item list-group-item-action">

                    Lead Assignment

                </a>

                <a href="{{ route('docs.lead.followup') }}"
                   class="list-group-item list-group-item-action">

                    Lead Follow-up

                </a>

            </div>

        </div>

    </div>

</div>

@endsection