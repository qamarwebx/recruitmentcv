@extends('layout.docs.docs_layout')

@section('title', 'Deal Pipeline Module - Settings & Lookups')

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
                Settings &amp; Lookups
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Settings &amp; Lookups
    </h2>

    <p class="text-muted mb-4">
        Four lookup tables configure a deal: Business Type, Deal Stage, Recruit Status, and Job Title.
    </p>

    <!-- Business Type -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Business Type</h5>
        </div>

        <div class="card-body">

            <p>
                <code>admin.business.list</code> &rarr; <code>BusinessController</code> &rarr; table
                <code>businesses</code>, model <code>Business</code>. This is what determines the <em>shape</em> of
                a deal record &mdash; whether it uses the "Job seeker" field set (candidate/passport/etc.) or the
                "common" field set (name/company). Live values: Job seeker, Associte, Party wakala, Recruitment
                office, Company, Tradetest, HIRING US. Attaches via <code>deal_pipeline.business_id</code>.
            </p>

            <div class="alert alert-danger mb-0">
                <strong>There is a second, unrelated "Business Type" lookup elsewhere in the app.</strong> Under the
                Contact Plus settings group there's <code>admin.businesstype.list</code> &rarr;
                <code>BusinessTypeController</code> &rarr; table <code>businesstypes</code> (note the plural,
                different model <code>Businesstype</code>) &mdash; a completely separate 3-row lookup (Recruitment
                Agency, B2C, Company) used for Contacts/Leads context, <strong>not connected to
                <code>deal_pipeline</code> at all</strong>. Only <code>admin.business.list</code> /
                <code>businesses</code> (singular) is the Deal Pipeline one documented here. Easy to mix these two
                up by name alone.
            </div>

        </div>

    </div>

    <!-- Deal Stage -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Deal Stage</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>admin.dealStage.list</code> &rarr; <code>DealStagecontroller</code> &rarr; table
                <code>deal_stages</code>. See <a href="{{ route('docs.deal-pipeline-module.stages') }}">Pipeline Stages &amp; Kanban Board</a>
                for the live stage list. Deletion is blocked if any deal currently uses that stage
                (<code>DealPipeline::where('deal_stage_id', ...)-&gt;count()</code> guard).
            </p>

        </div>

    </div>

    <!-- Recruit Status -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Recruit Status</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>admin.recruitStatus.list</code> &rarr; <code>RecruitStatusController</code> &rarr; table
                <code>recruit_statuses</code>. Represents the candidate's recruitment/medical progress (On Medical,
                Medical Fit, Not Ready, FOL). Attaches via <code>deal_pipeline.recruite_status_id</code> (the
                misspelling "recruite" is baked into the actual column name). Updated via a dropdown in the list
                view, which looks up the target status by name.
            </p>

        </div>

    </div>

    <!-- Job Title -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Job Title</h5>
        </div>

        <div class="card-body">

            <p>
                <code>admin.jobTitle.list</code> &rarr; <code>JobTitleController</code> &rarr; table
                <code>job_titles</code> (only 2 rows seeded by default). Attaches loosely via
                <code>deal_pipeline.job_title</code>, which holds a <code>job_titles.id</code> as a string in most
                rows but also accepts free text.
            </p>

            <div class="alert alert-danger mb-0">
                <strong>Two real bugs in <code>JobTitleController::destroy()</code>:</strong>
                <ol class="mb-0 mt-2">
                    <li>It calls <code>$jobTitle-&gt;deals()-&gt;count()</code> to check for in-use records before deleting &mdash; but the <code>JobTitle</code> model has <strong>no <code>deals()</code> relation defined</strong>, so this throws a <code>BadMethodCallException</code> at runtime rather than actually protecting against deletion of an in-use job title.</li>
                    <li>The delete itself calls <code>$recruitStatus-&gt;delete()</code> instead of <code>$jobTitle-&gt;delete()</code> &mdash; an undefined-variable bug, clearly copy-pasted from <code>RecruitStatusController::destroy()</code> and never fixed.</li>
                </ol>
            </div>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.deal-pipeline-module.creation') }}" class="btn btn-outline-secondary btn-sm">&larr; Deal Creation</a>
        <a href="{{ route('docs.deal-pipeline-module.notes-files') }}" class="btn btn-outline-primary btn-sm">Notes &amp; Files &rarr;</a>
    </div>

</div>

@endsection
