<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Allcontact;

class Allcontactnote extends Model
{
    use HasFactory;


    public function admin(){
        return $this->belongsTo(Admin::class);
    }


    protected static function booted()
    {
        static::saved(function ($note) {
            Allcontact::where('id', $note->allcontact_id)
                ->update([
                    'staff_updated_date' => now(),
                ]);
        });

        static::deleted(function ($note) {
            Allcontact::where('id', $note->allcontact_id)
                ->update([
                    'staff_updated_date' => now(),
                ]);
        });
    }

}
