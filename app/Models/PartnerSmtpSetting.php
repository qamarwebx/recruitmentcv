<?php

namespace App\Models;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * A partner's own SMTP sender (Partner Portal -> Website -> SMTP), used for
 * the emails RecruitmentCV sends to that partner's customers. At most one
 * row per partner (partner_smtp_settings, created by the CRM's
 * 2026_09_29_150100 migration). No row, an inactive row or an incomplete
 * one = the global Website SMTP / .env mailer
 * (WebsiteSmtpSetting::mailerOrDefault()).
 *
 * `password` is encrypted at rest with the shared APP_KEY and hidden from
 * every array/JSON serialization; it is only ever read to build a mailer.
 * The partner always comes from the server side (partner guard, or the
 * booking/site the email is about) - never from a request field.
 */
class PartnerSmtpSetting extends Model
{
    public const MAILERS = WebsiteSmtpSetting::MAILERS;

    public const ENCRYPTIONS = WebsiteSmtpSetting::ENCRYPTIONS;

    /** Ports a partner may configure (standard SMTP submission ports). */
    public const PORTS = [25, 465, 587, 2525];

    protected $fillable = [
        'partner_id',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'from_address',
        'from_name',
        'status',
        'updated_by',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'encrypted',
        'port' => 'integer',
        'status' => 'boolean',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public static function forPartner(?int $partnerId): ?self
    {
        return $partnerId ? static::where('partner_id', $partnerId)->first() : null;
    }

    /**
     * The mailer for emails to this partner's customers: the partner's own
     * SMTP when it is enabled and complete, otherwise the global Website
     * SMTP / .env mailer - also when the row can't be read (e.g. database
     * or decryption error), so email keeps working either way.
     */
    public static function mailerFor(?int $partnerId): Mailer
    {
        try {
            $smtp = static::forPartner($partnerId);

            if ($smtp && $smtp->isUsable()) {
                return $smtp->mailer();
            }
        } catch (\Throwable $e) {
            // Class only - never the message (it could echo settings).
            Log::warning('Partner SMTP unavailable, using the default mailer', ['partner_id' => $partnerId, 'exception' => get_class($e)]);
        }

        return WebsiteSmtpSetting::mailerOrDefault();
    }

    public function isUsable(): bool
    {
        return $this->status && $this->isComplete();
    }

    /** Every value a working SMTP sender needs is set. */
    public function isComplete(): bool
    {
        return filled($this->host)
            && $this->port > 0
            && filled($this->username)
            && filled($this->password)
            && filled($this->from_address);
    }

    /**
     * A mailer bound to this partner's configuration only, registered as a
     * runtime mailer named after the partner (purged first so an earlier
     * instance is never reused). The app's default mailer is untouched.
     */
    public function mailer(): Mailer
    {
        $name = 'partner_smtp_' . $this->partner_id;

        config(['mail.mailers.' . $name => [
            'transport' => 'smtp',
            'host' => $this->host,
            'port' => $this->port,
            'encryption' => $this->encryption ?: null,
            'username' => $this->username,
            'password' => $this->password,
            'timeout' => 20,
        ]]);

        Mail::purge($name);

        $mailer = Mail::mailer($name);
        $mailer->alwaysFrom($this->from_address, $this->from_name ?: null);

        return $mailer;
    }
}
