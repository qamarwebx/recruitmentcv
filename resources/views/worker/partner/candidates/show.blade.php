@extends('worker.partner.layouts.portal')

@section('title', $post->display_name)
@section('page-title', __('locale.Candidate'))

@section('page-style')
    {{-- jQuery-dependent select2, same vendored files/config/reskin already
    used elsewhere in the Partner Portal - scoped to this page only, and
    only actually needed when the Hire Now modal (below) is rendered at
    all, i.e. this candidate hasn't already been hired by this partner. --}}
    @unless ($hasBooking)
        <link rel="stylesheet" href="{{ asset('admin/assets/vendor/libs/select2/select2.css') }}">
    @endunless
@endsection

@section('content')
    <a href="{{ route('worker.partner.candidates') }}" class="w-form-back" style="margin:0 0 18px;display:inline-flex;align-items:center;gap:6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        {{ __('locale.Back to Candidates') }}
    </a>

    <div class="w-details-grid wp-fade-in">
        <div class="w-gallery">
            @include('worker.partials.candidate-gallery')

            <div class="w-profile-card">
                <div class="w-profile-name">
                    <h1>{{ $post->display_name }}@include('worker.partials.candidate-verified-icon')</h1>
                    @if ($hasBooking)
                        <span class="w-verified">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 14l10 5 10-5-10-5Z"/></svg>
                            {{ __('locale.Linked to you') }}
                        </span>
                    @endif
                </div>     
                
                 
               <div class="w-profile-tags">
                    {{-- Locale-aware: Profession::display_name (existing
                    accessor, already used elsewhere on this page) picks
                    ar_name over eng_name when app()->getLocale() is 'ar' -
                    the same locale detection already used throughout this
                    project, not a new one. "Indian Experience"/"Ex-Abroad"
                    go through the existing __('locale.*') translation
                    system the same way. --}}
                    <span class="w-tag">
                        {{ $post->display_profession_label }}
                    </span>
               </div>

                <div class="w-profile-tags">
                    @if (optional($post->profession)->display_name)
                        <span class="w-tag">{{ $post->profession->display_name }}</span>
                    @endif
                    <span class="w-tag is-neutral">{{ $totalExperience > 0 ? $totalExperience . ' ' . __('locale.Years Experience') : __('locale.Fresher') }}</span>
                    @if ($post->age)
                        <span class="w-tag is-neutral">{{ $post->age }} {{ __('locale.yrs old') }}</span>
                    @endif
                </div>

            
                <div class="w-cta-row" style="margin-top:14px;">
                    @if ($hasBooking)
                        <a href="{{ route('worker.partner.orders.show', $existingBooking->id) }}" class="w-btn w-btn-accent">
                            {{ __('locale.View Order') }}
                        </a>
                    @else
                        <button type="button" class="w-btn w-btn-accent" data-hire-now-trigger
                            data-hire-url="{{ route('worker.partner.candidates.hire', $post->slug_text) }}"
                            data-mobile-verified="{{ Auth::guard('partner')->user()->hasVerifiedMobile() ? '1' : '0' }}">
                            {{ __('locale.Hire Now') }}
                        </button>
                    @endif

                    {{-- Reachable only once already authenticated as a partner
                    (this page sits behind worker.partner.auth), so unlike the
                    public resumes/details page there's no guest/login-modal
                    branch needed here. Same cv_execute gate; the link serves
                    this partner's own B2B CV (PartnerPortalController::candidateCv). --}}
                    @if ($post->cv_execute == 1 && $post->cv_execute_file != '')
                        <a href="{{ route('worker.partner.candidates.cv', $post->slug_text) }}" target="_blank" class="w-btn w-btn-outline">
                            {{ __('locale.Download CV') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="w-info-card wp-fade-in">
                {{-- Reference No. on the right of the header, in the same style as
                the reference on the Orders cards (.wp-order-card-ref). --}}
                <h2 class="wp-card-head-with-ref">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    {{ __('locale.Personal Details') }}
                    @if ($post->reference_no)
                        <span class="wp-order-card-ref" title="{{ __('locale.Reference No.') }}">{{ $post->reference_no }}</span>
                    @endif
                </h2>
                <div class="w-info-grid">
                    <div class="w-info-item">
                        <span>{{ __('locale.Applied For') }}</span>
                        <strong>{{ optional($post->profession)->display_name ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Full Name') }}</span>
                        <strong>{{ $post->display_name }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Age') }}</span>
                        <strong>{{ $post->age ?: '---' }} {{ __('locale.Years') }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Religion') }}</span>
                        <strong>{{ optional($religion)->display_name ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Marital Status') }}</span>
                        <strong>{{ $post->display_marital_status ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Nationality') }}</span>
                        <strong>{{ optional($nation)->display_name ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Region') }}</span>
                        <strong>{{ optional($region)->display_name ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Language') }}</span>
                        <strong>{{ $post->display_language ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Preferred Work Location') }}</span>
                        <strong>{{ $expectedWorkPlaces->count() ? $expectedWorkPlaces->pluck('display_name')->implode(', ') : __('locale.Anywhere') }}</strong>
                    </div>
                </div>
            </div>

            {{-- Same source/logic as worker.resumes.details (public page) -
            per-row Country/Profession/City lookups happen right here in
            the loop there too, not pre-resolved into arrays server-side,
            so this mirrors it exactly rather than introducing a second
            way of shaping the same data. --}}
            <div class="w-info-card wp-fade-in">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    {{ __('locale.Employment Experience') }}
                </h2>

                @if ($post->experience && $post->experience != '0')
                    @php
                        $exps = explode(',', $post->experience);
                        $exprof = explode(',', (string) $post->proff_id);
                        $expcont = explode(',', (string) $post->expcountry_id);
                        $expcity = explode(',', (string) $post->expcity_id);
                    @endphp
                    @foreach ($exps as $key => $exp)
                        @php
                            $expCountry = \App\Models\Country::find($expcont[$key] ?? null);
                            $expProf = \App\Models\Profession::find($exprof[$key] ?? null);
                            $expCity = \App\Models\City::find($expcity[$key] ?? null);
                        @endphp
                        <div class="w-exp-row">
                            <div class="w-info-item">
                                <span>{{ __('locale.Job') }}</span>
                                <strong>{{ optional($expProf)->display_name ?: '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Period') }}</span>
                                <strong>{{ $exp }} {{ __('locale.Years Experience') }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.Country') }}</span>
                                <strong>{{ optional($expCountry)->display_name ?: '---' }}</strong>
                            </div>
                            <div class="w-info-item">
                                <span>{{ __('locale.City') }}</span>
                                <strong>{{ optional($expCity)->display_name ?: '---' }}</strong>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="color:var(--w-ink-500);">{{ __('locale.This candidate is a fresher with no prior employment record on file.') }}</p>
                @endif
            </div>

            <div class="w-info-card wp-fade-in">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5-10-5Zm4 2v5c0 1 3 3 6 3s6-2 6-3v-5"/></svg>
                    {{ __('locale.Education & Skills') }}
                </h2>
                <div class="w-info-grid w-cols-4">
                    <div class="w-info-item">
                        <span>{{ __('locale.Education') }}</span>
                        <strong>{{ optional($education)->name ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Vehicle Known') }}</span>
                        <strong>{{ $vehiclesKnown->count() ? $vehiclesKnown->pluck('name')->implode(', ') : '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Vehicle Transmission') }}</span>
                        <strong>{{ $vehicleTransmissions->count() ? $vehicleTransmissions->map(fn($t) => (app()->getLocale() === 'ar' && !empty($t->ar_name)) ? $t->ar_name : $t->eng_name)->implode(', ') : '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Google Map Location') }}</span>
                        <strong>{{ $post->google_map == 1 ? __('locale.Available') : __('locale.Not Available') }}</strong>
                    </div>
                </div>
            </div>

            <div class="w-info-card wp-fade-in">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                    {{ __('locale.Passport Details') }}
                </h2>
                <div class="w-info-grid">
                    <div class="w-info-item">
                        <span>{{ __('locale.Passport Number') }}</span>
                        <strong>{{ $post->pass_no ? substr_replace($post->pass_no, '**', -2) : '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Passport Type') }}</span>
                        <strong>{{ $post->pass_type ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Date of Issue') }}</span>
                        <strong>{{ $post->doi ? \Illuminate\Support\Carbon::parse($post->doi)->format('d/m/Y') : '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Date of Expiry') }}</span>
                        <strong>{{ $post->doe ? \Illuminate\Support\Carbon::parse($post->doe)->format('d/m/Y') : '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Place of Issue') }}</span>
                        <strong>{{ optional($placeOfIssue)->display_name ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Date of Birth') }}</span>
                        <strong>{{ $post->dob ? \Illuminate\Support\Carbon::parse($post->dob)->format('d/m/Y') : '---' }}</strong>
                    </div>
                </div>
            </div>

            <div class="w-info-card wp-fade-in">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    {{ __('locale.Price & Departure') }}
                </h2>
                <div class="w-summary-grid">
                    <div class="w-summary-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M9 9.5a2.5 2.5 0 0 1 2.5-2.5h1a2 2 0 1 1 0 4h-1a2 2 0 1 0 0 4h1a2.5 2.5 0 0 0 2.5-2.5"/></svg>
                        <strong>{{ $priceLabel }}</strong>
                        <span>{{ __('locale.Service Price') }}</span>
                    </div>
                    <div class="w-summary-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 15-9-9-9 9M12 6v15"/></svg>
                        <strong>{{ $departureLabel }}</strong>
                        <span>{{ __('locale.Departure from India') }}</span>
                    </div>
                    <div class="w-summary-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4Z"/></svg>
                        <strong>{{ __('locale.90 Days') }}</strong>
                        <span>{{ __('locale.Warranty') }}</span>
                    </div>
                    <div class="w-summary-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="12" cy="10" r="3"/></svg>
                        <strong>{{ __('locale.In Office') }}</strong>
                        <span>{{ __('locale.Passport') }}</span>
                    </div>
                    <div class="w-summary-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-8-4.5-8-11a8 8 0 0 1 16 0c0 6.5-8 11-8 11Z"/></svg>
                        <strong>{{ __('locale.Medically Fit') }}</strong>
                        <span>{{ __('locale.Medical Status') }}</span>
                    </div>
                </div>
            </div>

            @if ($bookingRequirements->count() > 0)
                <div class="w-info-card wp-fade-in">
                    <h2>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                        {{ __('locale.Booking Requirements') }}
                    </h2>
                    <ul class="w-requirement-list">
                        @foreach ($bookingRequirements as $requirement)
                            <li>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                                {{ $requirement->requirement_text }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    @unless ($hasBooking)
        @include('worker.partials.hire-now-modal')
    @endunless
@endsection

@section('page-script')
    {{-- Same worker/js/main.js the public resumes/details page loads - its
    initGallery() is what drives the shared candidate-gallery partial above
    (thumbnail/arrow clicks), reused as-is rather than a second copy of that
    logic. Its other init*() calls all no-op safely here (each guards on a
    selector - [data-nav-toggle], [data-filter-panel], etc. - that doesn't
    exist on this Partner Portal page), so loading the whole file scoped to
    just this page is safe. --}}
    <script src="{{ asset('worker/js/main.js') }}?v={{ @filemtime(public_path('worker/js/main.js')) ?: time() }}"></script>

    @unless ($hasBooking)
        <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('worker/js/hire-now-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/hire-now-vendor-init.js')) ?: time() }}"></script>
        <script src="{{ asset('worker/js/hire-now.js') }}?v={{ @filemtime(public_path('worker/js/hire-now.js')) ?: time() }}"></script>
    @endunless
@endsection
