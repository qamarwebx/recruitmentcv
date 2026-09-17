<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMemberWebPage extends Model
{
    use HasFactory;

    protected $table = 'team_member_web_pages';

    protected $fillable = [
        'staff_id',
        'name',
        'image',
        'whatsapp_number',
        'calling_number',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function staff()
    {
        return $this->belongsTo(Admin::class);
    }

    protected $appends = ['base_path'];

    public function getBasePathAttribute()
    {
        $basepathstatus = \App\Models\Basepathstatus::first();
    
        if ($basepathstatus && $basepathstatus->base_path_status == 1) {
    
            return url('admin/assets/img/avatars');
    
        } else {
    
            return url('admin/assets/img/avatars');
        }
    }
}