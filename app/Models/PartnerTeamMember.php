<?php

namespace App\Models;

use App\Support\PhoneNumber;
use Illuminate\Database\Eloquent\Model;

/**
 * A Partner Portal team member: belongs to exactly one partner, created
 * only by that partner (Partner Portal -> Team Members), signs in on the
 * existing Partner Login with its own identifiers and then works inside
 * that partner's portal with the permissions the partner granted
 * (App\Support\PartnerTeam).
 */
class PartnerTeamMember extends Model
{
    protected $fillable = ['partner_id', 'full_name', 'username', 'email', 'country_code', 'mobile', 'status', 'permissions'];

    protected $hidden = ['password', 'google_id'];

    protected $casts = [
        'permissions' => 'array',
        'status' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /** Granted actions for a module (view / create / update / delete). */
    public function allows(string $module, string $action = 'view'): bool
    {
        return in_array($action, (array) (($this->permissions ?? [])[$module] ?? []), true);
    }

    /** A module's sub-permission (tab), e.g. ("website", "smtp"): the module itself must be granted too. */
    public function allowsSection(string $module, string $section): bool
    {
        return $this->allows($module, 'view') && $this->allows($module . '.' . $section, 'view');
    }

    /** Mobile as dialled, e.g. "+966512345678"; null when not set. */
    public function getDisplayMobileAttribute(): ?string
    {
        return $this->mobile ? '+' . $this->country_code . ' ' . $this->mobile : null;
    }

    /**
     * Login lookup by username or email (an identifier with "@" is an
     * email, as for partners). Only an exact single match counts.
     */
    public static function findByAccountIdentifier(?string $identifier): ?self
    {
        $identifier = trim((string) $identifier);
        if ($identifier === '') {
            return null;
        }

        $matches = static::where(str_contains($identifier, '@') ? 'email' : 'username', $identifier)->limit(2)->get();

        return $matches->count() === 1 ? $matches->first() : null;
    }

    /** Login lookup by mobile (stored as local number + country code). */
    public static function findByMobile(?string $countryCode, ?string $mobile): ?self
    {
        $code = preg_replace('/\D+/', '', (string) $countryCode);
        $local = PhoneNumber::local($code, $mobile);
        if ($local === '') {
            return null;
        }

        $matches = static::where('mobile', $local)->where('country_code', $code)->limit(2)->get();

        return $matches->count() === 1 ? $matches->first() : null;
    }
}
