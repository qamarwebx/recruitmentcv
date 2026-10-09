<?php

namespace App\Support;

use App\Jobs\SendPartnerActivityNotification;
use App\Models\PartnerActivityEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Partner Activity alerts to CRM staff (CRM -> Website -> Partner
 * Notification): a partner staying on a Candidate Detail page for more than
 * a minute, clicking Hire Now, or clicking Download CV.
 *
 * Recording happens on RecruitmentCV (PartnerPortalController: presence
 * and Hire Now via the candidateActivity* actions, Download CV in
 * candidateCv() when the CV is actually served - the partner and candidate
 * always come from the session and the candidate access rule, never the
 * request). Sending
 * is the SendPartnerActivityNotification job (CRM queue worker): email via
 * the Website SMTP, WhatsApp via the Meta Automation rules for
 * template_for "partner_activity". Twin file in both apps.
 */
class PartnerActivity
{
    public const VIEWED = 'candidate_viewed';
    public const HIRE = 'hire_now';
    public const DOWNLOAD = 'download_cv';

    /** Continuous presence needed before the "viewed" alert. */
    public const VIEW_SECONDS = 60;

    /** More than this between heartbeats = presence was interrupted (timer restarts). */
    public const HEARTBEAT_GAP_SECONDS = 40;

    /** One alert per partner + candidate + event within this window (refresh, tabs, double clicks). */
    public const COOLDOWN_MINUTES = 30;

    /**
     * Download CV is recorded server-side for every CV actually served
     * (PartnerPortalController::candidateCv), so only the requests of one
     * click - a double click, a PDF viewer re-requesting the file - are
     * folded together; every later download alerts again.
     */
    public const DOWNLOAD_DEDUPE_SECONDS = 15;

    /** Meta Automation / Template "Template For" key (config constants.template_for_map). */
    public const TEMPLATE_FOR = 'partner_activity';

    /**
     * event => trigger (Meta Automation "Trigger"), activity line, email subject.
     */
    public const EVENTS = [
        self::VIEWED => [
            'trigger' => 'partner_candidate_viewed',
            'activity' => 'Candidate Detail viewed for more than 1 minute',
            'subject' => 'Partner Activity: Candidate viewed for over 1 minute',
        ],
        self::HIRE => [
            'trigger' => 'partner_hire_now_clicked',
            'activity' => 'Hire Now clicked',
            'subject' => 'Partner Activity: Hire Now clicked',
        ],
        self::DOWNLOAD => [
            'trigger' => 'partner_download_cv_clicked',
            'activity' => 'Download CV clicked',
            'subject' => 'Partner Activity: Download CV clicked',
        ],
    ];

    /**
     * WhatsApp template texts to create/approve in the provider and register
     * in CRM (Template -> Template For "Partner Activity"). {{n}} order =
     * the variables listed in VARIABLE_ORDER.
     */
    public const WHATSAPP_TEMPLATES = [
        self::VIEWED => "*Partner Activity Alert*\n\nPartner: {{1}}\nMobile: {{2}}\nCity: {{3}}\n\nCandidate: {{4}}\nCandidate ID: {{5}}\n\nActivity: Candidate Detail viewed for more than 1 minute\nTime: {{6}}\n\nView Candidate:\n{{7}}",
        self::HIRE => "*Partner Activity Alert - Hire Now*\n\nPartner: {{1}}\nMobile: {{2}}\nCity: {{3}}\n\nCandidate: {{4}}\nCandidate ID: {{5}}\n\nActivity: Hire Now clicked\nTime: {{6}}\n\nView Candidate:\n{{7}}",
        self::DOWNLOAD => "*Partner Activity Alert - Download CV*\n\nPartner: {{1}}\nMobile: {{2}}\nCity: {{3}}\n\nCandidate: {{4}}\nCandidate ID: {{5}}\n\nActivity: Download CV clicked\nTime: {{6}}\n\nView Candidate:\n{{7}}",
    ];

    /** Variable labels in {{1}}..{{7}} order (keys of template_for_map.partner_activity). */
    public const VARIABLE_ORDER = ['partner name', 'partner mobile', 'partner city', 'candidate name', 'candidate id', 'date time', 'candidate url'];

