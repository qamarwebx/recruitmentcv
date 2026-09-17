<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    protected $fillable = ['type', 'created_by', 'last_message_id', 'last_message_at'];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function participants()
    {
        return $this->hasMany(ChatParticipant::class, 'conversation_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function lastMessage()
    {
        return $this->belongsTo(ChatMessage::class, 'last_message_id');
    }

    /**
     * A direct conversation containing exactly these two admins (in either order).
     */
    public function scopeBetweenAdmins($query, int $adminA, int $adminB)
    {
        return $query->where('type', 'direct')
            ->whereHas('participants', fn ($q) => $q->where('admin_id', $adminA))
            ->whereHas('participants', fn ($q) => $q->where('admin_id', $adminB));
    }
}
