<div class="w-listing-toolbar">
    <p class="w-result-count"><strong>{{ $posts->total() }}</strong> {{ $posts->total() == 1 ? __('locale.candidate found') : __('locale.candidates found') }}</p>
    {{-- Available / Already Hired with their counts (the complete filtered result
    set - WorkerPageController::resumes()), same switch as the Partner candidates
    list. Switching keeps the filters and starts again at page 1 (main.js). --}}
    <div class="w-hiring-switch" role="tablist" aria-label="{{ __('locale.Candidate availability') }}">
        @foreach (['available' => __('locale.Available Candidates'), 'hired' => __('locale.Already Hired Candidates')] as $hiringKey => $hiringLabel)
            <a href="{{ request()->fullUrlWithQuery(['hiring' => $hiringKey === 'hired' ? 'hired' : null, 'page' => null]) }}"
               class="w-hiring-btn {{ $hiring === $hiringKey ? 'is-active' : '' }}" role="tab"
               aria-selected="{{ $hiring === $hiringKey ? 'true' : 'false' }}" @if ($hiring === $hiringKey) aria-current="page" @endif
               data-hiring-filter="{{ $hiringKey }}">{{ $hiringLabel }} ({{ $hiringCounts[$hiringKey] }})</a>
        @endforeach
    </div>
</div>

@if ($posts->count() > 0)
    {{-- Hiring-status pills on the photos: one lookup for this page of results (also on AJAX filter / page loads). --}}
    @php $hiringStatuses = \App\Support\PartnerCandidateAccess::hiringStatusesForViewer($posts); @endphp
    <div class="w-candidate-grid">
        @foreach ($posts as $post)
            @include('worker.partials.candidate-card', ['post' => $post, 'hiringStatuses' => $hiringStatuses])
        @endforeach
    </div>

    {{ $posts->links('worker.partials.pagination') }}
@else
    <div class="w-empty-state">
        <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        <h3>{{ $hiring === 'hired' ? __('locale.No already hired candidates found.') : __('locale.No matching candidates') }}</h3>
        <p>{{ __('locale.Try adjusting or clearing your filters to see more results.') }}</p>
    </div>
@endif
