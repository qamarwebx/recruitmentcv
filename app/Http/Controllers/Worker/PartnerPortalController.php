<?php

namespace App\Http\Controllers\Worker;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Basepathstatus;
use App\Models\Booking;
use App\Models\Candidate;
use App\Models\Carnknown;
use App\Models\City;
use App\Models\Country;
use App\Models\Customercost;
use App\Models\Domain;
use App\Models\Education;
use App\Models\Employercandidate;
use App\Models\Employerplus;
use App\Models\Expecworkcity;
use App\Models\PartnerEmployerSaveFilter;
use App\Models\PartnerPageContent;
use App\Models\Placeofissue;
use App\Models\Profession;
use App\Models\Region;
use App\Models\Religion;
use App\Models\Visadetails;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Worker-hosted Partner Portal, reachable only after partner login (see
 * EnsureWorkerPartnerAuthenticated). Reuses existing scoping conventions
 * already proven in PartnerBookingController/PartnerDashboardController
 * (partner_id column on bookings) rather than duplicating their business
 * logic - these are new, narrower views over the same underlying data,
 * presented with a custom design (not Vuexy/admin).
 *
 * Employerplus is scoped differently: the admin "select a Partner" field
 * on the Add/Edit Employer Plus form (EmployerController::empVisaStore()/
 * empVisaUpdateP(), resources/views/admin/employer/index.blade.php's
 * name="partner_office_id" select) writes to employerpluses.partneroffice_id,
 * NOT employerpluses.partner_id (a separate, always-null-in-practice column
 * on the same table) - so every Employerplus query here filters on
 * partneroffice_id to match what the admin side actually writes.
 */
class PartnerPortalController extends Controller
{
    private const DONE_STATUSES = ['Deployed', 'Cancelled'];

    private function partnerId()
    {
        return Auth::guard('partner')->user()->id;
    }

    private function candidateIdsForPartner($partnerId)
    {
        return DB::table('bookings')
            ->where('partner_id', $partnerId)
            ->distinct()
            ->pluck('cand_id');
    }

    /**
     * Candidates a partner is allowed to browse/hire: the same
     * status/publish/isdelete/cv_execute rule the public Worker site uses
     * (WorkerPageController::availableCandidates()) - i.e. the existing
     * "ready to be shown externally" rule, not the admin's unrestricted
     * isdelete-only view (admin staff need to see unpublished/draft
     * records too; an external partner should not).
     */
    private function availableCandidates()
    {
        return Candidate::where('status', 1)
            ->where('publish', 1)
            ->where('isdelete', 0)
            ->where('cv_execute', 1);
    }

    public function dashboard()
    {
        $partnerId = $this->partnerId();
        $candidateIds = $this->candidateIdsForPartner($partnerId);

        $candidateCount = $candidateIds->count();

        $availableCount = Candidate::whereIn('id', $candidateIds)
            ->whereNotIn('candidate_current_status', self::DONE_STATUSES)
            ->count();

        $employerPlusCount = Employerplus::where('partneroffice_id', $partnerId)->count();

        $pendingBookingsCount = DB::table('bookings')
            ->where('partner_id', $partnerId)
            ->where('booking_status', 0)
            ->count();

        $recentCandidates = Candidate::with('profession:id,eng_name,ar_name')
            ->whereIn('id', $candidateIds)
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        $recentEmployerPlus = Employerplus::where('partneroffice_id', $partnerId)
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        $recentActivity = DB::table('bookings as booking')
            ->leftJoin('candidates as cand', 'booking.cand_id', '=', 'cand.id')
            ->select('booking.id', 'booking.reference_no', 'booking.booking_date', 'booking.booking_status', 'booking.created_at', 'cand.cand_name', 'cand.arcand_name', 'cand.photo_file')
            ->where('booking.partner_id', $partnerId)
            ->orderByDesc('booking.id')
            ->limit(6)
            ->get();

        $notifications = DB::table('bookings as booking')
            ->leftJoin('candidates as cand', 'booking.cand_id', '=', 'cand.id')
            ->select('booking.id', 'booking.reference_no', 'cand.cand_name', 'cand.arcand_name')
            ->where('booking.partner_id', $partnerId)
            ->where('booking.booking_status', 0)
            ->orderByDesc('booking.id')
            ->limit(5)
            ->get();

        return view('worker.partner.dashboard', compact(
            'candidateCount',
            'availableCount',
            'employerPlusCount',
            'pendingBookingsCount',
            'recentCandidates',
            'recentEmployerPlus',
            'recentActivity',
            'notifications'
        ));
    }

