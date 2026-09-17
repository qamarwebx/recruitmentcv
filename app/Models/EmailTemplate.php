<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $table = 'email_templates';

    protected $fillable = [
        'smtp_id',
        'template_name',
        'subject',
        'template_for',
        'template_used_for',
        'email_body',
        'email_body_bg',
        'language',
        'attachment',
        'public',
        'status',
        'field_variable', 
        'assign_variable',
        'careoff_id',
        'created_by',
    ];

    // 🔗 Relationships
    public function smtp()
    {
        return $this->belongsTo(EmailSmtp::class, 'smtp_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function careoff()
    {
        return $this->belongsTo(Admin::class, 'careoff_id');
    }

    // 🧩 Accessors (nice for DataTables)
    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    public function getPublicLabelAttribute()
    {
        return $this->public ? 'Public' : 'Private';
    }
}
