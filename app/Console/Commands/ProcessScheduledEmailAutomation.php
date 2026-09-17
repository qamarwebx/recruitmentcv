<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Models\ScheduledSendEmailAutomation;
use App\Models\Autoemailnotification;
use App\Models\EmailTemplate;
use App\Models\EmailSmtp;
use Carbon\Carbon;

class ProcessScheduledEmailAutomation extends Command
{
    protected $signature = 'automation:process-email-scheduled';
    protected $description = 'Process scheduled email automation every minute';

    /** Map template_for → email field */
    protected $emailMap = [
        'contactpluses'  => 'prim_email',
        'allcontacts'    => 'email',
        'associates'     => 'pty_email',
        'partners'       => 'primary_email',
        'employers'      => 'email',
        'leads'          => 'email',
        'todos'          => 'email',
    ];

    public function handle()
    {
        Log::channel('scheduled_email_automation')->info("📨 EMAIL CRON START");

        try {
            $now = Carbon::now();

            $pending = ScheduledSendEmailAutomation::where('status', 0)
                ->whereBetween('calculated_time', [
                    $now->copy()->startOfMinute(),
                    $now->copy()->endOfMinute()
                ])
                ->get();

            Log::channel('scheduled_email_automation')->info("📌 Jobs Found", [
                'count' => $pending->count()
            ]);

            if ($pending->isEmpty()){
                Log::channel('scheduled_email_automation')->info("📨 EMAIL CRON FINISHED — NO JOBS");
                return;
            }

            foreach ($pending as $job) {
                $this->processSingleJob($job);
            }

        } catch (\Exception $ex) {
            Log::channel('scheduled_email_automation')->error("🔥 FATAL ERROR", $this->err($ex));
        }

        Log::channel('scheduled_email_automation')->info("📨 EMAIL CRON FINISHED");
    }


    // ============================================
    // PROCESS ONE JOB
    // ============================================
    private function processSingleJob($job)
    {
        try {

            $data = $this->prepareEmail($job);
            if (!$data) {
                Log::channel('scheduled_email_automation')->info("⛔ Skipping job (prepareEmail returned false)", ['job_id' => $job->id]);
                // Mark as processed? You currently mark after send — keep logic as you prefer.
                // $job->update(['status' => 1]);
                return;
            }

            Log::channel('scheduled_email_automation')->info("📤 Sending Email To: " . $data['to'], ['job_id' => $job->id]);

            $sent = $this->sendEmail($data);

            Log::channel('scheduled_email_automation')->info(
                $sent ? '✅ Email Sent' : '❌ Email Failed',
                ['job_id' => $job->id]
            );

            // Mark Processed
            $job->update(['status' => 1]);

        } catch (\Exception $ex) {
            Log::channel('scheduled_email_automation')->error("❌ Email Job Error", $this->err($ex, ['job_id' => $job->id]));
        }
    }


    // ============================================
    // PREPARE EMAIL (WITH VARIABLE REPLACEMENT)
    // ============================================
    private function prepareEmail($job)
    {
        $emailNotification = Autoemailnotification::find($job->autoemailnotifications_id);
        if (!$emailNotification) return $this->fail("Meta email missing", $job);

        $template = EmailTemplate::find($job->emailtemp_id);
        if (!$template) return $this->fail("Email template missing", $job);

        // Attachment support
        if ($template->attachment) {
            $template->attachment_url  = asset("admin/assets/images/email-template/{$template->attachment}");
            $template->attachment_path = public_path("admin/assets/images/email-template/{$template->attachment}");
        } else {
            $template->attachment_url  = null;
            $template->attachment_path = null;
        }

        // Validate table
        if (!Schema::hasTable($job->template_table_name)) {
            return $this->fail("Table missing", $job);
        }

        // Get user
        $user = DB::table($job->template_table_name)->find($job->send_user_to);
        if (!$user) return $this->fail("User not found", $job);

        // Which field to pick for email?
        $emailField = $this->emailMap[$job->template_for] ?? 'email';
        $userEmail = $user->{$emailField} ?? null;

        if (!$userEmail) {
            return $this->fail("User email missing", $job, ['email_field' => $emailField]);
        }

        // --------------------------
        // field_not_completed check
        // --------------------------
        // If field_not_completed is present on autometa → send only when any of those fields are empty.
        if (!$this->passesFieldNotCompletedCheck($emailNotification, $user)) {
            Log::channel('scheduled_email_automation')->info("⛔ Skipping: field_not_completed criteria not met (all fields present)", [
                'job_id' => $job->id,
                'autoemailnotifications_id' => $emailNotification->id,
                'field_not_completed' => $emailNotification->field_not_completed
            ]);
            return false;
        }

        // LOAD SMTP
        $emailSmtp = EmailSmtp::find($template->smtp_id);
        if (!$emailSmtp) return $this->fail("SMTP missing", $job);

        // --------------------------
        // DYNAMIC VARIABLE HANDLING
        // --------------------------
        $body = $template->email_body ?? '';
        $fieldVars  = $template->field_variable ? array_map('trim', explode(",", $template->field_variable)) : [];
        $assignVars = $template->assign_variable ? array_map('trim', explode(",", $template->assign_variable)) : [];

        // For each field_1, field_2, field_3 ...
        foreach ($fieldVars as $i => $fieldKey) {

            // Template variable ex: {{1}}, {{2}}, …
            $variableTag = "{{" . ($i+1) . "}}";

            // If matching assign variable exists
            if (isset($assignVars[$i]) && $assignVars[$i] !== '') {

                $colName = trim($assignVars[$i]);
                $value = $user->{$colName} ?? '';

                // Replace placeholder with column value (may be empty)
                $body = str_replace($variableTag, $value, $body);
            } else {
                // If mapping missing → remove placeholder (leave text readable)
                $body = str_replace($variableTag, '', $body);
            }
        }

        // Remove any leftover numeric placeholders like {{5}} etc.
        $body = preg_replace('/\{\{\d+\}\}/', '', $body);

        // --------------------------
        // STYLE WRAP
        // --------------------------
        $emailBg = $template->email_body_bg ?? '#FFFFFF';

        $body = "<div style='background:$emailBg; padding:20px;'>$body</div>";
        $body = str_replace("<table", "<table style='border-collapse:collapse;width:100%;border:2px solid #000'", $body);
        $body = str_replace("<th", "<th style='border:1px solid #000;padding:8px;background:#000;color:#fff;text-align:left'", $body);
        $body = str_replace("<td", "<td style='border:1px solid #000;padding:8px'", $body);

        return [
            'to'                => $userEmail,
            'subject'           => $template->subject ?? 'QAMR INTERNATIONAL',
            'body'              => $body,
            'attachment_path'   => $template->attachment_path,
            'attachment_url'    => $template->attachment_url,
            'user'              => $user,
            'emailNotification' => $emailNotification,
            'smtp'              => $emailSmtp
        ];
    }


