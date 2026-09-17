<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allcontactadminsavefilter extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'custom_filters' => 'array',
    ];
}
