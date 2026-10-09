<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One RecruitmentCV login session of a partner or a team member (CRM ->
 * Website -> Live Partners). Written by RecruitmentCV's
 * App\Support\PartnerLoginTracker; read by the CRM report. Twin file in both
 * apps (same partner_login_sessions table).
 *
 * - Login count  = rows started by a real login (started_by = login).
 * - Session time = login_at -> ended_at, or -> last_activity_at while the
 *   row is open (never "now - login" for someone who went inactive).
 * - Active time  = active_seconds: grown only by activity signals (requests
 *   + the heartbeat of a visible tab), each adding the time since the
 *   previous signal when that gap is at most ACTIVE_GAP_SECONDS. One row per
 *   session, updated atomically, so several tabs never multiply it.
 * - Online       = open row with at least one open tab: a partner_presence_tabs
 *   row whose expires_at is still ahead (whereOnline()). Each open tab sends
 *   a heartbeat that moves its expires_at forward (PRESENCE_TTL_SECONDS
 *   visible / HIDDEN_PRESENCE_TTL_SECONDS hidden); closing a tab sends a
 *   leave signal (LEAVE_GRACE_SECONDS, so a navigation/refresh of that tab
 *   continues seamlessly); a tab that stops sending (crash, network) just
 *   expires. All times are the server's, never the browser's.
 * - An open row idle for longer than its idle_timeout_minutes (the
 *   RecruitmentCV session lifetime at login) is a session that has expired.
 */
class PartnerLoginSession extends Model
{
    public const TYPE_PARTNER = 'partner';

    public const TYPE_TEAM_MEMBER = 'team_member';

    /** Heartbeat interval of a visible portal/website tab. */
    public const HEARTBEAT_SECONDS = 30;

    /** Heartbeat interval of a hidden (background) tab - browsers run its timers about once a minute at most. */
    public const HIDDEN_HEARTBEAT_SECONDS = 60;

    /** A visible tab stays present this long after its last heartbeat (3 missed beats = gone). */
    public const PRESENCE_TTL_SECONDS = 90;

    /** Same for a hidden tab (its timers may be delayed by the browser). */
    public const HIDDEN_PRESENCE_TTL_SECONDS = 150;

    /** After a tab's leave signal: gone unless that tab (navigation / refresh) reports again within this. */
    public const LEAVE_GRACE_SECONDS = 15;

    /** Open tabs tracked per login session (more = ignored, the session is online anyway). */
    public const MAX_TABS = 20;

    /** A gap between two activity signals counts as active time only up to this. */
    public const ACTIVE_GAP_SECONDS = 300;

    /** Activity writes are skipped when the previous one was this recent (time is not lost: the next write adds it). */
    public const WRITE_EVERY_SECONDS = 30;

    protected $fillable = [
        'partner_id', 'team_member_id', 'user_type', 'started_by',
        'login_at', 'last_activity_at', 'ended_at', 'end_reason',
        'active_seconds', 'idle_timeout_minutes',
        'host', 'site_partner_id', 'ip', 'browser', 'platform', 'device',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'ended_at' => 'datetime',
        'active_seconds' => 'integer',
        'idle_timeout_minutes' => 'integer',
    ];

    /** SQL: session length in seconds (closed: to its end; open: to its last activity). */
    public static function durationSql(string $alias = 'partner_login_sessions'): string
    {
        return "TIMESTAMPDIFF(SECOND, {$alias}.login_at, COALESCE({$alias}.ended_at, {$alias}.last_activity_at))";
    }

    /**
     * Limits a query on partner_login_sessions (as $alias) to sessions online
     * right now: not ended, and at least one tab whose presence has not
     * expired. The one definition of "Online" used everywhere.
     */
    public static function whereOnline($query, string $alias = 'partner_login_sessions')
    {
        $now = now()->toDateTimeString();

        return $query->whereNull($alias . '.ended_at')
            ->whereExists(fn ($tabs) => $tabs->selectRaw('1')->from('partner_presence_tabs as pt')
                ->whereColumn('pt.login_session_id', $alias . '.id')
                ->where('pt.expires_at', '>', $now));
    }

    public function isOnline(): bool
    {
        return $this->ended_at === null
            && \Illuminate\Support\Facades\DB::table('partner_presence_tabs')->where('login_session_id', $this->id)->where('expires_at', '>', now()->toDateTimeString())->exists();
    }
}
