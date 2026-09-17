<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoNote extends Model
{
    use HasFactory;

    protected $table = 'todo_notes';

    protected $fillable = [
        'todo_id',
        'created_by',
        'notes'
    ];

    /**
     * Get the deal associated with this note.
     */
    public function todo()
    {
        return $this->belongsTo(Todo::class, 'todo_id');
    }

    /**
     * Get the admin/user who created this note.
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
