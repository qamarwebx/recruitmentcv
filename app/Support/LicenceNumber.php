<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Recruitment Licence Number (partners.licence_number) - ONE rule for every
 * place it is saved (twin file, identical in the CRM and RecruitmentCV):
 * CRM -> Partner -> Account / Information, and the Partner Portal's
 * Account page + Account Details prompt (recruitmentcv.com, every partner
 * subdomain and own domain - all the same endpoint).
 *
 * - normalize(): surrounding whitespace (incl. Unicode spaces) trimmed; an
 *   empty value is "not provided" (null). Stored as typed otherwise (no
 *   casing rule is defined for the number).
 * - unique across ALL partners, compared case-insensitively on the
 *   normalized value (the column's own collation is case-insensitive) - so
 *   " AB123 " and "ab123" are the same number.
 * - checked when the number CHANGES (the CRM's existing rule for login
 *   identifiers): a record can always be saved with its own current number,
 *   and a number used by another partner can never be newly set.
 * - saveExclusively(): the check + save run under one database lock shared
 *   by both apps, so two simultaneous requests can't both set the same
 *   number. The unique index on the generated licence_number_key column
 *   (migration 2026_10_08_120000) is the database-level guarantee once the
 *   pre-existing duplicates are resolved.
 */
final class LicenceNumber
{
    public const MESSAGE = 'This Recruitment Licence Number is already in use.';

    private const LOCK = 'partners_licence_number';

    public static function normalize($value): ?string
    {
        $text = preg_replace('/^[\s\p{Z}]+|[\s\p{Z}]+$/u', '', (string) $value);

        return $text === '' || $text === null ? null : $text;
    }

    /** Comparison key (normalized, upper-case); null = no number. */
    public static function key($value): ?string
    {
        $text = self::normalize($value);

        return $text === null ? null : mb_strtoupper($text);
    }

    /** Would $value be a change for a partner whose current number is $current? */
    public static function changes($value, $current): bool
    {
        return self::key($value) !== self::key($current);
    }

    /** Is the number already another partner's ($exceptPartnerId = the record being saved)? */
    public static function takenByOther($value, ?int $exceptPartnerId): bool
    {
        $key = self::key($value);
        if ($key === null) {
            return false;
        }

        return DB::table('partners')
            ->whereNotNull('licence_number')
            ->when($exceptPartnerId, fn ($q) => $q->where('id', '!=', $exceptPartnerId))
            ->whereRaw('UPPER(TRIM(licence_number)) = ?', [$key])
            ->exists();
    }

    /** The validation check for a save: an error message, or null. */
    public static function error($value, $current, ?int $exceptPartnerId): ?string
    {
        return self::changes($value, $current) && self::takenByOther($value, $exceptPartnerId) ? self::MESSAGE : null;
    }

    /**
     * Runs $save with the check repeated under the shared lock (race-safe).
     * Returns false - nothing saved - when the number was taken in the
     * meantime (or the lock could not be obtained).
     */
    public static function saveExclusively($value, $current, ?int $exceptPartnerId, callable $save): bool
    {
        if (!self::changes($value, $current) || self::key($value) === null) {
            $save();

            return true;
        }

        $locked = (int) (DB::selectOne('SELECT GET_LOCK(?, 10) AS l', [self::LOCK])->l ?? 0) === 1;
        if (!$locked) {
            return false;
        }
        try {
            if (self::takenByOther($value, $exceptPartnerId)) {
                return false;
            }
            $save();

            return true;
        } finally {
            DB::selectOne('SELECT RELEASE_LOCK(?) AS r', [self::LOCK]);
        }
    }
}
