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
        'sub_domain',

        // Hostinger subdomain provisioning (sub_domain above)
        'hostinger_status',
        'hostinger_created_at',
        'hostinger_error',
        'hostinger_reference',

        // Branding
        'company_name',
        'company_name_ar',

        'website_logo',
        'website_logo_ar',
        'website_favicon',

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

        'hostinger_created_at' => 'datetime',
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

    /**
     * Stores only the subdomain label itself (e.g. "raha"), never the
     * ".recruitmentcv.com" suffix shown next to it in the Website tab -
     * normalized the same way domain_name is, so lookups/uniqueness checks
     * are consistent regardless of how it was typed.
     */
    public function setSubDomainAttribute($value)
    {
        $value = strtolower(trim((string) $value));

        $this->attributes['sub_domain'] = $value === '' ? null : $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * sub_domain + the configured Hostinger parent domain, e.g. "raha" ->
     * "raha.recruitmentcv.com". Computed, not stored - see the Hostinger
     * status migration's doc comment for why.
     */
    public function getFullDomainAttribute(): ?string
    {
        if (empty($this->sub_domain)) {
            return null;
        }

        return $this->sub_domain . '.' . config('services.hostinger.recruitmentcv_domain', 'recruitmentcv.com');
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

    /**
     * Public URL of the partner's Branding favicon, versioned with the
     * file's modification time so browsers/CDN refetch it only when a new
     * favicon is actually uploaded. Null when none is set or the file is
     * missing, so callers fall back to their default icon.
     */
    public function faviconUrl(): ?string
    {
        if (empty($this->website_favicon)) {
            return null;
        }

        $relative = 'admin/assets/images/partner/' . $this->website_favicon;
        $version = @filemtime(public_path($relative));

        if (!$version) {
            return null;
        }

        return asset($relative) . '?v=' . $version;
    }

    /**
     * Site-wide default favicon (the QamarHire icon, same file as
     * qamarhire.com/user/img/favicon.png), used whenever a partner has no
     * Branding favicon. Versioned the same way as faviconUrl().
     */
    public static function defaultFaviconUrl(): string
    {
        $relative = 'user/img/favicon.png';
        $version = @filemtime(public_path($relative));

        return asset($relative) . ($version ? '?v=' . $version : '');
    }
}