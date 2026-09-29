<?php

namespace App\Support;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Builder;

/**
 * Partner lookup by mobile that recognizes the same number in either stored
 * format (see PhoneNumber) - the CRM stores owner_mobile_no as code + number,
 * this site's registration as the local number. An exact match on the typed
 * number made CRM-created partners (e.g. 919327063401) unrecognizable at
 * login, which sent them to "Complete Your Registration".
 */
class PartnerMobile
{
    public static function query(?string $countryCode, ?string $mobile): Builder
    {
        $forms = PhoneNumber::storedForms($countryCode, $mobile);

        return Partner::whereIn('owner_mobile_no', $forms ?: ['__no_number__']);
    }

    /**
     * If the same number somehow exists more than once (different formats),
     * prefer the verified, approved, oldest record - never guess a newer
     * pending copy over the real partner.
     */
    public static function find(?string $countryCode, ?string $mobile): ?Partner
    {
        return self::query($countryCode, $mobile)
            ->orderByRaw('mobile_verified_at IS NULL')
            ->orderByRaw('registration_status = 1 DESC')
            ->orderBy('id')
            ->first();
    }
}
