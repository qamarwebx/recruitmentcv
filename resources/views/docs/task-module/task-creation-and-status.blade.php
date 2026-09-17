@extends('layout.docs.docs_layout')

@section('title', 'Task Module - Task Creation & Status Workflow')

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
                Task Creation &amp; Status Workflow
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Task Creation &amp; Status Workflow
    </h2>

    <p class="text-muted mb-4">
        How a task is created, and the free-text status pipeline that drives List and Kanban views alike.
    </p>

    <!-- Creation -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Creation &mdash; <code>store()</code></h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Builds a new <code>Todo</code> from <code>task_title</code>, <code>task_description</code>,
                <code>assignto_id</code> (imploded from an array of Admin ids, defaulting to the creator if left
                empty), <code>todolabel_id</code>, <code>department_id</code>, <code>start_on</code>/
                <code>finish_on</code>, <code>support_team_name</code>/<code>support_team_number</code>, and
                <code>type</code> (defaults <code>'Regular'</code>). An optional file uploads to
                <code>public/admin/assets/images/todo</code> (or <code>public_html/...</code>). Every new task is
                hard-coded to <code>task_status = "New Task"</code>. A <code>todoactivities</code> row is written,
                and <code>reindexColumn()</code> re-sorts <code>sort_order</code> within that status column for the
                Kanban view.
            </p>

        </div>

    </div>

    <!-- Status pipeline -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The Status Pipeline</h5>
        </div>

        <div class="card-body">

            <p>
                <code>task_status</code> is free text driven entirely by controller literals &mdash; despite a
                <code>todostatus_id</code> column and a <code>todostatuses</code> table existing in the schema,
                <strong>neither is actually used</strong> for this (see the callout below). The live values in use:
            </p>

            <p class="mb-0">
                <strong>New Task</strong> &rarr; <strong>In Process</strong> &rarr;
                <strong>Complete</strong> / <strong>Achieved</strong> / <strong>Not Required</strong>,
                plus side statuses <strong>Incomplete</strong>, <strong>On Hold</strong>, <strong>Cancelled</strong>,
                and <strong>Always</strong> (for recurring tasks that never "complete").
                There is no stored "Overdue" state &mdash; overdue-ness is computed on the fly from
                <code>finish_on</code> in the views, not persisted.
            </p>

        </div>

    </div>

    <!-- How status changes -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">How a Status Change Happens</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li><strong><code>updateStatus()</code></strong> &mdash; the dropdown/quick-action handler. Maps "Mark as X" text to <code>task_status</code> + <code>is_completed</code>; <code>Complete</code> stamps <code>complete_date</code>, <code>Achieved</code>/<code>Not Required</code> stamp <code>achieved_date</code>.</li>
                <li><strong><code>todokanbanstatusupdate()</code></strong> &mdash; Kanban drag-drop. Maps <code>stage-*</code> slugs (<code>stage-new-task</code>, <code>stage-in-process</code>, <code>stage-complete</code>, <code>stage-always</code>, <code>stage-achieved</code>, <code>stage-not-required</code>) to the same <code>task_status</code> values.</li>
                <li><strong><code>todobulkstatusupdate()</code></strong> &mdash; bulk status change across multiple selected tasks.</li>
            </ul>

            <div class="alert alert-danger mt-3 mb-0">
                <strong>Known bug in <code>todobulkstatusupdate()</code>:</strong> the condition chain uses an
                assignment (<code>if ($request-&gt;task_status = 'New Task')</code>) instead of a comparison
                (<code>==</code>), so the <code>is_completed</code> branch logic is effectively broken &mdash; the
                first condition is always true regardless of the actual requested status. Worth being aware of if
                you're relying on bulk status updates to correctly set <code>is_completed</code>.
            </div>

        </div>

    </div>

    <!-- Dead Todostatus -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">The Dead <code>todostatuses</code> Table</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Two dated backup controllers (<code>TodoController08-11-2024.php</code>,
                <code>13-11-2024.php</code>) show this table <em>was</em> queried
                (<code>Todostatus::orderBy('id','DESC')-&gt;get()</code>) as part of an earlier "configurable
                status" design, letting admins define their own statuses. That design was abandoned in favor of the
                hardcoded <code>task_status</code> strings above, but the table, model, and a leftover
                <code>use App\Models\Todostatus;</code> import in the live controller were never cleaned up. The
                table currently has 0 rows in production.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.task-module.database') }}" class="btn btn-outline-secondary btn-sm">&larr; Database Structure</a>
        <a href="{{ route('docs.task-module.recurring') }}" class="btn btn-outline-primary btn-sm">Recurring Tasks &amp; Scheduling &rarr;</a>
    </div>

</div>

@endsection
