<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\BookingController;
use App\Http\Controllers\Controller;
use App\Mail\CustomerBookingConfirmation;
use App\Models\Booking;
use App\Models\Candidate;
use App\Models\Embassy;
use App\Models\Expecworkcity;
use App\Models\Partner;
use App\Models\Websiteconfig;
use App\Support\CustomerMail;
use App\Support\CustomerSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Customer "Hire Now" on the RecruitmentCV site. Not a new order system:
 * after validating, it hands off to the existing BookingController::store4()
 * (booking record, reference number, order-received staff assignment,
 * candidate booking limit, activity timeline and the queued customer/partner
 * notification jobs). What this adds in front of it is everything the legacy
 * flow only checked in the browser - and, most importantly, the partner is
 * decided HERE, never taken from the request:
 *   - on a partner subdomain: always that partner (App\Support\CustomerSite);
 *   - on the main domain: the legacy rule (FrontEndController::fullresumes /
 *     fullresumes.blade.php) - the only portal office if there is exactly
 *     one, else the office already handling this candidate, else the office
 *     the customer picked (validated against the offices offered).
 */
class CustomerHireController extends Controller
{
    /** Offices offered on the main domain (same query as the legacy page). */
    public static function portalOffices()
    {
        return DB::table('partners as partner')
            ->where('partner.portal_status', '=', '1')
            ->orderBy('partner.portal_rec_off_name')
            ->get(['partner.id', 'partner.portal_rec_off_name', 'partner.rec_off_name', 'partner.portal_add_disp_only']);
    }

    /**
     * @return array{0:?int,1:bool} [partnerId, customerMustChoose]
     */
    public static function resolvePartner(Candidate $candidate, $chosenOfficeId = null): array
    {
        $sitePartnerId = CustomerSite::partnerId();
        if ($sitePartnerId !== null) {
            return [$sitePartnerId, false];
        }

        $offices = self::portalOffices();
        if ($offices->count() === 1) {
            return [(int) $offices->first()->id, false];
        }

        $handlingOffice = DB::table('bookings as booking')
            ->join('partners as partner', 'booking.partner_id', '=', 'partner.id')
            ->where('partner.portal_status', '=', '1')
            ->where('booking.booking_status', '!=', 2)
            ->where('booking.cand_id', $candidate->id)
            ->value('booking.partner_id');
        if ($handlingOffice) {
            return [(int) $handlingOffice, false];
        }

        $chosen = (int) $chosenOfficeId;

        return [$offices->contains('id', $chosen) ? $chosen : null, true];
    }

