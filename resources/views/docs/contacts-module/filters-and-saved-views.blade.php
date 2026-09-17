@extends('layout.docs.docs_layout')

@section('title', 'Contacts Module - Filters & Saved Views')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.contacts-module') }}">Contacts Module</a>
            </li>
            <li class="breadcrumb-item active">
                Filters &amp; Saved Views
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Filters &amp; Saved Views
    </h2>

    <p class="text-muted mb-4">
        How the Contacts list screen filters, and how an admin's filter choices persist between visits.
    </p>

    <!-- List JSON -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The List Endpoint</h5>
        </div>

        <div class="card-body">

            <p>
                <code>GET allcontact-list/json</code> (<code>jsonData()</code>) is the DataTables source behind the
                list page. It's permission-scoped: an admin without <code>allcontact_view</code> permission is
                restricted to contacts where <code>careoff_id</code> equals their own id.
            </p>

            <p class="mb-0">
                If the request carries any recognized filter field (business type, careoff, created-by, group,
                lifecycle status, lead stage, priority, industry, region/country/city, owner, conversation type,
                dial-code fields, date fields, follow-up-before, or <code>custom_filters</code>), those are applied
                live via the model's scopes. Otherwise it falls back to the admin's <strong>saved filter</strong>.
            </p>

        </div>

    </div>

    <!-- Saved filter -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Saved Filters</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>Allcontactadminsavefilter</code> (table <code>allcontactadminsavefilters</code>) stores one
                row per admin with a column for nearly every filter dimension, plus a <code>custom_filters</code>
                JSON column. Save via <code>allcontact.saveadminfilter</code>, reset via
                <code>allcontact.resetadminfilter</code>. Both the <a href="{{ route('docs.contacts-module.export') }}">CSV export job</a>
                and the <a href="{{ route('docs.contacts-module.export') }}">email-portal export job</a> re-apply
                this same saved filter when exporting "all" matching contacts, so what an admin sees on screen is
                exactly what gets exported.
            </p>

        </div>

    </div>

    <!-- Scopes -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Query Scopes on <code>Allcontact</code></h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Scope</th><th>What it does</th></tr></thead>
                    <tbody>
                        <tr><td><code>FilterCountry</code>, <code>FilterCity</code>, <code>FilterState</code></td><td>Location filters.</td></tr>
                        <tr><td><code>FilterLcs</code>, <code>FilterLs</code></td><td>Lifecycle Status / Lead Stage.</td></tr>
                        <tr><td><code>FilterBusinesstype</code>, <code>FilterIndustry</code></td><td>Classification filters.</td></tr>
                        <tr><td><code>FilterGroup</code></td><td>Group filter — supports a <code>'null'</code> sentinel meaning "no group".</td></tr>
                        <tr><td><code>FilterCreatedBy</code>, <code>FilterLeadOwner</code>, <code>FilterLeadCareoff</code></td><td>Ownership filters.</td></tr>
                        <tr><td><code>FilterLeadPriority</code></td><td>Priority filter.</td></tr>
                        <tr><td><code>FilterDateRange($column, $range)</code></td><td>Generic date-range applier, used for <code>created_at</code>, <code>updated_at</code>, and <code>staff_updated_date</code>.</td></tr>
                        <tr><td><code>FilterCountryDialCodeField</code>, <code>FilterExludeCountryMobileCode</code></td><td>Matches/excludes contacts whose WhatsApp numbers start with a given country dial code.</td></tr>
                        <tr><td><code>FilterCountryDialCode</code></td><td>Same idea, but against the dedicated <code>*_dial_code</code> columns.</td></tr>
                        <tr><td><code>FilterSearchText</code></td><td>Global search across ~15 direct columns plus related model names (country, city, state, user, lcs, owner, industry, group, careoff, lead stage).</td></tr>
                        <tr><td><code>FilterFollowupBefore($days)</code></td><td>"Due soon" filter — filters by <code>updated_at</code> between today and today+N days (note: keyed off <code>updated_at</code>, not <code>followup_date</code>).</td></tr>
                        <tr><td><code>FilterConversationType</code></td><td>Filters contacts that have a note of a given conversation type (<code>whereHas('notes', ...)</code>).</td></tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Custom filter caveat -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">The Generic <code>FilterCustom</code> Builder</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>FilterCustom(array $customFilters)</code> lets an admin build ad hoc "Column + Operator +
                Value" filters (LIKE, NOT LIKE, =, !=, REGEXP, IN, BETWEEN, and negations/variants). The scope's own
                docblock states it <strong>trusts its input completely</strong> &mdash; the caller
                (<code>AllContactController::validateCustomFilters</code>) is responsible for whitelisting which
                columns and operators are allowed before the scope ever runs. Worth keeping in mind if this code is
                ever touched: the safety boundary is in the controller, not the scope itself.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.contacts-module.export') }}" class="btn btn-outline-secondary btn-sm">&larr; Export &amp; History</a>
        <a href="{{ route('docs.contacts-module.lifecycle') }}" class="btn btn-outline-primary btn-sm">Lifecycle Status &rarr;</a>
    </div>

</div>

@endsection
