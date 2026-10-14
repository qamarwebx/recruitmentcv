@extends('worker.layouts.app')

{{-- How It Works, laid out like the design reference ("RecruitmentCV Pages").
     Texts + lists (setup card, compare table, FAQs) from Website Config -> How
     It Works ($page / $lists: partner -> Recruitmentcv Partner (Global) ->
     recruitmentcv.com; Arabic falls back to English); steps from
     App\Support\InfoPages. A part with nothing saved is not shown. --}}
@php
    $locale = app()->getLocale();
    $txt = fn ($key) => trim((string) ($page[$key] ?? ''));
    $item = fn ($row, $field) => \App\Support\PageLists::text($row, $field, $locale);
    $body = \App\Support\HtmlSanitizer::clean((string) ($page['body'] ?? ''));
    $hasBody = trim(strip_tags($body)) !== '';
    $cards = array_values(array_filter($steps, fn ($s) => \App\Support\InfoPages::text($s, 'title', $locale) !== ''));
    $setup = array_values(array_filter($lists['setup'] ?? [], fn ($r) => $item($r, 'title') !== ''));
    $compare = array_values(array_filter($lists['compare'] ?? [], fn ($r) => $item($r, 'label') !== ''));
    $hasSetup = count($setup) || $txt('setup_title') !== '';
    $title = $txt('header_title') ?: __('locale.How It Works');
    $stateBadge = ['done' => 'w-badge-ok', 'now' => 'w-badge-gold', 'next' => 'w-badge-grey'];
@endphp

@section('title', __('locale.How It Works') . ' — Qamr Worker Portal')
@section('meta_description', \Illuminate\Support\Str::limit($txt('header_subtitle') ?: trim(preg_replace('/\s+/', ' ', strip_tags($body))) ?: __('locale.How hiring works on this website, step by step.'), 160, '…'))

