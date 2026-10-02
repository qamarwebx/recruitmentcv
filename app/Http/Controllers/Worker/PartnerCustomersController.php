<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Country;
use App\Models\User;
use App\Support\CustomerSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Partner Portal -> Customers. Customer accounts are global ("one account,
 * all sites" - App\Support\CustomerSite), so a partner sees a customer who:
 *   - registered on this partner's website (users.partner_id = partner)
 *     -> "Registered here": editable (name/company/address; mobile/email
 *        only while the customer hasn't taken the account into use - see
 *        User::isInUseByCustomer()) and unlinkable; or
 *   - has at least one order with this partner (bookings.partner_id =
 *     partner), wherever they registered, incl. legacy customers with no
 *     partner -> "Ordered here": view only.
 * The partner is always the partner guard's own id; every lookup goes
 * through visibleCustomers(), so any other id resolves to 404, and only
 * this partner's orders are ever shown or counted.
 */
class PartnerCustomersController extends Controller
{
    /** Same country set/order as the site's phone selectors. */
    private const COUNTRY_CODES = ['966', '91', '965', '974', '971'];

    private function partnerId(): int
    {
        return (int) Auth::guard('partner')->id();
    }

    /**
     * Customers this partner may see: registered here OR ordered here.
     * One server-side query for the list (search/filter/count/pagination)
     * and every single-customer lookup.
     */
    private function visibleCustomers()
    {
        $partnerId = $this->partnerId();

        return User::query()->where(function ($query) use ($partnerId) {
            $query->where('users.partner_id', $partnerId)
                ->orWhereExists(function ($bookings) use ($partnerId) {
                    $bookings->selectRaw('1')
                        ->from('bookings')
                        ->whereColumn('bookings.user_id', 'users.id')
                        ->where('bookings.partner_id', $partnerId);
                });
        });
    }

    private function findVisible($id): ?User
    {
        return $this->visibleCustomers()->where('users.id', (int) $id)->first();
    }

    /** "Registered here" (editable) vs "Ordered here" (view only). */
    private function registeredHere(User $customer): bool
    {
        return (int) $customer->partner_id === $this->partnerId();
    }

    private function viewOnlyResponse()
    {
        return response()->json([
            'status' => 'error',
            'message' => __('locale.This customer registered on another website. You can view them and your orders only.'),
        ], 403);
    }

    public function index(Request $request)
    {
        $partnerId = $this->partnerId();
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');

        $query = $this->visibleCustomers()
            ->select('users.*')
            ->selectSub(
                Booking::selectRaw('count(*)')
                    ->whereColumn('bookings.user_id', 'users.id')
                    ->where('bookings.partner_id', $partnerId),
                'orders_count'
            );

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('users.mobile_no', 'like', "%{$search}%");
            });
        }

        if ($status === '0' || $status === '1') {
            $query->where('users.status', (int) $status);
        }

        $customers = $query->orderByDesc('users.id')->paginate(15)->withQueryString();

        return view('worker.partner.customers.index', [
            'customers' => $customers,
            'countries' => $this->countryOptions(),
            'partnerId' => $partnerId,
        ]);
    }

    public function show($id)
    {
        $customer = $this->findVisible($id);
        abort_if(!$customer, 404);

        // Only this customer's orders WITH this partner - same joins as the
        // partner Orders page (PartnerPortalController::orders).
        $orders = DB::table('bookings as booking')
            ->leftJoin('candidates as cand', 'booking.cand_id', '=', 'cand.id')
            ->leftJoin('professions as proff', 'cand.jobtype_id', '=', 'proff.id')
            ->leftJoin('order_statuses as ordstatus', 'ordstatus.id', '=', 'booking.ord_status_id')
            ->select(
                'booking.id',
                'booking.reference_no',
                'booking.booking_date',
                'booking.payment_status',
                'booking.booking_status',
                'cand.cand_name',
                'cand.arcand_name',
                'proff.eng_name as profession_eng',
                'proff.ar_name as profession_ar',
                'ordstatus.ord_status',
                'ordstatus.ar_status'
            )
            ->where('booking.partner_id', $this->partnerId())
            ->where('booking.user_id', $customer->id)
            ->orderByDesc('booking.id')
            ->get();

        return view('worker.partner.customers.show', [
            'customer' => $customer,
            'orders' => $orders,
            'countries' => $this->countryOptions(),
            'partnerId' => $this->partnerId(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);
        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }

        $customer = new User();
        $this->fill($customer, $data);
        // Server-side only - never from the request.
        $customer->partner_id = $this->partnerId();
        $customer->status = 1;
        $customer->save();

        return response()->json(['status' => 'success', 'message' => __('locale.Customer saved successfully.')]);
    }

    public function update(Request $request, $id)
    {
        $customer = $this->findVisible($id);
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => __('locale.Customer not found.')], 404);
        }
        if (!$this->registeredHere($customer)) {
            return $this->viewOnlyResponse();
        }

        $data = $this->validated($request, $customer);
        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }

        $mobileChanged = CustomerSite::canonicalMobile($customer->country_code, $customer->mobile_no) !== CustomerSite::canonicalMobile($data['country_code'], $data['mobile_no'])
            || ltrim((string) $customer->country_code, '+') !== $data['country_code'];
        $emailChanged = strcasecmp(trim((string) $customer->email), (string) $data['email']) !== 0;

        // Account takeover guard: once the customer uses the account, its
        // login identifiers are theirs to change (verified flow on
        // /account/profile or /account/security) - refused here whatever
        // the form sent.
        if ($customer->isInUseByCustomer() && ($mobileChanged || $emailChanged)) {
            $message = __('locale.Only the customer can change their mobile number and email, from their own account.');
            return response()->json(['status' => 'error', 'message' => $message, 'errors' => array_fill_keys(
                array_keys(array_filter(['mobile_no' => $mobileChanged, 'email' => $emailChanged])),
                [$message]
            )], 422);
        }

        // Same number/email typed in another form (leading 0, letter case):
        // keep the stored values untouched - customer login matches them.
        if ($customer->isInUseByCustomer()) {
            $data['country_code'] = ltrim((string) $customer->country_code, '+');
            $data['mobile_no'] = $customer->mobile_no;
            $data['email'] = $customer->email;
        }

        $this->fill($customer, $data);
        // A new number hasn't been OTP-verified by the customer.
        if ($mobileChanged) {
            $customer->mobile_verified_at = null;
        }
        $customer->save();

        return response()->json(['status' => 'success', 'message' => __('locale.Customer saved successfully.')]);
    }

    /**
     * "Delete" = unlink only (user's decision): partner_id -> NULL. The users
     * row, its orders and the CRM record are all kept.
     */
    public function unlink($id)
    {
        $customer = $this->findVisible($id);
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => __('locale.Customer not found.')], 404);
        }
        // Only a customer registered here can be unlinked from this partner.
        if (!$this->registeredHere($customer)) {
            return $this->viewOnlyResponse();
        }

        $customer->partner_id = null;
        $customer->save();

        return response()->json(['status' => 'success', 'message' => __('locale.Customer removed from your list.')]);
    }

    /** @return array|\Illuminate\Http\JsonResponse */
    private function validated(Request $request, ?User $customer)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable', 'email', 'max:255',
                // users.email is unique across every site.
                Rule::unique('users', 'email')->ignore($customer?->id),
            ],
            'country_code' => ['required', Rule::in(self::COUNTRY_CODES)],
            'mobile_no' => ['required', 'regex:/^[0-9]{6,15}$/'],
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
        ], [
            'email.unique' => __('locale.An account with this email already exists.'),
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        // One customer account per mobile number across ALL websites (one
        // account, all sites) - the same normalized check registration uses
        // (CustomerSite::registrationConflicts()). Only when the number is
        // new or changed, so an unchanged legacy duplicate never blocks
        // saving the other fields.
        $mobileIsNew = !$customer
            || CustomerSite::canonicalMobile($customer->country_code, $customer->mobile_no) !== CustomerSite::canonicalMobile($request->country_code, $request->mobile_no)
            || ltrim((string) $customer->country_code, '+') !== (string) $request->country_code;
        $mobileTaken = $mobileIsNew
            && isset(CustomerSite::registrationConflicts(null, $request->country_code, $request->mobile_no, $customer?->id)['mobile']);

        if ($mobileTaken) {
            return response()->json([
                'status' => 'error',
                'errors' => ['mobile_no' => [__('locale.An account with this mobile number already exists.')]],
            ], 422);
        }

        return [
            'name' => trim($request->name),
            'email' => trim((string) $request->email) ?: null,
            'country_code' => $request->country_code,
            'mobile_no' => $request->mobile_no,
            'company_name' => $request->company_name,
            'address' => $request->address,
        ];
    }

    private function fill(User $customer, array $data): void
    {
        $customer->name = $data['name'];
        $customer->email = $data['email'];
        $customer->country_code = $data['country_code'];
        $customer->country_id = optional(Country::where('country_code', $data['country_code'])->first())->id;
        $customer->mobile_no = $data['mobile_no'];
        $customer->company_name = $data['company_name'];
        $customer->address = $data['address'];
    }

    private function countryOptions()
    {
        $countries = Country::whereIn('country_code', self::COUNTRY_CODES)->get()->keyBy('country_code');

        return collect(self::COUNTRY_CODES)
            ->filter(fn ($code) => isset($countries[$code]))
            ->mapWithKeys(fn ($code) => [$code => trim($countries[$code]->display_name) . ' +' . $code]);
    }
}
