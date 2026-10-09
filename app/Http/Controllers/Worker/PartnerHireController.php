<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\Candidate;
use App\Models\Expecworkcity;
use App\Models\Orderrecivepanel;
use App\Models\Visadetails;
use App\Models\Websiteconfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Partner-initiated order actions from the Worker Partner Portal: "Hire
 * Now" (create) and "Cancel Booking" (cancel), both scoped to the logged-in
 * partner's own orders.
 *
 * Neither of these calls into BookingController::store3()/PartnerBookingController::cancelBooking()
 * (the existing customer-facing "Choose Your Nearest Recruitment Office"
 * flow and its admin-side cancel counterpart). Those are hard-wired to a
 * web-guard User for customer notifications - store3() unconditionally
 * loads `User::find($post->user_id)` to send a WhatsApp/email "order
 * confirmation", and cancelBooking() does the same for cancellation
 * notifications. Partners authenticate through a completely separate
 * `partners` table with no link to `users` at all, so there is no real
 * customer to notify here for orders placed via this portal.
 *
 * Both methods below reproduce the same *booking record shape* as their
 * reference counterparts (reference_no generation, candidate-booking-limit
 * handling, the Activity timeline entry) but skip the customer
 * notification blast entirely. `store()` stores `user_id = 0` as an
 * explicit sentinel for "partner self-hire, no customer" (bookings.user_id
 * is NOT NULL in the schema, so 0 - not a real user - is the marker).
 * `cancel()` additionally scopes by `partner_id` before touching a booking
 * (PartnerBookingController::cancelBooking() does not - any authenticated
 * partner could cancel any other partner's booking there; that gap is not
 * reproduced here). BookingController.php/PartnerBookingController.php are
 * intentionally left untouched.
 */
class PartnerHireController extends Controller
{
    /**
     * Hire Now's first step (hire-now.js): is this candidate currently hired
     * by another customer / partner (App\Support\PartnerCandidateAccess::hiredByOther())?
     * Only a warning - the page then shows "Already Hired" before the Hire
     * Now modal; store() below still decides (and re-checks everything) on
     * submit. Read-only, a yes/no only (never who), for a candidate this
     * partner may open (same access rule as the candidate page).
     */
    public function status($id)
    {
        $partnerId = (int) Auth::guard('partner')->id();
        $post = Candidate::where('slug_text', $id)->where('isdelete', 0)->first();

        if (!$post || !\App\Support\PartnerCandidateAccess::allows($partnerId, $post)) {
            return response()->json(['status' => 'error', 'message' => __('locale.Candidate not found.')], 404)
                ->header('Cache-Control', 'no-store, private');
        }

        return response()->json([
            'status' => 'ok',
            'hired_by_other' => \App\Support\PartnerCandidateAccess::hiredByOther($partnerId, (int) $post->id),
        ])->header('Cache-Control', 'no-store, private');
    }

