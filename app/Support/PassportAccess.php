<?php

namespace App\Support;

use App\Models\Partner;
use Illuminate\Support\Facades\Auth;

/**
 * Who may see a candidate's ORIGINAL passport or driving licence image on
 * RecruitmentCV (recruitmentcv.com, every partner subdomain and own domain) -
 * the one rule for the candidate gallery (public resume page + Partner
 * Portal), the blurred previews and the document files themselves. Passport
 * and driving licence follow exactly the same rule.
 *
 * The ONE rule - Registration Status is the only condition:
 *
 *     clear document  <=>  the signed-in partner's Registration Status is
 *                          Approved (Partner::isRegistrationApproved(),
 *                          partners.registration_status === 1)
 *
 * Anything else - Pending, Rejected, any other value, or no partner at all
 * (guests, customers) - gets the blurred copy. Nothing else is considered
 * (not Partner Status, email/mobile verification, the website/domain, team
 * permissions or candidate access - those keep governing their own pages
 * and actions, not the document images). Read from the partner row on every
 * request, so a status change applies on the next page / image load. (CRM
 * admins see documents in the CRM.)
 *
 * Protected = the page never gets the original file's URL, only a
 * server-blurred copy; the document files on RecruitmentCV hosts are served
 * through PassportImageController (see public/.htaccess), which applies
 * this same rule (App\Support\PassportImage renders both answers).
 */
final class PassportAccess
{
    /** Document types. */
    public const PASSPORT = 'passport';
    public const LICENCE = 'licence';

    /** Nobody signed in. */
    public const GUEST = 'guest';

    /** A signed-in customer (no partner) - unchanged: blurred, as before. */
    public const CUSTOMER = 'customer';

    /** The signed-in partner's Registration Status is not Approved (Pending, Rejected, ...). */
    public const REGISTRATION = 'registration';

    /** Passport files (front / back) in the candidate folder (twin App\Support\PassportImage). */
    public const FILE_PATTERN = PassportImage::FILE_PATTERN;

    /** Driving licence files in the candidate folder - every current upload is named ...-DL-.... */
    public const LICENCE_FILE_PATTERN = '[^/]*-DL-[^/]*';

    /** Either document (the file route; public/.htaccess sends the same names to it). */
    public const DOCUMENT_FILE_PATTERN = '[^/]*(?:PP(?:FRONT|BACK)|-DL-)[^/]*';

    /**
     * RecruitmentCV's blurred copies - lighter than the CRM's defaults (48px, 6
     * passes): shrunk to 96px wide, so the layout, photo and document type show
     * but text stays ~3px tall even on a close-up (unreadable), then 2 passes.
     */
    public const BLUR_DETAIL = 96;
    public const BLUR_PASSES = 2;

    private const CANDIDATE_DIR = 'admin/assets/images/candidate/';

    /** The rule: Registration Status Approved -> clear, anything else -> blurred. */
    public static function canViewClearPassport(?Partner $partner): bool
    {
        return $partner !== null && $partner->isRegistrationApproved();
    }

    /** Kept name (callers): the same rule. */
    public static function canViewPassport(?Partner $partner): bool
    {
        return self::canViewClearPassport($partner);
    }

    /**
     * Why the current viewer gets the blurred copy (GUEST / CUSTOMER /
     * REGISTRATION), or null = the clear document - the same answer for a
     * passport and a driving licence. $candidate is accepted for the callers
     * but plays no part in the decision.
     */
    public static function protection($candidate = null): ?string
    {
        $viewer = Auth::guard('partner')->user();
        if ($viewer) {
            return self::canViewClearPassport($viewer) ? null : self::REGISTRATION;
        }

        return Auth::guard('web')->check() ? self::CUSTOMER : self::GUEST;
    }

    /** The note shown on a blurred document, for its type. */
    public static function message(string $reason, string $document = self::PASSPORT): string
    {
        $licence = $document === self::LICENCE;

        if ($reason === self::GUEST) {
            return $licence
                ? __('locale.View the driving licence after logging in to your account.')
                : __('locale.View the passport after logging in to your account.');
        }

        return $licence
            ? __('locale.Driving licence is available after Partner account approval.')
            : __('locale.Passport is available after Partner account approval.');
    }

    /** Blurred-copy URL for the gallery: the Partner Portal's own (its candidate access rule) or the public one. */
    public static function previewUrl(string $slug, string $document = self::PASSPORT): string
    {
        $portal = request()->routeIs('worker.partner.*') && Auth::guard('partner')->check();

        if ($document === self::LICENCE) {
            return $portal ? route('worker.partner.candidates.licence', $slug) : route('worker.resume.licence', $slug);
        }

        return $portal ? route('worker.partner.candidates.passport', $slug) : route('worker.resume.passport', $slug);
    }

    public static function isPassportFile(string $name): bool
    {
        return PassportImage::isPassportFile($name);
    }

    public static function isLicenceFile(?string $name): bool
    {
        return is_string($name) && $name !== '' && preg_match('#^' . self::LICENCE_FILE_PATTERN . '$#', $name) === 1;
    }

    /** PASSPORT / LICENCE for a protected document file name, null for anything else. */
    public static function documentType(?string $name): ?string
    {
        if (PassportImage::isPassportFile($name)) {
            return self::PASSPORT;
        }

        return self::isLicenceFile($name) ? self::LICENCE : null;
    }

    /** Absolute path of a candidate-folder file (basename only), null when missing. */
    public static function path(?string $file): ?string
    {
        $name = basename((string) $file);
        $path = $name !== '' && $name === (string) $file ? public_path(self::CANDIDATE_DIR . $name) : null;

        return $path && is_file($path) ? $path : null;
    }

    /** The blurred copy of a candidate-folder file (never the original). */
    public static function blurredResponse(?string $file)
    {
        return PassportImage::blurred(self::path($file), self::BLUR_PASSES, self::BLUR_DETAIL);
    }
}
