@extends('layout.docs.docs_layout')

@section('title', 'Contacts Module - Contact Creation')

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
                Contact Creation
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Contact Creation
    </h2>

    <p class="text-muted mb-4">
        Contacts can enter the system three ways: manual entry, CSV import, or Google Contacts sync (covered
        separately — see <a href="{{ route('docs.contacts-module.google-sync') }}">Google Contacts Sync</a>).
    </p>

    <!-- Manual -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">1. Manual Entry &mdash; <code>store()</code></h5>
        </div>

        <div class="card-body">

            <p>
                A staff member fills in the contact form, submitted to <code>POST allcontact-list/store</code>.
                Each phone field is prefixed with its selected dial code (defaulting to <code>91</code> / India if
                none is chosen), e.g.:
            </p>

<pre><code>$post->primary_no_wsp = $primary_no_wsp_dial_code . '' . $request->primary_no_wsp;</code></pre>

            <p>
                <code>user_id</code> is set to the logged-in admin, <code>source_id</code> from the selected
                source.
            </p>

            <div class="alert alert-primary mb-0">
                <strong>Automatic WhatsApp trigger.</strong> After saving, <code>createScheduledAutomationForAllcontact()</code>
                looks up active automation rules (<code>Autometanotification</code> rows where
                <code>template_table_name = 'allcontacts'</code>) and schedules a
                <code>ScheduledSendMsgAutomation</code> record. Separately, if the contact's <code>send_whatsapp</code>
                checkbox was ticked, <code>SendAllcontactautomessage::dispatch($post)</code> is queued immediately
                (<code>default</code> queue).
            </div>

        </div>

    </div>

    <!-- CSV Import -->

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">2. CSV Import</h5>
        </div>

        <div class="card-body">

            <p>A two-step, mapping-based import:</p>

            <ol>
                <li>
                    <strong>Preview</strong> (<code>allcontact.csv.import.preview</code>) &mdash; parses the
                    uploaded CSV into headers and rows for a column-mapping UI.
                </li>
                <li>
                    <strong>Upload</strong> (<code>allcontact.csv.upload</code>) &mdash; takes the submitted
                    <code>mapped_fields</code> + <code>csv_data</code> + dropdown defaults (<code>lead_type</code>,
                    <code>lcs_id</code>, <code>ls_id</code>, <code>careoff_id</code>, <code>group_id</code>,
                    <code>source</code>). <code>Country</code>/<code>Region</code>/<code>City</code> rows are
                    resolved or created (<code>firstOrCreate</code> by name), and rows are de-duplicated against
                    existing contacts by <strong><code>email</code> OR <code>primary_no_wsp</code></strong> before
                    <code>ProcessCsvImport::dispatch($insertData)</code> queues the actual bulk insert.
                </li>
            </ol>

            <p class="mb-0">
                There's also a simpler "short form" import path (<code>allcontact.uploadstore</code>) and a
                separate bulk-import-store endpoint (<code>allcontact.import.store</code>) for programmatic bulk
                creation outside the CSV-mapping UI.
            </p>

        </div>

    </div>

    <!-- Bulk mutation -->

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Bulk Mutations (Not Creation)</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                A large set of endpoints reshape <em>existing</em> contacts rather than create new ones:
                <code>update</code>, <code>bulkupdatecountrycode</code>, <code>updatecountrybasequery</code>,
                <code>allbulkleadownertransfer</code>, <code>allbulkcareofftransfer</code>,
                <code>allbulkgrouptransfer</code>, <code>updatelifecyclestatus</code>, <code>updateleadstage</code>,
                <code>leadpriorityupdate</code>, <code>optinoutupdt</code>. Deletion (<code>deleteContact</code>,
                <code>bulkdeleteallc</code>) is a real hard delete — see the caveat on
                <code>contact_dl_status</code> in <a href="{{ route('docs.contacts-module.database') }}">Database Structure</a>.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.contacts-module.database') }}" class="btn btn-outline-secondary btn-sm">&larr; Database Structure</a>
        <a href="{{ route('docs.contacts-module.google-sync') }}" class="btn btn-outline-primary btn-sm">Google Contacts Sync &rarr;</a>
    </div>

</div>

@endsection
