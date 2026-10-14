<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * "Recruitmentcv Partner (Global)" - the partner-wide website settings
 * scope. Twin file, identical in the CRM and RecruitmentCV.
 *
 * Every partner website resolves each setting field by field:
 *
 *   the partner's own value -> Partner Global -> recruitmentcv.com (global)
 *   -> the built-in default
 *
 * (prices: partner -> Partner Global -> the configured default, as before
 * partner sites never used the recruitmentcv.com prices). The
 * recruitmentcv.com website itself never reads this scope, and editing it
 * never touches recruitmentcv.com's own settings or any partner's rows.
 *
 * Storage (existing tables, no schema change):
 * - website settings rows (partner_page_contents): partner_id NULL with the
 *   page key prefixed "pg:" (pg:home, pg:contact, pg:branding, pg:whatsapp,
 *   pg:company, pg:how_it_works ...) - one row per page through the
 *   existing central_page unique index; same content shapes as the other
 *   scopes (pg:company = {name, name_ar, address, address_ar, mobile, email};
 *   pg:branding also holds the header/footer/favicon files that partners
 *   keep on their domains row).
 * - prices (partner_prices): partner_id 0 (partner_prices has no foreign key;
 *   unique per exp_type + proff_id through the existing unique index).
 * Edited in CRM -> Website with the website selector on
 * "Recruitmentcv Partner (Global)" (?partner=partner-global).
 */
final class PartnerGlobal
{
    /** The CRM website selector / query value of this scope. */
    public const KEY = 'partner-global';

    public const LABEL = 'Recruitmentcv Partner (Global)';

    public const PAGE_PREFIX = 'pg:';

    /** partner_prices.partner_id of this scope. */
    public const PRICE_PARTNER_ID = 0;

    /** Short memo of the rows (one web request reads several; the CRM queue worker is long-running). */
    private static array $memo = [];

    private const MEMO_SECONDS = 5;

    public static function page(string $page): string
    {
        return self::PAGE_PREFIX . $page;
    }

    /** This scope's saved row of a page (whole content JSON), [] when none. */
    public static function content(string $page): array
    {
        $hit = self::$memo[$page] ?? null;
        if ($hit && $hit[0] > microtime(true)) {
            return $hit[1];
        }
        $raw = DB::table('partner_page_contents')->whereNull('partner_id')->where('page', self::page($page))->value('content');
        $content = is_string($raw) ? (json_decode($raw, true) ?: []) : [];
        self::$memo[$page] = [microtime(true) + self::MEMO_SECONDS, $content];

        return $content;
    }

    /** This scope's values of a page in one language, without empty ones. */
    public static function localeContent(string $page, string $locale): array
    {
        return self::filled(self::content($page)[$locale] ?? []);
    }

    /** A list saved in this scope (branches, steps, plans), or null when it has none of its own. */
    public static function list(string $page, string $key): ?array
    {
        $list = self::content($page)[$key] ?? null;

        return is_array($list) ? array_values($list) : null;
    }

    /**
     * Values without empty ones (recursively): null, '' and whitespace-only
     * strings are dropped so the next layer shows through; 0 / false stay.
     * Lists (numeric arrays) are kept whole.
     */
    public static function filled(array $values): array
    {
        $out = [];
        foreach ($values as $key => $value) {
            if (is_array($value) && $value !== [] && array_keys($value) !== range(0, count($value) - 1)) {
                $value = self::filled($value);
                if ($value === []) {
                    continue;
                }
            } elseif ($value === null || (is_string($value) && trim($value) === '')) {
                continue;
            }
            $out[$key] = $value;
        }

        return $out;
    }

    /** Is this text value empty (falls back)? */
    public static function blank($value): bool
    {
        return $value === null || (is_string($value) && trim($value) === '');
    }

    public static function forget(): void
    {
        self::$memo = [];
    }
}
