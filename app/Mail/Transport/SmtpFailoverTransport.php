<?php

namespace App\Mail\Transport;

use App\Support\SmtpMailer;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\FailoverTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;

/**
 * The "smtp_failover" mail transport (App\Support\SmtpMailer) - the default
 * mailer of both apps. For every email it reads the website's SMTP list
 * (SmtpMailer::chain()) and sends through Symfony's FailoverTransport,
 * built fresh each time so it always starts with SMTP #1: the first SMTP
 * that accepts the email ends it, the next one is tried only after a
 * failure, and no SMTP is tried twice. No SMTP configured/usable = the
 * .env mailer. Twin file in both apps.
 */
final class SmtpFailoverTransport implements TransportInterface
{
    /**
     * @param string $context SmtpMailer::SITE (the website of the current request), ::GLOBAL or ::PARTNER
     */
    public function __construct(private string $context = SmtpMailer::SITE, private ?int $partnerId = null)
    {
    }

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        $partnerId = match ($this->context) {
            SmtpMailer::PARTNER => $this->partnerId,
            SmtpMailer::GLOBAL => null,
            default => SmtpMailer::sitePartnerId(),
        };

        $transports = [];
        foreach (SmtpMailer::chain($partnerId) as $smtp) {
            try {
                $transports[] = $smtp->transport(count($transports) + 1);
            } catch (\Throwable $e) {
                // Class only - never the message (it could echo settings).
                Log::warning('SMTP skipped: could not be prepared', ['smtp_id' => $smtp->id, 'exception' => get_class($e)]);
            }
        }

        if (!$transports) {
            $sent = SmtpMailer::fallbackTransport()->send($message, $envelope);
            SmtpMailer::$lastDelivery = ['provider' => 'Default (.env) mailer', 'smtp_id' => null, 'position' => null];

            return $sent;
        }

        try {
            // Retry period = never within this send: a failed SMTP stays failed.
            return (new FailoverTransport($transports, PHP_INT_MAX))->send($message, $envelope);
        } catch (TransportExceptionInterface $e) {
            Log::error('Email not sent: every SMTP failed', ['partner_id' => $partnerId, 'smtps_tried' => count($transports)]);

            // Without the previous exception: its debug log holds the SMTP dialogue (incl. AUTH).
            throw new TransportException('Email could not be sent: all ' . count($transports) . ' SMTP server(s) failed.');
        }
    }

    public function __toString(): string
    {
        return SmtpMailer::TRANSPORT . '(' . $this->context . ($this->partnerId ? ':' . $this->partnerId : '') . ')';
    }
}
