<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Allcontact;

class AllcontactFile extends Model
{
    use HasFactory;

    protected $table = 'allcontact_files';

    protected $fillable = [
        'contact_id',
        'uploaded_by',
        'file_name',
        'file_path',
    ];

    // ✅ Contact relation
    public function contact()
    {
        return $this->belongsTo(AllContact::class, 'contact_id');
    }

    // ✅ Uploaded By (Admin/User)
    public function uploader()
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }


    protected static function booted()
    {
        static::saved(function ($file) {
            Allcontact::where('id', $file->contact_id)
                ->update([
                    'staff_updated_date' => now(),
                ]);
        });

        static::deleted(function ($file) {
            Allcontact::where('id', $file->contact_id)
                ->update([
                    'staff_updated_date' => now(),
                ]);
        });
    }
}