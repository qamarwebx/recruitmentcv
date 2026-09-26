@extends('worker.layouts.app')

@section('title', __('locale.Contact Us') . ' — Qamr Worker Portal')
@section('meta_description', 'Get in touch with Qamr International - Worker Portal support, hiring enquiries and office details.')

@section('content')

    @php
        // Partner Website Config override, then the SAME site-wide
        // frontendwebsiteconfigs fields the CRM's own Contact Us config
        // already drives, then the hardcoded default.
        $contactIntro = $contactContent['intro_text'] ?? ((app()->getLocale() === 'ar' && !empty($frontwebsite->contact_us_ar ?? null))
            ? $frontwebsite->contact_us_ar
            : ($frontwebsite->contact_us_eng ?? __('locale.Fill out the form and our team will get back to you within 24 hours.')));

        // Same three fields (and the same fallback values) as qamarhire.com's
        // own Contact Us page (resources/views/contact.blade.php's "Contact
        // cards" section) - not bottom_contact_us_* (that's the site-wide
        // footer/legal contact block used elsewhere, a different field set
        // for a different purpose).
        $contactAddr = $contactContent['address'] ?? ((app()->getLocale() === 'ar' && !empty($frontwebsite->contact_us_location_ar ?? null))
            ? $frontwebsite->contact_us_location_ar
            : ($frontwebsite->contact_us_location ?: __('locale.Mumbai, India')));
        $contactPhone = $contactContent['phone'] ?? ($frontwebsite->contact_us_phone ?: '+919969566388');
        $contactEmail = $contactContent['email'] ?? ($frontwebsite->contact_us_email ?: 'info@qamrintl.com');
    @endphp

    <section class="w-page-header">
        <div class="w-container">
            <div class="w-breadcrumb">
                <a href="{{ route('worker.home') }}">{{ __('locale.Home') }}</a>
                <span>/</span>
                <span>{{ __('locale.Contact Us') }}</span>
            </div>
            <h1>{{ $contactContent['header_title'] ?? __('locale.Get in touch!') }}</h1>
            <p>{{ $contactContent['header_subtitle'] ?? __('locale.Have questions about hiring or working with Qamr International? Reach us directly using the details below.') }}</p>
        </div>
    </section>

    <section class="w-section">
        <div class="w-container">
            <div class="w-info-card w-fade" style="max-width:640px;margin-inline:auto;">
                <img src="{{ asset('user/img/real-estate/illustrations/contact.svg') }}" alt="{{ __('locale.Get in touch!') }}" style="max-width:220px;margin:0 auto 20px;">
                <p style="color:var(--w-ink-600);margin-bottom:20px;">{{ $contactIntro }}</p>

                <div class="w-form-alert" data-contact-form-alert hidden></div>

                <form data-contact-form action="{{ route('worker.contact.store') }}" method="POST">
                    <div class="w-form-row">
                        <label class="w-form-label" for="contactName">{{ __('locale.Full Name') }}</label>
                        <input type="text" class="w-input" id="contactName" name="name" placeholder="{{ __('locale.Full Name') }}" required maxlength="150">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label" for="contactEmail">{{ __('locale.Email') }}</label>
                        <input type="email" class="w-input" id="contactEmail" name="email" placeholder="{{ __('locale.Email') }}" required maxlength="150">
                    </div>
                    <div class="w-form-row">
                        <label class="w-form-label" for="contactMessage">{{ __('locale.Message') }}</label>
                        <textarea class="w-input" id="contactMessage" name="message" rows="4" placeholder="{{ __('locale.Leave your message') }}" required maxlength="2000"></textarea>
                    </div>
                    <button type="submit" class="w-btn w-btn-primary w-btn-block" data-contact-submit
                        data-default-text="{{ __('locale.Send Message') }}"
                        data-loading-text="{{ __('locale.Submitting…') }}">
                        {{ __('locale.Send Message') }}
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="w-container" style="padding-bottom:80px;">
        <div class="w-contact-grid w-fade">
            @if ($contactEmail)
                <a href="mailto:{{ $contactEmail }}" class="w-stat-card">
                    <span class="w-stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/></svg>
                    </span>
                    <span>
                        <strong style="font-size:1rem;">{{ $contactEmail }}</strong>
                        <span>{{ __('locale.Drop us a line') }}</span>
                    </span>
                </a>
            @endif
            @if ($contactPhone)
                <a href="tel:{{ $contactPhone }}" class="w-stat-card">
                    <span class="w-stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .3 2 .7 3a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c1 .4 2 .6 3 .7a2 2 0 0 1 1.7 2z"/></svg>
                    </span>
                    <span>
                        <strong style="font-size:1rem;">{{ $contactPhone }}</strong>
                        <span>{{ __('locale.Call us any time') }}</span>
                    </span>
                </a>
            @endif
            @if ($contactAddr)
                <div class="w-stat-card">
                    <span class="w-stat-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s7-7.5 7-12a7 7 0 1 0-14 0c0 4.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    </span>
                    <span>
                        <strong style="font-size:1rem;">{{ $contactAddr }}</strong>
                        <span>{{ __('locale.Our Office') }}</span>
                    </span>
                </div>
            @endif
        </div>
    </section>

    <section class="w-section" style="padding-top:0;">
        <div class="w-container">
            <div class="w-section-head w-center w-fade">
                <span class="w-eyebrow" style="margin-inline:auto;">{{ $contactContent['branches_eyebrow'] ?? __('locale.Our Office') }}</span>
                <h2>{{ $contactContent['branches_heading'] ?? __('locale.Location') }}</h2>
                <p>{{ $contactContent['branches_subheading'] ?? __('locale.Our branches across the region.') }}</p>
            </div>

            <div class="w-branch-grid w-fade">
                @foreach ($branches as $branch)
                    @php
                        $branchLabel = (app()->getLocale() === 'ar' && !empty($branch['name_ar']))
                            ? $branch['name_ar']
                            : ($branch['name_en'] ?? $branch['name_ar'] ?? '');
                    @endphp
                    <div class="w-branch-card">
                        <img src="{{ $branch['image'] }}" alt="{{ $branchLabel }}">
                        <h3>{{ $branchLabel }}</h3>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection

@section('page-script')
    <script src="{{ asset('worker/js/contact-form.js') }}?v={{ @filemtime(public_path('worker/js/contact-form.js')) ?: time() }}"></script>
@endsection
