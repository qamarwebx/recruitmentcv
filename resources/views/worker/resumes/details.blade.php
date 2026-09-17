@extends('worker.layouts.app')

@section('title', $post->display_name . ' — Candidate Profile — Qamr Worker Portal')
@section('meta_description', 'View the full verified profile for ' . $post->display_name . ' including experience, education and passport details.')

@section('content')

    @php
        $totalExp = $totalExperience;
        $imagePath = \App\Support\CandidatePhoto::url($post->photo_file);
        $defaultAvatar = \App\Support\CandidatePhoto::defaultUrl();
        $imgFallback = 'onerror="this.onerror=null;this.src=\'' . $defaultAvatar . '\';"';

        $slides = [];

        // Always shown; CandidatePhoto::url() already falls back to the default avatar
        // when photo_file is empty, and onerror covers a genuinely broken CRM URL.
        $slides[] = ['thumb' => $imagePath, 'html' => '<img src="' . $imagePath . '" alt="' . e($post->display_name) . '" ' . $imgFallback . '>'];

        if ($passUrl = \App\Support\CandidatePhoto::urlIfExists($post->pass_file)) {
            $slides[] = ['thumb' => $passUrl, 'html' => '<img src="' . $passUrl . '" alt="Passport photo" ' . $imgFallback . '>'];
        }
        if ($licUrl = \App\Support\CandidatePhoto::urlIfExists($post->lic_file)) {
            $slides[] = ['thumb' => $licUrl, 'html' => '<img src="' . $licUrl . '" alt="License document" ' . $imgFallback . '>'];
        }
        if ($post->video_file) {
            $src = asset('videos/' . $post->video_file);
            $slides[] = ['thumb' => $imagePath, 'html' => '<video controls src="' . $src . '"></video>'];
        }
        if ($post->video_link) {
            $thumb = isset($videoId[4]) ? 'https://img.youtube.com/vi/' . $videoId[4] . '/hqdefault.jpg' : $imagePath;
            $slides[] = ['thumb' => $thumb, 'html' => '<iframe src="' . e($post->video_link) . '" allowfullscreen></iframe>'];
        }
        if ($post->trade_test_video_link) {
            $thumb = isset($testVideoId[4]) ? 'https://img.youtube.com/vi/' . $testVideoId[4] . '/hqdefault.jpg' : $imagePath;
            $slides[] = ['thumb' => $thumb, 'html' => '<iframe src="' . e($post->trade_test_video_link) . '" allowfullscreen></iframe>'];
        }

        $contactPhone = $frontwebsite->bottom_contact_us_phone ?? ($frontwebsite->contact_us_phone ?? null);
    @endphp

    <section class="w-section" style="padding-top:36px;">
        <div class="w-container">
            <div class="w-breadcrumb" style="color:var(--w-ink-500);">
                <a href="{{ route('worker.home') }}" style="color:var(--w-ink-700);">{{ __('locale.Home') }}</a>
                <span>/</span>
                <a href="{{ route('worker.resumes') }}" style="color:var(--w-ink-700);">{{ __('locale.Resumes') }}</a>
                <span>/</span>
                <span>{{ $post->display_name }}</span>
            </div>

            <div class="w-details-grid">

                {{-- Gallery + profile card --}}
                <div class="w-gallery">
                    <div data-gallery>
                        {{-- Prev/next arrows are siblings of .w-gallery-main, not
                        children - initGallery() replaces .w-gallery-main's entire
                        innerHTML on every slide change (thumbnail click or arrow
                        click alike), which would wipe out the arrows if they lived
                        inside it, and that element's own overflow:hidden (for the
                        image's rounded corners) would clip them too. --}}
                        <div class="w-gallery-main-wrap">
                            @if (count($slides) > 1)
                                <button type="button" class="w-gallery-nav w-gallery-nav-prev" data-gallery-prev aria-label="{{ __('locale.Previous') }}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M15 18l-6-6 6-6"/></svg>
                                </button>
                            @endif
                            <div class="w-gallery-main" data-gallery-main>
                                {!! $slides[0]['html'] !!}
                            </div>
                            @if (count($slides) > 1)
                                <button type="button" class="w-gallery-nav w-gallery-nav-next" data-gallery-next aria-label="{{ __('locale.Next') }}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M9 6l6 6-6 6"/></svg>
                                </button>
                            @endif
                        </div>

                        @if (count($slides) > 1)
                            <div class="w-gallery-thumbs">
                                @foreach ($slides as $i => $slide)
                                    <button type="button" data-gallery-thumb data-slide-html="{{ $slide['html'] }}" class="{{ $i === 0 ? 'is-active' : '' }}">
                                        <img src="{{ $slide['thumb'] }}" alt="Thumbnail {{ $i + 1 }}" onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="w-profile-card">
                        <div class="w-profile-name">
                            <h1>{{ $post->display_name }}</h1>
                            <span class="w-verified">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 14l10 5 10-5-10-5Z"/></svg>
                                {{ __('locale.Verified') }}
                            </span>
                        </div>

                        <div class="w-profile-tags">
                            @if (optional($post->profession)->display_name)
                                <span class="w-tag">{{ $post->profession->display_name }}</span>
                            @endif
                            <span class="w-tag is-neutral">{{ $totalExp > 0 ? $totalExp . ' ' . __('locale.Years Experience') : __('locale.Fresher') }}</span>
                            @if ($post->age)
                                <span class="w-tag is-neutral">{{ $post->age }} {{ __('locale.yrs old') }}</span>
                            @endif
                        </div>

                        <div class="w-cta-row">
                            @guest('partner')
                                <button type="button" class="w-btn w-btn-accent" data-partner-auth-trigger data-redirect="{{ route('worker.partner.candidates.show', $post->slug_text) }}?hire=1">
                                    {{ __('locale.Hire Now') }}
                                </button>
                            @else
                                <a href="{{ route('worker.partner.candidates.show', $post->slug_text) }}?hire=1" class="w-btn w-btn-accent">
                                    {{ __('locale.Hire Now') }}
                                </a>
                            @endguest

                            @if ($post->cv_execute == 1 && $post->cv_execute_file != '')
                                @guest('partner')
                                    <button type="button" class="w-btn w-btn-outline" data-partner-auth-trigger data-redirect="{{ route('worker.resume.details', $post->slug_text) }}">
                                        {{ __('locale.Download CV') }}
                                    </button>
                                @else
                                    <a href="{{ asset('admin/assets/images/pdf/' . $post->cv_execute_file) }}" target="_blank" class="w-btn w-btn-outline">
                                        {{ __('locale.Download CV') }}
                                    </a>
                                @endguest
                            @elseif ($contactPhone)
                                <a href="tel:{{ $contactPhone }}" class="w-btn w-btn-outline">{{ __('locale.Call') }}</a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Details column --}}
                <div>
                    <div class="w-info-card w-fade">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            {{ __('locale.Personal Details') }}
                        </h2>
                        <div class="w-info-grid">
                            <div class="w-info-item">
                                <span>{{ __('locale.Full Name') }}</span>
                                <strong>{{ $post->display_name }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Age') }}</span>
                                <strong>{{ $post->age ?: '---' }} {{ __('locale.Years') }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Religion') }}</span>
                                <strong>{{ optional($religion)->display_name ?: '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Marital Status') }}</span>
                                <strong>{{ $post->display_marital_status ?: '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Nationality') }}</span>
                                <strong>{{ optional($nation)->display_name ?: '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Region') }}</span>
                                <strong>{{ optional($region)->display_name ?: '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Language') }}</span>
                                <strong>{{ $post->display_language ?: '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Preferred Work Location') }}</span>
                                <strong>{{ $expectedWorkPlaces->count() ? $expectedWorkPlaces->pluck('display_name')->implode(', ') : __('locale.Anywhere') }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="w-info-card w-fade">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            {{ __('locale.Employment Experience') }}
                        </h2>

                        @if ($post->experience && $post->experience != '0')
                            @php
                                $exps = explode(',', $post->experience);
                                $exprof = explode(',', (string) $post->proff_id);
                                $expcont = explode(',', (string) $post->expcountry_id);
                                $expcity = explode(',', (string) $post->expcity_id);
                            @endphp
                            @foreach ($exps as $key => $exp)
                                @php
                                    $expCountry = \App\Models\Country::find($expcont[$key] ?? null);
                                    $expProf = \App\Models\Profession::find($exprof[$key] ?? null);
                                    $expCity = \App\Models\City::find($expcity[$key] ?? null);
                                @endphp
                                <div class="w-exp-row">
                                    <div class="w-info-item">
                                        <span>{{ __('locale.Job') }}</span>
                                        <strong>{{ optional($expProf)->display_name ?: '---' }}</strong>
                                    </div>
                                    <div class="w-info-item">
                                        <span>{{ __('locale.Period') }}</span>
                                        <strong>{{ $exp }} {{ __('locale.Years Experience') }}</strong>
                                    </div>
                                    <div class="w-info-item">
                                        <span>{{ __('locale.Country') }}</span>
                                        <strong>{{ optional($expCountry)->display_name ?: '---' }}</strong>
                                    </div>
                                    <div class="w-info-item">
                                        <span>{{ __('locale.City') }}</span>
                                        <strong>{{ optional($expCity)->display_name ?: '---' }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p style="color:var(--w-ink-500);">{{ __('locale.This candidate is a fresher with no prior employment record on file.') }}</p>
                        @endif
                    </div>

                    <div class="w-info-card w-fade">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5-10-5Zm4 2v5c0 1 3 3 6 3s6-2 6-3v-5"/></svg>
                            {{ __('locale.Education & Skills') }}
                        </h2>
                        <div class="w-info-grid w-cols-4">
                            <div class="w-info-item">
                                <span>{{ __('locale.Education') }}</span>
                                <strong>{{ optional($education)->name ?: '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Vehicle Known') }}</span>
                                <strong>{{ $vehiclesKnown->count() ? $vehiclesKnown->pluck('name')->implode(', ') : '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Vehicle Transmission') }}</span>
                                <strong>{{ $vehicleTransmissions->count() ? $vehicleTransmissions->map(fn($t) => (app()->getLocale() === 'ar' && !empty($t->ar_name)) ? $t->ar_name : $t->eng_name)->implode(', ') : '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Google Map Location') }}</span>
                                <strong>{{ $post->google_map == 1 ? __('locale.Available') : __('locale.Not Available') }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="w-info-card w-fade">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                            {{ __('locale.Passport Details') }}
                        </h2>
                        <div class="w-info-grid">
                            <div class="w-info-item">
                                <span>{{ __('locale.Passport Number') }}</span>
                                <strong>{{ $post->pass_no ? substr_replace($post->pass_no, '**', -2) : '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Passport Type') }}</span>
                                <strong>{{ $post->pass_type ?: '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Date of Issue') }}</span>
                                <strong>{{ $post->doi ? date('d/m/Y', strtotime($post->doi)) : '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Date of Expiry') }}</span>
                                <strong>{{ $post->doe ? date('d/m/Y', strtotime($post->doe)) : '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Place of Issue') }}</span>
                                <strong>{{ optional($placeOfIssue)->display_name ?: '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Date of Birth') }}</span>
                                <strong>{{ $post->dob ? date('d/m/Y', strtotime($post->dob)) : '---' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="w-info-card w-fade">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            {{ __('locale.Price & Departure') }}
                        </h2>
                        <div class="w-summary-grid">
                            <div class="w-summary-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M9 9.5a2.5 2.5 0 0 1 2.5-2.5h1a2 2 0 1 1 0 4h-1a2 2 0 1 0 0 4h1a2.5 2.5 0 0 0 2.5-2.5"/></svg>
                                <strong>{{ $priceLabel }}</strong>
                                <span>{{ __('locale.Service Price') }}</span>
                            </div>
                            <div class="w-summary-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 15-9-9-9 9M12 6v15"/></svg>
                                <strong>{{ $departureLabel }}</strong>
                                <span>{{ __('locale.Departure from India') }}</span>
                            </div>
                            <div class="w-summary-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4Z"/></svg>
                                <strong>{{ __('locale.90 Days') }}</strong>
                                <span>{{ __('locale.Warranty') }}</span>
                            </div>
                            <div class="w-summary-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="12" cy="10" r="3"/></svg>
                                <strong>{{ __('locale.In Office') }}</strong>
                                <span>{{ __('locale.Passport') }}</span>
                            </div>
                            <div class="w-summary-item">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-8-4.5-8-11a8 8 0 0 1 16 0c0 6.5-8 11-8 11Z"/></svg>
                                <strong>{{ __('locale.Medically Fit') }}</strong>
                                <span>{{ __('locale.Medical Status') }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($bookingRequirements->count() > 0)
                        <div class="w-info-card w-fade">
                            <h2>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                                {{ __('locale.Booking Requirements') }}
                            </h2>
                            <ul class="w-requirement-list">
                                @foreach ($bookingRequirements as $requirement)
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                                        {{ $requirement->requirement_text }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($relatedPosts->count() > 0)
        <section class="w-section w-related" style="padding-top:0;">
            <div class="w-container">
                <div class="w-section-head w-fade">
                    <span class="w-eyebrow">{{ __('locale.Similar Profiles') }}</span>
                    <h2>{{ __('locale.Related candidates') }}</h2>
                </div>
                <div class="w-candidate-grid w-fade">
                    @foreach ($relatedPosts as $related)
                        @include('worker.partials.candidate-card', ['post' => $related])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
