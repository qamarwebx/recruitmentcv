@extends('layout.docs.docs_layout')

@section('title', 'Lead Module - Duplicate Check')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.lead-module') }}">Lead Module</a>
            </li>
            <li class="breadcrumb-item active">
                Duplicate Check
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Duplicate Check
    </h2>

    <p class="text-muted mb-4">
        How the system detects that a candidate has submitted the form before, and what it does differently for
        that lead.
    </p>

    <div class="alert alert-warning">
        <strong>Important:</strong> a duplicate/repeat submission does <em>not</em> update the old lead row or block
        the new one. A brand-new <code>leads</code> row is <strong>always</strong> inserted &mdash; duplicate
        detection only changes how that new row is flagged and handled afterwards.
    </div>

    <!-- Detection -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">How a Duplicate Is Detected</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Path</th>
                            <th>Match field</th>
                            <th>What happens</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Optional pre-check &mdash; <code>POST /api/leads/checkexists</code></td>
                            <td><code>mob_no</code></td>
                            <td>The frontend can call this before submitting. If a lead with that mobile number exists, its <code>lead_status_text</code> is set back to <code>"New"</code> and its id is returned so the form can submit in "update" mode.</td>
                        </tr>
                        <tr>
                            <td>First-time submit &mdash; <code>lead_store_new()</code></td>
                            <td><code>mob_no</code></td>
                            <td><code>Lead::where('mob_no', $mob_no)-&gt;exists()</code> is checked. A new row is inserted regardless; if a match existed, the new row is flagged <code>is_repeted = 1</code>.</td>
                        </tr>
                        <tr>
                            <td>Update-mode submit (<code>firstRequest != '1'</code>)</td>
                            <td><code>whatsapp_no</code></td>
                            <td>Instead of mobile number, the WhatsApp number on the incoming request is compared against the existing lead's WhatsApp number to decide the repeat flag.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Fields -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">What Gets Recorded</h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li><code>is_repeted = 1</code> is set on the <strong>new</strong> lead row (not the old one) when a match is found.</li>
                <li><code>candidate_updated_at</code> is stamped on the new row if the original matching lead <em>wasn't created today</em> &mdash; this is what "repeat candidate" reporting is built on.</li>
                <li>The original/older lead row is left untouched by this check itself (aside from the pre-check endpoint resetting its <code>lead_status_text</code>).</li>
            </ul>

        </div>

    </div>

    <!-- Downstream effects -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Why the Repeat Flag Matters Downstream</h5>
        </div>

        <div class="card-body">

            <ol class="mb-0">
                <li>
                    <strong>Assignment is skipped at creation time.</strong><br>
                    The auto-assignment step in <a href="{{ route('docs.lead-module.creation') }}">Lead Creation</a> only runs for non-repeat leads. A repeat lead is created with <code>leadassign_id</code> still empty at that point.
                </li>

                <li class="mt-3">
                    <strong><code>leadAssignedProcess()</code> takes over.</strong><br>
                    For an update-mode submission of a repeat lead, this method checks whether the current owner has a <code>leadnotes</code> entry for the lead within the last <strong>7 days</strong>. If not, it reassigns the lead to a different random <em>active</em> admin (<code>status = 1</code> and <code>login_status = 1</code>) &mdash; independent of the daily <code>lead_assign_status</code> opt-in used elsewhere. See <a href="{{ route('docs.lead-module.notes') }}">Lead Notes</a> and <a href="{{ route('docs.lead-assignment') }}#reassignment">Lead Reassignment</a>.
                </li>

                <li class="mt-3">
                    <strong>It can still end up unassigned.</strong><br>
                    Since a fresh repeat lead (created via a first-time, non-update submit) skips both the creation-time assignment step and never reaches <code>leadAssignedProcess()</code> (which only checks an existing <code>leadassign_id</code>), it can sit unassigned until the <code>leads:assign-continuously</code> or <code>leads:assign-pending-night</code> cron picks it up. See <a href="{{ route('docs.lead-assignment') }}">Lead Assignment</a>.
                </li>
            </ol>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.lead-module.creation') }}" class="btn btn-outline-secondary btn-sm">&larr; Lead Creation</a>
        <a href="{{ route('docs.lead-assignment') }}" class="btn btn-outline-primary btn-sm">Lead Assignment &rarr;</a>
    </div>

</div>

@endsection
