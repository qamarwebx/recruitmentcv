@extends('worker.layouts.app')

@section('title', __('locale.Partner Login') . ' — ' . __('locale.Partner Portal'))
@section('meta_description', __('locale.Sign in or register your recruitment office to manage candidates and orders.'))
{{-- Standalone page: no public website header/footer. --}}
@section('hide-site-chrome', '1')

@section('content')
    <section class="w-partner-auth-page">
        <div class="w-partner-auth-page-inner">
            <div class="w-partner-auth-page-head">
                {{-- Partner logo (subdomain branding, else the default logo -
                same x-brand-logo component the public header uses). --}}
                <div class="w-partner-auth-page-brand">
                    <x-brand-logo mode="light" class="w-partner-auth-page-logo" alt="{{ __('locale.Partner Portal') }}" />
                    <span class="w-eyebrow">{{ __('locale.Partner Portal') }}</span>
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