    /**
     * Read-only view over the partner's orders (bookings). Mirrors the
     * query shape of PartnerBookingController::index() (the existing
     * order-management flow on the qamarhire.com domain) - same table
     * joins/fields, same `partner_id` scoping - but deliberately does not
     * port the confirm/visa/payment/cancel actions from that controller:
     * those mutate booking state and trigger WhatsApp/email notifications,
     * and continue to live exclusively on the existing
     * qamarhire.com/partner/booking pages.
     *
     * Unlike that controller's default listing, this does NOT filter to
     * `status = 0` (its "active/not archived" flag) - this is a read-only
     * history view, so cancelled/completed orders stay visible too rather
     * than disappearing from the partner's own order list.
     */
    public function orders(Request $request)
    {
        $partnerId = $this->partnerId();

        $query = DB::table('bookings as booking')
            ->leftJoin('candidates as cand', 'booking.cand_id', '=', 'cand.id')
            ->leftJoin('professions as proff', 'cand.jobtype_id', '=', 'proff.id')
            ->leftJoin('order_statuses as ordstatus', 'ordstatus.id', '=', 'booking.ord_status_id')
            ->leftJoin('visadetails as visa', 'booking.id', '=', 'visa.booking_id')
            ->select(
                'booking.id',
                'booking.reference_no',
                'booking.amount',
                'booking.booking_date',
                'booking.payment_status',
                'booking.visa_status',
                'booking.booking_status',
                'booking.ord_status_id',
                'cand.cand_name',
                'cand.arcand_name',
                'cand.photo_file',
                'cand.slug_text as candidate_slug',
                'cand.exp_sal',
                'cand.cv_execute',
                'cand.cv_execute_file',
                'proff.eng_name as profession_eng',
                'proff.ar_name as profession_ar',
                'ordstatus.ord_status',
                'ordstatus.ar_status',
                'visa.visa_no',
                'visa.id_no',
                'visa.proff_id as visa_proff_id',
                'visa.employer_name',
                'visa.employer_ar_name',
                'visa.issuing_authority',
                'visa.wpcity_id',
                'visa.salary',
                'visa.mobile_no'
            )
            ->where('booking.partner_id', $partnerId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking.reference_no', 'like', "%{$search}%")
                    ->orWhere('cand.cand_name', 'like', "%{$search}%")
                    ->orWhere('cand.arcand_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('order_status')) {
            $query->where('booking.ord_status_id', $request->order_status);
        }

        if ($request->filled('payment_status')) {
            $query->where('booking.payment_status', $request->payment_status);
        }

        if ($request->filled('profession_id')) {
            $query->where('cand.jobtype_id', $request->profession_id);
        }

        if ($request->filled('location_id')) {
            $query->where('booking.worklocation', $request->location_id);
        }

        // Same "MM/DD/YYYY - MM/DD/YYYY" single-field format and split-on-
        // ' - ' parsing as LeadController's daterangepicker fields, so the
        // Orders date-range picker (matched to the Leads module as the
        // reference implementation) round-trips through the same shape.
        if ($request->filled('date_range') && str_contains($request->date_range, ' - ')) {
            [$dateFrom, $dateTo] = array_map('trim', explode(' - ', $request->date_range, 2));
            if ($dateFrom && $dateTo) {
                $query->whereBetween('booking.booking_date', [
                    \Illuminate\Support\Carbon::parse($dateFrom)->startOfDay(),
                    \Illuminate\Support\Carbon::parse($dateTo)->endOfDay(),
                ]);
            }
        }

        $orders = $query->orderByDesc('booking.id')->paginate(10)->withQueryString();

        $orderStatuses = \App\Models\OrderStatus::orderBy('id')->get();
        $professionOptions = Profession::orderBy('eng_name')->get();
        $locationOptions = Expecworkcity::whereExists(function ($q) use ($partnerId) {
                $q->select(DB::raw(1))
                    ->from('bookings')
                    ->whereColumn('bookings.worklocation', 'expecworkcities.id')
                    ->where('bookings.partner_id', $partnerId);
            })
            ->orderBy('name')
            ->get();

        // Unlike $locationOptions above (scoped to cities a booking already
        // has, for the filter dropdown), the Add Visa modal's own City of
        // Work field needs the full list to pick from - same source
        // (Expecworkcity) the Employer Plus Add/Edit forms already use.
        $allWorkCities = Expecworkcity::orderBy('name')->get();

        if ($request->ajax()) {
            return view('worker.partner.orders.partial', compact('orders'));
        }

        return view('worker.partner.orders.index', compact(
            'orders',
            'orderStatuses',
            'professionOptions',
            'locationOptions',
            'allWorkCities'
        ));
    }

    public function orderShow($id)
    {
        $partner = Auth::guard('partner')->user();
        $partnerId = $partner->id;

        $order = DB::table('bookings as booking')
            ->leftJoin('candidates as cand', 'booking.cand_id', '=', 'cand.id')
            ->leftJoin('professions as proff', 'cand.jobtype_id', '=', 'proff.id')
            ->leftJoin('order_statuses as ordstatus', 'ordstatus.id', '=', 'booking.ord_status_id')
            ->leftJoin('expecworkcities as expwork', 'booking.worklocation', '=', 'expwork.id')
            ->leftJoin('visadetails as visa', 'booking.id', '=', 'visa.booking_id')
            ->select(
                'booking.*',
                'cand.cand_name',
                'cand.arcand_name',
                'cand.photo_file',
                'cand.reference_no as candidate_ref',
                'cand.exp_sal',
                'proff.eng_name as profession_eng',
                'proff.ar_name as profession_ar',
                'ordstatus.ord_status',
                'ordstatus.ar_status',
                'expwork.name as worklocation_eng',
                'expwork.arname as worklocation_ar',
                'visa.visa_no',
                'visa.id_no',
                'visa.proff_id as visa_proff_id',
                'visa.employer_name',
                'visa.employer_ar_name',
                'visa.issuing_authority',
                'visa.wpcity_id',
                'visa.salary',
                'visa.mobile_no'
            )
            ->where('booking.partner_id', $partnerId)
            ->where('booking.id', $id)
            ->first();

        abort_if(!$order, 404);

        $professionOptions = Profession::orderBy('eng_name')->get();
        $allWorkCities = Expecworkcity::orderBy('name')->get();

        return view('worker.partner.orders.show', compact('order', 'partner', 'professionOptions', 'allWorkCities'));
    }

    /**
     * Mirrors the CRM admin's Booking "Add Visa Details" flow
     * (BookingController@visaStr, resources/views/admin/booking/
     * index.blade.php's #addvisadetail offcanvas) and the legacy partner
     * portal's own already-partner-scoped equivalent
     * (PartnerBookingController@visaStr): one Visadetails row per booking,
     * updated in place if it already exists rather than duplicated, and
     * the same Booking status cascade (visa_status true, plus
     * booking_status/status true once payment is also settled) and
     * Activity timeline entry on save.
     *
     * Unlike either reference implementation, the booking is verified to
     * belong to the authenticated partner before anything is written -
     * neither admin's nor the legacy partner controller's visaStr()
     * checks that the posted booking_id is actually one that requester
     * may act on. salary is also a real, validated, submitted field here
     * instead of the admin form's `disabled` (and therefore never actually
     * submitted) input - it's still pre-filled from the candidate's own
     * expected salary as a convenience default (see order-visa.js), just
     * left editable and functional rather than silently inert.
     */
    public function orderVisaStore(Request $request, $id)
    {
        $partnerId = $this->partnerId();

        $booking = Booking::where('partner_id', $partnerId)->where('id', $id)->first();

        abort_if(!$booking, 404);

        $validator = Validator::make($request->all(), [
            'employer_name' => 'required|string|max:150',
            'employer_ar_name' => 'nullable|string|max:150',
            'visa_no' => 'required|string|max:25',
            'id_no' => 'nullable|string|max:25',
            'proff_id' => 'required|integer|exists:professions,id',
            'issuing_authority' => 'required|in:Mumbai,New Delhi',
            'wpcity_id' => 'required|integer|exists:expecworkcities,id',
            'salary' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Visadetails declares neither $fillable nor $guarded, so any
        // array-based mass assignment (firstOrNew() included) would throw
        // a MassAssignmentException - built up via direct property
        // assignment instead, which isn't subject to that guard.
        $visa = Visadetails::where('booking_id', $booking->id)->first();
        $isNew = !$visa;
        if ($isNew) {
            $visa = new Visadetails();
            $visa->booking_id = $booking->id;
        }

        $visa->user_id = $booking->user_id;
        $visa->cand_id = $booking->cand_id;
        $visa->visa_no = $request->visa_no;
        $visa->id_no = $request->id_no;
        $visa->proff_id = $request->proff_id;
        $visa->employer_name = $request->employer_name;
        $visa->employer_ar_name = $request->employer_ar_name;
        $visa->issuing_authority = $request->issuing_authority;
        $visa->wpcity_id = $request->wpcity_id;
        $visa->salary = $request->salary;
        $visa->status = true;
        if ($isNew) {
            $visa->partner_id = $partnerId;
        }
        $visa->save();

        $booking->visa_status = true;
        if ($booking->payment_status == 1) {
            $booking->booking_status = 1;
            $booking->status = true;
        }
        $booking->save();

        $partner = Auth::guard('partner')->user();
        $timeline = new Activity();
        $timeline->cand_id = $booking->cand_id;
        $timeline->headline = 'Candidate booking visa detail updated!';
        $timeline->bodyMessage = 'Candidate booking visa detail updated by ' . ($partner->owner_name ?: $partner->rec_off_name) . ' on ' . date('d-m-Y h:i A');
        $timeline->icons = 'ti ti-file-check';
        $timeline->partner_id = $partnerId;
        $timeline->save();

        return redirect()->back()->with('success', __('locale.Visa details saved successfully.'));
    }

    public function candidates(Request $request)
    {
        $query = $this->availableCandidates()->with('profession:id,eng_name,ar_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('cand_name', 'like', "%{$search}%")
                    ->orWhere('reference_no', 'like', "%{$search}%")
                    ->orWhere('pass_no', 'like', "%{$search}%");
            });
        }

        if ($request->filled('profession_id')) {
            $query->where('jobtype_id', $request->profession_id);
        }

        if ($request->filled('location_id')) {
            $query->whereRaw('FIND_IN_SET(?, expwp_id)', [$request->location_id]);
        }

        if ($request->filled('expcity_id')) {
            $query->where('gulfexperience', $request->expcity_id);
        }

        if ($request->filled('age')) {
            [$minAge, $maxAge] = array_pad(explode('-', $request->age), 2, null);
            if ($minAge && $maxAge) {
                $query->whereBetween('age', [$minAge, $maxAge]);
            }
        }

        $candidates = $query->orderByDesc('id')->paginate(12)->withQueryString();

        // Partner-scoped "already hired" lookup for Hired badge / View Order
        // vs Hire Now - one query for the whole page (keyed by cand_id), not
        // one per candidate, to avoid N+1. Same "still live" convention as
        // candidateShow()'s $existingBooking: excludes cancelled bookings
        // (booking_status = 2) so a partner can hire again after cancelling,
        // and is scoped to partner_id so another partner's booking for the
        // same candidate never shows as hired here.
        $partnerBookings = DB::table('bookings')
            ->where('partner_id', $this->partnerId())
            ->where('booking_status', '!=', 2)
            ->whereIn('cand_id', $candidates->pluck('id'))
            ->get()
            ->keyBy('cand_id');

        $professionOptions = Profession::orderBy('eng_name')->get();

        $locationOptions = Expecworkcity::whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('candidates')
                    ->whereRaw('FIND_IN_SET(expecworkcities.id, candidates.expwp_id)')
                    ->where('status', 1)->where('publish', 1)->where('isdelete', 0)->where('cv_execute', 1);
            })
            ->orderBy('name')
            ->get();

        // Powers the searchable Select2 "Candidate" filter - same
        // unfiltered/all-available pool professionOptions/locationOptions
        // above are built from, not the current (possibly already
        // filtered) $candidates result. Options carry cand_name (English)
        // as their value since that's exactly what the search clause above
        // matches against - submitting arcand_name for an Arabic-locale
        // label would silently fail to match on the backend.
        $candidateOptions = $this->availableCandidates()
            ->orderBy('cand_name')
            ->get(['id', 'cand_name', 'arcand_name', 'reference_no']);

