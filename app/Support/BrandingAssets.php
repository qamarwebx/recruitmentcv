<?php

namespace App\Support;

use App\Models\Domain;
use App\Models\PartnerPageContent;

/**
 * The website branding images - header logo EN/AR, footer logo EN/AR,
 * favicon - and the one place their effective file is resolved:
 *
 *   partner's own -> global (recruitmentcv.com) -> built-in file
 *
 * (a partner's footer logo falls back to that partner's header logo first,
 * as partner sites always showed). Storage reuses what exists:
 * - global: the central 'branding' row (partner_page_contents, partner_id NULL);
 * - partner header logos + favicon: its domains row (website_logo /
 *   website_logo_ar / website_favicon - shared with the Partner Portal's
 *   Branding tab and the CRM partner profile);
 * - partner footer logos: its own 'branding' row.
 * All files live in the shared admin/assets/images/partner folder. Edited
 * from CRM -> Website -> Company Profile & Branding. Kept in step with the
 * CRM's twin of this class.
 */
class BrandingAssets
{
    public const SLOTS = [
        'header_logo_en' => 'Header Logo (EN)',
        'header_logo_ar' => 'Header Logo (AR)',
        'footer_logo_en' => 'Footer Logo (EN)',
        'footer_logo_ar' => 'Footer Logo (AR)',
        'favicon' => 'Favicon',
    ];

    /** The built-in files used before any upload (unchanged from before). */
    public const DEFAULT_FILES = [
        'header_logo_en' => 'user/img/logo/Logo_eng_dark.webp',
        'header_logo_ar' => 'user/img/logo/new_logo_Arabic.png',
        'footer_logo_en' => 'user/img/logo/Logo_eng_white.webp',
        'footer_logo_ar' => 'user/img/logo/new_logo_Arabic_white.webp',
        'favicon' => 'user/img/favicon.png',
    ];

    /** Partner slots stored on the partner's domains row. */
    public const DOMAIN_COLUMNS = [
        'header_logo_en' => 'website_logo',
        'header_logo_ar' => 'website_logo_ar',
        'favicon' => 'website_favicon',
    ];

    public const DIRECTORY = 'admin/assets/images/partner';

    public const PAGE = 'branding';

    /** Per-request cache of own() per context. */
    private static array $cache = [];

    /** One context's own uploaded file per slot (null = not set); null partner = global. */
    public static function own(?int $partnerId): array
    {
        $key = (int) $partnerId;

        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        $content = PartnerPageContent::query()
            ->where('page', static::PAGE)
            ->when($partnerId, fn ($q) => $q->where('partner_id', $partnerId), fn ($q) => $q->whereNull('partner_id'))
            ->first()->content ?? [];

        $files = [];
        foreach (array_keys(static::SLOTS) as $slot) {
            $files[$slot] = static::clean($content[$slot] ?? null);
        }

        if ($partnerId) {
            $domain = Domain::where('partner_id', $partnerId)->first();
            foreach (static::DOMAIN_COLUMNS as $slot => $column) {
                $files[$slot] = static::clean(optional($domain)->{$column});
            }
        }

        return static::$cache[$key] = $files;
    }

    /**
     * Effective file for a slot, as [relative public path, source] -
     * source = partner | partner_header | global | default. A saved file
     * missing on disk is skipped, so the URL is never broken.
     */
    public static function resolve(string $slot, ?int $partnerId): array
    {
        $candidates = [];

        if ($partnerId) {
            $own = static::own($partnerId);
            $candidates[] = [$own[$slot], 'partner'];
            if (str_starts_with($slot, 'footer_')) {
                $candidates[] = [$own[str_replace('footer_', 'header_', $slot)], 'partner_header'];
            }
        }

        $candidates[] = [static::own(null)[$slot], 'global'];

        foreach ($candidates as [$file, $source]) {
            if ($file && is_file(public_path(static::DIRECTORY . '/' . $file))) {
                return [static::DIRECTORY . '/' . $file, $source];
            }
        }

        return [static::DEFAULT_FILES[$slot], 'default'];
    }

    /** Public URL of a slot's effective file, versioned by its mtime. */
    public static function url(string $slot, ?int $partnerId): string
    {
        [$relative] = static::resolve($slot, $partnerId);
        $version = @filemtime(public_path($relative));

        return asset($relative) . ($version ? '?v=' . $version : '');
    }

    public static function forget(): void
    {
        static::$cache = [];
    }

    private static function clean($file): ?string
    {
        $file = basename(trim((string) $file));

        return ($file === '' || $file === '.' || $file === '..') ? null : $file;
    }
}
