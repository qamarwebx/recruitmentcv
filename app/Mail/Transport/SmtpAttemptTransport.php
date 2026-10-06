<?php

namespace App\Mail\Transport;

use App\Models\WebsiteSmtpSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\RawMessage;

/**
 * One SMTP (WebsiteSmtpSetting) as a step of SmtpFailoverTransport's chain,
 * or alone for a Test email. Sends as that SMTP's own From address/name -
 * each SMTP account may only send as itself, so a fallback SMTP must not
 * reuse the primary's sender - and logs a failure (never the password)
 * before handing it back to the failover. Twin file in both apps.
 */
final class SmtpAttemptTransport implements TransportInterface
{
    public function __construct(
        private WebsiteSmtpSetting $smtp,
        private TransportInterface $transport,
        private int $position = 1
    ) {
    }

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        if ($message instanceof Message) {
            [$message, $envelope] = $this->asOwnSender($message, $envelope);
        }

        try {
            $sent = $this->transport->send($message, $envelope);
        } catch (TransportExceptionInterface $e) {
            Log::warning('SMTP send failed', $this->logContext() + ['error' => $this->safeError($e)]);

            throw $e;
        }

        if ($this->position > 1) {
            Log::info('Email sent by a fallback SMTP', $this->logContext());
        }

        return $sent;
    }

    public function __toString(): string
    {
        return 'smtp#' . ($this->smtp->id ?: 'new') . '(' . $this->smtp->host . ':' . $this->smtp->port . ')';
    }

    /** [message, envelope] with this SMTP's From as header and envelope sender (same recipients). */
    private function asOwnSender(Message $message, ?Envelope $envelope): array
    {
        $message = clone $message;
        $from = new Address((string) $this->smtp->from_address, trim(strip_tags((string) $this->smtp->from_name)));

        $headers = $message->getHeaders();
        $headers->remove('From');
        $headers->remove('Sender');
        $headers->addMailboxListHeader('From', [$from]);

        $recipients = ($envelope ?? Envelope::create($message))->getRecipients();

        return [$message, new Envelope($from, $recipients)];
    }

    private function logContext(): array
    {
        return [
            'smtp_id' => $this->smtp->id,
            'partner_id' => $this->smtp->partner_id,
            'position' => $this->position,
            'host' => $this->smtp->host,
        ];
    }

    /** One line, password scrubbed (server replies can echo what they were sent). */
    private function safeError(\Throwable $e): string
    {
        $message = $e->getMessage();
        try {
            $password = (string) $this->smtp->password;
            if ($password !== '') {
                $message = str_replace([$password, base64_encode($password)], '********', $message);
            }
        } catch (\Throwable $ignored) {
            // Unreadable password: nothing to scrub.
        }

        return Str::limit(preg_replace('/\s+/', ' ', $message), 300);
    }
}
