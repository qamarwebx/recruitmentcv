@extends('layout.docs.docs_layout')

@section('title', 'Duplicate Lead Detection')

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
                Duplicate Lead Detection
            </li>

        </ol>

    </nav>

    <!-- Header -->

    <div class="mb-4">

        <h2 class="fw-bold">

            Duplicate Lead Detection

        </h2>

        <p class="text-muted">

            This document explains how duplicate leads are identified and handled in the CRM.

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

                Before creating a new lead, the CRM checks whether the candidate
                already exists in the system.

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

New Lead Request

        │

        ▼

Check Mobile Number

        │

        ├────────────► Found

        │                 │

        │                 ▼

        │          Existing Lead

        │                 │

        │                 ▼

        │        Update Existing Data

        │

        ▼

Not Found

        │

        ▼

Create New Lead

</pre>

        </div>

    </div>

    <!-- Duplicate Criteria -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Duplicate Criteria

            </h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                <tr>

                    <th>Field</th>

                    <th>Purpose</th>

                </tr>

                </thead>

                <tbody>

                <tr>

                    <td>Mobile Number</td>

                    <td>Primary duplicate identifier</td>

                </tr>

                <tr>

                    <td>Email</td>

                    <td>Optional validation</td>

                </tr>

                <tr>

                    <td>Name</td>

                    <td>Reference only</td>

                </tr>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Business Rules -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Business Rules

            </h5>

        </div>

        <div class="card-body">

            <ul>

                <li>Check duplicate before saving.</li>

                <li>Mobile number is the primary key for duplicate detection.</li>

                <li>Existing lead can be updated instead of creating a new record.</li>

                <li>Maintain duplicate history if required.</li>

                <li>Do not create unnecessary duplicate records.</li>

            </ul>

        </div>

    </div>

    <!-- Laravel Implementation -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Laravel Implementation

            </h5>

        </div>

        <div class="card-body">

<pre>

LeadController

↓

Check Mobile Number

↓

Lead::where('mob_no', $request->mob_no)

↓

Exists ?

↓

Yes → Update Existing Lead

No → Create New Lead

</pre>

        </div>

    </div>

    <!-- Database Impact -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Database Impact

            </h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                <tr>

                    <th>Action</th>

                    <th>Result</th>

                </tr>

                </thead>

                <tbody>

                <tr>

                    <td>Duplicate Found</td>

                    <td>Existing Lead Updated</td>

                </tr>

                <tr>

                    <td>No Duplicate</td>

                    <td>New Lead Created</td>

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

                <a href="{{ route('docs.lead.assignment') }}"
                   class="list-group-item list-group-item-action">

                    Lead Assignment

                </a>

                <a href="{{ route('docs.lead.followup') }}"
                   class="list-group-item list-group-item-action">

                    Lead Follow-up

                </a>

                <a href="{{ route('docs.lead.qualification') }}"
                   class="list-group-item list-group-item-action">

                    Lead Qualification

                </a>

            </div>

        </div>

    </div>

</div>

@endsection