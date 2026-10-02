{{-- Website Visitor results (initial render + AJAX filter/search/pagination).
Table on desktop, cards on small screens - same CSS as the Payment page.
The visitor IP is kept in the data (and still searchable) but not shown. --}}
@php
    $deviceLine = fn ($visit) => trim(($visit->platform ?? '') . ' · ' . ($visit->device ? __('locale.' . ucfirst($visit->device)) : ''), ' ·');
@endphp

<div class="wp-listing-toolbar">
    <p class="wp-result-count">
        <strong>{{ $visits->total() }}</strong> {{ $visits->total() == 1 ? __('locale.visit') : __('locale.visits') }}
        &middot; <strong>{{ $uniqueVisitors }}</strong> {{ __('locale.unique visitors') }}
    </p>

    @if ($visits->count() > 0)
        {{-- Chart View (default) / Table View - switched in the browser
        (partner-visitor-charts.js); both show the same filtered visits. --}}
        <div class="wp-view-switch" role="tablist" aria-label="{{ __('locale.Visitor view') }}">
            <button type="button" class="wp-view-btn is-active" data-visitor-view-btn="chart" aria-pressed="true">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 5-6"/></svg>
                {{ __('locale.Chart View') }}
            </button>
            <button type="button" class="wp-view-btn" data-visitor-view-btn="table" aria-pressed="false">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                {{ __('locale.Table View') }}
            </button>
        </div>
    @endif
</div>

@if ($visits->count() > 0)
    {{-- Chart View: aggregates of ALL visits matching the current search /
    filters (PartnerWebsiteVisitorController::chartData), as JSON. --}}
    <div class="wp-visitor-charts wp-fade-in" data-visitor-view-panel="chart">
        <script type="application/json" data-visitor-chart-data>@json($chart)</script>
        <div class="w-info-card wp-chart-card wp-chart-card-wide">
            <div class="wp-chart-head">
                <h2>{{ __('locale.Visits over time') }}</h2>
                <span class="wp-chart-sub">{{ $chart['granularity'] === 'monthly' ? __('locale.Monthly') : __('locale.Daily') }}</span>
            </div>
            <div class="wp-chart" data-visitor-chart="trend" role="img" aria-label="{{ __('locale.Visits over time') }}"></div>
        </div>
        <div class="w-info-card wp-chart-card">
            <div class="wp-chart-head"><h2>{{ __('locale.Top pages') }}</h2></div>
            <div class="wp-chart" data-visitor-chart="pages" role="img" aria-label="{{ __('locale.Top pages') }}"></div>
        </div>
        <div class="w-info-card wp-chart-card">
            <div class="wp-chart-head"><h2>{{ __('locale.Devices') }}</h2></div>
            <div class="wp-chart" data-visitor-chart="devices" role="img" aria-label="{{ __('locale.Devices') }}"></div>
        </div>
        <div class="w-info-card wp-chart-card">
            <div class="wp-chart-head"><h2>{{ __('locale.Traffic sources') }}</h2></div>
            <div class="wp-chart" data-visitor-chart="sources" role="img" aria-label="{{ __('locale.Traffic sources') }}"></div>
        </div>
    </div>

    <div data-visitor-view-panel="table" hidden>
    <div class="wp-table-wrap wp-fade-in wp-payment-table">
        <div class="wp-table-scroll">
            <table class="wp-table">
                <thead>
                    <tr>
                        <th>{{ __('locale.Date / Time') }}</th>
                        <th>{{ __('locale.Page') }}</th>
                        <th>{{ __('locale.Customer') }}</th>
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
                    <div class="wp-payment-employers">
                        <span>{{ __('locale.Referrer') }}</span>
                        <strong dir="ltr">{{ $visit->referrer ?: __('locale.Direct') }}</strong>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $visits->links('worker.partials.pagination') }}
    </div>
@else
    <div class="wp-empty">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
        <h3>{{ __('locale.No visitors found') }}</h3>
        <p>{{ __('locale.Visits to your website will appear here.') }}</p>
    </div>
@endif
