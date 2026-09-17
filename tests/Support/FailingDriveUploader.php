<?php

namespace Tests\Support;

use App\Models\BackupGoogleAccount;
use App\Models\BackupHistory;
use App\Services\Backup\Contracts\DriveUploader;
use App\Services\Backup\Exceptions\GoogleDriveException;

/**
 * Test double that always fails, for exercising the Google Drive backup
 * feature's failure path (upload marked failed, local backup untouched).
 */
class FailingDriveUploader implements DriveUploader
{
    public function upload(BackupHistory $backup, BackupGoogleAccount $account, string $accessToken): array
    {
        throw new GoogleDriveException('Simulated Google Drive upload failure for testing.');
    }

    public function delete(BackupGoogleAccount $account, string $accessToken, string $driveFileId): void
    {
        throw new GoogleDriveException('Simulated Google Drive delete failure for testing.');
    }

    public function testConnection(BackupGoogleAccount $account, string $accessToken): array
    {
        throw new GoogleDriveException('Simulated Google Drive connection failure for testing.');
    }
}
