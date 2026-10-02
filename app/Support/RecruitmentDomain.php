<?php

namespace App\Support;

/**
 * The one place RecruitmentCV's root domain and partner hosts are built
 * and recognised. The root comes from config
 * services.hostinger.recruitmentcv_domain (env HOSTINGER_RECRUITMENTCV_DOMAIN,
 * default recruitmentcv.com - same name as in the CRM), so a staging/local
 * copy only needs its own .env value. Partner websites are "{sub}.{root}".
 */
class RecruitmentDomain
{
    /** e.g. "recruitmentcv.com" (lower-case, no leading/trailing dot). */
    public static function root(): string
    {
        $root = strtolower(trim((string) config('services.hostinger.recruitmentcv_domain'), " \t\n\r\0\x0B."));

        return $root !== '' ? $root : 'recruitmentcv.com';
    }

    /** A partner's website host, e.g. "raha.recruitmentcv.com"; null without a subdomain. */
    public static function partnerHost(?string $subdomain): ?string
    {
        $subdomain = trim((string) $subdomain);

        return $subdomain === '' ? null : $subdomain . '.' . self::root();
    }

    /**
     * The subdomain label of a request host ("raha" for raha.recruitmentcv.com,
     * also with a leading "www." and in any letter case); null for the root
     * domain itself or any host outside it. Only ever used to LOOK UP a
     * domains row - an unknown label is still the caller's 404.
     */
    public static function subdomainFromHost(string $host): ?string
    {
        $host = preg_replace('#^www\.#', '', strtolower($host));
        $suffix = '.' . self::root();

        if ($host === self::root() || !str_ends_with($host, $suffix)) {
            return null;
        }

        $label = substr($host, 0, -strlen($suffix));

        return $label !== '' ? $label : null;
    }

    public static function isPartnerHost(string $host): bool
    {
        return self::subdomainFromHost($host) !== null;
    }
}
