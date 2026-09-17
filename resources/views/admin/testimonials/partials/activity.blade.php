{{-- Renders just the <li> timeline items for one page of activity — the
     <ul class="timeline"> wrapper, empty state, and Load More button live
     as static markup in the View Testimonial modal and are controlled by
     the AJAX response (has_more / total) instead. --}}
@foreach($timeline as $entry)
    <li class="timeline-item timeline-item-{{ $entry['color'] }} pb-4 border-left-dashed">

        <span class="timeline-indicator timeline-indicator-{{ $entry['color'] }}">
            <i class="ti {{ $entry['icon'] }}"></i>
        </span>

        <div class="timeline-event">

            <div class="timeline-header border-bottom mb-3">
                <h6 class="mb-0">{{ $entry['title'] }}</h6>
                <span class="text-muted">{{ $entry['created_at']->format('d M Y h:i A') }}</span>
            </div>

            @foreach($entry['details'] as $detail)
                <p class="mb-2">
                    <strong>{{ $detail['label'] }}:</strong> {{ $detail['value'] }}
                </p>
            @endforeach

            <div class="d-flex align-items-center mt-3">
                <div class="avatar me-3">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        {{ strtoupper(substr($entry['admin_name'] ?? 'S', 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h6 class="mb-0">{{ $entry['admin_name'] ?? 'System' }}</h6>
                    <small class="text-muted">{{ $entry['created_at']->diffForHumans() }}</small>
                </div>
            </div>

        </div>
    </li>
@endforeach
