<div class="w-modal-backdrop" data-cancel-order-backdrop></div>
<div class="w-modal" data-cancel-order-modal data-cancelled-label="{{ __('locale.Cancelled') }}" role="dialog" aria-modal="true" aria-labelledby="cancelOrderTitle">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-cancel-order-close aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <h3 id="cancelOrderTitle" class="w-modal-title">{{ __('locale.Cancel Booking') }}</h3>
            <p class="w-modal-subtitle">{{ __('locale.Are you sure you want to cancel this order?') }} {{ __('locale.This action cannot be undone.') }}</p>

            <div class="w-form-alert" data-cancel-order-alert hidden></div>

            <div style="display:flex;gap:10px;margin-top:18px;">
                <button type="button" class="w-btn w-btn-outline w-btn-block" data-cancel-order-close>
                    {{ __('locale.No, Keep Order') }}
                </button>
                <button type="button" class="w-btn w-btn-danger w-btn-block" data-cancel-order-confirm
                    data-default-text="{{ __('locale.Yes, Cancel Order') }}"
                    data-loading-text="{{ __('locale.Cancelling…') }}">
                    {{ __('locale.Yes, Cancel Order') }}
                </button>
            </div>
        </div>
    </div>
</div>
