<?php

namespace App\Support;

/**
 * Comparing phone numbers typed/stored in different formats. Records are
 * stored two ways in the shared DB: CRM-created partners keep country code +
 * number together in owner_mobile_no ("919327063401"), numbers entered on
 * this site keep only the local part ("9327063401", code stored separately).
 * These helpers only COMPARE - nothing is re-stored.
 */
class PhoneNumber
{
    /** Local-number length per country code (the site's 5 countries). */
    private const LOCAL_LENGTH = ['966' => 9, '91' => 10, '965' => 8, '974' => 8, '971' => 9];

    /**
     * Local number: digits only, no leading zeros, and without the country
     * code if it was typed in front. The code is only stripped when the total
     * length is exactly code + that country's local length, so a local number
     * that merely starts with the same digits (e.g. Indian 91xxxxxxxx) is kept.
     */
    public static function local(?string $countryCode, ?string $mobile): string
    {
        $digits = ltrim(preg_replace('/\D+/', '', (string) $mobile), '0');
        $code = preg_replace('/\D+/', '', (string) $countryCode);

        if ($code === '' || !str_starts_with($digits, $code)) {
            return $digits;
        }

        $expected = self::LOCAL_LENGTH[$code] ?? null;
        $remaining = strlen($digits) - strlen($code);

        if ($expected !== null ? $remaining === $expected : $remaining >= 9) {
            return substr($digits, strlen($code));
        }

        return $digits;
    }

    /**
     * Display form "+<code> <local>" of a stored number (display only -
     * nothing is re-stored). Numbers stored CRM-style already carry their
     * code: the given (partner's) code is recognized first, then any known
     * code whose remainder - trunk 0 dropped - is exactly that country's
     * local length (local numbers are never that long). Otherwise the given
     * code is put in front; with no code known the number is shown as stored.
     */
    public static function international(?string $countryCode, ?string $mobile): string
    {
        [$code, $local] = self::split($countryCode, $mobile);
        if ($local === '') {
            return '';
        }

        return $code === null ? trim((string) $mobile) : '+' . $code . ' ' . $local;
    }

    /**
     * [code, local number] of a stored number, by the same rules as
     * international(): code null when none is known (local = digits as
     * stored); local '' when there is no number.
     */
    public static function split(?string $countryCode, ?string $mobile): array
    {
        $digits = preg_replace('/\D+/', '', (string) $mobile);
        $code = preg_replace('/\D+/', '', (string) $countryCode);
        if ($digits === '') {
            return [$code !== '' ? $code : null, ''];
        }

        $candidates = array_unique(array_merge([$code], array_map('strval', array_keys(self::LOCAL_LENGTH))));
        foreach ($candidates as $candidate) {
            $expected = self::LOCAL_LENGTH[$candidate] ?? null;
            if ($expected === null || !str_starts_with($digits, $candidate)) {
                continue;
            }
            $rest = ltrim(substr($digits, strlen($candidate)), '0');
            if (strlen($rest) === $expected) {
                return [$candidate, $rest];
            }
        }

        if ($code === '') {
            return [null, $digits];
        }

        return [$code, self::local($code, $digits)];
    }

    /**
     * Every stored form the same number can have: local, code + local, and
     * the same with the national trunk 0 kept (e.g. CRM "9660569990906").
     */
    public static function storedForms(?string $countryCode, ?string $mobile): array
    {
        $local = self::local($countryCode, $mobile);
        if ($local === '') {
            return [];
        }

        $code = preg_replace('/\D+/', '', (string) $countryCode);
        $forms = [$local, '0' . $local];
        if ($code !== '') {
            $forms[] = $code . $local;
            $forms[] = $code . '0' . $local;
        }

        return array_values(array_unique($forms));
    }
}
