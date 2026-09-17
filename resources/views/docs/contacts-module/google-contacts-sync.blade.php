@extends('layout.docs.docs_layout')

@section('title', 'Contacts Module - Google Contacts Sync')

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
                Google Contacts Sync
            </li>
        </ol>
    </nav>

    <h2 class="fw-bold mb-3">
        Google Contacts Sync
    </h2>

    <p class="text-muted mb-4">
        A manual, admin-initiated OAuth pull from a staff member's Google account &mdash; not a scheduled cron.
    </p>

    <div class="alert alert-info">
        This is handled by a <strong>separate controller</strong>, <code>GoogleContactController</code>, not
        <code>AllContactController</code>.
    </div>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">The Flow</h5>
        </div>

        <div class="card-body">

            <ol class="mb-0">
                <li>
                    <strong>Management page</strong> &mdash; <code>GET allcontact/sync-with-google</code> lists
                    connected <code>GoogleAccount</code> rows.
                </li>

                <li class="mt-3">
                    <strong>Start sync</strong> &mdash; an admin picks a target careoff user or Gmail account and
                    clicks Sync, which submits to <code>GET allcontact/google/redirect</code>
                    (<code>GoogleContactController::redirect</code>). The target careoff admin id is stashed in
                    <code>Session::put('careoff_user', ...)</code>, then a Laravel Socialite
                    <code>GoogleProvider</code> OAuth flow begins, scoped to
                    <code>https://www.googleapis.com/auth/contacts.readonly</code> (config key
                    <code>services.google_contact</code>).
                </li>

                <li class="mt-3">
                    <strong>Callback</strong> &mdash; <code>GET google-contacts/callback</code>
                    (<code>GoogleContactController::callback</code>) upserts a <code>GoogleAccount</code> row
                    (google id, admin id, name, email, tokens, <code>token_expires_at</code>, <code>is_active</code>),
                    then calls <code>syncContacts($token)</code>.
                </li>

                <li class="mt-3">
                    <strong>Pull contacts</strong> &mdash; uses Google's <code>PeopleService</code>
                    (<code>people/me</code> connections, <code>names,emailAddresses,phoneNumbers</code>, paginated
                    1000/page).
                </li>
            </ol>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">How Each Google Contact Is Saved</h5>
        </div>

        <div class="card-body">

            <p>
                For every Google contact with a usable phone number (non-digit characters stripped), the sync does:
            </p>

<pre><code>Allcontact::updateOrCreate(
    ['primary_no_wsp' => $phone],
    ['source' => 'google', 'user_id' => Auth::id(), 'full_name' => $name, 'email' => $email, 'careoff_id' => $careoffUser]
);</code></pre>

            <div class="alert alert-warning mb-0">
                <strong>Dedup key is <code>primary_no_wsp</code> alone.</strong> If a contact with that exact phone
                string already exists, its <code>careoff_id</code>, <code>full_name</code>, <code>email</code>, and
                <code>source</code> get <strong>overwritten</strong>, not merged. There's no fuzzy matching on
                phone-number formatting.
            </div>

        </div>

    </div>

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">Tracking</h5>
        </div>

        <div class="card-body">

            <p class="mb-0">
                <code>wasRecentlyCreated</code> is used to count genuinely new inserts, returned as
                <code>total_contacts</code> and shown as "N contacts synced successfully." The
                <code>GoogleAccount</code> model tracks <code>last_synced_at</code>, <code>total_contacts</code>,
                and <code>is_active</code>. There is <strong>no scheduled command</strong> for this in
                <code>Kernel.php</code> &mdash; every sync is a manual click.
            </p>

        </div>

    </div>

    <!-- Prev/Next -->
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('docs.contacts-module.creation') }}" class="btn btn-outline-secondary btn-sm">&larr; Contact Creation</a>
        <a href="{{ route('docs.contacts-module.groups') }}" class="btn btn-outline-primary btn-sm">Groups &rarr;</a>
    </div>

</div>

@endsection
