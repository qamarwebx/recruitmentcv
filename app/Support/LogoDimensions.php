<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Header / Footer Logo Width & Height - ONE definition and effective-value
 * resolver for both apps (twin file, identical in the CRM and RecruitmentCV).
 *
 * Stored on the existing 'branding' settings rows (partner_page_contents,
 * the same rows BrandingAssets keeps the footer logos in): the central row
 * (partner_id NULL, CRM -> Website -> Global) holds the global values, a
 * partner's row only its own overrides (Partner Website -> Branding, or
 * CRM -> Website with that partner selected). Plain numbers (px).
 *
 * Effective value per field: partner override -> global -> system default
 * (the sizes the site used before this setting existed). The values are
 * the MAXIMUM display area of the logo: the shared .w-brand-logo rule
 * (style.css) fits the logo inside it with object-fit: contain - never
 * stretched, cropped or resized on disk - and still scales it down on
 * tablet / phone so the header never breaks. Header height is capped at
 * 70px because the website header itself is a fixed 76px.
 */
final class LogoDimensions
{
    /** field => [label, min px, max px, default px]. */
    public const FIELDS = [
        'header_logo_width' => ['Header Logo Width', 40, 400, 300],
        'header_logo_height' => ['Header Logo Height', 20, 70, 52],
        'footer_logo_width' => ['Footer Logo Width', 40, 400, 220],
        'footer_logo_height' => ['Footer Logo Height', 20, 150, 34],
    ];

    /** The CSS custom property each field is published as (read by style.css / portal.css). */
    public const CSS_VARS = [
        'header_logo_width' => '--w-header-logo-w',
        'header_logo_height' => '--w-header-logo-h',
        'footer_logo_width' => '--w-footer-logo-w',
        'footer_logo_height' => '--w-footer-logo-h',
    ];

    private const PAGE = 'branding';

    /** One context's own saved values (null = not set); $partnerId null = the global row. */
    public static function own(?int $partnerId): array
    {
        $content = DB::table('partner_page_contents')
            ->where('page', self::PAGE)
            ->when($partnerId, fn ($q) => $q->where('partner_id', $partnerId), fn ($q) => $q->whereNull('partner_id'))
            ->value('content');
        $saved = is_string($content) ? (json_decode($content, true) ?: []) : [];

        return self::valid($saved);
    }

    /** Saved sizes -> field => int|null (only whole numbers within the field's range). */
    private static function valid(array $saved): array
    {
        $values = [];
        foreach (self::FIELDS as $field => [, $min, $max]) {
            $value = $saved[$field] ?? null;
            $values[$field] = is_numeric($value) && (int) $value >= $min && (int) $value <= $max ? (int) $value : null;
        }

        return $values;
    }

    /** What a partner without its own value gets: the global value, else the default. */
    /** "Recruitmentcv Partner (Global)" sizes ('pg:branding' row; null = not set there). */
    public static function partnerGlobal(): array
    {
        return self::valid(PartnerGlobal::content(self::PAGE));
    }

    /** What a partner falls back to: Partner Global -> global -> system default. */
    public static function inherited(): array
    {
        $global = self::own(null);
        $partnerGlobal = self::partnerGlobal();
        $values = [];
        foreach (self::FIELDS as $field => [, , , $default]) {
            $values[$field] = $partnerGlobal[$field] ?? $global[$field] ?? $default;
        }

        return $values;
    }

    /** What the Partner Global scope falls back to: global -> system default. */
    public static function globalInherited(): array
    {
        $global = self::own(null);
        $values = [];
        foreach (self::FIELDS as $field => [, , , $default]) {
            $values[$field] = $global[$field] ?? $default;
        }

        return $values;
    }

    /**
     * Effective values for a context: ['values' => field => px, 'sources' => field => partner|global|default].
     * $partnerId null = the main site (global -> default).
     */
    public static function effective(?int $partnerId, bool $partnerGlobalScope = false): array
    {
        $global = self::own(null);
        $none = array_fill_keys(array_keys(self::FIELDS), null);
        $own = $partnerId ? self::own($partnerId) : $none;
        // Partners (and the Partner Global editor itself): Partner Global before global.
        $partnerGlobal = ($partnerId || $partnerGlobalScope) ? self::partnerGlobal() : $none;
        $values = $sources = [];
        foreach (self::FIELDS as $field => [, , , $default]) {
            if ($own[$field] !== null) {
                [$values[$field], $sources[$field]] = [$own[$field], 'partner'];
            } elseif ($partnerGlobal[$field] !== null) {
                [$values[$field], $sources[$field]] = [$partnerGlobal[$field], 'partner_global'];
            } elseif ($global[$field] !== null) {
                [$values[$field], $sources[$field]] = [$global[$field], 'global'];
            } else {
                [$values[$field], $sources[$field]] = [$default, 'default'];
            }
        }

        return ['values' => $values, 'sources' => $sources];
    }

    /** Validation rules (empty = not set / inherit). */
    public static function rules(): array
    {
        $rules = [];
        foreach (self::FIELDS as $field => [, $min, $max]) {
            $rules[$field] = ['nullable', 'integer', 'min:' . $min, 'max:' . $max];
        }

        return $rules;
    }

    /** Field labels for validation messages. */
    public static function labels(): array
    {
        return array_map(fn ($f) => $f[0], self::FIELDS);
    }

    /**
     * What to store for a context from the submitted (validated) fields that
     * are present: field => int, or null = remove (inherit). A partner keeps
     * only real overrides - an empty value, or one equal to what it would
     * inherit anyway, is removed. Global: empty = back to the default.
     * Only fields whose stored value actually changes are returned, so an
     * empty result = nothing to save.
     */
    public static function changes(?int $partnerId, array $input, bool $partnerGlobalScope = false): array
    {
        // Partner: values equal to what it inherits are not overrides; the
        // Partner Global scope likewise against global -> default.
        $inherited = $partnerGlobalScope ? self::globalInherited() : ($partnerId ? self::inherited() : null);
        $own = $partnerGlobalScope ? self::partnerGlobal() : self::own($partnerId);
        $changes = [];
        foreach (array_keys(self::FIELDS) as $field) {
            if (!array_key_exists($field, $input)) {
                continue;
            }
            $value = $input[$field] === null || $input[$field] === '' ? null : (int) $input[$field];
            if ($inherited !== null && $value !== null && $value === $inherited[$field]) {
                $value = null;
            }
            if ($value !== $own[$field]) {
                $changes[$field] = $value;
            }
        }

        return $changes;
    }

    /** For a settings form: effective values + sources, this context's own values, and what an empty field falls back to. */
    public static function formData(?int $partnerId, bool $partnerGlobalScope = false): array
    {
        $defaults = array_map(fn ($f) => $f[3], self::FIELDS);

        if ($partnerGlobalScope) {
            return self::effective(null, true) + ['own' => self::partnerGlobal(), 'fallback' => self::globalInherited()];
        }

        return self::effective($partnerId) + [
            'own' => self::own($partnerId),
            'fallback' => $partnerId ? self::inherited() : $defaults,
        ];
    }

    /** The effective values as CSS custom properties ("--w-header-logo-w:300px;..."), numbers only. */
    public static function cssVariables(?int $partnerId): string
    {
        $values = self::effective($partnerId)['values'];
        $out = '';
        foreach (self::CSS_VARS as $field => $var) {
            $out .= $var . ':' . (int) $values[$field] . 'px;';
        }

        return $out;
    }
}
