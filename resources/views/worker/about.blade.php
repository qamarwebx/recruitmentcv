@extends('worker.layouts.app')

@section('title', __('locale.About Us') . ' — Qamr Worker Portal')
@section('meta_description', 'Learn about Qamr International\'s mission, vision and values behind the Worker Portal at recruitmentcv.com.')

@section('content')

    @php
        $aboutText = (app()->getLocale() === 'ar' && !empty($frontwebsite->about_us_ar ?? null))
            ? $frontwebsite->about_us_ar
            : ($frontwebsite->about_us_eng ?? __('locale.Connecting verified, work-ready candidates with employers who need reliable talent, fast.'));

        // Same photos as qamarhire.com's own About Us gallery
        // (resources/views/about.blade.php's #gallery section) - both sites
        // share the same public/ asset tree, so these are referenced
        // directly rather than duplicated into a Worker-specific folder.
        $galleryImages = [
            'user/img/gallery/team.jpg',
            'user/img/gallery/Recp.jpg',
            'user/img/gallery/team3.JPG',
            'user/img/gallery/team4.JPG',
            'user/img/gallery/team5.JPG',
            'user/img/gallery/team8.JPG',
            'user/img/gallery/team6.JPG',
            'user/img/gallery/team7.JPG',
        ];
        $gallerySlides = collect($galleryImages)->map(function ($path, $i) {
            $url = asset($path);
            $html = '<img src="' . $url . '" alt="' . __('locale.Gallery') . ' ' . ($i + 1) . '">';
            return ['thumb' => $url, 'html' => $html];
        })->values();
    @endphp

    <section class="w-page-header">
        <div class="w-container">
            <div class="w-breadcrumb">
                <a href="{{ route('worker.home') }}">{{ __('locale.Home') }}</a>
                <span>/</span>
                <span>{{ __('locale.About Us') }}</span>
            </div>
            <h1>{{ __('locale.About Us') }}</h1>
            <p>{{ __('locale.Learn about our mission, vision and the team behind Qamr International.') }}</p>
        </div>
    </section>

    <section class="w-section">
        <div class="w-container">
            <div class="w-info-card w-fade" style="max-width:820px;margin-inline:auto;text-align:center;">
                <p style="font-size:1.02rem;color:var(--w-ink-700);">{{ $aboutText }}</p>
                <a href="{{ route('worker.contact') }}" class="w-btn w-btn-primary" style="margin-top:8px;">{{ __('locale.Contact Us') }}</a>
            </div>
        </div>
    </section>

    <section class="w-section" style="padding-top:0;">
        <div class="w-container">
            <div class="w-section-head w-center w-fade">
                <span class="w-eyebrow" style="margin-inline:auto;">{{ __('locale.About Us') }}</span>
                <h2>{{ __('locale.Why Choose Us') }}</h2>
            </div>

            <div class="w-steps w-fade">
                <div class="w-step-card">
                    <span class="w-step-num">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                    </span>
                    <h3>{{ __('locale.Mission') }}</h3>
                    <p>{{ __('locale.To provide unmatched recruitment solutions that help our clients become more productive and profitable.') }}</p>
                </div>
                <div class="w-step-card">
                    <span class="w-step-num">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </span>
                    <h3>{{ __('locale.Vision') }}</h3>
                    <p>{{ __('locale.To be globally known for an impactful, efficient and innovative Human Resources Consulting Partner.') }}</p>
                </div>
                <div class="w-step-card">
                    <span class="w-step-num">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
                    </span>
                    <h3>{{ __('locale.Values') }}</h3>
                    <p>{{ __('locale.Creating a self-sustaining, productive environment that gives the best experiences and opportunities of growth to our customers and employees alike.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="w-section" style="padding-top:0;">
        <div class="w-container">
            <div class="w-section-head w-center w-fade">
                <span class="w-eyebrow" style="margin-inline:auto;">{{ __('locale.Why Choose Us') }}</span>
                <h2>{{ __('locale.Value Added Services for World-Class Customer Experience') }}</h2>
            </div>

            <div class="w-steps w-fade">
                <div class="w-step-card">
                    <span class="w-step-num">1</span>
                    <h3>{{ __('locale.Providing Service Before Self-Interest') }}</h3>
                    <p>{{ __('locale.We take care of everything before your visit so that you can focus on your business and growth by saving your valuable time.') }}</p>
                </div>
                <div class="w-step-card">
                    <span class="w-step-num">2</span>
                    <h3>{{ __('locale.We have always been at the forefront of providing value-added services') }}</h3>
                    <p>{{ __('locale.Catering our clients with all the benefits and convenience.') }}</p>
                </div>
                <div class="w-step-card">
                    <span class="w-step-num">3</span>
                    <h3>{{ __('locale.Delightful Experience') }}</h3>
                    <p>{{ __('locale.Taking into account the overall journey by building long term relationship with our clients.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="w-section" style="padding-top:0;">
        <div class="w-container">
            <div class="w-section-head w-center w-fade">
                <span class="w-eyebrow" style="margin-inline:auto;">{{ __('locale.About Us') }}</span>
                <h2>{{ __('locale.Gallery') }}</h2>
            </div>

            {{-- Same reusable slider already used on /resumes/details/{id}
            (initGallery() in main.js runs on every page, no extra JS/CSS
            needed here) - prev/next arrows + thumbnail indicators, not a
            static image grid. position:static overrides that component's
            own position:sticky (meant for its 2-column candidate-details
            layout; this page has nothing beside it to stick against). --}}
            <div class="w-gallery w-fade" style="position:static;max-width:640px;margin-inline:auto;">
                <div data-gallery>
                    <div class="w-gallery-main-wrap">
                        <button type="button" class="w-gallery-nav w-gallery-nav-prev" data-gallery-prev aria-label="{{ __('locale.Previous') }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M15 18l-6-6 6-6"/></svg>
                        </button>
                        <div class="w-gallery-main" data-gallery-main>
                            {!! $gallerySlides[0]['html'] !!}
                        </div>
                        <button type="button" class="w-gallery-nav w-gallery-nav-next" data-gallery-next aria-label="{{ __('locale.Next') }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M9 6l6 6-6 6"/></svg>
                        </button>
                    </div>

                    <div class="w-gallery-thumbs">
                        @foreach ($gallerySlides as $i => $slide)
                            <button type="button" data-gallery-thumb data-slide-html="{{ $slide['html'] }}" class="{{ $i === 0 ? 'is-active' : '' }}">
                                <img src="{{ $slide['thumb'] }}" alt="{{ __('locale.Gallery') }} {{ $i + 1 }}">
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="w-container" style="padding-bottom:80px;">
        <div class="w-cta-banner w-fade">
            <div>
                <h2>{{ __('locale.Have a question for our team?') }}</h2>
                <p>{{ __("locale.We'd love to hear from you - get in touch and we'll respond as soon as we can.") }}</p>
            </div>
            <a href="{{ route('worker.contact') }}" class="w-btn w-btn-accent">{{ __('locale.Contact Us') }}</a>
        </div>
    </section>

@endsection
