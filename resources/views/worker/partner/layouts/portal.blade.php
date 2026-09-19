<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Partner Portal') — Qamr Worker Portal</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('user/img/favicon.png') }}">

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
        $__partnerInitials = collect(explode(' ', trim($__partnerLabel)))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
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
        // query) - same gating condition already used by the Settings > Domain
        // tab's own Live URL link (profile.blade.php): active status + a
        // non-empty sub_domain. Null means "hide gracefully" per spec, never
        // a fallback URL (unlike Partner::portalBaseUrl(), which is used for
        // post-login redirects and intentionally falls back to app().url).
        $__partnerDomain = $__partner->domain;
        $__partnerLiveUrl = ($__partnerDomain && $__partnerDomain->status === 'active' && !empty($__partnerDomain->sub_domain))
            ? 'https://' . $__partnerDomain->full_domain
            : null;
    @endphp

    <div class="wp-shell" data-wp-shell>
        <aside class="wp-sidebar" data-wp-sidebar>
            <div class="wp-sidebar-brand">
                @if ($__partnerLogoUrl)
                    <img src="{{ $__partnerLogoUrl }}" alt="{{ $__partnerLabel }}" onerror="this.onerror=null;this.src='{{ $__defaultPartnerLogo }}';this.alt='Qamr International';">
                @else
                    <x-brand-logo mode="dark" alt="Qamr International" />
                @endif
                <button type="button" class="wp-collapse-toggle" data-wp-collapse-toggle aria-label="Collapse sidebar">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button type="button" class="w-nav-toggle wp-sidebar-close" data-wp-sidebar-close aria-label="Close menu">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            

            <nav class="wp-nav">
                <a href="{{ route('worker.home') }}" class="wp-nav-link wp-nav-link-back">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    <span>{{ __('locale.Back to Website') }}</span>
                </a>
               
                <a href="{{ route('worker.partner.candidates') }}" class="wp-partner-card {{ request()->routeIs('worker.partner.candidates*') ? 'is-active' : '' }}">
                    <span class="wp-partner-avatar">QW</span>
                    <div>
                        <span class="wp-partner-name">{{ __('locale.Worker CV') }}</span>
                        <span class="wp-partner-role">{{ __('locale.All Candidate CV') }}</span>
                    </div>
                </a>

              
                <a href="{{ route('worker.partner.dashboard') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.dashboard') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12 12 3l9 9"/><path d="M5 10v10h14V10"/></svg>
                    <span>{{ __('locale.Dashboard') }}</span>
                </a>
                <a href="{{ route('worker.partner.orders') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.orders*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
                    <span>{{ __('locale.Orders') }}</span>
                </a>
              
                <a href="{{ route('worker.partner.employer') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.employer*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m-1 4h1m4-4h1m-1 4h1M9 21v-4h6v4"/></svg>
                    <span>{{ __('locale.Employer') }}</span>
                </a>

                <div class="wp-nav-label">{{ __('locale.Account') }}</div>
                <a href="{{ route('worker.partner.website') }}" class="wp-nav-link {{ request()->routeIs('worker.partner.website*') ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <span>{{ __('locale.Website') }}</span>
                </a>
                <a href="{{ route('worker.partner.account') }}" class="wp-nav-link {{ (request()->routeIs('worker.partner.account*') || request()->routeIs('worker.partner.profile*')) ? 'is-active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                    <span>{{ __('locale.My Account') }}</span>
                </a>
            </nav>

            <div class="wp-sidebar-footer">
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
                    <a href="{{ $__partnerLiveUrl }}" target="_blank" rel="noopener" class="wp-live-url" title="{{ $__partnerLiveUrl }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        <span class="wp-live-url-text">{{ $__partnerLiveUrl }}</span>
                    </a>
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
                            <span>{{ $__partnerLabel }}</span>
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
    @include('worker.partials.whatsapp-float')

    <script src="{{ asset('worker/js/portal.js') }}?v={{ @filemtime(public_path('worker/js/portal.js')) ?: time() }}"></script>
    @include('worker.partials.partner-auth-script')
    @yield('page-script')
</body>
</html>
