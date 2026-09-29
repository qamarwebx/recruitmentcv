<?php

use App\Models\WebsiteSmtpSetting;
use Illuminate\Database\Migrations\Migration;

/**
 * Copies the SMTP settings RecruitmentCV currently sends with (its .env
 * mailer, as resolved by config/mail.php) into the global
 * website_smtp_settings row, so CRM -> Website -> SMTP opens with the
 * working configuration and email keeps going out exactly as before.
 * Only runs when no global row exists yet - never overwrites one saved
 * from the CRM. The password is stored encrypted (WebsiteSmtpSetting's
 * cast) and is never printed.
 */
return new class extends Migration
{
    public function up()
    {
        if (WebsiteSmtpSetting::current()) {
            return;
        }

        $smtp = config('mail.mailers.smtp');
        $encryption = strtolower((string) ($smtp['encryption'] ?? ''));

        if (config('mail.default') !== 'smtp' || empty($smtp['host']) || empty($smtp['username']) || empty($smtp['password'])) {
            // Nothing complete to import: the CRM starts empty and the .env fallback stays in use.
            return;
        }

        $setting = new WebsiteSmtpSetting([
            'mailer' => 'smtp',
            'host' => $smtp['host'],
            'port' => (int) $smtp['port'],
            'encryption' => in_array($encryption, ['ssl', 'tls'], true) ? $encryption : null,
            'username' => $smtp['username'],
            'password' => $smtp['password'],
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'status' => true,
        ]);
        $setting->id = WebsiteSmtpSetting::ID;
        $setting->save();
    }

    public function down()
    {
        // Intentionally no-op: after this runs the row is managed from the CRM.
    }
};
