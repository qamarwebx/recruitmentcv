@extends('layout.docs.docs_layout')

@section('title', 'Lead Module Overview')

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

            <li class="breadcrumb-item">
                Lead Module
            </li>

            <li class="breadcrumb-item active">
                Overview
            </li>

        </ol>

    </nav>

    <!-- Page Header -->

    <div class="mb-4">

        <h2 class="fw-bold">

            Lead Module Overview

        </h2>

        <p class="text-muted">

            The Lead Module is the core component of the CRM.
            It manages the complete lifecycle of a candidate lead,
            from lead creation to final placement.

        </p>

    </div>

    <!-- Overview -->

    <div class="card mb-4">

        <div class="card-header">

            <h5 class="mb-0">

                Overview

            </h5>

        </div>

        <div class="card-body">

            <p>

                Every candidate entering the CRM is treated as a Lead.

            </p>

            <p>

                The Lead Module provides complete functionality for:

            </p>

            <ul>

                <li>Lead Creation</li>

                <li>Lead Assignment</li>

                <li>Lead Reassignment</li>

                <li>Duplicate Lead Detection</li>

                <li>Follow-up Management</li>

                <li>Lead Qualification</li>

                <li>Lead Notes</li>

                <li>Lead Activity Logs</li>

                <li>Export</li>

                <li>Filters</li>

                <li>WhatsApp Integration</li>

                <li>Email Notifications</li>

                <li>Meta Conversion API</li>

            </ul>

        </div>

    </div>

    <!-- Workflow -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Lead Workflow

            </h5>

        </div>

        <div class="card-body">

<pre>

Candidate
     │
     ▼
Lead Created
     │
     ▼
Duplicate Check
     │
     ▼
Lead Assignment
     │
     ▼
Follow-up
     │
     ▼
Qualification
     │
     ▼
Selection
     │
     ▼
Deployment

</pre>

        </div>

    </div>

    <!-- Key Features -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Key Features

            </h5>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <ul>

                        <li>Auto Lead Assignment</li>

                        <li>Manual Lead Assignment</li>

                        <li>Reassignment</li>

                        <li>Activity Timeline</li>

                        <li>Lead Notes</li>

                        <li>History Tracking</li>

                    </ul>

                </div>

                <div class="col-md-6">

                    <ul>

                        <li>WhatsApp Notifications</li>

                        <li>Email Notifications</li>

                        <li>Meta Conversion API</li>

                        <li>Advanced Filters</li>

                        <li>Export</li>

                        <li>Cron Jobs</li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

    <!-- Business Flow -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Business Flow

            </h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th width="50">

                            Step

                        </th>

                        <th>

                            Description

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>1</td>

                        <td>Create Lead</td>

                    </tr>

                    <tr>

                        <td>2</td>

                        <td>Validate Lead</td>

                    </tr>

                    <tr>

                        <td>3</td>

                        <td>Assign Lead</td>

                    </tr>

                    <tr>

                        <td>4</td>

                        <td>Follow-up</td>

                    </tr>

                    <tr>

                        <td>5</td>

                        <td>Qualification</td>

                    </tr>

                    <tr>

                        <td>6</td>

                        <td>Deployment</td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Related Documentation -->

    <div class="card">

        <div class="card-header">

            <h5>

                Related Documents

            </h5>

        </div>

        <div class="card-body">

            <div class="list-group">

                <a href="{{ route('docs.lead.database') }}" class="list-group-item list-group-item-action">

                    Database Structure

                </a>

                <a href="{{ route('docs.lead.create') }}" class="list-group-item list-group-item-action">

                    Lead Creation

                </a>

                <a href="{{ route('docs.lead.assignment') }}" class="list-group-item list-group-item-action">

                    Lead Assignment

                </a>

                <a href="{{ route('docs.lead.followup') }}" class="list-group-item list-group-item-action">

                    Follow-up

                </a>

                <a href="{{ route('docs.lead.qualification') }}" class="list-group-item list-group-item-action">

                    Qualification

                </a>

            </div>

        </div>

    </div>

</div>

@endsection