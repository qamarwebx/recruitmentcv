@extends('layout.docs.docs_layout')

@section('title', 'Candidate Module - Documents')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.candidate-module') }}">Candidate Module</a>
            </li>
            <li class="breadcrumb-item active">
                Documents
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Documents
    </h2>

    <p class="text-muted mb-4">
        There are <strong>three separate</strong> document-upload code paths for a candidate. They look similar but
        write to different places &mdash; this page exists to keep them straight.
    </p>

    <div class="table-responsive mb-4">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Path</th>
                    <th>Route</th>
                    <th>Writes to</th>
                    <th>Purpose</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>General re-upload</td>
                    <td><code>admin.candidate.docs.store</code></td>
                    <td><code>candidates</code> columns (overwrite)</td>
                    <td>Replace one of the 5 canonical document slots.</td>
                </tr>
                <tr>
                    <td>Ad hoc labeled attachment</td>
                    <td><code>admin.candidate.docs2.store</code></td>
                    <td>New row in <code>candidatefiles</code></td>
                    <td>Attach any number of arbitrarily labeled documents.</td>
                </tr>
                <tr>
                    <td>Publish-wizard document stage</td>
                    <td><code>candidate.publish.docsstg</code></td>
                    <td><code>candidates</code> columns + <code>candpubsts.document_st</code></td>
                    <td>Stage 3 of the <a href="{{ route('docs.candidate-module.publish') }}">Publish Wizard</a>.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Path 1 -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">1. General Re-upload &mdash; <code>uploadDocs()</code></h5>
        </div>

        <div class="card-body">

            <p>
                Accepts <code>pass_file</code>, <code>pass_back_file</code>, <code>lic_file</code>,
                <code>cv_file</code>, <code>photo_file</code> and <strong>overwrites the matching columns directly
                on the <code>candidates</code> row</strong>. Files are renamed with a pattern like
                <code>{pass_no}-PPFRONT-{datetime}</code>, <code>-PPBACK-</code>, <code>-DL-</code>, <code>-FP-</code>,
                <code>-P-</code>. No new table row is created &mdash; it's a "replace the 5 fixed slots" action.
            </p>

            <div class="alert alert-primary mb-0">
                <strong>Profile-completeness check.</strong> After saving, this endpoint re-evaluates ~24 required
                fields (name, photo, salary, marital status, religion, DOB, place of birth, nationality, region,
                passport fields, gulf experience, job type, contacts, associate confirmation, etc.) and sets
                <code>candidates.cv_execute</code> to <code>true</code>/<code>false</code> accordingly. If the
                candidate no longer qualifies, any previously generated CV PDF under
                <code>assets/images/pdf/</code> (and the partner copy under <code>assets/images/pdf/partner/</code>)
                is deleted, and related <code>companycvexecutes</code> rows are deactivated.
            </div>

        </div>

    </div>

    <!-- Path 2 -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">2. Ad Hoc Labeled Attachment &mdash; <code>uploadDocs2()</code></h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Accepts a free-text <code>label</code> and a <code>filename</code> file. The physical file is saved
                as <code>{pass_no}-{label}-{datetime}</code>, and a <strong>new row is inserted into
                <code>candidatefiles</code></strong> (<code>cand_id</code>, <code>label</code>,
                <code>filename</code>, <code>admin_id</code>). Unlike the 5 fixed slots above, this is an unbounded
                "attach anything" mechanism &mdash; useful for extra documents that don't fit the standard set
                (e.g. certificates, medical reports, contracts).
            </p>

        </div>

    </div>

    <!-- Path 3 -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">3. Publish-Wizard Document Stage</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Stage 3 of the <a href="{{ route('docs.candidate-module.publish') }}">Publish Wizard</a>. It re-uses
                the same fixed-slot fields as path 1 (<code>pass_file</code>, <code>lic_file</code>,
                <code>cv_file</code>, <code>photo_file</code>), but additionally sets
                <code>candpubsts.document_st = 1</code> and advances <code>stage_name</code> to
                <code>"Ready For Publish"</code> once both <code>pass_file</code> and <code>photo_file</code> are
                present. Both re-upload endpoints and this wizard step log an <code>activities</code> row
                ("Candidate documents uploaded!").
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.candidate-module.publish') }}" class="btn btn-outline-secondary btn-sm">&larr; Publish Wizard</a>
        <a href="{{ route('docs.candidate-module.status') }}" class="btn btn-outline-primary btn-sm">Status &amp; Deployment Pipeline &rarr;</a>
    </div>

</div>

@endsection
