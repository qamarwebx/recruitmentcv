<?php

namespace App\Support;

/**
 * Candidate passport image files on every host that serves the shared
 * candidate folder (recruitmentcv.com + partner sites, crm.qamarhire.com,
 * qamarhire.com) - twin file, identical in the CRM and RecruitmentCV.
 *
 * Passport files (...PPFRONT... / ...PPBACK...) are never handed out by the
 * web server: each app's .htaccess sends them to a controller that decides
 * (RecruitmentCV: App\Support\PassportAccess - approved partner; CRM: a
 * signed-in admin) and answers with original() or blurred(). Both are
 * private + no-store, so no browser cache, proxy or CDN keeps one answer
 * for another viewer.
 */
final class PassportImage
{
    /** Passport files in the candidate folder - every current upload is named ...PPFRONT... / ...PPBACK.... */
    public const FILE_PATTERN = '[^/]*PP(?:FRONT|BACK)[^/]*';

    /** URL path of a passport file (no leading slash), as the web servers see it. */
    public const PATH_PATTERN = '#^admin/assets/images/(?:payment/)?candidate/' . self::FILE_PATTERN . '$#';

    public const NO_STORE = ['Cache-Control' => 'private, no-store, max-age=0', 'X-Content-Type-Options' => 'nosniff'];

    public static function isPassportFile(?string $name): bool
    {
        return is_string($name) && $name !== '' && preg_match('#^' . self::FILE_PATTERN . '$#', $name) === 1;
    }

    public static function isPassportPath(string $path): bool
    {
        return preg_match(self::PATH_PATTERN, ltrim($path, '/')) === 1;
    }

    /** The original file (private, no-store). */
    public static function original(string $path)
    {
        return response()->file($path, self::NO_STORE)->setPrivate();
    }

    /**
     * A blurred, downscaled JPEG of a passport file (or a plain placeholder) -
     * never the original bytes. Rendered in memory (shrunk to $detailWidth px
     * wide first, so no readable detail survives, then blurred and scaled back
     * up); nothing is written to disk. Same width as the original (max 640px),
     * so layouts keep their proportions. $detailWidth = the shrink - what keeps
     * text unreadable; $passes = blur passes on that copy (softness). The CRM
     * uses the defaults; RecruitmentCV PassportAccess::BLUR_DETAIL / BLUR_PASSES.
     */
    public static function blurred(?string $path, int $passes = 6, int $detailWidth = 48)
    {
        $source = ($path && is_file($path)) ? @imagecreatefromstring((string) file_get_contents($path)) : false;

        if ($source) {
            $width = imagesx($source);
            $height = imagesy($source);
            $smallWidth = max(8, min($detailWidth, $width));
            $smallHeight = max(1, (int) round($height * $smallWidth / max(1, $width)));
            $small = imagecreatetruecolor($smallWidth, $smallHeight);
            imagecopyresampled($small, $source, 0, 0, 0, 0, $smallWidth, $smallHeight, $width, $height);
            for ($i = 0; $i < max(1, $passes); $i++) {
                imagefilter($small, IMG_FILTER_GAUSSIAN_BLUR);
            }
            $outWidth = min(640, $width);
            $outHeight = max(1, (int) round($height * $outWidth / max(1, $width)));
            $image = imagecreatetruecolor($outWidth, $outHeight);
            imagecopyresampled($image, $small, 0, 0, 0, 0, $outWidth, $outHeight, $smallWidth, $smallHeight);
            imagedestroy($small);
            imagedestroy($source);
        } else {
            $image = imagecreatetruecolor(480, 320);
            imagefill($image, 0, 0, imagecolorallocate($image, 226, 232, 240));
        }

        ob_start();
        imagejpeg($image, null, 70);
        imagedestroy($image);

        return response(ob_get_clean(), 200, ['Content-Type' => 'image/jpeg'] + self::NO_STORE);
    }
}
