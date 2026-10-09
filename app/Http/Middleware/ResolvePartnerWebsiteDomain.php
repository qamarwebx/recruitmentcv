<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

/**
 * Resolves which Partner's website (if any) the current request is, from
 * the Host header - the single place this happens for the whole
 * recruitmentcv.com install (never duplicate this lookup in a controller /
 * blade). The rules live in App\Support\PartnerDomains: the main site, a
 * live "{sub_domain}.recruitmentcv.com" subdomain, or a partner's active
 * Own Domain (custom_domain, verified + HTTPS, selected Domain Type) - all
 * the same application. Anything else is a 404. Shares `partnerBrand`
 * (brand-logo component, SiteBrand) and binds `currentPartner`.
 *
 * This ONLY ever resolves branding/content, never identity: an
 * authenticated partner's session/guard is unaffected by the host, so
 * Partner A visiting Partner B's website can never see Partner B's data
 * through this middleware (EnsureWorkerPartnerAuthenticated keeps a signed-
 * in partner on its own website).
 */
class ResolvePartnerWebsiteDomain
{
    public function handle(Request $request, Closure $next)
    {
        $brand = [
            'logo_en' => null,
            'logo_ar' => null,
            'favicon' => null,
            // Partner Company Profile for public titles/meta/footer
            // (App\Support\SiteBrand); null = main site, global branding.
            'company' => null,
            // Effective "Append Company Name" (App\Support\CompanyProfile:
            // the partner's own value, else the CRM Global one; the main site
            // = the Global one; see SiteBrand::currentAppendedName()).
            'append_company_name' => false,
        ];

        $currentPartner = null;

        // The one host -> partner resolution (App\Support\PartnerDomains):
        // the main site, a live "{label}.{root}" subdomain, or a partner's
        // active Own Domain. Never falls back to the default site or another
        // partner - an unconfigured / not-yet-live / disabled / removed
        // address, or one whose partner no longer exists, is a hard 404.
        $resolved = \App\Support\PartnerDomains::resolve($request->getHost());

        // Own Domain routing check (CRM "Verify Domain"): answered for a
        // verified domain (also before it is active), with a proof keyed by
        // that domain's secret - no partner data, no session.
        $challenge = $this->challenge($request);
        if ($challenge) {
            return $challenge;
        }

        if ($resolved['kind'] === 'unknown') {
            abort(404);
        }

        if ($resolved['kind'] === 'custom') {
            // The session/CSRF cookies of *.recruitmentcv.com (SESSION_DOMAIN)
            // would be refused by the browser on another domain: on an Own
            // Domain they are host-only cookies of that domain.
            config(['session.domain' => null]);
            // Queued cookies (e.g. the partner guard's remember-me) too.
            app('cookie')->setDefaultPathAndDomain(config('session.path', '/'), null, config('session.secure'), config('session.same_site'));
        }

        $record = $resolved['domain'];

        if ($record) {
            $englishLogo = $record->portalLogoFile(false);
            $arabicLogo = $record->portalLogoFile(true);

            $brand['logo_en'] = $englishLogo ? asset('admin/assets/images/partner/' . $englishLogo) : null;
            $brand['logo_ar'] = $arabicLogo ? asset('admin/assets/images/partner/' . $arabicLogo) : null;
            // Same Domain row as the logos; versioned by the file's mtime.
            $brand['favicon'] = $record->faviconUrl();
            // Same row = the partner's Company Profile (Partner Portal ->
            // Website -> Company Profile); raw values, the locale and the
            // per-field global fallback are applied by SiteBrand at render.
            $brand['company'] = \App\Support\SiteBrand::companyFromDomain($record);
            $brand['append_company_name'] = \App\Models\PartnerPageContent::brandingFor((int) $record->partner_id)[\App\Models\PartnerPageContent::APPEND_COMPANY_NAME];

            // Identity for CONTENT selection only (which partner's public
            // website this is), never auth - the partner guard/session is
            // untouched by this.
            $currentPartner = $record->partner;
        }

        // The main site keeps the default (empty) $brand / no partner, with
        // the Global "Append Company Name" (shown with the Global Company Name).
        if (!$record) {
            $brand['append_company_name'] = \App\Support\CompanyProfile::appendSetting(null)[\App\Support\CompanyProfile::APPEND];
        }
        View::share('partnerBrand', $brand);
        app()->instance('currentPartner', $currentPartner);

        return $next($request);
    }

    /**
     * GET /.well-known/recruitmentcv-domain-check/{nonce} on a verified Own
     * Domain: {"domain": host, "proof": HMAC(nonce, domain secret)} - shows
     * the CRM that this domain reaches this application. Null otherwise.
     */
    private function challenge(Request $request): ?\Illuminate\Http\JsonResponse
    {
        $prefix = \App\Support\PartnerDomains::CHALLENGE_PATH . '/';
        $path = ltrim($request->path(), '/');
        if (!$request->isMethod('GET') || !str_starts_with($path, $prefix)) {
            return null;
        }
        $nonce = substr($path, strlen($prefix));
        if (!preg_match('/^[A-Za-z0-9]{16,64}$/', $nonce)) {
            return null;
        }
        $domain = \App\Support\PartnerDomains::challengeDomain($request->getHost());
        if (!$domain) {
            return null;
        }

        return response()->json([
            'domain' => $domain->custom_domain,
            'proof' => \App\Support\PartnerDomains::challengeProof($domain->custom_domain_token, $nonce),
        ])->header('Cache-Control', 'no-store');
    }
}
