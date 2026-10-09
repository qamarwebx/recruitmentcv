<?php

namespace App\Support;

use App\Models\Domain;
use Illuminate\Support\Facades\DB;

/**
 * The one place a partner website address is validated, owned and
 * resolved - for both kinds of address on a partner's domains row:
 *
 * - Sub Domain  "{sub_domain}.recruitmentcv.com" (Hostinger subdomain of the
 *               recruitmentcv.com website - unchanged flow)
 * - Own Domain  custom_domain, e.g. "ridajobbook.com" (Hostinger parked
 *               domain of the same recruitmentcv.com website)
 *
 * Both serve the same application/folder; the host only selects WHICH
 * partner's website it is. A host serves a partner only when that address
 * is fully live: a subdomain provisioned on Hostinger and not removed; an
 * own domain whose ownership was proven (DNS TXT), that reaches this app,
 * has a valid HTTPS certificate (status "active") and is the partner's
 * selected Domain Type. Anything else - unknown, pending, inactive, removed,
 * a deleted partner - resolves to nothing (the caller's 404), never to
 * another partner or the main site. Twin file in both apps.
 */
final class PartnerDomains
{
    public const TYPE_SUBDOMAIN = 'subdomain';

    public const TYPE_CUSTOM = 'custom';

    public const PENDING = 'pending';

    public const VERIFIED = 'verified';

    public const ACTIVE = 'active';

    public const REMOVED = 'removed';

    /** DNS TXT ownership record: "_recruitmentcv-verify.<domain>" = "rcv-verify=<token>". */
    public const TXT_LABEL = '_recruitmentcv-verify';

    public const TXT_PREFIX = 'rcv-verify=';

    /** Routing check served by RecruitmentCV for a verified own domain (no partner data). */
    public const CHALLENGE_PATH = '.well-known/recruitmentcv-domain-check';

    /** Addresses no partner may claim as an own domain (the platform's own). */
    private const RESERVED = ['qamarhire.com', 'qamarhire.test', 'hostinger.com', 'hostingersite.com', 'main-hosting.eu', 'dns-parking.com', 'localhost'];

    public static function root(): string
    {
        $root = strtolower(trim((string) config('services.hostinger.recruitmentcv_domain'), " \t\n\r\0\x0B."));

        return $root !== '' ? $root : 'recruitmentcv.com';
    }

    // ---- input -------------------------------------------------------------

    /**
     * Why $input is not an acceptable own domain (null = acceptable). A bare
     * host name only: no scheme, path, query, port, credentials, wildcard,
     * IP address or other characters - rejected, never silently stripped.
     */
    public static function problem(?string $input): ?string
    {
        $value = strtolower(trim((string) $input));
        if ($value === '') {
            return 'Enter the domain, e.g. ridajobbook.com.';
        }
        if (preg_match('#[/\\\\?\#@:%*\s]|^[a-z][a-z0-9+.-]*://#', $value) || preg_match('/[\x00-\x1F\x7F]/', $value)) {
            return 'Enter the domain name only, e.g. ridajobbook.com - without http:// or https://, paths, ports, @ or other characters.';
        }

        $domain = self::normalize($value);
        if ($domain === null) {
            return 'This is not a valid domain name.';
        }
        if (self::isReserved($domain)) {
            return 'This domain belongs to the platform and can\'t be used as a partner\'s own domain.';
        }

        return null;
    }

    /** Normalized host ("www." and a trailing dot removed, IDN in ASCII), or null when invalid. */
    public static function normalize(?string $input): ?string
    {
        $value = rtrim(strtolower(trim((string) $input)), '.');
        if ($value === '' || preg_match('#[^a-z0-9.\-\x80-\xFF]#', $value)) {
            return null;
        }
        if (preg_match('/[\x80-\xFF]/', $value)) {
            if (!function_exists('idn_to_ascii')) {
                return null;
            }
            $value = idn_to_ascii($value, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46) ?: '';
        }
        $value = preg_replace('/^www\./', '', $value);

        if ($value === '' || strlen($value) > 253 || filter_var($value, FILTER_VALIDATE_IP)) {
            return null;
        }
        $labels = explode('.', $value);
        if (count($labels) < 2) {
            return null;
        }
        foreach ($labels as $label) {
            if (!preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $label)) {
                return null;
            }
        }
        if (!preg_match('/^(?:[a-z]{2,63}|xn--[a-z0-9-]{1,59})$/', end($labels))) {
            return null;
        }

