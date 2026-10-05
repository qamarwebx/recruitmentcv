<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Partner Portal') — Qamr Worker Portal</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- The signed-in partner's Branding favicon (Partner Website → Branding),
    otherwise the default icon. Shared by every /partner/* page. --}}
    <link rel="icon" href="{{ Auth::guard('partner')->user()?->domain?->faviconUrl() ?: \App\Models\Domain::defaultFaviconUrl() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('worker/css/style.css') }}?v={{ @filemtime(public_path('worker/css/style.css')) ?: time() }}">
    <link rel="stylesheet" href="{{ asset('worker/css/portal.css') }}?v={{ @filemtime(public_path('worker/css/portal.css')) ?: time() }}">
    @yield('page-style')
</head>
<body class="worker-scope wp-body">
    @php
        $__partner = Auth::guard('partner')->user();
        $__partnerLabel = $__partner->rec_off_name ?: $__partner->owner_name ?: 'Partner';
        // The signed-in partner's own Full Name (Account -> Full Name) - shown
        // in the header profile button only.
        $__partnerFullName = trim((string) $__partner->owner_name);
        $__partnerHeaderName = $__partnerFullName !== '' ? $__partnerFullName : __('locale.Partner');
        // Signed in as a team member (App\Support\PartnerTeam): their own name.
        $__teamMember = \App\Support\PartnerTeam::current();
        if ($__teamMember) {
            $__partnerHeaderName = $__teamMember->full_name;
        }
        $__partnerInitials = collect(explode(' ', $__partnerHeaderName))->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
        $__notifications = $notifications ?? collect();

        // Resolved once here (the one shared layout every /partner/* page
        // renders through), not per-page - $__partner->domain is an Eloquent
        // relation, so even if something below reads it again later in this
        // same request it's served from the already-loaded relation, not a
        // second query. Null (no Domain row, or that locale's logo column
        // empty/missing on disk) means "keep the current/default Worker
        // logo exactly as before" - Partner::portalLogoFile() already
        // handles that safely; calling it on $__partner (never null for an
        // authenticated request) rather than on a separately-queried
        // Domain row avoids a "call to a member function on null" fatal
        // for every partner who simply has no Domain row at all.
        $__isArabic = app()->getLocale() === 'ar';
        $__partnerLogoFile = $__partner->portalLogoFile($__isArabic);
        $__partnerLogoUrl = $__partnerLogoFile ? asset('admin/assets/images/partner/' . $__partnerLogoFile) : null;
        $__defaultPartnerLogo = asset('user/img/logo/' . ($__isArabic ? 'new_logo_Arabic_white.webp' : 'Logo_eng_white.webp'));

        // Same $__partner->domain relation as above (already loaded, no extra
        // query) - same gating as the Website > Domain tab's own Live URL
        // link: the subdomain is live (Domain::isLiveSubdomain(), the rule
        // ResolvePartnerWebsiteDomain serves sites by). Null means "hide gracefully" per spec, never
        // a fallback URL (unlike Partner::portalBaseUrl(), which is used for
        // post-login redirects and intentionally falls back to app().url).
        $__partnerDomain = $__partner->domain;
        $__partnerLiveUrl = ($__partnerDomain && $__partnerDomain->isLiveSubdomain())
            ? 'https://' . $__partnerDomain->full_domain
            : null;

        // Website -> Branding "Append Company Name": the signed-in partner's
        // Company Profile name beside the logo (same rule as the public site
        // and Partner Login - SiteBrand::appendedName()); '' = logo only.
        $__brandName = \App\Support\SiteBrand::appendedName(
            \App\Support\SiteBrand::companyFromDomain($__partnerDomain),
            $__partnerDomain ? \App\Models\PartnerPageContent::brandingFor($__partner->id)['append_company_name'] : false
        );
    @endphp

    <div class="wp-shell" data-wp-shell>
        <aside class="wp-sidebar" data-wp-sidebar>
            <div class="wp-sidebar-brand">
                {{-- "Append Company Name": logo + name side by side. --}}
                @if ($__brandName !== '')<div class="wp-sidebar-brand-row">@endif
                @if ($__partnerLogoUrl)
                    <img src="{{ $__partnerLogoUrl }}" alt="{{ $__partnerLabel }}" onerror="this.onerror=null;this.src='{{ $__defaultPartnerLogo }}';this.alt='Qamr International';">
                @else
                    <x-brand-logo mode="dark" alt="Qamr International" />
                @endif
                @if ($__brandName !== '')
                    <span class="wp-sidebar-brand-name" title="{{ $__brandName }}">{{ $__brandName }}</span>
                    </div>
                @endif
                <button type="button" class="wp-collapse-toggle" data-wp-collapse-toggle aria-label="Collapse sidebar">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button type="button" class="w-nav-toggle wp-sidebar-close" data-wp-sidebar-close aria-label="Close menu">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            

            <nav class="wp-nav">
                @if (\App\Support\PartnerTeam::canOpen('candidates'))
                <a href="{{ route('worker.partner.candidates') }}" class="wp-partner-card {{ request()->routeIs('worker.partner.candidates', 'worker.partner.candidates.*') ? 'is-active' : '' }}">
                    <span class="wp-partner-avatar">
                        {{-- CV / resume document, same inline stroke icon style as the menu. --}}
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M16 13H8M16 17H8M10 9H8"/></svg>
                    </span>
                    <div>
                        <span class="wp-partner-name">{{ __('locale.Worker CV') }}</span>
                        <span class="wp-partner-role">{{ __('locale.All Candidate CV') }}</span>
                    </div>
                </a>
                @endif

              
                @if (\App\Support\PartnerTeam::canOpen('dashboard'))
                <a href="{{ route('worker.partner.dashboard') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.dashboard') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12 12 3l9 9"/><path d="M5 10v10h14V10"/></svg>
                    <span>{{ __('locale.Dashboard') }}</span>
                </a>
                @endif
                @if (\App\Support\PartnerTeam::canOpen('orders'))
                <a href="{{ route('worker.partner.orders') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.orders', 'worker.partner.orders.*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
                    <span>{{ __('locale.Orders') }}</span>
                </a>
                @endif
              
                @if (\App\Support\PartnerTeam::canOpen('employer'))
                <a href="{{ route('worker.partner.employer') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.employer', 'worker.partner.employer.*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m-1 4h1m4-4h1m-1 4h1M9 21v-4h6v4"/></svg>
                    <span>{{ __('locale.Employer') }}</span>
                </a>
                @endif
                @if (\App\Support\PartnerTeam::canOpen('payment'))
                <a href="{{ route('worker.partner.payment') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.payment', 'worker.partner.payment.*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>
                    <span>{{ __('locale.Payment') }}</span>
                </a>
                @endif

                @if (\App\Support\PartnerTeam::canOpen('customers'))
                <a href="{{ route('worker.partner.customers') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.customers', 'worker.partner.customers.*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>{{ __('locale.Customers') }}</span>
                </a>
                @endif
                @if (\App\Support\PartnerTeam::canOpen('website-visitors'))
                <a href="{{ route('worker.partner.website-visitors') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.website-visitors', 'worker.partner.website-visitors.*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <span>{{ __('locale.Website Visitor') }}</span>
                </a>
                @endif

                <div class="wp-nav-label">{{ __('locale.Account') }}</div>
                @if (\App\Support\PartnerTeam::canOpen('prices'))
                <a href="{{ route('worker.partner.prices') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.prices', 'worker.partner.prices.*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>
                    <span>{{ __('locale.Price Update') }}</span>
                </a>
                @endif
                @if (\App\Support\PartnerTeam::canOpen('website'))
                <a href="{{ route('worker.partner.website') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.website', 'worker.partner.website.*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span>{{ __('locale.Website') }}</span>
                </a>
                @endif
                <a href="{{ route('worker.partner.account') }}" class="wp-nav-link {{ (request()->routeIs('worker.partner.account', 'worker.partner.account.*') || request()->routeIs('worker.partner.profile', 'worker.partner.profile.*')) ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                    <span>{{ __('locale.My Account') }}</span>
                </a>
                @if (\App\Support\PartnerTeam::isOwner())
                <a href="{{ route('worker.partner.team-members') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.team-members', 'worker.partner.team-members.*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
                    <span>{{ __('locale.Team Members') }}</span>
                </a>
                @endif
            </nav>

            <div class="wp-sidebar-footer">
                {{-- Main RecruitmentCV site (configured root domain), new tab. --}}
                <a href="https://{{ \App\Support\RecruitmentDomain::root() }}" target="_blank" rel="noopener">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span>RecruitmentCV.com</span>
                </a>
                <a href="{{ route('worker.privacy') }}" target="_blank" rel="noopener">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s7-3.5 7-9V5l-7-3-7 3v8c0 5.5 7 9 7 9z"/></svg>
                    <span>{{ __('locale.Privacy Policy') }}</span>
                </a>
                <a href="{{ route('worker.terms') }}" target="_blank" rel="noopener">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M8 7h8M8 11h8M8 15h5"/></svg>
                    <span>{{ __('locale.Terms of Service') }}</span>
                </a>
            </div>

        </aside>

        <div class="wp-drawer-backdrop" data-wp-drawer-backdrop></div>

        <div class="wp-main">
            <header class="wp-topbar">
                <button type="button" class="wp-menu-toggle" data-wp-menu-toggle aria-label="Open menu">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="wp-page-title">@yield('page-title')</div>

                @if ($__partnerLiveUrl)
                    <div class="wp-live-url-group">
                        <a href="{{ $__partnerLiveUrl }}" target="_blank" rel="noopener" class="wp-live-url" title="{{ $__partnerLiveUrl }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            <span class="wp-live-url-text">{{ $__partnerLiveUrl }}</span>
                        </a>
                        {{-- Copies the same dynamic Live URL shown above (portal.js initCopyLinks). --}}
                        <button type="button" class="wp-live-url-copy" data-copy-text="{{ $__partnerLiveUrl }}"
                            aria-label="{{ __('locale.Copy link') }}" title="{{ __('locale.Copy link') }}">
                            <svg class="wp-copy-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            <svg class="wp-copied-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>
                            <span class="wp-live-url-copied" role="status" aria-live="polite">{{ __('locale.Copied') }}</span>
                        </button>
                        {{-- Share the PUBLIC website URL (never this /partner/* page):
                        native share sheet where available, otherwise copy
                        (portal.js initShareLinks). --}}
                        <button type="button" class="wp-live-url-copy wp-live-url-share"
                            data-share-url="{{ $__partnerLiveUrl }}" data-share-title="{{ $__partnerLabel }}"
                            aria-label="{{ __('locale.Share Website') }}" title="{{ __('locale.Share Website') }}">
                            <svg class="wp-share-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/></svg>
                            <span class="wp-live-url-copied" role="status" aria-live="polite">{{ __('locale.Link copied') }}</span>
                        </button>
                    </div>
                @endif

                <div class="wp-topbar-actions">
                    @include('worker.partials.language-switcher', ['switchRoute' => 'worker.partner.lang.switch'])

                    <div class="wp-dropdown-wrap">
                        <button type="button" class="wp-icon-btn" data-wp-dropdown-trigger="notifications" aria-label="{{ __('locale.Notifications') }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                            @if ($__notifications->count() > 0)
                                <span class="wp-icon-dot"></span>
                            @endif
                        </button>
                        <div class="wp-dropdown" data-wp-dropdown="notifications">
                            <div class="wp-dropdown-head">{{ __('locale.Notifications') }}</div>
                            @forelse ($__notifications as $notif)
                                <div class="wp-notif-item">
                                    <span class="wp-notif-dot"></span>
                                    <div>
                                        <strong>{{ __('locale.Booking awaiting confirmation') }}</strong>
                                        <span>{{ (app()->getLocale() === 'ar' && !empty($notif->arcand_name)) ? $notif->arcand_name : ($notif->cand_name ?? __('locale.Candidate')) }} · Ref #{{ $notif->reference_no }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="wp-notif-item">
                                    <div><span>{{ __("locale.You're all caught up — no pending notifications.") }}</span></div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="wp-dropdown-wrap">
                        <button type="button" class="wp-profile-trigger" data-wp-dropdown-trigger="profile">
                            <span class="wp-partner-avatar" style="padding-top: 6px;color: white;">{{ $__partnerInitials ?: 'P' }}</span>
                            <span>{{ $__partnerHeaderName }}</span>
                        </button>
                        <div class="wp-dropdown" data-wp-dropdown="profile">
                            <a href="{{ route('worker.partner.account') }}" class="wp-menu-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                                {{ __('locale.My Account') }}
                            </a>
                            <form action="{{ route('worker.partner.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="wp-menu-item is-danger">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>
                                    {{ __('locale.Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="wp-content">
                @if (session('success'))
                    <div class="w-form-alert is-success" style="margin-bottom:20px;">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="w-form-alert is-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Not for login/register here (partner is already authenticated) - only
    for its "add-mobile" mode, reused by the Hire Now gate and the Profile
    page's Add/Change Number action (see partner-auth.js's
    window.WorkerPartnerAuth.openAddMobile). --}}
    @include('worker.partials.partner-auth-modal')
    {{-- Once per login, only when Edit Account Details are incomplete. --}}
    @php $accountPromptMissing = \App\Support\PartnerAccountPrompt::take(); @endphp
    @if ($accountPromptMissing)
        @include('worker.partner.partials.account-details-prompt', ['missing' => $accountPromptMissing])
    @endif
    {{-- Portal "Customer Support" = RecruitmentCV/Qamr platform support for
    the partner's own staff: the CENTRAL WhatsApp settings (partner_id NULL
    row, CRM -> Website -> WhatsApp), never the partner's own Website ->
    WhatsApp number, which is for that partner's customers on its public
    site. Explicit null so no outer variable can change it. --}}
    {{-- Central support button, labelled with this partner's saved Button Label. --}}
    @include('worker.partials.whatsapp-float', ['wa' => \App\Models\PartnerPageContent::portalWhatsapp($__partner->id)])

    <script src="{{ asset('worker/js/portal.js') }}?v={{ @filemtime(public_path('worker/js/portal.js')) ?: time() }}"></script>
    @include('worker.partials.partner-auth-script')
    @yield('page-script')
    @if ($accountPromptMissing)
        {{-- After page-script: reuses that page's jQuery/select2 when present, else loads them. --}}
        <script src="{{ asset('worker/js/partner-account-prompt.js') }}?v={{ @filemtime(public_path('worker/js/partner-account-prompt.js')) ?: time() }}"
            data-jquery-url="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"
            data-select2-url="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"
            data-select2-css-url="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}"
            data-select-init-url="{{ asset('worker/js/profile-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/profile-vendor-init.js')) ?: time() }}"
            data-i18n="{{ json_encode([
                'saving' => __('locale.Saving…'),
                'save' => __('locale.Save Changes'),
                'saved' => __('locale.Account details saved.'),
                'errRequired' => __('locale.Please fill in all required fields.'),
                'errTechnical' => __('locale.A technical error occurred. Please try again shortly.'),
            ]) }}"></script>
    @endif
</body>
</html>
