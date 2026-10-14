{{-- Closing call-to-action box of the info pages (About Us / How It Works /
Pricing): $page texts cta_heading / cta_text / cta_button_text (+ cta_url:
http(s)://... or /path; blank = $ctaFallback), and this website's own
contact list ($contact: phone / email / web). Nothing to say = not shown. --}}
@php
    $ctaHeading = trim((string) ($page['cta_heading'] ?? ''));
    $ctaText = trim((string) ($page['cta_text'] ?? ''));
    $ctaButton = trim((string) ($page['cta_button_text'] ?? ''));
    $ctaUrl = trim((string) ($page['cta_url'] ?? ''));
    $ctaHref = $ctaUrl !== '' ? $ctaUrl : $ctaFallback;
    $ctaExternal = (bool) preg_match('#^https?://#i', $ctaUrl);
@endphp
@if ($ctaHeading !== '' || $ctaButton !== '')
    <section class="w-cta-sec" data-info-cta>
        <div class="w-container">
            <div class="w-cta-box">
                <div>
                    @if ($ctaHeading !== '')<h2>{{ $ctaHeading }}</h2>@endif
                    @if ($ctaText !== '')<p>{{ $ctaText }}</p>@endif
                    @if ($ctaButton !== '')
                        <a href="{{ $ctaHref }}" class="w-btn w-btn-gold" @if ($ctaExternal) target="_blank" rel="noopener" @endif>{{ $ctaButton }}</a>
                    @endif
                </div>
                @if (count($contact ?? []))
                    <ul class="w-contact-list">
                        @if (!empty($contact['phone']))
                            <li>@include('worker.partials.ref-icon', ['name' => 'phone'])<a href="tel:{{ preg_replace('/[^\d+]/', '', $contact['phone']) }}" dir="ltr">{{ $contact['phone'] }}</a></li>
                        @endif
                        @if (!empty($contact['email']))
                            <li>@include('worker.partials.ref-icon', ['name' => 'mail'])<a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a></li>
                        @endif
                        @if (!empty($contact['web']))
                            <li>@include('worker.partials.ref-icon', ['name' => 'globe'])<span dir="ltr">{{ $contact['web'] }}</span></li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
    </section>
@endif
