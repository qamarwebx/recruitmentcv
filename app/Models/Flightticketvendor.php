<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flightticketvendor extends Model
{
    use HasFactory;

    public function admin(){
        return $this->belongsTo(Admin::class);
    }

    public function scopeFilterSearchText($query, $searchText)
    {
        return $query->when($searchText, function ($query, $searchText) {
            $searchText = '%' . $searchText . '%';
            $query->where(function ($q) use ($searchText) {
                $q->orWhere('id', 'like', $searchText)
                ->orWhere('name', 'like', $searchText)
                ->orWhere('contact_no', 'like', $searchText)
                ->orWhere('email', 'like', $searchText)
                ->orWhere('website', 'like', $searchText)
                ->orWhereHas('admin', fn($q) => $q->where('name', 'like', $searchText));
            });
        });
    }

}
