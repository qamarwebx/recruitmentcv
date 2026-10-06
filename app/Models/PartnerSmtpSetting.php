<?php

namespace App\Models;

use App\Support\SmtpMailer;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Database\Eloquent\Model;

/**
 * LEGACY - the former single SMTP per partner (partner_smtp_settings). Each
 * row was copied into website_smtp_settings as that partner's SMTP #1 by the
 * 2026_10_07_120000 migration; partners now have an ordered SMTP list there
 * (WebsiteSmtpSetting, App\Support\SmtpMailer / SmtpSettings). The table is
 * kept untouched as a backup and nothing writes to it any more.
 *
 * Kept only so any older caller keeps working: mailerFor() sends through
 * the partner's current SMTP list with failover. Twin file in both apps.
 */
class PartnerSmtpSetting extends Model
{
    public const MAILERS = WebsiteSmtpSetting::MAILERS;

    public const ENCRYPTIONS = WebsiteSmtpSetting::ENCRYPTIONS;

    public const PORTS = WebsiteSmtpSetting::PARTNER_PORTS;

    protected $guarded = ['*'];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'encrypted',
        'port' => 'integer',
        'status' => 'boolean',
    ];

    /** The partner's SMTPs in failover order, then the Global ones, else .env. */
    public static function mailerFor(?int $partnerId): Mailer
    {
        return SmtpMailer::forPartner($partnerId);
    }
}
