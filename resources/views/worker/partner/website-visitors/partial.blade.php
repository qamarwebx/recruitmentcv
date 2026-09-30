{{-- Website Visitor results (initial render + AJAX filter/search/pagination).
Table on desktop, cards on small screens - same CSS as the Payment page. --}}
@php
    $deviceLine = fn ($visit) => trim(($visit->platform ?? '') . ' · ' . ($visit->device ? __('locale.' . ucfirst($visit->device)) : ''), ' ·');
@endphp

<div class="wp-listing-toolbar">
    <p class="wp-result-count">
        <strong>{{ $visits->total() }}</strong> {{ $visits->total() == 1 ? __('locale.visit') : __('locale.visits') }}
        &middot; <strong>{{ $uniqueVisitors }}</strong> {{ __('locale.unique visitors') }}
    </p>
</div>

@if ($visits->count() > 0)
    <div class="wp-table-wrap wp-fade-in wp-payment-table">
        <div class="wp-table-scroll">
            <table class="wp-table">
                <thead>
                    <tr>
                        <th>{{ __('locale.Date / Time') }}</th>
                        <th>{{ __('locale.Page') }}</th>
                        <th>{{ __('locale.Customer') }}</th>
                        <th>{{ __('locale.IP Address') }}</th>
                        <th>{{ __('locale.Device') }}</th>
                        <th>{{ __('locale.Referrer') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visits as $visit)
                        <tr>
                            <td style="white-space:nowrap;">
                                <span class="wp-cell-title">{{ $visit->visited_at->format('d M Y') }}</span>
                                <span class="wp-cell-sub">{{ $visit->visited_at->format('h:i:s A') }}</span>
                            </td>
                            <td class="wp-payment-employers" dir="ltr">{{ $visit->path }}</td>
                            <td>
                                @if ($visit->user)
                                    <span class="wp-cell-title">{{ $visit->user->name }}</span>
                                    <span class="wp-cell-sub">{{ $visit->user->email }}</span>
                                @else
                                    <span class="wp-cell-sub">{{ __('locale.Guest') }}</span>
                                @endif
                            </td>
                            <td dir="ltr">{{ $visit->ip_address }}</td>
                            <td>
                                <span class="wp-cell-title">{{ $visit->browser ?: '—' }}</span>
                                <span class="wp-cell-sub">{{ $deviceLine($visit) }}</span>
                            </td>
                            <td class="wp-payment-employers" dir="ltr">{{ $visit->referrer ?: __('locale.Direct') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="wp-order-grid wp-fade-in wp-payment-cards">
        @foreach ($visits as $visit)
            <div class="wp-order-card">
                <div class="wp-order-card-head">
                    <div>
                        <span class="wp-order-card-name" dir="ltr">{{ $visit->path }}</span>
                        <span class="wp-order-card-sub">{{ $visit->visited_at->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
                <div class="wp-order-card-meta">
                    <div>
                        <span>{{ __('locale.Customer') }}</span>
                        <strong>{{ $visit->user ? $visit->user->name : __('locale.Guest') }}</strong>
                    </div>
                    <div>
                        <span>{{ __('locale.Device') }}</span>
                        <strong>{{ trim(($visit->browser ?: '—') . ' · ' . $deviceLine($visit), ' ·') }}</strong>
                    </div>
                    <div>
                        <span>{{ __('locale.IP Address') }}</span>
                        <strong dir="ltr">{{ $visit->ip_address }}</strong>
                    </div>
                    <div class="wp-payment-employers">
                        <span>{{ __('locale.Referrer') }}</span>
                        <strong dir="ltr">{{ $visit->referrer ?: __('locale.Direct') }}</strong>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $visits->links('worker.partials.pagination') }}
@else
    <div class="wp-empty">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
        <h3>{{ __('locale.No visitors found') }}</h3>
        <p>{{ __('locale.Visits to your website will appear here.') }}</p>
    </div>
@endif
