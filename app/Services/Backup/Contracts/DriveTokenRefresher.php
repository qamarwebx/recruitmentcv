<?php

namespace App\Services\Backup\Contracts;

interface DriveTokenRefresher
{
    /**
     * Exchange a stored refresh token for a new access token. Returns
     * ['access_token' => string, 'expires_in' => int (seconds)]. Throws
     * \App\Services\Backup\Exceptions\GoogleDriveException (with a message
     * that never contains the refresh token) on any failure, e.g. the
     * refresh token was revoked on Google's side.
     */
    public function refresh(string $refreshToken): array;
}
