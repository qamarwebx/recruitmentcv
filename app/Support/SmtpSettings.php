<?php

namespace App\Support;

use App\Models\WebsiteSmtpSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Managing a website's SMTP list - the one backend behind CRM -> Website ->
 * SMTP (Global or any partner) and Partner Portal -> Website -> SMTP (the
 * signed-in partner only). Twin file in both apps.
 *
 * A "website" is a partner id, or null for the Global (recruitmentcv.com)
 * list. Callers resolve it on the server (CRM context selector, partner
 * guard) - never from a submitted field - and every lookup is scoped to it,
 * so one website's SMTP can never be read or changed through another's.
 * Passwords are write-only: blank keeps the saved one; never returned,
 * rendered or logged.
 *
 * Google SMTPs (WebsiteSmtpSetting::MAILER_GOOGLE, "Add SMTP With Google"):
 * added only through saveGoogle() with an account connected by OAuth (its
 * refresh token is the "password"), always connection-checked first; an
 * edit changes only the name, From name and status - the server, login
 * and From address stay those of the authorized Gmail account.
 */
final class SmtpSettings
{
    /** English defaults; the Partner Portal passes translated ones (same keys). */
    private const MESSAGES = [
        'host.regex' => 'Enter a host name only, e.g. smtp.gmail.com.',
        'port.in' => 'Use one of the standard SMTP ports: :ports.',
        'host_not_found' => 'This SMTP host could not be found.',
        'host_not_allowed' => 'This SMTP host is not allowed.',
        'duplicate' => 'This SMTP (same host, port and username) is already in the list.',
        'limit' => 'At most :max SMTPs can be added.',
        'auth_failed' => 'The SMTP server rejected the username or password.',
        'connect_failed' => 'Could not connect to the SMTP server. Please check the host, port and encryption.',
        'google_not_configured' => 'Google sign-in for SMTP is not set up yet. Please contact support.',
        'google_revoked' => 'Google no longer allows sending with this account (access removed or expired). Please connect the account again.',
        'google_unavailable' => 'Google could not be reached. Please try again in a moment.',
        'google_auth_failed' => 'Gmail refused the sign-in for this account. For a Google Workspace account, the administrator may need to allow SMTP access for it.',
    ];

    /** A website's SMTPs in sending order. */
    public static function list(?int $partnerId): Collection
    {
        return WebsiteSmtpSetting::ofWebsite($partnerId)->ordered()->get();
    }

    /** One SMTP of this website - 404 for anything else (another website's, or unknown). */
    public static function find(?int $partnerId, $id): WebsiteSmtpSetting
    {
        abort_unless(ctype_digit((string) $id), 404);

        return WebsiteSmtpSetting::ofWebsite($partnerId)->findOrFail((int) $id);
    }

    /** The SMTP tried first: the first enabled + complete one. */
    public static function primaryId(Collection $smtps): ?int
    {
        $primary = $smtps->first(function (WebsiteSmtpSetting $smtp) {
            try {
                return $smtp->isUsable();
            } catch (\Throwable $e) {
                return false;
            }
        });

        return $primary ? (int) $primary->id : null;
    }

