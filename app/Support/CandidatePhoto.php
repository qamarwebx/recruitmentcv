<?php

namespace App\Support;

class CandidatePhoto
{
    /**
     * Candidate photos are uploaded and stored on the CRM (crm.qamarhire.com);
     * this Worker Portal's own local admin/assets/images/candidate/ copy is
     * only a periodic snapshot that lags behind new CRM uploads, so resolving
     * against it (as this class used to) silently falls back to the default
     * avatar for any recently-added or recently-changed candidate photo. The
     * CRM is the single source of truth, so build the URL against it
     * directly - this mirrors the CRM's own reference markup exactly:
     *   asset('admin/assets/images/candidate/'.$post->photo_file)
     *   asset('admin/assets/img/avatars/blank.jpeg') // fallback
     * just against the CRM's base URL instead of this app's own.
     */
    private const CRM_BASE_URL = 'https://crm.qamarhire.com';
    private const CANDIDATE_DIR = '/admin/assets/images/candidate/';
    private const DEFAULT_AVATAR = '/admin/assets/img/avatars/blank.jpeg';

    /**
     * Resolve a candidate image filename to its CRM public URL, falling back
     * to the default avatar when the filename is empty.
     */
    public static function url(?string $filename): string
    {
        if (!empty($filename)) {
            return self::CRM_BASE_URL . self::CANDIDATE_DIR . $filename;
        }

        return self::defaultUrl();
    }

    public static function defaultUrl(): string
    {
        return self::CRM_BASE_URL . self::DEFAULT_AVATAR;
    }

    public static function exists(?string $filename): bool
    {
        return !empty($filename);
    }

    /**
     * Returns the CRM public URL only when a filename is present, otherwise null.
     * Useful for optional document images (passport/license) that should simply be
     * skipped rather than shown as a broken image.
     */
    public static function urlIfExists(?string $filename): ?string
    {
        return self::exists($filename) ? self::CRM_BASE_URL . self::CANDIDATE_DIR . $filename : null;
    }
}
