@extends('layout.docs.docs_layout')

@section('title', 'Orders Module - Requirement Snippets')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.orders-module') }}">Orders Module</a>
            </li>
            <li class="breadcrumb-item active">
                Requirement Snippets
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Requirement Snippets
    </h2>

    <div class="alert alert-warning">
        <strong>Not a per-order requirement/vacancy spec.</strong> Despite living under
        <code>admin.booking.requirement</code>, this is a settings-level list of reusable text snippets — it has no
        foreign key to any individual order.
    </div>

    <!-- What it is -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">What It Actually Is</h5>
        </div>

        <div class="card-body">

            <p>
                Model <code>App\Models\RequirementInfo</code>, table <code>requirement_info</code>:
                <code>requirement_text</code>, <code>language_type</code> (1 = English, 2 = Arabic),
                <code>status</code>, <code>admin_id</code>. Managed at
                <code>GET admin/booking-requirement</code> (<code>BackEndController::bookingRequirement</code>),
                saved via <code>admin.booking.requirementStr</code>. Duplicate-check endpoints
                (<code>checkRequirementEng</code>/<code>checkRequirementAr</code>) prevent adding the same text
                twice per language.
            </p>

            <p class="mb-0">
                The table currently has <strong>0 rows</strong> in the live database — the feature exists in code
                but is unused/empty.
            </p>

        </div>

    </div>

    <!-- Where used -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Where These Snippets Are Actually Shown</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                These are standard terms/requirements displayed to <strong>prospective employers browsing a
                candidate's public CV page</strong> (<code>FrontEndController</code> /
                <code>ArabicPageController</code> pull English/Arabic rows and render them as a bullet list on
                <code>resources/views/fullresumes.blade.php</code> and its Arabic counterpart) — a general
                disclaimer/terms list shown alongside every candidate's profile, not something attached to a
                specific booking.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.orders-module.receiver-panel') }}" class="btn btn-outline-secondary btn-sm">&larr; Order Receiver Panel</a>
        <a href="{{ route('docs.orders-module.cancellation-notifications') }}" class="btn btn-outline-primary btn-sm">Cancellation &amp; Notifications &rarr;</a>
    </div>

</div>

@endsection
