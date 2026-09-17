<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Structured, immutable record of a stage/status change on a trackable
 * model (Lead.is_qualified, DealPipeline.deal_stage_id). Written only via
 * model-level booted() hooks on Lead/DealPipeline — never write to this
 * table directly from a controller, or future changes could bypass it.
 */
class StageHistory extends Model
{
    protected $fillable = [
        'trackable_type',
        'trackable_id',
        'from_value',
        'to_value',
        'from_label',
        'to_label',
        'changed_by',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function trackable()
    {
        return $this->morphTo();
    }

    public function changedBy()
    {
        return $this->belongsTo(Admin::class, 'changed_by');
    }
}
