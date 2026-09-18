@php
    $footerAddr = $frontwebsite->bottom_contact_us_addr ?? null;
    $footerPhone = $frontwebsite->bottom_contact_us_phone ?? ($frontwebsite->contact_us_phone ?? null);
    $footerEmail = $frontwebsite->bottom_contact_us_email ?? null;
    $socials = [
        'Facebook' => $frontwebsite->bottom_contact_us_fb_link ?? null,
        'Twitter' => $frontwebsite->bottom_contact_us_twitter_link ?? null,
        'Instagram' => $frontwebsite->bottom_contact_us_instagram_link ?? null,
        'LinkedIn' => $frontwebsite->bottom_contact_us_linkedin_link ?? null,
    ];
@endphp
<footer class="w-footer">
    <div class="w-container">
        <div class="w-footer-grid">
            <div class="w-footer-brand">
                <a href="{{ route('worker.home') }}" class="w-logo">
                    <x-brand-logo mode="dark" alt="Qamr International" />
                </a>
                <p>
                    @if (app()->getLocale() === 'ar' && !empty($frontwebsite->about_us_ar ?? null))
                        {{ $frontwebsite->about_us_ar }}
                    @else
                        {{ $frontwebsite->about_us_eng ?? __('locale.Connecting verified, work-ready candidates with employers who need reliable talent, fast.') }}
                    @endif
                </p>
                <div class="w-social-row">
                    @foreach ($socials as $label => $link)
                        @if (!empty($link))
                            <a href="{{ $link }}" target="_blank" rel="noopener" aria-label="{{ $label }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/></svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div>
                <h4>{{ __('locale.Quick Links') }}</h4>
                <ul>
                    <li><a href="{{ route('worker.home') }}">{{ __('locale.Home') }}</a></li>
                    <li><a href="{{ route('worker.resumes') }}">{{ __('locale.Browse Resumes') }}</a></li>
                    <li><a href="{{ route('worker.about') }}">{{ __('locale.About Us') }}</a></li>
                    <li><a href="{{ route('worker.contact') }}">{{ __('locale.Contact Us') }}</a></li>
                    <li><a href="{{ route('worker.privacy') }}">{{ __('locale.Privacy Policy') }}</a></li>
                    <li><a href="{{ route('worker.terms') }}">{{ __('locale.Terms of Service') }}</a></li>
                </ul>
            </div>

            <div>
                <h4>{{ __('locale.For Employers') }}</h4>
                <ul>
                    <li><a href="{{ route('worker.resumes') }}">{{ __('locale.Search by profession') }}</a></li>
                    <li><a href="{{ route('worker.resumes') }}">{{ __('locale.Search by work location') }}</a></li>
                    <li><a href="{{ route('worker.resumes') }}">{{ __('locale.Search by experience level') }}</a></li>
                </ul>
            </div>

            <div>
                <h4>{{ __('locale.Get In Touch') }}</h4>
                <ul class="w-footer-contact">
                    @if ($footerAddr)
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s7-7.5 7-12a7 7 0 1 0-14 0c0 4.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <span>{{ $footerAddr }}</span>
                        </li>
                    @endif
                    @if ($footerPhone)
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .3 2 .7 3a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c1 .4 2 .6 3 .7a2 2 0 0 1 1.7 2z"/></svg>
                            <a href="tel:{{ $footerPhone }}">{{ $footerPhone }}</a>
                        </li>
                    @endif
                    @if ($footerEmail)
                        <li>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/></svg>
                            <a href="mailto:{{ $footerEmail }}">{{ $footerEmail }}</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="w-footer-bottom">
            <span>&copy; {{ date('Y') }} Qamr International. {{ __('locale.All rights reserved.') }}</span>
            <span>{{ __('locale.Worker Portal') }} &middot; recruitmentcv.com</span>
        </div>
    </div>
</footer>

<button type="button" class="w-back-top" data-back-top aria-label="Back to top">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</button>
