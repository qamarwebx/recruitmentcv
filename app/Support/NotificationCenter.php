<?php

namespace App\Support;

use App\Jobs\SendRecruitmentNotification;
use App\Mail\RecruitmentNotificationMail;
use App\Models\Autometanotification;
use App\Models\Metawhatsapptemplate;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * The one place RecruitmentCV notifications are gated, sent and logged -
 * built on what already exists, not a parallel system:
 *
 * - settings:  App\Support\NotificationSettings (partner -> Global -> on)
 * - email:     App\Support\SmtpMailer (partner SMTPs -> Global -> .env, failover)
 * - WhatsApp:  Meta Automation rules + approved templates, sent with
 *              App\Support\MetaWhatsappTemplateSender (existing API rows);
 *              order events reuse the existing order jobs
 * - staff:     the Partner Notification recipients (PartnerActivity::recipients())
 * - log:       recruitment_notification_logs (Notification Logs tab)
 *
 * notify() queues an event (never blocks the page); deliver() runs in the
 * queue worker. Existing direct sends call sendEmail() / record(). Recipients
 * are always resolved from server-side records - never from a request. A
 * dedupe key stops a retried event from sending the same message twice.
 * Twin file in both apps.
 */
final class NotificationCenter
{
    /** CRM base URL for staff links (the CRM is only served here). */
    public const CRM_URL = 'https://crm.qamarhire.com';

    /** Queue an event (email + WhatsApp as configured). $data: display values + ids (see deliver()). */
    public static function notify(string $event, ?int $partnerId, array $data = []): void
    {
        if (!NotificationEvents::get($event)) {
            return;
        }

        $channels = array_filter(self::channels($event, $data), fn ($c) => NotificationSettings::enabled($event, $c, $partnerId));
        if (!$channels) {
            foreach (self::channels($event, $data) as $channel) {
                self::log($event, $channel, 'skipped', null, NotificationEvents::get($event)['recipient'], $partnerId, null, $channel === 'email' ? NotificationEvents::get($event)['email'] : (NotificationEvents::get($event)['whatsapp'][1] ?? null), 'Disabled in Notification Settings', null, $data['context'] ?? []);
            }

            return;
        }

        $data['occurred_at'] = $data['occurred_at'] ?? now()->format('d M Y, h:i A');

        try {
            SendRecruitmentNotification::dispatch($event, $partnerId, $data)->onQueue('default');
        } catch (\Throwable $e) {
            Log::warning('Notification not queued', ['event' => $event, 'exception' => get_class($e)]);
            self::log($event, reset($channels), 'failed', null, null, $partnerId, null, null, 'Could not be queued', null, $data['context'] ?? []);
        }
    }

    /**
     * Queue-worker side of notify(). $data keys: title, message, lines
     * ([[label, value], ...]), action ([label, url]), note, dedupe (business
     * id), customer_id / team_member_id / email (recipient references),
     * channels (default both; ['email'] when the WhatsApp side is sent by
     * an existing job, e.g. the order jobs), context.
     */
    public static function deliver(string $event, ?int $partnerId, array $data): void
    {
        $def = NotificationEvents::get($event);
        if (!$def) {
            return;
        }
        NotificationSettings::forget();   // the worker re-reads the latest settings for every event

        $recipients = self::recipients($def['recipient'], $partnerId, $data);
        $brand = self::brand($partnerId, in_array($def['recipient'], ['customer', 'team_member'], true));
        $context = $data['context'] ?? [];

        $channels = self::channels($event, $data);

        if (in_array('email', $channels, true)) {
            if (!NotificationSettings::enabled($event, 'email', $partnerId)) {
                self::log($event, 'email', 'skipped', null, $def['recipient'], $partnerId, null, $def['email'], 'Disabled in Notification Settings', null, $context);
            } elseif (!array_filter($recipients, fn ($r) => $r['email'])) {
                self::log($event, 'email', 'skipped', null, $def['recipient'], $partnerId, null, $def['email'], 'No recipient with an email address', null, $context);
            } else {
                foreach ($recipients as $r) {
                    if (!$r['email']) {
                        continue;
                    }
                    $mail = new RecruitmentNotificationMail($data['title'] ?? $def['label'], array_merge($data, ['recipient_name' => $r['name'], 'brand' => $brand]));
                    self::sendEmail($event, $partnerId, $r['email'], $mail, in_array($def['recipient'], ['customer', 'team_member'], true) ? 'partner' : 'global', [
                        'recipient_type' => $r['type'], 'dedupe' => $data['dedupe'] ?? null, 'context' => $context,
                    ]);
                }
            }
        }

        if (in_array('whatsapp', $channels, true)) {
            self::deliverWhatsapp($event, $def, $partnerId, $recipients, $data, $brand);
        }
    }

