<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DealReminder extends Model
{
    use HasFactory;

    protected $table = 'deal_reminders';

    protected $fillable = [
        'deal_id',
        'created_by',
        'reminder_at',
        'whatsapp_description',
        'is_done',
        'is_notified',
        'notified_at',
    ];

    protected $casts = [
        'reminder_at' => 'datetime',
        'notified_at' => 'datetime',
        'is_done' => 'boolean',
        'is_notified' => 'boolean',
        'whatsapp_description' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Deal relation (if you have Deal model)
    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }

    // User relation (creator)
    public function user()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes (optional but useful)
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('is_done', 0);
    }

    public function scopeDue($query)
    {
        return $query->where('reminder_at', '<=', now())
                     ->where('is_done', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers (optional)
    |--------------------------------------------------------------------------
    */

    public function markAsDone()
    {
        $this->update(['is_done' => 1]);
    }

    public function markAsNotified()
    {
        $this->update([
            'is_notified' => 1,
            'notified_at' => now()
        ]);
    }
}