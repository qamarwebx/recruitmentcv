@extends('worker.layouts.app')

@section('title', 'Qamr Worker Portal — Hire Verified, Work-Ready Candidates')
@section('meta_description', 'Browse verified, work-ready candidate resumes and hire dependable talent fast through Qamr International\'s Worker Portal.')

@section('page-style')
    {{-- jQuery-dependent select2, same vendored files/config/reskin already
    used elsewhere in the portal. Loading it here is what lets
    partner-auth.js's own conditional Select2 upgrade for the shared
    Partner Login/Register modal's country-code field activate when that
    modal is opened from the homepage - the modal itself and its JS aren't
    touched, this only supplies the dependency they already check for. --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
@endsection

@section('content')

    <section class="w-hero">
        <div class="w-container w-hero-grid">
            <div>
                <span class="w-hero-eyebrow">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>
                    {{ $homeContent['hero']['eyebrow'] ?? 'Qamr Worker Portal' }}
                </span>
                <h1>{{ $homeContent['hero']['heading_prefix'] ?? __('locale.Hire') }} <span>{{ $homeContent['hero']['heading_highlight'] ?? __('locale.Verified, Work-Ready') }}</span> {{ $homeContent['hero']['heading_suffix'] ?? __('locale.Talent — Faster') }}</h1>
                <p class="w-lead">
                    {{ $homeContent['hero']['lead'] ?? __('locale.Browse professionally screened candidate resumes across trades, domestic and skilled roles. Every profile is reviewed for accuracy so you can shortlist with confidence.') }}
                </p>

                <div class="w-hero-actions">
                    <a href="{{ route('worker.resumes') }}" class="w-btn w-btn-accent">
                        {{ $homeContent['hero']['primary_cta_text'] ?? __('locale.Browse Resumes') }}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    @php
                        $waNumber = isset($webconfig) && !empty($webconfig->whatsapp_number)
                            ? preg_replace('/[^0-9]/', '', $webconfig->whatsapp_number)
                            : null;
                        $contactPhone = $frontwebsite->bottom_contact_us_phone ?? ($frontwebsite->contact_us_phone ?? null);
                    @endphp
                    @if ($waNumber)
                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" class="w-btn w-btn-ghost-light">{{ __('locale.Chat on WhatsApp') }}</a>
                    @elseif ($contactPhone)
                        <a href="tel:{{ $contactPhone }}" class="w-btn w-btn-ghost-light">{{ __('locale.Call') }} {{ $contactPhone }}</a>
                    @endif
                </div>
            </div>

            <div class="w-hero-card">
                <h3>{{ $homeContent['hero']['card_title'] ?? __('locale.Find talent in seconds') }}</h3>
                <p>{{ $homeContent['hero']['card_subtitle'] ?? __("locale.Jump straight to what you're hiring for.") }}</p>
                <div class="w-hero-card-list">
                    <a href="{{ route('worker.resumes') }}">
                        {{ __('locale.Search by profession') }}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="{{ route('worker.resumes') }}">
                        {{ __('locale.Search by work location') }}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="{{ route('worker.resumes') }}">
                        {{ __('locale.Search by experience level') }}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                    <a href="{{ route('worker.resumes') }}">
                        {{ __('locale.View all resumes') }}
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="w-container w-stats">
        <div class="w-stats-grid w-fade">
            <div class="w-stat-card">
                <span class="w-stat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <div>
                    <strong>{{ number_format($totalResumes) }}+</strong>
                    <span>{{ __('locale.Verified Candidates') }}</span>
                </div>
            </div>
            <div class="w-stat-card">
                <span class="w-stat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </span>
                <div>
                    <strong>{{ number_format($totalProfessions) }}+</strong>
                    <span>{{ __('locale.Job Categories') }}</span>
                </div>
            </div>
            <div class="w-stat-card">
                <span class="w-stat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s7-7.5 7-12a7 7 0 1 0-14 0c0 4.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
                </span>
                <div>
                    <strong>{{ number_format($totalCities) }}+</strong>
                    <span>{{ __('locale.Work Locations') }}</span>
                </div>
            </div>
            <div class="w-stat-card">
                <span class="w-stat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20Z"/></svg>
                </span>
                <div>
                    <strong>{{ number_format($totalCountries) }}+</strong>
                    <span>{{ __('locale.Countries of Experience') }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="w-section">
        <div class="w-container">
            <div class="w-section-head-row w-fade">
                <div class="w-section-head" style="margin-bottom:0;">
                    <span class="w-eyebrow">{{ __('locale.Featured Talent') }}</span>
                    <h2>{{ __('locale.Recently added candidates') }}</h2>
                    <p>{{ __('locale.A quick look at the latest work-ready profiles added to the portal.') }}</p>
                </div>
                <a href="{{ route('worker.resumes') }}" class="w-btn w-btn-outline">{{ __('locale.View All Resumes') }}</a>
            </div>

            @if ($featured->count() > 0)
                <div class="w-candidate-grid w-cols-4 w-fade">
                    @foreach ($featured as $post)
                        @include('worker.partials.candidate-card', ['post' => $post])
                    @endforeach
                </div>
            @else
                <div class="w-empty-state">
                    <h3>{{ __('locale.No candidates available right now') }}</h3>
                    <p>{{ __('locale.Please check back soon — new profiles are added regularly.') }}</p>
                </div>
            @endif
        </div>
    </section>

    <section class="w-section" style="padding-top:0;">
        <div class="w-container">
            <div class="w-section-head w-center w-fade">
                <span class="w-eyebrow" style="margin-inline:auto;">{{ $homeContent['process']['eyebrow'] ?? __('locale.Simple Process') }}</span>
                <h2>{{ $homeContent['process']['heading'] ?? __('locale.Hiring made straightforward') }}</h2>
                <p>{{ $homeContent['process']['subheading'] ?? __('locale.From search to shortlist in three simple steps.') }}</p>
            </div>

            <div class="w-steps w-fade">
                <div class="w-step-card">
                    <span class="w-step-num">1</span>
                    <h3>{{ $homeContent['process']['step1_heading'] ?? __('locale.Search & Filter') }}</h3>
                    <p>{{ $homeContent['process']['step1_text'] ?? __('locale.Narrow candidates by profession, experience type, work location and more.') }}</p>
                </div>
                <div class="w-step-card">
                    <span class="w-step-num">2</span>
                    <h3>{{ $homeContent['process']['step2_heading'] ?? __('locale.Review Full Profile') }}</h3>
                    <p>{{ $homeContent['process']['step2_text'] ?? __('locale.Check personal details, employment history, education and passport information.') }}</p>
                </div>
                <div class="w-step-card">
                    <span class="w-step-num">3</span>
                    <h3>{{ $homeContent['process']['step3_heading'] ?? __('locale.Reach Out To Hire') }}</h3>
                    <p>{{ $homeContent['process']['step3_text'] ?? __('locale.Contact our team directly by phone or WhatsApp to start the hiring process.') }}</p>
                </div>
            </div>
        </div>
    </section>

    @if ($jobTypes->count() > 0)
        <section class="w-section" style="padding-top:0;">
            <div class="w-container">
                <div class="w-section-head w-fade">
                    <span class="w-eyebrow">{{ $homeContent['categories']['eyebrow'] ?? __('locale.Popular Categories') }}</span>
                    <h2>{{ $homeContent['categories']['heading'] ?? __('locale.Browse by profession') }}</h2>
                </div>
                <div class="w-chip-row w-fade">
                    @foreach ($jobTypes as $jobType)
                        <a href="{{ route('worker.resumes', ['proff_id' => $jobType->id]) }}" class="w-chip">{{ $jobType->display_name }}</a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="w-container" style="padding-bottom:80px;">
        <div class="w-cta-banner w-fade">
            <div>
                <h2>{{ $homeContent['cta']['heading'] ?? __('locale.Ready to find your next hire?') }}</h2>
                <p>{{ $homeContent['cta']['text'] ?? __('locale.Explore the full list of verified, work-ready candidates on the portal today.') }}</p>
            </div>
            <a href="{{ route('worker.resumes') }}" class="w-btn w-btn-accent">{{ $homeContent['cta']['button_text'] ?? __('locale.Browse All Resumes') }}</a>
        </div>
    </section>

@endsection

@section('page-script')
    <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
@endsection
