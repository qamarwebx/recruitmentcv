@extends('layout.docs.docs_layout')

@section('title', 'Task Module - Assignment & Permissions')

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
                Assignment &amp; Permissions
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Assignment &amp; Permissions
    </h2>

    <p class="text-muted mb-4">
        Tasks are assigned only to staff &mdash; never to CRM records &mdash; and multiple staff can share one
        task.
    </p>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Who a Task Can Be Assigned To</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li><strong>Assignees</strong> &mdash; Admin/staff users only, via <code>assignto_id</code> (a comma-separated list of Admin ids, queried with <code>FIND_IN_SET</code>). Multiple staff can be assigned to the same task. Defaults to the creating admin if left blank at creation.</li>
                <li><strong>Owner/creator</strong> &mdash; <code>admin_id</code>, a single required value.</li>
                <li><strong>Department</strong> &mdash; <code>department_id</code>, a single FK. This is a categorization/filter dimension, <strong>not</strong> an assignment target &mdash; nothing auto-fans a task out to "everyone in this department."</li>
                <li><strong>Label</strong> &mdash; <code>todolabel_id</code>, a single FK. Pure tag/category, also reused when other modules duplicate a reminder into this table (see <a href="{{ route('docs.task-module.shared-sink') }}">Shared Reminder Sink</a>).</li>
                <li><strong>No polymorphic relation to Lead/Candidate/Contact.</strong> Confirmed absent from both the schema and the model &mdash; a task cannot be directly attached to a CRM record.</li>
            </ul>

        </div>

    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Permission Gating</h5>
        </div>

        <div class="card-body">

            <p>
                <code>Adminpermission</code> (table <code>adminpermissions</code>) has a dedicated block of Task
                permissions:
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Permission</th><th>Controls</th></tr></thead>
                    <tbody>
                        <tr><td><code>todo</code>, <code>todo_add</code>, <code>todo_view</code>, <code>todo_edit</code>, <code>todo_delete</code></td><td>Base CRUD access to tasks.</td></tr>
                        <tr><td><code>todo_achieved</code></td><td>Whether the "Achieved" status is visible/usable for this staff member.</td></tr>
                        <tr><td><code>todo_setting</code></td><td>Access to the Task module's settings (Labels/Departments).</td></tr>
                        <tr><td><code>todo_label(_add/_view/_edit/_delete)</code></td><td>Fine-grained access to Todo Label management.</td></tr>
                        <tr><td><code>bulk_todo_update_status</code>, <code>bulk_todo_update_priority</code>, <code>bulk_todo_update_assignto</code>, <code>bulk_todo_delete</code></td><td>Access to the bulk-action endpoints.</td></tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 mb-0">
                A non-admin without <code>todo_view</code> is restricted, via <code>FIND_IN_SET(?, assignto_id)</code>,
                to seeing only tasks they're personally assigned to. Without <code>todo_achieved</code>, the
                "Achieved" status is hidden from their view entirely. As with other modules, a
                <code>full_access</code> flag on the admin bypasses all of the above.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.task-module.shared-sink') }}" class="btn btn-outline-secondary btn-sm">&larr; Shared Reminder Sink</a>
        <a href="{{ route('docs.task-module.labels') }}" class="btn btn-outline-primary btn-sm">Labels &amp; Departments &rarr;</a>
    </div>

</div>

@endsection
