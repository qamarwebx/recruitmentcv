@extends('layout.docs.docs_layout')

@section('title', 'Employer Module - Work Agreement PDF')

@section('content')

<div class="container-fluid">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('docs.index') }}">Documentation</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('docs.employer-module') }}">Employer Module</a>
            </li>
            <li class="breadcrumb-item active">
                Work Agreement PDF
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Work Agreement PDF
    </h2>

    <p class="text-muted mb-4">
        Generates a signed-looking work agreement document for a candidate/employer pairing — another feature
        that's Employer Plus only despite its URL suggesting otherwise.
    </p>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">How It Works</h5>
        </div>

        <div class="card-body">

            <p>
                Handled by <code>PdfGeneratorController</code>:
            </p>

            <ul>
                <li><code>generateworkagreement()</code> (route <code>admin.employer.generateworkagreement</code>, <code>POST employer-list/generate-workagreement/{id}</code>)</li>
                <li><code>downloadWorkAgreement()</code> (route <code>admin.employer.downloadworkagreement</code>, <code>GET employer-list/download-workagreement/{emp_id}/{cand_id}</code>)</li>
            </ul>

            <p class="mb-0">
                Both query <code>Employercandidate::where('emp_id', ...)</code> &mdash; the Employer Plus join
                column. The PDF overlays the candidate's photo, employer name (Arabic/English), and salary (minus a
                fixed 200 SAR food/accommodation deduction) onto a template image at
                <code>admin/assets/images/workagreement/workagreement.jpg</code>.
            </p>

        </div>

    </div>

    <div class="alert alert-warning">
        <strong>URL prefix is misleading.</strong> Both routes are registered under the <code>employer-list</code>
        prefix (the base-Employer-styled URL), but the underlying query only ever matches
        <code>employercandidates.emp_id</code> assignments — i.e. Employer Plus. There is no equivalent work
        agreement generator wired for <code>emp2_id</code> (base Employer) assignments at all.
    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.employer-module.payment') }}" class="btn btn-outline-secondary btn-sm">&larr; Payment Status &amp; Invoicing</a>
        <a href="{{ route('docs.employer-module.filters') }}" class="btn btn-outline-primary btn-sm">Filters, Permissions &amp; Routes &rarr;</a>
    </div>

</div>

@endsection
