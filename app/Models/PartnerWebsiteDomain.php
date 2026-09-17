<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * The RecruitmentCV partner-subdomain branding record (one row per
 * partner's *.recruitmentcv.com subdomain). Managed from the CRM's new
 * "RecruitmentCV Domain" tab (admin/partner/show.blade.php) and read by
 * the recruitmentcv.com install's ResolvePartnerWebsiteDomain middleware
 * to pick which logo to show for the current Host header. Intentionally
 * has no relationship to App\Models\Domain - see the migration's doc
 * comment for why these two systems are kept separate.
 */
class PartnerWebsiteDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'domain',
        'english_logo',
        'arabic_logo',
        'status',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Normalizes on write so lookups by Host header (already normalized
     * the same way by ResolvePartnerWebsiteDomain) always match regardless
     * of how the domain was typed into the CRM form - mirrors
     * App\Models\Domain::setDomainNameAttribute() without depending on it.
     */
    public function setDomainAttribute($value)
    {
        $domain = strtolower(trim($value));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#^www\.#', '', $domain);
        $domain = rtrim($domain, '/');

        $this->attributes['domain'] = $domain;
    }

    public function logoFile(bool $arabic = false): ?string
    {
        $file = $arabic ? $this->arabic_logo : $this->english_logo;

        if (empty($file)) {
            return null;
        }

        return file_exists(public_path('admin/assets/images/partnerwebsite/' . $file)) ? $file : null;
    }
}
