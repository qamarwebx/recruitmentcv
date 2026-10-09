{{--
    Candidate image/document/video gallery - shared by the public
    worker.resumes.details page and the Partner Portal's candidates.show
    page so both ever build/render this exactly once. Expects $post (the
    Candidate model); videos come from App\Support\CandidateVideos.
    Renders only the <div data-gallery> block itself - the
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

    // Passport + driving licence (App\Support\PassportAccess - the one rule,
    // the same for both): a signed-in partner whose Registration Status is
    // Approved gets the originals; everyone else - guests and customers
    // included - gets server-blurred copies (main image and thumbnail) with
    // the red note for that document, and never the original file's URL.
    $documentProtection = \App\Support\PassportAccess::protection($post);
    $lockIcon = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>';
    $documents = [
        \App\Support\PassportAccess::PASSPORT => [$post->pass_file, 'Passport photo'],
        \App\Support\PassportAccess::LICENCE => [$post->lic_file, 'License document'],
    ];
    foreach ($documents as $document => [$documentFile, $documentAlt]) {
        if (!\App\Support\CandidatePhoto::exists($documentFile)) {
            continue;
        }
        if ($documentProtection) {
            $preview = \App\Support\PassportAccess::previewUrl($post->slug_text, $document);
            $note = \App\Support\PassportAccess::message($documentProtection, $document);
            $slides[] = ['thumb' => $preview, 'restricted' => $note, 'html' => '<div class="w-gallery-blurred" data-document="' . $document . '"><img src="' . $preview . '" alt="' . $documentAlt . ' (blurred)" ' . $imgFallback . '>'
                . '<span class="w-gallery-blurred-note" role="note">' . $lockIcon . '<span>' . e($note) . '</span></span></div>'];
        } else {
            $documentUrl = \App\Support\CandidatePhoto::url($documentFile);
            $slides[] = ['thumb' => $documentUrl, 'html' => '<img src="' . $documentUrl . '" alt="' . $documentAlt . '" ' . $imgFallback . '>'];
        }
    }
    // Videos last, in CRM order: Video Upload, Introduction Video, Trade
    // Test Video (App\Support\CandidateVideos - only the ones that exist).
    foreach (\App\Support\CandidateVideos::slides($post) as $video) {
        $title = e($video['label']);
        $src = e($video['src']);
        $html = match ($video['kind']) {
            // Inner link = fallback for browsers that can't play the file.
            'file' => '<video controls preload="metadata" playsinline src="' . $src . '" title="' . $title . '">'
                . '<a href="' . $src . '" target="_blank" rel="noopener">' . e(__('locale.Open Video')) . '</a></video>',
            'embed' => '<iframe src="' . $src . '" title="' . $title . '" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>',
            default => '<div class="w-gallery-video-link"><span>' . $title . '</span>'
                . '<a href="' . $src . '" target="_blank" rel="noopener noreferrer" class="w-btn w-btn-primary w-btn-sm">' . e(__('locale.Open Video')) . '</a></div>',
        };
        $slides[] = ['thumb' => $video['thumb'], 'html' => $html, 'video' => $video['label']];
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
                <button type="button" data-gallery-thumb data-slide-html="{{ $slide['html'] }}"
                    class="{{ $i === 0 ? 'is-active' : '' }}{{ !empty($slide['video']) ? ' is-video' : '' }}{{ !empty($slide['restricted']) ? ' is-restricted' : '' }}"
                    @if (!empty($slide['video'])) title="{{ $slide['video'] }}" aria-label="{{ $slide['video'] }}" @elseif (!empty($slide['restricted'])) title="{{ $slide['restricted'] }}" aria-label="{{ $slide['restricted'] }}" @endif>
                    @if ($slide['thumb'])
                        <img src="{{ $slide['thumb'] }}" alt="{{ $slide['video'] ?? 'Thumbnail ' . ($i + 1) }}" onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';">
                    @endif
                    @if (!empty($slide['video']))
                        {{-- Play badge: marks the thumbnail as a video. --}}
                        <span class="w-gallery-thumb-play" aria-hidden="true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                        </span>
                    @elseif (!empty($slide['restricted']))
                        {{-- Red lock: a blurred (restricted) passport / driving licence. --}}
                        <span class="w-gallery-thumb-lock" aria-hidden="true">{!! $lockIcon !!}</span>
                    @endif
                </button>
            @endforeach
        </div>
    @endif
</div>
