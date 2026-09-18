<?php

namespace App\Http\Middleware;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

/**
 * Resolves which Partner's branding (if any) the current request should
 * show, from the Host header alone - the single place this happens for
 * the whole recruitmentcv.com install (never duplicate this lookup in a
 * controller/blade instead). Shares `partnerBrand` globally; consumed by
 * resources/views/components/brand-logo.blade.php.
 *
 * Reads App\Models\Domain's `sub_domain` column (added alongside the
 * pre-existing `domain_name`/website_logo/website_logo_ar columns that
 * back the CRM's "Website" tab) - one Domain row per partner now carries
 * both an optional custom domain_name AND an optional *.recruitmentcv.com
 * sub_domain, sharing the same status/logo fields. This project's own
 * app/Http/Controllers/Worker/PartnerPortalController::settingsDomainUpdate()
 * (the Partner Portal's own "Domain" settings tab) writes the very same
 * column, so a Partner's own subdomain change here is immediately what
 * this middleware resolves on their next request - no separate table.
 *
 * This ONLY ever resolves branding, never identity: an authenticated
 * partner's session/guard is completely unaffected by which host they're
 * on, so Partner A visiting Partner B's subdomain can never see Partner
 * B's data through this middleware - it can only ever change which logo
 * renders for anonymous/all visitors on that host.
 */
class ResolvePartnerWebsiteDomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = strtolower($request->getHost());
        $host = preg_replace('#^www\.#', '', $host);

        $brand = [
            'logo_en' => null,
            'logo_ar' => null,
        ];

        if ($host !== 'recruitmentcv.com' && str_ends_with($host, '.recruitmentcv.com')) {
            $label = substr($host, 0, -strlen('.recruitmentcv.com'));

            $record = Domain::active()->where('sub_domain', $label)->first();

            if (!$record) {
                // Never falls back to the default site or another partner -
                // an unconfigured/inactive subdomain is a hard 404.
                abort(404);
            }

            $englishLogo = $record->portalLogoFile(false);
            $arabicLogo = $record->portalLogoFile(true);

            $brand['logo_en'] = $englishLogo ? asset('admin/assets/images/partner/' . $englishLogo) : null;
            $brand['logo_ar'] = $arabicLogo ? asset('admin/assets/images/partner/' . $arabicLogo) : null;
        }

        // Any other host (apex, www, or a non-recruitmentcv.com dev/local
        // domain) keeps the default (empty) $brand and is never rejected.
        View::share('partnerBrand', $brand);

        return $next($request);
    }
}
