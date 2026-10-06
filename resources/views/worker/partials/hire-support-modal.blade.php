{{--
    Partner Candidate Detail support modal with the RecruitmentCV support
    number - the recruitmentcv.com footer phone (SiteBrand::globalFooterPhone()),
    shown as "+<code> <number>" and dialled like the footer's link. Opened by
    partner-candidate-activity.js ([data-partner-hire-support] buttons, ?hire=1).

    $supportReason:
      'not_approved'  - CRM Registration Request not Approved yet
                        (Partner::isRegistrationApproved()): Hire Now and
                        Download CV wait for approval.
      'hire_disabled' - (default) the hiring form is switched off for everyone
                        (config partner.hire_now_enabled).
    $supportOpenOnLoad: open on page load (e.g. back from a refused CV download).
--}}
@php
    $supportReason = $supportReason ?? 'hire_disabled';
    $hireSupportRaw = \App\Support\SiteBrand::globalFooterPhone();
    $hireSupportDigits = preg_replace('/\D+/', '', (string) $hireSupportRaw);
    $hireSupportNumber = $hireSupportDigits !== '' ? \App\Support\PhoneNumber::international(null, $hireSupportDigits) : '';
    if ($hireSupportNumber !== '' && !str_starts_with($hireSupportNumber, '+')) {
        $hireSupportNumber = '+' . $hireSupportDigits;
    }
    [$supportTitle, $supportMessage] = $supportReason === 'not_approved'
        ? [__('locale.Account Not Approved'), __('locale.Your Account is not Approved yet, please contact our support team.')]
        : [__('locale.Hire Candidate'), __('locale.To hire this candidate, please contact our support team.')];
@endphp
<div class="w-modal-backdrop" data-hire-support-backdrop></div>
<div class="w-modal" data-hire-support-modal data-support-reason="{{ $supportReason }}" @if (!empty($supportOpenOnLoad)) data-open-on-load @endif role="dialog" aria-modal="true" aria-labelledby="hireSupportTitle">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-hire-support-close aria-label="{{ __('locale.Close') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <div class="w-modal-body">
            <h3 id="hireSupportTitle" class="w-modal-title">{{ $supportTitle }}</h3>
            <p class="w-modal-subtitle">{{ $supportMessage }}</p>
            @if ($hireSupportNumber !== '')
                <a href="tel:+{{ $hireSupportDigits }}" class="w-btn w-btn-outline w-btn-block w-support-contact" data-hire-support-contact>
                    <svg class="w-support-contact-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 14v-3a8 8 0 0 1 16 0v3"/><path d="M18 19c0 1.1-.9 2-2 2h-2"/><rect x="2.5" y="13" width="4" height="6" rx="1.5"/><rect x="17.5" y="13" width="4" height="6" rx="1.5"/></svg>
                    <span class="w-support-contact-text">
                        <span>{{ __('locale.Contact Support') }}</span>
                        <span class="w-support-contact-number" dir="ltr">{{ $hireSupportNumber }}</span>
                    </span>
                </a>
            @endif
        </div>
    </div>
</div>
