<?php

namespace App\Services\Backup\Exceptions;

/**
 * Raised for any Google Drive backup failure (expired/revoked credentials,
 * upload error, API error, ...). Messages must never contain an access or
 * refresh token.
 */
class GoogleDriveException extends \RuntimeException
{
}
