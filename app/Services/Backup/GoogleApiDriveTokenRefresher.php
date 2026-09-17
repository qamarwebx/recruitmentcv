<?php

namespace App\Services\Backup;

use App\Services\Backup\Contracts\DriveTokenRefresher;
use App\Services\Backup\Exceptions\GoogleDriveException;
use Google\Client;

class GoogleApiDriveTokenRefresher implements DriveTokenRefresher
{
    public function refresh(string $refreshToken): array
    {
        $client = new Client();
        $client->setClientId((string) config('services.google_drive.client_id'));
        $client->setClientSecret((string) config('services.google_drive.client_secret'));

        try {
            $token = $client->fetchAccessTokenWithRefreshToken($refreshToken);
        } catch (\Throwable $e) {
            throw new GoogleDriveException('Failed to refresh Google Drive access token: '.$e->getMessage());
        }

        if (isset($token['error'])) {
            throw new GoogleDriveException(
                'Google rejected the Drive refresh token: '.($token['error_description'] ?? $token['error'])
            );
        }

        if (empty($token['access_token'])) {
            throw new GoogleDriveException('Google Drive token refresh did not return an access token.');
        }

        return [
            'access_token' => $token['access_token'],
            'expires_in' => (int) ($token['expires_in'] ?? 3600),
        ];
    }
}
