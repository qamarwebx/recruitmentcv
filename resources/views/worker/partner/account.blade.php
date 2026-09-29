@extends('worker.partner.layouts.portal')

@section('title', 'Account')
@section('page-title', __('locale.My Account'))

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
            <form action="{{ route('worker.partner.account.update') }}" method="POST">
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

        <div class="wp-account-side">
            <div class="w-info-card wp-fade-in">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .3 2 .7 3a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c1 .4 2 .6 3 .7a2 2 0 0 1 1.7 2z"/></svg>
                    {{ __('locale.Account Information') }}
                </h2>
                <div class="wp-info-grid">
                    <div class="wp-info-item">
                        <span>{{ __('locale.Mobile Number') }}</span>
                        <strong class="wp-editable-value">
                            <span dir="ltr">{{ $partner->owner_mobile_no ?: __('locale.Not set (signed in via Google)') }}</span>
                            {{-- Existing add/verify-mobile flow (partner-auth.js). --}}
                            <button type="button" class="wp-edit-icon" data-add-mobile-trigger aria-label="{{ __('locale.Edit Mobile Number') }}" title="{{ __('locale.Edit Mobile Number') }}"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button>
                        </strong>
                    </div>
                    <div class="wp-info-item">
                        <span>{{ __('locale.Email') }}</span>
                        <strong class="wp-editable-value">
                            <span data-partner-email-value>{{ $partner->email ?: '---' }}</span>
                            <button type="button" class="wp-edit-icon" data-email-edit-trigger aria-label="{{ __('locale.Edit Email') }}" title="{{ __('locale.Edit Email') }}"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button>
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

            {{-- Password for "Login with Password": New + Confirm only. Never shows the stored one. --}}
            <div class="w-info-card wp-fade-in">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    {{ __('locale.Change Password') }}
                </h2>
                <form action="{{ route('worker.partner.account.password') }}" method="POST">
                    @csrf
                    <div class="w-form-row">
                        <label class="w-form-label" for="partner-new-password">{{ __('locale.New Password') }}</label>
                        <input type="password" name="password" id="partner-new-password" class="w-input" autocomplete="new-password" minlength="8" required>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label" for="partner-confirm-password">{{ __('locale.Confirm Password') }}</label>
                        <input type="password" name="password_confirmation" id="partner-confirm-password" class="w-input" autocomplete="new-password" minlength="8" required>
                    </div>
                    <p class="wp-field-hint" style="margin:0 0 14px;">{{ __('locale.At least 8 characters.') }}</p>
                    <button type="submit" class="w-btn w-btn-primary">{{ __('locale.Update Password') }}</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Change email: code sent to the NEW address; saved only after it's verified. --}}
    <div class="w-modal-backdrop" data-email-change-backdrop></div>
    <div class="w-modal" data-email-change-modal role="dialog" aria-modal="true" aria-labelledby="emailChangeTitle"
         data-send-url="{{ route('worker.partner.account.email.send') }}"
         data-verify-url="{{ route('worker.partner.account.email.verify') }}">
        <div class="w-modal-dialog">
            <button type="button" class="w-modal-close" data-email-change-close aria-label="{{ __('locale.Close') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
            <div class="w-modal-body">
                <h3 id="emailChangeTitle" class="w-modal-title">{{ __('locale.Change Email') }}</h3>
                <p class="w-modal-subtitle" data-email-change-subtitle>{{ __('locale.We will send a verification code to your new email address.') }}</p>
                <div class="w-form-alert" data-email-change-alert hidden></div>

                <div data-email-change-step="email">
                    <div class="w-form-row">
                        <label class="w-form-label" for="partner-new-email">{{ __('locale.New Email') }}</label>
                        <input type="email" id="partner-new-email" class="w-input" autocomplete="email" maxlength="255" data-email-change-input>
                    </div>
                    <button type="button" class="w-btn w-btn-primary w-btn-block" data-email-change-send>{{ __('locale.Send Code') }}</button>
                </div>

                <div data-email-change-step="code" hidden>
                    <div class="w-form-row">
                        <label class="w-form-label" for="partner-email-code">{{ __('locale.Verification Code') }}</label>
                        <input type="text" id="partner-email-code" class="w-input w-input-otp" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="••••••" data-email-change-code>
                    </div>
                    <button type="button" class="w-btn w-btn-primary w-btn-block" data-email-change-verify>{{ __('locale.Verify & Save') }}</button>
                    <button type="button" class="w-form-back" data-email-change-back>&larr; {{ __('locale.Use a different email') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('worker/js/profile-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/profile-vendor-init.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/partner-account-email.js') }}?v={{ @filemtime(public_path('worker/js/partner-account-email.js')) ?: time() }}"
        data-i18n="{{ json_encode([
            'sending' => __('locale.Sending…'),
            'sendCode' => __('locale.Send Code'),
            'verifying' => __('locale.Verifying…'),
            'verifySave' => __('locale.Verify & Save'),
            'errEmailRequired' => __('locale.Please enter a valid email address.'),
            'errCodeRequired' => __('locale.Please enter the verification code.'),
            'errTechnical' => __('locale.A technical error occurred. Please try again shortly.'),
        ]) }}"></script>
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
