@extends('layout.docs.docs_layout')

@section('title', 'Task Module - Filters & Saved Views')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.task-module') }}">Task Module</a>
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
        The Task module supports both a List and a Kanban view, sharing the same filter pipeline and per-admin
        persisted state.
    </p>

    <!-- List vs kanban -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">List &amp; Kanban Views</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>index()</code> and <code>loadMoreKanban()</code> share the same underlying filter query, and
                a per-admin <code>view_type</code> preference (persisted via <code>admin.todo.switchto</code>)
                decides which one loads by default. Kanban drag-reorder within a column is handled by
                <code>kanbanreorder()</code>, updating <code>sort_order</code>.
            </p>

        </div>

    </div>

    <!-- Scopes -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Query Scopes on <code>Todo</code></h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Scope</th><th>What it does</th></tr></thead>
                    <tbody>
                        <tr><td><code>FilterDepartment</code>, <code>FilterLabel</code></td><td>Filter by department / label.</td></tr>
                        <tr><td><code>FilterReminderCycle</code></td><td>Filter by priority (Low/Medium/High/Urgent).</td></tr>
                        <tr><td><code>FilterType</code>, <code>FilterTaskStatus</code></td><td>Filter by <code>type</code> / <code>task_status</code>.</td></tr>
                        <tr><td><code>FilterAssignedTo</code></td><td>Matches against the CSV <code>assignto_id</code> via <code>FIND_IN_SET</code>.</td></tr>
                        <tr><td><code>FilterCreatedBy</code></td><td>Filter by creator — oddly also implemented with <code>FIND_IN_SET</code> even though <code>admin_id</code> is a single int, not a CSV list.</td></tr>
                        <tr><td><code>FilterFollowupBefore</code></td><td>Days-ago window on <code>updated_at</code>.</td></tr>
                        <tr><td><code>FilterTodayReminderTask</code></td><td>The most complex scope — computes "is this task due today" across every reminder mode (OneTime/Daily/Weekly/Monthly/Yearly/Custom), using <code>JSON_CONTAINS</code> for the weekday array.</td></tr>
                        <tr><td><code>FilterDate</code>, <code>FilterDateRange</code></td><td>Generic date filtering.</td></tr>
                        <tr><td><code>filterSearchText</code></td><td>Searches id/title/description plus related department, label, and admin names.</td></tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Saved filters -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Saved Filters</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>Todoadminsavefilter</code> (table <code>todoadminsavefilters</code>) stores one row per
                admin — department, label, priority, task status, assignee, creator, several date ranges, a
                follow-up-before window, a "today reminder task" toggle, the list/kanban <code>switch_to</code>
                preference, and <code>type</code>. Saved via <code>admin.todo.savefilter</code>, reset via
                <code>admin.todo.resetfilter</code> — the same persisted-filter pattern used by the Contacts and
                Candidate modules.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.task-module.labels') }}" class="btn btn-outline-secondary btn-sm">&larr; Labels &amp; Departments</a>
        <a href="{{ route('docs.task-module') }}" class="btn btn-outline-primary btn-sm">Back to Overview &rarr;</a>
    </div>

</div>

@endsection
