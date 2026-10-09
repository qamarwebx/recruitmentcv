<?php

namespace App\Http\Middleware;

use App\Support\PartnerLoginTracker;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Records activity of a signed-in partner / team member session for CRM ->
 * Website -> Live Partners (App\Support\PartnerLoginTracker::touch()). Runs
 * after the middlewares that may end the session (portal revoked, Pending
 * session expired), so an ended session is never counted as active.
 *
 * Never saves the session itself: a second save in the same request ages
 * the flash data again and would wipe this request's validation errors and
 * success messages before the next page shows them. So:
 * - a session with no tracking row yet (signed in before tracking existed)
 *   gets one in handle(), BEFORE the response - its id is then stored by
 *   StartSession's normal save;
 * - the activity UPDATE (database only, no session write) runs after the
 *   response in terminate().
 *
 * Presence signals (route worker.presence) are left to
 * PartnerPresenceController: only a visible tab's heartbeat is activity - a
 * background tab's heartbeat or a closing tab's leave signal is not.
 */
class TrackPartnerActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (!$this->isPresence($request) && $request->hasSession() && !$request->session()->has(PartnerLoginTracker::SESSION_KEY) && Auth::guard('partner')->check()) {
            PartnerLoginTracker::touch();
        }

        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        if (!$this->isPresence($request) && $request->hasSession() && $request->session()->has(PartnerLoginTracker::SESSION_KEY) && Auth::guard('partner')->check()) {
            PartnerLoginTracker::touch();
        }
    }

    private function isPresence(Request $request): bool
    {
        return $request->routeIs('worker.presence');
    }
}