    /**
     * Claims the right to notify for this event row. Exactly one caller ever
     * wins (notified_at set atomically); inside the cooldown for the same
     * partner + candidate + event (cooldownSeconds()) the row is marked
     * suppressed instead.
     * Serialized per partner + candidate + event (MySQL named lock, the same
     * mechanism the B2B CV download uses), so two tabs can't both send.
     */
    public static function claim(PartnerActivityEvent $event): bool
    {
        $lock = 'partner_activity:' . $event->partner_id . ':' . $event->candidate_id . ':' . $event->event;
        DB::selectOne('SELECT GET_LOCK(?, 5) AS acquired', [$lock]);

        try {
            $claimed = PartnerActivityEvent::whereKey($event->id)->whereNull('notified_at')
                ->update(['notified_at' => now(), 'status' => 'claimed']);
            if (!$claimed) {
                return false;
            }

            $recent = PartnerActivityEvent::where('partner_id', $event->partner_id)
                ->where('candidate_id', $event->candidate_id)
                ->where('event', $event->event)
                ->where('status', 'sent')
                ->where('notified_at', '>=', now()->subSeconds(self::cooldownSeconds($event->event)))
                ->where('id', '!=', $event->id)
                ->exists();

            PartnerActivityEvent::whereKey($event->id)->update(['status' => $recent ? 'suppressed' : 'sent']);

            return !$recent;
        } finally {
            DB::selectOne('SELECT RELEASE_LOCK(?) AS released', [$lock]);
        }
    }

    /** Duplicate window of an event: one click for Download CV, else COOLDOWN_MINUTES. */
    public static function cooldownSeconds(string $event): int
    {
        return $event === self::DOWNLOAD ? self::DOWNLOAD_DEDUPE_SECONDS : self::COOLDOWN_MINUTES * 60;
    }

    /**
     * The alert's content, built server-side when the event fires.
     * $candidateUrl: the partner's own Candidate Detail URL (their site).
     */
    public static function context(PartnerActivityEvent $event, $partner, $candidate, string $candidateUrl, ?string $teamMember = null): array
    {
        $city = $partner->city_id ? DB::table('cities')->where('id', $partner->city_id)->value('name') : null;

        return [
            'event' => $event->event,
            'event_id' => $event->id,
            'partner_id' => (int) $event->partner_id,
            'activity' => self::EVENTS[$event->event]['activity'],
            'subject' => self::EVENTS[$event->event]['subject'],
            'partner_name' => trim((string) ($partner->rec_off_name ?: $partner->owner_name)) ?: '---',
            'partner_mobile' => PartnerContact::display(PartnerContact::primaryMobile($partner)) ?: '---',
            'partner_city' => $city ?: '---',
            'team_member' => $teamMember,
            'candidate_name' => trim((string) $candidate->cand_name) ?: '---',
            'candidate_ref' => $candidate->reference_no ?: ('#' . $candidate->id),
            'candidate_url' => $candidateUrl,
            'occurred_at' => ($event->notified_at ?? now())->copy()->timezone(config('app.timezone'))->format('d M Y, h:i A'),
        ];
    }

    /** Queue the alert (email + WhatsApp) for a claimed event. */
    public static function dispatch(array $context): void
    {
        if (!self::recipients()) {
            // Recorded as not delivered, so it never counts towards the cooldown.
            PartnerActivityEvent::whereKey($context['event_id'] ?? 0)->update(['status' => 'no_recipients']);
            Log::info('Partner activity alert not sent: no recipients configured', ['event_id' => $context['event_id'] ?? null]);

            return;
        }

        SendPartnerActivityNotification::dispatch($context)->onQueue('default');
    }

    /**
     * Configured recipients: active CRM staff from
     * partner_notification_recipients, with the staff member's CRM
     * notification contacts (Staff -> "Work Email CRM Notification" =
     * admins.working_email, "Work Mobile No CRM Notification" =
     * admins.work_number) - read at send time, so Staff changes apply at
     * once. Empty = that channel is skipped (like the other CRM reminders).
     *
     * @return array<int, array{admin_id:int, name:string, email:?string, mobile:?string}>
     */
    public static function recipients(): array
    {
        return DB::table('partner_notification_recipients as r')
            ->join('admins as a', 'a.id', '=', 'r.admin_id')
            ->where('a.status', 1)
            ->orderBy('r.id')
            ->get(['a.id', 'a.name', 'a.working_email', 'a.work_number'])
            ->map(fn ($a) => [
                'admin_id' => (int) $a->id,
                'name' => (string) $a->name,
                'email' => filter_var(trim((string) $a->working_email), FILTER_VALIDATE_EMAIL) ?: null,
                'mobile' => preg_replace('/\D+/', '', (string) $a->work_number) ?: null,
            ])
            ->all();
    }
}
