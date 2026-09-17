@extends('layout.docs.docs_layout')

@section('title', 'Deal Pipeline Module - Filters & Saved Views')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.deal-pipeline-module') }}">Deal Pipeline Module</a>
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
        Search behaves differently depending on whether you're in the list view or the Kanban board.
    </p>

    <!-- Scopes -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Two Different Search Scopes</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Scope</th><th>Used by</th><th>Matches</th></tr></thead>
                    <tbody>
                        <tr>
                            <td><code>scopeFilterSearchText()</code></td>
                            <td>List view</td>
                            <td>Broad OR search across nearly every fillable column, plus related model names (business, stage, recruit status, care-of, creator, modifier).</td>
                        </tr>
                        <tr>
                            <td><code>scopeFilterSearchTextForkanban()</code></td>
                            <td>Kanban board's search box</td>
                            <td>Narrower — exact match on <code>passport_no</code>, a LIKE match on <code>candidate</code>, or an exact match on <code>id</code>.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Saved filters -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Saved Filters &amp; View Preference</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>DealAdminSaveFilter</code> (table <code>deal_admin_save_filters</code>, unique on
                <code>admin_id</code>) persists each admin's filter selections, saved via
                <code>admin.dealPipeline.saveFilter</code> and reset via
                <code>admin.dealPipeline.resetfilter</code> — the same pattern used across the Lead, Contacts,
                Candidate, and Task modules. It also stores <code>switch_to</code> (0 = kanban, 1 = list), the
                per-admin default view, updated via <code>admin.dealPipeline.switchto</code>.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.deal-pipeline-module.relationships') }}" class="btn btn-outline-secondary btn-sm">&larr; Relationship to Leads &amp; Candidates</a>
        <a href="{{ route('docs.deal-pipeline-module') }}" class="btn btn-outline-primary btn-sm">Back to Overview &rarr;</a>
    </div>

</div>

@endsection
