@extends('layout.docs.docs_layout')

@section('title', 'Deal Pipeline Module - Notes & Files')

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
                Notes &amp; Files
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Notes &amp; Files
    </h2>

    <p class="text-muted mb-4">
        Two dedicated controllers, separate from the Lead module's equivalents — Deal Pipeline has its own notes
        and files tables, not shared ones.
    </p>

    <!-- Notes -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0"><code>DealNoteController</code> &mdash; the <code>deal_notes</code> Table</h5>
        </div>

        <div class="card-body">

            <p>
                Analogous to Lead notes, but a completely separate, dedicated table &mdash; not shared with
                <code>leadnotes</code>. <code>store()</code> validates <code>deal_id</code> (must exist in
                <code>deal_pipeline</code>), <code>notes</code> (required), and an optional
                <code>conversation_type</code>; creates the row with <code>created_by = auth()-&gt;id()</code>.
            </p>

            <div class="alert alert-primary mb-0">
                <strong>Adding a note bumps the deal's <code>updated_at</code>.</strong> As a side effect,
                <code>store()</code> does a raw <code>DealPipeline::where('id', ...)-&gt;update(['updated_at' =&gt; now()])</code>.
                This is what feeds and resets the staleness check behind the
                <code>deals:notify-not-updated</code> cron &mdash; see
                <a href="{{ route('docs.deal-pipeline-module.reminders') }}">Reminders &amp; Cron Jobs</a>.
            </div>

            <p class="mt-3 mb-0">
                <code>list()</code> renders an HTML table server-side (not a JSON feed), with the delete link only
                shown to full admins (<code>user_type == 1</code>). <code>destroy()</code> validates
                <code>delete_id</code> exists in <code>deal_notes</code> and hard-deletes it.
            </p>

        </div>

    </div>

    <!-- Files -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0"><code>DealFileController</code> &mdash; the <code>deal_files</code> Table</h5>
        </div>

        <div class="card-body">

            <p>
                <code>store()</code> validates <code>deal_id</code> exists, an uploaded <code>file_upload</code>
                (max 2MB), and a <code>file_name</code>. The original filename is sanitized
                (<code>preg_replace('/[^A-Za-z0-9_\-]/','_', ...)</code>) and a <code>_{time()}</code> suffix is
                appended to avoid collisions. Files physically move to
                <code>public/admin/assets/deal_files</code> or <code>public_html/admin/assets/deal_files</code>,
                depending on the app's <code>Basepathstatus</code> deployment setting.
            </p>

            <p class="mb-0">
                <code>list()</code> returns JSON with computed <code>file_url</code>/<code>download_url</code>/
                <code>delete_url</code>. <code>download()</code> streams the file back with a friendly filename.
                <code>destroy()</code> physically <code>unlink()</code>s the file before deleting the DB row.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.deal-pipeline-module.settings') }}" class="btn btn-outline-secondary btn-sm">&larr; Settings &amp; Lookups</a>
        <a href="{{ route('docs.deal-pipeline-module.reminders') }}" class="btn btn-outline-primary btn-sm">Reminders &amp; Cron Jobs &rarr;</a>
    </div>

</div>

@endsection
