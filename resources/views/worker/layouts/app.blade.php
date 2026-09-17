<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Qamr Worker Portal — Hire Verified, Work-Ready Candidates')</title>
    <meta name="description" content="@yield('meta_description', 'Browse verified, work-ready candidate resumes and hire dependable talent through Qamr International\'s Worker Portal.')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('worker/css/style.css') }}?v={{ @filemtime(public_path('worker/css/style.css')) ?: time() }}">
    @yield('page-style')
</head>
<body class="worker-scope">

    @include('worker.partials.header')

    <main>
        @yield('content')
    </main>

    @include('worker.partials.footer')

    @include('worker.partials.partner-auth-modal')
    @include('worker.partials.whatsapp-float')

    <script src="{{ asset('worker/js/main.js') }}?v={{ @filemtime(public_path('worker/js/main.js')) ?: time() }}"></script>
    @include('worker.partials.partner-auth-script')
    @yield('page-script')
</body>
</html>
