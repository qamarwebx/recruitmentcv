@extends('layout.docs.docs_layout')

@section('title', 'Contacts Module - Database Structure')

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
                Database Structure
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Database Structure
    </h2>

    <p class="text-muted mb-4">
        Sidebar label "Contacts" maps to routes <code>admin.allcontact.*</code>, model
        <code>App\Models\Allcontact</code>, table <code>allcontacts</code>.
    </p>

    <div class="alert alert-warning">
        <strong>Don't confuse this with "Contact Plus".</strong> The sidebar has a separate module literally called
        "Contact Plus" (routes <code>admin.contact.*</code>, table <code>contactplus</code>) which is a different
        module entirely. This page is only about <strong>Contacts</strong> (<code>allcontacts</code>).
    </div>

    <!-- Identity -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Identity &amp; Classification</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>full_name</code>, <code>company_name</code>, <code>job_title</code>, <code>job_desg</code>, <code>office_name</code></td><td>Contact and company identity.</td></tr>
                        <tr><td><code>lead_type</code></td><td>Business-type tag, filterable.</td></tr>
                        <tr><td><code>regarding</code>, <code>descr</code></td><td>Free-text context/description.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Contact -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Contact Numbers &amp; Email</h5></div>
        <div class="card-body">
            <p class="text-muted">
                Every WhatsApp-capable number is stored with a paired <code>*_dial_code</code> column, and there are
                <strong>three</strong> generic mobile slots plus primary/secondary &mdash; useful for contacts with
                multiple numbers on file.
            </p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>primary_no_wsp</code> + <code>primary_no_wsp_dial_code</code></td><td>Primary WhatsApp number. This is the field the <a href="{{ route('docs.contacts-module.lead-sync') }}">Lead Sync</a> and <a href="{{ route('docs.contacts-module.google-sync') }}">Google Sync</a> both key off.</td></tr>
                        <tr><td><code>secondary_no_wsp</code> + dial code</td><td>Secondary number.</td></tr>
                        <tr><td><code>mobile_no1_wsp</code>/<code>2</code>/<code>3</code> + dial codes</td><td>Up to 3 more numbers.</td></tr>
                        <tr><td><code>send_whatsapp</code></td><td>Opt-in flag checked at creation time &mdash; triggers an automatic WhatsApp message. See <a href="{{ route('docs.contacts-module.creation') }}">Contact Creation</a>.</td></tr>
                        <tr><td><code>email</code>, <code>email0</code>, <code>email1</code>, <code>email2</code></td><td>Four email slots. Export code resolves the first non-empty one in that priority order.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Location -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Location</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>country_id</code></td><td>Country (int).</td></tr>
                        <tr><td><code>state_id</code>, <code>city_id</code></td><td>Region/city. Oddly typed as <code>text</code> rather than int, but joins to <code>Region</code>/<code>City</code> still work because MySQL coerces the comparison.</td></tr>
                        <tr><td><code>full_address</code></td><td>Free-text address.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Ownership -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Ownership, Classification &amp; Linkage</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>assign_id</code>, <code>user_id</code>, <code>owner_id</code>, <code>careoff_id</code></td><td>All FK-style links to <code>admins</code> &mdash; creator (<code>user_id</code>), owner, assignee, and care-of office, each via its own relationship on the model.</td></tr>
                        <tr><td><code>lcs_id</code></td><td>Lifecycle Status &mdash; see <a href="{{ route('docs.contacts-module.lifecycle') }}">Lifecycle Status</a>. Not shared with Leads.</td></tr>
                        <tr><td><code>ls_id</code></td><td>Lead Stage.</td></tr>
                        <tr><td><code>ct_id</code>, <code>indust_id</code></td><td>Industry classification.</td></tr>
                        <tr><td><code>expectedc_id</code></td><td>Expected-something classification (limited usage).</td></tr>
                        <tr><td><code>group_id</code></td><td>Contact Group, stored as a string. See <a href="{{ route('docs.contacts-module.groups') }}">Groups</a>.</td></tr>
                        <tr><td><code>source</code>, <code>source_id</code></td><td>Free-text source plus a lookup FK to <code>allcontactsources</code>.</td></tr>
                        <tr><td><code>lead_prority</code> <em>(sic)</em>, <code>lead_id</code>, <code>lead_msg</code></td><td>A direct pointer to a lead &mdash; present in the schema but effectively legacy/unused. The real Contacts&harr;Leads cross-reference is the separate <code>sync_allcontact_to_lead</code> table, see <a href="{{ route('docs.contacts-module.lead-sync') }}">Lead Sync</a>.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Social -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Social &amp; Misc</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>fb_link</code>, <code>tw_link</code>, <code>linkedin_link</code>, <code>skype</code>, <code>googleplus</code>, <code>tradesitecenter</code></td><td>Social/contact profile links.</td></tr>
                        <tr><td><code>profile_pic</code></td><td>Profile photo.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Status &amp; Flags</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>public_st</code>, <code>status</code></td><td>Visibility/active flags.</td></tr>
                        <tr><td><code>contact_dl_status</code>, <code>deleteby_id</code></td><td>Present in the schema for a soft-delete pattern, but <strong>not actually wired into the delete flow</strong> &mdash; deletes are real hard deletes. <code>deleteby_id</code> is only read for display purposes elsewhere.</td></tr>
                        <tr><td><code>optinout</code>, <code>asperoptin</code></td><td>Communication opt-in/out status.</td></tr>
                        <tr><td><code>ai_all_status</code>, <code>ai_call_response</code></td><td>AI-calling feature bookkeeping. <code>ai_call_response</code> is cast to an array on the model.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Dates -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Dates</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>created_at</code>, <code>updated_at</code></td><td>Standard timestamps.</td></tr>
                        <tr><td><code>staff_updated_date</code></td><td>Set automatically by a model event on every update &mdash; a distinct "last touched by staff" timestamp, separately filterable from <code>updated_at</code>. See <a href="{{ route('docs.contacts-module.filters') }}">Filters &amp; Saved Views</a>.</td></tr>
                        <tr><td><code>dum_date</code>, <code>followup_date</code></td><td>Scheduling dates.</td></tr>
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
                        <tr><td><code>allcontactnotes</code></td><td>Conversation/notes log per contact.</td></tr>
                        <tr><td><code>allcontactreminders</code></td><td>Reminders per contact.</td></tr>
                        <tr><td><code>allcontact_files</code></td><td>Uploaded file attachments.</td></tr>
                        <tr><td><code>allcontactsources</code></td><td>Lookup list of "Source" values.</td></tr>
                        <tr><td><code>groupallcs</code></td><td>Contact Groups. See <a href="{{ route('docs.contacts-module.groups') }}">Groups</a>.</td></tr>
                        <tr><td><code>lifecyclestatuses</code></td><td>Lifecycle Status master list, shared with Contact Plus. See <a href="{{ route('docs.contacts-module.lifecycle') }}">Lifecycle Status</a>.</td></tr>
                        <tr><td><code>allcontactadminsavefilters</code></td><td>Per-admin saved filter preferences. See <a href="{{ route('docs.contacts-module.filters') }}">Filters &amp; Saved Views</a>.</td></tr>
                        <tr><td><code>allcontactsendwhatsapps</code></td><td>Scheduled/queued bulk WhatsApp sends targeting contacts.</td></tr>
                        <tr><td><code>sync_allcontact_to_lead</code></td><td>Contacts&harr;Leads cross-reference bridge table. See <a href="{{ route('docs.contacts-module.lead-sync') }}">Lead Sync</a>.</td></tr>
                        <tr><td><code>export_all_contact_histories</code></td><td>CSV export job history. See <a href="{{ route('docs.contacts-module.export') }}">Export &amp; History</a>.</td></tr>
                        <tr><td><code>export_all_contact_email_portal_histories</code></td><td>External email-portal subscriber export history. See <a href="{{ route('docs.contacts-module.export') }}">Export &amp; History</a>.</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="mt-3 mb-0 text-muted">
                As with <code>leads</code> and <code>candidates</code>, several of these tables (notably
                <code>sync_allcontact_to_lead</code>) have no matching Laravel migration &mdash; they exist in the
                live database ahead of the migration history.
            </p>
        </div>
    </div>

    <!-- Model -->
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Model &mdash; <code>app/Models/Allcontact.php</code></h5></div>
        <div class="card-body">
            <ul class="mb-0">
                <li><code>protected $guarded = [];</code> and <code>'ai_call_response' =&gt; 'array'</code> cast.</li>
                <li>Relationships: <code>owner()</code>, <code>assign()</code>, <code>user()</code>, <code>deleteby()</code>, <code>careoff()</code> (all <code>belongsTo(Admin::class)</code> via their respective FK columns), <code>state()</code> &rarr; <code>Region</code>, <code>city()</code> &rarr; <code>City</code>, <code>country()</code> &rarr; <code>Country</code>, <code>lcs()</code> &rarr; <code>Lifecyclestatus</code>, <code>ls()</code> &rarr; <code>Leadstage</code>, <code>indust()</code> &rarr; <code>Industry</code>, <code>group()</code> &rarr; <code>Groupallc</code>, <code>notes()</code> &rarr; <code>hasMany(Allcontactnote::class, 'allcontact_id')</code>.</li>
                <li>Model event: a <code>static::updating(...)</code> hook stamps <code>staff_updated_date = now()</code> on every update.</li>
                <li>Many <code>scopeFilterX</code> query scopes power the list/filter screen &mdash; see <a href="{{ route('docs.contacts-module.filters') }}">Filters &amp; Saved Views</a> for the full list, including a generic <code>FilterCustom</code> builder worth reading the caveat on.</li>
            </ul>
        </div>
    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.contacts-module') }}" class="btn btn-outline-secondary btn-sm">&larr; Overview</a>
        <a href="{{ route('docs.contacts-module.creation') }}" class="btn btn-outline-primary btn-sm">Contact Creation &rarr;</a>
    </div>

</div>

@endsection
