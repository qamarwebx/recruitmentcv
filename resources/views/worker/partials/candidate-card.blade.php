{{-- The one candidate card, used everywhere cards are shown (home, /resumes,
related candidates on the detail page, Partner Portal candidates). Order:
photo (experience badge), name + blue verified mark, then Experience Type,
Profession, Employment Experience (Candidate::employment_periods - the
detail page's Period values), Age, Religion, View Profile / Hire Now.

Optional, for the Partner Portal: $cardUrl (profile link, default the public
resume page), $cardHireUrl/$cardHireLabel (second button, default Hire Now to
the profile), $cardHired (shows the "Hired" badge next to the name). --}}
@php
    $totalExp = $post->experience ? array_sum(array_filter(explode(',', $post->experience), 'is_numeric')) : 0;
    $imagePath = \App\Support\CandidatePhoto::url($post->photo_file);
    $defaultAvatar = \App\Support\CandidatePhoto::defaultUrl();
    $detailsUrl = $cardUrl ?? route('worker.resume.details', $post->slug_text);
    // Row icons (same inline stroke icons used elsewhere in the site):
    // briefcase = experience type, user = profession, calendar = age.
    $svg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">%s</svg>';
    $experienceIcon = sprintf($svg, '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>');
    $ageIcon = sprintf($svg, '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>');
    $professionIcon = sprintf($svg, '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>');
    // clock-with-arrow = employment history, open book = religion.
    $periodIcon = sprintf($svg, '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/>');
    $religionIcon = sprintf($svg, '<path d="M2 4h6a4 4 0 0 1 4 4v13a3 3 0 0 0-3-3H2z"/><path d="M22 4h-6a4 4 0 0 0-4 4v13a3 3 0 0 1 3-3h7z"/>');
@endphp
<div class="w-candidate-card">
    <a href="{{ $detailsUrl }}" class="w-candidate-photo">
        <span class="w-candidate-exp">{{ $totalExp > 0 ? $totalExp . '+ ' . __('locale.Years Experience') : __('locale.Fresher') }}</span>
        <img src="{{ $imagePath }}" alt="{{ $post->display_name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';">
    </a>
    <div class="w-candidate-body">
        @if (!empty($cardHired))
            <div class="w-profile-name">
                <h3 style="margin:0;">{{ $post->display_name }}@include('worker.partials.candidate-verified-icon')</h3>
                <span class="w-verified">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 14l10 5 10-5-10-5Z"/></svg>
                    {{ __('locale.Hired') }}
                </span>
            </div>
        @else
            <h3>{{ $post->display_name }}@include('worker.partials.candidate-verified-icon')</h3>
        @endif
        {{-- Same order on every card: Experience Type, Profession, Employment
        Experience (the detail page's Period values), Age, Religion. --}}
        <div class="w-candidate-meta">
            <div>
                {!! $experienceIcon !!}
                {{ $post->display_experience_type }}
            </div>
            <div>
                {!! $professionIcon !!}
                {{ optional($post->profession)->display_name ?: '---' }}
            </div>
            <div>
                {!! $periodIcon !!}
                {{ $post->employment_periods ? implode(app()->getLocale() === 'ar' ? '، ' : ', ', $post->employment_periods) : __('locale.Fresher') }}
            </div>
            <div>
                {!! $ageIcon !!}
                {{ $post->age ? $post->age . ' ' . __('locale.years old') : '---' }}
            </div>
            <div>
                {!! $religionIcon !!}
                {{ $post->display_religion }}
            </div>
        </div>
        <div class="w-candidate-actions">
            <a href="{{ $detailsUrl }}" class="w-btn w-btn-outline">{{ __('locale.View Profile') }}</a>
            <a href="{{ $cardHireUrl ?? $detailsUrl }}" class="w-btn w-btn-primary">{{ $cardHireLabel ?? __('locale.Hire Now') }}</a>
        </div>
    </div>
</div>
