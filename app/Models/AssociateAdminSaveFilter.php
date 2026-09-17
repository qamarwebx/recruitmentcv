<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociateAdminSaveFilter extends Model
{
    use HasFactory;

    protected $table = 'associate_admin_save_filters';

    protected $fillable = [
        'admin_id',
        'by_country',
        'by_city',
        'by_region',
        'by_created_by',
        'by_careoff',
    ];

    public function getByCountryArrayAttribute()
    {
        return $this->by_country ? explode(',', $this->by_country) : [];
    }

    public function getByCityArrayAttribute()
    {
        return $this->by_city ? explode(',', $this->by_city) : [];
    }

    public function getByRegionArrayAttribute()
    {
        return $this->by_region ? explode(',', $this->by_region) : [];
    }

    public function getByCreatedByArrayAttribute()
    {
        return $this->by_created_by ? explode(',', $this->by_created_by) : [];
    }

    public function getByCareoffArrayAttribute()
    {
        return $this->by_careoff ? explode(',', $this->by_careoff) : [];
    }
}
