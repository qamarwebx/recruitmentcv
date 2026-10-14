{{-- Update Payment (order-payment.js): records that the order's payment was
received, optionally with its amount in SAR. Saved to the booking's one
payment record - the same one the CRM's Add Payment drawer edits - via
PartnerPortalController::orderPaymentStore. Rendered outside
#worker-orders-grid (its innerHTML is replaced on every AJAX refresh); only
the per-order trigger buttons live in the swapped partial. --}}
<div class="w-modal-backdrop" data-order-payment-backdrop></div>
<div class="w-modal" data-order-payment-modal
    data-msg-received="{{ __('locale.Please select Payment Received.') }}"
    data-msg-amount="{{ __('locale.Enter a valid amount: 0 or more, up to 2 decimals.') }}"
    data-msg-failed="{{ __('locale.Something went wrong. Please try again.') }}"
    data-msg-network="{{ __('locale.A technical error occurred. Please try again shortly.') }}"
    role="dialog" aria-modal="true" aria-labelledby="orderPaymentTitle">
    <div class="w-modal-dialog">
        <button type="button" class="w-modal-close" data-order-payment-close aria-label="Close">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        <div class="w-modal-body">
            <h3 id="orderPaymentTitle" class="w-modal-title">{{ __('locale.Update Payment') }}</h3>
            <p class="w-modal-subtitle">{{ __('locale.Order') }} #<span data-order-payment-ref></span></p>

            <div class="w-form-alert" data-order-payment-alert hidden></div>

            <form data-order-payment-form novalidate>
                <div class="w-form-row">
                    <label class="w-form-label" for="order-payment-received">{{ __('locale.Payment Received') }} <span style="color:#dc2626;">*</span></label>
                    <select name="payment_received" id="order-payment-received" class="w-select" required>
                        <option value="">{{ __('locale.Select Payment Received') }}</option>
                        <option value="{{ \App\Support\PaymentAmount::RECEIVED_YES }}">{{ __('locale.Yes') }}</option>
                    </select>
                    <div class="w-form-error" data-order-payment-error="payment_received" hidden></div>
                </div>
                <div class="w-form-row">
                    <label class="w-form-label" for="order-payment-amount">{{ __('locale.Amount (SAR)') }} <span class="w-form-optional-tag">({{ __('locale.Optional') }})</span></label>
                    <input type="number" name="amount" id="order-payment-amount" class="w-input" min="0" step="0.01" inputmode="decimal" dir="ltr" placeholder="{{ __('locale.Enter Amount...') }}">
                    <div class="w-form-hint" data-order-payment-current hidden>{{ __('locale.Current amount') }}: <span dir="ltr" data-order-payment-current-value></span> · {{ __('locale.Leave blank to keep the saved amount') }}</div>
                    <div class="w-form-error" data-order-payment-error="amount" hidden></div>
                </div>

                <div style="display:flex;gap:10px;margin-top:18px;">
                    <button type="button" class="w-btn w-btn-outline w-btn-block" data-order-payment-close>{{ __('locale.Cancel') }}</button>
                    <button type="submit" class="w-btn w-btn-primary w-btn-block" data-order-payment-submit
                        data-default-text="{{ __('locale.Save') }}"
                        data-loading-text="{{ __('locale.Saving…') }}">{{ __('locale.Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
