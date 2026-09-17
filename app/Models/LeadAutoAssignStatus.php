<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadAutoAssignStatus extends Model
{
    /**
     * Table name (because Laravel expects plural by default)
     */
    protected $table = 'lead_auto_assign_status';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'status',
    ];

    /**
     * Status constants (recommended)
     */
    const STATUS_DEACTIVE = 0;
    const STATUS_ACTIVE   = 1;

    /**
     * Casts
     */
    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * Helpers (optional but useful)
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isDeactive(): bool
    {
        return $this->status === self::STATUS_DEACTIVE;
    }
}
