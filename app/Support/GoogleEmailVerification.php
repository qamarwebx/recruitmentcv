<?php

namespace App\Support;

/**
 * A successful Google OAuth sign-in proves the account's email address - the
 * one rule for marking a Partner's email verified from Google (twin file,
 * identical in the CRM and RecruitmentCV).
 *
 * The existing email verification field is reused (email_verified_at - the
 * same one the emailed-code flow sets and the CRM "Verified" badge reads).
 * It is set only when Google itself reports the address verified (OpenID
 * "email_verified") AND it is exactly this account's email; never cleared,
 * never for an address merely typed into a form.
 */
final class GoogleEmailVerification
{
    /** Google reports the signed-in account's email as verified ("email_verified"; older v2 userinfo "verified_email"). */
    public static function claimed($googleUser): bool
    {
        $raw = is_array($googleUser->user ?? null) ? $googleUser->user : [];
        $flag = $raw['email_verified'] ?? $raw['verified_email'] ?? false;

        return $flag === true || $flag === 1 || $flag === '1' || $flag === 'true';
    }

    /**
     * Marks $account's email verified when Google proved it: same address
     * (case-insensitive), verified by Google, not verified yet. Returns
     * whether it changed (the caller saves).
     */
    public static function apply($account, ?string $googleEmail, bool $googleVerified): bool
    {
        $email = trim((string) ($account->email ?? ''));
        if (!$googleVerified || $email === '' || strcasecmp($email, trim((string) $googleEmail)) !== 0 || $account->email_verified_at !== null) {
            return false;
        }
        $account->email_verified_at = now();

        return true;
    }
}
