@extends('layout.docs.docs_layout')

@section('title', 'Associate Module')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <div class="mb-3">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('docs.index') }}">
                        Documentation
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    Associate Module
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-4">

        <h2 class="fw-bold">
            Associate Module
        </h2>

        <p class="text-muted">
            This document covers the Associate module (sidebar label "Associate", routes
            <code>admin.associate.*</code>, table <code>associates</code>): what an associate is, how one is
            created and verified, and — on the
            <a href="{{ route('docs.associate-module.verification') }}">Verification &amp; Cross-Module Linkage</a>
            page — how associates plug into Candidates and Deal Pipeline.
        </p>

    </div>

    <!-- What it is -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">What an Associate Is</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                An <strong>Associate</strong> is a sourcing agency/sub-agent — an external party who brings in
                candidates or deals — tracked purely as an <strong>internal admin-managed record</strong>. There is
                no associate login/portal: only three auth guards exist in this app (<code>web</code>,
                <code>admin</code>, <code>partner</code>) — no <code>associate</code> guard — so associates never
                log in themselves. Everything about them is created and edited by staff through the admin panel.
            </p>

        </div>

    </div>

    <!-- Database -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Database &mdash; the <code>associates</code> Table</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>pty_full_name</code>, <code>pty_ag_name</code></td><td>Person name and agency name &mdash; an associate is typically an agency/sub-agent, not just an individual.</td></tr>
                        <tr><td><code>logo</code></td><td>Agency logo.</td></tr>
                        <tr><td><code>pty_email</code>, <code>pty_mobile</code>, <code>sec_mob_no</code></td><td>Contact details &mdash; primary and secondary mobile.</td></tr>
                        <tr><td><code>pty_mobile_verifed</code>, <code>sec_mob_no_verified</code>, <code>pty_email_verified</code>, <code>contact_verified</code></td><td>OTP-verification flags. See <a href="{{ route('docs.associate-module.verification') }}">Verification &amp; Cross-Module Linkage</a>.</td></tr>
                        <tr><td><code>address</code>, <code>city_id</code>, <code>country_id</code>, <code>region_id</code></td><td>Location.</td></tr>
                        <tr><td><code>careoff_id</code></td><td>Referring/care-of staff member (FK-by-convention &rarr; <code>admins</code>).</td></tr>
                        <tr><td><code>admin_id</code></td><td>Creator/owner staff member.</td></tr>
                        <tr><td><code>status</code></td><td>Active/inactive toggle.</td></tr>
                        <tr><td><code>membership</code></td><td>A manually-allocated membership number, starting at 1000 &mdash; separate sequence from the primary key.</td></tr>
                        <tr><td><code>created_at</code>, <code>updated_at</code></td><td>Standard timestamps.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-secondary mt-3 mb-0">
                <strong>Don't confuse with <code>App\AdminModel\Associate</code>.</strong> A separate, dead legacy
                model of a similar name points at a different table (<code>qr_associate</code>) and is never
                referenced by any live controller or route. The real model used everywhere is
                <code>App\Models\Associates</code> (plural).
            </div>

        </div>

    </div>

    <!-- Model -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Model &mdash; <code>app/Models/Associates.php</code></h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Plain model, no <code>$fillable</code>/<code>$guarded</code> override, no casts, no model events.
                Relationships: <code>careoff()</code> and <code>admin()</code> (both <code>belongsTo(Admin::class)</code>,
                via <code>careoff_id</code> and <code>admin_id</code> respectively), plus <code>city()</code>,
                <code>country()</code>, <code>region()</code>. One scope: <code>scopeFilterSearchText()</code>
                (searches name, agency name, email, mobile, address, plus related admin/location names).
            </p>

        </div>

    </div>

    <!-- Creation & membership -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Creation &amp; Membership Numbering</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>store()</code> sets <code>admin_id</code> to the logged-in admin, then auto-allocates a
                <code>membership</code> number: finds the current highest value and increments it (starting at
                1000 if none exist) &mdash; a manual sequence maintained in application code, not a real
                <code>AUTO_INCREMENT</code> column. <code>generate_memberid($id)</code> can (re)allocate this after
                the fact if needed.
            </p>

        </div>

    </div>

    <!-- Permissions & oddity -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Permissions, and a Hardcoded Exclusion</h5>
        </div>

        <div class="card-body">

            <p>
                <code>index()</code> permission gating: full-access admins (<code>user_type == 1</code> or
                <code>full_access == 1</code>) see every associate; staff with the <code>view_associate</code>
                permission see all; everyone else is restricted to associates they personally created
                (<code>admin_id == $userID</code>).
            </p>

            <div class="alert alert-warning mb-0">
                <strong>Every associate list query hardcodes <code>where('id', '!=', 73)</code>.</strong> Record 73
                is permanently excluded from every associate list and every dropdown across the app &mdash; almost
                certainly a dummy/placeholder row (it's the same id
                <a href="{{ route('docs.deal-pipeline-module.relationships') }}">hardcoded as the default associate</a>
                on auto-created Deal Pipeline records from Lead qualification). It still exists in the database and
                can still be referenced by FK-by-convention from candidates/deals, it's just invisible in the UI.
            </div>

        </div>

    </div>

    <!-- Routes -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Routes</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Route</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>admin.associate</code></td><td>List.</td></tr>
                        <tr><td><code>admin.associate.store</code>, <code>.update</code>, <code>.delete</code>, <code>.show</code></td><td>Core CRUD.</td></tr>
                        <tr><td><code>admin.associate.activeStatus</code> / <code>.deactiveStatus</code></td><td>Toggle active status.</td></tr>
                        <tr><td><code>admin.associate.primmobverif</code>, <code>.secmobverif</code>, <code>.emailverif</code> (+ their <code>_getotp</code>/<code>_otp_validate</code> pairs)</td><td>The OTP verification trio. See <a href="{{ route('docs.associate-module.verification') }}">Verification &amp; Cross-Module Linkage</a>.</td></tr>
                        <tr><td><code>admin.associate.generate_memberid</code></td><td>(Re)allocate the membership number.</td></tr>
                        <tr><td><code>admin.associate.show.verified</code> / <code>.notverified</code>, <code>.show.active</code> / <code>.inactive</code></td><td>Manual force-toggles for the verification/status flags.</td></tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 mb-0 text-muted">
                No cron jobs are associated with this module.
            </p>

        </div>

    </div>

</div>

@endsection
