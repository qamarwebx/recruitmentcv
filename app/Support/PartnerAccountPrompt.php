<?php

namespace App\Support;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

/**
 * "Account Details" prompt (Partner Portal): once per partner LOGIN, the
 * first /partner/* page opens a modal when the signed-in partner's Edit
 * Account Details fields are incomplete (Partner::missingAccountDetails(),
 * read from the database). A successful partner login - any login path,
 * via Laravel's Login event - starts the cycle by storing that partner's id
 * in the session; the first portal page consumes it, so closing the modal or
 * moving between pages never reopens it until the next login.
 */
class PartnerAccountPrompt
{
    public const SESSION_KEY = 'partner_account_details_prompt';

    /** Login event listener: a new partner login starts a new prompt cycle. */
    public static function startCycle(Login $event): void
    {
        if ($event->guard !== 'partner' || !$event->user) {
            return;
        }

        session()->put(self::SESSION_KEY, (string) $event->user->getAuthIdentifier());
        // A new partner login is never a team member's until marked again.
        PartnerTeam::clear();
    }

    /**
     * Called once by the portal layout: the missing field keys to prompt for
     * on this page, or [] (no new login pending, another partner's flag, or
     * nothing missing). The flag is consumed either way.
     */
    public static function take(): array
    {
        $partner = Auth::guard('partner')->user();
        if (!$partner || !session()->has(self::SESSION_KEY)) {
            return [];
        }
        // Account details are the owner's to complete, not a team member's.
        if (PartnerTeam::current()) {
            session()->forget(self::SESSION_KEY);

            return [];
        }

        $flaggedId = session()->pull(self::SESSION_KEY);
        if ($flaggedId !== (string) $partner->id) {
            return [];
        }

        return $partner->missingAccountDetails();
    }
}
