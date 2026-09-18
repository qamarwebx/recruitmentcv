@extends('worker.partner.layouts.portal')

@section('title', 'Settings')
@section('page-title', __('locale.Settings'))

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
        $domainStatusLabels = [
            'pending' => __('locale.Pending'),
            'active' => __('locale.Active'),
            'inactive' => __('locale.Inactive'),
            'suspended' => __('locale.Suspended'),
        ];
    @endphp

    <div class="wp-profile-header wp-fade-in">
        <span class="wp-partner-avatar">{{ $initials }}</span>
        <div>
            <h1>{{ $partner->rec_off_name }}</h1>
            <p>{{ __('locale.Recruitment Partner Account') }}</p>
        </div>
    </div>

    <div class="wp-settings-tabs wp-fade-in" role="tablist">
        <button type="button" class="wp-settings-tab-btn is-active" data-settings-tab-btn="personal">{{ __('locale.Personal Details') }}</button>
        <button type="button" class="wp-settings-tab-btn" data-settings-tab-btn="company">{{ __('locale.Company Profile') }}</button>
        <button type="button" class="wp-settings-tab-btn" data-settings-tab-btn="branding">{{ __('locale.Branding') }}</button>
        <button type="button" class="wp-settings-tab-btn" data-settings-tab-btn="domain">{{ __('locale.Domain') }}</button>
    </div>

    {{-- Personal Details - existing form/route/validation, unchanged --}}
    <div class="wp-settings-tab-pane is-active" data-settings-tab-pane="personal">
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
    </div>

    {{-- Company Profile - Domain model's company_name/company_address/etc,
    the SAME row + columns the CRM's "Website" tab -> Address sub-tab
    manages (DomainController::updateAddress()). --}}
    <div class="wp-settings-tab-pane" data-settings-tab-pane="company">
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m-1 4h1m4-4h1m-1 4h1M9 21v-4h6v4"/></svg>
                {{ __('locale.Company Profile') }}
            </h2>
            <div class="w-form-alert" style="display:none;" data-settings-alert="company"></div>
            <form data-settings-form="company">
                <div class="wp-info-grid" style="grid-template-columns:repeat(2,1fr);">
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Company Name') }}</label>
                        <input type="text" name="company_name" class="w-input" value="{{ $domain->company_name }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Company Name (Arabic)') }}</label>
                        <input type="text" name="company_name_ar" class="w-input" dir="rtl" value="{{ $domain->company_name_ar }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Company Address') }}</label>
                        <textarea name="company_address" class="w-input" rows="3">{{ $domain->company_address }}</textarea>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Company Address (Arabic)') }}</label>
                        <textarea name="company_address_ar" class="w-input" dir="rtl" rows="3">{{ $domain->company_address_ar }}</textarea>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Company Mobile') }}</label>
                        <input type="text" name="company_mobile" class="w-input" value="{{ $domain->company_mobile }}">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Company Email') }}</label>
                        <input type="email" name="company_email" class="w-input" value="{{ $domain->company_email }}">
                    </div>
                </div>
                <button type="submit" class="w-btn w-btn-primary" data-settings-submit>{{ __('locale.Save Changes') }}</button>
            </form>
        </div>
    </div>

    {{-- Branding - website_logo/website_logo_ar, the SAME columns +
    admin/assets/images/partner/ folder the CRM's "Website" tab -> Logo
    sub-tab manages (DomainController::websitelogoupdt()). --}}
    <div class="wp-settings-tab-pane" data-settings-tab-pane="branding">
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                {{ __('locale.Branding') }}
            </h2>
            <div class="w-form-alert" style="display:none;" data-settings-alert="branding"></div>
            <form data-settings-form="branding">
                <div class="wp-info-grid" style="grid-template-columns:repeat(2,1fr);">
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.English Logo') }}</label>
                        <div class="wp-logo-upload">
                            <img src="{{ $domain->website_logo ? asset('admin/assets/images/partner/'.$domain->website_logo) : asset('user/img/logo/Logo_eng_dark.webp') }}" alt="English logo" data-logo-preview="en">
                            <input type="file" class="w-input" accept="image/png,image/jpeg,image/webp" data-logo-input="en">
                        </div>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Arabic Logo') }}</label>
                        <div class="wp-logo-upload">
                            <img src="{{ $domain->website_logo_ar ? asset('admin/assets/images/partner/'.$domain->website_logo_ar) : asset('user/img/logo/new_logo_Arabic.png') }}" alt="Arabic logo" data-logo-preview="ar">
                            <input type="file" class="w-input" accept="image/png,image/jpeg,image/webp" data-logo-input="ar">
                        </div>
                    </div>
                </div>
                <div class="form-text" style="color:var(--w-ink-500);font-size:0.82rem;margin-bottom:16px;">{{ __('locale.JPG, PNG or WEBP, up to 2MB.') }}</div>
                <button type="submit" class="w-btn w-btn-primary" data-settings-submit>{{ __('locale.Save Changes') }}</button>
            </form>
        </div>
    </div>

    {{-- Domain - the sub_domain column on the SAME Domain row, resolved by
    ResolvePartnerWebsiteDomain middleware on *.recruitmentcv.com requests. --}}
    <div class="wp-settings-tab-pane" data-settings-tab-pane="domain">
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20z"/></svg>
                {{ __('locale.Domain') }}
            </h2>
            <div class="w-form-alert" style="display:none;" data-settings-alert="domain"></div>
            <form data-settings-form="domain">
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Your RecruitmentCV Subdomain') }}</label>
                    <div class="wp-domain-preview">
                        <input type="text" name="sub_domain" value="{{ $domain->sub_domain }}" placeholder="raha" autocomplete="off">
                        <span>.recruitmentcv.com</span>
                    </div>
                    <div class="form-text" style="color:var(--w-ink-500);font-size:0.82rem;margin-top:8px;">{{ __('locale.Lowercase letters, numbers and hyphens only.') }}</div>
                </div>
                <div class="w-form-row">
                    <span class="w-form-label">{{ __('locale.Status') }}</span>
                    @php $domainStatus = $domain->status ?: 'pending'; @endphp
                    <div>
                        <span class="wp-badge is-{{ $domainStatus === 'active' ? 'success' : ($domainStatus === 'suspended' ? 'danger' : 'warning') }}">
                            {{ $domainStatusLabels[$domainStatus] ?? ucfirst($domainStatus) }}
                        </span>
                        @if ($domainStatus !== 'active')
                            <span style="color:var(--w-ink-500);font-size:0.82rem;margin-inline-start:8px;">{{ __('locale.A new or changed subdomain needs admin approval before it goes live.') }}</span>
                        @endif
                    </div>
                </div>
                <button type="submit" class="w-btn w-btn-primary" data-settings-submit>{{ __('locale.Save Changes') }}</button>
            </form>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('worker/js/profile-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/profile-vendor-init.js')) ?: time() }}"></script>
    <script>
        window.settingsUrls = {
            company: "{{ route('worker.partner.settings.company') }}",
            logo: "{{ route('worker.partner.settings.logo') }}",
            domain: "{{ route('worker.partner.settings.domain') }}"
        };
    </script>
    <script src="{{ asset('worker/js/partner-settings.js') }}?v={{ @filemtime(public_path('worker/js/partner-settings.js')) ?: time() }}"></script>
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
