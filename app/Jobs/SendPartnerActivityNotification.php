<?php

namespace App\Jobs;

use App\Mail\PartnerActivityAlert;
use App\Models\Metawhatsapptemplate;
use App\Models\PartnerActivityEvent;
use App\Support\MetaWhatsappTemplateSender;
use App\Support\NotificationCenter;
use App\Support\NotificationSettings;
use App\Support\PartnerActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Partner Activity alert (App\Support\PartnerActivity) to every configured
 * CRM staff recipient:
 *
 * - Email: the professional HTML alert (PartnerActivityAlert) through the
 *   Global SMTPs in failover order (CRM -> Website -> SMTP; the .env mailer
 *   when none is set up) - SmtpMailer::global().
 * - WhatsApp: the active Meta Automation rules for template_for
 *   "partner_activity" with this event's trigger, each sending its approved
 *   template (variables from config constants.template_for_map) through the
 *   existing WhatsApp API (MetaWhatsappTemplateSender).
 *
 * Each channel follows CRM -> Website -> Settings -> Candidate/Recruitment
 * (NotificationSettings: partner override -> Global; the event key is the
 * trigger name) and every message is logged in Notification Logs
 * (NotificationCenter).
 *
 * $context is built server-side when the event fires (partner, candidate,
 * URL). Dispatched from RecruitmentCV, run by the CRM queue worker - twin
 * file in both apps. Not retried, so an alert is never sent twice.
 */
class SendPartnerActivityNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public function __construct(public array $context)
    {
    }

    public function handle()
    {
        $event = PartnerActivity::EVENTS[$this->context['event'] ?? '']['trigger'] ?? null;
        if (!$event) {
            return;
        }
        NotificationSettings::forget();   // latest settings for every job (long-running worker)
        $partnerId = isset($this->context['partner_id'])
            ? (int) $this->context['partner_id']
            : ((int) PartnerActivityEvent::whereKey($this->context['event_id'] ?? 0)->value('partner_id') ?: null);
        $log = ['activity_event_id' => $this->context['event_id'] ?? null, 'candidate' => $this->context['candidate_ref'] ?? null];

        $recipients = PartnerActivity::recipients();
        if (!$recipients) {
            NotificationCenter::log($event, 'email', 'skipped', null, 'staff', $partnerId, null, 'Partner Activity alert', 'No Partner Notification recipients configured', null, $log);

            return;
        }

        if (!NotificationSettings::enabled($event, 'email', $partnerId)) {
            NotificationCenter::log($event, 'email', 'skipped', null, 'staff', $partnerId, null, 'Partner Activity alert', 'Disabled in Notification Settings', null, $log);
        } else {
            foreach ($recipients as $recipient) {
                if (!$recipient['email']) {
                    NotificationCenter::log($event, 'email', 'skipped', $recipient['name'], 'staff', $partnerId, null, 'Partner Activity alert', 'No Work Email CRM Notification', null, $log);
                    continue;
                }
                NotificationCenter::sendEmail($event, $partnerId, $recipient['email'], new PartnerActivityAlert($this->context), 'global', [
                    'recipient_type' => 'staff', 'dedupe' => 'activity:' . ($this->context['event_id'] ?? ''), 'context' => $log,
                ]);
            }
        }

        if (!NotificationSettings::enabled($event, 'whatsapp', $partnerId)) {
            NotificationCenter::log($event, 'whatsapp', 'skipped', null, 'staff', $partnerId, null, $event, 'Disabled in Notification Settings', null, $log);

            return;
        }
        $this->sendWhatsapp($event, $partnerId, $recipients, $log);
    }

    private function sendWhatsapp(string $event, ?int $partnerId, array $recipients, array $log): void
    {
        $rules = NotificationCenter::whatsappRules($event);
        if (!$rules->count()) {
            NotificationCenter::log($event, 'whatsapp', 'skipped', null, 'staff', $partnerId, null, $event, 'Template Not Configured (Meta Automation: ' . PartnerActivity::TEMPLATE_FOR . ' / ' . $event . ')', null, $log);

            return;
        }

        $mapping = (array) config('constants.template_for_map.' . PartnerActivity::TEMPLATE_FOR);

        foreach ($rules as $rule) {
            $template = Metawhatsapptemplate::where('id', $rule->metatemp_id)->where('status', 1)->first();
            if (!$template || !$template->template_name) {
                continue;
            }

            // field_n -> variable label (same storage as the order templates).
            $fields = array_map('trim', explode(',', (string) $template->field_variable));
            $labels = array_map('trim', explode(',', str_replace(['[', ']'], '', (string) $template->assign_variable)));
            $values = [];
            foreach ($fields as $i => $field) {
                if ($field === '') {
                    continue;
                }
                $source = $mapping[$labels[$i] ?? ''] ?? null;   // e.g. "activity.partner_name"
                $key = $source ? substr($source, strpos($source, '.') + 1) : null;
                $values[$field] = (string) ($key !== null ? ($this->context[$key] ?? '') : '');
            }

            foreach ($recipients as $recipient) {
                if (!$recipient['mobile']) {
                    NotificationCenter::log($event, 'whatsapp', 'skipped', $recipient['name'], 'staff', $partnerId, null, $template->template_name, 'No Work Mobile No CRM Notification', null, $log);
                    continue;
                }
                $key = NotificationCenter::dedupeKey($event, 'whatsapp:' . $template->id, $recipient['mobile'], 'activity:' . ($this->context['event_id'] ?? ''));
                if (NotificationCenter::alreadySent($key)) {
                    NotificationCenter::log($event, 'whatsapp', 'skipped', $recipient['mobile'], 'staff', $partnerId, null, $template->template_name, 'Duplicate - already sent for this event', $key, $log);
                    continue;
                }
                $result = MetaWhatsappTemplateSender::send(
                    ['phone_number' => $recipient['mobile'], 'template_name' => $template->template_name, 'template_language' => $template->language_code ?: 'en'] + $values,
                    $template->metaapi_id ? (int) $template->metaapi_id : null,
                    ['template_for' => PartnerActivity::TEMPLATE_FOR, 'autometanotification_id' => $rule->id, 'metatemplate_id' => $template->id, 'reference_id' => $this->context['event_id'] ?? null]
                );
                NotificationCenter::log($event, 'whatsapp', $result['ok'] ? 'sent' : 'failed', $recipient['mobile'], 'staff', $partnerId, $result['provider'], $template->template_name, $result['ok'] ? null : $result['message'], $key, $log);
            }
        }
    }
}
