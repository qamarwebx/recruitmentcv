<?php

namespace App\Models;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * The single global SMTP configuration for RecruitmentCV (CRM -> Website ->
 * SMTP), used for partner email-change verification - one config for
 * every partner, never per partner. Always row id 1. Kept identical in the
 * CRM and RecruitmentCV codebases (same website_smtp_settings table).
 *
 * `password` is encrypted at rest with the shared APP_KEY and hidden from
 * every array/JSON serialization; it is only ever read to build a mailer.
 */
class WebsiteSmtpSetting extends Model
{
    public const ID = 1;

    public const MAILERS = ['smtp' => 'SMTP'];

    public const ENCRYPTIONS = ['ssl' => 'SSL', 'tls' => 'TLS', '' => 'None'];

    private const MAILER = 'website_smtp';

    protected $fillable = [
        'mailer',
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

    public static function current(): ?self
    {
        return static::find(static::ID);
    }

    /**
     * The mailer RecruitmentCV should send through: this global SMTP when
     * it is enabled and complete, otherwise the app's default (.env)
     * mailer - also when the row can't be read (e.g. database or
     * decryption error), so email keeps working either way.
     */
    public static function mailerOrDefault(): Mailer
    {
        try {
            $smtp = static::current();

            if ($smtp && $smtp->isUsable()) {
                return $smtp->mailer();
            }
        } catch (\Throwable $e) {
            // Class only - never the message (it could echo settings).
            Log::warning('Website SMTP unavailable, using the default mailer', ['exception' => get_class($e)]);
        }

        return Mail::mailer();
    }

    public function isUsable(): bool
    {
        return $this->status
            && $this->mailer === 'smtp'
            && filled($this->host)
            && $this->port > 0
            && filled($this->username)
            && filled($this->password)
            && filled($this->from_address);
    }

    /**
     * A mailer bound to this configuration only, registered as a runtime
     * mailer (purged first so an earlier instance is never reused).
     */
    public function mailer(): Mailer
    {
        config(['mail.mailers.' . self::MAILER => [
            'transport' => 'smtp',
            'host' => $this->host,
            'port' => $this->port,
            'encryption' => $this->encryption ?: null,
            'username' => $this->username,
            'password' => $this->password,
            'timeout' => 20,
        ]]);

        Mail::purge(self::MAILER);

        $mailer = Mail::mailer(self::MAILER);
        $mailer->alwaysFrom($this->from_address, $this->from_name);

        return $mailer;
    }
}
