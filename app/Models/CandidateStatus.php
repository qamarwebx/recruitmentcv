<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateStatus extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function candidateStatus()
    {
        return $this->belongsTo(CandidateStatus::class);
    }
    public function candidate()
    {
        return $this->hasMany(Candidate::class);
    }
}
