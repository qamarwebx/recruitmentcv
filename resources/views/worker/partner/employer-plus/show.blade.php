@extends('worker.partner.layouts.portal')

@section('title', $employer->employer_name)
@section('page-title', __('locale.Employer Plus'))

@section('content')
    <a href="{{ route('worker.partner.employer') }}" class="w-form-back" style="margin:0 0 18px;display:inline-flex;align-items:center;gap:6px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        {{ __('locale.Back to Employer Plus') }}
    </a>

    <div class="wp-profile-header wp-fade-in">
        <span class="wp-partner-avatar" style="background:linear-gradient(135deg, var(--w-primary-600), #7c3aed);">
            {{ mb_substr($employer->employer_name, 0, 2) }}
        </span>
        <div>
            <h1>{{ $employer->employer_name }}</h1>
            <p>
                @if ($employer->status)
                    <span class="wp-badge is-success">{{ __('locale.Active') }}</span>
                @else
                    <span class="wp-badge is-neutral">{{ __('locale.Inactive') }}</span>
                @endif
            </p>
        </div>
    </div>

    <div class="w-info-card wp-fade-in">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
            {{ __('locale.Visa & Employer Details') }}
        </h2>
        <div class="w-info-grid">
            <div class="w-info-item">
                <span>{{ __('locale.Visa No.') }}</span>
                <strong>{{ $employer->visa_no ?: '---' }}</strong>
            </div>
            <div class="w-info-item">
                <span>{{ __('locale.ID No') }}</span>
                <strong>{{ $employer->id_no ?: '---' }}</strong>
            </div>
            <div class="w-info-item">
                <span>{{ __('locale.Issuing Authority') }}</span>
                <strong>{{ $employer->issuing_authority ?: '---' }}</strong>
            </div>
            <div class="w-info-item">
                <span>{{ __('locale.Mobile No') }}</span>
                <strong>{{ $employer->mobile_no ?: '---' }}</strong>
            </div>
            <div class="w-info-item">
                <span>{{ __('locale.Payment Status') }}</span>
                <strong>{{ $employer->payment_status ?: '---' }}</strong>
            </div>
        </div>
    </div>

    <div class="w-info-card wp-fade-in">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            {{ __('locale.Profession') }} / {{ __('locale.Openings') }} / {{ __('locale.Monthly Salary') }}
        </h2>
        @if ($visaRows->isNotEmpty())
            <div class="wp-table-wrap">
                <div class="wp-table-scroll">
                    <table class="wp-table">
                        <thead>
                            <tr>
                                <th>{{ __('locale.Profession') }}</th>
                                <th>{{ __('locale.Openings') }}</th>
                                <th>{{ __('locale.Monthly Salary') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($visaRows as $row)
                                <tr>
                                    <td>{{ $row['profession'] }}</td>
                                    <td>{{ $row['openings'] }}</td>
                                    <td>{{ $row['salary'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <p style="color:var(--w-ink-500);font-size:0.88rem;margin:0;">{{ __('locale.No profession details added.') }}</p>
        @endif
    </div>

    <div class="w-info-card wp-fade-in">
        <h2>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            {{ __('locale.Assigned Candidates') }}
        </h2>
        <div class="w-form-alert" data-deassign-page-alert hidden></div>

        <div class="wp-table-wrap" data-assigned-candidates-wrap @if ($assignedCandidates->isEmpty()) hidden @endif>
            <div class="wp-table-scroll">
                <table class="wp-table">
                    <thead>
                        <tr>
                            <th>{{ __('locale.Candidate') }}</th>
                            <th>{{ __('locale.Passport No') }}</th>
                            <th>{{ __('locale.Profession') }}</th>
                            <th>{{ __('locale.Status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody data-assigned-candidates-rows>
                        @foreach ($assignedCandidates as $assignment)
                            <tr data-assigned-row="{{ $assignment->id }}">
                                <td>{{ optional($assignment->candidate)->cand_name ?: '---' }}</td>
                                <td>{{ optional($assignment->candidate)->pass_no ?: '---' }}</td>
                                <td>{{ optional($assignment->proff)->display_name ?: '---' }}</td>
                                <td><span class="wp-badge is-success">{{ __('locale.Assigned') }}</span></td>
                                <td>
                                    <button type="button" class="w-btn w-btn-outline w-btn-sm"
                                        data-deassign-trigger
                                        data-employercandidate-id="{{ $assignment->id }}">
                                        {{ __('locale.Deassign') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="wp-empty" data-assigned-candidates-empty style="padding:24px 0;" @if ($assignedCandidates->isNotEmpty()) hidden @endif>
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
            <p>{{ __('locale.No candidates assigned to this employer yet.') }}</p>
        </div>
    </div>

    @if ($employer->notes)
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                {{ __('locale.Notes') }}
            </h2>
            <p style="color:var(--w-ink-700);font-size:0.92rem;">{{ $employer->notes }}</p>
        </div>
    @endif

    {{-- Deassign confirmation - same backdrop/modal/confirm structure as
    the Employer listing's delete-modal.blade.php. --}}
    <div class="w-modal-backdrop" data-deassign-backdrop></div>
    <div class="w-modal" data-deassign-modal role="dialog" aria-modal="true" aria-labelledby="deassignCandidateTitle">
        <div class="w-modal-dialog">
            <button type="button" class="w-modal-close" data-deassign-close aria-label="Close">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>

            <div class="w-modal-body">
                <h3 id="deassignCandidateTitle" class="w-modal-title">{{ __('locale.Deassign Candidate') }}</h3>
                <p class="w-modal-subtitle" data-deassign-subtitle>{{ __('locale.Are you sure you want to deassign this candidate from this employer?') }}</p>

                <div class="w-form-alert" data-deassign-alert hidden></div>

                <div style="display:flex;gap:10px;margin-top:18px;">
                    <button type="button" class="w-btn w-btn-outline w-btn-block" data-deassign-close>
                        {{ __('locale.No, Keep') }}
                    </button>
                    <button type="button" class="w-btn w-btn-danger w-btn-block" data-deassign-confirm
                        data-default-text="{{ __('locale.Yes, Deassign') }}"
                        data-loading-text="{{ __('locale.Deassigning…') }}">
                        {{ __('locale.Yes, Deassign') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        window.WorkerEmployerDeassignUrl = "{{ route('worker.partner.employer.deassign-candidate', $employer->id) }}";
    </script>
    <script src="{{ asset('worker/js/employer-deassign-candidate.js') }}?v={{ @filemtime(public_path('worker/js/employer-deassign-candidate.js')) ?: time() }}"></script>
@endsection
