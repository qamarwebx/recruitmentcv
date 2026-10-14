<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Server-side proof that the mobile number in the session actually passed
 * OTP validation. generate-otp/generate-otp2 store the number in the session
 * before any code is entered, so the login/register endpoints must never
 * trust that number on its own - only once check() has succeeded for it.
 */
class OtpVerification
{
    private const VERIFIED_KEY = 'otp_verified_mobile';
    private const ATTEMPTS_KEY = 'otp_attempts';
    private const ISSUED_KEY = 'otp_issued';
    private const MAX_ATTEMPTS = 5;

    /** How long a code from issued() stays valid. */
    public const TTL_SECONDS = 600;

    /** Called whenever a new code is generated. */
    public static function reset(Request $request): void
    {
        $request->session()->forget([self::VERIFIED_KEY, self::ATTEMPTS_KEY, self::ISSUED_KEY]);
    }

    /**
     * Called right after a code has been put in the session (generate-otp2):
     * starts that code's expiry clock. Tied to the code itself, so a code
     * stored by a generator that never calls this keeps its old behaviour
     * instead of inheriting another code's clock.
     */
    public static function issued(Request $request): void
    {
        $otp = (string) $request->session()->get('OTP');
        if ($otp !== '') {
            $request->session()->put(self::ISSUED_KEY, ['code' => hash('sha256', $otp), 'at' => time()]);
        }
    }

    /** True when the session's current code came from issued() more than TTL_SECONDS ago. */
    public static function isExpired(Request $request): bool
    {
        $issued = $request->session()->get(self::ISSUED_KEY);
        $otp = (string) $request->session()->get('OTP');

        return is_array($issued) && $otp !== ''
            && hash_equals((string) ($issued['code'] ?? ''), hash('sha256', $otp))
            && time() - (int) ($issued['at'] ?? 0) > self::TTL_SECONDS;
    }

    /**
     * Strict, single-use, attempt-limited, expiring comparison. After
     * MAX_ATTEMPTS wrong codes - or once the code has expired - it is
     * discarded and a new one must be requested.
     */
    public static function check(Request $request, $submitted): bool
    {
        $session = $request->session();
        $expected = $session->get('OTP');
        $submitted = trim((string) $submitted);

        if ($expected === null || (string) $expected === '' || $submitted === '') {
            return false;
        }

        if (self::isExpired($request)) {
            $session->forget(['OTP', self::ATTEMPTS_KEY, self::ISSUED_KEY]);

            return false;
        }

        if (!hash_equals((string) $expected, $submitted)) {
            $attempts = (int) $session->get(self::ATTEMPTS_KEY, 0) + 1;
            $session->put(self::ATTEMPTS_KEY, $attempts);

            if ($attempts >= self::MAX_ATTEMPTS) {
                $session->forget(['OTP', self::ATTEMPTS_KEY]);
            }

            return false;
        }

        $session->forget(['OTP', self::ATTEMPTS_KEY, self::ISSUED_KEY]);
        $session->put(self::VERIFIED_KEY, (string) $session->get('Mobile'));

        return true;
    }

    /** True only if the session's current Mobile is the one that was verified. */
    public static function isVerified(Request $request): bool
    {
        $verified = $request->session()->get(self::VERIFIED_KEY);
        $mobile = (string) $request->session()->get('Mobile');

        return $verified !== null && $mobile !== '' && hash_equals((string) $verified, $mobile);
    }

    /** One login/registration per successful verification. */
    public static function consume(Request $request): void
    {
        $request->session()->forget(self::VERIFIED_KEY);
    }
}
