@php
    $waNumber = isset($webconfig) && !empty($webconfig->whatsapp_number)
        ? preg_replace('/[^0-9]/', '', $webconfig->whatsapp_number)
        : null;
    // Main recruitmentcv.com = PARTNER entry point (existing partner auth);
    // a partner subdomain = that partner's CUSTOMER login.
    $isPartnerSite = \App\Support\CustomerSite::isPartnerSite();
@endphp
<header class="w-header">
    <div class="w-nav-backdrop" data-nav-backdrop></div>
    <div class="w-container w-header-inner">
        <a href="{{ route('worker.home') }}" class="w-logo">
            <x-brand-logo mode="light" alt="{{ \App\Support\SiteBrand::name() }}" />
            @include('worker.partials.brand-company-name')
        </a>

        <nav class="w-nav" data-nav>
            <a href="{{ route('worker.home') }}" class="{{ request()->routeIs('worker.home') ? 'is-active' : '' }}">{{ __('locale.Home') }}</a>
            <a href="{{ route('worker.resumes') }}" class="{{ request()->routeIs('worker.resumes*') ? 'is-active' : '' }}">{{ __('locale.Browse Resumes') }}</a>
            <a href="{{ route('worker.about') }}" class="{{ request()->routeIs('worker.about') ? 'is-active' : '' }}">{{ __('locale.About Us') }}</a>
            <a href="{{ route('worker.contact') }}" class="{{ request()->routeIs('worker.contact') ? 'is-active' : '' }}">{{ __('locale.Contact Us') }}</a>
            @if ($waNumber)
                <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener">{{ __('locale.WhatsApp Us') }}</a>
            @endif
            {{-- Mobile-only copy of the header button below (the header
            button is hidden at the mobile breakpoint). --}}
            @if ($isPartnerSite)
                {{-- A logged-in customer uses the account dropdown in the
                header (shown on mobile too), so only guests need this. --}}
                @auth('partner')
                    <a href="{{ route('worker.partner.candidates') }}" class="w-nav-account">{{ __('locale.Dashboard') }}</a>
                @else
                    @guest('web')
                        <button type="button" class="w-nav-auth" data-customer-auth-trigger>{{ __('locale.Login') }}</button>
                    @endguest
                @endauth
            @else
                @guest('partner')
                    <button type="button" class="w-nav-auth" data-partner-auth-trigger data-redirect="{{ route('worker.partner.candidates') }}">{{ __('locale.Login as Partner') }}</button>
                @else
                    <a href="{{ route('worker.partner.candidates') }}" class="w-nav-account">{{ __('locale.Partner Dashboard') }}</a>
                @endguest
            @endif
        </nav>

        <div class="w-header-actions">
            @include('worker.partials.language-switcher', ['switchRoute' => 'worker.lang.switch'])
            @if ($isPartnerSite)
                {{-- Partner subdomain: CUSTOMER auth (users / `web` guard).
                Partner auth here is only at /partner/login; a logged-in
                partner keeps the existing Dashboard link and never gets the
                customer menu. --}}
                @auth('partner')
                    <a href="{{ route('worker.partner.candidates') }}" class="w-btn w-btn-primary w-btn-sm w-btn-primary-desktop">
                        {{ __('locale.Dashboard') }}
                    </a>
                @else
                    @auth('web')
                        @include('worker.partials.customer-menu')
                    @else
                        <button type="button" class="w-btn w-btn-primary w-btn-sm w-btn-primary-desktop" data-customer-auth-trigger>
                            {{ __('locale.Login') }}
                        </button>
                    @endauth
                @endauth
            @else
                {{-- Main recruitmentcv.com: the existing PARTNER login/register
                (same partner-auth modal + partner-auth.js + PartnerAuthController
                as /partner/login). --}}
                @guest('partner')
                    <button type="button" class="w-btn w-btn-primary w-btn-sm w-btn-primary-desktop"
                        data-partner-auth-trigger
                        data-redirect="{{ route('worker.partner.candidates') }}">
                        {{ __('locale.Login as Partner') }}
                    </button>
                @else
                    <a href="{{ route('worker.partner.candidates') }}" class="w-btn w-btn-primary w-btn-sm w-btn-primary-desktop">
                        {{ __('locale.Partner Dashboard') }}
                    </a>
                @endguest
            @endif
            <button type="button" class="w-nav-toggle" data-nav-toggle aria-label="Toggle navigation" aria-expanded="false">
                <span></span>
            </button>
        </div>
    </div>
</header>
