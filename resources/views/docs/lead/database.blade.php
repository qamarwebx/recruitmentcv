@extends('layout.docs.docs_layout')

@section('title', 'Lead Database Structure')

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
                Database Structure
            </li>
        </ol>
    </nav>

    <!-- Heading -->

    <div class="mb-4">

        <h2 class="fw-bold">

            Lead Database Structure

        </h2>

        <p class="text-muted">

            This document explains all database tables used in the Lead Module.

        </p>

    </div>

    <!-- Main Tables -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Main Tables

            </h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th width="250">

                            Table

                        </th>

                        <th>

                            Purpose

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>

                            leads

                        </td>

                        <td>

                            Stores all candidate leads.

                        </td>

                    </tr>

                    <tr>

                        <td>

                            leadnotes

                        </td>

                        <td>

                            Stores follow-up notes.

                        </td>

                    </tr>

                    <tr>

                        <td>

                            lead_activity_logs

                        </td>

                        <td>

                            Stores lead activity history.

                        </td>

                    </tr>

                    <tr>

                        <td>

                            lead_admin_save_filters

                        </td>

                        <td>

                            Stores saved filters.

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <!-- Leads Table -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Leads Table

            </h5>

        </div>

        <div class="card-body">

<pre>

Primary Key

id

Important Fields

lead_no
name
mob_no
email
city
state
country

leadassign_id

lead_date

is_qualified

status

source

created_at

updated_at

</pre>

        </div>

    </div>

    <!-- Relationships -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Relationships

            </h5>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Table</th>

                        <th>Relation</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>Lead → Admin</td>

                        <td>Many To One</td>

                    </tr>

                    <tr>

                        <td>Lead → Notes</td>

                        <td>One To Many</td>

                    </tr>

                    <tr>

                        <td>Lead → Activity Log</td>

                        <td>One To Many</td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

    <!-- ER Diagram -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                ER Diagram

            </h5>

        </div>

        <div class="card-body">

<pre>

Admin
  │
  │ 1
  │
  │
  ▼

Lead
  │
  ├──────────────► Lead Notes

  │
  └──────────────► Lead Activity Logs

</pre>

        </div>

    </div>

    <!-- Laravel Models -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Laravel Models

            </h5>

        </div>

        <div class="card-body">

<pre>

Lead.php

Leadnote.php

LeadActivityLog.php

Admin.php

</pre>

        </div>

    </div>

    <!-- Important Indexes -->

    <div class="card mb-4">

        <div class="card-header">

            <h5>

                Frequently Used Columns

            </h5>

        </div>

        <div class="card-body">

            <ul>

                <li>id</li>

                <li>leadassign_id</li>

                <li>lead_date</li>

                <li>is_qualified</li>

                <li>mob_no</li>

                <li>email</li>

                <li>created_at</li>

                <li>updated_at</li>

            </ul>

        </div>

    </div>

    <!-- Related Pages -->

    <div class="card">

        <div class="card-header">

            <h5>

                Next Documentation

            </h5>

        </div>

        <div class="card-body">

            <div class="list-group">

                <a href="{{ route('docs.lead.create') }}" class="list-group-item list-group-item-action">

                    Lead Creation

                </a>

                <a href="{{ route('docs.lead.assignment') }}" class="list-group-item list-group-item-action">

                    Lead Assignment

                </a>

                <a href="{{ route('docs.lead.followup') }}" class="list-group-item list-group-item-action">

                    Follow-up

                </a>

            </div>

        </div>

    </div>

</div>

@endsection