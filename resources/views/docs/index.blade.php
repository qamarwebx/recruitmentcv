@extends('layout.docs.docs_layout')

@section('title','Documentation')

@section('content')

<div class="container-fluid">

    <div class="row mb-4">

        <div class="col-md-12">

            <h2 class="fw-bold">
                Documentation
            </h2>

            <p class="text-muted">
                Welcome to QAMAR HIRE Documentation Portal.
            </p>

        </div>

    </div>

    <div class="row">

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-users text-primary"></i>
                    </h4>

                    <h5>Lead Module</h5>

                    <p class="text-muted">
                        Complete Lead Management Documentation
                    </p>

                    <a href="{{ route('docs.lead-module') }}"
                       class="btn btn-primary btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-user-check text-success"></i>
                    </h4>

                    <h5>Lead Assignment</h5>

                    <p class="text-muted">
                        Automatic Lead Distribution Logic
                    </p>

                    <a href="{{ route('docs.lead-assignment') }}"
                       class="btn btn-success btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-address-book text-info"></i>
                    </h4>

                    <h5>Contacts Module</h5>

                    <p class="text-muted">
                        Creation, Google Sync, Groups &amp; Lead Cross-Reference
                    </p>

                    <a href="{{ route('docs.contacts-module') }}"
                       class="btn btn-info btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-square-check text-primary"></i>
                    </h4>

                    <h5>Task Module</h5>

                    <p class="text-muted">
                        Recurring Reminders &amp; Cross-Module Sink
                    </p>

                    <a href="{{ route('docs.task-module') }}"
                       class="btn btn-primary btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-chart-line text-success"></i>
                    </h4>

                    <h5>Deal Pipeline Module</h5>

                    <p class="text-muted">
                        Kanban Stages, Creation &amp; Lead Linkage
                    </p>

                    <a href="{{ route('docs.deal-pipeline-module') }}"
                       class="btn btn-success btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-shopping-cart text-warning"></i>
                    </h4>

                    <h5>Orders Module</h5>

                    <p class="text-muted">
                        Bookings, Visa Step &amp; Employer Creation
                    </p>

                    <a href="{{ route('docs.orders-module') }}"
                       class="btn btn-warning btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-briefcase text-secondary"></i>
                    </h4>

                    <h5>Employer Module</h5>

                    <p class="text-muted">
                        Employer vs. Employer Plus, Explained
                    </p>

                    <a href="{{ route('docs.employer-module') }}"
                       class="btn btn-secondary btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-message text-info"></i>
                    </h4>

                    <h5>Testimonial Module</h5>

                    <p class="text-muted">
                        Self-Service Video Collection Links
                    </p>

                    <a href="{{ route('docs.testimonial-module') }}"
                       class="btn btn-info btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-user-plus text-primary"></i>
                    </h4>

                    <h5>Associate Module</h5>

                    <p class="text-muted">
                        Sourcing Agents, Verification &amp; Confirmation Gate
                    </p>

                    <a href="{{ route('docs.associate-module') }}"
                       class="btn btn-primary btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-user text-danger"></i>
                    </h4>

                    <h5>Candidate Module</h5>

                    <p class="text-muted">
                        Profiles, Publish Wizard &amp; Deployment Pipeline
                    </p>

                    <a href="{{ route('docs.candidate-module') }}"
                       class="btn btn-danger btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-clock text-warning"></i>
                    </h4>

                    <h5>Cron Jobs</h5>

                    <p class="text-muted">
                        Scheduled Background Processes
                    </p>

                    <a href="{{ route('docs.cron-jobs') }}"
                       class="btn btn-warning btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card h-100">

                <div class="card-body">

                    <h4>
                        <i class="ti ti-brand-meta text-info"></i>
                    </h4>

                    <h5>Meta API</h5>

                    <p class="text-muted">
                        Meta Conversion API Integration
                    </p>

                    <a href="{{ route('docs.meta-api') }}"
                       class="btn btn-info btn-sm">
                        Open
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection