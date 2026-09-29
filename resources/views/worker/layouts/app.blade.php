<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Qamr Worker Portal — Hire Verified, Work-Ready Candidates')</title>
    <meta name="description" content="@yield('meta_description', 'Browse verified, work-ready candidate resumes and hire dependable talent through Qamr International\'s Worker Portal.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Partner favicon on a partner subdomain (ResolvePartnerWebsiteDomain),
    otherwise the site default favicon. --}}
    <link rel="icon" href="{{ !empty($partnerBrand['favicon']) ? $partnerBrand['favicon'] : \App\Models\Domain::defaultFaviconUrl() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('worker/css/style.css') }}?v={{ @filemtime(public_path('worker/css/style.css')) ?: time() }}">
    @yield('page-style')
</head>
<body class="worker-scope">

    {{-- Pages that set @section('hide-site-chrome') (the standalone
    /partner/login page) render without the public header/footer. --}}
    @sectionMissing('hide-site-chrome')
        @include('worker.partials.header')
    @endif

    <main>
        @yield('content')
    </main>

    @sectionMissing('hide-site-chrome')
        @include('worker.partials.footer')
    @endif

    {{-- The /partner/login page renders this same form inline in its own
    content instead (see PartnerAuthController::loginPage) - skipped here so
    the page never has two copies of it. --}}
    @unless (!empty($partnerAuthInline))
        @include('worker.partials.partner-auth-modal')
    @endunless
    {{-- Customer (web guard) login/register - partner subdomains only. The
    main recruitmentcv.com site is the partner entry point. --}}
    @if (\App\Support\CustomerSite::isPartnerSite())
        @guest('web')
            @include('worker.partials.customer-auth-modal')
        @endguest
    @endif
    @include('worker.partials.whatsapp-float', ['waPartnerId' => app()->bound('currentPartner') ? optional(app('currentPartner'))->id : null])

    <script src="{{ asset('worker/js/main.js') }}?v={{ @filemtime(public_path('worker/js/main.js')) ?: time() }}"></script>
    @auth('web')
        <script src="{{ asset('worker/js/customer-wishlist.js') }}?v={{ @filemtime(public_path('worker/js/customer-wishlist.js')) ?: time() }}"></script>
    @endauth
    @include('worker.partials.partner-auth-script')
    @yield('page-script')
</body>
</html>
