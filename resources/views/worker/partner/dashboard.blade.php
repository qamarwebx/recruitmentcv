@extends('worker.partner.layouts.portal')

@section('title', 'Dashboard')
@section('page-title', __('locale.Dashboard'))

@section('content')
    @php
        $partner = Auth::guard('partner')->user();
        $displayName = $partner->owner_name ?: $partner->rec_off_name;
    @endphp

    <div class="wp-welcome wp-fade-in">
        <div>
            <h1>{{ __('locale.Welcome back') }}, {{ $displayName }} 👋</h1>
            <p>{{ __("locale.Here's what's happening with your candidates and employer records today.") }}</p>
        </div>
        <div class="wp-welcome-actions">
            <a href="{{ route('worker.partner.candidates') }}" class="w-btn w-btn-ghost-light">{{ __('locale.Browse Candidates') }}</a>
            <a href="{{ route('worker.resumes') }}" class="w-btn w-btn-accent">{{ __('locale.Find New Talent') }}</a>
        </div>
    </div>

    <div class="wp-stat-grid wp-fade-in">
        <div class="wp-stat-card">
            <span class="wp-stat-icon is-primary">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <div>
                <strong>{{ number_format($candidateCount) }}</strong>
                <span>{{ __('locale.Total Candidates') }}</span>
            </div>
        </div>
        <div class="wp-stat-card">
            <span class="wp-stat-icon is-success">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
            </span>
            <div>
                <strong>{{ number_format($availableCount) }}</strong>
                <span>{{ __('locale.Available Candidates') }}</span>
            </div>
        </div>
        <div class="wp-stat-card">
            <span class="wp-stat-icon is-info">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
            </span>
            <div>
                <strong>{{ number_format($employerPlusCount) }}</strong>
                <span>{{ __('locale.Employer Plus Records') }}</span>
            </div>
        </div>
        <div class="wp-stat-card">
            <span class="wp-stat-icon is-accent">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            </span>
            <div>
                <strong>{{ number_format($pendingBookingsCount) }}</strong>
                <span>{{ __('locale.Bookings Awaiting Confirmation') }}</span>
            </div>
        </div>
    </div>

    <div class="wp-grid-2">
        <div>
            <div class="wp-section-card wp-fade-in">
                <div class="wp-section-head">
                    <h2>{{ __('locale.Recent Candidates') }}</h2>
                    <a href="{{ route('worker.partner.candidates') }}">{{ __('locale.View all') }} →</a>
                </div>

                @if ($recentCandidates->count() > 0)
                    <div class="wp-mini-grid">
                        @foreach ($recentCandidates as $candidate)
                            <a href="{{ route('worker.partner.candidates.show', $candidate->slug_text) }}" class="wp-mini-card">
                                <img src="{{ \App\Support\CandidatePhoto::url($candidate->photo_file) }}" alt="{{ $candidate->display_name }}" onerror="this.onerror=null;this.src='{{ \App\Support\CandidatePhoto::defaultUrl() }}';">
                                <div>
                                    <strong>{{ $candidate->display_name }}</strong>
                                    <span>{{ optional($candidate->profession)->display_name ?? __('locale.Profession not set') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="wp-empty">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <h3>{{ __('locale.No candidates yet') }}</h3>
                        <p>{{ __('locale.Candidates linked to your account will appear here.') }}</p>
                    </div>
                @endif
            </div>

            <div class="wp-section-card wp-fade-in">
                <div class="wp-section-head">
                    <h2>{{ __('locale.Quick Actions') }}</h2>
                </div>
                <div class="wp-quick-actions">
                    <a href="{{ route('worker.partner.candidates') }}" class="wp-quick-action">
                        <span class="wp-stat-icon is-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                        </span>
                        <div>
                            <strong>{{ __('locale.Search Candidates') }}</strong>
                            <span>{{ __('locale.Filter by profession & status') }}</span>
                        </div>
                    </a>
                    <a href="{{ route('worker.partner.employer') }}" class="wp-quick-action">
                        <span class="wp-stat-icon is-info">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
                        </span>
                        <div>
                            <strong>{{ __('locale.Employer Plus') }}</strong>
                            <span>{{ __('locale.View employer records') }}</span>
                        </div>
                    </a>
                    <a href="{{ route('worker.partner.profile') }}" class="wp-quick-action">
                        <span class="wp-stat-icon is-accent">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
                        </span>
                        <div>
                            <strong>{{ __('locale.Update Profile') }}</strong>
                            <span>{{ __('locale.Keep your details current') }}</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div>
            <div class="wp-section-card wp-fade-in">
                <div class="wp-section-head">
                    <h2>{{ __('locale.Recent Activity') }}</h2>
                </div>

                @if ($recentActivity->count() > 0)
                    <div class="wp-activity-list">
                        @foreach ($recentActivity as $activity)
                            <div class="wp-activity-item">
                                <span class="wp-activity-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                </span>
                                <div>
                                    <strong>{{ (app()->getLocale() === 'ar' && !empty($activity->arcand_name)) ? $activity->arcand_name : ($activity->cand_name ?? __('locale.Candidate')) }}</strong>
                                    <span>Ref #{{ $activity->reference_no }} · {{ \Illuminate\Support\Carbon::parse($activity->booking_date)->format('d M Y') }}</span>
                                </div>
                                @if ($activity->booking_status)
                                    <span class="wp-badge is-success">{{ __('locale.Confirmed') }}</span>
                                @else
                                    <span class="wp-badge is-warning">{{ __('locale.Pending') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="wp-empty">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                        <h3>{{ __('locale.No activity yet') }}</h3>
                        <p>{{ __('locale.Booking activity will show up here.') }}</p>
                    </div>
                @endif
            </div>

            <div class="wp-section-card wp-fade-in">
                <div class="wp-section-head">
                    <h2>{{ __('locale.Employer Plus Summary') }}</h2>
                    <a href="{{ route('worker.partner.employer') }}">{{ __('locale.View all') }} →</a>
                </div>

                @if ($recentEmployerPlus->count() > 0)
                    <div class="wp-activity-list">
                        @foreach ($recentEmployerPlus as $employer)
                            <a href="{{ route('worker.partner.employer.show', $employer->id) }}" class="wp-activity-item" style="text-decoration:none;">
                                <span class="wp-activity-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
                                </span>
                                <div>
                                    <strong>{{ $employer->employer_name }}</strong>
                                    <span>{{ __('locale.Visa') }}: {{ $employer->visa_no ?: '---' }}</span>
                                </div>
                                @if ($employer->status)
                                    <span class="wp-badge is-success">{{ __('locale.Active') }}</span>
                                @else
                                    <span class="wp-badge is-neutral">{{ __('locale.Inactive') }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="wp-empty">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
                        <h3>{{ __('locale.No employer plus records') }}</h3>
                        <p>{{ __('locale.Records linked to your account will appear here.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