    /**
     * One email for an event: setting -> duplicate check -> SMTP router ->
     * log. $via: partner (that partner's SMTPs -> Global), site (the current
     * website) or global. Returns whether it was sent.
     */
    public static function sendEmail(string $event, ?int $partnerId, ?string $to, Mailable $mail, string $via = 'global', array $meta = []): bool
    {
        $def = NotificationEvents::get($event);
        $template = $def['email'] ?? class_basename($mail);
        $type = $meta['recipient_type'] ?? ($def['recipient'] ?? null);
        $context = $meta['context'] ?? [];

        if (!filter_var((string) $to, FILTER_VALIDATE_EMAIL)) {
            self::log($event, 'email', 'skipped', $to, $type, $partnerId, null, $template, 'No valid email address', null, $context);

            return false;
        }
        if ($def && !NotificationSettings::enabled($event, 'email', $partnerId)) {
            self::log($event, 'email', 'skipped', $to, $type, $partnerId, null, $template, 'Disabled in Notification Settings', null, $context);

            return false;
        }

        $key = !empty($meta['dedupe']) ? self::dedupeKey($event, 'email', (string) $to, (string) $meta['dedupe']) : null;
        if ($key && self::alreadySent($key)) {
            self::log($event, 'email', 'skipped', $to, $type, $partnerId, null, $template, 'Duplicate - already sent for this event', $key, $context);

            return false;
        }

        SmtpMailer::$lastDelivery = null;
        try {
            $mailer = match ($via) {
                'partner' => SmtpMailer::forPartner($partnerId),
                'site' => SmtpMailer::forSite(),
                default => SmtpMailer::global(),
            };
            $mailer->to($to)->send($mail);
        } catch (\Throwable $e) {
            self::log($event, 'email', 'failed', $to, $type, $partnerId, 'SMTP', $template, self::safeReason($e), $key, $context);

            return false;
        }

        self::log($event, 'email', 'sent', $to, $type, $partnerId, SmtpMailer::$lastDelivery['provider'] ?? 'Mailer', $template, null, $key, $context);

        return true;
    }

    /**
     * WhatsApp of an order event through the existing order jobs (booking
     * variables, Meta Automation "recruitmentcv" rules). Queued, logged.
     */
    public static function queueOrderWhatsapp(string $event, int $bookingId, ?int $partnerId, ?int $sitePartnerId): void
    {
        $def = NotificationEvents::get($event);
        [$templateFor, $trigger] = $def['whatsapp'] ?? [null, null];
        if (!$templateFor) {
            return;
        }
        $context = ['booking_id' => $bookingId];

        if (!NotificationSettings::enabled($event, 'whatsapp', $partnerId)) {
            self::log($event, 'whatsapp', 'skipped', null, $def['recipient'], $partnerId, null, $trigger, 'Disabled in Notification Settings', null, $context);

            return;
        }
        // The order jobs' own rule check (they send nothing without one).
        if (!Autometanotification::where('template_for', $templateFor)->where('trigger_template_type', $trigger)->where('status', 1)->exists()) {
            self::log($event, 'whatsapp', 'skipped', null, $def['recipient'], $partnerId, null, $trigger, 'Template Not Configured (Meta Automation: ' . $templateFor . ' / ' . $trigger . ')', null, $context);

            return;
        }

        $job = $def['recipient'] === 'partner'
            ? new \App\Jobs\AutoSendMessageForOrderToPartner($bookingId, $templateFor, $sitePartnerId, $trigger)
            : new \App\Jobs\AutoSendMessageForOrder($bookingId, $templateFor, $sitePartnerId, $trigger);
        try {
            dispatch($job->onQueue('default'));
            self::log($event, 'whatsapp', 'queued', null, $def['recipient'], $partnerId, 'Meta Automation (' . $templateFor . ')', $trigger, null, null, $context);
        } catch (\Throwable $e) {
            self::log($event, 'whatsapp', 'failed', null, $def['recipient'], $partnerId, null, $trigger, 'Could not be queued', null, $context);
        }
    }

