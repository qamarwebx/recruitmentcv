<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;

/**
 * "Add SMTP With Google" (Partner Website -> SMTP) between connecting the
 * Google account and saving the SMTP: kept server-side in the signed-in
 * account's session, bound to its partner (and team member). The refresh
 * token is stored encrypted and never leaves the server - the page only
 * ever gets the connected email address. Also the session half of the
 * OAuth `state` check (PartnerSmtpGoogleController).
 */
final class GoogleSmtpPending
{
    private const FLOW = 'google_smtp_flow';
    private const PENDING = 'google_smtp_pending';

    /** Seconds a started sign-in / a connected-but-unsaved account stays valid. */
    public const FLOW_TTL = 600;
    public const PENDING_TTL = 1800;

    /** A sign-in starts: remember (a hash of) its state for this session. */
    public static function startFlow(string $state, int $partnerId): void
    {
        session()->put(self::FLOW, ['state' => hash('sha256', $state), 'partner_id' => $partnerId, 'member_id' => self::memberId(), 'at' => time()]);
    }

    /** The sign-in coming back (its state's sha256) belongs to this session, partner and team member (single use). */
    public static function finishFlow(string $stateHash, int $partnerId): bool
    {
        $flow = session()->pull(self::FLOW);

        return is_array($flow) && $stateHash !== ''
            && hash_equals((string) ($flow['state'] ?? ''), $stateHash)
            && (int) ($flow['partner_id'] ?? 0) === $partnerId
            && ($flow['member_id'] ?? null) === self::memberId()
            && time() - (int) ($flow['at'] ?? 0) <= self::FLOW_TTL;
    }

    /** The connected account waiting to be tested + saved (replaces an earlier one). */
    public static function put(int $partnerId, string $email, string $refreshToken): void
    {
        self::forget(true);
        session()->put(self::PENDING, [
            'partner_id' => $partnerId,
            'member_id' => self::memberId(),
            'email' => $email,
            'token' => Crypt::encryptString($refreshToken),
            'tested' => false,
            'at' => time(),
        ]);
    }

    /** ['email', 'refresh_token', 'tested'] for this partner, or null (none / expired / someone else's). */
    public static function get(int $partnerId): ?array
    {
        $pending = session()->get(self::PENDING);
        if (!is_array($pending)) {
            return null;
        }
        if ((int) ($pending['partner_id'] ?? 0) !== $partnerId || ($pending['member_id'] ?? null) !== self::memberId()
            || time() - (int) ($pending['at'] ?? 0) > self::PENDING_TTL) {
            self::forget(true);

            return null;
        }

        try {
            $token = Crypt::decryptString((string) $pending['token']);
        } catch (\Throwable $e) {
            session()->forget(self::PENDING);

            return null;
        }

        return ['email' => (string) $pending['email'], 'refresh_token' => $token, 'tested' => (bool) ($pending['tested'] ?? false)];
    }

    /** The connection check passed: Save is allowed. */
    public static function markTested(int $partnerId): void
    {
        $pending = session()->get(self::PENDING);
        if (is_array($pending) && (int) ($pending['partner_id'] ?? 0) === $partnerId) {
            $pending['tested'] = true;
            session()->put(self::PENDING, $pending);
        }
    }

    /**
     * Drops the waiting account. $revoke: it will not be saved, so its
     * authorization is revoked at Google (unless that Gmail account already
     * sends for a saved SMTP). After a save: forget(false).
     */
    public static function forget(bool $revoke): void
    {
        $pending = session()->pull(self::PENDING);
        if (!$revoke || !is_array($pending)) {
            return;
        }
        try {
            SmtpSettings::revokeGoogleUnlessInUse(Crypt::decryptString((string) $pending['token']), (string) ($pending['email'] ?? ''));
        } catch (\Throwable $e) {
            // Undecryptable: nothing usable to revoke.
        }
    }

    /** For the page: the connected address only (never a token). */
    public static function forView(int $partnerId): ?array
    {
        $pending = self::get($partnerId);

        return $pending ? ['email' => $pending['email'], 'tested' => $pending['tested']] : null;
    }

    private static function memberId(): ?int
    {
        $member = PartnerTeam::current();

        return $member ? (int) $member->id : null;
    }
}
