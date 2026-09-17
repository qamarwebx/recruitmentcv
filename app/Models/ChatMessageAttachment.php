<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ChatMessageAttachment extends Model
{
    protected $fillable = [
        'message_id', 'disk', 'path', 'original_name',
        'stored_name', 'mime_type', 'extension', 'size',
    ];

    const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    public function getIsImageAttribute(): bool
    {
        return in_array(strtolower($this->extension ?? ''), self::IMAGE_EXTENSIONS, true);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = (int) $this->size;
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return round($bytes / 1048576, 1) . ' MB';
    }
}