    public function store(Request $request, $id)
    {
        $partner = Auth::guard('partner')->user();

        // CRM Registration Request must be Approved (Partner::isRegistrationApproved())
        // - checked first, from the partner row loaded for this request, so a
        // CRM change applies on the next click. The page shows the same
        // "not approved - contact support" modal.
        if (!$partner->isRegistrationApproved()) {
            return response()->json([
                'status' => 'not_approved',
                'message' => __('locale.Your Account is not Approved yet, please contact our support team.'),
            ], 403);
        }

        // Authoritative gate - the Hire Now button/modal is only ever
        // JS-gated on the client (data-mobile-verified), which is a UX
        // convenience, not a security boundary. This is the one place that
        // actually creates the order, so it's re-checked here regardless of
        // how the request arrived (direct POST, ?hire=1 auto-open, stale
        // client-side attribute, etc.) - see Partner::isFullyVerified():
        // mobile AND email verified. The status says which one is missing so
        // the page starts the right existing flow (mobile OTP modal / My
        // Account email verification).
        if (!$partner->hasVerifiedMobile()) {
            return response()->json([
                'status' => 'mobile_unverified',
                'message' => __('locale.Please verify your mobile number first.'),
            ], 403);
        }
        if (!$partner->isFullyVerified()) {
            return response()->json([
                'status' => 'email_unverified',
                'message' => __('locale.Please verify your email address first.'),
            ], 403);
        }

        $post = Candidate::where('slug_text', $id)->where('isdelete', 0)->first();

        if (!$post) {
            return response()->json(['status' => 'error', 'message' => __('locale.Candidate not found.')], 404);
        }

        // reservation_lock: the CRM reservation queue holds / has selected / is
        // full for this candidate - never hireable here, even if status says so.
        $isAvailable = (int) $post->status === 1 && (int) $post->publish === 1 && (int) $post->cv_execute === 1 && empty($post->reservation_lock);

        if (!$isAvailable) {
            return response()->json(['status' => 'error', 'message' => __('locale.This candidate is no longer available to hire.')], 422);
        }

        $validator = Validator::make($request->all(), [
            'worklocation' => 'required|integer|exists:expecworkcities,id',
            'employer_name' => 'nullable|string|max:150',
            'mobile_number' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        // Re-validate city eligibility server-side. The reference flow
        // (FrontEndController::getexpectedwork3(), wired up as a
        // jquery-validate `remote` rule on #bookingsubForm3) only checks
        // this in the browser - store3() itself accepts whatever
        // worklocation the request carries with no re-check, so a direct
        // POST bypassing the picker isn't actually blocked there either.
        // This closes that gap rather than reproducing it: a candidate
        // with no preference set is eligible for any city (matching
        // getexpectedwork3()'s own "empty = any" rule); one with
        // preferences must get a city from that list.
        if (!empty($post->expwp_id)) {
            $eligibleCityIds = explode(',', $post->expwp_id);
            if (!in_array((string) $request->worklocation, $eligibleCityIds, true)) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('locale.This candidate is not eligible for the selected city, please try a different candidate.'),
                ], 422);
            }
        }

