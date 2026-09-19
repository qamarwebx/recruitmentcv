@extends('worker.partner.layouts.portal')

@section('title', 'Website')
@section('page-title', __('locale.Website'))

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
        <button type="button" class="wp-settings-tab-btn is-active" data-settings-tab-btn="company">{{ __('locale.Company Profile') }}</button>
        <button type="button" class="wp-settings-tab-btn" data-settings-tab-btn="branding">{{ __('locale.Branding') }}</button>
        <button type="button" class="wp-settings-tab-btn" data-settings-tab-btn="domain">{{ __('locale.Domain') }}</button>
    </div>

    {{-- Company Profile - Domain model's company_name/company_address/etc,
    the SAME row + columns the CRM's "Website" tab -> Address sub-tab
    manages (DomainController::updateAddress()). --}}
    <div class="wp-settings-tab-pane is-active" data-settings-tab-pane="company">
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
    {{-- Display-only: subdomain provisioning now runs entirely through the
    CRM's automatic Hostinger creation flow, so there is nothing left for a
    Partner to submit here - no <form>/submit button, and the input is
    disabled (excluded from any accidental submission too, not just
    visually locked). PartnerPortalController::settingsDomainUpdate()
    independently rejects any direct POST regardless of what the UI does. --}}
    <div class="wp-settings-tab-pane" data-settings-tab-pane="domain">
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20z"/></svg>
                {{ __('locale.Domain') }}
            </h2>
            <div class="w-form-row">
                <label class="w-form-label">{{ __('locale.Your RecruitmentCV Subdomain') }}</label>
                <div class="wp-domain-preview">
                    <input type="text" value="{{ $domain->sub_domain }}" placeholder="{{ __('locale.Not configured yet') }}" disabled readonly>
                    <span>.recruitmentcv.com</span>
                </div>
                <div class="form-text" style="color:var(--w-ink-500);font-size:0.82rem;margin-top:8px;">{{ __('locale.Your subdomain is managed automatically and cannot be changed here.') }}</div>
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
            @if (!empty($domain->hostinger_status))
                <div class="w-form-row">
                    <span class="w-form-label">{{ __('locale.Provisioning Status') }}</span>
                    <div>
                        <span class="wp-badge is-{{ $domain->hostinger_status === 'failed' ? 'danger' : ($domain->hostinger_status === 'created' ? 'success' : 'warning') }}">
                            {{ ucfirst($domain->hostinger_status) }}
                        </span>
                        @if (!empty($domain->hostinger_error))
                            <span style="color:var(--w-ink-500);font-size:0.82rem;margin-inline-start:8px;">{{ $domain->hostinger_error }}</span>
                        @endif
                    </div>
                </div>
            @endif
            @if ($domainStatus === 'active' && !empty($domain->sub_domain))
                <div class="w-form-row">
                    <span class="w-form-label">{{ __('locale.Live URL') }}</span>
                    <div>
                        <a href="https://{{ $domain->full_domain }}" target="_blank" rel="noopener">https://{{ $domain->full_domain }}</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        window.settingsUrls = {
            company: "{{ route('worker.partner.settings.company') }}",
            logo: "{{ route('worker.partner.settings.logo') }}",
            domain: "{{ route('worker.partner.settings.domain') }}"
        };
    </script>
    <script src="{{ asset('worker/js/partner-settings.js') }}?v={{ @filemtime(public_path('worker/js/partner-settings.js')) ?: time() }}"></script>
@endsection
