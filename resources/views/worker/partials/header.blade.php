@php
    $waNumber = isset($webconfig) && !empty($webconfig->whatsapp_number)
        ? preg_replace('/[^0-9]/', '', $webconfig->whatsapp_number)
        : null;
@endphp
<header class="w-header">
    <div class="w-nav-backdrop" data-nav-backdrop></div>
    <div class="w-container w-header-inner">
        <a href="{{ route('worker.home') }}" class="w-logo">
            <x-brand-logo mode="light" alt="Qamr International" />
        </a>

        <nav class="w-nav" data-nav>
            <button type="button" class="w-nav-close" data-nav-close aria-label="{{ __('locale.Close') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
            <a href="{{ route('worker.home') }}" class="{{ request()->routeIs('worker.home') ? 'is-active' : '' }}">{{ __('locale.Home') }}</a>
            <a href="{{ route('worker.resumes') }}" class="{{ request()->routeIs('worker.resumes*') ? 'is-active' : '' }}">{{ __('locale.Browse Resumes') }}</a>
            <a href="{{ route('worker.about') }}" class="{{ request()->routeIs('worker.about') ? 'is-active' : '' }}">{{ __('locale.About Us') }}</a>
            <a href="{{ route('worker.contact') }}" class="{{ request()->routeIs('worker.contact') ? 'is-active' : '' }}">{{ __('locale.Contact Us') }}</a>
            @if ($waNumber)
                <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener">{{ __('locale.WhatsApp Us') }}</a>
            @endif
        </nav>

        <div class="w-header-actions">
            @include('worker.partials.language-switcher', ['switchRoute' => 'worker.lang.switch'])
            @guest('partner')
                <button type="button" class="w-btn w-btn-primary w-btn-sm w-btn-primary-desktop" data-partner-auth-trigger data-redirect="{{ route('worker.partner.candidates') }}">
                    {{ __('locale.Login as Partner') }}
                </button>
            @else
                <a href="{{ route('worker.partner.candidates') }}" class="w-btn w-btn-primary w-btn-sm w-btn-primary-desktop">
                    {{ __('locale.Partner Dashboard') }}
                </a>
            @endguest
            <button type="button" class="w-nav-toggle" data-nav-toggle aria-label="Toggle navigation" aria-expanded="false">
                <span></span>
            </button>
        </div>
    </div>
</header>
