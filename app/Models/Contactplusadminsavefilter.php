<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contactplusadminsavefilter extends Model
{
    use HasFactory;

    protected $casts = [
        'custom_filters' => 'array',
    ];
}
