<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Customer accounts (users, `web` guard) on RecruitmentCV: ONE account per
 * customer, usable on every partner website. users.partner_id records the
 * website the customer registered through ("registered via" - drives the
 * Partner Portal Customers list), never who may log in where. Orders still
 * belong to the partner of the site they are placed on (CustomerHire-
 * Controller). Customer accounts are completely separate from Partner
 * accounts (partners, `partner` guard) - nothing here reads `partners`.
 * The current site comes from the existing ResolvePartnerWebsiteDomain
 * middleware (app('currentPartner')); never from the request.
 */
class CustomerSite
{
    public static function partnerId(): ?int
    {
        $partner = app()->bound('currentPartner') ? app('currentPartner') : null;

        return $partner ? (int) $partner->id : null;
    }

    /**
     * Customer login/register exists only on partner subdomains. The main
     * recruitmentcv.com site is the PARTNER entry point ("Login as Partner").
     */
    public static function isPartnerSite(): bool
    {
        return self::partnerId() !== null;
    }

    /**
     * Every customer account (any website), for login lookups and duplicate
     * checks. Where the same number/email matched several legacy rows, the
     * account registered on the current site comes first, then a verified
     * one, then the oldest - so ->first() is deterministic.
     */
    public static function accounts(): Builder
    {
        return User::query()
            ->orderByRaw('CASE WHEN partner_id <=> ? THEN 0 ELSE 1 END', [self::partnerId()])
            ->orderByRaw('mobile_verified_at IS NULL')
            ->orderBy('id');
    }

    /**
     * A mobile number as the customer would mean it, independent of typing:
     * digits only, no leading zeros, no country-code prefix typed in front
     * (users store the local number in mobile_no and the code separately in
     * country_code). Only used for comparing - storage format is unchanged.
     */
    public static function canonicalMobile(?string $countryCode, ?string $mobile): string
    {
        return PhoneNumber::local($countryCode, $mobile);
    }

    /**
     * Duplicate check before creating a customer. One customer account per
     * email and per mobile number across all websites (users.email is also
     * unique at the DB level). Same number with a different country code is
     * a different number; legacy rows without a country code still match.
     *
     * @return array<string, array<int, string>> field => messages
     */
    public static function registrationConflicts(?string $email, ?string $countryCode, ?string $mobile, ?int $ignoreId = null): array
    {
        $errors = [];

        $email = strtolower(trim((string) $email));
        if ($email !== '' && User::where('email', $email)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $errors['email'] = [__('locale.This email is already registered. Please login.')];
        }

        $canonical = self::canonicalMobile($countryCode, $mobile);
        $code = preg_replace('/\D+/', '', (string) $countryCode);
        if ($canonical !== '') {
            $taken = self::accounts()
                ->whereNotNull('mobile_no')
                ->where('mobile_no', 'like', '%' . substr($canonical, -7))
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->get(['id', 'mobile_no', 'country_code'])
                ->contains(function ($user) use ($canonical, $code) {
                    $userCode = preg_replace('/\D+/', '', (string) $user->country_code);
                    $sameCode = $userCode === '' || $code === '' || $userCode === $code;

                    return $sameCode && self::canonicalMobile($userCode ?: $code, $user->mobile_no) === $canonical;
                });

            if ($taken) {
                $errors['mobile'] = [__('locale.This mobile number is already registered. Please login.')];
            }
        }

        return $errors;
    }

    /**
     * May this customer session be used on the current site? Any customer
     * account works on any partner website; the main recruitmentcv.com site
     * has no customer login (it is the Partner entry point).
     */
    public static function owns(?User $user): bool
    {
        return $user !== null && self::isPartnerSite();
    }
}
