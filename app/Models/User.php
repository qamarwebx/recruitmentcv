<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'facebook_id',
        'google_id',
        'avatar_url',
        'email_verified_at',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            \Log::info('User creating', [
                'data' => $user->toArray(),
                'url' => request()->fullUrl(),
                'ip' => request()->ip(),
            ]);
        });
    }

    /**
     * Has the customer themselves taken this account into use? True once
     * any existing verification/login marker is set: mobile verified by OTP
     * (mobile_verified_at), email verified or Google-linked
     * (email_verified_at / google_id), or a recorded login
     * (last_login_from). From then on its login identifiers (mobile, email)
     * can only be changed by the customer through the verified change flow
     * on /account/profile or /account/security - never by a partner
     * (PartnerCustomersController), since one account works on every site.
     */
    public function isInUseByCustomer(): bool
    {
        return !empty($this->mobile_verified_at)
            || !empty($this->email_verified_at)
            || !empty($this->google_id)
            || !empty($this->last_login_from);
    }
}
