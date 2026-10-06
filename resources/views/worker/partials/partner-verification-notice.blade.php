{{--
    Candidate detail pages (Partner Portal candidates.show and the public
    resumes/details page): a signed-in partner who is not fully verified
    (Partner::isFullyVerified() - mobile AND email) sees why Hire Now,
    Download CV and the passport image are unavailable, and where to verify.
    hire-now.js / the Download CV buttons point here (data-partner-verify-notice).
    The rule itself is enforced server-side (PartnerHireController::store(),
    PartnerPortalController::candidateCv()).
--}}
@php $noticePartner = \Illuminate\Support\Facades\Auth::guard('partner')->user(); @endphp
@if ($noticePartner && !$noticePartner->isFullyVerified())
    <div class="w-form-alert is-error w-verify-notice" role="alert" data-partner-verify-notice tabindex="-1">
        <strong>{{ __('locale.Verification required') }}</strong>
        <span>{{ __('locale.Verify your mobile number and email address to hire candidates, download CVs and view passport images.') }}</span>
        <ul class="w-verify-notice-list">
            <li>{{ $noticePartner->hasVerifiedMobile() ? '✓' : '✕' }} {{ __('locale.Primary Mobile') }}: {{ $noticePartner->hasVerifiedMobile() ? __('locale.Verified') : __('locale.Not Verified') }}</li>
            <li>{{ $noticePartner->hasVerifiedEmail() ? '✓' : '✕' }} {{ __('locale.Primary Email') }}: {{ $noticePartner->hasVerifiedEmail() ? __('locale.Verified') : __('locale.Not Verified') }}</li>
        </ul>
        <a href="{{ route('worker.partner.account') }}" class="w-verify-notice-link">{{ __('locale.Go to My Account') }}</a>
    </div>
    <script>
        // Blocked actions (data-partner-verify-required, and hire-now.js on an
        // unverified email) bring this notice into view instead of acting.
        (function () {
            if (window.WorkerPartnerVerifyNotice) return;
            window.WorkerPartnerVerifyNotice = {
                show: function () {
                    var notice = document.querySelector('[data-partner-verify-notice]');
                    if (!notice) return false;
                    notice.classList.remove('is-highlighted');
                    void notice.offsetWidth;   // restart the highlight animation
                    notice.classList.add('is-highlighted');
                    notice.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return true;
                }
            };
            document.addEventListener('click', function (e) {
                if (e.target.closest('[data-partner-verify-required]')) {
                    e.preventDefault();
                    window.WorkerPartnerVerifyNotice.show();
                }
            });
        })();
    </script>
@endif
