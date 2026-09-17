<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileManagerAdminSaveFilter extends Model
{
    use HasFactory;

    protected $table = 'file_manager_admin_save_filters';

    protected $fillable = [
        'admin_id',
        'by_type',
        'by_category',
        'by_owner',
        'by_size_min',
        'by_size_max',
        'by_date_from',
        'by_date_to',
        'by_location',
    ];

    public function getByOwnerArrayAttribute()
    {
        return $this->by_owner ? explode(',', $this->by_owner) : [];
    }

    public function getByCategoryArrayAttribute()
    {
        return $this->by_category ? explode(',', $this->by_category) : [];
    }
}
