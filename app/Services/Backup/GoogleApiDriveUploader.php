<?php

namespace App\Services\Backup;

use App\Models\BackupGoogleAccount;
use App\Models\BackupHistory;
use App\Services\Backup\Contracts\DriveUploader;
use App\Services\Backup\Exceptions\GoogleDriveException;
use Google\Client;
use Google\Http\MediaFileUpload;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Storage;

/**
 * Real Google Drive Files API implementation of DriveUploader. Uploads are
 * resumable/chunked via MediaFileUpload so the backup file is streamed
 * from disk straight to Google - it is never read into memory whole.
 */
class GoogleApiDriveUploader implements DriveUploader
{
    public function upload(BackupHistory $backup, BackupGoogleAccount $account, string $accessToken): array
    {
        if (!$backup->path || !Storage::disk($backup->disk ?: config('backup.disk'))->exists($backup->path)) {
            throw new GoogleDriveException('Local backup file no longer exists; cannot upload it to Google Drive.');
        }

        $absolutePath = Storage::disk($backup->disk ?: config('backup.disk'))->path($backup->path);
        $fileSize = filesize($absolutePath);
        if ($fileSize === false) {
            throw new GoogleDriveException('Could not determine the backup file size.');
        }

        $client = $this->client($accessToken);
        $service = new Drive($client);

        $folderId = $this->ensureFolder($service, $account);

        $client->setDefer(true);

        $driveFile = new DriveFile();
        $driveFile->setName($backup->filename ?: $backup->path);
        $driveFile->setParents([$folderId]);

        try {
            /** @var \Psr\Http\Message\RequestInterface $request */
            $request = $service->files->create($driveFile, ['fields' => 'id, webViewLink']);

            $chunkSize = max(262144, (int) config('backup.google_drive.upload_chunk_size', 1048576));

            $media = new MediaFileUpload($client, $request, 'application/gzip', null, true, $chunkSize);
            $media->setFileSize($fileSize);

            $handle = fopen($absolutePath, 'rb');
            if ($handle === false) {
                throw new GoogleDriveException('Could not open the backup file for streaming upload.');
            }

            $status = false;
            try {
                while (!$status && !feof($handle)) {
                    $chunk = fread($handle, $chunkSize);
                    $status = $media->nextChunk($chunk);
                }
            } finally {
                fclose($handle);
            }
        } catch (\Throwable $e) {
            $client->setDefer(false);
            throw new GoogleDriveException('Google Drive upload failed: '.$this->sanitize($e->getMessage()));
        }

        $client->setDefer(false);

        if (!$status instanceof DriveFile || !$status->getId()) {
            throw new GoogleDriveException('Google Drive upload finished without returning a file id.');
        }

        return [
            'drive_file_id' => $status->getId(),
            'drive_folder_id' => $folderId,
            'drive_view_link' => $status->getWebViewLink(),
            'file_size' => $fileSize,
        ];
    }

    public function delete(BackupGoogleAccount $account, string $accessToken, string $driveFileId): void
    {
        $service = new Drive($this->client($accessToken));

        try {
            $service->files->delete($driveFileId);
        } catch (\Google\Service\Exception $e) {
            if ($e->getCode() === 404) {
                return;
            }

            throw new GoogleDriveException('Failed to delete file from Google Drive: '.$this->sanitize($e->getMessage()));
        } catch (\Throwable $e) {
            throw new GoogleDriveException('Failed to delete file from Google Drive: '.$this->sanitize($e->getMessage()));
        }
    }

    public function testConnection(BackupGoogleAccount $account, string $accessToken): array
    {
        $service = new Drive($this->client($accessToken));

        try {
            $service->about->get(['fields' => 'user']);
        } catch (\Throwable $e) {
            throw new GoogleDriveException('Could not reach Google Drive with the stored credentials: '.$this->sanitize($e->getMessage()));
        }

        $folderId = $this->ensureFolder($service, $account);

        return [
            'drive_folder_id' => $folderId,
            'drive_folder_name' => (string) config('backup.google_drive.folder_name'),
        ];
    }

    protected function client(string $accessToken): Client
    {
        $client = new Client();
        $client->setAccessToken(['access_token' => $accessToken]);

        return $client;
    }

    /**
     * Find-or-create the dedicated backup folder in the connected
     * account's Drive. Reuses $account->drive_folder_id when already
     * known; otherwise searches by name (only files/folders this app's
     * drive.file-scoped token can see) before creating a new one, so
     * reconnecting the same account never creates duplicate folders.
     */
    protected function ensureFolder(Drive $service, BackupGoogleAccount $account): string
    {
        if ($account->drive_folder_id) {
            return $account->drive_folder_id;
        }

        $folderName = (string) config('backup.google_drive.folder_name');
        $escapedName = str_replace("'", "\\'", $folderName);

        try {
            $results = $service->files->listFiles([
                'q' => "mimeType='application/vnd.google-apps.folder' and name='{$escapedName}' and trashed=false",
                'spaces' => 'drive',
                'fields' => 'files(id, name)',
                'pageSize' => 1,
            ]);

            $existing = $results->getFiles();
            if (!empty($existing)) {
                return $existing[0]->getId();
            }

            $folder = new DriveFile();
            $folder->setName($folderName);
            $folder->setMimeType('application/vnd.google-apps.folder');

            $created = $service->files->create($folder, ['fields' => 'id']);

            return $created->getId();
        } catch (\Throwable $e) {
            throw new GoogleDriveException('Could not create/find the Google Drive backup folder: '.$this->sanitize($e->getMessage()));
        }
    }

    /**
     * Defense in depth, same intent as MysqldumpRunner::sanitizeError() -
     * an access token must never end up in backup_drive_histories.error_message
     * or the logs even if a client library ever echoed it back in an
     * exception message.
     */
    protected function sanitize(string $message): string
    {
        $message = preg_replace('/(access_token|Bearer)[=:\s]+\S+/i', '$1=***', $message) ?? $message;

        return trim(mb_substr($message, 0, 2000));
    }
}
