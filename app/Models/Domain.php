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

        // "Remove Subdomain" (CRM): set = RecruitmentCV portal access revoked
        // until a new subdomain is saved.
        'subdomain_removed_at',
        'subdomain_removed_by',
        'removed_sub_domain',

        // Own Domain (App\Support\PartnerDomains): selected Domain Type and
        // the partner's verified custom domain (Hostinger parked domain).
        'domain_type',
        'custom_domain',
        'custom_domain_status',
        'custom_domain_token',
        'custom_domain_verified_at',
        'custom_domain_active_at',
        'custom_domain_checks',
        'custom_domain_removed_at',
        'removed_custom_domain',

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

    protected $hidden = ['custom_domain_token'];

    protected $casts = [

        'dns_verified'     => 'boolean',
        'ssl_verified'     => 'boolean',

        'dns_verified_at'  => 'datetime',
        'ssl_verified_at'  => 'datetime',

        'ssl_expiry_date'  => 'date',

        'hostinger_created_at' => 'datetime',
        'subdomain_removed_at' => 'datetime',
        'custom_domain_verified_at' => 'datetime',
        'custom_domain_active_at' => 'datetime',
        'custom_domain_removed_at' => 'datetime',
        'custom_domain_checks' => 'array',
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

        return \App\Support\RecruitmentDomain::partnerHost($this->sub_domain);
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

    /**
     * A *.recruitmentcv.com subdomain that is actually live: provisioned
     * (hostinger_status 'success', maintained automatically by
     * HostingerSubdomainService) and not disabled via its two kill-switch
     * status values. `status` itself is NOT the signal - it belongs to the
     * older custom-domain flow and stays 'pending' for these subdomains.
     * The one rule used by ResolvePartnerWebsiteDomain (which subdomains
     * serve a site) and Partner::portalBaseUrl() (where a partner lands
     * after login).
     */
    public function scopeLiveSubdomain($query)
    {
        // Table-qualified so it also works when joined (e.g. with partners,
        // which has its own `status` column).
        return $query->whereNotNull('domains.sub_domain')
            ->where('domains.sub_domain', '!=', '')
            ->where('domains.hostinger_status', 'success')
            ->whereNotIn('domains.status', ['inactive', 'suspended'])
            ->whereNull('domains.subdomain_removed_at');
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

    /** Instance form of scopeLiveSubdomain(). */
    public function isLiveSubdomain(): bool
    {
        return !empty($this->sub_domain)
            && $this->hostinger_status === 'success'
            && !in_array($this->status, ['inactive', 'suspended'], true)
            && !$this->isSubdomainRemoved();
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
        // The global favicon (CRM -> Website -> Company Profile & Branding),
        // else the built-in file - see App\Support\BrandingAssets.
        return \App\Support\BrandingAssets::url('favicon', null);
    }

    /*
    |--------------------------------------------------------------------------
    | Branding files (admin/assets/images/partner/)
    |--------------------------------------------------------------------------
    | One physical folder shared by every partner and by both apps (the CRM's
    | Website tab and the RecruitmentCV Partner Portal's Branding tab - kept
    | identical in both codebases). Filenames used to be time()-only, so two
    | uploads in the same second collided and overwrote/deleted each other.
    */

    /** Columns of a domains row that hold a Branding file in that folder. */
    public const BRANDING_FILE_COLUMNS = ['website_logo', 'website_logo_ar', 'website_favicon'];

    /**
     * Collision-free name for a NEW Branding upload of this domain row:
     * "{domain id}_{timestamp}_{random}_{suffix}.{extension}", e.g.
     * "21_1790800000_k3j9x0q2ab_en.png". Existing files keep their names.
     */
    public function newBrandingFileName(string $suffix, string $extension): string
    {
        return $this->getKey() . '_' . time() . '_' . strtolower(\Illuminate\Support\Str::random(10)) . '_' . $suffix . '.' . $extension;
    }

    /**
     * Deletes a replaced Branding file from $directory only when NO row
     * still references that exact filename - any domain's logo/Arabic
     * logo/favicon, a partner's logo/website_logo, or a branding/WhatsApp
     * settings row (same folder). Call it
     * after the replacement has been saved. Returns whether it was deleted.
     */
    public static function deleteBrandingFileIfUnreferenced(string $directory, ?string $file): bool
    {
        $file = basename((string) $file);

        if ($file === '' || $file === '.' || $file === '..') {
            return false;
        }

        $inUse = static::where(function ($query) use ($file) {
            foreach (static::BRANDING_FILE_COLUMNS as $column) {
                $query->orWhere($column, $file);
            }
        })->exists()
            || Partner::where('logo', $file)->orWhere('website_logo', $file)->exists()
            // Global / partner branding rows (header/footer logos, favicon) and WhatsApp icons.
            || PartnerPageContent::whereIn('page', ['branding', 'whatsapp'])
                ->where('content', 'like', '%"' . addcslashes($file, '%_\\') . '"%')
                ->exists();

        $path = rtrim($directory, '/') . '/' . $file;

        return !$inUse && is_file($path) && unlink($path);
    }

    /**
     * The partner's RecruitmentCV subdomain was removed (CRM -> Partner ->
     * Website -> Domain -> Remove Subdomain): its portal access stays revoked
     * until a new subdomain is saved.
     */
    public function isSubdomainRemoved(): bool
    {
        return $this->subdomain_removed_at !== null;
    }
}
