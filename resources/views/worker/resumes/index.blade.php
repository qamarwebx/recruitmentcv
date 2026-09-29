@extends('worker.layouts.app')

@section('title', 'Browse Worker Resumes — Qamr Worker Portal')
@section('meta_description', 'Search and filter verified, work-ready candidate resumes by profession, experience and work location.')

@section('page-style')
    {{-- jQuery-dependent select2, same vendored files/config/reskin the
    Worker Partner Portal's Employer and Orders pages already use - scoped
    to this page only, not the shared public-site layout, which is
    otherwise vanilla JS. --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')

    <section class="w-page-header">
        <div class="w-container">
            <div class="w-breadcrumb">
                <a href="{{ route('worker.home') }}">{{ __('locale.Home') }}</a>
                <span>/</span>
                <span>{{ __('locale.Resumes') }}</span>
            </div>
            <h1>{{ __('locale.Browse Verified Worker Resumes') }}</h1>
            <p>{{ __('locale.Filter by profession, experience and work location to find the right candidate for your team.') }}</p>
        </div>
    </section>

    <section class="w-section" style="padding-top:40px;">
        <div class="w-container">

            <button type="button" class="w-btn w-btn-outline w-filter-toggle" data-filter-open style="margin-bottom:20px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 3H2l8 9.5V19l4 2v-8.5L22 3z"/></svg>
                {{ __('locale.Filters') }}
            </button>

            <div class="w-listing">
                <aside class="w-filter-panel" data-filter-panel>
                    <h3>
                        {{ __('locale.Filter Candidates') }}
                        <button type="button" class="w-nav-toggle w-filter-toggle" data-filter-close aria-label="Close filters">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                    </h3>

                    <form data-resume-filter-form data-endpoint="{{ route('worker.resumes') }}">

                        <div class="w-filter-group">
                            <label for="expcity_id">{{ __('locale.Experience Type') }}</label>
                            <select name="expcity_id" id="expcity_id" class="w-select select2">
                                <option value="">{{ __('locale.Any experience') }}</option>
                                <option value="1" @selected(request('expcity_id') == 1)>{{ __('locale.Indian Experience') }}</option>
                                <option value="2" @selected(request('expcity_id') == 2)>{{ __('locale.Gulf / Abroad Experience') }}</option>
                            </select>
                        </div>

                        <div class="w-filter-group">
                            <label for="proff_id">{{ __('locale.Profession') }}</label>
                            <select name="proff_id" id="proff_id" class="w-select select2">
                                <option value="">{{ __('locale.All professions') }}</option>
                                @foreach ($jobTypes as $jobType)
                                    <option value="{{ $jobType->id }}" @selected(request('proff_id') == $jobType->id)>{{ $jobType->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-filter-group">
                            <label for="location_id">{{ __('locale.Work Location') }}</label>
                            <select name="location_id" id="location_id" class="w-select select2">
                                <option value="">{{ __('locale.Any location') }}</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->id }}" @selected(request('location_id') == $city->id)>{{ $city->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-filter-group">
                            <label for="age">{{ __('locale.Age Range') }}</label>
                            <select name="age" id="age" class="w-select select2">
                                <option value="">{{ __('locale.Any age') }}</option>
                                <option value="22-25" @selected(request('age') == '22-25')>22 – 25</option>
                                <option value="26-30" @selected(request('age') == '26-30')>26 – 30</option>
                                <option value="31-35" @selected(request('age') == '31-35')>31 – 35</option>
                                <option value="36-40" @selected(request('age') == '36-40')>36 – 40</option>
                                <option value="41-45" @selected(request('age') == '41-45')>41 – 45</option>
                                <option value="46-50" @selected(request('age') == '46-50')>46 – 50</option>
                            </select>
                        </div>

                        <div class="w-filter-group">
                            <label for="religions">{{ __('locale.Religion') }}</label>
                            <select name="religions" id="religions" class="w-select select2">
                                <option value="">{{ __('locale.Any religion') }}</option>
                                @foreach ($religions as $religion)
                                    <option value="{{ $religion->id }}" @selected(request('religions') == $religion->id)>{{ $religion->display_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-filter-actions">
                            <button type="button" class="w-btn w-btn-primary w-btn-block" data-filter-search>{{ __('locale.Search') }}</button>
                            <button type="button" class="w-btn w-btn-outline w-btn-block" data-filter-reset>{{ __('locale.Reset') }}</button>
                        </div>
                    </form>
                </aside>

                <div>
                    <div class="w-loading" data-resume-loading>
                        <span class="w-spinner"></span> {{ __('locale.Loading candidates...') }}
                    </div>
                    <div id="worker-resumes-grid">
                        @include('worker.resumes.partial', ['posts' => $posts])
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="w-filter-backdrop" data-filter-backdrop></div>

@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('worker/js/resumes-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/resumes-vendor-init.js')) ?: time() }}"></script>
@endsection