        $booking = null;
        // Order number + insert under the order-number lock shared with the CRM
        // (App\Support\BookingReference), held until the order is committed.
        $response = \App\Support\BookingReference::withLock(function () use ($request, $post, $partner, &$booking) {
            return DB::transaction(function () use ($request, $post, $partner, &$booking) {
            // Duplicate guard: a partner should never end up with two live
            // orders for the same candidate, whether from a genuine
            // double-click or a repeated request. Locks the matching rows
            // for the duration of this transaction to close the race
            // window between this check and the insert below.
            $existing = DB::table('bookings')
                ->where('partner_id', $partner->id)
                ->where('cand_id', $post->id)
                ->where('booking_status', '!=', 2)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return response()->json([
                    'status' => 'duplicate',
                    'message' => __('locale.You already have an order for this candidate.'),
                    'booking_id' => $existing->id,
                ], 409);
            }

            // Next free order number (highest + 1), allocated under the shared lock.
            $referenceNo = \App\Support\BookingReference::next();

            // Order-received-panel staff round-robin - same pattern as
            // store3(), so partner self-hire orders route to staff the
            // same way customer orders do.
            $candLimit = Websiteconfig::first();
            $orderReceiveUserId = '';

            if ($candLimit && (int) $candLimit->order_receieved !== 0) {
                $panels = Orderrecivepanel::where('status', 1)->get();
                $panelCount = $panels->count();

                if ($panelCount > 0) {
                    if ($panelCount > 1) {
                        $assignedCount = Booking::where('orderrecuser_id', '!=', '')->count();
                        $avgPerPanel = $assignedCount / $panelCount;
                        $candidates = [];
                        foreach ($panels as $panel) {
                            $panelLoad = Booking::where('orderrecuser_id', '=', $panel->staff_id)->count();
                            if ($panelLoad <= $avgPerPanel) {
                                $candidates[] = $panel->staff_id;
                            }
                        }
                        $orderReceiveUserId = $candidates[0] ?? $panels[0]->staff_id;
                    } else {
                        $orderReceiveUserId = $panels[0]->staff_id;
                    }
                }
            }

            $bookingData = [
                'user_id' => 0,
                'cand_id' => $post->id,
                'partner_id' => $partner->id,
                'reference_no' => $referenceNo,
                'ord_status_id' => '1',
                'booking_date' => now(),
                'worklocation' => $request->worklocation,
            ];

            // orderrecuser_id is a nullable int column - only set it when a
            // staff member was actually assigned, matching store3()'s own
            // `if ($ordrrUserID != '')` guard (an unconditional '' would
            // fail the int cast at insert time).
            if ($orderReceiveUserId !== '') {
                $bookingData['orderrecuser_id'] = $orderReceiveUserId;
            }

            $booking = Booking::create($bookingData);

            // Employer Name / Mobile Number are optional, quick-capture
            // fields on the Hire Now modal - reused from visadetails'
            // existing employer_name/mobile_no columns (already surfaced
            // on the order-details "Visa & Employer Details" card, and
            // otherwise unused) rather than adding new order columns.
            // Visadetails declares neither $fillable nor $guarded, so
            // mass assignment would throw - built up via direct property
            // assignment, same pattern as PartnerPortalController::orderVisaStore().
            $employerName = trim((string) $request->input('employer_name'));
            $mobileNumber = trim((string) $request->input('mobile_number'));
            if ($employerName !== '' || $mobileNumber !== '') {
                $visa = new Visadetails();
                $visa->booking_id = $booking->id;
                $visa->user_id = $booking->user_id;
                $visa->cand_id = $post->id;
                $visa->partner_id = $partner->id;
                if ($employerName !== '') {
                    $visa->employer_name = $employerName;
                }
                if ($mobileNumber !== '') {
                    $visa->mobile_no = $mobileNumber;
                }
                $visa->save();
            }

            // Candidate booking-limit handling - same pattern as store3():
            // once a candidate has reached the configured booking limit
            // across all partners, take it off the market.
            $liveBookingCount = Booking::where('cand_id', $post->id)->where('status', 0)->count();
            if ($candLimit && $candLimit->cand_booking_limit <= $liveBookingCount) {
                $post->status = false;
                $post->save();
            }

            $partnerLabel = $partner->rec_off_name ?: $partner->owner_name ?: 'Partner';

            Activity::create([
                'cand_id' => $post->id,
                'partner_id' => $partner->id,
                'headline' => 'Candidate booked by ' . $partnerLabel,
                'bodyMessage' => 'Candidate booked by ' . $partnerLabel . ' on ' . now()->format('d-m-Y h:i A'),
                'icons' => 'ti ti-checklist',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('locale.Order placed! Your order number is') . ' ' . $booking->reference_no . '.',
                'booking_id' => $booking->id,
                'reference_no' => $booking->reference_no,
            ]);
            });
        });

        // Only for an order this request created, after it committed.
        if ($booking) {
            $workCity = Expecworkcity::find($booking->worklocation);
            \App\Support\NotificationCenter::notify('partner_order_placed', (int) $partner->id, [
                'title' => 'New partner order ' . $booking->reference_no,
                'message' => ($partner->rec_off_name ?: $partner->owner_name) . ' placed an order (Hire Now) in the Partner Portal.',
                'partner_name' => (string) ($partner->rec_off_name ?: $partner->owner_name),
                'lines' => [
                    ['Partner', (string) ($partner->rec_off_name ?: $partner->owner_name)],
                    ['Team Member', optional(\App\Support\PartnerTeam::current())->full_name],
                    ['Order No', (string) $booking->reference_no],
                    ['Candidate', trim((string) $post->cand_name) . ' (' . ($post->reference_no ?: '#' . $post->id) . ')'],
                    ['Work City', $workCity ? (string) $workCity->name : null],
                ],
                'action' => ['Open Order in CRM', \App\Support\NotificationCenter::crmUrl('admin/booking/view/' . $booking->id)],
                'dedupe' => 'booking:' . $booking->id,
                'context' => ['booking_id' => $booking->id, 'id' => $booking->id],
            ]);
        }

        return $response;
    }

    public function cancel($id)
    {
        $partner = Auth::guard('partner')->user();

        $booking = Booking::where('id', $id)->where('partner_id', $partner->id)->first();

        if (!$booking) {
            return response()->json(['status' => 'error', 'message' => __('locale.Order not found.')], 404);
        }

        if ((int) $booking->booking_status === 2) {
            return response()->json(['status' => 'error', 'message' => __('locale.This order is already cancelled.')], 422);
        }

        $response = DB::transaction(function () use ($booking, $partner) {
            $booking->status = true;
            $booking->booking_status = 2;
            $booking->save();

            // Candidate booking-limit handling - same rule as
            // PartnerBookingController::cancelBooking(): re-publish the
            // candidate once they're back under the configured limit.
            $candLimit = Websiteconfig::first();
            $liveBookingCount = Booking::where('cand_id', $booking->cand_id)->where('status', 0)->count();
            if ($candLimit && $liveBookingCount < $candLimit->cand_booking_limit) {
                $cand = Candidate::find($booking->cand_id);
                // Re-shown only if the CRM reservation queue didn't hide it (reservation_lock full / hold / selected).
                if ($cand && empty($cand->reservation_lock)) {
                    $cand->status = true;
                    $cand->publish = true;
                    $cand->save();
                }
            }

            $partnerLabel = $partner->rec_off_name ?: $partner->owner_name ?: 'Partner';

            Activity::create([
                'cand_id' => $booking->cand_id,
                'partner_id' => $partner->id,
                'headline' => 'Candidate booking cancelled by ' . $partnerLabel,
                'bodyMessage' => 'Candidate booking cancelled by ' . $partnerLabel . ' on ' . now()->format('d-m-Y h:i A'),
                'icons' => 'ti ti-square-x',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('locale.Order cancelled.'),
            ]);
        });

        $this->notifyCancelled($booking->fresh(), $partner);

        return $response;
    }

    /**
     * Order cancelled: CRM staff, and the customer when it is a customer
     * order (email here, WhatsApp through the existing order job with the
     * Meta Automation "recruitmentcv / cancel_order" rule). Each follows
     * CRM -> Website -> Settings and is logged.
     */
    private function notifyCancelled(Booking $booking, $partner): void
    {
        $candidate = Candidate::find($booking->cand_id);
        $partnerName = (string) ($partner->rec_off_name ?: $partner->owner_name);
        $candidateLabel = $candidate ? trim((string) $candidate->cand_name) . ' (' . ($candidate->reference_no ?: '#' . $candidate->id) . ')' : '#' . $booking->cand_id;
        $context = ['booking_id' => $booking->id, 'id' => $booking->id];

        \App\Support\NotificationCenter::notify('order_cancelled_staff', (int) $partner->id, [
            'title' => 'Order ' . $booking->reference_no . ' cancelled',
            'message' => $partnerName . ' cancelled an order in the Partner Portal.',
            'partner_name' => $partnerName,
            'lines' => [
                ['Partner', $partnerName],
                ['Team Member', optional(\App\Support\PartnerTeam::current())->full_name],
                ['Order No', (string) $booking->reference_no],
                ['Candidate', $candidateLabel],
                ['Order Type', (int) $booking->user_id > 0 ? 'Customer order' : 'Partner order'],
            ],
            'action' => ['Open Order in CRM', \App\Support\NotificationCenter::crmUrl('admin/booking/view/' . $booking->id)],
            'dedupe' => 'cancel:' . $booking->id,
            'context' => $context,
        ]);

        if ((int) $booking->user_id <= 0) {
            return;
        }
        $customer = \App\Models\User::find($booking->user_id);
        \App\Support\NotificationCenter::notify('customer_order_cancelled', (int) $partner->id, [
            'channels' => ['email'],
            'customer_id' => (int) $booking->user_id,
            'title' => 'Your order ' . $booking->reference_no . ' was cancelled',
            'message' => 'Your order has been cancelled by the recruitment office. If you have any questions, please contact us.',
            'lines' => [
                ['Order No', (string) $booking->reference_no],
                ['Candidate', $candidate ? (string) $candidate->display_name : null],
            ],
            'action' => ['View My Orders', \App\Support\NotificationCenter::partnerSiteUrl($customer && $customer->partner_id ? (int) $customer->partner_id : null) . '/account/orders'],
            'dedupe' => 'cancel:' . $booking->id,
            'context' => $context,
        ]);
        \App\Support\NotificationCenter::queueOrderWhatsapp('customer_order_cancelled', (int) $booking->id, (int) $partner->id, (int) $booking->partner_id);
    }
}
