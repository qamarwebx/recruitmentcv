@extends('layout.docs.docs_layout')

@section('title', 'Contacts Module - Groups')

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
                Groups
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Groups
    </h2>

    <p class="text-muted mb-4">
        A named bucket of contacts with an optional capacity limit &mdash; mainly used to cap WhatsApp campaign
        sending list sizes.
    </p>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The <code>groupallcs</code> Table</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>name</code></td><td>Group name.</td></tr>
                        <tr><td><code>max_limit</code></td><td>Maximum number of contacts allowed in the group.</td></tr>
                        <tr><td><code>admin_id</code></td><td>Creator.</td></tr>
                        <tr><td><code>staff_id</code></td><td>Present in schema; the assignment logic for this is currently commented out (dead code) in <code>groupstore()</code>.</td></tr>
                        <tr><td><code>status</code></td><td>Active flag.</td></tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 mb-0">
                Model: <code>App\Models\Groupallc</code> (note the class name is <code>Groupallc</code>, not
                <code>Group</code>).
            </p>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Assigning Contacts to a Group</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>allcontacts.group_id</code> (a varchar, not an int FK) holds a single group id as a string.
                It's set either at CSV-import time (as a dropdown default) or via bulk transfer:
            </p>

<pre><code>Allcontact::whereIn('id', $ids)->update(['group_id' => $request->group_id]);</code></pre>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Limit Enforcement</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Before a bulk transfer/send, <code>allcheckgroupLimit</code> computes:
            </p>

<pre><code>$remaining = $group->max_limit - Allcontact::where('group_id', $grpID)->count();</code></pre>

            <p class="mt-2 mb-0">
                and returns whether the requested batch (a comma-separated id list) fits before allowing the
                operation to proceed.
            </p>

        </div>

    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">CRUD &amp; Reporting</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li>List: <code>admin.allcontactgroup.list</code> &mdash; JSON via a plain <code>Groupallc::orderBy('id','DESC')-&gt;get()</code>.</li>
                <li>Name-uniqueness check, create (<code>admin.allcontactgroup.store</code>), edit, and update (<code>admin.allcontactgroup.update</code>) round out the CRUD.</li>
                <li>Filtering: the <code>FilterGroup</code> scope on <code>Allcontact</code> supports selecting one or more group ids, plus a special <code>'null'</code> sentinel meaning "ungrouped" (<code>orWhereNull('group_id')</code>).</li>
                <li>A separate group-wise report exists at <code>admin.allcontact.groupwise.report</code>.</li>
            </ul>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.contacts-module.google-sync') }}" class="btn btn-outline-secondary btn-sm">&larr; Google Contacts Sync</a>
        <a href="{{ route('docs.contacts-module.lead-sync') }}" class="btn btn-outline-primary btn-sm">Lead Sync &rarr;</a>
    </div>

</div>

@endsection
