<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSaveFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'filter_data',
    ];

    protected $casts = [
        'filter_data' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
