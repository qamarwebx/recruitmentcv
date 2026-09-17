@extends('layout.docs.docs_layout')

@section('title', 'Contacts Module - Lifecycle Status')

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
                Lifecycle Status
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Lifecycle Status
    </h2>

    <div class="alert alert-warning">
        <strong>Not shared with Leads.</strong> This is easy to assume given the sidebar groups it near Lead-related
        settings, but Lifecycle Status is shared master data between exactly two modules:
        <strong>Contacts</strong> (this module, <code>allcontacts.lcs_id</code>) and <strong>Contact Plus</strong>
        (<code>contactplus.lcs_id</code>). Neither <code>leads</code> nor <code>candidates</code> has an
        <code>lcs_id</code> column.
    </div>

    <!-- Table -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The <code>lifecyclestatuses</code> Table</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Model: <code>App\Models\Lifecyclestatus</code> &mdash; deliberately thin, just
                <code>id</code>, <code>name</code>, and timestamps. No relationships or scopes are defined on the
                model itself; it's a plain lookup table.
            </p>

        </div>

    </div>

    <!-- Where managed -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Where It's Actually Managed</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Despite conceptually belonging next to Contacts, the CRUD screens are implemented in
                <strong><code>ContactpController</code></strong> (the Contact Plus module's controller), not
                <code>AllContactController</code>:
            </p>

            <ul class="mt-2 mb-0">
                <li><code>admin.lifecycle.list</code> &mdash; list page.</li>
                <li><code>admin.lifecycle.store</code> / <code>.edit</code> / <code>.update</code> &mdash; CRUD (just a <code>name</code> field).</li>
                <li>Name-uniqueness check endpoint.</li>
            </ul>

        </div>

    </div>

    <!-- Usage in Contacts -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Usage in This Module</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li><code>Allcontact::lcs()</code> relationship (<code>belongsTo(Lifecyclestatus::class)</code> via <code>lcs_id</code>).</li>
                <li>List filtering: <code>scopeFilterLcs()</code>, wired into the list JSON filter map, the saved-filter fallback, and both export jobs. See <a href="{{ route('docs.contacts-module.filters') }}">Filters &amp; Saved Views</a>.</li>
                <li>Inline update: <code>POST allcontact-list/update/lifecyclestatus</code> &mdash; a trivial <code>Allcontact::find($id)-&gt;update(['lcs_id' =&gt; $request-&gt;lcs_id])</code>, with no dedicated history/audit entry for the change itself (though <code>staff_updated_date</code> gets bumped as a side effect of the model's update event).</li>
                <li>Set at creation time (manual entry dropdown default, and CSV import mapped/dropdown field).</li>
                <li>Included in global search — <code>FilterSearchText</code> also matches against the related lifecycle status name.</li>
            </ul>

        </div>

    </div>

    <!-- Unrelated concept -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">A Different, Unrelated "Lifecycle Stage"</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                There's a separate "Lifecycle Stage" concept living in <code>CrmsController</code>
                (<code>lifecycle_stage_list</code>, <code>lifecycle_stage_store</code>,
                <code>contacts_lifecycle_update</code>) that appears to belong to a different/older CRM contacts
                flow. Don't conflate it with <code>Lifecyclestatus</code>/<code>lcs_id</code> documented above
                &mdash; if you run into it, treat it as a separate area worth its own investigation before writing
                about it.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.contacts-module.filters') }}" class="btn btn-outline-secondary btn-sm">&larr; Filters &amp; Saved Views</a>
        <a href="{{ route('docs.contacts-module') }}" class="btn btn-outline-primary btn-sm">Back to Overview &rarr;</a>
    </div>

</div>

@endsection
