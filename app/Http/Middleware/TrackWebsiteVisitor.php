<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

/**
 * Records public page views on recruitmentcv.com and every partner
 * subdomain into website_visitors (CRM -> Website -> Website Visitor).
 * Registered once in the `web` group - the single tracking point for all
 * public pages.
 *
 * All work happens in terminate(), after the response has been sent to
 * the browser, and costs one INSERT: the partner comes from
 * ResolvePartnerWebsiteDomain (already resolved for this request) and the
 * customer id straight from the session. Only successful, full-page GETs
 * of the public site's routes (worker.*) are recorded - never the Partner
 * Portal (worker.partner.*), admin routes, AJAX, redirects, errors or
 * known bots. Query strings are dropped from the page and referrer.
 */
class TrackWebsiteVisitor
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        try {
            if (!$this->shouldTrack($request, $response)) {
                return;
            }

            $userAgent = (string) $request->userAgent();
            $agent = new Agent();
            $agent->setUserAgent($userAgent);

            if ($userAgent === '' || $agent->isRobot()) {
                return;
            }

            $partner = app()->bound('currentPartner') ? app('currentPartner') : null;
            $ip = (string) $request->ip();
            $now = now();

            DB::table('website_visitors')->insert([
                'partner_id' => $partner->id ?? null,
                'host' => Str::limit(preg_replace('#^www\.#', '', strtolower($request->getHost())), 100, ''),
                'path' => Str::limit('/' . ltrim($request->path(), '/'), 255, ''),
                'route_name' => Str::limit((string) optional($request->route())->getName(), 100, ''),
                'referrer' => $this->referrer($request),
                'ip_address' => $ip,
                'visitor_hash' => hash('sha256', $ip . '|' . $userAgent . '|' . config('app.key')),
                'user_id' => $this->customerId($request),
                'browser' => Str::limit((string) $agent->browser(), 50, '') ?: null,
                'platform' => Str::limit((string) $agent->platform(), 50, '') ?: null,
                'device' => $agent->isTablet() ? 'tablet' : ($agent->isMobile() ? 'mobile' : 'desktop'),
                'user_agent' => Str::limit($userAgent, 255, ''),
                'visited_at' => $now,
                'visited_on' => $now->toDateString(),
            ]);
        } catch (\Throwable $e) {
            // Tracking must never affect the website.
            Log::warning('Website visitor not recorded', ['error' => $e->getMessage()]);
        }
    }

    private function shouldTrack(Request $request, $response): bool
    {
        $routeName = (string) optional($request->route())->getName();

        return $request->isMethod('GET')
            && !$request->ajax()
            && !$request->headers->has('Purpose') // browser prefetch
            && $response->getStatusCode() === 200
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html')
            && str_starts_with($routeName, 'worker.')
            && !str_starts_with($routeName, 'worker.partner.');
    }

    private function referrer(Request $request): ?string
    {
        $referrer = (string) $request->headers->get('referer');
        $parts = $referrer !== '' ? parse_url($referrer) : false;

        if (empty($parts['host'])) {
            return null;
        }

        return Str::limit(strtolower($parts['host']) . ($parts['path'] ?? ''), 255, '');
    }

    /** Logged-in customer (web guard) id from the session - no user query. */
    private function customerId(Request $request): ?int
    {
        if (!$request->hasSession()) {
            return null;
        }

        $id = $request->session()->get(Auth::guard('web')->getName());

        return is_numeric($id) ? (int) $id : null;
    }
}
