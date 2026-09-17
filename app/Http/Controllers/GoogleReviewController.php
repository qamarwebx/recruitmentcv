<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adminpermission;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\GoogleReview;
use App\Models\GoogleReviewAdminSaveFilter;
use App\Models\GoogleReviewApprovalLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GoogleReviewController extends Controller
{
    /**
     * Ensure the current admin can access the Google Review module, and
     * optionally a specific ability within it (e.g. 'edit_google_review').
     * Admins (user_type == 1) and staff with full_access always pass.
     */
    protected function authorizeGoogleReview(?string $ability = null)
    {
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        if ($user->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            return $permission;
        }

        if (!isset($permission) || $permission->google_review != 1) {
            abort(403, 'You do not have permission to access the Google Review module.');
        }

        if ($ability && $permission->{$ability} != 1) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $permission;
    }

    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;

        $permission = $this->authorizeGoogleReview();

        $canViewAll = $isAdmin || (isset($permission) && ($permission->full_access == 1 || $permission->view_google_review == 1));

        $posts = GoogleReview::with(['creator', 'branches'])->orderBy('id', 'DESC');

        if (!$canViewAll) {
            $posts->where('created_by', $user->id);
        }

        $saveadminfilter = GoogleReviewAdminSaveFilter::where('admin_id', $user->id)->first();

        if ($request->ajax()) {
            $branchIds = (array) $request->input('branch_id', []);
            $createdBy = (array) $request->input('created_by', []);
            $dateRange = $request->date_range;
            $approvalStatus = (array) $request->input('approval_status', []);
            $paymentStatus = (array) $request->input('payment_status', []);
            $searchText = $request->search_text;

            $posts->when($searchText, function ($q) use ($searchText) {
                $q->whereHas('branches', function ($q2) use ($searchText) {
                    $q2->where('name', 'like', '%' . $searchText . '%');
                });
            });
        } else {
            $branchIds = optional($saveadminfilter)->by_branch_array ?? [];
            $createdBy = optional($saveadminfilter)->by_created_by_array ?? [];
            $dateRange = optional($saveadminfilter)->by_date_range;
            $approvalStatus = optional($saveadminfilter)->by_approval_status_array ?? [];
            $paymentStatus = optional($saveadminfilter)->by_payment_status_array ?? [];
        }

        $posts->when(filled($branchIds), function ($q) use ($branchIds) {
            $q->whereHas('branches', function ($q2) use ($branchIds) {
                $q2->whereIn('branches.id', (array) $branchIds);
            });
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

        $posts->when($dateRange, function ($q) use ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                $q->whereBetween('created_at', [
                    Carbon::parse($dates[0])->startOfDay(),
                    Carbon::parse($dates[1])->endOfDay(),
                ]);
            }
        });

        $googleReviewLists = $posts->paginate($request->page_list ?? 10)->withQueryString();

        if ($request->ajax()) {
            return view('admin.google_reviews.google_review_load', [
                'googleReviewLists' => $googleReviewLists,
                'perm' => $permission,
            ]);
        }

        $careOfStaffs = Admin::where('status', 1)->orderBy('name')->get(['id', 'name']);
        $branches = Branch::where('status', 1)->orderBy('name')->get(['id', 'name']);

        return view('admin.google_reviews.index', [
            'googleReviewLists' => $googleReviewLists,
            'perm' => $permission,
            'saveadminfilter' => $saveadminfilter,
            'careOfStaffs' => $careOfStaffs,
            'branches' => $branches,
            'summaryCounts' => $this->buildSummaryCounts($canViewAll, $user->id),
        ]);
    }

    /**
     * Summary-card counts for the top of the listing page. A single query
     * with conditional aggregates, scoped the same way the list itself is
     * (own records only, unless the viewer can see all) — counts stay fixed
     * while the table below is filtered via AJAX, same as Testimonials.
     */
    private function buildSummaryCounts(bool $canViewAll, int $userId): array
    {
        $query = GoogleReview::query();

        if (!$canViewAll) {
            $query->where('created_by', $userId);
        }

        $row = $query->selectRaw(
            'COUNT(*) as total,
            SUM(CASE WHEN approval_status = ? THEN 1 ELSE 0 END) as pending_approval,
            SUM(CASE WHEN approval_status = ? THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN approval_status = ? THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN approval_status = ? AND payment_status != ? THEN 1 ELSE 0 END) as payment_pending,
            SUM(CASE WHEN payment_status = ? THEN 1 ELSE 0 END) as payment_paid',
            [
                GoogleReview::APPROVAL_PENDING,
                GoogleReview::APPROVAL_APPROVED,
                GoogleReview::APPROVAL_REJECTED,
                GoogleReview::APPROVAL_APPROVED, GoogleReview::PAYMENT_PAID,
                GoogleReview::PAYMENT_PAID,
            ]
        )->first();

        return [
            'total' => (int) $row->total,
            'pending_approval' => (int) $row->pending_approval,
            'approved' => (int) $row->approved,
            'rejected' => (int) $row->rejected,
            'payment_pending' => (int) $row->payment_pending,
            'payment_paid' => (int) $row->payment_paid,
        ];
    }

    public function saveFilter(Request $request)
    {
        $this->authorizeGoogleReview();

        $adminId = Auth::guard('admin')->user()->id;

        $filter = GoogleReviewAdminSaveFilter::firstOrNew(['admin_id' => $adminId]);

        $filter->by_branch = $request->by_branch ? implode(',', (array) $request->by_branch) : '';
        $filter->by_created_by = $request->by_created_by ? implode(',', (array) $request->by_created_by) : '';
        $filter->by_date_range = $request->by_date_range ?? '';
        $filter->by_approval_status = $request->by_approval_status ? implode(',', (array) $request->by_approval_status) : '';
        $filter->by_payment_status = $request->by_payment_status ? implode(',', (array) $request->by_payment_status) : '';

        $filter->save();

        return response()->json([
            'message' => $filter->wasRecentlyCreated ? 'Filter saved successfully!' : 'Filter updated successfully!',
        ]);
    }

    public function resetFilter(Request $request)
    {
        $this->authorizeGoogleReview();

        $adminId = Auth::guard('admin')->user()->id;

        $filter = GoogleReviewAdminSaveFilter::where('admin_id', $adminId)->first();

        if ($filter) {
            $filter->by_branch = null;
            $filter->by_created_by = null;
            $filter->by_date_range = null;
            $filter->by_approval_status = null;
            $filter->by_payment_status = null;
            $filter->save();
        }

        return response()->json([
            'message' => 'Filter reset successfully!',
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeGoogleReview('add_google_review');

        $request->validate([
            'branch_id' => 'required|array|min:1',
            'branch_id.*' => 'integer|exists:branches,id',
            'screenshot' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5 MB
        ]);

        $file = $request->file('screenshot');
        $fileName = time() . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
        $file->storeAs('google_reviews/screenshots', $fileName, 'public');

        $review = new GoogleReview();
        $review->screenshot = $fileName;
        $review->created_by = Auth::guard('admin')->user()->id;
        $review->approval_status = GoogleReview::APPROVAL_PENDING;
        $review->payment_status = GoogleReview::PAYMENT_PENDING;
        $review->save();
        $review->branches()->sync($request->branch_id);

        return redirect()->back()->with('success', 'Google Review added successfully.');
    }

    public function show($id)
    {
        $this->authorizeGoogleReview('view_google_review');

        $review = GoogleReview::with(['creator', 'branches', 'paidBy', 'approvalLogs.admin'])->findOrFail($id);

        return response()->json([
            'id' => $review->id,
            'branch_ids' => $review->branches->pluck('id'),
            'branch_names' => $review->branches->pluck('name')->implode(', '),
            'care_of' => optional($review->creator)->name,
            'created_at' => $review->created_at?->format('d M Y, h:i A'),
            'has_screenshot' => (bool) $review->screenshot,
            'screenshot_view_url' => $review->screenshot ? route('admin.google_review.screenshot.view', $review->id) : null,
            'screenshot_download_url' => $review->screenshot ? route('admin.google_review.screenshot.download', $review->id) : null,
            'approval_status' => $review->approval_status,
            'payment_status' => $review->payment_status,
            'paid_amount' => $review->paid_amount,
            'paid_amount_formatted' => $this->formatCurrency($review->paid_amount),
            'paid_by_name' => optional($review->paidBy)->name,
            'paid_at' => optional($review->paid_at)?->format('d M Y, h:i A'),
            'has_payment_slip' => (bool) $review->payment_slip,
            'payment_slip_view_url' => $review->payment_slip
                ? route('admin.google_review.payment_slip.view', $review->id)
                : null,
            'payment_slip_download_url' => $review->payment_slip
                ? route('admin.google_review.payment_slip.download', $review->id)
                : null,
            'approval_history' => $review->approvalLogs->map(function ($log) {
                return [
                    'status' => $log->new_status,
                    'action' => $log->action,
                    'action_by' => optional($log->admin)->name,
                    'date_time' => $log->created_at->format('d M Y, h:i A'),
                    'remarks' => $log->remarks,
                ];
            })->values(),
        ]);
    }

    public function edit(Request $request)
    {
        $this->authorizeGoogleReview('edit_google_review');

        $review = GoogleReview::with('branches')->findOrFail($request->id);

        return response()->json([
            'id' => $review->id,
            'branch_ids' => $review->branches->pluck('id'),
        ]);
    }

    public function update(Request $request)
    {
        $this->authorizeGoogleReview('edit_google_review');

        $request->validate([
            'google_review_id' => 'required|integer|exists:google_reviews,id',
            'branch_id' => 'required|array|min:1',
            'branch_id.*' => 'integer|exists:branches,id',
            'screenshot' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $review = GoogleReview::findOrFail($request->google_review_id);
        $review->branches()->sync($request->branch_id);

        if ($request->hasFile('screenshot')) {
            if ($review->screenshot) {
                $oldPath = storage_path('app/public/google_reviews/screenshots/' . basename($review->screenshot));
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('screenshot');
            $fileName = time() . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
            $file->storeAs('google_reviews/screenshots', $fileName, 'public');
            $review->screenshot = $fileName;
        }

        $review->save();

        return redirect()->back()->with('success', 'Google Review updated successfully.');
    }

    public function destroy(Request $request)
    {
        $this->authorizeGoogleReview('delete_google_review');

        $review = GoogleReview::findOrFail($request->google_review_id);

        if ($review->screenshot) {
            $screenshotPath = storage_path('app/public/google_reviews/screenshots/' . basename($review->screenshot));
            if (file_exists($screenshotPath)) {
                unlink($screenshotPath);
            }
        }

        if ($review->payment_slip) {
            $paymentSlipPath = storage_path('app/public/google_reviews/payment_slips/' . basename($review->payment_slip));
            if (file_exists($paymentSlipPath)) {
                unlink($paymentSlipPath);
            }
        }

        $review->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Google Review deleted successfully!',
            ]);
        }

        return redirect()->back()->with('success', 'Google Review deleted successfully!');
    }

    /**
     * Approve a Google Review. Isolated to this module — does not touch any
     * unrelated approval/count logic elsewhere in the app.
     */
    public function approve(Request $request)
    {
        $this->authorizeGoogleReview('approval_google_review');

        $review = GoogleReview::findOrFail($request->google_review_id);
        $this->recordApprovalDecision($review, GoogleReview::APPROVAL_APPROVED, 'approved', $request->remarks);

        return response()->json([
            'success' => true,
            'approval_status' => $review->approval_status,
            'message' => 'Google Review approved successfully.',
        ]);
    }

    /**
     * Reject a Google Review. Isolated to this module — does not touch any
     * unrelated approval/count logic elsewhere in the app.
     */
    public function reject(Request $request)
    {
        $this->authorizeGoogleReview('approval_google_review');

        $review = GoogleReview::findOrFail($request->google_review_id);
        $this->recordApprovalDecision($review, GoogleReview::APPROVAL_REJECTED, 'rejected', $request->remarks);

        return response()->json([
            'success' => true,
            'approval_status' => $review->approval_status,
            'message' => 'Google Review rejected successfully.',
        ]);
    }

    private function recordApprovalDecision(GoogleReview $review, string $newStatus, string $action, ?string $remarks): void
    {
        $previousStatus = $review->approval_status;

        $review->approval_status = $newStatus;
        $review->save();

        GoogleReviewApprovalLog::create([
            'google_review_id' => $review->id,
            'action' => $action,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'admin_id' => Auth::guard('admin')->user()->id,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Mark an approved Google Review's payment as Paid. A payment slip
     * upload is mandatory — if it fails, the payment status is left untouched.
     */
    public function markPaid(Request $request)
    {
        $this->authorizeGoogleReview('payment_google_review');

        $request->validate([
            'google_review_id' => 'required|integer|exists:google_reviews,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_slip' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5 MB
        ]);

        $review = GoogleReview::findOrFail($request->google_review_id);

        if ($review->approval_status !== GoogleReview::APPROVAL_APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Only approved Google Reviews can be marked as paid.',
            ], 422);
        }

        if ($review->payment_status === GoogleReview::PAYMENT_PAID) {
            return response()->json([
                'success' => false,
                'message' => 'This Google Review has already been marked as paid.',
            ], 422);
        }

        try {
            $file = $request->file('payment_slip');
            $fileName = time() . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
            $stored = $file->storeAs('google_reviews/payment_slips', $fileName, 'public');

            if (!$stored) {
                throw new \RuntimeException('Payment slip could not be stored.');
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment slip upload failed. Payment was not marked as paid. Please try again.',
            ], 500);
        }

        $review->payment_status = GoogleReview::PAYMENT_PAID;
        $review->payment_slip = $fileName;
        $review->paid_amount = $request->input('amount');
        $review->paid_by = Auth::guard('admin')->user()->id;
        $review->paid_at = now();
        $review->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment marked as paid successfully.',
            'payment_status' => $review->payment_status,
            'paid_amount' => $review->paid_amount,
            'paid_amount_formatted' => $this->formatCurrency($review->paid_amount),
            'paid_by_name' => Auth::guard('admin')->user()->name,
            'paid_at' => $review->paid_at->format('d M Y, h:i A'),
        ]);
    }

    private function formatCurrency($amount): ?string
    {
        if ($amount === null) {
            return null;
        }

        return '₹' . number_format((float) $amount);
    }

    private function resolveScreenshotPath(GoogleReview $review): string
    {
        if (!$review->screenshot) {
            abort(404, 'No screenshot found for this Google Review.');
        }

        $path = storage_path('app/public/google_reviews/screenshots/' . basename($review->screenshot));

        if (!file_exists($path)) {
            abort(404, 'Screenshot file not found.');
        }

        return $path;
    }

    public function viewScreenshot($id)
    {
        $this->authorizeGoogleReview('view_google_review');

        $review = GoogleReview::findOrFail($id);
        $path = $this->resolveScreenshotPath($review);

        return response()->file($path);
    }

    public function downloadScreenshot($id)
    {
        $this->authorizeGoogleReview('view_google_review');

        $review = GoogleReview::findOrFail($id);
        $path = $this->resolveScreenshotPath($review);

        return response()->download($path, basename($review->screenshot));
    }

    private function resolvePaymentSlipPath(GoogleReview $review): string
    {
        if (!$review->payment_slip) {
            abort(404, 'No payment slip found for this Google Review.');
        }

        $path = storage_path('app/public/google_reviews/payment_slips/' . basename($review->payment_slip));

        if (!file_exists($path)) {
            abort(404, 'Payment slip file not found.');
        }

        return $path;
    }

    public function viewPaymentSlip($id)
    {
        $this->authorizeGoogleReview('view_google_review');

        $review = GoogleReview::findOrFail($id);
        $path = $this->resolvePaymentSlipPath($review);

        return response()->file($path);
    }

    public function downloadPaymentSlip($id)
    {
        $this->authorizeGoogleReview('view_google_review');

        $review = GoogleReview::findOrFail($id);
        $path = $this->resolvePaymentSlipPath($review);

        return response()->download($path, basename($review->payment_slip));
    }
}
