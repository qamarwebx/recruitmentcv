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
     * Partner Account -> Edit Account Details fields the partner can complete
     * that are still empty (form field names): Full Name, Company /
     * Recruitment Office Name, Country, City. Drives the once-per-login
     * "Account Details" prompt (App\Support\PartnerAccountPrompt). The
     * Recruitment Licence Number is not one of them: it is read-only for
     * partners (managed in the CRM), so it never triggers the prompt.
     */
    public function missingAccountDetails(): array
    {
        $missing = [];
        foreach (['owner_name', 'rec_off_name', 'country_id', 'city_id'] as $field) {
            $value = $this->{$field};
            $isId = str_ends_with($field, '_id');
            if (blank($value) || ($isId && (int) $value <= 0)) {
                $missing[] = $field;
            }
        }

        return $missing;
    }

    /**
     * A verified mobile: a number on file AND mobile_verified_at (set only by
     * the mobile OTP flows). mobile_verified_at can be set (e.g. stale/legacy
     * data, or a partner who verified a number and then had it cleared)
     * while owner_mobile_no is empty, so checking mobile_verified_at alone is
     * NOT sufficient - both are checked together, every time.
     */
    public function hasVerifiedMobile(): bool
    {
        return !empty($this->owner_mobile_no) && !is_null($this->mobile_verified_at);
    }

    /**
     * A verified email: an address on file AND email_verified_at (set only by
     * the emailed-code flow, PartnerPortalController::accountEmailVerify()).
     * Same both-flags rule as hasVerifiedMobile().
     */
    public function hasVerifiedEmail(): bool
    {
        return filled($this->email) && !is_null($this->email_verified_at);
    }

    /**
     * Single source of truth for a partner's candidate actions - Hire Now,
     * Download CV and seeing the passport image: mobile AND email verified.
     * Enforced server-side (PartnerHireController::store(),
     * PartnerPortalController::candidateCv(), candidate-gallery's passport).
     */
    public function isFullyVerified(): bool
    {
        return $this->hasVerifiedMobile() && $this->hasVerifiedEmail();
    }

    /**
     * CRM Registration Request = Approved (registration_status 1). The one
     * check for Hire Now and Download CV (PartnerHireController::store(),
     * PartnerPortalController::candidateCv()) - read from this row on every
     * request, so a change in the CRM applies on the next click. It does NOT
     * gate signing in or the Portal itself (see isRegistrationRejected()).
     */
    public function isRegistrationApproved(): bool
    {
        return (int) $this->registration_status === 1;
    }

    /**
     * CRM Registration Request = Rejected (registration_status 2): the only
     * registration state that refuses sign-in / the Portal. Pending partners
     * sign in and use the Portal; only Hire Now / Download CV wait for
     * approval (isRegistrationApproved()).
     */
    public function isRegistrationRejected(): bool
    {
        return (int) $this->registration_status === 2;
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
     * Recruitment Licence Number is always stored normalized (App\Support\
     * LicenceNumber::normalize(): trimmed, empty = null), whichever code
     * path saves it - the uniqueness rule compares that same form.
     */
    public function setLicenceNumberAttribute($value): void
    {
        $this->attributes['licence_number'] = \App\Support\LicenceNumber::normalize($value);
    }

    /**
     * RecruitmentCV portal access revoked: the partner's subdomain was removed
     * in CRM (Domain::isSubdomainRemoved()). Such a partner can't sign in or
     * keep a session anywhere on RecruitmentCV until a new subdomain is saved
     * (PartnerAuthController / SocialLoginController, EnforcePartnerPortalAccess).
     * A partner that never had a subdomain (e.g. a new registration) is not
     * revoked.
     */
    public function isPortalRevoked(): bool
    {
        // A website address was removed in CRM (Remove Subdomain / Remove
        // Own Domain) and no other address of this partner serves now.
        $domain = $this->domain;

        return $domain !== null
            && ($domain->isSubdomainRemoved() || $domain->custom_domain_removed_at !== null)
            && \App\Support\PartnerDomains::hosts($domain) === [];
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
     * The partner's account status as the CRM sets it (Admin -> Partner):
     * registration_status (Registration Request: 0 pending, 1 approved,
     * 2 rejected - also what EnsureWorkerPartnerAuthenticated gates the
     * Portal on) and status (Partner Status: 1 active, 0 inactive).
     * Derived only - no separate status is stored. Portal Status
     * (portal_status) is not part of it: it only lists the partner as an
     * office on the main site's booking flow.
     *
     * @return string 'pending' | 'rejected' | 'inactive' | 'active'
     */
    public function accountStatus(): string
    {
        return match ((int) $this->registration_status) {
            1 => (int) $this->status === 1 ? 'active' : 'inactive',
            2 => 'rejected',
            default => 'pending',
        };
    }

    /**
     * Where THIS partner's Portal pages should live after login - their
     * own *.recruitmentcv.com subdomain (same domain()/sub_domain the
     * Settings "Domain" tab and CRM's Website tab both write - see
     * ResolvePartnerWebsiteDomain, which resolves the other direction:
     * subdomain -> Partner) when it is live (Domain::isLiveSubdomain()),
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
        // The partner's primary live address - its active Own Domain when
        // that is the selected Domain Type, else its live subdomain - by the
        // same rules ResolvePartnerWebsiteDomain serves sites by
        // (App\Support\PartnerDomains); else the main site.
        return \App\Support\PartnerDomains::primaryUrl($this->domain) ?? rtrim(config('app.url'), '/');
    }
}
