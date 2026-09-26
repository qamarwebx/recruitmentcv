{{--
    Candidate image/document/video gallery - shared by the public
    worker.resumes.details page and the Partner Portal's candidates.show
    page so both ever build/render this exactly once. Expects $post (the
    Candidate model), $videoId/$testVideoId (explode($post->video_link/
    trade_test_video_link, '/') or null - see either caller for how these
    are computed). Renders only the <div data-gallery> block itself - the
    caller keeps its own surrounding .w-gallery wrapper (and whatever else,
    e.g. a sibling .w-profile-card, lives inside it) unchanged.
--}}
@php
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
@endphp

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
