@extends('layout.docs.docs_layout')

@section('title', 'Candidate Module')

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
                    Candidate Module
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-4">

        <h2 class="fw-bold">
            Candidate Module
        </h2>

        <p class="text-muted">
            This document explains the Candidate module: profile creation, the publish wizard, document uploads,
            the deployment-status pipeline, transactions/finance, activity logging, and how (and how not) it
            relates to Leads and Deals.
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

                    <a href="{{ route('docs.candidate-module.database') }}" class="list-group-item">
                        2. Database Structure
                    </a>

                    <a href="{{ route('docs.candidate-module.creation') }}" class="list-group-item">
                        3. Candidate Creation
                    </a>

                    <a href="{{ route('docs.candidate-module.publish') }}" class="list-group-item">
                        4. Publish Wizard
                    </a>

                    <a href="{{ route('docs.candidate-module.documents') }}" class="list-group-item">
                        5. Documents
                    </a>

                    <a href="{{ route('docs.candidate-module.status') }}" class="list-group-item">
                        6. Status &amp; Deployment Pipeline
                    </a>

                    <a href="{{ route('docs.candidate-module.transactions') }}" class="list-group-item">
                        7. Transactions &amp; Finance
                    </a>

                    <a href="{{ route('docs.candidate-module.activity') }}" class="list-group-item">
                        8. Activity Log
                    </a>

                    <a href="{{ route('docs.candidate-module.leads-deals') }}" class="list-group-item">
                        9. Relationship to Leads &amp; Deals
                    </a>

                </div>

            </div>

        </div>

        <!-- Right Content -->

        <div class="col-lg-9">

            <!-- Overview -->

            <div class="card mb-4" id="overview">

                <div class="card-header">
                    <h5 class="mb-0">Overview</h5>
                </div>

                <div class="card-body">

                    <p>
                        A <strong>candidate</strong> is a job-seeker's profile as it moves through recruitment and
                        deployment &mdash; documents, experience, medical history, and a status pipeline that ends
                        at "Deployed". Unlike Leads, candidates are created directly by staff, not converted
                        automatically from anything (see <a href="{{ route('docs.candidate-module.leads-deals') }}">Relationship to Leads &amp; Deals</a>).
                    </p>

                    <p>
                        Every candidate profile carries three independent tracking systems side by side:
                    </p>

                    <ul>
                        <li>A <strong>publish wizard</strong> (4 stages: Passport &rarr; Skills/Experience &rarr; Documents &rarr; Ready For Publish) that gates whether the profile is publicly selectable.</li>
                        <li>A <strong>deployment pipeline</strong> (12 sequential statuses from "New Candidate" to "Deployed", plus Cancelled/Hold/Visa Cancelled) tracking the actual placement journey.</li>
                        <li>A <strong>finance record</strong> (service charge + payments) tracking what's owed and paid.</li>
                    </ul>

                    <p class="mb-0">
                        All of it &mdash; creation, document uploads, every stage/status change, every payment
                        &mdash; is written to one flat <strong>Activity</strong> timeline per candidate, the
                        candidate equivalent of the Lead module's Activity Log.
                    </p>

                </div>

            </div>

            <!-- Section cards -->

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Database Structure</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The <code>candidates</code> table (~90 columns) plus 13 related tables covering documents,
                        payments, activity, medical history, and more.
                        <a href="{{ route('docs.candidate-module.database') }}">Read the Database Structure guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Candidate Creation</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        What happens the moment a staff member saves a new candidate profile.
                        <a href="{{ route('docs.candidate-module.creation') }}">Read the Candidate Creation guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Publish Wizard</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The 4-stage checklist (Passport, Skills/Experience, Documents, Ready For Publish) a profile
                        passes through before it can be published.
                        <a href="{{ route('docs.candidate-module.publish') }}">Read the Publish Wizard guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Documents</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        Three separate upload paths &mdash; fixed-slot re-upload, ad hoc labeled attachments, and the
                        publish wizard's document stage &mdash; and the profile-completeness check behind
                        <code>cv_execute</code>.
                        <a href="{{ route('docs.candidate-module.documents') }}">Read the Documents guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Status &amp; Deployment Pipeline</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The 12-step deployment pipeline from "New Candidate" to "Deployed", how status changes are
                        recorded, and how it differs from the legacy <code>cand_status</code> lookup.
                        <a href="{{ route('docs.candidate-module.status') }}">Read the Status &amp; Deployment Pipeline guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Transactions &amp; Finance</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        How service charges and payments are tracked, and how <code>cand_payment_status</code>
                        ("Paid" / "Partial Paid") gets computed.
                        <a href="{{ route('docs.candidate-module.transactions') }}">Read the Transactions &amp; Finance guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Activity Log</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        The single flat timeline table every candidate action gets written to, and how it differs
                        from the Lead module's split notes/activity-log approach.
                        <a href="{{ route('docs.candidate-module.activity') }}">Read the Activity Log guide &rarr;</a>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Relationship to Leads &amp; Deals</h5></div>
                <div class="card-body">
                    <p class="mb-0">
                        There is no database link between Leads/Deals and Candidates &mdash; important context if
                        you're coming from the Lead module.
                        <a href="{{ route('docs.candidate-module.leads-deals') }}">Read the Relationship to Leads &amp; Deals guide &rarr;</a>
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
