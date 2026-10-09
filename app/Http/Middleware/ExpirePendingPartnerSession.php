<?php

namespace App\Http\Middleware;

use App\Support\PendingPartnerSession;
use App\Support\WebsiteSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Ends a Pending partner's session once the Pending Partner Session
 * Duration (CRM -> Website -> Settings -> Login Session) has passed since
 * they logged in - server-side, on EVERY web request (portal pages, public
 * pages, AJAX, any tab, any URL), so no client-side timer is involved.
 *
 * The duration is read from the settings on each request (a change in CRM
 * applies at once); the sign-in time is the server-side session stamp set
 * at login (App\Support\PendingPartnerSession). Approved partners, team
 * member logins and Rejected partners (still ended by
 * EnsureWorkerPartnerAuthenticated) are not touched here.
 *
 * Expired: logged out, session invalidated + new CSRF token (as for a
 * Rejected partner), then the Partner login page of the SAME host (the
 * partner's own subdomain or the main site) with the session-expired
 * message - or a 401 JSON answer carrying that login URL for AJAX.
 */
class ExpirePendingPartnerSession
{
    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('partner');

        if (!$guard->check() || !PendingPartnerSession::applies($guard->user())) {
            return $next($request);
        }

        $session = $request->session();
        $now = now()->getTimestamp();
        $signedInAt = (int) $session->get(PendingPartnerSession::SESSION_KEY);

        // Signed in before this rule existed (or an impossible value): the clock starts now.
        if ($signedInAt <= 0 || $signedInAt > $now) {
            $session->put(PendingPartnerSession::SESSION_KEY, $now);

            return $next($request);
        }

        if ($now < $signedInAt + WebsiteSettings::pendingPartnerSessionMinutes() * 60) {
            return $next($request);
        }

        \App\Support\PartnerLoginTracker::close('expired');
        $guard->logout();
        $session->invalidate();
        $session->regenerateToken();

        $message = __('locale.Your session has expired. Please log in again.');
        $params = ['login' => 1, 'auth_message' => $message];
        $wantsJson = $request->expectsJson() || $request->ajax();

        // Back to the same page after logging in again (relative path only - the
        // login page accepts nothing else).
        if (!$wantsJson && $request->isMethod('GET') && str_starts_with($request->getRequestUri(), '/') && !str_starts_with($request->getRequestUri(), '//')) {
            $params['redirect'] = $request->getRequestUri();
        }

        $loginUrl = route('worker.partner.login.page', $params);

        return $wantsJson
            ? response()->json(['status' => 'session_expired', 'message' => $message, 'redirect' => $loginUrl], 401)
            : redirect()->to($loginUrl);
    }
}
