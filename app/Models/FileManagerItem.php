<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileManagerItem extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_FOLDER = 'folder';
    const TYPE_FILE = 'file';

    protected $fillable = [
        'owner_id',
        'owner_user_type',
        'parent_id',
        'type',
        'name',
        'storage_path',
        'original_name',
        'extension',
        'mime_type',
        'size',
        'disk',
        'metadata',
        'favorite',
        'last_opened_at',
        'color',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
        'favorite' => 'boolean',
        'last_opened_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(Admin::class, 'owner_id');
    }

    public function parent()
    {
        return $this->belongsTo(FileManagerItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(FileManagerItem::class, 'parent_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    public function isFolder(): bool
    {
        return $this->type === self::TYPE_FOLDER;
    }

    public function isFile(): bool
    {
        return $this->type === self::TYPE_FILE;
    }

    public function scopeFolders($query)
    {
        return $query->where('type', self::TYPE_FOLDER);
    }

    public function scopeFiles($query)
    {
        return $query->where('type', self::TYPE_FILE);
    }

    public function scopeInFolder($query, $parentId)
    {
        return $parentId ? $query->where('parent_id', $parentId) : $query->whereNull('parent_id');
    }

    public function scopeOwnedBy($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeFavorited($query)
    {
        return $query->where('favorite', true);
    }

    /**
     * File-type category used for card icon/color and quick filters.
     */
    public function category(): string
    {
        if ($this->isFolder()) {
            return 'folder';
        }

        $ext = strtolower((string) $this->extension);
        $mime = (string) $this->mime_type;

        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }
        if (str_starts_with($mime, 'audio/')) {
            return 'audio';
        }
        if ($ext === 'pdf') {
            return 'pdf';
        }
        if (in_array($ext, ['doc', 'docx', 'odt', 'rtf'], true)) {
            return 'word';
        }
        if (in_array($ext, ['xls', 'xlsx', 'ods', 'csv'], true)) {
            return 'excel';
        }
        if (in_array($ext, ['ppt', 'pptx'], true)) {
            return 'powerpoint';
        }
        if (in_array($ext, ['zip', 'rar', '7z'], true)) {
            return 'archive';
        }
        if ($ext === 'txt') {
            return 'text';
        }

        return 'other';
    }
}
