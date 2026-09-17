@extends('worker.partner.layouts.portal')

@section('title', 'Profile / Account')
@section('page-title', __('locale.Profile / Account'))

@section('page-style')
    {{-- jQuery-dependent select2, same vendored files/config/reskin the
    rest of the Worker Partner Portal (Employer, Orders) already uses -
    scoped to this page only. Loading it here is also what lets
    partner-auth.js's own conditional Select2 upgrade for the "Verify Your
    Mobile Number" modal's country-code field activate when opened from
    this page, without forcing that shared, site-wide modal to depend on
    jQuery everywhere else it appears. --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')
    @php
        $initials = collect(explode(' ', trim($partner->rec_off_name ?: $partner->owner_name ?: 'P')))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    @endphp

    <div class="wp-profile-header wp-fade-in">
        <span class="wp-partner-avatar">{{ $initials }}</span>
        <div>
            <h1>{{ $partner->rec_off_name }}</h1>
            <p>{{ __('locale.Recruitment Partner Account') }}</p>
        </div>
    </div>

    <div class="wp-grid-2">
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                {{ __('locale.Edit Account Details') }}
            </h2>
            <form action="{{ route('worker.partner.profile.update') }}" method="POST">
                @csrf
                 <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Full Name') }}</label>
                    <input type="text" name="owner_name" class="w-input" value="{{ old('owner_name', $partner->owner_name) }}" required>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Company / Recruitment Office Name') }}</label>
                    <input type="text" name="rec_off_name" class="w-input" value="{{ old('rec_off_name', $partner->rec_off_name) }}" required>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Email') }}</label>
                    <input type="email" name="email" class="w-input" value="{{ old('email', $partner->email) }}" placeholder="you@example.com">
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Country') }}</label>
                    <select name="country_id" class="w-select select2">
                        <option value="">{{ __('locale.Select') }}</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" @selected(old('country_id', $partner->country_id) == $country->id)>{{ $country->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.City') }}</label>
                    <select name="city_id" class="w-select select2">
                        <option value="">{{ __('locale.Select') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}" @selected(old('city_id', $partner->city_id) == $city->id)>{{ $city->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-btn w-btn-primary">{{ __('locale.Save Changes') }}</button>
            </form>
        </div>

        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .3 2 .7 3a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c1 .4 2 .6 3 .7a2 2 0 0 1 1.7 2z"/></svg>
                {{ __('locale.Account Information') }}
            </h2>
            <div class="wp-info-grid">
                <div class="wp-info-item">
                    <span>{{ __('locale.Mobile Number') }}</span>
                    <strong>
                        {{ $partner->owner_mobile_no ?: __('locale.Not set (signed in via Google)') }}
                        <button type="button" class="w-btn w-btn-outline w-btn-sm" style="margin-inline-start:10px;" data-add-mobile-trigger>
                            {{ $partner->owner_mobile_no ? __('locale.Change Number') : __('locale.Add / Verify Mobile') }}
                        </button>
                    </strong>
                </div>
                <div class="wp-info-item">
                    <span>{{ __('locale.Registration Status') }}</span>
                    <strong>
                        @if ((int) $partner->registration_status === 1)
                            <span class="wp-badge is-success">{{ __('locale.Approved') }}</span>
                        @elseif ((int) $partner->registration_status === 2)
                            <span class="wp-badge is-danger">{{ __('locale.Rejected') }}</span>
                        @else
                            <span class="wp-badge is-warning">{{ __('locale.Pending') }}</span>
                        @endif
                    </strong>
                </div>
                <div class="wp-info-item">
                    <span>{{ __('locale.Member Since') }}</span>
                    <strong>{{ $partner->created_at ? $partner->created_at->format('d M Y') : '---' }}</strong>
                </div>
                <div class="wp-info-item">
                    <span>{{ __('locale.Verification') }}</span>
                    <strong>{{ $partner->mobile_verified_at || $partner->google_id ? __('locale.Verified') : __('locale.Not verified') }}</strong>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('worker/js/profile-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/profile-vendor-init.js')) ?: time() }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var trigger = document.querySelector('[data-add-mobile-trigger]');
            if (!trigger) return;
            trigger.addEventListener('click', function () {
                if (!window.WorkerPartnerAuth) return;
                window.WorkerPartnerAuth.openAddMobile(function () {
                    window.location.reload();
                });
            });
        });
    </script>
@endsection
