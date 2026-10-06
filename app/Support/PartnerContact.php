<?php

namespace App\Support;

use App\Models\Country;

/**
 * A partner's contact fields as the CRM Partner Account tab and the Partner
 * Portal Account page show and save them - the same partners row for both:
 *
 *   Primary Email    = email            (login email; email_verified_at)
 *   Primary Mobile   = owner_mobile_no  + owner_mobile_country_code
 *                      (login / OTP mobile; mobile_verified_at)
 *   Secondary Email  = secondary_email
 *   Secondary Mobile = secondary_mob    + secondary_mob_country_code
 *
 * primary_email / primary_mob are older CRM contact columns, kept as they are.
 * Numbers keep their stored format (code in front, CRM-style, or the local
 * number, RecruitmentCV-style - PhoneNumber); the code columns say which
 * country code the number belongs to. Twin file in both apps.
 */
class PartnerContact
{
    private static ?array $countryCodes = null;

    /** Primary Mobile as [code|null, local number]. */
    public static function primaryMobile(object $partner): array
    {
        return self::mobile($partner->owner_mobile_country_code ?? null, $partner->owner_mobile_no ?? null, $partner->country_id ?? null);
    }

    /** Secondary Mobile as [code|null, local number]. */
    public static function secondaryMobile(object $partner): array
    {
        return self::mobile($partner->secondary_mob_country_code ?? null, $partner->secondary_mob ?? null, $partner->country_id ?? null);
    }

    /** "+<code> <number>" ('' without a number; the number alone when no code is known). */
    public static function display(array $mobile): string
    {
        [$code, $local] = $mobile;
        if ($local === '') {
            return '';
        }

        return $code !== null ? '+' . $code . ' ' . $local : $local;
    }

    /**
     * Value to store for a mobile entered as country code + number, keeping
     * the record's stored format: the same number as now = the stored value
     * unchanged; a new number = code + number when the old value had its
     * code in front, else the local number. null = no number.
     */
    public static function storedValue(?string $current, string $code, string $local): ?string
    {
        if ($local === '') {
            return null;
        }

        $currentDigits = preg_replace('/\D+/', '', (string) $current);
        if ($currentDigits !== '' && in_array($currentDigits, PhoneNumber::storedForms($code, $local), true)) {
            return $current;
        }

        $hadCode = $currentDigits !== '' && PhoneNumber::split(null, $currentDigits)[0] !== null;

        return $hadCode ? $code . $local : $local;
    }

    /**
     * Saved code first; without one, the partner country's code (how the
     * number was always shown). A number stored with a code in front shows
     * under that code (PhoneNumber::split()).
     */
    private static function mobile(?string $storedCode, ?string $mobile, $countryId): array
    {
        $code = preg_replace('/\D+/', '', (string) $storedCode);
        if ($code === '' && $countryId) {
            $code = (string) (self::countryCodes()[(int) $countryId] ?? '');
        }

        return PhoneNumber::split($code, $mobile);
    }

    private static function countryCodes(): array
    {
        return self::$countryCodes ??= Country::whereNotNull('country_code')->pluck('country_code', 'id')->map(fn ($code) => (string) $code)->all();
    }
}
