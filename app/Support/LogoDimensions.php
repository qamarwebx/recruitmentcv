<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Logo Width & Height per logo - ONE definition and effective-value
 * resolver for both apps (twin file, identical in the CRM and RecruitmentCV).
 *
 * Seven logos, each with its own width + height (px), independent of the
 * others: Website Header (English / Arabic), Website Footer (English /
 * Arabic), Portal (English / Arabic) and the Favicon (square). Field keys:
 * header_logo_* / footer_logo_* / portal_logo_* = English (the header and
 * footer keys are the ones used before the Arabic logos had their own),
 * *_ar_* = Arabic, favicon_*.
 *
 * Stored on the existing 'branding' settings rows (partner_page_contents,
 * the same rows BrandingAssets keeps logos in - no schema change): the
 * central row (partner_id NULL) = global (recruitmentcv.com), 'pg:branding'
 * = Recruitmentcv Partner (Global), a partner's row only its own overrides.
 * Plain numbers (px).
 *
 * Effective value per field: partner -> Partner Global -> global -> system
 * default. The values are the MAXIMUM display area: the shared .w-brand-logo
 * rule (style.css) fits the logo inside with object-fit: contain - never
 * stretched, cropped or resized on disk - and still scales it down on tablet
 * / phone. The page publishes the sizes of ITS language as CSS variables
 * (cssVariables()). The favicon's size is its <link rel="icon" sizes> hint
 * (browsers draw tab icons at their own size).
 */
final class LogoDimensions
{
    /** field => [label, min px, max px, default px]. */
    public const FIELDS = [
        'header_logo_width' => ['Header Logo Width (English)', 40, 400, 300],
        'header_logo_height' => ['Header Logo Height (English)', 20, 70, 52],
        'header_logo_ar_width' => ['Header Logo Width (Arabic)', 40, 400, 300],
        'header_logo_ar_height' => ['Header Logo Height (Arabic)', 20, 70, 52],
        'footer_logo_width' => ['Footer Logo Width (English)', 40, 400, 220],
        'footer_logo_height' => ['Footer Logo Height (English)', 20, 150, 34],
        'footer_logo_ar_width' => ['Footer Logo Width (Arabic)', 40, 400, 220],
        'footer_logo_ar_height' => ['Footer Logo Height (Arabic)', 20, 150, 34],
        'portal_logo_width' => ['Portal Logo Width (English)', 40, 240, 170],
        'portal_logo_height' => ['Portal Logo Height (English)', 16, 60, 30],
        'portal_logo_ar_width' => ['Portal Logo Width (Arabic)', 40, 240, 170],
        'portal_logo_ar_height' => ['Portal Logo Height (Arabic)', 16, 60, 30],
        'favicon_width' => ['Favicon Width', 16, 512, 32],
        'favicon_height' => ['Favicon Height', 16, 512, 32],
    ];

    /** Branding slot (App\Support\BrandingAssets::SLOTS) => [width field, height field]. */
    public const SLOT_FIELDS = [
        'header_logo_en' => ['header_logo_width', 'header_logo_height'],
        'header_logo_ar' => ['header_logo_ar_width', 'header_logo_ar_height'],
        'footer_logo_en' => ['footer_logo_width', 'footer_logo_height'],
        'footer_logo_ar' => ['footer_logo_ar_width', 'footer_logo_ar_height'],
        'portal_logo_en' => ['portal_logo_width', 'portal_logo_height'],
        'portal_logo_ar' => ['portal_logo_ar_width', 'portal_logo_ar_height'],
        'favicon' => ['favicon_width', 'favicon_height'],
    ];

    /**
     * The CSS custom properties a page reads (style.css / portal.css), per page language:
     * var => [English field, Arabic field].
     */
    public const CSS_VARS = [
        '--w-header-logo-w' => ['header_logo_width', 'header_logo_ar_width'],
        '--w-header-logo-h' => ['header_logo_height', 'header_logo_ar_height'],
        '--w-footer-logo-w' => ['footer_logo_width', 'footer_logo_ar_width'],
        '--w-footer-logo-h' => ['footer_logo_height', 'footer_logo_ar_height'],
        '--w-portal-logo-w' => ['portal_logo_width', 'portal_logo_ar_width'],
        '--w-portal-logo-h' => ['portal_logo_height', 'portal_logo_ar_height'],
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

    /** The system defaults (field => px). */
    public static function defaults(): array
    {
        return array_map(fn ($f) => $f[3], self::FIELDS);
    }

    /**
     * Effective values for a context: ['values' => field => px, 'sources' => field => partner|partner_global|global|default].
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

    /**
     * Validation rules (empty = not set / inherit). The favicon is square: its width and
     * height are set together and must be equal.
     */
    public static function rules(): array
    {
        $rules = [];
        foreach (self::FIELDS as $field => [, $min, $max]) {
            $rules[$field] = ['nullable', 'integer', 'min:' . $min, 'max:' . $max];
        }
        $rules['favicon_width'][] = 'same:favicon_height';
        $rules['favicon_height'][] = 'same:favicon_width';

        return $rules;
    }

    /** Field labels for validation messages. */
    public static function labels(): array
    {
        return array_map(fn ($f) => $f[0], self::FIELDS);
    }

    /** Custom messages (the favicon must stay square). */
    public static function messages(): array
    {
        return [
            'favicon_width.same' => 'The favicon is square: enter the same Favicon Width and Height (or leave both empty).',
            'favicon_height.same' => 'The favicon is square: enter the same Favicon Width and Height (or leave both empty).',
        ];
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
        if ($partnerGlobalScope) {
            return self::effective(null, true) + ['own' => self::partnerGlobal(), 'fallback' => self::globalInherited()];
        }

        return self::effective($partnerId) + [
            'own' => self::own($partnerId),
            'fallback' => $partnerId ? self::inherited() : self::defaults(),
        ];
    }

    /** CSS custom property => "123px" for the page language ($locale null = the current one), from effective values. */
    public static function cssValues(array $values, ?string $locale = null): array
    {
        $arabic = ($locale ?? app()->getLocale()) === 'ar';
        $out = [];
        foreach (self::CSS_VARS as $var => [$english, $arabicField]) {
            $out[$var] = (int) $values[$arabic ? $arabicField : $english] . 'px';
        }

        return $out;
    }

    /** The effective values for this page's language as CSS custom properties ("--w-header-logo-w:300px;..."), numbers only. */
    public static function cssVariables(?int $partnerId, ?string $locale = null): string
    {
        $out = '';
        foreach (self::cssValues(self::effective($partnerId)['values'], $locale) as $var => $px) {
            $out .= $var . ':' . $px . ';';
        }

        return $out;
    }

    /** The favicon's "sizes" hint for <link rel="icon"> (e.g. "32x32"). */
    public static function faviconSizes(?int $partnerId): string
    {
        $values = self::effective($partnerId)['values'];

        return (int) $values['favicon_width'] . 'x' . (int) $values['favicon_height'];
    }
}
