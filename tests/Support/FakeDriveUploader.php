<?php

namespace Tests\Support;

use App\Models\BackupGoogleAccount;
use App\Models\BackupHistory;
use App\Services\Backup\Contracts\DriveUploader;
use Illuminate\Support\Str;

/**
 * Test double for App\Services\Backup\Contracts\DriveUploader - simulates
 * a successful Drive upload/delete/test without calling the real Google
 * API, so the Google Drive backup feature tests can exercise the full
 * upload -> record -> retention pipeline offline.
 */
class FakeDriveUploader implements DriveUploader
{
    public static array $deleted = [];

    public function upload(BackupHistory $backup, BackupGoogleAccount $account, string $accessToken): array
    {
        $fileId = 'fake-drive-file-'.Str::lower(Str::random(10));

        return [
            'drive_file_id' => $fileId,
            'drive_folder_id' => $account->drive_folder_id ?: 'fake-drive-folder-id',
            'drive_view_link' => 'https://drive.google.com/file/d/'.$fileId.'/view',
            'file_size' => $backup->file_size,
        ];
    }

    public function delete(BackupGoogleAccount $account, string $accessToken, string $driveFileId): void
    {
        static::$deleted[] = $driveFileId;
    }

    public function testConnection(BackupGoogleAccount $account, string $accessToken): array
    {
        return [
            'drive_folder_id' => $account->drive_folder_id ?: 'fake-drive-folder-id',
            'drive_folder_name' => config('backup.google_drive.folder_name'),
        ];
    }
}
