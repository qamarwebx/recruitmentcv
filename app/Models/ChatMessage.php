<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'conversation_id', 'sender_id', 'body', 'message_type',
        'reply_to_message_id', 'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(Admin::class, 'sender_id');
    }

    public function attachments()
    {
        return $this->hasMany(ChatMessageAttachment::class, 'message_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_message_id');
    }

    public function getDisplayBodyAttribute(): ?string
    {
        return $this->is_deleted ? 'This message was deleted' : $this->body;
    }
}