@section('content')
    <section class="w-ihero" data-info-hero>
        <div class="w-container w-ihero-grid{{ $hasSetup ? '' : ' is-single' }}">
            <div>
                <nav class="w-crumbs" aria-label="{{ __('locale.Home') }}">
                    <a href="{{ route('worker.home') }}">{{ __('locale.Home') }}</a><span aria-hidden="true">/</span><span>{{ __('locale.How It Works') }}</span>
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
                            <a class="w-btn w-btn-line" href="{{ route('worker.pricing') }}">{{ $txt('hero_secondary_text') }}</a>
                        @endif
                    </div>
                @endif
            </div>

            @if ($hasSetup)
                <div class="w-setup-card" data-info-setup>
                    @if ($txt('setup_title') !== '' || $txt('setup_subtitle') !== '' || $txt('setup_badge') !== '')
                        <div class="w-setup-head">
                            <div>
                                @if ($txt('setup_title') !== '')<b>{{ $txt('setup_title') }}</b>@endif
                                @if ($txt('setup_subtitle') !== '')<small>{{ $txt('setup_subtitle') }}</small>@endif
                            </div>
                            @if ($txt('setup_badge') !== '')
                                <span class="w-badge w-badge-ok">@include('worker.partials.ref-icon', ['name' => 'check']){{ $txt('setup_badge') }}</span>
                            @endif
                        </div>
                    @endif
                    @if (count($setup))
                        <ol class="w-setup-steps">
                            @foreach ($setup as $row)
                                @php $state = in_array($row['state'] ?? '', ['done', 'now', 'next'], true) ? $row['state'] : 'next'; @endphp
                                <li class="is-{{ $state }}">
                                    <i aria-hidden="true">@if ($state === 'done')@include('worker.partials.ref-icon', ['name' => 'check'])@endif</i>
                                    <div>
                                        <b>{{ $item($row, 'title') }}</b>
                                        @if ($item($row, 'text') !== '')<small>{{ $item($row, 'text') }}</small>@endif
                                    </div>
                                    @if ($item($row, 'status') !== '')<span class="w-badge {{ $stateBadge[$state] }}">{{ $item($row, 'status') }}</span>@else<span></span>@endif
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            @endif
        </div>
    </section>

    @if ($hasBody)
        <section class="w-section" data-info-intro>
            <div class="w-container">
                <div class="w-prose w-prose-center">{!! $body !!}</div>
            </div>
        </section>
    @endif

    @if (count($cards))
        <section class="w-section w-flow-sec" data-info-steps>
            <div class="w-container">
                @if ($txt('steps_eyebrow') !== '' || $txt('steps_heading') !== '' || $txt('steps_subheading') !== '')
                    <div class="w-section-head w-center">
                        @if ($txt('steps_eyebrow') !== '')<span class="w-eyebrow">{{ $txt('steps_eyebrow') }}</span>@endif
                        @if ($txt('steps_heading') !== '')<h2>{{ $txt('steps_heading') }}</h2>@endif
                        @if ($txt('steps_subheading') !== '')<p>{{ $txt('steps_subheading') }}</p>@endif
                    </div>
                @endif
                <ol class="w-flow{{ count($cards) === 4 ? ' is-four' : '' }}">
                    @foreach ($cards as $i => $step)
                        @php
                            $tag = \App\Support\InfoPages::text($step, 'tag', $locale);
                            $tagStyle = in_array($step['tag_style'] ?? '', \App\Support\InfoPages::TAG_STYLES, true) ? $step['tag_style'] : 'agency';
                        @endphp
                        <li class="w-flow-step" data-info-step>
                            <div class="w-flow-top">
                                @if (!empty($step['image']))
                                    <img src="{{ $step['image'] }}" alt="" class="w-flow-img" loading="lazy">
                                @else
                                    <span class="w-flow-n">{{ $i + 1 }}</span>
                                @endif
                                @if (!empty($step['icon']))
                                    <span class="w-flow-ic">@include('worker.partials.ref-icon', ['name' => $step['icon']])</span>
                                @endif
                            </div>
                            @if ($tag !== '')<span class="w-own is-{{ $tagStyle }}">{{ $tag }}</span>@endif
                            <h3>{{ \App\Support\InfoPages::text($step, 'title', $locale) }}</h3>
                            @if (\App\Support\InfoPages::text($step, 'text', $locale) !== '')
                                <p>{!! nl2br(e(\App\Support\InfoPages::text($step, 'text', $locale))) !!}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    @if (count($compare))
        <section class="w-section w-band" data-info-compare>
            <div class="w-container">
                @if ($txt('compare_eyebrow') !== '' || $txt('compare_heading') !== '')
                    <div class="w-section-head w-center">
                        @if ($txt('compare_eyebrow') !== '')<span class="w-eyebrow">{{ $txt('compare_eyebrow') }}</span>@endif
                        @if ($txt('compare_heading') !== '')<h2>{{ $txt('compare_heading') }}</h2>@endif
                    </div>
                @endif
                <div class="w-scroll-x">
                    <table class="w-compare">
                        <thead>
                            <tr>
                                <th scope="col"><span class="w-sr-only">{{ __('locale.Features') }}</span></th>
                                <th scope="col" class="is-us">{{ $txt('compare_col_with') }}</th>
                                <th scope="col">{{ $txt('compare_col_without') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($compare as $row)
                                <tr>
                                    <td>{{ $item($row, 'label') }}</td>
                                    <td class="is-us">@if ($item($row, 'with') !== '')@include('worker.partials.ref-icon', ['name' => 'check']){{ $item($row, 'with') }}@endif</td>
                                    <td class="is-them">{{ $item($row, 'without') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif

    @include('worker.partials.info-faq', ['faqs' => $lists['faqs'] ?? [], 'eyebrow' => $txt('faq_eyebrow'), 'heading' => $txt('faq_heading')])

    {{-- CTA link from Website Config (http(s)://... or /path); blank = the candidate list (Browse Resumes). --}}
    @include('worker.partials.info-cta', ['ctaFallback' => route('worker.resumes')])
@endsection
