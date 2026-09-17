<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecruitStatus extends Model
{
    use HasFactory;

    protected $table = 'recruit_statuses';

    protected $fillable = [
        'name',
    ];

    public function deals() {
        return $this->hasMany(DealPipeline::class, 'recruite_status_id');
    }
    
}
