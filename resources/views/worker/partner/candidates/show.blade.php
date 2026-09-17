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
    @php
        $imagePath = \App\Support\CandidatePhoto::url($post->photo_file);
        $defaultAvatar = \App\Support\CandidatePhoto::defaultUrl();
    @endphp

    <a href="{{ route('worker.partner.candidates') }}" class="w-form-back" style="margin:0 0 18px;display:inline-flex;align-items:center;gap:6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        {{ __('locale.Back to Candidates') }}
    </a>

    <div class="w-details-grid wp-fade-in">
        <div class="w-gallery">
            <div class="w-gallery-main">
                <img src="{{ $imagePath }}" alt="{{ $post->display_name }}" onerror="this.onerror=null;this.src='{{ $defaultAvatar }}';">
            </div>

            <div class="w-profile-card">
                <div class="w-profile-name">
                    <h1>{{ $post->display_name }}</h1>
                    @if ($hasBooking)
                        <span class="w-verified">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 2 7l10 5 10-5-10-5Zm0 7L2 14l10 5 10-5-10-5Z"/></svg>
                            {{ __('locale.Linked to you') }}
                        </span>
                    @endif
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

                <div class="wp-badge {{ in_array($post->candidate_current_status, ['Deployed','Cancelled']) ? 'is-neutral' : 'is-success' }}" style="margin-top:14px;width:fit-content;">
                    {{ $post->candidate_current_status ? __('locale.' . $post->candidate_current_status) : __('locale.Available') }}
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
                    branch needed here - just the same cv_execute gate and the
                    same direct static-asset link, no download.cv controller
                    round trip (that one is hard-wired to a web-guard User). --}}
                    @if ($post->cv_execute == 1 && $post->cv_execute_file != '')
                        <a href="{{ asset('admin/assets/images/pdf/' . $post->cv_execute_file) }}" target="_blank" class="w-btn w-btn-outline">
                            {{ __('locale.Download CV') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="w-info-card wp-fade-in">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    {{ __('locale.Personal Details') }}
                </h2>
                <div class="w-info-grid">
                    <div class="w-info-item">
                        <span>{{ __('locale.Full Name') }}</span>
                        <strong>{{ $post->display_name }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Age') }}</span>
                        <strong>{{ $post->age ?: '---' }}</strong>
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
                        <span>{{ __('locale.Language') }}</span>
                        <strong>{{ $post->display_language ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Reference No.') }}</span>
                        <strong>{{ $post->reference_no ?: '---' }}</strong>
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
                </div>
            </div>

            <div class="w-info-card wp-fade-in">
                <h2>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.7A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .3 2 .7 3a2 2 0 0 1-.5 2.1L8 10a16 16 0 0 0 6 6l1.2-1.3a2 2 0 0 1 2.1-.5c1 .4 2 .6 3 .7a2 2 0 0 1 1.7 2z"/></svg>
                    {{ __('locale.Contact') }}
                </h2>
                <div class="w-info-grid">
                    <div class="w-info-item">
                        <span>{{ __('locale.Mobile Number') }}</span>
                        <strong>{{ $post->mobile_no ?: '---' }}</strong>
                    </div>
                    <div class="w-info-item">
                        <span>{{ __('locale.Email') }}</span>
                        <strong>{{ $post->email ?: '---' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @unless ($hasBooking)
        @include('worker.partials.hire-now-modal')
    @endunless
@endsection

@section('page-script')
    @unless ($hasBooking)
        <script src="{{ asset('admin/assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('admin/assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('worker/js/hire-now-vendor-init.js') }}?v={{ @filemtime(public_path('worker/js/hire-now-vendor-init.js')) ?: time() }}"></script>
        <script src="{{ asset('worker/js/hire-now.js') }}?v={{ @filemtime(public_path('worker/js/hire-now.js')) ?: time() }}"></script>
    @endunless
@endsection