    public function store(Request $request, $slug)
    {
        $customer = Auth::guard('web')->user();

        // Same gate as the legacy Hire button: active account, verified mobile.
        if ((int) $customer->status !== 1 || empty($customer->mobile_verified_at)) {
            return response()->json(['status' => 'mobile_unverified', 'message' => __('locale.Please verify your mobile number first.')], 403);
        }

        $candidate = Candidate::where('slug_text', $slug)->where('isdelete', 0)->first();
        if (!$candidate) {
            return response()->json(['status' => 'error', 'message' => __('locale.Candidate not found.')], 404);
        }

        // reservation_lock: held / selected / full in the CRM reservation queue - never hireable here.
        $isAvailable = (int) $candidate->status === 1 && (int) $candidate->publish === 1 && (int) $candidate->cv_execute === 1 && empty($candidate->reservation_lock);
        if (!$isAvailable) {
            return response()->json(['status' => 'error', 'message' => __('locale.This candidate is no longer available to hire.')], 422);
        }

        $validator = Validator::make($request->all(), [
            'worklocation' => 'required|integer|exists:expecworkcities,id',
            'embassy_id' => 'required|integer|exists:embassies,id',
            'office_id' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        // City eligibility (legacy FrontEndController::getexpectedwork3 rule).
        if (!empty($candidate->expwp_id) && !in_array((string) $request->worklocation, explode(',', $candidate->expwp_id), true)) {
            return response()->json(['status' => 'error', 'message' => __('locale.This candidate is not eligible for the selected city, please try a different candidate.')], 422);
        }

        // Embassy eligibility (legacy FrontEndController::getvisaembassyfor rule).
        if (!empty($candidate->embassy_for) && (string) $candidate->embassy_for !== (string) $request->embassy_id) {
            return response()->json(['status' => 'error', 'message' => __('locale.This candidate is not eligible for the selected embassy.')], 422);
        }

        [$partnerId, $mustChoose] = self::resolvePartner($candidate, $request->office_id);
        if (!$partnerId) {
            return response()->json(['status' => 'error', 'message' => $mustChoose
                ? __('locale.Please choose a recruitment office.')
                : __('locale.No recruitment office is available right now.')], 422);
        }

        $settings = Websiteconfig::first();
        $booking = null;

        // The order-number lock shared with the CRM (App\Support\BookingReference)
        // is held until this transaction - which inserts the order - has committed.
        $response = \App\Support\BookingReference::withLock(function () use ($request, $customer, $candidate, $partnerId, $settings, &$booking) {
            return DB::transaction(function () use ($request, $customer, $candidate, $partnerId, $settings, &$booking) {
            // Lock this customer's live orders while checking the limits, so a
            // double click can't create two.
            $liveOrders = Booking::where('user_id', $customer->id)->where('booking_status', '!=', 2)->lockForUpdate()->get(['id', 'cand_id', 'reference_no']);

            $duplicate = $liveOrders->firstWhere('cand_id', $candidate->id);
            if ($duplicate) {
                return response()->json(['status' => 'duplicate', 'message' => __('locale.You already have an order for this candidate.'), 'reference_no' => $duplicate->reference_no], 409);
            }

            // Legacy per-customer limit ($userbkc < max_booking_limit).
            if ($settings && $liveOrders->count() >= (int) $settings->max_booking_limit) {
                return response()->json(['status' => 'error', 'message' => __('locale.You have reached the maximum number of active orders.')], 422);
            }

            // Existing booking logic, with server-decided values only.
            $inner = Request::create('/resumes/details/booking/4', 'POST', [
                'cand_id4' => $candidate->id,
                'user_id4' => $customer->id,
                'partner_id4' => $partnerId,
                'worklocation4' => (int) $request->worklocation,
                'embassy_for4' => (int) $request->embassy_id,
            ]);
            $inner->setLaravelSession($request->session());
            $inner->setUserResolver(fn () => $customer);
            app(BookingController::class)->store4($inner);

            $booking = Booking::where('user_id', $customer->id)->where('cand_id', $candidate->id)->orderByDesc('id')->first();

            return response()->json([
                'status' => 'success',
                'message' => __('locale.Order placed! Your order number is') . ' ' . $booking->reference_no . '.',
                'reference_no' => $booking->reference_no,
                'orders_url' => route('worker.account.orders'),
            ]);
            });
        });

        // Only for an order this request actually created (a retry/double
        // click gets the 'duplicate' response above and no $booking), and
        // only after the transaction has committed.
        if ($booking) {
            $this->sendConfirmation($booking, $customer, $candidate);
        }

        return $response;
    }

    /**
     * Order confirmation email to the customer, through the booking
     * partner's SMTP (App\Support\CustomerMail). Once per booking, even if
     * this is somehow reached twice for the same order.
     */
    private function sendConfirmation(Booking $booking, $customer, Candidate $candidate): void
    {
        if (!Cache::add('customer-booking-confirmation-mail:' . $booking->id, 1, now()->addDays(7))) {
            return;
        }

        $workCity = Expecworkcity::find($booking->worklocation);
        $embassy = Embassy::find($booking->embassy_id);

        CustomerMail::send((int) $booking->partner_id, $customer->email, new CustomerBookingConfirmation([
            'brand' => CustomerMail::brand((int) $booking->partner_id),
            'customer_name' => $customer->name,
            'candidate_name' => $candidate->display_name,
            'candidate_profession' => $candidate->display_profession_label,
            'candidate_age' => $candidate->age,
            'candidate_url' => route('worker.resume.details', $candidate->slug_text),
            'reference_no' => $booking->reference_no,
            'booking_date' => $booking->created_at ? $booking->created_at->format('d M Y, h:i A') . ' (GMT' . $booking->created_at->format('P') . ')' : null,
            'work_city' => $workCity ? (app()->getLocale() === 'ar' && !empty($workCity->arname) ? $workCity->arname : $workCity->name) : null,
            'embassy' => $embassy ? $embassy->embassy : null,
            'orders_url' => route('worker.account.orders'),
        ]), 'customer_order_created', ['dedupe' => 'booking:' . $booking->id, 'context' => ['booking_id' => $booking->id]]);

        // The partner hears about its new order by email (its WhatsApp is
        // the existing order_to_partner job queued by store4()).
        \App\Support\NotificationCenter::notify('partner_new_customer_order', (int) $booking->partner_id ?: null, [
            'channels' => ['email'],
            'title' => 'New customer order ' . $booking->reference_no,
            'message' => 'A customer placed an order on your RecruitmentCV website.',
            'lines' => [
                ['Order No', (string) $booking->reference_no],
                ['Candidate', (string) $candidate->display_name],
                ['Customer', (string) $customer->name],
                ['Work City', $workCity ? (string) $workCity->name : null],
                ['Embassy', $embassy ? (string) $embassy->embassy : null],
            ],
            'action' => ['View Order', \App\Support\NotificationCenter::partnerSiteUrl((int) $booking->partner_id) . '/partner/orders/' . $booking->id],
            'dedupe' => 'booking:' . $booking->id,
            'context' => ['booking_id' => $booking->id],
        ]);
    }
}
