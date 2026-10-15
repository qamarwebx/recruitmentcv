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
        // Back from "Add SMTP With Google" (PartnerSmtpGoogleController::complete()): the SMTP tab.
        if (request()->query('tab') === 'smtp' && in_array('smtp', $allowedTabs, true)) {
            $activeTopTab = 'smtp';
        }
        $smtpGoogleNotice = session('smtp_google_notice');
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
                {{-- Fields, order and labels: App\Support\CompanyProfile::FIELDS - the
                same definition CRM -> Website -> Company Profile uses. Empty =
                the CRM Global value is used on the website. --}}
                <div class="wp-info-grid" style="grid-template-columns:repeat(2,1fr);">
                    @foreach (\App\Support\CompanyProfile::FIELDS as $__field => [$__input, $__label, $__type])
                    <div class="w-form-row">
                        <label class="w-form-label">{{ __('locale.' . $__label) }}</label>
                        @if ($__type === 'textarea')
                        <textarea name="{{ $__input }}" class="w-input" @if (\App\Support\CompanyProfile::isArabic($__field)) dir="rtl" @endif rows="3">{{ $domain->{$__input} }}</textarea>
                        @else
                        <input type="{{ $__type }}" name="{{ $__input }}" class="w-input" @if (\App\Support\CompanyProfile::isArabic($__field)) dir="rtl" @endif value="{{ $domain->{$__input} }}">
                        @endif
                    </div>
                    @endforeach
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
            <form data-settings-form="branding" data-msg-no-changes="{{ __('locale.No changes to save.') }}">
                {{-- Seven images in four sections, each with its own Width & Height (px): Website
                Header / Website Footer / Portal Branding (English + Arabic each) and the Favicon.
                Preview = what is used now (App\Support\BrandingAssets: your image -> your header
                logo for footer / portal -> Partner Global -> global -> built-in), drawn at its
                configured size (object-fit: contain). Header logos + favicon: your Domain row;
                footer + portal logos and every size: your 'branding' row
                (App\Support\LogoDimensions; empty size = the CRM Global / default one). --}}
                @php
                    $logoSize = \App\Support\LogoDimensions::formData($partner->id);
                    $brandSections = [
                        'Website Header' => ['hint' => 'Shown at the top of your website, on a light background.', 'cards' => [['en', 'header_logo_en', 'English Logo'], ['ar', 'header_logo_ar', 'Arabic Logo']]],
                        'Website Footer' => ['hint' => 'Shown in your website footer, on a dark green background. Without one, your header logo is used.', 'cards' => [['footer_en', 'footer_logo_en', 'English Logo'], ['footer_ar', 'footer_logo_ar', 'Arabic Logo']]],
                        'Portal Branding' => ['hint' => 'Shown in this dashboard sidebar, on a dark green background. Without one, your header logo is used.', 'cards' => [['portal_en', 'portal_logo_en', 'English Logo'], ['portal_ar', 'portal_logo_ar', 'Arabic Logo']]],
                        'Favicon' => ['hint' => 'The small icon in the browser tab. Square image, e.g. 64 × 64 px.', 'cards' => [['fav', 'favicon', 'Favicon']]],
                    ];
                    $brandSourceLabels = ['partner' => 'Your image', 'partner_header' => 'Using your header logo', 'footer' => 'Using your footer logo'];
                @endphp
                @foreach ($brandSections as $sectionTitle => $section)
                    <fieldset class="wp-branding-section" data-branding-section="{{ \Illuminate\Support\Str::slug($sectionTitle) }}">
                        <legend>{{ __('locale.' . $sectionTitle) }}</legend>
                        <p class="wp-field-hint" style="margin-top:0;">{{ __('locale.' . $section['hint']) }}</p>
                        <div class="wp-branding-grid">
                            @foreach ($section['cards'] as [$logoKey, $logoSlot, $logoLabel])
                                @php
                                    [, $logoSource] = \App\Support\BrandingAssets::resolve($logoSlot, $partner->id);
                                    [$widthField, $heightField] = \App\Support\LogoDimensions::SLOT_FIELDS[$logoSlot];
                                    $logoW = $logoSize['values'][$widthField];
                                    $logoH = $logoSize['values'][$heightField];
                                @endphp
                                <div class="wp-brand-card" data-brand-card="{{ $logoKey }}">
                                    <div class="wp-brand-card-head">
                                        <span class="w-form-label" style="margin:0;">{{ __('locale.' . $logoLabel) }}</span>
                                        <span class="wp-brand-source{{ $logoSource === 'partner' ? ' is-own' : '' }}">{{ __('locale.' . ($brandSourceLabels[$logoSource] ?? 'Default')) }}</span>
                                    </div>
                                    <div class="wp-brand-preview{{ in_array($logoSlot, \App\Support\BrandingAssets::DARK_SLOTS, true) ? ' is-dark' : '' }}">
                                        <img src="{{ \App\Support\BrandingAssets::url($logoSlot, $partner->id) }}" alt="{{ __('locale.' . $logoLabel) }}" data-logo-preview="{{ $logoKey }}"
                                            style="height:{{ (int) $logoH }}px;max-width:min({{ (int) $logoW }}px, 100%);">
                                    </div>
                                    <input type="file" class="w-input" accept="{{ \App\Support\ImageUpload::ACCEPT }}" data-logo-input="{{ $logoKey }}" aria-label="{{ __('locale.' . $sectionTitle) }} - {{ __('locale.' . $logoLabel) }}">
                                    <div class="wp-logo-size-grid wp-brand-size-grid">
                                        @foreach (['w' => [$widthField, 'Width'], 'h' => [$heightField, 'Height']] as $dim => [$field, $dimLabel])
                                            @php [, $min, $max] = \App\Support\LogoDimensions::FIELDS[$field]; @endphp
                                            <div>
                                                <label class="w-form-label" for="logo-size-{{ $field }}">{{ __('locale.' . $dimLabel) }}</label>
                                                <div class="wp-input-unit" dir="ltr">
                                                    <input type="number" id="logo-size-{{ $field }}" name="{{ $field }}" class="w-input"
                                                        min="{{ $min }}" max="{{ $max }}" step="1" inputmode="numeric"
                                                        value="{{ $logoSize['own'][$field] }}" placeholder="{{ $logoSize['fallback'][$field] }}"
                                                        data-logo-size data-size-for="{{ $logoKey }}" data-size-dim="{{ $dim }}" data-saved="{{ $logoSize['own'][$field] }}"
                                                        @if ($logoSlot === 'favicon') data-size-square @endif>
                                                    <span class="wp-input-unit-suffix" aria-hidden="true">px</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="wp-field-hint" data-size-now="{{ $logoKey }}">{{ __('locale.Now: :w × :h px', ['w' => (int) $logoW, 'h' => (int) $logoH]) }} · {{ __('locale.:min–:max px', ['min' => \App\Support\LogoDimensions::FIELDS[$widthField][1], 'max' => \App\Support\LogoDimensions::FIELDS[$widthField][2]]) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach
                <div class="form-text" style="color:var(--w-ink-500);font-size:0.82rem;margin-bottom:16px;">{{ __('locale.JPG, PNG, GIF, WEBP or SVG, up to 2MB.') }} {{ __('locale.Sizes are the maximum space each logo can use: it keeps its proportions (never stretched or cropped) and gets smaller on phones and tablets. Leave a size empty to use the default.') }}</div>
                {{-- Company Profile name under the logo in this Partner Portal's
                sidebar (English / Arabic name per page language). Saved with
                this form (partner_page_contents 'branding' row) only when
                changed; until then the CRM Global setting applies
                (PartnerPageContent::brandingFor()). "Use default" removes
                this partner's own value again. --}}
                @php $appendCompanyName = \App\Models\PartnerPageContent::brandingFor(Auth::guard('partner')->id()); @endphp
                <div class="w-form-row">
                    <label class="wp-switch">
                        <input type="checkbox" role="switch" name="append_company_name" value="1" data-append-company-name
                            data-saved="{{ $appendCompanyName['append_company_name'] ? '1' : '0' }}" @checked($appendCompanyName['append_company_name'])>
                        <span class="wp-switch-track" aria-hidden="true"></span>
                        <span class="wp-switch-text">{{ __('locale.Append Company Name') }}</span>
                    </label>
                    <p class="wp-field-hint">{{ __('locale.Shows your Company Profile name under the logo in the dashboard sidebar.') }}</p>
                    <p class="wp-field-hint" data-append-hint="default" @if ($appendCompanyName['append_company_name_configured']) hidden @endif>
                        {{ $appendCompanyName['append_company_name_default'] ? __('locale.Using the default setting (On).') : __('locale.Using the default setting (Off).') }}
                    </p>
                    <p class="wp-field-hint" data-append-hint="custom" @unless ($appendCompanyName['append_company_name_configured']) hidden @endunless>
                        {{ __('locale.Using your own setting.') }}
                        <button type="button" data-append-company-name-default
                            style="background:none;border:0;padding:0;font:inherit;color:var(--w-primary);text-decoration:underline;cursor:pointer;">{{ __('locale.Use default') }}</button>
                    </p>
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

    {{-- Domain - the signed-in partner's own website address, read-only.
    Which one comes from the partner's own domains row (never the request
    host) through App\Support\PartnerDomains: its Own Domain when that is
    the selected Domain Type, else its *.recruitmentcv.com subdomain, else
    "not configured". Both are managed only in CRM (Partner -> Website ->
    Domain): no <form>/submit button here and the inputs are disabled;
    PartnerPortalController::settingsDomainUpdate() independently rejects
    any direct POST regardless of what the UI does. --}}
    @if (in_array('domain', $allowedTabs, true))
    @php
        $domainMode = \App\Support\PartnerDomains::displayMode($domain->exists ? $domain : null);
        $ownDomain = $domainMode === 'custom' ? \App\Support\PartnerDomains::ownDomainState($domain) : null;
        $domainBadge = ['active' => 'success', 'verified' => 'success', 'pending' => 'warning', 'unknown' => 'neutral', 'inactive' => 'neutral'];
        $domainLabel = [
            'active' => __('locale.Active'),
            'verified' => __('locale.Verified'),
            'pending' => __('locale.Pending'),
            'unknown' => __('locale.Not available'),
            'inactive' => __('locale.Inactive'),
        ];
    @endphp
    <div class="wp-settings-tab-pane {{ $activeTopTab === 'domain' ? 'is-active' : '' }}" data-settings-tab-pane="domain" data-domain-mode="{{ $domainMode }}">
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20z"/></svg>
                {{ __('locale.Domain') }}
            </h2>
            @if ($domainMode === 'custom')
                <div class="w-form-row">
                    <label class="w-form-label" for="wpOwnDomain">{{ __('locale.Your Own Domain') }}</label>
                    <div class="wp-domain-preview">
                        <input type="text" id="wpOwnDomain" value="{{ $ownDomain['domain'] }}" dir="ltr" disabled readonly aria-readonly="true">
                        <span title="{{ __('locale.Your custom domain is managed automatically and cannot be changed here.') }}" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                        </span>
                    </div>
                    <div class="form-text" style="color:var(--w-ink-500);font-size:0.82rem;margin-top:8px;">{{ __('locale.Your custom domain is managed automatically and cannot be changed here.') }}</div>
                </div>
                @foreach (['status' => __('locale.Status'), 'verification' => __('locale.Verification Status'), 'provisioning' => __('locale.Provisioning Status'), 'ssl' => __('locale.SSL')] as $key => $label)
                    <div class="w-form-row">
                        <span class="w-form-label">{{ $label }}</span>
                        <div>
                            <span class="wp-badge is-{{ $domainBadge[$ownDomain[$key]] }}" data-own-domain-{{ $key }}="{{ $ownDomain[$key] }}">{{ $domainLabel[$ownDomain[$key]] }}</span>
                            @if ($key === 'status' && $ownDomain['status'] === 'pending')
                                <span style="color:var(--w-ink-500);font-size:0.82rem;margin-inline-start:8px;">{{ __('locale.Your own domain is being set up by the administrator.') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if ($ownDomain['live_url'])
                    <div class="w-form-row">
                        <span class="w-form-label">{{ __('locale.Live URL') }}</span>
                        <div>
                            <a href="{{ $ownDomain['live_url'] }}" target="_blank" rel="noopener" dir="ltr">{{ $ownDomain['live_url'] }}</a>
                        </div>
                    </div>
                @endif
            @elseif ($domainMode === 'subdomain')
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
            @else
                <div class="w-form-row">
                    <label class="w-form-label">{{ __('locale.Domain') }}</label>
                    <div class="wp-domain-preview">
                        <input type="text" value="{{ __('locale.Domain not configured') }}" disabled readonly aria-readonly="true" data-domain-not-configured>
                    </div>
                    <div class="form-text" style="color:var(--w-ink-500);font-size:0.82rem;margin-top:8px;">{{ __('locale.Your website address is set up by the administrator.') }}</div>
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
            {{-- Also the result of an "Add SMTP With Google" sign-in (flashed by PartnerSmtpGoogleController::complete()). --}}
            <div class="w-form-alert {{ is_array($smtpGoogleNotice) ? ($smtpGoogleNotice[0] === 'success' ? 'is-success' : 'is-error') : '' }}" @unless (is_array($smtpGoogleNotice)) style="display:none;" @endunless data-settings-alert="smtp" role="status">{{ is_array($smtpGoogleNotice) ? $smtpGoogleNotice[1] : '' }}</div>
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
    <script src="{{ asset('worker/js/partner-website-config-lists.js') }}?v={{ @filemtime(public_path('worker/js/partner-website-config-lists.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/partner-website-config-collapse.js') }}?v={{ @filemtime(public_path('worker/js/partner-website-config-collapse.js')) ?: time() }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script src="{{ asset('worker/js/partner-website-config-quill.js') }}?v={{ @filemtime(public_path('worker/js/partner-website-config-quill.js')) ?: time() }}"></script>
@endsection
