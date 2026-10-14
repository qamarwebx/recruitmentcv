<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Carnknown;
use App\Models\City;
use App\Models\Country;
use App\Models\Education;
use App\Models\Expecworkcity;
use App\Models\Placeofissue;
use App\Models\PartnerPageContent;
use App\Models\PartnerPrice;
use App\Models\Profession;
use App\Models\Region;
use App\Models\Religion;
use App\Models\Websiteconfig;
use App\Models\WorkerContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkerPageController extends Controller
{
    /**
     * Base query for candidates that are safe to show publicly.
     */
    private function availableCandidates()
    {
        return Candidate::where('status', 1)
            ->where('publish', 1)
            ->where('isdelete', 0)
            ->where('cv_execute', 1);
    }

    /**
     * This request's Website Config overrides for one public page, in the
     * current locale - resolved from the SAME Partner ResolvePartnerWebsiteDomain
     * already bound into the container from the Host header (never a
     * second subdomain lookup). Always an array (never null), and always
     * only the fields a Partner actually chose to override - every call
     * site falls back to the existing default with `?? __('locale...')` /
     * `?? $frontwebsite->...`, so an apex-domain visit or a partner who
     * hasn't configured anything renders byte-identical to today.
     */
    private function pageContent(string $page): array
    {
        return PartnerPageContent::contentFor($this->currentPartnerId(), $page, app()->getLocale());
    }

    private function currentPartnerId(): ?int
    {
        $partner = app()->bound('currentPartner') ? app('currentPartner') : null;

        return optional($partner)->id;
    }

    public function home()
    {
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        $webconfig = Websiteconfig::first();

        // Live stat-card counts (shared with the Website Config defaults).
        $stats = PartnerPageContent::homeStats();
        $totalResumes = $stats['candidates'];
        $totalProfessions = $stats['professions'];
        $totalCities = $stats['cities'];
        $totalCountries = $stats['countries'];

        $featured = $this->availableCandidates()
            ->with('profession:id,eng_name,ar_name', 'religion:id,name,arbname')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $jobTypes = Profession::orderBy('eng_name')->limit(10)->get();

        $homeContent = $this->pageContent('home');

        return view('worker.home', compact(
            'frontwebsite',
            'webconfig',
            'totalResumes',
            'totalProfessions',
            'totalCities',
            'totalCountries',
            'featured',
            'jobTypes',
            'homeContent'
        ));
    }

    public function resumes(Request $request)
    {
        $cand = $this->availableCandidates()->with('profession:id,eng_name,ar_name', 'religion:id,name,arbname');

        if (!empty($request->location_id)) {
            $cand->whereRaw('FIND_IN_SET(?, expwp_id)', [$request->location_id]);
        }

        if (!empty($request->proff_id)) {
            $cand->whereRaw('FIND_IN_SET(?, proff_id)', [$request->proff_id]);
        }

        if (!empty($request->expcity_id)) {
            $cand->where('gulfexperience', $request->expcity_id);
        }

        if (!empty($request->age)) {
            [$minage, $maxage] = explode('-', $request->age);
            $cand->whereBetween('age', [$minage, $maxage]);
        }

        if (!empty($request->religions)) {
            $cand->where('religion_id', $request->religions);
        }

        if (!empty($request->finalExp)) {
            $cand->whereIn('overall_exp', array_unique($request->finalExp));
        }

        // Available / Already Hired (?hiring=hired; default Available) for this
        // viewer (partner / customer / guest): the same rule as the Already Hired
        // badge (PartnerCandidateAccess::whereAlreadyHired()). Counts are over the
        // whole filtered result set, not just this page.
        $hiring = $request->query('hiring') === 'hired' ? 'hired' : 'available';
        [$viewerPartnerId, $viewerUserId] = \App\Support\PartnerCandidateAccess::viewer();
        $hiringCounts = [
            'available' => \App\Support\PartnerCandidateAccess::whereAlreadyHired(clone $cand, $viewerPartnerId, $viewerUserId, false)->count(),
            'hired' => \App\Support\PartnerCandidateAccess::whereAlreadyHired(clone $cand, $viewerPartnerId, $viewerUserId, true)->count(),
        ];
        \App\Support\PartnerCandidateAccess::whereAlreadyHired($cand, $viewerPartnerId, $viewerUserId, $hiring === 'hired');

        $posts = $cand->orderBy('id', 'DESC')->paginate(9)->withQueryString();

        $cities = $this->workCities();

        $jobTypes = Profession::orderBy('eng_name')->get();
        $religions = Religion::orderBy('name')->get();
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        $webconfig = Websiteconfig::first();

        if ($request->ajax()) {
            return view('worker.resumes.partial', compact('posts', 'hiring', 'hiringCounts'));
        }

        // Banner texts: partner override -> global -> default (Website Config).
        $resumesContent = $this->pageContent('resumes');

        return view('worker.resumes.index', compact(
            'resumesContent',
            'posts',
            'hiring',
            'hiringCounts',
            'cities',
            'jobTypes',
            'religions',
            'frontwebsite',
            'webconfig'
        ));
    }

    public function privacyPolicy()
    {
        return view('worker.privacy-policy', [
            'frontwebsite' => DB::table('frontendwebsiteconfigs')->first(),
            'webconfig' => Websiteconfig::first(),
            'privacyContent' => $this->pageContent('privacy'),
        ]);
    }

    public function termsOfService()
    {
        return view('worker.terms-of-service', [
            'frontwebsite' => DB::table('frontendwebsiteconfigs')->first(),
            'webconfig' => Websiteconfig::first(),
            'termsContent' => $this->pageContent('terms'),
        ]);
    }

    /**
     * About Us / How It Works / Pricing texts for this website: this
     * language's values over the English ones over the standard texts
     * (InfoPages::defaults) - a field saved only in English shows in English
     * on the Arabic page rather than disappearing. Each value follows the
     * usual chain: partner -> "Recruitmentcv Partner (Global)" ->
     * recruitmentcv.com (PartnerPageContent::contentFor).
     */
    private function infoPageContent(string $page): array
    {
        $locale = app()->getLocale();
        $content = \App\Support\PartnerGlobal::filled($this->pageContent($page));

        if ($locale !== 'en') {
            $english = \App\Support\PartnerGlobal::filled(PartnerPageContent::contentFor($this->currentPartnerId(), $page, 'en'));
            $content = array_replace($english, $content);
        }

        return array_replace(\App\Support\InfoPages::defaults($page, $locale), $content);
    }

    /** What every redesigned info page shares: site config, this page's lists, the CTA contact list. */
    private function infoPageData(string $page): array
    {
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        $footer = \App\Support\SiteBrand::footer($frontwebsite);

        return [
            'frontwebsite' => $frontwebsite,
            'webconfig' => Websiteconfig::first(),
            'page' => $this->infoPageContent($page),
            'lists' => \App\Support\PageLists::all($this->currentPartnerId(), $page),
            // This website's own phone / e-mail (Company Profile, else the global footer ones) + its host.
            'contact' => array_filter([
                'phone' => trim((string) ($footer['phone'] ?? '')),
                'email' => trim((string) ($footer['email'] ?? '')),
                'web' => request()->getHost(),
            ]),
        ];
    }

    /** Work locations that have at least one public candidate (the /resumes filter list). */
    private function workCities()
    {
        return Expecworkcity::whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('candidates')
                    ->whereRaw('FIND_IN_SET(expecworkcities.id, candidates.expwp_id)')
                    ->where('status', 1)
                    ->where('publish', 1)
                    ->where('isdelete', 0)
                    ->where('cv_execute', 1);
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * The most common values among public candidates of a comma-list column
     * (proff_id / expwp_id): [id => count], most first - the About page's
     * "Browse" shortcuts, so they only ever point at filters with results.
     */
    private function topCandidateValues(string $table, string $column, int $limit): array
    {
        return DB::table($table)
            ->join('candidates', fn ($join) => $join->on(DB::raw("FIND_IN_SET({$table}.id, candidates.{$column})"), '>', DB::raw('0')))
            ->where('candidates.status', 1)
            ->where('candidates.publish', 1)
            ->where('candidates.isdelete', 0)
            ->where('candidates.cv_execute', 1)
            ->groupBy("{$table}.id")
            ->orderByRaw('COUNT(*) DESC')
            ->limit($limit)
            ->selectRaw("{$table}.id, COUNT(*) AS total")
            ->pluck('total', 'id')
            ->all();
    }

    /**
     * About Us (/about-us), laid out like the design reference: hero with
     * the latest available candidates (the same public candidates, fields and
     * photos as the listing cards - never passport or contact details), the
     * Browse Resumes filters, who we are, why choose us, mission / vision /
     * values, offices, gallery, CTA. Texts + lists from Website Config ->
     * About Us (partner -> Partner Global -> recruitmentcv.com).
     */
    public function aboutUs()
    {
        [$viewerPartnerId, $viewerUserId] = \App\Support\PartnerCandidateAccess::viewer();
        $candidates = \App\Support\PartnerCandidateAccess::whereAlreadyHired(
                $this->availableCandidates()->with('profession:id,eng_name,ar_name'),
                $viewerPartnerId,
                $viewerUserId,
                false
            )
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        $professions = Profession::orderBy('eng_name')->get();
        $cities = $this->workCities();
        $topProfessions = $this->topCandidateValues('professions', 'proff_id', 4);
        $topCities = $this->topCandidateValues('expecworkcities', 'expwp_id', 3);

        return view('worker.about', $this->infoPageData('about') + [
            'candidates' => $candidates,
            'professions' => $professions,
            'cities' => $cities,
            'chipProfessions' => $professions->whereIn('id', array_keys($topProfessions))->sortByDesc(fn ($p) => $topProfessions[$p->id])->values(),
            'chipCities' => $cities->whereIn('id', array_keys($topCities))->sortByDesc(fn ($c) => $topCities[$c->id])->values(),
        ]);
    }

    /**
     * How It Works (/how-it-works): Website Config texts + lists (setup card,
     * compare table, FAQs) and the steps (App\Support\InfoPages: own ->
     * Partner Global -> recruitmentcv.com -> the Home process steps).
     */
    public function howItWorks()
    {
        return view('worker.how-it-works', $this->infoPageData(\App\Support\InfoPages::HOW) + [
            'steps' => \App\Support\InfoPages::steps($this->currentPartnerId()),
        ]);
    }

    /**
     * Pricing (/pricing): Website Config texts + lists and this website's
     * plans (own -> Partner Global -> recruitmentcv.com; never invented).
     * Display only - no checkout or payment behind any plan.
     */
    public function pricing()
    {
        return view('worker.pricing', $this->infoPageData(\App\Support\InfoPages::PRICING) + [
            'plans' => \App\Support\InfoPages::plans($this->currentPartnerId()),
        ]);
    }

    /**
     * Content/structure mirrors qamarhire.com's own Contact Us page
     * (FrontEndController::contact() / resources/views/contact.blade.php)
     * - same frontendwebsiteconfigs "bottom_contact_us_*" address/phone/
     * email fields already used by this portal's own footer.blade.php and
     * privacy-policy.blade.php, rendered through the Worker Portal's own
     * design system.
     */
    public function contactUs()
    {
        return view('worker.contact', [
            'frontwebsite' => DB::table('frontendwebsiteconfigs')->first(),
            'webconfig' => Websiteconfig::first(),
            'contactContent' => $this->pageContent('contact'),
            'branches' => PartnerPageContent::effectiveBranches($this->currentPartnerId()),
        ]);
    }

    /**
     * Unlike the reference site's contact form (a plain HTML form with no
     * action/backend at all - see contact.blade.php there), this actually
     * saves the enquiry. Deliberately its own small worker_contact_messages
     * table rather than the CRM's Contactp/Allcontact or Lead models - see
     * the migration for why reusing either would be the wrong fit here.
     */
    public function contactUsStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        WorkerContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => __('locale.Thanks for reaching out! Our team will get back to you within 24 hours.'),
        ]);
    }

    public function resumeDetails($id)
    {
        $post = Candidate::with('profession:id,eng_name,ar_name')
            ->where('slug_text', $id)
            ->first();

        if (!$post) {
            return view('page_not_found');
        }

        $workPlaces = City::whereIn('id', explode(',', (string) $post->expcity_id))->get();
        $experienceCountries = Country::whereIn('id', explode(',', (string) $post->expcountry_id))->get();

        $relatedPosts = $this->availableCandidates()
            ->with('profession:id,eng_name,ar_name', 'religion:id,name,arbname')
            ->where('job_type', $post->job_type)
            ->where('slug_text', '!=', $id)
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        $placeOfIssue = Placeofissue::find($post->poi);
        $totalExperience = $post->experience ? array_sum(explode(',', $post->experience)) : 0;
        $vehiclesKnown = Carnknown::whereIn('id', explode(',', (string) $post->carknown_id))->get();
        $education = Education::find($post->education_id);
        $vehicleTransmissions = DB::table('vehical_transmission')
            ->whereIn('id', explode(',', (string) $post->vehical_transmission))
            ->get();

        $nation = Country::find($post->nation_id);
        $region = Region::find($post->region_id);
        $religion = Religion::find($post->religion_id);

        $expectedWorkPlaces = Expecworkcity::whereIn('id', explode(',', (string) $post->expwp_id))->get();

        $bookingRequirements = DB::table('requirement_info')->where('language_type', 1)->get();

        // This subdomain partner's own price for the candidate's experience
        // type + profession, else the global default (apex: default only).
        [$priceLabel, $departureLabel] = PartnerPrice::labelsFor($this->currentPartnerId(), $post);

        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        $webconfig = Websiteconfig::first();

        // Customer wishlist state for the Add/Saved button (web guard only).
        $customer = \Illuminate\Support\Facades\Auth::guard('web')->user();
        $customerId = $customer ? $customer->id : null;
        $isWishlisted = $customerId
            ? DB::table('wishlists')->where('user_id', $customerId)->where('cand_id', $post->id)->where('status', 1)->exists()
            : false;

        // Customer Hire Now modal options (logged-in customers only). The
        // partner shown/offered comes from the same server-side rule the
        // order itself uses (CustomerHireController::resolvePartner).
        $hire = null;
        // Customers only exist on partner subdomains (main site = partner entry).
        if ($customer && \App\Support\CustomerSite::isPartnerSite()) {
            [$hirePartnerId, $hireMustChoose] = \App\Http\Controllers\Worker\CustomerHireController::resolvePartner($post);
            $existingOrder = DB::table('bookings')->where('user_id', $customerId)->where('cand_id', $post->id)->where('booking_status', '!=', 2)->first(['reference_no']);

            $hire = [
                'cities' => empty($post->expwp_id)
                    ? Expecworkcity::orderBy('name')->get()
                    : Expecworkcity::whereIn('id', explode(',', (string) $post->expwp_id))->get(),
                'embassies' => DB::table('embassies')
                    ->when(!empty($post->embassy_for), fn ($q) => $q->where('id', $post->embassy_for))
                    ->orderBy('embassy')->get(['id', 'embassy']),
                'partner' => $hirePartnerId ? \App\Models\Partner::find($hirePartnerId, ['id', 'rec_off_name', 'rec_office_arname', 'portal_rec_off_name', 'portal_add_disp_only']) : null,
                'offices' => $hireMustChoose ? \App\Http\Controllers\Worker\CustomerHireController::portalOffices() : collect(),
                'canHire' => (int) $customer->status === 1 && !empty($customer->mobile_verified_at),
                // Prefill for the modal's inline mobile verification.
                'mobile' => $customer->mobile_no,
                'countryCode' => ltrim((string) $customer->country_code, '+'),
                'limitReached' => $webconfig && DB::table('bookings')->where('user_id', $customerId)->where('booking_status', '!=', 2)->count() >= (int) $webconfig->max_booking_limit,
                'existingRef' => $existingOrder->reference_no ?? null,
            ];
        }

        // "Already Hired" badge + notice, for whoever is looking: a signed-in
        // partner (its own orders don't count), a customer (its own orders
        // don't count) or a guest (any live order counts). Hidden when the
        // viewer already has a live order of their own for this candidate.
        // Same lookup as the listings (PartnerCandidateAccess::alreadyHiredIds()).
        $alreadyHired = \App\Support\PartnerCandidateAccess::alreadyHiredIdsForViewer([$post->id]) === [(int) $post->id];

        return view('worker.resumes.details', [
            'alreadyHired' => $alreadyHired,
            'isWishlisted' => $isWishlisted,
            'hire' => $hire,
            'post' => $post,
            'workPlaces' => $workPlaces,
            'experienceCountries' => $experienceCountries,
            'relatedPosts' => $relatedPosts,
            'placeOfIssue' => $placeOfIssue,
            'totalExperience' => $totalExperience,
            'vehiclesKnown' => $vehiclesKnown,
            'education' => $education,
            'vehicleTransmissions' => $vehicleTransmissions,
            'nation' => $nation,
            'region' => $region,
            'religion' => $religion,
            'expectedWorkPlaces' => $expectedWorkPlaces,
            'bookingRequirements' => $bookingRequirements,
            'priceLabel' => $priceLabel,
            'departureLabel' => $departureLabel,
            'frontwebsite' => $frontwebsite,
            'webconfig' => $webconfig,
        ]);
    }
}
