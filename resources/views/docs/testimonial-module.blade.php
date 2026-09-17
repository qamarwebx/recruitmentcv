@extends('layout.docs.docs_layout')

@section('title', 'Testimonial Module')

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
                    Testimonial Module
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-4">

        <h2 class="fw-bold">
            Testimonial Module
        </h2>

        <p class="text-muted">
            This document covers the Testimonial module (sidebar label "Testimonial", routes
            <code>admin.testimonial.*</code> / <code>admin.testimonials.*</code>, table
            <code>testimonials</code>) — a much smaller module than the others documented so far, so it gets one
            page instead of a multi-page series.
        </p>

    </div>

    <div class="alert alert-warning">
        <strong>There is no "publish" workflow.</strong> Despite the sidebar checking for an
        <code>admin.testimonial.publish</code> route (leftover/copy-pasted from the Candidate module's sidebar item
        directly above it), that route <strong>does not exist</strong>. This module has no publish concept at all.
    </div>

    <!-- What it is -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">What This Module Actually Is</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                Not a candidate-linked "success story" feature, and not a public-website display feature (no
                rendering of testimonials was found anywhere in this codebase — qamrjob.com's public front end
                lives in a separate repo). This is purely a <strong>self-service video-collection tool</strong>:
                staff generate a shareable upload link, send it to someone (typically a candidate) outside the
                system, and that person uploads a video through a public, unauthenticated form. There's no FK or
                Eloquent relationship to <code>candidates</code>, <code>employers</code>, or <code>leads</code> at
                all.
            </p>

        </div>

    </div>

    <!-- Database -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Database &mdash; the <code>testimonials</code> Table</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Column</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>Primary key.</td></tr>
                        <tr><td><code>full_name</code></td><td>Name of the person giving the testimonial &mdash; filled in by them via the public upload form, or copied from the staff member's own name when the link is first generated.</td></tr>
                        <tr><td><code>passport_number</code></td><td>Free-text identifier, editable by admin/staff only &mdash; not present on the public upload form.</td></tr>
                        <tr><td><code>uploaded_video</code></td><td>Despite the singular name, stores a <strong>JSON array</strong> of uploaded video filenames, appended to on every upload. Not cast on the model &mdash; manually <code>json_decode</code>/<code>json_encode</code>d in the controller and views.</td></tr>
                        <tr><td><code>uploading_video_max_limit</code></td><td><strong>Dead column.</strong> Defined in the schema (int, default 0) but never referenced anywhere in the controller, model, or views.</td></tr>
                        <tr><td><code>link</code></td><td>The generated public upload URL: <code>url('/testimonials/link/' . Str::random(8))</code>.</td></tr>
                        <tr><td><code>created_by</code></td><td>The <strong>staff/admin user</strong> who generated the link &mdash; not the candidate. There is effectively one row per creating staff user, reused across multiple video uploads on that same link.</td></tr>
                        <tr><td><code>created_at</code>, <code>updated_at</code></td><td>Standard timestamps.</td></tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-3 mb-0 text-muted">
                Live schema matches the migration exactly — no drift, unlike several other tables documented
                elsewhere in this CRM.
            </p>

        </div>

    </div>

    <!-- Model -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Model &mdash; <code>app/Models/Testimonial.php</code></h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>$fillable</code> covers all six data columns. One relationship,
                <code>creator()</code> &mdash; <code>belongsTo(User::class, 'created_by')</code> (note: the
                front-end/customer <code>User</code> model, not the admin <code>Staff</code> model, even though
                <code>Staff</code> is imported at the top of the file and never used). No scopes, no casts, no
                model events.
            </p>

        </div>

    </div>

    <!-- Flow -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The Video Collection Flow</h5>
        </div>

        <div class="card-body">

            <ol class="mb-0">
                <li>
                    <strong>Staff generates a link</strong> &mdash; <code>admin.testimonials.generate_link</code>
                    calls <code>Testimonial::updateOrCreate(['created_by' =&gt; $user_id], ['full_name' =&gt; Auth::user()-&gt;name, 'link' =&gt; $url])</code>,
                    creating or reusing one row keyed by the logged-in staff member.
                </li>

                <li class="mt-3">
                    <strong>The link is shared externally</strong> (e.g. sent to a candidate). Opening it hits
                    <code>uploadVideoForm($link)</code> &mdash; a <strong>public, unauthenticated</strong> route
                    that looks up the testimonial by <code>link</code> and shows a simple form asking for a name and
                    a video file.
                </li>

                <li class="mt-3">
                    <strong>Submission</strong> &mdash; <code>uploadVideoProcess()</code> (also public, no auth)
                    validates the video (<code>mimes:mp4,mov,avi</code>, up to 1GB), stores it under
                    <code>storage/app/public/testimonials/videos/</code>, and appends the filename into the
                    <code>uploaded_video</code> JSON array on the matching row &mdash; overwriting
                    <code>full_name</code> with whatever was submitted.
                </li>
            </ol>

        </div>

    </div>

    <!-- Admin CRUD -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Admin List &amp; Management</h5>
        </div>

        <div class="card-body">

            <p>
                Standard admin-only, AJAX/offcanvas-based CRUD (<code>index</code>, <code>show</code>,
                <code>edit</code>, <code>update</code>, <code>destroy</code>), filterable by name, passport number,
                creator, link, and creation date range. <code>create()</code> and <code>store()</code> exist as
                empty stubs &mdash; creation only ever happens through <code>generateLink()</code> above.
            </p>

            <div class="alert alert-secondary mb-0">
                <strong>Latent permission bug:</strong> <code>index()</code> checks a variable
                <code>$perms-&gt;full_access</code> for non-super-admin users, but that variable is never actually
                assigned (only a differently-named <code>$permission</code> variable is set) &mdash; so the
                "full access" branch can never trigger for staff who aren't <code>user_type == 1</code>. In
                practice, non-super-admin staff are always restricted to testimonials where
                <code>created_by == Auth::user()-&gt;id</code>, regardless of any full-access permission they may
                have been granted.
            </div>

        </div>

    </div>

    <!-- Routes -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Routes</h5>
        </div>

        <div class="card-body">

            <p class="mb-2">Authenticated (admin):</p>
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Route</th><th>URI</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>admin.testimonial</code></td><td><code>GET admin/testimonial/list</code></td><td>List/index.</td></tr>
                        <tr><td><code>admin.testimonials.generate_link</code></td><td><code>POST admin/testimonial/generate_link</code></td><td>Create/reuse a collection link.</td></tr>
                        <tr><td><code>admin.testimonials.edit</code></td><td><code>GET admin/testimonials/{testimonial}/edit</code></td><td>Edit form.</td></tr>
                        <tr><td><code>admin.testimonials.update</code></td><td><code>POST admin/testimonials/update</code></td><td>Save edits.</td></tr>
                        <tr><td><code>admin.testimonials.show</code></td><td><code>GET admin/testimonials/{id}</code></td><td>Detail view.</td></tr>
                        <tr><td><code>admin.testimonials.delete</code></td><td><code>POST admin/testimonials/delete</code></td><td>Delete.</td></tr>
                    </tbody>
                </table>
            </div>

            <p class="mb-2">Public (unauthenticated):</p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead><tr><th>Route</th><th>URI</th><th>Purpose</th></tr></thead>
                    <tbody>
                        <tr><td><code>testimonials.upload_video_form</code></td><td><code>GET testimonials/link/{encrypted_id}</code></td><td>The video-upload form.</td></tr>
                        <tr><td><code>testimonials.upload_video_process</code></td><td><code>POST testimonials/upload-video</code></td><td>Handles the upload.</td></tr>
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
