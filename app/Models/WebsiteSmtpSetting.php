<?php

namespace App\Models;

use App\Mail\Transport\SmtpAttemptTransport;
use App\Support\GoogleSmtpOAuth;
use App\Support\SmtpMailer;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailer as LaravelMailer;
use Symfony\Component\Mailer\Transport\Smtp\Auth\XOAuth2Authenticator;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;

/**
 * One RecruitmentCV SMTP server (CRM -> Website -> SMTP, Partner Portal ->
 * Website -> SMTP). partner_id NULL = a Global SMTP (recruitmentcv.com, and
 * the fallback for every partner); otherwise that partner's own. Each
 * website keeps an ordered list - priority 1 is tried first - which
 * App\Support\SmtpMailer sends through with strictly sequential failover
 * and App\Support\SmtpSettings manages. Kept identical in the CRM and
 * RecruitmentCV codebases (same website_smtp_settings table).
 *
 * `password` is encrypted at rest with the shared APP_KEY and hidden from
 * every array/JSON serialization; it is only ever read to build a transport.
 *
 * mailer = MAILER_GOOGLE ("Add SMTP With Google"): Gmail with OAuth 2.0 -
 * `password` then holds the Google refresh token (same encryption, same
 * write-only handling), `username` / `from_address` the authorized Gmail
 * address, and the SMTP login is XOAUTH2 with a short-lived access token
 * (App\Support\GoogleSmtpOAuth). Without a configured OAuth client such a
 * row is incomplete, so it is skipped when sending.
 */
class WebsiteSmtpSetting extends Model
{
    public const MAILERS = ['smtp' => 'SMTP', self::MAILER_GOOGLE => 'Google (OAuth 2.0)'];

    /** Gmail SMTP authenticated with Google OAuth 2.0 (XOAUTH2). */
    public const MAILER_GOOGLE = 'google_oauth';

    public const ENCRYPTIONS = ['ssl' => 'SSL', 'tls' => 'TLS', '' => 'None'];

    /** Ports a partner SMTP may use (standard SMTP submission ports). */
    public const PARTNER_PORTS = [25, 465, 587, 2525];

    /** SMTPs per website (Global, or one partner). */
    public const MAX_PER_WEBSITE = 10;

    /** Seconds before an unreachable SMTP counts as failed (the next one is then tried). */
    public const TIMEOUT = 20;

    protected $fillable = [
        'partner_id',
        'name',
        'priority',
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
        'partner_id' => 'integer',
        'priority' => 'integer',
        'port' => 'integer',
        'status' => 'boolean',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /** The SMTPs of one website: that partner's own, or (null) the Global ones. */
    public function scopeOfWebsite(Builder $query, ?int $partnerId): Builder
    {
        return $partnerId ? $query->where('partner_id', $partnerId) : $query->whereNull('partner_id');
    }

    /** Sending order: priority, then the oldest first. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('priority')->orderBy('id');
    }

    /** The Global SMTP tried first. */
    public static function current(): ?self
    {
        return static::ofWebsite(null)->ordered()->first();
    }

    /** The Global failover mailer (Global SMTPs in order, else the .env mailer). */
    public static function mailerOrDefault(): Mailer
    {
        return SmtpMailer::global();
    }

    /** Access token used by this instance's transport (scrubbed from errors; never stored). */
    private ?string $oauthAccessToken = null;

    public function isUsable(): bool
    {
        return $this->status && $this->isComplete();
    }

    /** Gmail with Google OAuth 2.0 ("Add SMTP With Google"). */
    public function isGoogle(): bool
    {
        return $this->mailer === self::MAILER_GOOGLE;
    }

    /** Every value a working SMTP sender needs is set (a Google SMTP: and the OAuth client to refresh with). */
    public function isComplete(): bool
    {
        $mailer = $this->mailer ?: 'smtp';

        return in_array($mailer, ['smtp', self::MAILER_GOOGLE], true)
            && ($mailer === 'smtp' || GoogleSmtpOAuth::canRefresh())
            && filled($this->host)
            && $this->port > 0
            && filled($this->username)
            && filled($this->password)
            && filled($this->from_address);
    }

    /** A password is saved (without decrypting or exposing it). */
    public function hasPassword(): bool
    {
        return filled($this->getRawOriginal('password'));
    }

    /** Its own name, else "SMTP #<position>". */
    public function label(int $position): string
    {
        return filled($this->name) ? (string) $this->name : 'SMTP #' . $position;
    }

    /** Laravel mail config for this SMTP alone. Holds the password: never log it. */
    public function transportConfig(): array
    {
        return [
            'transport' => 'smtp',
            'host' => $this->host,
            'port' => $this->port,
            'encryption' => $this->encryption ?: null,
            'username' => $this->username,
            'password' => $this->password,
            'timeout' => self::TIMEOUT,
        ];
    }

    /**
     * The bare Symfony SMTP transport (connection checks). A Google SMTP logs
     * in with XOAUTH2 only - username = the Gmail address, password = a
     * current access token (GoogleSmtpOAuth::accessToken(), which throws
     * GoogleSmtpException when the authorization no longer works).
     */
    public function symfonyTransport(): TransportInterface
    {
        if (!$this->isGoogle()) {
            return app('mail.manager')->createSymfonyTransport($this->transportConfig());
        }

        $this->oauthAccessToken = GoogleSmtpOAuth::accessToken((string) $this->password);
        $transport = app('mail.manager')->createSymfonyTransport(['password' => $this->oauthAccessToken] + $this->transportConfig());
        if ($transport instanceof EsmtpTransport) {
            $transport->setAuthenticators([new XOAuth2Authenticator()]);
        }

        return $transport;
    }

    /** $text with this SMTP's secrets (password / refresh token, access token, their base64) masked. */
    public function scrub(string $text): string
    {
        $secrets = [$this->oauthAccessToken];
        try {
            $secrets[] = (string) $this->password;
        } catch (\Throwable $e) {
            // Unreadable password: nothing to mask from it.
        }
        foreach (array_filter($secrets, fn ($secret) => is_string($secret) && $secret !== '') as $secret) {
            $text = str_replace([$secret, base64_encode($secret)], '********', $text);
        }

        return $text;
    }

    /** This SMTP as one step of a failover chain, sending as its own From. */
    public function transport(int $position = 1): TransportInterface
    {
        return new SmtpAttemptTransport($this, $this->symfonyTransport(), $position);
    }

    /**
     * A mailer bound to this SMTP only - no failover (Test email). Built
     * directly (not registered), so it is never cached or reused and works
     * for unsaved settings too.
     */
    public function mailer(): Mailer
    {
        return new LaravelMailer('website_smtp_' . ($this->id ?: 'unsaved'), app('view'), $this->transport(), app('events'));
    }
}
