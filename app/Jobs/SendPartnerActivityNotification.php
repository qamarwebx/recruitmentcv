<?php

namespace App\Jobs;

use App\Mail\PartnerActivityAlert;
use App\Models\Autometanotification;
use App\Models\Metawhatsapptemplate;
use App\Support\SmtpMailer;
use App\Support\MetaWhatsappTemplateSender;
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
        $recipients = PartnerActivity::recipients();
        if (!$recipients) {
            return;
        }

        foreach ($recipients as $recipient) {
            if (!$recipient['email']) {
                continue;
            }
            try {
                SmtpMailer::global()->to($recipient['email'])->send(new PartnerActivityAlert($this->context));
            } catch (\Throwable $e) {
                Log::warning('Partner activity email not sent', ['event_id' => $this->context['event_id'] ?? null, 'admin_id' => $recipient['admin_id'], 'error' => $e->getMessage()]);
            }
        }

        $this->sendWhatsapp($recipients);
    }

    private function sendWhatsapp(array $recipients): void
    {
        $trigger = PartnerActivity::EVENTS[$this->context['event'] ?? '']['trigger'] ?? null;
        if (!$trigger) {
            return;
        }

        $rules = Autometanotification::where('template_for', PartnerActivity::TEMPLATE_FOR)
            ->where('trigger_template_type', $trigger)
            ->where('status', 1)
            ->get();

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
                    continue;
                }
                MetaWhatsappTemplateSender::send(
                    ['phone_number' => $recipient['mobile'], 'template_name' => $template->template_name, 'template_language' => $template->language_code ?: 'en'] + $values,
                    $template->metaapi_id ? (int) $template->metaapi_id : null,
                    ['template_for' => PartnerActivity::TEMPLATE_FOR, 'autometanotification_id' => $rule->id, 'metatemplate_id' => $template->id, 'reference_id' => $this->context['event_id'] ?? null]
                );
            }
        }
    }
}
