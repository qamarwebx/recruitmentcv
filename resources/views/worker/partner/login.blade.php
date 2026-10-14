@extends('worker.layouts.app')

@section('title', __('locale.Partner Login') . ' — ' . __('locale.Partner Portal'))
@section('meta_description', __('locale.Sign in or register your recruitment office to manage candidates and orders.'))
{{-- Standalone page: no public website header/footer. --}}
@section('hide-site-chrome', '1')

@section('page-style')
    {{-- Admin design system v1 (same tokens + components as the portal), scoped to .w-partner-auth-page. --}}
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('worker/css/tokens.css') }}?v={{ @filemtime(public_path('worker/css/tokens.css')) ?: time() }}">
    <link rel="stylesheet" href="{{ asset('worker/css/components.css') }}?v={{ @filemtime(public_path('worker/css/components.css')) ?: time() }}">
@endsection

@section('content')
    <section class="w-partner-auth-page">
        <div class="w-partner-auth-page-inner">
            <div class="w-partner-auth-page-head">
                {{-- Partner logo (subdomain branding, else the default logo -
                same x-brand-logo component the public header uses). --}}
                <div class="w-partner-auth-page-brand">
                    <x-brand-logo mode="light" class="w-partner-auth-page-logo" alt="{{ __('locale.Partner Portal') }}" data-brand-logo />
                    @include('worker.partials.brand-company-name')
                </div>
                <p>{{ __('locale.Sign in or register your recruitment office to manage candidates and orders.') }}</p>
            </div>

            {{-- Same partial/flow as the site-wide overlay (partner-auth.js),
            rendered inline - the layout skips its own copy via
            $partnerAuthInline. --}}
            @include('worker.partials.partner-auth-modal', [
                'inline' => true,
                'inlineRedirect' => $redirect,
                'inlineMode' => $mode,
            ])
        </div>
    </section>
@endsection

@section('page-script')
    {{-- English <-> Arabic on this page without a reload (keeps the form state). --}}
    <script src="{{ asset('worker/js/partner-login-language.js') }}?v={{ @filemtime(public_path('worker/js/partner-login-language.js')) ?: time() }}"></script>
@endsection
