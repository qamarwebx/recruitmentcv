<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\City;
use App\Models\Country;
use App\Models\Cvsetting;
use App\Models\Partner;
use App\Models\Partnercvsetting;
use App\Models\Partnerfiliterlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Basepathstatus;
use App\Models\Booking;
use App\Models\Bookingpayment;
use App\Models\Visadetails;
use App\Models\Admin;
use App\Models\Profession;
use App\Models\Partnerservicecharge;
use App\Models\Domain;



class PartnerController extends Controller
{
    public function index()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        $countries = Country::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        $filter_user = Partnerfiliterlist::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

        // Employer ,City and Booking Status list Country List
        $country = DB::table('partners')->select('country_id')->groupBy('country_id')->where('country_id','!=','')->get();
        $city = DB::table('partners')->select('city_id')->groupBy('city_id')->where('city_id','!=','')->get();
        $status = DB::table('partners')->select('status')->groupBy('status')->get();

        $partnerStatusSummary = $this->getPartnerStatusSummary($permission);

        return view('admin.partner.index',['countries' => $countries,'filter_user' => $filter_user,'statusfs' => $status,'cities' => $cities,'perm' => $permission,'countryfs' => $country,'cityfs' => $city,'partnerStatusSummary' => $partnerStatusSummary]);
    }

    // Mirrors indexjson()'s own visibility rules (full access / view_partner
    // sees every partner, otherwise only the ones this admin owns) so the
    // status bar's counts always match what the list itself actually shows.
    private function getPartnerStatusSummary($permission)
    {
        $query = DB::table('partners');

        $isFullAccess = Auth::guard('admin')->user()->user_type == 1
            || (isset($permission) && $permission->full_access == 1)
            || (isset($permission) && $permission->view_partner == 1);

        if (!$isFullAccess) {
            $query->where('admin_id', '=', Auth::guard('admin')->user()->id);
        }

        $total = (clone $query)->count();

        $statusCounts = (clone $query)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        $portalStatusCounts = (clone $query)->select('portal_status', DB::raw('count(*) as total'))
            ->groupBy('portal_status')->pluck('total', 'portal_status');

        $registrationStatusCounts = (clone $query)->select('registration_status', DB::raw('count(*) as total'))
            ->groupBy('registration_status')->pluck('total', 'registration_status');

        return [
            'total' => $total,
            'status' => [
                'active' => $statusCounts[1] ?? 0,
                'inactive' => $statusCounts[0] ?? 0,
            ],
            'portal_status' => [
                'active' => $portalStatusCounts[1] ?? 0,
                'inactive' => $portalStatusCounts[0] ?? 0,
            ],
            'registration_status' => [
                'pending' => $registrationStatusCounts[0] ?? 0,
                'approved' => $registrationStatusCounts[1] ?? 0,
                'rejected' => $registrationStatusCounts[2] ?? 0,
            ],
        ];
    }

    public function indexjson(Request $request)
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $posts = DB::table('partners as partner')
            ->leftJoin('countries as country','partner.country_id','=','country.id')
            ->leftJoin('cities as city','partner.city_id','=','city.id')
            ->select('partner.*','country.name as country','city.name as city')
            ->orderBy('id','DESC')
            ->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_partner == 1){
                $posts = DB::table('partners as partner')
                ->leftJoin('countries as country','partner.country_id','=','country.id')
                ->leftJoin('cities as city','partner.city_id','=','city.id')
                ->select('partner.*','country.name as country','city.name as city')
                ->orderBy('id','DESC')
                ->get();
            }else{
                $posts = DB::table('partners as partner')
                ->leftJoin('countries as country','partner.country_id','=','country.id')
                ->leftJoin('cities as city','partner.city_id','=','city.id')
                ->select('partner.*','country.name as country','city.name as city')
                ->where('partner.admin_id','=',Auth::guard('admin')->user()->id)
                ->orderBy('id','DESC')
                ->get();
            }
        }

        // $posts = Partner::orderBy('id','DESC')->get();


        $data['data'] = $posts;
        return response()->json($data);
    }

    public function store(Request $request)
    {

        $post = new Partner();
        $post->rec_off_name = $request->rec_off_name;
        $post->user_type = '1';
        $post->rec_office_arname = $request->rec_office_arname;
        $post->owner_name = $request->owner_name;
        $post->owner_mobile_no = $request->owner_mobile_no;
        $post->city_id = $request->city_id;
        $post->country_id = $request->country_id;
        $post->primary_email = $request->primary_email;
        $post->secondary_email = $request->secondary_email;
        $post->office_no = $request->office_no;
        $post->primary_mob = $request->primary_mob;
        $post->secondary_mob = $request->secondary_mob;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->status = false;
        $post->customer_no = $request->customer_no;
        $post->save();

        $createdPartnerId = $post->id;

        $post = Partner::find($createdPartnerId);
        $post->partner_id = $createdPartnerId;// Ensure it's unique
        $post->save();

        return redirect()->back();
    }

    public function editP(Request $request)
    {
        $post = Partner::find($request->id);
        return response()->json($post);
    }

    public function update(Request $request)
    {
        $post = Partner::find($request->edit_id);
        $post->rec_off_name = $request->rec_off_name;
        $post->rec_office_arname = $request->rec_office_arname;
        $post->owner_name = $request->owner_name;
        $post->owner_mobile_no = $request->owner_mobile_no;
        $post->city_id = $request->city_id;
        $post->country_id = $request->country_id;
        $post->primary_email = $request->primary_email;
        $post->secondary_email = $request->secondary_email;
        $post->office_no = $request->office_no;
        $post->primary_mob = $request->primary_mob;
        $post->secondary_mob = $request->secondary_mob;
        $post->user_type = '1';
        $post->partner_id = $request->edit_id;
        $post->customer_no = $request->customer_no;
        $post->save();

        return redirect()->back();

    }

    public function show($id)
    {
        $post = DB::table('partners as partner')
            // ->leftjoin('partnercreds as login','partner.id','=','login.partner_id')
            ->leftJoin('countries as country','partner.country_id','=','country.id')
            ->leftJoin('cities as city','partner.city_id','=','city.id')
            ->select('partner.*')
            ->where('partner.id','=',$id)
            ->first();

        $countries = Country::orderBy('name','ASC')->get();
        $cities = City::orderBy('name','ASC')->get();

        // image1
        $image1 = Partnercvsetting::where('partner_id','=',$id)->where('db_field_name','=','image1')->first();
        $image2 = Partnercvsetting::where('partner_id','=',$id)->where('db_field_name','=','image2')->first();

        $users = Admin::where('status',1)->orderBy('name')->get();
        $profession = Profession::orderBy('eng_name')->get();

        $partnerScs = Partnerservicecharge::where('partner_id','=',$id)->get();

        $partnerDomain = Domain::where('partner_id','=',$id)->first();

        return view('admin.partner.show',['post' => $post,'partnerId' => $id,'partnerDomain' => $partnerDomain,'partnerScs' => $partnerScs,'professions' =>  $profession,'users' => $users,'countries' => $countries,'cities' => $cities,'image1' => $image1,'image2' => $image2]);
    }

    public function addsercharge(Request $request){
        try {
            // Check Previous same service charge is active
            $prevCheckSc = Partnerservicecharge::where('partner_id','=',$request->partner_id)->where('profession_id','=',$request->profession_id)->where('status',1)->get();

            // Disable That previously active
            if ($prevCheckSc->count() > 0) {
                foreach ($prevCheckSc as $prevCheckS) {
                    $prevCheckS->status = false;
                    $prevCheckS->updateby_id = Auth::guard('admin')->user()->id;
                    $prevCheckS->save();
                }
            }



            $post = new Partnerservicecharge();
            $post->partner_id = $request->partner_id;
            $post->givenby_id = $request->givenby_id;
            $post->service_charge = $request->service_charge;
            $post->profession_id = $request->profession_id;
            $post->notes = $request->notes;
            $post->createby_id = Auth::guard('admin')->user()->id;
            if (Auth::guard('admin')->user()->user_type == 2) {
                $post->status = false;
            }

            $post->save();

            // Update Partner status

            $partner = Partner::find($request->partner_id);
            $partner->status = true;
            $partner->portal_status = true;
            $partner->save();

            $data = [
                'status' => '1',
                'respnseMsg' => "Service Charge added successfully!"
            ];
        } catch (\Throwable $th) {
            $data = [
                'status' => '0',
                'respnseMsg' => $th->getMessage()
            ];
        }


        return response()->json($data);


    }

    public function updateStatusSc(Request $request){
        try {
            $post = Partnerservicecharge::find($request->sc_id);

            if ($request->sc_status == 1) {

                $checkPrevSc = Partnerservicecharge::where('id','!=',$request->sc_id)->where('partner_id','=',$post->partner_id)->where('profession_id','=',$post->profession_id)->where('status',1)->get();

                // Deactive Previous
                if ($checkPrevSc->count() > 0) {
                    foreach ($checkPrevSc as $prevCheckS) {
                        $prevCheckS->status = false;
                        $prevCheckS->updateby_id = Auth::guard('admin')->user()->id;
                        $prevCheckS->save();
                    }
                }

                // Active Request
                $post->status = true;
                $post->save();

                $data = [
                    'status' => '1',
                    'respnseMsg' => "Service Charge active successfully"
                ];
            } else {

                $checkPrevSc = Partnerservicecharge::where('id','!=',$request->sc_id)->where('partner_id','=',$post->partner_id)->where('profession_id','=',$post->profession_id)->where('status',0)->first();

                if ($checkPrevSc) {
                    $checkPrevSc->status = true;
                    $checkPrevSc->updateby_id = Auth::guard('admin')->user()->id;
                    $checkPrevSc->save();
                }else{
                    // Inactive Partnr
                    $partner = Partner::find($post->partner_id);
                    $partner->status = false;
                    $partner->portal_status = false;
                    $partner->save();
                }

                // Active Request
                $post->status = false;
                $post->save();

                $data = [
                    'status' => '1',
                    'respnseMsg' => "Service Charge inactive successfully"
                ];
            }

        } catch (\Throwable $th) {
            $data = [
                'status' => '0',
                'respnseMsg' => $th->getMessage()
            ];
        }



        // return response()->json($data);

        return redirect()->back()->with('success',$data['respnseMsg']);
    }

    public function  updatesercharge(Request $request) {
        try {
            // Check Previous same service charge is active
            $prevCheckSc = Partnerservicecharge::where('id','!=',$request->sc_id)->where('partner_id','=',$request->partner_id)->where('profession_id','=',$request->profession_id)->where('status',1)->get();

            // Disable That previously active
            if ($prevCheckSc->count() > 0) {
                foreach ($prevCheckSc as $prevCheckS) {
                    $prevCheckS->status = false;
                    $prevCheckS->updateby_id = Auth::guard('admin')->user()->id;
                    $prevCheckS->save();
                }
            }


            $post = Partnerservicecharge::find($request->sc_id);
            $post->givenby_id = $request->givenby_id;
            $post->service_charge = $request->service_charge;
            $post->profession_id = $request->profession_id;
            $post->notes = $request->notes;
            $post->updateby_id = Auth::guard('admin')->user()->id;

            $post->save();

            // Update Partner status

            $partner = Partner::find($request->partner_id);
            $partner->status = true;
            $partner->portal_status = true;
            $partner->save();

            $data = [
                'status' => '1',
                'respnseMsg' => "Service Charge added successfully!"
            ];
        } catch (\Throwable $th) {
            $data = [
                'status' => '0',
                'respnseMsg' => $th->getMessage()
            ];
        }


        return response()->json($data);
    }

    public function editsercharge(Request $request){
        $post = Partnerservicecharge::find($request->id);

        return response()->json($post);
    }

    public function checkpartnerexists(Request $request){
        $booking = Booking::where('partner_id','=',$request->id)->count();
        $bookingpayment = Bookingpayment::where('partner_id','=',$request->id)->count();
        $visadetail = Visadetails::where('partner_office_id','=',$request->id)->count();

        if ($booking > 0 || $bookingpayment > 0 || $visadetail > 0) {
            return response()->json('1');
        } else {

        }


    }

    public function deletePartner(Request $request) {
        $post = Partner::find($request->partner_id);

        $post->delete();

        return redirect()->back()->with('success','Partner deleted!');

    }

    public function websitelogoupdt(Request $request,$id){
        $post = Partner::find($id);
        $basepathstatus = Basepathstatus::first();
        // upload photo files
        if ($request->hasFile('website_logo')) {
            $file = $request->file('website_logo');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren1);
            $new_file1 = $repspfilename.'.'.$fileext_ren1;

            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/partner/',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/partner/',$new_file1);
            }


            $website_logo = $new_file1;
        }else{
            $website_logo = $post->website_logo;
        }

        $post->website_logo = $website_logo;
        $post->save();

        return redirect()->back()->with('success','Partner website logo updated!');
    }

    public function checkprimemail(Request $request)
    {
        $primary_email = $request->primary_email;
        $post = Partner::where('primary_email','=',$primary_email)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function checkocn(Request $request)
    {
        $owner_mobile_no = $request->owner_mobile_no;
        $post = Partner::where('owner_mobile_no','=',$owner_mobile_no)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckprimemail(Request $request)
    {
        $primary_email = $request->primary_email;
        $id = $request->id;
        $post = Partner::where('primary_email','=',$primary_email)->where('id','!=',$id)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edsecondaryemail(Request $request)
    {
        $email = $request->secondary_email;
        $id = $request->id;

        $post = Partner::where('secondary_email','=',$email)->where('id','!=',$id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckofficeno(Request $request)
    {
        $officeno = $request->office_no;
        $id = $request->id;
        $post = Partner::where('office_no','=',$officeno)->where('id','!=',$id)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckprimaryno(Request $request)
    {
        $mob_no = $request->primary_mob;
        $id = $request->id;

        $post = Partner::where('primary_mob','=',$mob_no)->where('id','!=',$id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edchecksecondaryno(Request $request)
    {
        $mob_no = $request->secondary_mob;
        $id = $request->id;

        $post = Partner::where('secondary_mob','=',$mob_no)->where('id','!=',$id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckocn(Request $request)
    {
        $owner_mobile_no = $request->owner_mobile_no;
        $id = $request->id;
        $post = Partner::where('owner_mobile_no','=',$owner_mobile_no)->where('id','!=',$id)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckemail(Request $request)
    {
        $email = $request->email;
        $id = $request->id;
        $post = Partner::where('email','=',$email)->where('id','!=',$id)->count();

        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckusername(Request $request)
    {
        $username = $request->username;
        $id = $request->id;

        $post = Partner::where('username','=',$username)->where('id','!=',$id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }


    public function accountUpdate(Request $request,$id)
    {
        $post = Partner::find($id);

        $basepathstatus = Basepathstatus::first();

        // upload photo files
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren1);
            $new_file1 = $repspfilename.'.'.$fileext_ren1;

            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/images/partner/',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/images/partner/',$new_file1);
            }


            $profile_photo = $new_file1;
        }else{
            $profile_photo = $post->logo;
        }

        $post->owner_name = $request->owner_name;
        $post->email = $request->email;
        $post->username = $request->username;
        $post->user_type = '1';
        $post->owner_mobile_no = $request->owner_mobile_no;
        $post->partner_id = $id;
        $post->logo = $profile_photo;
        $post->save();

        return redirect()->back()->with('success','Acount detail updated!');


    }

    public function accountUpdate2(Request $request,$id)
    {
        $post = Partner::find($id);
        $basepathstatus = Basepathstatus::first();

        // upload photo files
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $name = $file->getClientOriginalName();
            // remove space from image
            $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
            $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
            $repspfilename = str_replace(" ","_",$filename_ren1);
            $new_file1 = $repspfilename.'.'.$fileext_ren1;
            if($basepathstatus->base_path_status == 1){
	            $file->move(base_path().'/public/admin/assets/img/avatars/',$new_file1);
            }else{
                $file->move(base_path().'/public_html/admin/assets/img/avatars/',$new_file1);
            }
            $profile_photo = $new_file1;
        }else{
            $profile_photo = $post->profile;
        }

        $post->owner_name = $request->owner_name;
        // $post->email = $request->email;
        $post->username = $request->username;
        $post->user_type = '1';
        $post->owner_mobile_no = $request->owner_mobile_no;
        $post->partner_id = $id;
        $post->profile = $profile_photo;
        $post->save();

        return redirect()->back()->with('success','Acount detail updated!');


    }

    public function edcheckpassword(Request $request)
    {
        $password = $request->currentPassword;
        $id = $request->id;

        $post = Partner::find($id);
        if ($post->password != '') {
            if (Hash::check($password,$post->password)) {
                $isAvailable = 'true';
            } else {
                $isAvailable = 'false';
            }
            echo json_encode(array(
                'valid' => $isAvailable,
            ));
        }else{
            echo json_encode(array(
                'valid' => 'true',
            ));
        }

    }

    public function personaUpdate(Request $request,$id)
    {
        $post = Partner::find($id);
        $post->rec_off_name = $request->rec_off_name;
        $post->rec_office_arname = $request->rec_office_arname;
        $post->country_id = $request->country_id;
        $post->city_id = $request->city_id;
        $post->primary_email = $request->primary_email;
        $post->secondary_email = $request->secondary_email;
        $post->office_no = $request->office_no;
        $post->primary_mob = $request->primary_mob;
        $post->secondary_mob = $request->secondary_mob;
        $post->info_eng_address = $request->info_eng_address;
        $post->info_ar_address = $request->info_ar_address;
        $post->partner_id = $id;
        $post->save();

        return redirect()->back()->with('success','Personal details updated!');
    }

    public function passwordUpdate(Request $request,$id)
    {
        $post = Partner::find($id);
        $post->password = Hash::make($request->newPassword);
        $post->save();

        return redirect()->back()->with('success','Password updated!');
    }

    public function sharecvUpdate(Request $request,$id){
        $post = Partner::find($id);
        $post->sharecv_per_name1 = $request->sharecv_per_name1;
        $post->sharecv_per_name2 = $request->sharecv_per_name2;
        $post->sharecv_per_name3 = $request->sharecv_per_name3;
        $post->sharecv_per_name4 = $request->sharecv_per_name4;
        $post->sharecv_per_mobile1= $request->sharecv_per_mobile1;
        $post->sharecv_per_mobile2 = $request->sharecv_per_mobile2;
        $post->sharecv_per_mobile4	 = $request->sharecv_per_mobile4;
        $post->sharecv_per_mobile3= $request->sharecv_per_mobile3;
        $post->save();

        return redirect()->back()->with('success','ShareCV details updated!');
    }

    public function portalUpdate(Request $request,$id)
    {
        $post = Partner::find($id);
        $post->portal_rec_off_name = $request->portal_rec_off_name;
        $post->portal_rec_off_arname = $request->portal_rec_off_arname;
        $post->portal_cons_per_name = $request->portal_cons_per_name;
        $post->portal_email = $request->portal_email;
        $post->portal_consern_person_name1= $request->portal_consern_person_name1;
        $post->portal_mobile_no_1 = $request->portal_mobile_no_1;
        $post->portal_mobile_no_1_text = $request->portal_mobile_no_1_text;
        $post->portal_consern_person_name2= $request->portal_consern_person_name2;
        $post->portal_mobile_no_2 = $request->portal_mobile_no_2;
        $post->portal_mobile_no_2_text = $request->portal_mobile_no_2_text;
        $post->portal_consern_person_name3= $request->portal_consern_person_name3;
        $post->portal_mobile_no_3 = $request->portal_mobile_no_3;
        $post->portal_mobile_no_3_text = $request->portal_mobile_no_3_text;
        $post->portal_consern_person_name4= $request->portal_consern_person_name4;
        $post->portal_mobile_no_4 = $request->portal_mobile_no_4;
        $post->portal_mobile_no_4_text = $request->portal_mobile_no_4_text;
        $post->portal_add_for_cust = $request->portal_add_for_cust;
        $post->portal_add_disp_only = $request->portal_add_disp_only;
        $post->portal_ar_add_disp_only = $request->portal_ar_add_disp_only;
        $post->portal_status = $request->portal_status;
        $post->partner_calling_number = $request->partner_calling_number;
        $post->partner_whatsapp_number = $request->partner_whatsapp_number;
        $post->partner_careoff_id = $request->partner_careoff_id;
        $post->save();

        return redirect()->back()->with('success','Portal details updated!');
    }


    public function updateFilterList(Request $request){
        $post = Partnerfiliterlist::where('admin_id','=',Auth::guard('admin')->user()->id)->count();

        if ($post > 0) {
            $updateF = Partnerfiliterlist::where('admin_id','=',Auth::guard('admin')->user()->id)->first();

            if($request->countryf == 1){
				$updateF->country_filter = true;
			}else{
				$updateF->country_filter = false;
			}

            if($request->cityf == 1){
				$updateF->city_filter = true;
			}else{
				$updateF->city_filter = false;
			}

            if($request->statusf == 1){
				$updateF->status_filter = true;
			}else{
				$updateF->status_filter = false;
			}

            if($request->created_datef == 1){
				$updateF->created_date_filter = true;
			}else{
				$updateF->created_date_filter = false;
			}

            $updateF->save();
    		return response()->json('success');

        } else {
            $newFilter = new Partnerfiliterlist();

            $newFilter->admin_id = Auth::guard('admin')->user()->id;

            if($request->countryf == 1){
				$newFilter->country_filter = true;
			}else{
				$newFilter->country_filter = false;
			}

            if($request->cityf == 1){
				$newFilter->city_filter = true;
			}else{
				$newFilter->city_filter = false;
			}

            if($request->statusf == 1){
				$newFilter->status_filter = true;
			}else{
				$newFilter->status_filter = false;
			}

            if($request->created_datef == 1){
				$newFilter->created_date_filter = true;
			}else{
				$newFilter->created_date_filter = false;
			}

            $newFilter->save();

            return response()->json('success');
        }
    }

     public function cvsettingupdte(Request $request,$id){
        $basepathstatus = Basepathstatus::first();
        try {

            if ($request->pcvsetting1 != '') {
                $updateimg1 = Partnercvsetting::find($request->pcvsetting1);

                if($request->hasFile('image1')){
                    $file = $request->file('image1');
                    $name = $file->getClientOriginalName();
                    // remove space from image
                    $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
                    $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
                    $repspfilename = str_replace(" ","_",$filename_ren1);
                    $new_file1 = $repspfilename.'.'.$fileext_ren1;
                    if($basepathstatus->base_path_status == 1){
	                    $file->move(base_path().'/public/admin/assets/images/cv_setting/',$new_file1);
                    }else{
                        $file->move(base_path().'/public_html/admin/assets/images/cv_setting/',$new_file1);
                    }

                    $imag1photo = $new_file1;
                }else{
                    $imag1photo = $updateimg1->filename;
                }

                $updateimg1->x_axis = $request->x_axis1;
                $updateimg1->y_axis = $request->y_axis1;
                $updateimg1->filename = $imag1photo;
                $updateimg1->width = $request->width1;
                if($request->status1 != ''){
                    $updateimg1->status = $request->status1;
                }
                $updateimg1->save();
            }else{
                $postimg1 = new Partnercvsetting();


                if($request->hasFile('image1')){
                    $file = $request->file('image1');
                    $name = $file->getClientOriginalName();
                    // remove space from image
                    $filename_ren1 = pathinfo($name,PATHINFO_FILENAME);
                    $fileext_ren1 = pathinfo($name,PATHINFO_EXTENSION);
                    $repspfilename1 = str_replace(" ","_",$filename_ren1);
                    $new_file1 = $repspfilename1.'.'.$fileext_ren1;
                    if($basepathstatus->base_path_status == 1){
	                    $file->move(base_path().'/public/admin/assets/images/cv_setting/',$new_file1);
                    }else{
                        $file->move(base_path().'/public_html/admin/assets/images/cv_setting/',$new_file1);
                    }
                    $imag1photo = $new_file1;
                }else{
                    $imag1photo = "";
                }



                $postimg1->label_name = 'Image1';
                $postimg1->db_field_name = 'image1';
                $postimg1->x_axis = $request->x_axis1;
                $postimg1->y_axis = $request->y_axis1;
                $postimg1->filename = $imag1photo;
                $postimg1->width = $request->width1;
                $postimg1->partner_id = $id;
                if($request->status1 != ''){
                    $postimg1->status = $request->status1;
                }
                $postimg1->admin_id = Auth::guard('admin')->user()->id;
                $postimg1->save();


            }

            if($request->pcvsetting2 != ''){
                $updateimg2 = Partnercvsetting::find($request->pcvsetting2);

                if($request->hasFile('image2')){
                    $file = $request->file('image2');
                    $name2 = $file->getClientOriginalName();
                    // remove space from image
                    $filename_ren1 = pathinfo($name2,PATHINFO_FILENAME);
                    $fileext_ren1 = pathinfo($name2,PATHINFO_EXTENSION);
                    $repspfilename2 = str_replace(" ","_",$filename_ren1);
                    $new_file2 = $repspfilename2.'.'.$fileext_ren1;
                    if($basepathstatus->base_path_status == 1){
	                    $file->move(base_path().'/public/admin/assets/images/cv_setting/',$new_file2);
                    }else{
                        $file->move(base_path().'/public_html/admin/assets/images/cv_setting/',$new_file2);
                    }
                    $imag2photo = $new_file2;
                }else{
                    $imag2photo = $updateimg2->filename;
                }

                $updateimg2->x_axis = $request->x_axis2;
                $updateimg2->y_axis = $request->y_axis2;
                $updateimg2->filename = $imag2photo;
                $updateimg2->width = $request->width2;
                if($request->status2 != ''){
                    $updateimg2->status = $request->status2;
                }
                $updateimg2->save();

            }else{
                $postimg2 = new Partnercvsetting();

                if($request->hasFile('image2')){
                    $file = $request->file('image2');
                    $name2 = $file->getClientOriginalName();
                    // remove space from image
                    $filename_ren2 = pathinfo($name2,PATHINFO_FILENAME);
                    $fileext_ren2 = pathinfo($name2,PATHINFO_EXTENSION);
                    $repspfilename2 = str_replace(" ","_",$filename_ren2);
                    $new_file2 = $repspfilename2.'.'.$fileext_ren2;
                    if($basepathstatus->base_path_status == 1){
	                    $file->move(base_path().'/public/admin/assets/images/cv_setting/',$new_file2);
                    }else{
                        $file->move(base_path().'/public_html/admin/assets/images/cv_setting/',$new_file2);
                    }

                    $imag2photo = $new_file2;
                }else{
                    $imag2photo = "";
                }

                $postimg2->label_name = 'Image2';
                $postimg2->db_field_name = 'image2';
                $postimg2->x_axis = $request->x_axis2;
                $postimg2->y_axis = $request->y_axis2;
                $postimg2->filename = $imag2photo;
                $postimg2->width = $request->width2;
                $postimg2->partner_id = $id;
                if($request->status2 != ''){
                    $postimg2->status = $request->status2;
                }
                $postimg2->admin_id = Auth::guard('admin')->user()->id;
                $postimg2->save();
            }


            return redirect()->back()->with('success','CV image setting updated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('cverror','Something went wrong! please check details before submit!');
        }
    }

    public function getData(Request $request){
        $post = Partner::find($request->id);

        return response()->json($post);
    }

    public function statusUpdate(Request $request){
        $post = Partner::find($request->partner_id);
        $post->status = $request->status;
        $post->save();

        return redirect()->back()->with('success','Status updated!');
    }

    public function portalstatusUpdate(Request $request){
        $post = Partner::find($request->partner_id);
        $post->portal_status = $request->portal_status;
        $post->save();

        return redirect()->back()->with('success','Portal Status updated!');
    }

    // registration_status: 0 = Pending, 1 = Approved, 2 = Rejected.
    // Gates login on the Worker portal's phone+OTP partner flow.
    public function registrationStatusUpdate(Request $request){
        $post = Partner::find($request->partner_id);

        // Approving needs a Recruitment Licence Number (partners.licence_number;
        // whitespace-only counts as empty). Checked only when changing to
        // Approved - Pending / Rejected, and an already-approved partner, are
        // unaffected. Nothing is saved when blocked.
        if ($post && (string) $request->registration_status === '1' && (int) $post->registration_status !== 1
            && !preg_match('/[^\s\p{Z}]/u', (string) $post->licence_number)) {
            $message = 'Recruitment Licence Number is not added. Please add it before approving this Partner.';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $message, 'errors' => ['registration_status' => [$message]]], 422);
            }

            // Partner View page (plain form): back with the message, which re-opens its modal.
            return redirect()->back()->withErrors(['registration_status' => $message], 'registrationStatus')->withInput();
        }

        $post->registration_status = $request->registration_status;
        $post->save();

        // Same update either way - only the response shape differs. The
        // Partner View page's modal still does a plain form POST (no
        // X-Requested-With header), so it keeps getting the redirect/flash
        // it always has; the partner list's inline select (app-partner-list.js)
        // submits via $.ajax, which jQuery marks as XHR by default, so it
        // gets JSON back instead of navigating away.
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Registration status updated!']);
        }

        return redirect()->back()->with('success','Registration status updated!');
    }
}
