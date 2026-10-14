@extends('worker.layouts.app')

{{-- Pricing, laid out like the design reference ("RecruitmentCV Pages"). Texts +
     lists (trust items, driver categories, billing checks, FAQs) from Website
     Config -> Pricing ($page / $lists: partner -> Recruitmentcv Partner (Global)
     -> recruitmentcv.com; Arabic falls back to English). Plans: this website's
     plans (App\Support\InfoPages - never invented), printed exactly as entered;
     none = the configured empty-state text. Display only: no plan sells
     anything (no checkout / payment), test plans are always labelled and only
     ever link to the Contact page. --}}
@php
    $locale = app()->getLocale();
    $txt = fn ($key) => trim((string) ($page[$key] ?? ''));
    $item = fn ($row, $field) => \App\Support\PageLists::text($row, $field, $locale);
    $paragraphs = fn ($text) => array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/', (string) $text))));
    $body = \App\Support\HtmlSanitizer::clean((string) ($page['body'] ?? ''));
    $hasBody = trim(strip_tags($body)) !== '';
    $link = fn ($url) => trim((string) $url) !== '' ? trim((string) $url) : route('worker.contact');
    $external = fn ($url) => (bool) preg_match('#^https?://#i', trim((string) $url));
    // A price in this language: the Arabic text when one was entered, else the price as typed.
    $priceText = fn ($plan, $field) => $locale === 'ar' && trim((string) ($plan[$field . '_ar'] ?? '')) !== '' ? trim($plan[$field . '_ar']) : trim((string) ($plan[$field] ?? ''));
    // Only plans switched on ("Show on website"); test plans are labelled and never sell anything.
    $plans = \App\Support\InfoPages::visiblePlans($plans);
    $hasTest = \App\Support\InfoPages::hasTestPlans($plans);
    // Feature comparison: every feature (in this language), in first-seen order - shown only
    // when at least one feature is offered by more than one plan (otherwise it compares nothing).
    $planFeatures = array_map(fn ($p) => \App\Support\InfoPages::features($p, $locale), $plans);
    $allFeatures = array_values(array_unique(array_merge([], ...array_values($planFeatures ?: [[]]))));
    $sharedFeature = count(array_filter(array_count_values(array_merge([], ...array_map('array_unique', array_values($planFeatures ?: [[]])))), fn ($n) => $n > 1)) > 0;
    $trust = array_values(array_filter($lists['trust'] ?? [], fn ($r) => $item($r, 'text') !== ''));
    $categories = array_values(array_filter($lists['categories'] ?? [], fn ($r) => $item($r, 'name') !== ''));
    $checks = array_values(array_filter($lists['checks'] ?? [], fn ($r) => $item($r, 'text') !== ''));
    $billing = $paragraphs($txt('billing_text'));
    $title = $txt('header_title') ?: __('locale.Pricing');
@endphp

@section('title', __('locale.Pricing') . ' — Qamr Worker Portal')
@section('meta_description', \Illuminate\Support\Str::limit($txt('header_subtitle') ?: trim(preg_replace('/\s+/', ' ', strip_tags($body))) ?: $title, 160, '…'))

