<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Support\PassportAccess;
use App\Support\PassportImage;

/**
 * Passport + driving licence images on RecruitmentCV hosts (App\Support\PassportAccess):
 *
 * - file(): /admin/assets/images/candidate/<...PPFRONT/PPBACK... | ...-DL-...>
 *   - the document files themselves (public/.htaccess sends these requests
 *   here instead of letting the web server hand out the file). The original
 *   only for a signed-in partner whose Registration Status is Approved
 *   (PassportAccess); everyone else - and a document-named file no candidate
 *   owns (an orphaned old upload) - gets the blurred copy. So typing /
 *   guessing / opening the file URL never bypasses the blur.
 * - preview() / licencePreview(): the blurred copy for the public resume
 *   page's gallery. Never the original, whoever asks.
 */
class PassportImageController extends Controller
{
    public function file(string $file)
    {
        $path = PassportAccess::path($file);
        $type = PassportAccess::documentType($file);
        abort_unless($path && $type, 404);

        $owned = $type === PassportAccess::LICENCE
            ? Candidate::where('lic_file', $file)->exists()
            : Candidate::where('pass_file', $file)->orWhere('pass_back_file', $file)->exists();

        // Both answers are private + no-store (no CDN / proxy keeps one for anyone else).
        return $owned && PassportAccess::protection() === null
            ? PassportImage::original($path)
            : PassportAccess::blurredResponse($file);
    }

    public function preview($id)
    {
        $post = Candidate::where('slug_text', (string) $id)->where('isdelete', 0)->first(['id', 'pass_file']);
        abort_unless($post, 404);

        return PassportAccess::blurredResponse($post->pass_file);
    }

    public function licencePreview($id)
    {
        $post = Candidate::where('slug_text', (string) $id)->where('isdelete', 0)->first(['id', 'lic_file']);
        abort_unless($post, 404);

        return PassportAccess::blurredResponse($post->lic_file);
    }
}
