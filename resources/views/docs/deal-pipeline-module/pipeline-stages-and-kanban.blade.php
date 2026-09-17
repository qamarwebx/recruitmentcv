@extends('layout.docs.docs_layout')

@section('title', 'Deal Pipeline Module - Pipeline Stages & Kanban Board')

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
                Pipeline Stages &amp; Kanban Board
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Pipeline Stages &amp; Kanban Board
    </h2>

    <p class="text-muted mb-4">
        Deals move through a 5-stage pipeline, viewable as either a Kanban board or a paginated list.
    </p>

    <!-- Stages -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The 5 Stages</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Stage</th><th>Meaning</th></tr></thead>
                    <tbody>
                        <tr><td><strong>Prospecting</strong></td><td>The default starting stage &mdash; both manual creation and lead-qualification auto-create always start here.</td></tr>
                        <tr><td>Discussion</td><td>Active conversation stage.</td></tr>
                        <tr><td>Proposal Review</td><td>Proposal stage.</td></tr>
                        <tr><td>Closed Won</td><td>Terminal — deal won.</td></tr>
                        <tr><td>Closed Lost</td><td>Terminal — deal lost.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-warning mb-0">
                <strong>Stale hardcoded stage map.</strong> <code>updateDealStage()</code> in the controller contains
                a hardcoded badge/message map referencing stage names <code>Prospecting</code>,
                <code>Qualification</code>, <code>Discussion</code>, <code>Proposal</code>, <code>Review</code>,
                <code>Closed Won</code>, <code>Closed Lost</code> — but the live database has <code>Proposal
                Review</code> as one stage (not separate <code>Proposal</code>/<code>Review</code>), and no
                <code>Qualification</code> stage exists at all. This stale map only affects a cosmetic
                fallback/message, not which stages can actually be set, but it's worth knowing about if that code
                is ever touched.
            </div>

        </div>

    </div>

    <!-- How it moves -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Three Ways a Deal Changes Stage</h5>
        </div>

        <div class="card-body">

            <ol class="mb-0">
                <li>
                    <strong>Drag-and-drop on the Kanban board.</strong> Dropping a card into a new column fires two
                    AJAX calls: <code>admin.dealPipeline.kanbanReorder</code> (persists <code>sort_order</code> in
                    the new column) and <code>admin.dealPipeline.kanbanUpdateStatus</code> (updates
                    <code>deal_stage_id</code> and reindexes <code>sort_order</code> for the target column).
                    Reordering within the same column only calls <code>kanbanReorder</code>. Drag-drop is gated by
                    the <code>deal_update_stage</code> permission (or full admin access).
                </li>

                <li class="mt-3">
                    <strong>Dropdown in the list view.</strong> Wired to
                    <code>admin.dealPipeline.updateDealStage</code>, which looks up the target
                    <code>DealStage</code> by name and updates <code>deal_stage_id</code> directly.
                </li>

                <li class="mt-3">
                    <strong>Automatic — Lead qualification.</strong> Marking a lead "Lead Qualified" auto-creates a
                    new deal always in <strong>Prospecting</strong>. See
                    <a href="{{ route('docs.deal-pipeline-module.relationships') }}">Relationship to Leads &amp; Candidates</a>.
                </li>
            </ol>

        </div>

    </div>

    <!-- Views -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Kanban vs. List View</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>admin.dealPipeline.list</code> renders both a Kanban board and a paginated list; which one
                loads by default persists per admin via <code>deal_admin_save_filters.switch_to</code> (0 = kanban,
                1 = list), toggled through <code>admin.dealPipeline.switchto</code>. See
                <a href="{{ route('docs.deal-pipeline-module.filters') }}">Filters &amp; Saved Views</a>.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.deal-pipeline-module.database') }}" class="btn btn-outline-secondary btn-sm">&larr; Database Structure</a>
        <a href="{{ route('docs.deal-pipeline-module.creation') }}" class="btn btn-outline-primary btn-sm">Deal Creation &rarr;</a>
    </div>

</div>

@endsection
