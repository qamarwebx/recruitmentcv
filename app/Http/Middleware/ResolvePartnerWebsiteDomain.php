<?php

namespace App\Http\Middleware;

use App\Models\PartnerWebsiteDomain;
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
            $record = PartnerWebsiteDomain::active()->where('domain', $host)->first();

            if (!$record) {
                // Never falls back to the default site or another partner -
                // an unconfigured/inactive subdomain is a hard 404.
                abort(404);
            }

            $englishLogo = $record->logoFile(false);
            $arabicLogo = $record->logoFile(true);

            $brand['logo_en'] = $englishLogo ? asset('admin/assets/images/partnerwebsite/' . $englishLogo) : null;
            $brand['logo_ar'] = $arabicLogo ? asset('admin/assets/images/partnerwebsite/' . $arabicLogo) : null;
        }

        // Any other host (apex, www, or a non-recruitmentcv.com dev/local
        // domain) keeps the default (empty) $brand and is never rejected.
        View::share('partnerBrand', $brand);

        return $next($request);
    }
}