@section('content')
    <section class="w-ihero is-stack" data-info-hero>
        <div class="w-container w-ihero-grid">
            <div>
                <nav class="w-crumbs" aria-label="{{ __('locale.Home') }}">
                    <a href="{{ route('worker.home') }}">{{ __('locale.Home') }}</a><span aria-hidden="true">/</span><span>{{ __('locale.Pricing') }}</span>
                </nav>
                <h1>{{ $title }}@if ($txt('header_title_highlight') !== '') <em>{{ $txt('header_title_highlight') }}</em>@endif</h1>
                @if ($txt('header_subtitle') !== '')
                    <p class="w-ihero-lead">{{ $txt('header_subtitle') }}</p>
                @endif
                @if ($txt('hero_primary_text') !== '' || $txt('hero_secondary_text') !== '')
                    <div class="w-actions">
                        @if ($txt('hero_primary_text') !== '')
                            <a class="w-btn w-btn-primary" href="{{ route('worker.contact') }}">{{ $txt('hero_primary_text') }}</a>
                        @endif
                        @if ($txt('hero_secondary_text') !== '')
                            <a class="w-btn w-btn-line" href="{{ route('worker.how-it-works') }}">{{ $txt('hero_secondary_text') }}</a>
                        @endif
                    </div>
                @endif
                @if (count($trust))
                    <div class="w-trust">
                        @foreach ($trust as $row)
                            <span>@include('worker.partials.ref-icon', ['name' => $row['icon'] ?? '']){{ $item($row, 'text') }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                @if (count($plans))
                    @if ($txt('plans_heading') !== '' || $txt('plans_subheading') !== '')
                        <div class="w-section-head w-section-head-tight">
                            @if ($txt('plans_heading') !== '')<h2>{{ $txt('plans_heading') }}</h2>@endif
                            @if ($txt('plans_subheading') !== '')<p>{{ $txt('plans_subheading') }}</p>@endif
                        </div>
                    @endif
                    @if ($hasTest)
                        <div class="w-form-alert is-warning w-pricing-test-notice" role="note" data-pricing-test-notice>{{ __('locale.These plans and prices are for testing purposes only and are not commercial offers.') }}</div>
                    @endif
                    <div class="w-pricing-grid{{ count($plans) === 2 ? ' is-two' : '' }}" data-pricing-plans>
                        @foreach ($plans as $i => $plan)
                            @php
                                $name = \App\Support\InfoPages::text($plan, 'name', $locale);
                                $badge = \App\Support\InfoPages::text($plan, 'badge', $locale);
                                $price = $priceText($plan, 'price');
                                $price2 = $priceText($plan, 'price_2');
                                $currency = trim((string) ($plan['currency'] ?? ''));
                                $period = \App\Support\InfoPages::text($plan, 'period', $locale);
                                $buttonText = \App\Support\InfoPages::text($plan, 'button_text', $locale);
                                $isTest = !empty($plan['test']);
                                $isHighlighted = !empty($plan['highlighted']);
                                // A test plan's button only ever leads to the Contact page - never a purchase.
                                $buttonUrl = $isTest ? '' : ($plan['button_url'] ?? '');
                                // The badge already names the plan (reference "Service charge" card): no repeated title.
                                $showName = $badge === '' || mb_strtolower($badge) !== mb_strtolower($name);
                            @endphp
                            <article class="{{ trim('w-plan-card ' . ($isHighlighted ? 'is-highlighted ' : '') . ($isTest ? 'is-test' : '')) }}" data-pricing-plan{{ $isTest ? ' data-pricing-test' : '' }}>
                                @if ($badge !== '')
                                    <span class="w-plan-badge">{{ $badge }}</span>
                                @elseif ($isHighlighted)
                                    <span class="w-plan-badge">{{ __('locale.Recommended') }}</span>
                                @endif
                                @if ($isTest)
                                    <span class="w-plan-test-label">{{ __('locale.TEST PLAN · DUMMY PRICE — NOT FOR SALE') }}</span>
                                @endif
                                @if ($showName)<h3>{{ $name }}</h3>@endif
                                @if (\App\Support\InfoPages::text($plan, 'description', $locale) !== '')
                                    <p class="w-plan-desc">{{ \App\Support\InfoPages::text($plan, 'description', $locale) }}</p>
                                @endif
                                @if ($price !== '')
                                    <div class="w-plan-price" data-pricing-price><span dir="ltr">{{ $price }}@if ($currency !== '') {{ $currency }}@endif</span>@if ($period !== '')<small>{{ $period }}</small>@endif</div>
                                @endif
                                @if ($price2 !== '')
                                    <div class="w-plan-price is-second"><span dir="ltr">{{ $price2 }}</span></div>
                                @endif
                                @if (count($planFeatures[$i]))
                                    <ul class="w-plan-features">
                                        @foreach ($planFeatures[$i] as $feature)
                                            <li>@include('worker.partials.ref-icon', ['name' => 'check'])<span>{{ $feature }}</span></li>
                                        @endforeach
                                    </ul>
                                @endif
                                @if ($buttonText !== '')
                                    <a href="{{ $link($buttonUrl) }}" class="w-btn {{ $isHighlighted ? 'w-btn-gold' : 'w-btn-line' }} w-plan-btn" @if ($external($buttonUrl)) target="_blank" rel="noopener" @endif>{{ $buttonText }}</a>
                                @endif
                                @if ($isTest)
                                    <small class="w-plan-test-note">{{ __('locale.Test plan — nothing can be bought here.') }}</small>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @elseif ($txt('empty_text') !== '' || ($txt('cta_heading') === '' && $txt('cta_button_text') === ''))
                    <div class="w-info-card w-pricing-empty" data-pricing-empty>
                        @if ($txt('empty_text') !== '')<p>{{ $txt('empty_text') }}</p>@endif
                        @if ($txt('cta_heading') === '' && $txt('cta_button_text') === '')
                            <a href="{{ route('worker.contact') }}" class="w-btn w-btn-primary">{{ __('locale.Contact Us') }}</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($hasBody)
        <section class="w-section" data-info-intro>
            <div class="w-container">
                <div class="w-prose w-prose-center">{!! $body !!}</div>
            </div>
        </section>
    @endif

    @if (count($plans) > 1 && $sharedFeature)
        <section class="w-section" data-pricing-compare>
            <div class="w-container">
                <div class="w-section-head w-center"><h2>{{ __('locale.Compare plans') }}</h2></div>
                <div class="w-scroll-x">
                    <table class="w-compare w-compare-plans">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('locale.Features') }}</th>
                                @foreach ($plans as $plan)
                                    <th scope="col">{{ \App\Support\InfoPages::text($plan, 'name', $locale) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allFeatures as $feature)
                                <tr>
                                    <th scope="row">{{ $feature }}</th>
                                    @foreach ($planFeatures as $features)
                                        <td>@if (in_array($feature, $features, true))<span class="w-plan-yes" aria-label="{{ __('locale.Included') }}">✓</span>@else<span class="w-plan-no" aria-label="{{ __('locale.Not included') }}">—</span>@endif</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif

    @if (count($categories))
        <section class="w-section" data-pricing-categories>
            <div class="w-container">
                @if ($txt('categories_eyebrow') !== '' || $txt('categories_heading') !== '')
                    <div class="w-section-head w-center">
                        @if ($txt('categories_eyebrow') !== '')<span class="w-eyebrow">{{ $txt('categories_eyebrow') }}</span>@endif
                        @if ($txt('categories_heading') !== '')<h2>{{ $txt('categories_heading') }}</h2>@endif
                    </div>
                @endif
                <div class="w-scroll-x">
                    <table class="w-compare">
                        <thead>
                            <tr>
                                <th scope="col" class="is-us">{{ $txt('categories_col_name') }}</th>
                                <th scope="col">{{ $txt('categories_col_meaning') }}</th>
                                <th scope="col">{{ $txt('categories_col_charge') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $row)
                                <tr>
                                    <td class="is-us">{{ $item($row, 'name') }}</td>
                                    <td class="is-them">{{ $item($row, 'meaning') }}</td>
                                    <td>@if (($row['charge'] ?? 'free') === 'paid')<span class="w-badge w-badge-gold">{{ __('locale.Paid') }}</span>@else<span class="w-badge w-badge-ok">{{ __('locale.Free') }}</span>@endif</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif

    @if ($txt('billing_heading') !== '' || count($billing) || count($checks))
        <section class="w-section w-band" data-pricing-billing>
            <div class="w-container">
                <div class="w-narrow">
                    @if ($txt('billing_eyebrow') !== '')<span class="w-eyebrow">{{ $txt('billing_eyebrow') }}</span>@endif
                    @if ($txt('billing_heading') !== '')
                        <div class="w-section-head w-section-head-tight"><h2>{{ $txt('billing_heading') }}</h2></div>
                    @endif
                    @if (count($billing))
                        <div class="w-prose">
                            @foreach ($billing as $paragraph)
                                <p>{!! nl2br(e($paragraph)) !!}</p>
                            @endforeach
                        </div>
                    @endif
                    @if (count($checks))
                        <ul class="w-checks">
                            @foreach ($checks as $row)
                                <li>@include('worker.partials.ref-icon', ['name' => 'check'])<span>{{ $item($row, 'text') }}</span></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @include('worker.partials.info-faq', ['faqs' => $lists['faqs'] ?? [], 'eyebrow' => $txt('faq_eyebrow'), 'heading' => $txt('faq_heading')])

    @include('worker.partials.info-cta', ['ctaFallback' => route('worker.contact')])
@endsection
