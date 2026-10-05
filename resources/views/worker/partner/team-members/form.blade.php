@extends('worker.partner.layouts.portal')

@php $editing = $member->exists; @endphp
@section('title', $editing ? __('locale.Edit Team Member') : __('locale.Add Team Member'))
@section('page-title', __('locale.Team Members'))

@section('content')
    <a href="{{ route('worker.partner.team-members') }}" class="wp-back-link" style="display:inline-flex;align-items:center;gap:6px;margin-bottom:16px;">&larr; {{ __('locale.Team Members') }}</a>

    <form method="POST" action="{{ $editing ? route('worker.partner.team-members.update', $member->id) : route('worker.partner.team-members.store') }}" autocomplete="off">
        @csrf
        <div class="wp-grid-2">
            <div class="w-info-card wp-fade-in">
                <h2>{{ $editing ? __('locale.Edit Team Member') : __('locale.Add Team Member') }}</h2>
                <div class="w-form-row">
                    <label class="w-form-label" for="tm-full-name">{{ __('locale.Full Name') }}</label>
                    <input type="text" id="tm-full-name" name="full_name" class="w-input" value="{{ old('full_name', $member->full_name) }}" maxlength="255" required>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label" for="tm-username">{{ __('locale.Username') }}</label>
                    <input type="text" id="tm-username" name="username" class="w-input" dir="ltr" value="{{ old('username', $member->username) }}" maxlength="120" autocapitalize="off" spellcheck="false">
                </div>
                <div class="w-form-row">
                    <label class="w-form-label" for="tm-email">{{ __('locale.Email') }}</label>
                    <input type="email" id="tm-email" name="email" class="w-input" dir="ltr" value="{{ old('email', $member->email) }}" maxlength="255">
                </div>
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Mobile Number') }}</label>
                    <div class="w-form-phone-row">
                        <select name="country_code" class="w-select">
                            @foreach (\App\Support\CountryCodeOptions::all() as $code => $option)
                                <option value="{{ $code }}" @selected((string) old('country_code', $member->country_code ?: '966') === (string) $code)>{{ $option['flag'] }} {{ $option['label'] }} +{{ $code }}</option>
                            @endforeach
                        </select>
                        <input type="tel" name="mobile" class="w-input" dir="ltr" inputmode="numeric" value="{{ old('mobile', $member->mobile) }}" maxlength="20">
                    </div>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label" for="tm-password">{{ __('locale.Password') }}</label>
                    <div class="w-input-password">
                        <input type="password" id="tm-password" name="password" class="w-input" autocomplete="new-password" placeholder="{{ $editing ? __('locale.Leave blank to keep the current password') : '' }}">
                        @include('worker.partials.password-toggle')
                    </div>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label" for="tm-password-confirm">{{ __('locale.Confirm Password') }}</label>
                    <div class="w-input-password">
                        <input type="password" id="tm-password-confirm" name="password_confirmation" class="w-input" autocomplete="new-password">
                        @include('worker.partials.password-toggle')
                    </div>
                </div>
                <p class="wp-field-hint" style="margin-top:-6px;">{{ __('locale.They sign in on your Partner Login with their username, email or mobile (password, OTP or Google with the same email).') }}</p>
                <div class="w-form-row">
                    <label class="w-form-label" for="tm-status">{{ __('locale.Status') }}</label>
                    <select id="tm-status" name="status" class="w-select">
                        <option value="1" @selected((string) old('status', $member->status ? '1' : '0') === '1')>{{ __('locale.Active') }}</option>
                        <option value="0" @selected((string) old('status', $member->status ? '1' : '0') === '0')>{{ __('locale.Inactive') }}</option>
                    </select>
                </div>
            </div>

            {{-- Modules / actions come from the portal routes (App\Support\PartnerTeam::modules()). --}}
            <div class="w-info-card wp-fade-in">
                <h2>{{ __('locale.Permissions') }}</h2>
                <p class="wp-field-hint" style="margin:-6px 0 12px;">{{ __('locale.Choose what this team member can do. Any action also grants View.') }}</p>
                @include('worker.partner.team-members.permissions', ['editable' => true, 'granted' => old('permissions', $member->permissions ?? [])])
            </div>
        </div>
        <button type="submit" class="w-btn w-btn-primary" style="margin-top:18px;">{{ __('locale.Save Changes') }}</button>
    </form>
@endsection

@section('page-script')
    {{-- The Website Config sections' collapse handler (delegated, generic). --}}
    <script src="{{ asset('worker/js/partner-website-config-collapse.js') }}?v={{ @filemtime(public_path('worker/js/partner-website-config-collapse.js')) ?: time() }}"></script>
@endsection
