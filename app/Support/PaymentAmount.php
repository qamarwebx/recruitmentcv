<?php

namespace App\Support;

/**
 * A booking's payment as recorded from the CRM's Add Payment drawer
 * (BookingController::paymentStr) and the Partner Portal's Update Payment
 * (PartnerPortalController::orderPaymentStore) - twin file, identical in the
 * CRM and RecruitmentCV. Both write the booking's ONE payment record
 * (bookingpayments, the first row - updated in place, never a second one).
 *
 * The form: "Payment Received" (required, only "Yes") and "Amount (SAR)"
 * (optional; digits with up to 2 decimals - no sign, exponent or separators).
 * Payment status and amount are separate: a received payment may have no
 * amount. A blank amount is NOT zero and never clears a saved amount - it
 * leaves it as it is (a first payment record then simply has none).
 * Amounts are Saudi Riyal, stored as typed, never converted or summed.
 */
final class PaymentAmount
{
    public const CURRENCY = 'SAR';

    /** The only "Payment Received" choice. */
    public const RECEIVED_YES = 'yes';

    /** Server-side rules of the payment form (both apps). */
    public static function rules(): array
    {
        return [
            'payment_received' => ['required', 'in:' . self::RECEIVED_YES],
            'amount' => ['nullable', 'regex:/^\d{1,10}(\.\d{1,2})?$/'],
        ];
    }

    /** The amount to save: trimmed, or null for a blank field (= keep the saved amount). */
    public static function input($value): ?string
    {
        if ($value === null || is_array($value)) {
            return null;
        }
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * "100 SAR", "2,000 SAR": a plain number gets thousands separators (decimals
     * as stored), an already-grouped "2,000" stays, a value already ending in
     * SAR is not suffixed twice. Null for no amount (callers show their "—").
     */
    public static function format($amount): ?string
    {
        if ($amount === null || is_array($amount)) {
            return null;
        }
        $amount = trim((string) $amount);
        if ($amount === '') {
            return null;
        }

        if (preg_match('/^(\d+)(\.\d+)?$/', $amount, $m)) {
            $amount = number_format((float) $m[1], 0, '.', ',') . ($m[2] ?? '');
        }

        return preg_match('/\bSAR$/i', $amount) ? $amount : $amount . ' ' . self::CURRENCY;
    }
}
