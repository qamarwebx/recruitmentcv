<?php

namespace App\Support;

/**
 * A Google SMTP (OAuth) step that did not succeed - App\Support\GoogleSmtpOAuth.
 * $reason is one of the REASON constants (shown to partners as a translated
 * message); the exception message never holds a token, code or secret.
 * Twin file in both apps.
 */
final class GoogleSmtpException extends \RuntimeException
{
    /** No OAuth client id / secret / redirect configured for Google SMTP. */
    public const NOT_CONFIGURED = 'not_configured';

    /** The user cancelled or Google refused the consent (access_denied, admin policy...). */
    public const DENIED = 'denied';

    /** Consent given without the Gmail sending permission (https://mail.google.com/). */
    public const SCOPE_MISSING = 'scope_missing';

    /** No refresh token in Google's answer (offline access not granted). */
    public const NO_REFRESH_TOKEN = 'no_refresh_token';

    /** Google did not confirm a verified email address for the account. */
    public const EMAIL_UNVERIFIED = 'email_unverified';

    /** The authorization code could not be exchanged (expired, reused, wrong client...). */
    public const EXCHANGE_FAILED = 'exchange_failed';

    /** The saved authorization no longer works (revoked, expired, password changed). */
    public const REVOKED = 'revoked';

    /** Google could not be reached / answered unexpectedly while refreshing. */
    public const REFRESH_FAILED = 'refresh_failed';

    public function __construct(public readonly string $reason, string $message = '')
    {
        parent::__construct($message !== '' ? $message : 'Google SMTP: ' . $reason);
    }
}
