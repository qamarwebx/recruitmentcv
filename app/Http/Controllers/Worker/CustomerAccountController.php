<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Candidate;
use App\Models\Country;
use App\Models\Wishlist;
use App\Support\CustomerSite;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Customer account pages of the RecruitmentCV site (/account/*, `web`
 * guard, EnsureWorkerCustomerAuthenticated). Built only on existing tables
 * (bookings, wishlists, activities, users) - every query is scoped to the
 * logged-in customer. Order cancel and profile/mobile/email changes reuse the
 * existing endpoints (DashboardController), not a copy of them.
 */
class CustomerAccountController extends Controller
{
    private function customerId(): int
    {
        return (int) Auth::guard('web')->id();
    }

    /**
     * Same joins/columns as the partner Orders page, for this customer -
     * limited to the orders this site may show (CustomerSite::
     * whereOrderVisible(): this partner's + no-website/no-office orders,
     * never another website partner's).
     */
    private function ordersQuery()
    {
        return DB::table('bookings as booking')
            ->leftJoin('candidates as cand', 'booking.cand_id', '=', 'cand.id')
            ->leftJoin('professions as proff', 'cand.jobtype_id', '=', 'proff.id')
            ->leftJoin('order_statuses as ordstatus', 'ordstatus.id', '=', 'booking.ord_status_id')
            ->select(
                'booking.id',
                'booking.partner_id',
                'booking.reference_no',
                'booking.amount',
                'booking.booking_date',
                'booking.payment_status',
                'booking.visa_status',
                'booking.booking_status',
                'booking.updated_at',
                'cand.cand_name',
                'cand.arcand_name',
                'cand.photo_file',
                'cand.slug_text as candidate_slug',
                'proff.eng_name as profession_eng',
                'proff.ar_name as profession_ar',
                'ordstatus.ord_status',
                'ordstatus.ar_status'
            )
            ->where('booking.user_id', $this->customerId())
            ->where(fn ($visible) => CustomerSite::whereOrderVisible($visible, 'booking.partner_id'));
    }

    public function orders()
    {
        $orders = $this->ordersQuery()->orderByDesc('booking.id')->paginate(10);

        return view('worker.account.orders', compact('orders'));
    }

    public function profile()
    {
        $customer = Auth::guard('web')->user();
        $country = $customer->country_id ? Country::find($customer->country_id) : null;

        // The Change Mobile modal's country list comes from the shared
        // App\Support\CountryCodeOptions (same as Partner Login).
        return view('worker.account.profile', compact('customer', 'country'));
    }

    /**
     * Password & Security - read-only, from existing data only. Customers
     * have no password on this site (mobile OTP / Google sign-in), so this
     * shows how they sign in and their last recorded sign-in
     * (users.last_login_from, written by the OTP and Google login flows).
     */
    public function security()
    {
        $customer = Auth::guard('web')->user();
        $lastLogin = json_decode((string) $customer->last_login_from, true);
        $lastLogin = is_array($lastLogin) ? $lastLogin : null;

        return view('worker.account.security', compact('customer', 'lastLogin'));
    }

    public function wishlist()
    {
        $items = DB::table('wishlists as wish')
            ->join('candidates as cand', 'wish.cand_id', '=', 'cand.id')
            ->leftJoin('professions as proff', 'cand.jobtype_id', '=', 'proff.id')
            ->select(
                'wish.id',
                'wish.created_at',
                'cand.cand_name',
                'cand.arcand_name',
                'cand.photo_file',
                'cand.slug_text',
                'cand.age',
                'proff.eng_name as profession_eng',
                'proff.ar_name as profession_ar'
            )
            ->where('wish.user_id', $this->customerId())
            ->where('wish.status', 1)
            ->where('cand.isdelete', 0)
            ->orderByDesc('wish.id')
            ->paginate(12);

        return view('worker.account.wishlist', compact('items'));
    }

    /**
     * Adds/removes a candidate on the logged-in customer's wishlist. The
     * candidate comes from its public slug; user_id/partner_id are always the
     * logged-in customer's own, never from the request.
     */
    public function wishlistToggle(Request $request)
    {
        $candidate = Candidate::where('slug_text', (string) $request->candidate)->where('isdelete', 0)->first();

        if (!$candidate) {
            return response()->json(['status' => 'error', 'message' => __('locale.Something went wrong. Please try again.')], 404);
        }

        $customer = Auth::guard('web')->user();
        $existing = Wishlist::where('user_id', $customer->id)->where('cand_id', $candidate->id)->first();

        if ($existing) {
            $existing->delete();

            return response()->json(['status' => 'success', 'wishlisted' => false]);
        }

        $wish = new Wishlist();
        $wish->user_id = $customer->id;
        // Attribution: the partner website the heart was clicked on (the
        // current site - CustomerSite), not where the customer registered.
        // The wishlist itself stays per customer (user_id) on every site.
        $wish->partner_id = CustomerSite::partnerId();
        $wish->cand_id = $candidate->id;
        $wish->ref_no = $candidate->reference_no;
        $wish->wishlist_date = now();
        $wish->status = 1;
        $wish->save();

        return response()->json(['status' => 'success', 'wishlisted' => true]);
    }

    /**
     * There is no customer notification table - this is a read-only feed of
     * what already exists for this customer: their own booking/cancel events
     * (activities) and the latest state of each of their orders (bookings).
     */
    public function notifications()
    {
        $isAr = app()->getLocale() === 'ar';
        $items = collect();

        $customerId = $this->customerId();
        // Booking/cancel events carry no partner (activities.partner_id is
        // never set for them) - they follow the SAME order visibility rule
        // through their candidate: shown when this customer has a visible
        // order for that candidate, or no order for it at all.
        Activity::where('user_id', $customerId)
            ->where(function ($activity) use ($customerId) {
                $activity->whereExists(function ($order) use ($customerId) {
                    $order->selectRaw('1')->from('bookings as booking')
                        ->whereColumn('booking.cand_id', 'activities.cand_id')
                        ->where('booking.user_id', $customerId)
                        ->where(fn ($visible) => CustomerSite::whereOrderVisible($visible, 'booking.partner_id'));
                })->orWhereNotExists(function ($order) use ($customerId) {
                    $order->selectRaw('1')->from('bookings as booking')
                        ->whereColumn('booking.cand_id', 'activities.cand_id')
                        ->where('booking.user_id', $customerId);
                });
            })
            ->orderByDesc('id')
            ->limit(50)
            ->get(['headline', 'bodyMessage', 'created_at'])
            ->each(function ($activity) use ($items) {
                $items->push([
                    'kind' => 'activity',
                    'title' => $activity->headline,
                    'body' => $activity->bodyMessage,
                    'date' => $activity->created_at,
                    'url' => null,
                ]);
            });

        $this->ordersQuery()->orderByDesc('booking.updated_at')->limit(50)->get()
            ->each(function ($order) use ($items, $isAr) {
                if ((int) $order->booking_status === 2) {
                    $status = __('locale.Cancelled');
                } else {
                    $status = ($isAr && !empty($order->ar_status)) ? $order->ar_status : ($order->ord_status ?: '---');
                }

                $candName = ($isAr && !empty($order->arcand_name)) ? $order->arcand_name : ($order->cand_name ?? '---');

                $items->push([
                    'kind' => 'order',
                    'title' => __('locale.Order') . ' #' . $order->reference_no . ' — ' . $candName,
                    'body' => __('locale.Order Status') . ': ' . $status
                        . ' · ' . __('locale.Payment Status') . ': ' . ($order->payment_status ? __('locale.Paid') : __('locale.Unpaid'))
                        . (CustomerSite::isHandledByTeam($order->partner_id) ? ' · ' . __('locale.Handled by our team') : ''),
                    'date' => $order->updated_at ? Carbon::parse($order->updated_at) : null,
                    'url' => route('worker.account.orders'),
                ]);
            });

        $items = $items
            ->sortByDesc(fn ($item) => $item['date'] ? $item['date']->timestamp : 0)
            ->take(50)
            ->values();

        return view('worker.account.notifications', compact('items'));
    }
}
