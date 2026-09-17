@extends('layout.docs.docs_layout')

@section('title', 'Employer Module - Filters, Permissions & Routes')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.employer-module') }}">Employer Module</a>
            </li>
            <li class="breadcrumb-item active">
                Filters, Permissions &amp; Routes
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Filters, Permissions &amp; Routes
    </h2>

    <p class="text-muted mb-4">
        Filtering and permissions mirror the base/plus split; a couple of route names are misleading enough to be
        worth flagging explicitly.
    </p>

    <!-- Filters -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Filters</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Both models carry a matching set of query scopes (Employer Plus scope names have a trailing "P" to
                avoid collisions, e.g. <code>scopeFilterCareoff</code> vs <code>scopeFilterCareoffP</code>):
                <code>FilterpartnerOffice</code>, <code>FilterCreateByPartner</code>, <code>FilterWakalaStatus</code>,
                <code>FilterCareoff</code>, <code>FilterCreatedBy</code>, <code>FilterBusinessType</code>,
                <code>FilterCityOfWork</code>, <code>FilterVisaIssuingAuthority</code>,
                <code>FilterProfession</code> (uses <code>FIND_IN_SET</code> against the CSV <code>proff_id</code>),
                <code>FilterDateRange</code>, <code>FilterSearchText</code>. Saved filter state persists per admin
                via <code>employeradminsavefilters</code> / <code>employerplusadminsavefilters</code>, the same
                pattern used throughout the rest of the CRM.
            </p>

        </div>

    </div>

    <!-- Permissions -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Permissions</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Gated by separate <code>employer</code> and <code>employerplus</code> flags on
                <code>Adminpermission</code>, plus <code>view_employer</code> for row-level visibility. Full-access
                admins see everything; permission-limited staff (<code>user_type == 2</code>) see all-or-own rows
                depending on their <code>view_employer</code> flag.
            </p>

        </div>

    </div>

    <!-- Misleading routes -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Two Misleading Route/Method Names</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li>
                    <strong><code>admin.employer.show</code></strong> (<code>showp()</code>, <code>GET employer-list/show/{id}</code>)
                    doesn't show an Employer at all — it renders a <strong>Booking</strong> detail view. A naming
                    quirk carried over from an earlier iteration of the module, not a real employer detail page.
                </li>
                <li>
                    The unnamed <strong><code>POST employer-list-plus/json</code></strong> route
                    (<code>indexpJson()</code>) — despite living under the Employer Plus URL prefix — actually
                    serves a <strong>Bookings</strong> DataTables feed, unrelated to <code>employerpluses</code>
                    data.
                </li>
            </ul>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.employer-module.work-agreement') }}" class="btn btn-outline-secondary btn-sm">&larr; Work Agreement PDF</a>
        <a href="{{ route('docs.employer-module') }}" class="btn btn-outline-primary btn-sm">Back to Overview &rarr;</a>
    </div>

</div>

@endsection
