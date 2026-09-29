@extends('worker.layouts.app')

@section('content')
    @php
        $accountCustomer = Auth::guard('web')->user();
        $accountTabs = [
            ['route' => 'worker.account.orders', 'label' => __('locale.My Orders'), 'icon' => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/>'],
            ['route' => 'worker.account.wishlist', 'label' => __('locale.Wishlist'), 'icon' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8Z"/>'],
            ['route' => 'worker.account.profile', 'label' => __('locale.Personal Information'), 'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
            ['route' => 'worker.account.security', 'label' => __('locale.Password & Security'), 'icon' => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>'],
            ['route' => 'worker.account.notifications', 'label' => __('locale.Notifications'), 'icon' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>'],
        ];
    @endphp

    <section class="w-section w-account-section">
        <div class="w-container">
            <div class="w-account-head">
                <h1>{{ __('locale.My Account') }}</h1>
                <p>{{ $accountCustomer->name }}</p>
            </div>

            <div class="w-account-grid">
                <nav class="w-account-nav" aria-label="{{ __('locale.My Account') }}">
                    @foreach ($accountTabs as $tab)
                        <a href="{{ route($tab['route']) }}" class="{{ request()->routeIs($tab['route']) ? 'is-active' : '' }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $tab['icon'] !!}</svg>
                            <span>{{ $tab['label'] }}</span>
                        </a>
                    @endforeach
                </nav>

                <div class="w-account-content">
                    @yield('account-content')
                </div>
            </div>
        </div>
    </section>
@endsection
