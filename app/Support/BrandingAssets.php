<?php

namespace App\Support;

use App\Models\Domain;
use App\Models\PartnerPageContent;

/**
 * The branding images - website header logo EN/AR, website footer logo
 * EN/AR, portal logo EN/AR, favicon - and the one place their effective
 * file is resolved:
 *
 *   partner's own -> Recruitmentcv Partner (Global) -> global (recruitmentcv.com) -> built-in file
 *
 * (a partner's footer logo falls back to that partner's header logo first,
 * as partner sites always showed; a portal logo falls back to the partner's
 * header logo first - what the portal sidebar always showed - and, after the
 * global portal logo, to the site's footer logo). Sizes:
 * App\Support\LogoDimensions. Storage reuses what exists:
 * - global: the central 'branding' row (partner_page_contents, partner_id NULL);
 * - partner header logos + favicon: its domains row (website_logo /
 *   website_logo_ar / website_favicon - shared with the Partner Portal's
 *   Branding tab and the CRM partner profile);
 * - partner footer + portal logos: its own 'branding' row.
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
        'portal_logo_en' => 'Portal Logo (EN)',
        'portal_logo_ar' => 'Portal Logo (AR)',
        'favicon' => 'Favicon',
    ];

    /** Settings sections (Website Header, Website Footer, Portal Branding, Favicon) => slots. */
    public const SECTIONS = [
        'Website Header' => ['header_logo_en', 'header_logo_ar'],
        'Website Footer' => ['footer_logo_en', 'footer_logo_ar'],
        'Portal Branding' => ['portal_logo_en', 'portal_logo_ar'],
        'Favicon' => ['favicon'],
    ];

    /** Slots shown on a dark background (footer + portal sidebar). */
    public const DARK_SLOTS = ['footer_logo_en', 'footer_logo_ar', 'portal_logo_en', 'portal_logo_ar'];

    /** Upload file-name suffix per slot (Domain::newBrandingFileName()). */
    public const FILE_SUFFIXES = [
        'header_logo_en' => 'en', 'header_logo_ar' => 'ar', 'footer_logo_en' => 'footer_en', 'footer_logo_ar' => 'footer_ar',
        'portal_logo_en' => 'portal_en', 'portal_logo_ar' => 'portal_ar', 'favicon' => 'fav',
    ];

    /** The built-in files used before any upload (unchanged from before). */
    public const DEFAULT_FILES = [
        'header_logo_en' => 'user/img/logo/Logo_eng_dark.webp',
        'header_logo_ar' => 'user/img/logo/new_logo_Arabic.png',
        'footer_logo_en' => 'user/img/logo/Logo_eng_white.webp',
        'footer_logo_ar' => 'user/img/logo/new_logo_Arabic_white.webp',
        'portal_logo_en' => 'user/img/logo/Logo_eng_white.webp',
        'portal_logo_ar' => 'user/img/logo/new_logo_Arabic_white.webp',
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
     * source = partner | partner_header | partner_global | global | footer |
     * default. A saved file missing on disk is skipped, so the URL is never broken.
     */
    public static function resolve(string $slot, ?int $partnerId): array
    {
        $candidates = [];

        if ($partnerId) {
            $own = static::own($partnerId);
            $candidates[] = [$own[$slot], 'partner'];
            // Footer and portal logos fall back to the partner's header logo first.
            if (str_starts_with($slot, 'footer_') || str_starts_with($slot, 'portal_')) {
                $candidates[] = [$own[preg_replace('/^(footer|portal)_/', 'header_', $slot)], 'partner_header'];
            }
        }

        if ($partnerId) {
            // "Recruitmentcv Partner (Global)" (App\Support\PartnerGlobal) before the global image.
            $candidates[] = [static::partnerGlobal()[$slot], 'partner_global'];
        }
        $candidates[] = [static::own(null)[$slot], 'global'];

        foreach ($candidates as [$file, $source]) {
            if ($file && is_file(public_path(static::DIRECTORY . '/' . $file))) {
                return [static::DIRECTORY . '/' . $file, $source];
            }
        }

        // No portal logo anywhere: the site's footer logo (same language), as the sidebar showed before.
        if (str_starts_with($slot, 'portal_')) {
            [$footer, $footerSource] = static::resolve(str_replace('portal_', 'footer_', $slot), $partnerId);
            if ($footerSource !== 'default') {
                return [$footer, 'footer'];
            }
        }

        return [static::DEFAULT_FILES[$slot], 'default'];
    }

    /**
     * Server-side image size check for an inspected upload (App\Support\ImageUpload):
     * the favicon must be square, 16-1024 px; a logo 16-6000 px per side. SVGs
     * scale freely (no pixel size) and pass. Null = fine, else the reason.
     */
    public static function uploadDimensionError(array $upload, string $slot): ?string
    {
        if (($upload['extension'] ?? '') === 'svg') {
            return null;
        }
        $size = @getimagesizefromstring($upload['content'] ?? '');
        if (!$size) {
            return 'The image could not be read.';
        }
        [$width, $height] = $size;
        if ($slot === 'favicon') {
            $square = abs($width - $height) <= max(1, (int) round(max($width, $height) * 0.02));

            return $square && min($width, $height) >= 16 && max($width, $height) <= 1024 ? null
                : "The favicon must be a square image between 16 and 1024 px (e.g. 64 × 64 px); this one is {$width} × {$height} px.";
        }

        return min($width, $height) >= 16 && max($width, $height) <= 6000 ? null
            : "The logo must be between 16 and 6000 px on each side; this one is {$width} × {$height} px.";
    }

    /** "Recruitmentcv Partner (Global)" uploaded files per slot ('pg:branding' row; null = not set). */
    public static function partnerGlobal(): array
    {
        $content = \App\Support\PartnerGlobal::content(static::PAGE);
        $files = [];
        foreach (array_keys(static::SLOTS) as $slot) {
            $files[$slot] = static::clean($content[$slot] ?? null);
        }

        return $files;
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
