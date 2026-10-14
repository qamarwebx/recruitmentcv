@extends('worker.layouts.app')

{{-- About Us, laid out like the design reference ("RecruitmentCV Pages"). Every
     text and list comes from Website Config -> About Us ($page / $lists: partner
     -> Recruitmentcv Partner (Global) -> recruitmentcv.com; an Arabic field left
     empty shows the English one). A part with nothing saved is not shown.
     Candidates: the latest available public candidates with the same fields and
     photo as the listing cards (WorkerPageController::aboutUs). --}}
@php
    $locale = app()->getLocale();
    $txt = fn ($key) => trim((string) ($page[$key] ?? ''));
    $item = fn ($row, $field) => \App\Support\PageLists::text($row, $field, $locale);
    $body = \App\Support\HtmlSanitizer::clean((string) ($page['body'] ?? ''));
    $hasBody = trim(strip_tags($body)) !== '';
    $trust = array_values(array_filter($lists['trust'] ?? [], fn ($r) => $item($r, 'text') !== ''));
    $features = array_values(array_filter($lists['features'] ?? [], fn ($r) => $item($r, 'title') !== ''));
    $offices = array_values(array_filter($lists['offices'] ?? [], fn ($r) => $item($r, 'city') !== ''));
    $mvv = array_values(array_filter([
        ['heading' => $txt('mission_heading'), 'text' => $txt('mission_text'), 'icon' => 'target'],
        ['heading' => $txt('vision_heading'), 'text' => $txt('vision_text'), 'icon' => 'eye'],
        ['heading' => $txt('values_heading'), 'text' => $txt('values_text'), 'icon' => 'heart'],
    ], fn ($card) => $card['heading'] !== '' || $card['text'] !== ''));
    // Photos already on the site (public/user/img/gallery, as qamarhire.com's About page).
    $whoPhoto = asset('user/img/gallery/team.jpg');
    $officePhoto = asset('user/img/gallery/Recp.jpg');
    $galleryImages = ['team.jpg', 'team3.JPG', 'team4.JPG', 'team5.JPG', 'team8.JPG', 'team6.JPG', 'team7.JPG'];
    $title = $txt('header_title') ?: __('locale.About Us');
@endphp

@section('title', __('locale.About Us') . ' — Qamr Worker Portal')
@section('meta_description', \Illuminate\Support\Str::limit($txt('header_subtitle') ?: $title, 160, '…'))

