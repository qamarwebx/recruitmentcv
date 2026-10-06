<?php

namespace App\Support;

use App\Models\Domain;
use App\Models\Partner;
use App\Models\PartnerPageContent;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;

/**
 * Emails RecruitmentCV sends to a customer on behalf of a partner (booking
 * confirmation, email changed). Sent through that partner's SMTPs in
 * failover order, then the Global SMTPs, else the .env mailer
 * (App\Support\SmtpMailer::forPartner()). The partner id always comes from
 * the server side (the booking's partner, or the site the customer is on) -
 * never the request.
 *
 * Sending is synchronous (the queue worker on this server belongs to
 * another app) and never throws: a failed email must not undo or block the
 * booking / email change it is about.
 */
class CustomerMail
{
    public static function send(?int $partnerId, ?string $to, Mailable $mail): bool
    {
        if (!filter_var((string) $to, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail->locale(app()->getLocale());

        try {
            SmtpMailer::forPartner($partnerId)->to($to)->send($mail);

            return true;
        } catch (\Throwable $e) {
            // Class only - never the message (it could echo settings).
            Log::warning('Customer email failed', ['partner_id' => $partnerId, 'mail' => get_class($mail), 'exception' => get_class($e)]);

            return false;
        }
    }

    /**
     * Header/footer branding for a partner's customer emails: company name,
     * Branding logo and website (RecruitmentCV defaults when none), plus the
     * partner's WhatsApp support link for the "need help" line.
     */
    public static function brand(?int $partnerId): array
    {
        $isArabic = app()->getLocale() === 'ar';
        $partner = $partnerId ? Partner::find($partnerId) : null;
        $domain = $partner ? Domain::where('partner_id', $partner->id)->first() : null;

        $name = $domain ? ($isArabic ? ($domain->company_name_ar ?: $domain->company_name) : $domain->company_name) : null;
        $name = $name ?: ($partner ? ($partner->portal_rec_off_name ?: $partner->rec_off_name) : null);

        $logo = $domain ? $domain->portalLogoFile($isArabic) : null;

        return [
            'name' => $name ?: 'RecruitmentCV',
            'logo' => $logo
                ? asset('admin/assets/images/partner/' . $logo)
                : asset('user/img/logo/' . ($isArabic ? 'new_logo_Arabic.png' : 'logo_english.png')),
            'site_url' => $domain && $domain->full_domain ? 'https://' . $domain->full_domain : url('/'),
            'whatsapp' => $partner ? PartnerPageContent::effectiveWhatsapp($partner->id)['link'] : null,
            'dir' => $isArabic ? 'rtl' : 'ltr',
            'lang' => $isArabic ? 'ar' : 'en',
        ];
    }
}
