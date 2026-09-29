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
    private const MAX_ATTEMPTS = 5;

    /** Called whenever a new code is generated. */
    public static function reset(Request $request): void
    {
        $request->session()->forget([self::VERIFIED_KEY, self::ATTEMPTS_KEY]);
    }

    /**
     * Strict, single-use, attempt-limited comparison. After MAX_ATTEMPTS
     * wrong codes the code is discarded and a new one must be requested.
     */
    public static function check(Request $request, $submitted): bool
    {
        $session = $request->session();
        $expected = $session->get('OTP');
        $submitted = trim((string) $submitted);

        if ($expected === null || (string) $expected === '' || $submitted === '') {
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

        $session->forget(['OTP', self::ATTEMPTS_KEY]);
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
