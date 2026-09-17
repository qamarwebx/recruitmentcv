@extends('layout.docs.docs_layout')

@section('title', 'Candidate Module - Publish Wizard')

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
                Publish Wizard
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Publish Wizard
    </h2>

    <p class="text-muted mb-4">
        A 4-stage checklist a candidate profile must pass through before it can be published (made publicly
        selectable). Tracked entirely by the <code>candpubsts</code> table &mdash; one row per candidate.
    </p>

    <div class="alert alert-info">
        This is a <strong>separate</strong> system from the deployment pipeline (<code>candidate_current_status</code>)
        covered in <a href="{{ route('docs.candidate-module.status') }}">Status &amp; Deployment Pipeline</a>. A
        candidate can be "Published For Selection" in the deployment pipeline while independently sitting mid-way
        through this publish wizard &mdash; the two don't drive each other.
    </div>

    <!-- Stages -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The 4 Stages</h5>
        </div>

        <div class="card-body">

            <p>
                <code>GET candidate/publish/stage/{id}</code> (<code>publishstg()</code>) looks at the candidate's
                <code>candpubsts</code> row and renders whichever stage view matches its current flags:
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th><code>passport_st</code></th>
                            <th><code>skill_exp_st</code></th>
                            <th><code>document_st</code></th>
                            <th><code>publish</code></th>
                            <th>View rendered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>0</td><td>0</td><td>0</td><td>0</td><td><code>passportStg</code> &mdash; Stage 1: Passport</td></tr>
                        <tr><td>1</td><td>0</td><td>0</td><td>0</td><td><code>skillexpStg</code> &mdash; Stage 2: Skills &amp; Experience</td></tr>
                        <tr><td>1</td><td>1</td><td>0</td><td>0</td><td><code>docsStg</code> &mdash; Stage 3: Documents</td></tr>
                        <tr><td>1</td><td>1</td><td>1</td><td>0</td><td><code>readyforpublish</code> &mdash; Stage 4: Ready For Publish</td></tr>
                        <tr><td colspan="4">else (<code>publish=1</code>)</td><td><code>publish</code> &mdash; final publish toggle screen</td></tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Endpoints -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Stage-Advance Endpoints</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr><th>Route name</th><th>What it does</th></tr>
                    </thead>
                    <tbody>
                        <tr><td><code>candidate.publish.pass</code></td><td>Saves passport fields on <code>candidates</code>, sets <code>passport_st = 1</code>, <code>stage_name = 'Experience Stage'</code>.</td></tr>
                        <tr><td><code>candidate.publish.skillandexp</code></td><td>Saves experience/skills fields, sets <code>skill_exp_st = 1</code>, <code>stage_name = 'Document Stage'</code>.</td></tr>
                        <tr><td><code>candidate.publish.docsstg</code></td><td>Re-uploads <code>pass_file</code>/<code>lic_file</code>/<code>cv_file</code>/<code>photo_file</code>. If both <code>pass_file</code> and <code>photo_file</code> end up non-empty, sets <code>document_st = 1</code>, <code>stage_name = 'Ready For Publish'</code>.</td></tr>
                        <tr><td><code>candidate.publish.uppub</code></td><td>Final toggle &mdash; sets <code>candidates.publish</code> and <code>candpubsts.publish</code> to 1/0, <code>stage_name</code> to <code>"Published"</code>/<code>"Unpublished"</code>.</td></tr>
                        <tr><td><code>candidate.publish.backskillandexp</code>, <code>.backdocs</code>, <code>.backpublish</code></td><td>"Back" actions &mdash; reset the corresponding stage flag to send the candidate back one step.</td></tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 mb-0">
                Every stage transition writes an <code>activities</code> row, so the full publish history is visible
                on the candidate's timeline. See <a href="{{ route('docs.candidate-module.activity') }}">Activity Log</a>.
            </p>

        </div>

    </div>

    <!-- Bulk publish -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Bulk Publish</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Beyond the per-candidate wizard, <code>candidate.published.update</code> and
                <code>candidate.bulk.published.update</code> let staff toggle the published flag for one or many
                candidates directly from the list screen, without stepping through the 4 stages again (useful once a
                candidate has already completed the wizard once).
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.candidate-module.creation') }}" class="btn btn-outline-secondary btn-sm">&larr; Candidate Creation</a>
        <a href="{{ route('docs.candidate-module.documents') }}" class="btn btn-outline-primary btn-sm">Documents &rarr;</a>
    </div>

</div>

@endsection
