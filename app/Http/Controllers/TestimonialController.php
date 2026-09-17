<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adminpermission;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use App\Models\Testimonial;
use App\Models\TestimonialAdminSaveFilter;
use App\Models\TestimonialApprovalLog;
use App\Models\TestimonialActivityLog;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Carbon\Carbon;


class TestimonialController extends Controller
{
    /**
     * Ensure the current admin can access the Testimonial module, and
     * optionally a specific ability within it (e.g. 'edit_testimonial').
     * Admins (user_type == 1) and staff with full_access always pass.
     *
     * @return \App\Models\Adminpermission|null
     */
    protected function authorizeTestimonial(?string $ability = null)
    {
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if ($user->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            return $permission;
        }

        if (!isset($permission) || $permission->testimonial != 1) {
            abort(403, 'You do not have permission to access the Testimonial module.');
        }

        if ($ability && $permission->{$ability} != 1) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $permission;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;

        $permission = $this->authorizeTestimonial();

        $canViewAll = $isAdmin || (isset($permission) && ($permission->full_access == 1 || $permission->view_testimonial == 1));

        // ✅ base query
        $posts = Testimonial::with(['creator'])->orderBy('id', 'DESC');

        if (!$canViewAll) {
            $posts->where('created_by', $user->id);
        }

        $saveadminfilter = TestimonialAdminSaveFilter::where('admin_id', $user->id)->first();

        // --- FILTERS ---
        if ($request->ajax()) {
            $fullName = $request->full_name;
            $passportNumber = $request->passport_number;
            $createdBy = (array) $request->input('created_by', []);
            $dateRange = $request->date_range;
            $approvalStatus = (array) $request->input('approval_status', []);
            $paymentStatus = (array) $request->input('payment_status', []);
            $videoReceived = (array) $request->input('video_received', []);
            $socialMediaStatus = (array) $request->input('social_media_status', []);
            $searchText = $request->search_text;

            $posts->when($searchText, function ($q) use ($searchText) {
                $q->where(function ($q2) use ($searchText) {
                    $q2->where('full_name', 'like', '%' . $searchText . '%')
                        ->orWhere('passport_number', 'like', '%' . $searchText . '%');
                });
            });
        } else {
            // ✅ Normal load defaults to the admin's saved filter
            $fullName = optional($saveadminfilter)->by_full_name;
            $passportNumber = optional($saveadminfilter)->by_passport_number;
            $createdBy = optional($saveadminfilter)->by_created_by_array ?? [];
            $dateRange = optional($saveadminfilter)->by_date_range;
            $approvalStatus = optional($saveadminfilter)->by_approval_status_array ?? [];
            $paymentStatus = optional($saveadminfilter)->by_payment_status_array ?? [];
            $videoReceived = optional($saveadminfilter)->by_video_received_array ?? [];
            $socialMediaStatus = optional($saveadminfilter)->by_social_media_status_array ?? [];
        }

        $posts->when($fullName, function ($q) use ($fullName) {
            $q->where('full_name', 'like', '%' . $fullName . '%');
        });

        $posts->when($passportNumber, function ($q) use ($passportNumber) {
            $q->where('passport_number', 'like', '%' . $passportNumber . '%');
        });

        $posts->when(filled($createdBy), function ($q) use ($createdBy) {
            $q->whereIn('created_by', (array) $createdBy);
        });

        $posts->when(filled($approvalStatus), function ($q) use ($approvalStatus) {
            $q->whereIn('approval_status', (array) $approvalStatus);
        });

        $posts->when(filled($paymentStatus), function ($q) use ($paymentStatus) {
            $q->whereIn('payment_status', (array) $paymentStatus);
        });

        $posts->when(filled($videoReceived), function ($q) use ($videoReceived) {
            $q->whereIn('video_received_status', (array) $videoReceived);
        });

        $posts->when(filled($socialMediaStatus), function ($q) use ($socialMediaStatus) {
            $q->whereIn('social_media_status', (array) $socialMediaStatus);
        });

        $posts->when($dateRange, function ($q) use ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                $q->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay(),
                ]);
            }
        });

        $testimonialLists = $posts->paginate($request->page_list ?? 10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.testimonials.testimonial_load', [
                'testimonialLists' => $testimonialLists,
                'perm' => $permission,
            ]);
        }

        $careOfStaffs = Admin::where('status', 1)->orderBy('name')->get(['id', 'name']);

        return view('admin.testimonials.index', [
            'testimonialLists' => $testimonialLists,
            'perm' => $permission,
            'saveadminfilter' => $saveadminfilter,
            'careOfStaffs' => $careOfStaffs,
            'summaryCounts' => $this->buildSummaryCounts($canViewAll, $user->id),
        ]);
    }

    /**
     * Summary-card counts for the top of the listing page. A single query
     * with conditional aggregates, scoped the same way the list itself is
     * (own records only, unless the viewer can see all) — avoids fetching
     * the whole dataset or running one query per card.
     */
    private function buildSummaryCounts(bool $canViewAll, int $userId): array
    {
        $query = Testimonial::query();

        if (!$canViewAll) {
            $query->where('created_by', $userId);
        }

        $row = $query->selectRaw(
            'COUNT(*) as total,
            SUM(CASE WHEN link IS NOT NULL THEN 1 ELSE 0 END) as links_generated,
            SUM(CASE WHEN video_received_status = ? THEN 1 ELSE 0 END) as videos_received,
            SUM(CASE WHEN approval_status = ? AND payment_status != ? THEN 1 ELSE 0 END) as payments_pending,
            SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as payments_paid,
            SUM(CASE WHEN social_media_status = ? THEN 1 ELSE 0 END) as posts_published,
            SUM(CASE WHEN social_media_status = ? THEN 1 ELSE 0 END) as posts_pending',
            [
                Testimonial::VIDEO_RECEIVED,
                Testimonial::APPROVAL_APPROVED,
                Testimonial::PAYMENT_PAID,
                Testimonial::PAYMENT_PAID,
                Testimonial::SOCIAL_POSTED,
                Testimonial::SOCIAL_NOT_POSTED,
            ]
        )->first();

        return [
            'links_generated' => (int) $row->links_generated,
            'videos_received' => (int) $row->videos_received,
            'payments_pending' => (int) $row->payments_pending,
            'payments_paid' => (int) $row->payments_paid,
            'posts_published' => (int) $row->posts_published,
            'posts_pending' => (int) $row->posts_pending,
        ];
    }

    public function saveFilter(Request $request)
    {
        $this->authorizeTestimonial();

        $adminId = Auth::guard('admin')->user()->id;

        $filter = TestimonialAdminSaveFilter::firstOrNew(['admin_id' => $adminId]);

        $filter->by_full_name = $request->by_full_name ?? '';
        $filter->by_passport_number = $request->by_passport_number ?? '';
        $filter->by_created_by = $request->by_created_by ? implode(',', (array) $request->by_created_by) : '';
        $filter->by_date_range = $request->by_date_range ?? '';
        $filter->by_approval_status = $request->by_approval_status ? implode(',', (array) $request->by_approval_status) : '';
        $filter->by_payment_status = $request->by_payment_status ? implode(',', (array) $request->by_payment_status) : '';
        $filter->by_video_received = $request->by_video_received ? implode(',', (array) $request->by_video_received) : '';
        $filter->by_social_media_status = $request->by_social_media_status ? implode(',', (array) $request->by_social_media_status) : '';

        $filter->save();

        return response()->json([
            'message' => $filter->wasRecentlyCreated ? 'Filter saved successfully!' : 'Filter updated successfully!',
        ]);
    }

    public function resetFilter(Request $request)
    {
        $this->authorizeTestimonial();

        $adminId = Auth::guard('admin')->user()->id;

        $filter = TestimonialAdminSaveFilter::where('admin_id', $adminId)->first();

        if ($filter) {
            $filter->by_full_name = null;
            $filter->by_passport_number = null;
            $filter->by_created_by = null;
            $filter->by_date_range = null;
            $filter->by_approval_status = null;
            $filter->by_payment_status = null;
            $filter->by_video_received = null;
            $filter->by_social_media_status = null;
            $filter->save();
        }

        return response()->json([
            'message' => 'Filter reset successfully!',
        ]);
    }


    public function generateLink(Request $request)
    {
        $this->authorizeTestimonial('add_testimonial');

        $request->validate([
            'full_name' => 'required|string|max:255',
            'passport_number' => 'required|string|max:255',
        ]);

        $user_id = Auth::user()->id;
        $fullName = $request->full_name;
        $passportNumber = $request->passport_number;

        // Try to get an existing testimonial for this candidate (matched by passport number)
        $testimonial = Testimonial::where('passport_number', $passportNumber)->first();

        if ($testimonial && $testimonial->link) {
            // If exists, reuse the existing link
            $url = $testimonial->link;
            $testimonial->full_name = $fullName;
            $testimonial->save();
        } else {
            $url = $this->generateUniqueTestimonialLink();

            $testimonial = Testimonial::updateOrCreate(
                ['passport_number' => $passportNumber],
                [
                    'full_name' => $fullName,
                    'link' => $url,
                    'created_by' => $user_id,
                ]
            );

            Helper::testimonialActivityLog(
                $testimonial->id,
                [
                    'action' => 'Link Generated',
                    'full_name' => $fullName,
                    'passport_number' => $passportNumber,
                ],
                $user_id,
                'link_generated'
            );
        }

        return response()->json(['url' => $url]);
    }

    protected function generateUniqueTestimonialLink(): string
    {
        do {
            $randomString = Str::random(8);
            $url = url('/testimonials/link/' . $randomString);

            $exists = Testimonial::where('link', $url)->exists();
        } while ($exists);

        return $url;
    }


    public function uploadVideoForm($link)
    {
        $testimonial = $this->findTestimonialByLinkSegment($link);

        if (!$testimonial) {
            abort(404, 'Link not found'); // proper 404 if link does not exist
        }

        return view('admin.testimonials.upload_vedio_form', [
            'testimonial_id' => $testimonial->id,
            'full_name' => $testimonial->full_name,
            'encrypted_id' => $link,
            'files' => $this->testimonialFiles($testimonial, $link),
        ]);
    }

    /**
     * Look up a testimonial by the random link segment used in the public URL
     * (`/testimonials/link/{encrypted_id}`).
     */
    private function findTestimonialByLinkSegment(string $linkSegment): ?Testimonial
    {
        return Testimonial::where('link', url('/testimonials/link/' . $linkSegment))->first();
    }

    /**
     * Build the list of uploaded files for a testimonial, for display on the
     * public link page. Only files that still exist on disk are included.
     */
    private function testimonialFiles(Testimonial $testimonial, string $linkSegment): array
    {
        $videos = $testimonial->uploaded_video ? json_decode($testimonial->uploaded_video, true) : [];
        $videos = is_array($videos) ? $videos : [];

        $files = [];

        foreach ($videos as $filename) {
            $path = storage_path('app/public/testimonials/videos/' . basename($filename));

            if (!file_exists($path)) {
                continue;
            }

            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            $files[] = [
                'name' => $filename,
                'type' => $extension !== '' ? strtoupper($extension) : 'FILE',
                'size' => $this->formatFileSize(filesize($path)),
                'uploaded_at' => $this->fileUploadedAt($filename, $path),
                'previewable' => in_array($extension, ['mp4', 'webm', 'ogg', 'ogv']),
                'download_url' => route('testimonials.download_file', ['encrypted_id' => $linkSegment, 'filename' => $filename]),
                'preview_url' => route('testimonials.preview_file', ['encrypted_id' => $linkSegment, 'filename' => $filename]),
            ];
        }

        usort($files, fn ($a, $b) => $b['uploaded_at']->timestamp <=> $a['uploaded_at']->timestamp);

        return $files;
    }

    /**
     * Uploaded filenames are stored as "{unix_timestamp}_{original_name}"
     * (see uploadVideoProcess()), so the upload date can be read straight off
     * the filename without any schema change. Falls back to the file's mtime
     * for any filename that doesn't follow that pattern.
     */
    private function fileUploadedAt(string $filename, string $path): Carbon
    {
        if (preg_match('/^(\d{9,11})_/', $filename, $matches)) {
            return Carbon::createFromTimestamp((int) $matches[1]);
        }

        return Carbon::createFromTimestamp(filemtime($path));
    }

    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $power = $bytes > 0 ? min((int) floor(log($bytes, 1024)), count($units) - 1) : 0;

        return round($bytes / (1024 ** $power), 1) . ' ' . $units[$power];
    }

    /**
     * Resolve a public file request to an on-disk path, scoped to the given
     * testimonial link. Aborts 404 unless the filename is one this specific
     * testimonial actually owns, so a filename can't be swapped in the URL
     * to reach another testimonial's video.
     */
    private function resolveTestimonialFile(string $encryptedId, string $filename): string
    {
        $testimonial = $this->findTestimonialByLinkSegment($encryptedId);

        if (!$testimonial) {
            abort(404, 'Link not found');
        }

        $videos = $testimonial->uploaded_video ? json_decode($testimonial->uploaded_video, true) : [];
        $videos = is_array($videos) ? $videos : [];

        $filename = basename($filename);

        if (!in_array($filename, $videos, true)) {
            abort(404, 'File not found for this testimonial.');
        }

        $path = storage_path('app/public/testimonials/videos/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        return $path;
    }

    /**
     * Force-download a single uploaded file from the public testimonial link page.
     */
    public function downloadTestimonialFile(string $encrypted_id, string $filename)
    {
        $path = $this->resolveTestimonialFile($encrypted_id, $filename);

        return response()->download($path, basename($filename));
    }

    /**
     * Stream a single uploaded file inline (for browser preview) from the
     * public testimonial link page.
     */
    public function previewTestimonialFile(string $encrypted_id, string $filename)
    {
        $path = $this->resolveTestimonialFile($encrypted_id, $filename);

        return response()->file($path);
    }


    public function uploadVideoProcess(Request $request)
    {
        $request->validate([
            'uploaded_video' => 'required|mimes:mp4,mov,avi|max:1048576', // max 1 GB
            'full_name' => 'required|string|max:255',
            'testimonial_id' => 'required|integer|exists:testimonials,id',
        ]);

        $full_name = $request->input('full_name');

        if ($request->hasFile('uploaded_video')) {
            $file = $request->file('uploaded_video');
            $fileName = time().'_'.$file->getClientOriginalName();

            // Store video in storage/app/public/testimonials/videos
            $filePath = $file->storeAs('testimonials/videos', $fileName, 'public');

            $testimonial = Testimonial::findOrFail($request->input('testimonial_id'));

            // Get existing uploaded videos (as array)
            $videos = $testimonial->uploaded_video ? json_decode($testimonial->uploaded_video, true) : [];

            // Add new video path
            $videos[] = $fileName;

            // Save back as JSON
            $testimonial->uploaded_video = json_encode($videos);
            $testimonial->full_name = $full_name;
            $testimonial->save();

            Helper::testimonialActivityLog(
                $testimonial->id,
                [
                    'action' => 'Video Uploaded',
                    'file_name' => $fileName,
                    'submitted_name' => $full_name,
                ],
                null,
                'video_uploaded'
            );

            return response()->json([
                'success' => true,
                'path' => $filePath,
                'all_videos' => $videos
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No video uploaded'], 400);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorizeTestimonial('view_testimonial');

        $testimonial = Testimonial::with(['creator', 'paidBy'])->findOrFail($id);

        return response()->json([
            'id' => $testimonial->id,
            'full_name' => $testimonial->full_name,
            'passport_number' => $testimonial->passport_number,
            'uploaded_video' => $testimonial->uploaded_video, // array of video paths
            'link' => $testimonial->link,
            'care_of' => optional($testimonial->creator)->name,
            'approval_status' => $testimonial->approval_status,
            'payment_status' => $testimonial->payment_status,
            'paid_amount' => $testimonial->paid_amount,
            'paid_amount_formatted' => $this->formatCurrency($testimonial->paid_amount),
            'paid_by_name' => optional($testimonial->paidBy)->name,
            'paid_at' => optional($testimonial->paid_at)?->format('d M Y, h:i A'),
            'has_payment_slip' => (bool) $testimonial->payment_slip,
            'payment_slip_view_url' => $testimonial->payment_slip
                ? route('admin.testimonials.payment_slip.view', $testimonial->id)
                : null,
            'payment_slip_download_url' => $testimonial->payment_slip
                ? route('admin.testimonials.payment_slip.download', $testimonial->id)
                : null,
            'video_received_status' => $testimonial->video_received_status,
            'social_media_status' => $testimonial->social_media_status,
            'social_media_platforms' => $testimonial->social_media_platforms ?? [],
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($testimonialId)
    {
        $this->authorizeTestimonial('edit_testimonial');

        $testimonial = Testimonial::findOrFail($testimonialId);
        return response()->json([
            'id' => $testimonial->id,
            'full_name' => $testimonial->full_name,
            'passport_number' => $testimonial->passport_number,
            'uploaded_video' => $testimonial->uploaded_video, // array of video paths
            'link' => $testimonial->link,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->authorizeTestimonial('edit_testimonial');

        $testimonialId = $request->input('testimonial_id');
        $testimonial = Testimonial::findOrFail($testimonialId);

        $oldFullName = $testimonial->full_name;
        $oldPassportNumber = $testimonial->passport_number;

        // Update full name
        $testimonial->full_name = $request->input('full_name');
        $testimonial->passport_number = $request->input('passport_number');

        if ($testimonial->isDirty(['full_name', 'passport_number'])) {
            Helper::testimonialActivityLog(
                $testimonial->id,
                [
                    'action' => 'Testimonial Updated',
                    'old_full_name' => $oldFullName,
                    'new_full_name' => $testimonial->full_name,
                    'old_passport_number' => $oldPassportNumber,
                    'new_passport_number' => $testimonial->passport_number,
                ],
                Auth::guard('admin')->user()->id,
                'testimonial_updated'
            );
        }

        $testimonial->save();

        return redirect()->back()->with('success', 'Testimonial updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request)
    {
        $this->authorizeTestimonial('delete_testimonial');

        $testimonial = Testimonial::findOrFail($request->testimonial_id);

        $videos = $testimonial->uploaded_video;

        if (is_string($videos)) {
            $videos = json_decode($videos, true); // force array
        }

        if (is_array($videos)) {
            foreach ($videos as $video) {
                $videoPath = storage_path('app/public/testimonials/videos/' . $video);

                if (file_exists($videoPath)) {
                    unlink($videoPath);
                }
            }
        }

        if ($testimonial->payment_slip) {
            $slipPath = storage_path('app/public/testimonials/payment_slips/' . basename($testimonial->payment_slip));

            if (file_exists($slipPath)) {
                unlink($slipPath);
            }
        }

        $testimonial->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Testimonial deleted successfully!',
            ]);
        }

        return redirect()->back()->with('success', 'Testimonial deleted successfully!');
    }

    /**
     * Remove a single uploaded video from a testimonial.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyVideo(Request $request)
    {
        $this->authorizeTestimonial('delete_testimonial');

        $testimonial = Testimonial::findOrFail($request->testimonial_id);

        $videos = $testimonial->uploaded_video;
        if (is_string($videos)) {
            $videos = json_decode($videos, true);
        }
        $videos = is_array($videos) ? $videos : [];

        $videoName = $request->video;

        if (($key = array_search($videoName, $videos)) !== false) {
            $videoPath = storage_path('app/public/testimonials/videos/' . $videoName);
            if (file_exists($videoPath)) {
                unlink($videoPath);
            }
            unset($videos[$key]);
            $videos = array_values($videos);

            Helper::testimonialActivityLog(
                $testimonial->id,
                [
                    'action' => 'Video Removed',
                    'file_name' => $videoName,
                ],
                Auth::guard('admin')->user()->id,
                'video_removed'
            );
        }

        $testimonial->uploaded_video = json_encode($videos);
        $testimonial->save();

        return response()->json([
            'success' => true,
            'all_videos' => $videos,
        ]);
    }

    /**
     * Approve a candidate's testimonial. Isolated to this module — does not
     * touch any unrelated approval/count logic elsewhere in the app.
     */
    public function approve(Request $request)
    {
        $this->authorizeTestimonial('approval_testimonial');

        $testimonial = Testimonial::findOrFail($request->testimonial_id);
        $this->recordApprovalDecision($testimonial, Testimonial::APPROVAL_APPROVED, 'approved', $request->remarks);

        return response()->json([
            'success' => true,
            'approval_status' => $testimonial->approval_status,
            'message' => 'Testimonial approved successfully.',
        ]);
    }

    /**
     * Reject a candidate's testimonial. Isolated to this module — does not
     * touch any unrelated approval/count logic elsewhere in the app.
     */
    public function reject(Request $request)
    {
        $this->authorizeTestimonial('approval_testimonial');

        $testimonial = Testimonial::findOrFail($request->testimonial_id);
        $this->recordApprovalDecision($testimonial, Testimonial::APPROVAL_REJECTED, 'rejected', $request->remarks);

        return response()->json([
            'success' => true,
            'approval_status' => $testimonial->approval_status,
            'message' => 'Testimonial rejected successfully.',
        ]);
    }

    /**
     * Apply an approve/reject decision and write it to the approval history
     * (testimonial_approval_logs) so View Testimonial can show a full audit trail.
     */
    private function recordApprovalDecision(Testimonial $testimonial, string $newStatus, string $action, ?string $remarks): void
    {
        $previousStatus = $testimonial->approval_status;

        $testimonial->approval_status = $newStatus;
        $testimonial->save();

        TestimonialApprovalLog::create([
            'testimonial_id' => $testimonial->id,
            'action' => $action,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'admin_id' => Auth::guard('admin')->user()->id,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Mark an approved testimonial's payment as Paid. A payment slip upload is
     * mandatory — if it fails, the payment status is left untouched.
     */
    public function markPaid(Request $request)
    {
        $this->authorizeTestimonial('payment_testimonial');

        $request->validate([
            'testimonial_id' => 'required|integer|exists:testimonials,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_slip' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5 MB
        ]);

        $testimonial = Testimonial::findOrFail($request->testimonial_id);

        if ($testimonial->approval_status !== Testimonial::APPROVAL_APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Only approved testimonials can be marked as paid.',
            ], 422);
        }

        if ($testimonial->payment_status === Testimonial::PAYMENT_PAID) {
            return response()->json([
                'success' => false,
                'message' => 'This testimonial has already been marked as paid.',
            ], 422);
        }

        try {
            $file = $request->file('payment_slip');
            $fileName = time() . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
            $stored = $file->storeAs('testimonials/payment_slips', $fileName, 'public');

            if (!$stored) {
                throw new \RuntimeException('Payment slip could not be stored.');
            }
        } catch (\Throwable $e) {
            // Do not mark payment as Paid if the slip upload fails.
            return response()->json([
                'success' => false,
                'message' => 'Payment slip upload failed. Payment was not marked as paid. Please try again.',
            ], 500);
        }

        $testimonial->payment_status = Testimonial::PAYMENT_PAID;
        $testimonial->payment_slip = $fileName;
        $testimonial->paid_amount = $request->input('amount');
        $testimonial->paid_by = Auth::guard('admin')->user()->id;
        $testimonial->paid_at = now();
        $testimonial->save();

        Helper::testimonialActivityLog(
            $testimonial->id,
            [
                'action' => 'Payment Marked as Paid',
                'amount' => $testimonial->paid_amount,
                'amount_formatted' => $this->formatCurrency($testimonial->paid_amount),
                'payment_slip' => $fileName,
            ],
            Auth::guard('admin')->user()->id,
            'payment'
        );

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as paid successfully.',
            'payment_status' => $testimonial->payment_status,
            'paid_amount' => $testimonial->paid_amount,
            'paid_amount_formatted' => $this->formatCurrency($testimonial->paid_amount),
            'paid_by_name' => Auth::guard('admin')->user()->name,
            'paid_at' => $testimonial->paid_at->format('d M Y, h:i A'),
        ]);
    }

    /**
     * Format a rupee amount for display, e.g. 3000 -> "₹3,000".
     */
    private function formatCurrency($amount): ?string
    {
        if ($amount === null) {
            return null;
        }

        return '₹' . number_format((float) $amount);
    }

    /**
     * Resolve an existing payment slip to an on-disk path for a specific
     * testimonial, so a slip can't be fetched via another testimonial's id.
     */
    private function resolvePaymentSlipPath(Testimonial $testimonial): string
    {
        if (!$testimonial->payment_slip) {
            abort(404, 'No payment slip found for this testimonial.');
        }

        $path = storage_path('app/public/testimonials/payment_slips/' . basename($testimonial->payment_slip));

        if (!file_exists($path)) {
            abort(404, 'Payment slip file not found.');
        }

        return $path;
    }

    /**
     * Stream a testimonial's payment slip inline (for preview).
     */
    public function viewPaymentSlip($id)
    {
        $this->authorizeTestimonial('view_testimonial');

        $testimonial = Testimonial::findOrFail($id);
        $path = $this->resolvePaymentSlipPath($testimonial);

        return response()->file($path);
    }

    /**
     * Force-download a testimonial's payment slip.
     */
    public function downloadPaymentSlip($id)
    {
        $this->authorizeTestimonial('view_testimonial');

        $testimonial = Testimonial::findOrFail($id);
        $path = $this->resolvePaymentSlipPath($testimonial);

        return response()->download($path, basename($testimonial->payment_slip));
    }

    /**
     * Update whether the testimonial video has been received. Independent of
     * approval/payment — does not touch either flow.
     */
    public function updateVideoReceived(Request $request)
    {
        $this->authorizeTestimonial('video_received_testimonial');

        $request->validate([
            'testimonial_id' => 'required|integer|exists:testimonials,id',
            'status' => 'required|in:' . Testimonial::VIDEO_NOT_RECEIVED . ',' . Testimonial::VIDEO_RECEIVED,
        ]);

        $testimonial = Testimonial::findOrFail($request->testimonial_id);
        $oldStatus = $testimonial->video_received_status;
        $testimonial->video_received_status = $request->status;

        if ($testimonial->isDirty('video_received_status')) {
            Helper::testimonialActivityLog(
                $testimonial->id,
                [
                    'action' => 'Video Received Status Changed',
                    'old_status' => $oldStatus,
                    'new_status' => $testimonial->video_received_status,
                ],
                Auth::guard('admin')->user()->id,
                'video_received'
            );
        }

        $testimonial->save();

        return response()->json([
            'success' => true,
            'message' => 'Video received status updated successfully.',
            'video_received_status' => $testimonial->video_received_status,
        ]);
    }

    /**
     * Complete activity/history timeline for View Testimonial's Activity tab.
     * Merges the approval log (testimonial_approval_logs) and the general
     * activity log (testimonial_activity_logs) into one reverse-chronological
     * feed, so there's a single source of truth instead of two separate,
     * overlapping history displays.
     *
     * Paginated server-side via offset/limit (default page size 4, matching
     * the Activity tab's "Load More" button) — the client never asks for
     * more than one page's worth of rendered HTML at a time. The per-record
     * merge+sort itself stays in PHP rather than a cross-table SQL UNION:
     * a single testimonial's total activity count is inherently small (a
     * handful to a few dozen rows across its lifetime), so this is cheap,
     * and it avoids reconciling two differently-shaped tables in raw SQL.
     */
    public function activity($id, Request $request)
    {
        $this->authorizeTestimonial('view_testimonial');

        $testimonial = Testimonial::findOrFail($id);

        $offset = max(0, (int) $request->input('offset', 0));
        $limit = min(50, max(1, (int) $request->input('limit', 4)));

        $approvalEntries = TestimonialApprovalLog::with('admin')
            ->where('testimonial_id', $id)
            ->get()
            ->map(function ($log) {
                return [
                    'icon' => $log->action === 'approved' ? 'ti-check' : 'ti-x',
                    'color' => $log->action === 'approved' ? 'success' : 'danger',
                    'title' => $log->action === 'approved' ? 'Approved Testimonial' : 'Rejected Testimonial',
                    'details' => $log->remarks ? [['label' => 'Remarks', 'value' => $log->remarks]] : [],
                    'admin_name' => optional($log->admin)->name,
                    'created_at' => $log->created_at,
                ];
            });

        $activityEntries = TestimonialActivityLog::with('admin')
            ->where('testimonial_id', $id)
            ->get()
            ->map(fn ($log) => $this->mapActivityLogEntry($log));

        $timeline = $approvalEntries->concat($activityEntries)
            ->sortByDesc('created_at')
            ->values();

        $total = $timeline->count();
        $page = $timeline->slice($offset, $limit)->values();

        $html = view('admin.testimonials.partials.activity', [
            'timeline' => $page,
        ])->render();

        return response()->json([
            'html' => $html,
            'total' => $total,
            'offset' => $offset,
            'limit' => $limit,
            'has_more' => ($offset + $limit) < $total,
        ]);
    }

    /**
     * Turn one testimonial_activity_logs row (whose "activity" payload shape
     * varies per module) into the common {icon, color, title, details,
     * admin_name, created_at} shape the Activity tab timeline renders.
     */
    private function mapActivityLogEntry(TestimonialActivityLog $log): array
    {
        $data = $log->activity ?? [];
        $details = [];

        switch ($log->module) {
            case 'link_generated':
                $icon = 'ti-link';
                $color = 'primary';
                $title = $data['action'] ?? 'Link Generated';
                if (!empty($data['passport_number'])) {
                    $details[] = ['label' => 'Passport No.', 'value' => $data['passport_number']];
                }
                break;

            case 'video_uploaded':
                $icon = 'ti-video';
                $color = 'info';
                $title = $data['action'] ?? 'Video Uploaded';
                if (!empty($data['file_name'])) {
                    $details[] = ['label' => 'File', 'value' => $data['file_name']];
                }
                break;

            case 'video_removed':
                $icon = 'ti-video-off';
                $color = 'danger';
                $title = $data['action'] ?? 'Video Removed';
                if (!empty($data['file_name'])) {
                    $details[] = ['label' => 'File', 'value' => $data['file_name']];
                }
                break;

            case 'testimonial_updated':
                $icon = 'ti-edit';
                $color = 'secondary';
                $title = $data['action'] ?? 'Testimonial Updated';
                if (($data['old_full_name'] ?? null) !== ($data['new_full_name'] ?? null)) {
                    $details[] = ['label' => 'Name', 'value' => ($data['old_full_name'] ?? '—') . ' → ' . ($data['new_full_name'] ?? '—')];
                }
                if (($data['old_passport_number'] ?? null) !== ($data['new_passport_number'] ?? null)) {
                    $details[] = ['label' => 'Passport No.', 'value' => ($data['old_passport_number'] ?? '—') . ' → ' . ($data['new_passport_number'] ?? '—')];
                }
                break;

            case 'payment':
                $icon = 'ti-cash';
                $color = 'success';
                $title = $data['action'] ?? 'Payment Marked as Paid';
                $details[] = ['label' => 'Amount', 'value' => $data['amount_formatted'] ?? $this->formatCurrency($data['amount'] ?? null)];
                if (!empty($data['payment_slip'])) {
                    $details[] = ['label' => 'Payment Slip', 'value' => $data['payment_slip']];
                }
                break;

            case 'video_received':
                $icon = 'ti-video';
                $color = 'warning';
                $title = $data['action'] ?? 'Video Received Status Changed';
                $details[] = ['label' => 'Status', 'value' => ($data['old_status'] ?? '—') . ' → ' . ($data['new_status'] ?? '—')];
                break;

            case 'social_media':
                $icon = 'ti-share';
                $color = 'warning';
                $title = $data['action'] ?? 'Social Media Status Changed';
                $details[] = ['label' => 'Status', 'value' => ($data['old_status'] ?? '—') . ' → ' . ($data['new_status'] ?? '—')];
                if (!empty($data['new_platforms'])) {
                    $details[] = ['label' => 'Platforms', 'value' => implode(', ', $data['new_platforms'])];
                }
                break;

            default:
                $icon = 'ti-history';
                $color = 'secondary';
                $title = $data['action'] ?? 'Testimonial Activity';
                break;
        }

        return [
            'icon' => $icon,
            'color' => $color,
            'title' => $title,
            'details' => $details,
            'admin_name' => optional($log->admin)->name ?? ($log->module === 'video_uploaded' ? 'Candidate (via public link)' : 'System'),
            'created_at' => $log->created_at,
        ];
    }

    /**
     * Update the social media posting status for a testimonial. Marking as
     * Posted requires at least one platform; marking as Not Posted always
     * clears any previously stored platforms, so stale data never lingers.
     */
    public function updateSocialMedia(Request $request)
    {
        $this->authorizeTestimonial('social_media_testimonial');

        $request->validate([
            'testimonial_id' => 'required|integer|exists:testimonials,id',
            'status' => 'required|in:' . Testimonial::SOCIAL_NOT_POSTED . ',' . Testimonial::SOCIAL_POSTED,
            'platforms' => 'required_if:status,' . Testimonial::SOCIAL_POSTED . '|array',
            'platforms.*' => 'in:' . implode(',', Testimonial::SOCIAL_MEDIA_PLATFORMS),
        ]);

        $testimonial = Testimonial::findOrFail($request->testimonial_id);

        $oldStatus = $testimonial->social_media_status;
        $oldPlatforms = $testimonial->social_media_platforms ?? [];

        $testimonial->social_media_status = $request->status;
        $testimonial->social_media_platforms = $request->status === Testimonial::SOCIAL_POSTED
            ? array_values(array_intersect(Testimonial::SOCIAL_MEDIA_PLATFORMS, (array) $request->platforms))
            : null;

        if ($testimonial->isDirty(['social_media_status', 'social_media_platforms'])) {
            Helper::testimonialActivityLog(
                $testimonial->id,
                [
                    'action' => 'Social Media Status Changed',
                    'old_status' => $oldStatus,
                    'new_status' => $testimonial->social_media_status,
                    'old_platforms' => $oldPlatforms,
                    'new_platforms' => $testimonial->social_media_platforms ?? [],
                ],
                Auth::guard('admin')->user()->id,
                'social_media'
            );
        }

        $testimonial->save();

        return response()->json([
            'success' => true,
            'message' => 'Social media status updated successfully.',
            'social_media_status' => $testimonial->social_media_status,
            'social_media_platforms' => $testimonial->social_media_platforms ?? [],
        ]);
    }


}
