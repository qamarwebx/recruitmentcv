<?php

namespace App\Http\Middleware;

use App\Support\PhoneNumber;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Rate limit for Partner OTP sending (each accepted request sends a paid
 * WhatsApp message or a login email). Two Laravel RateLimiter counters,
 * both must allow: per identifier - the normalized mobile number (country
 * code + local number, as the rest of partner auth compares numbers -
 * App\Support\PhoneNumber), or the username/email (email login code) or
 * email address (Forgot Password) -
 * and per IP.
 * Check + hit run inside one cache lock, so concurrent requests can't all
 * pass the check before any of them is counted. The 429 response is the
 * same whether or not the number belongs to a Partner. Limits:
 * config/partner.php otp_send.
 */
class ThrottlePartnerOtpSend
{
    public function handle(Request $request, Closure $next)
    {
        $limits = config('partner.otp_send');
        $code = preg_replace('/\D+/', '', (string) $request->input('country_code'));
        // Username/email login code (identifier) or Forgot Password (email):
        // counted per typed account/address; otherwise per mobile number.
        $account = $request->input('identifier', $request->input('email'));
        $mobileKey = filled($account)
            ? 'partner-otp-send:account:' . sha1(strtolower(trim((string) $account)))
            : 'partner-otp-send:mobile:' . sha1($code . '|' . PhoneNumber::local($code, (string) $request->input('mobile')));
        $ipKey = 'partner-otp-send:ip:' . sha1((string) $request->ip());

        $allowed = Cache::lock('partner-otp-send-limiter', 10)->block(5, function () use ($limits, $mobileKey, $ipKey) {
            if (RateLimiter::tooManyAttempts($mobileKey, (int) $limits['per_mobile'])
                || RateLimiter::tooManyAttempts($ipKey, (int) $limits['per_ip'])) {
                return false;
            }

            RateLimiter::hit($mobileKey, (int) $limits['per_mobile_minutes'] * 60);
            RateLimiter::hit($ipKey, (int) $limits['per_ip_minutes'] * 60);

            return true;
        });

        if (!$allowed) {
            return response()->json([
                'status' => 'error',
                'message' => __('locale.Too many attempts. Please try again later.'),
            ], 429);
        }

        return $next($request);
    }
}
