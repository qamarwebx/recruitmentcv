<?php

namespace App\Support;

use App\Models\Candidate;

/**
 * A candidate's videos for the media slider (worker.partials.candidate-
 * gallery, shared by the public resume page and the Partner candidate page),
 * from the columns the CRM's Candidate -> Video Upload tab writes:
 * video_file (Video Upload), video_link (Introduction Video) and
 * trade_test_video_link (Trade Test Video) - always in that order, only the
 * ones that actually exist.
 */
class CandidateVideos
{
    /**
     * @return array<int, array{label: string, kind: string, src: string, thumb: ?string}>
     *   kind: 'file' (<video>), 'embed' (<iframe>) or 'link' (Open Video button)
     */
    public static function slides(Candidate $candidate): array
    {
        $slides = [];

        // Video Upload (CRM Candidate -> File -> "Uploaded Video", i.e.
        // candidates.video_file) - same URL the CRM File tab plays it from,
        // /videos/{video_file}, which serves either an older upload from
        // public/videos/ or a current one from storage/app/public/
        // testimonials/videos/ (the /videos/{filename} route; where the
        // CRM's videoFileStore() saves). Skipped if the file is in neither,
        // so there's never a broken player.
        $file = trim((string) $candidate->video_file);
        if ($file !== '' && basename($file) === $file && self::uploadedVideoExists($file)) {
            $slides[] = [
                'label' => __('locale.Video'),
                'kind' => 'file',
                'src' => asset('videos/' . rawurlencode($file)),
                'thumb' => null,
            ];
        }

        foreach ([
            'video_link' => __('locale.Introduction Video'),
            'trade_test_video_link' => __('locale.Trade Test Video'),
        ] as $column => $label) {
            if ($slide = self::fromUrl((string) $candidate->{$column}, $label)) {
                $slides[] = $slide;
            }
        }

        return $slides;
    }

    /** The two places /videos/{file} is served from (see slides()). */
    private static function uploadedVideoExists(string $file): bool
    {
        return is_file(public_path('videos/' . $file))
            || is_file(storage_path('app/public/testimonials/videos/' . $file));
    }

    /** A saved video URL -> embeddable player, or an "Open Video" link. */
    private static function fromUrl(string $url, string $label): ?array
    {
        $url = trim($url);
        if ($url === '' || !preg_match('#^https?://#i', $url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        // YouTube: embed/, watch?v=, youtu.be/, shorts/, live/ formats.
        if (preg_match('#(?:youtube(?:-nocookie)?\.com/(?:embed/|shorts/|live/|v/|watch\?(?:.*&)?v=)|youtu\.be/)([A-Za-z0-9_-]{11})#', $url, $m)) {
            return [
                'label' => $label,
                'kind' => 'embed',
                'src' => 'https://www.youtube.com/embed/' . $m[1],
                'thumb' => 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg',
            ];
        }

        // Vimeo: vimeo.com/{id} or player.vimeo.com/video/{id}.
        if (preg_match('#vimeo\.com/(?:video/)?(\d+)#', $url, $m)) {
            return ['label' => $label, 'kind' => 'embed', 'src' => 'https://player.vimeo.com/video/' . $m[1], 'thumb' => null];
        }

        // Anything else can't be embedded safely - open it in a new tab.
        return ['label' => $label, 'kind' => 'link', 'src' => $url, 'thumb' => null];
    }
}
