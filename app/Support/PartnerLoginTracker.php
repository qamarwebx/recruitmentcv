<?php

namespace App\Support;

use App\Models\PartnerLoginSession;
use App\Models\PartnerTeamMember;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

/**
 * Partner / team member login-session history for CRM -> Website -> Live
 * Partners (App\Models\PartnerLoginSession). Hooks into the existing auth
 * flow only - nothing here decides who may sign in:
 *
 * - start()           Login event (every partner login path)  -> new row
 * - markTeamMember()  PartnerTeam::start() (team member login) -> row type
 * - touch()           every web request of a signed-in partner (middleware
 *                     TrackPartnerActivity) + a visible tab's heartbeat -> activity
 * - presence()        each open tab's heartbeat / hidden / leave signal
 *                     (PartnerPresenceController) -> partner_presence_tabs,
 *                     which decides "Online" (PartnerLoginSession::whereOnline())
 * - close()           logout (Logout event), and before the existing
 *                     middlewares end a session (expired / revoked / rejected)
 *                     - also ends every tab of the session
 *
 * The row id lives in the server-side session, so refreshes, AJAX calls and
 * other tabs of the same session all update the same row. Tracking never
 * breaks a request: every failure is logged (class only) and ignored.
 */
final class PartnerLoginTracker
{
    public const SESSION_KEY = 'partner_login_session_id';

    /** Login event listener. */
    public static function start(Login $event): void
    {
        if ($event->guard !== 'partner' || !$event->user) {
            return;
        }

        self::guard(function () use ($event) {
            // A login inside a session that still has an open row (e.g. another
            // account signing in on the same browser) ends that row first.
            self::close('replaced');
            self::open((int) $event->user->getAuthIdentifier(), 'login');
        });
    }

    /** PartnerTeam::start(): this session is a team member's. */
    public static function markTeamMember(PartnerTeamMember $member): void
    {
        self::guard(function () use ($member) {
            $id = (int) session()->get(self::SESSION_KEY);
            if ($id) {
                PartnerLoginSession::whereKey($id)->whereNull('ended_at')
                    ->update(['user_type' => PartnerLoginSession::TYPE_TEAM_MEMBER, 'team_member_id' => $member->id]);
            }
        });
    }

    /**
     * This session's open row was not started by the partner's own login
     * (e.g. 'crm_admin' - CRM Login As Partner): kept as a session, but not
     * counted as one of the partner's logins (the report counts
     * started_by = 'login').
     */
    public static function markStartedBy(string $startedBy): void
    {
        self::guard(function () use ($startedBy) {
            $id = (int) session()->get(self::SESSION_KEY);
            if ($id) {
                PartnerLoginSession::whereKey($id)->whereNull('ended_at')->update(['started_by' => Str::limit($startedBy, 10, '')]);
            }
        });
    }

    /**
     * Activity of a signed-in partner session. One atomic UPDATE: adds the time
     * since the previous signal when it is a short gap (the user was active
     * all along), at most once per WRITE_EVERY_SECONDS - concurrent requests
     * of several tabs can't both add the same interval.
     */
    public static function touch(): void
    {
        self::guard(function () {
            $id = (int) session()->get(self::SESSION_KEY);

            if (!$id) {
                // Signed in before tracking existed: the history starts now.
                $partnerId = (int) Auth::guard('partner')->id();
                if ($partnerId) {
                    self::open($partnerId, 'resume');
                    $member = PartnerTeam::current();
                    if ($member) {
                        self::markTeamMember($member);
                    }
                }

                return;
            }

            $now = now()->toDateTimeString();
            PartnerLoginSession::whereKey($id)
                ->whereNull('ended_at')
                ->where('last_activity_at', '<=', now()->subSeconds(PartnerLoginSession::WRITE_EVERY_SECONDS)->toDateTimeString())
                ->update([
                    'active_seconds' => self::activeIncrement($now),
                    'last_activity_at' => $now,
                    'updated_at' => $now,
                ]);
        });
    }

    /**
     * One tab's presence signal: $state visible / hidden (heartbeat) or left
     * (the tab is being closed or navigated away). Moves that tab's expiry
     * with the server clock - visible/hidden: PRESENCE_TTL / HIDDEN_PRESENCE_TTL
     * from now; left: LEAVE_GRACE from now, so a navigation or refresh of the
     * same tab (same $tabId, kept in its sessionStorage) continues seamlessly
     * while a real close drops out. Only rows of this session's own login
     * row (id from the server-side session) are ever written - the request
     * supplies just the opaque tab id. Other tabs are untouched, so the
     * session stays online until its LAST tab is gone.
     */
    public static function presence(string $tabId, string $state): void
    {
        self::guard(function () use ($tabId, $state) {
            $row = self::currentRow();
            if (!$row) {
                return;
            }

            $now = now();
            $ttl = match ($state) {
                'left' => PartnerLoginSession::LEAVE_GRACE_SECONDS,
                'hidden' => PartnerLoginSession::HIDDEN_PRESENCE_TTL_SECONDS,
                default => PartnerLoginSession::PRESENCE_TTL_SECONDS,
            };
            $tabs = DB::table('partner_presence_tabs')->where('login_session_id', $row->id);

            if (!(clone $tabs)->where('tab_id', $tabId)->exists()) {
                // A leave of a tab never seen needs no row; past MAX_TABS the session is online anyway.
                if ($state === 'left' || (clone $tabs)->where('expires_at', '>', $now->toDateTimeString())->count() >= PartnerLoginSession::MAX_TABS) {
                    return;
                }
            }

            DB::table('partner_presence_tabs')->upsert([[
                'login_session_id' => $row->id,
                'partner_id' => $row->partner_id,
                'team_member_id' => $row->team_member_id,
                'tab_id' => $tabId,
                'state' => $state,
                'last_seen_at' => $now->toDateTimeString(),
                'expires_at' => $now->copy()->addSeconds($ttl)->toDateTimeString(),
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ]], ['login_session_id', 'tab_id'], ['partner_id', 'team_member_id', 'state', 'last_seen_at', 'expires_at', 'updated_at']);
        });
    }

