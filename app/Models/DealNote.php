<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealNote extends Model
{
    use HasFactory;

    protected $table = 'deal_notes';

    protected $fillable = [
        'deal_id',
        'created_by',
        'notes',
        'conversation_type',
    ];

    /**
     * Get the deal associated with this note.
     */
    public function deal()
    {
        return $this->belongsTo(DealPipeline::class, 'deal_id');
    }

    /**
     * Get the admin/user who created this note.
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