        return $value;
    }

    public static function isReserved(string $domain): bool
    {
        foreach (array_merge([self::root()], self::RESERVED) as $reserved) {
            if ($domain === $reserved || str_ends_with($domain, '.' . $reserved)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The partner (id) that already holds $domain as its own domain - or as
     * the legacy domain_name of the older manual flow - other than
     * $partnerId; null when free. One domain, one partner.
     */
    public static function takenBy(string $domain, int $partnerId): ?int
    {
        $owner = DB::table('domains')
            ->where('partner_id', '!=', $partnerId)
            ->where(fn ($q) => $q->whereIn('custom_domain', [$domain, 'www.' . $domain])->orWhereIn('domain_name', [$domain, 'www.' . $domain]))
            ->value('partner_id');

        return $owner !== null ? (int) $owner : null;
    }

    // ---- state of a domains row --------------------------------------------

    /** The *.recruitmentcv.com subdomain is live (same rule as Domain::scopeLiveSubdomain()). */
    public static function subdomainServes(?Domain $domain): bool
    {
        return $domain !== null
            && trim((string) $domain->sub_domain) !== ''
            && $domain->hostinger_status === 'success'
            && !in_array($domain->status, ['inactive', 'suspended'], true)
            && $domain->subdomain_removed_at === null;
    }

    /** The own domain serves: selected Domain Type, verified, reachable, HTTPS (status active), not removed. */
    public static function customServes(?Domain $domain): bool
    {
        return $domain !== null
            && $domain->domain_type === self::TYPE_CUSTOM
            && trim((string) $domain->custom_domain) !== ''
            && $domain->custom_domain_status === self::ACTIVE
            && $domain->custom_domain_removed_at === null
            && !in_array($domain->status, ['inactive', 'suspended'], true);
    }

    /** Every host currently serving this partner's website. */
    public static function hosts(?Domain $domain): array
    {
        $hosts = [];
        if (self::customServes($domain)) {
            $hosts[] = $domain->custom_domain;
        }
        if (self::subdomainServes($domain)) {
            $hosts[] = strtolower(trim($domain->sub_domain)) . '.' . self::root();
        }

        return $hosts;
    }

    /** The partner's primary address: the own domain when it is the selected, active type, else the live subdomain. */
    public static function primaryHost(?Domain $domain): ?string
    {
        return self::hosts($domain)[0] ?? null;
    }

    public static function primaryUrl(?Domain $domain): ?string
    {
        $host = self::primaryHost($domain);

        return $host ? 'https://' . $host : null;
    }

    /**
     * Limits a domains query (as $alias) to rows with a live website - the
     * SQL form of hosts() !== [].
     */
    public static function whereServing($query, string $alias = 'domains')
    {
        return $query->whereNotIn($alias . '.status', ['inactive', 'suspended'])->where(function ($q) use ($alias) {
            $q->where(function ($sub) use ($alias) {
                $sub->whereNotNull($alias . '.sub_domain')->where($alias . '.sub_domain', '!=', '')
                    ->where($alias . '.hostinger_status', 'success')->whereNull($alias . '.subdomain_removed_at');
            })->orWhere(function ($custom) use ($alias) {
                $custom->where($alias . '.domain_type', self::TYPE_CUSTOM)->whereNotNull($alias . '.custom_domain')
                    ->where($alias . '.custom_domain_status', self::ACTIVE)->whereNull($alias . '.custom_domain_removed_at');
            });
        });
    }

    /**
     * What a partner sees for its own website address (Partner Portal ->
     * Website -> Domain, read-only): 'custom' when Own Domain is the
     * selected type and one is assigned, 'subdomain' when a subdomain is
     * assigned, else 'none' (nothing configured / removed).
     */
    public static function displayMode(?Domain $domain): string
    {
        if ($domain && $domain->domain_type === self::TYPE_CUSTOM && trim((string) $domain->custom_domain) !== '') {
            return self::TYPE_CUSTOM;
        }
        if ($domain && trim((string) $domain->sub_domain) !== '') {
            return self::TYPE_SUBDOMAIN;
        }

        return 'none';
    }

    /**
     * Read-only state of an assigned Own Domain, from its stored status and
     * last verification checks (set by the CRM's Verify Domain):
     * status active|pending|inactive, verification verified|pending,
     * provisioning active|pending, ssl active|pending|unknown, live_url =
     * the address the website is on right now.
     */
    public static function ownDomainState(Domain $domain): array
    {
        $steps = (array) (($domain->custom_domain_checks['steps'] ?? null) ?: []);
        $ok = fn (string $step) => (bool) ($steps[$step]['ok'] ?? false);
        $active = self::customServes($domain);
        $inactive = in_array($domain->status, ['inactive', 'suspended'], true);

        return [
            'domain' => (string) $domain->custom_domain,
            'status' => $inactive ? 'inactive' : ($active ? 'active' : 'pending'),
            'verification' => $domain->custom_domain_verified_at !== null || in_array($domain->custom_domain_status, [self::VERIFIED, self::ACTIVE], true) ? 'verified' : 'pending',
            'provisioning' => $domain->custom_domain_status === self::ACTIVE || ($ok('hosting') && $ok('routing')) ? 'active' : 'pending',
            'ssl' => $domain->custom_domain_status === self::ACTIVE || $ok('ssl') ? 'active' : ($steps ? 'pending' : 'unknown'),
            'live_url' => self::primaryUrl($domain),
        ];
    }

    // ---- SQL forms of the same rules (reports / filters) ----------------------

    /** SQL condition: the domains row (as $alias) has a serving Own Domain - customServes(). */
    public static function customServingSql(string $alias = 'domains'): string
    {
        return "({$alias}.domain_type = '" . self::TYPE_CUSTOM . "' AND {$alias}.custom_domain IS NOT NULL AND {$alias}.custom_domain <> ''"
            . " AND {$alias}.custom_domain_status = '" . self::ACTIVE . "' AND {$alias}.custom_domain_removed_at IS NULL"
            . " AND {$alias}.status NOT IN ('inactive', 'suspended'))";
    }

    /** SQL condition: the domains row (as $alias) has a live subdomain - subdomainServes(). */
    public static function subdomainServingSql(string $alias = 'domains'): string
    {
        return "({$alias}.sub_domain IS NOT NULL AND {$alias}.sub_domain <> '' AND {$alias}.hostinger_status = 'success'"
            . " AND {$alias}.subdomain_removed_at IS NULL AND {$alias}.status NOT IN ('inactive', 'suspended'))";
    }

    /** SQL: the primary address type ('custom' / 'subdomain' / NULL) - the type of primaryHost(). */
    public static function primaryTypeSql(string $alias = 'domains'): string
    {
        return 'CASE WHEN ' . self::customServingSql($alias) . " THEN '" . self::TYPE_CUSTOM . "'"
            . ' WHEN ' . self::subdomainServingSql($alias) . " THEN '" . self::TYPE_SUBDOMAIN . "' END";
    }

    /** SQL: the primary address host (NULL when none) - primaryHost(). */
    public static function primaryHostSql(string $alias = 'domains'): string
    {
        $suffix = DB::getPdo()->quote('.' . self::root());

        return 'CASE WHEN ' . self::customServingSql($alias) . " THEN LOWER({$alias}.custom_domain)"
            . ' WHEN ' . self::subdomainServingSql($alias) . " THEN CONCAT(LOWER({$alias}.sub_domain), {$suffix}) END";
    }

    /**
     * A search term as a host fragment: trimmed, lower-case, without a
     * scheme, path, query, port or leading "www." ("https://RidaJobBook.com/x"
     * -> "ridajobbook.com"). Only ever compared against stored hosts.
     */
    public static function searchHost(string $term): string
    {
        $term = strtolower(trim($term));
        $term = preg_replace('#^[a-z][a-z0-9+.-]*://#', '', $term);
        $term = preg_replace('#[/?\#].*$#', '', $term);
        $term = preg_replace('#:\d+$#', '', $term);
        $term = preg_replace('/^www\./', '', rtrim($term, '.'));

        return $term;
    }

    // ---- request host -> partner ---------------------------------------------

    /**
     * Which website a request host is: ['kind' => 'main'|'subdomain'|'custom'|'unknown', 'domain' => ?Domain].
     * 'unknown' = must not be served (404): an unconfigured / not live
     * address, or a live one whose partner no longer exists.
     */
    public static function resolve(string $host): array
    {
        $host = rtrim(strtolower(trim($host)), '.');
        $bare = preg_replace('/^www\./', '', $host);
        $root = self::root();

        if ($bare === $root || in_array($host, self::mainHosts(), true)) {
            return ['kind' => 'main', 'domain' => null];
        }

        if (str_ends_with($bare, '.' . $root)) {
            $label = substr($bare, 0, -strlen('.' . $root));
            $domain = $label !== '' ? Domain::where('sub_domain', $label)->first() : null;

            return self::subdomainServes($domain) && self::partnerExists($domain)
                ? ['kind' => 'subdomain', 'domain' => $domain]
                : ['kind' => 'unknown', 'domain' => null];
        }

        $domain = self::normalize($bare) ? Domain::where('custom_domain', $bare)->first() : null;

        return self::customServes($domain) && self::partnerExists($domain)
            ? ['kind' => 'custom', 'domain' => $domain]
            : ['kind' => 'unknown', 'domain' => null];
    }

    /** A verified (not yet necessarily active) own domain row for the routing check, by host. */
    public static function challengeDomain(string $host): ?Domain
    {
        $bare = preg_replace('/^www\./', '', rtrim(strtolower(trim($host)), '.'));
        $domain = self::normalize($bare) ? Domain::where('custom_domain', $bare)->first() : null;

        return $domain && in_array($domain->custom_domain_status, [self::VERIFIED, self::ACTIVE], true)
            && $domain->custom_domain_removed_at === null && $domain->custom_domain_token
            ? $domain
            : null;
    }

    /**
     * Base URL to keep a partner on after login: the CURRENT host when it is
     * one of this partner's own live addresses (resolved server-side by
     * ResolvePartnerWebsiteDomain, never trusted from input), else the
     * partner's primary address, else the main site.
     */
    public static function siteBaseUrl($partner): string
    {
        $current = app()->bound('currentPartner') ? app('currentPartner') : null;
        if ($partner && $current && (int) $current->id === (int) $partner->id && request()) {
            return 'https://' . strtolower(request()->getHost());
        }

        return self::primaryUrl($partner ? $partner->domain : null) ?? rtrim((string) config('app.url'), '/');
    }

    /** Hosts that are the main site itself besides the root (the app URL, local tooling). */
    private static function mainHosts(): array
    {
        $appHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));

        return array_values(array_filter(array_unique(array_merge(
            [$appHost, 'www.' . self::root(), 'localhost', '127.0.0.1'],
            array_map('strtolower', (array) config('services.hostinger.main_hosts', []))
        ))));
    }

    private static function partnerExists(Domain $domain): bool
    {
        return $domain->partner_id && DB::table('partners')->where('id', $domain->partner_id)->exists();
    }

    // ---- verification ------------------------------------------------------

    public static function txtName(string $domain): string
    {
        return self::TXT_LABEL . '.' . $domain;
    }

    public static function txtValue(string $token): string
    {
        return self::TXT_PREFIX . $token;
    }

    /** Proof the routing check expects from RecruitmentCV for a nonce (keyed with the domain's secret token). */
    public static function challengeProof(string $token, string $nonce): string
    {
        return hash_hmac('sha256', $nonce, $token);
    }
}
