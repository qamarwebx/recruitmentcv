@extends('layout.docs.docs_layout')

@section('title', 'Deal Pipeline Module - Database Structure')

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
                Database Structure
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Database Structure
    </h2>

    <p class="text-muted mb-4">
        Model <code>App\Models\DealPipeline</code>, table <code>deal_pipeline</code> (singular).
    </p>

    <div class="alert alert-warning">
        Several columns are typed more loosely than their name suggests &mdash; <code>amount</code> and
        <code>close_date</code> are plain <code>varchar</code>, not decimal/date columns, and <code>care_of</code>
        (the assigned staff member) is stored as varchar even though it always holds an Admin id. Keep this in mind
        if you're writing queries or reports against this table.
    </div>

    <!-- Core fields -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Classification &amp; Ownership</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>sort_order</code></td><td>Kanban card position within a stage column.</td></tr>
                        <tr><td><code>business_id</code></td><td>FK-style link (no DB constraint) to <code>businesses</code> &mdash; determines whether this deal uses the "Job seeker" field set or the "common" field set. See <a href="{{ route('docs.deal-pipeline-module.creation') }}">Deal Creation</a>.</td></tr>
                        <tr><td><code>deal_stage_id</code></td><td>FK-style link to <code>deal_stages</code>. See <a href="{{ route('docs.deal-pipeline-module.stages') }}">Pipeline Stages &amp; Kanban Board</a>.</td></tr>
                        <tr><td><code>recruite_status_id</code></td><td>FK-style link to <code>recruit_statuses</code> &mdash; note the misspelling ("recruite") is baked into the actual column name throughout the codebase.</td></tr>
                        <tr><td><code>associate_id</code></td><td>The one genuinely FK-backed relationship on this table &mdash; links to <code>associates.id</code>. Required on manual "Job seeker" deal creation.</td></tr>
                        <tr><td><code>care_of</code></td><td>The assigned staff member (Admin id), stored as varchar.</td></tr>
                        <tr><td><code>created_by</code>, <code>modified_by</code></td><td>Admin who created / last edited the deal.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Job seeker fields -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">"Job Seeker" Fields</h5></div>
        <div class="card-body">
            <p class="text-muted">Populated when <code>business_id</code> resolves to the "Job seeker" business type.</p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>candidate</code></td><td><strong>Plain free text, no FK to the <code>candidates</code> table.</strong> Populated from a lead's <code>cand_name</code> on auto-create, or a manual entry. See <a href="{{ route('docs.deal-pipeline-module.relationships') }}">Relationship to Leads &amp; Candidates</a>.</td></tr>
                        <tr><td><code>passport_no</code></td><td><strong>Also plain free text, no FK.</strong> Only used to check for duplicates <em>within this same table</em>, never matched against <code>candidates</code>.</td></tr>
                        <tr><td><code>job_title</code></td><td>Mixed-use: usually holds a <code>job_titles.id</code> as a string, but also accepts free text (data shows both). Not reliable as a real foreign key despite the model's <code>jobTitle()</code> relation.</td></tr>
                        <tr><td><code>job_title_other</code></td><td>Free-text overflow, populated from a lead's <code>other_job_title</code> on auto-create.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Common fields -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">"Common" Fields (Non-"Job Seeker" Deals)</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>name</code></td><td>Contact/deal name &mdash; only populated for non-"Job seeker" business types.</td></tr>
                        <tr><td><code>company</code></td><td>Company name (free text; also set from a lead's <code>company_name</code> on auto-create).</td></tr>
                        <tr><td><code>deal_name</code></td><td><strong>Dead column.</strong> Present in the schema and the model's fillable list, but never actually written by any create/update path &mdash; effectively unused.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Shared fields -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Shared Fields</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>amount</code></td><td>Deal value &mdash; stored as varchar, despite the create/update form validating it as numeric.</td></tr>
                        <tr><td><code>notes</code></td><td>A single free-text notes field on the deal itself, separate from the <code>deal_notes</code> timeline table. See <a href="{{ route('docs.deal-pipeline-module.notes-files') }}">Notes &amp; Files</a>.</td></tr>
                        <tr><td><code>contact</code>, <code>contact_whatsapp</code>, <code>email</code></td><td>Contact details.</td></tr>
                        <tr><td><code>country</code>, <code>city</code></td><td>Location, free text.</td></tr>
                        <tr><td><code>close_date</code></td><td>Stored as varchar, not a real date column, despite being validated with <code>date_format:Y-m-d</code> on input. Only applicable to Job seeker deals.</td></tr>
                        <tr><td><code>source</code></td><td>Mixed data: real values include <code>www.qamrjob.com</code>, <code>Direct</code>, <code>Google</code>, <code>Facebook</code>, plus numeric-looking strings that are actually <code>global_source_type.id</code> values. The model's <code>source()</code> relation only resolves correctly for the numeric rows.</td></tr>
                        <tr><td><code>created_at</code>, <code>updated_at</code></td><td>Standard timestamps. <code>updated_at</code> doubles as the "staleness" signal used by the <code>deals:notify-not-updated</code> cron &mdash; see <a href="{{ route('docs.deal-pipeline-module.reminders') }}">Reminders &amp; Cron Jobs</a>.</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="mt-3 mb-0 text-muted">No <code>SoftDeletes</code> &mdash; deletion is a real hard delete.</p>
        </div>
    </div>

    <!-- Related tables -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Related Tables</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Table</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>businesses</code></td><td>Business type lookup. See <a href="{{ route('docs.deal-pipeline-module.settings') }}">Settings &amp; Lookups</a> &mdash; there's a second, unrelated table with a similar name, worth reading that caveat.</td></tr>
                        <tr><td><code>deal_stages</code></td><td>Pipeline stage lookup. See <a href="{{ route('docs.deal-pipeline-module.stages') }}">Pipeline Stages &amp; Kanban Board</a>.</td></tr>
                        <tr><td><code>recruit_statuses</code></td><td>Recruitment/medical progress lookup.</td></tr>
                        <tr><td><code>job_titles</code></td><td>Job title lookup.</td></tr>
                        <tr><td><code>deal_notes</code></td><td>Timeline/conversation log per deal (<code>deal_id, created_by, notes, conversation_type</code>).</td></tr>
                        <tr><td><code>deal_files</code></td><td>File attachments per deal (<code>deal_id, uploaded_by, file_name, file_path</code>).</td></tr>
                        <tr><td><code>deal_reminders</code></td><td>Reminders per deal (<code>deal_id, created_by, reminder_at, whatsapp_description, is_done, is_notified, notified_at</code>).</td></tr>
                        <tr><td><code>deal_admin_save_filters</code></td><td>Per-admin persisted filter state, unique on <code>admin_id</code>, including the kanban/list view toggle.</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="mt-3 mb-0 text-muted">
                There is no dedicated "deal activity log" table &mdash; the closest equivalent is the
                <code>deal_notes</code> timeline, plus <code>updated_at</code> being bumped whenever a note is
                added.
            </p>
        </div>
    </div>

    <!-- Model -->
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Model &mdash; <code>app/Models/DealPipeline.php</code></h5></div>
        <div class="card-body">
            <ul class="mb-0">
                <li>Relationships: <code>business()</code>, <code>stage()</code>, <code>recruitStatus()</code>, <code>careOf()</code> (&rarr; Admin via <code>care_of</code>), <code>creator()</code>, <code>modifier()</code>, <code>associate()</code>, <code>jobTitle()</code>, <code>source()</code> (&rarr; GlobalSourceType), <code>dealNotes()</code> hasMany.</li>
                <li>No <code>$casts</code> defined at all.</li>
                <li><strong>No model events.</strong> Unlike the Lead model's automatic activity-logging <code>booted()</code> hook, this model has none &mdash; every side effect (bumping <code>updated_at</code> on note-add, <code>sort_order</code> reindexing) is handled manually in the controller.</li>
                <li>Scopes: <code>scopeFilterSearchText()</code> (broad OR search across most fillable columns plus related model names) and <code>scopeFilterSearchTextForkanban()</code> (narrower — exact <code>passport_no</code>, LIKE <code>candidate</code>, or exact <code>id</code>), used specifically by the kanban search box.</li>
            </ul>
        </div>
    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.deal-pipeline-module') }}" class="btn btn-outline-secondary btn-sm">&larr; Overview</a>
        <a href="{{ route('docs.deal-pipeline-module.stages') }}" class="btn btn-outline-primary btn-sm">Pipeline Stages &amp; Kanban Board &rarr;</a>
    </div>

</div>

@endsection
