<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Company Profile + "Append Company Name" - the ONE definition and
 * effective-value calculation for both apps (twin file: identical in the
 * CRM and RecruitmentCV; never edit only one copy).
 *
 * Company Profile - FIELDS is the order and labels of RecruitmentCV's
 * Partner Website -> Company Profile, shown the same way in CRM ->
 * Website -> Company Profile:
 * - partner value: its domains row (company_name ... company_email), saved
 *   by the Partner Website (and the CRM partner profile);
 * - global value (CRM -> Website -> Global): the Company Name (English /
 *   Arabic) on the central 'company' settings row (partner_page_contents,
 *   partner_id NULL; built-in default DEFAULT_NAME), address / mobile /
 *   email in the shared frontendwebsiteconfigs footer row (GLOBAL_COLUMNS,
 *   also qamarhire.com's footer);
 * - effective = partner value -> global value, field by field.
 *
 * Append Company Name (show the Company Name under the logo):
 * the partner's explicit ON/OFF -> the global value -> OFF, both on the
 * 'branding' settings rows. An absent key = not configured; an explicit
 * OFF is a real value and never falls back.
 */
final class CompanyProfile
{
    /** field => [domains column / form input, label, input type], in the Partner Website order. */
    public const FIELDS = [
        'name' => ['company_name', 'Company Name', 'text'],
        'name_ar' => ['company_name_ar', 'Company Name (Arabic)', 'text'],
        'address' => ['company_address', 'Company Address', 'textarea'],
        'address_ar' => ['company_address_ar', 'Company Address (Arabic)', 'textarea'],
        'mobile' => ['company_mobile', 'Company Mobile', 'text'],
        'email' => ['company_email', 'Company Email', 'email'],
    ];

    /** Global fields kept in the shared frontendwebsiteconfigs row (the name is on the central 'company' row). */
    public const GLOBAL_COLUMNS = [
        'address' => 'bottom_contact_us_addr',
        'address_ar' => 'bottom_contact_us_addr_arabic',
        'mobile' => 'bottom_contact_us_phone',
        'email' => 'bottom_contact_us_email',
    ];

    /** Central settings row holding the global Company Name (English / Arabic). */
    public const PAGE = 'company';

    /** Global Company Name until one is saved (what recruitmentcv.com has always shown). */
    public const DEFAULT_NAME = 'Qamr International';

    public const BRANDING_PAGE = 'branding';

    public const APPEND = 'append_company_name';

    /** Short memo (the CRM queue worker is long-running; a web request is far shorter). */
    private static array $memo = [];

    private const MEMO_SECONDS = 5;

    // ---- Company Profile ---------------------------------------------------

    /** Whether a field is Arabic (right-to-left input). */
    public static function isArabic(string $field): bool
    {
        return str_ends_with($field, '_ar');
    }

    /**
     * Global Company Profile values (field => ?string). Mobile = the footer
     * phone (bottom contact phone, else the contact phone) exactly as the
     * websites already use it. $frontwebsite omitted = the saved row.
     */
    public static function global($frontwebsite = false): array
    {
        $fw = $frontwebsite === false ? self::frontwebsite() : $frontwebsite;
        $saved = self::row(self::PAGE);

        return [
            'name' => self::text($saved['name'] ?? null) ?? self::DEFAULT_NAME,
            'name_ar' => self::text($saved['name_ar'] ?? null),
            'address' => self::text($fw->bottom_contact_us_addr ?? null),
            'address_ar' => self::text($fw->bottom_contact_us_addr_arabic ?? null),
            'mobile' => self::text($fw->bottom_contact_us_phone ?? ($fw->contact_us_phone ?? null)),
            'email' => self::text($fw->bottom_contact_us_email ?? null),
        ];
    }

    /** What the global form edits (saved values only - a blank name means DEFAULT_NAME, a blank mobile the contact phone). */
    public static function globalSaved($frontwebsite = false): array
    {
        $fw = $frontwebsite === false ? self::frontwebsite() : $frontwebsite;
        $saved = self::row(self::PAGE);
        $values = ['name' => self::text($saved['name'] ?? null), 'name_ar' => self::text($saved['name_ar'] ?? null)];
        foreach (self::GLOBAL_COLUMNS as $field => $column) {
            $values[$field] = self::text($fw->{$column} ?? null);
        }

        return $values;
    }

    /** A partner's own values from its domains row (null = not set / no row). */
    public static function own($domain): array
    {
        $values = [];
        foreach (self::FIELDS as $field => [$column]) {
            $values[$field] = self::text(optional($domain)->{$column});
        }

        return $values;
    }

    /**
     * Effective Company Profile of a partner (null domain = the global one):
     * ['values' => field => ?string, 'sources' => field => partner|global].
     */
    public static function effective($domain, $frontwebsite = false): array
    {
        $global = self::global($frontwebsite);
        $own = $domain ? self::own($domain) : array_fill_keys(array_keys(self::FIELDS), null);
        $values = $sources = [];
        foreach (array_keys(self::FIELDS) as $field) {
            $values[$field] = $own[$field] ?? $global[$field];
            $sources[$field] = $own[$field] !== null ? 'partner' : 'global';
        }

        return ['values' => $values, 'sources' => $sources];
    }

    /**
     * Effective Company Name in one language - Company Name (English) for
     * English, Company Name (Arabic) for Arabic, no cross-language mixing:
     * $own (a partner's ['name' => .., 'name_ar' => ..], null = the main
     * site) -> the global name of that language; '' when neither is set.
     */
    public static function nameIn(?array $own, string $locale): string
    {
        $field = $locale === 'ar' ? 'name_ar' : 'name';

        return self::text($own[$field] ?? null) ?? (self::global()[$field] ?? '');
    }

    /** The global Company Name for a language: the Arabic one for 'ar' when set, else the English one. */
    public static function globalName(?string $locale = null): string
    {
        $global = self::global();

        return ($locale ?? app()->getLocale()) === 'ar' && $global['name_ar'] !== null ? $global['name_ar'] : $global['name'];
    }

    // ---- Append Company Name ----------------------------------------------

    /**
     * Effective "Append Company Name" for a partner (null = the global
     * value itself, used by the main site and the CRM Global toggle).
     *
     * @return array{append_company_name: bool, append_company_name_configured: bool, append_company_name_default: bool, append_company_name_source: string}
     *   _configured = this context saved its own value; _default = what it
     *   gets without one; _source = partner | global | default.
     */
    public static function appendSetting(?int $partnerId): array
    {
        $rows = DB::table('partner_page_contents')
            ->where('page', self::BRANDING_PAGE)
            ->where(fn ($q) => $q->whereNull('partner_id')->when($partnerId, fn ($q) => $q->orWhere('partner_id', $partnerId)))
            ->get(['partner_id', 'content']);

        $content = fn (?object $row) => $row ? (json_decode((string) $row->content, true) ?: []) : [];
        $global = self::flag($content($rows->first(fn ($row) => $row->partner_id === null)));
        $own = $partnerId ? self::flag($content($rows->first(fn ($row) => (int) $row->partner_id === $partnerId))) : $global;
        $default = $partnerId ? ($global ?? false) : false;

        return [
            self::APPEND => $own ?? $default,
            self::APPEND . '_configured' => $own !== null,
            self::APPEND . '_default' => $default,
            self::APPEND . '_source' => $own !== null ? ($partnerId ? 'partner' : 'global') : ($global !== null ? 'global' : 'default'),
        ];
    }

    /**
     * The name shown under the logo: '' when the effective setting is off,
     * else nameIn() - the partner's Company Name of the page language,
     * falling back to the global one ($own null = the main site).
     */
    public static function appendedName(?array $own, bool $on, ?string $locale = null): string
    {
        return $on ? self::nameIn($own, $locale ?? app()->getLocale()) : '';
    }

    /** Drops the memo (after a save in the same process, and between in-process test requests). */
    public static function forget(): void
    {
        self::$memo = [];
    }

    // ---- internals -----------------------------------------------------------

    /** A saved true/false (also 1/0, "1"/"0") under APPEND; null = not configured. */
    private static function flag(array $content): ?bool
    {
        if (!array_key_exists(self::APPEND, $content) || $content[self::APPEND] === null) {
            return null;
        }

        return filter_var($content[self::APPEND], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    /** The central settings row's content for a page ([] when there is none). */
    private static function row(string $page): array
    {
        return self::remember('row:' . $page, function () use ($page) {
            $content = DB::table('partner_page_contents')->whereNull('partner_id')->where('page', $page)->value('content');

            return is_string($content) ? (json_decode($content, true) ?: []) : [];
        });
    }

    private static function frontwebsite(): ?object
    {
        return self::remember('frontwebsite', fn () => DB::table('frontendwebsiteconfigs')->first());
    }

    private static function remember(string $key, \Closure $load)
    {
        $now = microtime(true);
        if (!isset(self::$memo[$key]) || $now - self::$memo[$key][0] > self::MEMO_SECONDS) {
            self::$memo[$key] = [$now, $load()];
        }

        return self::$memo[$key][1];
    }

    /** Trimmed text; null when empty. */
    private static function text($value): ?string
    {
        $value = is_scalar($value) ? trim((string) $value) : '';

        return $value !== '' ? $value : null;
    }
}
