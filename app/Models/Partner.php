<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Partner extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guard = 'guard';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        'mobile_verified_at' => 'datetime',
    ];

    /**
     * Single source of truth for "can this partner Hire Now" - both a
     * mobile number on file AND it being verified are required.
     * mobile_verified_at can be set (e.g. stale/legacy data, or a partner
     * who verified a number and then had it cleared) while
     * owner_mobile_no is empty, so checking mobile_verified_at alone is
     * NOT sufficient - both flags must be checked together, every time.
     */
    public function hasVerifiedMobile(): bool
    {
        return !empty($this->owner_mobile_no) && !is_null($this->mobile_verified_at);
    }

    /**
     * The CRM's "Website" tab on a Partner's admin record
     * (admin/partner/show.blade.php - $partnerDomain, PartnerController::show())
     * - English Logo/Arabic Logo (website_logo/website_logo_ar) live here,
     * not on this model directly. One partner has at most one Domain row.
     */
    public function domain()
    {
        return $this->hasOne(Domain::class);
    }

    /**
     * Single source of truth for "which logo file should the Worker
     * Partner Portal header show for this partner, in the given locale" -
     * the English/Arabic Website-tab logo when one has actually been
     * uploaded AND the file genuinely exists, or null (meaning: fall back
     * to the portal's own default logo) for anything else - no domain row,
     * an empty column, the locale's own logo never having been set, or a
     * column value pointing at a file that no longer exists on disk (this
     * happens in practice - some existing Domain rows reference filenames
     * under admin/assets/images/partner/ that aren't actually there, which
     * would otherwise render a broken image instead of falling back).
     * file_exists() is a local disk stat, not a network call, so this is
     * cheap enough to run on every request. Callers should always treat a
     * null return as "use the default", not surface an error - a partner
     * with no custom branding configured is the normal case, not a fault
     * condition.
     */
    public function portalLogoFile(bool $arabic = false): ?string
    {
        $domain = $this->domain;

        if (!$domain) {
            return null;
        }

        $file = $arabic ? $domain->website_logo_ar : $domain->website_logo;

        if (empty($file)) {
            return null;
        }

        return file_exists(public_path('admin/assets/images/partner/' . $file)) ? $file : null;
    }

    /**
     * Where THIS partner's Portal pages should live after login - their
     * own active *.recruitmentcv.com subdomain (same domain()/sub_domain
     * the Settings "Domain" tab and CRM's Website tab both write - see
     * ResolvePartnerWebsiteDomain, which resolves the other direction:
     * subdomain -> Partner) if one is configured and admin-approved,
     * otherwise the app's own default URL.
     *
     * Deliberately computed ONLY from this Partner's own domain() row -
     * never from the current request's Host header, a query/POST
     * parameter, or any other partner's data - so it can't be spoofed by
     * a manipulated Host header or by authenticating while browsing a
     * DIFFERENT partner's subdomain (that page's "Login as Partner"
     * modal is the same shared modal every subdomain shows). Both
     * PartnerAuthController::login() (OTP) and SocialLoginController::
     * handlePartnerGoogleCallback() (Google) call this same method
     * rather than each re-deriving the redirect host their own way.
     */
    public function portalBaseUrl(): string
    {
        $domain = $this->domain;

        if ($domain && $domain->status === 'active' && !empty($domain->sub_domain)) {
            return 'https://' . $domain->sub_domain . '.recruitmentcv.com';
        }

        return rtrim(config('app.url'), '/');
    }
}
