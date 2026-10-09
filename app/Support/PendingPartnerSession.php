<?php

namespace App\Support;

use Illuminate\Auth\Events\Login;

/**
 * Pending Partner session limit (CRM -> Website -> Settings -> Login
 * Session, App\Support\WebsiteSettings::pendingPartnerSessionMinutes()).
 *
 * Every partner login stamps the server-side session with its sign-in time
 * (Login event - every login path: password, OTP, email code, Google,
 * registration). App\Http\Middleware\ExpirePendingPartnerSession compares it
 * with the CURRENT setting on every request, so the limit is never fixed at
 * login. Only the timestamp lives in the session (server-side store; the
 * browser only holds the session id) - nothing from the client counts.
 */
final class PendingPartnerSession
{
    public const SESSION_KEY = 'partner_signed_in_at';

    /** Login event listener: a partner login starts a new session clock. */
    public static function stamp(Login $event): void
    {
        if ($event->guard !== 'partner' || !$event->user) {
            return;
        }

        session()->put(self::SESSION_KEY, now()->getTimestamp());
    }

    /**
     * The limit applies to a partner whose Registration Status is Pending (0
     * exactly - Approved, Rejected or anything unknown never), signed in as
     * themselves: a team member's login (PartnerTeam) keeps the normal session.
     */
    public static function applies($partner): bool
    {
        return $partner
            && $partner->registration_status !== null
            && (int) $partner->registration_status === 0
            && !PartnerTeam::hasSession();
    }
}
