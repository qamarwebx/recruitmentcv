@extends('worker.partner.layouts.portal')

@section('title', __('locale.Team Members'))
@section('page-title', __('locale.Team Members'))

@section('content')
    <form method="GET" action="{{ route('worker.partner.team-members') }}" class="wp-filter-bar wp-fade-in">
        <div class="wp-filter-search">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="search" class="w-input" placeholder="{{ __('locale.Search by name, username, email or mobile') }}" value="{{ $search }}">
        </div>
        <select name="status" class="w-select">
            <option value="">{{ __('locale.All Statuses') }}</option>
            <option value="1" @selected(request('status') === '1')>{{ __('locale.Active') }}</option>
            <option value="0" @selected(request('status') === '0')>{{ __('locale.Inactive') }}</option>
        </select>
        <button type="submit" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Search') }}</button>
        <a href="{{ route('worker.partner.team-members') }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.Reset') }}</a>
    </form>

    <div class="wp-listing-toolbar">
        <p class="wp-result-count"><strong>{{ $members->total() }}</strong> {{ __('locale.Team Members') }}</p>
        <a href="{{ route('worker.partner.team-members.create') }}" class="w-btn w-btn-primary w-btn-sm">+ {{ __('locale.Add Team Member') }}</a>
    </div>

    @if ($members->count() > 0)
        <div class="wp-table-wrap wp-fade-in">
            <div class="wp-table-scroll">
                <table class="wp-table">
                    <thead>
                        <tr>
                            <th>{{ __('locale.Full Name') }}</th>
                            <th>{{ __('locale.Username') }}</th>
                            <th>{{ __('locale.Email') }}</th>
                            <th>{{ __('locale.Mobile Number') }}</th>
                            <th>{{ __('locale.Status') }}</th>
                            <th>{{ __('locale.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $member)
                            <tr>
                                <td><span class="wp-cell-title">{{ $member->full_name }}</span></td>
                                <td dir="ltr">{{ $member->username ?: '---' }}</td>
                                <td>{{ $member->email ?: '---' }}</td>
                                <td dir="ltr">{{ $member->display_mobile ?: '---' }}</td>
                                <td><span class="wp-badge {{ $member->status ? 'is-success' : 'is-neutral' }}">{{ $member->status ? __('locale.Active') : __('locale.Inactive') }}</span></td>
                                <td style="white-space:nowrap;">
                                    <a href="{{ route('worker.partner.team-members.show', $member->id) }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.View') }}</a>
                                    <a href="{{ route('worker.partner.team-members.edit', $member->id) }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.Edit') }}</a>
                                    <form method="POST" action="{{ route('worker.partner.team-members.destroy', $member->id) }}" style="display:inline;" onsubmit="return confirm(@js(__('locale.Delete this team member?')))">
                                        @csrf
                                        <button type="submit" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $members->links('worker.partials.pagination') }}
    @else
        <div class="wp-empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <h3>{{ __('locale.No team members yet') }}</h3>
            <p>{{ __('locale.Add team members to let them sign in to your portal with the permissions you choose.') }}</p>
        </div>
    @endif
@endsection
