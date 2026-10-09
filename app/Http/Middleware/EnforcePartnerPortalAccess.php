<?php

namespace App\Http\Middleware;

use App\Support\PartnerTeam;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * A partner whose RecruitmentCV subdomain was removed in CRM
 * (Partner::isPortalRevoked()) loses an already-open session on the next
 * request - any route, page or AJAX, on any host. Its removed subdomain
 * itself already 404s in ResolvePartnerWebsiteDomain (it is no longer a live
 * subdomain), and every login path refuses it (PartnerAuthController,
 * SocialLoginController); this covers sessions that were open at removal.
 *
 * Only the partner login is ended (and the session id renewed) - a customer
 * signed in on the same browser stays signed in. Sent to the main
 * recruitmentcv.com partner login page (the partner has no site of its own
 * any more - never another partner's) with the "portal unavailable" message.
 */
class EnforcePartnerPortalAccess
{
    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('partner');

        if (!$guard->check() || !$guard->user()->isPortalRevoked()) {
            return $next($request);
        }

        \App\Support\PartnerLoginTracker::close('revoked');
        $guard->logout();
        PartnerTeam::clear();
        $request->session()->migrate(true);

        $message = __('locale.Your RecruitmentCV portal is currently unavailable. Please contact support.');
        $loginUrl = rtrim((string) config('app.url'), '/') . route('worker.partner.login.page', ['login' => 1, 'auth_message' => $message], false);

        return ($request->expectsJson() || $request->ajax())
            ? response()->json(['status' => 'portal_unavailable', 'message' => $message, 'redirect' => $loginUrl], 403)
            : redirect()->away($loginUrl);
    }
}
