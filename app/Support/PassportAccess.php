<?php

namespace App\Support;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Who may see a candidate's ORIGINAL passport or driving licence image on
 * RecruitmentCV (recruitmentcv.com, every partner subdomain and own domain) -
 * the one rule for the candidate gallery (public resume page + Partner
 * Portal), the blurred previews and the document files themselves. Passport
 * and driving licence follow exactly the same rule.
 *
 * The rule (viewing these two documents only), by the signed-in account:
 *
 *     clear document  <=>  a signed-in CUSTOMER (`web` guard) whose mobile
 *                          number is OTP verified (User::hasVerifiedMobile():
 *                          a number on file AND mobile_verified_at, set only
 *                          by the customer mobile OTP flows) - nothing about
 *                          any partner (approval, verification) plays a part
 *                     OR   a signed-in PARTNER (`partner` guard) whose mobile
 *                          number is OTP verified (Partner::hasVerifiedMobile())
 *                          AND whose registration is not Rejected (a
 *                          Rejected registration ends the partner's session
 *                          anyway - EnsureWorkerPartnerAuthenticated)
 *
 * Partner account approval (Registration Status) is NOT required to view
 * them: a Pending partner with a verified mobile sees the originals. Anyone
 * else - a partner without a verified mobile (MOBILE: "update your mobile
 * number" + Update Now), a Rejected partner, a customer without a verified
 * mobile (CUSTOMER), guests - gets the blurred copy. Approval still governs everything else (Hire Now, Download
 * CV, ...) through its own checks; nothing here changes them. Nothing else is
 * considered here (not Partner Status, the website/domain, team permissions
 * or candidate access - those keep governing their own pages and actions).
 * Team members act as their partner, so the partner's (owner's) mobile
 * counts. Read from the partner row on every request, so a change applies on
 * the next page / image load. (CRM admins see documents in the CRM.)
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

    /** A signed-in customer without an OTP-verified mobile number. */
    public const CUSTOMER = 'customer';

    /** The signed-in partner's registration is Rejected. */
    public const REGISTRATION = 'registration';

    /** The signed-in partner has no OTP-verified mobile number yet. */
    public const MOBILE = 'mobile';

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

    /** The rule: OTP-verified mobile (and not Rejected) -> clear, anything else -> blurred. Approval is not required. */
    public static function canViewClearPassport(?Partner $partner): bool
    {
        return $partner !== null && $partner->hasVerifiedMobile() && !$partner->isRegistrationRejected();
    }

    /**
     * No OTP-verified mobile (Partner::hasVerifiedMobile() - the account's own
     * fields, never a candidate's): the documents stay blurred, with the
     * "Update Now" mobile OTP step.
     */
    public static function needsMobileVerification(?Partner $partner): bool
    {
        return $partner !== null && !$partner->hasVerifiedMobile();
    }

    /** A customer: OTP-verified mobile -> clear. No partner status of any kind is considered. */
    public static function canCustomerViewClearPassport(?User $customer): bool
    {
        return $customer !== null && $customer->hasVerifiedMobile();
    }

    /** Kept name (callers): the same rule. */
    public static function canViewPassport(?Partner $partner): bool
    {
        return self::canViewClearPassport($partner);
    }

    /**
     * Why the current viewer gets the blurred copy (GUEST / CUSTOMER = no
     * verified mobile / REGISTRATION = Rejected / MOBILE = partner without a
     * verified mobile), or null = the clear document - the same answer for a
     * passport and a driving licence. A verified customer is answered first,
     * so a partner session in the same browser can never take that away.
     * $candidate is accepted for the callers but plays no part in the decision.
     */
    public static function protection($candidate = null): ?string
    {
        $customer = Auth::guard('web')->user();
        if ($customer instanceof User && self::canCustomerViewClearPassport($customer)) {
            return null;
        }

        $viewer = Auth::guard('partner')->user();
        if ($viewer) {
            if ($viewer->isRegistrationRejected()) {
                return self::REGISTRATION;
            }

            return self::needsMobileVerification($viewer) ? self::MOBILE : null;
        }

        return $customer ? self::CUSTOMER : self::GUEST;
    }

    /** The note shown on a blurred document, for its type. */
    public static function message(string $reason, string $document = self::PASSPORT): string
    {
        $licence = $document === self::LICENCE;

        // Mobile verification: the one Passport message, on either document's
        // overlay (the driving licence stays blurred by the same rule).
        if ($reason === self::MOBILE) {
            return __('locale.Please update your mobile number to this account to view the Passport.');
        }

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
