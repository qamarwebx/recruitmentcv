<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\Adminprofile;
use App\Models\Candidate;
use App\Models\City;
use App\Models\Country;
use App\Models\Partner;
use App\Models\Region;
use App\Models\User;
use App\Models\Userprofile;
use App\Models\Visadetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountryCityController extends Controller
{
    public function contindex()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.countcity.contindex',['perm' => $permission]);
    }

    public function contindexjson(Request $request)
    {

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = Country::orderBy('id','DESC')->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_country == 1){
                $post = Country::orderBy('id','DESC')->get();
            }else{
                $post = Country::where('admin_id','=',Auth::guard('admin')->user()->id)->orderBy('id','DESC')->get();
            }
        }

        
        $data['data'] = $post;
        return response()->json($data);
    }

    public function contstore(Request $request)
    {
        $post = new Country();
        $post->name = $request->name;
        $post->arname = $request->arname;
        $post->country_code = $request->country_code;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back();
    }

    public function checkcontname(Request $request)
    {
        $post = Country::where('name','=',$request->name)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckcontname(Request $request)
    {
        $post = Country::where('name','=',$request->name)->where('id','!=',$request->id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function cityindex()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.countcity.cityindex',['perm' => $permission]);
    }

    public function cityindexjson(Request $request)
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = City::orderBy('id','DESC')->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_city == 1){
                $post = City::orderBy('id','DESC')->get();
            }else{
                $post = City::where('admin_id','=',Auth::guard('admin')->user()->id)->orderBy('id','DESC')->get();
            }
        }

        
        $data['data'] = $post;
        return response()->json($data);
    }

    public function citystore(Request $request)
    {
        $post = new City();
        $post->name = $request->name;
        $post->arname = $request->arname;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back();
    }

    public function checkcityname(Request $request)
    {
        $post = City::where('name','=',$request->name)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function checkcityarname(Request $request)
    {
        $post = City::where('arname','=',$request->arname)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckcityname(Request $request)
    {
        $post = City::where('name','=',$request->name)->where('id','!=',$request->id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function edcheckcityarname(Request $request)
    {
        $post = City::where('arname','=',$request->arname)->where('id','!=',$request->id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function regionIndex()
    {
        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        return view('admin.countcity.regionindex',['perm' => $permission]);
    }

    public function regionIndexJson(Request $request)
    {

        $permission = Adminpermission::where('staff_id','=',Auth::guard('admin')->user()->id)->first();
        if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1)) {
            $post = Region::orderBy('id','DESC')->get();
        }elseif(Auth::guard('admin')->user()->user_type == 2 || (isset($permission) && $permission->full_access == 0)){
            if($permission->view_region == 1){
                $post = Region::orderBy('id','DESC')->get();
            }else{
                $post = Region::where('admin_id','=',Auth::guard('admin')->user()->id)->orderBy('id','DESC')->get();
            }
        }

        
        $data['data'] = $post;
        return response()->json($data);
    }

    public function regionStore(Request $request)
    {
        $post = new Region();
        $post->name = $request->name;
        $post->arname = $request->arname;
        $post->admin_id = Auth::guard('admin')->user()->id;
        $post->save();

        return redirect()->back();
    }

    public function checkregionname(Request $request)
    {
        $post = Region::where('name','=',$request->name)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
        
    }

    public function checkregionarname(Request $request)
    {
        $post = Region::where('arname','=',$request->arname)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
        
    }

    public function edcheckregionname(Request $request)
    {
        $post = Region::where('name','=',$request->name)->where('id','!=',$request->id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
        
    }

    public function edcheckregionarname(Request $request)
    {
        $post = Region::where('arname','=',$request->arname)->where('id','!=',$request->id)->count();
        if($post == 0){
            $isAvailable = 'true';
        }else{
            $isAvailable = 'false';
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
        
    }

    public function contEdit(Request $request){
        $post = Country::find($request->id);

        return response()->json($post);
    }

    function contUpdate(Request $request) {
        $post = Country::find($request->edit_id);
        $post->name = $request->name;
        $post->arname = $request->arname;
        $post->country_code = $request->country_code;
        $post->save();
        return redirect()->back()->with('success','Country name updated');
    }

    public function delCheck(Request $request){
        $data1 = Candidate::where('nation_id','=',$request->id)->count();
        $data2 = Partner::where('country_id','=',$request->id)->count();
        $data3 = User::where('country_id','=',$request->id)->count();
        // $data4 = Userprofile::where('country_id','=',$request->id)->count();

        if ($data1 > 0 || $data2 > 0 || $data3 > 0) {
            return response()->json('1');
        } else {
            
        }

    }

    public function deleteC(Request $request){
        $post = Country::find($request->proff_ids);
        $post->delete();

        return redirect()->back()->with('success','Country deleted!');
    }

    public function regionEdit(Request $request){
        $post = Region::find($request->id);

        return response()->json($post);
    }

    public function regionUpdate(Request $request){
        $post = Region::find($request->edit_id);
        $post->name = $request->name;
        $post->arname = $request->arname;
        $post->save();

        return redirect()->back()->with('success','Region updated!');
    }

    public function delregionex(Request $request){
        $data1 = Candidate::where('region_id','=',$request->id)->count();
        $data2 = Adminprofile::where('region_id','=',$request->id)->count();

        if ($data1 > 0 || $data2 > 0) {
            return response()->json('1');
        } else {
            
        }

    }

    public function regionDel(Request $request){
        $post = Region::find($request->proff_ids);
        $post->delete();

        return redirect()->back()->with('success','Region deleted!');
    }

    public function edcity(Request $request) {
        $post = City::find($request->id);

        return response()->json($post);
    }

    public function cityUpdate(Request $request){
        $post = City::find($request->edit_id);

        $post->name = $request->name;
        $post->arname = $request->arname;
        $post->save();

        return redirect()->back()->with('success','City updated!');
    }

    public function delCityex(Request $request){
        $data1 = User::where('city_id','=',$request->id)->count();
        $data2 = Candidate::where('candcity_id','=',$request->id)->count();
        $data3 = Candidate::where('plb_id','=',$request->id)->count();
        $data4 = Adminprofile::where('city_id','=',$request->id)->count();
        $data5 = Partner::where('city_id','=',$request->id)->count();
        $data5 = Visadetails::where('wpcity_id','=',$request->id)->count();

        if ($data1 > 0 || $data2 > 0 || $data3 > 0 || $data4 > 0 || $data5 > 0) {
            return response()->json('1');
        } else {
            
        }

    }

    public function cityDelete(Request $request){
        $post = City::find($request->proff_ids);
        $post->delete();

        return redirect()->back()->with('success','City deleted!');

    }
}
