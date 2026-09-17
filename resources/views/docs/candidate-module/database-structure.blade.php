@extends('layout.docs.docs_layout')

@section('title', 'Candidate Module - Database Structure')

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
                Database Structure
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Database Structure
    </h2>

    <p class="text-muted mb-4">
        The <code>candidates</code> table is large (around 90 columns) because it holds the candidate's entire
        profile in one place &mdash; identity, documents, job experience, medical history, and status. It's grouped
        below by purpose rather than as one giant list.
    </p>

    <div class="alert alert-warning">
        There is no single migration that creates <code>candidates</code> with its current shape &mdash; only a
        small original create migration plus dozens of later <code>add_*_to_candidates</code> alters. The columns
        below reflect the live schema, not any one migration file.
    </div>

    <!-- Identity -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Identity &amp; Personal</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>cand_name</code>, <code>arcand_name</code></td><td>Name in English and Arabic.</td></tr>
                        <tr><td><code>email</code></td><td>Candidate email.</td></tr>
                        <tr><td><code>dob</code>, <code>age</code></td><td>Date of birth and age.</td></tr>
                        <tr><td><code>marital_status</code>, <code>ar_marital_status</code></td><td>Marital status, English and Arabic.</td></tr>
                        <tr><td><code>religion</code>, <code>religion_id</code></td><td>Free-text religion plus a manual join to the <code>religions</code> lookup table (no DB foreign key).</td></tr>
                        <tr><td><code>nation_id</code></td><td>Nationality.</td></tr>
                        <tr><td><code>reference_no</code></td><td>Human-facing reference like <code>RF123</code>, generated sequentially on creation.</td></tr>
                        <tr><td><code>slug_text</code></td><td>Random public-facing slug (e.g. for a shareable CV link).</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Passport -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Passport &amp; Travel Documents</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>pass_no</code>, <code>pass_type</code></td><td>Passport number and type.</td></tr>
                        <tr><td><code>doi</code>, <code>doe</code></td><td>Date of issue / date of expiry.</td></tr>
                        <tr><td><code>poi</code>, <code>poi_text</code></td><td>Place of issue.</td></tr>
                        <tr><td><code>pass_file</code>, <code>pass_back_file</code></td><td>Uploaded passport front/back scans.</td></tr>
                        <tr><td><code>lic_file</code></td><td>Driving license file.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Contact -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Contact</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>contact_no</code>, <code>contact_no_dial_code</code></td><td>Primary contact number.</td></tr>
                        <tr><td><code>mobile_no</code>, <code>mobile_no_dial_code</code></td><td>Mobile number.</td></tr>
                        <tr><td><code>relative_contact_no</code>, <code>relative_contact_no_dial_code</code></td><td>Emergency/relative contact.</td></tr>
                        <tr><td><code>address</code>, <code>google_map</code></td><td>Address and map link.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Job -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Job &amp; Experience</h5></div>
        <div class="card-body">
            <p class="text-muted">
                Several of these columns store <strong>comma-separated id lists</strong> (CSV) rather than a normal
                relational join &mdash; e.g. a candidate with multiple past countries of work. Filters against them
                use SQL <code>FIND_IN_SET</code> instead of a join.
            </p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>job_type</code>, <code>jobtype_id</code></td><td>Desired job/profession (FK-style link to <code>professions</code>, no DB constraint).</td></tr>
                        <tr><td><code>proff_id</code></td><td>CSV of additional profession ids.</td></tr>
                        <tr><td><code>experience</code>, <code>overall_exp</code></td><td>Experience details and total years.</td></tr>
                        <tr><td><code>expr_country_name</code>, <code>expcountry_id</code></td><td>Country/countries of prior work experience (CSV).</td></tr>
                        <tr><td><code>expcity_id</code>, <code>expcity_id_text</code>, <code>expcity_text</code></td><td>Cities of prior work experience.</td></tr>
                        <tr><td><code>expwp_id</code>, <code>expwp_text</code></td><td>CSV of "work-place" ids, queried with <code>FIND_IN_SET</code>.</td></tr>
                        <tr><td><code>gulfexperience</code></td><td>Whether the candidate has Gulf-region work experience.</td></tr>
                        <tr><td><code>lang_known</code>, <code>ar_language</code></td><td>Languages known (CSV) and Arabic proficiency.</td></tr>
                        <tr><td><code>exp_sal</code></td><td>Expected salary (stored as text).</td></tr>
                        <tr><td><code>carknown_id</code>, <code>vehical_transmission</code></td><td>Vehicles the candidate can drive, and transmission type.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Location -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Location &amp; Origin</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>region_id</code></td><td>Region of origin.</td></tr>
                        <tr><td><code>candcity_id</code>, <code>candcity_text</code></td><td>City of origin.</td></tr>
                        <tr><td><code>plb_id</code>, <code>plb_text</code></td><td>Place of birth.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Files -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Files &amp; Media</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>cv_file</code></td><td>Uploaded/generated CV.</td></tr>
                        <tr><td><code>photo_file</code></td><td>Profile photo.</td></tr>
                        <tr><td><code>video_link</code>, <code>video_file</code></td><td>Introduction video (external link or uploaded file).</td></tr>
                        <tr><td><code>trade_test_video_link</code></td><td>Skill/trade test video link.</td></tr>
                        <tr><td><code>musaned_file</code></td><td>Musaned registration document (see Musaned below).</td></tr>
                        <tr><td><code>cv_execute</code>, <code>cv_execute_file</code>, <code>cv_executae_date</code></td><td>Whether a formatted CV PDF has been auto-generated, and where it lives. See <a href="{{ route('docs.candidate-module.documents') }}">Documents</a>.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Medical -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Medical</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>medical_expiry_date</code>, <code>medical_examine_date</code></td><td>Medical exam and expiry dates.</td></tr>
                        <tr><td><code>medical_examine_unfit</code>, <code>medical_examine_unfit_date</code></td><td>Failed-exam flag and date.</td></tr>
                        <tr><td><code>repeat_examine_date</code>, <code>on_medical_date</code>, <code>waiting_medical</code></td><td>Re-examination scheduling.</td></tr>
                        <tr><td><code>medical_health_status</code></td><td>Current medical status label.</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="mb-0 mt-2">Full medical history is kept separately in the <code>candmedicalhistories</code> table (see Related Tables below), not just these summary columns.</p>
        </div>
    </div>

    <!-- Musaned -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Musaned (Saudi Domestic-Worker System)</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>musaned_status</code>, <code>musaned_reg_date</code>, <code>musaned_file</code></td><td>Registration status/date/document for Saudi Arabia's Musaned platform.</td></tr>
                        <tr><td><code>mofa_no</code></td><td>MOFA (Saudi Ministry of Foreign Affairs) reference number.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Ownership -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Ownership &amp; Office</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>admin_id</code></td><td>Staff member who created the candidate profile.</td></tr>
                        <tr><td><code>careoff_id</code></td><td>Care-of office/branch. This is the column actually used everywhere &mdash; see the note in <a href="{{ route('docs.candidate-module') }}">Overview</a> about the model's <code>careoff()</code> relationship.</td></tr>
                        <tr><td><code>associate_id</code></td><td>Associate/agent who sourced the candidate.</td></tr>
                        <tr><td><code>associateconfirmby_id</code>, <code>associateconfirmby_status</code>, <code>associate_confirm</code></td><td>Confirmation workflow for the sourcing associate.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status -->
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0">Status &amp; Lifecycle</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>status</code></td><td>Generic active/inactive flag.</td></tr>
                        <tr><td><code>verified</code></td><td>Verification flag.</td></tr>
                        <tr><td><code>publish</code></td><td>Whether the candidate is publicly published/selectable. Driven by the publish wizard &mdash; see <a href="{{ route('docs.candidate-module.publish') }}">Publish Wizard</a>.</td></tr>
                        <tr><td><code>isdelete</code></td><td>Manual soft-delete-style flag (<code>where('isdelete', 0)</code> everywhere) &mdash; <strong>not</strong> Laravel's real <code>SoftDeletes</code>. The actual delete action hard-deletes the row.</td></tr>
                        <tr><td><code>cand_status</code></td><td>Foreign key to the <code>candidate_statuses</code> lookup table (with a real DB constraint, <code>ON DELETE CASCADE</code>) &mdash; a largely legacy/parallel status taxonomy. See <a href="{{ route('docs.candidate-module.status') }}">Status &amp; Deployment Pipeline</a>.</td></tr>
                        <tr><td><code>order_status</code></td><td>Foreign key to <code>order_statuses</code> (real DB constraint).</td></tr>
                        <tr><td><code>candidate_current_status</code></td><td>The actively-used, human-readable deployment pipeline status (e.g. "New Candidate", "Deployed"). See <a href="{{ route('docs.candidate-module.status') }}">Status &amp; Deployment Pipeline</a>.</td></tr>
                        <tr><td><code>cand_payment_status</code></td><td>"Paid" / "Partial Paid", computed from payments. See <a href="{{ route('docs.candidate-module.transactions') }}">Transactions &amp; Finance</a>.</td></tr>
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
                    <thead><tr><th>Table</th><th>Key columns</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>candidatefiles</code></td><td><code>cand_id, label, filename, admin_id</code></td><td>Free-form, unlimited labeled document attachments. See <a href="{{ route('docs.candidate-module.documents') }}">Documents</a>.</td></tr>
                        <tr><td><code>candidate_statuses</code></td><td><code>status, priority</code></td><td>Lookup list referenced by <code>candidates.cand_status</code>.</td></tr>
                        <tr><td><code>cand_statuses</code></td><td>15 boolean flags (one per pipeline stage) + <code>cand_id</code></td><td>The actual deployment-pipeline tracker, one row per candidate.</td></tr>
                        <tr><td><code>candpubsts</code></td><td><code>cand_id, passport_st, skill_exp_st, document_st, publish, stage_name</code></td><td>Publish-wizard stage tracker, one row per candidate.</td></tr>
                        <tr><td><code>activities</code></td><td><code>cand_id, headline, bodyMessage, icons, admin_id, activity_type</code></td><td>Candidate timeline log. See <a href="{{ route('docs.candidate-module.activity') }}">Activity Log</a>.</td></tr>
                        <tr><td><code>candmedicalhistories</code></td><td><code>cand_id, medical_status, medical_examination_date, notes, staff_id</code></td><td>Full medical exam history.</td></tr>
                        <tr><td><code>paymentcands</code></td><td><code>cand_id, amount, payment_mode, txn_id, status</code></td><td>Payments received against a candidate. See <a href="{{ route('docs.candidate-module.transactions') }}">Transactions &amp; Finance</a>.</td></tr>
                        <tr><td><code>candsercharges</code></td><td><code>cand_id, amount, status, given_by</code></td><td>Service-charge (fee) owed per candidate; only one row is "active" at a time.</td></tr>
                        <tr><td><code>candidatebackupreferences</code></td><td><code>reference_no</code></td><td>Pool of reusable/recycled reference numbers.</td></tr>
                        <tr><td><code>candidatebookinglimits</code></td><td><code>cand_id, limit, admin_id</code></td><td>Per-candidate booking/limit setting.</td></tr>
                        <tr><td><code>candidatereminders</code></td><td><code>candidate_id, title, due_date, careoff_id</code></td><td>Candidate-specific reminders/todos.</td></tr>
                        <tr><td><code>employercandidates</code></td><td><code>emp_id, cand_id, proff_id, status</code></td><td>Assignment of a candidate to an employer job order.</td></tr>
                        <tr><td><code>candidateadminsavefilters</code>, <code>candidatefilterlists</code></td><td>~15&ndash;17 filter columns each</td><td>Per-admin saved filter preferences for the candidate list screen.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Model -->
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Model &mdash; <code>app/Models/Candidate.php</code></h5></div>
        <div class="card-body">
            <ul class="mb-0">
                <li>No <code>$fillable</code>/<code>$guarded</code> declared, and no casts. In practice the controller never mass-assigns &mdash; it always does <code>$c = new Candidate(); $c->field = ...; $c->save();</code>.</li>
                <li>Relationships are all <code>belongsTo</code>: <code>admin()</code>, <code>profession()</code> (<code>jobtype_id</code>), <code>religion()</code>, <code>careoff()</code>, <code>associate()</code>, <code>associateconfirmby()</code>.</li>
                <li>None of the related tables above (<code>candidatefiles</code>, <code>paymentcands</code>, <code>activities</code>, etc.) are modeled as Eloquent relationships &mdash; the controller queries them with plain <code>Model::where('cand_id', $id)</code> calls instead of <code>$candidate->files</code>-style access.</li>
                <li>Query scopes power the candidate list/filter screen: <code>scopeFilterPassType</code>, <code>scopeFilterExpSal</code>, <code>scopeFilterJobType</code>, <code>scopeFilterCareoff</code>, <code>scopeFilterCreateBy</code>, <code>scopeFilterReligion</code>, <code>scopeFilterCity</code>, <code>scopeFilterRegion</code>, <code>scopeFilterExperienceRegion</code>, <code>scopeFilterPublish</code>, <code>scopeFilterCandStatus</code>, <code>scopeFilterMedicalStatus</code>, <code>scopeFilterPaymentStatus</code>, <code>scopeFilterExpwp</code>, <code>scopeFilterDate</code>, <code>scopeFilterDateRange</code>, <code>scopeFilterSearchText</code>.</li>
            </ul>
        </div>
    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.candidate-module') }}" class="btn btn-outline-secondary btn-sm">&larr; Overview</a>
        <a href="{{ route('docs.candidate-module.creation') }}" class="btn btn-outline-primary btn-sm">Candidate Creation &rarr;</a>
    </div>

</div>

@endsection
