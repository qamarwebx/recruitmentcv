<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google OAuth 2.0 for Gmail SMTP ("Add SMTP With Google", Partner Website ->
 * SMTP). Twin file in both apps.
 *
 * Gmail SMTP has no password to hand out: the SMTP login is XOAUTH2 with a
 * short-lived access token, obtained from the refresh token Google issues
 * once the account owner allows the Gmail scope (https://mail.google.com/ -
 * Google's only scope that covers SMTP). So:
 *
 * - authorization: server-side authorization-code flow with PKCE, offline
 *   access, `state` checked by the caller (PartnerSmtpGoogleController);
 * - a saved Google SMTP (WebsiteSmtpSetting::MAILER_GOOGLE) keeps the
 *   refresh token in its encrypted, hidden `password` column;
 * - every send / connection check asks accessToken() for a current access
 *   token: cached (encrypted) until shortly before it expires, refreshed
 *   with the refresh token otherwise. A revoked / expired authorization
 *   throws GoogleSmtpException(REVOKED) - that SMTP is then skipped and the
 *   next one sends (SmtpFailoverTransport).
 *
 * Never logged: codes, tokens, the client secret, Google's response bodies.
 */
final class GoogleSmtpOAuth
{
    public const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    public const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    public const USERINFO_URL = 'https://openidconnect.googleapis.com/v1/userinfo';
    public const REVOKE_URL = 'https://oauth2.googleapis.com/revoke';

    /** The Gmail scope SMTP (XOAUTH2) needs. */
    public const MAIL_SCOPE = 'https://mail.google.com/';

    /** Requested scopes: the account's verified email + Gmail SMTP. Nothing else. */
    public const SCOPES = ['openid', 'email', self::MAIL_SCOPE];

    /** Seconds an access token is used less than Google's expiry (clock skew / long sends). */
    private const EXPIRY_MARGIN = 120;

    private const TIMEOUT = 15;

    /** Client id + secret set: saved Google SMTPs can get access tokens. */
    public static function canRefresh(): bool
    {
        return filled(config('services.google_smtp.client_id')) && filled(config('services.google_smtp.client_secret'));
    }

    /** ... and a redirect URI: a partner can connect an account. */
    public static function canAuthorize(): bool
    {
        return self::canRefresh() && filled(config('services.google_smtp.redirect'));
    }

    /** SMTP server values for Gmail with XOAUTH2 (configurable, see config/services.php). */
    public static function smtpDefaults(): array
    {
        return [
            'host' => strtolower((string) (config('services.google_smtp.host') ?: 'smtp.gmail.com')),
            'port' => (int) (config('services.google_smtp.port') ?: 587),
            'encryption' => (string) (config('services.google_smtp.encryption') ?? 'tls') ?: null,
        ];
    }

    /** A PKCE code verifier (RFC 7636, 64 chars). */
    public static function newCodeVerifier(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
    }

    /** Google's consent page for this flow. */
    public static function authorizationUrl(string $state, string $codeVerifier, ?string $loginHint = null): string
    {
        self::assertConfigured(true);
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        return self::AUTH_URL . '?' . http_build_query(array_filter([
            'client_id' => config('services.google_smtp.client_id'),
            'redirect_uri' => config('services.google_smtp.redirect'),
            'response_type' => 'code',
            'scope' => implode(' ', self::SCOPES),
            'access_type' => 'offline',                // a refresh token: sending happens server-side, later
            'prompt' => 'select_account consent',      // choose the account; consent again = a refresh token every time
            'include_granted_scopes' => 'false',
            'state' => $state,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
            'login_hint' => $loginHint ?: null,
        ]), '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * Authorization code -> the connected account: ['email' => verified address,
     * 'refresh_token' => ...]. Checks that the Gmail scope was granted and a
     * refresh token issued; anything else throws (a token already issued for
     * an unusable answer is revoked again).
     *
     * @throws GoogleSmtpException
     */
    public static function connect(string $code, string $codeVerifier): array
    {
        self::assertConfigured(true);

        try {
            $response = Http::asForm()->timeout(self::TIMEOUT)->post(self::TOKEN_URL, [
                'code' => $code,
                'client_id' => config('services.google_smtp.client_id'),
                'client_secret' => config('services.google_smtp.client_secret'),
                'redirect_uri' => config('services.google_smtp.redirect'),
                'grant_type' => 'authorization_code',
                'code_verifier' => $codeVerifier,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Google SMTP: code exchange failed', ['exception' => get_class($e)]);

            throw new GoogleSmtpException(GoogleSmtpException::EXCHANGE_FAILED);
        }

        if (!$response->successful() || !filled($response->json('access_token'))) {
            Log::warning('Google SMTP: code exchange refused', ['status' => $response->status(), 'error' => self::errorCode($response->json('error'))]);

            throw new GoogleSmtpException(GoogleSmtpException::EXCHANGE_FAILED);
        }

        $accessToken = (string) $response->json('access_token');
        $refreshToken = (string) $response->json('refresh_token');
        $granted = preg_split('/\s+/', trim((string) $response->json('scope')), -1, PREG_SPLIT_NO_EMPTY);

        $email = '';
        $verified = false;
        try {
            $user = Http::withToken($accessToken)->timeout(self::TIMEOUT)->acceptJson()->get(self::USERINFO_URL);
            if ($user->successful()) {
                $email = strtolower(trim((string) $user->json('email')));
                $verified = filter_var($user->json('email_verified'), FILTER_VALIDATE_BOOLEAN) && filter_var($email, FILTER_VALIDATE_EMAIL);
            }
        } catch (\Throwable $e) {
            Log::warning('Google SMTP: account lookup failed', ['exception' => get_class($e)]);
        }

        // An unusable answer: the authorization just issued is revoked again -
        // unless that Gmail account already sends for a saved SMTP (revoking
        // would end that one's access too).
        $fail = function (string $reason) use ($accessToken, $refreshToken, $email) {
            SmtpSettings::revokeGoogleUnlessInUse($refreshToken !== '' ? $refreshToken : $accessToken, $email);

            return new GoogleSmtpException($reason);
        };

        if (!$verified) {
            throw $fail(GoogleSmtpException::EMAIL_UNVERIFIED);
        }
        if (!in_array(self::MAIL_SCOPE, $granted, true)) {
            throw $fail(GoogleSmtpException::SCOPE_MISSING);
        }
        if ($refreshToken === '') {
            throw $fail(GoogleSmtpException::NO_REFRESH_TOKEN);
        }

        // The first access token is good for the connection check right away.
        $expiresIn = (int) $response->json('expires_in');
        if ($expiresIn > self::EXPIRY_MARGIN) {
            Cache::put(self::cacheKey($refreshToken), Crypt::encryptString($accessToken), $expiresIn - self::EXPIRY_MARGIN);
        }

        return ['email' => $email, 'refresh_token' => $refreshToken];
    }

    /**
     * A current access token for a saved authorization (cached, else
     * refreshed). Used as the XOAUTH2 bearer token - never stored in the
     * database or logged.
     *
     * @throws GoogleSmtpException
     */
    public static function accessToken(string $refreshToken): string
    {
        if ($refreshToken === '') {
            throw new GoogleSmtpException(GoogleSmtpException::REVOKED);
        }
        self::assertConfigured(false);

        $key = self::cacheKey($refreshToken);
        $cached = Cache::get($key);
        if (is_string($cached)) {
            try {
                return Crypt::decryptString($cached);
            } catch (\Throwable $e) {
                Cache::forget($key);
            }
        }

        try {
            $response = Http::asForm()->timeout(self::TIMEOUT)->post(self::TOKEN_URL, [
                'client_id' => config('services.google_smtp.client_id'),
                'client_secret' => config('services.google_smtp.client_secret'),
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Google SMTP: token refresh failed', ['exception' => get_class($e)]);

            throw new GoogleSmtpException(GoogleSmtpException::REFRESH_FAILED);
        }

        $token = (string) $response->json('access_token');
        if (!$response->successful() || $token === '') {
            $error = self::errorCode($response->json('error'));
            Log::warning('Google SMTP: token refresh refused', ['status' => $response->status(), 'error' => $error]);

            // invalid_grant = revoked / expired / password changed / removed by an admin.
            throw new GoogleSmtpException(in_array($error, ['invalid_grant', 'unauthorized_client', 'invalid_client'], true)
                ? GoogleSmtpException::REVOKED
                : GoogleSmtpException::REFRESH_FAILED);
        }

        $ttl = (int) $response->json('expires_in') - self::EXPIRY_MARGIN;
        if ($ttl > 0) {
            Cache::put($key, Crypt::encryptString($token), $ttl);
        }

        return $token;
    }

    /** Drops the cached access token (after the SMTP server refused it). */
    public static function forgetAccessToken(string $refreshToken): void
    {
        if ($refreshToken !== '') {
            Cache::forget(self::cacheKey($refreshToken));
        }
    }

    /** Best effort: tells Google to drop an authorization nobody will use again. Never throws. */
    public static function revoke(string $token): void
    {
        if ($token === '') {
            return;
        }
        self::forgetAccessToken($token);

        try {
            Http::asForm()->timeout(self::TIMEOUT)->post(self::REVOKE_URL, ['token' => $token]);
        } catch (\Throwable $e) {
            Log::info('Google SMTP: revoke failed', ['exception' => get_class($e)]);
        }
    }

    /** @throws GoogleSmtpException */
    private static function assertConfigured(bool $authorize): void
    {
        if (!($authorize ? self::canAuthorize() : self::canRefresh())) {
            throw new GoogleSmtpException(GoogleSmtpException::NOT_CONFIGURED);
        }
    }

    /** Cache key from the refresh token's hash (the token itself is never a key). */
    private static function cacheKey(string $refreshToken): string
    {
        return 'google-smtp-access:' . hash('sha256', $refreshToken);
    }

    /** Google's OAuth error code only (e.g. invalid_grant) - safe to log. */
    private static function errorCode($error): ?string
    {
        return is_string($error) && preg_match('/^[a-z_]{1,40}$/', $error) ? $error : null;
    }
}
