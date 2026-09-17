<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class Leadnote extends Model
{
    use HasFactory;

    protected $table = 'leadnotes';

    protected $fillable = [
        'lead_id',
        'notes',
        'conversation_type',
        'admin_id',
        'is_qualified',
        'call_not_connected_type',
    ];

    // ✅ Relation: Leadnote → Admin
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    // ✅ Relation: Leadnote → Lead
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

}