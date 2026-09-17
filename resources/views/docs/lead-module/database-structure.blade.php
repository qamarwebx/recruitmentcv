@extends('layout.docs.docs_layout')

@section('title', 'Lead Module - Database Structure')

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
                Database Structure
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Database Structure
    </h2>

    <p class="text-muted mb-4">
        The tables behind every lead: the main <code>leads</code> table plus the smaller tables that store notes,
        activity history, and integration logs.
    </p>

    <!-- Leads table -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The <code>leads</code> Table</h5>
        </div>

        <div class="card-body">

            <p>
                Every lead is a single row in the <code>leads</code> table. Below are the fields that matter
                day-to-day (the full table also stores a few internal/rarely-used columns).
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Column</th>
                            <th>Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>cand_name</code></td><td>Candidate's name.</td></tr>
                        <tr><td><code>mob_no</code></td><td>Mobile number &mdash; the field used to detect duplicate/repeat leads. See <a href="{{ route('docs.lead-module.duplicate') }}">Duplicate Check</a>.</td></tr>
                        <tr><td><code>whatsapp_no</code>, <code>email</code></td><td>Additional contact details.</td></tr>
                        <tr><td><code>required_service</code>, <code>looking_for</code>, <code>no_of_requirement</code></td><td>What the candidate is applying for.</td></tr>
                        <tr><td><code>country</code>, <code>experience</code>, <code>saudi_license</code>, <code>india_license</code></td><td>Profile details used for filtering and Meta ad reporting.</td></tr>
                        <tr><td><code>lead_source</code></td><td>Where the lead came from (defaults to <code>www.qamrjob.com</code>).</td></tr>
                        <tr><td><code>lead_status_text</code></td><td>Simple text tag, e.g. <code>"New"</code>, <code>"Transfered"</code>.</td></tr>
                        <tr><td><code>leadassign_id</code></td><td>Foreign key to <code>admins.id</code> &mdash; the staff member currently owning this lead. <code>NULL</code> = unassigned. See <a href="{{ route('docs.lead-assignment') }}">Lead Assignment</a>.</td></tr>
                        <tr><td><code>is_qualified</code></td><td>Qualification status code, <code>1&ndash;5</code>. <code>NULL</code>/<code>0</code> means "Not Yet". See <a href="{{ route('docs.lead-module.qualification') }}">Qualification</a>.</td></tr>
                        <tr><td><code>qualified_reason</code></td><td>Free-text reason entered when a lead is qualified/disqualified.</td></tr>
                        <tr><td><code>is_repeted</code></td><td><code>1</code> if a lead with the same mobile number already existed when this one was submitted.</td></tr>
                        <tr><td><code>submit_lead_from</code></td><td>JSON blob capturing where the form was submitted from: <code>{from, ip, country, region, city}</code>. This is where IP/geo data actually lives &mdash; there are <strong>no separate <code>ip</code>/<code>city</code>/<code>state</code> columns</strong>.</td></tr>
                        <tr><td><code>lead_request_data</code></td><td>The full raw request payload (JSON) at submit time, kept for audit/replay.</td></tr>
                        <tr><td><code>meta_lead_sent</code>, <code>browser_meta_event</code></td><td>Meta Conversions API bookkeeping (see <a href="{{ route('docs.meta-api') }}">Meta Conversion API</a>).</td></tr>
                        <tr><td><code>lead_date</code></td><td>Business date/time of the lead (set on creation, used by assignment crons to define "today's leads").</td></tr>
                        <tr><td><code>staff_updated_at</code></td><td>Stamped every time staff adds a note or updates the lead &mdash; used to detect leads that haven't been followed up recently. See <a href="{{ route('docs.lead-module.followup') }}">Follow-up</a>.</td></tr>
                        <tr><td><code>candidate_updated_at</code></td><td>Set when a repeat lead resubmits on a different day than the original lead was created.</td></tr>
                        <tr><td><code>created_at</code>, <code>updated_at</code></td><td>Standard timestamps.</td></tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Related tables -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Related Tables</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Table</th>
                            <th>Purpose</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>leadnotes</code></td>
                            <td>One row per follow-up note/call: <code>lead_id</code>, <code>admin_id</code>, <code>notes</code>, <code>conversation_type</code>, <code>is_qualified</code>, <code>call_not_connected_type</code>. See <a href="{{ route('docs.lead-module.notes') }}">Lead Notes</a>.</td>
                        </tr>
                        <tr>
                            <td><code>lead_activity_logs</code></td>
                            <td>Full audit trail: <code>lead_id</code>, <code>admin_id</code>, <code>module</code>, <code>activity</code> (JSON). See <a href="{{ route('docs.lead-module.activity') }}">Activity Logs</a>.</td>
                        </tr>
                        <tr>
                            <td><code>lead_auto_assign_status</code></td>
                            <td>Single row, global on/off switch for auto-assignment.</td>
                        </tr>
                        <tr>
                            <td><code>meta_lead_logs</code></td>
                            <td>One row per Meta Conversion API send attempt (<code>success</code>/<code>failed</code>), with event id, page, and response trace.</td>
                        </tr>
                        <tr>
                            <td><code>leadstages</code></td>
                            <td>Pipeline stage names used when a qualified lead is converted into a Deal.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <!-- Model -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Model &mdash; <code>app/Models/Lead.php</code></h5>
        </div>

        <div class="card-body">

            <ul class="mb-0">
                <li><code>protected $guarded = [];</code> &mdash; every column is mass-assignable, there is no fillable whitelist.</li>
                <li><code>leadassign()</code> &mdash; <code>belongsTo(Admin::class, 'leadassign_id')</code>.</li>
                <li><code>notes()</code> &mdash; <code>hasMany(Leadnote::class, 'lead_id')</code>.</li>
                <li>A <code>booted()</code> model event automatically writes a <code>lead_assign_action</code> Activity Log entry whenever <code>leadassign_id</code> changes.</li>
                <li>Several query scopes power the CRM's list filters: <code>scopeFilterByAssignee</code>, <code>scopeFilterDateRange</code>, <code>scopeFilterSearchText</code>, <code>scopeFilterQualified</code>, <code>scopeFilterQualifiedStatus</code>, <code>scopeFilterDrivingLicense</code>, <code>scopeFilterCallNotConnectedType</code>, and <code>scopeFilterCustom</code> (a whitelisted dynamic filter builder for admin-defined filters).</li>
            </ul>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.lead-module') }}" class="btn btn-outline-secondary btn-sm">&larr; Overview</a>
        <a href="{{ route('docs.lead-module.creation') }}" class="btn btn-outline-primary btn-sm">Lead Creation &rarr;</a>
    </div>

</div>

@endsection
