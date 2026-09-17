<?php

return [

    /*
     * Private local disk the dumps are written to (see config/filesystems.php).
     * Never the "public" disk - backups must not be web-accessible.
     */
    'disk' => 'db_backups',

    'directories' => [
        'daily' => 'daily',
        'interval' => 'interval',
        'tmp' => 'tmp',
    ],

    /*
     * Backup types this module understands. Used to whitelist request input
     * everywhere a "type" is accepted, so a manipulated value can never
     * reach the filesystem, cache keys, or a query.
     */
    'types' => ['daily', 'interval'],

    /*
     * Interval options in minutes, with their UI label. Anything not in this
     * list is rejected by validation.
     */
    'interval_options' => [
        1 => '1 Minute',
        10 => '10 Minutes',
        20 => '20 Minutes',
        30 => '30 Minutes',
        60 => '1 Hour',
        180 => '3 Hours',
        240 => '4 Hours',
        300 => '5 Hours',
        360 => '6 Hours',
    ],

    'retention' => [
        'min' => 1,
        'max' => 100,
    ],

    // mysqldump/gzip binaries - overridable per-server via .env.
    'mysqldump_binary' => env('BACKUP_MYSQLDUMP_PATH', 'mysqldump'),
    'gzip_binary' => env('BACKUP_GZIP_PATH', 'gzip'),

    // Hard ceiling on how long a single dump may run.
    'process_timeout' => env('BACKUP_PROCESS_TIMEOUT', 1800),

    /*
     * Cache key prefixes used to serialize backup generation per type:
     * - "{prefix}:{type}:queued"  set right before a job is dispatched,
     *   cleared as soon as the job starts - blocks a second dispatch for
     *   the same type while one is already queued.
     * - "{prefix}:{type}:running" an atomic Cache::lock held only while
     *   the dump/move/retention actually executes - blocks true concurrent
     *   execution (e.g. a manual click while the scheduler's job runs).
     */
    'lock_prefix' => 'db-backup',
    'queued_marker_seconds' => 1800,
    'running_lock_seconds' => 1800,

    /*
     * Google Drive backup (Settings > Backup > DB Backup > Google Drive).
     * Every generated backup is optionally also uploaded here - see
     * App\Services\Backup\GoogleDriveService. Retention reuses the same
     * daily_retention/interval_retention counts as the local backups
     * (App\Models\BackupSetting), so there is only one retention policy
     * to configure.
     */
    'google_drive' => [
        // Least-privilege scope: only files this app itself created/opened,
        // never the account's whole Drive.
        'scopes' => ['https://www.googleapis.com/auth/drive.file'],

        // Name of the dedicated folder created in the connected account's
        // Drive the first time a backup is uploaded.
        'folder_name' => env('BACKUP_DRIVE_FOLDER_NAME', 'QamarHire DB Backups'),

        // Resumable upload chunk size (bytes) - the file is streamed to
        // Drive in chunks of this size, never loaded into memory whole.
        'upload_chunk_size' => env('BACKUP_DRIVE_UPLOAD_CHUNK_SIZE', 1048576),

        // Refresh the access token this many seconds before its recorded
        // expiry, so a near-expiry token is never used for an API call.
        'token_refresh_buffer_seconds' => 60,
    ],
];
