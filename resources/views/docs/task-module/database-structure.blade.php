@extends('layout.docs.docs_layout')

@section('title', 'Task Module - Database Structure')

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
                Database Structure
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Database Structure
    </h2>

    <p class="text-muted mb-4">
        Sidebar label "Task" maps to routes <code>admin.todo.*</code>, model <code>App\Models\Todo</code>, table
        <code>todos</code>.
    </p>

    <div class="alert alert-warning">
        <strong>Don't confuse this with <code>App\AdminModel\Todo</code>.</strong> A completely separate, legacy
        model of the same name points at a table (<code>qr_todo</code>) that no longer exists in the live database.
        It's still referenced by ~15 old, unrouted controllers/commands, but has nothing to do with the real Task
        module documented here. If you ever see <code>use App\AdminModel\Todo;</code> in code, it's dead legacy
        code, not this module.
    </div>

    <!-- Identity -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Identity &amp; Content</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>task_title</code>, <code>task_description</code></td><td>The task itself.</td></tr>
                        <tr><td><code>staff_work_name</code></td><td>JSON array &mdash; an ad hoc "who actually did the physical work" log, managed via <code>addStaffWork()</code>/<code>deleteStaffWork()</code>, separate from assignment.</td></tr>
                        <tr><td><code>file_attachment</code></td><td>Optional attached file.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Assignment -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Assignment &amp; Categorization</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>assignto_id</code></td><td><strong>Comma-separated Admin ids</strong> (text, not a real FK) &mdash; supports assigning one task to multiple staff. Queried with <code>FIND_IN_SET</code>. Defaults to the creator if left blank. See <a href="{{ route('docs.task-module.assignment') }}">Assignment &amp; Permissions</a>.</td></tr>
                        <tr><td><code>admin_id</code></td><td>Creator/owner (single, required, real FK-style int).</td></tr>
                        <tr><td><code>todolabel_id</code></td><td>Single FK to <code>todolabels</code> &mdash; a tag/category. See <a href="{{ route('docs.task-module.labels') }}">Labels &amp; Departments</a>.</td></tr>
                        <tr><td><code>todostatus_id</code></td><td>FK to <code>todostatuses</code> &mdash; present in the schema but <strong>effectively dead</strong>. See <a href="{{ route('docs.task-module.creation') }}">Task Creation &amp; Status Workflow</a>.</td></tr>
                        <tr><td><code>department_id</code></td><td>Single FK to <code>departments</code> &mdash; a categorization/filter dimension, not an auto-fan-out assignment target.</td></tr>
                        <tr><td><code>type</code></td><td>Free text: <code>Once</code>/<code>Always</code>/<code>Regular</code> (defaults to <code>Regular</code>).</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scheduling -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Scheduling &amp; Reminder Engine</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>start_on</code>, <code>finish_on</code></td><td>Task date window.</td></tr>
                        <tr><td><code>complete_date</code>, <code>achieved_date</code></td><td>Stamped when the task reaches those respective statuses.</td></tr>
                        <tr><td><code>reminder_cycle</code></td><td>Priority: Low/Medium/High/Urgent (free text, not FK-driven).</td></tr>
                        <tr><td><code>reminder_type</code></td><td><code>OneTime</code>/<code>Recurring</code>/<code>Custom</code>/<code>Periodic</code>/<code>None</code> &mdash; drives which reminder engine applies. See <a href="{{ route('docs.task-module.recurring') }}">Recurring Tasks &amp; Scheduling</a>.</td></tr>
                        <tr><td><code>scheduled_date_time</code>, <code>scheduled_status</code></td><td>Used by the <code>OneTime</code> mode &mdash; <code>scheduled_status</code> auto-flips off after the reminder fires once.</td></tr>
                        <tr><td><code>reminder_before</code></td><td>"Minutes before task to trigger reminder."</td></tr>
                        <tr><td><code>reminder_at</code></td><td>"Exact datetime to send reminder" &mdash; used by the snooze/reschedule feature. See <a href="{{ route('docs.task-module.reminders') }}">Reminder Delivery &amp; Cron Jobs</a>.</td></tr>
                        <tr><td><code>notification_type</code></td><td>JSON array, cast on the model &mdash; e.g. <code>["email","whatsapp"]</code>.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recurrence -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Recurrence</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>recurring_type</code></td><td>Daily/Weekly/Monthly/Yearly.</td></tr>
                        <tr><td><code>recurring_time</code></td><td>JSON array of times (supports multiple times/day for Daily).</td></tr>
                        <tr><td><code>recurring_weekdays</code></td><td>JSON array of weekday names, for Weekly.</td></tr>
                        <tr><td><code>recurring_month_day</code></td><td>Day-of-month, for Monthly.</td></tr>
                        <tr><td><code>recurring_year_month_day</code></td><td><code>MM-DD</code>, for Yearly.</td></tr>
                        <tr><td><code>custom_start_date</code>, <code>custom_end_date</code>, <code>custom_time</code></td><td>Date-range window with a single daily time, for <code>Custom</code> mode.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Support team -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Support-Team Fields</h5></div>
        <div class="card-body">
            <p class="mb-0">
                <code>support_team_name</code>, <code>support_team_number</code> &mdash; a secondary WhatsApp-only
                recipient <em>outside</em> the admin/staff <code>assignto_id</code> list, notified alongside the
                regular assignees.
            </p>
        </div>
    </div>

    <!-- Status/workflow -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Status &amp; Workflow</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>status</code></td><td>Soft on/off flag (1 = active).</td></tr>
                        <tr><td><code>task_status</code></td><td>The real workflow stage, as free text (not FK-driven despite <code>todostatus_id</code> existing). See <a href="{{ route('docs.task-module.creation') }}">Task Creation &amp; Status Workflow</a>.</td></tr>
                        <tr><td><code>is_completed</code></td><td>Boolean, set alongside <code>task_status</code> transitions.</td></tr>
                        <tr><td><code>sort_order</code></td><td>Kanban ordering, reindexed per status column.</td></tr>
                        <tr><td><code>view_type</code></td><td>0 = List, 1 = Kanban &mdash; per-admin persisted view preference.</td></tr>
                    </tbody>
                </table>
            </div>
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
                        <tr><td><code>todolabels</code></td><td>Label/tag lookup list. See <a href="{{ route('docs.task-module.labels') }}">Labels &amp; Departments</a>.</td></tr>
                        <tr><td><code>departments</code></td><td>Department lookup list.</td></tr>
                        <tr><td><code>todostatuses</code></td><td><strong>0 rows, effectively dead.</strong> An earlier "configurable status" design, abandoned in favor of hardcoded <code>task_status</code> strings, but the table/model were left behind.</td></tr>
                        <tr><td><code>todoactivities</code></td><td>Free-text activity/audit log &mdash; one row appended on create/update (<code>todo_id</code>, <code>message</code>, <code>admin_id</code>).</td></tr>
                        <tr><td><code>todo_notes</code></td><td>Notes on a task (<code>todo_id</code>, <code>created_by</code>, <code>notes</code>).</td></tr>
                        <tr><td><code>todoreminderresponses</code></td><td>Delivery log for every reminder attempt (WhatsApp or email), one row per send. See <a href="{{ route('docs.task-module.reminders') }}">Reminder Delivery &amp; Cron Jobs</a>.</td></tr>
                        <tr><td><code>todoadminsavefilters</code></td><td>Per-admin persisted filter/view state. See <a href="{{ route('docs.task-module.filters') }}">Filters &amp; Saved Views</a>.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Model -->
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Model &mdash; <code>app/Models/Todo.php</code></h5></div>
        <div class="card-body">
            <ul class="mb-0">
                <li>Cast: <code>notification_type =&gt; array</code> (the only cast defined).</li>
                <li>Relations: <code>admin()</code>, <code>todolabel()</code>, <code>department()</code> &mdash; all <code>belongsTo</code>.</li>
                <li><strong>Known bug:</strong> <code>todoNotes()</code> is declared as <code>hasMany(TodoNote::class, 'deal_id')</code> &mdash; the real FK column on <code>todo_notes</code> is <code>todo_id</code>, not <code>deal_id</code> (apparently copy-pasted from the Deal Pipeline module). This relation is unused elsewhere in the app (<code>TodoNoteController</code> queries <code>todo_id</code> directly), so it's latent/dead rather than actively broken behavior — but calling <code>$todo-&gt;todoNotes</code> anywhere would silently return nothing useful.</li>
                <li>No model events/observers &mdash; activity-log entries and <code>sort_order</code> reindexing are all handled manually in the controller, not automatically.</li>
                <li>Other Todo-family models (<code>Todolabel</code>, <code>Department</code>, <code>Todostatus</code>, <code>Todoactivity</code>, <code>Todoreminderresponse</code>, <code>Todoadminsavefilter</code>) are all bare models with no relations/casts &mdash; cross-table joins are done via raw <code>DB::table()</code> queries in the controller.</li>
            </ul>
        </div>
    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.task-module') }}" class="btn btn-outline-secondary btn-sm">&larr; Overview</a>
        <a href="{{ route('docs.task-module.creation') }}" class="btn btn-outline-primary btn-sm">Task Creation &amp; Status Workflow &rarr;</a>
    </div>

</div>

@endsection
