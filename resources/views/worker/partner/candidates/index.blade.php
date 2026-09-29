@extends('worker.partner.layouts.portal')

@section('title', 'Candidates')
@section('page-title', __('locale.Candidates'))

@section('page-style')
    {{-- jQuery-dependent Select2, scoped to this page only (rest of the
    Worker Portal is vanilla JS) - same vendor asset + reskin already used
    by the Orders page's filter bar, reused as-is rather than duplicated. --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('worker/css/orders-vendor.css') }}?v={{ @filemtime(public_path('worker/css/orders-vendor.css')) ?: time() }}">
@endsection

@section('content')
    <form method="GET" class="wp-filter-bar wp-fade-in">
        <select name="search" class="w-select select2" data-placeholder="{{ __('locale.Search by name, reference or passport no.') }}" data-allow-clear="true" onchange="this.form.submit()">
            <option value=""></option>
            @php
                $__searchValue = request('search');
                $__searchMatched = false;
            @endphp
            @foreach ($candidateOptions as $candidateOption)
                @php $__searchMatched = $__searchMatched || $__searchValue === $candidateOption->cand_name; @endphp
                <option value="{{ $candidateOption->cand_name }}" @selected($__searchValue === $candidateOption->cand_name)>
                    {{ $candidateOption->display_name }} @if ($candidateOption->reference_no) — #{{ $candidateOption->reference_no }} @endif
                </option>
            @endforeach
            {{-- Preserves a search value that doesn't (or no longer) match any
            current option - e.g. a bookmarked/shared URL, or a candidate that's
            since been hired/unpublished - so the dropdown still reflects what's
            actually active instead of silently resetting to blank. --}}
            @if ($__searchValue && !$__searchMatched)
                <option value="{{ $__searchValue }}" selected>{{ $__searchValue }}</option>
            @endif
        </select>
        <select name="profession_id" class="w-select select2" data-placeholder="{{ __('locale.All Professions') }}" data-allow-clear="true" onchange="this.form.submit()">
            <option value="">{{ __('locale.All Professions') }}</option>
            @foreach ($professionOptions as $profession)
                <option value="{{ $profession->id }}" @selected(request('profession_id') == $profession->id)>{{ $profession->display_name }}</option>
            @endforeach
        </select>
        <select name="location_id" class="w-select select2" data-placeholder="{{ __('locale.All Work Locations') }}" data-allow-clear="true" onchange="this.form.submit()">
            <option value="">{{ __('locale.All Work Locations') }}</option>
            @foreach ($locationOptions as $location)
                <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>{{ $location->display_name }}</option>
            @endforeach
        </select>
        <select name="expcity_id" class="w-select select2" data-placeholder="{{ __('locale.Any Experience') }}" data-allow-clear="true" onchange="this.form.submit()">
            <option value="">{{ __('locale.Any Experience') }}</option>
            <option value="1" @selected(request('expcity_id') == 1)>{{ __('locale.Indian Experience') }}</option>
            <option value="2" @selected(request('expcity_id') == 2)>{{ __('locale.Gulf / Abroad Experience') }}</option>
        </select>
        <button type="submit" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Search') }}</button>
        @if (request()->anyFilled(['search', 'profession_id', 'location_id', 'expcity_id']))
            <a href="{{ route('worker.partner.candidates') }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.Reset') }}</a>
        @endif
    </form>

    <div class="wp-listing-toolbar">
        <p class="wp-result-count">
            <strong>{{ $candidates->total() }}</strong> {{ $candidates->total() == 1 ? __('locale.candidate available') : __('locale.candidates available') }}
        </p>
        <div class="wp-view-switch" data-wp-view-switch role="tablist" aria-label="Candidate view">
            <button type="button" class="wp-view-btn" data-wp-view-btn="card" aria-pressed="true">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                {{ __('locale.Card View') }}
            </button>
            <button type="button" class="wp-view-btn" data-wp-view-btn="table" aria-pressed="false">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                {{ __('locale.Table View') }}
            </button>
        </div>
    </div>

    @if ($candidates->count() > 0)
        {{-- Card view --}}
        <div class="w-candidate-grid w-cols-4 wp-fade-in" data-wp-view-panel="card">
            @foreach ($candidates as $candidate)
                @php
                    $partnerBooking = $partnerBookings->get($candidate->id);
                @endphp
                {{-- Same shared card as the public pages; partner links, Hired badge
                and View Order (when this partner already booked the candidate). --}}
                @include('worker.partials.candidate-card', [
                    'post' => $candidate,
                    'cardUrl' => route('worker.partner.candidates.show', $candidate->slug_text),
                    'cardHireUrl' => $partnerBooking ? route('worker.partner.orders.show', $partnerBooking->id) : null,
                    'cardHireLabel' => $partnerBooking ? __('locale.View Order') : null,
                    'cardHired' => (bool) $partnerBooking,
                ])
            @endforeach
        </div>

        {{-- Table view --}}
        <div class="wp-table-wrap wp-fade-in" data-wp-view-panel="table" hidden>
            <div class="wp-table-scroll">
                <table class="wp-table">
                    <thead>
                        <tr>
                            <th>{{ __('locale.Candidate') }}</th>
                            <th>{{ __('locale.Profession') }}</th>
                            <th>{{ __('locale.Experience') }}</th>
                            <th>{{ __('locale.Nationality') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidates as $candidate)
                            @php
                                $imagePath = \App\Support\CandidatePhoto::url($candidate->photo_file);
                                $totalExp = $candidate->experience ? array_sum(array_filter(explode(',', $candidate->experience), 'is_numeric')) : 0;
                                $nation = $candidate->nation_id ? \App\Models\Country::find($candidate->nation_id) : null;
                                $partnerBooking = $partnerBookings->get($candidate->id);
                            @endphp
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <img src="{{ $imagePath }}" alt="{{ $candidate->display_name }}" style="width:38px;height:38px;border-radius:9px;object-fit:cover;flex-shrink:0;" onerror="this.onerror=null;this.src='{{ \App\Support\CandidatePhoto::defaultUrl() }}';">
                                        <span class="wp-cell-title">{{ $candidate->display_name }}</span>
                                        @if ($partnerBooking)
                                            <span class="w-verified">{{ __('locale.Hired') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ optional($candidate->profession)->display_name ?? '---' }}</td>
                                <td>{{ $totalExp > 0 ? $totalExp . ' ' . __('locale.Years Experience') : __('locale.Fresher') }}</td>
                                <td>{{ $nation->display_name ?? '---' }}</td>
                                <td>
                                    <div style="display:flex;gap:8px;">
                                        <a href="{{ route('worker.partner.candidates.show', $candidate->slug_text) }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.View Profile') }}</a>
                                        @if ($partnerBooking)
                                            <a href="{{ route('worker.partner.orders.show', $partnerBooking->id) }}" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.View Order') }}</a>
                                        @else
                                            <a href="{{ route('worker.partner.candidates.show', $candidate->slug_text) }}" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Hire Now') }}</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{ $candidates->links('worker.partials.pagination') }}
    @else
        <div class="wp-empty">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <h3>{{ __('locale.No candidates found') }}</h3>
            <p>{{ __('locale.Try adjusting your search or filters.') }}</p>
        </div>
    @endif
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('worker/js/candidate-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/candidate-vendor-init.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/candidate-view-toggle.js') }}?v={{ @filemtime(public_path('worker/js/candidate-view-toggle.js')) ?: time() }}"></script>
@endsection
