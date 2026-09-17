<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DealPipeline;
use App\Models\Admin;

class DealActivityLog extends Model
{
    protected $fillable = [
        'deal_id',
        'admin_id',
        'module',
        'activity',
    ];

    protected $casts = [
        'activity' => 'array',
    ];

    public function deal()
    {
        return $this->belongsTo(DealPipeline::class, 'deal_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
