<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | Private disk (see config/filesystems.php) that backs all File Manager
    | uploads. Never expose this disk's root through a public URL.
    |
    */

    'disk' => env('FILE_MANAGER_DISK', 'file_manager'),

    /*
    |--------------------------------------------------------------------------
    | Upload Limits
    |--------------------------------------------------------------------------
    */

    'max_upload_size' => (int) env('FILE_MANAGER_MAX_UPLOAD_SIZE', 52428800), // 50 MB, bytes
    'max_batch_files' => (int) env('FILE_MANAGER_MAX_BATCH_FILES', 20),

    /*
    |--------------------------------------------------------------------------
    | Allowed File Types
    |--------------------------------------------------------------------------
    |
    | Extensions/mimes accepted on upload. No executables or scripts. Keep
    | this list maintainable and deployment-tunable via env if needed.
    |
    */

    'allowed_extensions' => [
        // images
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp',
        // documents
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf', 'odt', 'ods',
        // archives
        'zip', 'rar', '7z',
        // audio/video
        'mp3', 'wav', 'ogg', 'mp4', 'mov', 'avi', 'mkv', 'webm',
    ],

    'allowed_mimes' => [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'image/bmp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain', 'text/csv', 'application/rtf',
        'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet',
        'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed', 'application/x-zip-compressed',
        'audio/mpeg', 'audio/wav', 'audio/ogg',
        'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska', 'video/webm',
    ],

    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    |
    | Extensions that may be streamed inline (preview) rather than forced as
    | a download. Everything else always downloads.
    |
    | Video/audio is limited to formats browsers can actually play natively
    | via <video>/<audio> - .mov/.avi/.mkv are left out on purpose since no
    | major browser renders them inline, so those correctly fall back to a
    | download instead of another broken preview.
    |
    */

    'previewable_extensions' => [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'pdf', 'txt', 'csv',
        'mp4', 'webm',
        'mp3', 'wav', 'ogg',
    ],

    /*
    |--------------------------------------------------------------------------
    | Quota
    |--------------------------------------------------------------------------
    */

    'default_quota_bytes' => (int) env('FILE_MANAGER_DEFAULT_QUOTA', 5368709120), // 5 GB

    // If true, admins may allocate more than the currently available
    // allocatable storage. Off by default per spec.
    'allow_overallocation' => (bool) env('FILE_MANAGER_ALLOW_OVERALLOCATION', false),

    /*
    |--------------------------------------------------------------------------
    | Trash
    |--------------------------------------------------------------------------
    |
    | Soft-deleted items keep consuming quota until purged. Retention days
    | controls how long they sit in trash before file-manager:recalculate
    | permanently purges them (0 disables auto purge).
    |
    */

    'trash_retention_days' => (int) env('FILE_MANAGER_TRASH_RETENTION_DAYS', 30),

];
