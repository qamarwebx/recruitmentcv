<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Partner Portal -> Customers. A partner's customers are the users rows with
 * users.partner_id = that partner (set server-side when a customer registers
 * on the partner's subdomain - see App\Support\CustomerSite). Every query
 * here starts from the logged-in partner's id; a customer id that belongs to
 * anyone else always resolves to 404, so changing an id in the URL can never
 * read or modify another partner's customer.
 */
class PartnerCustomersController extends Controller
{
    /** Same country set/order as the site's phone selectors. */
    private const COUNTRY_CODES = ['966', '91', '965', '974', '971'];

    private function partnerId(): int
    {
        return (int) Auth::guard('partner')->id();
    }

    private function findOwned($id): ?User
    {
        return User::where('partner_id', $this->partnerId())->where('id', (int) $id)->first();
    }

    public function index(Request $request)
    {
        $partnerId = $this->partnerId();
        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');

        $query = User::query()
            ->select('users.*')
            ->selectSub(
                Booking::selectRaw('count(*)')
                    ->whereColumn('bookings.user_id', 'users.id')
                    ->where('bookings.partner_id', $partnerId),
                'orders_count'
            )
            ->where('users.partner_id', $partnerId);

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
        ]);
    }

    public function show($id)
    {
        $customer = $this->findOwned($id);
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
        $customer = $this->findOwned($id);
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => __('locale.Customer not found.')], 404);
        }

        $data = $this->validated($request, $customer);
        if ($data instanceof \Illuminate\Http\JsonResponse) {
            return $data;
        }

        $mobileChanged = $customer->mobile_no !== $data['mobile_no'] || (string) $customer->country_code !== $data['country_code'];
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
        $customer = $this->findOwned($id);
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => __('locale.Customer not found.')], 404);
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

        // One account per mobile number per website (see CustomerSite).
        $mobileTaken = User::where('partner_id', $this->partnerId())
            ->where('mobile_no', $request->mobile_no)
            ->when($customer, fn ($q) => $q->where('id', '!=', $customer->id))
            ->exists();

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
