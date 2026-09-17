<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSourceType extends Model
{
    use HasFactory;

    // Explicitly set the table name
    protected $table = 'global_source_type';

    // Fields that are mass assignable
    protected $fillable = [
        'name',
        'is_active',
        'created_by_id',
    ];

    /**
     * Relation to Admin or User who created it (optional)
     * Change 'Admin' to 'User' if your system uses users instead of admins.
     */
    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by_id');
    }
}
