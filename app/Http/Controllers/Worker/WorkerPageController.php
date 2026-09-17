<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Carnknown;
use App\Models\City;
use App\Models\Country;
use App\Models\Customercost;
use App\Models\Education;
use App\Models\Expecworkcity;
use App\Models\Placeofissue;
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

    public function home()
    {
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        $webconfig = Websiteconfig::first();

        $totalResumes = $this->availableCandidates()->count();
        $totalProfessions = Profession::count();

        $totalCities = DB::table('expecworkcities')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('candidates')
                    ->whereRaw('FIND_IN_SET(expecworkcities.id, candidates.expwp_id)')
                    ->where('status', 1)
                    ->where('publish', 1)
                    ->where('isdelete', 0)
                    ->where('cv_execute', 1);
            })
            ->count();

        $totalCountries = DB::table('countries')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('candidates')
                    ->whereRaw('FIND_IN_SET(countries.id, candidates.expcountry_id)')
                    ->where('status', 1)
                    ->where('publish', 1)
                    ->where('isdelete', 0)
                    ->where('cv_execute', 1);
            })
            ->count();

        $featured = $this->availableCandidates()
            ->with('profession:id,eng_name,ar_name')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $jobTypes = Profession::orderBy('eng_name')->limit(10)->get();

        return view('worker.home', compact(
            'frontwebsite',
            'webconfig',
            'totalResumes',
            'totalProfessions',
            'totalCities',
            'totalCountries',
            'featured',
            'jobTypes'
        ));
    }

    public function resumes(Request $request)
    {
        $cand = $this->availableCandidates()->with('profession:id,eng_name,ar_name');

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

        $posts = $cand->orderBy('id', 'DESC')->paginate(9)->withQueryString();

        $cities = Expecworkcity::whereExists(function ($query) {
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

        $jobTypes = Profession::orderBy('eng_name')->get();
        $religions = Religion::orderBy('name')->get();
        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        $webconfig = Websiteconfig::first();

        if ($request->ajax()) {
            return view('worker.resumes.partial', compact('posts'));
        }

        return view('worker.resumes.index', compact(
            'posts',
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
        ]);
    }

    public function termsOfService()
    {
        return view('worker.terms-of-service', [
            'frontwebsite' => DB::table('frontendwebsiteconfigs')->first(),
            'webconfig' => Websiteconfig::first(),
        ]);
    }

    /**
     * Content/structure mirrors qamarhire.com's own About Us page
     * (FrontEndController::about() / resources/views/about.blade.php) -
     * same frontendwebsiteconfigs-driven about_us_eng/about_us_ar copy, so
     * editing it in the CRM's front-end website config updates both sites
     * at once - rendered through the Worker Portal's own design system
     * instead of that page's Bootstrap/Vuexy markup.
     */
    public function aboutUs()
    {
        return view('worker.about', [
            'frontwebsite' => DB::table('frontendwebsiteconfigs')->first(),
            'webconfig' => Websiteconfig::first(),
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

        $videoId = $post->video_link ? explode('/', $post->video_link) : null;
        $testVideoId = $post->trade_test_video_link ? explode('/', $post->trade_test_video_link) : null;

        $workPlaces = City::whereIn('id', explode(',', (string) $post->expcity_id))->get();
        $experienceCountries = Country::whereIn('id', explode(',', (string) $post->expcountry_id))->get();

        $relatedPosts = $this->availableCandidates()
            ->with('profession:id,eng_name,ar_name')
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

        $frontwebsite = DB::table('frontendwebsiteconfigs')->first();
        $webconfig = Websiteconfig::first();

        return view('worker.resumes.details', [
            'post' => $post,
            'videoId' => $videoId,
            'testVideoId' => $testVideoId,
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
