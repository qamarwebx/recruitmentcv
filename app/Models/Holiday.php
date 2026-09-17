<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'holiday_date',
        'name',
        'description',
        'created_by',
    ];

    protected $casts = [
        'holiday_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function scopeFilterDateFrom($query, $date)
    {
        return $query->when($date, fn ($query, $date) => $query->whereDate('holiday_date', '>=', $date));
    }

    public function scopeFilterDateTo($query, $date)
    {
        return $query->when($date, fn ($query, $date) => $query->whereDate('holiday_date', '<=', $date));
    }

    public function scopeFilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query, $searchText) {
            $query->where('name', 'like', '%' . $searchText . '%');
        });
    }
}
