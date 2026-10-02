<?php

namespace App\Http\Middleware;

use App\Support\CustomerSite;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Customer accounts are "one account, all partner sites": a customer
 * (`web` guard) can use the same account on every Partner website.
 * users.partner_id only records the website the customer registered on - it
 * never restricts which Partner website they may use. The session cookie is
 * shared across every *.recruitmentcv.com host, so one customer login is
 * valid on all Partner sites (see App\Support\CustomerSite::owns()). The
 * only restriction enforced here is the main-site one below; only the `web`
 * guard is ever touched - Partner authentication (`partner` guard) is
 * completely separate and never affected.
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

        // Partner website: any customer account may be used here
        // (CustomerSite::owns() is the single place that rule lives).
        if ($guard->check() && !CustomerSite::owns($guard->user())) {
            $guard->logout();
        }

        return $next($request);
    }
}
