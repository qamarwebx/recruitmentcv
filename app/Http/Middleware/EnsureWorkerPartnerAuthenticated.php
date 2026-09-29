<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Guards the worker-hosted Partner Portal routes. Deliberately separate from
 * the shared `auth` middleware (App\Http\Middleware\Authenticate), which
 * hardcodes redirects to route('partner.login') - the pre-existing
 * email/password login page - for any /partner/* path. That would collide
 * with the Worker site's own phone+OTP login on the same URL prefix. This
 * sends unauthenticated visitors to the dedicated /partner/login page
 * (PartnerAuthController::loginPage) instead.
 *
 * Also re-checks registration_status on every request, not just at login:
 * PartnerAuthController::login() already refuses to establish a session for
 * a Pending/Rejected partner, but a partner who was Approved when they logged
 * in and is later moved to Pending/Rejected by an admin would otherwise keep
 * their existing session and stay able to browse the portal.
 */
class EnsureWorkerPartnerAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('partner');

        if (!$guard->check()) {
            return redirect()->route('worker.partner.login.page');
        }

        $partner = $guard->user();

        if ((int) $partner->registration_status !== 1) {
            $message = (int) $partner->registration_status === 2
                ? 'Your registration has been rejected.'
                : 'Your registration is pending approval.';

            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('worker.partner.login.page', ['login' => 1, 'auth_message' => $message]);
        }

        $response = $next($request);

        // Authenticated partner pages must not be served from the browser
        // cache (e.g. Back after logout).
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
