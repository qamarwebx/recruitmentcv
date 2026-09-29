<?php

namespace App\Http\Middleware;

use App\Support\CustomerSite;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Strict per-website customer accounts. The session cookie is shared across
 * every *.recruitmentcv.com host, so a customer logged in on one website
 * would otherwise also count as logged in on every other one. A customer
 * (`web` guard) whose users.partner_id doesn't match the site of this request
 * (resolved by the global ResolvePartnerWebsiteDomain) is logged out of the
 * customer guard here. Only the `web` guard is touched - a partner session
 * is unaffected.
 *
 * The main recruitmentcv.com site is the PARTNER entry point only: customer
 * login/registration requests are refused there and no customer session is
 * honoured. Partner auth has its own routes (/partner/otp/*, /partner/login-otp,
 * /partner/register-*, /partner-google/*), so it is never affected.
 */
class EnforceCustomerSite
{
    /** Customer (web guard) login/registration endpoints. */
    private const CUSTOMER_AUTH_POSTS = [
        'generate-otp', 'generate-otp2', 'validate-otp', 'validate-otp2',
        'login-user', 'login-user2', 'registeruser', 'registeruser2',
        'user/login', 'register',
    ];

    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('web');

        if (!CustomerSite::isPartnerSite()) {
            if ($guard->check()) {
                $guard->logout();
            }

            if ($request->isMethod('post') && $request->is(self::CUSTOMER_AUTH_POSTS)) {
                return response()->json(['status' => 'error', 'message' => 'Customer login is only available on a partner website.'], 403);
            }

            // Customer Google sign-in start. (The callback stays open: partner-
            // subdomain customers' Google sign-in returns through this host.)
            if ($request->is('auth/google')) {
                return redirect()->route('worker.home');
            }

            return $next($request);
        }

        if ($guard->check() && !CustomerSite::owns($guard->user())) {
            $guard->logout();
        }

        return $next($request);
    }
}
