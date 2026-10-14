{{-- Mobile bottom navigation (phones, < 768px - style.css "Mobile bottom
navigation"; hidden on tablet / desktop). One copy, from worker.layouts.app,
so every public page of every host (recruitmentcv.com, partner subdomains,
own domains) gets it; not on pages without the site chrome (/partner/login)
and not in the Partner Portal (its own layout and menu). Links are route()
URLs of the current host, never a fixed domain.

Home / My Order / All Workers CV / Menu. On the resumes listing itself
(route worker.resumes - the route name, not the URL, so candidate pages and
other /resumes... URLs keep All Workers CV) the third item is Filter: it
opens the page's filter drawer (data-filter-open, main.js initFilterPanel).
Menu opens the header's mobile menu (data-nav-proxy, main.js initNavToggle).

My Order keeps the header's account rules: a signed-in partner -> Partner
Portal orders; a partner site (customers) -> /account/orders (its own
middleware sends a guest to login and back); recruitmentcv.com guest -> the
partner login, then Partner Portal orders. --}}
@php
    $bnIsPartnerSite = \App\Support\CustomerSite::isPartnerSite();
    $bnOnResumes = request()->routeIs('worker.resumes');
    $bnIcon = fn (string $paths) => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths . '</svg>';
    $bnCart = $bnIcon('<circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/><path d="M2.5 3h2.6l2.4 11.3a1.5 1.5 0 0 0 1.5 1.2h8.5a1.5 1.5 0 0 0 1.5-1.1L21 7H6"/>');
    $bnOrdersActive = request()->routeIs('worker.account.orders*');
@endphp
<nav class="w-bottom-nav" data-bottom-nav aria-label="{{ __('locale.Quick navigation') }}">
    <a href="{{ route('worker.home') }}" class="w-bottom-nav-item{{ request()->routeIs('worker.home') ? ' is-active' : '' }}" @if (request()->routeIs('worker.home')) aria-current="page" @endif>
        {!! $bnIcon('<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9v11a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9"/>') !!}
        <span class="w-bottom-nav-label">{{ __('locale.Home') }}</span>
    </a>

    @if (Auth::guard('partner')->check())
        <a href="{{ route('worker.partner.orders') }}" class="w-bottom-nav-item">
            {!! $bnCart !!}
            <span class="w-bottom-nav-label">{{ __('locale.My Order') }}</span>
        </a>
    @elseif ($bnIsPartnerSite)
        <a href="{{ route('worker.account.orders') }}" class="w-bottom-nav-item{{ $bnOrdersActive ? ' is-active' : '' }}" @if ($bnOrdersActive) aria-current="page" @endif>
            {!! $bnCart !!}
            <span class="w-bottom-nav-label">{{ __('locale.My Order') }}</span>
        </a>
    @else
        <button type="button" class="w-bottom-nav-item" data-partner-auth-trigger data-redirect="{{ route('worker.partner.orders') }}">
            {!! $bnCart !!}
            <span class="w-bottom-nav-label">{{ __('locale.My Order') }}</span>
        </button>
    @endif

    @if ($bnOnResumes)
        <button type="button" class="w-bottom-nav-item" data-filter-open aria-controls="worker-filter-panel" aria-expanded="false" aria-haspopup="dialog">
            <span class="w-bottom-nav-icon">
                {!! $bnIcon('<path d="M22 3H2l8 9.5V19l4 2v-8.5L22 3z"/>') !!}
                <span class="w-bottom-nav-badge" data-filter-count hidden></span>
            </span>
            <span class="w-bottom-nav-label">{{ __('locale.Filter') }}</span>
        </button>
    @else
        <a href="{{ route('worker.resumes') }}" class="w-bottom-nav-item{{ request()->routeIs('worker.resume.details') ? ' is-active' : '' }}">
            {!! $bnIcon('<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/>') !!}
            <span class="w-bottom-nav-label">{{ __('locale.All Workers CV') }}</span>
        </a>
    @endif

    <button type="button" class="w-bottom-nav-item" data-nav-proxy aria-expanded="false">
        {!! $bnIcon('<path d="M4 6h16M4 12h16M4 18h16"/>') !!}
        <span class="w-bottom-nav-label">{{ __('locale.Menu') }}</span>
    </button>
</nav>