    /** Records a message sent by its own code path (sign-in / verification codes). Never stores the code. */
    public static function record(string $event, string $channel, string $status, ?string $recipient, ?int $partnerId, ?string $provider = null, ?string $reason = null): void
    {
        $def = NotificationEvents::get($event);
        self::log($event, $channel, $status, $recipient, $def['recipient'] ?? null, $partnerId, $provider, $def[$channel === 'email' ? 'email' : 'label'] ?? null, $reason);
    }

    /**
     * A RecruitmentCV mobile OTP sent by the legacy OTP code paths, which
     * all write a Metawhatsapplog row (message_for "OTP") - recorded from
     * that row's created event (RecruitmentCV's Metawhatsapplog model). The
     * row holds no number, so no recipient; codes are masked in the reason.
     */
    public static function recordMobileOtp(?string $status, ?string $template, ?string $message): void
    {
        $sent = strcasecmp((string) $status, 'success') === 0;
        self::log('mobile_otp', 'whatsapp', $sent ? 'sent' : 'failed', null, 'user', SmtpMailer::sitePartnerId(), 'WhatsApp API (OTP)', $template,
            $sent ? null : Str::limit(preg_replace('/\b\d{4,6}\b/', '****', (string) ($message ?: 'Not sent')), 300));
    }

    /** Active Meta Automation rules (with an active approved template) for an event's WhatsApp. */
    public static function whatsappRules(string $event)
    {
        [$templateFor, $trigger] = NotificationEvents::get($event)['whatsapp'] ?? [null, null];
        if (!$templateFor || $templateFor === 'otp') {
            return collect();
        }

        return Autometanotification::where('template_for', $templateFor)->where('trigger_template_type', $trigger)->where('status', 1)->get()
            ->filter(fn ($rule) => Metawhatsapptemplate::where('id', $rule->metatemp_id)->where('status', 1)->exists())
            ->values();
    }

    /** For the settings page: is the WhatsApp side configured? */
    public static function whatsappStatus(string $event): array
    {
        $def = NotificationEvents::get($event);
        [$templateFor, $trigger] = $def['whatsapp'] ?? [null, null];
        if (!$templateFor) {
            return ['available' => false, 'configured' => false, 'label' => 'Not available'];
        }
        if ($templateFor === 'otp') {
            $api = DB::table('metawhatsappapis')->where('status', 1)->where(fn ($q) => $q->whereRaw('FIND_IN_SET(?, api_assign_to)', ['recruitmentcv_otp'])->orWhereRaw('FIND_IN_SET(?, api_assign_to)', ['otp']))->exists();

            return ['available' => true, 'configured' => $api, 'label' => $api ? 'OTP WhatsApp API configured' : 'OTP API Not Configured'];
        }

        $rules = self::whatsappRules($event);
        if (!$rules->count()) {
            return ['available' => true, 'configured' => false, 'label' => 'Template Not Configured', 'template_for' => $templateFor, 'trigger' => $trigger];
        }
        $names = Metawhatsapptemplate::whereIn('id', $rules->pluck('metatemp_id'))->pluck('template_name')->implode(', ');

        return ['available' => true, 'configured' => true, 'label' => $names, 'template_for' => $templateFor, 'trigger' => $trigger];
    }

    public static function crmUrl(string $path): string
    {
        return self::CRM_URL . '/' . ltrim($path, '/');
    }

    /** Public URL of a partner's website (live subdomain) or the main site. */
    public static function partnerSiteUrl(?int $partnerId): string
    {
        // The partner's primary live address (Own Domain or subdomain - App\Support\PartnerDomains).
        $url = $partnerId ? PartnerDomains::primaryUrl(\App\Models\Domain::where('partner_id', $partnerId)->first()) : null;

        return $url ?? 'https://' . PartnerDomains::root();
    }

