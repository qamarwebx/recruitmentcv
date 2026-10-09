{{-- "Already Hired" notice in front of the Hire Now modal (hire-now.js):
shown only when PartnerHireController::status() reports the candidate is
currently hired by another customer / partner. Cancel closes it (Hire Now
never opens); Continue closes it and opens the existing Hire Now modal.
A warning only - the order submit still re-checks everything server-side.
Same .w-modal markup / styling as hire-now-modal.blade.php. --}}
<div class="w-modal-backdrop" data-already-hired-backdrop></div>
<div class="w-modal" data-already-hired-modal role="alertdialog" aria-modal="true" aria-labelledby="alreadyHiredTitle" aria-describedby="alreadyHiredText">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-already-hired-cancel aria-label="{{ __('locale.Close') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <div class="wp-already-hired-icon" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/></svg>
            </div>
            <h3 id="alreadyHiredTitle" class="w-modal-title wp-already-hired-title">{{ __('locale.Already Hired') }}</h3>

            <div id="alreadyHiredText" class="wp-already-hired-text">
                <p>{{ __('locale.This candidate is currently hired by another customer. However, you can still place a hire request for this candidate.') }}</p>
                <p>{{ __('locale.The candidate will become available to you once the previous customer releases the candidate or their hiring period expires (within 24–48 hours).') }}</p>
                <p>{{ __('locale.Would you like to proceed with hiring this candidate?') }}</p>
            </div>

            <div class="wp-form-actions wp-already-hired-actions">
                <button type="button" class="w-btn w-btn-outline" data-already-hired-cancel>{{ __('locale.Cancel') }}</button>
                <button type="button" class="w-btn w-btn-primary" data-already-hired-continue>{{ __('locale.Continue') }}</button>
            </div>
        </div>
    </div>
</div>
