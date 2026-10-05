@extends('worker.partner.layouts.portal')

@section('title', $member->full_name)
@section('page-title', __('locale.Team Members'))

@section('content')
    <a href="{{ route('worker.partner.team-members') }}" style="display:inline-flex;align-items:center;gap:6px;margin-bottom:16px;">&larr; {{ __('locale.Team Members') }}</a>

    <div class="wp-grid-2">
        <div class="w-info-card wp-fade-in">
            <h2 class="wp-card-head-with-action">
                {{ $member->full_name }}
                <a href="{{ route('worker.partner.team-members.edit', $member->id) }}" class="w-btn w-btn-outline w-btn-sm wp-card-head-action">{{ __('locale.Edit') }}</a>
            </h2>
            <div class="wp-info-grid">
                <div class="wp-info-item"><span>{{ __('locale.Username') }}</span><strong dir="ltr">{{ $member->username ?: '---' }}</strong></div>
                <div class="wp-info-item"><span>{{ __('locale.Email') }}</span><strong>{{ $member->email ?: '---' }}</strong></div>
                <div class="wp-info-item"><span>{{ __('locale.Mobile Number') }}</span><strong dir="ltr">{{ $member->display_mobile ?: '---' }}</strong></div>
                <div class="wp-info-item"><span>{{ __('locale.Status') }}</span><strong><span class="wp-badge {{ $member->status ? 'is-success' : 'is-neutral' }}">{{ $member->status ? __('locale.Active') : __('locale.Inactive') }}</span></strong></div>
                <div class="wp-info-item"><span>{{ __('locale.Password') }}</span><strong>{{ $member->password ? __('locale.Set') : __('locale.Not set') }}</strong></div>
                <div class="wp-info-item"><span>{{ __('locale.Last Login') }}</span><strong>{{ $member->last_login_at ? $member->last_login_at->format('d M Y, h:i A') : '---' }}</strong></div>
            </div>
            <form method="POST" action="{{ route('worker.partner.team-members.destroy', $member->id) }}" style="margin-top:18px;" onsubmit="return confirm(@js(__('locale.Delete this team member?')))">
                @csrf
                <button type="submit" class="w-btn w-btn-outline">{{ __('locale.Delete') }}</button>
            </form>
        </div>
        <div class="w-info-card wp-fade-in">
            <h2>{{ __('locale.Permissions') }}</h2>
            @include('worker.partner.team-members.permissions', ['editable' => false, 'granted' => $member->permissions ?? []])
        </div>
    </div>
@endsection

@section('page-script')
    {{-- The Website Config sections' collapse handler (delegated, generic). --}}
    <script src="{{ asset('worker/js/partner-website-config-collapse.js') }}?v={{ @filemtime(public_path('worker/js/partner-website-config-collapse.js')) ?: time() }}"></script>
@endsection
