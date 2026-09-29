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