    /**
     * Has this session gone without real activity (a request or a visible tab)
     * for longer than its session lifetime? Background-tab heartbeats are
     * requests too and would otherwise keep an unused session alive forever.
     */
    public static function idleExpired(): bool
    {
        $row = self::currentRow();

        return $row && $row->last_activity_at
            && $row->last_activity_at->lt(now()->subMinutes(max(1, (int) $row->idle_timeout_minutes)));
    }

    /**
     * This session's open login row; a signed-in session whose row is
     * missing or already ended (e.g. closed by the CRM's expiry sweep while
     * the session was still alive) gets a fresh "resume" row.
     */
    private static function currentRow(): ?PartnerLoginSession
    {
        $id = (int) session()->get(self::SESSION_KEY);
        $row = $id ? PartnerLoginSession::whereKey($id)->whereNull('ended_at')->first() : null;
        if ($row) {
            return $row;
        }

        $partnerId = (int) Auth::guard('partner')->id();
        if (!$partnerId) {
            return null;
        }
        session()->forget(self::SESSION_KEY);
        self::open($partnerId, 'resume');
        $member = PartnerTeam::current();
        if ($member) {
            self::markTeamMember($member);
        }

        return PartnerLoginSession::whereKey((int) session()->get(self::SESSION_KEY))->first();
    }

    /**
     * Ends this session's row. logout: now (the last activity is counted
     * first); expired / revoked / rejected / replaced: at its last activity -
     * the session was not in use after that.
     */
    public static function close(string $reason): void
    {
        self::guard(function () use ($reason) {
            $id = (int) session()->get(self::SESSION_KEY);
            if (!$id) {
                return;
            }

            if ($reason === 'logout') {
                self::touchNow($id);
                PartnerLoginSession::whereKey($id)->whereNull('ended_at')->update(['ended_at' => now(), 'end_reason' => $reason]);
            } else {
                PartnerLoginSession::whereKey($id)->whereNull('ended_at')
                    ->update(['ended_at' => DB::raw('last_activity_at'), 'end_reason' => $reason]);
            }
            // Every tab of this session is gone with it (Offline at once).
            DB::table('partner_presence_tabs')->where('login_session_id', $id)->where('expires_at', '>', now()->toDateTimeString())
                ->update(['state' => 'left', 'expires_at' => now()->toDateTimeString(), 'updated_at' => now()->toDateTimeString()]);

            session()->forget(self::SESSION_KEY);
        });
    }

    /** Logout event listener (an earlier explicit close() wins). */
    public static function onLogout(Logout $event): void
    {
        if ($event->guard === 'partner') {
            self::close('logout');
        }
    }

    private static function open(int $partnerId, string $startedBy): void
    {
        $request = request();
        $agent = new Agent();
        $agent->setUserAgent((string) $request->userAgent());
        $site = app()->bound('currentPartner') ? app('currentPartner') : null;

        $row = PartnerLoginSession::create([
            'partner_id' => $partnerId,
            'user_type' => PartnerLoginSession::TYPE_PARTNER,
            'started_by' => $startedBy,
            'login_at' => now(),
            'last_activity_at' => now(),
            'active_seconds' => 0,
            'idle_timeout_minutes' => max(1, (int) config('session.lifetime', 120)),
            'host' => Str::limit((string) $request->getHost(), 250, ''),
            'site_partner_id' => $site ? (int) $site->id : null,
            'ip' => $request->ip(),
            'browser' => Str::limit((string) $agent->browser(), 50, '') ?: null,
            'platform' => Str::limit((string) $agent->platform(), 50, '') ?: null,
            'device' => $agent->isTablet() ? 'tablet' : ($agent->isMobile() ? 'mobile' : 'desktop'),
        ]);

        session()->put(self::SESSION_KEY, $row->id);
    }

    /** Unthrottled activity write (used right before a logout). */
    private static function touchNow(int $id): void
    {
        $now = now()->toDateTimeString();
        PartnerLoginSession::whereKey($id)->whereNull('ended_at')->update([
            'active_seconds' => self::activeIncrement($now),
            'last_activity_at' => $now,
        ]);
    }

    /**
     * active_seconds + the gap since the previous signal when it is short
     * enough to mean "active all along" (else + 0). $now is the server's own
     * clock (Y-m-d H:i:s), quoted - never request input.
     */
    private static function activeIncrement(string $now)
    {
        $gap = 'TIMESTAMPDIFF(SECOND, last_activity_at, ' . DB::getPdo()->quote($now) . ')';

        return DB::raw("active_seconds + IF({$gap} <= " . PartnerLoginSession::ACTIVE_GAP_SECONDS . ", GREATEST({$gap}, 0), 0)");
    }

    private static function guard(callable $work): void
    {
        try {
            $work();
        } catch (\Throwable $e) {
            // Class only - tracking must never break a login or a page.
            Log::warning('Partner login tracking skipped', ['exception' => get_class($e)]);
        }
    }
}
