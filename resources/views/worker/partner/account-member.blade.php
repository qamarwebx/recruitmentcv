@extends('worker.partner.layouts.portal')

{{-- My Account for a signed-in team member: their own details, same cards
and Edit toggle as the partner's account page (account.blade.php). Saves
through the same account.update route, which resolves the member from the
session (PartnerTeam::accountHolder()). --}}
@section('title', 'Account')
@section('page-title', __('locale.My Account'))

@section('content')
    @php
        $initials = collect(explode(' ', trim($member->full_name ?: 'T')))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
        $accountFields = ['full_name', 'username', 'email', 'country_code', 'mobile'];
        $accountEditing = $errors->hasAny($accountFields);
    @endphp

    <div class="wp-profile-header wp-fade-in">
        <span class="wp-partner-avatar">{{ $initials }}</span>
        <div>
            <h1>{{ $member->full_name }}</h1>
            <p>{{ __('locale.Team Member Account') }}</p>
        </div>
    </div>

    <div class="wp-grid-2">
        <div class="w-info-card wp-fade-in" data-account-details @if ($accountEditing) data-account-editing @endif>
            <h2 class="wp-card-head-with-action">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                {{ __('locale.Edit Account Details') }}
                <button type="button" class="w-btn w-btn-outline w-btn-sm wp-card-head-action" data-account-edit aria-controls="member-account-form" @if ($accountEditing) hidden @endif>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    {{ __('locale.Edit') }}
                </button>
            </h2>
            <div class="wp-info-grid" data-account-view @if ($accountEditing) hidden @endif>
                <div class="wp-info-item">
                    <span>{{ __('locale.Full Name') }}</span>
                    <strong>{{ $member->full_name ?: '---' }}</strong>
                </div>
                <div class="wp-info-item">
                    <span>{{ __('locale.Username') }}</span>
                    <strong dir="ltr">{{ $member->username ?: '---' }}</strong>
                </div>
                <div class="wp-info-item">
                    <span>{{ __('locale.Email') }}</span>
                    <strong>{{ $member->email ?: '---' }}</strong>
                </div>
                <div class="wp-info-item">
                    <span>{{ __('locale.Mobile Number') }}</span>
                    <strong dir="ltr">{{ $member->display_mobile ?: '---' }}</strong>
                </div>
            </div>
            <form action="{{ route('worker.partner.account.update') }}" method="POST" id="member-account-form" data-account-form @unless ($accountEditing) hidden @endunless>
                @csrf
                <div class="w-form-row">
                    <label class="w-form-label" for="member-full-name">{{ __('locale.Full Name') }}</label>
                    <input type="text" name="full_name" id="member-full-name" class="w-input" value="{{ old('full_name', $member->full_name) }}" maxlength="255" required>
                </div>
                {{-- Used to sign in (Login with Password / OTP by username). --}}
                <div class="w-form-row">
                    <label class="w-form-label" for="member-username">{{ __('locale.Username') }}</label>
                    <input type="text" name="username" id="member-username" class="w-input" dir="ltr" value="{{ old('username', $member->username) }}" maxlength="120" autocomplete="username" autocapitalize="off" spellcheck="false">
                </div>
                <div class="w-form-row">
                    <label class="w-form-label" for="member-email">{{ __('locale.Email') }}</label>
                    <input type="email" name="email" id="member-email" class="w-input" dir="ltr" value="{{ old('email', $member->email) }}" maxlength="255" autocomplete="email">
                </div>
                <div class="w-form-row">
                    <label class="w-form-label" for="member-mobile">{{ __('locale.Mobile Number') }}</label>
                    <div class="w-form-phone-row">
                        <select name="country_code" class="w-select" aria-label="{{ __('locale.Country Code') }}">
                            @foreach (\App\Support\CountryCodeOptions::all() as $code => $option)
                                <option value="{{ $code }}" @selected((string) old('country_code', $member->country_code ?: '966') === (string) $code)>{{ $option['flag'] }} {{ $option['label'] }} +{{ $code }}</option>
                            @endforeach
                        </select>
                        <input type="tel" name="mobile" id="member-mobile" class="w-input" dir="ltr" inputmode="numeric" value="{{ old('mobile', $member->mobile) }}" maxlength="20">
                    </div>
                </div>
                <div class="wp-form-actions">
                    <button type="submit" class="w-btn w-btn-primary">{{ __('locale.Save Changes') }}</button>
                    <button type="button" class="w-btn w-btn-outline" data-account-cancel>{{ __('locale.Cancel') }}</button>
                </div>
            </form>
        </div>

        <div class="wp-account-side">
            <div class="w-info-card wp-fade-in">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                    {{ __('locale.Account Information') }}
                </h2>
                <div class="wp-info-grid">
                    <div class="wp-info-item">
                        <span>{{ __('locale.Status') }}</span>
                        <strong><span class="wp-badge {{ $member->status ? 'is-success' : 'is-neutral' }}">{{ $member->status ? __('locale.Active') : __('locale.Inactive') }}</span></strong>
                    </div>
                    <div class="wp-info-item">
                        <span>{{ __('locale.Member Since') }}</span>
                        <strong>{{ $member->created_at ? $member->created_at->format('d M Y') : '---' }}</strong>
                    </div>
                    <div class="wp-info-item">
                        <span>{{ __('locale.Last Login') }}</span>
                        <strong>{{ $member->last_login_at ? $member->last_login_at->format('d M Y, h:i A') : '---' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Edit Account Details: view mode <-> the form (same as account.blade.php).
            var details = document.querySelector('[data-account-details]');
            if (!details) return;
            var view = details.querySelector('[data-account-view]');
            var form = details.querySelector('[data-account-form]');
            var editBtn = details.querySelector('[data-account-edit]');
            var setEditing = function (editing) {
                view.hidden = editing;
                form.hidden = !editing;
                editBtn.hidden = editing;
            };
            editBtn.addEventListener('click', function () {
                setEditing(true);
                var first = form.querySelector('input[type="text"]');
                if (first) first.focus();
            });
            details.querySelector('[data-account-cancel]').addEventListener('click', function () {
                if (details.hasAttribute('data-account-editing')) {
                    window.location.assign(window.location.pathname);
                    return;
                }
                form.reset();
                setEditing(false);
            });
        });
    </script>
@endsection
