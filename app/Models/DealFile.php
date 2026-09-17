<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'uploaded_by',
        'file_name',
        'file_path',
    ];

    public function deal()
    {
        return $this->belongsTo(DealPipeline::class, 'deal_id');
    }

    public function uploader()
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }
}
