<?php

namespace App\Http\Controllers;

use App\Mail\SendOTPVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Associates;
use App\Models\AssociateAdminSaveFilter;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\Candidate;
use App\Models\Country;
use App\Models\City;
use App\Models\Metawhatsappapi;
use App\Models\Region;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AssociateController extends Controller
{
    public function index_old(){
        $country = Country::orderBy('name')->get();
        $city = City::orderBy('name')->get();
        $user = Admin::where('status',1)->orderBy('name')->get();
        $regions = Region::orderBy('name')->get();
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        return view('admin.associate.index',['countries' => $country,'cities' => $city,'users' => $user,'permission' => $permission,'regions' => $regions]);
    }

    public function index(Request $request){
        $country = Country::orderBy('name')->get();
        $city = City::orderBy('name')->get();
        $user = Admin::where('status',1)->orderBy('name')->get();
        $regions = Region::orderBy('name')->get();
        $userID = Auth::guard('admin')->user()->id;
        $permission = Adminpermission::where('staff_id','=',$userID)->first();
        $saveadminfilter = AssociateAdminSaveFilter::where('admin_id','=',$userID)->first();
        $filters = $this->resolveAssociateFilters($request, $saveadminfilter);

        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1) ) {

            $post = Associates::with(['careoff','city','country','region','admin']);

            $post->FilterSearchText($request->search_text);
            $post->FilterStatus($request->by_status);
            $post->FilterContactVerified($request->by_contact_verified);
            $post->FilterCountry($filters['country']);
            $post->FilterCity($filters['city']);
            $post->FilterRegion($filters['region']);
            $post->FilterCreatedBy($filters['created_by']);
            $post->FilterCareoff($filters['careoff']);


            $posts = $post->where('id','!=',73)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

            if ($request->ajax()) {
                return view('admin.associate.load',['posts' => $posts,'permission' => $permission])->render();
            }

            $associateStatusSummary = $this->buildAssociateStatusSummary($permission);

            return view('admin.associate.index',['posts' => $posts,'countries' => $country,'cities' => $city,'users' => $user,'permission' => $permission,'regions' => $regions,'associateStatusSummary' => $associateStatusSummary,'saveadminfilter' => $saveadminfilter]);

        }elseif (Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 1)) {
            if ($permission->view_associate == 1) {
                $post = Associates::with(['careoff','city','country','region','admin']);

                $post->FilterSearchText($request->search_text);
                $post->FilterStatus($request->by_status);
                $post->FilterContactVerified($request->by_contact_verified);
                $post->FilterCountry($filters['country']);
                $post->FilterCity($filters['city']);
                $post->FilterRegion($filters['region']);
                $post->FilterCreatedBy($filters['created_by']);
                $post->FilterCareoff($filters['careoff']);


                $posts = $post->where('id','!=',73)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                if ($request->ajax()) {
                    return view('admin.associate.load',['posts' => $posts,'permission' => $permission])->render();
                }

                $associateStatusSummary = $this->buildAssociateStatusSummary($permission);

                return view('admin.associate.index',['posts' => $posts,'countries' => $country,'cities' => $city,'users' => $user,'permission' => $permission,'regions' => $regions,'associateStatusSummary' => $associateStatusSummary,'saveadminfilter' => $saveadminfilter]);
            } else {
                $post = Associates::with(['careoff','city','country','region','admin']);

                $post->FilterSearchText($request->search_text);
                $post->FilterStatus($request->by_status);
                $post->FilterContactVerified($request->by_contact_verified);
                $post->FilterCountry($filters['country']);
                $post->FilterCity($filters['city']);
                $post->FilterRegion($filters['region']);
                $post->FilterCreatedBy($filters['created_by']);
                $post->FilterCareoff($filters['careoff']);

                $posts = $post->where('admin_id','=',$userID)->where('id','!=',73)->orderBy('id','DESC')->paginate($request->page_list ?? 10)->withQueryString();

                if ($request->ajax()) {
                    return view('admin.associate.load',['posts' => $posts,'permission' => $permission])->render();
                }

                $associateStatusSummary = $this->buildAssociateStatusSummary($permission);

                return view('admin.associate.index',['posts' => $posts,'countries' => $country,'cities' => $city,'users' => $user,'permission' => $permission,'regions' => $regions,'associateStatusSummary' => $associateStatusSummary,'saveadminfilter' => $saveadminfilter]);
            }

        }


    }

    /**
     * Resolve the effective values for the 5 saved-filter fields (Country,
     * City, Region, Created By, Care Of).
     *
     * Only a plain (non-AJAX) page load falls back to the admin's saved
     * filter when a field is absent from the request — every AJAX request
     * already sends the current, authoritative state of the filter panel
     * (including fields the user just cleared), so falling back there
     * would resurrect a filter right after the user cleared it. This is
     * the same "resolve once, never stack" principle used for Todo/Google
     * Review's saved-filter handling.
     */
    private function resolveAssociateFilters(Request $request, $saveadminfilter): array
    {
        $filters = [
            'country'    => $request->by_country,
            'city'       => $request->by_city,
            'region'     => $request->by_region,
            'created_by' => $request->by_created_by,
            'careoff'    => $request->by_careoff,
        ];

        if (!$request->ajax() && $saveadminfilter) {
            $filters['country']    = $filters['country']    ?? $saveadminfilter->by_country_array;
            $filters['city']       = $filters['city']       ?? $saveadminfilter->by_city_array;
            $filters['region']     = $filters['region']     ?? $saveadminfilter->by_region_array;
            $filters['created_by'] = $filters['created_by'] ?? $saveadminfilter->by_created_by_array;
            $filters['careoff']    = $filters['careoff']    ?? $saveadminfilter->by_careoff_array;
        }

        return $filters;
    }

    /**
     * Global (permission-scoped, not filter-scoped) Associate Status /
     * Contact Verification counts for the status bar — same "counts stay
     * fixed while the table filters" principle used by the Employer/
     * Todo/Booking summary cards. Two lightweight GROUP BY aggregates,
     * not one query per bucket. Scoped exactly like index()'s own list
     * query (full_access / view_associate / admin_id), always excluding
     * id 73 since every branch of index() does the same.
     */
    private function buildAssociateStatusSummary($permission): array
    {
        $user = Auth::guard('admin')->user();
        $isAdmin = $user->user_type == 1;

        $base = Associates::query()->where('id', '!=', 73);

        if (!$isAdmin && !(isset($permission) && $permission->full_access == 1)) {
            if (!isset($permission) || $permission->view_associate != 1) {
                $base->where('admin_id', '=', $user->id);
            }
        }

        $statusCounts = (clone $base)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $contactVerifiedCounts = (clone $base)
            ->select('contact_verified', DB::raw('COUNT(*) as total'))
            ->groupBy('contact_verified')
            ->pluck('total', 'contact_verified');

        return [
            'total' => (int) $statusCounts->sum(),
            'statuses' => [
                0 => (int) ($statusCounts[0] ?? 0),
                1 => (int) ($statusCounts[1] ?? 0),
            ],
            'contactVerified' => [
                0 => (int) ($contactVerifiedCounts[0] ?? 0),
                1 => (int) ($contactVerifiedCounts[1] ?? 0),
            ],
        ];
    }

    public function saveFilter(Request $request)
    {
        $adminId = Auth::guard('admin')->user()->id;

        $filter = AssociateAdminSaveFilter::firstOrNew(['admin_id' => $adminId]);

        $filter->by_country    = $request->by_country    ? implode(',', (array) $request->by_country)    : '';
        $filter->by_city       = $request->by_city       ? implode(',', (array) $request->by_city)       : '';
        $filter->by_region     = $request->by_region     ? implode(',', (array) $request->by_region)     : '';
        $filter->by_created_by = $request->by_created_by ? implode(',', (array) $request->by_created_by) : '';
        $filter->by_careoff    = $request->by_careoff    ? implode(',', (array) $request->by_careoff)    : '';

        $filter->save();

        return response()->json([
            'message' => $filter->wasRecentlyCreated ? 'Filter saved successfully!' : 'Filter updated successfully!'
        ]);
    }

    public function indexJson(Request $request){
        $post = DB::table('associates as associate')
        ->leftJoin('admins as careoff','careoff.id','=','associate.careoff_id')
        ->leftJoin('countries as country','country.id','=','associate.country_id')
        ->leftJoin('cities as city','city.id','=','associate.city_id')
        ->leftJoin('admins as admin','admin.id','=','associate.admin_id')
        ->where('associate.id','!=',73)
        ->select('associate.*','careoff.name as cofname','country.name as contname','city.name as citname','admin.name as uname')
        ->orderBy('associate.id','DESC')
        ->get();

        $data['data'] = $post;

        return response()->json($data);
    }

    public function store(Request $request){
        $latest_member_id = Associates::where('membership','!=','')->orderBy('membership','DESC')->first();
        $start_member_id = 1000;

        $post = new Associates();
        $post->pty_full_name = $request->name;
        $post->pty_ag_name = $request->pty_ag_name;
        $post->pty_email = $request->pty_email;
        $post->pty_mobile = $request->pty_mobile;
        $post->sec_mob_no = $request->sec_mob_no;
        $post->careoff_id = $request->careoff_id;
        $post->address = $request->address;
        $post->city_id = $request->city_id;
        $post->region_id = $request->region_id;
        $post->country_id = $request->country_id;
        $post->admin_id = Auth::guard('admin')->user()->id;
        // if (Auth::guard('admin')->user()->user_type == 1) {
        //     $post->contact_verified = true;
        //     $post->pty_mobile_verifed = true;
        //     $post->sec_mob_no_verified = true;
        //     $post->pty_email_verified = true;
        // }

        if ($latest_member_id) {
            $latest_membership = $latest_member_id->membership;
            $post->membership = $latest_membership + 1;
            $post->save();
        } else {
            $post->membership = $start_member_id;
            $post->save();
        }


        $post->save();

        return redirect()->back()->with('success','Associates added!');
    }

    public function edit(Request $request){
        $post = Associates::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request){
        $id = $request->edit_ID;

        $post = Associates::find($id);
        $post->pty_full_name = $request->name;
        $post->pty_ag_name = $request->pty_ag_name;
        $post->pty_email = $request->pty_email;
        $post->pty_mobile = $request->pty_mobile;
        $post->sec_mob_no = $request->sec_mob_no;
        $post->careoff_id = $request->careoff_id;
        $post->address = $request->address;
        $post->city_id = $request->city_id;
        $post->country_id = $request->country_id;
        $post->region_id = $request->region_id;

        // if (Auth::guard('admin')->user()->user_type == 1) {
        //     if ($post->contact_verified == false) {
        //         $post->pty_mobile_verifed = true;
        //         $post->sec_mob_no_verified = true;
        //         $post->pty_email_verified = true;
        //         $post->contact_verified = true;
        //     }
        // }

        $post->save();

        return redirect()->back()->with('success','Associate updated!');
    }

    public function show($id){
        $post = Associates::find($id);
        $adminpermission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();

        return view('admin.associate.show',compact('post','adminpermission'));
    }


    public function primary_getotp(Request $request){
        $mobile_no = $request->mobile_no;
        // $metaAPI = Metawhatsappapi::where('status',1)->first();
        $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['otp'])->first();

        $otp = mt_rand(1000,9999);

        if (isset($metaAPI)) {
            $request->session()->put('OTP', $otp);
            $request->session()->put('Mobile', $mobile_no);

            // API Details
            $base_url = $metaAPI->api_base_url;
            $vendor_id = $metaAPI->vendor_uid;
            $access_token = $metaAPI->api_access_token;
            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';

            $token = "Authorization: Bearer ".$access_token;

            // Send OTP to Mobile Number

            $data = [];
            $data['phone_number'] = $mobile_no;
            $data['template_name'] = "otpone";
            $data['template_language'] = "en";
            $data['field_1'] = $otp;
            $data['button_0'] = $otp;
            $data['contact'] =  [
                'first_name' => "Customer",
                'last_name' => "--",
                "email" => "customer@gmail.com",
                "country" => "india",
                "language_code" => "en"
            ];

            // Send for OTP

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                CURLOPT_URL => $endpoint_api,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data)
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $responseGet = json_decode($response);

            if (isset($responseGet->errors) || $responseGet->result == 'failed') {
                $responseData = [
                    'status' => '0',
                    'response_msg' => $responseGet->message,
                    'data' => $data
                ];
            }else{
                $responseData = [
                    'status' => '1',
                    'response_msg' => 'OTP has been send!',
                    'data' => $data
                ];
            }

        } else {
            $responseData = [
                'status' => '0',
                'response_msg' => 'Whatsapp API is Not Connected',
            ];
        }

        return response()->json($responseData);

    }

    public function secondary_getotp(Request $request){
        $mobile_no = $request->mobile_no;
        // $metaAPI = Metawhatsappapi::where('status',1)->first();
        $metaAPI = Metawhatsappapi::whereRaw("FIND_IN_SET (?,api_assign_to)",['otp'])->first();


        $otp = mt_rand(1000,9999);

        if (isset($metaAPI)) {
            $request->session()->put('OTP', $otp);
            $request->session()->put('Mobile', $mobile_no);

            // API Details
            $base_url = $metaAPI->api_base_url;
            $vendor_id = $metaAPI->vendor_uid;
            $access_token = $metaAPI->api_access_token;
            $endpoint_api = $base_url.'/'.$vendor_id.'/contact/send-template-message';

            $token = "Authorization: Bearer ".$access_token;

            // Send OTP to Mobile Number

            $data = [];
            $data['phone_number'] = $mobile_no;
            $data['template_name'] = "otpone";
            $data['template_language'] = "en";
            $data['field_1'] = $otp;
            $data['button_0'] = $otp;
            $data['contact'] =  [
                'first_name' => "Customer",
                'last_name' => "--",
                "email" => "customer@gmail.com",
                "country" => "india",
                "language_code" => "en"
            ];

            // Send for OTP

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_HTTPHEADER => array('Content-Type: application/json',$token),
                CURLOPT_URL => $endpoint_api,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data)
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $responseGet = json_decode($response);

            if (isset($responseGet->errors) || $responseGet->result == 'failed') {
                $responseData = [
                    'status' => '0',
                    'response_msg' => $responseGet->message,
                    'data' => $data
                ];
            }else{
                $responseData = [
                    'status' => '1',
                    'response_msg' => 'OTP has been send!',
                    'data' => $data
                ];
            }

        } else {
            $responseData = [
                'status' => '0',
                'response_msg' => 'Whatsapp API is Not Connected',
            ];
        }

        return response()->json($responseData);

    }

    public function email_getotp(Request $request){
        $email = $request->email;


        $otp = mt_rand(1000,9999);

        $data = [
            'name' => $email,
            'EmailOtp' => $otp
        ];

        $request->session()->put('OTP', $otp);
        $request->session()->put('Mobile', $email);

        // Send OTP to Mobile Number

        try {
            Mail::to($data['name'])->send(New SendOTPVerification($data));
            $responseData = [
                'status' => '1',
                'response_msg' => 'Email Send!',
                'data' => $data
            ];
        } catch (\Throwable $th) {
            $responseData = [
                'status' => '0',
                'response_msg' => 'Email Not Send',
            ];
        }


        return response()->json($responseData);

    }

    public function primary_otp_validate(Request $request){
        $otp_number = $request->pty_mobile_otp;
        $session_otp = $request->session()->get('OTP');

        if ($otp_number == $session_otp) {
            $isAvailable = 'true';
        } else {
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));

    }

    public function secondary_otp_validate(Request $request){
        $otp_number = $request->sec_mob_no_otp;
        $session_otp = $request->session()->get('OTP');

        if ($otp_number == $session_otp) {
            $isAvailable = 'true';
        } else {
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));

    }

    public function email_otp_validate(Request $request){
        $otp_number = $request->pty_email_otp;
        $session_otp = $request->session()->get('OTP');

        if ($otp_number == $session_otp) {
            $isAvailable = 'true';
        } else {
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));

    }

    public function primmobverif($id,Request $request){
        $post = Associates::find($id);

        if ($request->primary_mobile_no != '') {
            $post->pty_mobile = $request->primary_mobile_no;
            $post->pty_mobile_verifed = true;
            $post->contact_verified = true;
            $post->status = true;



        }





        // if ($post->pty_email_verified == true) {
        //     $post->contact_verified = true;
        // }



        $post->save();

        return redirect()->back()->with('success','Primary Mobile No Verified!');

    }

    public function secmobverif($id,Request $request){
        $post = Associates::find($id);

        if ($request->secondary_mobile_no != '') {
            $post->sec_mob_no = $request->secondary_mobile_no;
            $post->sec_mob_no_verified = true;

            if ($post->pty_mobile_verifed == true) {
                $post->status = true;
                $post->contact_verified = true;
            }
        }



        $post->save();

        return redirect()->back()->with('success','Secondary Mobile No Verified!');

    }

    public function emailverif($id,Request $request){
        $post = Associates::find($id);

        if ($request->pty_email != '') {
            $post->pty_email = $request->pty_email;
            $post->pty_email_verified = true;

            if ($post->pty_mobile_verifed == true) {
                $post->contact_verified = true;
            }
        }



        $post->save();

        return redirect()->back()->with('success','Email Verified!');

    }

    public function generate_memberid($id){
        $post = Associates::find($id);

        // Get Latest Member ID
        $latest_member_id = Associates::where('membership','!=','')->orderBy('membership','DESC')->first();

        $start_member_id = 1000;

        if ($latest_member_id) {
            $latest_membership = $latest_member_id->membership;
            $post->membership = $latest_membership + 1;
            $post->save();
        } else {
            $post->membership = $start_member_id;
            $post->save();
        }

        // dd($latest_membership);

        return redirect()->back()->with('success','Membership is allocated successfully!');


    }


    public function checkemail(Request $request) {

        if($request->id != ''){
            $post = Associates::where('pty_email','=',$request->pty_email)->where('id','!=',$request->id)->count();

            if($post == 0){
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }else{
            $post = Associates::where('pty_email','=',$request->pty_email)->count();

            if($post == 0){
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }


    }

    public function checkassoccand(Request $request){
        $post = Candidate::where('associate_id','=',$request->id)->count();

        if($post > 0){
            return response()->json('1');
        }else{

        }

    }

    public function checkmobile(Request $request){


        if($request->id != ''){
            $post = Associates::where('pty_mobile','=',$request->pty_mobile)->where('id','!=',$request->id)->count();

            if($post == 0){
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }else{
            $post = Associates::where('pty_mobile','=',$request->pty_mobile)->count();

            if($post == 0){
                $isAvailable = 'true';
            }else{
                $isAvailable = 'false';
            }

            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }

    }

    public function delete(Request $request){
        $post = Associates::find($request->assoc_id);
        $post->delete();
        return redirect()->back()->with('success','Associate deleted!');
    }

    public function deactiveStatus(Request $request){
        $post = Associates::find($request->id);

        $post->status = false;
        $post->save();

        return redirect()->back()->with('success','Status deactivated!');
    }

    public function activeStatus(Request $request){
        $post = Associates::find($request->id);

        $post->status = true;
        $post->save();

        return redirect()->back()->with('success','Status activated!');
    }

    public function showinactivestatus($id){
        $post = Associates::find($id);
        $post->status = false;
        $post->save();

        return redirect()->back()->with('success','Status inactive successfully!');
    }

    public function showactivestatus($id){
        $post = Associates::find($id);
        $post->status = true;
        $post->save();

        return redirect()->back()->with('success','Status active successfully!');
    }


    public function shownotverifiedcontact($id){
        $post = Associates::find($id);
        $post->contact_verified = false;
        $post->save();

        return redirect()->back()->with('success','Contact Not Verify successfully!');
    }

    public function showverifiedcontact($id){
        $post = Associates::find($id);
        $post->contact_verified = true;
        $post->save();

        return redirect()->back()->with('success','Contact Verify successfully!');
    }
}
