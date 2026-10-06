@extends('worker.partner.layouts.portal')

{{-- Team Members -> ⋮ -> Permission: one team member's permissions, on their
own page. The same permissions UI as before (team-members.permissions partial:
modules/actions from App\Support\PartnerTeam::modules(), permissions[module][] /
permissions[module.section][] inputs), saved by
PartnerTeamMemberController::updatePermissions(). --}}
@section('title', __('locale.Permissions') . ' - ' . $member->full_name)
@section('page-title', __('locale.Team Members'))

@section('content')
    <a href="{{ route('worker.partner.team-members') }}" style="display:inline-flex;align-items:center;gap:6px;margin-bottom:16px;">&larr; {{ __('locale.Team Members') }}</a>

    <form method="POST" action="{{ route('worker.partner.team-members.permissions.update', $member->id) }}" style="max-width:820px;">
        @csrf
        <div class="w-info-card wp-fade-in">
            <h2 class="wp-card-head-with-action">
                <span>
                    {{ __('locale.Permissions') }}
                    <span style="display:block;font-size:0.88rem;font-weight:500;color:var(--w-ink-500);margin-top:4px;">
                        {{ $member->full_name }}
                        @if ($member->username || $member->email)
                            <span dir="ltr">({{ $member->username ?: $member->email }})</span>
                        @endif
                        &middot;
                        <span class="wp-badge {{ $member->status ? 'is-success' : 'is-neutral' }}">{{ $member->status ? __('locale.Active') : __('locale.Inactive') }}</span>
                    </span>
                </span>
                <a href="{{ route('worker.partner.team-members.show', $member->id) }}" class="w-btn w-btn-outline w-btn-sm wp-card-head-action">{{ __('locale.View Profile') }}</a>
            </h2>
            <p class="wp-field-hint" style="margin:-6px 0 12px;">{{ __('locale.Choose what this team member can do. Any action also grants View.') }}</p>
            {{-- Live filter over the groups below (team-member-permission-search.js) - display only. --}}
            <div class="wp-filter-search wp-perm-search">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="search" class="w-input" data-perm-search placeholder="{{ __('locale.Search Permission') }}" aria-label="{{ __('locale.Search Permission') }}" autocomplete="off">
            </div>
            <p class="wp-field-hint wp-perm-search-hidden" data-perm-search-empty>{{ __('locale.No matching permissions.') }}</p>
            @include('worker.partner.team-members.permissions', ['editable' => true, 'open' => true, 'granted' => old('permissions', $member->permissions ?? [])])
        </div>
        <button type="submit" class="w-btn w-btn-primary" style="margin-top:18px;">{{ __('locale.Save Changes') }}</button>
    </form>
@endsection

@section('page-script')
    {{-- The Website Config sections' collapse handler (delegated, generic). --}}
    <script src="{{ asset('worker/js/partner-website-config-collapse.js') }}?v={{ @filemtime(public_path('worker/js/partner-website-config-collapse.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/team-member-permission-search.js') }}?v={{ @filemtime(public_path('worker/js/team-member-permission-search.js')) ?: time() }}"></script>
@endsection
