<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Basepathstatus;


class Domain extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        // Relations
        'partner_id',

        // Domain
        'domain_name',

        // Branding
        'company_name',
        'company_name_ar',

        'website_logo',
        'website_logo_ar',

        'company_address',
        'company_address_ar',

        'company_mobile',
        'company_email',

        // DNS / SSL
        'dns_verified',
        'ssl_verified',

        'dns_verified_at',
        'ssl_verified_at',

        'server_ip',
        'ssl_expiry_date',

        // Status
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'dns_verified'     => 'boolean',
        'ssl_verified'     => 'boolean',

        'dns_verified_at'  => 'datetime',
        'ssl_verified_at'  => 'datetime',

        'ssl_expiry_date'  => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    public function setDomainNameAttribute($value)
    {
        $value = strtolower(trim($value));

        // Remove http / https
        $value = preg_replace('#^https?://#', '', $value);

        // Remove www
        $value = preg_replace('/^www\./', '', $value);

        // Remove trailing slash
        $value = rtrim($value, '/');

        $this->attributes['domain_name'] = $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDnsVerified($query)
    {
        return $query->where('dns_verified', true);
    }

    public function scopeSslVerified($query)
    {
        return $query->where('ssl_verified', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isDnsVerified()
    {
        return $this->dns_verified;
    }

    public function isSslVerified()
    {
        return $this->ssl_verified;
    }

    public function portalLogoFile(bool $arabic = false): ?string
    {
        $file = $arabic
            ? $this->website_logo_ar
            : $this->website_logo;

        if (empty($file)) {
            return null;
        }

        return $file;

    }
}