        return view('worker.partner.candidates.index', compact(
            'candidates',
            'professionOptions',
            'locationOptions',
            'candidateOptions',
            'partnerBookings'
        ));
    }

    public function candidateShow($id)
    {
        $partnerId = $this->partnerId();

        // Marketplace browsing only shows "available" candidates, but a
        // partner should still be able to open a candidate from their own
        // booking history (e.g. Dashboard > Recent Candidates) even if that
        // candidate has since been deployed/unpublished.
        $post = Candidate::with('profession:id,eng_name,ar_name')
            ->where('slug_text', $id)
            ->where('isdelete', 0)
            ->first();

        abort_if(!$post, 404);

        // Excludes cancelled bookings (booking_status = 2), matching the
        // same "still live" convention the existing customer-facing flow
        // uses ($checkCand/$candBkc in FrontEndController::fullresumes()) -
        // a cancelled order shouldn't block a partner from hiring again.
        $existingBooking = DB::table('bookings')
            ->where('partner_id', $partnerId)
            ->where('cand_id', $post->id)
            ->where('booking_status', '!=', 2)
            ->first();
        $hasBooking = (bool) $existingBooking;

        $isAvailable = (int) $post->status === 1 && (int) $post->publish === 1 && (int) $post->cv_execute === 1;

        abort_if(!$isAvailable && !$hasBooking, 404);

        $totalExperience = $post->experience ? array_sum(array_filter(explode(',', $post->experience), 'is_numeric')) : 0;
        $nation = Country::find($post->nation_id);

        // Same computation as WorkerPageController::resumeDetails() - feeds
        // the image/document gallery's YouTube-thumbnail slides, reusing that
        // page's own worker.resumes.details gallery partial rather than a
        // second copy of this slide-building logic.
        $videoId = $post->video_link ? explode('/', $post->video_link) : null;
        $testVideoId = $post->trade_test_video_link ? explode('/', $post->trade_test_video_link) : null;

        // Same source the existing "Choose Your Nearest Recruitment Office"
        // flow uses for its own worklocation dropdown (FrontEndController::
        // fullresumes()'s $expwpf) - the candidate's own preferred work
        // locations, not the full city list. Matches
        // FrontEndController::getexpectedwork3()'s own rule: a candidate
        // with no preference set (expwp_id empty) is eligible for ANY
        // city, not zero - explode(',', '') would otherwise produce [''],
        // and whereIn('id', ['']) matches nothing.
        $hireWorkLocations = empty($post->expwp_id)
            ? Expecworkcity::orderBy('name')->get()
            : Expecworkcity::whereIn('id', explode(',', (string) $post->expwp_id))->get();

        // Everything below mirrors WorkerPageController::resumeDetails()
        // field-for-field (same models, same lookups) so this page's right
        // column shows exactly what the public resumes/details/{id}) page
        // does - not a second, divergent data-loading path. $expectedWorkPlaces
        // is deliberately its own query (not reused from $hireWorkLocations
        // above): that one falls back to every city when the candidate has
        // no preference, which is correct for a picker but wrong for a
        // read-only "Preferred Work Location" display (which should read
        // "Anywhere" instead) - see resumeDetails()'s identical query.
        $religion = Religion::find($post->religion_id);
        $region = Region::find($post->region_id);
        $expectedWorkPlaces = Expecworkcity::whereIn('id', explode(',', (string) $post->expwp_id))->get();

        $placeOfIssue = Placeofissue::find($post->poi);
        $vehiclesKnown = Carnknown::whereIn('id', explode(',', (string) $post->carknown_id))->get();
        $education = Education::find($post->education_id);
        $vehicleTransmissions = DB::table('vehical_transmission')
            ->whereIn('id', explode(',', (string) $post->vehical_transmission))
            ->get();

        $bookingRequirements = DB::table('requirement_info')->where('language_type', 1)->get();

        $serviceCost = Customercost::where('proff_id', $post->jobtype_id)
            ->where('exp_type', $post->gulfexperience)
            ->where('status', 1)
            ->first();

        $priceLabel = __('locale.On Request');
        $departureLabel = '---';
        if ($serviceCost) {
            $priceLabel = round($serviceCost->cost, 0) == 0 ? __('locale.Free') : round($serviceCost->cost, 0) . ' SAR';
            $departureLabel = $serviceCost->days . ' ' . __('locale.Days');
        }

        return view('worker.partner.candidates.show', compact(
            'post',
            'hasBooking',
            'existingBooking',
            'totalExperience',
            'nation',
            'videoId',
            'testVideoId',
            'hireWorkLocations',
            'religion',
            'region',
            'expectedWorkPlaces',
            'placeOfIssue',
            'vehiclesKnown',
            'education',
            'vehicleTransmissions',
            'bookingRequirements',
            'priceLabel',
            'departureLabel'
        ));
    }

    public function employerPlus(Request $request)
    {
        $partnerId = $this->partnerId();

        // A bare visit (no "filtered" marker - a hidden input present on
        // every real Search/Apply Filter/Reset Filter/pagination
        // submission, see filter-modal.blade.php) falls back to the
        // partner's saved filter, if any - mirrors the admin's own
        // Employer Plus listing, which always merges Employeradminsavefilter
        // the same way (EmployerController@indexp), except scoped so an
        // explicit Reset Filter (which also submits "filtered") is never
        // silently overridden by a stale saved filter.
        $savedFilter = $request->has('filtered')
            ? null
            : PartnerEmployerSaveFilter::where('partner_id', $partnerId)->first();

        $filters = $request->has('filtered')
            ? [
                'status' => $request->status,
                'profession' => (array) $request->profession,
                'wpcity_id' => (array) $request->wpcity_id,
                'businesstype' => (array) $request->businesstype,
                'wakala_status' => (array) $request->wakala_status,
                'payment_status' => (array) $request->payment_status,
                'created_date' => $request->created_date,
            ]
            : [
                'status' => optional($savedFilter)->status,
                'profession' => $this->explodeSaved(optional($savedFilter)->profession),
                'wpcity_id' => $this->explodeSaved(optional($savedFilter)->wpcity_id),
                'businesstype' => $this->explodeSaved(optional($savedFilter)->businesstype),
                'wakala_status' => $this->explodeSaved(optional($savedFilter)->wakala_status),
                'payment_status' => $this->explodeSaved(optional($savedFilter)->payment_status),
                'created_date' => optional($savedFilter)->created_date,
            ];

        // Every filter below reuses a scope already defined on Employerplus
        // for the admin's own Employer Plus listing (see
        // app/Models/Employerplus.php) - chained onto a query that is
        // always scoped to the logged-in partner FIRST, so no filter can
        // ever widen the result set past that partner's own records.
        $query = Employerplus::where('partneroffice_id', $partnerId)
            ->filterSearchTextP($request->search)
            ->filterStatusP($filters['status'] !== null && $filters['status'] !== '' ? (int) $filters['status'] : null)
            ->filterProfessionP($this->intArray($filters['profession']))
            ->filterCityOfWorkP($this->intArray($filters['wpcity_id']))
            ->filterBusinessTypeP($filters['businesstype'])
            ->filterWakalaStatusP($filters['wakala_status'])
            ->filterPaymentStatusP($filters['payment_status'])
            ->filterDateRangeP('created_at', $filters['created_date']);

        $activeFilterCount = collect([
            $filters['status'] !== null && $filters['status'] !== '',
            !empty($filters['profession']),
            !empty($filters['wpcity_id']),
            !empty($filters['businesstype']),
            !empty($filters['wakala_status']),
            !empty($filters['payment_status']),
            !empty($filters['created_date']),
        ])->filter()->count();

        $employers = $query->orderByDesc('id')->paginate(9)->withQueryString();

        // Batch-resolve every profession referenced on this page in one
        // query instead of the view calling Profession::find() per row
        // (proff_id can itself be a comma-separated list of ids, e.g. "1,4").
        $professionIds = $employers->getCollection()
            ->pluck('proff_id')
            ->filter()
            ->flatMap(fn ($ids) => explode(',', $ids))
            ->filter()
            ->unique();

        $professionsById = Profession::whereIn('id', $professionIds)->get()->keyBy('id');

        // Full profession list (not just referenced ones) for the Add/Edit
        // modal pickers - same source the admin Add/Edit form uses.
        $professionOptions = Profession::orderBy('eng_name')->get();

        // City of Work options for the Add Employer modal - same source
        // (Expecworkcity) as the admin's own Add Employer form.
        $workCities = Expecworkcity::orderBy('name')->get();

        return view('worker.partner.employer-plus.index', compact(
            'employers',
            'professionsById',
            'professionOptions',
            'workCities',
            'activeFilterCount',
            'filters'
        ));
    }

    /**
     * Turns a saved filter's comma-joined string column back into an array,
     * the shape a multi-select's request field would already be in.
     */
    private function explodeSaved($csv)
    {
        return $csv ? explode(',', $csv) : [];
    }

    /**
     * AJAX-persists the Employer Filter modal's current selections as this
     * partner's sticky default filter - mirrors the admin's own Save Filter
     * action (EmployerController@savefilterP / Employerplusadminsavefilter)
     * but scoped to the authenticated partner rather than trusting a
     * posted admin/user id, and using one update-or-create row per partner
     * (same pattern as ClientAdminSaveFilter) instead of the admin's
     * separate per-module tables.
     */
    public function employerFilterSave(Request $request)
    {
        $partnerId = $this->partnerId();

        PartnerEmployerSaveFilter::updateOrCreate(
            ['partner_id' => $partnerId],
            [
                'status' => $request->status,
                'profession' => implode(',', (array) $request->profession),
                'wpcity_id' => implode(',', (array) $request->wpcity_id),
                'businesstype' => implode(',', (array) $request->businesstype),
                'wakala_status' => implode(',', (array) $request->wakala_status),
                'payment_status' => implode(',', (array) $request->payment_status),
                'created_date' => $request->created_date,
            ]
        );

        return response()->json(['status' => 'success', 'message' => __('locale.Filter saved successfully.')]);
    }

    /**
     * Blanks out (rather than deletes) the partner's saved filter row -
     * matches the admin's own Reset Filter behavior (EmployerController@
     * resetEmpPFilter), which clears every field on the existing row
     * instead of removing it.
     */
    public function employerFilterReset(Request $request)
    {
        $partnerId = $this->partnerId();

        $saved = PartnerEmployerSaveFilter::where('partner_id', $partnerId)->first();
        if ($saved) {
            $saved->update([
                'status' => null,
                'profession' => null,
                'wpcity_id' => null,
                'businesstype' => null,
                'wakala_status' => null,
                'payment_status' => null,
                'created_date' => null,
            ]);
        }

        return response()->json(['status' => 'success', 'message' => __('locale.Filters reset successfully.')]);
    }

    /**
     * Sanitizes a GET filter's raw input into a clean array of ints, e.g.
     * Select2 multi-selects submit profession/city ids as an array of
     * numeric-looking strings. Used only for read-side filtering, so bad
     * input is simply dropped (yielding no match) rather than rejected the
     * way a mutating request's Validator would.
     */
    private function intArray($value)
    {
        return collect((array) $value)
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();
    }

    public function employerPlusShow($id)
    {
        $partnerId = $this->partnerId();

        // Scoped by partneroffice_id (not just id) so a partner can never
        // load another partner's Employer Plus record by guessing/changing
        // the URL - a mismatch resolves to nothing, not someone else's row.
        $employer = Employerplus::where('partneroffice_id', $partnerId)
            ->where('id', $id)
            ->first();

        abort_if(!$employer, 404);

        // proff_id/openings/salary are parallel comma-separated lists (same
        // index across all three, written by employerStore()/employerUpdate())
        // - one entry per Profession/Openings/Monthly Salary row added via
        // the Add/Edit Employer modal's repeatable rows (see
        // employer-plus/_visa-rows.blade.php). This page was previously
        // doing Profession::find($employer->proff_id), which silently fails
        // against a multi-id string like "12,15" and only ever shows raw,
        // unpaired openings/salary strings. Split + batch-resolve instead,
        // mirroring employerPlus()'s own $professionsById pattern (one
        // query for every profession this employer references, not
        // Profession::find() per row).
        $professionIds = collect(explode(',', (string) $employer->proff_id))->filter()->values();
        $openings = collect(explode(',', (string) $employer->openings))->values();
        $salaries = collect(explode(',', (string) $employer->salary))->values();

        $professionsById = Profession::whereIn('id', $professionIds)->get()->keyBy('id');

        $visaRows = $professionIds->map(function ($proffId, $i) use ($professionsById, $openings, $salaries) {
            $openingsValue = trim((string) $openings->get($i, ''));
            $salaryValue = trim((string) $salaries->get($i, ''));

            return [
                'profession' => optional($professionsById->get($proffId))->display_name ?: '---',
                'openings' => $openingsValue !== '' ? $openingsValue : '---',
                'salary' => $salaryValue !== '' ? $salaryValue : '---',
            ];
        })->values();

        // Assigned Candidates section - the same active-assignment rows
        // (status=1) the CRM's own admin/employer-listp/add-visa-view/{id}
        // page lists as $candempLists (EmployerController::VisaDetViewp()),
        // scoped to this already-ownership-verified Employer. candidate()/
        // proff() are the existing Employercandidate relationships, not a
        // new query pattern.
        $assignedCandidates = Employercandidate::where('emp_id', $employer->id)
            ->where('status', 1)
            ->with(['candidate', 'proff'])
            ->latest('id')
            ->get();

        return view('worker.partner.employer-plus.show', compact('employer', 'visaRows', 'assignedCandidates'));
    }

    /**
     * Mirrors the admin's EmployerController@empVisaStore field set and
     * repeatable proff_id[]/openings[] visa-row shape (resources/views/
     * admin/employer/index.blade.php's "Add Employer" offcanvas), but the
     * Partner and Careoff selects are dropped entirely - both are
     * internal/admin-only assignment fields there, and here
     * partneroffice_id is always the logged-in partner, never a request
     * input, so a partner can never create an Employer under another
     * partner's account. Also adds real validation, which the admin
     * version lacks.
     *
     * businesstype/wakala_status/visa_received_date are intentionally not
     * collected here (removed from the Add/Edit forms per partner
     * request) - existing values on a record are simply left untouched by
     * not being assigned, not wiped. salary is a third parallel array
     * alongside proff_id/openings (implode(',', ...), same index) instead
     * of a single value, since a multi-profession Employer can reasonably
     * pay a different salary per profession.
     */
    public function employerStore(Request $request)
    {
        $partnerId = $this->partnerId();

        $validator = Validator::make($request->all(), $this->employerValidationRules());

        if ($validator->fails()) {
            return redirect()->route('worker.partner.employer')
                ->withErrors($validator)
                ->withInput()
                ->with('add_employer_open', true);
        }

        $employer = new Employerplus();
        $employer->visa_no = $request->visa_no;
        $employer->id_no = $request->id_no;
        $employer->proff_id = implode(',', $request->proff_id);
        $employer->openings = implode(',', $request->openings);
        $employer->salary = implode(',', (array) $request->salary);
        $employer->employer_name = $request->employer_name;
        $employer->employer_ar_name = $request->employer_ar_name;
        $employer->issuing_authority = $request->issuing_authority;
        $employer->visa_date = $request->visa_date;
        $employer->wpcity_id = $this->resolveWorkCityId($request);
        $employer->partneroffice_id = $partnerId;
        $employer->notes = $request->notes;
        $employer->save();

        return redirect()->route('worker.partner.employer')->with('success', __('locale.Employer added successfully.'));
    }

    /**
     * Shared by employerStore()/employerUpdate() - wpcity_id is either a
     * real Expecworkcity id (normal dropdown selection) or the literal
     * "other" sentinel the Add/Edit Employer forms' City of Work select
     * uses for its extra "Other" option; custom_city_name is only
     * present/required in that second case (see employerValidationRules()).
     */
    private function employerValidationRules(): array
    {
        return [
            'employer_name' => 'nullable|string|max:150',
            'employer_ar_name' => 'required|string|max:150',
            'visa_no' => 'required|string|max:25',
            'id_no' => 'required|string|max:25',
            'visa_date' => 'nullable|date',
            'issuing_authority' => 'required|in:Mumbai,New Delhi',
            'proff_id' => 'required|array|min:1',
            'proff_id.*' => 'integer|exists:professions,id',
            'openings' => 'required|array|min:1',
            'openings.*' => 'required|string|max:50',
            'salary' => 'nullable|array',
            'salary.*' => 'nullable|string|max:50',
            'wpcity_id' => ['required', function ($attribute, $value, $fail) {
                if ($value === 'other') {
                    return;
                }
                if (!ctype_digit((string) $value) || !Expecworkcity::whereKey($value)->exists()) {
                    $fail(__('locale.The selected city of work is invalid.'));
                }
            }],
            'custom_city_name' => 'required_if:wpcity_id,other|nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Resolves the wpcity_id to actually save: the submitted value as-is
     * for a normal dropdown selection, or - when the "Other" option was
     * chosen - the id of a matching Expecworkcity found or created from
     * custom_city_name (Expecworkcity::findOrCreateByName(), case-
     * insensitive/trimmed, never a duplicate). admin_id on a
     * newly-created row is the logged-in partner's own managing admin
     * (Partner::admin_id, the same "which staff member owns this
     * partner" column PartnerController::store() already sets from the
     * admin side) - there's no admin session here to pull it from, and
     * this reuses existing data instead of inventing a sentinel value.
     */
    private function resolveWorkCityId(Request $request): int
    {
        if ((string) $request->wpcity_id !== 'other') {
            return (int) $request->wpcity_id;
        }

        $partner = Auth::guard('partner')->user();

        $city = Expecworkcity::findOrCreateByName($request->custom_city_name, (int) ($partner->admin_id ?? 0));

        return $city->id;
    }

    /**
     * Mirrors the admin's EmployerController@empVisaUpdateP field set - now
     * the same one employerStore() uses, since the admin's own Edit
     * offcanvas mirrors its Add offcanvas field-for-field - plus Status,
     * which the admin toggles separately (empStatusUpdatep) rather than
     * from its Edit form. Adds the ownership scoping the admin version has
     * no equivalent of, and validates rather than blindly trusting the
     * request.
     *
     * businesstype/wakala_status/visa_received_date are intentionally not
     * collected here (removed from the Add/Edit forms per partner
     * request) - existing values on a record are simply left untouched by
     * not being assigned, not wiped. salary is a third parallel array
     * alongside proff_id/openings (implode(',', ...), same index) instead
     * of a single value, since a multi-profession Employer can reasonably
     * pay a different salary per profession.
     */
    public function employerUpdate(Request $request, $id)
    {
        $partnerId = $this->partnerId();

        $employer = Employerplus::where('partneroffice_id', $partnerId)->where('id', $id)->first();

        abort_if(!$employer, 404);

        $validator = Validator::make($request->all(), $this->employerValidationRules());

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employer->employer_name = $request->employer_name;
        $employer->employer_ar_name = $request->employer_ar_name;
        $employer->visa_no = $request->visa_no;
        $employer->id_no = $request->id_no;
        $employer->visa_date = $request->visa_date;
        $employer->proff_id = implode(',', $request->proff_id);
        $employer->openings = implode(',', $request->openings);
        $employer->salary = implode(',', (array) $request->salary);
        $employer->issuing_authority = $request->issuing_authority;
        $employer->wpcity_id = $this->resolveWorkCityId($request);
        $employer->status = $request->boolean('status');
        $employer->notes = $request->notes;
        $employer->save();

        return redirect()->back()->with('success', __('locale.Employer updated successfully.'));
    }

    /**
     * Mirrors the admin's EmployerController@deleteEmployerp cascade
     * (re-publish any candidates assigned via employercandidates, then
     * remove those join rows before deleting the Employerplus record
     * itself) but, unlike the admin version, actually scopes to the
     * requesting partner's own records and runs inside a transaction.
     */
    public function employerDelete($id)
    {
        $partnerId = $this->partnerId();

        $employer = Employerplus::where('partneroffice_id', $partnerId)->where('id', $id)->first();

        if (!$employer) {
            return response()->json(['status' => 'error', 'message' => __('locale.Employer not found.')], 404);
        }

        DB::transaction(function () use ($employer) {
            Employercandidate::where('emp_id', $employer->id)->get()->each(function ($assignment) {
                $candidate = Candidate::find($assignment->cand_id);
                if ($candidate) {
                    $candidate->status = true;
                    $candidate->save();
                }
                $assignment->delete();
            });

            $employer->delete();
        });

        return response()->json(['status' => 'success', 'message' => __('locale.Employer deleted successfully.')]);
    }

    /**
     * "Assign Candidate" modal's candidate table. Eligibility is the same
     * rule the CRM's own Add/Assign Candidate dropdown uses (Candidate::
     * where('status', 1) - see EmployerController::VisaDetViewp() and
     * Helper::assignCandidateToEmployer(), which flips status back to
     * false the moment a candidate is assigned and deassigngetdata()
     * flips it back to true on release), narrowed further to only the
     * professions this specific Employer actually needs (explode on
     * $employer->proff_id, the same CSV format Helper::
     * assignCandidateToEmployer() itself parses) so a partner is never
     * offered a candidate whose assignment would just fail that Helper's
     * own "Profession is not available for this employer" check a
     * moment later - not a new business rule, just not surfacing a
     * guaranteed-failure choice.
     */
    public function employerAssignableCandidates($id)
    {
        $partnerId = $this->partnerId();

        $employer = Employerplus::where('partneroffice_id', $partnerId)->where('id', $id)->first();

        if (!$employer) {
            return response()->json(['status' => 'error', 'message' => __('locale.Employer not found.')], 404);
        }

        $professionIds = array_filter(array_map('trim', explode(',', (string) $employer->proff_id)));

        $candidates = Candidate::where('status', 1)
            ->whereIn('jobtype_id', $professionIds)
            ->with('profession')
            ->orderBy('cand_name')
            ->get()
            ->map(function ($candidate) {
                return [
                    'id' => $candidate->id,
                    'name' => $candidate->cand_name,
                    'pass_no' => $candidate->pass_no,
                    'profession_id' => $candidate->jobtype_id,
                    'profession_name' => optional($candidate->profession)->eng_name,
                ];
            });

        return response()->json(['status' => 'success', 'data' => $candidates]);
    }

    /**
     * Assigns one candidate to this Employer, reusing the exact same
     * Helper::assignCandidateToEmployer() the CRM's own Assign Candidate
     * modal (admin/employer-listp/add-visa-view/{id}) posts to - not a
     * second assignment implementation. proff_id is deliberately derived
     * here from the candidate's own jobtype_id rather than trusted from
     * the request, and eligibility (status=1, profession actually needed
     * by this employer) is re-checked immediately before calling the
     * Helper - opening the modal and clicking Assign are two separate
     * requests, so another assignment could have happened in between
     * (the actual race-safety net, not the modal's own candidate list).
     *
     * assignbystaff_id ends up null on the resulting Employercandidate
     * row (Helper::assignCandidateToEmployer() always reads Auth::
     * guard('admin'), which has no session here) - left as-is rather
     * than patched around, since changing that shared Helper is exactly
     * what reusing it unmodified means not doing; partneroffice_id
     * (which partner owns this Employer) is still correctly set from
     * $employer->partneroffice_id regardless, and is the field that
     * actually enforces ownership elsewhere in this controller.
     */
    public function employerAssignCandidate(Request $request, $id)
    {
        $partnerId = $this->partnerId();

        $employer = Employerplus::where('partneroffice_id', $partnerId)->where('id', $id)->first();

        if (!$employer) {
            return response()->json(['status' => 'error', 'message' => __('locale.Employer not found.')], 404);
        }

        $validator = Validator::make($request->all(), [
            'cand_id' => 'required|integer|exists:candidates,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $candidate = Candidate::find($request->cand_id);

        $professionIds = array_filter(array_map('trim', explode(',', (string) $employer->proff_id)));

        if (!$candidate || (int) $candidate->status !== 1 || !in_array((string) $candidate->jobtype_id, $professionIds, true)) {
            return response()->json([
                'status' => 'error',
                'message' => __('locale.This candidate is no longer available. Please choose another candidate.'),
            ], 409);
        }

        $result = Helper::assignCandidateToEmployer([
            'fromreq' => 'employerplus',
            'visaeditid' => $employer->id,
            'cand_id' => $candidate->id,
            'proff_id' => $candidate->jobtype_id,
        ]);

        $succeeded = (int) ($result['status'] ?? 0) === 1;

        return response()->json([
            'status' => $succeeded ? 'success' : 'error',
            'message' => $result['message'] ?? __('locale.Something went wrong.'),
        ], $succeeded ? 200 : 422);
    }

    /**
     * Deassigns one candidate from this Employer - the smallest
     * equivalent of the CRM's own EmployerController::deassigngetdata()
     * (same two-step release: the Employercandidate row's status -> 0,
     * the Candidate's own status -> 1), reimplemented rather than called
     * directly because that admin method trusts $request->candemp_id on
     * its own (Employercandidate::find(), no ownership check at all -
     * fine there since only an authenticated admin can reach it) and has
     * no partner/employer scoping to reuse. Authorization chain mirrors
     * employerAssignCandidate(): authenticated partner -> owns this
     * Employer -> the assignment row itself belongs to THIS Employer's
     * own emp_id, never trusted from the request in isolation - a bare
     * employercandidate_id alone could otherwise name a row belonging to
     * a completely different (possibly another partner's) Employer.
     */
    public function employerDeassignCandidate(Request $request, $id)
    {
        $partnerId = $this->partnerId();

        $employer = Employerplus::where('partneroffice_id', $partnerId)->where('id', $id)->first();

        if (!$employer) {
            return response()->json(['status' => 'error', 'message' => __('locale.Employer not found.')], 404);
        }

        $validator = Validator::make($request->all(), [
            'employercandidate_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $assignment = Employercandidate::where('id', $request->employercandidate_id)
            ->where('emp_id', $employer->id)
            ->where('status', 1)
            ->first();

        if (!$assignment) {
            return response()->json([
                'status' => 'error',
                'message' => __('locale.This assignment could not be found or was already removed.'),
            ], 404);
        }

        DB::transaction(function () use ($assignment) {
            $assignment->status = false;
            $assignment->save();

            $candidate = Candidate::find($assignment->cand_id);

            if ($candidate) {
                $candidate->status = true;
                $candidate->save();
            }
        });

        return response()->json(['status' => 'success', 'message' => __('locale.Candidate deassigned successfully.')]);
    }

    /**
     * The old combined Profile/Settings page is now split into Account
     * (Personal Details) and Website (Company Profile/Branding/Domain) -
     * this bookmarked URL redirects to its Personal Details replacement
     * rather than 404ing.
     */
    public function profile()
    {
        return redirect()->route('worker.partner.account');
    }

    public function account()
    {
        $partner = Auth::guard('partner')->user();

        // Same Country/City models + flat, unfiltered list already used by
        // the admin partner-edit form (PartnerController/admin/partner/show.blade.php)
        // against these exact country_id/city_id columns - no new schema.
        $countries = Country::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('worker.partner.account', compact('partner', 'countries', 'cities'));
    }

    public function accountUpdate(Request $request)
    {
        $partner = Auth::guard('partner')->user();

        $validator = Validator::make($request->all(), [
            'rec_off_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'country_id' => 'nullable|integer|exists:countries,id',
            'city_id' => 'nullable|integer|exists:cities,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('worker.partner.account')
                ->withErrors($validator)
                ->withInput();
        }

        $partner->rec_off_name = $request->rec_off_name;
        $partner->owner_name = $request->owner_name;
        $partner->email = $request->email;
        $partner->country_id = $request->country_id;
        $partner->city_id = $request->city_id;
        $partner->save();

        return redirect()->route('worker.partner.account')->with('success', 'Profile updated successfully.');
    }

    /**
     * Website page - Company Profile/Branding/Domain tabs, all three
     * reading/writing this SAME Domain row (App\Models\Partner::domain(),
     * partner_id-scoped) that the CRM's "Website" tab manages - not
     * persisted here on a bare page view (only settingsCompanyUpdate/
     * settingsLogoUpdate/settingsDomainUpdate actually create the row, on
     * first real save), just built in-memory so the form fields below
     * have something to bind to for a partner who hasn't saved anything
     * there yet.
     */
    public function website()
    {
        $partner = Auth::guard('partner')->user();

        $domain = $partner->domain ?: $partner->domain()->make();

        // One row per page (Home/About/Contact/Privacy/Terms), each holding
        // both locales' EFFECTIVE values (this Partner's saved override
        // merged over the same default every public page itself falls
        // back to - see websiteConfigDefaults()), keyed by page so the
        // Website Config sub-tabs below can prefill their fields with
        // data_get($pageContents['home']['en'] ?? [], 'hero.eyebrow') etc.
        // Prefilling with the resolved default (not blank) is what lets a
        // Partner see exactly what's currently live before changing
        // anything; array_replace_recursive keeps this merge field-by-
        // field within each nested group (e.g. overriding just hero.lead
        // never blanks out the rest of hero.*).
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        $pageContentRows = PartnerPageContent::where('partner_id', $partner->id)->get()->keyBy('page');
        $pageContents = collect(PartnerPageContent::PAGES)->mapWithKeys(function ($page) use ($pageContentRows, $frontwebsite) {
            $row = $pageContentRows->get($page);
            $override = [
                'en' => $row->content['en'] ?? [],
                'ar' => $row->content['ar'] ?? [],
            ];

            return [$page => [
                'en' => array_replace_recursive($this->websiteConfigDefaults($page, 'en', $frontwebsite), $override['en']),
                'ar' => array_replace_recursive($this->websiteConfigDefaults($page, 'ar', $frontwebsite), $override['ar']),
            ]];
        })->all();

        // Contact Us > Branches/Locations - a sibling of content.en/ar
        // (see PartnerPageContent's own doc comment for why), so it's
        // resolved separately rather than folded into the merge above.
        $pageContents['contact']['branches'] = PartnerPageContent::effectiveBranches($partner->id);

        return view('worker.partner.website', compact('partner', 'domain', 'pageContents'));
    }

    /**
     * Website Config save - one endpoint for all 5 pages (Home/About/
     * Contact/Privacy/Terms), $page validated against an allow-list so an
     * unknown value 404s rather than silently creating a stray row.
     * Always resolves the partner from the authenticated guard, never from
     * request input - a Partner can only ever write their own row (the
     * unique(partner_id,page) index also makes this a single updateOrCreate,
     * never a duplicate).
     */
    public function websiteConfigUpdate(Request $request, string $page)
    {
        abort_unless(in_array($page, PartnerPageContent::PAGES, true), 404);

        $partner = Auth::guard('partner')->user();

        $validator = Validator::make($request->all(), $this->websiteConfigValidationRules($page));

        if ($validator->fails()) {
            return redirect()->route('worker.partner.website', ['open' => $page])
                ->withErrors($validator)
                ->withInput();
        }

        // Fields are prefilled with the resolved default (see
        // websiteConfigDefaults()) so the Partner can see the live site
        // before editing - which means an unchanged field arrives back
        // here holding that same default text, not blank. Anything that
        // still matches the CURRENT default is dropped rather than saved,
        // so it keeps tracking that default going forward (e.g. if the
        // CRM's frontendwebsiteconfigs or a translation later changes)
        // instead of being frozen as a stale "override" the moment the
        // Partner saves the form without touching it.
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();

        $contentEn = (array) $request->input('content.en', []);
        $contentAr = (array) $request->input('content.ar', []);
        $skipDefaultCompare = [];

        // Privacy/Terms "body" is Quill's HTML output - sanitize it
        // server-side before it's ever persisted (the only Website Config
        // value rendered unescaped on the public site), and never
        // auto-revert it by text-equality (see stripDefaultValues()'s own
        // doc comment for why that check doesn't apply to this field).
        if (in_array($page, ['privacy', 'terms'], true)) {
            if (array_key_exists('body', $contentEn)) {
                $contentEn['body'] = HtmlSanitizer::clean($contentEn['body']);
            }
            if (array_key_exists('body', $contentAr)) {
                $contentAr['body'] = HtmlSanitizer::clean($contentAr['body']);
            }
            $skipDefaultCompare = ['body'];
        }

        $this->saveWebsiteConfigContent($partner->id, $page, [
            'en' => $this->stripDefaultValues($contentEn, $this->websiteConfigDefaults($page, 'en', $frontwebsite), $skipDefaultCompare),
            'ar' => $this->stripDefaultValues($contentAr, $this->websiteConfigDefaults($page, 'ar', $frontwebsite), $skipDefaultCompare),
        ]);

        return redirect()->route('worker.partner.website', ['open' => $page])
            ->with('success', __('locale.Website Config updated successfully.'));
    }

    /**
     * Contact Us > Branches/Locations - a repeater of {name_en, name_ar,
     * image} records (add/edit/delete/reorder all happen client-side in
     * the DOM; Save resubmits the whole current list in one request,
     * which is what "reorder" and "delete" actually persist as - there's
     * no separate per-branch endpoint). image is either an existing URL
     * (unchanged/default - left exactly as submitted, never re-uploaded)
     * or a data: URI for a newly chosen file, decoded/validated/stored the
     * same way settingsLogoUpdate() already does for the Branding tab's
     * logos, into its own admin/assets/images/partner/branches folder so
     * filenames can never collide with a partner's logo uploads.
     *
     * If the submitted list (after upload) is identical to the current
     * default branches, it's dropped rather than saved - same "don't
     * freeze today's default as a permanent override" rule
     * stripDefaultValues() applies to every other Website Config field.
     */
    public function websiteConfigBranchesUpdate(Request $request)
    {
        $partner = Auth::guard('partner')->user();

        $validator = Validator::make($request->all(), [
            'branches' => 'present|array|max:24',
            'branches.*.name_en' => 'required|string|max:150',
            'branches.*.name_ar' => 'required|string|max:150',
            'branches.*.image' => 'required|string|max:5000000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxBytes = 2048 * 1024;

        $basepathstatus = Basepathstatus::first();
        $uploadPath = ($basepathstatus && $basepathstatus->base_path_status == 1)
            ? base_path('public/admin/assets/images/partner/branches')
            : base_path('public_html/admin/assets/images/partner/branches');

        $branches = [];

        foreach ($request->input('branches') as $i => $branch) {
            $image = trim((string) $branch['image']);

            if (preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,(.+)$/', $image, $matches)) {
                if (!in_array(strtolower($matches[1]), $allowedMimes, true)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => __('locale.Only JPG, PNG or WEBP images are allowed.'),
                    ], 422);
                }

                $binary = base64_decode($matches[2]);

                if ($binary === false || strlen($binary) > $maxBytes) {
                    return response()->json([
                        'status' => 'error',
                        'message' => __('locale.Image must not be larger than 2MB.'),
                    ], 422);
                }

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $extension = strtolower($matches[1]) === 'image/png' ? 'png' : (strtolower($matches[1]) === 'image/webp' ? 'webp' : 'jpg');
                $filename = 'branch_' . $partner->id . '_' . time() . '_' . $i . '.' . $extension;
                file_put_contents($uploadPath . '/' . $filename, $binary);

                // New file is written and confirmed before the image URL
                // below ever points at it - an old, now-replaced image
                // (if any) simply becomes unreferenced, never deleted
                // here, so a failed/interrupted save can never leave a
                // branch pointing at nothing.
                $image = asset('admin/assets/images/partner/branches/' . $filename);
            }

            $branches[] = [
                'name_en' => trim($branch['name_en']),
                'name_ar' => trim($branch['name_ar']),
                'image' => $image,
            ];
        }

        if ($branches === PartnerPageContent::defaultBranches()) {
            $branches = null;
        }

        $this->saveWebsiteConfigContent($partner->id, 'contact', ['branches' => $branches]);

        return response()->json([
            'status' => 'success',
            'message' => __('locale.Branches updated successfully.'),
            'data' => ['branches' => PartnerPageContent::effectiveBranches($partner->id)],
        ]);
    }

    /**
     * Merges into (rather than replaces) a page's saved content JSON -
     * content.en/content.ar (websiteConfigUpdate) and content.branches
     * (websiteConfigBranchesUpdate) are sibling keys on the SAME row, each
     * saved by a different form/request, so a plain updateOrCreate()
     * would silently wipe out whichever sibling key wasn't part of the
     * request that just saved.
     */
    private function saveWebsiteConfigContent(int $partnerId, string $page, array $partial): void
    {
        $row = PartnerPageContent::where('partner_id', $partnerId)->where('page', $page)->first();
        $content = array_merge($row->content ?? [], $partial);

        PartnerPageContent::updateOrCreate(
            ['partner_id' => $partnerId, 'page' => $page],
            ['content' => $content]
        );
    }

    /**
     * Every field is optional (nullable) - a Partner overrides only what
     * they want to change, everything else keeps rendering the existing
     * default (frontendwebsiteconfigs field or hardcoded/translated copy)
     * exactly as it does today. Applied identically to both content.en.*
     * and content.ar.* so neither locale can be partially validated.
     */
    private function websiteConfigValidationRules(string $page): array
    {
        $text = 'nullable|string|max:255';
        $longText = 'nullable|string|max:2000';
        $body = 'nullable|string|max:50000';

        $fieldsByPage = [
            'home' => [
                'hero.eyebrow' => $text,
                'hero.heading_prefix' => $text,
                'hero.heading_highlight' => $text,
                'hero.heading_suffix' => $text,
                'hero.lead' => $longText,
                'hero.primary_cta_text' => $text,
                'hero.card_title' => $text,
                'hero.card_subtitle' => $text,
                'process.eyebrow' => $text,
                'process.heading' => $text,
                'process.subheading' => $longText,
                'process.step1_heading' => $text,
                'process.step1_text' => $longText,
                'process.step2_heading' => $text,
                'process.step2_text' => $longText,
                'process.step3_heading' => $text,
                'process.step3_text' => $longText,
                'categories.eyebrow' => $text,
                'categories.heading' => $text,
                'cta.heading' => $text,
                'cta.text' => $longText,
                'cta.button_text' => $text,
            ],
            'about' => [
                'header_title' => $text,
                'header_subtitle' => $longText,
                'intro_text' => $longText,
                'why_choose_eyebrow' => $text,
                'why_choose_heading' => $text,
                'mission_heading' => $text,
                'mission_text' => $longText,
                'vision_heading' => $text,
                'vision_text' => $longText,
                'values_heading' => $text,
                'values_text' => $longText,
                'value_added_eyebrow' => $text,
                'value_added_heading' => $text,
                'value_added_1_heading' => $text,
                'value_added_1_text' => $longText,
                'value_added_2_heading' => $text,
                'value_added_2_text' => $longText,
                'value_added_3_heading' => $text,
                'value_added_3_text' => $longText,
                'cta_heading' => $text,
                'cta_text' => $longText,
                'cta_button_text' => $text,
            ],
            'contact' => [
                'header_title' => $text,
                'header_subtitle' => $longText,
                'intro_text' => $longText,
                'address' => $text,
                'phone' => $text,
                'email' => 'nullable|email|max:255',
                'branches_eyebrow' => $text,
                'branches_heading' => $text,
                'branches_subheading' => $longText,
            ],
            'privacy' => [
                'title' => $text,
                'subtitle' => $longText,
                'body' => $body,
            ],
            'terms' => [
                'title' => $text,
                'subtitle' => $longText,
                'body' => $body,
            ],
        ];

        $rules = [];
        foreach (['en', 'ar'] as $locale) {
            foreach ($fieldsByPage[$page] as $field => $rule) {
                $rules["content.{$locale}.{$field}"] = $rule;
            }
        }

        return $rules;
    }

    /**
     * Drops any submitted field that still matches the current default -
     * see websiteConfigUpdate()'s own comment for why (Website Config's
     * inputs are prefilled with the resolved default, so "didn't touch
     * this field" and "typed the default text back in" are indistinguishable
     * on submit; both should keep tracking the live default rather than
     * freezing today's default text as a permanent override).
     */
    /**
     * $skipDefaultCompare names fields (e.g. 'body') that never get the
     * "matches the current default, so drop it" check - only the "empty
     * means revert to default" rule still applies to them. Needed for the
     * Privacy/Terms rich-text body: its default is the richer 14/18-
     * section legal markup (TOC nav, anchored sections), which Quill's
     * simpler paragraph/heading/list model can never round-trip back to
     * byte-for-byte - comparing it for equality would never match and is
     * pointless, not "safe to skip for other reasons".
     */
    private function stripDefaultValues(array $submitted, array $defaults, array $skipDefaultCompare = []): array
    {
        $flatDefaults = Arr::dot($defaults);
        $result = [];

        foreach (Arr::dot($submitted) as $key => $value) {
            $value = is_string($value) ? trim($value) : $value;

            if ($value === '') {
                continue;
            }

            if (!in_array($key, $skipDefaultCompare, true)) {
                $defaultValue = array_key_exists($key, $flatDefaults) ? trim((string) $flatDefaults[$key]) : null;
                if ($value === $defaultValue) {
                    continue;
                }
            }

            Arr::set($result, $key, $value);
        }

        return $result;
    }

    /**
     * The value every public page itself already falls back to for one
     * Website Config field, in one locale - built from the exact same
     * sources those pages read from (never a second copy of the actual
     * copy): translation keys via __(..., $locale) (resources/lang/{en,ar}/
     * locale.php - the same key the public Blade's own `?? __('locale.X')`
     * uses), the frontendwebsiteconfigs row (About/Contact intro + contact
     * details, same as worker.about/worker.contact), and for Privacy/Terms'
     * full legal body, the same worker.partials.*-legal-content partial
     * the public page itself @includes, rendered here to a string. This is
     * ALSO what a Partner's saved override is diffed against on save (see
     * stripDefaultValues()) - one function, two call sites, so the two can
     * never drift apart.
     */
    private function websiteConfigDefaults(string $page, string $locale, $frontwebsite): array
    {
        $isArabic = $locale === 'ar';

        switch ($page) {
            case 'home':
                return [
                    'hero' => [
                        // Not translated on the public page either (worker/home.blade.php
                        // writes this eyebrow as a plain literal, not __()) - kept
                        // identical to that literal, not reworded here.
                        'eyebrow' => 'Qamr Worker Portal',
                        'heading_prefix' => __('locale.Hire', [], $locale),
                        'heading_highlight' => __('locale.Verified, Work-Ready', [], $locale),
                        'heading_suffix' => __('locale.Talent — Faster', [], $locale),
                        'lead' => __('locale.Browse professionally screened candidate resumes across trades, domestic and skilled roles. Every profile is reviewed for accuracy so you can shortlist with confidence.', [], $locale),
                        'primary_cta_text' => __('locale.Browse Resumes', [], $locale),
                        'card_title' => __('locale.Find talent in seconds', [], $locale),
                        'card_subtitle' => __("locale.Jump straight to what you're hiring for.", [], $locale),
                    ],
                    'process' => [
                        'eyebrow' => __('locale.Simple Process', [], $locale),
                        'heading' => __('locale.Hiring made straightforward', [], $locale),
                        'subheading' => __('locale.From search to shortlist in three simple steps.', [], $locale),
                        'step1_heading' => __('locale.Search & Filter', [], $locale),
                        'step1_text' => __('locale.Narrow candidates by profession, experience type, work location and more.', [], $locale),
                        'step2_heading' => __('locale.Review Full Profile', [], $locale),
                        'step2_text' => __('locale.Check personal details, employment history, education and passport information.', [], $locale),
                        'step3_heading' => __('locale.Reach Out To Hire', [], $locale),
                        'step3_text' => __('locale.Contact our team directly by phone or WhatsApp to start the hiring process.', [], $locale),
                    ],
                    'categories' => [
                        'eyebrow' => __('locale.Popular Categories', [], $locale),
                        'heading' => __('locale.Browse by profession', [], $locale),
                    ],
                    'cta' => [
                        'heading' => __('locale.Ready to find your next hire?', [], $locale),
                        'text' => __('locale.Explore the full list of verified, work-ready candidates on the portal today.', [], $locale),
                        'button_text' => __('locale.Browse All Resumes', [], $locale),
                    ],
                ];

            case 'about':
                return [
                    'header_title' => __('locale.About Us', [], $locale),
                    'header_subtitle' => __('locale.Learn about our mission, vision and the team behind Qamr International.', [], $locale),
                    // Same frontendwebsiteconfigs.about_us_eng/about_us_ar +
                    // hardcoded fallback chain as worker.about's own $aboutText.
                    'intro_text' => ($isArabic && !empty($frontwebsite->about_us_ar ?? null))
                        ? $frontwebsite->about_us_ar
                        : ($frontwebsite->about_us_eng ?? __('locale.Connecting verified, work-ready candidates with employers who need reliable talent, fast.', [], $locale)),
                    'why_choose_eyebrow' => __('locale.About Us', [], $locale),
                    'why_choose_heading' => __('locale.Why Choose Us', [], $locale),
                    'mission_heading' => __('locale.Mission', [], $locale),
                    'mission_text' => __('locale.To provide unmatched recruitment solutions that help our clients become more productive and profitable.', [], $locale),
                    'vision_heading' => __('locale.Vision', [], $locale),
                    'vision_text' => __('locale.To be globally known for an impactful, efficient and innovative Human Resources Consulting Partner.', [], $locale),
                    'values_heading' => __('locale.Values', [], $locale),
                    'values_text' => __('locale.Creating a self-sustaining, productive environment that gives the best experiences and opportunities of growth to our customers and employees alike.', [], $locale),
                    'value_added_eyebrow' => __('locale.Why Choose Us', [], $locale),
                    'value_added_heading' => __('locale.Value Added Services for World-Class Customer Experience', [], $locale),
                    'value_added_1_heading' => __('locale.Providing Service Before Self-Interest', [], $locale),
                    'value_added_1_text' => __('locale.We take care of everything before your visit so that you can focus on your business and growth by saving your valuable time.', [], $locale),
                    'value_added_2_heading' => __('locale.We have always been at the forefront of providing value-added services', [], $locale),
                    'value_added_2_text' => __('locale.Catering our clients with all the benefits and convenience.', [], $locale),
                    'value_added_3_heading' => __('locale.Delightful Experience', [], $locale),
                    'value_added_3_text' => __('locale.Taking into account the overall journey by building long term relationship with our clients.', [], $locale),
                    'cta_heading' => __('locale.Have a question for our team?', [], $locale),
                    'cta_text' => __("locale.We'd love to hear from you - get in touch and we'll respond as soon as we can.", [], $locale),
                    'cta_button_text' => __('locale.Contact Us', [], $locale),
                ];

            case 'contact':
                return [
                    'header_title' => __('locale.Get in touch!', [], $locale),
                    'header_subtitle' => __('locale.Have questions about hiring or working with Qamr International? Reach us directly using the details below.', [], $locale),
                    // Same frontendwebsiteconfigs fields + hardcoded fallback
                    // chain as worker.contact's own $contactIntro/$contactAddr/
                    // $contactPhone/$contactEmail.
                    'intro_text' => ($isArabic && !empty($frontwebsite->contact_us_ar ?? null))
                        ? $frontwebsite->contact_us_ar
                        : ($frontwebsite->contact_us_eng ?? __('locale.Fill out the form and our team will get back to you within 24 hours.', [], $locale)),
                    'address' => ($isArabic && !empty($frontwebsite->contact_us_location_ar ?? null))
                        ? $frontwebsite->contact_us_location_ar
                        : ($frontwebsite->contact_us_location ?: __('locale.Mumbai, India', [], $locale)),
                    'phone' => $frontwebsite->contact_us_phone ?: '+919969566388',
                    'email' => $frontwebsite->contact_us_email ?: 'info@qamrintl.com',
                    'branches_eyebrow' => __('locale.Our Office', [], $locale),
                    'branches_heading' => __('locale.Location', [], $locale),
                    'branches_subheading' => __('locale.Our branches across the region.', [], $locale),
                ];

            case 'privacy':
                return [
                    'title' => __('locale.Privacy Policy', [], $locale),
                    'subtitle' => __('locale.How Qamr International collects, uses, discloses and protects information on the Worker Portal.', [], $locale),
                    'body' => $this->renderDefaultLegalBody('worker.partials.privacy-policy-legal-content', $frontwebsite, $locale),
                ];

            case 'terms':
                return [
                    'title' => __('locale.Terms of Service', [], $locale),
                    'subtitle' => __('locale.The terms that govern access to and use of the Qamr International Worker Portal.', [], $locale),
                    'body' => $this->renderDefaultLegalBody('worker.partials.terms-of-service-legal-content', $frontwebsite, $locale),
                ];
        }

        return [];
    }

    /**
     * Renders the same worker.partials.*-legal-content partial the public
     * Privacy/Terms pages @include, in a specific locale, to a plain HTML
     * string - the Website Config "Full Page Content" textarea's default.
     * Temporarily swaps the app locale (that partial's __() calls read it,
     * not a parameter) and always restores it in a finally, since this
     * runs mid-request while building the Partner's own admin page (which
     * has its own, unrelated current locale).
     */
    private function renderDefaultLegalBody(string $view, $frontwebsite, string $locale): string
    {
        $previousLocale = App::getLocale();
        App::setLocale($locale);

        try {
            return view($view, ['frontwebsite' => $frontwebsite])->render();
        } finally {
            App::setLocale($previousLocale);
        }
    }

    /**
     * Company Profile settings tab - the SAME Domain row (by partner_id)
     * the CRM's "Website" tab -> Address sub-tab manages
     * (DomainController::updateAddress()), so a change either side shows
     * up on both. Deliberately not calling that method directly - it
     * trusts $request->id for which partner to update, which is correct
     * there (only an authenticated admin can reach it) but would be an
     * IDOR here; $domain is always resolved from the logged-in partner's
     * own domain() relationship instead, never from request input.
     */
    public function settingsCompanyUpdate(Request $request)
    {
        $partner = Auth::guard('partner')->user();

        $validator = Validator::make($request->all(), [
            'company_name' => 'nullable|string|max:255',
            'company_name_ar' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',
            'company_address_ar' => 'nullable|string',
            'company_mobile' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $domain = $partner->domain()->firstOrCreate([]);

        $domain->update([
            'company_name' => $request->company_name,
            'company_name_ar' => $request->company_name_ar,
            'company_address' => $request->company_address,
            'company_address_ar' => $request->company_address_ar,
            'company_mobile' => $request->company_mobile,
            'company_email' => $request->company_email,
        ]);

        return response()->json(['status' => 'success', 'message' => __('locale.Company profile updated successfully.')]);
    }

    /**
     * Branding settings tab - same website_logo/website_logo_ar columns
     * and admin/assets/images/partner/ upload folder as the CRM's
     * "Website" tab -> Logo sub-tab (DomainController::websitelogoupdt()),
     * whose base64/MIME/size validation this mirrors, scoped to the
     * logged-in partner's own Domain row instead of a request-supplied id.
     */
    public function settingsLogoUpdate(Request $request)
    {
        $partner = Auth::guard('partner')->user();

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxBytes = 2048 * 1024;

        $decoded = [];

        foreach (['english_logo' => 'website_logo', 'arabic_logo' => 'website_logo_ar'] as $input => $column) {
            $value = $request->input($input);

            if (!$value) {
                continue;
            }

            if (!preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,(.+)$/', $value, $matches)) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [$input => ['Invalid image data.']],
                ], 422);
            }

            if (!in_array(strtolower($matches[1]), $allowedMimes, true)) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [$input => ['Only JPG, PNG or WEBP images are allowed.']],
                ], 422);
            }

            $binary = base64_decode($matches[2]);

            if ($binary === false || strlen($binary) > $maxBytes) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [$input => ['Image must not be larger than 2MB.']],
                ], 422);
            }

            $decoded[$column] = $binary;
        }

        if (empty($decoded)) {
            return response()->json(['status' => 'error', 'message' => __('locale.Choose at least one logo to upload.')], 422);
        }

        $domain = $partner->domain()->firstOrCreate([]);

        $basepathstatus = Basepathstatus::first();
        $uploadPath = ($basepathstatus && $basepathstatus->base_path_status == 1)
            ? base_path('public/admin/assets/images/partner')
            : base_path('public_html/admin/assets/images/partner');

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        foreach ($decoded as $column => $binary) {
            $suffix = $column === 'website_logo' ? 'en' : 'ar';

            if ($domain->{$column} && file_exists($uploadPath . '/' . $domain->{$column})) {
                unlink($uploadPath . '/' . $domain->{$column});
            }

            $name = time() . '_' . $suffix . '.png';
            file_put_contents($uploadPath . '/' . $name, $binary);
            $domain->{$column} = $name;
        }

        $domain->save();

        return response()->json([
            'status' => 'success',
            'message' => __('locale.Logos updated successfully.'),
            'data' => [
                'english_logo' => $domain->website_logo ? asset('admin/assets/images/partner/' . $domain->website_logo) : null,
                'arabic_logo' => $domain->website_logo_ar ? asset('admin/assets/images/partner/' . $domain->website_logo_ar) : null,
            ],
        ]);
    }

    /**
     * The Domain settings tab is now DISPLAY-ONLY - a Partner can see
     * their configured *.recruitmentcv.com subdomain (App\Models\
     * Partner::domain()->sub_domain, the same column ResolvePartner
     * WebsiteDomain middleware reads and the CRM's automatic Hostinger
     * provisioning flow - HostingerSubdomainService - writes) but can no
     * longer change it from here. This endpoint is intentionally kept
     * rather than removed, specifically so it can go on rejecting writes
     * even though the form/button that used to POST here is gone from
     * the UI - the frontend no longer offering a way to reach this is
     * not what prevents a change, this is.
     */
    public function settingsDomainUpdate(Request $request)
    {
        return response()->json([
            'status' => 'error',
            'message' => __('locale.Your subdomain is managed automatically and cannot be changed here.'),
        ], 403);
    }
}
