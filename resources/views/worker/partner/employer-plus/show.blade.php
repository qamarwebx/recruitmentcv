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

    @if ($employer->notes)
        <div class="w-info-card wp-fade-in">
            <h2>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                {{ __('locale.Notes') }}
            </h2>
            <p style="color:var(--w-ink-700);font-size:0.92rem;">{{ $employer->notes }}</p>
        </div>
    @endif
@endsection
