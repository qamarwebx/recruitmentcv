<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    protected $fillable = [
        'conversation_id', 'admin_id', 'last_read_message_id',
        'last_read_at', 'is_unread_manual', 'joined_at',
    ];

    protected $casts = [
        'last_read_at'    => 'datetime',
        'joined_at'       => 'datetime',
        'is_unread_manual' => 'boolean',
    ];

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
