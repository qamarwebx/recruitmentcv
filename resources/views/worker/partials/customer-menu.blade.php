{{-- Logged-in CUSTOMER (web guard) account dropdown - partner subdomains
only (header.blade.php). Same structure as the qamarhire.com customer menu
(avatar, name, mobile, email, My Order / Wishlist / Personal Info /
Password & Security / Notifications, divider, Sign Out). Behavior: main.js
initUserMenu(). --}}
@php
    $__cu = Auth::guard('web')->user();
    $__cuPhoto = $__cu->photo ? asset('user/img/avatars/' . $__cu->photo) : ($__cu->avatar_url ?: null);
    $__cuInitial = mb_strtoupper(mb_substr($__cu->name ?: '?', 0, 1));
    $__cuMobile = $__cu->mobile_no ? '+' . ltrim((string) $__cu->country_code, '+') . ' ' . $__cu->mobile_no : null;
    // Signing out on an account page returns to the homepage, otherwise stay.
    $__cuReturn = request()->routeIs('worker.account.*') ? '/' : request()->getRequestUri();
    $__cuItems = [
        ['route' => 'worker.account.orders', 'label' => __('locale.My Order'), 'icon' => '<path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/>'],
        ['route' => 'worker.account.wishlist', 'label' => __('locale.Wishlist'), 'icon' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8Z"/>'],
        ['route' => 'worker.account.profile', 'label' => __('locale.Personal Info'), 'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
        ['route' => 'worker.account.security', 'label' => __('locale.Password & Security'), 'icon' => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>'],
        ['route' => 'worker.account.notifications', 'label' => __('locale.Notifications'), 'icon' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>'],
    ];
@endphp
<div class="w-user-menu">
    <button type="button" class="w-user-menu-trigger" data-user-menu-trigger aria-haspopup="true" aria-expanded="false" aria-label="{{ __('locale.My Account') }}">
        @if ($__cuPhoto)
            <img src="{{ $__cuPhoto }}" alt="{{ $__cu->name }}" class="w-user-avatar" onerror="this.onerror=null;this.src='{{ asset('admin/assets/img/avatars/avatar.jpg') }}';">
        @else
            <span class="w-user-avatar is-initial">{{ $__cuInitial }}</span>
        @endif
        <svg class="w-user-menu-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6"/></svg>
    </button>

    <div class="w-user-menu-panel" data-user-menu role="menu">
        <div class="w-user-menu-head">
            @if ($__cuPhoto)
                <img src="{{ $__cuPhoto }}" alt="{{ $__cu->name }}" class="w-user-avatar is-lg" onerror="this.onerror=null;this.src='{{ asset('admin/assets/img/avatars/avatar.jpg') }}';">
            @else
                <span class="w-user-avatar is-lg is-initial">{{ $__cuInitial }}</span>
            @endif
            <div class="w-user-menu-id">
                <strong>{{ $__cu->name }}</strong>
                @if ($__cuMobile)
                    <span dir="ltr">{{ $__cuMobile }}</span>
                @endif
                @if ($__cu->email)
                    <span>{{ $__cu->email }}</span>
                @endif
            </div>
        </div>

        @foreach ($__cuItems as $item)
            <a href="{{ route($item['route']) }}" class="w-user-menu-item {{ request()->routeIs($item['route']) ? 'is-active' : '' }}" role="menuitem">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $item['icon'] !!}</svg>
                {{ $item['label'] }}
            </a>
        @endforeach

        <div class="w-user-menu-divider"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <input type="hidden" name="current_page" value="{{ $__cuReturn }}">
            <button type="submit" class="w-user-menu-item is-danger" role="menuitem">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>
                {{ __('locale.Sign Out') }}
            </button>
        </form>
    </div>
</div>