    // ============================================
    // field_not_completed policy helper
    // ============================================
    /**
     * Returns true if the email SHOULD be sent.
     *
     * Rules:
     * - If $emailNotification->field_not_completed is empty → always send (returns true).
     * - If it's a JSON array of column names → send only if ANY of those columns are empty on $user.
     *   (i.e. presence of an empty required field triggers sending)
     */
    private function passesFieldNotCompletedCheck($emailNotification, $user)
    {
        if (empty($emailNotification->field_not_completed)) {
            return true; // no restriction -> send
        }

        // field_not_completed stored as JSON like: ["cand_name","whatsapp_no"]
        $fields = json_decode($emailNotification->field_not_completed, true);
        if (!is_array($fields) || empty($fields)) {
            return true; // malformed -> don't block sending
        }

        // If any of the specified fields on the user row is empty -> send
        foreach ($fields as $col) {
            $col = trim($col);
            if ($col === '') continue;
            $val = $user->{$col} ?? null;
            if (empty(trim((string)$val))) {
                // found missing field -> should send
                return true;
            }
        }

        // All fields present -> do NOT send
        return false;
    }


    // ============================================
    // SEND EMAIL
    // ============================================
    private function sendEmail($data)
    {
        try {
            $smtp = $data['smtp'];

            // Backup current mail config
            $backup = [
                'mailer'        => config('mail.default'),
                'host'          => config('mail.mailers.smtp.host'),
                'port'          => config('mail.mailers.smtp.port'),
                'username'      => config('mail.mailers.smtp.username'),
                'password'      => config('mail.mailers.smtp.password'),
                'encryption'    => config('mail.mailers.smtp.encryption'),
                'from_address'  => config('mail.from.address'),
                'from_name'     => config('mail.from.name'),
            ];

            // Apply dynamic SMTP
            config([
                'mail.default'                   => 'smtp',
                'mail.mailers.smtp.transport'    => 'smtp',
                'mail.mailers.smtp.host'         => $smtp->mail_host,
                'mail.mailers.smtp.port'         => $smtp->mail_port,
                'mail.mailers.smtp.username'     => $smtp->mail_username,
                'mail.mailers.smtp.password'     => $smtp->mail_password,
                'mail.mailers.smtp.encryption'   => $smtp->mail_encryption,
                'mail.from.address'              => $smtp->from_address,
                'mail.from.name'                 => $smtp->from_name,
            ]);

            // Send mail
            Mail::send([], [], function ($m) use ($data, $smtp) {
                $m->to($data['to'])
                ->subject($data['subject'])
                ->from($smtp->from_address, $smtp->from_name)
                ->html($data['body']);

                if (!empty($data['attachment_path']) && file_exists($data['attachment_path'])) {
                    $m->attach($data['attachment_path'], [
                        'as' => basename($data['attachment_path']),
                        'mime' => mime_content_type($data['attachment_path'])
                    ]);
                }
            });

            // Restore mail settings
            config([
                'mail.default'                   => $backup['mailer'],
                'mail.mailers.smtp.host'         => $backup['host'],
                'mail.mailers.smtp.port'         => $backup['port'],
                'mail.mailers.smtp.username'     => $backup['username'],
                'mail.mailers.smtp.password'     => $backup['password'],
                'mail.mailers.smtp.encryption'   => $backup['encryption'],
                'mail.from.address'              => $backup['from_address'],
                'mail.from.name'                 => $backup['from_name'],
            ]);

            return true;

        } catch (\Exception $ex) {

            Log::channel('scheduled_email_automation')->error("❌ Mail Sending Failed", $this->err($ex));

            return false;
        }
    }


    private function err($ex, $extra = [])
    {
        return array_merge([
            'error' => $ex->getMessage(),
            'line'  => $ex->getLine(),
            'file'  => $ex->getFile(),
        ], $extra);
    }

    private function fail($msg, $job, $extra = [])
    {
        Log::channel('scheduled_email_automation')->warning("⚠ $msg", array_merge(['job_id' => $job->id], $extra));
        return false;
    }
}
