@extends('worker.partner.layouts.portal')

@section('title', 'Website')
@section('page-title', __('locale.Website'))

@section('page-style')
    {{-- Quill 2.0.3 (Privacy Policy / Terms of Service body editors) - the
    official Quickstart build (https://quilljs.com/docs/quickstart), not
    the CRM's older vendored 1.x bundle. Loaded once, here only - never
    alongside a second Quill version on this same page. --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endsection

@section('content')
    @php
        $initials = collect(explode(' ', trim($partner->rec_off_name ?: $partner->owner_name ?: 'P')))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
        // Domain tab "Status" = the partner's CRM approval/status
        // (Partner::accountStatus()), not domains.status (the older
        // custom-domain flow's own field, never updated for subdomains).
        $accountStatus = $partner->accountStatus();
        $accountStatusLabels = [
            'pending' => __('locale.Pending'),
            'active' => __('locale.Active'),
            'inactive' => __('locale.Inactive'),
            'rejected' => __('locale.Rejected'),
        ];
        $accountStatusBadges = ['active' => 'success', 'pending' => 'warning', 'inactive' => 'neutral', 'rejected' => 'danger'];
        $accountStatusNotes = [
            'pending' => __('locale.Your registration is pending approval.'),
            'inactive' => __('locale.Your partner account is inactive. Please contact the administrator.'),
            'rejected' => __('locale.Your registration has been rejected.'),
        ];

        // Website Config save handlers redirect back with ?open={page} so
        // the partner lands back on the tab/sub-tab they just saved,
        // rather than resetting to Company Profile - resolved server-side
        // (not just via JS hash-routing) so it's correct on first paint.
        $openWcPage = request()->query('open');
        // Tabs this account may use: all for the partner; a team member only
        // the ones granted (App\Support\PartnerTeam::SECTIONS, enforced
        // server-side on every save too). Denied tabs are not rendered.
        $tabSections = ['company' => 'company_profile', 'branding' => 'branding', 'whatsapp' => 'whatsapp', 'website-config' => 'website_configuration', 'smtp' => 'smtp', 'domain' => 'domain'];
        $allowedTabs = array_keys(array_filter($tabSections, fn ($section) => \App\Support\PartnerTeam::allowsSection('website', $section)));
        $activeTopTab = ($openWcPage && in_array('website-config', $allowedTabs, true)) ? 'website-config' : ($allowedTabs[0] ?? null);
        $activeWcPage = in_array($openWcPage, \App\Models\PartnerPageContent::PAGES, true) ? $openWcPage : 'home';
    @endphp

    <div class="wp-profile-header wp-fade-in">
        <span class="wp-partner-avatar">{{ $initials }}</span>
        <div>
            <h1>{{ $partner->rec_off_name }}</h1>
            <p>{{ __('locale.Recruitment Partner Account') }}</p>
        </div>
    </div>

    <div class="wp-settings-tabs wp-fade-in" role="tablist">
        @if (in_array('company', $allowedTabs, true))
        <button type="button" class="wp-settings-tab-btn {{ $activeTopTab === 'company' ? 'is-active' : '' }}" data-settings-tab-btn="company">{{ __('locale.Company Profile') }}</button>
        @endif
        @if (in_array('branding', $allowedTabs, true))
        <button type="button" class="wp-settings-tab-btn {{ $activeTopTab === 'branding' ? 'is-active' : '' }}" data-settings-tab-btn="branding">{{ __('locale.Branding') }}</button>
        @endif
        @if (in_array('whatsapp', $allowedTabs, true))
        <button type="button" class="wp-settings-tab-btn {{ $activeTopTab === 'whatsapp' ? 'is-active' : '' }}" data-settings-tab-btn="whatsapp">{{ __('locale.WhatsApp') }}</button>
        @endif
        @if (in_array('website-config', $allowedTabs, true))
        <button type="button" class="wp-settings-tab-btn {{ $activeTopTab === 'website-config' ? 'is-active' : '' }}" data-settings-tab-btn="website-config">{{ __('locale.Website Config') }}</button>
        @endif
        @if (in_array('smtp', $allowedTabs, true))
        <button type="button" class="wp-settings-tab-btn {{ $activeTopTab === 'smtp' ? 'is-active' : '' }}" data-settings-tab-btn="smtp">{{ __('locale.SMTP') }}</button>
        @endif
        @if (in_array('domain', $allowedTabs, true))
        <button type="button" class="wp-settings-tab-btn {{ $activeTopTab === 'domain' ? 'is-active' : '' }}" data-settings-tab-btn="domain">{{ __('locale.Domain') }}</button>
        @endif
    </div>

    {{-- Company Profile - Domain model's company_name/company_address/etc,
    the SAME row + columns the CRM's "Website" tab -> Address sub-tab
    manages (DomainController::updateAddress()). --}}
    @if (in_array('company', $allowedTabs, true))
    <div class="wp-settings-tab-pane {{ $activeTopTab === 'company' ? 'is-active' : '' }}" data-settings-tab-pane="company">
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
    @endif

    {{-- Branding - website_logo/website_logo_ar, the SAME columns +
    admin/assets/images/partner/ folder the CRM's "Website" tab -> Logo
    sub-tab manages (DomainController::websitelogoupdt()). --}}
    @if (in_array('branding', $allowedTabs, true))
    <div class="wp-settings-tab-pane {{ $activeTopTab === 'branding' ? 'is-active' : '' }}" data-settings-tab-pane="branding">
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                {{ __('locale.Branding') }}
            </h2>
            <div class="w-form-alert" style="display:none;" data-settings-alert="branding"></div>
            <form data-settings-form="branding">
                {{-- English Logo / Arabic Logo / Favicon side by side, wrapping on narrow screens. --}}
                <div class="wp-branding-grid">
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
                    {{-- Same Domain row / folder as the logos (website_favicon). Preview =
                    the effective favicon the website itself uses (BrandingAssets: this
                    partner's file -> the global one -> the built-in default). --}}
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.Favicon') }}</label>
                        <div class="wp-logo-upload">
                            <img src="{{ \App\Support\BrandingAssets::url('favicon', $partner->id) }}" alt="Favicon" class="wp-favicon-preview" data-logo-preview="fav">
                            <input type="file" class="w-input" accept="image/png,image/jpeg,image/webp" data-logo-input="fav">
                        </div>
                        <div class="form-text" style="color:var(--w-ink-500);font-size:0.8rem;margin-top:6px;">{{ __('locale.Square image recommended, e.g. 64 × 64 px.') }}</div>
                    </div>
                </div>
                <div class="form-text" style="color:var(--w-ink-500);font-size:0.82rem;margin-bottom:16px;">{{ __('locale.JPG, PNG or WEBP, up to 2MB.') }}</div>
                {{-- Company Profile name under the logo in this Partner Portal's
                sidebar (English / Arabic name per page language). Saved with
                this form (partner_page_contents 'branding' row). --}}
                @php $appendCompanyName = \App\Models\PartnerPageContent::brandingFor(Auth::guard('partner')->id())['append_company_name']; @endphp
                <div class="w-form-row">
                    <label class="wp-switch">
                        <input type="checkbox" role="switch" name="append_company_name" value="1" data-append-company-name @checked($appendCompanyName)>
                        <span class="wp-switch-track" aria-hidden="true"></span>
                        <span class="wp-switch-text">{{ __('locale.Append Company Name') }}</span>
                    </label>
                    <p class="wp-field-hint">{{ __('locale.Shows your Company Profile name under the logo in the dashboard sidebar.') }}</p>
                </div>
                <button type="submit" class="w-btn w-btn-primary" data-settings-submit>{{ __('locale.Save Changes') }}</button>
            </form>
        </div>
    </div>
    @endif

    {{-- WhatsApp - the floating WhatsApp button on this partner's public
    website + portal (partner_page_contents 'whatsapp' row, see
    PartnerPageContent::effectiveWhatsapp()). Prefilled with the saved
    values, otherwise the defaults; an empty number keeps the default one. --}}
    @if (in_array('whatsapp', $allowedTabs, true))
    <div class="wp-settings-tab-pane {{ $activeTopTab === 'whatsapp' ? 'is-active' : '' }}" data-settings-tab-pane="whatsapp">
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 21l2.1-5.4A8.4 8.4 0 1 1 21 11.5Z"/></svg>
                {{ __('locale.WhatsApp') }}
            </h2>
            <div class="w-form-alert" style="display:none;" data-settings-alert="whatsapp"></div>
            <form data-settings-form="whatsapp">
                <div class="wp-info-grid">
                    <div class="w-form-row">
                        <label class="w-form-label" for="wa-number">{{ __('locale.WhatsApp Number') }}</label>
                        <input type="tel" id="wa-number" name="whatsapp_number" class="w-input" dir="ltr" inputmode="tel" maxlength="25"
                               value="{{ $whatsapp['is_custom'] && $whatsapp['number'] !== $whatsappDefaults['number'] ? '+' . $whatsapp['number'] : '' }}"
                               placeholder="+{{ $whatsappDefaults['number'] }}">
                        <div class="wp-field-hint">{{ __('locale.Include the country code, e.g. +966 5X XXX XXXX. Leave empty to use the default number.') }}</div>
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label" for="wa-label">{{ __('locale.Button Label') }}</label>
                        <input type="text" id="wa-label" name="label" class="w-input" maxlength="40"
                               value="{{ $whatsapp['label'] ?? '' }}" placeholder="{{ __('locale.Customer Support') }}" data-wa-label>
                        <div class="wp-field-hint">{{ __('locale.Text shown beside the WhatsApp icon, e.g. Customer Support, WhatsApp Support or Contact Us. Leave empty for the default.') }}</div>
                    </div>
                </div>

                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Preview') }}</label>
                    <div class="wp-wa-preview">
                        {{-- The real floating button partial, in preview mode. --}}
                        @include('worker.partials.whatsapp-float', ['wa' => $whatsapp, 'waPreview' => true])
                        <span class="wp-wa-preview-link" dir="ltr" data-wa-preview-link>{{ $whatsapp['link'] }}</span>
                    </div>
                </div>

                <button type="submit" class="w-btn w-btn-primary" data-settings-submit>{{ __('locale.Save Changes') }}</button>
            </form>
        </div>
    </div>
    @endif

    {{-- Domain - the sub_domain column on the SAME Domain row, resolved by
    ResolvePartnerWebsiteDomain middleware on *.recruitmentcv.com requests. --}}
    {{-- Display-only: subdomain provisioning now runs entirely through the
    CRM's automatic Hostinger creation flow, so there is nothing left for a
    Partner to submit here - no <form>/submit button, and the input is
    disabled (excluded from any accidental submission too, not just
    visually locked). PartnerPortalController::settingsDomainUpdate()
    independently rejects any direct POST regardless of what the UI does. --}}
    @if (in_array('domain', $allowedTabs, true))
    <div class="wp-settings-tab-pane {{ $activeTopTab === 'domain' ? 'is-active' : '' }}" data-settings-tab-pane="domain">
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20z"/></svg>
                {{ __('locale.Domain') }}
            </h2>
            <div class="w-form-row">
                <label class="w-form-label">{{ __('locale.Your RecruitmentCV Subdomain') }}</label>
                <div class="wp-domain-preview">
                    <input type="text" value="{{ $domain->sub_domain }}" placeholder="{{ __('locale.Not configured yet') }}" disabled readonly>
                    <span>.{{ \App\Support\RecruitmentDomain::root() }}</span>
                </div>
                <div class="form-text" style="color:var(--w-ink-500);font-size:0.82rem;margin-top:8px;">{{ __('locale.Your subdomain is managed automatically and cannot be changed here.') }}</div>
            </div>
            <div class="w-form-row">
                <span class="w-form-label">{{ __('locale.Status') }}</span>
                <div>
                    <span class="wp-badge is-{{ $accountStatusBadges[$accountStatus] }}" data-domain-account-status="{{ $accountStatus }}">
                        {{ $accountStatusLabels[$accountStatus] }}
                    </span>
                    @if (isset($accountStatusNotes[$accountStatus]))
                        <span style="color:var(--w-ink-500);font-size:0.82rem;margin-inline-start:8px;">{{ $accountStatusNotes[$accountStatus] }}</span>
                    @endif
                </div>
            </div>
            @if (!empty($domain->hostinger_status))
                <div class="w-form-row">
                    <span class="w-form-label">{{ __('locale.Provisioning Status') }}</span>
                    <div>
                        <span class="wp-badge is-{{ $domain->hostinger_status === 'failed' ? 'danger' : (in_array($domain->hostinger_status, ['success', 'created'], true) ? 'success' : 'warning') }}">
                            {{ ucfirst($domain->hostinger_status) }}
                        </span>
                        @if (!empty($domain->hostinger_error))
                            <span style="color:var(--w-ink-500);font-size:0.82rem;margin-inline-start:8px;">{{ $domain->hostinger_error }}</span>
                        @endif
                    </div>
                </div>
            @endif
            @if ($domain->isLiveSubdomain())
                <div class="w-form-row">
                    <span class="w-form-label">{{ __('locale.Live URL') }}</span>
                    <div>
                        <a href="https://{{ $domain->full_domain }}" target="_blank" rel="noopener">https://{{ $domain->full_domain }}</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- SMTP - this partner's own ordered SMTP list (the same list and
    backend as CRM -> Website -> SMTP: App\Support\SmtpSettings, actions in
    PartnerPortalController::smtp*). Emails from this partner's website and
    dashboard try them in order, then the default RecruitmentCV settings.
    Passwords are write-only - never rendered. --}}
    @if (in_array('smtp', $allowedTabs, true))
    <div class="wp-settings-tab-pane {{ $activeTopTab === 'smtp' ? 'is-active' : '' }}" data-settings-tab-pane="smtp" id="smtp">
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>
                {{ __('locale.SMTP') }}
            </h2>
            <p class="wp-field-hint wp-smtp-intro">{{ __('locale.Emails from your website and dashboard (order confirmations, verification codes, email changes) are sent with your SMTPs, one at a time in this order: the next SMTP is used only if the one before it fails. When none is enabled or all fail, the default RecruitmentCV email settings are used.') }}</p>
            <div class="w-form-alert" style="display:none;" data-settings-alert="smtp"></div>
            <div data-smtp-root data-error-generic="{{ __('locale.Something went wrong. Please try again.') }}">
                @include('worker.partner.partials.smtp-list', ['smtps' => $smtps])
            </div>
        </div>
    </div>
    @endif

    {{-- Website Config (Home/About/Contact/Privacy/Terms) - its own file,
    included here rather than inlined, since this one tab-pane is already
    as large as the rest of this page combined. --}}
    @if (in_array('website-config', $allowedTabs, true))
    <div class="wp-settings-tab-pane {{ $activeTopTab === 'website-config' ? 'is-active' : '' }}" data-settings-tab-pane="website-config">
        @include('worker.partner.website-config-partial', ['activeWcPage' => $activeWcPage])
    </div>
    @endif
@endsection

@section('page-script')
    <script>
        window.settingsUrls = {
            company: "{{ route('worker.partner.settings.company') }}",
            logo: "{{ route('worker.partner.settings.logo') }}",
            domain: "{{ route('worker.partner.settings.domain') }}",
            whatsapp: "{{ route('worker.partner.settings.whatsapp') }}"
        };
        window.WorkerBranchesUpdateUrl = "{{ route('worker.partner.website-config.branches.update') }}";
        window.WorkerBranchesConfirmDelete = "{{ __('locale.Are you sure you want to delete this branch?') }}";
    </script>
    <script src="{{ asset('worker/js/partner-settings.js') }}?v={{ @filemtime(public_path('worker/js/partner-settings.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/partner-smtp.js') }}?v={{ @filemtime(public_path('worker/js/partner-smtp.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/partner-website-config.js') }}?v={{ @filemtime(public_path('worker/js/partner-website-config.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/partner-website-config-branches.js') }}?v={{ @filemtime(public_path('worker/js/partner-website-config-branches.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/partner-website-config-collapse.js') }}?v={{ @filemtime(public_path('worker/js/partner-website-config-collapse.js')) ?: time() }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="{{ asset('worker/js/partner-website-config-quill.js') }}?v={{ @filemtime(public_path('worker/js/partner-website-config-quill.js')) ?: time() }}"></script>
@endsection
