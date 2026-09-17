<?php

namespace App\Services\Backup\Contracts;

use App\Models\BackupGoogleAccount;
use App\Models\BackupHistory;

/**
 * Raw Google Drive Files API operations, given an already-valid access
 * token (refreshing it is App\Services\Backup\GoogleDriveService's job,
 * same split as DumpRunner vs DatabaseBackupService). Swappable in tests
 * via Tests\Support\FakeDriveUploader so they never call the real API.
 */
interface DriveUploader
{
    /**
     * Ensure $account's dedicated backup folder exists (creating it if
     * $account->drive_folder_id is empty) and upload $backup's file into
     * it, streaming from disk rather than buffering it in PHP memory.
     * Returns ['drive_file_id', 'drive_folder_id', 'drive_view_link',
     * 'file_size']. Must throw
     * \App\Services\Backup\Exceptions\GoogleDriveException (with a message
     * that never contains the access token) on any failure.
     */
    public function upload(BackupHistory $backup, BackupGoogleAccount $account, string $accessToken): array;

    /**
     * Delete one file from Drive by id. Must not throw when the file is
     * already gone (a 404 from Drive is treated as success) - only a real
     * failure to reach/authenticate against the API should throw.
     */
    public function delete(BackupGoogleAccount $account, string $accessToken, string $driveFileId): void;

    /**
     * Verify $accessToken actually works against the Drive API and that
     * the dedicated backup folder exists (creating it if needed). Returns
     * ['drive_folder_id', 'drive_folder_name']. Throws
     * GoogleDriveException on any failure.
     */
    public function testConnection(BackupGoogleAccount $account, string $accessToken): array;
}
