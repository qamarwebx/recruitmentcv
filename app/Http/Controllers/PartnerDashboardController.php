<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Expecworkcity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnerDashboardController extends Controller
{
    public function index()
    {
        return view('partner.dashboard');
    }

    public function profile($id)
    {
        $post = DB::table('partners as partner')
            ->leftJoin('countries as country','partner.country_id','=','country.id')
            ->leftJoin('cities as city','partner.city_id','=','city.id')
            ->select('partner.*')
            ->where('partner.id','=',$id)
            ->first();

        $countries = Country::orderBy('name','ASC')->get();
        $cities = City::orderBy('name','ASC')->get();

        return view('partner.profile',['post' => $post,'countries' => $countries,'cities' => $cities]);
    }

    public function employer()
    {
        return view('partner.employer.index');
    }

    public function employerJson(Request $request)
    {
        $posts = DB::table('bookings as booking')
        ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
        ->leftjoin('users as user','booking.user_id','=','user.id')
        // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
        ->leftjoin('cities as city','user.city_id','=','city.id')
        ->leftJoin('visadetails as visa','booking.id','visa.booking_id')
        ->select('booking.id','visa.id_no','visa.visa_no','booking.reference_no','booking.booking_date','booking.booking_status','user.name as cuname','cand.cand_name','cand.pass_no','cand.job_type','cand.photo_file','user.mobile_no','user.photo','city.name as city')
        ->where('booking.status','=','0')
        ->where('booking.booking_status','=','0')
        ->where('booking.partner_id','=',Auth::guard('partner')->user()->id)
        ->get();

        $data['data'] = $posts;
        return response()->json($data);
    }

    public function employerView($id){

        $post = DB::table('bookings as booking')
        ->leftjoin('candidates as cand','booking.cand_id','=','cand.id')
        ->leftjoin('users as user','booking.user_id','=','user.id')
        // ->leftjoin('userprofiles as profile','user.id','=','profile.user_id')
        ->leftjoin('cities as city','user.city_id','=','city.id')
        ->leftjoin('countries as country','country.id','=','user.country_id')
        ->leftJoin('visadetails as visa','booking.id','=','visa.booking_id')
        ->leftjoin('expecworkcities as expwork','booking.worklocation','=','expwork.id')
        ->leftjoin('partners as partner','partner.id','=','booking.partner_orderconfirmby_id')
        ->select(
            'booking.*',
            'city.name as citname',
            'user.name as cuname',
            'cand.cand_name',
            'cand.pass_no',
            'cand.exp_sal',
            'cand.marital_status',
            'cand.experience',
            'cand.expcity_id',
            'cand.photo_file',
            'cand.reference_no as refNo',
            'user.mobile_no',
            'user.address as cuaddr',
            'visa.visa_no',
            'visa.employer_name',
            'visa.employer_ar_name',
            'visa.id_no',
            'visa.visa_no',
            'visa.issuing_authority',
            'country.name as contname',
            'expwork.name as expwname',
            'partner.owner_name as partername',
            'partner.rec_off_name as partneroffice'
            )
        ->where('booking.status','=','0')
        ->where('booking.id','=',$id)
        ->first();

        $total_exp = array_sum(explode(',',$post->experience));

        $expcities = Expecworkcity::wherein('id',explode(',',$post->expcity_id))->get();
        $mycity = [];
        foreach ($expcities as $expcity) {
            $mycity[] =  $expcity->name;
        }


        return view('partner.employer.show',['post' => $post,'total_exp' => $total_exp,'mycity' => $mycity]);

    }

    
}