    /**
     * Validates and saves one SMTP of a website ($smtp null = add a new one,
     * last in the order). Every SMTP must be complete; partner SMTPs also
     * need a standard port and a public mail host. $options: 'messages' /
     * 'attributes' (translations), 'verify' => true to check the connection
     * + login before an enabled SMTP is saved.
     *
     * @throws ValidationException
     */
    public static function save(?int $partnerId, ?WebsiteSmtpSetting $smtp, array $input, ?int $actorId, array $options = []): WebsiteSmtpSetting
    {
        if ($smtp && $smtp->isGoogle()) {
            return self::saveGoogle($partnerId, $smtp, $input, $actorId, $options);
        }

        $msg = fn (string $key, array $replace = []) => self::message($options, $key, $replace);
        $isPartner = $partnerId !== null;

        $validator = Validator::make($input, [
            'name' => ['nullable', 'string', 'max:100'],
            'host' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9]([A-Za-z0-9.-]*[A-Za-z0-9])?$/'],
            'port' => $isPartner
                ? ['required', 'integer', Rule::in(WebsiteSmtpSetting::PARTNER_PORTS)]
                : ['required', 'integer', 'between:1,65535'],
            'encryption' => ['nullable', Rule::in(['ssl', 'tls'])],
            'username' => ['required', 'string', 'max:255'],
            'password' => [$smtp && $smtp->hasPassword() ? 'nullable' : 'required', 'string', 'max:255'],
            'from_address' => ['required', 'email', 'max:255'],
            'from_name' => [$isPartner ? 'nullable' : 'required', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
        ], [
            'host.regex' => $msg('host.regex'),
            'port.in' => $msg('port.in', ['ports' => implode(', ', WebsiteSmtpSetting::PARTNER_PORTS)]),
        ], $options['attributes'] ?? []);

        $validator->after(function ($validator) use ($input, $partnerId, $smtp, $isPartner, $msg) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            if (!$smtp && WebsiteSmtpSetting::ofWebsite($partnerId)->count() >= WebsiteSmtpSetting::MAX_PER_WEBSITE) {
                $validator->errors()->add('smtp', $msg('limit', ['max' => WebsiteSmtpSetting::MAX_PER_WEBSITE]));

                return;
            }

            $host = strtolower(trim((string) $input['host']));
            $duplicate = WebsiteSmtpSetting::ofWebsite($partnerId)
                ->whereRaw('LOWER(host) = ?', [$host])
                ->where('port', (int) $input['port'])
                ->whereRaw('LOWER(username) = ?', [strtolower(trim((string) $input['username']))])
                ->when($smtp, fn ($query) => $query->whereKeyNot($smtp->id))
                ->exists();
            if ($duplicate) {
                $validator->errors()->add('host', $msg('duplicate'));

                return;
            }

            // A partner's SMTP: public mail servers only - never an internal/private address.
            if ($isPartner) {
                $ips = gethostbynamel($host);
                if (!$ips) {
                    $validator->errors()->add('host', $msg('host_not_found'));

                    return;
                }
                foreach ($ips as $ip) {
                    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        $validator->errors()->add('host', $msg('host_not_allowed'));

                        return;
                    }
                }
            }
        });

        $data = $validator->validate();

        $isNew = !$smtp;
        $smtp ??= new WebsiteSmtpSetting([
            'partner_id' => $partnerId,
            'priority' => (int) WebsiteSmtpSetting::ofWebsite($partnerId)->max('priority') + 1,
        ]);
        $name = trim(strip_tags((string) ($data['name'] ?? '')));
        $smtp->fill([
            'name' => $name !== '' ? $name : null,
            'mailer' => 'smtp',
            'host' => strtolower(trim((string) $data['host'])),
            'port' => (int) $data['port'],
            'encryption' => ($data['encryption'] ?? null) ?: null,
            'username' => trim((string) $data['username']),
            'from_address' => trim((string) $data['from_address']),
            'from_name' => trim(strip_tags((string) ($data['from_name'] ?? ''))),
            'status' => filter_var($data['status'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'updated_by' => $actorId,
        ]);
        if (filled($data['password'] ?? null)) {
            $smtp->password = (string) $data['password'];
        }

        if (($options['verify'] ?? false) && $smtp->status && ($error = self::connectionError($smtp, $options))) {
            throw ValidationException::withMessages(['smtp' => $error]);
        }

        self::persist($smtp, $isNew, $partnerId);

        return $smtp;
    }

    /**
     * A Google SMTP: adds one ($smtp null + $connection = ['email' => verified
     * Gmail address, 'refresh_token' => ...] from GoogleSmtpOAuth::connect())
     * or edits one (name / From name / status only). The server, port,
     * encryption, login and From address come from the connection and
     * GoogleSmtpOAuth::smtpDefaults() - never from the request. A new one is
     * connection-checked (XOAUTH2) before anything is saved, whatever its
     * status; nothing is written when the check fails, so the list - and
     * which SMTP sends first - stays as it was. Added last in the order.
     *
     * @throws ValidationException
     */
    public static function saveGoogle(?int $partnerId, ?WebsiteSmtpSetting $smtp, array $input, ?int $actorId, array $options = [], ?array $connection = null): WebsiteSmtpSetting
    {
        $msg = fn (string $key, array $replace = []) => self::message($options, $key, $replace);
        $isNew = !$smtp;
        if ($isNew && (!filter_var($connection['email'] ?? null, FILTER_VALIDATE_EMAIL) || !filled($connection['refresh_token'] ?? null))) {
            throw ValidationException::withMessages(['smtp' => $msg('google_revoked')]);
        }
        if ($smtp && !$smtp->isGoogle()) {
            throw ValidationException::withMessages(['smtp' => $msg('google_revoked')]);
        }

        $validator = Validator::make($input, [
            'name' => ['nullable', 'string', 'max:100'],
            'from_name' => [$partnerId !== null ? 'nullable' : 'required', 'string', 'max:255'],
            'status' => ['nullable', 'boolean'],
        ], [], $options['attributes'] ?? []);

        $defaults = GoogleSmtpOAuth::smtpDefaults();
        $email = $isNew ? strtolower(trim((string) $connection['email'])) : (string) $smtp->username;
        $validator->after(function ($validator) use ($input, $partnerId, $smtp, $isNew, $defaults, $email, $msg) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            // Adding / enabling needs the OAuth client; disabling or renaming never does.
            if (($isNew || filter_var($input['status'] ?? false, FILTER_VALIDATE_BOOLEAN)) && !GoogleSmtpOAuth::canRefresh()) {
                $validator->errors()->add('smtp', $msg('google_not_configured'));

                return;
            }
            if ($isNew && WebsiteSmtpSetting::ofWebsite($partnerId)->count() >= WebsiteSmtpSetting::MAX_PER_WEBSITE) {
                $validator->errors()->add('smtp', $msg('limit', ['max' => WebsiteSmtpSetting::MAX_PER_WEBSITE]));

                return;
            }
            $duplicate = WebsiteSmtpSetting::ofWebsite($partnerId)
                ->whereRaw('LOWER(host) = ?', [$defaults['host']])
                ->where('port', $defaults['port'])
                ->whereRaw('LOWER(username) = ?', [strtolower($email)])
                ->when($smtp, fn ($query) => $query->whereKeyNot($smtp->id))
                ->exists();
            if ($duplicate) {
                $validator->errors()->add('smtp', $msg('duplicate'));
            }
        });

        $data = $validator->validate();

        $smtp ??= new WebsiteSmtpSetting([
            'partner_id' => $partnerId,
            'priority' => (int) WebsiteSmtpSetting::ofWebsite($partnerId)->max('priority') + 1,
        ]);
        $name = trim(strip_tags((string) ($data['name'] ?? '')));
        $smtp->fill([
            'name' => $name !== '' ? $name : null,
            'from_name' => trim(strip_tags((string) ($data['from_name'] ?? ''))),
            'status' => filter_var($data['status'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'updated_by' => $actorId,
        ]);
        if ($isNew) {
            $smtp->fill([
                'mailer' => WebsiteSmtpSetting::MAILER_GOOGLE,
                'host' => $defaults['host'],
                'port' => $defaults['port'],
                'encryption' => $defaults['encryption'],
                'username' => $email,
                'from_address' => $email,
            ]);
            $smtp->password = (string) $connection['refresh_token'];
        }

        if (($isNew || $smtp->status) && ($error = self::connectionError($smtp, $options))) {
            throw ValidationException::withMessages(['smtp' => $error]);
        }

        self::persist($smtp, $isNew, $partnerId);

        return $smtp;
    }

    /**
     * Saves (+ renumbers a new one) as ONE transaction, after any connection
     * check passed: a database failure leaves no half-added SMTP and the
     * list / sending order exactly as before (the exception goes to the caller).
     */
    private static function persist(WebsiteSmtpSetting $smtp, bool $isNew, ?int $partnerId): void
    {
        DB::transaction(function () use ($smtp, $isNew, $partnerId) {
            $smtp->save();
            if ($isNew) {
                self::renumber($partnerId);
            }
        });
    }

    public static function delete(WebsiteSmtpSetting $smtp): void
    {
        DB::transaction(function () use ($smtp) {
            $smtp->delete();
            self::renumber($smtp->partner_id);
        });

        // A deleted Google SMTP: its authorization is revoked at Google too,
        // unless the same Gmail account still sends for another SMTP (one
        // revoke ends the whole grant).
        if ($smtp->isGoogle()) {
            try {
                $token = (string) $smtp->password;
            } catch (\Throwable $e) {
                $token = '';
            }
            self::revokeGoogleUnlessInUse($token, (string) $smtp->username);
        }
    }

    /**
     * Revokes a Google authorization nobody will use (a deleted / abandoned
     * connection) - skipped while any saved Google SMTP (any website) still
     * uses that Gmail account, because revoking ends the app's whole grant
     * for the account. Best effort, never throws.
     */
    public static function revokeGoogleUnlessInUse(string $token, string $email): void
    {
        if ($token === '') {
            return;
        }
        $inUse = $email !== '' && WebsiteSmtpSetting::query()
            ->where('mailer', WebsiteSmtpSetting::MAILER_GOOGLE)
            ->whereRaw('LOWER(username) = ?', [strtolower(trim($email))])
            ->exists();
        if ($inUse) {
            GoogleSmtpOAuth::forgetAccessToken($token);

            return;
        }
        GoogleSmtpOAuth::revoke($token);
    }

    /** Moves an SMTP one place up/down, or to the top (= tried first). */
    public static function move(WebsiteSmtpSetting $smtp, string $direction): void
    {
        DB::transaction(function () use ($smtp, $direction) {
            $ids = WebsiteSmtpSetting::ofWebsite($smtp->partner_id)->ordered()->lockForUpdate()->pluck('id')->map(fn ($id) => (int) $id)->all();
            $index = array_search((int) $smtp->id, $ids, true);
            if ($index === false) {
                return;
            }

            $target = match ($direction) {
                'up' => max(0, $index - 1),
                'down' => min(count($ids) - 1, $index + 1),
                default => 0,
            };
            array_splice($ids, $index, 1);
            array_splice($ids, $target, 0, [(int) $smtp->id]);

            self::applyOrder($ids);
        });
    }

    /**
     * Opens + authenticates an SMTP session without sending anything. A
     * friendly message on failure - never the server's raw reply.
     */
    public static function connectionError(WebsiteSmtpSetting $smtp, array $options = []): ?string
    {
        try {
            $transport = $smtp->symfonyTransport();
            $transport->start();
            $transport->stop();

            return null;
        } catch (GoogleSmtpException $e) {
            Log::info('SMTP connection check failed', ['smtp_id' => $smtp->id, 'partner_id' => $smtp->partner_id, 'google' => $e->reason]);

            return self::message($options, self::googleMessageKey($e->reason));
        } catch (\Throwable $e) {
            Log::info('SMTP connection check failed', ['smtp_id' => $smtp->id, 'partner_id' => $smtp->partner_id, 'exception' => get_class($e)]);
            $authFailed = str_contains(strtolower($e->getMessage()), 'authenticat');
            if ($smtp->isGoogle()) {
                // A refused access token is never reused.
                try {
                    GoogleSmtpOAuth::forgetAccessToken((string) $smtp->password);
                } catch (\Throwable $ignored) {
                }

                return self::message($options, $authFailed ? 'google_auth_failed' : 'connect_failed');
            }

            return self::message($options, $authFailed ? 'auth_failed' : 'connect_failed');
        }
    }

    /** The message key for a GoogleSmtpException reason. */
    public static function googleMessageKey(string $reason): string
    {
        return match ($reason) {
            GoogleSmtpException::NOT_CONFIGURED => 'google_not_configured',
            GoogleSmtpException::REFRESH_FAILED => 'google_unavailable',
            default => 'google_revoked',
        };
    }

    /**
     * Sends a short test email through this one SMTP (no failover, even when
     * disabled, so it can be checked before it is switched on). Returns null
     * on success, else a one-line error with the password scrubbed.
     */
    public static function sendTest(WebsiteSmtpSetting $smtp, string $to, string $siteName, string $label): ?string
    {
        try {
            $smtp->mailer()->raw(
                'This is a test email from ' . $siteName . ' (' . $label . ': ' . $smtp->host . ':' . $smtp->port . '). These SMTP settings are working.',
                fn ($message) => $message->to($to)->subject($siteName . ' SMTP test - ' . $label)
            );

            return null;
        } catch (\Throwable $e) {
            $error = $e instanceof GoogleSmtpException ? self::message([], self::googleMessageKey($e->reason)) : $smtp->scrub($e->getMessage());
            $error = Str::limit(preg_replace('/\s+/', ' ', $error), 200);
            Log::warning('SMTP test email failed', ['smtp_id' => $smtp->id, 'partner_id' => $smtp->partner_id, 'error' => $error]);

            return $error;
        }
    }

    /**
     * The order an email from this website goes through right now:
     * [['smtp' => WebsiteSmtpSetting, 'scope' => 'partner'|'global', 'label' => ...], ...]
     * - [] = the .env mailer.
     */
    public static function sendingOrder(?int $partnerId): array
    {
        // Each SMTP's number within its own list (for "SMTP #n" labels).
        $positions = [];
        foreach ($partnerId ? [$partnerId, null] : [null] as $website) {
            foreach (self::list($website)->values() as $i => $smtp) {
                $positions[$smtp->id] = $i + 1;
            }
        }

        return array_map(fn (WebsiteSmtpSetting $smtp) => [
            'smtp' => $smtp,
            'scope' => $smtp->partner_id ? 'partner' : 'global',
            'label' => $smtp->label($positions[$smtp->id] ?? 1),
        ], SmtpMailer::chain($partnerId));
    }

    /** Priorities 1..n in the current order (after an add/delete). */
    private static function renumber(?int $partnerId): void
    {
        self::applyOrder(WebsiteSmtpSetting::ofWebsite($partnerId)->ordered()->pluck('id')->map(fn ($id) => (int) $id)->all());
    }

    /** @param int[] $ids one website's SMTP ids in the wanted order */
    private static function applyOrder(array $ids): void
    {
        foreach (array_values($ids) as $i => $id) {
            WebsiteSmtpSetting::whereKey($id)->where('priority', '!=', $i + 1)->update(['priority' => $i + 1]);
        }
    }

    private static function message(array $options, string $key, array $replace = []): string
    {
        $text = $options['messages'][$key] ?? self::MESSAGES[$key];
        foreach ($replace as $name => $value) {
            $text = str_replace(':' . $name, (string) $value, $text);
        }

        return $text;
    }
}
