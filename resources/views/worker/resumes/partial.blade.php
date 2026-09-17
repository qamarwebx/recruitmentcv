<div class="w-listing-toolbar">
    <p class="w-result-count"><strong>{{ $posts->total() }}</strong> {{ $posts->total() == 1 ? __('locale.candidate found') : __('locale.candidates found') }}</p>
</div>

@if ($posts->count() > 0)
    <div class="w-candidate-grid">
        @foreach ($posts as $post)
            @include('worker.partials.candidate-card', ['post' => $post])
        @endforeach
    </div>

    {{ $posts->links('worker.partials.pagination') }}
@else
    <div class="w-empty-state">
        <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        <h3>{{ __('locale.No matching candidates') }}</h3>
        <p>{{ __('locale.Try adjusting or clearing your filters to see more results.') }}</p>
    </div>
@endif
