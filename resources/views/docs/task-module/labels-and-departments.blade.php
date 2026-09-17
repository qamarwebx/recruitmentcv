@extends('layout.docs.docs_layout')

@section('title', 'Task Module - Labels & Departments')

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
                Labels &amp; Departments
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Labels &amp; Departments
    </h2>

    <p class="text-muted mb-4">
        Two simple lookup lists. The sidebar presents them as nested Settings sub-items, but both are actually
        managed by the same <code>TodoController</code> as the rest of the Task module — this is one cohesive
        module in the code, split across the sidebar for navigation purposes only.
    </p>

    <!-- Labels -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Todo Labels</h5>
        </div>

        <div class="card-body">

            <p>
                Table <code>todolabels</code>: <code>id</code>, <code>name</code>, <code>status</code>,
                <code>admin_id</code>. Model <code>Todolabel</code> &mdash; plain, no relations.
            </p>

            <ul class="mb-0">
                <li>List: <code>admin.todolabel.list</code></li>
                <li>Create: <code>admin.todolabel.store</code></li>
                <li>Edit fetch: <code>admin.todolabel.edit</code></li>
                <li>Update: <code>admin.todolabel.update</code></li>
                <li>Name-uniqueness check endpoint (unnamed route)</li>
            </ul>

        </div>

    </div>

    <!-- Departments -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Departments</h5>
        </div>

        <div class="card-body">

            <p>
                Table <code>departments</code>: <code>id</code>, <code>name</code>, <code>status</code>,
                <code>admin_id</code>. Model <code>Department</code> &mdash; plain, no relations.
            </p>

            <ul class="mb-0">
                <li>List: <code>admin.department.list</code></li>
                <li>Create: <code>admin.department.store</code></li>
                <li>Edit fetch: <code>admin.department.edit</code></li>
                <li>Update: <code>admin.department.update</code></li>
                <li>Name-uniqueness check endpoint (unnamed route)</li>
            </ul>

            <p class="mt-3 mb-0 text-muted">
                A department is purely a categorization/filter dimension on a task (<code>department_id</code>)
                &mdash; there's no auto-fan-out logic that assigns a task to every member of a department.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.task-module.assignment') }}" class="btn btn-outline-secondary btn-sm">&larr; Assignment &amp; Permissions</a>
        <a href="{{ route('docs.task-module.filters') }}" class="btn btn-outline-primary btn-sm">Filters &amp; Saved Views &rarr;</a>
    </div>

</div>

@endsection
