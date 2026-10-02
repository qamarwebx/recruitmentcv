<?php

namespace App\Support;

class CandidatePhoto
{
    /**
     * Candidate images are served from the current RecruitmentCV site's own
     * host (asset(), which also honours ASSET_URL if it is ever set).
     * public/admin/assets is a symlink to the CRM's own admin/assets folder,
     * so admin/assets/images/candidate/ here is the very directory the CRM
     * uploads candidate photos to - always current, never a copy. Paths
     * mirror the CRM's own markup:
     *   asset('admin/assets/images/candidate/'.$post->photo_file)
     *   asset('admin/assets/img/avatars/blank.jpeg') // fallback
     */
    private const CANDIDATE_DIR = 'admin/assets/images/candidate/';
    private const DEFAULT_AVATAR = 'admin/assets/img/avatars/blank.jpeg';
    // The CRM's blue "verified" mark (same file qamarhire.com shows after a
    // candidate's name), from the same shared candidate folder.
    private const VERIFIED_ICON = 'admin/assets/images/candidate/cadidate-verified.svg';

    /**
     * Resolve a candidate image filename to its public URL on the current
     * site, falling back to the default avatar when the filename is empty.
     */
    public static function url(?string $filename): string
    {
        if (!empty($filename)) {
            return asset(self::CANDIDATE_DIR . $filename);
        }

        return self::defaultUrl();
    }

    public static function defaultUrl(): string
    {
        return asset(self::DEFAULT_AVATAR);
    }

    public static function verifiedIconUrl(): string
    {
        return asset(self::VERIFIED_ICON);
    }

    public static function exists(?string $filename): bool
    {
        return !empty($filename);
    }

    /**
     * Returns the public URL only when a filename is present, otherwise null.
     * Useful for optional document images (passport/license) that should simply be
     * skipped rather than shown as a broken image.
     */
    public static function urlIfExists(?string $filename): ?string
    {
        return self::exists($filename) ? asset(self::CANDIDATE_DIR . $filename) : null;
    }
}
