@extends('worker.partner.layouts.portal')

@section('title', 'Employer')
@section('page-title', __('locale.Employer Plus'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.css') }}">
@endsection

@section('content')
    <div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
        <button type="button" class="w-btn w-btn-primary" data-add-employer-trigger>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-3px;margin-inline-end:6px;"><path d="M12 5v14M5 12h14"/></svg>
            {{ __('locale.Add Employer') }}
        </button>
    </div>

    {{-- Single form so Search, every Filter modal field, and pagination all
    combine via one query string (Search/Apply Filter both just submit
    this same form; Reset Filter clears the modal's own fields via
    employer-filter.js before resubmitting it).

    The Filter modal itself is deliberately rendered OUTSIDE this <form>
    (further down the page) and its fields associate back to it purely via
    the HTML5 form="employerFilterForm" attribute - .wp-filter-bar carries
    a leftover transform from its .wp-fade-in entrance animation
    (animation-fill-mode: both keeps it after the animation ends), which
    per spec makes it a stacking context root forever. A position:fixed
    modal nested inside a stacking context can never paint above anything
    outside that context regardless of its own z-index (see the
    .wp-filter-bar comment in portal.css for the same rule biting a
    dropdown), which is exactly why the Filter modal was being painted
    UNDER .wp-topbar's sticky header - moving it out of this form's DOM
    entirely, not just raising a z-index, is the actual fix. --}}
    <form method="GET" id="employerFilterForm" class="wp-filter-bar wp-fade-in" data-clean-url="{{ route('worker.partner.employer') }}">
        <div class="wp-filter-search">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="search" class="w-input" placeholder="{{ __('locale.Search by employer name or visa no.') }}" value="{{ request('search') }}">
        </div>
        <button type="submit" class="w-btn w-btn-primary w-btn-sm">{{ __('locale.Search') }}</button>
        <button type="button" class="w-btn w-btn-outline w-btn-sm" data-employer-filter-open>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-inline-end:5px;"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
            {{ __('locale.Filter') }}
            <span class="wp-filter-badge" data-employer-filter-badge @if($activeFilterCount === 0) hidden @endif>{{ $activeFilterCount }}</span>
        </button>
        @if (request()->filled('search'))
            <a href="{{ route('worker.partner.employer') }}" class="w-btn w-btn-outline w-btn-sm">{{ __('locale.Reset') }}</a>
        @endif
    </form>

    @include('worker.partner.employer-plus.filter-panel', ['professionOptions' => $professionOptions, 'workCities' => $workCities])

    <div class="wp-table-wrap wp-fade-in">
        <div class="wp-table-scroll">
            <table class="wp-table">
                <thead>
                    <tr>
                        <th>{{ __('locale.Employer Name') }}</th>
                        <th>{{ __('locale.Visa No.') }}</th>
                        <th>{{ __('locale.ID No') }}</th>
                        <th>{{ __('locale.Profession') }}</th>
                        <th>{{ __('locale.Status') }}</th>
                        <th>{{ __('locale.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employers as $employer)
                        @php
                            // Resolved from $professionsById (batch-fetched once in the
                            // controller for the whole page) instead of Profession::find()
                            // per row here - proff_id can itself be a comma-separated list
                            // of ids (e.g. "1,4").
                            $employerProfessionIds = collect(explode(',', (string) $employer->proff_id))->filter();
                            $employerProfessionNames = $employerProfessionIds
                                ->map(fn ($id) => optional($professionsById->get($id))->display_name)
                                ->filter()
                                ->implode(', ');
                        @endphp
                        <tr id="employer-row-{{ $employer->id }}">
                            <td>
                                <span class="wp-cell-title">{{ $employer->employer_name }}</span>
                            </td>
                            <td>{{ $employer->visa_no ?: '---' }}</td>
                            <td>{{ $employer->id_no ?: '---' }}</td>
                            <td>{{ $employerProfessionNames ?: '---' }}</td>
                            <td>
                                @if ($employer->status)
                                    <span class="wp-badge is-success">{{ __('locale.Active') }}</span>
                                @else
                                    <span class="wp-badge is-neutral">{{ __('locale.Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                {{-- Body-appended-clone dropdown (see employer-actions-menu.js)
                                - same pattern already proven on the Orders page's own 3-dot
                                Actions column, needed here for the same reason: .wp-table-wrap/
                                .wp-table-scroll's own overflow rules would otherwise clip it. --}}
                                <div class="wp-dropdown-wrap">
                                    <button type="button" class="wp-icon-btn" data-employer-actions-trigger data-employer-id="{{ $employer->id }}" aria-label="{{ __('locale.Actions') }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                                    </button>
                                    <div class="wp-dropdown" data-employer-actions-menu="{{ $employer->id }}">
                                        <a href="{{ route('worker.partner.employer.show', $employer->id) }}" class="wp-menu-item">{{ __('locale.View Profile') }}</a>
                                        <button type="button" class="wp-menu-item"
                                            data-edit-employer-trigger
                                            data-edit-url="{{ route('worker.partner.employer.update', $employer->id) }}"
                                            data-employer-name="{{ $employer->employer_name }}"
                                            data-employer-ar-name="{{ $employer->employer_ar_name }}"
                                            data-visa-no="{{ $employer->visa_no }}"
                                            data-id-no="{{ $employer->id_no }}"
                                            data-visa-date="{{ $employer->visa_date }}"
                                            data-proff-ids="{{ $employerProfessionIds->implode(',') }}"
                                            data-issuing-authority="{{ $employer->issuing_authority }}"
                                            data-wpcity-id="{{ $employer->wpcity_id }}"
                                            data-salary="{{ $employer->salary }}"
                                            data-openings="{{ $employer->openings }}"
                                            data-status="{{ (int) $employer->status }}"
                                            data-notes="{{ $employer->notes }}">
                                            {{ __('locale.Edit') }}
                                        </button>
                                        <button type="button" class="wp-menu-item is-danger"
                                            data-delete-employer-trigger
                                            data-delete-url="{{ route('worker.partner.employer.delete', $employer->id) }}"
                                            data-employer-row="employer-row-{{ $employer->id }}">
                                            {{ __('locale.Delete') }}
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="wp-empty" style="padding:32px 0;">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
                                    <h3>{{ __('locale.No Employer Plus records') }}</h3>
                                    <p>{{ __('locale.Records linked to your account will appear here.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($employers->count() > 0)
        {{ $employers->links('worker.partials.pagination') }}
    @endif

    @include('worker.partner.employer-plus.add-modal', ['professionOptions' => $professionOptions, 'workCities' => $workCities])
    @include('worker.partner.employer-plus.edit-modal', ['professionOptions' => $professionOptions, 'workCities' => $workCities])
    @include('worker.partner.employer-plus.delete-modal')
@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('worker/js/employer-widgets.js') }}?v={{ @filemtime(public_path('worker/js/employer-widgets.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/employer-actions-menu.js') }}?v={{ @filemtime(public_path('worker/js/employer-actions-menu.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/employer-filter.js') }}?v={{ @filemtime(public_path('worker/js/employer-filter.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/employer-add.js') }}?v={{ @filemtime(public_path('worker/js/employer-add.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/employer-edit.js') }}?v={{ @filemtime(public_path('worker/js/employer-edit.js')) ?: time() }}"></script>
    <script src="{{ asset('worker/js/employer-delete.js') }}?v={{ @filemtime(public_path('worker/js/employer-delete.js')) ?: time() }}"></script>
@endsection
