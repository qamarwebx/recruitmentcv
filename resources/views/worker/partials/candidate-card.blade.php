{{-- The one candidate card, used everywhere cards are shown (home, /resumes,
related candidates on the detail page, Partner Portal candidates). Order:
photo (Verified + experience badges), name, experience + profession label
(Candidate::display_profession_label, same as the detail page), age,
profession, View Profile / Hire Now.

Optional, for the Partner Portal: $cardUrl (profile link, default the public
resume page), $cardHireUrl/$cardHireLabel (second button, default Hire Now to
the profile), $cardHired (shows the "Hired" badge next to the name). --}}
@php
    $totalExp = $post->experience ? array_sum(array_filter(explode(',', $post->experience), 'is_numeric')) : 0;
    $imagePath = \App\Support\CandidatePhoto::url($post->photo_file);
    $defaultAvatar = \App\Support\CandidatePhoto::defaultUrl();
    $detailsUrl = $cardUrl ?? route('worker.resume.details', $post->slug_text);
    // Row icons (same inline stroke icons used elsewhere in the site):
    // briefcase = experience, calendar = age, user = profession.
    $svg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">%s</svg>';
    $experienceIcon = sprintf($svg, '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>');
    $ageIcon = sprintf($svg, '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>');
    $professionIcon = sprintf($svg, '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>');
@endphp
<div class="w-candidate-card">
    <a href="{{ $detailsUrl }}" class="w-candidate-photo">
        <span class="w-candidate-badge">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 14l10 5 10-5-10-5Z"/></svg>
            {{ __('locale.Verified') }}
        </span>
        <span class="w-candidate-exp">{{ $totalExp > 0 ? $totalExp . '+ ' . __('locale.Years Experience') : __('locale.Fresher') }}</span>
        <img src="{{ $imagePath }}" alt="{{ $post->display_name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';">
    </a>
    <div class="w-candidate-body">
        @if (!empty($cardHired))
            <div class="w-profile-name">
                <h3 style="margin:0;">{{ $post->display_name }}</h3>
                <span class="w-verified">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 14l10 5 10-5-10-5Z"/></svg>
                    {{ __('locale.Hired') }}
                </span>
            </div>
        @else
            <h3>{{ $post->display_name }}</h3>
        @endif
        <div class="w-candidate-meta">
            <div>
                {!! $experienceIcon !!}
                {{ $post->display_profession_label }}
            </div>
            @if ($post->age)
                <div>
                    {!! $ageIcon !!}
                    {{ $post->age }} {{ __('locale.years old') }}
                </div>
            @endif
            @if (optional($post->profession)->display_name)
                <div>
                    {!! $professionIcon !!}
                    {{ $post->profession->display_name }}
                </div>
            @endif
        </div>
        <div class="w-candidate-actions">
            <a href="{{ $detailsUrl }}" class="w-btn w-btn-outline">{{ __('locale.View Profile') }}</a>
            <a href="{{ $cardHireUrl ?? $detailsUrl }}" class="w-btn w-btn-primary">{{ $cardHireLabel ?? __('locale.Hire Now') }}</a>
        </div>
    </div>
</div>
