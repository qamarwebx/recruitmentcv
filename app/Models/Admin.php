<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_qamr_api_token',
        'email_qamr_contactplus_config',
        'email_qamr_allcontact_config',
        'monthly_salary',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_qamr_api_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at'             => 'datetime',
        'email_qamr_api_token'          => 'encrypted',
        'email_qamr_contactplus_config' => 'array',
        'email_qamr_allcontact_config'  => 'array',
        'last_seen_at'                  => 'datetime',
    ];

    protected $appends = ['base_path'];

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'admin_id');
    }

    public function fileManagerQuota()
    {
        return $this->hasOne(FileManagerQuota::class, 'admin_id');
    }

    public function fileManagerItems()
    {
        return $this->hasMany(FileManagerItem::class, 'owner_id');
    }

    public function adminpermission()
    {
        return $this->hasOne(Adminpermission::class, 'staff_id');
    }

    public function chatParticipants()
    {
        return $this->hasMany(ChatParticipant::class, 'admin_id');
    }

    public function getIsOnlineAttribute(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subSeconds(90));
    }

    public function getAvatarUrlAttribute(): string
    {
        return asset('admin/assets/img/avatars/' . ($this->profile ?: '1.png'));
    }

    public function getBasePathAttribute()
    {
        $basepathstatus = \App\Models\Basepathstatus::first();

        if ($basepathstatus && $basepathstatus->base_path_status == 1) {
            return asset('admin/assets/img/avatars/');
        } else {
            return asset('admin/assets/img/avatars/');
        }
    }

    public function scopeIsActiveAdmin($query)
    {
        return $query->where('status', 1);  // careoff

    }
}