@section('content')
    <section class="w-ihero" data-info-hero>
        <div class="w-container w-ihero-grid{{ count($candidates) ? '' : ' is-single' }}">
            <div>
                <nav class="w-crumbs" aria-label="{{ __('locale.Home') }}">
                    <a href="{{ route('worker.home') }}">{{ __('locale.Home') }}</a><span aria-hidden="true">/</span><span>{{ __('locale.About Us') }}</span>
                </nav>
                <h1>{{ $title }}@if ($txt('header_title_highlight') !== '') <em>{{ $txt('header_title_highlight') }}</em>@endif</h1>
                @if ($txt('header_subtitle') !== '')
                    <p class="w-ihero-lead">{{ $txt('header_subtitle') }}</p>
                @endif
                @if ($txt('hero_primary_text') !== '' || $txt('hero_secondary_text') !== '')
                    <div class="w-actions">
                        @if ($txt('hero_primary_text') !== '')
                            <a class="w-btn w-btn-primary" href="{{ route('worker.resumes') }}">@include('worker.partials.ref-icon', ['name' => 'search']){{ $txt('hero_primary_text') }}</a>
                        @endif
                        @if ($txt('hero_secondary_text') !== '')
                            <a class="w-btn w-btn-line" href="{{ route('worker.contact') }}">{{ $txt('hero_secondary_text') }}</a>
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

            @if (count($candidates))
                <div class="w-cands" data-about-candidates>
                    @foreach ($candidates as $post)
                        @php
                            $detailsUrl = route('worker.resume.details', $post->slug_text);
                            $experience = $post->display_experience_label;
                        @endphp
                        <article class="w-cand" data-about-candidate>
                            <a href="{{ $detailsUrl }}" class="w-cand-ph">
                                <img src="{{ \App\Support\CandidatePhoto::url($post->photo_file) }}" alt="{{ $post->display_name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ \App\Support\CandidatePhoto::defaultUrl() }}';">
                                <i aria-hidden="true"></i>
                            </a>
                            <div>
                                <div class="w-cand-st">{{ __('locale.Candidate Available') }}</div>
                                <div class="w-cand-nm"><span>{{ $post->display_name }}@include('worker.partials.candidate-verified-icon')</span>@if ($experience !== '---')<span class="w-badge w-badge-gold">{{ $experience }}</span>@endif</div>
                                <div class="w-cand-meta">
                                    @if (optional($post->profession)->display_name)<span>{{ $post->profession->display_name }}</span>@endif
                                    <span>{{ $post->total_experience_label ?? __('locale.Fresher') }}</span>
                                    @if ($post->age)<span>{{ $post->age }} {{ __('locale.years old') }}</span>@endif
                                </div>
                            </div>
                            <div class="w-cand-act">
                                <a class="w-btn w-btn-line w-btn-sm" href="{{ $detailsUrl }}">{{ __('locale.View Profile') }}</a>
                                <a class="w-btn w-btn-gold w-btn-sm" href="{{ $detailsUrl }}">{{ __('locale.Hire Now') }}</a>
                            </div>
                        </article>
                    @endforeach
                    <p class="w-cands-note">{{ __('locale.Latest available profiles.') }} <a href="{{ route('worker.resumes') }}">{{ __('locale.Browse Resumes') }}</a></p>
                </div>
            @endif
        </div>
    </section>

    {{-- Find a driver: the Browse Resumes filters (same field names as /resumes). --}}
    <section class="w-section" data-about-search>
        <div class="w-container">
            @if ($txt('search_eyebrow') !== '' || $txt('search_heading') !== '' || $txt('search_subheading') !== '')
                <div class="w-section-head w-center">
                    @if ($txt('search_eyebrow') !== '')<span class="w-eyebrow">{{ $txt('search_eyebrow') }}</span>@endif
                    @if ($txt('search_heading') !== '')<h2>{{ $txt('search_heading') }}</h2>@endif
                    @if ($txt('search_subheading') !== '')<p>{{ $txt('search_subheading') }}</p>@endif
                </div>
            @endif
            <form class="w-search-panel" action="{{ route('worker.resumes') }}" method="get">
                <div class="w-search-row">
                    <label>{{ __('locale.Profession') }}
                        <select name="proff_id">
                            <option value="">{{ __('locale.All professions') }}</option>
                            @foreach ($professions as $profession)
                                <option value="{{ $profession->id }}">{{ $profession->display_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>{{ __('locale.Experience Type') }}
                        <select name="expcity_id">
                            <option value="">{{ __('locale.Any experience') }}</option>
                            <option value="1">{{ __('locale.Indian Experience') }}</option>
                            <option value="2">{{ __('locale.Gulf / Abroad Experience') }}</option>
                        </select>
                    </label>
                    <label>{{ __('locale.Work Location') }}
                        <select name="location_id">
                            <option value="">{{ __('locale.Any location') }}</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->display_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>{{ __('locale.Age Range') }}
                        <select name="age">
                            <option value="">{{ __('locale.Any age') }}</option>
                            @foreach (['22-25', '26-30', '31-35', '36-40', '41-45', '46-50'] as $range)
                                <option value="{{ $range }}">{{ str_replace('-', ' – ', $range) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="w-btn w-btn-primary" type="submit">@include('worker.partials.ref-icon', ['name' => 'search']){{ __('locale.Search') }}</button>
                </div>
                @if (count($chipProfessions) || count($chipCities))
                    <div class="w-chips">
                        <small>{{ __('locale.Browse:') }}</small>
                        @foreach ($chipProfessions as $profession)
                            <a href="{{ route('worker.resumes', ['proff_id' => $profession->id]) }}">{{ $profession->display_name }}</a>
                        @endforeach
                        <a href="{{ route('worker.resumes', ['expcity_id' => 2]) }}">{{ __('locale.Gulf / Abroad Experience') }}</a>
                        @foreach ($chipCities as $city)
                            <a href="{{ route('worker.resumes', ['location_id' => $city->id]) }}">{{ $city->display_name }}</a>
                        @endforeach
                    </div>
                @endif
            </form>
        </div>
    </section>

    @if ($txt('who_heading') !== '' || $hasBody)
        <section class="w-section w-band" data-about-who>
            <div class="w-container w-split">
                <div>
                    @if ($txt('who_eyebrow') !== '')<span class="w-eyebrow">{{ $txt('who_eyebrow') }}</span>@endif
                    @if ($txt('who_heading') !== '')
                        <div class="w-section-head w-section-head-tight"><h2>{{ $txt('who_heading') }}</h2></div>
                    @endif
                    @if ($hasBody)
                        <div class="w-prose">{!! $body !!}</div>
                    @endif
                    @if ($txt('who_cta_text') !== '')
                        <p class="w-split-cta"><a class="w-btn w-btn-line" href="{{ route('worker.how-it-works') }}">{{ $txt('who_cta_text') }}</a></p>
                    @endif
                </div>
                <div class="w-media">
                    <div class="w-photo"><img src="{{ $whoPhoto }}" alt="{{ $txt('who_eyebrow') ?: __('locale.About Us') }}" loading="lazy"></div>
                    @if ($txt('who_photo_tag') !== '')
                        <div class="w-media-tag">@include('worker.partials.ref-icon', ['name' => 'pin']){{ $txt('who_photo_tag') }}</div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if (count($features))
        <section class="w-section" data-about-features>
            <div class="w-container">
                @if ($txt('why_choose_eyebrow') !== '' || $txt('why_choose_heading') !== '')
                    <div class="w-section-head w-center">
                        @if ($txt('why_choose_eyebrow') !== '')<span class="w-eyebrow">{{ $txt('why_choose_eyebrow') }}</span>@endif
                        @if ($txt('why_choose_heading') !== '')<h2>{{ $txt('why_choose_heading') }}</h2>@endif
                    </div>
                @endif
                <div class="w-feat-grid">
                    @foreach ($features as $row)
                        <div class="w-feat">
                            @if (!empty($row['icon']))<div class="w-ico">@include('worker.partials.ref-icon', ['name' => $row['icon']])</div>@endif
                            <h3>{{ $item($row, 'title') }}</h3>
                            @if ($item($row, 'text') !== '')<p>{{ $item($row, 'text') }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (count($mvv))
        <section class="w-section w-band" data-about-mvv>
            <div class="w-container">
                @if ($txt('mvv_eyebrow') !== '' || $txt('mvv_heading') !== '')
                    <div class="w-section-head w-center">
                        @if ($txt('mvv_eyebrow') !== '')<span class="w-eyebrow">{{ $txt('mvv_eyebrow') }}</span>@endif
                        @if ($txt('mvv_heading') !== '')<h2>{{ $txt('mvv_heading') }}</h2>@endif
                    </div>
                @endif
                <div class="w-mvv">
                    @foreach ($mvv as $card)
                        <div class="w-mv">
                            <div class="w-ico">@include('worker.partials.ref-icon', ['name' => $card['icon']])</div>
                            @if ($card['heading'] !== '')<h3>{{ $card['heading'] }}</h3>@endif
                            @if ($card['text'] !== '')<p>{{ $card['text'] }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (count($offices) || $txt('offices_heading') !== '')
        <section class="w-section" data-about-offices>
            <div class="w-container w-split is-rev">
                <div>
                    @if ($txt('offices_eyebrow') !== '')<span class="w-eyebrow">{{ $txt('offices_eyebrow') }}</span>@endif
                    @if ($txt('offices_heading') !== '' || $txt('offices_subheading') !== '')
                        <div class="w-section-head w-section-head-tight">
                            @if ($txt('offices_heading') !== '')<h2>{{ $txt('offices_heading') }}</h2>@endif
                            @if ($txt('offices_subheading') !== '')<p>{{ $txt('offices_subheading') }}</p>@endif
                        </div>
                    @endif
                    @if (count($offices))
                        <div class="w-offices">
                            @foreach ($offices as $row)
                                <div class="w-office{{ !empty($row['highlight']) ? ' is-hq' : '' }}">
                                    @if ($item($row, 'tag') !== '')<span class="w-office-tag">{{ $item($row, 'tag') }}</span>@endif
                                    <h3>@include('worker.partials.ref-icon', ['name' => 'pin']){{ $item($row, 'city') }}</h3>
                                    @if ($item($row, 'text') !== '')<p>{{ $item($row, 'text') }}</p>@endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="w-media">
                    <div class="w-photo"><img src="{{ $officePhoto }}" alt="{{ $txt('offices_photo_tag') ?: $txt('offices_eyebrow') }}" loading="lazy"></div>
                    @if ($txt('offices_photo_tag') !== '')
                        <div class="w-media-tag">@include('worker.partials.ref-icon', ['name' => 'pin']){{ $txt('offices_photo_tag') }}</div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <section class="w-section w-band" data-about-gallery>
        <div class="w-container">
            @if ($txt('gallery_eyebrow') !== '' || $txt('gallery_heading') !== '')
                <div class="w-section-head w-center">
                    @if ($txt('gallery_eyebrow') !== '')<span class="w-eyebrow">{{ $txt('gallery_eyebrow') }}</span>@endif
                    @if ($txt('gallery_heading') !== '')<h2>{{ $txt('gallery_heading') }}</h2>@endif
                </div>
            @endif
            <div class="w-photo-grid">
                @foreach ($galleryImages as $i => $file)
                    <figure><img src="{{ asset('user/img/gallery/' . $file) }}" alt="{{ __('locale.Gallery') }} {{ $i + 1 }}" loading="lazy"></figure>
                @endforeach
            </div>
        </div>
    </section>

    @include('worker.partials.info-cta', ['ctaFallback' => route('worker.contact')])
@endsection
