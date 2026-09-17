@php
    $totalExp = $post->experience ? array_sum(array_filter(explode(',', $post->experience), 'is_numeric')) : 0;
    $imagePath = \App\Support\CandidatePhoto::url($post->photo_file);
    $defaultAvatar = \App\Support\CandidatePhoto::defaultUrl();
    $detailsUrl = route('worker.resume.details', $post->slug_text);
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
        <h3>{{ $post->display_name }}</h3>
        <div class="w-candidate-meta">
            @if ($post->age)
                <div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ $post->age }} {{ __('locale.years old') }}
                </div>
            @endif
            @if (optional($post->profession)->display_name)
                <div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    {{ $post->profession->display_name }}
                </div>
            @endif
        </div>
        <div class="w-candidate-actions">
            <a href="{{ $detailsUrl }}" class="w-btn w-btn-outline">{{ __('locale.View Profile') }}</a>
            <a href="{{ $detailsUrl }}" class="w-btn w-btn-primary">{{ __('locale.Hire Now') }}</a>
        </div>
    </div>
</div>
