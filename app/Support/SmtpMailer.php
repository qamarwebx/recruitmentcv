<?php

namespace App\Support;

use App\Mail\Transport\SmtpFailoverTransport;
use App\Models\WebsiteSmtpSetting;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\TransportInterface;

/**
 * Which SMTPs an email goes through, in order - the one mail router of
 * both apps (twin file). The default mailer ("smtp_failover", config/
 * mail.php) uses the website of the current request, so every existing
 * Mail::to(), notification, verification and password-reset email follows
 * it without changes:
 *
 * - a partner website/dashboard (app('currentPartner'), set from the Host
 *   by ResolvePartnerWebsiteDomain): that partner's SMTPs, then the Global
 *   SMTPs;
 * - recruitmentcv.com, the CRM, queue jobs and console commands: the Global
 *   SMTPs;
 * - none configured/usable: the .env mailer (mail.fallback).
 *
 * Sending is strictly sequential failover (SmtpFailoverTransport): SMTP #1
 * first, the next only after a failure, stop at the first success.
 */
final class SmtpMailer
{
    /** Transport + default mailer name (config/mail.php). */
    public const TRANSPORT = 'smtp_failover';

    public const SITE = 'site';

    public const GLOBAL = 'global';

    public const PARTNER = 'partner';

    /**
     * Which server delivered the last email of this process
     * (['provider' => 'SMTP smtp.gmail.com (Global #1)'] or the .env mailer) -
     * read by App\Support\NotificationCenter for its log; never credentials.
     */
    public static ?array $lastDelivery = null;

    /** AppServiceProvider::boot(): makes "smtp_failover" a mail transport. */
    public static function register(): void
    {
        Mail::extend(self::TRANSPORT, fn (array $config) => new SmtpFailoverTransport(
            $config['context'] ?? self::SITE,
            isset($config['partner_id']) ? (int) $config['partner_id'] : null
        ));
    }

    /** The website of the current request (= the default mailer). */
    public static function forSite(): Mailer
    {
        return Mail::mailer(self::TRANSPORT);
    }

    /** Global SMTPs only (system emails not tied to a partner website). */
    public static function global(): Mailer
    {
        return self::mailer(self::TRANSPORT . '_global', ['context' => self::GLOBAL]);
    }

    /**
     * A given partner's SMTPs, then the Global ones (emails about that
     * partner sent from anywhere, e.g. a booking confirmation). The partner
     * id must come from the server side. Send synchronously: this runtime
     * mailer is not known to the queue worker.
     */
    public static function forPartner(?int $partnerId): Mailer
    {
        return $partnerId
            ? self::mailer(self::TRANSPORT . '_partner_' . $partnerId, ['context' => self::PARTNER, 'partner_id' => $partnerId])
            : self::global();
    }

    /**
     * The SMTPs to try, in order: the partner's enabled + complete ones by
     * priority, then the Global ones. [] = use the .env mailer (also when
     * the list can't be read, so email keeps working).
     *
     * @return WebsiteSmtpSetting[]
     */
    public static function chain(?int $partnerId): array
    {
        try {
            $rows = WebsiteSmtpSetting::query()
                ->where(function ($query) use ($partnerId) {
                    $query->whereNull('partner_id');
                    if ($partnerId) {
                        $query->orWhere('partner_id', $partnerId);
                    }
                })
                ->where('status', 1)
                ->orderByRaw('partner_id IS NULL')
                ->ordered()
                ->get();
        } catch (\Throwable $e) {
            // Class only - never the message (it could echo settings).
            Log::warning('SMTP list unavailable, using the default mailer', ['exception' => get_class($e)]);

            return [];
        }

        return $rows->filter(function (WebsiteSmtpSetting $smtp) {
            try {
                return $smtp->isUsable();
            } catch (\Throwable $e) {
                Log::warning('SMTP skipped: settings unreadable', ['smtp_id' => $smtp->id, 'exception' => get_class($e)]);

                return false;
            }
        })->values()->all();
    }

    /** The partner whose website this request is on (null = recruitmentcv.com / CRM / console). */
    public static function sitePartnerId(): ?int
    {
        $partner = app()->bound('currentPartner') ? app('currentPartner') : null;

        return $partner && $partner->id ? (int) $partner->id : null;
    }

    /** The .env mailer's transport (mail.fallback = MAIL_MAILER). */
    public static function fallbackTransport(): TransportInterface
    {
        $name = (string) config('mail.fallback', 'smtp');
        if ($name === '' || str_starts_with($name, self::TRANSPORT)) {
            $name = 'smtp';
        }

        return Mail::mailer($name)->getSymfonyTransport();
    }

    /** A cached runtime mailer; its transport reads the SMTP list on every send, so nothing goes stale. */
    private static function mailer(string $name, array $config): Mailer
    {
        if (!config('mail.mailers.' . $name)) {
            config(['mail.mailers.' . $name => ['transport' => self::TRANSPORT] + $config]);
        }

        return Mail::mailer($name);
    }
}
