<?php

namespace App\Http\Middleware;

use App\Support\PartnerTeam;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Server-side permission check for Partner Portal team members (web group,
 * so it sees every route). Owner sessions are never affected. A team
 * member session:
 *  - is ended if the member was removed / disabled / moved;
 *  - may use a Partner Portal route only when its module + action is
 *    granted (App\Support\PartnerTeam::resolve()), never owner-only ones;
 *  - may not use any other partner-guarded route (e.g. the legacy
 *    /partner/* portal) at all.
 * Denied: JSON 403 for AJAX/JSON, otherwise a redirect to the member's
 * first permitted page (403 when nothing is granted).
 */
class EnforcePartnerTeamPermissions
{
    public function handle(Request $request, Closure $next)
    {
        // Permissions / status are read fresh for every request.
        PartnerTeam::forgetMemo();

        if (!PartnerTeam::hasSession()) {
            return $next($request);
        }

        $member = PartnerTeam::current();
        if (!$member) {
            // Removed, disabled or no longer this partner's: end the session.
            PartnerTeam::clear();
            if (Auth::guard('partner')->check()) {
                Auth::guard('partner')->logout();
            }

            return $this->isPartnerRoute($request)
                ? redirect()->route('worker.partner.login.page')
                : $next($request);
        }

        $route = $request->route();
        $resolved = PartnerTeam::resolve($route ? $route->getName() : null, $request->method());

        if ($resolved === 'always') {
            return $next($request);
        }
        if (is_array($resolved) && PartnerTeam::permits($member, $resolved)) {
            return $next($request);
        }
        // e.g. Orders > View Candidate / Download CV for this partner's own orders
        if (is_array($resolved) && PartnerTeam::crossGranted($member, $route->getName(), $request)) {
            return $next($request);
        }
        if ($resolved === null && !$this->isPartnerRoute($request)) {
            return $next($request);   // public / customer pages
        }

        return $this->deny($request);
    }

    private function isPartnerRoute(Request $request): bool
    {
        $route = $request->route();
        if (!$route) {
            return false;
        }
        $middleware = $route->gatherMiddleware();

        return in_array('worker.partner.auth', $middleware, true) || in_array('auth:partner', $middleware, true);
    }

    private function deny(Request $request)
    {
        $message = __('locale.You do not have permission to access this page.');

        if ($request->expectsJson() || $request->ajax() || !in_array($request->method(), ['GET', 'HEAD'], true)) {
            return response()->json(['status' => 'error', 'message' => $message], 403);
        }

        $home = PartnerTeam::homeUrl();
        if ($home && rtrim($home, '/') !== rtrim($request->url(), '/')) {
            return redirect()->to($home)->withErrors(['permission' => $message]);
        }

        abort(403, $message);
    }
}
