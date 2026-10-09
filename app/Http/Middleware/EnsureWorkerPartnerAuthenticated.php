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
 * a partner moved to Rejected by an admin loses their session immediately.
 * Pending partners keep full Portal access - only Hire Now / Download CV need
 * an Approved registration (Partner::isRegistrationApproved()).
 *
 * And, on a partner subdomain, only lets the partner that owns that
 * subdomain use the portal there - any other logged-in partner is sent to
 * the same page on their own site (see handle()).
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

        // Only a Rejected registration ends the session (same message as
        // before). Pending partners use the Portal normally.
        if ($partner->isRegistrationRejected()) {
            $message = 'Your registration has been rejected.';

            \App\Support\PartnerLoginTracker::close('rejected');   // Live Partners history
            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('worker.partner.login.page', ['login' => 1, 'auth_message' => $message]);
        }

        // The session cookie is shared by every *.recruitmentcv.com host, so
        // a logged-in partner could otherwise open their portal under ANOTHER
        // partner's subdomain. On a partner subdomain (the host's partner,
        // resolved once by the global ResolvePartnerWebsiteDomain), the
        // portal only runs for that same partner; anyone else is sent to the
        // same /partner/... page on their OWN site (Partner::portalBaseUrl()).
        // The main domain (no host partner) is unchanged.
        $hostPartner = app()->bound('currentPartner') ? app('currentPartner') : null;

        if ($hostPartner && (int) $hostPartner->id !== (int) $partner->id) {
            $ownBase = $partner->portalBaseUrl();

            // Never redirect to this same host (no loop) - if the partner's
            // own base resolves here, something is inconsistent: refuse.
            if (strcasecmp((string) parse_url($ownBase, PHP_URL_HOST), $request->getHost()) === 0) {
                abort(403);
            }

            $target = $ownBase . $request->getRequestUri();

            // Only page loads are redirected. A form/AJAX submit made on the
            // wrong host is refused rather than replayed elsewhere.
            if (!$request->isMethod('GET') && !$request->isMethod('HEAD')) {
                return $request->expectsJson()
                    ? response()->json(['status' => 'error', 'message' => __('locale.Please continue on your own partner website.'), 'redirect' => $ownBase . route('worker.partner.dashboard', [], false)], 403)
                    : redirect()->away($ownBase . route('worker.partner.dashboard', [], false));
            }

            return redirect()->away($target);
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