    public static function log(string $event, string $channel, string $status, ?string $recipient, ?string $recipientType, ?int $partnerId, ?string $provider, ?string $template, ?string $reason, ?string $dedupeKey = null, array $context = []): void
    {
        try {
            DB::table('recruitment_notification_logs')->insert([
                'event' => Str::limit($event, 60, ''),
                'channel' => $channel,
                'status' => $status,
                'recipient' => $recipient !== null ? Str::limit($recipient, 250, '') : null,
                'recipient_type' => $recipientType,
                'partner_id' => $partnerId,
                'provider' => $provider !== null ? Str::limit($provider, 150, '') : null,
                'template' => $template !== null ? Str::limit($template, 150, '') : null,
                'reason' => $reason !== null ? Str::limit($reason, 500, '') : null,
                'dedupe_key' => $dedupeKey,
                'context' => $context ? json_encode($context) : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Notification log not written', ['event' => $event, 'exception' => get_class($e)]);
        }
    }

    // ---------------------------------------------------------------------

    private static function deliverWhatsapp(string $event, array $def, ?int $partnerId, array $recipients, array $data, array $brand): void
    {
        [$templateFor, $trigger] = $def['whatsapp'];
        $context = $data['context'] ?? [];

        if (!NotificationSettings::enabled($event, 'whatsapp', $partnerId)) {
            self::log($event, 'whatsapp', 'skipped', null, $def['recipient'], $partnerId, null, $trigger, 'Disabled in Notification Settings', null, $context);

            return;
        }
        $rules = self::whatsappRules($event);
        if (!$rules->count()) {
            self::log($event, 'whatsapp', 'skipped', null, $def['recipient'], $partnerId, null, $trigger, 'Template Not Configured (Meta Automation: ' . $templateFor . ' / ' . $trigger . ')', null, $context);

            return;
        }
        if (!array_filter($recipients, fn ($r) => $r['mobile'])) {
            self::log($event, 'whatsapp', 'skipped', null, $def['recipient'], $partnerId, null, $trigger, 'No recipient with a mobile number', null, $context);

            return;
        }

        $mapping = (array) config('constants.template_for_map.' . $templateFor);
        foreach ($rules as $rule) {
            $template = Metawhatsapptemplate::where('id', $rule->metatemp_id)->where('status', 1)->first();
            $fields = array_map('trim', explode(',', (string) $template->field_variable));
            $labels = array_map('trim', explode(',', str_replace(['[', ']'], '', (string) $template->assign_variable)));

            foreach ($recipients as $r) {
                if (!$r['mobile']) {
                    continue;
                }
                $key = !empty($data['dedupe']) ? self::dedupeKey($event, 'whatsapp:' . $template->id, $r['mobile'], (string) $data['dedupe']) : null;
                if ($key && self::alreadySent($key)) {
                    self::log($event, 'whatsapp', 'skipped', $r['mobile'], $r['type'], $partnerId, null, $template->template_name, 'Duplicate - already sent for this event', $key, $context);
                    continue;
                }

                $values = ['recipient_name' => $r['name'], 'partner_name' => $data['partner_name'] ?? $brand['name'], 'title' => $data['title'] ?? $def['label'],
                    'details' => $data['message'] ?? '', 'link' => $data['action'][1] ?? '', 'occurred_at' => $data['occurred_at'] ?? ''];
                $payload = ['phone_number' => $r['mobile'], 'template_name' => $template->template_name, 'template_language' => $template->language_code ?: 'en'];
                foreach ($fields as $i => $field) {
                    if ($field === '') {
                        continue;
                    }
                    $source = $mapping[$labels[$i] ?? ''] ?? null;   // "event.title"
                    $name = $source ? substr($source, strpos($source, '.') + 1) : null;
                    $payload[$field] = (string) ($name !== null ? ($values[$name] ?? '') : '');
                }

                $result = MetaWhatsappTemplateSender::send($payload, $template->metaapi_id ? (int) $template->metaapi_id : null,
                    ['template_for' => $templateFor, 'autometanotification_id' => $rule->id, 'metatemplate_id' => $template->id, 'reference_id' => $context['id'] ?? null]);
                self::log($event, 'whatsapp', $result['ok'] ? 'sent' : 'failed', $r['mobile'], $r['type'], $partnerId, $result['provider'], $template->template_name, $result['ok'] ? null : $result['message'], $key, $context);
            }
        }
    }

    /** Channels an event sends here: its own, limited by $data['channels']. */
    private static function channels(string $event, array $data): array
    {
        $wanted = isset($data['channels']) ? (array) $data['channels'] : ['email', 'whatsapp'];

        return array_values(array_filter(['email', 'whatsapp'], fn ($c) => in_array($c, $wanted, true) && NotificationEvents::hasChannel($event, $c)));
    }

    /** [['type', 'name', 'email', 'mobile'], ...] from server-side records only. */
    private static function recipients(string $type, ?int $partnerId, array $data): array
    {
        $digits = fn ($v) => preg_replace('/\D+/', '', (string) $v) ?: null;

        switch ($type) {
            case 'staff':
                return array_map(fn ($r) => ['type' => 'staff', 'name' => $r['name'], 'email' => $r['email'], 'mobile' => $r['mobile']], PartnerActivity::recipients());

            case 'partner':
                $partner = $partnerId ? DB::table('partners')->where('id', $partnerId)->first() : null;
                if (!$partner) {
                    return [];
                }
                [$code, $local] = array_pad(array_values(PartnerContact::primaryMobile($partner)), 2, null);

                return [['type' => 'partner', 'name' => (string) ($partner->owner_name ?: $partner->rec_off_name), 'email' => filter_var((string) $partner->email, FILTER_VALIDATE_EMAIL) ?: null, 'mobile' => $local ? $digits($code . $local) : null]];

            case 'customer':
                $customer = !empty($data['customer_id']) ? DB::table('users')->where('id', (int) $data['customer_id'])->first() : null;
                // A customer is only ever reached for its own partner's events.
                if (!$customer || ($partnerId && $customer->partner_id && (int) $customer->partner_id !== $partnerId)) {
                    return [];
                }

                return [['type' => 'customer', 'name' => (string) $customer->name, 'email' => filter_var((string) $customer->email, FILTER_VALIDATE_EMAIL) ?: null, 'mobile' => $digits($customer->mobile_no)]];

            case 'team_member':
                $member = !empty($data['team_member_id']) && $partnerId
                    ? DB::table('partner_team_members')->where('id', (int) $data['team_member_id'])->where('partner_id', $partnerId)->first()
                    : null;
                if (!$member) {
                    return [];
                }

                return [['type' => 'team_member', 'name' => (string) ($member->full_name ?: $member->username), 'email' => filter_var((string) $member->email, FILTER_VALIDATE_EMAIL) ?: null, 'mobile' => $member->mobile ? $digits($member->country_code . $member->mobile) : null]];

            case 'previous_email':
                return !empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)
                    ? [['type' => 'partner', 'name' => (string) ($data['recipient_name'] ?? ''), 'email' => $data['email'], 'mobile' => null]]
                    : [];
        }

        return [];
    }

    /** Header/footer branding: the partner's (customer / team member mail) or RecruitmentCV's. */
    private static function brand(?int $partnerId, bool $partnerBranded): array
    {
        $root = (string) config('services.hostinger.recruitmentcv_domain', 'recruitmentcv.com') ?: 'recruitmentcv.com';
        $brand = ['name' => 'RecruitmentCV', 'site_url' => 'https://' . $root];

        if ($partnerId && $partnerBranded) {
            $company = DB::table('domains')->where('partner_id', $partnerId)->value('company_name');
            $brand['name'] = (string) ($company ?: DB::table('partners')->where('id', $partnerId)->value('rec_off_name') ?: 'RecruitmentCV');
            $brand['site_url'] = self::partnerSiteUrl($partnerId);
        }

        return $brand;
    }

    /**
     * A send error fit for the log: SmtpFailoverTransport's own message is
     * already generic; a .env-mailer error can quote the SMTP username or
     * server dialogue, so usernames and the configured password are masked.
     */
    private static function safeReason(\Throwable $e): string
    {
        $message = preg_replace('/\s+/', ' ', $e->getMessage());
        $message = preg_replace('/username "[^"]*"/i', 'username "***"', $message);
        foreach ((array) config('mail.mailers') as $mailer) {
            foreach (['password', 'username'] as $secret) {
                if (is_array($mailer) && !empty($mailer[$secret]) && is_string($mailer[$secret])) {
                    $message = str_replace([$mailer[$secret], base64_encode($mailer[$secret])], '***', $message);
                }
            }
        }

        return Str::limit($message, 300);
    }

    /** Same event + channel + recipient + business id = the same message (duplicate check key). */
    public static function dedupeKey(string $event, string $channel, string $recipient, string $businessId): string
    {
        return hash('sha256', $event . '|' . $channel . '|' . strtolower($recipient) . '|' . $businessId);
    }

    public static function alreadySent(string $key): bool
    {
        return DB::table('recruitment_notification_logs')->where('dedupe_key', $key)->whereIn('status', ['sent', 'queued'])->exists();
    }
}
