<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'admin_id',
        'status',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function googleReviews()
    {
        return $this->belongsToMany(GoogleReview::class, 'google_review_branch');
    }
}
