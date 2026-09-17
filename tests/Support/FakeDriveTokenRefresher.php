<?php

namespace Tests\Support;

use App\Services\Backup\Contracts\DriveTokenRefresher;

/**
 * Test double for App\Services\Backup\Contracts\DriveTokenRefresher - hands
 * back a fake access token instead of calling Google's token endpoint.
 */
class FakeDriveTokenRefresher implements DriveTokenRefresher
{
    public function refresh(string $refreshToken): array
    {
        return [
            'access_token' => 'fake-refreshed-access-token',
            'expires_in' => 3600,
        ];
    }
}
